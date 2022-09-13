<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadImageRequest;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserStatusResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $user;

    public function __construct(UserService $user)
    {
        $this->user = $user;
    }

    public function getUserInfo(Request $request)
    {
        dd(User::with('userPackage')->where('id', $request->user()->id)->first());
        // return response()->json(new UserResource($request->user()));
    }

    public function getUserStatus(Request $request)
    {
        return response()->json(new UserStatusResource($request->user()));
    }

    public function changeAvatar(UploadImageRequest $request)
    {
        return $this->user->changeAvatar($request->validated('image'));
    }
}
