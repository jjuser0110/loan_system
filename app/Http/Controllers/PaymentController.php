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
use App\Models\PaymentSchedule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\LoanController;
use Exception;
use Bouncer;
use Carbon\Carbon;

class PaymentController extends Controller
{
    protected $loanController;
    
    public function __construct(LoanController $loanController, ScheduleController $scheduleController)
    {
        $this->loanController = $loanController;
        $this->scheduleController = $scheduleController;
    }

    public function index(Request $request)
    {
        return view('payment.index');
    }

    public function load_payment(Request $request){
        try{
            $draw = $request->input('draw');
            $search = $request->input('search.value');
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);
            $orderByColumn = $request->input('columns')[$request->input('order.0.column')]['data'];
            $orderByDirection = $request->input('order.0.dir');
            $query = Payment::query()
                ->select([
                    'payments.*',
                    'customers.customer_name as customer_name',
                    'customers.nric_number as nric_number',
                    'customers.customer_code as customer_code',
                    'companies.company_name as company_name',
                    'companies.company_code as company_code',
                    'branches.branch_name as branch_name',
                    'branches.branch_code as branch_code',
                    'users.username as created_by_name',
                    'loans.loan_code as loan_code'
                ])
                ->join('customers', 'customers.id', '=', 'payments.customer_id')
                ->join('users', 'users.id', '=', 'payments.created_by')
                ->join('loans', 'loans.id', '=', 'payments.loan_id')
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
                    $q->where('payments.payment_code', 'like', "%{$search}%")
                    ->orWhere('customers.customer_name', 'like', "%{$search}%")
                    ->orWhere('customers.nric_number', 'like', "%{$search}%")
                    ->orWhere('customers.customer_code', 'like', "%{$search}%")
                    ->orWhere('companies.company_name', 'like', "%{$search}%")
                    ->orWhere('companies.company_code', 'like', "%{$search}%")
                    ->orWhere('branches.branch_code', 'like', "%{$search}%")
                    ->orWhere('branches.branch_name', 'like', "%{$search}%");
                });
            }

            if(isset($request->loan_code)){
                $query->where('loans.loan_code', $request->loan_code);
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

    public function load_payment_schedule(Request $request){
        try{
            $draw = $request->input('draw');
            $search = $request->input('search.value');
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);
            $orderByColumn = $request->input('columns')[$request->input('order.0.column')]['data'];
            $orderByDirection = $request->input('order.0.dir');
            $query = PaymentSchedule::query()
                ->select([
                    'payment_schedules.*',
                    'customers.customer_name as customer_name',
                    'customers.nric_number as nric_number',
                    'customers.customer_code as customer_code',
                    'companies.company_name as company_name',
                    'companies.company_code as company_code',
                    'branches.branch_name as branch_name',
                    'branches.branch_code as branch_code',
                    'loans.next_due_date',
                    'loans.next_due_amount'
                ])
                ->join('customers', 'customers.id', '=', 'payment_schedules.customer_id')
                ->join('companies', 'companies.id', '=', 'payment_schedules.company_id')
                ->join('branches', 'branches.id', '=', 'companies.branch_id')
                ->join('loans', 'loans.loan_code', '=', 'payment_schedules.loan_code');

            switch (Auth::user()->role_id) {
                case 1:
                    break;

                case 2:
                    $query->where('companies.branch_id', Auth::user()->branch_id);
                    break;

                case 3:
                case 4:
                    $query->where('payment_schedules.company_id', Auth::user()->company_id);
                    break;

                default:
                    throw new Exception('Invalid role id.');
            }
            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('payment_schedules.loan_code', 'like', "%{$search}%")
                    ->orWhere('customers.customer_name', 'like', "%{$search}%")
                    ->orWhere('customers.nric_number', 'like', "%{$search}%")
                    ->orWhere('customers.customer_code', 'like', "%{$search}%")
                    ->orWhere('companies.company_name', 'like', "%{$search}%")
                    ->orWhere('companies.company_code', 'like', "%{$search}%")
                    ->orWhere('branches.branch_code', 'like', "%{$search}%")
                    ->orWhere('branches.branch_name', 'like', "%{$search}%")
                    ->orWhere('payments.payment_code', 'like', "%{$search}%");
                });
            }
            if(isset($request->loan_code)){
                $query->where('payment_schedules.loan_code', $request->loan_code);
            }

            if(isset($request->start_date) && $request->end_date){
                $query->whereBetween('loans.next_due_date', [
                    $request->start_date,
                    $request->end_date
                ]);
            }

            $totalsQuery = clone $query;
            $allData = $totalsQuery->get();
            $uniqueLoans = $allData->unique('loan_code');
            $total_due_amount = $uniqueLoans->sum('next_due_amount');
            $recordsTotal = $query->count();
            $data = $query->orderBy($orderByColumn, $orderByDirection)->skip($start)->take($length)->get();
            return response()->json([
                "draw" => intval($draw),
                "recordsTotal" => $recordsTotal,
                "recordsFiltered" => $recordsTotal,
                "data" => $data,
                'total_due_amount' => $total_due_amount
            ]);
        }
        catch(Exception $e){
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function create(Request $request)
    {
        $loan = Loan::where('loan_code',$request->loan_code)->first();
        if(!isset($loan) || $this->accessToLoan($loan) == false){
            $loan = null;
        }
        return view('payment.create')->with('loan', $loan);
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

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $v = $request->validate([
                'loan_code' => 'required|string',
                'payment_amount' => 'nullable|numeric|min:0',
                'discount_amount' => 'nullable|numeric|min:0',
                'late_paid_amount' => 'nullable|numeric',
                'interest_paid_amount' => 'nullable|numeric',
                'collection_type' => 'nullable|string',
                'cheque' => 'nullable|string',
                'bank'=> 'nullable|string',
            ]);
            bcscale(10);
            $loan = Loan::lockForUpdate()->where('loan_code',$v['loan_code'])->first();
            if(!$loan){
                throw new Exception('Loan not found.');
            }
            $prefix = $loan->loan_code.'-P' ?? $customer->company_code."LN";
            $payment_code = $this->getSequenceNumber($prefix,'payment_code');
            $payment_amount = $v['payment_amount'] ?? 0;
            $discount_amount = $v['discount_amount'] ?? 0;
            $total_payment = $payment_amount + $discount_amount;
            $late_paid_amount = $v['late_paid_amount'] ?? 0;
            $interest_paid_amount = $v['interest_paid_amount'] ?? 0;
            if ($late_paid_amount > $loan->late_balance) {
                throw new Exception('Late payment exceeds remaining late balance.');
            }

            if ($total_payment > $loan->balance) {
                throw new Exception('Payment exceeds remaining loan balance.');
            }

            if ($interest_paid_amount > $loan->interest_balance) {
                throw new Exception('Interest payment exceeds remaining interest balance.');
            }
            if($loan->interest_group == "SKIM A"){
                $schedules = PaymentSchedule::lockForUpdate()
                    ->where('loan_code',$loan->loan_code)
                    ->whereRaw('interest_amount - interest_paid_amount > 0')
                    ->orderBy('due_date', 'asc')
                    ->get();
                
                $remainPayment = $interest_paid_amount;
                foreach($schedules as $s){
                    $unpaid = $s->interest_amount - $s->interest_paid_amount;
                    if ($remainPayment <= 0) break;
                    if ($remainPayment >= $unpaid) {
                        $s->update(['interest_paid_amount' => $s->interest_paid_amount + $unpaid]);
                        $remainPayment -= $unpaid;
                    } else {
                        $s->update(['interest_paid_amount' => $s->interest_paid_amount + $remainPayment]);
                        $remainPayment = 0;
                        break;
                    }
                }

                // if($remainPayment > 0){
                //     $lastSchedule = PaymentSchedule::where('loan_code',$loan->loan_code)
                //         ->orderBy('due_date','desc')
                //         ->first();
                    
                //     $currentBalance = $loan->balance - $total_payment;
                //     $monthlyInterest = ($currentBalance / 100) * $loan->interest_rate;
                //     while($remainPayment > 0 && $monthlyInterest > 0){
                //         $newInterestAmount = ($currentBalance / 100) * $loan->interest_rate;
                //         $payForThisSchedule = min($remainPayment, $newInterestAmount);
                //         $nextDueDate = $lastSchedule 
                //             ? Carbon::parse($lastSchedule->due_date)->addMonths(1)->format('Y-m-d')
                //             : Carbon::now()->addMonths(1)->format('Y-m-d');
                //         $newSchedule = PaymentSchedule::create([
                //             'loan_code' => $loan->loan_code,
                //             'company_id' => $loan->company_id,
                //             'customer_id' => $loan->customer_id,
                //             'due_date' => $nextDueDate,
                //             'interest_amount' => $newInterestAmount,
                //             'interest_paid_amount' => $payForThisSchedule,
                //             'late_amount' => 0,
                //             'late_paid_amount' => 0,
                //         ]);
                        
                //         $remainPayment -= $payForThisSchedule;
                //         $lastSchedule = $newSchedule;
                //         if($currentBalance < 100) break;
                //     }
                // }


                $remain_late_paid = $late_paid_amount;
                $lateSchedules = PaymentSchedule::lockForUpdate()
                    ->where('loan_code', $loan->loan_code)
                    ->whereRaw('late_amount - late_paid_amount > 0')
                    ->orderBy('due_date', 'asc')
                    ->get();
                
                foreach ($lateSchedules as $ls) {
                    $unpaidLate = $ls->late_amount - $ls->late_paid_amount;
                    if ($remain_late_paid <= 0) break;
                    if ($remain_late_paid >= $unpaidLate) {
                        $ls->update(['late_paid_amount' => $ls->late_paid_amount + $unpaidLate]);
                        $remain_late_paid -= $unpaidLate;
                    } else {
                        $ls->update(['late_paid_amount' => $ls->late_paid_amount + $remain_late_paid]);
                        $remain_late_paid = 0;
                        break;
                    }
                }
                $newBalance = $loan->balance - $total_payment;
                $interest_balance_change = 0;
                $next_due_date = null;
                $next_due_amount = null;
                
                if($total_payment >= $loan->payment){
                    $removeTarget = PaymentSchedule::lockForUpdate()
                        ->where('loan_code',$loan->loan_code)
                        ->orderBy('due_date', 'desc')
                        ->first();
                    
                    if($removeTarget){
                        $interest_balance_change = $removeTarget->interest_amount * -1;
                        $removeTarget->delete();
                    }
                } 
                else {
                    $nextSchedule = PaymentSchedule::lockForUpdate()
                        ->where('loan_code',$loan->loan_code)
                        ->whereRaw('interest_amount - interest_paid_amount > 0')
                        ->orderBy('due_date', 'asc')
                        ->first();
                    
                    if(!$nextSchedule){
                        $newInterestAmount = ($newBalance / 100) * $loan->interest_rate;
                        $lastSchedule = PaymentSchedule::where('loan_code',$loan->loan_code)->orderBy('due_date','desc')->first();
                        $nextSchedule = PaymentSchedule::create([
                            'loan_code' => $loan->loan_code,
                            'company_id' => $loan->company_id,
                            'customer_id' => $loan->customer_id,
                            'due_date' => Carbon::parse($lastSchedule->due_date)->addMonths(1)->format('Y-m-d'),
                            'interest_amount' => $newInterestAmount,
                            'interest_paid_amount' => 0,
                        ]);
                        $interest_balance_change = $newInterestAmount;
                    }
                }
                
                $loan->update([
                    'paid' => $loan->paid + $payment_amount,
                    'balance' => $newBalance,
                    'interest' => $loan->interest + $interest_balance_change,
                    'interest_paid' => $loan->interest_paid + $interest_paid_amount,
                    'interest_balance' => ($loan->interest + $interest_balance_change) - ($loan->interest_paid + $interest_paid_amount),
                    'late_paid' => $loan->late_paid + $late_paid_amount,
                    'late_balance' => $loan->late_balance - $late_paid_amount,
                    'discount' => $loan->discount + $discount_amount,
                ]);
                $this->loanController->update_loan_misc($loan);
            }

            else if($loan->interest_group == "SKIM B"){
                $schedules = PaymentSchedule::lockForUpdate()
                    ->where('loan_code', $loan->loan_code)
                    ->whereRaw('payment_amount - (paid_amount + discount_amount) > 0')
                    ->orderBy('due_date', 'asc')
                    ->get();
                $remainingPayment = $payment_amount;
                $remainingDiscount = $discount_amount;
                foreach($schedules as $s){
                    $owed = $s->payment_amount - ($s->paid_amount + $s->discount_amount);
                    if($remainingPayment <= 0 && $remainingDiscount <= 0){
                        break;
                    }
                    
                    if($remainingPayment > 0){
                        $paymentToApply = min($remainingPayment, $owed);
                        $s->paid_amount += $paymentToApply;
                        $remainingPayment -= $paymentToApply;
                        $owed -= $paymentToApply;
                    }
                    
                    if($remainingDiscount > 0 && $owed > 0){
                        $discountToApply = min($remainingDiscount, $owed);
                        $s->discount_amount += $discountToApply;
                        $remainingDiscount -= $discountToApply;
                        $owed -= $discountToApply;
                    }
                    $s->save();
                }

                if($late_paid_amount > 0){
                    $remainLatePaid = $late_paid_amount;
                    $lateSchedules = PaymentSchedule::lockForUpdate()
                        ->where('loan_code', $loan->loan_code)
                        ->whereRaw('late_amount - late_paid_amount > 0')
                        ->orderBy('due_date', 'asc')
                        ->get();
                    
                    foreach($lateSchedules as $lateSchedule){
                        $unpaidLate = $lateSchedule->late_amount - $lateSchedule->late_paid_amount;
                        if($remainLatePaid <= 0) break;
                        
                        if($remainLatePaid >= $unpaidLate){
                            $lateSchedule->late_paid_amount += $unpaidLate;
                            $remainLatePaid -= $unpaidLate;
                        } else {
                            $lateSchedule->late_paid_amount += $remainLatePaid;
                            $remainLatePaid = 0;
                        }
                        
                        $lateSchedule->save();
                        
                        if($remainLatePaid <= 0) break;
                    }
                }

                if($interest_paid_amount > 0){
                    $remainInterestPaid = $interest_paid_amount;
                    $interestSchedules = PaymentSchedule::lockForUpdate()
                        ->where('loan_code', $loan->loan_code)
                        ->whereRaw('interest_amount - interest_paid_amount > 0')
                        ->orderBy('due_date', 'asc')
                        ->get();
                    
                    foreach($interestSchedules as $is){
                        $unpaidInterest = $is->interest_amount - $is->interest_paid_amount;
                        
                        if($remainInterestPaid <= 0) break;
                        
                        if($remainInterestPaid >= $unpaidInterest){
                            $is->interest_paid_amount += $unpaidInterest;
                            $remainInterestPaid -= $unpaidInterest;
                        } else {
                            $is->interest_paid_amount += $remainInterestPaid;
                            $remainInterestPaid = 0;
                        }
                        $is->save();
                        if($remainInterestPaid <= 0) break;
                    }
                }

                $loan->update([
                    'paid' => $loan->paid + $payment_amount,
                    'balance' => $loan->balance - $total_payment,
                    'interest_paid' => $loan->interest_paid + $interest_paid_amount,
                    'interest_balance' => $loan->interest - ($loan->interest_paid + $interest_paid_amount),
                    'late_paid' => $loan->late_paid + $late_paid_amount,
                    'late_balance' => $loan->late_balance - $late_paid_amount,
                    'discount' => $loan->discount + $discount_amount
                ]);

                $this->loanController->update_loan_misc($loan);
            }

            else{
                throw new Exception('Invalid interest group.');
            }

            Payment::create([
                'payment_code'=>$payment_code,
                'customer_id'=>$loan->customer_id,
                'loan_id'=>$loan->id,
                'late_paid_amount'=>$late_paid_amount,
                'interest_paid_amount'=>$interest_paid_amount,
                'payment_amount'=>$payment_amount,
                'discount_amount'=>$discount_amount,
                'collection_type'=>$v['collection_type'],
                'cheque'=>$v['cheque'],
                'bank'=>$v['bank'],
                'created_by'=>Auth::user()->id
            ]);

            DB::commit();
            return response()->json(['success'=>true,'message'=>"Payment created."]);
        }
        catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first()
            ]);
            DB::rollback();
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
            $v = $request->validate([
                'payment_id' => 'required|numeric',
                'payment_amount' => 'nullable|numeric|min:0',
                'discount_amount' => 'nullable|numeric|min:0',
                'late_paid_amount' => 'nullable|numeric',
                'interest_paid_amount' => 'nullable|numeric',
                'collection_type' => 'nullable|string',
                'cheque' => 'nullable|string',
                'bank'=> 'nullable|string',
            ]);
            bcscale(10);
            
            $payment = Payment::lockForUpdate()->where('id',$request->payment_id)->first();
            if(!$payment || $this->accesstoPayment($payment) == false){
                throw new Exception('Failed to get selected payment.');
            }

            $loan = Loan::lockForUpdate()->where('id', $payment->loan_id)->first();
            if(!$loan){
                throw new Exception('Loan not found.');
            }

            $old_payment_amount = $payment->payment_amount ?? 0;
            $old_interest_paid = $payment->interest_paid_amount ?? 0;
            $old_late_paid = $payment->late_paid_amount ?? 0;
            $old_discount = $payment->discount_amount ?? 0;
            $old_total = $old_payment_amount + $old_discount;

            $new_payment_amount = $v['payment_amount'] ?? 0;
            $new_interest_paid = $v['interest_paid_amount'] ?? 0;
            $new_late_paid = $v['late_paid_amount'] ?? 0;
            $new_discount = $v['discount_amount'] ?? 0;
            $new_total = $new_payment_amount + $new_discount;

            $diff_payment = $new_payment_amount - $old_payment_amount;
            $diff_interest_paid = $new_interest_paid - $old_interest_paid;
            $diff_late_paid = $new_late_paid - $old_late_paid;
            $diff_discount = $new_discount - $old_discount;
            $diff_total = $new_total - $old_total;

            if ($diff_late_paid > $loan->late_balance) {
                throw new Exception('Late payment exceeds remaining late balance.');
            }

            if ($diff_total > $loan->balance) {
                throw new Exception('Payment exceeds remaining loan balance.');
            }

            if ($diff_interest_paid > $loan->interest_balance) {
                throw new Exception('Interest payment exceeds remaining interest balance.');
            }

            if ($loan->interest_group == "SKIM A") {
                if ($diff_interest_paid != 0) {
                    if ($diff_interest_paid > 0) {
                        $schedules = PaymentSchedule::lockForUpdate()
                            ->where('loan_code', $loan->loan_code)
                            ->whereRaw('interest_amount - interest_paid_amount > 0')
                            ->orderBy('due_date', 'asc')
                            ->get();
                        
                        $remainPayment = $diff_interest_paid;
                        foreach ($schedules as $s) {
                            $unpaid = $s->interest_amount - $s->interest_paid_amount;
                            if ($remainPayment <= 0) break;
                            if ($remainPayment >= $unpaid) {
                                $s->update(['interest_paid_amount' => $s->interest_paid_amount + $unpaid]);
                                $remainPayment -= $unpaid;
                            } else {
                                $s->update(['interest_paid_amount' => $s->interest_paid_amount + $remainPayment]);
                                $remainPayment = 0;
                                break;
                            }
                        }
                    } else {
                        $schedules = PaymentSchedule::lockForUpdate()
                            ->where('loan_code', $loan->loan_code)
                            ->where('interest_paid_amount', '>', 0)
                            ->orderBy('due_date', 'desc')
                            ->get();
                        
                        $remainDeduct = abs($diff_interest_paid);
                        foreach ($schedules as $s) {
                            if ($remainDeduct <= 0) break;
                            $paid = $s->interest_paid_amount;
                            
                            if ($paid >= $remainDeduct) {
                                $s->update(['interest_paid_amount' => $s->interest_paid_amount - $remainDeduct]);
                                $remainDeduct = 0;
                                break;
                            } else {
                                $s->update(['interest_paid_amount' => 0]);
                                $remainDeduct -= $paid;
                            }
                        }
                    }
                }

                if ($diff_late_paid != 0) {
                    if ($diff_late_paid > 0) {
                        $lateSchedules = PaymentSchedule::lockForUpdate()
                            ->where('loan_code', $loan->loan_code)
                            ->whereRaw('late_amount - late_paid_amount > 0')
                            ->orderBy('due_date', 'asc')
                            ->get();
                        
                        $remain_late_paid = $diff_late_paid;
                        foreach ($lateSchedules as $ls) {
                            $unpaidLate = $ls->late_amount - $ls->late_paid_amount;
                            if ($remain_late_paid <= 0) break;
                            if ($remain_late_paid >= $unpaidLate) {
                                $ls->update(['late_paid_amount' => $ls->late_paid_amount + $unpaidLate]);
                                $remain_late_paid -= $unpaidLate;
                            } else {
                                $ls->update(['late_paid_amount' => $ls->late_paid_amount + $remain_late_paid]);
                                $remain_late_paid = 0;
                                break;
                            }
                        }
                    } else {
                        $lateSchedules = PaymentSchedule::lockForUpdate()
                            ->where('loan_code', $loan->loan_code)
                            ->where('late_paid_amount', '>', 0)
                            ->orderBy('due_date', 'desc')
                            ->get();
                        
                        $remainDeduct = abs($diff_late_paid);
                        foreach ($lateSchedules as $ls) {
                            if ($remainDeduct <= 0) break;
                            $paidLate = $ls->late_paid_amount;
                            
                            if ($paidLate >= $remainDeduct) {
                                $ls->update(['late_paid_amount' => $ls->late_paid_amount - $remainDeduct]);
                                $remainDeduct = 0;
                                break;
                            } else {
                                $ls->update(['late_paid_amount' => 0]);
                                $remainDeduct -= $paidLate;
                            }
                        }
                    }
                }

                $newBalance = $loan->balance - $diff_total;
                $interest_balance_change = 0;

                if ($new_total >= $loan->payment) {
                    $removeTarget = PaymentSchedule::lockForUpdate()
                        ->where('loan_code', $loan->loan_code)
                        ->orderBy('due_date', 'desc')
                        ->first();
                    
                    if ($removeTarget) {
                        $interest_balance_change = $removeTarget->interest_amount * -1;
                        $removeTarget->delete();
                    }
                } else {
                    $nextSchedule = PaymentSchedule::lockForUpdate()
                        ->where('loan_code', $loan->loan_code)
                        ->whereRaw('interest_amount - interest_paid_amount > 0')
                        ->orderBy('due_date', 'asc')
                        ->first();
                    
                    if (!$nextSchedule) {
                        $newInterestAmount = ($newBalance / 100) * $loan->interest_rate;
                        $lastSchedule = PaymentSchedule::where('loan_code', $loan->loan_code)
                            ->orderBy('due_date', 'desc')
                            ->first();
                        
                        if ($lastSchedule) {
                            $nextSchedule = PaymentSchedule::create([
                                'loan_code' => $loan->loan_code,
                                'company_id' => $loan->company_id,
                                'customer_id' => $loan->customer_id,
                                'due_date' => Carbon::parse($lastSchedule->due_date)->addMonths(1)->format('Y-m-d'),
                                'interest_amount' => $newInterestAmount,
                                'interest_paid_amount' => 0,
                            ]);
                            $interest_balance_change = $newInterestAmount;
                        }
                    }
                }

                $loan->update([
                    'paid' => $loan->paid + $diff_payment,
                    'balance' => $newBalance,
                    'interest' => $loan->interest + $interest_balance_change,
                    'interest_paid' => $loan->interest_paid + $diff_interest_paid,
                    'interest_balance' => ($loan->interest + $interest_balance_change) - ($loan->interest_paid + $diff_interest_paid),
                    'late_paid' => $loan->late_paid + $diff_late_paid,
                    'late_balance' => $loan->late_balance - $diff_late_paid,
                    'discount' => $loan->discount + $diff_discount,
                ]);

                $this->loanController->update_loan_misc($loan);

            } else if ($loan->interest_group == "SKIM B") {
                if ($diff_payment != 0) {
                    if ($diff_payment > 0) {
                        $schedules = PaymentSchedule::lockForUpdate()
                            ->where('loan_code', $loan->loan_code)
                            ->whereRaw('payment_amount - (paid_amount + discount_amount) > 0')
                            ->orderBy('due_date', 'asc')
                            ->get();
                        
                        $remainingPayment = $diff_payment;
                        foreach ($schedules as $s) {
                            $owed = $s->payment_amount - ($s->paid_amount + $s->discount_amount);
                            if ($remainingPayment <= 0) break;
                            
                            $paymentToApply = min($remainingPayment, $owed);
                            $s->paid_amount += $paymentToApply;
                            $remainingPayment -= $paymentToApply;
                            $s->save();
                        }
                    } else {
                        $schedules = PaymentSchedule::lockForUpdate()
                            ->where('loan_code', $loan->loan_code)
                            ->where('paid_amount', '>', 0)
                            ->orderBy('due_date', 'desc')
                            ->get();
                        
                        $remainDeduct = abs($diff_payment);
                        foreach ($schedules as $s) {
                            if ($remainDeduct <= 0) break;
                            $paid = $s->paid_amount;
                            
                            if ($paid >= $remainDeduct) {
                                $s->paid_amount -= $remainDeduct;
                                $remainDeduct = 0;
                            } else {
                                $s->paid_amount = 0;
                                $remainDeduct -= $paid;
                            }
                            $s->save();
                            if ($remainDeduct <= 0) break;
                        }
                    }
                }

                if ($diff_discount != 0) {
                    if ($diff_discount > 0) {
                        $schedules = PaymentSchedule::lockForUpdate()
                            ->where('loan_code', $loan->loan_code)
                            ->whereRaw('payment_amount - (paid_amount + discount_amount) > 0')
                            ->orderBy('due_date', 'asc')
                            ->get();
                        
                        $remainingDiscount = $diff_discount;
                        foreach ($schedules as $s) {
                            $owed = $s->payment_amount - ($s->paid_amount + $s->discount_amount);
                            if ($remainingDiscount <= 0) break;
                            
                            $discountToApply = min($remainingDiscount, $owed);
                            $s->discount_amount += $discountToApply;
                            $remainingDiscount -= $discountToApply;
                            $s->save();
                        }
                    } else {
                        $schedules = PaymentSchedule::lockForUpdate()
                            ->where('loan_code', $loan->loan_code)
                            ->where('discount_amount', '>', 0)
                            ->orderBy('due_date', 'desc')
                            ->get();
                        
                        $remainDeduct = abs($diff_discount);
                        foreach ($schedules as $s) {
                            if ($remainDeduct <= 0) break;
                            $disc = $s->discount_amount;
                            
                            if ($disc >= $remainDeduct) {
                                $s->discount_amount -= $remainDeduct;
                                $remainDeduct = 0;
                            } else {
                                $s->discount_amount = 0;
                                $remainDeduct -= $disc;
                            }
                            $s->save();
                            if ($remainDeduct <= 0) break;
                        }
                    }
                }

                if ($diff_late_paid != 0) {
                    if ($diff_late_paid > 0) {
                        $lateSchedules = PaymentSchedule::lockForUpdate()
                            ->where('loan_code', $loan->loan_code)
                            ->whereRaw('late_amount - late_paid_amount > 0')
                            ->orderBy('due_date', 'asc')
                            ->get();
                        
                        $remainLatePaid = $diff_late_paid;
                        foreach ($lateSchedules as $lateSchedule) {
                            $unpaidLate = $lateSchedule->late_amount - $lateSchedule->late_paid_amount;
                            if ($remainLatePaid <= 0) break;
                            
                            if ($remainLatePaid >= $unpaidLate) {
                                $lateSchedule->late_paid_amount += $unpaidLate;
                                $remainLatePaid -= $unpaidLate;
                            } else {
                                $lateSchedule->late_paid_amount += $remainLatePaid;
                                $remainLatePaid = 0;
                            }
                            
                            $lateSchedule->save();
                            if ($remainLatePaid <= 0) break;
                        }
                    } else {
                        $lateSchedules = PaymentSchedule::lockForUpdate()
                            ->where('loan_code', $loan->loan_code)
                            ->where('late_paid_amount', '>', 0)
                            ->orderBy('due_date', 'desc')
                            ->get();
                        
                        $remainDeduct = abs($diff_late_paid);
                        foreach ($lateSchedules as $lateSchedule) {
                            if ($remainDeduct <= 0) break;
                            $paidLate = $lateSchedule->late_paid_amount;
                            
                            if ($paidLate >= $remainDeduct) {
                                $lateSchedule->late_paid_amount -= $remainDeduct;
                                $remainDeduct = 0;
                            } else {
                                $lateSchedule->late_paid_amount = 0;
                                $remainDeduct -= $paidLate;
                            }
                            $lateSchedule->save();
                        }
                    }
                }

                if ($diff_interest_paid != 0) {
                    if ($diff_interest_paid > 0) {
                        $interestSchedules = PaymentSchedule::lockForUpdate()
                            ->where('loan_code', $loan->loan_code)
                            ->whereRaw('interest_amount - interest_paid_amount > 0')
                            ->orderBy('due_date', 'asc')
                            ->get();
                        
                        $remainInterestPaid = $diff_interest_paid;
                        foreach ($interestSchedules as $is) {
                            $unpaidInterest = $is->interest_amount - $is->interest_paid_amount;
                            if ($remainInterestPaid <= 0) break;
                            
                            if ($remainInterestPaid >= $unpaidInterest) {
                                $is->interest_paid_amount += $unpaidInterest;
                                $remainInterestPaid -= $unpaidInterest;
                            } else {
                                $is->interest_paid_amount += $remainInterestPaid;
                                $remainInterestPaid = 0;
                            }
                            $is->save();
                            if ($remainInterestPaid <= 0) break;
                        }
                    } else {
                        $interestSchedules = PaymentSchedule::lockForUpdate()
                            ->where('loan_code', $loan->loan_code)
                            ->where('interest_paid_amount', '>', 0)
                            ->orderBy('due_date', 'desc')
                            ->get();
                        
                        $remainDeduct = abs($diff_interest_paid);
                        foreach ($interestSchedules as $is) {
                            if ($remainDeduct <= 0) break;
                            $paidInterest = $is->interest_paid_amount;
                            
                            if ($paidInterest >= $remainDeduct) {
                                $is->interest_paid_amount -= $remainDeduct;
                                $remainDeduct = 0;
                            } else {
                                $is->interest_paid_amount = 0;
                                $remainDeduct -= $paidInterest;
                            }
                            $is->save();
                        }
                    }
                }

                $loan->update([
                    'paid' => $loan->paid + $diff_payment,
                    'balance' => $loan->balance - $diff_total,
                    'interest_paid' => $loan->interest_paid + $diff_interest_paid,
                    'interest_balance' => $loan->interest - ($loan->interest_paid + $diff_interest_paid),
                    'late_paid' => $loan->late_paid + $diff_late_paid,
                    'late_balance' => $loan->late_balance - $diff_late_paid,
                    'discount' => $loan->discount + $diff_discount,
                ]);

                $this->loanController->update_loan_misc($loan);

            } else {
                throw new Exception('Invalid interest group.');
            }
            $payment->update([
                'payment_amount' => $new_payment_amount,
                'interest_paid_amount' => $new_interest_paid,
                'late_paid_amount' => $new_late_paid,
                'discount_amount' => $new_discount,
                'collection_type' => $v['collection_type'] ?? $payment->collection_type,
                'cheque' => $v['cheque'] ?? $payment->cheque,
                'bank' => $v['bank'] ?? $payment->bank,
                'updated_by' => Auth::user()->id,
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Payment updated successfully.']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first()
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function delete(Request $request)
    {
        try {
            DB::beginTransaction();
            
            bcscale(10);
            
            $payment = Payment::lockForUpdate()->where('id',$request->payment_id)->first();
            if(!$payment || $this->accesstoPayment($payment) == false){
                throw new Exception('Failed to get selected payment.');
            }

            $loan = Loan::lockForUpdate()->where('id', $payment->loan_id)->first();
            if(!$loan){
                throw new Exception('Loan not found.');
            }
            
            $payment_amount = $payment->payment_amount ?? 0;
            $interest_paid_amount = $payment->interest_paid_amount ?? 0;
            $late_paid_amount = $payment->late_paid_amount ?? 0;
            $discount_amount = $payment->discount_amount ?? 0;
            $total_payment = $payment_amount + $discount_amount;

            if ($loan->interest_group == "SKIM A") {
                if ($interest_paid_amount > 0) {
                    $schedules = PaymentSchedule::lockForUpdate()
                        ->where('loan_code', $loan->loan_code)
                        ->where('interest_paid_amount', '>', 0)
                        ->orderBy('due_date', 'desc')
                        ->get();
                    
                    $remainDeduct = $interest_paid_amount;
                    foreach ($schedules as $s) {
                        if ($remainDeduct <= 0) break;
                        $paid = $s->interest_paid_amount;
                        
                        if ($paid >= $remainDeduct) {
                            $s->update(['interest_paid_amount' => $s->interest_paid_amount - $remainDeduct]);
                            $remainDeduct = 0;
                            break;
                        } else {
                            $s->update(['interest_paid_amount' => 0]);
                            $remainDeduct -= $paid;
                        }
                    }
                }

                if ($late_paid_amount > 0) {
                    $lateSchedules = PaymentSchedule::lockForUpdate()
                        ->where('loan_code', $loan->loan_code)
                        ->where('late_paid_amount', '>', 0)
                        ->orderBy('due_date', 'desc')
                        ->get();
                    
                    $remainDeduct = $late_paid_amount;
                    foreach ($lateSchedules as $ls) {
                        if ($remainDeduct <= 0) break;
                        $paidLate = $ls->late_paid_amount;
                        
                        if ($paidLate >= $remainDeduct) {
                            $ls->update(['late_paid_amount' => $ls->late_paid_amount - $remainDeduct]);
                            $remainDeduct = 0;
                            break;
                        } else {
                            $ls->update(['late_paid_amount' => 0]);
                            $remainDeduct -= $paidLate;
                        }
                    }
                }

                $newBalance = $loan->balance + $total_payment;
                $interest_balance_change = 0;

                if ($total_payment >= $loan->payment) {
                    $newInterestAmount = ($newBalance / 100) * $loan->interest_rate;
                    $lastSchedule = PaymentSchedule::where('loan_code', $loan->loan_code)
                        ->orderBy('due_date', 'desc')
                        ->first();
                    
                    if ($lastSchedule) {
                        $nextSchedule = PaymentSchedule::create([
                            'loan_code' => $loan->loan_code,
                            'company_id' => $loan->company_id,
                            'customer_id' => $loan->customer_id,
                            'due_date' => Carbon::parse($lastSchedule->due_date)->addMonths(1)->format('Y-m-d'),
                            'interest_amount' => $newInterestAmount,
                            'interest_paid_amount' => 0,
                        ]);
                        $interest_balance_change = $newInterestAmount;
                    }
                } else {
                    $possiblyCreatedSchedule = PaymentSchedule::lockForUpdate()
                        ->where('loan_code', $loan->loan_code)
                        ->where('interest_paid_amount', 0)
                        ->where('late_paid_amount', 0)
                        ->orderBy('due_date', 'desc')
                        ->first();
                    
                    if ($possiblyCreatedSchedule) {
                        $interest_balance_change = $possiblyCreatedSchedule->interest_amount * -1;
                        $possiblyCreatedSchedule->delete();
                    }
                }

                $loan->update([
                    'paid' => $loan->paid - $payment_amount,
                    'balance' => $newBalance,
                    'interest' => $loan->interest + $interest_balance_change,
                    'interest_paid' => $loan->interest_paid - $interest_paid_amount,
                    'interest_balance' => ($loan->interest + $interest_balance_change) - ($loan->interest_paid - $interest_paid_amount),
                    'late_paid' => $loan->late_paid - $late_paid_amount,
                    'late_balance' => $loan->late_balance + $late_paid_amount,
                    'discount' => $loan->discount - $discount_amount,
                ]);

                $this->loanController->update_loan_misc($loan);

            } 
            else if ($loan->interest_group == "SKIM B") {
                if ($payment_amount > 0) {
                    $schedules = PaymentSchedule::lockForUpdate()
                        ->where('loan_code', $loan->loan_code)
                        ->where('paid_amount', '>', 0)
                        ->orderBy('due_date', 'desc')
                        ->get();
                    
                    $remainDeduct = $payment_amount;
                    foreach ($schedules as $s) {
                        if ($remainDeduct <= 0) break;
                        $paid = $s->paid_amount;
                        
                        if ($paid >= $remainDeduct) {
                            $s->paid_amount -= $remainDeduct;
                            $remainDeduct = 0;
                        } else {
                            $s->paid_amount = 0;
                            $remainDeduct -= $paid;
                        }
                        $s->save();
                        if ($remainDeduct <= 0) break;
                    }
                }

                if ($discount_amount > 0) {
                    $schedules = PaymentSchedule::lockForUpdate()
                        ->where('loan_code', $loan->loan_code)
                        ->where('discount_amount', '>', 0)
                        ->orderBy('due_date', 'desc')
                        ->get();
                    
                    $remainDeduct = $discount_amount;
                    foreach ($schedules as $s) {
                        if ($remainDeduct <= 0) break;
                        $disc = $s->discount_amount;
                        
                        if ($disc >= $remainDeduct) {
                            $s->discount_amount -= $remainDeduct;
                            $remainDeduct = 0;
                        } else {
                            $s->discount_amount = 0;
                            $remainDeduct -= $disc;
                        }
                        $s->save();
                        if ($remainDeduct <= 0) break;
                    }
                }

                if ($late_paid_amount > 0) {
                    $lateSchedules = PaymentSchedule::lockForUpdate()
                        ->where('loan_code', $loan->loan_code)
                        ->where('late_paid_amount', '>', 0)
                        ->orderBy('due_date', 'desc')
                        ->get();
                    
                    $remainDeduct = $late_paid_amount;
                    foreach ($lateSchedules as $lateSchedule) {
                        if ($remainDeduct <= 0) break;
                        $paidLate = $lateSchedule->late_paid_amount;
                        
                        if ($paidLate >= $remainDeduct) {
                            $lateSchedule->late_paid_amount -= $remainDeduct;
                            $remainDeduct = 0;
                        } else {
                            $lateSchedule->late_paid_amount = 0;
                            $remainDeduct -= $paidLate;
                        }
                        $lateSchedule->save();
                        if ($remainDeduct <= 0) break;
                    }
                }

                if ($interest_paid_amount > 0) {
                    $interestSchedules = PaymentSchedule::lockForUpdate()
                        ->where('loan_code', $loan->loan_code)
                        ->where('interest_paid_amount', '>', 0)
                        ->orderBy('due_date', 'desc')
                        ->get();
                    
                    $remainDeduct = $interest_paid_amount;
                    foreach ($interestSchedules as $is) {
                        if ($remainDeduct <= 0) break;
                        $paidInterest = $is->interest_paid_amount;
                        
                        if ($paidInterest >= $remainDeduct) {
                            $is->interest_paid_amount -= $remainDeduct;
                            $remainDeduct = 0;
                        } else {
                            $is->interest_paid_amount = 0;
                            $remainDeduct -= $paidInterest;
                        }
                        $is->save();
                        if ($remainDeduct <= 0) break;
                    }
                }

                $loan->update([
                    'paid' => $loan->paid - $payment_amount,
                    'balance' => $loan->balance + $total_payment,
                    'interest_paid' => $loan->interest_paid - $interest_paid_amount,
                    'interest_balance' => $loan->interest - ($loan->interest_paid - $interest_paid_amount),
                    'late_paid' => $loan->late_paid - $late_paid_amount,
                    'late_balance' => $loan->late_balance + $late_paid_amount,
                    'discount' => $loan->discount - $discount_amount,
                ]);

                $this->loanController->update_loan_misc($loan);

            }
            else {
                throw new Exception('Invalid interest group.');
            }

            $payment->delete();
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Payment deleted successfully.']);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Delete failed',
                'errors' => [$e->getMessage()]
            ]);
        }
    }



}