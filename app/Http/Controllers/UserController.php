<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadImageRequest;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserStatusResource;
use App\Models\User;
use App\Services\BaseService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    protected $user;

    public function __construct(UserService $user)
    {
        $this->user = $user;
    }

    public function getUserInfo(Request $request)
    {
        return new UserResource($request->user());
    }

    public function getAssetInfo(Request $request)
    {
        return $this->user->getAssetInfo();
    }

    public function getUserStatus(Request $request)
    {
        $userStatus = new UserStatusResource($request->user());

        return $this->user->ok($userStatus);
    }

    public function changeAvatar(UploadImageRequest $request)
    {
        return $this->user->changeAvatar($request->validated('image'));
    }

    public function uploadFrontIdentityImage(UploadImageRequest $request)
    {
        try {
            $frontIdCardImage = $request->validated('image');

            $response = $this->user->uploadEKycImage($frontIdCardImage);

            $currentUser = User::find(Auth::id());

            $currentUser->identity_image_front = $response['path'];

            $currentUser->identity_image_front_hash = $response['hash'];

            $currentUser->save();

            return new UserResource($currentUser);
        } catch (\Throwable $th) {
            return $this->user->error($th, BaseService::HTTP_INTERNAL_SERVER_ERROR, 'Image upload failure');
        }
    }

    public function uploadBackIdentityImage(UploadImageRequest $request)
    {
        try {
            $frontIdCardImage = $request->validated('image');

            $response = $this->user->uploadEKycImage($frontIdCardImage);

            $currentUser = User::find(Auth::id());

            $currentUser->identity_image_back = $response['path'];

            $currentUser->identity_image_back_hash = $response['hash'];

            $currentUser->save();

            return new UserResource($currentUser);
        } catch (\Throwable $th) {
            return $this->user->error($th, BaseService::HTTP_INTERNAL_SERVER_ERROR, 'Image upload failure');
        }
    }

    public function checkIdentityCardImage()
    {
        return $this->user->checkIdentityCardImage(Auth::user());
    }
}
