<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AngularController extends Controller
{
    public function serveApp()
    {
        return view('index');
    }
    public function unsupported()
    {
        return view('unsupported_browser');
    }
}
