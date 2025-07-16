<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index()
    {
        return auth()->user()->orders()
            ->with(['orderItems.product'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'shipping_address' => 'required|array',
            'shipping_address.full_name' => 'required|string',
            'shipping_address.phone' => 'required|string',
            'shipping_address.address' => 'required|string',
            'shipping_address.city' => 'required|string',
            'shipping_address.postal_code' => 'required|string',
            'shipping_address.province' => 'required|string',
        ]);

        $totalAmount = 0;
        $orderItems = [];

        foreach ($request->items as $item) {
            $product = Product::find($item['product_id']);
            $itemTotal = $product->price * $item['quantity'];
            $totalAmount += $itemTotal;

            $orderItems[] = [
                'product_id' => $product->id,
                'quantity' => $item['quantity'],
                'price' => $product->price,
            ];
        }

        $order = Order::create([
            'user_id' => auth()->id(),
            'total_amount' => $totalAmount,
            'status' => 'pending',
            'shipping_address' => $request->shipping_address,
            'tracking_number' => 'TRK-' . strtoupper(Str::random(8)),
        ]);

        foreach ($orderItems as $item) {
            $order->orderItems()->create($item);
        }

        return $order->load(['orderItems.product']);
    }

    public function show(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return $order->load(['orderItems.product']);
    }
}
