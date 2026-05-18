<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\DailyReport;
use App\Models\PaymentMethodLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class ReportController extends Controller
{
    public function daily_report(Request $request)
    {
        switch(Auth::user()->role_id){
            case 1:
                $query = DB::table('companies');
                break;

            case 2:
                $query = DB::table('companies')->where('branch_id', Auth::user()->branch_id);
                break;

            default:
                $query = DB::table('companies')->where('id', Auth::user()->company_id);
                break;
        }
        $companies = $query->get();
        return view('report.dailyreport')->with('companies', $companies);
    }

    public function load_daily_reports(Request $request)
    {
        try {
            $draw = $request->input('draw');
            $search = $request->input('search.value');
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);

            $fromDate = $request->input('from_date');
            $toDate = $request->input('to_date');
            $companyId = $request->input('company_id');

            if (!$fromDate && !$toDate && !$companyId) {
                return response()->json([
                    "draw" => intval($draw),
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => [],
                ]);
            }

            $orderColumnIndex = $request->input('order.0.column', 10);
            $columns = $request->input('columns', []);
            $orderByColumn = isset($columns[$orderColumnIndex]['data'])
                ? $columns[$orderColumnIndex]['data']
                : 'created_date';

            $orderByDirection = $request->input('order.0.dir', 'desc');

            $query = DailyReport::query()
                ->select([
                    'daily_reports.*',
                    'companies.company_name',
                    'companies.company_code',
                    'branches.branch_name',
                    'branches.branch_code',
                ])
                ->join('companies', 'daily_reports.company_id', '=', 'companies.id')
                ->join('branches', 'companies.branch_id', '=', 'branches.id');

            switch (Auth::user()->role_id) {
                case 1:
                    break;

                case 2:
                    $query->where('branches.id', Auth::user()->branch_id);
                    break;

                case 3:
                case 4:
                    $query->where('daily_reports.company_id', Auth::user()->company_id);
                    break;

                default:
                    throw new Exception('Invalid role id.');
            }

            if ($fromDate && $toDate) {
                $query->whereBetween('daily_reports.created_date', [
                    $fromDate . ' 00:00:00',
                    $toDate . ' 23:59:59'
                ]);
            } elseif ($fromDate) {
                $query->whereDate('daily_reports.created_date', '>=', $fromDate);
            } elseif ($toDate) {
                $query->whereDate('daily_reports.created_date', '<=', $toDate);
            }

            if ($companyId) {
                $query->where('daily_reports.company_id', $companyId);
            }

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('companies.company_name', 'like', "%{$search}%")
                        ->orWhere('companies.company_code', 'like', "%{$search}%")
                        ->orWhere('branches.branch_name', 'like', "%{$search}%")
                        ->orWhere('branches.branch_code', 'like', "%{$search}%");
                });
            }

            $recordsTotal = $query->count();
            $recordsFiltered = $recordsTotal;

            $columnMap = [
                'company_name' => 'companies.company_name',
                'company_code' => 'companies.company_code',
                'branch_name' => 'branches.branch_name',
                'branch_code' => 'branches.branch_code',
            ];

            $orderColumn = $columnMap[$orderByColumn] ?? 'daily_reports.' . $orderByColumn;

            $data = $query->orderBy($orderColumn, $orderByDirection)
                ->skip($start)
                ->take($length)
                ->get();

            return response()->json([
                "draw" => intval($draw),
                "recordsTotal" => $recordsTotal,
                "recordsFiltered" => $recordsFiltered,
                "data" => $data,
            ]);
        } catch (Exception $e) {

            \Log::error('Daily Reports Load Error: ' . $e->getMessage());

            return response()->json([
                'error' => 'Server error occurred',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function cash_book_report(Request $request)
    {
        switch(Auth::user()->role_id){
            case 1:
                $query = DB::table('companies');
                break;

            case 2:
                $query = DB::table('companies')->where('branch_id', Auth::user()->branch_id);
                break;

            default:
                $query = DB::table('companies')->where('id', Auth::user()->company_id);
                break;
        }
        $companies = $query->get();
        return view('report.cashbookreport')->with('companies', $companies);
    }

    public function load_cash_book_reports(Request $request)
    {
        try {
            $draw      = $request->input('draw');
            $start     = $request->input('start', 0);
            $length    = $request->input('length', 10);
            $fromDate  = $request->input('from_date');
            $toDate    = $request->input('to_date');
            $companyId = $request->input('company_id');

            if (!$fromDate && !$toDate && !$companyId) {
                return response()->json([
                    "draw"            => intval($draw),
                    "recordsTotal"    => 0,
                    "recordsFiltered" => 0,
                    "data"            => [],
                ]);
            }

            $query = \DB::table('payment_method_logs')
                ->select([
                    'payment_method_logs.*',
                    'customers.id as customer_id',
                    'customers.customer_name',
                    'payments.remark as remark',
                    'payments.collection_type as collection_type',
                    'expenses.expense_title as expenses_name',
                    'expenses.expense_description as expenses_description',
                    \DB::raw("CASE WHEN payment_method_logs.type = 'payment' THEN payment_method_logs.amount ELSE 0 END as customer_payment"),
                    \DB::raw("CASE WHEN payment_method_logs.type = 'loan'    THEN payment_method_logs.amount ELSE 0 END as loan_top_up"),
                    \DB::raw("CASE WHEN payment_method_logs.type = 'expense' THEN payment_method_logs.amount ELSE 0 END as expenses"),
                    \DB::raw("payment_method_logs.total as account_total_amount"),
                    \DB::raw("DATE(payment_method_logs.created_at) as date"),
                    \DB::raw("
                        CASE
                            WHEN payment_method_logs.type = 'payment' AND payments.top_up IS NOT NULL AND payments.top_up > 0 THEN CONCAT('Loan TopUp - Payment #', COALESCE(payments.payment_code, payment_method_logs.description))
                            WHEN payment_method_logs.type = 'payment' THEN CONCAT('Payment #', COALESCE(payments.payment_code, payment_method_logs.description))
                            WHEN payment_method_logs.type = 'loan'    THEN CONCAT('Loan #',    COALESCE(loans.loan_code,       payment_method_logs.description))
                            WHEN payment_method_logs.type = 'expense' THEN CONCAT('Expense #', COALESCE(expenses.expense_code, payment_method_logs.description))
                            ELSE CONCAT('Manual # ', COALESCE(payment_method_logs.description, '-'))
                        END as description
                    "),
                    \DB::raw("
                        CASE
                            WHEN payment_method_logs.type = 'loan'    THEN (SELECT l.interest_paid        FROM loans    l WHERE l.id = payment_method_logs.content_id LIMIT 1)
                            WHEN payment_method_logs.type = 'payment' THEN (SELECT p.interest_paid_amount FROM payments p WHERE p.id = payment_method_logs.content_id LIMIT 1)
                            ELSE NULL
                        END as interest_paid
                    "),
                    \DB::raw("
                        CASE
                            WHEN payment_method_logs.type = 'payment' THEN (SELECT p.top_up_capital FROM payments p WHERE p.id = payment_method_logs.content_id LIMIT 1)
                            ELSE NULL
                        END as top_up_capital
                    "),
                ])
                ->join('payment_methods', 'payment_method_logs.payment_method_id', '=', 'payment_methods.id')
                ->join('companies',       'payment_methods.company_id',            '=', 'companies.id')
                ->join('branches',        'companies.branch_id',                   '=', 'branches.id')
                ->leftJoin('payments', function ($join) {
                    $join->on('payment_method_logs.content_id', '=', 'payments.id')
                        ->where('payment_method_logs.type', '=', 'payment');
                })
                ->leftJoin('loans', function ($join) {
                    $join->on('payment_method_logs.content_id', '=', 'loans.id')
                        ->where('payment_method_logs.type', '=', 'loan');
                })
                ->leftJoin('expenses', function ($join) {
                    $join->on('payment_method_logs.content_id', '=', 'expenses.id')
                        ->where('payment_method_logs.type', '=', 'expense');
                })
                ->leftJoin('customers', function ($join) {
                    $join->on(function ($q) {
                        $q->on('payments.customer_id', '=', 'customers.id')
                        ->orOn('loans.customer_id',  '=', 'customers.id');
                    });
                });

            switch (Auth::user()->role_id) {
                case 1: break;
                case 2: $query->where('branches.id', Auth::user()->branch_id); break;
                case 3:
                case 4: $query->where('payment_methods.company_id', Auth::user()->company_id); break;
                default: throw new \Exception('Invalid role id.');
            }

            $query->where(function ($q) {
                $q->where('payment_method_logs.description', '!=', 'Expense Updated')
                ->where('payment_method_logs.description', '!=', 'Payment Updated')
                ->orWhereNull('payment_method_logs.description');
            });

            if ($fromDate && $toDate) {
                $query->whereBetween(\DB::raw('DATE(payment_method_logs.created_at)'), [$fromDate, $toDate]);
            } elseif ($fromDate) {
                $query->whereDate('payment_method_logs.created_at', '>=', $fromDate);
            } elseif ($toDate) {
                $query->whereDate('payment_method_logs.created_at', '<=', $toDate);
            }

            if ($companyId) {
                $query->where('payment_methods.company_id', $companyId);
            }

            $total = $query->count();

            $columnMap = [
                0  => 'payment_method_logs.id',
                1  => 'payment_method_logs.description',
                2  => 'payment_method_logs.created_at',
                3  => 'customers.customer_name',
                4  => 'payments.collection_type',
                5  => 'expenses.expense_title',
                6  => 'expenses.expense_description',
                7  => 'payments.remark',
                8  => 'payments.payment_amount',
                9  => 'payments.interest_paid_amount',
                10 => 'payments.top_up',
                11 => 'payments.top_up_capital',
                12 => 'expenses.amount',
                13 => 'payment_method_logs.total',
            ];

            $orderColIdx = (int) $request->input('order.0.column', 2);
            $orderDir    = $request->input('order.0.dir', 'asc') === 'desc' ? 'desc' : 'asc';
            $orderCol    = $columnMap[$orderColIdx] ?? 'payment_method_logs.created_at';

            $data = $query->orderBy($orderCol, $orderDir)
                        ->skip($start)
                        ->take($length)
                        ->get();

            return response()->json([
                "draw"            => intval($draw),
                "recordsTotal"    => $total,
                "recordsFiltered" => $total,
                "data"            => $data,
            ]);

        } catch (\Exception $e) {
            \Log::error('Cash Book Reports Load Error: ' . $e->getMessage());
            return response()->json(['error' => 'Server error occurred', 'message' => $e->getMessage()], 500);
        }
    }

    public function cash_book_report_history(Request $request)
    {
        switch(Auth::user()->role_id){
            case 1:
                $query = DB::table('companies');
                break;

            case 2:
                $query = DB::table('companies')->where('branch_id', Auth::user()->branch_id);
                break;

            default:
                $query = DB::table('companies')->where('id', Auth::user()->company_id);
                break;
        }
        $companies = $query->get();
        return view('report.cashbookreporthistory')->with('companies', $companies);
    }

    public function load_cash_book_report_history(Request $request)
    {
        try {
            $draw      = $request->input('draw');
            $start     = $request->input('start', 0);
            $length    = $request->input('length', 10);
            $fromDate  = $request->input('from_date');
            $toDate    = $request->input('to_date');
            $companyId = $request->input('company_id');

            if (!$fromDate && !$toDate && !$companyId) {
                return response()->json([
                    "draw"            => intval($draw),
                    "recordsTotal"    => 0,
                    "recordsFiltered" => 0,
                    "data"            => [],
                ]);
            }

            $query = \DB::table('payment_method_logs')
                ->select([
                    'payment_method_logs.*',
                    'customers.id as customer_id',
                    'customers.customer_name',
                    'payments.remark as remark',
                    'payments.collection_type as collection_type',
                    'expenses.expense_title as expenses_name',
                    'expenses.expense_description as expenses_description',
                    \DB::raw("CASE WHEN payment_method_logs.type = 'payment' THEN payment_method_logs.amount ELSE 0 END as customer_payment"),
                    \DB::raw("CASE WHEN payment_method_logs.type = 'loan'    THEN payment_method_logs.amount ELSE 0 END as loan_top_up"),
                    \DB::raw("CASE WHEN payment_method_logs.type = 'expense' THEN payment_method_logs.amount ELSE 0 END as expenses"),
                    \DB::raw("payment_method_logs.total as account_total_amount"),
                    \DB::raw("DATE(payment_method_logs.created_at) as date"),
                    \DB::raw("
                        CASE
                            WHEN payment_method_logs.type = 'payment' AND payments.top_up IS NOT NULL AND payments.top_up > 0
                                                                    THEN CONCAT('Loan TopUp - Payment #', COALESCE(payments.payment_code, payment_method_logs.description))
                            WHEN payment_method_logs.type = 'payment' THEN CONCAT('Payment #', COALESCE(payments.payment_code, payment_method_logs.description))
                            WHEN payment_method_logs.type = 'loan'    THEN CONCAT('Loan #',    COALESCE(loans.loan_code,       payment_method_logs.description))
                            WHEN payment_method_logs.type = 'expense' THEN CONCAT('Expense #', COALESCE(expenses.expense_code, payment_method_logs.description))
                            ELSE CONCAT('Manual # ', COALESCE(payment_method_logs.description, '-'))
                        END as description
                    "),
                    \DB::raw("
                        CASE
                            WHEN payment_method_logs.type = 'loan'    THEN (SELECT l.interest_paid        FROM loans    l WHERE l.id = payment_method_logs.content_id LIMIT 1)
                            WHEN payment_method_logs.type = 'payment' THEN (SELECT p.interest_paid_amount FROM payments p WHERE p.id = payment_method_logs.content_id LIMIT 1)
                            ELSE NULL
                        END as interest_paid
                    "),
                    \DB::raw("
                        CASE
                            WHEN payment_method_logs.type = 'payment' THEN (SELECT p.top_up_capital FROM payments p WHERE p.id = payment_method_logs.content_id LIMIT 1)
                            ELSE NULL
                        END as top_up_capital
                    "),
                ])
                ->join('payment_methods', 'payment_method_logs.payment_method_id', '=', 'payment_methods.id')
                ->join('companies',       'payment_methods.company_id',            '=', 'companies.id')
                ->join('branches',        'companies.branch_id',                   '=', 'branches.id')
                ->leftJoin('payments', function ($join) {
                    $join->on('payment_method_logs.content_id', '=', 'payments.id')
                        ->where('payment_method_logs.type', '=', 'payment');
                })
                ->leftJoin('loans', function ($join) {
                    $join->on('payment_method_logs.content_id', '=', 'loans.id')
                        ->where('payment_method_logs.type', '=', 'loan');
                })
                ->leftJoin('expenses', function ($join) {
                    $join->on('payment_method_logs.content_id', '=', 'expenses.id')
                        ->where('payment_method_logs.type', '=', 'expense');
                })
                ->leftJoin('customers', function ($join) {
                    $join->on(function ($q) {
                        $q->on('payments.customer_id', '=', 'customers.id')
                        ->orOn('loans.customer_id',  '=', 'customers.id');
                    });
                });

            switch (Auth::user()->role_id) {
                case 1: break;
                case 2: $query->where('branches.id', Auth::user()->branch_id); break;
                case 3:
                case 4: $query->where('payment_methods.company_id', Auth::user()->company_id); break;
                default: throw new \Exception('Invalid role id.');
            }

            // NO description filter here — history shows all records including updated ones

            if ($fromDate && $toDate) {
                $query->whereBetween(\DB::raw('DATE(payment_method_logs.created_at)'), [$fromDate, $toDate]);
            } elseif ($fromDate) {
                $query->whereDate('payment_method_logs.created_at', '>=', $fromDate);
            } elseif ($toDate) {
                $query->whereDate('payment_method_logs.created_at', '<=', $toDate);
            }

            if ($companyId) {
                $query->where('payment_methods.company_id', $companyId);
            }

            $total = $query->count();

            $columnMap = [
                0  => 'payment_method_logs.id',
                1  => 'payment_method_logs.description',
                2  => 'payment_method_logs.created_at',
                3  => 'customers.customer_name',
                4  => 'payments.collection_type',
                5  => 'expenses.expense_title',
                6  => 'expenses.expense_description',
                7  => 'payments.remark',
                8  => 'payments.payment_amount',
                9  => 'payments.interest_paid_amount',
                10 => 'payments.top_up',
                11 => 'payments.top_up_capital',
                12 => 'expenses.amount',
                13 => 'payment_method_logs.total',
            ];

            $orderColIdx = (int) $request->input('order.0.column', 2);
            $orderDir    = $request->input('order.0.dir', 'asc') === 'desc' ? 'desc' : 'asc';
            $orderCol    = $columnMap[$orderColIdx] ?? 'payment_method_logs.created_at';

            $data = $query->orderBy($orderCol, $orderDir)
                        ->skip($start)
                        ->take($length)
                        ->get();

            return response()->json([
                "draw"            => intval($draw),
                "recordsTotal"    => $total,
                "recordsFiltered" => $total,
                "data"            => $data,
            ]);

        } catch (\Exception $e) {
            \Log::error('Cash Book Report History Load Error: ' . $e->getMessage());
            return response()->json(['error' => 'Server error occurred', 'message' => $e->getMessage()], 500);
        }
    }

    public function customer_payment_report(Request $request)
    {
        switch(Auth::user()->role_id){
            case 1:
                $query = DB::table('companies');
                break;
            case 2:
                $query = DB::table('companies')->where('branch_id', Auth::user()->branch_id);
                break;
            default:
                $query = DB::table('companies')->where('id', Auth::user()->company_id);
                break;
        }
        $companies = $query->get();
        return view('report.customerpaymentreport')->with('companies', $companies);
    }

    public function load_customer_payment_report(Request $request)
    {
        try {
            $draw      = $request->input('draw');
            $search    = $request->input('search.value');
            $start     = $request->input('start', 0);
            $length    = $request->input('length', 10);
            $fromDate  = $request->input('from_date');
            $toDate    = $request->input('to_date');
            $companyId = $request->input('company_id');

            if (!$fromDate && !$toDate && !$companyId) {
                return response()->json([
                    "draw"            => intval($draw),
                    "recordsTotal"    => 0,
                    "recordsFiltered" => 0,
                    "data"            => [],
                ]);
            }

            $query = DB::table('payments')
                ->select([
                    'payments.id',
                    'payments.payment_code',
                    'payments.loan_id',
                    'payments.collection_type',
                    'payments.created_at as pay_date',
                    'payments.payment_amount',
                    'payments.late_paid_amount',
                    'payments.interest_paid_amount',
                    'payments.discount_amount',
                    'payments.top_up',
                    'payments.top_up_capital',
                    'loans.payment as payment',
                    'loans.first_payment as first_payment',
                    'customers.customer_name as customer_name',
                    'companies.id as company_id',
                ])
                ->join('customers', 'customers.id', '=', 'payments.customer_id')
                ->join('loans',     'loans.id',     '=', 'payments.loan_id')
                ->join('companies', 'companies.id', '=', 'loans.company_id')
                ->join('branches',  'branches.id',  '=', 'companies.branch_id');

            // ── Role filter ──────────────────────────────────────────────
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
                    throw new \Exception('Invalid role id.');
            }

            // ── Date filter (using created_at) ───────────────────────────
            if ($fromDate && $toDate) {
                $query->whereBetween('payments.created_at', [
                    $fromDate . ' 00:00:00',
                    $toDate   . ' 23:59:59',
                ]);
            } elseif ($fromDate) {
                $query->whereDate('payments.created_at', '>=', $fromDate);
            } elseif ($toDate) {
                $query->whereDate('payments.created_at', '<=', $toDate);
            }

            // ── Company filter ───────────────────────────────────────────
            if ($companyId) {
                $query->where('companies.id', $companyId);
            }

            // ── Collection type filter ───────────────────────────────────────────
            $collectionType = $request->input('collection_type');
            if (!empty($collectionType)) {
                $query->where('payments.collection_type', $collectionType);
            }

            // ── Search ───────────────────────────────────────────────────
            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('payments.payment_code',    'like', "%{$search}%")
                    ->orWhere('customers.customer_name','like', "%{$search}%")
                    ->orWhere('companies.company_name', 'like', "%{$search}%")
                    ->orWhere('companies.company_code', 'like', "%{$search}%");
                });
            }

            $recordsTotal    = $query->count();
            $recordsFiltered = $recordsTotal;

            $columnMap = [
                0  => 'payments.id',
                1  => 'payments.payment_code',
                2  => 'payments.customer_id',
                3  => 'payments.collection_type',
                4  => 'payments.created_at',
                5  => 'payments.payment_amount',
                6  => 'payments.late_paid_amount',
                7  => 'payments.interest_paid_amount',
                8  => 'payments.discount_amount',
                9  => 'payments.top_up_capital',
                10  => 'payments.top_up',
                11  => 'payments.top_up',
                12  => 'loans.balance',
            ];

            $orderColIdx = $request->input('order.0.column');
            $orderDir    = $request->input('order.0.dir', 'desc') === 'asc' ? 'asc' : 'desc';

            if ($orderColIdx !== null && isset($columnMap[$orderColIdx])) {
                $orderCol = $columnMap[$orderColIdx];
            } else {
                // default order
                $orderCol = 'payments.created_at';
                $orderDir = 'desc';
            }

            $data = $query->orderBy($orderCol, $orderDir)
                        ->skip($start)
                        ->take($length)
                        ->get();

            $totalPayment = [];

            foreach ($data as $row) {
                $loanId = $row->loan_id;

                if (!isset($running[$loanId])) {
                    $running[$loanId] = 0;
                }

                if (!isset($totalPayment[$loanId])) {
                    $totalPayment[$loanId] = 0;
                }

                $row->running_payment = (float) $row->payment_amount
                    + (float) $row->late_paid_amount
                    + (float) $row->interest_paid_amount
                    + (float) $row->discount_amount
                    - (float) ($row->top_up ?? 0);

                // accumulate paid amount
                $totalPayment[$loanId] += $row->running_payment;

                // deducted balance
                $row->deducted_balance = (float) ($row->payment ?? 0) - $totalPayment[$loanId];

                // this is what you want
                $row->total_paid_amount = $totalPayment[$loanId];

                $row->installment_calc = ($row->first_payment && $row->first_payment > 0)
                    ? floor($totalPayment[$loanId] / $row->first_payment)
                    : null;
            }

            return response()->json([
                "draw"            => intval($draw),
                "recordsTotal"    => $recordsTotal,
                "recordsFiltered" => $recordsFiltered,
                "data"            => $data,
            ]);

        } catch (\Exception $e) {
            \Log::error('Customer Payment Report Load Error: ' . $e->getMessage());

            return response()->json([
                'error'   => 'Server error occurred',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}