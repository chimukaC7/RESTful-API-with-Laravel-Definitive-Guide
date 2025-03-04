<?php

namespace App\Transformers;

use App\Seller;
use League\Fractal\TransformerAbstract;

class SellerTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Seller $seller)
    {
        return [
            'identifier' => (int)$seller->id,
            'name' => (string)$seller->name,
            'email' => (string)$seller->email,
            'isVerified' => (int)$seller->verified,
            'creationDate' => (string)$seller->created_at,
            'lastChange' => (string)$seller->updated_at,
            'deletedDate' => isset($seller->deleted_at) ? (string) $seller->deleted_at : null,

            'links' => [
                [
                    'rel' => 'self',
                    'href' => route('sellers.show', $seller->id),
                ],
                [
                    'rel' => 'seller.buyers',//A link to the list of buyers
                    'href' => route('sellers.buyers.index', $seller->id),
                ],
                [
                    'rel' => 'seller.categories',//A link to all the categories on which this seller sold something
                    'href' => route('sellers.categories.index', $seller->id),
                ],
                [
                    'rel' => 'seller.products',//A link to the products sold by this seller
                    'href' => route('sellers.products.index', $seller->id),
                ],
                [
                    'rel' => 'seller.transactions',//A link to the transactions/sales of this seller
                    'href' => route('sellers.transactions.index', $seller->id),
                ],
                [
                    'rel' => 'user',//A link to the user profile of this seller
                    'href' => route('users.show', $seller->id),
                ],
            ],
        ];
    }

    public static function originalAttribute($index)
    {
        $attributes = [
            'identifier' => 'id',
            'name' => 'name',
            'email' => 'email',
            'isVerified' => 'verified',
            'creationDate' => 'created_at',
            'lastChange' => 'updated_at',
            'deletedDate' => 'deleted_at',
        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
    }

    //for use in the validation response to map transformed key names to original key names
    public static function transformedAttribute($index)
    {
        $attributes = [
            'id' => 'identifier',
            'name' => 'name',
            'email' => 'email',
            'verified' => 'isVerified',
            'created_at' => 'creationDate',
            'updated_at' => 'lastChange',
            'deleted_at' => 'deletedDate',
        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
    }
}
