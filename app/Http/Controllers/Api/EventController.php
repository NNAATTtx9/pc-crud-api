<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;

class EventController extends Controller
{
   public function show()
   {
       $event = Event::where('is_active', true)->first();

       if (! $event) {
           return response()->json(["error" => "Event not found"], 404);
       }
       return response()->json($event->toArrayPublic(), 200);
   }
}
