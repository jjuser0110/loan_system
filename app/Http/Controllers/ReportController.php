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
                    'expenses.expense_title as expenses_name',
                    'expenses.expense_description as expenses_description',
                    'payments.top_up as top_up',
                    'payments.payment_amount as customer_payment',
                    \DB::raw("
                        COALESCE(
                            CASE WHEN payment_method_logs.type = 'payment' THEN payments.collection_type END,
                            CASE WHEN payment_method_logs.type = 'loan'    THEN loans.interest_group END,
                            CASE WHEN payment_method_logs.type = 'payment' THEN payment_loans.interest_group END
                        ) as collection_type
                    "),
                    \DB::raw("CASE WHEN payment_method_logs.type = 'loan'    THEN payment_method_logs.amount ELSE 0 END as loan_top_up"),
                    \DB::raw("CASE WHEN payment_method_logs.type = 'expense' THEN payment_method_logs.amount ELSE 0 END as expenses"),
                    \DB::raw("
                        (
                            SELECT pml_latest.total
                            FROM payment_method_logs pml_latest
                            WHERE pml_latest.content_id = payment_method_logs.content_id
                            AND pml_latest.type = payment_method_logs.type
                            AND pml_latest.payment_method_id = payment_method_logs.payment_method_id
                            ORDER BY pml_latest.created_at DESC, pml_latest.id DESC
                            LIMIT 1
                        ) as account_total_amount
                    "),
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
                            WHEN payment_method_logs.description LIKE 'Loan Deleted%' THEN 1
                            ELSE 0
                        END as is_deleted_row
                    "),
                    \DB::raw("
                        CASE
                            WHEN payment_method_logs.type = 'payment' THEN (SELECT p.interest_paid_amount FROM payments p WHERE p.id = payment_method_logs.content_id LIMIT 1)
                            ELSE NULL
                        END as interest_paid
                    "),
                    \DB::raw("
                        CASE
                            WHEN payment_method_logs.type = 'payment' THEN (
                                SELECT p.top_up_capital FROM payments p WHERE p.id = payment_method_logs.content_id LIMIT 1
                            )
                            WHEN payment_method_logs.description LIKE 'Loan Deleted%' THEN (
                                SELECT SUM(pml_del.amount)
                                FROM payment_method_logs pml_del
                                WHERE pml_del.description = payment_method_logs.description
                            )
                            ELSE NULL
                        END as top_up_capital
                    "),
                    \DB::raw("
                        CASE
                            WHEN payment_method_logs.type = 'loan' THEN (
                                SELECT 
                                    l.capital
                                    - COALESCE((
                                        SELECT SUM(p_all.top_up_capital)
                                        FROM payments p_all
                                        WHERE p_all.loan_id = l.id
                                        AND p_all.top_up_capital IS NOT NULL
                                        AND p_all.top_up_capital > 0
                                    ), 0)
                                FROM loans l
                                WHERE l.id = payment_method_logs.content_id
                                LIMIT 1
                            )
                            ELSE NULL
                        END as new_capital_loan
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
                ->leftJoin('loans as payment_loans', function ($join) {
                    $join->on('payments.loan_id', '=', 'payment_loans.id')
                        ->where('payment_method_logs.type', '=', 'payment');
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
                })
                ->where(function ($q) {
                    $q->where('payment_method_logs.type', '!=', 'payment')
                    ->orWhereNotNull('payments.payment_code');
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
                ->where('payment_method_logs.description', '!=', 'Loan Updated')
                ->where(function ($q2) {
                    $q2->where('payment_method_logs.description', 'NOT LIKE', 'Loan Deleted%')
                    ->orWhere('payment_method_logs.id', '=', \DB::raw("(
                        SELECT MAX(pml_del.id)
                        FROM payment_method_logs pml_del
                        WHERE pml_del.content_id = payment_method_logs.content_id
                        AND pml_del.type = payment_method_logs.type
                        AND pml_del.description LIKE 'Loan Deleted%'
                    )"));
                })
                ->where(function ($q3) {
                    // Hide Loan Created rows where loan no longer exists (hard deleted)
                    $q3->where('payment_method_logs.description', '!=', 'Loan Created')
                    ->orWhereExists(function ($sub) {
                        $sub->select(\DB::raw(1))
                            ->from('loans')
                            ->whereColumn('loans.id', 'payment_method_logs.content_id');
                    });
                })
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
                1  => 'customers.customer_name',
                2  => 'payment_method_logs.created_at',
                3  => 'customers.customer_name',
                4  => 'payments.collection_type',
                5  => 'expenses.expense_title',
                6  => 'expenses.expense_description',
                7  => 'payments.remark',
                9  => 'payments.interest_paid_amount',
                8  => 'payments.payment_amount',
                10 => 'payments.top_up_capital',
                11 => 'payments.top_up',
                12 => 'expenses.amount',
                13 => 'payment_method_logs.total',
            ];

            $orderColIdx = (int) $request->input('order.0.column', 2);
            $orderDir    = $request->input('order.0.dir', 'asc') === 'desc' ? 'desc' : 'asc';
            $orderCol    = $columnMap[$orderColIdx] ?? 'payment_method_logs.created_at';

            $baseQuery = clone $query;

            if ($orderCol === 'payment_method_logs.created_at') {

                $query->orderBy(
                    \DB::raw('(
                        SELECT MAX(pml2.created_at)
                        FROM payment_method_logs pml2
                        WHERE pml2.content_id = payment_method_logs.content_id
                        AND pml2.type = payment_method_logs.type
                    )'),
                    $orderDir
                );

            } else {

                $query->orderBy($orderCol, $orderDir);

            }

            $data = $query
                ->skip($start)
                ->take($length)
                ->get();

            $firstRow = $baseQuery
                ->orderBy('payment_method_logs.created_at', 'asc')
                ->orderBy('payment_method_logs.id', 'asc')
                ->select('payment_method_logs.total')
                ->first();

            $openingBalance = (float) ($firstRow->total ?? 0);

            return response()->json([
                'draw'            => intval($draw),
                'recordsTotal'    => $total,
                'recordsFiltered' => $total,
                'data'            => $data,
                'opening_balance' => $openingBalance,
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
                    'payments.top_up as top_up',
                    'payments.payment_amount as customer_payment',
                    \DB::raw("CASE WHEN payment_method_logs.type = 'loan'    THEN payment_method_logs.amount ELSE 0 END as loan_top_up"),
                    \DB::raw("CASE WHEN payment_method_logs.type = 'expense' THEN payment_method_logs.amount ELSE 0 END as expenses"),
                    \DB::raw("DATE(payment_method_logs.created_at) as date"),
                    \DB::raw("
                        CASE
                            WHEN payment_method_logs.type = 'payment' AND payments.top_up IS NOT NULL AND payments.top_up > 0
                                THEN CONCAT('Loan TopUp - Payment #', COALESCE(payments.payment_code, payment_method_logs.description),
                                    IF(payment_method_logs.description = 'Payment Updated', ' Edit', ''))
                            WHEN payment_method_logs.type = 'payment'
                                THEN CONCAT('Payment #', COALESCE(payments.payment_code, payment_method_logs.description),
                                    IF(payment_method_logs.description = 'Payment Updated', ' Edit', ''))
                            WHEN payment_method_logs.type = 'loan'
                                THEN CONCAT('Loan #', COALESCE(loans.loan_code, payment_method_logs.description))
                            WHEN payment_method_logs.type = 'expense'
                                THEN CONCAT('Expense #', COALESCE(expenses.expense_code, payment_method_logs.description))
                            ELSE CONCAT('Manual # ', COALESCE(payment_method_logs.description, '-'))
                        END as description
                    "),
                    // interest_paid
                    \DB::raw("
                        CASE
                            WHEN payment_method_logs.type = 'payment' AND (
                                SELECT p.interest_paid_amount FROM payments p 
                                WHERE p.id = payment_method_logs.content_id LIMIT 1
                            ) > 0 THEN payment_method_logs.amount
                            ELSE NULL
                        END as interest_paid
                    "),

                    // customer_payment
                    \DB::raw("
                        CASE
                            WHEN payment_method_logs.type = 'payment' AND (
                                SELECT p.payment_amount FROM payments p 
                                WHERE p.id = payment_method_logs.content_id LIMIT 1
                            ) > 0 THEN payment_method_logs.amount
                            ELSE NULL
                        END as customer_payment
                    "),
                    \DB::raw("payment_method_logs.total as account_total_amount"),
                    \DB::raw("
                        CASE
                            WHEN payment_method_logs.type = 'loan' THEN (
                                SELECT 
                                    l.capital
                                    - COALESCE((
                                        SELECT SUM(p_all.top_up_capital)
                                        FROM payments p_all
                                        WHERE p_all.loan_id = l.id
                                        AND p_all.top_up_capital IS NOT NULL
                                        AND p_all.top_up_capital > 0
                                    ), 0)
                                FROM loans l
                                WHERE l.id = payment_method_logs.content_id
                                LIMIT 1
                            )
                            ELSE NULL
                        END as new_capital_loan
                    "),
                    // top_up_capital
                    \DB::raw("
                        CASE
                            WHEN payment_method_logs.type = 'payment' AND (
                                SELECT p.top_up_capital FROM payments p 
                                WHERE p.id = payment_method_logs.content_id LIMIT 1
                            ) > 0 THEN payment_method_logs.amount
                            WHEN payment_method_logs.description LIKE 'Loan Deleted%' AND payment_method_logs.amount != 0 THEN payment_method_logs.amount
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

            // Hide Loan Created rows where loan has been hard deleted
            $query->where(function ($q) {
                $q->where('payment_method_logs.description', '!=', 'Loan Created')
                ->orWhereExists(function ($sub) {
                    $sub->select(\DB::raw(1))
                        ->from('loans')
                        ->whereColumn('loans.id', 'payment_method_logs.content_id');
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
                1  => 'customers.customer_name',
                2  => 'payment_method_logs.id',
                3  => 'customers.customer_name',
                4  => 'payments.collection_type',
                5  => 'expenses.expense_title',
                6  => 'expenses.expense_description',
                7  => 'payments.remark',
                9  => 'payments.interest_paid_amount',
                8  => 'payments.payment_amount',
                10 => 'payments.top_up_capital',
                11 => 'payments.top_up',
                12 => 'expenses.amount',
                13 => 'payment_method_logs.total',
            ];

            $orderColIdx = (int) $request->input('order.0.column', 2);
            $orderDir    = $request->input('order.0.dir', 'asc') === 'desc' ? 'desc' : 'asc';
            $orderCol    = $columnMap[$orderColIdx] ?? 'payment_method_logs.created_at';

            $baseQuery = (clone $query); // ← clone BEFORE orderBy/skip/take

            $data = $query->orderBy($orderCol, $orderDir)
                        ->skip($start)
                        ->take($length)
                        ->get();

            $firstRow = $baseQuery
                ->orderBy('payment_method_logs.created_at', 'asc')
                ->orderBy('payment_method_logs.id', 'asc')
                ->select('payment_method_logs.total')
                ->first();

            $openingBalance = (float) ($firstRow->total ?? 0);

            return response()->json([
                "draw"            => intval($draw),
                "recordsTotal"    => $total,
                "recordsFiltered" => $total,
                "data"            => $data,
                "opening_balance" => $openingBalance,
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
                    'loans.interest as interest',
                    'loans.installment as installment',
                    'loans.first_payment as first_payment',
                    'customers.customer_name as customer_name',
                    'customers.id as customer_id',
                    'companies.id as company_id',
                    'loans.balance as balance',
                    \DB::raw("
                        (loans.payment + COALESCE(loans.interest, 0) + COALESCE(loans.installment, 0))
                        - COALESCE((
                            SELECT SUM(
                                p2.payment_amount
                                + COALESCE(p2.late_paid_amount, 0)
                                + COALESCE(p2.interest_paid_amount, 0)
                                + COALESCE(p2.discount_amount, 0)
                                - COALESCE(p2.top_up, 0)
                            )
                            FROM payments p2
                            WHERE p2.loan_id = payments.loan_id
                            AND p2.id <= payments.id
                        ), 0) as outstanding_balance
                    "),
                    \DB::raw("
                        loans.loan_amount
                        - COALESCE((
                            SELECT SUM(
                                p2.payment_amount
                                + COALESCE(p2.late_paid_amount, 0)
                                + COALESCE(p2.interest_paid_amount, 0)
                                + COALESCE(p2.discount_amount, 0)
                                - COALESCE(p2.top_up, 0)
                            )
                            FROM payments p2
                            WHERE p2.loan_id = payments.loan_id
                            AND p2.id <= payments.id
                        ), 0) as balance
                    "),
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
                1  => 'payments.customer_id',
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
                    - (float) ($row->top_up_capital ?? 0);

                // accumulate paid amount
                $totalPayment[$loanId] += $row->running_payment;

                // deducted balance (kept as-is)
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