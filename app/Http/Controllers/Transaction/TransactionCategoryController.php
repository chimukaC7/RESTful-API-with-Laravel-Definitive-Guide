<?php

namespace App\Http\Controllers\Transaction;

use App\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Response;

class TransactionCategoryController extends ApiController
{
    public function __construct()
    {
        $this->middleware('client.credentials')->only(['index']);
    }


    /*
     * complex controllers are controllers that in some of those operations need to use two or more resources.
     * As you may know that does not exist any relationship between transaction and category.
        So, we need to go through different resources or models to obtain that.
        In this case, the categories of a transaction are the categories of the product that is part of the transaction.
     * */

    /**
     * Display a listing of the resource.
     *
     * @param Transaction $transaction
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Transaction $transaction)
    {
        //returning the categories of the products in transactions
        $categories = $transaction->product->categories;

        return $this->showAll($categories);
    }
}
