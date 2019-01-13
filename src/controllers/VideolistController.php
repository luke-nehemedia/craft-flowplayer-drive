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
use craft\web\Controller;
use lucasbares\craftflowplayerdrive\CraftFlowplayerDrive;

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
		$page = 1;
		while($video = array_pop($videolist)){


			if(count($videolist) == 0){
				$page++;
				$videolist = $this->service->getVideoList($page,50);
			}
		}

	}

}