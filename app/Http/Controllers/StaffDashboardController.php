<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Spatie\Browsershot\Browsershot;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Loan;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\PaymentSchedule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;
use Bouncer;
use Carbon\Carbon;

class StaffDashboardController extends Controller
{
    public function index(Request $request)
    {
        $query = Company::query();
        switch(Auth::user()->role_id){
            case 1:
                break;
            case 2:
                $query->where('branch_id', Auth::user()->branch_id);
                break;
            default:
                $query->where('id', Auth::user()->company_id);
                break;
        }
        $companies = $query->selectRaw('SUM(amount) as total_amount,SUM(stocka) as total_stocka, SUM(stockb) as total_stockb, SUM(stockbb) as total_stockbb')->first();
        return view('staffdashboard')->with('companies',$companies);
    }
    
    public function fetch_profit(Request $request)
    {
        switch(Auth::user()->role_id){
            case 1:
                $query = DB::table('loans');
                break;

            case 2:
                $userBranchId = Auth::user()->branch_id;
                $query = DB::table('loans')->join('companies.id','=','loans.company_id')->where('compaines.branch_id',$userBranchId);
                break;

            default:
                $companyId = Auth::user()->company_id;
                $query = DB::table('loans')->where('company_id',$companyId);
                break;
        }
        if(isset($request->loan_code)){
            $query->where('loan_code', $request->loan_code);
        }
        $loans = $query->get();
        $total_profit = 0;
        foreach($loans as $l){
            // DONT DELETE THIS CALCUALTION
            // if($l->interest_group == "SKIM A"){
            //     $total_profit += max(0,(($l->interest_paid + $l->late_paid) - ($l->capital - $l->paid)) - $l->discount);
            // }
            // if($l->interest_group == "SKIM B"){
    
            //     $total_loan = $l->first_payment + $l->last_payment + ($l->installment * ($l->loan_term - 2));
            //     $profit_ratio = ($total_loan - $l->capital) / $total_loan;
            //     $total_profit += ((($l->paid + $l->discount) * $profit_ratio) + $l->late_paid + $l->interest_paid) - $l->discount;
            // }
            if($l->interest_group == "SKIM A"){
                $total_profit += (($l->interest_paid + $l->late_paid + $l->paid) - ($l->capital));
            }
            if($l->interest_group == "SKIM B"){
                $total_profit += ($l->paid + $l->late_paid + $l->interest_paid) - $l->capital;
            }
        }
        return response()->json(['success'=>true,'total_profit'=>number_format($total_profit,2,'.',',')]);
    }

