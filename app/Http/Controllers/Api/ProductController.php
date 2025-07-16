<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');

        if ($request->has('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        return $query->paginate(12);
    }

    public function show($slug)
    {
        $product = Product::where('slug', $slug)->with('category')->first();

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json($product);
    }

    public function featured()
    {
        return Product::where('is_featured', true)->take(5)->get();
    }
}
