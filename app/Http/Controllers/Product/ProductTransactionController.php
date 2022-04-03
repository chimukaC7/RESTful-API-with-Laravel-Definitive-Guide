<?php

namespace App\Http\Controllers\Product;

use App\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class ProductTransactionController extends ApiController
{
    public function __construct()
    {
//        parent::__construct();//invoking the $this->middleware('auth:api') found in the parent controller across all actions
    }

    /**
     * Display a listing of the resource.
     *
     * @param Product $product
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Product $product)
    {
        //$this->allowedAdminAction();
        
        $transactions = $product->transactions;

        return $this->showAll($transactions);
    }
}
