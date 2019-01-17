<?php
/**
 * Craft Flowplayer Drive plugin for Craft CMS 3.x
 *
 * This plugin includes Flowplayer Drive into craftcms.
 *
 * @link      http://luke.nehemedia.de
 * @copyright Copyright (c) 2018 Lucas Bares
 */

namespace lucasbares\craftflowplayerdrive\fields;

use craft\fields\BaseRelationField;
use lucasbares\craftflowplayerdrive\elements\FlowplayerDriveVideoElement;

use Craft;
use craft\base\ElementInterface;


/**
 * FlowplayerVideoField Field
 *
 * @author    Lucas Bares
 * @package   CraftFlowplayerDrive
 * @since     1.0.0
 *
 * @property null|string $settingsHtml
 */
class FlowplayerVideoField extends BaseRelationField
{
    // Public Properties
    // =========================================================================

    /**
     * Option that only published videos will be listed
     *
     * @var boolean
     */
    public $published;

    /**
     * Option to limit the number of selectable videos
     *
     * @var integer
     */
    public $limit;

    // Static Methods
    // =========================================================================

    /**
     * Returns the display name of this class.
     *
     * @return string The display name of this class.
     */
    public static function displayName(): string
    {
        return Craft::t('craft-flowplayer-drive', 'FlowplayerVideoField');
    }

    /**
     * @inheritdoc
     */
    protected static function elementType(): string
    {
        return FlowplayerDriveVideoElement::class;
    }

    // Public Methods
    // =========================================================================

    public function init()
    {
        parent::init();
        $this->allowLargeThumbsView = true;
    }

    /**
     * Returns the validation rules for attributes.
     *
     * @return array
     */
    public function rules()
    {
        $rules = parent::rules();
        $rules = array_merge($rules, [
            ['published', 'boolean'],
            ['published', 'default', 'value' => 0],
            ['limit','integer'],
            ['limit', 'default', 'value' => 1]
        ]);
        return $rules;
    }

    /**
     * Returns the component’s settings HTML.
     *
     * @return string|null
     * @throws \Twig_Error_Loader
     * @throws \yii\base\Exception
     */
    public function getSettingsHtml()
    {
        // Render the settings template
        return Craft::$app->getView()->renderTemplate(
            'craft-flowplayer-drive/_components/fields/VideoField_settings',
            [
                'field' => $this,
            ]
        );
    }

    /**
     * @inheritdoc
     *
     * @todo Add some descriptions
     */
    public function getInputHtml($value, ElementInterface $element = null): string
    {
        // Just let the parent do its work
        return parent::getInputHtml($value, $element);
    }

    // Protected Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    protected function inputTemplateVariables($value = null, ElementInterface $element = null): array
    {
        $variables = parent::inputTemplateVariables($value, $element);
        $variables['limit'] = $this->limit;

        // Option: Only show published videos
        if($this->published){
            $variables['criteria'] = ['published' => 1];
            $variables['sources'] = [
                'key' => '*',
                'label' => Craft::t('craft-flowplayer-drive', 'All Videos'),
                'criteria' => [],
                'hasThumbs' => true,
            ];
        }

        return $variables;
    }
}
