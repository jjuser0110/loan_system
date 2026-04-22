<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Spatie\Browsershot\Browsershot;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Company;
use App\Models\EmployerType;
use App\Models\Race;
use App\Models\MaritalStatuses;
use App\Models\State;
use App\Models\ReferenceType;
use App\Models\Reference;
use App\Models\Customer;
use App\Models\HouseOwnership;
use App\Models\Asset;
use App\Models\Loan;
use Exception;
use Bouncer;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class CustomerController extends Controller
{
    public function index(Request $request)
    {
        return view('customer.index');
    }

    public function fetch(Request $request){
        try{
            $draw = $request->input('draw');
            $search = $request->input('search.value');
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);
            $orderByColumn = $request->input('columns')[$request->input('order.0.column')]['data'];
            $orderByDirection = $request->input('order.0.dir');
            $query = Customer::query()
                ->select([
                    'customers.*',
                    'customers.company_name as customer_company', 
                    'companies.company_name as company_name',
                    'companies.company_code as company_code',
                    'branches.branch_name as branch_name',
                    'branches.branch_code as branch_code',
                        DB::raw("(
                    CASE 
                        WHEN NOT EXISTS (SELECT 1 FROM loans WHERE loans.customer_id = customers.id) 
                            THEN 'New'
                        WHEN EXISTS (SELECT 1 FROM loans WHERE loans.customer_id = customers.id AND loans.closed = 0 AND loans.next_due_date < CURDATE()) 
                            THEN 'Delay'
                        WHEN NOT EXISTS (SELECT 1 FROM loans WHERE loans.customer_id = customers.id AND loans.closed = 0) 
                            THEN 'Settled'
                        WHEN EXISTS (SELECT 1 FROM loans WHERE loans.customer_id = customers.id AND loans.closed = 0 AND loans.next_due_date >= CURDATE()) 
                            THEN 'Active'
                        ELSE 'Unknown'
                    END
                ) as stats")
                ])
                ->join('companies', 'customers.company_id', '=', 'companies.id')
                ->join('branches', 'companies.branch_id', '=', 'branches.id');
    
            switch (Auth::user()->role_id) {
                case 1:
                    break;

                case 2:
                    $query->where('branches.id', Auth::user()->branch_id);
                    break;

                case 3:
                case 4:
                    $query->where('companies.id', Auth::user()->company_id);
                    break;

            default:
                throw new Exception('Invalid role id.');
            }

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('customers.customer_code', 'like', "%{$search}%")
                    ->orWhere('customers.customer_name', 'like', "%{$search}%")
                    ->orWhere('customers.nric_number', 'like', "%{$search}%")
                    ->orWhere('customers.email', 'like', "%{$search}%")
                    ->orWhere('customers.mobile', 'like', "%{$search}%")
                    ->orWhere('customers.address1', 'like', "%{$search}%")
                    ->orWhere('customers.created_at', 'like', "%{$search}%")
                    ->orWhere('customers.company_name', 'like', "%{$search}%")
                    ->orWhere('branches.branch_name', 'like', "%{$search}%")
                    ->orWhere('companies.company_code', 'like', "%{$search}%")
                    ->orWhere('branches.branch_code', 'like', "%{$search}%")
                    ->orWhere('customers.status', 'like', "%{$search}%")
                    ->orWhereRaw("
                    CASE 
                        WHEN NOT EXISTS (SELECT 1 FROM loans WHERE loans.customer_id = customers.id) 
                            THEN 'New'
                        WHEN EXISTS (SELECT 1 FROM loans WHERE loans.customer_id = customers.id AND loans.closed = 0 AND loans.next_due_date < CURDATE()) 
                            THEN 'Delay'
                        WHEN NOT EXISTS (SELECT 1 FROM loans WHERE loans.customer_id = customers.id AND loans.closed = 0) 
                            THEN 'Settled'
                        WHEN EXISTS (SELECT 1 FROM loans WHERE loans.customer_id = customers.id AND loans.closed = 0 AND loans.next_due_date >= CURDATE()) 
                            THEN 'Active'
                        ELSE 'Unknown'
                    END LIKE ?
                ", ["%{$search}%"]);
                });
            }

            $recordsTotal = $query->count();

            if ($request->hide_fully_paid == 1) {
                $query->where('customers.status', '!=', 'fully_paid');
            }

            $columnMap = [
                'customer_code'    => 'customers.customer_code',
                'nric_number'      => 'customers.nric_number',
                'mobile'           => 'customers.mobile',
                'customer_company' => 'customers.company_name',
                'address1'         => 'customers.address1',
                'email'            => 'customers.email',
                'branch_name'      => 'branches.branch_name',
                'company_name'     => 'companies.company_name',
                'status'           => 'customers.status',
                'created_at'       => 'customers.created_at',
            ];
            $orderByColumn = $columnMap[$orderByColumn] ?? 'customers.created_at';

            $data = $query->orderBy($orderByColumn, $orderByDirection)->skip($start)->take($length)->get();

            $data = $query->orderBy($orderByColumn, $orderByDirection)->skip($start)->take($length)->get();
            return response()->json([
                "draw" => intval($draw),
                "recordsTotal" => $recordsTotal,
                "recordsFiltered" => $recordsTotal,
                "data" => $data,
            ]);
        }
        catch(Exception $e){
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }

    }

    public function create()
    {
         switch(Auth::user()->role_id){
            case 1:
                $query = DB::table('companies');
                break;

            case 2:
                $userBranchId = Auth::user()->branch_id;
                $query = DB::table('companies')->where('branch_id',$userBranchId);
                break;

            default:
                $companyId = Auth::user()->company_id;
                $query = DB::table('companies')->where('id',$companyId);
                break;
        }
   
        $company = $query->get();
        $races = Race::all();
        $marital_statuses = MaritalStatuses::all();
        $states = State::all();
        $employer_types = EmployerType::all();
        $reference_types = ReferenceType::all();
        $house_ownership = HouseOwnership::all();

        return view('customer.create')
            ->with('company', $company)
            ->with('races', $races)
            ->with('marital_statues', $marital_statuses)
            ->with('states', $states)
            ->with('employer_types', $employer_types)
            ->with('house_ownership', $house_ownership)
            ->with('reference_types', $reference_types);
    }

    public function store(Request $request) 
    { 
        try {
            // Validate only required fields
            $validator = Validator::make($request->all(), [
                'customer_name' => 'required|string|max:255',
                'nric_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
                'profile_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            ]);

            if ($validator->fails()) {
                $message = $validator->errors()->first();
                throw new Exception($message);
            }

            // Get company_id and company_code if company is selected
            $company = null;
            if ($request->company_code) {
                $company = Company::where('company_code', $request->company_code)->first();
                if (!$company) {
                    throw new Exception('Selected company not found.');
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
            if ($request->warganegara) {
                $customerData['warganegara'] = strtolower($request->warganegara);
            }
            
            $customer_code = $this->getSystemCode();
            
            $customerData = array_merge($customerData, [
                'customer_code' => $customer_code,
            ]);
            
            $customer = Customer::create($customerData);
            
            // Handle NRIC image upload (OPTIONAL)
            if ($request->hasFile('nric_image')) { 
                $file = $request->file('nric_image');
                $folder = 'customers/'.$customer_code;
                $randomCode = $customer_code.Str::upper(Str::random(12));
                $extension  = $file->getClientOriginalExtension();
                $filename = $randomCode . '.' . $extension;
                $nric_path = $file->storeAs($folder, $filename, 'public');
                $customer->update(['nric_path' => $nric_path]);
            }
            
            // Handle PROFILE image upload (OPTIONAL)
            if ($request->hasFile('profile_image')) { 
                $file = $request->file('profile_image');
                $folder = 'customers/'.$customer_code.'/profile';
                $randomCode = 'profile_'.$customer_code.Str::upper(Str::random(12));
                $extension  = $file->getClientOriginalExtension();
                $filename = $randomCode . '.' . $extension;
                $profile_path = $file->storeAs($folder, $filename, 'public');
                $customer->update(['profile_image' => $profile_path]);
            }
            
            return redirect()->route('customer.edit', $customer->id)->withSuccess('Customer personal information saved successfully. Please add work and reference information.'); 
        }
        catch(Exception $e){
            return redirect()->back()->withError($e->getMessage())->withInput();
        }
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

        if ($request->phone_type == 'mobile') {
            $referenceData['mobile'] = $request->phone_number;
            $referenceData['telephone'] = null;
        } else {
            $referenceData['telephone'] = $request->phone_number;
            $referenceData['mobile'] = null;
        }
        
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

        if ($request->phone_type == 'mobile') {
            $referenceData['mobile'] = $request->phone_number;
            $referenceData['telephone'] = null;
        } else {
            $referenceData['telephone'] = $request->phone_number;
            $referenceData['mobile'] = null;
        }

        $reference->update($referenceData); // update AFTER phone logic

        return redirect()->to(url()->previous() . '#reference')->withSuccess('Reference information updated successfully');
    }

    public function edit($customer)
    {
        switch(Auth::user()->role_id){
            case 1:
                $query = DB::table('companies');
                break;

            case 2:
                $userBranchId = Auth::user()->branch_id;
                $query = DB::table('companies')->where('branch_id',$userBranchId);
                break;

            default:
                $companyId = Auth::user()->company_id;
                $query = DB::table('companies')->where('id',$companyId);
                break;
        }

        $company = $query->get();
        $customer = Customer::findOrFail($customer);
        $races = Race::all();
        $states = State::all();
        $employer_types = EmployerType::all();
        $marital_statues = MaritalStatuses::all();
        $reference_types = ReferenceType::all();
        $house_ownership = HouseOwnership::all();
        $assets = Asset::all();
        
        $references = Reference::where('customer_id', $customer->id)->get();
        $assets = Asset::where('customer_id', $customer->id)->get();
        $loans = Loan::where('customer_id', $customer->id)->get();
        
        // Calculate loan statistics
        $total_loan_count = $loans->count();
        $total_loan_amount = $loans->sum('loan_amount');
        $total_outstanding = $loans->sum('outstanding');
        
        return view('customer.edit', compact(
            'customer', 
            'company', 
            'races', 
            'employer_types',
            'states', 
            'marital_statues', 
            'reference_types', 
            'references', 
            'house_ownership', 
            'assets', 
            'loans',
            'total_loan_count',
            'total_loan_amount',
            'total_outstanding'
        ));
    }

    public function update(Request $request, $id)
    {
        try{
            DB::beginTransaction();
            $rules = [
                'customer_name' => 'required|string|max:255',
                'nric_number' => 'required|string|min:10',
                'gender' => 'required|string',
                'race' => 'required|string',

                'address1' => auth()->user()->role_id == 4 ? 'nullable' : 'required|string',
                'address2' => auth()->user()->role_id == 4 ? 'nullable' : 'required|string',
                'postcode' => auth()->user()->role_id == 4 ? 'nullable' : 'required|string',
                'city' => auth()->user()->role_id == 4 ? 'nullable' : 'required|string',
                'mobile' => auth()->user()->role_id == 4 ? 'nullable' : 'required|string',
                'status' => auth()->user()->role_id == 4 ? 'nullable' : 'required|string',

                'marital_status' => auth()->user()->role_id == 4 ? 'nullable' : 'required',
                'house_ownership' => auth()->user()->role_id == 4 ? 'nullable' : 'required',
                'warganegara' => auth()->user()->role_id == 4 ? 'nullable' : 'required',
                'state' => auth()->user()->role_id == 4 ? 'nullable' : 'required',

                'new_nric_image' => 'nullable|mimes:jpg,jpeg,png,webp,pdf|max:2048',
                'new_profile_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($request->remove_existing_image == '1' && !$request->hasFile('new_nric_image')) {
                $validator->errors()->add('new_nric_image', 'NRIC image is required');
            }
            if ($validator->fails()) {
                $message = $validator->errors()->first();
                throw new Exception($message);
            }

            $customer = Customer::findOrFail($id);

            if (auth()->user()->role_id == 4) {
                $request->merge([
                    'mobile' => $customer->mobile,
                    'address1' => $customer->address1,
                    'address2' => $customer->address2,
                    'postcode' => $customer->postcode,
                    'city' => $customer->city,
                    'state' => $customer->state,
                    'house_ownership' => $customer->house_ownership,
                    'warganegara' => $customer->warganegara,
                    'marital_status' => $customer->marital_status,
                    'remark' => $customer->remark,
                    'status' => $customer->status,
                ]);
            }

            $company = null;
            if ($request->company_code) {
                $company = Company::where('company_code', $request->company_code)->first();
                if (!$company) {
                    return redirect()->back()->withErrors(['company_code' => 'Selected company not found.']);
                }
                switch(Auth::user()->role_id){
                    case 1:
                        break;

                    case 2:
                        if($company->branch_id != Auth::user()->branch_id){
                            throw new Exception('Company code does not found.');
                        }
                        break;

                    default:
                        if(Auth::user()->branch_id != $company->id){
                            throw new Exception('Company code does not found.');
                        }
                        break;
                }
            }

            $old_nric_path = $customer->nric_path;
            $old_profile_path = $customer->profile_image;
            
            if ($request->remove_existing_image == '1' && !$request->hasFile('new_nric_image')) {
                throw new Exception('NRIC image is required.');
            }
            
            $customer->update([
                'company_code' => $request->company_code,
                'customer_name' => $request->customer_name,
                'nric_number' => $request->nric_number,
                'gender' => $request->gender,
                'race' => $request->race,
                'address1' => $request->address1,
                'address2' => $request->address2,
                'postcode' => $request->postcode,
                'city' => $request->city,
                'mobile' => $request->mobile,
                'email' => $request->email,
                'marital_status' => $request->marital_status,
                'warganegara' => $request->warganegara,
                'house_ownership' => $request->house_ownership,
                'state' => $request->state,
                'status' => $request->status,
                'remark' => $request->remark
            ]);

            // Handle NRIC image update
            if ($request->hasFile('new_nric_image')) { 
                $file = $request->file('new_nric_image');
                $folder = 'customers/'.$customer->customer_code;
                $randomCode = $customer->customer_code.Str::upper(Str::random(12));
                $extension  = $file->getClientOriginalExtension();
                $filename = $randomCode . '.' . $extension;
                $path = $file->storeAs($folder, $filename, 'public');
                $customer->update(['nric_path'=>$path]);
                
                // Only delete if old path exists and is not null
                if ($old_nric_path) {
                    Storage::disk('public')->delete($old_nric_path);
                }
            }
            
            // Handle PROFILE image update
            if ($request->hasFile('new_profile_image')) { 
                $file = $request->file('new_profile_image');
                $folder = 'customers/'.$customer->customer_code.'/profile';
                $randomCode = 'profile_'.$customer->customer_code.Str::upper(Str::random(12));
                $extension  = $file->getClientOriginalExtension();
                $filename = $randomCode . '.' . $extension;
                $profile_path = $file->storeAs($folder, $filename, 'public');
                $customer->update(['profile_image' => $profile_path]);
                
                // Only delete if old profile path exists and is not null
                if ($old_profile_path) {
                    Storage::disk('public')->delete($old_profile_path);
                }
            }
            
            // Handle remove profile image
            if ($request->remove_profile_image == '1') {
                if ($old_profile_path) {
                    Storage::disk('public')->delete($old_profile_path);
                }
                $customer->update(['profile_image' => null]);
            }
            
            DB::commit();
            return redirect()->back()->withSuccess('Personal information saved successfully');
        }
        catch(Exception $e){
            DB::rollBack();
            return redirect()->back()->withError($e->getMessage());
        }
    }

    public function delete(Request $request)
    {
        try {
            DB::beginTransaction();

            if(Auth::user()->role_id != 1){
                throw new Exception('Access denied.');
            }
            
            $customer = Customer::join('companies', 'customers.company_id', '=', 'companies.id')
                ->join('branches', 'companies.branch_id', '=', 'branches.id')
                ->where('customers.id', $request->customer_id)->first();

            if(!$customer){
                throw new Exception('Unable to find selected customers.');
            }
            
            $loan = Loan::where('customer_id',$customer->id)->where('closed',0)->first();
            if($loan){
                throw new Exception('The customer still have unresolved loan.');
            }
            $customer->delete();
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Customer deleted successfully.']);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
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

    public function single_customer(Request $request)
    {
        $nric = $request->nric_number;

        if (!$nric) {
            return back()->with('error', 'Please enter a NRIC number.');
        }

        // 1. Search in customers table (by nric_number)
        $customer = Customer::where('nric_number', $nric)->first();

        if ($customer) {
            return redirect()->route('customer.edit', $customer->id);
        }

        // 2. Search in references table (by new_ic)
        $reference = \App\Models\Reference::where('new_ic', $nric)->first();

        if ($reference) {
            return redirect()->to(route('customer.edit', $reference->customer_id) . '#reference');
        }

        // 3. Not found
        return back()->with('error', 'No record found with this NRIC.');
    }
}