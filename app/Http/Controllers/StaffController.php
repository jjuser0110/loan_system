<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::user()->company_id == null) {
            if (Auth::user()->branch_id == null) {
                $staff = User::where('role_id', 4)->get();
            } else {
                $staff = User::where('role_id', 4)->where('branch_id', Auth::user()->branch_id)->get();
            }
        } else {
            $staff = User::where('role_id', 4)->where('company_id', Auth::user()->company_id)->get();
        }

        return view('staff.index')->with('staff', $staff);
    }

    public function create()
    {
        if (Auth::user()->company_id == null) {
            if (Auth::user()->branch_id == null) {
                $company = Company::all();
            } else {
                $company = Company::where('id', Auth::user()->branch_id)->get();
            }
        } else {
            $company = Company::where('id', Auth::user()->company_id)->get();
        }

        return view('staff.create')->with('company', $company);
    }

    public function store(Request $request)
    {
        if (strlen($request->password) < 8) {
            return back()->withErrors(['password' => 'Password must be at least 8 characters.']);
        }

        $checkusername = User::where('username', $request->username)->first();
        if (isset($checkusername)) {
            return back()->withErrors(['username' => 'Username is used. Please try another..']);
        }

        // Validate login time if provided
        if ($request->login_time_start && $request->login_time_end) {
            if ($request->login_time_start >= $request->login_time_end) {
                return back()->withErrors(['login_time_end' => 'End time must be after start time.'])->withInput();
            }
        }

        $company = Company::find($request->company_id);

        $request->merge([
            'role_id'             => 4,
            'password'            => Hash::make($request->password),
            'branch_id'           => $company->branch_id,
            'allow_outside_hours' => $request->has('allow_outside_hours') ? 1 : 0,
        ]);

        $staff = User::create($request->all());

        return redirect()->route('staff.index')->withSuccess('Data saved');
    }

    public function edit(User $staff)
    {
        if (Auth::user()->company_id == null) {
            if (Auth::user()->branch_id == null) {
                $company = Company::all();
            } else {
                $company = Company::where('id', Auth::user()->branch_id)->get();
            }
        } else {
            $company = Company::where('id', Auth::user()->company_id)->get();
        }

        return view('staff.create')->with('staff', $staff)->with('company', $company);
    }

    public function update(Request $request, User $staff)
    {
        if ($request->password == null) {
            $data = $request->except(['password', 'allow_outside_hours']);
        } else {
            if (strlen($request->password) < 8) {
                return back()->withErrors(['password' => 'Password must be at least 8 characters.']);
            }
            $data             = $request->except(['password', 'allow_outside_hours']);
            $data['password'] = Hash::make($request->password);
        }

        if (!empty($data['login_time_start']) && !empty($data['login_time_end'])) {
            if ($data['login_time_start'] >= $data['login_time_end']) {
                return back()->withErrors(['login_time_end' => 'End time must be after start time.'])->withInput();
            }
        }

        // Always set these three explicitly
        $data['allow_outside_hours'] = $request->has('allow_outside_hours') ? 1 : 0;
        $data['login_time_start']    = $request->login_time_start ?: null;
        $data['login_time_end']      = $request->login_time_end ?: null;

        $staff->update($data);

        return redirect()->route('staff.index')->withSuccess('Data updated');
    }

    public function destroy(User $staff)
    {
        $staff->delete();

        return redirect()->route('staff.index')->withSuccess('Data deleted');
    }
}