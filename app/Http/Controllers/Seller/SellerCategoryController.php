<?php

namespace App\Http\Controllers\Seller;

use App\Seller;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class SellerCategoryController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
//        $this->middleware('scope:read-general')->only('index');
//        $this->middleware('can:view,seller')->only('index');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Seller $seller)
    {
        //return categories where a seller performed a transaction
        $categories = $seller->products()
            ->whereHas('categories')//ensuring the fetched product has category
            ->with('categories')
            ->get()
            ->pluck('categories')
            ->collapse()//create a unique list with several lists
            ->unique('id')
            ->values();//remove the empty values using values()

        return $this->showAll($categories);
    }
}
