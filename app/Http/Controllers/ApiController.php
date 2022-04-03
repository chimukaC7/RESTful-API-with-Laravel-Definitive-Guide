<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Access\AuthorizationException;

//So, every method that we define in the ApiController can be used directly in the other controllers.
class ApiController extends Controller
{
    //We can use the trait in every one of our controllers but it is not completely practical because we really don't
    //need to do that.
    //Remember that we are extending now from the ApiController.
    //So, every method that we define in the ApiController can be used directly in the other controllers.
    use ApiResponser;

    public function __construct()
    {
        //is going to require an access token that has related the user information.
        //That means that an access token obtained using only client
    	$this->middleware('auth:api');//protecting all the routes of the API
    }

    protected function allowedAdminAction()
    {
    	if (Gate::denies('admin-action')) {
            throw new AuthorizationException('This action is unauthorized');
        }
    }
}
