<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\DailyReport;
use App\Models\CashBookReport;
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
            
            $orderColumnIndex = $request->input('order.0.column', 10);
            $columns = $request->input('columns', []);
            $orderByColumn = isset($columns[$orderColumnIndex]['data']) ? $columns[$orderColumnIndex]['data'] : 'created_date';
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
            
            if (isset($columnMap[$orderByColumn])) {
                $orderColumn = $columnMap[$orderByColumn];
            } else {
                $orderColumn = 'daily_reports.' . $orderByColumn;
            }
            
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
        }
        catch(Exception $e) {
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
            $draw = $request->input('draw');
            $search = $request->input('search.value');
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);
            
            $orderColumnIndex = $request->input('order.0.column', 2);
            $columns = $request->input('columns', []);
            $orderByColumn = isset($columns[$orderColumnIndex]['data']) ? $columns[$orderColumnIndex]['data'] : 'date';
            $orderByDirection = $request->input('order.0.dir', 'desc');
            
            $query = CashBookReport::query()
                ->select([
                    'cash_book_reports.*',
                    'companies.company_name',
                    'companies.company_code',
                    'branches.branch_name',
                    'branches.branch_code',
                ])
                ->join('companies', 'cash_book_reports.company_id', '=', 'companies.id')
                ->join('branches', 'companies.branch_id', '=', 'branches.id');
            
            switch (Auth::user()->role_id) {
                case 1:
                    break;
                
                case 2:
                    $query->where('branches.id', Auth::user()->branch_id);
                    break;
                
                case 3:
                case 4:
                    $query->where('cash_book_reports.company_id', Auth::user()->company_id);
                    break;
                
                default:
                    throw new Exception('Invalid role id.');
            }
            
            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('companies.company_name', 'like', "%{$search}%")
                        ->orWhere('companies.company_code', 'like', "%{$search}%")
                        ->orWhere('branches.branch_name', 'like', "%{$search}%")
                        ->orWhere('branches.branch_code', 'like', "%{$search}%")
                        ->orWhere('cash_book_reports.description', 'like', "%{$search}%");
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
            
            if (isset($columnMap[$orderByColumn])) {
                $orderColumn = $columnMap[$orderByColumn];
            } else {
                $orderColumn = 'cash_book_reports.' . $orderByColumn;
            }
            
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
        }
        catch(Exception $e) {
            \Log::error('Cash Book Reports Load Error: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'Server error occurred',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}