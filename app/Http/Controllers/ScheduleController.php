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

class ScheduleController extends Controller
{
    protected $loanController;
    
    public function __construct(LoanController $loanController)
    {
        $this->loanController = $loanController;
    }

    public function index(Request $request)
    {
        return view('schedule.index');
    }

    public function load_schedule(Request $request){
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
                    ->orWhere('payment_schedules.schedule_code', 'like', "%{$search}%");
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
                'message' => 'Validation failed',
                'errors' => $e->getMessage()
            ]);
        }
    }

    public function create(Request $request)
    {
        $loan = Loan::where('loan_code',$request->loan_code)->first();
        if(!isset($loan) || $this->accessToLoan($loan) == false){
            $loan = null;
        }
        return view('schedule.create')->with('loan', $loan);
    }

    public function store(Request $request)
    {
        try{
            DB::beginTransaction();
            $v = $request->validate([
                'loan_code' => 'required|string',
                // 'payment_amount' => 'nullable|numeric|min:0',
                'late_amount' => 'nullable|numeric',
                'interest_amount' => 'nullable|numeric',
                'due_date' => "required|date"
            ]);
            $loan = Loan::where('loan_code',$v['loan_code'])->first();
            if(!$loan || $this->accessToLoan($loan) == false){
                throw new Exception('Invalid loan code.');
            }

            // if($v['interest_amount'] + $v['late_amount'] + $v['payment_amount'] <= 0){
            //     throw new Exception('Total amount is 0.');
            // }
            
            $prefix = $loan->loan_code.'-SM' ?? $customer->company_code."LN";
            $schedule_code = $this->getSequenceNumber($prefix,'schedule_code');
            $new = PaymentSchedule::create([
                'schedule_code' => $schedule_code,
                'loan_code' => $v['loan_code'],
                'customer_id' => $loan->customer_id,
                'company_id' => $loan->company_id,
                // 'payment_amount' => ($v['payment_amount'] ?? 0),
                'interest_amount' => ($v['interest_amount'] ?? 0),
                'late_amount' => ($v['late_amount'] ?? 0),
                'due_date'=> $v['due_date']
            ]);

            $loan->update([
                'payment' => $loan->payment + $new->payment_amount,
                'balance' => $loan->balance + $new->payment_amount,
                'late' => $loan->late + $new->late_amount,
                'late_balance' => $loan->late_balance + $new->late_amount,
                // 'interest' => $loan->interest + $new->interest_amount,
                'interest_balance' => $loan->interest_balance + $new->interest_amount
            ]);

            $this->loanController->update_loan_misc($loan);
            DB::commit();
            return response()->json(['success'=>true,'message'=>'Schedule created.']);
        }
        catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
         
        }
        catch(Exception $e){
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function update(Request $request)
    {
        try{
            DB::beginTransaction();
            $v = $request->validate([
                'schedule_id' => 'required|numeric',
                // 'payment_amount' => 'nullable|numeric|min:0',
                'late_amount' => 'nullable|numeric',
                'interest_amount' => 'nullable|numeric',
                'paid_amount' => 'nullable|numeric|min:0',
                'late_paid_amount' => 'nullable|numeric',
                'interest_paid_amount' => 'nullable|numeric',
                'discount_amount' => 'nullable|numeric|min:0',
                'due_date' => "required|date"
            ]);
           
            $schedule = PaymentSchedule::lockForUpdate()->where('id',$v['schedule_id'])->first();
            if(!$schedule || $this->accessToSchedule($schedule) == false){
                throw new Exception('Fail to get selected schedule.');
            }

            // if($v['interest_amount'] + $v['late_amount'] + $v['payment_amount'] <= 0){
            //     throw new Exception('Total amount is 0.');
            // }

            $loan = Loan::where('loan_code',$schedule->loan_code)->first();
            if(!$loan){
                throw new Exception('Failed to get loan detail.');
            }

            $old = clone $schedule;
            $schedule->update([
                // 'payment_amount' => $v['payment_amount'] ?? 0,
                'late_amount' => $v['late_amount'] ?? 0,
                'interest_amount' => $v['interest_amount'] ?? 0,
                'paid_amount' => $v['paid_amount'] ?? 0,
                'late_paid_amount' => $v['late_paid_amount'] ?? 0,
                'interest_paid_amount' => $v['interest_paid_amount'] ?? 0,
                'discount_amount' => $v['discount_amount'] ?? 0,
                'due_date' =>  $v['due_date']
            ]);

            // $paymentDiff = $schedule->payment_amount - $old->payment_amount;
            $paidDiff = $schedule->paid_amount - $old->paid_amount;
            $interestDiff = $schedule->interest_amount - $old->interest_amount;
            $interestPaidDiff = $schedule->interest_paid_amount - $old->interest_paid_amount;
            $lateDiff = $schedule->late_amount - $old->late_amount;
            $latePaidDiff = $schedule->late_paid_amount - $old->late_paid_amount;
            $discountDiff = $schedule->discount_amount - $old->discount_amount;

            $newPayment = $loan->payment + $paymentDiff;
            $newPaid = $loan->paid + $paidDiff;
            $newInterest = $loan->interest + $interestDiff;
            $newInterestPaid = $loan->interest_paid + $interestPaidDiff;
            $newLate = $loan->late + $lateDiff;
            $newLatePaid = $loan->late_paid + $latePaidDiff;

            $loan->update([
                'payment' => $newPayment,
                'paid' => $newPaid,
                'balance' => $newPayment - $newPaid,
                // 'interest' => $newInterest,
                'interest_paid' => $newInterestPaid,
                'interest_balance' => $newInterest - $newInterestPaid,
                'late' => $newLate,
                'late_paid' => $newLatePaid,
                'late_balance' => $newLate - $newLatePaid,
                'discount' => $loan->discount + $discountDiff,
            ]);

            $this->loanController->update_loan_misc($loan);
            DB::commit();
            return response()->json(['success'=>true,'message'=>'Schedule updated.']);
        }
        catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => collect($e->errors())->flatten()->implode(' ')
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

    public function delete(Request $request)
    {
        try{
            DB::beginTransaction();
            
            $s = PaymentSchedule::lockForUpdate()->where('id',$request->schedule_id)->first();
            if(!$s || $this->accessToSchedule($s) == false){
                throw new Exception('Fail to get selected schedule.'.json_encode($request->all()));
            }

            $loan = Loan::where('loan_code',$s->loan_code)->first();
            if(!$loan){
                throw new Exception('Failed to get loan detail.');
            }

            $total = PaymentSchedule::where('loan_code',$loan->loan_code)->count();
            if($total <= 1){
                throw new Exception('Failed to delete selected schedule.');
            }

            $loan->update([
                    'payment' => $loan->payment - $s->payment_amount,
                    'paid' => $loan->paid - $s->paid_amount,
                    'balance' => $loan->balance - ($s->payment_amount - $s->paid_amount),
                    'interest' => $loan->interest - $s->interest_amount,
                    'interest_paid' => $loan->interest_paid - $s->interest_paid_amount,
                    'interest_balance' => $loan->interest_balance - ($s->interest_amount - $s->interest_paid_amount),
                    'late' => $loan->late - $s->late_amount,
                    'late_paid' => $loan->late_paid - $s->late_paid_amount,
                    'late_balance' => $loan->late_balance - ($s->late_amount - $s->late_paid_amount),
                    'discount' => $loan->discount - $s->discount_amount,
            ]);

            $s->delete();

            $this->loanController->update_loan_misc($loan);
            DB::commit();
            return response()->json(['success'=>true,'message'=>'Schedule updated.']);
        }
        catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => collect($e->errors())->flatten()->implode(' ')
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
}