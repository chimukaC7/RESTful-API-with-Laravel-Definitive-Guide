<?php

namespace App\Http\Controllers\Seller;

use App\Seller;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class SellerController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('scope:read-general')->only('show');
        $this->middleware('can:view,seller')->only('show');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $this->allowedAdminAction();

        // we need to be careful that in this case, we are going to obtain the users that have products.
        //it means, in the context of our RESTful API, a user that has at least one product, it is a seller in our system.
        //It doesn't matter if the user already sold that products. The important thing is that has a product in the system.
        $sellers = Seller::has('products')->get();

        return $this->showAll($sellers);
    }

    /**
     * Display the specified resource.
     *
     * @param Seller $seller
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Seller $seller)
    {
        //$seller = Seller::has('products')->findOrFail($id);
        return $this->showOne($seller);
    }
}
