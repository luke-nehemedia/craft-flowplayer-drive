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
use craft\helpers\ElementHelper;
use lucasbares\craftflowplayerdrive\CraftFlowplayerDrive;
use lucasbares\craftflowplayerdrive\assetbundles\videofieldfield\VideoFieldFieldAsset;
use lucasbares\craftflowplayerdrive\elements\FlowplayerDriveVideoElement;

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use yii\db\Schema;
use craft\helpers\Json;


/**
 * FlowplayerVideoField Field
 *
 * Whenever someone creates a new field in Craft, they must specify what
 * type of field it is. The system comes with a handful of field types baked in,
 * and we’ve made it extremely easy for plugins to add new ones.
 *
 * https://craftcms.com/docs/plugins/field-types
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
     * Validation rules are used by [[validate()]] to check if attribute values are valid.
     * Child classes may override this method to declare different validation rules.
     *
     * More info: http://www.yiiframework.com/doc-2.0/guide-input-validation.html
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
     * Modifies an element query.
     *
     * This method will be called whenever elements are being searched for that may have this field assigned to them.
     *
     * If the method returns `false`, the query will be stopped before it ever gets a chance to execute.
     *
     * @param mixed $value The value that was set on this field’s corresponding [[ElementCriteriaModel]] param, if any.
     * @return null|false `false` in the event that the method is sure that no elements are going to be found.
     */
    public function serializeValue($value, ElementInterface $element = null)
    {
        return parent::serializeValue($value, $element);
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
     */
    public function getInputHtml($value, ElementInterface $element = null): string
    {
        // Just let the parent do its work
        return parent::getInputHtml($value, $element);
    }

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
                'label' => 'Alle Videos',
                'criteria' => [],
                'hasThumbs' => true,
            ];
        }

        return $variables;
    }
}
