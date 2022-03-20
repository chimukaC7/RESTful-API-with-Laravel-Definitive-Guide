<?php

namespace App\Http\Controllers\Category;

use App\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class CategoryTransactionController extends ApiController
{
    public function __construct()
    {
        //parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
     * @param Category $category
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Category $category)
    {
        //$this->allowedAdminAction();

        //return a list of transactions for a specific category
        $transactions = $category->products()
            ->whereHas('transactions')//ensure the product has at least one transaction,they could have products without transactions
            ->with('transactions')
            ->get()
            ->pluck('transactions')
            ->collapse();//collapsing a list of collections into one

        return $this->showAll($transactions);
    }
}
