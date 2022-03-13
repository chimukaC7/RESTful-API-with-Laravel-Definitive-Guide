<?php

namespace App;

use App\Product;
use Illuminate\Database\Eloquent\Model;
use App\Transformers\CategoryTransformer;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
	use SoftDeletes;

    public $transformer = CategoryTransformer::class;//linking the model with its transformer

	protected $dates = ['deleted_at'];

    protected $fillable = [
    	'name',
    	'description',
    ];

    protected $hidden = [//removing the pivot table from the results
        'pivot'
    ];

    public function products()
    {
    	return $this->belongsToMany(Product::class);//many to many relationship using belongsToMany
    }
}

/*
to many to many relationship requires a pivot table
*/
