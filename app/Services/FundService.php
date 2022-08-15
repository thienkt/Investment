<?php

namespace App\Services;

use App\Http\Resources\FundCollection;
use App\Http\Resources\FundResource;
use App\Repositories\FundRepository;
use Database\Seeders\FundSeeder;
use Exception;

class FundService extends BaseService
{
    protected $fund;

    public function __construct(FundRepository $fund)
    {
        $this->fund = $fund;
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
            return $this->error($e, 'The fund ID does not exist', BaseService::HTTP_NOT_FOUND);
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
}
