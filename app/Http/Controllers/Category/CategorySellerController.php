<?php

namespace App\Http\Controllers\Category;

use App\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class CategorySellerController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Category $category)
    {
        $this->allowedAdminAction();

        //which sellers are for a particular category
        $sellers = $category->products()
            ->with('seller')//category does not have a direct relation with seller so we go through the products
            ->get()
            ->pluck('seller')
            ->unique()
            ->values();//to avoid empty elements

        return $this->showAll($sellers);
    }
}
