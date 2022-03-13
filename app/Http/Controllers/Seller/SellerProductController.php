<?php

namespace App\Http\Controllers\Seller;

use App\User;
use App\Seller;
use App\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Storage;
use App\Transformers\ProductTransformer;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SellerProductController extends ApiController
{
    public function __construct()
    {
        parent::__construct();

        //using the transformer
        $this->middleware('transform.input:' . ProductTransformer::class)->only(['store', 'update']);

        //restricting the action that needs the 'manage-products'
        $this->middleware('scope:manage-products')->except('index');

        $this->middleware('can:view,seller')->only('index');
        $this->middleware('can:sale,seller')->only('store');
        $this->middleware('can:edit-product,seller')->only('update');
        $this->middleware('can:delete-product,seller')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @param Seller $seller
     * @return \Illuminate\Http\JsonResponse
     * @throws AuthorizationException
     */
    public function index(Seller $seller)
    {
        //a different way of checking what an access token can do
        if (request()->user()->tokenCan('read-general') || request()->user()->tokenCan('manage-products')) {

            //return the products from a seller
            $products = $seller->products;//direct relationship btn seller and products

            return $this->showAll($products);
        }

        throw new AuthorizationException('Invalid scope(s)');
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param User $seller
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request, User $seller)//instead of injecting a Seller, inject a User, to allow first time sellers to register their products
    {
        $rules = [
            'name' => 'required',
            'description' => 'required',
            'quantity' => 'required|integer|min:1',
            'image' => 'required|image',//not a file,must be an image
        ];

        $this->validate($request, $rules);

        $data = $request->all();

        $data['status'] = Product::UNAVAILABLE_PRODUCT;//default value
        //$data['image'] = $request->image->store('path','images');//store('path','file system to use')
        //path is calculated from relatively from the file system config, the public folder
        //storing an image when creating a product
        $data['image'] = $request->image->store('');//store('path','file system to use')
        $data['seller_id'] = $seller->id;

        $product = Product::create($data);

        return $this->showOne($product);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Seller $seller
     * @param Product $product
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, Seller $seller, Product $product)
    {
        $rules = [
            'quantity' => 'integer|min:1',
            'status' => 'in:' . Product::AVAILABLE_PRODUCT . ',' . Product::UNAVAILABLE_PRODUCT,//status must a valid value btn the specified values
            'image' => 'image',
        ];

        $this->validate($request, $rules);

        $this->checkSeller($seller, $product);//verify that the user who is trying to update the product is the owner of the product

        $product->fill($request->only([
            'name',
            'description',
            'quantity',
        ]));

        if ($request->has('status')) {
            $product->status = $request->status;

            //if the status of the product is available
            //and the product does not have categories
            if ($product->isAvailable() && $product->categories()->count() == 0) {
                return $this->errorResponse('An active product must have at least one category', 409);
            }
        }

        if ($request->hasFile('image')) {//request has a file
            //updating the image when editing a product
            Storage::delete($product->image);//delete the previous independent of if it is a new image or not

            $product->image = $request->image->store('');//store('path','file system to use')
        }

        if ($product->isClean()) {
            return $this->errorResponse('You need to specify a different value to update', 422);
        }

        $product->save();

        return $this->showOne($product);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Seller $seller
     * @param Product $product
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Seller $seller, Product $product)
    {
        $this->checkSeller($seller, $product);//check if the user specified who is trying to delete is the actual owner of the specified product

        $product->delete();
        Storage::delete($product->image);

        return $this->showOne($product);
    }

    protected function checkSeller(Seller $seller, Product $product)
    {
        if ($seller->id != $product->seller_id) {//check if the received seller is different the initial/original seller id
            throw new HttpException(422, 'The specified seller is not the actual seller of the product');            
        }
    }
}
