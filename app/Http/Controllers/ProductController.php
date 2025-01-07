<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        return response()->json([
            'message' => 'success',
            'data' => Product::get()
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|integer',
            // 'image' => 'nullable|string',
        ]);

        $file_name = null;

        if ($request->file('image')) {
            $file_name = $request->name.'.'.$request->file('image')->getClientOriginalExtension();
            $request->file('image')->storeAs('image', $file_name);
        }

        $product = Product::create($request->except('image'));

        $file_name? $product->update(['image' => $file_name]):null;

        return response()->json([
            'message' => 'Success',
            'data' => $product,
            // 'image' =>
        ], 200);
    }

    public function show($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => ['Product not found', $product]], 404);
        }

        return response()->json($product, 200);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'price' => 'sometimes|required|integer',
            // 'image' => 'nullable|string',
        ]);

        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $product->update($request->except('image'));

        if ($request->file('image')) {
            $product->image? Storage::delete('image/'.$product->image):null;

            $file_name = $request->name.'.'.$request->file('image')->getClientOriginalExtension();
            $request->file('image')->storeAs('image', $file_name);
            $product->update(['image' => $file_name]);
        }



        return response()->json($product, 200);
    }

    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $product->image? Storage::delete('image/'.$product->image):null;

        $product->delete();

        return response()->json(['message' => 'Product deleted'], 200);
    }
}
