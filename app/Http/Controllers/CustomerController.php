<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Spatie\Browsershot\Browsershot;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Company;
use App\Models\Race;
use App\Models\MaritalStatuses;
use App\Models\State;
use App\Models\ReferenceType;
use App\Models\Customer;
use Bouncer;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $customer = Customer::all();

        return view('customer.index')->with('customer',$customer);
    }

    public function create()
    {
        $company = Company::all();
        $races = Race::all();
        $marital_statuses = MaritalStatuses::all();
        $states = State::all();
        $reference_types = ReferenceType::all();

        return view('customer.create')
            ->with('company', $company)
            ->with('races', $races)
            ->with('marital_statues', $marital_statuses)
            ->with('states', $states)
            ->with('reference_types', $reference_types);
    }

    public function store(Request $request)
    {
        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            $profileImagePath = $request->file('profile_image')->store('customer_profiles', 'public');
            $request->merge(['profile_image' => $profileImagePath]);
        }

        $request->merge([
            'city' => strtolower($request->city),
            'state' => strtolower($request->state),
            'warganegara' => strtolower($request->warganegara),
        ]);

        // Create customer record
        $customer = Customer::create($request->all());

        return redirect()->route('customer.index')->withSuccess('Customer data saved successfully');
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
