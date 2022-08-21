<?php

namespace App\Http\Controllers;

use App\Services\FundService;
use Illuminate\Http\Request;

class FundController extends Controller
{
    protected $fund;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(FundService $fund)
    {
        // $this->middleware('auth:sanctum');
        $this->fund = $fund;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->fund->index();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return $this->fund->store($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->fund->show($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        return $this->fund->update($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return $this->fund->destroy($id);
    }

    public function getHistory($id)
    {
        return $this->fund->getHistory($id);
    }
}
