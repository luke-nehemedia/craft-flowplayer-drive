<?php
/**
 * Craft Flowplayer Drive plugin for Craft CMS 3.x
 *
 * This plugin includes Flowplayer Drive into craftcms.
 *
 * @link      http://luke.nehemedia.de
 * @copyright Copyright (c) 2018 Lucas Bares
 */

namespace lucasbares\craftflowplayerdrive\controllers;

use lucasbares\craftflowplayerdrive\services\FlowplayerDriveService;
use lucasbares\craftflowplayerdrive\elements\FlowplayerDriveVideoElement;
use craft\web\Controller;
use lucasbares\craftflowplayerdrive\CraftFlowplayerDrive;
use Craft;
use craft\helpers\Json;
use craft\helpers\UrlHelper;
use yii\web\Response;

class VideoController extends Controller
{

	protected $service;

	public function init(){
		$this->service = CraftFlowplayerDrive::getInstance()->flowplayerDriveService;
		parent::init();
	}

	

	public function actionEdit(int $videoId = null, string $siteHandle = null, FlowplayerDriveVideoElement $video = null): Response
    {
        $variables = [
            'videoId' => $videoId,
            'video' => $video
        ];

        if ($siteHandle !== null) {
            $variables['site'] = Craft::$app->getSites()->getSiteByHandle($siteHandle);

            if (!$variables['site']) {
                throw new NotFoundHttpException('Invalid site handle: ' . $siteHandle);
            }
        }

        // Get the site
        // ---------------------------------------------------------------------

        if (Craft::$app->getIsMultiSite()) {
            // Only use the sites that the user has access to
            $variables['siteIds'] = Craft::$app->getSites()->getEditableSiteIds();
        } else {
            /** @noinspection PhpUnhandledExceptionInspection */
            $variables['siteIds'] = [Craft::$app->getSites()->getPrimarySite()->id];
        }

        if (!$variables['siteIds']) {
            throw new ForbiddenHttpException('User not permitted to edit content in any sites');
        }

        if (empty($variables['site'])) {
            /** @noinspection PhpUnhandledExceptionInspection */
            $variables['site'] = Craft::$app->getSites()->getCurrentSite();

            if (!in_array($variables['site']->id, $variables['siteIds'], false)) {
                $variables['site'] = Craft::$app->getSites()->getSiteById($variables['siteIds'][0]);
            }

            $site = $variables['site'];
        } else {
            // Make sure they were requesting a valid site
            /** @var Site $site */
            $site = $variables['site'];
            if (!in_array($site->id, $variables['siteIds'], false)) {
                throw new ForbiddenHttpException('User not permitted to edit content in this site');
            }
        }

        // Get the video
        // ---------------------------------------------------------------------

        if (empty($variables['video'])) {
            if (!empty($variables['video'])) {
                $variables['video'] = FlowplayerDriveVideoElement::find()->id($variables['video']);

                if (!$variables['video']) {
                    throw new NotFoundHttpException('Video not found');
                }
            } else {
                $variables['video'] = new FlowplayerDriveVideoElement();
                $variables['video']->siteId = $site->id;
            }
        }

        // Todo: $this->_prepEditCategoryVariables($variables);

        /** @var Site $site */
        $site = $variables['site'];

        /** @var Site $site */
        $video = $variables['video'];

        // Todo: $this->_enforceEditCategoryPermissions($category);

        $request = Craft::$app->getRequest();

        // Body class
        $variables['bodyClass'] = 'edit-category site--' . $site->handle;

        // Page title
        if ($video->id === null or $video->id === 0) {
            $variables['title'] = Craft::t('app', 'Create a new video');
        } else {
            $variables['docTitle'] = $variables['title'] = trim($video->name) ?: Craft::t('app', 'Edit Video');
        }

        // Breadcrumbs
        $variables['crumbs'] = [
            [
                'label' => Craft::t('app', 'Flowplayer Drive'),
                'url' => UrlHelper::url('craft-flowplayer-drive')
            ]
        ];
        
		$variables['showPreviewBtn'] = false;


        // Set the base CP edit URL
        $variables['baseCpEditUrl'] = "craft-flowplayer-drive/{id}-{slug}";

        // Set the "Continue Editing" URL
        $siteSegment = Craft::$app->getIsMultiSite() && Craft::$app->getSites()->getCurrentSite()->id != $site->id ? "/{$site->handle}" : '';
        $variables['continueEditingUrl'] = $variables['baseCpEditUrl'] . $siteSegment;

        // Set the "Save and add another" URL
        $variables['nextCategoryUrl'] = "craft-flowplayer-drive/new{$siteSegment}";

        // Render the template!
        //$this->getView()->registerAssetBundle(EditCategoryAsset::class);

        return $this->renderTemplate('craft-flowplayer-drive/_edit', $variables);
    }

    public function actionStore(){
        $this->requirePostRequest();
        $request = Craft::$app->getRequest();

        $videoElement = new FlowplayerDriveVideoElement;

        // Todo: Nice implementation incl. validation
        foreach ($videoElement->editable as $key) {
            $videoElement->$key = $request->getBodyParam($key);
        }

        // Asset
        $videoFile = Craft::$app->assets->getAssetById($request->getBodyParam('asset.0'))->getUrl();

        // push to API and save Element
        $videoElement = $this->service->createVideoElement($videoElement,$videoFile);
        Craft::$app->elements->saveElement($videoElement);

        Craft::$app->getSession()->setNotice(Craft::t('craft-flowplayer-drive', 'Video saved.'));

        return $this->redirectToPostedUrl($videoElement);

    }

}