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

use lucasbares\craftflowplayerdrive\CraftFlowplayerDrive;

use Craft;
use Twig_Markup;
use craft\web\View;

/**
 * Craft Flowplayer Drive Variable
 *
 * Craft allows plugins to provide their own template variables, accessible from
 * the {{ craft }} global variable (e.g. {{ craft.craftFlowplayerDrive }}).
 *
 * https://craftcms.com/docs/plugins/variables
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
     * Whatever you want to output to a Twig template can go into a Variable method.
     * You can have as many variable functions as you want.  From any Twig template,
     * call it like this:
     *
     *     {{ craft.craftFlowplayerDrive.exampleVariable }}
     *
     * Or, if your variable requires parameters from Twig:
     *
     *     {{ craft.craftFlowplayerDrive.exampleVariable(twigValue) }}
     *
     * @param null $optional
     * @return string
     */
    public function exampleVariable($optional = null)
    {
        $result = "And away we go to the Twig template...";
        if ($optional) {
            $result = "I'm feeling optional today...";
        }
        return $result;
    }

    public function getPlayer($entry){
        $settings = Craft::$app->getPlugins()->getPlugin('craft-flowplayer-drive')->getSettings();

        // Render template
        $oldMode = \Craft::$app->view->getTemplateMode();
        \Craft::$app->view->setTemplateMode(View::TEMPLATE_MODE_CP);
        $html = \Craft::$app->view->renderTemplate('craft-flowplayer-drive/_components/fields/VideoField_render', ['settings' => $settings, 'video' => $entry]);
        \Craft::$app->view->setTemplateMode($oldMode);

        return new Twig_Markup($html,Craft::$app->getView()->getTwig()->getCharset());
    }
}
