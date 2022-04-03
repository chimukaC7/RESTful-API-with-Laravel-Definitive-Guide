<?php

namespace App\Http\Controllers\Buyer;

use App\Buyer;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class BuyerController extends ApiController
{
    public function __construct()
    {
//        parent::__construct();//invoking the $this->middleware('auth:api') found in the parent controller across all actions

//        $this->middleware('scope:read-general')->only('index');
//        $this->middleware('can:view,buyer')->only('show');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index()
    {
//        $this->allowedAdminAction();

        //so to be sure that a user is a buyer, we need to verify if that specific user has a transaction.
        $buyers = Buyer::has('transactions')->get();

        //return response()->json(['data'=>$buyers],200);
        return $this->showAll($buyers);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Buyer $buyer)
    {
        // $buyer = Buyer::has('transactions')->findOrFail($id);//helps us to retrieve users who are actually buyers
        return $this->showOne($buyer);
    }
}
