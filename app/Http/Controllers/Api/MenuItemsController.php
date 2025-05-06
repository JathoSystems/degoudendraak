<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;

class MenuItemsController extends Controller
{
    /**
     * Return all menu items for the cash desk application
     */
    public function index()
    {
        $menuItems = Menu::orderBy('menunummer')
                    ->orderBy('menu_toevoeging')
                    ->get();

        return response()->json($menuItems);
    }
}
