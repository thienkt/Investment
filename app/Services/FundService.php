<?php

namespace App\Services;

use App\Http\Resources\FundCollection;
use App\Http\Resources\FundResource;
use App\Repositories\FundRepository;
use Exception;

class FundService extends BaseService
{
    protected $fund;
    protected $vendor;

    public function __construct(FundRepository $fund, VendorService $vendor)
    {
        $this->fund = $fund;
        $this->vendor = $vendor;
    }

    public function index()
    {
        try {
            $funds = $this->fund->index();

            return $this->ok(new FundCollection($funds));
        } catch (Exception $e) {
            return $this->error($e);
        }
    }

    public function store($data)
    {
        $fund = $this->fund->store($data);
        return $fund;
    }

    public function show($id)
    {
        try {
            $fund = $this->fund->show($id);

            return $this->ok(new FundResource($fund));
        } catch (Exception $e) {
            return $this->error($e, BaseService::HTTP_NOT_FOUND, 'The fund ID does not exist');
        }
    }

    public function update($data, $id)
    {
        $fund = $this->fund->update($data, $id);
        return $fund;
    }

    public function destroy($id)
    {
        $fund = $this->fund->destroy($id);
        return $fund;
    }

    public function getHistory($id)
    {
        try {
            $fund = $this->fund->show($id);
            $credential = $fund->credential;
            $token = $this->vendor->getCredential($credential->id);
            $history = $this->vendor->get($fund->historical_data_url, [
                'headers' => ['Authorization' => 'Bearer ' . $token],
            ]);

            return $this->ok($history->value);
        } catch (Exception $e) {
            return $this->error($e);
        }
    }
}
