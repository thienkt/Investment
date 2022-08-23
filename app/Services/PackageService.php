<?php

namespace App\Services;

use App\Http\Resources\PackageCollection;
use App\Models\Package;
use App\Repositories\PackageRepository;
use Exception;
use Illuminate\Support\Facades\Auth;

class PackageService extends BaseService
{
    protected $package;

    public function __construct(PackageRepository $package)
    {
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

    public function store($data)
    {
        $package = $this->package->store($data);
        return $package;
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
                ->where('user_package.owner_id', Auth::id())
                ->orderBy('user_package.id')
                ->get();

            return $this->ok(new PackageCollection($packages));
        } catch (Exception $e) {
            return $this->error($e);
        }
    }
}
