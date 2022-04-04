<?php

namespace App\Http\Controllers\Transaction;

use App\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Response;

class TransactionSellerController extends ApiController
{
    public function __construct()
    {
//        parent::__construct();//invoking the $this->middleware('auth:api') found in the parent controller across all actions

//        $this->middleware('scope:read-general')->only('index');//further, restricting what the client can do

//        $this->middleware('can:view,transaction')->only('index');
    }

    /**
     * Display a listing of the resource.
     *
     * @param Transaction $transaction
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Transaction $transaction)
    {
        //So, basically what we are going to obtain is the specific seller of a transaction.
        //As you may know there does not exist equally a relationship between transaction and seller.
        //So, we have to do that through the product relationship because a product has a seller and a transaction has a product.

        //seller of that product in the transaction
        $seller = $transaction->product->seller;

        return $this->showOne($seller);
    }
}
