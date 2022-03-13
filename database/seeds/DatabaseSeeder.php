<?php

use App\User;
use App\Product;
use App\Category;
use App\Transaction;
use Laravel\Passport\Passport;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');//disable foreign key checks

        User::truncate();
        Category::truncate();
        Product::truncate();
        Transaction::truncate();
        DB::table('category_product')->truncate();//pivot table

        //disabling the event listeners when seeding the database
        User::flushEventListeners();
        Category::flushEventListeners();
        Product::flushEventListeners();
        Transaction::flushEventListeners();

        //quantities
        $usersQuantity = 1000;
        $categoriesQuantity = 30;
        $productsQuantity = 1000;
        $transactionsQuantity = 1000;

        factory(User::class, $usersQuantity)->create();
        factory(Category::class, $categoriesQuantity)->create();

        factory(Product::class, $productsQuantity)->create()->each(function   ($product) {
                $categories = Category::all()->random(mt_rand(1, 5))->pluck('id');//obtain randomly categories
            //we only need the id of the category hence we pluck

            //receives an array of category id
                $product->categories()->attach($categories);//the attach function receives an array of category id that are going to be
                //associated with that specific product
            });

        factory(Transaction::class, $transactionsQuantity)->create();

        Passport::client()->forceCreate([
            'user_id' => null,
            'name' => '',
            'secret' => 'secret',
            'redirect' => '',
            'personal_access_client' => true,
            'password_client' => true,
            'revoked' => false,
        ]);

        $personalClient = Passport::client()->forceCreate([
            'user_id' => null,
            'name' => '',
            'secret' => 'secret',
            'redirect' => '',
            'personal_access_client' => true,
            'password_client' => false,
            'revoked' => false,
        ]);

        Passport::personalAccessClient()->forceCreate([
            'client_id' => $personalClient->id,
        ]);
    }
}
