<?php
/**
 * Craft Flowplayer Drive plugin for Craft CMS 3.x
 *
 * This plugin includes Flowplayer Drive into craftcms.
 *
 * @link      http://luke.nehemedia.de
 * @copyright Copyright (c) 2018 Lucas Bares
 */

namespace lucasbares\craftflowplayerdrive\models;

use lucasbares\craftflowplayerdrive\CraftFlowplayerDrive;

use Craft;
use craft\base\Model;

/**
 * CraftFlowplayerDrive Settings Model
 *
 * This is a model used to define the plugin's settings.
 *
 * Models are containers for data. Just about every time information is passed
 * between services, controllers, and templates in Craft, it’s passed via a model.
 *
 * https://craftcms.com/docs/plugins/models
 *
 * @author    Lucas Bares
 * @package   CraftFlowplayerDrive
 * @since     1.0.0
 */
class Settings extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * SiteID - Used for identification.
     *
     * @var string
     */
    public $siteId = '00000000-0000-0000-ab00-abc0d000000';
    
    /**
     * API Key - Used for authentication. Please keep secret.
     *
     * @var string
     */
    public $apiKey = 'abc000de-0000-0a0b-00ab-abc0d000000';
    
    /**
     * User ID - Needed in some API-requests.
     *
     * @var string
     */
    public $userId = 'abc000de-0000-0a0b-00ab-abc0d000000';


    /**
     * Default Player ID - To display the player on site
     *
     * @var string
     */
    public $defaultPlayerId = 'abc000de-0000-0a0b-00ab-abc0d000000';

    /**
     * Refresh-Interval
     *
     */
    public $refreshInterval = 60*60*12; // 12h


    // Public Methods
    // =========================================================================

    /**
     * Returns the validation rules for attributes.
     *
     * Validation rules are used by [[validate()]] to check if attribute values are valid.
     * Child classes may override this method to declare different validation rules.
     *
     * More info: http://www.yiiframework.com/doc-2.0/guide-input-validation.html
     *
     * @return array
     */
    public function rules()
    {
        return [
            [['siteId', 'apiKey','userId'], 'string'],
            [['siteId', 'apiKey'], 'required'],
            [['refreshInterval'], 'integer'],
        ];
    }
}
