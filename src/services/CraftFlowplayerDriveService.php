<?php
/**
 * Craft Flowplayer Drive plugin for Craft CMS 3.x
 *
 * This plugin includes Flowplayer Drive into craftcms.
 *
 * @link      http://luke.nehemedia.de
 * @copyright Copyright (c) 2018 Lucas Bares
 */

namespace lucasbares\craftflowplayerdrive\services;

use lucasbares\craftflowplayerdrive\CraftFlowplayerDrive;
use lucasbares\craftflowplayerdrive\elements\FlowplayerDriveVideoElement;

use Craft;
use craft\base\Component;

use GuzzleHttp\Client as Client;

/**
 * CraftFlowplayerDriveService Service
 *
 * All of your plugin’s business logic should go in services, including saving data,
 * retrieving data, etc. They provide APIs that your controllers, template variables,
 * and other plugins can interact with.
 *
 * https://craftcms.com/docs/plugins/services
 *
 * @author    Lucas Bares
 * @package   CraftFlowplayerDrive
 * @since     1.0.0
 */
class CraftFlowplayerDriveService extends Component
{
	/**
	 * Instance of the http client
	 * 
	 * @var \Guzzle\Http\Client
	 * @access protected
	 */
	protected $client;
	
	/**
	 * Plugins Settings
	 * 
	 * @var lucasbares\craftflowplayerdrive\models\Settings
	 * @access protected
	 */
	protected $settings;
	
    // Public Methods
    // =========================================================================

	public function __construct(){

		// Initiate client
		$this->client = new Client;
		
		// Store settings
		$this->settings = CraftFlowplayerDrive::$plugin->getSettings();
	}
	


	public function listVideos($page = 1){
		$uri = 'https://api.flowplayer.com/ovp/web/video/v2/site/'.$this->settings->siteId.'.json?api_key='.$this->settings->apiKey.'&page='.$page;

		$response = $this->client->get($uri);

		if($response->getStatusCode() != '200'){
			throw new Exception("Error getting Video list", $response->getStatusCode());
			return false;
		}else{
			return json_decode($response->getBody())->videos;
		}	
	}

	public function listVideoElements($page = 1){
		$uri = 'https://api.flowplayer.com/ovp/web/video/v2/site/'.$this->settings->siteId.'.json?api_key='.$this->settings->apiKey.'&page_size=50&page='.$page;

		$response = $this->client->get($uri);

		$return = [];

		if($response->getStatusCode() != '200'){
			throw new Exception("Error getting Video list", $response->getStatusCode());
			return false;
		}else{
			foreach(json_decode($response->getBody())->videos as $video){
				$return[$video->id] = new FlowplayerDriveVideoElement();
				$return[$video->id]->fill($video);
			}
		}

		return $return;
	}


	public function getVideosByIds($ids)
	{
		if($ids == null){
			return [];
		}

		$return = [];

		foreach($ids as $video){
				
			$return[$video->id] = FlowplayerDriveVideoElement::createById($video->id);
		}

		return $return;
	}

	public function getVideoDetailById(string $id){
		$uri = 'https://api.flowplayer.com/ovp/web/video/v2/'.$id.'.json?api_key='.$this->settings->apiKey;

		$response = $this->client->get($uri);

		if($response->getStatusCode() != '200'){
			throw new Exception("Error getting Video list", $response->getStatusCode());
			return false;
		}else{
			return json_decode($response->getBody()->getContents());
		}	
	}


    /**
     * This function can literally be anything you want, and you can have as many service
     * functions as you want
     *
     * From any other plugin file, call it like this:
     *
     *     CraftFlowplayerDrive::$plugin->craftFlowplayerDriveService->exampleService()
     *
     * @return mixed
     */
    public function exampleService()
    {
        $result = 'something';
        // Check our Plugin's settings for `someAttribute`
        if (CraftFlowplayerDrive::$plugin->getSettings()->someAttribute) {
        }

        return $result;
    }
}
