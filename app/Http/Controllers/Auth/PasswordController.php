<?php

namespace Zoomov\Http\Controllers\Auth;

use Auth;
use Hash;
use Illuminate\Http\Request;
use Session;
use Validator;
use Lang;
use Zoomov\User;
use Zoomov\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Password;

class PasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    protected $subject = 'Your password for ZOOMOV';
    protected $redirectPath = '/auth/personal';
    /**
     * Create a new password controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->subject = Lang::get('auth.reset');
        $this->middleware('guest');
    }

    public function initiation(Request $request)
    {
        $key = $request->key;
        $user = User::where("password", $key)->first();

        if (is_null($user)) {
            return View('auth.activation', ['message' => trans('auth.unactivated'), 'title'=>trans('auth.failed'), 'email'=>'contact@zoomov.com', 'user'=>null]);
        }

        Session::set('locale', $user->locale);

        if (strtotime('-30 day', time()) > strtotime($user->updated_at)) {
            return View('auth.activation', ['message' => trans('auth.late'), 'title'=>trans('auth.failed'), 'email'=>'contact@zoomov.com', 'user'=>$user]);
        }

        return view('auth.passwords.reset', ['token'=>$request->session()->token(), 'user'=>$user->id, 'username'=>$user->name]);
    }

    public function getReset(Request $request)
    {
        return view('auth.passwords.reset', ['token'=>$request->session()->token(), 'user'=>null]);
    }

    public function firstReset(Request $request){
        $this->validate($request, $this->getResetValidationRules());
        $user = User::find($request->id);

        $user->password = bcrypt($request->password);

        $user->active = 0;
        $user->save();

        Auth::guard($this->getGuard())->login($user);

        return redirect('/personal');
    }

    protected function getResetValidationRules()
    {
        return [
            'token' => 'required',
            'id' => 'required',
            'password' => 'required|confirmed|min:6',
            'password_confirmation' => 'required|same:password',
        ];
    }
}
