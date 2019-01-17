<?php
/**
 * Craft Flowplayer Drive plugin for Craft CMS 3.x
 *
 * This plugin includes Flowplayer Drive into craftcms.
 *
 * @link      http://luke.nehemedia.de
 * @copyright Copyright (c) 2018 Lucas Bares
 */

namespace lucasbares\craftflowplayerdrive;

use lucasbares\craftflowplayerdrive\fields\FlowplayerVideoField;
use lucasbares\craftflowplayerdrive\services\FlowplayerDriveService;
use lucasbares\craftflowplayerdrive\variables\FlowplayerDriveVariable;
use lucasbares\craftflowplayerdrive\models\Settings;
use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\services\Fields;
use craft\web\twig\variables\CraftVariable;
use craft\events\RegisterComponentTypesEvent;
use craft\web\UrlManager;
use yii\base\Event;
use craft\events\RegisterUrlRulesEvent;

/**
 * Craft Flowplayer Drive plugin for Craft CMS 3.x
 *
 * This plugin includes Flowplayer Drive into craftcms.
 *
 * @author    Lucas Bares
 * @package   CraftFlowplayerDrive
 * @since     1.0.0
 *
 * @property  FlowplayerDriveService $flowplayerDriveService
 * @property  Settings $settings
 * @method    Settings getSettings()
 */
class CraftFlowplayerDrive extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * Static property that is an instance of this plugin class
     *
     * @var CraftFlowplayerDrive
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * Schema Version
     *
     * @var string
     */
    public $schemaVersion = '1.0.0';

    /**
     * @var boolean Whether there is a settings page
     */
    public $hasCpSettings = true;

    /**
     * @var bool Whether the plugin has its own section in the CP
     */
    public $hasCpSection = true;


    // Public Methods
    // =========================================================================

    /**
     * Plugin initalization, registering routes, components, elements
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        //Register fields
        Event::on(
            Fields::class,
            Fields::EVENT_REGISTER_FIELD_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = FlowplayerVideoField::class;
            }
        );

        //Register variables
        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('craftFlowplayerDrive', FlowplayerDriveVariable::class);
            }
        );

        // Register services
        $this->setComponents([
            'flowplayerDriveService' => FlowplayerDriveService::class,
        ]);


        // register the urls
        Event::on(
            UrlManager::class, 
            UrlManager::EVENT_REGISTER_CP_URL_RULES, 
            function(RegisterUrlRulesEvent $e) {
                $e->rules['craft-flowplayer-drive/clear'] = 'craft-flowplayer-drive/videolist/clear';
            }
        );

        Event::on(
            UrlManager::class, 
            UrlManager::EVENT_REGISTER_CP_URL_RULES, 
            function(RegisterUrlRulesEvent $e) {
                $e->rules['craft-flowplayer-drive/refresh'] = 'craft-flowplayer-drive/videolist/refresh';
            }
        );

        Event::on(
            UrlManager::class, 
            UrlManager::EVENT_REGISTER_CP_URL_RULES, 
            function(RegisterUrlRulesEvent $e) {
                $e->rules['craft-flowplayer-drive/create'] = 'craft-flowplayer-drive/video/edit';
            }
        );

        Event::on(
            UrlManager::class, 
            UrlManager::EVENT_REGISTER_CP_URL_RULES, 
            function(RegisterUrlRulesEvent $e) {
                $e->rules['craft-flowplayer-drive/store'] = 'craft-flowplayer-drive/video/store';
            }
        );

        // Log
        Craft::info(
            Craft::t(
                'craft-flowplayer-drive',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    /**
     * @inheritdoc
     */
    public function getCpNavItem()
    {
        $item = parent::getCpNavItem();
        $item['subnav'] = [
            'index' => ['label' => Craft::t('craft-flowplayer-drive', 'Video List'), 'url' => 'craft-flowplayer-drive/index'],
            'create' => ['label' => Craft::t('craft-flowplayer-drive', 'New Video'), 'url' => 'craft-flowplayer-drive/create'],
        ];
        $item['label'] = Craft::t('craft-flowplayer-drive', 'Flowplayer Drive');
        return $item;
    }


    // Protected Methods
    // =========================================================================

    /**
     * Creates and returns the model used to store the plugin’s settings.
     *
     * @return \craft\base\Model|null
     */
    protected function createSettingsModel()
    {
        return new Settings();
    }

    /**
     * Returns the rendered settings HTML, which will be inserted into the content
     * block on the settings page.
     *
     * @return string The rendered settings HTML
     * @throws \Twig_Error_Loader
     * @throws \yii\base\Exception
     */
    protected function settingsHtml(): string
    {
        return Craft::$app->view->renderTemplate(
            'craft-flowplayer-drive/settings',
            [
                'settings' => $this->getSettings()
            ]
        );
    }
}
