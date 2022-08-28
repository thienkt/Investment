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

            $package->owners()->sync([
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

            $newPackage = $package->join('user_package', 'packages.id', '=', 'user_package.package_id')
                ->where('user_id', Auth::id())
                ->orderBy('user_package.created_at', 'desc')
                ->firstOrFail();

            return $this->ok(new PackageResource($newPackage));
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
            return $this->error($e, 'The package ID does not exist', BaseService::HTTP_NOT_FOUND);
        }
    }

    public function update($data, $id)
    {
        $package = $this->package->update($data, $id);
        return $package;
    }

    public function destroy($id)
    {
        $package = $this->package->destroy($id);
        return $package;
    }

    public function changeAvatar($userPackageId, $avatar)
    {
        try {
            $userPackage = DB::table('user_package')->where(['user_id' => Auth::id(), 'id' => $userPackageId])->first();

            if (!$userPackage) {
                throw new Exception('Permission denied', BaseService::HTTP_FORBIDDEN);
            }

            $filePath = Config('app.asset_url') . $this->store->upload($avatar);

            $isSuccess = DB::table('user_package')->where(['user_id' => Auth::id(), 'id' => $userPackageId])->update(['avatar' => $filePath]);

            if ($isSuccess) {
                return $this->ok("Success");
            }

            throw new Exception('An error has occurred', BaseService::HTTP_INTERNAL_SERVER_ERROR);
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
