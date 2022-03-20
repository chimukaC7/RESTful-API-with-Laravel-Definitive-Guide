<?php

namespace App\Http\Controllers\User;

use App\User;
use App\Mail\UserCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Transformers\UserTransformer;
use App\Http\Controllers\ApiController;

class UserController extends ApiController
{
    public function __construct()
    {
//        $this->middleware('client.credentials')->only(['resend']);
//        $this->middleware('auth:api')->except(['showRegisterForm', 'store', 'verify', 'resend']);
//
//        //using the transformer to fix the problem when validating as well as storing and updating
//        $this->middleware('transform.input:' . UserTransformer::class)->only(['update']);
//
//        $this->middleware('scope:manage-account')->only(['show', 'update']);
//        $this->middleware('can:view,user')->only('show');
//        $this->middleware('can:update,user')->only('update');
//        $this->middleware('can:delete,user')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index()
    {
        $this->allowedAdminAction();
        
        $users = User::all();


        // return $users;//this returns a json but no possibility to modify the structure

        //return response()->json(['data'=> $users],200);
        return $this->showAll($users);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        //validating the inputs
        $rules = [
            'name' => 'required',
            'email' => ['required','email','unique:users'],//email is required,valid email format,unique in users table
            'password' => ['required','min:6','confirmed'],
        ];

        $this->validate($request, $rules);

        $data = $request->all();//obtaining all the data the we will receive from the request
        //replacing the values received from the user
        $data['password'] = bcrypt($request->password);//encrypt the user's password
        $data['verified'] = User::UNVERIFIED_USER;
        $data['verification_token'] = User::generateVerificationCode();
        $data['admin'] = User::REGULAR_USER;//by default all the users are going to be ordinary users

        $user = User::create($data);

        //return response()->json(['date'=> $user],201);

        Auth::login($user);

        return redirect('/home');
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Display the specified resource.
     *
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(User $user)//using laravel implicit model binding by injecting the model
    {

        //$user = User::findOrFail($id);//no longer needed, laravel will do automatically using implicit model binding

        //return response()->json(['date'=>$user],200);
        return $this->showOne($user);//using the Laravel Model Binding
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(Request $request, User $user)
    {
        //usually for the rules in update method, required is not used
        $rules = [
            'email' => 'email|unique:users,email,' . $user->id,//accept the update to the email if user owns that email
            'password' => 'min:6|confirmed',
            'admin' => 'in:' . User::ADMIN_USER . ',' . User::REGULAR_USER,//two possible values
        ];

        $this->validate($request,$rules);

        if ($request->has('name')) {//if request has name
            $user->name = $request->name;//update the name field
        }

        //if request has email and the email is different from the existing email
        if ($request->has('email') && $user->email != $request->email) {
            $user->verified = User::UNVERIFIED_USER;
            $user->verification_token = User::generateVerificationCode();
            $user->email = $request->email;//update the email field
        }

        if ($request->has('password')) {//if request has a password, we need to encrypt the password
            $user->password = bcrypt($request->password);
        }

        if ($request->has('admin')) {
            $this->allowedAdminAction();
        
            if (!$user->isVerified()) {//if not verified
                //return response()->json(['error'=> 'Only verified users can modify the admin field','code'=> 409], 409);
                return $this->errorResponse('Only verified users can modify the admin field', 409);
            }

            $user->admin = $request->admin;
        }

        if (!$user->isDirty()) {//if nothing changed in the values compared to what is in the database
            //return response()->json(['error'=> 'You need to specify a different value to update','code'=>422]);
            return $this->errorResponse('You need to specify a different value to update', 422);
        }

        $user->save();

        //return response()->json(['date'=>$user],200);
        return $this->showOne($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(User $user)
    {
        //$user = User::findOrFail($id);
        
        $user->delete();

        //return response()->json(['data'=>$user],200);
        return $this->showOne($user);
    }

    public function me(Request $request)
    {
        $user = $request->user();

        return $this->showOne($user);
    }

    public function verify($token)
    {
        $user = User::where('verification_token', $token)->firstOrFail();

        $user->verified = User::VERIFIED_USER;//change status to verified
        $user->verification_token = null;//remove the verification token

        $user->save();

        return $this->showMessage('The account has been verified successfully');
    }

    //re-sending the verification email if requested
    public function resend(User $user)
    {
        if ($user->isVerified()) {
            return $this->errorResponse('This user is already verified', 409);
        }

        //sending emails is an error prone action so as using third party services as those services can be done
        //we run the action incase of fail
        retry(5, function() use ($user) {
                Mail::to($user)->send(new UserCreated($user));
            }, 100);

        return $this->showMessage('The verification email has been resend');
    }
}
