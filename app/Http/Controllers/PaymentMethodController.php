<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Spatie\Browsershot\Browsershot;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Company;
use App\Models\Bank;
use App\Models\PaymentMethod;
use App\Models\PaymentMethodLog;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\LoanController;
use Exception;
use Bouncer;
use Carbon\Carbon;

class PaymentMethodController extends Controller
{
    protected $loanController;
    
    public function __construct(LoanController $loanController, ScheduleController $scheduleController)
    {
        $this->loanController = $loanController;
        $this->scheduleController = $scheduleController;
    }

    public function index(Request $request)
    {
        $banks = Bank::all();
        
        switch(Auth::user()->role_id){
            case 1:
                $query = DB::table('companies');
                break;

            case 2:
                $query = DB::table('companies')->where('branch_id', Auth::user()->branch_id);
                break;

            default:
                $query = DB::table('loans')->where('company_id', Auth::user()->company_id);
                break;
        }
        $companies = $query->get();
        return view('payment_method.index')->with('companies',$companies)->with('banks',$banks);
    }

    public function logs(Request $request)
    {
        return view('payment_method.logs');
    }

    public function load_payment_method(Request $request){
        try{
            $draw = $request->input('draw');
            $search = $request->input('search.value');
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);
            $orderByColumn = $request->input('columns')[$request->input('order.0.column')]['data'];
            $orderByDirection = $request->input('order.0.dir');
            $query = PaymentMethod::query()
                ->select([
                    'payment_methods.*',
                    'banks.bank_name',
                    'companies.company_name as company_name',
                    'companies.company_code as company_code',
                    'branches.branch_name as branch_name',
                    'branches.branch_code as branch_code',
                ])
                ->join('branches', 'payment_methods.branch_id', '=', 'branches.id')
                ->join('banks', 'payment_methods.bank_id', '=', 'banks.id')
                ->join('companies', 'payment_methods.company_id', '=', 'companies.id');
            switch (Auth::user()->role_id) {
                case 1:
                    break;

                case 2:
                    $query->where('companies.branch_id', Auth::user()->branch_id);
                    break;

                case 3:
                case 4:
                    $query->where('payment_methods.company_id', Auth::user()->company_id);
                    break;

                default:
                    throw new Exception('Invalid role id.');
            }
            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('payment_methods.account_no', 'like', "%{$search}%")
                    ->orWhere('payment_method.owner_name', 'like', "%{$search}%")
                    ->orWhere('companies.company_name', 'like', "%{$search}%")
                    ->orWhere('companies.company_code', 'like', "%{$search}%")
                    ->orWhere('branches.branch_code', 'like', "%{$search}%")
                    ->orWhere('branches.branch_name', 'like', "%{$search}%");
                });
            }

            $recordsTotal = $query->count();
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

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $v = $request->validate([
                'owner_name' => 'required|string',
                'account_no' => 'required|numeric',
            ]);

            $company = $request->company_id ?? false;
            $c = Company::where('id',$company)->first();
            if(!$company || !$c){
                throw new Exception('Invalid company.');
            }

            if(Auth::user()->role_id > 1){
                if($c->branch_id != Auth::user()->branch_id && $c->company_id != Auth::user()->company_id){
                    throw new Exception('Access denied.');
                }
            }

            if($request->status != 1 && $request->status != 0){
                throw new Exception('Invalid status.');
            }

            $b = Bank::where('id',$request->bank_id)->first();
            if(!$b){
                throw new Exception('Invalid bank');
            }

            $pm = PaymentMethod::create([
                'account_no'=>$request->account_no,
                'owner_name'=>$request->owner_name,
                'is_active'=>$request->status,
                'company_id'=>$c->id,
                'branch_id'=>$c->branch_id,
                'bank_id'=>$request->bank_id
            ]);
            DB::commit();
            return response()->json(['success'=>true,'message'=>"Payment method created."]);
        }
        catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first()
            ]);
           
        }
        catch(Exception $e){
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function update(Request $request)
    {
        try {
            DB::beginTransaction();
            
            $p = PaymentMethod::where('id',$request->payment_method_id)->first();
            if(!isset($p)){
                throw new Exception('Unable to get payment method details.');
            }

            if(Auth::user()->role_id > 1){
                if($p->company_id != Auth::user()->company_id && $p->branch_id != Auth::user()->branch_id){
                    throw new Exception('Access denied');
                }
            }
            
            $v = $request->validate([
                'owner_name' => 'required|string',
                'account_no' => 'required|numeric',
            ]);

            $company = $request->company_id ?? false;
            $c = Company::where('id',$company)->first();
            if(!$company || !$c){
                throw new Exception('Invalid company.');
            }

            if($c->branch_id != Auth::user()->branch_id && $c->company_id != Auth::user()->company_id){
                throw new Exception('Access denied.');
            }

            if($request->status != 1 && $request->status != 0){
                throw new Exception('Invalid status.');
            }

            $b = Bank::where('id',$request->bank_id)->first();
            if(!$b){
                throw new Exception('Invalid bank');
            }

            $p->update([
                'account_no'=>$request->account_no,
                'owner_name'=>$request->owner_name,
                'is_active'=>$request->status,
                'company_id'=>$c->id,
                'branch_id'=>$c->branch_id,
                'bank_id'=>$request->bank_id
            ]);
            DB::commit();
            return response()->json(['success'=>true,'message'=>"Payment method created."]);
        }
        catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first()
            ]);
           
        }
        catch(Exception $e){
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

   public function update_credit(Request $request)
    {
        try {
            DB::beginTransaction();
         
            $v = $request->validate([
                'remark' => 'required|string',
                'amount' => 'required|numeric',
            ]);

            $p = PaymentMethod::where('id',$request->payment_method_id)->first();
            if(!isset($p)){
                throw new Exception('Unable to get payment method details.');
            }

            if(Auth::user()->role_id > 1){
                if($p->company_id != Auth::user()->company_id && $p->branch_id != Auth::user()->branch_id){
                    throw new Exception('Access denied');
                }
            }

            $previous = $p->amount;
          
            $after = $p->amount + $request->amount;
            $p->update(['amount'=>$after]);
            $p->payment_method_logs()->create([
                'payment_method_id' => $p->id,
                'type' => 'manual',
                'description' => $request->remark,
                'prev_amount' => $previous,
                'amount' => $request->amount,
                'total' => $after
            ]);
            DB::commit();
            return response()->json(['success'=>true,'message'=>"Payment method created."]);
        }
        catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first()
            ]);
           
        }
        catch(Exception $e){
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function search_payment_methods(Request $request)
    {
        $query = PaymentMethod::join('banks','banks.id','=','payment_methods.bank_id')->join('companies','companies.id','=','payment_methods.company_id')->where('companies.company_code',$request->company_code)->where('payment_methods.is_active',1)->select('payment_methods.*','companies.company_code','banks.bank_name');
        switch(Auth::user()->role_id){
            case 1:
                $pymt = $query->get();
            break;

            case 2:
                $pymt = $query->where('payment_methods.branch_id',Auth::user()->branch_id)->get();
            break;

            default:
                $pymt = $query->where('payment_methods.company_id',Auth::user()->company_id)->get();
        }
        return response()->json($pymt);
    }

    public function load_payment_method_logs(Request $request){
        try{
            $draw = $request->input('draw');
            $search = $request->input('search.value');
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);
            $orderByColumn = $request->input('columns')[$request->input('order.0.column')]['data'];
            $orderByDirection = $request->input('order.0.dir');
            $query = PaymentMethodLog::with('content')
                ->select([
                    'payment_method_logs.*',
                    'payment_methods.account_no',
                    'payment_methods.owner_name',
                    'companies.company_name as company_name',
                    'companies.company_code as company_code',
                    'branches.branch_name as branch_name',
                    'branches.branch_code as branch_code',
                ])
                ->join('payment_methods','payment_methods.id','=','payment_method_logs.payment_method_id')
                ->join('companies', 'payment_methods.company_id', '=', 'companies.id')
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
                    $q->Where('companies.company_name', 'like', "%{$search}%")
                        ->orWhere('companies.company_code', 'like', "%{$search}%")
                        ->orWhere('branches.branch_code', 'like', "%{$search}%")
                        ->orWhere('branches.branch_name', 'like', "%{$search}%")
                        ->orWhere('payment_methods.account_no', 'like', "%{$search}%")
                        ->orWhere('payment_methods.owner_name', 'like', "%{$search}%");
                });
            }

            $recordsTotal = $query->count();
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
}