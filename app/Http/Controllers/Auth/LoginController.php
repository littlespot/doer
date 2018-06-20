<?php

namespace Zoomov\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Zoomov\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use Zoomov\User;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm(Request $request)
    {
        $user = json_decode($request->cookie('zoomover'));

        if($request->has('new')){
            $user = null;
            cookie()->forget('zoomover');
        }
        elseif(!is_null($user)){

            if($user->locale){
                app()->setLocale($user->locale);
            }
            else{
                $user->locale = app()->getLocale();
            }

        }
        return view('auth.login', ['user'=>$user]);
    }

    public function login(Request $request)
    {

        $this->validate($request, [
            'email' => 'required|string'
        ]);
        if (is_null($request->password)) {
            $user = User::where('email', $request->email)->select('id','username', 'email', 'locale', 'active')->first();
            if(is_null($user)){
                $errors = trans('auth.error_user');
                return back()->withInput()->withErrors(compact('errors'));
            }
            else{
                $cookie = cookie('zoomover', $user);
                Cookie::queue($cookie);
                return view('auth.login', ['user'=>$user]);
            }
        } else {

            $this->validateLogin($request);

            // If the class is using the ThrottlesLogins trait, we can automatically throttle
            // the login attempts for this application. We'll key this by the username and
            // the IP address of the client making these requests into this application.
            if ($this->hasTooManyLoginAttempts($request)) {
                $this->fireLockoutEvent($request);
                $seconds = $this->limiter()->availableIn(
                    $this->throttleKey($request)
                );
                $errors = ['email'=>trans('auth.throttle', ['seconds' => $seconds])];
                return back()->withInput()->withErrors(compact('errors'))->with('status',423);
            }

            if($request->session()->has('imherefor')){
                $this->redirectTo = '/festivals';
            }

            if ($this->attemptLogin($request)) {
                return redirect()->intended($this->redirectPath());
            }

            // If the login attempt was unsuccessful we will increment the number of attempts
            // to login and redirect the user back to the login form. Of course, when this
            // user surpasses their maximum number of attempts they will get locked out.
            $this->incrementLoginAttempts($request);
            $errors = ['password'=>trans('auth.failed')];
            return back()->withInput()->withErrors(compact('errors'));
        }
    }

    public function relogin()
    {
         Cookie::forget('zoomover');
        return redirect()->route('login');
    }
}
