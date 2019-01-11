<?php
namespace lucasbares\craftflowplayerdrive\elements;

use craft\base\Element;
use Craft;
use lucasbares\craftflowplayerdrive\elements\storage\FlowplayerDriveVideoElementQuery;
use craft\elements\db\ElementQueryInterface;
use lucasbares\craftflowplayerdrive\CraftFlowplayerDrive;

class FlowplayerDriveVideoElement extends Element
{

    public $adtag = '';
    public $categoryid = 0;
    public $created_at = '';
    public $description = '';
    public $duration = 0;
    public $episode = false;
    public $externalvideoid = '';
    public $id = 0;
    public $thumbnail_url = '';
    public $normal_image_url = '';

    public $mediafiles = ['original_file_url' => '', 'base_url' => '', 'standard_url' => '', 'high_url' => '', 'm3u8_url' => '', 'webm_url' => ''];

    public $name = '';
    public $noads = true;
    public $published = false;
    public $published_at = '';
    public $siteid = '';
    public $use_unpublish_date = false;
    public $unpublished_at = '';
    public $state = ''; // FINISHED, PROCESSING
    public $tags = ''; // CSV
    public $updated_at = '';
    public $userid = '';
    public $views = 0;

    protected $editable = ['userid', 'tags', 'name', 'description', 'categoryid', 'image', 'published', 'published_at', 'use_unpublish_date', 'unpublish_at', 'customfield1', 'customfield', 'additionalCustomFields'];


    /**
     * This function is responsible for keeping your element table updated when elements are saved. 
     * The afterSave() method is a part of the standard element saving control flow.
     *
     * @param boolean $isNew
     */
    public function afterSave(bool $isNew)
    {
        if ($isNew) {
            \Craft::$app->db->createCommand()
                ->insert('{{%craftflowplayerdrive}}', [
                    'id' => $this->id,
                    'name' => $this->name,
                    'description' => $this->description,
                    'published' => $this->published,
                    'views' => $this->views,

                    'adtag' => $this->adtag,
                    'categoryid' =>  $this->categoryid,
                    'created_at' => $this->created_at,
                    'duration' => $this->duration,
                    'episode' => $this->episode,
                    'externalvideoid' => $this->externalvideoid,
                    'thumbnail_url' => $this->thumbnail_url,
                    'normal_image_url' => $this->normal_image_url,
                    'mediafiles' => $this->mediafiles,
                    'noads' => $this->noads,
                    'published_at' => $this->published_at,
                    'siteid' => $this->siteid,
                    'use_unpublish_date' => $this->use_unpublish_date,
                    'unpublished_at' => $this->unpublished_at,
                    'state' => $this->state,
                    'tags' => $this->tags,
                    'updated_at' => $this->updated_at,
                    'userid' => $this->userid,
                ])
                ->execute();
        } else {
            \Craft::$app->db->createCommand()
                ->update('{{%craftflowplayerdrive}}', [
                    'name' => $this->name,
                    'description' => $this->description,
                    'published' => $this->published,
                    'views' => $this->views,

                    'adtag' => $this->adtag,
                    'categoryid' =>  $this->categoryid,
                    'created_at' => $this->created_at,
                    'duration' => $this->duration,
                    'episode' => $this->episode,
                    'externalvideoid' => $this->externalvideoid,
                    'thumbnail_url' => $this->thumbnail_url,
                    'normal_image_url' => $this->normal_image_url,
                    'mediafiles' => $this->mediafiles,
                    'noads' => $this->noads,
                    'published_at' => $this->published_at,
                    'siteid' => $this->siteid,
                    'use_unpublish_date' => $this->use_unpublish_date,
                    'unpublished_at' => $this->unpublished_at,
                    'state' => $this->state,
                    'tags' => $this->tags,
                    'updated_at' => $this->updated_at,
                    'userid' => $this->userid,
                ], ['id' => $this->id])
                ->execute();
        }

        parent::afterSave($isNew);
    }


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
            self::STATUS_ENABLED => Craft::t('app', 'Published'),
            self::STATUS_DISABLED => Craft::t('app', 'Unpublished')
        ];

        
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