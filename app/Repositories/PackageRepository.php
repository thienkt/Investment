<?php

namespace App\Repositories;

use App\Models\Package;

class PackageRepository
{
    protected $package;

    public function __construct(Package $package)
    {
        $this->Package = $package;
    }

    public function index()
    {
        $packages = $this->Package::all();
        return $packages;
    }

    public function store($data)
    {
        $package = $this->Package::create($data);
        return $package;
    }

    public function show($id)
    {
        $package = $this->package::findOrFail($id);
        return $package;
    }

    public function update($data, $id)
    {
        $package = $this->Package::findOrFail($id);
        $package->fill($data);
        $package->save();
        return $package;
    }

    public function destroy($id)
    {
        $package = $this->Package::findOrFail($id);
        $package->delete();
        return $package;
    }
}
