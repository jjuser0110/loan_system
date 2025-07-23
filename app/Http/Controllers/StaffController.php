<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Spatie\Browsershot\Browsershot;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Company;
use Bouncer;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        if(Auth::user()->company_id == null){
            if(Auth::user()->branch_id == null){
                $staff = User::where('role_id',4)->get();
            }else{
                $staff = User::where('role_id',4)->where('branch_id',Auth::user()->branch_id)->get();
            }
        }else{
            $staff = User::where('role_id',4)->where('company_id',Auth::user()->company_id)->get();
        }

        return view('staff.index')->with('staff',$staff);
    }

    public function create()
    {
        if(Auth::user()->company_id == null){
            if(Auth::user()->branch_id == null){
                $company = Company::all();
            }else{
                $company = Company::where('id',Auth::user()->branch_id)->get();
            }
        }else{
            $company = Company::where('id',Auth::user()->company_id)->get();
        }
        return view('staff.create')->with('company',$company);
    }

    public function store(Request $request)
    {
        // dd($request->all());
        if (strlen($request->password) < 8) {
            return back()->withErrors(['password' => 'Password must be at least 8 characters.']);
        }
        $checkusername = User::where('username',$request->username)->first();
        if (isset($checkusername)) {
            return back()->withErrors(['username' => 'Username is used. Please try another..']);
        }
        $company = Company::find($request->company_id);
        $request->merge([
            'role_id'=>4,
            'password'=>Hash::make($request->password),
            'branch_id'=>$company->branch_id,
        ]);
        $staff = User::create($request->all());

        return redirect()->route('staff.index')->withSuccess('Data saved');
    }

    public function edit(User $staff)
    {
        if(Auth::user()->company_id == null){
            if(Auth::user()->branch_id == null){
                $company = Company::all();
            }else{
                $company = Company::where('id',Auth::user()->branch_id)->get();
            }
        }else{
            $company = Company::where('id',Auth::user()->company_id)->get();
        }
        return view('staff.create')->with('staff',$staff)->with('company',$company);
    }

    public function update(Request $request, User $staff)
    {
        // dd($request->all());
        if ($request->password == null) {
            $data = $request->except('password');
        } else {
            if (strlen($request->password) < 8) {
                return back()->withErrors(['password' => 'Password must be at least 8 characters.']);
            }

            $data = $request->all();
            $data['password'] = Hash::make($request->password);
        }

        $staff->update($data);
        return redirect()->route('staff.index')->withSuccess('Data updated');
    }

    public function destroy(User $staff)
    {
        $staff->delete();

        return redirect()->route('staff.index')->withSuccess('Data deleted');
    }

}
