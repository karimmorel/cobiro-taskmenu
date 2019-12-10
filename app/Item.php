<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
	// Model's configuration
    protected $table = "items";
    protected $primaryKey = 'id';

    // constructor
    public function __construct()
    {
    	parent::__construct();
    	return $this;
    }



    /** 
    * Incremental method for creating items objects and persist them
    *
    * $hashItems -> List of Items sent for creation
    * $intParentId -> ID of the very next parent
    * $menuId -> ID of the menu
    */
    public function saveItems($hashItems, $intParentId, $menuId, $intMaxChildren = false, $intMaxDepth = false, $intActualLayer = 0)
    {
    	$intActualLayer = $intActualLayer + 1;

    	// If more than max_depth, we dont save
    	if($intMaxDepth)
    	{
    		if($intMaxDepth < $intActualLayer)
    		{
    			return;
    		}
    	}

    	foreach ($hashItems as $mixItem)
    	{
    		// New save for each Item
    		$objItem = new Item();
    		$objItem->field = $mixItem->field;
    		$objItem->parent_id = $intParentId;
    		$objItem->menu_id = (int) $menuId;

    		// If to much children --> no save
    		if($this->countItemChildrens($intParentId, $menuId) <= $intMaxChildren)
    		{
    			$objItem->save();
    		}

    		// If the item have childrens (not empty)
    		// new call of this method
    		if(isset($mixItem->children))
    		{
    			if(!empty($mixItem->children))
    			{    				
    				$this->saveItems($mixItem->children, $objItem->id, $menuId, $intMaxChildren, $intMaxDepth, $intActualLayer);
    			}
    		}
    	}
    }

    /**
	* Get all the items from a menu ID
	*
	*/
    public function getMenuItems($intMenuId, $intParentId = null, $boolIdAsKey = false, $boolIdInValues = false)
    {
    	// Get all items of a menu
    	$hashItems = self::select('id','parent_id','field')->where('menu_id', '=', $intMenuId)->get()->toArray();

    	// Have ID as key
    	$hashKeyItems = array();
    	foreach ($hashItems as $hashItem)
    	{
    		$hashKeyItems[$hashItem['id']] = $hashItem;
    	}

    	// Link items to their childrens
    	foreach ($hashKeyItems as $intItemId => $hashItem)
    	{
    		if($hashItem['parent_id'])
    		{
    			$hashParentItem = $hashKeyItems[$hashItem['parent_id']];
    			if(!isset($hashParentItem['children']))
    			{
    				$hashParentItem['children'] = array($intItemId);
    			}
    			else
    			{
    				$hashParentItem['children'][] = $intItemId;
    			}
    			$hashKeyItems[$hashItem['parent_id']] = $hashParentItem;
    		}
    	}

    	return $this->sortMenuItems($hashKeyItems, $intParentId, $boolIdAsKey, $boolIdInValues);
    }


    /**
    * Create the same structure as the Api request it
    *
    * It sorts menu items layer by layer
    *
    * The parameters $boolIdAsKey & $boolIdInValues can be used to have more infos on the items (id, parent_id)
    */
    protected function sortMenuItems($hashMenuItems, $intParentId, $boolIdAsKey, $boolIdInValues)
    {
    	$hashReturn = array();


    	foreach ($hashMenuItems as $intItemId => $hashItem)
    	{
    		$hashData = array();
    		if($hashItem['parent_id'] == $intParentId)
    		{
    			$hashData['field'] = $hashItem['field'];

    			if(isset($hashItem['children']))
    			{
    				$hashData['children'] = $this->sortMenuItems($hashMenuItems, $hashItem['id'], $boolIdAsKey, $boolIdInValues);
    			}

    			// Adding Id in returned data (value or key)
    			if($boolIdInValues)
    			{
    				$hashData['id'] = $hashItem['id'];
    				$hashData['parent_id'] = $hashItem['parent_id'];
    			}
    			if($boolIdAsKey)
    			{
    				$hashReturn[$hashItem['id']] = $hashData;
    			}
    			else
    			{
    				$hashReturn[] = $hashData;
    			}
    		}
    	}

    	return $hashReturn;
    }

    /**
    *	Get list of items with the item ids
    */
    public function getChildrensList($hashMenuItems, $intParentId)
    {
    	$hashData = $this->getMenuItems($hashMenuItems, $intParentId, true);

    	return $this->getItemIdFromMenuItemsList($hashData);
    }

    /**
    *	Get list of item ids from a list from getMenuItems() method
    *
    */
    protected function getItemIdFromMenuItemsList($hashData)
    {
    	$arrItemsIdList = array();

    	foreach ($hashData as $key => $hashItem)
    	{
    		$arrItemsIdList[] = $key;
    		if(isset($hashItem["children"]))
    		{
    			// Merge item and children ids in one array
    			$arrItemsIdList = array_merge($arrItemsIdList, $this->getItemIdFromMenuItemsList($hashItem["children"]));
    		}
    	}

    	return $arrItemsIdList;
    }

    /**
    *	Return only the layer requested
    *
    */
    public function getLayerFromMenuItems($hashData, $layer)
    {
    	return $this->findLayerFromMenuItems($hashData, $layer, 0, array());
    }


    /**
    *	Find the layer's items in a list of Menu Items
    */
    protected function findLayerFromMenuItems($hashData, $layer, $intCount, $arrReturn)
    {
    	$intCount = $intCount + 1;

    	// If we are in the good layer, we return the item, else we call this function on the item
    	if($layer > $intCount)
    	{
    		foreach ($hashData as $mixItem)
    		{
    			if(isset($mixItem["children"]))
    			{
    				$arrReturn = $this->findLayerFromMenuItems($mixItem["children"], $layer, $intCount, $arrReturn);
    			}
    		}
    	}
    	else
    	{
    		foreach ($hashData as $mixItem)
    		{
    			if(isset($mixItem["children"]))
    			{
    				unset($mixItem["children"]);
    			}
    				$arrReturn[] = $mixItem;
    		}
    	}
    	return $arrReturn;
    }

    // Just count item's childrens
    protected function countItemChildrens($intItemId, $intMenuId)
    {
    	return Item::where('parent_id', '=', $intItemId)->where('menu_id', '=', $intMenuId)->count();
    }

    // Return the layer of a item
    public function getItemLayer($intItemId)
    {
    	return $this->countItemLayer($intItemId, 0);
    }

    // Count the layer of an item
    protected function countItemLayer($intItemId, $intCount)
    {
    	$intCount = $intCount + 1;
    	$objItem  = self::find($intItemId);

    	if($objItem->parent_id != null)
    	{
    		$intCount = $this->countItemLayer($objItem->parent_id, $intCount);
    	}
    	return $intCount;
    }

}
