<?php

namespace App\Http\Controllers\Category;

use App\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class CategoryBuyerController extends ApiController
{
    public function __construct()
    {
//        parent::__construct();//invoking the $this->middleware('auth:api') found in the parent controller across all actions
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Category $category)
    {
        //$this->allowedAdminAction();

        //return all the buyers for a specific category
        $buyers = $category->products()
            ->whereHas('transactions')//ensuring to return the products that have transactions
            ->with('transactions.buyer')//we need the buyer of those transactions as well
            ->get()
            ->pluck('transactions')
            ->collapse()//obtain a unique collection
            ->pluck('buyer')
            ->unique('id')
            ->values()
        ;

        return $this->showAll($buyers);
        //pagination, in this case we are not directly dealing with the model
    }
}
