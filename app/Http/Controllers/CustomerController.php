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
use App\Models\Reference;
use App\Models\Customer;
use App\Models\HouseOwnership;
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
        $branchId = Auth::user()->branch_id;

        $company = Company::where('branch_id', $branchId)->get();
        $races = Race::all();
        $marital_statuses = MaritalStatuses::all();
        $states = State::all();
        $reference_types = ReferenceType::all();
        $house_ownership = HouseOwnerShip::all();

        return view('customer.create')
            ->with('company', $company)
            ->with('races', $races)
            ->with('marital_statues', $marital_statuses)
            ->with('states', $states)
            ->with('house_ownership', $house_ownership)
            ->with('reference_types', $reference_types);
    }

    public function store(Request $request)
    {
        if ($request->hasFile('profile_image')) {
            $file = $request->file('profile_image');
            $path = $file->store('profile_images', 'public');
            $customer->profile_image = $path;
        }

        $request->merge([
            'city' => strtolower($request->city),
            'state' => strtolower($request->state),
            'warganegara' => strtolower($request->warganegara),
        ]);

        $customer = Customer::create($request->all());

        return redirect()->route('customer.edit', $customer->id)->withSuccess('Customer personal information saved successfully. Please add work and reference information.');
    }

    public function updateWork(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);
        
        $workData = $request->all();
        $workData['company_city'] = strtolower($request->company_city);
        $workData['company_state'] = strtolower($request->company_state);
        
        $customer->update($workData);
        
        return redirect()->back()->withSuccess('Work information saved successfully');
    }

    public function storeReference(Request $request)
    {
        $referenceData = $request->all();
        $referenceData['city'] = strtolower($request->city);
        $referenceData['company_city'] = strtolower($request->company_city);
        
        Reference::create($referenceData);
        
        return redirect()->back()->withSuccess('Reference information saved successfully');
    }

    public function edit($id)
    {
        $branchId = Auth::user()->branch_id;

        $company = Company::where('branch_id', $branchId)->get();
        $customer = Customer::findOrFail($id);
        $races = Race::all();
        $states = State::all();
        $marital_statues = MaritalStatuses::all();
        $reference_types = ReferenceType::all();
        $house_ownership = HouseOwnerShip::all();
        
        $references = Reference::where('customer_id', $id)->get();
        
        return view('customer.edit', compact('customer', 'company', 'races', 'states', 'marital_statues', 'reference_types', 'references', 'house_ownership'));
    }

    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        // Handle new profile image upload
        if ($request->hasFile('profile_image')) {
            $file = $request->file('profile_image');
            $path = $file->store('profile_images', 'public'); // Save to storage/app/public/profile_images
            $customer->profile_image = $path;
        }

        // Convert fields to lowercase before updating
        $request->merge([
            'city' => strtolower($request->city),
            'state' => strtolower($request->state),
            'warganegara' => strtolower($request->warganegara),
        ]);

        // Update other fields
        $customer->update($request->except('profile_image'));

        // Save profile_image if it was uploaded
        $customer->save();

        return redirect()->back()->withSuccess('Personal information saved successfully');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()->route('customer.index')->withSuccess('Data deleted');
    }

    public function destroyReference($id)
    {
        try {
            $reference = Reference::findOrFail($id);
            $reference->delete();
            
            return redirect()->back()->withSuccess('Reference deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->withError('Failed to delete reference');
        }
    }

}
