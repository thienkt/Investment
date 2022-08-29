<?php

namespace App\Services;

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
            Auth::user()->avatar = $filePath;
            Auth::user()->save();

            return $this->ok('Success');
        } catch (Exception $e) {
            return $this->error($e);
        }
    }
}