    public function load_loan(Request $request){
        try{
            $draw = $request->input('draw');
            $search = $request->input('search.value');
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);
            $orderByColumn = $request->input('columns')[$request->input('order.0.column')]['data'];
            $orderByDirection = $request->input('order.0.dir');
            $query = Loan::query()
                ->select([
                    'loans.*',
                    'customers.customer_name as customer_name',
                    'customers.nric_number as nric_number',
                    'customers.customer_code as customer_code',
                    'companies.company_name as company_name',
                    'companies.company_code as company_code',
                    'branches.branch_name as branch_name',
                    'branches.branch_code as branch_code',
                ])
                ->join('customers', 'customers.id', '=', 'loans.customer_id')
                ->join('companies', 'companies.id', '=', 'loans.company_id')
                ->join('branches', 'branches.id', '=', 'companies.branch_id');

            switch (Auth::user()->role_id) {
                case 1:
                    break;

                case 2:
                    $query->where('companies.branch_id', Auth::user()->branch_id);
                    break;

                case 3:
                case 4:
                    $query->where('loans.company_id', Auth::user()->company_id);
                    break;

                default:
                    throw new Exception('Invalid role id.');
            }

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('loans.loan_code', 'like', "%{$search}%")
                    ->orWhere('customers.customer_name', 'like', "%{$search}%")
                    ->orWhere('customers.nric_number', 'like', "%{$search}%")
                    ->orWhere('customers.customer_code', 'like', "%{$search}%")
                    ->orWhere('companies.company_name', 'like', "%{$search}%")
                    ->orWhere('companies.company_code', 'like', "%{$search}%")
                    ->orWhere('branches.branch_code', 'like', "%{$search}%")
                    ->orWhere('branches.branch_name', 'like', "%{$search}%");
                });
            }
            if($request->customer_code){
         
                $query->where('customers.customer_code',$request->customer_code);
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

    public function create(Request $request)
    {
        $customer = Customer::where('customer_code',$request->customer_code)->first();
        if(isset($request->loan_code)){
            $query->where('loan_code', $request->loan_code);
        }
        if(isset($customer) && $this->accessToCustomer($customer)){
            $company = $customer->company;
        }
        else{
            $company = false;
            $customer = false;
        }
        return view('loan.create')->with('company', $company)->with('customer', $customer);
    }

    public function calculate_loan(Request $request)
    {
        try {
            $v = $request->validate([
                'loan_amount' => 'required|numeric|min:1',
                'interest_rate' => 'required|numeric|min:0.0001|max:100',
                'loan_term' => 'nullable|integer|min:1|required_if:interest_group,SKIM B',
                'interest_group' => 'required|string|in:SKIM A,SKIM B',
            ]);
            
            bcscale(10);
            $amount = $v['loan_amount'];
            $rate = $v['interest_rate'];
            $term = $v['loan_term'];
            $type = $v['interest_group'];

            if($type == 'SKIM B'){
                $step1 = bcmul($amount, $rate, 10); 
                $step2 = bcdiv($step1, '100', 10);
                $step3 = bcmul($step2, $term, 10);
                $step4 = bcadd($step3, $amount, 10);
                $i = bcdiv($step4, $term, 2);
                $i =  ceil($i);  
                return response()->json([
                    'success' => true,
                    'data' => [
                        'amount' => (float) $i
                    ]
                ]);
            }
            else if($type == 'SKIM A'){
                $step1 = bcmul($amount,$rate, 10);
                $step2 = bcdiv($step1, '100', 10);
                $i = ceil($step2);
                return response()->json([
                    'success'=>true,
                    'data' => [
                        'amount' => (float) $i
                    ]
                    
                ]);
            }
        } 
        catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
    }

    public function calculate_interest(Request $request)
    {
        try {
            $v = $request->validate([
                'loan_amount' => 'required|numeric|min:1',
                'interest_group' => 'required|string|in:SKIM A,SKIM B',
                'loan_term' => 'nullable|integer|min:1|required_if:interest_group,SKIM B',
                'first_payment' => 'nullable|numeric|min:0|required_if:interest_group,SKIM B',
                'last_payment' => 'nullable|numeric|min:0|required_if:interest_group,SKIM B',
                'installment' => 'required|numeric|min:0.01'
            ]);
            bcscale(10);
            $amount = $v['loan_amount'];
            $total_term = $v['loan_term'];
            $type = $v['interest_group'];
            $first = $v['first_payment'];
            $last = $v['last_payment'];
            $installment = $v['installment'];
            $term = bcsub($total_term, '2', 2);
            $total = bcadd($first, $last, 2);
            if($type == 'SKIM B'){
                $step1 = bcmul($installment, $term, 2);
                $step2 = bcadd($step1, $total, 2);
                $step3 = bcsub($step2, $amount, 2);
                $step4 = bcdiv($step3, $total_term, 4);
                $step5 = bcdiv($amount, '100', 10);
                $i = round(bcdiv($step4, $step5, 5), 4);
                return response()->json([
                    'success' => true,
                    'data' => [
                        'amount' => (float) $i
                    ]
                ]);
            }
            else if($type == 'SKIM A'){
                $step1 = bcmul($installment, '100', 10);
                $i = round(bcdiv($step1, $amount, 10),4);
                return response()->json([
                    'success'=>true,
                    'data' => [
                        'amount' => (float) $i
                    ]
                    
                ]);
            }
        }
        catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
    }

    public function search_customer(Request $request)
    {
        $search = $request->get('search');
        switch(Auth::user()->role_id){
            case 1:
                $customers = Customer::where(function($query) use ($search) {
                    $query->where('customer_code', 'LIKE', "%{$search}%")
                        ->orWhere('customer_name', 'LIKE', "%{$search}%")
                        ->orWhere('nric_number', 'LIKE', "%{$search}%");
                })
                ->select('customer_code', 'customer_name', 'nric_number', 'company_code')
                ->limit(10)
                ->get();
            break;

            case 2:
                $userBranchId = Auth::user()->branch_id;
                $customers = Customer::join('companies', 'customers.company_id', '=', 'companies.id')
                    ->where('companies.branch_id', $userBranchId)
                    ->where(function($query) use ($search) {
                        $query->where('customers.customer_code', 'LIKE', "%{$search}%")
                            ->orWhere('customers.customer_name', 'LIKE', "%{$search}%")
                            ->orWhere('customers.nric_number', 'LIKE', "%{$search}%");
                    })
                    ->select('customers.customer_code', 'customers.customer_name', 'customers.nric_number', 'company_code')
                    ->limit(10)
                    ->get();
            break;

            default:
                $companyId = Auth::user()->company_id;
                $customers = Customer::where('company_id', $companyId)
                ->where(function($query) use ($search) {
                    $query->where('customer_code', 'LIKE', "%{$search}%")
                        ->orWhere('customer_name', 'LIKE', "%{$search}%")
                        ->orWhere('nric_number', 'LIKE', "%{$search}%");
                })
                ->select('customer_code', 'customer_name', 'nric_number', 'company_code')
                ->limit(10)
                ->get();
        }
        return response()->json($customers);
    }

    public function search_loan(Request $request)
    {
        $search = $request->get('search');
        switch(Auth::user()->role_id){
            case 1:
                $loans = Loan::join('customers', 'loans.customer_id', '=', 'customers.id')
                    ->join('companies', 'loans.company_id', '=', 'companies.id')
                    ->where(function($query) use ($search) {
                        $query->where('loans.loan_code', 'LIKE', "%{$search}%");
                    });
                break;

            case 2:
                $userBranchId = Auth::user()->branch_id;
                $loans = Loan::join('companies', 'loans.company_id', '=', 'companies.id')
                    ->join('customers', 'loans.customer_id', '=', 'customers.id')
                    ->where('companies.branch_id', $userBranchId)
                    ->where(function($query) use ($search) {
                        $query->where('loans.loan_code', 'LIKE', "%{$search}%");
                    });
                break;

            default:
                $companyId = Auth::user()->company_id;
                $loans = Loan::join('customers', 'loans.customer_id', '=', 'customers.id')
                    ->join('companies', 'loans.company_id', '=', 'companies.id')
                    ->where('loans.company_id', $companyId)
                    ->where(function($query) use ($search) {
                        $query->where('loans.loan_code', 'LIKE', "%{$search}%");
                    });
        }
        $data = $loans->select(
            'loans.loan_code',
            'customers.customer_name',
            'customers.customer_code',
            'companies.company_code',
            DB::raw('loans.payment - (loans.paid + loans.discount) as total_payment_balance'),
            DB::raw('loans.late - loans.late_paid as total_late_balance'),
            DB::raw('loans.interest - loans.interest_paid as total_interest_balance')
        )->limit(10)->get();
        return response()->json($data);
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $v = $request->validate([
                'loan_amount' => 'required|numeric|min:1',
                'interest_group' => 'required|string|in:SKIM A,SKIM B',
                'loan_term' => 'nullable|integer|min:1|required_if:interest_group,SKIM B',
                'first_payment' => 'nullable|numeric|min:0|required_if:interest_group,SKIM B',
                'last_payment' => 'nullable|numeric|min:0|required_if:interest_group,SKIM B',
                'installment' => 'required|numeric|min:0.01',
                'customer_code' => 'required|string',
                'year_month' => 'required|string',
                'interest_rate' => 'required|numeric',
                'processing_fee' => 'nullable|numeric',
                'stamp_fee' => 'nullable|numeric',
                'alternate_code' => 'nullable|string',
                'receipt_no' => 'nullable|string'
            ]);
            bcscale(10);
            $customer = Customer::where('customer_code',$v['customer_code'])->first();
            if(!$customer || $this->accessToCustomer($customer) == false){
                throw new Exception('Invalid customer code.');
            }

            $pym = PaymentMethod::where('id',$request->payment_method_id)->first();
            if(!isset($pym)){
                throw new Exception('Invalid payment method.');
            }
            if(Auth::user()->role_id > 1){
                if($pym->branch_id != Auth::user()->branch_id && $pym->company_id != Auth::user()->company_id){
                    throw new Exception('Invalid payment method #2. ');
                }
            }

            $prefix = $customer->customer_code.'-' ?? $customer->company_code."LN";
            $loan_code = $this->getSequenceNumber($prefix,'loan_code');
            
            $processing_fee = $v['processing_fee'] ?? 0;
            $loan_amount = $v['loan_amount'] ?? 0;
            $first_payment = $v['first_payment'] ?? 0;
            $last_payment = $v['last_payment'] ?? 0;
            $installment = $v['installment'];
            $stamp_fee = $v['stamp_fee'] ?? 0;
            $alternate_code = $v['alternate_code'] ?? null;
            $receipt_no = $v['receipt_no'] ?? null;
            $loan_term = $v['loan_term'] ?? null;

            $c = bcadd($processing_fee, $stamp_fee, 2);
            $capital = bcsub($loan_amount, $c, 2);
            $company = $customer->company;
            if($v['interest_group'] == 'SKIM A'){
                $l = Loan::create([
                    'loan_code' => $loan_code,
                    'company_id' => $company->id,
                    'customer_id' => $customer->id,
                    'year_month' => Carbon::parse($v['year_month'])->format('Y-m-d'),
                    'interest_group' => $v['interest_group'],
                    'loan_term' => $v['loan_term'],
                    'loan_amount' => $loan_amount,
                    'interest_rate' => $v['interest_rate'],
                    'processing_fee' => $processing_fee,
                    'stamp_fee' => $stamp_fee,
                    'capital' => $capital,
                    'payment' => $loan_amount,
                    'balance' => $loan_amount,
                    'interest' => $installment,
                    'interest_balance' => $installment,
                    'outstanding' => $loan_amount + $installment,
                    'next_due_date' => Carbon::now()->addMonths(1)->format('Y-m-d'),
                    'next_due_amount' => ($loan_amount/100) * $v['interest_rate'],
                    'alternate_code' => $alternate_code,
                    'receipt_no' => $receipt_no,
                    'payment_method_id'=>$pym->id,
                    'created_by' => Auth::user()->id
                ]);

                $abefore = $company->stocka;
                $aamount = $l->loan_amount;
                $aafter = $company->stocka + $l->loan_amount;
                $l->stock_logs()->create([
                    'company_id'=>$company->id,
                    'loan_id'=>$l->id,
                    'type'=>'add',
                    'description'=>'Payment Created',
                    'stock_type'=>'a',
                    'prev_amount'=>$abefore,
                    'amount'=>$aamount,
                    'total'=>$aafter
                ]);
                $company->update(['stocka'=>$aafter]);
            }

            else if($v['interest_group'] == 'SKIM B'){
                $total_loan = $first_payment + $last_payment + ($installment * ($loan_term - 2));
                $l = Loan::create([
                    'loan_code' => $loan_code,
                    'company_id' => $company->id,
                    'customer_id' => $customer->id,
                    'year_month' => Carbon::parse($v['year_month'])->format('Y-m-d'),
                    'interest_group' => $v['interest_group'],
                    'loan_term' => $loan_term,
                    'first_payment' => $first_payment,
                    'last_payment' => $last_payment,
                    'installment' => $installment,
                    'loan_amount' => $loan_amount,
                    'interest_rate' => $v['interest_rate'],
                    'processing_fee' => $processing_fee,
                    'stamp_fee' => $stamp_fee,
                    'capital' => $capital,
                    'payment' => $total_loan,
                    'balance' => $total_loan,
                    'outstanding' => $total_loan,
                    'next_due_date' => Carbon::now()->addMonths(1)->format('Y-m-d'),
                    'next_due_amount' => $first_payment,
                    'alternate_code' => $alternate_code,
                    'receipt_no' => $receipt_no,
                    'payment_method_id'=>$pym->id,
                    'created_by' => Auth::user()->id
                ]);

                $bbefore = $company->stockb;
                $bamount = $l->loan_amount;
                $bafter = $company->stockb + $l->loan_amount;
                $l->stock_logs()->create([
                    'company_id'=>$company->id,
                    'loan_id'=>$l->id,
                    'type'=>'add',
                    'stock_type'=>'b',
                    'description'=>'Payment Created',
                    'prev_amount'=>$bbefore,
                    'amount'=>$bamount,
                    'total'=>$bafter
                ]);

                $bbbefore = $company->stockbb;
                $bbamount = $l->payment;
                $bbafter = $company->stockbb + $l->payment;
                $l->stock_logs()->create([
                    'company_id'=>$company->id,
                    'loan_id'=>$l->id,
                    'type'=>'add',
                    'stock_type'=>'bb',
                    'description'=>'Payment Created',
                    'prev_amount'=>$bbbefore,
                    'amount'=>$bbamount,
                    'total'=>$bbafter
                ]);

                $company->update(['stockb'=>$bafter, 'stockbb'=>$bbafter]);
            }
            
            $pym_amount = ($l->capital) * -1;
            $pym_before = $pym->amount;
            $pym_after = $pym->amount + $pym_amount;
            $pym->update(['amount'=>$pym_after]);
            $l->payment_method_logs()->create([
                'payment_method_id' => $pym->id,
                'type' => 'loan',
                'description' => 'Loan Created',
                'prev_amount' => $pym_before,
                'amount' => $pym_amount,
                'total' => $pym_after
            ]);
            

     
            if(!$this->createPaymentSchedule($l)){
                throw new Exception('Failed to create payment schedule.');
            }
            DB::commit();
            return response()->json(['success'=>true,'message'=>"Loan created successfully!"]);
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

    public function createPaymentSchedule(Loan $loan)
    {
        try{
            switch($loan->interest_group){
                case 'SKIM A':
                    $prefix = $loan->loan_code.'-S' ?? $customer->company_code."LN";
                    $schedule_code = $this->getSequenceNumber($prefix,'schedule_code');
                    $start_date = Carbon::parse($loan->year_month);
                    PaymentSchedule::create([
                        'schedule_code'=>$schedule_code,
                        'loan_code'=>$loan->loan_code,
                        'company_id'=>$loan->company_id,
                        'customer_id'=>$loan->customer_id,
                        'due_date'=>$start_date->copy()->addMonths(1),
                        'interest_amount'=>$loan->interest
                    ]);
                    return true;
                break;

                case 'SKIM B':
                    $start_date = Carbon::parse($loan->year_month);
                    for ($i = 1; $i <= $loan->loan_term; $i++) {
                        $amount = $loan->installment;
                        if($i == 1){
                            $amount = $loan->first_payment;
                        }
                        else if($i == $loan->loan_term){
                            $amount = $loan->last_payment;
                        }
                        $prefix = $loan->loan_code.'-S';
                        $schedule_code = $this->getSequenceNumber($prefix,'schedule_code');
                        PaymentSchedule::create([
                            'schedule_code' => $schedule_code,
                            'loan_code'=>$loan->loan_code,
                            'company_id'=>$loan->company_id,
                            'customer_id'=>$loan->customer_id,
                            'due_date'=>$start_date->copy()->addMonths($i),
                            'payment_amount'=>$amount
                        ]);
                    }
                    return true;
                break;

                default:
                    throw new Exception('Invalid interest group.');
            }
        }
        catch(Exception $e){
            return false;
        }
    }

    public function single_loan(Request $request){
        try{
            $loan = Loan::where('loan_code',$request->loan_code)->first();
            if($loan){
                if($this->AccessToLoan($loan) == false){
                    throw new Exception('No loan found.');
                }
            }
            return view('loan.single')->with('success',true)->with('loan',$loan);
        }
        catch(Exception $e){
            return view('loan.single')->with('success',false)->with('error',$e->getMessage());
        }
    }

    public function update_due_date(Loan $loan){
        $schedule = PaymentSchedule::where('loan_code', $loan->loan_code)
            ->whereRaw('(payment_amount - (paid_amount + discount_amount)) + (interest_amount - interest_paid_amount) + (late_amount - late_paid_amount) > 0')
            ->orderBy('due_date', 'asc')
            ->first();

        if ($schedule) {
            $dueDate = $schedule->due_date;
            $schedules = PaymentSchedule::where('loan_code', $loan->loan_code)
                ->whereDate('due_date', $dueDate)
                ->get();

            $totalDue = $schedules->sum(function ($s) {
                return
                    ($s->payment_amount - ($s->paid_amount + $s->discount_amount)) +
                    ($s->interest_amount - $s->interest_paid_amount) +
                    ($s->late_amount - $s->late_paid_amount);
            });

            $loan->update([
                'next_due_date' => $dueDate,
                'next_due_amount' => $totalDue,
            ]);
        } else {
            $loan->update([
                'next_due_date' => null,
                'next_due_amount' => 0,
            ]);
        }
    }

    public function update_outstanding(Loan $loan){
        $outstanding = $loan->balance + $loan->late_balance + $loan->interest_balance;
        $loan->update(['outstanding'=>$outstanding, 'status'=>$outstanding > 0 ? 'Ongoing' : 'Fully Paid']);
    }

    public function update_loan_misc(Loan $loan){
        $this->update_due_date($loan);
        $this->update_outstanding($loan);
    }
}