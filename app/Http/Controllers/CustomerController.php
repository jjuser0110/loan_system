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
use App\Models\Asset;
use App\Models\Loan;
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
        // Validate only required fields
        $request->validate([
            'customer_name' => 'required|string|max:255',
        ]);

        // Handle profile image upload
        if ($request->hasFile('profile_image')) { 
            $file = $request->file('profile_image'); 
            $path = $file->store('profile_images', 'public'); 
        } 

        // Get company_id and company_code if company is selected
        $company = null;
        if ($request->company_code) {
            $company = Company::where('company_code', $request->company_code)->first();
            if (!$company) {
                return redirect()->back()->withErrors(['company_code' => 'Selected company not found.']);
            }
        }

        // Prepare data for creation
        $customerData = $request->all();
        
        // Store both company_id and company_code if company is selected
        if ($company) {
            $customerData['company_id'] = $company->id;
            $customerData['company_code'] = $company->company_code;
        }
        
        // Convert fields to lowercase if they exist
        if ($request->city) {
            $customerData['city'] = strtolower($request->city);
        }
        if ($request->state) {
            $customerData['state'] = strtolower($request->state);
        }
        if ($request->warganegara) {
            $customerData['warganegara'] = strtolower($request->warganegara);
        }
        
        // Add profile image path if uploaded
        if (isset($path)) {
            $customerData['profile_image'] = $path;
        }

        $customer = Customer::create($customerData);

        return redirect()->route('customer.edit', $customer->id)->withSuccess('Customer personal information saved successfully. Please add work and reference information.'); 
    }

    public function updateWork(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);
        
        $workData = $request->all();
        $workData['company_city'] = strtolower($request->company_city);
        $workData['company_state'] = strtolower($request->company_state);
        
        $customer->update($workData);
        
        return redirect()->to(url()->previous() . '#work')->withSuccess('Work information saved successfully');
    }

    public function storeReference(Request $request)
    {
        $referenceData = $request->all();
        $referenceData['city'] = strtolower($request->city);
        $referenceData['company_city'] = strtolower($request->company_city);
        
        Reference::create($referenceData);
        
        return redirect()->to(url()->previous() . '#reference')->withSuccess('Reference information saved successfully');
    }

    public function editReference($id)
    {
        $reference = Reference::findOrFail($id);
        $customer = Customer::findOrFail($reference->customer_id);
        $races = Race::all();
        $states = State::all();
        $reference_types = ReferenceType::all();
        $house_ownership = HouseOwnership::all();
        
        return response()->json([
            'reference' => $reference,
            'races' => $races,
            'states' => $states,
            'reference_types' => $reference_types,
            'house_ownership' => $house_ownership
        ]);
    }

    public function updateReference(Request $request, $id)
    {
        $reference = Reference::findOrFail($id);
        
        $referenceData = $request->all();
        $referenceData['city'] = strtolower($request->city);
        $referenceData['company_city'] = strtolower($request->company_city);
        
        $reference->update($referenceData);
        
        return redirect()->to(url()->previous() . '#reference')->withSuccess('Reference information updated successfully');
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
        $assets = Asset::all();
        
        $references = Reference::where('customer_id', $id)->get();
        $assets = Asset::where('customer_id', $id)->get();
        $loans = Loan::where('customer_id',$id)->get();
        
        return view('customer.edit', compact('customer', 'company', 'races', 'states', 'marital_statues', 'reference_types', 'references', 'house_ownership', 'assets', 'loans'));
    }

    public function update(Request $request, $id)
    {
        // Validate only required fields
        $request->validate([
            'customer_name' => 'required|string|max:255',
        ]);

        $customer = Customer::findOrFail($id);

        // Get company_id and company_code if company is selected
        $company = null;
        if ($request->company_code) {
            $company = Company::where('company_code', $request->company_code)->first();
            if (!$company) {
                return redirect()->back()->withErrors(['company_code' => 'Selected company not found.']);
            }
        }

        // Handle new profile image upload
        if ($request->hasFile('profile_image')) {
            $file = $request->file('profile_image');
            $path = $file->store('profile_images', 'public');
            $customer->profile_image = $path;
        }

        // Prepare data for update
        $updateData = $request->except('profile_image');
        
        // Store both company_id and company_code if company is selected
        if ($company) {
            $updateData['company_id'] = $company->id;
            $updateData['company_code'] = $company->company_code;
        } else {
            // If no company selected, set both to null
            $updateData['company_id'] = null;
            $updateData['company_code'] = null;
        }
        
        // Convert fields to lowercase before updating if they exist
        if ($request->city) {
            $updateData['city'] = strtolower($request->city);
        }
        if ($request->state) {
            $updateData['state'] = strtolower($request->state);
        }
        if ($request->warganegara) {
            $updateData['warganegara'] = strtolower($request->warganegara);
        }

        // Update other fields
        $customer->update($updateData);

        // Save profile_image if it was uploaded (already handled above)
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
            
            return redirect()->to(url()->previous() . '#reference')->withSuccess('Reference deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->withError('Failed to delete reference');
        }
    }

    public function storeAsset(Request $request)
    {
        $assetData = $request->all();
        
        Asset::create($assetData);
        
        return redirect()->to(url()->previous() . '#asset')->withSuccess('Asset added successfully');
    }

    public function editAsset($id)
    {
        $asset = Asset::findOrFail($id);
        
        return response()->json([
            'asset' => $asset
        ]);
    }

    public function updateAsset(Request $request, $id)
    {
        $asset = Asset::findOrFail($id);
        
        $asset->update($request->all());
        
        return redirect()->to(url()->previous() . '#asset')->withSuccess('Asset updated successfully');
    }

    public function destroyAsset($id)
    {
        try {
            $asset = Asset::findOrFail($id);
            $asset->delete();
            
            return redirect()->to(url()->previous() . '#asset')->withSuccess('Asset deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->withError('Failed to delete asset');
        }
    }
}