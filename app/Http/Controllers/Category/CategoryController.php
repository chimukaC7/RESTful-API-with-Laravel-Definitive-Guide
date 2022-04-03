<?php

namespace App\Http\Controllers\Category;

use App\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Transformers\CategoryTransformer;

class CategoryController extends ApiController//to use the response methods remember to extend ApiController
{
    public function __construct()
    {
        //using client credentials grant to protect some routes
//        $this->middleware('client.credentials')->only(['index', 'show']);//specifying which routes to protect using client credentials

        //is going to require an access token that has related the user information.
        //That means that an access token obtained using only client credentials is not going to work.
//        $this->middleware('auth:api')->except(['index', 'show']);// specifying which routes to protect using password credentials

//        //using the transformer
//        $this->middleware('transform.input:' . CategoryTransformer::class)->only(['store', 'update']);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        //$categories = Category::paginate();//this acts directly on the modal
        //the paginate method is only for eloquent collections and we are not always returning eloquent collections as
        // the case in CategoryBuyerController.php
        $categories = Category::all();

        return $this->showAll($categories);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $this->allowedAdminAction();
        
        $rules = [
            'name' => 'required',
            'description' => 'required',
        ];

        $this->validate($request, $rules);

        $newCategory = Category::create($request->all());

        return $this->showOne($newCategory, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        return $this->showOne($category);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param \App\Category $category
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(Request $request, Category $category)
    {
        $this->allowedAdminAction();

        //in this case, we don't need to perform any kind of validation because both, the name and the description,
        //are optional but we need to be sure that the user or the client really sent the values
        $category->fill($request->only([//fill with the new values received in the request
            'name',
            'description',
        ]));

        //validate if there is a change
        if ($category->isClean()) {
            return $this->errorResponse('You need to specify any different value to update', 422);
        }

        $category->save();

        return $this->showOne($category);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Category $category
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Category $category)
    {
        $this->allowedAdminAction();
        
        $category->delete();

        return $this->showOne($category);
    }
}
