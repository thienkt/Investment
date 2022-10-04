<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePackageAvatarRequest;
use App\Http\Requests\CreatePackageRequest;
use App\Http\Requests\CreateTransactionRequest;
use App\Http\Requests\PackageIdNeededRequest;
use App\Services\BankService;
use App\Services\PackageService;
use App\Services\TransactionService;
use Exception;
use Illuminate\Support\Facades\Auth;

class PackageController extends Controller
{
    protected $package;
    protected $transaction;
    protected $bank;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(PackageService $package, TransactionService $transaction, BankService $bank)
    {
        $this->package = $package;
        $this->transaction = $transaction;
        $this->bank = $bank;
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

    public function createTransaction($packageId, CreateTransactionRequest $request)
    {
        $ref = $this->transaction->create(Auth::id(), $packageId, $request->amount);

        if (!$ref) {
            return $this->package->error(new Exception('Create transaction failed'));
        }

        $bankInfo = $this->bank->getBankInfo();

        return $this->transaction->ok(
            array_merge(
                $bankInfo,
                [
                    'reference_number' => $ref,
                    'transfer_amount' => number_format($request->amount)
                ]
            )
        );
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
