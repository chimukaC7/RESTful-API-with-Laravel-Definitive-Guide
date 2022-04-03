<?php

namespace App;

use App\Transformers\UserTransformer;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Passport\HasApiTokens;//ensure to import from Passport

class User extends Authenticatable
{
    use Notifiable, HasApiTokens, SoftDeletes;//add HasApiTokens

    //verified field will only have two possible values
    //possible values for the verified field
    const VERIFIED_USER = '1';
    const UNVERIFIED_USER = '0';

    //admin field will only have two possible values
    //enums for the admin field
    const ADMIN_USER = 'true';
    const REGULAR_USER = 'false';

    public $transformer = UserTransformer::class;//Linking the model with its respective transformer

    protected $table = 'users';//ensure seller and buyer modals do not attempt to create their own table

    protected $dates = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'verified',
        'verification_token',
        'admin',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'verification_token',
    ];


    // The Laravel Mutators are basically methods that we can create in our models in order to modify the value of an
    //attribute before to be inserted in the database.

    //The Laravel Accessors are methods in our models that allow us to modify the value of an attribute
    //before to return it but after obtain it from the database.

    //It means that, the Mutators modify the original value while the Accessor do not modify it only return
    //a variation but do not modify directly the attribute value.

    //set{name of the attribute}
    //laravel will automatically call the
    public function setNameAttribute($name)//mutators for the name field
    {
        $this->attributes['name'] = strtolower($name);
    }

    public function getNameAttribute($name)//accessor
    {
        return ucwords($name);
    }

    public function setEmailAttribute($email)
    {
        $this->attributes['email'] = strtolower($email);
    }

    public function isVerified()
    {
        return $this->verified == User::VERIFIED_USER;
    }

    public function isAdmin()
    {
        return $this->admin == User::ADMIN_USER;
    }

    //function to generate the verification code
    public static function generateVerificationCode()
    {
        return Str::random(40);//use anything great than 24
    }
}
