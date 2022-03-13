<?php

namespace App;

use App\Seller;
use App\Category;
use App\Transaction;
use Illuminate\Database\Eloquent\Model;
use App\Transformers\ProductTransformer;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    //This "Soft Delete" is basically a way to remove an instance of a specific model from the list of
    //available items.
    //But, we are not going to remove it completely from the database.
    use SoftDeletes;

    //enums or constants for the status field
	const AVAILABLE_PRODUCT = 'available';
	const UNAVAILABLE_PRODUCT = 'unavailable';

    public $transformer = ProductTransformer::class;//linking the model with its transformer

    protected $dates = ['deleted_at'];

    protected $fillable = [
    	'name',
    	'description',
    	'quantity',
    	'status',
    	'image',
    	'seller_id',//product belongs to seller
    ];
    //the modal that has the FK,is the modal that belongs to

    protected $hidden = [
        'pivot'
    ];

    public function isAvailable()//returns true or false
    {
    	return $this->status == Product::AVAILABLE_PRODUCT;
    }

    /*
    -the modal that has the FK is the modal that belongs to 
    */
    public function seller()
    {
        return $this->belongsTo(Seller::class);//one to one
    }

    //a product has many transactions
    public function transactions()
    {
        return $this->hasMany(Transaction::class);//one to many
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);//many to many 
    }
}
