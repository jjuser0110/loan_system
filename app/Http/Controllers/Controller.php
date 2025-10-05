<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Carbon\Carbon;
use App\Jobs\DoClosing;
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
    use AuthorizesRequests, ValidatesRequests;
    
    
    public function daily_closing()
    {
        DoClosing::dispatch();
        return true;
    }

    public function getSequenceNumber($prefix, $type){
        return DB::transaction(function () use ($prefix, $type){
            $s = Sequence::lockForUpdate()->firstOrCreate(['prefix'=>$prefix,'type'=>$type],['last_number'=>0]);
            $s->last_number += 1;
            $s->save();
            return $prefix.str_pad($s->last_number, 6, '0', STR_PAD_LEFT);
        });
    }

    public function accessToCustomer(Customer $customer){
        try{
            switch(Auth::user()->role_id){
                case 1:
                    return true;
                break;
                
                case 2:
                    return Auth::user()->branch_id == $customer->company->branch_id;
                break;

                case 3:
                case 4:
                    return Auth::user()->company_id == $customer->company_id;
                break;
                
                default:
                    return false;
            }
        }
        catch(Exception $e){
            return false;
        }
    }

    public function accessToCompany(Company $company){
         try{
            switch(Auth::user()->role_id){
                case 1:
                    return true;
                break;
                
                case 2:
                    return Auth::user()->branch_id == $company->branch_id;
                break;

                case 3:
                case 4:
                    return Auth::user()->company_id == $company->company_id;
                break;
                
                default:
                    return false;
            }
        }
        catch(Exception $e){
            return false;
        }
    }

    public function accessToLoan(Loan $loan){
        try{
            switch (Auth::user()->role_id) {
                case 1:
                    break;

                case 2:
                    if($loan->company->branch_id != Auth::user()->branch_id){
                        return false;
                    }
                    break;

                case 3:
                case 4:
                    if($loan->company_id != Auth::user()->company_id){
                        return false;
                    }
                    break;

                default:
                    return false;
            }
            return true;
        }
        catch(Exception $e){
            return false;
        }
    }

    public function accessToSchedule(PaymentSchedule $schedule){
        try{
            switch (Auth::user()->role_id) {
                case 1:
                    break;

                case 2:
                    if($schedule->company->branch_id != Auth::user()->branch_id){
                        return false;
                    }
                    break;

                case 3:
                case 4:
                    if($schedule->company_id != Auth::user()->company_id){
                        return false;
                    }
                    break;

                default:
                    return false;
            }
            return true;
        }
        catch(Exception $e){
            return false;
        }
    }

    public function accessToPayment(Payment $payment){
        try{
            switch (Auth::user()->role_id) {
                case 1:
                    break;

                case 2:
                    if($payment->company->branch_id != Auth::user()->branch_id){
                        return false;
                    }
                    break;

                case 3:
                case 4:
                    if($payment->company_id != Auth::user()->company_id){
                        return false;
                    }
                    break;

                default:
                    return false;
            }
            return true;
        }
        catch(Exception $e){
            return false;
        }
    }
}
