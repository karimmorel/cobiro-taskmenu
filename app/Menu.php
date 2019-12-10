<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Item;

class Menu extends Model
{
	// Model's configuration
    protected $table = "menus";
    protected $primaryKey = 'id';

    // constructor
    public function __construct()
    {
    	parent::__construct();
    	return $this;
    }

    /**
    *	Return depth of a menu
    */
    public function getMenusDepth($menu)
    {
    	$objItemFactory = new Item();
        $hashData = $objItemFactory->getMenuItems($menu);

        return $this->countDepth($hashData);
    }

    /**
    *	Count depth of a menu, from list of items
    */
    protected function countDepth($hashData, $intMaxValue = 0, $intCount = 0)
    {
    	$intCount = $intCount + 1;
    	foreach ($hashData as $mixItem)
    	{
    		if(isset($mixItem["children"]))
    		{
    			$intCount = $this->countDepth($mixItem["children"], $intMaxValue, $intCount);
    		}
    		else
    		{
    			if($intCount > $intMaxValue)
    			{
    				$intMaxValue = $intCount;
    				$intCount = 1;
    			}
    		}
    	}
    	return $intMaxValue;
    }

}
