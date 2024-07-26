<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\EventRequest;
use App\Models\Event;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    //
    public function index()
    {
        try {
            //code...
            $events = Event::all();
            return response()->json(['data' => $events], 200);
        } catch (Exception $e) {
            //throw $th;
            return response()->json($e);
        }
    }

    public function show($id)
    {
        try {
            //code...
            $event = Event::find($id);
            if (!$event) {
                return response()->json(['message' => 'Event not found'], 404);
            }
            return response()->json(['data' => $event], 200);
        } catch (Exception $e) {
            //throw $th;
            return response()->json($e);
        }
    }

    public function store(EventRequest $request)
    {
        try {
            //code...
            $image = null;

            if ($request->hasFile('image')) {
                $image = $request->file('image')->store('pictures');
            }

            $event = Event::create([
                'name' => $request->name,
                'description' => $request->description,
                'image' => $image,
                'price' => $request->price,
                'country' => $request->country,
                'address' => $request->address,
                'startDate' => $request->startDate,
                'endDate' => $request->endDate,
            ]);

            return response()->json(['data' => $event, 'message' => 'Event succesfully added'], 201);
        } catch (Exception $e) {
            //throw $th;
            return response()->json($e);
        }
    }

    public function update(EventRequest $request, $id)
    {
        try {
            //code...
            $event = Event::find($id);
            if (!$event) {
                return response()->json(['message' => 'Event not found'], 404);
            }

            $image = null;

            if ($request->hasFile('image')) {
                if ($event->image) {
                    Storage::delete($event->image);
                }
                $image = $request->file('image')->store('pictures');
            }

            $event->update([
                'name' => $request->name,
                'description' => $request->description,
                'image' => $image,
                'price' => $request->price,
                'country' => $request->country,
                'address' => $request->address,
                'startDate' => $request->startDate,
                'endDate' => $request->endDate,
            ]);
            return response()->json(['message' => 'Event successfully updated', 'data' => $event], 200);
        } catch (Exception $e) {
            //throw $th;
            return response()->json($e);
        }
    }

    public function delete($id)
    {
        try {
            //code...
            $event = Event::find($id);
            if (!$event) {
                return response()->json(['message' => 'Event not found'], 404);
            }
            $event->delete();
            return response()->json(['message' => 'Event deleted'], 204);
        } catch (Exception $e) {
            //throw $th;
            return response()->json($e);
        }
    }
}
