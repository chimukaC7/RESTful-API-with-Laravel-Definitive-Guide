<?php

namespace App\Http\Controllers\Buyer;

use App\Buyer;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class BuyerSellerController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
     * @param Buyer $buyer
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Buyer $buyer)
    {
        $this->allowedAdminAction();

        //returning the sellers for a buyer
        $sellers = $buyer->transactions()
            //->with('seller')//we cannot do it this way becos there doesn't exist a relationship btn transaction and sellers directly,
            //->get()
            //we have to do it through product
            ->with('product.seller')//using a nested relationship through eager loading
            ->get()
            ->pluck('product.seller')
            ->unique('id')//we can have repeated sellers, we use the unique method
            ->values();//to avoid empty values

        return $this->showAll($sellers);
    }
}
