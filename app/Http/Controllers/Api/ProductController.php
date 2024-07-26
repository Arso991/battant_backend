<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    //
    public function index()
    {
        try {
            //code...
            $products = Product::all();
            return response()->json(['data' => $products], 200);
        } catch (Exception $e) {
            //throw $th;
        }
    }

    public function show($id)
    {
        try {
            //code...
            $product = Product::find($id);
            if (!$product) {
                return response()->json(['message' => 'Product not found'], 404);
            }
            return response()->json(['data' => $product], 200);
        } catch (Exception $e) {
            //throw $th;
        }
    }

    public function store(ProductRequest $request)
    {
        try {
            //code...
            $images = [];

            if ($request->hasFile('imageUrl')) {
                foreach ($request->file('imageUrl') as $image) {
                    $path = $image->store('pictures');
                    $images[] = $path;
                }
            }

            //dd($images);

            $product = Product::create([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'size' => $request->size,
                'color' => $request->color,
                'quantity' => $request->quantity,
                'imageUrl' => $images,
                'stock' => $request->stock,
                'category_id' => $request->category_id,
            ]);

            return response()->json(['data' => $product, 'message' => 'Product succesfully added'], 201);
        } catch (Exception $e) {
            //throw $th;
            return response()->json($e);
        }
    }

    public function update(ProductRequest $request, $id)
    {
        try {
            //code...
            //dd($request->all());
            $product = Product::find($id);
            if (!$product) {
                return response()->json(['message' => 'Product not found'], 404);
            }

            $images = $product->images ?? [];

            if ($request->hasFile('imageUrl')) {
                foreach ($request->file('imageUrl') as $image) {
                    $path = $image->store('pictures');
                    if (!in_array($path, $images)) {
                        $images[] = $path;
                    }
                }
            }

            $product->update([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'size' => $request->size,
                'color' => $request->color,
                'quantity' => $request->quantity,
                'imageUrl' => $images,
                'stock' => $request->stock,
                'category_id' => $request->category_id,
            ]);
            return response()->json(['data' => $product, 'message' => 'Product succesfully updated'], 200);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    public function delete($id)
    {
        try {
            //code...
            $product = Product::find($id);
            if (!$product) {
                return response()->json(['message' => 'Collection not found'], 404);
            }

            $product->delete(); // Supprime le produit
            return response()->json(['message' => 'Product deleted'], 204);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }
}
