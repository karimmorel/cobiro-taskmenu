<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Menu;

class MenuController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Getting Data
        $strData = $request->getContent();
        $objData = json_decode($strData);
        $strField = $objData->field;
        $intMaxDepth = $objData->max_depth;
        $intMaxChildren = $objData->max_children;

        // Creating Object / Set Default Values / Persist Data
        $objMenu = new Menu();
        $objMenu->field = $strField;
        $objMenu->max_depth = (!$intMaxDepth) ? 5 : $intMaxDepth;
        $objMenu->max_children = (!$intMaxChildren) ? 5 : $intMaxChildren;
        $objMenu->save();

        // Returning Json
        return response()->json([
            'field' => $objMenu->field,
            'max_depth' => $objMenu->max_depth,
            'max_children' => $objMenu->max_children
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  mixed  $menu
     * @return \Illuminate\Http\Response
     */
    public function show($menu)
    {
        // Find Menu
        $objMenu = Menu::find($menu);

        // Returning Json (Error if doesn't exists)
        if(!$objMenu)
        {
            return response()->json([
            'error' => 'Menu '.$menu.' doesn\'t exists.'
        ]);
        }
        else
        {
            return response()->json([
            'field' => $objMenu->field,
            'max_depth' => $objMenu->max_depth,
            'max_children' => $objMenu->max_children
        ]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $menu
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $menu)
    {
        // Find Menu
        $objMenu = Menu::find($menu);

        // Returning Json if doesn't exists
        if(!$objMenu)
        {
            return response()->json([
            'error' => 'Menu '.$menu.' doesn\'t exists.'
        ]);
        }

        // Getting Data
        $strData = $request->getContent();
        $objData = json_decode($strData);
        $strField = $objData->field;
        $intMaxDepth = $objData->max_depth;
        $intMaxChildren = $objData->max_children;

        // Update Values
        $objMenu->field = $strField;
        $objMenu->max_depth = (!$intMaxDepth) ? 5 : $intMaxDepth;
        $objMenu->max_children = (!$intMaxChildren) ? 5 : $intMaxChildren;
        $objMenu->save();

        // Returning Json
        return response()->json([
            'field' => $objMenu->field,
            'max_depth' => $objMenu->max_depth,
            'max_children' => $objMenu->max_children
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  mixed  $menu
     * @return \Illuminate\Http\Response
     */
    public function destroy($menu)
    {
        // Find Menu
        $objMenu = Menu::find($menu);

        // Delete if Menu found
        if($objMenu)
        {
            $objMenu->delete();
        }
    }
}
