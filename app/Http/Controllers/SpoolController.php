<?php

namespace App\Http\Controllers;

use App\Jobs\SpoolHNData;
use Illuminate\Http\Request;

class SpoolController extends Controller
{
    public function spoolData()
    {
        dispatch(new SpoolHNData());
    }

}
