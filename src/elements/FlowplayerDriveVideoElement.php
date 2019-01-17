<?php
namespace lucasbares\craftflowplayerdrive\elements;

use craft\base\Element;
use Craft;
use craft\web\View;
use lucasbares\craftflowplayerdrive\elements\db\FlowplayerDriveVideoElementQuery;
use lucasbares\craftflowplayerdrive\elements\actions\DeleteVideo;
use lucasbares\craftflowplayerdrive\services\FlowplayerDriveService;
use craft\elements\db\ElementQueryInterface;
use lucasbares\craftflowplayerdrive\CraftFlowplayerDrive;
use Twig_Markup;

class FlowplayerDriveVideoElement extends Element
{

    // Flowplayer-Drive Attributes
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

    /**
     * Editable attributes
     * 
     * @var array
     * @access public
     */
    public $editable = [ 'name', 'description', 'published'];

    /**
     * Attributes obtained by API
     * 
     * @var array
     * @access protected
     */
    protected $obtainable = ['adtag','categoryid', 'created_at', 'duration', 'episode', 'externalvideoid', 'mediafiles', 'noads', 'published_at', 'siteid', 'use_unpublish_date', 'unpublished_at', 'state',  'tags', 'updated_at', 'userid', 'views' ];

    /**
     * Flowplayer drive service instance
     * 
     * @var lucasbares\craftflowplayerdrive\services\FlowplayerDriveService;
     * @access protected
     */
    protected $service;

    /**
     * Initialization 
     *
     * Stores an instance of the servie provider into $this->service
     *
     * @return void
     */
    public function init(){
        $this->service = CraftFlowplayerDrive::getInstance()->flowplayerDriveService;

        parent::init();
    }

    /**
     * Updates the element via API before saving it to the local database
     *
     * @param boolean $isNew whether it is a new or updated element
     * @return bool
     */
    public function beforeSave(bool $isNew): bool
    {
        parent::beforeSave($isNew);

        // Type convert to bool (for API call)
        if($this->published == '1' or $this->published == 1){
            $this->published = true;
        }else{
            $this->published = false;
        }
        
        // Save existing items to API
        if(!$isNew){
            return $this->service->updateVideoElement($this);
        }

        return true;
    }

    /**
     * This function is responsible for keeping your element table updated when elements are saved. 
     * The afterSave() method is a part of the standard element saving control flow.
     *
     * @param boolean $isNew whether it is a new or updated element
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

    /**
     * Fill model with data obtained by the API
     *
     * @param stdObject $videoInfo json object with information obtained by the API-call
     * @return void
     */
    public function fillFromAPI($videoInfo){
        
        $this->video_id = $videoInfo->id;

        foreach ($this->editable as $key) {
            $this->$key = $videoInfo->$key;
        }

        foreach ($this->obtainable as $key) {
            $this->$key = $videoInfo->$key;
        }

        $this->thumbnail_url = $videoInfo->images->thumbnail_url;
        $this->normal_image_url = $videoInfo->images->normal_image_url;
    }

    /**
     * Update the model with information obtained from the API
     *
     * @param stdObject $videoInfo json object with information obtained by the API-call
     * @return void
     */
    public function updateFromAPI($videoInfo){
        foreach ($this->obtainable as $key) {
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

    public function isPublished(){
        if($this->published == 1 or $this->published == true or $this->published == '1'){
            return true;
        }else{
            return false;
        }
    }


    /**
     * @inheritdoc
     */
    public function getStatus()
    {
        if($this->isPublished()){
            return self::STATUS_ENABLED;
        }else{
            return self::STATUS_DISABLED;
        }
        
        // idea: include pending depending on "state" or when using unpublish_date
    }

    
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
                    'published' => 1
                ],
                'hasThumbs' => true
            ],
            [
                'key' => 'private',
                'label' => 'Privat',
                'criteria' => [
                    'published' => 'not 1'
                ],
                'hasThumbs' => true
            ],
        ];
    }

     /**
     * @inheritdoc
     */
    protected static function defineActions(string $source = null): array
    {
        $actions = [];

        $actions[] = DeleteVideo::class;

        return $actions;

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

    // protected function tableAttributeHtml(string $attribute): string
    // {
    //     switch ($attribute) {
    //         case 'published':
    //             return var_export($attribute,1);
    //             //return ($attribute == 1) ?  'ja' :  'nein';

    //     }

    //     return parent::tableAttributeHtml($attribute);
    // }





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
        $html .= '<div class="field"><div class="heading"><label id="editor_'.$this->id.'-publish-label" for="published">Veröffentlicht</label></div><div class="input ltr">';
        $html .= \Craft::$app->getView()->renderTemplateMacro('_includes/forms', 'lightswitch', [
            [
                'label' => \Craft::t('app', 'Veröffentlicht'),
                'siteId' => $this->siteId,
                'id' => 'published',
                'name' => 'published',
                'value' => true,
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
     * Returns html code for the flowplayer with the defaultPlayerId
     *
     * @return Twig_Markup
     * @throws \Twig_Error_Loader
     * @throws \yii\base\Exception
     */
    public function getPlayerHTML(){
        $settings = CraftFlowplayerDrive::getInstance()->getSettings();

        // Render template
        $oldMode = \Craft::$app->view->getTemplateMode();
        Craft::$app->view->setTemplateMode(View::TEMPLATE_MODE_CP);
        $html = Craft::$app->view->renderTemplate('craft-flowplayer-drive/_components/fields/VideoField_render', ['settings' => $settings, 'video' => $this]);
        Craft::$app->view->setTemplateMode($oldMode);

        return new Twig_Markup($html,Craft::$app->getView()->getTwig()->getCharset());
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