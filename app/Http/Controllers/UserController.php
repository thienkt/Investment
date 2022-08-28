<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Http\Resources\UserStatusResource;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getUserInfo(Request $request)
    {
        return response()->json(new UserResource($request->user()));
    }

    public function getUserStatus(Request $request)
    {
        return response()->json(new UserStatusResource($request->user()));
    }
}
