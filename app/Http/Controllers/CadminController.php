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

class CadminController extends Controller
{
    public function index(Request $request)
    {
        $cadmin = User::where('role_id',3)->get();

        return view('cadmin.index')->with('cadmin',$cadmin);
    }

    public function create()
    {
        $company = Company::all();
        return view('cadmin.create')->with('company',$company);
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
            'role_id'=>3,
            'password'=>Hash::make($request->password),
            'branch_id'=>$company->branch_id,
        ]);
        $cadmin = User::create($request->all());

        return redirect()->route('cadmin.index')->withSuccess('Data saved');
    }

    public function edit(User $cadmin)
    {
        $company = Company::all();
        return view('cadmin.create')->with('cadmin',$cadmin)->with('company',$company);
    }

    public function update(Request $request, User $cadmin)
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

        $cadmin->update($data);
        return redirect()->route('cadmin.index')->withSuccess('Data updated');
    }

    public function destroy(User $cadmin)
    {
        $cadmin->delete();

        return redirect()->route('cadmin.index')->withSuccess('Data deleted');
    }

}
