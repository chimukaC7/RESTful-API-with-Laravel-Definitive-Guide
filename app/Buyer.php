<?php

namespace App;

use App\Transaction;
use App\Scopes\BuyerScope;
use App\Transformers\BuyerTransformer;

//extends from User
class Buyer extends User
{
	public $transformer = BuyerTransformer::class;//linking the model with its transformer

	//The boot method basically executed when an instance of this model is created.
	//helps to retrieve actual buyer from user in the singular URL
	protected static function boot()
	{
		parent::boot();

		//The next step is to say to our buyer model to use directly this Global Scope every time when is
        //building a query and that can be done calling the addGlobalScope method directly inside the boot method
        //of the model.
		static::addGlobalScope(new BuyerScope);
	}

    //a buyer has several transactions
    public function transactions()//name of the function = name of the relationship
    {
    	return $this->hasMany(Transaction::class);
    }
}
