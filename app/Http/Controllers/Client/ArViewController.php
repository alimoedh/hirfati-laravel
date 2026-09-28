<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;

class ArViewController extends Controller
{
    public function index()
    {
        return view('client.ar-view');
    }
}
