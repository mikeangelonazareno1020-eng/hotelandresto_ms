<?php

namespace App\Http\Controllers;

use App\Models\RestoMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ManagerRestoController extends Controller
{

    public function dashboardIndex()
    {
        return view('manager_resto.restoDashboard');
    }

    // Inventory page: show menu cards
    public function menu()
    {
        $menus = RestoMenu::orderBy('category')->orderBy('name')->get([
            'id', 'menu_id', 'name', 'description', 'price', 'category', 'stock_quantity', 'is_available', 'image_url'
        ]);

        return view('manager_resto.restoMenu', compact('menus'));
    }

    // Add stock to a menu item
    public function addStock(Request $request, RestoMenu $menu)
    {
        $data = $request->validate([
            'amount' => ['required', 'integer', 'min:1', 'max:100000'],
        ]);

        $menu->stock_quantity = (int) $menu->stock_quantity + (int) $data['amount'];
        $menu->save();

        return back()->with('success', "Added {$data['amount']} stock to '{$menu->name}'.");
    }

    // Remove stock from a menu item
    public function removeStock(Request $request, RestoMenu $menu)
    {
        $data = $request->validate([
            'amount' => ['required', 'integer', 'min:1', 'max:100000'],
        ]);

        $newQty = max(0, (int) $menu->stock_quantity - (int) $data['amount']);
        $diff = (int) $menu->stock_quantity - $newQty;
        if ($diff <= 0) {
            return back()->with('error', 'No stock removed. Already at zero.');
        }

        $menu->stock_quantity = $newQty;
        $menu->save();

        return back()->with('success', "Removed {$diff} stock from '{$menu->name}'.");
    }

    // Toggle availability by adjusting stock quantity
    public function toggleAvailability(Request $request, RestoMenu $menu)
    {
        if ($menu->is_available) {
            // Mark unavailable by setting stock to 0
            $menu->stock_quantity = 0;
        } else {
            // Mark available by ensuring at least 1 stock
            $set = (int) $request->input('set_stock', 1);
            $menu->stock_quantity = max(1, $set);
        }
        $menu->save();

        return back()->with('success', "Updated availability for '{$menu->name}'.");
    }

    // List products (menu items)
    public function products()
    {
        $menus = RestoMenu::orderBy('category')->orderBy('name')->get([
            'id', 'menu_id', 'name', 'description', 'price', 'category', 'stock_quantity', 'is_available', 'image_url'
        ]);

        return view('manager_resto.restoProducts', compact('menus'));
    }

    // Show edit form for a menu item
    public function edit(RestoMenu $menu)
    {
        return view('manager_resto.restoProductEdit', compact('menu'));
    }

    // Update a menu item
    public function update(Request $request, RestoMenu $menu)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'category' => ['nullable', 'string', 'max:100'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'image_url' => ['nullable', 'string', 'max:255'],
        ]);

        $menu->fill($validated);
        $menu->save();

        return redirect()->route('restomanager.products')->with('success', 'Menu item updated successfully.');
    }

    // Delete a menu item
    public function destroy(RestoMenu $menu)
    {
        $menu->delete();
        return redirect()->route('restomanager.products')->with('success', 'Menu item deleted successfully.');
    }
}
