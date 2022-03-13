<?php

namespace App;

use App\Product;
use App\Scopes\SellerScope;
use App\Transformers\SellerTransformer;

//extends from User
class  Seller extends User
{
	public $transformer = SellerTransformer::class;//linking the model with its transformer

	protected static function boot()
	{
		parent::boot();

		static::addGlobalScope(new SellerScope);
	}

    //a seller has several products
    public function products()
    {
    	return $this->hasMany(Product::class);
    }
}
