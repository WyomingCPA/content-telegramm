<?php

namespace App\Http\Controllers\queue;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

class QueueController extends Controller
{
    public function set(Request $request)
    {
        $rows = $request->ids;
        $select = [];

        foreach ($rows as $value) {
            $select[] = $value;
        }
        Auth::user()->queuesPost()->attach(array_values($select));
    }

    public function unset(Request $request)
    {
        $rows = $request->ids;
        $select = [];
        foreach ($rows as $value) {
            $select[] = $value;
        }

        Auth::user()->queuesPost()->detach(array_values($select));

    }
}
