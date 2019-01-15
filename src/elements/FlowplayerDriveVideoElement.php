<?php
namespace lucasbares\craftflowplayerdrive\elements;

use craft\base\Element;
use Craft;
use lucasbares\craftflowplayerdrive\elements\db\FlowplayerDriveVideoElementQuery;
use lucasbares\craftflowplayerdrive\services\FlowplayerDriveService;
use craft\elements\db\ElementQueryInterface;
use lucasbares\craftflowplayerdrive\CraftFlowplayerDrive;

class FlowplayerDriveVideoElement extends Element
{

    public $video_id = 0;
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

    public $editable = [ 'name', 'description', 'published'];

    protected $obtained = ['adtag','categoryid', 'created_at', 'duration', 'episode', 'externalvideoid', 'mediafiles', 'noads', 'published_at', 'siteid', 'use_unpublish_date', 'unpublished_at', 'state',  'tags', 'updated_at', 'userid', 'views' ];

    /**
     * Flowplayer drive service
     * 
     * @var lucasbares\craftflowplayerdrive\services\FlowplayerDriveService;
     * @access protected
     */
    protected $service;

    public function init(){
        $this->service = CraftFlowplayerDrive::getInstance()->flowplayerDriveService;
    }

    public function beforeSave(bool $isNew): bool
    {
        // Type convert to bool
        if($this->published == '1' or $this->published == 1){
            $this->published = true;
        }else{
            $this->published = false;
        }
        
        // Save to API
        return $this->service->updateVideoElement($this);
    }

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
                    'video_id' => $this->video_id,
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
                    'video_id' => $this->video_id,
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

    public function fillFromAPI($videoInfo){
        // todo: automatic with fillable
        $this->video_id = $videoInfo->id;
        $this->name = $videoInfo->name;
        $this->description = $videoInfo->description;
        $this->published = $videoInfo->published;
        $this->views = $videoInfo->views;
        $this->adtag = $videoInfo->adtag;
        $this->categoryid = $videoInfo->categoryid;
        $this->created_at = $videoInfo->created_at;
        $this->duration = $videoInfo->duration;
        $this->episode = $videoInfo->name;
        $this->externalvideoid = $videoInfo->externalvideoid;
        $this->thumbnail_url = $videoInfo->images->thumbnail_url;
        $this->normal_image_url = $videoInfo->images->normal_image_url;
        $this->mediafiles = $videoInfo->mediafiles;
        $this->noads = $videoInfo->noads;
        $this->published_at = $videoInfo->published_at;
        $this->siteid = $videoInfo->siteid;
        $this->use_unpublish_date = $videoInfo->use_unpublish_date;
        $this->unpublished_at = $videoInfo->unpublished_at;
        $this->state = $videoInfo->state;
        $this->tags = $videoInfo->tags;
        $this->updated_at = $videoInfo->updated_at;
        $this->userid = $videoInfo->userid;
    }

    public function updateFromAPI($videoInfo){
        foreach ($this->obtained as $key) {
            $this->$key = $videoInfo->$key;
        }

        $this->thumbnail_url = $videoInfo->images->thumbnail_url;
        $this->normal_image_url = $videoInfo->images->normal_image_url;

        foreach ($this->editable as $key) {
            $this->$key = $videoInfo->$key;;
        }

        Craft::$app->elements->saveElement($this);

    }

    /**
     * @inheritdoc
     */
    // public static function hasStatuses(): bool
    // {
    //     return true;
    // }

    /**
     * @inheritdoc
     */
    // public static function statuses(): array
    // {
    //     return [
    //         self::STATUS_ENABLED => Craft::t('app', 'Published'),
    //         self::STATUS_DISABLED => Craft::t('app', 'Unpublished')
    //     ];

        
    // }

    
    protected static function defineSources(string $context = null): array
	{
	    return [
	        [
	            'key' => '*',
	            'label' => 'Alle Videos',
	            'criteria' => [],
                'hasThumbs' => true,
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
            'views' => \Craft::t('craft-flowplayer-drive', 'Views'),
            'published' => \Craft::t('craft-flowplayer-drive', 'Veröffentlicht'),
	    ];
	}

	protected static function defineTableAttributes(): array
	{
	    return [
	        'name' => \Craft::t('craft-flowplayer-drive', 'Name'),
	        'created_at' => \Craft::t('craft-flowplayer-drive', 'Erstellungsdatum'),
	        'published_at' => \Craft::t('craft-flowplayer-drive', 'Veröffentlichungsdatum'),
            'published' => \Craft::t('craft-flowplayer-drive', 'Veröffentlicht'),
	        'updated_at' => \Craft::t('craft-flowplayer-drive', 'Änderungsdatum'),
            'likes' => \Craft::t('craft-flowplayer-drive', 'Likes'),
            'dislikes' => \Craft::t('craft-flowplayer-drive', 'Dislikes'),
            'video_id' => \Craft::t('craft-flowplayer-drive', 'Video-ID'),
	    ];
	}

	protected static function defineDefaultTableAttributes(string $source): array
	{
	    return ['name', 'created_at','published','views'];
	}

	protected static function defineSearchableAttributes(): array
	{
   		return ['name', 'description'];
	}

    protected function tableAttributeHtml(string $attribute): string
    {
        switch ($attribute) {
            case 'published':
                return ($attribute == 1) ?  'true' :  'false';

        }

        return parent::tableAttributeHtml($attribute);
    }





	public static function find(): ElementQueryInterface
    {
        return new FlowplayerDriveVideoElementQuery(static::class);
    }


    public function getThumbUrl(int $size)
    {
        return $this->thumbnail_url;
    }

    public function getIsEditable(): bool
    {
        return true;
        //return \Craft::$app->user->checkPermission('edit-product:'.$this->getType()->id);
    }

    public function getEditorHtml(): string
    {
        // Name field
        $html = \Craft::$app->getView()->renderTemplateMacro('_includes/forms', 'textField', [
            [
                'label' => \Craft::t('app', 'Name'),
                'siteId' => $this->siteId,
                'id' => 'name',
                'name' => 'name',
                'value' => $this->name,
                'errors' => $this->getErrors('name'),
                'first' => true,
                'autofocus' => true,
                'required' => true
            ]
        ]);

        // Description field
        $html .= \Craft::$app->getView()->renderTemplateMacro('_includes/forms', 'textField', [
            [
                'label' => \Craft::t('app', 'Description'),
                'siteId' => $this->siteId,
                'id' => 'description',
                'name' => 'description',
                'value' => $this->description,
                'errors' => $this->getErrors('description'),
                'first' => false,
                'autofocus' => false,
                'required' => false
            ]
        ]);

        // Published
        $html .= '<div class="field"><div class="heading">
                            <label id="editor_'.$this->id.'-publish-label" for="published">Veröffentlicht</label></div><div class="input ltr">';
        $html .= \Craft::$app->getView()->renderTemplateMacro('_includes/forms', 'lightswitch', [
            [
                'label' => \Craft::t('app', 'Veröffentlicht'),
                'siteId' => $this->siteId,
                'id' => 'published',
                'name' => 'published',
                'value' => $this->published,
                'on' => $this->published,
                'errors' => $this->getErrors('published'),
                'first' => false,
                'autofocus' => false,
                'required' => false,
                'labelId' => 'editor_'.$this->id.'-publish-label',
            ]
        ]);
        $html .= '</div></div>';

        $html .= parent::getEditorHtml();

        return $html;
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