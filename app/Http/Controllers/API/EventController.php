<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\Event;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class EventController extends Controller
{
    //
    public function index(Request $request)
    {
        try {
            $events = Event::get();
            return response()->json([
                "data" => $events
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $error) {
            return response()->json([
                "message" => __("Event Error")
            ], 400);
        }
    }

    public function show($id)
    {
        try {
            $event = Event::findOrFail($id);
            return response()->json([
                "data" => $event
            ], 200);
        } catch (Exception $error) {
            return response()->json([
                "message" => __("Event not Found")
            ], 400);
        }
    }

    public function create(Request $request)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'name' => 'required|string',
                ],
            );
            if ($validator->fails()) {

                return response()->json([
                    "message" => $this->readalbeError($validator),
                ], 400);
            }
            $event = new Event;
            $event->name = $request->name;
            $event->slug = $this->createSlug($request->name);
            $event->save();
            return response()->json([
                "data" => $event
            ], 200);
        } catch (Exception $error) {
            return response()->json([
                "message" => __("Event not Found")
            ], 400);
        }
    }
    public function createOrUpdate(Request $request, $id)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'name' => 'required|string',
                ],
            );
            if ($validator->fails()) {

                return response()->json([
                    "message" => $this->readalbeError($validator),
                ], 400);
            }
            $event = Event::updateOrCreate([
                'id' => $id
            ], [
                'name' => $request->name,
                'slug' =>  $this->createSlug($request->name),
            ]);
            return response()->json([
                "data" => $event
            ], 200);
        } catch (Exception $error) {
            return response()->json([
                "message" => __("Event not Found")
            ], 400);
        }
    }
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'name' => 'required|string',
                ],
            );
            if ($validator->fails()) {

                return response()->json([
                    "message" => $this->readalbeError($validator),
                ], 400);
            }
            $event = Event::findOrFail($id);
            $event->name = $request->name;
            $event->slug = $this->modifySlug($event, $request->name);
            $event->save();
            return response()->json([
                "data" => $event
            ], 200);
        } catch (Exception $error) {
            return response()->json([
                "message" => __("Event not Found")
            ], 400);
        }
    }

    public function destroy($id)
    {
        try {
            $event = Event::findOrFail($id);
            $event->delete();
            return response()->json([
                "message" => 'Event deleted successfully'
            ], 200);
        } catch (Exception $error) {
            return response()->json([
                "message" => __("Event not Found")
            ], 400);
        }
    }

    public function createSlug($name)
    {
        $eventID = Event::orderBy('id', 'desc')->first()->id;
        if (Event::whereSlug($slug = str_slug($name))->exists()) {
            $slug = str_slug($name) . $eventID + 1;
        } else {
            $slug = str_slug($name);
        }
        return $slug;
    }
    public function modifySlug(Event $event, $name)
    {
        if (Event::where('id', '!=', $event->id)->whereSlug($slug = str_slug($name))->exists()) {
            $slug = str_slug($name) . $event->id;
        } else {
            $slug = str_slug($name);
        }
        return $slug;
    }
}
