<?php
/**
 * @package App/Http/Controllers
 *
 * @class UserController
 *
 * @author Ritu Slaria <ritu.slaria@surmountsoft.com>
 *
 * @copyright 2016 SurmountSoft Pvt. Ltd. All rights reserved.
 */
namespace App\Http\Controllers;

use App\Country;
use App\Employee;
use App\Franchise;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\UserRequest;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::where('role_name', '!=', '1')->with(['employee.franchise'=> function ($query) {
            $query->select('*')
                ->where('franchises.deleted_at', null);
        }])->paginate(10);
        $franchise = Franchise::whereDeletedAt(null)->pluck('name', 'id')->toArray();
        return view('user.index', compact('users', 'franchise'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $countries = Country::pluck('name', 'id')->toArray();
        $franchise = Franchise::pluck('name', 'id')->toArray();
        return view('user.create', compact('countries', 'franchise'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  UserRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        $input = $request->all();
        try {
            $password = randomPassword();
            if ($input['role_name'] == 3) {
                $hasWalletPermissions = 1;
            } else {
                $hasWalletPermissions = 0;
            }
            $user = User::create([
                'name'      => $input['name'],
                'email'     => $input['email'],
                'mobile'    => $input['mobile'],
                'role_name' => $input['role_name'],
                'has_wallet_permission' => $hasWalletPermissions,
                'password'  => bcrypt($password)
            ]);
            Employee::create([
                'user_id'       => $user->id,
                'franchise_id'  => $input['franchise_id'],
                'address_line_one'  => $input['address_line_one'],
                'address_line_two'  => $input['address_line_two'],
                'region'    => $input['region'],
                'city'      => $input['city'],
                'country_id'    => $input['country_id']
            ]);
            \Mail::send('emails.login_detail', [
                'user' => $user, 'password' => $password
            ], function ($message) use ($user) {
                $message->to($user->email, $user->name)->subject('TCL: Your Login Detail');
            });
            return redirect()->route('employee.index')->with('success', \Lang::get('messages.added'));
        } catch (\Exception $error) {
            return redirect()->back()->with('error', \Lang::get('messages.internal_error'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $userId
     * @return \Illuminate\Http\Response
     */
    public function edit($userId)
    {
        $user = User::whereId($userId)->with('employee')->first();
        $countries = Country::pluck('name', 'id')->toArray();
        $franchise = Franchise::pluck('name', 'id')->toArray();
        return view('user.edit', compact('countries', 'franchise', 'user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UserRequest  $request
     * @param  int  $userId
     * @return \Illuminate\Http\Response
     */
    public function update(UserRequest $request, $userId)
    {
        $input = $request->all();
        try {
            if ($input['role_name'] == 3) {
                $hasWalletPermissions = 1;
            } else {
                $hasWalletPermissions = 0;
            }
            User::findOrfail($userId)->update([
                'name'      => $input['name'],
                'email'     => $input['email'],
                'mobile'    => $input['mobile'],
                'role_name' => $input['role_name'],
                'has_wallet_permission' => $hasWalletPermissions,
            ]);
            $employee = User::whereId($userId)->with('employee')->first();
            $employeeId = $employee->employee->id;
            Employee::findOrFail($employeeId)->update([
                'franchise_id'  => $input['franchise_id'],
                'address_line_one'  => $input['address_line_one'],
                'address_line_two'  => $input['address_line_two'],
                'region'    => $input['region'],
                'city'      => $input['city'],
                'country_id'    => $input['country_id']
            ]);
            return redirect()->route('employee.index')->with('success', \Lang::get('messages.updated'));
        } catch (\Exception $error) {
            return redirect()->back()->with('error', \Lang::get('messages.internal_error'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $userId
     * @return \Illuminate\Http\Response
     */
    public function destroy($userId)
    {
        try {
            User::findOrfail($userId)->delete();
            return redirect()->back()->with('success', \Lang::get('messages.deleted'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', \Lang::get('messages.internal_error'));
        }
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getChangePassword()
    {
        return view('user.change-password');
    }

    /**
     * @param ChangePasswordRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postChangePassword(ChangePasswordRequest $request)
    {
        $input = $request->all();
        $user = User::findOrFail(Auth::User()->id);
        $password = $input['new_password'];
        $passwordConfirmation = $input['confirm_password'];
        if (!Hash::check($input['old_password'], $user->password)) {
            return back()->with('error', \Lang::get('messages.password_mismatch'));
        } else {
            if ($password != $passwordConfirmation) {
                return back()->with('error', \Lang::get('messages.password_mismatch'));
            }
            $user->update(['password' => bcrypt($password)]);
        }
        return redirect('/home')->with('success', \Lang::get('messages.password_change'));
    }
}
