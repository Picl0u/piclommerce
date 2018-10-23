<?php

namespace App\Http\Controllers\Piclommerce\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Piclou\Piclommerce\Http\Entities\Shoppingcart;

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
    protected $redirectTo = '/';


    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        return view('piclommerce::auth.login');
    }
    /**
     * Déconnexion
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        return redirect('/');
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response|void
     */
    public function login(Request $request)
    {
        Auth::logout();
        $this->guard()->logout();
        $this->validateLogin($request);
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }
        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * @param Request $request
     * @param $user
     * @return \Illuminate\Http\RedirectResponse | void
     */
    protected function authenticated(Request $request, $user)
    {
        if($user->role == 'admin') {
            /*if(empty($user->guard_name)){
                $guard_name = config('ikCommerce.superAdminRole');
                User::where('id', $user->id)->update(['guard_name' => $guard_name]);
            }
            $user->assignRole('SuperAdmin');*/
            return redirect()->route('admin.dashboard');
        }
        //Shoppingcart::where("identifier", $user->uuid)->delete();
        if(Shoppingcart::where("identifier", $user->uuid)->first()){
            Cart::instance('shopping')->restore($user->uuid);
            //Cart::instance('shopping')->store($user->uuid);
        }
        Cart::instance('whishlist')->restore($user->id);
        //Cart::instance('whishlist')->store($user->id);
        session(['custommers' => $user]);
    }
}
