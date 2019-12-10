<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Item;
use App\Menu;

class ItemChildrenController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $item)
    {
        $strData = $request->getContent();
        $hashData = json_decode($strData);
        $objItemFactory = new Item();
        $objItem = $objItemFactory->find($item);
        $intMenuId = $objItem->menu_id;

        // Find Menu
        if($objMenu = Menu::find($intMenuId))
        {
            $intMaxChildren = $objMenu->max_children;
            $intMaxDepth = $objMenu->max_depth;
        }
        else
        {
            $intMaxChildren = false;
            $intMaxDepth = false;
        }

        // Get the current depth
        $intCount = $objItemFactory->getItemLayer($objItem->id);
        
        $objItemFactory->saveItems($hashData, $item, $intMenuId, $intMaxChildren, $intMaxDepth, $intCount);

        // Returning Json
        return response()->json($hashData);
    }

    /**
     * Display the specified resource.
     *
     * @param  mixed  $item
     * @return \Illuminate\Http\Response
     */
    public function show($item)
    {
        $objItemFactory = new Item();
        $intMenuId = $objItemFactory->find($item)->menu_id;
        $hashData = $objItemFactory->getMenuItems($intMenuId, $item);

        // Returning Json
        return response()->json($hashData);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  mixed  $item
     * @return \Illuminate\Http\Response
     */
    public function destroy($item)
    {
        $objItemFactory = new Item();
        $intMenuId = $objItemFactory->find($item)->menu_id;
        $hashData = $objItemFactory->getChildrensList($intMenuId, $item);

        Item::whereIn('id', $hashData)->delete();
    }
}
