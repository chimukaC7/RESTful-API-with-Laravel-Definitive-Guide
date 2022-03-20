<?php

namespace App\Http\Controllers\Buyer;

use App\Buyer;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class BuyerTransactionController extends ApiController
{
    public function __construct()
    {
//        parent::__construct();
//        $this->middleware('scope:read-general')->only('index');
//        $this->middleware('can:view,buyer')->only('index');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Buyer $buyer)//notice the modal binding
    {
        //a specific buyer's transactions
        $transactions = $buyer->transactions;

        return $this->showAll($transactions);
    }
}
