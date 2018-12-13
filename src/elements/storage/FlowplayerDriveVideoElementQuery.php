<?php
namespace lucasbares\craftflowplayerdrive\elements\storage;

use craft\db\Query;
use craft\elements\db\ElementQuery;
use craft\helpers\Db;
use ns\prefix\elements\Product;
use lucasbares\craftflowplayerdrive\elements\FlowplayerDriveVideoElement;
use lucasbares\craftflowplayerdrive\CraftFlowplayerDrive;

class FlowplayerDriveVideoElementQuery extends ElementQuery 
{
    public $price;
    public $currency;
    public $page;

    public function price($value)
    {
        $this->price = $value;

        return $this;
    }

    public function currency($value)
    {
        $this->currency = $value;

        return $this;
    }

    protected function beforePrepare(): bool
    {
    	return parent::beforePrepare();
	}

	public function ids($db = NULL) : array
	{
		die('ide');
	}

	public function order(string $value) 
	{
		die('order');
		return $this;
	}

	public function asArray(bool $value = true)
    {
    	die('1');
        $this->asArray = $value;
        return $this;
    }

    public function orderBy($columns)
    {
    	//echo('2');
        parent::orderBy($columns);

        // If $columns normalizes to an empty array, just set it to null
        if ($this->orderBy === []) {
            $this->orderBy = null;
        }


        return $this;
    }

    public function prepare($builder){
    	die('3');
    }

    public function find(array $attributes = null): array
    {
        die('suche');
    }

    // Called by modal to list elements
    public function all($db = null)
    {
        $criteria = $this->getCriteria();

        if($criteria['search'] != ''){
            die('suche');
        }

        if($criteria['page'] != null){
            $page = $criteria['page'];
        }else{
            $page = 1;
        }

        if($criteria['orderBy'] != ''){
            
        }

    	return CraftFlowplayerDrive::getInstance()->flowplayerDrive->listVideoElements($page);

        // Cached?
        if (($cachedResult = $this->getCachedResult()) !== null) {
            if ($this->with) {
                Craft::$app->getElements()->eagerLoadElements($this->elementType, $cachedResult, $this->with);
            }
            return $cachedResult;
        }

        return parent::all($db);
    }

}