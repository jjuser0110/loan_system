<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Spatie\Browsershot\Browsershot;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Company;
use App\Models\Branch;
use Bouncer;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class BadminController extends Controller
{
    public function index(Request $request)
    {
        $badmin = User::where('role_id',2)->get();

        return view('badmin.index')->with('badmin',$badmin);
    }

    public function create()
    {
        $branches = Branch::all();
        return view('badmin.create')->with('branches',$branches);
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
        //$company = Company::find($request->company_id);
        $request->merge([
            'role_id'=>2,
            'password'=>Hash::make($request->password),
            'branch_id'=>$request->branch_id,
        ]);
        $badmin = User::create($request->all());

        return redirect()->route('badmin.index')->withSuccess('Data saved');
    }

    public function edit(User $badmin)
    {
        $branches = Branch::all();
        return view('badmin.create')->with('badmin',$badmin)->with('branches',$branches);
    }

    public function update(Request $request, User $badmin)
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

        $badmin->update($data);
        return redirect()->route('badmin.index')->withSuccess('Data updated');
    }

    public function destroy(User $badmin)
    {
        $badmin->delete();

        return redirect()->route('badmin.index')->withSuccess('Data deleted');
    }

}
