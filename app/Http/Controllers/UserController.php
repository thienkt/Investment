<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getUserInfo(Request $request)
    {
        return response()->json(new UserResource($request->user()));
    }
}
