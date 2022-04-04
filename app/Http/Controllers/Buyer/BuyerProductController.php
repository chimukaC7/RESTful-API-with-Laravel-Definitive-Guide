<?php

namespace App\Http\Controllers\Buyer;

use App\Buyer;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class BuyerProductController extends ApiController
{
    public function __construct()
    {
//        parent::__construct();//invoking the $this->middleware('auth:api') found in the parent controller across all actions

//        $this->middleware('scope:read-general')->only('index');//further, restricting what the client can do

//        $this->middleware('can:view,buyer')->only('index');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Buyer $buyer)
    {

        //$products = $buyer->transactions->product;

        //list of products for a specific buyer
        //eager loading to fetch directly the product within every transaction
        $products = $buyer->transactions()//query builder for the transactions
            ->with('product')//calling the product relation inside the transaction
            ->get()
            ->pluck('product');//instead of displaying the transactions, display only the products of those transactions

        return $this->showAll($products);
    }
}
