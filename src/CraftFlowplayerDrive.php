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

use lucasbares\craftflowplayerdrive\services\CraftFlowplayerDriveService as CraftFlowplayerDriveServiceService;
use lucasbares\craftflowplayerdrive\variables\CraftFlowplayerDriveVariable;
use lucasbares\craftflowplayerdrive\models\Settings;
use lucasbares\craftflowplayerdrive\fields\VideoField as VideoFieldField;
use lucasbares\craftflowplayerdrive\elements\FlowplayerDriveVideoElement;
use lucasbares\craftflowplayerdrive\elements\db\FlowplayerDriveVideoElementQuery;


use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\services\Fields;
use craft\web\twig\variables\CraftVariable;
use craft\web\twig\variables\Cp;
use craft\events\RegisterComponentTypesEvent;

use yii\base\Event;

/**
 * Craft plugins are very much like little applications in and of themselves. We’ve made
 * it as simple as we can, but the training wheels are off. A little prior knowledge is
 * going to be required to write a plugin.
 *
 * For the purposes of the plugin docs, we’re going to assume that you know PHP and SQL,
 * as well as some semi-advanced concepts like object-oriented programming and PHP namespaces.
 *
 * https://craftcms.com/docs/plugins/introduction
 *
 * @author    Lucas Bares
 * @package   CraftFlowplayerDrive
 * @since     1.0.0
 *
 * @property  CraftFlowplayerDriveServiceService $craftFlowplayerDriveService
 * @property  Settings $settings
 * @method    Settings getSettings()
 */
class CraftFlowplayerDrive extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * Static property that is an instance of this plugin class so that it can be accessed via
     * CraftFlowplayerDrive::$plugin
     *
     * @var CraftFlowplayerDrive
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * To execute your plugin’s migrations, you’ll need to increase its schema version.
     *
     * @var string
     */
    public $schemaVersion = '0.0.4-dev';

	/**
     * Whether there is a settings page
     *
     * @var boolean
     */
	public $hasCpSettings = true;

    /**
     * @var bool Whether the plugin has its own section in the CP
     */
    public $hasCpSection = true;


    // Public Methods
    // =========================================================================

    /**
     * Set our $plugin static property to this class so that it can be accessed via
     * CraftFlowplayerDrive::$plugin
     *
     * Called after the plugin class is instantiated; do any one-time initialization
     * here such as hooks and events.
     *
     * If you have a '/vendor/autoload.php' file, it will be loaded for you automatically;
     * you do not need to load it in your init() method.
     *
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        //Register our fields
        Event::on(
            Fields::class,
            Fields::EVENT_REGISTER_FIELD_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = VideoFieldField::class;
            }
        );

        //Register our variables
        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('craftFlowplayerDrive', CraftFlowplayerDriveVariable::class);
            }
        );

        $this->setComponents([
            'flowplayerDriveService' => CraftFlowplayerDriveServiceService::class,
        ]);

        // Do something after we're installed
        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin === $this) {
                    // We were just installed
                }
            }
        );

		/**
		 * Logging in Craft involves using one of the following methods:
		 *
		 * Craft::trace(): record a message to trace how a piece of code runs. This is mainly for development use.
		 * Craft::info(): record a message that conveys some useful information.
		 * Craft::warning(): record a warning message that indicates something unexpected has happened.
		 * Craft::error(): record a fatal error that should be investigated as soon as possible.
		 *
		 * Unless `devMode` is on, only Craft::warning() & Craft::error() will log to `craft/storage/logs/web.log`
		 *
		 * It's recommended that you pass in the magic constant `__METHOD__` as the second parameter, which sets
		 * the category to the method (prefixed with the fully qualified class name) where the constant appears.
		 *
		 * To enable the Yii debug toolbar, go to your user account in the AdminCP and check the
		 * [] Show the debug toolbar on the front end & [] Show the debug toolbar on the Control Panel
		 *
		 * http://www.yiiframework.com/doc-2.0/guide-runtime-logging.html
		 */
        Craft::info(
            Craft::t(
                'craft-flowplayer-drive',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );

        //$videos =  $this->flowplayerDriveService->listVideos();

        // foreach($videos as $video){
        //     // search
        //   //  if(true){
        //         $element = new FlowplayerDriveVideoElement();
        //         $element->fill($video);
        //         Craft::$app->elements->saveElement($element);

        //         //dd($element);
        //     //}



        // }
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

    public function getCpNavItem()
    {
        $item = parent::getCpNavItem();
        $item['badgeCount'] = 5;
        // $item['subnav'] = [
        //     'index' => ['label' => 'Übersicht', 'url' => 'flowplayerdrive/index'],
        //     'createe' => ['label' => 'Neues Video', 'url' => 'flowplayerdrive/create'],
        //     'settings' => ['label' => 'Einstellungen', 'url' => 'flowplayerdrive/settings'],
        // ];
        //$item['label'] = 'Flowplayer Drive';
        //$item['url'] = 'flowplayerdrive';
        return $item;
    }
}
