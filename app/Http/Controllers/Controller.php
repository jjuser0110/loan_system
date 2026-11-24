<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Traits\IncrementsScheduleCode;
use Carbon\Carbon;
use App\Models\Sequence;
use App\Models\Customer;
use App\Models\Company;
use App\Models\Loan;
use App\Models\Payment;
use App\Models\PaymentSchedule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests, IncrementsScheduleCode;

    /* =========================================================
       SEQUENCE NUMBERS
       ========================================================= */

    public function getSequenceNumber($prefix, $type)
    {
        return $this->nextSequence($prefix, $type, 3);
    }

    public function getSystemCode()
    {
        $prefix = 'S' . Carbon::now()->format('y');
        return $this->nextSequence($prefix, 'system_code', 4);
    }

    private function nextSequence($prefix, $type, int $padLength)
    {
        return DB::transaction(function () use ($prefix, $type, $padLength) {
            $s = Sequence::lockForUpdate()->firstOrCreate(['prefix' => $prefix, 'type' => $type], ['last_number' => 0]);
            $s->last_number += 1;
            $s->save();
            return $prefix . str_pad($s->last_number, $padLength, '0', STR_PAD_LEFT);
        });
    }

    /* =========================================================
       ROLE-SCOPED QUERIES
       Role 1 = all, Role 2 = own branch, other roles = own company
       ========================================================= */

    /**
     * Companies table limited to what the logged-in user can see.
     */
    protected function scopedCompaniesTable()
    {
        $query = DB::table('companies');

        switch (Auth::user()->role_id) {
            case 1:
                break;

            case 2:
                $query->where('branch_id', Auth::user()->branch_id);
                break;

            default:
                $query->where('id', Auth::user()->company_id);
                break;
        }

        return $query;
    }

    /**
     * Customer autocomplete (code / name / NRIC), limited to 10 results.
     * Shared by LoanController and PaymentController.
     */
    protected function searchCustomersForUser($search)
    {
        switch (Auth::user()->role_id) {
            case 1:
                $query = Customer::query();
                $prefix = '';
                break;

            case 2:
                $query = Customer::join('companies', 'customers.company_id', '=', 'companies.id')
                    ->where('companies.branch_id', Auth::user()->branch_id);
                $prefix = 'customers.';
                break;

            default:
                $query = Customer::where('company_id', Auth::user()->company_id);
                $prefix = '';
        }

        return $query
            ->where(function ($q) use ($search, $prefix) {
                $q->where($prefix . 'customer_code', 'LIKE', "%{$search}%")
                    ->orWhere($prefix . 'customer_name', 'LIKE', "%{$search}%")
                    ->orWhere($prefix . 'nric_number', 'LIKE', "%{$search}%");
            })
            ->select($prefix . 'customer_code', $prefix . 'customer_name', $prefix . 'nric_number', $prefix . 'company_code')
            ->limit(10)
            ->get();
    }

    /* =========================================================
       ACCESS CHECKS
       ========================================================= */

    /**
     * Common role check. The callbacks are only evaluated for the role
     * that needs them, so relations are loaded lazily as before.
     */
    private function hasScopedAccess(callable $branchId, callable $companyId): bool
    {
        try {
            switch (Auth::user()->role_id) {
                case 1:
                    return true;

                case 2:
                    return Auth::user()->branch_id == $branchId();

                case 3:
                case 4:
                    return Auth::user()->company_id == $companyId();

                default:
                    return false;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    public function accessToCustomer(Customer $customer)
    {
        return $this->hasScopedAccess(
            fn () => $customer->company->branch_id,
            fn () => $customer->company_id
        );
    }

    public function accessToCompany(Company $company)
    {
        return $this->hasScopedAccess(
            fn () => $company->branch_id,
            fn () => $company->company_id // NOTE: kept exactly as before; probably meant $company->id
        );
    }

    public function accessToLoan(Loan $loan)
    {
        return $this->hasScopedAccess(
            fn () => $loan->company->branch_id,
            fn () => $loan->company_id
        );
    }

    public function accessToSchedule(PaymentSchedule $schedule)
    {
        return $this->hasScopedAccess(
            fn () => $schedule->company->branch_id,
            fn () => $schedule->company_id
        );
    }

    public function accessToPayment(Payment $payment)
    {
        return $this->hasScopedAccess(
            fn () => $payment->company->branch_id,
            fn () => $payment->loan->company_id
        );
    }
}
