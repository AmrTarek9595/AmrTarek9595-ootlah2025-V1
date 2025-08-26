<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class SharedController extends Controller
{
public function me()
{
    return response()->json([
        'user' => Auth::user()->load('roles')
    ], 200);
}
}

