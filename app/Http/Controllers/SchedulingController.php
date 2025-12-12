<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;

class SchedulingController extends Controller
{
    private function getCart()
    {
        $user = Auth::user();
        if ($user) {
            $cart = Cart::firstOrCreate(['user_id' => $user->id]);
        } else {
            $sessionId = session()->getId();
            $cart = Cart::firstOrCreate(['session_id' => $sessionId]);
        }
        return $cart;
    }

    public function index()
    {
        $cart = $this->getCart();
        $cart->load('items.service');

        // Filter items that require scheduling
        $itemsInit = $cart->items->filter(function ($item) {
            return $item->service->requires_scheduling;
        });

        // Re-index simple array for frontend
        $items = $itemsInit->values();

        if ($items->isEmpty()) {
            return redirect()->route('checkout.index');
        }

        return Inertia::render('Shop/Scheduling', [
            'items' => $items
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'appointments' => 'required|array',
            'appointments.*.date' => 'required|date|after_or_equal:today',
            'appointments.*.time' => 'required',
        ]);

        // Store scheduling data in session to be picked up by Checkout
        session(['booking_appointments' => $request->appointments]);

        return redirect()->route('checkout.index');
    }
}
