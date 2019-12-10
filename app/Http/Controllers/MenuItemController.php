<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Item;
use App\Menu;

class MenuItemController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $menu)
    {
        // Get Data
        $strData = $request->getContent();
        $hashData = json_decode($strData);

        // Find Menu
        if($objMenu = Menu::find($menu))
        {
            $intMaxChildren = $objMenu->max_children;
            $intMaxDepth = $objMenu->max_depth;
            $objItemFactory = new Item();
        }
        else
        {
            $intMaxChildren = false;
            $intMaxDepth = false;
        }

            $objItemFactory->saveItems($hashData, null, $menu, $intMaxChildren, $intMaxDepth, 0);


        // Returning Json
        return response()->json($hashData);
    }

    /**
     * Display the specified resource.
     *
     * @param  mixed  $menu
     * @return \Illuminate\Http\Response
     */
    public function show($menu)
    {
        $objItemFactory = new Item();
        $hashData = $objItemFactory->getMenuItems($menu);

        // Returning Json
        return response()->json($hashData);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  mixed  $menu
     * @return \Illuminate\Http\Response
     */
    public function destroy($menu)
    {
        DB::table('items')->where('menu_id','=',$menu)->delete();
    }
}
