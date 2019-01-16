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
use GuzzleHttp\Psr7\Request;

use yii\base\Exception;

/**
 * FlowplayerDriveService Service
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
class FlowplayerDriveService extends Component
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
	

	// old
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

	public function getVideoList($page = 1, $page_size = 20){
		$uri = 'https://api.flowplayer.com/ovp/web/video/v2/site/'.$this->settings->siteId.'.json?api_key='.$this->settings->apiKey.'&page='.$page;

		if($page_size != 20) $uri .= '&page_size='.$page_size;

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

	public function updateVideo($videoElement, $values){
		$uri = 'https://api.flowplayer.com/ovp/web/video/v2/update.json';

		$requestBody = [
			'api_key'	=> 	$this->settings->apiKey,
			'siteid'	=>	$this->settings->siteId,
			'id'		=>	$videoElement->video_id
		];

		foreach ($values as $key => $value) {
			$requestBody[$key] = $value;
		}

		// send request
		$request = new Request('POST', $uri, [], $requestBody);
		$result = $this->client->send($request);


		dd($result->getBody());

	}

	public function updateVideoElement($videoElement){
		$uri = 'https://api.flowplayer.com/ovp/web/video/v2/update.json';

		$requestBody = [
			'api_key'	=> 	$this->settings->apiKey,
			'siteid'	=>	$this->settings->siteId,
			'id'		=>	$videoElement->video_id,
		];

		foreach ($videoElement->editable as $key) {
			$requestBody[$key] = $videoElement->$key;
		}

		$options = [
		    'json' => $requestBody,
		   ]; 

		// send request
		$response = $this->client->post($uri, $options);

		if($response->getStatusCode() != 200){
			Craft::$app->getSession()->setError('Error saving video details: '.$response->getReasonPhrase());
			return false;
		}

		return true;

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

    public function deleteVideoElement($videoElement)
    {
    	$uri = 'https://api.flowplayer.com/ovp/web/video/delete/video.json';

		$requestBody = [
			'api_key'	=> 	$this->settings->apiKey,
			'siteid'	=>	$this->settings->siteId,
			'userid'	=>	$this->settings->userId,
			'id'		=>	$videoElement->video_id,
		];

		$options = [
		    'json' => $requestBody,
		   ]; 

		// send request
		$response = $this->client->delete($uri, $options);

		if($response->getStatusCode() != 200){
			Craft::$app->getSession()->setError('Error saving video details: '.$response->getReasonPhrase());
			throw new Exception('Error saving video details: '.$response->getReasonPhrase());
			return false;
		}

		return true;
    }

    public function createVideoElement(FlowplayerDriveVideoElement $videoElement,$videoUrl){
    	$uri = 'https://api.flowplayer.com/ovp/web/video/v2/create.json';

    	$requestBody = [
			'api_key'	=> 	$this->settings->apiKey,
			'siteid'	=>	$this->settings->siteId,
			'userid'	=>	$this->settings->userId,
			'input'		=>	$videoUrl,
		];

		foreach ($videoElement->editable as $key) {
			if(!empty($videoElement->$key)){
				$requestBody[$key] = $videoElement->$key;
			}
		}

		$options = [
		    'json' => $requestBody,
		   ]; 

		// send request
		$response = $this->client->post($uri, $options);

		if($response->getStatusCode() != 200){
			Craft::$app->getSession()->setError('Error saving video details: '.$response->getReasonPhrase());
			throw new Exception('Error saving video details: '.$response->getReasonPhrase());
			return false;
		}

		// update local object
		$responseObj = json_decode($response->getBody());
		$videoElement->fillFromAPI($responseObj);

		return $videoElement;


    }
}
