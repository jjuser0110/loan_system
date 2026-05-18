<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Spatie\Browsershot\Browsershot;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Company;
use App\Models\Customer;
use App\Models\PaymentMethod;
use App\Models\Expense;
use App\Models\ExpensesType;
use App\Models\NonExpensesType;
use App\Models\PaymentMethodLog;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\LoanController;
use Exception;
use Bouncer;
use Carbon\Carbon;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Company::query();
            switch (Auth::user()->role_id) {
                case 1: break;
                case 2: $query->where('branch_id', Auth::user()->branch_id); break;
                case 3:
                case 4: $query->where('id', Auth::user()->company_id); break;
                default: throw new Exception('Invalid role id.');
            }
            $companies = $query->get();
            $expenseTypes = ExpensesType::orderBy('title')->get();
            $nonexpenseTypes = NonExpensesType::orderBy('title')->get();

            return view('expense.index')
                ->with('companies', $companies)
                ->with('expenseTypes', $expenseTypes)
                ->with('nonexpenseTypes', $nonexpenseTypes);
        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }

    public function load_expense(Request $request){
        try{
            $draw = $request->input('draw');
            $search = $request->input('search.value');
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);

            // Safe column ordering — fallback if called outside DataTables
            $orderByColumn = 'expenses.created_at';
            $orderByDirection = 'desc';
            if ($request->input('order.0.column') !== null && $request->input('columns')) {
                $colIndex = $request->input('order.0.column');
                $colName  = $request->input('columns')[$colIndex]['data'] ?? null;

                $columnMap = [
                    'expense_code'        => 'expenses.expense_code',
                    'expense_title'       => 'expenses.expense_title',
                    'expense_description' => 'expenses.expense_description',
                    'amount'              => 'expenses.amount',
                    'date'                => 'expenses.date',
                    'company'             => 'companies.company_name',
                    'bank'                => 'banks.bank_name',
                    'created_at'          => 'expenses.created_at',
                ];
                $orderByColumn    = $columnMap[$colName] ?? 'expenses.created_at';
                $orderByDirection = $request->input('order.0.dir', 'desc');
            }

            $query = Expense::query()
                ->select([
                    'expenses.*',
                    'companies.company_name as company_name',
                    'companies.company_code as company_code',
                    'branches.branch_name as branch_name',
                    'branches.branch_code as branch_code',
                    'payment_methods.account_no as bank_account_no',
                    'payment_methods.owner_name as bank_owner_name',
                    'banks.bank_name as bank_name'
                ])
                ->join('companies', 'companies.id', '=', 'expenses.company_id')
                ->join('payment_methods', 'expenses.payment_method_id', '=', 'payment_methods.id')
                ->join('banks', 'banks.id', '=', 'payment_methods.bank_id')
                ->join('branches', 'branches.id', '=', 'companies.branch_id');

            switch (Auth::user()->role_id) {
                case 1:
                    break;
                case 2:
                    $query->where('companies.branch_id', Auth::user()->branch_id);
                    break;
                case 3:
                case 4:
                    $query->where('expenses.company_id', Auth::user()->company_id);
                    break;
                default:
                    throw new Exception('Invalid role id.');
            }

            // Date and company filters
            if ($request->from_date) {
                $query->whereDate('expenses.date', '>=', $request->from_date);
            }
            if ($request->to_date) {
                $query->whereDate('expenses.date', '<=', $request->to_date);
            }
            if ($request->company) {
                $query->where('companies.company_code', $request->company);
            }

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('expenses.expense_title', 'like', "%{$search}%")
                    ->orWhere('expenses.amount', 'like', "%{$search}%")
                    ->orWhere('expenses.expense_code', 'like', "%{$search}%")
                    ->orWhere('expenses.expense_description', 'like', "%{$search}%")
                    ->orWhere('companies.company_name', 'like', "%{$search}%")
                    ->orWhere('companies.company_code', 'like', "%{$search}%")
                    ->orWhere('branches.branch_code', 'like', "%{$search}%")
                    ->orWhere('branches.branch_name', 'like', "%{$search}%");
                });
            }

            $recordsTotal = $query->count();
            $data = $query->orderBy($orderByColumn, $orderByDirection)->skip($start)->take($length)->get();

            foreach ($data as $row) {

                $row->expense_amount = 0;
                $row->non_amount = 0;

                if ($row->expense_type == 'expense') {
                    $row->expense_amount = $row->amount;
                }

                if ($row->expense_type == 'non-expense') {
                    $row->non_amount = $row->amount;
                }
            }

            return response()->json([
                "draw"            => intval($draw),
                "recordsTotal"    => $recordsTotal,
                "recordsFiltered" => $recordsTotal,
                "data"            => $data,
            ]);

        } catch(Exception $e){
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
                'expense_title'       => 'required|string|min:3|max:255',
                'expense_description' => 'required|string|min:3|max:255',
                'amount_in'           => 'nullable|numeric|min:0',
                'amount_out'          => 'nullable|numeric|min:0',
                'company'             => 'required',
                'payment_method_id'   => 'required',
                'date'                => 'required'
            ]);

            // Calculate final amount: amount_in is positive, amount_out is negative
            $amount_in  = floatval($request->amount_in  ?? 0);
            $amount_out = floatval($request->amount_out ?? 0);
            $amount     = $amount_in - $amount_out;

            $query = Company::query();
            switch (Auth::user()->role_id) {
                case 1: break;
                case 2: $query->where('branch_id', Auth::user()->branch_id); break;
                case 3:
                case 4: $query->where('id', Auth::user()->company_id); break;
                default: throw new Exception('Invalid role id.');
            }

            $company = $query->where('company_code', $request->company)->first();
            if (!$company) throw new Exception('Selected company does not exist.');

            $pm = PaymentMethod::where('id', $request->payment_method_id)
                            ->where('company_id', $company->id)->first();
            if (!$pm) throw new Exception('Invalid payment method.');

            $prefix       = $company->company_code . '-E';
            $expense_code = $this->getSequenceNumber($prefix, 'expense_code');

            $expense = Expense::create(array_merge(
                $request->except(['amount_in', 'amount_out']),
                [
                    'expense_code' => $expense_code,
                    'amount'       => $amount,
                    'updated_by'   => Auth()->id(),
                    'company_id'   => $company->id,
                    'expense_type' => $request->expense_type ?? 'expense',
                ]
            ));

            $pm_before = $pm->amount;
            $pm_after  = $pm_before + $amount; // positive = in, negative = out
            $pm->update(['amount' => $pm_after]);
            $expense->payment_method_logs()->create([
                'type'              => 'expense',
                'description'       => 'Expense Created',
                'prev_amount'       => $pm_before,
                'amount'            => $amount,
                'payment_method_id' => $pm->id,
                'total'             => $pm_after
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Expense created.']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => $e->validator->errors()->first()]);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        try {
            DB::beginTransaction();
            $v = $request->validate([
                'expense_title'       => 'required|string|min:3|max:255',
                'expense_description' => 'required|string|min:3|max:255',
                'amount_in'           => 'nullable|numeric|min:0',
                'amount_out'          => 'nullable|numeric|min:0',
                'company'             => 'required',
                'payment_method_id'   => 'required',
                'date'                => 'required'
            ]);

            // Calculate final amount
            $amount_in  = floatval($request->amount_in  ?? 0);
            $amount_out = floatval($request->amount_out ?? 0);
            $amount     = $amount_in - $amount_out;

            $query = Company::query();
            switch (Auth::user()->role_id) {
                case 1: break;
                case 2: $query->where('branch_id', Auth::user()->branch_id); break;
                case 3:
                case 4: $query->where('id', Auth::user()->company_id); break;
                default: throw new Exception('Invalid role id.');
            }

            $company = $query->where('company_code', $request->company)->first();
            if (!$company) throw new Exception('Selected company does not exist.');

            $expense = Expense::where('id', $request->id)->first();
            if (!$expense) throw new Exception('Invalid expense.');

            $pm = PaymentMethod::where('id', $request->payment_method_id)
                            ->where('company_id', $company->id)->first();
            if (!$pm) throw new Exception('Invalid payment method.');

            $ppm = PaymentMethod::where('id', $expense->payment_method_id)->first();
            if (!$ppm) throw new Exception('Invalid payment method.');

            // Reverse old amount on previous payment method
            $ppm_before = $ppm->amount;
            $ppm_after  = $ppm_before - $expense->amount; // reverse old amount
            $ppm->update(['amount' => $ppm_after]);
            $ppm->payment_method_logs()->create([
                'type'              => 'expense',
                'description'       => 'Expense Updated',
                'prev_amount'       => $ppm_before,
                'amount'            => $expense->amount * -1,
                'payment_method_id' => $ppm->id,
                'total'             => $ppm_after
            ]);

            // Apply new amount on new payment method
            $pm_before = $pm->id == $ppm->id ? $ppm_after : $pm->amount;
            $pm_after  = $pm_before + $amount;
            $pm->update(['amount' => $pm_after]);
            $pm->payment_method_logs()->create([
                'type'              => 'expense',
                'description'       => 'Expense Updated',
                'prev_amount'       => $pm_before,
                'amount'            => $amount,
                'payment_method_id' => $pm->id,
                'total'             => $pm_after
            ]);

            $expense->update([
                'amount'              => $amount,
                'expense_title'       => $request->expense_title,
                'expense_description' => $request->expense_description,
                'date'                => $request->date,
                'updated_by'          => Auth::user()->id,
                'company_id'          => $company->id,
                'payment_method_id'   => $pm->id,
                'expense_type'        => $request->expense_type ?? 'expense',
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Expense updated successfully.']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->validator->errors()->first()]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function delete(Request $request)
    {
        try {
            DB::beginTransaction();
            $expense = Expense::lockForUpdate()->where('id',$request->id)->first();
            if(!$expense){
                throw new Exceptionm('Failed to remove selected expense');
            }
            $query = Company::query();
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
            
            $company = $query->where('id',$expense->company_id)->first();
            if(!$company){
                throw new Exception('Access denied.');
            }

            $pm = PaymentMethod::where('id',$expense->payment_method_id)->first();
            if(!$pm){
                throw new Exception('Invalid payment method.');
            }

            $pm_before = $pm->amount;
            $pm_after = $pm_before + $expense->amount;
            $pm->update(['amount'=>$pm_after]);
            $pm->payment_method_logs()->create([
                'type'=> 'expense',
                'description'=>'Expense Deleted',
                'prev_amount'=> $pm_before,
                'amount' => $expense->amount,
                'payment_method_id'=>$pm->id,
                'total' => $pm_after
            ]);

            $expense->delete();
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Expense deleted successfully.']);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function fetch_expense()
    {
        $total_expenses = DB::table('expenses')
            ->whereNull('deleted_at')
            ->where('expense_type', 'expense')
            ->sum('amount');

        return response()->json([
            'total_expenses' => number_format($total_expenses, 2)
        ]);
    }
}