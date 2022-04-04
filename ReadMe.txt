-you can use the latest version without specifying any version at the end
-if preferred you can use 6.* to install the latest Laravel 6 release
$ composer create-project laravel/laravel RESTfulAPI 5.4.*

-endpoint- provide access different actions over the resource
endpoints to obtain a list of any resource
-the transactions of a buyer
-the products that a buyer purchased
-the categories where a buyer purchased something
-the seller for a specific buyer
-the transactions of a product
-the category of a product
-the seller of a product
-the buyers of a specific product

composer create-project laravel/laravel ProjectName


$ php artisan storage:link

php artisan config:cache
php artisan config:clear
php artisan cache:clear
php artisan route:cache

-starts the server
$ php artisan serve

cd RESTfulAPI
-adding homestead as a dependency for the laravel project
-Laravel Homestead is an official, pre-packaged Vagrant box that provides you a wonderful development environment without requiring you to install PHP, a web server, and any other server software on your local machine.
$ composer require laravel/homestead


-After Laravel 6.0, some visual components were removed from the default structure of Laravel and moved to independent packages.
-During the course,  for ease, we will use some UI components, and for that, we will need to install a package called laravel/ui.
-For this, you just need to use composer (as in the previous class) but requiring this package. So, issue this in the command prompt of your project:

$ composer require laravel/ui

That's all, this will be especially useful when we generate some auth component.


-creates a configuration file for homestead called homestead.yaml
$ php vendor/bin/homestead make
$ vendor\\bin\\homestead make (windows)


-generating public/private rsa key pair
$ ssh-keygen -t rsa -b 4096

-execute while in project folder
$ vagrant up

-check status
$ vagrant

$ vagrant destroy

$ vagrant halt

-connect to the virtual machine
$ vagrant ssh

-updates the versions of all the packages
$composer update

-check the commands available to you
$ php artisan

$ php artisan make:model Buyer

$ php artisan make:model Seller

-creating model with migration
$ php artisan make:model Category -m

$ php artisan make:model Product -m

$ php artisan make:model Transaction -m


-creating a controller with resource
(creating resource controllers(they have methods predefined))

$ php artisan make:controller User/UserController -r

$ php artisan make:controller Buyer/BuyerController -r

$ php artisan make:controller Seller/SellerController -r

$ php artisan make:controller Category/CategoryController -r

$ php artisan make:controller Product/ProductController -r

$ php artisan make:controller Transaction/TransactionController -r

-after creating a route,you can view the routes created
$ php artisan route:list

-to creat the table
-when creating pivot tables use the name of the modals and not the tables in alphabetical order

-to create the migration
$ php artisan make:migration category_product_table --create=category_product

-to modify the migration
$ php artisan make:migration category_product_table --table=category_product
$ php artisan make:migration category_attached_files --table=attached_files

$ php artisan

-to run the migration
$ php artisan migrate

-to the structure of the tables
$ php artisan migrate: refresh

-to seed the database
$ php artisan db:seed

-after table modifications
$ php artisan migrate:refresh --seed

-making a controller with modal binding
$ php artisan make:controller Category/CategoryController -r -m Category
$ php artisan make:controller Product/ProductController -r -m Product
$ php artisan make:controller Transaction/TransactionCategoryController -r -m Transaction
$ php artisan make:controller Transaction/TransactionSellerController -r -m Transaction
$ php artisan make:controller Buyer/BuyerTransactionController -r -m Buyer
$ php artisan make:controller Buyer/BuyerProductController -r -m Buyer
$ php artisan make:controller Buyer/BuyerSellerController -r -m Buyer
$ php artisan make:controller Buyer/BuyerCategoryController -r -m Buyer
$ php artisan make:controller Category/CategoryProductController -r -m Category
$ php artisan make:controller Category/CategorySellerController -r -m Category
$ php artisan make:controller Category/CategoryTransactionController -r -m Category
$ php artisan make:controller Category/CategoryBuyerController -r -m Category
$ php artisan make:controller Seller/SellerTransactionController -r -m Seller
$ php artisan make:controller Seller/SellerCategoryController -r -m Seller
$ php artisan make:controller Seller/SellerBuyerController -r -m Seller
$ php artisan make:controller Seller/SellerProductsController -r -m Seller
$ php artisan make:controller Product/ProductTransactionController -r -m Product
$ php artisan make:controller Product/ProductBuyerController -r -m Product
$ php artisan make:controller Product/ProductCategoryController -r -m Product
$ php artisan make:controller Product/ProductBuyerTransactionController -r -m Product

