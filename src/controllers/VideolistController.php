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

use lucasbares\craftflowplayerdrive\services\FlowplayerDriveService;
use lucasbares\craftflowplayerdrive\elements\FlowplayerDriveVideoElement;
use craft\web\Controller;
use lucasbares\craftflowplayerdrive\CraftFlowplayerDrive;
use Craft;

/**
 * VideolistController
 *
 * @author Lucas Bares
 * @since 1.0.0
 * @package lucasbares\craftflowplayerdrive\controllers
 */
class VideolistController extends Controller
{
    /**
     * @var FlowplayerDriveService
     */
    protected $service;

    /**
     * Controller initialization
     */
    public function init()
    {
        $this->service = CraftFlowplayerDrive::getInstance()->flowplayerDriveService;
        parent::init();
    }

    /**
     * Refreshes the videolist, adding new videos and updating existing ones
     *
     * @return \yii\web\Response
     * @throws \Throwable
     * @throws \craft\errors\ElementNotFoundException
     * @throws \craft\errors\MissingComponentException
     * @throws \yii\base\Exception
     */
    public function actionRefresh()
    {
        $this->_refreshVideoList(true);

        return $this->redirect(Craft::$app->getRequest()->referrer);
    }

    /**
     * Finds new videos not already indexed
     *
     * @return \yii\web\Response
     * @throws \Throwable
     * @throws \craft\errors\ElementNotFoundException
     * @throws \craft\errors\MissingComponentException
     * @throws \yii\base\Exception
     */
    public function actionRefreshIndex()
    {

        $this->_refreshVideoList(false);

        return $this->redirect(Craft::$app->getRequest()->referrer);

    }

    /**
     * Refreshes the video list
     *
     * @param bool $updateExisting whether existing elements should be updated
     * @throws \Throwable
     * @throws \craft\errors\ElementNotFoundException
     * @throws \craft\errors\MissingComponentException
     * @throws \yii\base\Exception
     */
    protected function _refreshVideoList($updateExisting = false)
    {
        // Get all videos
        $videolist = $this->service->getVideoList(1,300);
        $page = 1; $new=0; $updated=0;

        while($video = array_pop($videolist)){

            // Find Video with 'video_id' == id, check/update or create new
            $result = FlowplayerDriveVideoElement::find()->video_id($video->id);

            // If new, create new
            if($result->count() == 0){
                $newVideo = Craft::$app->elements->createElement('lucasbares\craftflowplayerdrive\elements\FlowplayerDriveVideoElement');
                $newVideo->fillFromAPI($video);
                Craft::$app->elements->saveElement($newVideo);
                $new++;

            // If already in Database, update with API-Data
            }elseif($updateExisting == true){
                $videoElement = $result->one()->fillFromAPI($video);
                Craft::$app->elements->saveElement($videoElement);
                $updated++;
            }

            if(count($videolist) == 0){
                $page++;
                $videolist = $this->service->getVideoList($page,300);
            }
        }

        if($updateExisting){
            Craft::$app->getSession()->setNotice(\Craft::t('craft-flowplayer-drive','Videolist refreshed. {updated} videos updated, {new} new videos indexed.', ['updated' => $updated, 'new' => $new]));
        }else{
            Craft::$app->getSession()->setNotice(\Craft::t('craft-flowplayer-drive','Videoindex refreshed. {new} new videos indexed.', ['new' => $new]));
        }

    }

    /**
     * Clears the video list completely
     *
     * @return \yii\web\Response
     * @throws \Throwable
     * @throws \craft\errors\MissingComponentException
     */
    public function actionClear(){
        $result = FlowplayerDriveVideoElement::find();

        foreach($result as $video){
            Craft::$app->elements->deleteElement($video);
        }

        Craft::$app->getSession()->setNotice(\Craft::t('craft-flowplayer-drive','Videolist cleared'));

        return $this->redirect(Craft::$app->getRequest()->referrer);
    }

}