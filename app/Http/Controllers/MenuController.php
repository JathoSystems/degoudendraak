<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function menukaart(){
        $menuItems = Menu::orderBy('soortgerecht')
            ->orderBy('menunummer')
            ->orderBy('menu_toevoeging')
            ->get();

        return view('menu.menukaart', [
            'menuItems' => $menuItems
        ]);
    }

    /**
     * Display a listing of the menu items.
     */
    public function index()
    {
        $menuItems = Menu::orderBy('soortgerecht')
                        ->orderBy('menunummer')
                        ->orderBy('menu_toevoeging')
                        ->get();

        return view('menu.index', [
            'menuItems' => $menuItems
        ]);
    }

    /**
     * Show the form for creating a new menu item.
     */
    public function create()
    {
        return view('menu.create');
    }

    /**
     * Store a newly created menu item in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'naam' => 'required|string|max:255',
            'menunummer' => 'nullable|integer',
            'menu_toevoeging' => 'nullable|string|max:10',
            'price' => 'required|numeric|min:0',
            'soortgerecht' => 'required|string|max:100',
            'beschrijving' => 'nullable|string',
        ]);

        Menu::create($validated);

        return redirect()->route('menu.index')
            ->with('success', 'Menu item is toegevoegd.');
    }

    /**
     * Display the specified menu item.
     */
    public function show(string $id)
    {
        $menuItem = Menu::findOrFail($id);

        return view('menu.show', [
            'menuItem' => $menuItem
        ]);
    }

    /**
     * Show the form for editing the specified menu item.
     */
    public function edit(string $id)
    {
        $menuItem = Menu::findOrFail($id);

        return view('menu.edit', [
            'menuItem' => $menuItem
        ]);
    }

    /**
     * Update the specified menu item in storage.
     */
    public function update(Request $request, string $id)
    {
        $menuItem = Menu::findOrFail($id);

        $validated = $request->validate([
            'naam' => 'required|string|max:255',
            'menunummer' => 'nullable|integer',
            'menu_toevoeging' => 'nullable|string|max:10',
            'price' => 'required|numeric|min:0',
            'soortgerecht' => 'required|string|max:100',
            'beschrijving' => 'nullable|string',
        ]);

        $menuItem->update($validated);

        return redirect()->route('menu.index')
            ->with('success', 'Menu item is bijgewerkt.');
    }

    /**
     * Remove the specified menu item from storage.
     */
    public function destroy(string $id)
    {
        $menuItem = Menu::findOrFail($id);
        $menuItem->delete();

        return redirect()->route('menu.index')
            ->with('success', 'Menu item is verwijderd.');
    }

    /**
     * Get menu items for the cash desk
     */
    public function getMenuForCashDesk()
    {
        $menuItems = Menu::orderBy('soortgerecht')
                    ->orderBy('menunummer')
                    ->orderBy('menu_toevoeging')
                    ->get();

        return view('kassa.menu', [
            'menuItems' => $menuItems
        ]);
    }
}
