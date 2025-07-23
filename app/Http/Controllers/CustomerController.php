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

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $customer = User::where('role_id',3)->get();

        return view('customer.index')->with('customer',$customer);
    }

    public function create()
    {
        $company = Company::all();
        return view('customer.create')->with('company',$company);
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
        $customer = User::create($request->all());

        return redirect()->route('customer.index')->withSuccess('Data saved');
    }

    public function edit(User $customer)
    {
        $company = Company::all();
        return view('customer.create')->with('customer',$customer)->with('company',$company);
    }

    public function update(Request $request, User $customer)
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

        $customer->update($data);
        return redirect()->route('customer.index')->withSuccess('Data updated');
    }

    public function destroy(User $customer)
    {
        $customer->delete();

        return redirect()->route('customer.index')->withSuccess('Data deleted');
    }

}
