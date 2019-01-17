<?php
/**
 * Craft Flowplayer Drive plugin for Craft CMS 3.x
 *
 * This plugin includes Flowplayer Drive into craftcms.
 *
 * @link      http://luke.nehemedia.de
 * @copyright Copyright (c) 2018 Lucas Bares
 */

namespace lucasbares\craftflowplayerdrive\variables;

use Craft;
use lucasbares\craftflowplayerdrive\elements\FlowplayerDriveVideoElement;
use Twig_Markup;
use craft\web\View;

/**
 * Craft Flowplayer Drive Variable
 *
 * Simple variable that provides a getPlayer function to twig
 *
 * @author    Lucas Bares
 * @package   CraftFlowplayerDrive
 * @since     1.0.0
 */
class FlowplayerDriveVariable
{
    // Public Methods
    // =========================================================================

    /**
     * Returns a videos embed code.
     *
     * @param $entry
     * @return Twig_Markup
     * @throws \Twig_Error_Loader
     * @throws \yii\base\Exception
     */
    public function getPlayer(FlowplayerDriveVideoElement $entry){
        $settings = Craft::$app->getPlugins()->getPlugin('craft-flowplayer-drive')->getSettings();

        // Render template
        $oldMode = \Craft::$app->view->getTemplateMode();
        \Craft::$app->view->setTemplateMode(View::TEMPLATE_MODE_CP);
        $html = \Craft::$app->view->renderTemplate('craft-flowplayer-drive/_components/fields/VideoField_render', ['settings' => $settings, 'video' => $entry]);
        \Craft::$app->view->setTemplateMode($oldMode);

        return new Twig_Markup($html,Craft::$app->getView()->getTwig()->getCharset());
    }
}
