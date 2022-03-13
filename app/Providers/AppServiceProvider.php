<?php

namespace App\Providers;

use App\Product;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //Solving a common issue with the laravel migrations
        Schema::defaultStringLength(191);

        //send verification using events
        /*User::created(function($user) {
            retry(5, function() use ($user) {
                Mail::to($user->email)->send(new UserCreated($user));
            }, 100);
        });

        //send verification for email changed using events
        User::updated(function($user) {
            if ($user->isDirty('email')) {//if empty laravel validates against all the fields
                retry(5, function() use ($user) {
                    Mail::to($user)->send(new UserMailChanged($user));
                }, 100);
            }
        });*/


        /*
         * we are going to see how to handle automatically the status or the availability of a product depending of the quantity of.
        For example, if an available product gets a zero quantity in some specific moment, we are going to change this status to unavailable as expected in order to keep the consistency of our RESTful API.
        For this,we are going to use events.

        Basically, what we are going to do is to listen for the updated event of the product that is going to be triggered.
        Every time that something changes in the product, in this case, specifically we are going to listen when the quantity of the product changes.
        In this case, specifically we are going to perform any change when the product quantity is changed to zero and its status is available*/

        //listen for the updated for the product modal
        Product::updated(function ($product) {
            if ($product->quantity == 0 && $product->isAvailable()) {
                $product->status = Product::UNAVAILABLE_PRODUCT;//change status to unavailable

                $product->save();
            }
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
