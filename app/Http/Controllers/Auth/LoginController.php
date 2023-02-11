<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

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

    public function login(Request $request)
    {
        //dd($request->all());
        $roles = [
            [
                'name' => 'admin'
            ],
            [
                'name' => 'user'
            ],
            [
                'name' => 'director'
            ]
        ];
        foreach ($roles as $key => $item) {
            Role::create($item);
        }
        if ($request->username != "" && $request->password != "") {
            $username = $request->username;
            $password = $request->password;

            $admin = DB::table('tb_admins')->where('username', '=', $username)->where('status_workadmin', '=', '1')->first();
            $director = DB::table('tb_directors')->where('username', '=', $username)->where('work_status', '=', '1')->first();
            $user = DB::table('users')->where('username', '=', $username)->where('work_status', '=', '1')->first();
            if ($user != null && $admin != null && $director == null) {
                return 'admin';
            } elseif ($user != null && $admin == null && $director != null) {
                return 'user and director';
            } elseif ($user == null && $admin == null && $director != null) {
                return 'director';
            } elseif ($user != null && $admin == null && $director == null) {
                return 'user';
            }


            return response()->json([
                'admin' => $admin,
                'director' => $director,
                'user' => $user
            ]);
        }
    }
}
