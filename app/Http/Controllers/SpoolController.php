<?php

namespace App\Http\Controllers;

use App\Jobs\SpoolHNData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class SpoolController extends Controller
{
    public function spoolData()
    {
        try {

            dispatch(new SpoolHNData());

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        } 
    }
}
