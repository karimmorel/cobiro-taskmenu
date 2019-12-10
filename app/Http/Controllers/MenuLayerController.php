<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Item;

class MenuLayerController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  mixed  $menu
     * @return \Illuminate\Http\Response
     */
    public function show($menu, $layer)
    {
        // Get the menu's items
        $objItemFactory = new Item();
        $hashData = $objItemFactory->getMenuItems($menu);

        // Get only the layer we are interested in
        $hashLayer = $objItemFactory->getLayerFromMenuItems($hashData, $layer);

        // Returning Json
        return response()->json($hashLayer);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  mixed  $menu
     * @return \Illuminate\Http\Response
     */
    public function destroy($menu, $layer)
    {
        // Get the menu's items
        $objItemFactory = new Item();
        $hashData = $objItemFactory->getMenuItems($menu, null, false, true);

        // Get the layer requested and the children layer (+1)
        $hashDestroyedLayer = $objItemFactory->getLayerFromMenuItems($hashData, $layer);
        $hashChildrenLayer = $objItemFactory->getLayerFromMenuItems($hashData, $layer + 1);

        // Sort the children by parent_id
        $hashChildrenList = array();
        foreach ($hashChildrenLayer as $hashItem)
        {
                $hashChildrenList[$hashItem["parent_id"]][] = $hashItem["id"];
        }

        // Get the destroyed item's parents
        $hashDestroyedList = array();
        foreach ($hashDestroyedLayer as $hashItem)
        {
            if(isset($hashDestroyedList[$hashItem["id"]]))
            {
                $hashDestroyedList[$hashItem["id"]] .= ",".$hashItem["parent_id"];
            }
            else
            {
                $hashDestroyedList[$hashItem["id"]] = $hashItem["parent_id"];
            }
        }

        // Destroy all layer's items
        $arrDestroyedIds = array_keys($hashDestroyedList);
        Item::whereIn('id', $arrDestroyedIds)->delete();

        // Update children's parent_id
        foreach($hashChildrenList as $key => $arrUpdateList)
        {
            DB::table('items')->whereIn('id', $arrUpdateList)->update(array('parent_id' => $hashDestroyedList[$key]));
        }
        
    }
}
