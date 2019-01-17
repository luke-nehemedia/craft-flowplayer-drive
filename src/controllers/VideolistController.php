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

class VideolistController extends Controller
{

	protected $service;

	public function init(){
		$this->service = CraftFlowplayerDrive::getInstance()->flowplayerDriveService;
		parent::init();
	}

	public function actionRefresh(){

		// Get all videos
		$videolist = $this->service->getVideoList(1,50);
		$page = 1; $new=0; $updated=0;
		$return = '';
		while($video = array_pop($videolist)){

			// Find Video with 'video_id' == id, check/update or create new
			$result = FlowplayerDriveVideoElement::find()->video_id($video->id);
			$return .= $page.' ';
			
			
			// If already in Database, update with API-Data
			if($result->count() > 0){
				$result->one()->fillFromAPI($video);
                Craft::$app->elements->saveElement($video);
				$updated++;
			// If new, create new
			}else{
				//$newVideo = new FlowplayerDriveVideoElement();
				$newVideo = Craft::$app->elements->createElement('lucasbares\craftflowplayerdrive\elements\FlowplayerDriveVideoElement');
				$newVideo->fillFromAPI($video);
				Craft::$app->elements->saveElement($newVideo);
				$new++;
			}

			if(count($videolist) == 0){
				$page++;
				$videolist = $this->service->getVideoList($page,50);
			}			
		}
		
		Craft::$app->getSession()->setNotice('All Videos refreshed, '.$updated.' updated videos, '.$new.' new videos.');

		return $this->redirect(Craft::$app->getRequest()->referrer);

		//return $return;

	}

	public function actionClear(){
		$result = FlowplayerDriveVideoElement::find();

		foreach($result as $video){
			Craft::$app->elements->deleteElement($video);
		}

		Craft::$app->getSession()->setNotice('Videolist cleared');

		return $this->redirect(Craft::$app->getRequest()->referrer);
	}

}