<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

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
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $user_data   = User::getUserbyEmail($credentials['email']);
        if(!$user_data){
            return response()->json(['errors' => ['null_user' => ['El email introducido no existe']]], 500);
        }

        if ($user_data->estado === 1){
            $this->login($request);
            return response()->json(['user_data' => $user_data, 'permissions' => $user_data->role->permissions]);

        } else {
            return response()->json(['errors' => ['user' => ['Usuario no autorizado']]], 500);
        }
    }

    protected function authenticated($request, $user)
    {
        return response([
            //
        ]);
    }

    public function checkActiveSession(){
        if(auth('api')->user()){
            return response()->json(['session_active' =>  true], 200);
        }
        return response()->json(['session_active' =>  false], 401);
    }
}