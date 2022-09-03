<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePackageAvatarRequest;
use App\Http\Requests\CreatePackageRequest;
use App\Http\Requests\PackageIdNeededRequest;
use App\Services\PackageService;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    protected $package;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(PackageService $package)
    {
        $this->package = $package;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getDefaultPackages()
    {
        return $this->package->getDefaultPackages();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getCustomizedPackages()
    {
        return $this->package->getCustomizedPackages();
    }

    public function getPackageDetail(PackageIdNeededRequest $request)
    {
        return $this->package->getPackageDetail($request->id);
    }

    /**
     * @QAparam month integer nullable
     */
    public function getHistory(PackageIdNeededRequest $request)
    {
        return $this->package->getHistory($request->id, $request->month);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function clone(PackageIdNeededRequest $request)
    {
        return $this->package->clone($request->id);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(CreatePackageRequest $request)
    {
        return $this->package->create($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->package->show($id);
    }

    public function changeAvatar(ChangePackageAvatarRequest $request)
    {
        return $this->package->changeAvatar($request->id, $request->avatar);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CreatePackageRequest $request, $id)
    {
        return $this->package->update($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(PackageIdNeededRequest $request)
    {
        return $this->package->destroy($request->id);
    }
}
