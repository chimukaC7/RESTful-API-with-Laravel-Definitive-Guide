<?php

namespace App\Providers;

use App\User;
use App\Buyer;
use App\Seller;
use App\Product;
use Carbon\Carbon;
use App\Transaction;
use App\Policies\UserPolicy;
use App\Policies\BuyerPolicy;
use App\Policies\SellerPolicy;
use Laravel\Passport\Passport;
use App\Policies\ProductPolicy;
use App\Policies\TransactionPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Buyer::class => BuyerPolicy::class,//register the policy
        Seller::class => SellerPolicy::class,//
        User::class => UserPolicy::class,
        Transaction::class => TransactionPolicy::class,
        Product::class => ProductPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //allowing remaining actions only admin users can do
        Gate::define('admin-action', function ($user) {
            return $user->isAdmin();
        });

        //add the following for passport
        Passport::routes();//registering routes for passport
        //This method will register the routes necessary to issue access tokens and revoke access tokens, clients, and personal access tokens:
        Passport::tokensExpireIn(Carbon::now()->addMinutes(5));//to make tokens expire
        Passport::refreshTokensExpireIn(Carbon::now()->addDays(30));

        Passport::enableImplicitGrant();//allowing implicit grant type

        //you need to register your own scopes here
        //scopes should be modular(You should define enough modularity in your scoops, but not too much modularity for every possible action.
        //You can choose to create a group of actions )
        //scopes apply to clients of the API
        Passport::tokensCan([
            //name => description
            'purchase-product' => 'Create a new transaction for a specific product',
            'manage-products' => 'Create, reade, update, and delete products (CRUD)',
            'manage-account' => 'Read your account data, id, name, email, if verified, and if admin (cannot read password). Modify your account data (email, and password). Cannot delete your account',
            'read-general' => 'Read general information like purchasing categories, purchased products, selling products, selling categories, your transactions (purchases and sales)',
        ]);
        /*
         * When a client is trying to obtain an access token and the client needs to specify a list of scoops that
         * he wants to obtain may be only one scoop or maybe all the scoops.
         * So that means that for any specific single access token, we are going to have several scoops associated with it.
         * Then that means that we can validate if an access token has any of those scoops, may be one, maybe two, maybe all of them.
         * */
    }
}
