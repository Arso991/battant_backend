<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CollectionRequest;
use App\Models\Collection;
use Exception;
use Illuminate\Support\Facades\Storage;

class CollectionController extends Controller
{
    //
    public function index()
    {
        try {
            $collections = Collection::with('categories')->get();

            return response()->json(['data' => $collections], 200);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    public function store(CollectionRequest $request)
    {
        try {

            $image = null;

            if ($request->hasFile('image')) {
                $image = $request->file('image')->store('pictures');
            }
            // Création de la collection
            $collection = Collection::create([
                "name" => $request['name'],
                "image" => $image,
                "description" => $request['description'],
            ]);

            if ($request->has('categories')) {
                foreach ($request->categories as $category) {
                    $collection->categories()->attach(
                        $category['category_id']
                    );
                }
            }
            return response()->json(['collection' => $collection, 'data' => $collection->load('categories'), 'message' => 'Collection succesfully added'], 201);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    public function show($id)
    {
        try {
            //code...
            $collection = Collection::with('categories')->find($id);
            if (!$collection) {
                return response()->json(['message' => 'Collection not found'], 404);
            }

            return response()->json(['data' => $collection], 200);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    public function update(CollectionRequest $request, $id)
    {
        try {
            //code...
            $collection = Collection::find($id);

            if (!$collection) {
                return response()->json(['message' => 'Collection not found'], 404);
            }

            $image = null;
            if ($request->hasFile('image')) {
                if ($collection->image) {
                    Storage::delete($collection->image);
                }
                $image = $request->file('image')->store('pictures');
            }
            // Mise à jour de la collection
            $collection->update([
                "name" => $request['name'],
                "image" => $image,
                "description" => $request['description'],
            ]);

            if ($request->has('categories')) {
                $collection->categories()->detach();

                foreach ($request->categories as $category) {
                    $collection->categories()->attach($category['category_id']);
                }
            }

            return response()->json(['message' => 'Collection successfully updated', 'data' => $collection->load('categories')], 200);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    public function delete($id)
    {
        try {
            //code...
            $collection = Collection::find($id);
            if (!$collection) {
                return response()->json(['message' => 'Collection not found'], 404);
            }
            $collection->categories()->detach(); // Supprime les relations dans la table pivot
            $collection->delete(); // Supprime la collection

            return response()->json(['message' => 'Collection deleted'], 204);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }
}
