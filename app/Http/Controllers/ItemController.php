<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Item;

class ItemController extends Controller
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

        // Creating Object / Set Values / Persist Data
        $objItem = new Item();
        $objItem->field = $strField;
        $objItem->save();

        // Returning Json
        return response()->json([
            'field' => $objItem->field
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  mixed  $item
     * @return \Illuminate\Http\Response
     */
    public function show($item)
    {
        // Find Item
        $objItem = Item::find($item);

        // Returning Json (Error if doesn't exists)
        if(!$objItem)
        {
            return response()->json([
            'error' => 'Item '.$item.' doesn\'t exists.'
        ]);
        }
        else
        {
            return response()->json([
            'field' => $objItem->field
        ]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $item
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $item)
    {
        // Find Item
        $objItem = Item::find($item);

        // Returning Json if doesn't exists
        if(!$objItem)
        {
            return response()->json([
            'error' => 'Item '.$item.' doesn\'t exists.'
        ]);
        }

        // Getting Data
        $strData = $request->getContent();
        $objData = json_decode($strData);
        $strField = $objData->field;

        // Update Values
        $objItem->field = $strField;
        $objItem->save();

        // Returning Json
        return response()->json([
            'field' => $objItem->field
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  mixed  $item
     * @return \Illuminate\Http\Response
     */
    public function destroy($item)
    {
        // Find Item
        $objItem = Item::find($item);

        // Delete if Menu found
        if($objItem)
        {
            $objItem->delete();
        }
    }
}
