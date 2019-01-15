<?php
namespace lucasbares\craftflowplayerdrive\elements\db;

use craft\db\Query;
use craft\elements\db\ElementQuery;
use craft\helpers\Db;
use ns\prefix\elements\Product;
use lucasbares\craftflowplayerdrive\elements\FlowplayerDriveVideoElement;
use lucasbares\craftflowplayerdrive\CraftFlowplayerDrive;

class FlowplayerDriveVideoElementQuery extends ElementQuery 
{

    public $video_id;
    public $name;
    public $description;
    public $state;
    public $published;

    public $adtag;
    public $categoryid;
    public $created_at;
    public $duration;
    public $episode;
    public $externalvideoid;
    public $id;
    public $thumbnail_url;
    public $normal_image_url;

    public $mediafiles;

    
    public $noads;
    public $published_at;
    public $siteid;
    public $use_unpublish_date;
    public $unpublished_at;
    
    public $tags;
    public $updated_at;
    public $userid;
    public $views;

    public function name($value)
    {
        $this->name = $value;

        return $this;
    }

    public function description($value)
    {
        $this->description = $value;

        return $this;
    }

    public function published($value)
    {
        $this->published = $value;

        return $this;
    }

    public function state($value)
    {
        $this->state = $value;

        return $this;
    }

    public function video_id($value){
        $this->video_id = $value;

        return $this;
    }


    protected function beforePrepare(): bool
    {
        // join in the craftflowplayerdrive table
        $this->joinElementTable('craftflowplayerdrive');

        // select the price column
        $this->query->select([
            'craftflowplayerdrive.name',
            'craftflowplayerdrive.video_id',
            'craftflowplayerdrive.description',
            'craftflowplayerdrive.published',
            'craftflowplayerdrive.state',
            'craftflowplayerdrive.published_at',
            'craftflowplayerdrive.created_at',
            'craftflowplayerdrive.thumbnail_url',

        ]);

        if ($this->name) {
            $this->subQuery->andWhere(Db::parseParam('craftflowplayerdrive.name', $this->name));
        }

        if ($this->video_id) {
            $this->subQuery->andWhere(Db::parseParam('craftflowplayerdrive.video_id', $this->video_id));
        }

        if ($this->description) {
            $this->subQuery->andWhere(Db::parseParam('craftflowplayerdrive.description', $this->description));
        }

        if ($this->published) {
            $this->subQuery->andWhere(Db::parseParam('craftflowplayerdrive.published', $this->published));
        }

        if ($this->state) {
            $this->subQuery->andWhere(Db::parseParam('craftflowplayerdrive.state', $this->state));
        }

        return parent::beforePrepare();
    }

    

}