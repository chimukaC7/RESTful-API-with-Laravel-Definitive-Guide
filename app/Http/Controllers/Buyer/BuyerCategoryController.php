<?php

namespace App\Http\Controllers\Buyer;

use App\Buyer;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class BuyerCategoryController extends ApiController
{
    public function __construct()
    {
//        parent::__construct();
//        $this->middleware('scope:read-general')->only('index');
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