$ composer require guzzlehttp/guzzle

$ php artisan make:mail UserCreated
$ php artisan make:mail UserMailChanged

$ php artisan make:mail Test -m emails.test

$ php artisan make:middleware SignatureMiddleware
$ php artisan make:middleware CustomThrottleRequests

$ composer require spatie/laravel-fractal
-Resources are for some people a replacement for Laravel fractal or php fractal.
 And probably the resources are a replacement, but in my opinion, not completely yet.

$ php artisan make:transformer UserTransformer
$ php artisan make:transformer BuyerTransformer
$ php artisan make:transformer SellerTransformer
$ php artisan make:transformer CategoryTransformer
$ php artisan make:transformer TransactionTransformer
$ php artisan make:transformer ProductTransformer

Since Laravel 6, building the authentication components is still possible but with some changes, because now those visual components are in a separate package (the one you installed in class 12).
In the following class, I will use a command called make: auth. Unfortunately, after Laravel 6.0, this command does NOT exist anymore and has been replaced by the following:

$ ui vue --auth, resulting in php artisan ui vue --auth

This command does two important things:
First, add Vue.js, along with Bootstrap, for visual components; and with the --auth parameter add the same components that the make: auth command used to add.



At this point, you will have the same components. However, since Laravel no longer comes by default with Boostrap or Vue.js, you will have to compile the assets again (ie CSS and JS) in order to use them without problems (as is done in the course).
To do this you must run npm install (to install the frontend dependencies, as composer does for the backend) and then run npm run dev to generate all those components.
These steps are optional because in a following class we will perform them (Class 181: "Preparing the API to Use the Passport Components").



I know it seems a bit cumbersome, but it is a process that should take less than 10 minutes and you only have to do it once.
Of course, here I will be in case of any doubt.
REMEMBER: If you are in Laravel 6, in the following class, you should use php artisan ui vue --auth instead of php artisan make: auth

composer require laravel/ui --dev
php artisan ui vue --auth
php artisan migrate
npm install && npm run dev


$ composer require laravel/passport
$ php artisan migrate
$ php artisan passport:install

remember a client is acting as a user with their respective permissions or basically scoop's that the user provides.

Obtaining and using tokens using the Client Credentials Grant Type
$ php artisan passport:client
-pick the id i.e use 0 for auto assign
-pick the client name
-pick the redirect url

Obtaining and using tokens using the Password Grant Type
$ php artisan passport:client --password

Preparing the API to Use the Passport Components
//Basically, those components are going to allow us to create very nice views in order to manage the
  clients, the personal access tokens and any other things, and those companies are being created by default
$ php artisan vendor:publish --tag=passport-components
$ npm install
$ npm run dev
$ npm watch

Obtaining and using tokens using the Personal Token
//now remember that the personal access tokens have not expiration time.
// It means they are valid forever, unless that you remove these, of course, and they are intended only
// for testing purposes or just to provide some specific access to the user.
// But remember, that can be a little insecure.
// You cannot trust completely in your user.
// So just use it if you are completely assured of that.
// if you don't want to use personal tokens, do not register a personal client in your cmd.
$ php artisan passport:client --personal

Gates and Policy
-define specific conditions that need to be accomplished by any specific user or any specific resource to perform something


$ php artisan make:policy --help
$ php artisan make:policy BuyerPolicy --model=Buyer
$ php artisan make:policy SellerPolicy --model=Seller
$ php artisan make:policy UserPolicy --model=User
$ php artisan make:policy TransactionPolicy --model=Transaction

$ composer require barryvdh/laravel-cors
$ php artisan vendor:publish --provider="Barryvdh\Cors\ServiceProvider"


$ composer require laravel/passport guzzlehttp/guzzle spatie/laravel-fractal barryvdh/laravel-cors
