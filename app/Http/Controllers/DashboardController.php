<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;

/**
 * Only index() is routed. The other methods that used to be here were
 * unused copies of LoanController methods and have been removed.
 */
class DashboardController extends Controller
{
    /** Blade view rendered by index(). StaffDashboardController overrides this. */
    protected $dashboardView = 'dashboard';

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
        return view($this->dashboardView)->with('companies',$companies);
    }
}
