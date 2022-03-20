<?php

namespace App\Http\Controllers\Seller;

use App\Seller;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class SellerTransactionController extends ApiController
{
    public function __construct()
    {
//        parent::__construct();
//        $this->middleware('scope:read-general')->only('index');
//        $this->middleware('can:view,seller')->only('index');
    }

    /**
     * Display a listing of the resource.
     *
     * @param Seller $seller
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Seller $seller)
    {
        //return a list of transactions for a seller
        $transactions = $seller->products()
            ->whereHas('transactions')//ensure products fetched have transactions
            ->with('transactions')
            ->get()
            ->pluck('transactions')
            ->collapse()
        ;

        return $this->showAll($transactions);
    }
}
