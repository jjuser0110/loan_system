<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\DailyReport;
use App\Models\CashBookReport;
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

            $query = PaymentMethodLog::query()
                ->select([
                    'payment_method_logs.*',
                    'payment_methods.company_id',
                    'companies.company_name',
                    'companies.company_code',
                    'branches.branch_name',
                    'branches.branch_code',
                    'customers.id as customer_id',
                    'customers.customer_name',
                    'expenses.expense_title as expenses_name',
                    // Conditional amounts
                    \DB::raw("CASE WHEN payment_method_logs.type = 'payment' THEN payment_method_logs.amount ELSE 0 END as customer_payment"),
                    \DB::raw("CASE WHEN payment_method_logs.type = 'loan'    THEN payment_method_logs.amount ELSE 0 END as loan_top_up"),
                    \DB::raw("CASE WHEN payment_method_logs.type = 'expense' THEN payment_method_logs.amount ELSE 0 END as expenses"),
                    \DB::raw("payment_method_logs.total as account_total_amount"),
                    \DB::raw("DATE(payment_method_logs.created_at) as date"),
                    // Description
                    \DB::raw("
                        CASE
                            WHEN payment_method_logs.type = 'payment' THEN CONCAT('Payment #', COALESCE(payments.payment_code, payment_method_logs.description))
                            WHEN payment_method_logs.type = 'loan'    THEN CONCAT('Loan #',    COALESCE(loans.loan_code,       payment_method_logs.description))
                            WHEN payment_method_logs.type = 'expense' THEN CONCAT('Expense #', COALESCE(expenses.expense_code, payment_method_logs.description))
                            ELSE CONCAT('Manual # ', COALESCE(payment_method_logs.description, '-'))
                        END as description
                    "),
                ])
                ->join('payment_methods', 'payment_method_logs.payment_method_id', '=', 'payment_methods.id')
                ->join('companies',       'payment_methods.company_id',            '=', 'companies.id')
                ->join('branches',        'companies.branch_id',                   '=', 'branches.id')
                ->leftJoin('payments', function($join) {
                    $join->on('payment_method_logs.content_id', '=', 'payments.id')
                        ->where('payment_method_logs.type', '=', 'payment');
                })
                ->leftJoin('loans', function($join) {
                    $join->on('payment_method_logs.content_id', '=', 'loans.id')
                        ->where('payment_method_logs.type', '=', 'loan');
                })
                ->leftJoin('expenses', function($join) {
                    $join->on('payment_method_logs.content_id', '=', 'expenses.id')
                        ->where('payment_method_logs.type', '=', 'expense');
                })
                ->leftJoin('customers', function($join) {
                    $join->on(function($q) {
                        $q->on('payments.customer_id', '=', 'customers.id')
                        ->orOn('loans.customer_id',  '=', 'customers.id');
                    });
                });

            // Role filter
            switch (Auth::user()->role_id) {
                case 1: break;
                case 2: $query->where('branches.id', Auth::user()->branch_id); break;
                case 3:
                case 4: $query->where('payment_methods.company_id', Auth::user()->company_id); break;
                default: throw new \Exception('Invalid role id.');
            }

            $query->where(function($q) {
                $q->where('payment_method_logs.description', '!=', 'Expense Updated')
                ->orWhereNull('payment_method_logs.description');
            });

            // Date filter
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

            $recordsTotal    = $query->count();
            $recordsFiltered = $recordsTotal;

            $data = $query->orderBy('payment_method_logs.created_at', 'asc')
                ->skip($start)
                ->take($length)
                ->get();

            return response()->json([
                "draw"            => intval($draw),
                "recordsTotal"    => $recordsTotal,
                "recordsFiltered" => $recordsFiltered,
                "data"            => $data,
            ]);

        } catch (\Exception $e) {
            \Log::error('Cash Book Reports Load Error: ' . $e->getMessage());
            return response()->json([
                'error'   => 'Server error occurred',
                'message' => $e->getMessage()
            ], 500);
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
        return view('report.cashbookreport')->with('companies', $companies);
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

            $query = PaymentMethodLog::query()
                ->select([
                    'payment_method_logs.*',
                    'payment_methods.company_id',
                    'companies.company_name',
                    'companies.company_code',
                    'branches.branch_name',
                    'branches.branch_code',
                    'customers.id as customer_id',
                    'customers.customer_name',
                    'expenses.expense_title as expenses_name',
                    // Conditional amounts
                    \DB::raw("CASE WHEN payment_method_logs.type = 'payment' THEN payment_method_logs.amount ELSE 0 END as customer_payment"),
                    \DB::raw("CASE WHEN payment_method_logs.type = 'loan'    THEN payment_method_logs.amount ELSE 0 END as loan_top_up"),
                    \DB::raw("CASE WHEN payment_method_logs.type = 'expense' THEN payment_method_logs.amount ELSE 0 END as expenses"),
                    \DB::raw("payment_method_logs.total as account_total_amount"),
                    \DB::raw("DATE(payment_method_logs.created_at) as date"),
                    // Description
                    \DB::raw("
                        CASE
                            WHEN payment_method_logs.type = 'payment' THEN CONCAT('Payment #', COALESCE(payments.payment_code, payment_method_logs.description))
                            WHEN payment_method_logs.type = 'loan'    THEN CONCAT('Loan #',    COALESCE(loans.loan_code,       payment_method_logs.description))
                            WHEN payment_method_logs.type = 'expense' THEN CONCAT('Expense #', COALESCE(expenses.expense_code, payment_method_logs.description))
                            ELSE CONCAT('Manual # ', COALESCE(payment_method_logs.description, '-'))
                        END as description
                    "),
                ])
                ->join('payment_methods', 'payment_method_logs.payment_method_id', '=', 'payment_methods.id')
                ->join('companies',       'payment_methods.company_id',            '=', 'companies.id')
                ->join('branches',        'companies.branch_id',                   '=', 'branches.id')
                ->leftJoin('payments', function($join) {
                    $join->on('payment_method_logs.content_id', '=', 'payments.id')
                        ->where('payment_method_logs.type', '=', 'payment');
                })
                ->leftJoin('loans', function($join) {
                    $join->on('payment_method_logs.content_id', '=', 'loans.id')
                        ->where('payment_method_logs.type', '=', 'loan');
                })
                ->leftJoin('expenses', function($join) {
                    $join->on('payment_method_logs.content_id', '=', 'expenses.id')
                        ->where('payment_method_logs.type', '=', 'expense');
                })
                ->leftJoin('customers', function($join) {
                    $join->on(function($q) {
                        $q->on('payments.customer_id', '=', 'customers.id')
                        ->orOn('loans.customer_id',  '=', 'customers.id');
                    });
                });

            // Role filter
            switch (Auth::user()->role_id) {
                case 1: break;
                case 2: $query->where('branches.id', Auth::user()->branch_id); break;
                case 3:
                case 4: $query->where('payment_methods.company_id', Auth::user()->company_id); break;
                default: throw new \Exception('Invalid role id.');
            }

            // Date filter
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

            $recordsTotal    = $query->count();
            $recordsFiltered = $recordsTotal;

            $data = $query->orderBy('payment_method_logs.created_at', 'asc')
                ->skip($start)
                ->take($length)
                ->get();

            return response()->json([
                "draw"            => intval($draw),
                "recordsTotal"    => $recordsTotal,
                "recordsFiltered" => $recordsFiltered,
                "data"            => $data,
            ]);

        } catch (\Exception $e) {
            \Log::error('Cash Book Reports Load Error: ' . $e->getMessage());
            return response()->json([
                'error'   => 'Server error occurred',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}