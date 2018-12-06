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

use Craft;
use craft\base\Component;

use \Guzzle\Http\Client;

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
		$this->client = new \Guzzle\Http\Client;
		
		// Store settings
		$this->settings = CraftFlowplayerDrive::$plugin->getSettings();
	}
	
	public function listVideos(){
		$uri = 'https://api.flowplayer.com/ovp/web/video/v2/site/'.$this->settings->siteId.'.json?api_key='.$this->settings->apiKey;
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
