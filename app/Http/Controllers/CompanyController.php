<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Spatie\Browsershot\Browsershot;
use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Branch;
use Bouncer;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        $company = Company::all();

        return view('company.index')->with('company',$company);
    }

    public function load_company(Request $request)
    {
        try{
            $draw = $request->input('draw');
            $search = $request->input('search.value');
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);
            $orderByColumn = $request->input('columns')[$request->input('order.0.column')]['data'];
            $orderByDirection = $request->input('order.0.dir');
            $query = Company::query()->withSum('payment_methods as total_amount', 'amount')->with('branch:id,branch_name,branch_code');
            switch (Auth::user()->role_id) {
                case 1:
                    break;

                case 2:
                    $query->where('branch_id', Auth::user()->branch_id);
                    break;

                case 3:
                case 4:
                    $query->where('id', Auth::user()->company_id);
                    break;

                default:
                    throw new Exception('Invalid role id.');
            }

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('company_code', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%")
                    ->orWhere('branch_code', 'like', "%{$search}%")
                    ->orWhere('branch_name', 'like', "%{$search}%");
                });
            }
     
            $recordsTotal = $query->count();
            $loans = $query->orderBy($orderByColumn, $orderByDirection)->skip($start)->take($length)->get();

            return response()->json([
                "draw" => intval($draw),
                "recordsTotal" => $recordsTotal,
                "recordsFiltered" => $recordsTotal,
                "data" => $loans,
            ]);
        }
        catch(Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->getMessage()
            ]);
        }
    }

        

    public function create()
    {
        $branch = Branch::all();
        return view('company.create')->with('branch',$branch);
    }

    public function store(Request $request)
    {
        $company = Company::create($request->all());

        return redirect()->route('company.index')->withSuccess('Data saved');
    }

    public function edit(Company $company)
    {
        $branch = Branch::all();
        return view('company.create')->with('company',$company)->with('branch',$branch);
    }

    public function update(Request $request, Company $company)
    {
        $company->update($request->all());
        return redirect()->route('company.index')->withSuccess('Data updated');
    }

    public function destroy(Company $company)
    {
        $company->delete();

        return redirect()->route('company.index')->withSuccess('Data deleted');
    }

}
