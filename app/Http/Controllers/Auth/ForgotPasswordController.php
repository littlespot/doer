<?php

namespace Zoomov\Http\Controllers\Auth;

use Zoomov\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Zoomov\User;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showLinkRequestForm(Request $request)
    {
        $this->validate($request, ['id'=>'required']);
        $user = User::find($request['id']);
        return view('auth.passwords.email', ['email'=>is_null($user) ? null : $user->email]);
    }
}
