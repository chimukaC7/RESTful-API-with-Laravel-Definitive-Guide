<?php

namespace App\Http\Controllers\Product;

use App\User;
use App\Product;
use App\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ApiController;
use App\Transformers\TransactionTransformer;

class ProductBuyerTransactionController extends ApiController
{
    public function __construct()
    {
        parent::__construct();

        //using the transformer
        $this->middleware('transform.input:' . TransactionTransformer::class)->only(['store']);

//        //restricting the action that needs the 'purchase-product'
//        $this->middleware('scope:purchase-product')->only(['store']);//stating the scope
//        $this->middleware('can:purchase,buyer')->only('store');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Product $product
     * @param User $buyer
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request, Product $product, User $buyer)
    {
        $rules = [
            'quantity' => ['required','integer','min:1'],
        ];

        $this->validate($request, $rules);

        //ensure the seller is different buyer
        if ($buyer->id == $product->seller_id) {
            return $this->errorResponse('The buyer must be different from the seller', 409);
        }

        /*if (!$buyer->isVerified()) {// the buyer must be a verified user
            return $this->errorResponse('The buyer must be a verified user', 409);
        }

        if (!$product->seller->isVerified()) {//obtaining the seller from the product
            return $this->errorResponse('The seller must be a verified user', 409);
        }*/

        if (!$product->isAvailable()) {
            return $this->errorResponse('The product is not available', 409);
        }

        if ($product->quantity < $request->quantity) {
            return $this->errorResponse('The product does not have enough units for this transaction', 409);
        }

        //we need to sure that the quantities and the operation are going to perform sequentially, basically,
        //because the quantity of the product is going to change and that can affect the transaction that comes later.
        // For this, we need to use a database transaction
        //the database transaction is going to roll back the database to its previous status.
        return DB::transaction(function () use ($request, $product, $buyer) {
            $product->quantity -= $request->quantity;//reduce the quantity of the product depending on the request
            $product->save();

            //proceed to create the transaction record in the db
            $transaction = Transaction::create([
                'quantity' => $request->quantity,
                'buyer_id' => $buyer->id,
                'product_id' => $product->id,
            ]);

            return $this->showOne($transaction, 201);
        });

    }
}
