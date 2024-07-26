<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Exception;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    //
    public function index()
    {
        try {
            //code...
            $categories = Category::with('collections')->with('products')->get();
            return response()->json(['data' => $categories], 200);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    public function store(CategoryRequest $request)
    {
        try {
            //code...
            $category = Category::create($request->all());

            return response()->json(['data' => $category, 'message' => 'Category added successfully'], 201);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    public function show($id)
    {
        try {
            //code...
            $category = Category::with('collections')->with('products')->find($id);

            if (!$category) {
                return response()->json(['message' => 'Category not found'], 404);
            }

            return response()->json(['data' => $category], 200);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    public function update(CategoryRequest $request, $id)
    {
        try {
            //code...
            $category = Category::find($id);
            if (!$category) {
                return response()->json(['message' => 'Category not found'], 404);
            }
            $category->update($request->all());
            return response()->json(['data' => $category, 'message' => 'Category successfully updated'], 200);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    public function delete($id)
    {
        try {
            //code...
            $category = Category::find($id);
            if (!$category) {
                return response()->json(['message' => 'Category not found'], 404);
            }
            $category->collections()->detach(); // Supprime les relations dans la table pivot
            $category->delete(); // Supprime la catégorie

            return response()->json(['message' => 'Category deleted'], 204);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }
}
