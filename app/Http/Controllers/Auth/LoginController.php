<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
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
        $role = Role::count();
        if ($role == 0) {
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
            $permissions = [
                [
                    'name' => 'send'
                ],
                [
                    'name' => 'edit_research'
                ],
                [
                    'name' => 'cancel_research'
                ],
                [
                    'name' => 'read_research'
                ],
                [
                    'name' => 'manage_user'
                ],
                [
                    'name' => 'add_director'
                ],
                [
                    'name' => 'approve'
                ],
                [
                    'name' => 'add_feed'
                ],
                [
                    'name' => 'add_sum_feed'
                ],
                [
                    'name' => 'edit_feed'
                ],
            ];
            foreach ($permissions as $key => $value) {
                Permission::create($value);
            }
        }
        $c_getR = DB::table('role_has_permissions')->count();
        if ($c_getR == 0) {
            //admin
            DB::insert('insert into role_has_permissions (permission_id, role_id) values (?, ?)', [4, 1]);
            DB::insert('insert into role_has_permissions (permission_id, role_id) values (?, ?)', [5, 1]);
            DB::insert('insert into role_has_permissions (permission_id, role_id) values (?, ?)', [6, 1]);
            DB::insert('insert into role_has_permissions (permission_id, role_id) values (?, ?)', [7, 1]);
            DB::insert('insert into role_has_permissions (permission_id, role_id) values (?, ?)', [8, 1]);
            DB::insert('insert into role_has_permissions (permission_id, role_id) values (?, ?)', [9, 1]);
            DB::insert('insert into role_has_permissions (permission_id, role_id) values (?, ?)', [10, 1]);

            //users
            DB::insert('insert into role_has_permissions (permission_id, role_id) values (?, ?)', [1, 2]);
            DB::insert('insert into role_has_permissions (permission_id, role_id) values (?, ?)', [2, 2]);
            DB::insert('insert into role_has_permissions (permission_id, role_id) values (?, ?)', [3, 2]);
            DB::insert('insert into role_has_permissions (permission_id, role_id) values (?, ?)', [4, 2]);

            //director
            DB::insert('insert into role_has_permissions (permission_id, role_id) values (?, ?)', [4, 3]);
            DB::insert('insert into role_has_permissions (permission_id, role_id) values (?, ?)', [8, 3]);
            DB::insert('insert into role_has_permissions (permission_id, role_id) values (?, ?)', [10, 3]);
        }


        if ($request->username != "" && $request->password != "") {
            $username = $request->username;
            $password = $request->password;

            $admin = DB::table('tb_admins')->where('username', '=', $username)->where('status_workadmin', '=', '1')->first();
            $director = DB::table('tb_directors')->where('username', '=', $username)->where('work_status', '=', '1')->first();
            $user = DB::table('users')->where('username', '=', $username)->where('work_status', '=', '1')->first();
            if ($user != null && $admin != null && $director == null) {
                //admin
                $c_ad_role = DB::table('model_has_roles')->where('role_id', '=', '1')->where('model_id', '=', $user->employee_id)->count();
                if ($c_ad_role == 0) {
                    DB::insert('insert into model_has_roles (role_id,model_type, model_id ) values (?, ?,?)', [1, '', $user->employee_id]);
                }
                return view('admin');
            } elseif ($user != null && $admin == null && $director != null) {
                //director
                $c_ad_role = DB::table('model_has_roles')->where('role_id', '=', '3')->where('model_id', '=', $user->employee_id)->count();
                if ($c_ad_role == 0) {
                    DB::insert('insert into model_has_roles (role_id,model_type, model_id ) values (?, ?,?)', [3, '', $user->employee_id]);
                }
                return view('director');
            } elseif ($user == null && $admin == null && $director != null) {
                //director
                $c_ad_role = DB::table('model_has_roles')->where('role_id', '=', '3')->where('model_id', '=', $director->employee_referees_id)->count();
                if ($c_ad_role == 0) {
                    DB::insert('insert into model_has_roles (role_id,model_type, model_id ) values (?, ?,?)', [3, '', $director->employee_referees_id]);
                }
                return view('director');
            } elseif ($user != null && $admin == null && $director == null) {
                //user
                $c_ad_role = DB::table('model_has_roles')->where('role_id', '=', '3')->where('model_id', '=', $user->employee_id)->count();
                if ($c_ad_role == 0) {
                    DB::insert('insert into model_has_roles (role_id,model_type, model_id ) values (?, ?,?)', [2, '', $user->employee_id]);
                }
                return view('user');
            }
        } else {
            return view('auth.login');
        }
    }
}
