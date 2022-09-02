<?php

namespace App\Services;

use Exception;
use App\Http\Resources\PackageCollection;
use App\Http\Resources\PackageResource;
use App\Models\Package;
use App\Repositories\PackageRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PackageService extends BaseService
{
    protected $package;
    protected $store;

    public function __construct(PackageRepository $package, FirebaseService $store)
    {
        $this->store = $store;
        $this->package = $package;
    }

    public function index()
    {
        try {
            $packages = $this->package->index();

            return $this->ok($packages);
        } catch (Exception $e) {
            return $this->error($e);
        }
    }

    public function create($data)
    {
        DB::beginTransaction();
        try {
            $package = $this->package->store(['name' => $data->name]);

            if (!$package) {
                throw new Exception('An error has occurred');
            }

            $package->owners()->attach([
                Auth::id() => [
                    'investment_amount' => 0,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            ]);

            $funds = [];

            foreach ($data->allocation as $fund) {
                $funds += [
                    $fund['fund_id'] => [
                        'allocation_percentage' => $fund['percentage'],
                        'created_at' => now(),
                        'updated_at' => now()

                    ]
                ];
            }

            $package->funds()->sync($funds);

            DB::commit();

            return $this->ok("Created", self::HTTP_CREATED);
        } catch (Exception $e) {
            DB::rollBack();

            return $this->error($e);
        }
    }

    public function show($id)
    {
        try {
            $package = $this->package->show($id);

            return $this->ok($package);
        } catch (Exception $e) {
            return $this->error($e, 'The package ID does not exist', self::HTTP_NOT_FOUND);
        }
    }

    public function clone($id)
    {
        try {
            $package = Package::findOrFail($id);

            $package->owners()->attach([
                Auth::id() => [
                    'investment_amount' => 0,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            ]);

            return $this->ok("Created", self::HTTP_CREATED);
        } catch (Exception $e) {
            return $this->error($e, 'An error has occurred');
        }
    }

    public function update($data, $id)
    {
        $package = $this->package->update($data, $id);
        return $package;
    }

    public function destroy($id)
    {
        try {
            $package = Package::find($id);
            $package->owners()->wherePivot('user_id', '=', Auth::id())->detach();

            return $this->ok(null, self::HTTP_NO_CONTENT);
        } catch (\Throwable $th) {
            return $this->error($th, 'An error has occurred');
        }
    }

    public function changeAvatar($userPackageId, $avatar)
    {
        try {
            $userPackage = DB::table('user_package')->where(['user_id' => Auth::id(), 'id' => $userPackageId])->first();

            if (!$userPackage) {
                throw new Exception('Permission denied', self::HTTP_FORBIDDEN);
            }

            $filePath = Config('app.asset_url') . $this->store->upload($avatar);

            $isSuccess = DB::table('user_package')->where(['user_id' => Auth::id(), 'id' => $userPackageId])->update(['avatar' => $filePath]);

            if ($isSuccess) {
                return $this->ok("Success");
            }

            throw new Exception('An error has occurred', self::HTTP_INTERNAL_SERVER_ERROR);
        } catch (Exception $e) {
            dd($e);
            return $this->error($e, $e->getMessage(), $e->getCode());
        }
    }

    public function getDefaultPackages()
    {
        try {
            $packages = Package::default()->orderBy('id')->get();

            return $this->ok(new PackageCollection($packages));
        } catch (Exception $e) {
            return $this->error($e);
        }
    }

    /**
     * @return PackageResource
     */
    public function getPackageDetail($id)
    {
        try {
            $package = Package::findOrFail($id);
            $user = $package->owners()
                ->wherePivot('user_id', '=', Auth::id())
                ->first();
            $package->owner = $user?->pivot;
            return $this->ok(new PackageResource($package));
        } catch (Exception $e) {
            return $this->error($e, 'An error has occurred');
        }
    }

    public function getHistory($id)
    {
        return $this->ok($id);
    }

    public function getCustomizedPackages()
    {
        try {
            $packages = Package::join(
                'user_package',
                'user_package.package_id',
                '=',
                'packages.id'
            )
                ->select('packages.*')
                ->where('user_package.user_id', Auth::id())
                ->orderBy('user_package.id')
                ->get();

            return $this->ok(new PackageCollection($packages));
        } catch (Exception $e) {
            return $this->error($e);
        }
    }
}
