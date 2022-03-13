<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Builder;

class BuyerScope implements Scope
{
    //The GlobalScopes are basically queries or parts of the query that we can automatically add for the operations
    //over a specific model.
	public function apply(Builder $builder, Model $model)
	{
		$builder->has('transactions');
	}

}