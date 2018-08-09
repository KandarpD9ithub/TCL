<?php

namespace App\Http\Controllers\Auth;

use App\Employee;
use App\Franchise;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
        $this->middleware('guest', ['except' => 'logout']);
    }

    /**
     * Log the user out of the application.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();
        $request->session()->flush();
        $request->session()->regenerate();
        return redirect('/login');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    protected function authenticated(Request $request)
    {
        $input = $request->all();
        if (\Auth::attempt(['email' => $input['email'], 'password' => $input['password']], $request->has('remember'))) {
            if(!superAdmin()) {
                if (\Auth::user()->role_name == '5') {
                    $this->logout($request);
                    return redirect()->intended('/login')->with('error', \Lang::get('messages.chef_access_denied'));
                }
                $franchiseId = Employee::whereUserId(\Auth::user()->id)->first()->franchise_id;
                $franchise = Franchise::whereId($franchiseId)->withTrashed()->first();
                if ($franchise->deleted_at != null) {
                    $this->logout($request);
                    return redirect()->intended('/login')->with('error', 'Franchise have been disabled by Admin,
                    Please Contact to admin');
                } else {
                    Log::useDailyFiles(public_path().'/logs/login/login.logs');
                    $logs = [
                        'last_login' => date('Y-m-d H:i:s'),
                        'user_id'    => \Auth::user()->id,
                        'remote_ip'  => \Request::ip(),
                    ];
                    json_encode($logs);
                    Log::info('login logs: '.json_encode($logs));
                    return redirect()->intended('/home');
                }
            }
        } else {
            return redirect()->intended('/login');
        }
    }
}
