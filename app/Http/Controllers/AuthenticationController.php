<?php

namespace Zoomov\Http\Controllers;

use Auth;
use Config;
use DB;
use Lang;
use Illuminate\Support\Facades\Mail;;
use Illuminate\Support\Facades\Session;
use Validator;
use Password;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

use Zoomov\User;
use Zoomov\Professional;

class AuthenticationController extends Controller
{
    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    protected $guard = '';

    public function __construct()
    {
        $this->middleware($this->guestMiddleware(), ['except' => 'logout']);
    }

    protected function activation(Request $request){
        $userid = $request->user;
        $password = $request->key;

        if(is_null($userid)){
            Session::set('locale', 'en');
            if($request->key){
                return View('auth.activation',  ['message' => Lang::get('auth.registered', ['email'=>$request->key])]);
            }
            else{
                return View('auth.activation',  ['message' => Lang::get('auth.unactivated')]);
            }
        }

        if(is_null($password)){
            Session::set('locale', 'en');
            return View('auth.activation',  ['message' => Lang::get('auth.key')]);
        }

        if($this->guard == 'user'){
            $user = User::whereRaw("md5(id) = '".$userid."'")->first();
        }
        else{
            $user = Professional::whereRaw("md5(id) = '".$userid."'")->first();
        }


        if(is_null($user)){
            Session::set('locale', 'en');
            return View('auth.activation', ['message' => Lang::get('auth.unactivated')]);
        }

        Session::set('locale', $user->locale);
        if(strtotime( '-30 day', time()) > strtotime($user->created_at)){
            return View('auth.activation', ['message' => Lang::get('auth.late')]);
        }

        if ($user->password == $password) {
            Auth::guard($this->guard)->login($user);
            return $user->active ? redirect()->guest('/') : View('auth.password', ['guard'=>$this->guard, 'username'=>$user->username, 'email'=>$user->email, 'key'=>$user->password]);
        }
        else{
            return View('auth.activation',  ['message' => Lang::get('auth.unactivated')]);
        }
    }

    public function forget(Request $request)
    {
        $user = DB::table($this->guard.'s')->where('email', $request->email)->first();

        if(is_null($user)){
            return Reponse('Unfound', 401);
        }

        $password = $this->generateStrongPassword();
        $user->password = bcrypt($password);
        $user->save();

        Mail::send('emails.'.$request->lang.'.forget', ['user' => $user->username, 'pwd'=>$password], function($message) use ($user)
        {
            $message->to($user->email, 'ZOOMOV')->subject(trans('auth.forget', ['name' => $user->username]));
        });

        return Response(trans('passwords.sent'), 200);
    }

    public function activated(Request $request)
    {
        if($this->guard == 'user'){
            $user = User::where("email", $request->email)->first();
        }
        else{
            $user = Professional::where("email", $request->email)->first();
        }

        if(is_null($user)){
            return redirect()->guest('/login');
        }

        $user->password = bcrypt($request->password);
        $user->active = 1;
        $user->save();

        Auth::guard($this->getGuard())->login($user);

        return $this->guard == 'user' ? redirect()->guest('/personal'):redirect()->guest('/');
    }

    public function authenticated(Request $request, \Illuminate\Foundation\Auth\User $user){
        if($request->lang != $user->locale){
            $user->locale = $request->lang;
            $user->save();
        }

        return Response('OK', 200);
    }

    // Generates a strong password of N length containing at least one lower case letter,
    // one uppercase letter, one digit, and one special character. The remaining characters
    // in the password are chosen at random from those four sets.
    //
    // The available characters in each set are user friendly - there are no ambiguous
    // characters such as i, l, 1, o, 0, etc. This, coupled with the $add_dashes option,
    // makes it much easier for users to manually type or speak their passwords.
    //
    // Note: the $add_dashes option will increase the length of the password by
    // floor(sqrt(N)) characters.

    protected function generateStrongPassword($length = 12, $add_dashes = false, $available_sets = 'luds')
    {
        $sets = array();
        if(strpos($available_sets, 'l') !== false)
            $sets[] = 'abcdefghjkmnpqrstuvwxyz';
        if(strpos($available_sets, 'u') !== false)
            $sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';
        if(strpos($available_sets, 'd') !== false)
            $sets[] = '23456789';
        if(strpos($available_sets, 's') !== false)
            $sets[] = '!@#$%&*?';

        $all = '';
        $password = '';
        foreach($sets as $set)
        {
            $password .= $set[array_rand(str_split($set))];
            $all .= $set;
        }

        $all = str_split($all);
        for($i = 0; $i < $length - count($sets); $i++)
            $password .= $all[array_rand($all)];

        $password = str_shuffle($password);

        if(!$add_dashes)
            return $password;

        $dash_len = floor(sqrt($length));
        $dash_str = '';
        while(strlen($password) > $dash_len)
        {
            $dash_str .= substr($password, 0, $dash_len) . '-';
            $password = substr($password, $dash_len);
        }
        $dash_str .= $password;
        return $dash_str;
    }

}
