<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserAsset;
use Exception;
use Illuminate\Support\Facades\Auth;

class UserService extends BaseService
{
    protected $store;

    public function __construct(FirebaseService $store)
    {
        $this->store = $store;
    }

    public function changeAvatar($image)
    {
        try {
            $filePath = Config('app.asset_url') . $this->store->upload($image);
            $user = User::find(Auth::id());
            $user->avatar = $filePath;
            $user->save();

            return $this->ok('Success');
        } catch (Exception $e) {
            return $this->error($e);
        }
    }

    public function getAssetInfo(User $user) {
        $userPackages = $user->userPackages;

        $userPackageIds = [];

        foreach ($userPackages as $key => $value) {
            array_push($userPackageIds, $value->id);
        }
        $asset = UserAsset::whereIn('user_package_id', $userPackageIds)->get();
        echo json_encode($userPackages);dd();
        // echo json_encode($asset);
        // dd(User::with('userPackage')->where('id', $request->user()->id)->first());
        // return response()->json(new UserResource($request->user()));
    }
}
