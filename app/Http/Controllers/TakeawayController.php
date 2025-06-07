<?php

namespace App\Http\Controllers;

use App\Models\Takeaway;
use Illuminate\Http\Request;
use App\Models\Menu;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;


class TakeawayController extends Controller
{
    public function index()
    {
        $menuItems = Menu::all()->groupBy('soortgerecht');

        return view('takeaway.index', compact('menuItems'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'pickup_time' => 'required|date|after:now',
            'menu_items' => 'required|array',
            'menu_items.*' => 'required|exists:menu,id',
            'quantities' => 'required|array',
            'quantities.*' => 'required|integer|min:1',
        ]);

        // Bereken totaalprijs
        $totalPrice = 0;
        foreach ($validated['menu_items'] as $index => $menuId) {
            $menuItem = Menu::find($menuId);
            $quantity = $validated['quantities'][$index];
            $totalPrice += $menuItem->price * $quantity;
        }

        // Maak bestelnummer
        $orderNumber = 'TO-' . date('Ymd') . '-' . strtoupper(Str::random(4));

        // Maak de bestelling
        $order = Takeaway::create([
            'order_number' => $orderNumber,
            'customer_name' => $validated['customer_name'],
            'customer_email' => $validated['customer_email'],
            'customer_phone' => $validated['customer_phone'],
            'pickup_time' => $validated['pickup_time'],
            'total_price' => $totalPrice,
            'status' => 'pending',
        ]);

        // Voeg menu-items toe aan bestelling
        foreach ($validated['menu_items'] as $index => $menuId) {
            $order->menuItems()->attach($menuId, [
                'quantity' => $validated['quantities'][$index]
            ]);
        }

        return redirect()->route('takeaway.thank-you', $order);
    }

    public function thankYou(Takeaway $order)
    {
        // Verzamel gegevens voor QR-code
        $qrData = [
            'order_number' => $order->order_number,
            'customer_name' => $order->customer_name,
            'items' => $order->menuItems->map(function ($item) {
                return [
                    'menu_number' => $item->menunummer,
                    'menu_name' => $item->naam,
                    'quantity' => $item->pivot->quantity
                ];
            })
        ];

        // Maak QR-code
        $qrCode = QrCode::size(250)
            ->generate(json_encode($qrData));

        return view('takeaway.thank-you', compact('order', 'qrCode'));
    }
}
