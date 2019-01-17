<?php
/**
 * Craft Flowplayer Drive plugin for Craft CMS 3.x
 *
 * This plugin includes Flowplayer Drive into Craftcms.
 *
 * @link      http://luke.nehemedia.de
 * @copyright Copyright (c) 2018 Lucas Bares
 */

namespace lucasbares\craftflowplayerdrive\controllers;

use lucasbares\craftflowplayerdrive\elements\FlowplayerDriveVideoElement;
use craft\web\Controller;
use lucasbares\craftflowplayerdrive\CraftFlowplayerDrive;
use Craft;
use craft\helpers\UrlHelper;
use lucasbares\craftflowplayerdrive\services\FlowplayerDriveService;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * VideoController
 *
 * @author Lucas Bares
 * @since 1.0.0
 * @package lucasbares\craftflowplayerdrive\controllers
 */
class VideoController extends Controller
{
    /**
     * @var FlowplayerDriveService
     */
    protected $service;

    /**
     * Controller initialization
     */
    public function init(){
        $this->service = CraftFlowplayerDrive::getInstance()->flowplayerDriveService;
        parent::init();
    }

    /**
     * Edit/Create a video
     *
     * @param int $videoId
     * @param FlowplayerDriveVideoElement|null $video
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionEdit(int $videoId = null, FlowplayerDriveVideoElement $video = null): Response
    {
        $variables = [
            'videoId' => $videoId,
            'video' => $video
        ];

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
            }
        }

        /** @var FlowplayerDriveVideoElement $video */
        $video = $variables['video'];

        // Body class
        $variables['bodyClass'] = 'edit-video';

        // Page title
        if ($video->id === null or $video->id === 0) {
            $variables['title'] = Craft::t('craft-flowplayer-drive', 'Create a new video');
        } else {
            $variables['docTitle'] = $variables['title'] = trim($video->name) ?: Craft::t('craft-flowplayer-drive', 'Edit video');
        }

        // Breadcrumbs
        $variables['crumbs'] = [
            [
                'label' => Craft::t('craft-flowplayer-drive', 'Flowplayer Drive'),
                'url' => UrlHelper::url('craft-flowplayer-drive')
            ]
        ];

        // Preview Button
        $variables['showPreviewBtn'] = false;

        // Set the base CP edit URL
        $variables['baseCpEditUrl'] = "craft-flowplayer-drive/{id}";

        return $this->renderTemplate('craft-flowplayer-drive/_edit', $variables);
    }

    /**
     * Handles a store request and stores a new video to the API and databse
     *
     * @return Response
     * @throws \Throwable
     * @throws \craft\errors\ElementNotFoundException
     * @throws \craft\errors\MissingComponentException
     * @throws \yii\base\Exception
     * @throws \yii\web\BadRequestHttpException
     */
    public function actionStore(){

        $this->requirePostRequest();

        $request = Craft::$app->getRequest();

        $videoElement = new FlowplayerDriveVideoElement;

        // Todo: Nice implementation incl. validation
        foreach ($videoElement->editable as $key) {
            $videoElement->$key = $request->getBodyParam($key);
        }

        // published
        $videoElement->published = (bool)$request->getBodyParam('published');

        // Asset
        $videoFile = Craft::$app->assets->getAssetById($request->getBodyParam('asset.0'))->getUrl();

        // push to API and save Element
        $videoElement = $this->service->createVideoElement($videoElement,$videoFile);
        Craft::$app->elements->saveElement($videoElement);

        Craft::$app->getSession()->setNotice(Craft::t('craft-flowplayer-drive', 'Video successfully saved.'));

        return $this->redirectToPostedUrl($videoElement);

    }

}