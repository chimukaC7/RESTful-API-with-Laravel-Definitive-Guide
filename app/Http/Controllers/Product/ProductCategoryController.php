<?php

namespace App\Http\Controllers\Product;

use App\Product;
use App\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class ProductCategoryController extends ApiController
{
    public function __construct()
    {
//        $this->middleware('client.credentials')->only(['index']);
//        $this->middleware('auth:api')->except(['index']);
//        //restricting the actions that need the 'manage-product'
//        $this->middleware('scope:manage-products')->except('index');
//        $this->middleware('can:add-category,product')->only('update');
//        $this->middleware('can:delete-category,product')->only('destroy');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Product $product)
    {
        $categories = $product->categories;

        return $this->showAll($categories);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Product $product
     * @param Category $category
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Product $product, Category $category)
    {
        //interacting with a many to many relationship
        //attach, sync, syncWithoutDetaching
        //attach method allows duplicates
        //sync method adds the new but detaches/deletes the other ones even the non duplicates;
        $product->categories()->syncWithoutDetaching([$category->id]);//since it is an array we can attach several

        return $this->showAll($product->categories);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Product $product, Category $category)
    {
        if (!$product->categories()->find($category->id)) {//check if that specified category exists for the specified product
            return $this->errorResponse('The specified category is not a category of this product', 404);
        }

        //deleting in a many-to-many relationship
        //removing from the pivot table
        $product->categories()->detach($category->id);

        return $this->showAll($product->categories);
    }
}
