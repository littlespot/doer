<?php

namespace Zoomov\Http\Controllers\Professional;

use Auth;
use Config;
use DB;
use Validator;
use Illuminate\Support\Facades\Mail;
use Zoomov\Http\Controllers\AuthenticationController;
use Zoomov\Professional;

class AuthController extends AuthenticationController
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectPath = '/';
    protected $guard = 'professional';

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */

}
