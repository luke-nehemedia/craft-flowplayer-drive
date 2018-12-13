<?php
namespace lucasbares\craftflowplayerdrive\elements;

use craft\base\Element;
use Craft;
use lucasbares\craftflowplayerdrive\elements\storage\FlowplayerDriveVideoElementQuery;
use craft\elements\db\ElementQueryInterface;
use lucasbares\craftflowplayerdrive\CraftFlowplayerDrive;

class FlowplayerDriveVideoElement extends Element
{

    public $published_at = 0;

    public $name = '';

    public $created_at = '';

    public $updated_at = '';

    public $id = 1;

    public $likes = 0;

    public $dislikes = 0;

    public $thumbnail = '';



    /**
     * @inheritdoc
     */
    public static function hasStatuses(): bool
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public static function statuses(): array
    {
        return [
            self::STATUS_ENABLED => Craft::t('app', 'Enabled2'),
            self::STATUS_DISABLED => Craft::t('app', 'Disabled')
        ];
    }

    public function fill($attributes){
        // fill from storage or cache?
        $this->id = $attributes->id;
        $this->name = $attributes->name;
        $this->likes = $attributes->likes;
        $this->dislikes = $attributes->dislikes;
        $this->thumbnail = $attributes->images->thumbnail_url;
    }

    public static function createById(string $id){
        $obj = new FlowplayerDriveVideoElement;
        $data = CraftFlowplayerDrive::getInstance()->flowplayerDrive->getVideoDetailById($id);
        $obj->fill($data);

        return $obj;
    }

    protected static function defineSources(string $context = null): array
	{
	    return [
	        [
	            'key' => '*',
	            'label' => 'Alle Videos',
	            'criteria' => []
	        ],
	        [
	            'key' => 'public',
	            'label' => 'Öffentlich',
	            'criteria' => [
	                'published' => true,
	            ]
	        ],
	        [
	            'key' => 'private',
	            'label' => 'Privat',
	            'criteria' => [
	                'published' => false,
	            ]
	        ],
	    ];
	}

	protected static function defineSortOptions(): array
	{
	    return [
	        'name' => \Craft::t('craft-flowplayer-drive', 'Name'),
	        'created_at' => \Craft::t('craft-flowplayer-drive', 'Erstellungsdatum'),
	        'published_at' => \Craft::t('craft-flowplayer-drive', 'Veröffentlichungsdatum'),
	        'updated_at' => \Craft::t('craft-flowplayer-drive', 'Änderungsdatum'),
            'likes' => \Craft::t('craft-flowplayer-drive', 'Likes'),
            'dislikes' => \Craft::t('craft-flowplayer-drive', 'Dislikes'),
	    ];
	}

	protected static function defineTableAttributes(): array
	{
	    return [
	        'name' => \Craft::t('craft-flowplayer-drive', 'Name'),
	        'created_at' => \Craft::t('craft-flowplayer-drive', 'Erstellungsdatum'),
	        'published_at' => \Craft::t('craft-flowplayer-drive', 'Veröffentlichungsdatum'),
	        'updated_at' => \Craft::t('craft-flowplayer-drive', 'Änderungsdatum'),
            'likes' => \Craft::t('craft-flowplayer-drive', 'Likes'),
            'dislikes' => \Craft::t('craft-flowplayer-drive', 'Dislikes'),
	    ];
	}

	protected static function defineDefaultTableAttributes(string $source): array
	{
	    return ['name', 'published_at','likes'];
	}

	protected static function defineSearchableAttributes(): array
	{
   		return ['name'];
	}

	public static function find(): ElementQueryInterface
    {
        return new FlowplayerDriveVideoElementQuery(static::class);
    }

    /**
     * @inheritdoc
     */
    public static function indexHtml(ElementQueryInterface $elementQuery, array $disabledElementIds = null, array $viewState, string $sourceKey = null, string $context = null, bool $includeContainer, bool $showCheckboxes): string
    {
        $variables = [
            'viewMode' => $viewState['mode'],
            'context' => $context,
            'disabledElementIds' => $disabledElementIds,
            'collapsedElementIds' => Craft::$app->getRequest()->getParam('collapsedElementIds'),
            'showCheckboxes' => $showCheckboxes,
        ];

        // Special case for sorting by structure
        if (isset($viewState['order']) && $viewState['order'] === 'structure') {
            $source = ElementHelper::findSource(static::class, $sourceKey, $context);

            if (isset($source['structureId'])) {
                $elementQuery->orderBy(['lft' => SORT_ASC]);
                $variables['structure'] = Craft::$app->getStructures()->getStructureById($source['structureId']);

                // Are they allowed to make changes to this structure?
                if ($context === 'index' && $variables['structure'] && !empty($source['structureEditable'])) {
                    $variables['structureEditable'] = true;

                    // Let StructuresController know that this user can make changes to the structure
                    Craft::$app->getSession()->authorize('editStructure:' . $variables['structure']->id);
                }
            } else {
                unset($viewState['order']);
            }
        } else {
            $orderBy = self::_indexOrderBy($viewState);
            if ($orderBy !== false) {
                $elementQuery->orderBy($orderBy);
            }
        }

        if ($viewState['mode'] === 'table') {
            // Get the table columns
            $variables['attributes'] = Craft::$app->getElementIndexes()->getTableAttributes(static::class, $sourceKey);

            // Give each attribute a chance to modify the criteria
            foreach ($variables['attributes'] as $attribute) {
                static::prepElementQueryForTableAttribute($elementQuery, $attribute[0]);
            }
        }

        $variables['elements'] = $elementQuery->all();

        $template = '_elements/' . $viewState['mode'] . 'view/' . ($includeContainer ? 'container2' : 'elements');

        return Craft::$app->getView()->renderTemplate($template, $variables);
    }

    private static function _indexOrderBy(array $viewState)
    {
        // Define the available sort attribute/option pairs
        $sortOptions = [];
        foreach (static::sortOptions() as $key => $sortOption) {
            if (is_string($key)) {
                // Shorthand syntax
                $sortOptions[$key] = $key;
            } else {
                if (!isset($sortOption['orderBy'])) {
                    throw new InvalidValueException('Sort options must specify an orderBy value');
                }
                $attribute = $sortOption['attribute'] ?? $sortOption['orderBy'];
                $sortOptions[$attribute] = $sortOption['orderBy'];
            }
        }
        $sortOptions['score'] = 'score';

        if (!empty($viewState['order']) && isset($sortOptions[$viewState['order']])) {
            $columns = $sortOptions[$viewState['order']];
        } else if (count($sortOptions) > 1) {
            $columns = reset($sortOptions);
        } else {
            return false;
        }

        // Borrowed from QueryTrait::normalizeOrderBy()
        $columns = preg_split('/\s*,\s*/', trim($columns), -1, PREG_SPLIT_NO_EMPTY);
        $result = [];
        foreach ($columns as $i => $column) {
            if ($i === 0) {
                // The first column's sort direction is always user-defined
                $result[$column] = !empty($viewState['sort']) && strcasecmp($viewState['sort'], 'desc') ? SORT_ASC : SORT_DESC;
            } else if (preg_match('/^(.*?)\s+(asc|desc)$/i', $column, $matches)) {
                $result[$matches[1]] = strcasecmp($matches[2], 'desc') ? SORT_ASC : SORT_DESC;
            } else {
                $result[$column] = SORT_ASC;
            }
        }

        return $result;
    }

    /**
     * Returns the string representation of the element.
     *
     * @return string
     */
    public function __toString()
    {
        if ($this->name) {
            return (string)$this->name;
        }
        return (string)$this->id ?: static::class;
    }



}