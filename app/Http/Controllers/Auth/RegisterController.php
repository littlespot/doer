<?php

namespace Zoomov\Http\Controllers\Auth;
use App;
use Auth;
use Config;
use Lang;
use Session;
use Zoomov\Mail\UserRegister;
use Zoomov\User;
use Zoomov\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Auth\Events\Registered;
use Zoomov\UserActivation;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function register(Request $request)
    {
        $data = $request->all();
        $validator = $this->validator($data);

        if ($validator->fails()) {
            $this->throwValidationException(
                $request, $validator
            );
        }

        $invite_code = $data['code'];
        if(strtolower($invite_code) == strtolower(config('constants.invite'))) {
            event(new Registered($user = $this->create($data)));
        }
        else{
            if(substr($invite_code, 0, 1) == 'u'){
                $code = UserInvitation::where('invitation_code', $invite_code)->where('sent', null)->first();
                if(is_null($code)){
                    return redirect()->back()
                        ->withInput($request->only('email'))
                        ->withErrors(['code' => trans('auth.uninivted')]);
                }
            }
            else{
                $code = AdminInvitation::where('invitation_code', $invite_code)->where('sent', null)->first();
                if(is_null($code)){
                    return redirect()->back()
                        ->withInput($request->only('email'))
                        ->withErrors(['code' => trans('auth.uninivted')]);
                }
            }


            event(new Registered($user = $this->create($data)));

            $code['sent'] = 1;
            $code['email'] = $user->email;
            $code->save();
        }

        $activation = UserActivation::create(['id'=>$user->id, 'code'=>$this->uuid()]);
        Mail::to($user->email)->send(new UserRegister($activation->code, $user));

        return View('auth.activation',  ['message'=>trans('auth.registered', ['email'=>$user->email]), 'title' => trans('auth.welcome', ['name'=>$user->username]), 'email'=>$user->email]);
    }

    protected function activation($key, $uid)
    {
        if (is_null($key)) {
            return View('auth.activation', ['message' => trans('auth.key')]);
        }

        if (is_null($uid)) {
            return View('auth.activation', ['message' => trans('auth.unactivated')]);
        }

        $user = User::whereRaw("md5(id) = '" . $uid . "'")->select('id','username', 'email', 'locale', 'created_at')->first();

        if (is_null($user)) {
            return View('auth.activation', ['message' => trans('auth.unactivated')]);
        }

        session(['locale' =>  $user->locale]);

        if (!is_null($user->active)) {
            return view('auth.login', ['user'=>$user])->with('message', trans('auth.activated'));
        }

        $activation = UserActivation::find($user->id);

        if (is_null($activation) || $activation->code != $key) {
            return View('auth.activation', ['message' => trans('auth.key')]);
        }

        if (strtotime('-30 day', time()) > strtotime($user->created_at)) {
            $activation->delete();
            $user->delete();
            return View('auth.activation', ['message' => trans('auth.late')]);
        }

        $user->active = 0;
        $user->save();
        $activation->created_at = gmdate("Y-m-d H:i:s", time());
        $activation->save();

        return view('auth.login', ['user'=>$user])->with('message', trans('auth.activated'));
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|string|min:6|max:16|confirmed',
            'password_confirmation' => 'required|same:password',
            'code' => 'required|max:48',
            'remember' => 'in:on'
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \Zoomov\User
     */
    protected function create(array $data)
    {
        $email = $data['email'];
        $id = $this->uuid2('z', $email);
        return User::create([
            'id' => $id,
            'username' => $data['name'],
            'email' => $email,
            'password' => bcrypt($data['password']),
            'locale' =>  Lang::locale(),
            'usernamed_at' => date("Y-m-d H:i:s", strtotime( '-1 month', time())),
            'created_at' => gmdate("Y-m-d H:i:s", time())
        ]);
      /*  return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);*/
    }
}
