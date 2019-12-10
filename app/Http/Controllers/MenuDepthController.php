<?php

namespace App\Http\Controllers;
use App\Menu;

class MenuDepthController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  mixed  $menu
     * @return \Illuminate\Http\Response
     */
    public function show($menu)
    {
        // Return depth of a Menu
        $objMenuFactory = new Menu();
        $intCount = $objMenuFactory->getMenusDepth($menu);

        return response()->json([
            'depth' => $intCount
        ]);
    }
}
