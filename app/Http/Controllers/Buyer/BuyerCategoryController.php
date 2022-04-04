<?php

namespace App\Http\Controllers\Buyer;

use App\Buyer;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class BuyerCategoryController extends ApiController
{
    public function __construct()
    {
//        parent::__construct();//invoking the $this->middleware('auth:api') found in the parent controller across all actions

//        $this->middleware('scope:read-general')->only('index');//further, restricting what the client can do

//        $this->middleware('can:view,buyer')->only('index');
    }
    
    /**
     * Display a listing of the resource.phpartisan
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Buyer $buyer)
    {
        //obtain the categories where the buyer made a purchase
        $sellers = $buyer->transactions()->with('product.categories')
            ->get()
            ->pluck('product.categories')
            ->collapse()//creates a unique list with several lists
            ->unique('id')
            ->values();

        return $this->showAll($sellers);
    }
}
