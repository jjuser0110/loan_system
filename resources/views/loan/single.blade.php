@extends('layouts.app')
<style>
    #btn-search{
        white-space: nowrap
    }

    #input-search{
        width: 100%;
        max-width: 275px
    }

    #search-wrapper{
        display: flex;
        gap: 5px;
        justify-content: flex-start;
        flex-wrap: wrap;
        align-items: center;
    }

    #btn-search{
        flex: 0 1 32px;height:100%
    }

    @media screen and (max-width:500px) {
        #input-search{
            flex: 1 1 60%;
        }
    }

    #loan-dropdown{
        width: 275px
    }

    .tab-content{
        padding: 0 !important;
    }

    .tab-pane{
        padding: 15px !important;
    }
</style>
@section('content')
<header class="page-header">
    <h2>{{ __('table.loan_detail') }}</h2> 
</header>
@include('layouts.flash-message')
<div class="row">
    <section class="card">
        <form class="theme-form mega-form" action="{{ route('loan.single_loan') }}" method="get">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <label id="label-search" class="col-form-label">{{ __('table.loan_code') }}</label>
                        <div id="search-wrapper">
                            <input type="text" id="input-search" class="form-control" name="loan_code" value="{{ $loan?->loan_code ?? '' }}">
                            <button type="submit" class="btn btn-primary" id="btn-search"><i class="fas fa-search"></i></button>
                        </div>
                       <div id="loan-dropdown" class="dropdown-menu col-md-5 col-10" style="display:none; max-height: 200px; overflow-y: auto; padding:0;"></div>
                    </div>
                    <div class="col-md-4" style="display:flex;flex-wrap:nowrap; gap: 5px;justify-content:flex-end;text-align:right">
                        @if($loan)
                        <div>
                            <label class="col-form-label" style="padding:0">{{ __('table.status') }}</label>
                            <select id="loan-status" name="status" class="form-control"
                                style="
                                    color: 
                                    {{ $loan->status == 'Active' ? 'green' : 
                                    ($loan->status == 'Overdue' ? '#7a6800' : 
                                    ($loan->status == 'Fully Paid' ? 'red' : 'black')) }};
                                    font-weight:700; 
                                    font-size: 1.5rem; 
                                    height: auto; 
                                    padding: 5px 5px; 
                                    width: 170px;
                                ">
                                
                                <option value="Active" style="color:green" {{ $loan->status == 'Active' ? 'selected' : '' }}>Active</option>
                                <option value="Overdue" style="color:#7a6800" {{ $loan->status == 'Overdue' ? 'selected' : '' }}>Overdue</option>
                                <option value="Fully Paid" style="color:red" {{ $loan->status == 'Fully Paid' ? 'selected' : '' }}>Fully Paid</option>

                            </select>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
         </form>
    </section>
</div>
<div class="row" style="padding-top:0;"> 
    <div class="col-sm-12 col-xl-12">
        <div class="tabs">
            <ul class="nav nav-tabs">
                <li class="nav-item active">
                    <a class="nav-link active" data-bs-target="#overview" href="#overview" data-bs-toggle="tab">{{ __('table.overview') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-target="#loan" href="#loan" data-bs-toggle="tab">{{ __('table.information') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-target="#schedule" href="#schedule" data-bs-toggle="tab">{{ __('table.schedule') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-target="#payment" href="#payment" data-bs-toggle="tab">{{ __('table.payment') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" 
                    href="{{ url('customer/' . $loan->customer_id . '/edit#loan') }}">
                        {{ __('table.all_loan') }}
                    </a>
                </li>
            </ul>
             <div class="tab-content">
                <div id="overview" class="tab-pane active">
                    <div class="col-lg-12">
                        <section class="card cus-display-only">
                        @if(!$loan)
                            <p style="width:100%;text-align:center;margin:5px 0;font-size:14px">
                                {{ __('table.no_loan_found') }}
                            </p>
                        @else

                        <form class="theme-form mega-form" id="form-loan-overview">
                            <div class="row">

                                <!-- LOAN AMOUNT -->
                                <div class="col-xl-3">
                                    <section class="card mb-3">
                                        <div class="card-body" style="background-color:#e6f2ff;">
                                            <div class="widget-summary cus-summary">
                                                <div class="widget-summary-col">
                                                    <div class="summary">
                                                        <h4 class="title">{{ __('table.loan_amount') }}</h4>
                                                        <div class="info">
                                                            <strong class="amount">RM {{ $loan->loan_amount }}</strong>
                                                        </div>
                                                    </div>
                                                    <div class="summary-footer">
                                                        <a class="text-muted text-uppercase">
                                                            {{ $loan->interest_group }} |
                                                            {{ $loan->interest_rate.'%'}}
                                                            {{ $loan->interest_group == "SKIM B" ? '| '.$loan->loan_term.' months' : '' }}
                                                            {{ $loan->interest_group == "SKIM B" ? '| '.$loan->next_due_amount.'$' : '' }}
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </section>
                                </div>

                                <!-- OUTSTANDING -->
                                <div class="col-xl-3">
                                    <section class="card mb-3">
                                        <div class="card-body" style="background-color:#e6f2ff;">
                                            <div class="widget-summary cus-summary">
                                                <div class="widget-summary-col">
                                                    <div class="summary">
                                                        <h4 class="title">{{ __('table.outstanding') }}</h4>
                                                        <div class="info">
                                                            <strong class="amount">RM {{ $loan->outstanding }}</strong>
                                                        </div>
                                                    </div>
                                                    <div class="summary-footer">
                                                        <a class="text-muted text-uppercase">
                                                            {{ __('table.next') }}:
                                                            {{ $loan->next_due_amount }}
                                                            ({{ $loan->next_due_date}})
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </section>
                                </div>

                                <!-- BALANCE -->
                                <div class="col-xl-3">
                                    <section class="card mb-3">
                                        <div class="card-body" style="background-color:#e6f2ff;">
                                            <div class="widget-summary cus-summary">
                                                <div class="widget-summary-col">
                                                    <div class="summary">
                                                        <h4 class="title">{{ __('table.balance') }}</h4>
                                                        <div class="info">
                                                            <strong class="amount">RM {{ $loan->balance }}</strong>
                                                        </div>
                                                    </div>
                                                    <div class="summary-footer">
                                                        <a class="text-muted text-uppercase">
                                                            {{ __('table.capital') }}: RM{{ $loan->capital }}
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </section>
                                </div>

                                <!-- PAYMENT -->
                                <div class="col-xl-3">
                                    <section class="card mb-3">
                                        <div class="card-body">
                                            <div class="widget-summary cus-summary">
                                                <div class="widget-summary-col">

                                                    @if($loan->interest_group == 'SKIM B')
                                                        <div class="summary">
                                                            <h4 class="title">{{ __('table.total_payment') }}</h4>
                                                            <div class="info">
                                                                <strong class="amount">
                                                                    RM {{ number_format(($loan->installment * ($loan->loan_term - 2)) + $loan->first_payment + $loan->last_payment,2,'.',',') }}
                                                                </strong>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="summary">
                                                            <h4 class="title">{{ __('table.payment') }}</h4>
                                                            <div class="info">
                                                                <strong class="amount">
                                                                    RM {{ ($loan->balance/100) * $loan->interest }}/m
                                                                </strong>
                                                            </div>
                                                        </div>
                                                    @endif

                                                    <div class="summary-footer">
                                                        <a class="text-muted text-uppercase">
                                                            {{ __('table.paid') }}: RM {{ $loan->paid }}
                                                        </a>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </section>
                                </div>

                                <!-- INTEREST -->
                                <div class="col-xl-3">
                                    <section class="card mb-3">
                                        <div class="card-body" style="{{ $loan->total_late_charge - $loan->tota_late_paid > 0 ? 'background:var(--background-outstanding)' : '' }}">
                                            <div class="widget-summary cus-summary">
                                                <div class="widget-summary-col">
                                                    <div class="summary">
                                                        <h4 class="title">{{ __('table.total_interest') }}</h4>
                                                        <div class="info">
                                                            <strong class="amount">RM {{ $loan->interest }}</strong>
                                                        </div>
                                                    </div>
                                                    <div class="summary-footer">
                                                        <a class="text-muted text-uppercase">
                                                            {{ __('table.paid') }}: RM{{ $loan->interest_paid }}
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </section>
                                </div>

                                <!-- LATE -->
                                <div class="col-xl-3">
                                    <section class="card mb-3">
                                        <div class="card-body" style="{{ $loan->total_late_charge - $loan->tota_late_paid > 0 ? 'background:var(--background-outstanding)' : '' }}">
                                            <div class="widget-summary cus-summary">
                                                <div class="widget-summary-col">
                                                    <div class="summary">
                                                        <h4 class="title">{{ __('table.total_late') }}</h4>
                                                        <div class="info">
                                                            <strong class="amount">RM {{ $loan->late }}</strong>
                                                        </div>
                                                    </div>
                                                    <div class="summary-footer">
                                                        <a class="text-muted text-uppercase">
                                                            {{ __('table.paid') }}: RM{{ $loan->late_paid }}
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </section>
                                </div>

                                <!-- DISCOUNT -->
                                <div class="col-xl-3">
                                    <section class="card mb-3">
                                        <div class="card-body">
                                            <div class="widget-summary cus-summary">
                                                <div class="widget-summary-col">
                                                    <div class="summary">
                                                        <h4 class="title">{{ __('table.total_discount') }}</h4>
                                                        <div class="info">
                                                            <strong class="amount">RM {{ $loan->discount }}</strong>
                                                        </div>
                                                    </div>
                                                    <div class="summary-footer">
                                                        <a class="text-muted text-uppercase">-</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </section>
                                </div>

                                <!-- PROFIT -->
                                <div class="col-xl-3">
                                    <section class="card mb-3">
                                        <div class="card-body">
                                            <div class="widget-summary cus-summary">
                                                <div class="widget-summary-col">
                                                    <div class="summary">
                                                        <h4 class="title">{{ __('table.total_profit') }}</h4>
                                                        <div class="info">
                                                            <strong class="amount">
                                                                RM <span id="total-profit">{{ __('table.loading') }}</span>
                                                            </strong>
                                                        </div>
                                                    </div>
                                                    <div class="summary-footer">
                                                        <a class="text-muted text-uppercase">-</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </section>
                                </div>

                            </div>
                        </form>

                        @endif
                        </section>
                    </div>
                </div>
            </div>

            <div class="tab-content">
                <div id="loan" class="tab-pane">
                    <div class="col-lg-12">
                        <section class="card cus-display-only">

                        @if(!$loan)
                            <p style="width:100%;text-align:center;margin:5px 0;font-size:14px">
                                {{ __('table.no_loan_found') }}
                            </p>
                        @else

                        @php
                            $hasPayment = \App\Models\Payment::where('payment_code', 'LIKE', $loan->loan_code . '%')->exists();
                        @endphp

                        <style>
                            .editable-field {
                                background-color: #ffffff;
                            }

                            /* 🔵 locked editable */
                            .locked-editable {
                                background-color: #cce5ff;
                                cursor: not-allowed;
                            }

                            /* ⚪ normal readonly */
                            .readonly-field {
                                background-color: #e9ecef;
                                cursor: not-allowed;
                            }
                        </style>

                        <form action="{{ route('loan.update', $loan->loan_code) }}" method="POST">
                        @csrf

                        <div class="card-body">

                        <!-- CUSTOMER -->
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="cus-header">{{ __('table.customer') }}</h4>
                                <div class="row">
                                    <div class="col-xl-4 mb-3">
                                        <label class="col-form-label">{{ __('table.system_code') }}</label>
                                        <input type="text" class="form-control readonly-field" value="{{ $loan?->customer->customer_code ?? '' }}" readonly>
                                    </div>

                                    <div class="col-xl-4 mb-3">
                                        <label class="col-form-label">{{ __('table.customer_name') }}</label>
                                        <input type="text" class="form-control readonly-field" value="{{ $loan?->customer->customer_name ?? '' }}" readonly>
                                    </div>

                                    <div class="col-xl-4 mb-3">
                                        <label class="col-form-label">NRIC {{ __('table.number') }}</label>
                                        <input type="text" class="form-control readonly-field" value="{{ $loan?->customer->nric_number ?? '' }}" readonly>
                                    </div>
                                </div>
                            </div>

                            <!-- COMPANY -->
                            <div class="col-md-6">
                                <h4 class="cus-header">{{ __('table.company') }}</h4>
                                <div class="row">
                                    <div class="col-xl-4 mb-3">
                                        <label class="col-form-label">{{ __('table.company_code') }}</label>
                                        <input type="text" class="form-control readonly-field" value="{{ $loan?->company->company_code ?? '' }}" readonly>
                                    </div>

                                    <div class="col-xl-4 mb-3">
                                        <label class="col-form-label">{{ __('table.company_name') }}</label>
                                        <input type="text" class="form-control readonly-field" value="{{ $loan?->company->company_name ?? '' }}" readonly>
                                    </div>

                                    <div class="col-xl-4 mb-3">
                                        <label class="col-form-label">{{ __('table.branch') }}</label>
                                        <input type="text" class="form-control readonly-field" value="{{ $loan?->company?->branch->branch_name ?? '' }}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h4 class="cus-header">
                            {{ __('table.loan') }} ({{ $loan->loan_code }})
                        </h4>

                        <div class="row">
                            
                        @if($loan?->interest_group == 'SKIM B')

                        <div class="col-xl-3 mb-3">
                            <label>{{ __('table.interest_group') }}</label>
                            <input type="text" id="interest-group" name="interest_group"
                                class="form-control {{ $hasPayment ? 'locked-editable' : 'readonly-field' }}"
                                value="{{ $loan?->interest_group ?? '' }}"
                                readonly>
                        </div>

                        <div class="col-xl-3 mb-3">
                            <label>{{ __('table.created_at') }}</label>
                            <input type="text" id="interest-group" name="created_at"
                                class="form-control {{ $hasPayment ? 'locked-editable' : 'readonly-field' }}"
                                value="{{ $loan?->created_at ?? '' }}"
                                readonly>
                        </div>
                        
                        <div class="col-xl-3 mb-3">
                            <label>{{ __('table.first_payment') }}</label>
                            <input type="text" id="first-payment" name="first_payment"
                                class="form-control {{ $hasPayment ? 'readonly-field' : 'editable-field' }}"
                                value="{{ $loan?->first_payment ?? '' }}"
                                {{ $hasPayment ? 'readonly' : '' }}>
                        </div>
                        
                        <div class="col-xl-3 mb-3">
                            <label>{{ __('table.start_date') }}</label>
                            <input type="text"
                                class="form-control readonly-field"
                                value="{{ $loan?->year_month ?? '' }}"
                                readonly>
                        </div>

                        <div class="col-xl-3 mb-3">
                            <label>{{ __('table.loan_amount') }}</label>
                            <input type="text" id="loan-amount" name="loan_amount"
                                class="form-control {{ $hasPayment ? 'locked-editable' : 'editable-field' }}"
                                value="{{ $loan?->loan_amount ?? '' }}"
                                {{ $hasPayment ? 'readonly' : '' }}>
                        </div>

                        <div class="col-xl-3 mb-3">
                            <label>{{ __('table.loan_term') }}</label>
                            <input type="text" id="loan-term" name="loan_term"
                                class="form-control {{ $hasPayment ? 'locked-editable' : 'editable-field' }}"
                                value="{{ $loan?->loan_term ?? '' }}"
                                {{ $hasPayment ? 'readonly' : '' }}>
                        </div>

                        <div class="col-xl-3 mb-3">
                            <label>{{ __('table.installment') }}</label>
                            <input type="text" id="installment" name="installment"
                                class="form-control {{ $hasPayment ? 'locked-editable' : 'editable-field' }}"
                                value="{{ $loan?->installment ?? '' }}"
                                {{ $hasPayment ? 'readonly' : '' }}>
                        </div>

                        <div class="col-xl-3 mb-3">
                            <label>{{ __('table.interest_rate') }}</label>
                            <div class="d-flex">
                                <input type="text" id="interest-rate" name="interest_rate"
                                    class="form-control {{ $hasPayment ? 'locked-editable' : 'editable-field' }}"
                                    value="{{ $loan?->interest_rate ?? '' }}"
                                    {{ $hasPayment ? 'readonly' : '' }}>

                                <button type="button"
                                    class="btn btn-primary ms-2"
                                    onclick="calculateInterest()"
                                    style="font-size:12px;">
                                    {{ __('table.calculate_interest') }}
                                </button>
                            </div>
                        </div>

                        <div class="col-xl-3 mb-3">
                            <label>{{ __('table.capital') }}</label>
                            <input type="text" id="capital" name="capital"
                                class="form-control {{ $hasPayment ? 'locked-editable' : 'readonly-field' }}"
                                value="{{ $loan?->capital ?? '' }}"
                                readonly>
                        </div>

                        <div class="col-xl-3 mb-3">
                            <label>{{ __('table.processing_fee') }}</label>
                            <input type="text" name="processing_fee"
                                class="form-control {{ $hasPayment ? 'locked-editable' : 'editable-field' }}"
                                value="{{ $loan?->processing_fee ?? '' }}"
                                {{ $hasPayment ? 'readonly' : '' }}>
                        </div>

                        <div class="col-xl-3 mb-3">
                            <label>{{ __('table.last_payment') }}</label>
                            <input type="text" id="last-payment" name="last_payment"
                                class="form-control {{ $hasPayment ? 'locked-editable' : 'editable-field' }}"
                                value="{{ $loan?->last_payment ?? '' }}"
                                {{ $hasPayment ? 'readonly' : '' }}>
                        </div>

                        @endif

                        @if($loan?->interest_group == 'SKIM A')

                        <div class="col-xl-3 mb-3">
                            <label>{{ __('table.interest_group') }}</label>
                            <input type="text" id="interest-group" name="interest_group"
                                class="form-control {{ $hasPayment ? 'locked-editable' : 'readonly-field' }}"
                                value="{{ $loan?->interest_group ?? '' }}"
                                readonly>
                        </div>

                        <div class="col-xl-3 mb-3">
                            <label>{{ __('table.created_at') }}</label>
                            <input type="text"
                                class="form-control readonly-field"
                                value="{{ $loan?->created_at ?? '' }}"
                                readonly>
                        </div>
                        
                        <div class="col-xl-3 mb-3">
                            <label>{{ __('table.installment') }}</label>
                            <input type="text" id="installment" name="installment"
                                class="form-control {{ $hasPayment ? 'locked-editable' : 'editable-field' }}"
                                value="{{ $loan?->installment ?? '' }}"
                                {{ $hasPayment ? 'readonly' : '' }}>
                        </div>

                        <div class="col-xl-3 mb-3">
                            <label>{{ __('table.start_date') }}</label>
                            <input type="text"
                                class="form-control readonly-field"
                                value="{{ $loan?->year_month ?? '' }}"
                                readonly>
                        </div>

                        <div class="col-xl-3 mb-3">
                            <label>{{ __('table.loan_amount') }}</label>
                            <input type="text" id="loan-amount" name="loan_amount"
                                class="form-control {{ $hasPayment ? 'locked-editable' : 'editable-field' }}"
                                value="{{ $loan?->loan_amount ?? '' }}"
                                {{ $hasPayment ? 'readonly' : '' }}>
                        </div>

                        <div class="col-xl-3 mb-3">
                            <label>{{ __('table.capital') }}</label>
                            <input type="text" id="capital" name="capital"
                                class="form-control {{ $hasPayment ? 'locked-editable' : 'readonly-field' }}"
                                value="{{ $loan?->capital ?? '' }}"
                                readonly>
                        </div>

                        <div class="col-xl-3 mb-3">
                            <label>{{ __('table.processing_fee') }}</label>
                            <input type="text" name="processing_fee"
                                class="form-control {{ $hasPayment ? 'locked-editable' : 'editable-field' }}"
                                value="{{ $loan?->processing_fee ?? '' }}"
                                {{ $hasPayment ? 'readonly' : '' }}>
                        </div>

                        <div class="col-xl-3 mb-3">
                            <label>{{ __('table.interest_rate') }}</label>
                            <div class="d-flex">
                                <input type="text" id="interest-rate" name="interest_rate"
                                    class="form-control {{ $hasPayment ? 'locked-editable' : 'editable-field' }}"
                                    value="{{ $loan?->interest_rate ?? '' }}"
                                    {{ $hasPayment ? 'readonly' : '' }}>

                                <button type="button"
                                    class="btn btn-primary ms-2"
                                    onclick="calculateInterest()"
                                    style="font-size:12px;">
                                    {{ __('table.calculate_interest') }}
                                </button>
                            </div>
                        </div>
                        
                        @endif

                        </div>

                        <button type="submit" class="btn btn-primary"
                            {{ $hasPayment ? 'disabled' : '' }}>
                            Submit
                        </button>

                        @if($hasPayment)
                            <div class="alert alert-warning mt-3">
                                {{ __('table.this_loan_is_lock') }}
                            </div>
                        @endif

                        </div>
                        </form>

                        @endif

                        </section>
                    </div>
                </div>
            </div>

            <div class="tab-content">
                <div id="payment" class="tab-pane">
                     <div class="col-lg-12">
                        <section class="card">
                            <div class="mb-3" style="text-align: right;">
                                <a class="btn btn-xs btn-square btn-success me-1" onclick="exportPaymentReport()">
                                    <i class="fas fa-file-excel"></i> {{ __('table.payment_report') }}
                                </a>
                                <a class="btn btn-xs btn-square btn-primary" onclick="new bootstrap.Modal('#modal-add-payment').show()">
                                    {{ __('table.create') }}
                                </a>
                            </div>
                            <table class="table cus-table table-bordered table-striped mb-0" id="table-payments">
                                <thead>
                                    <tr>
                                        <th>{{ __('table.payment_code') }}</th>
                                        <th>{{ __('table.payment_date') }}</th>
                                        <th>{{ __('table.sched') }}</th>
                                        <th>{{ __('table.paid') }}</th>
                                        <th>{{ __('table.discount') }}</th>
                                        <th>{{ __('table.int_paid') }}</th>
                                        <th>{{ __('table.late_paid') }}</th>
                                        <th>{{ __('table.top_up_cap') }}</th>
                                        <th>{{ __('table.top_up_amt') }}</th>
                                        <th>{{ __('table.balance') }}</th>
                                        <th>{{ __('table.remark') }}</th>
                                        <th>{{ __('table.type') }}</th>
                                        <th>{{ __('table.loan_code') }}</th>
                                        @if(Auth::user()->role_id <= 3)
                                        <th>{{ __('table.actions') }}</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                
                        </section>
                    </div>
                </div>
            </div>
            
            <div class="tab-content">
                <div id="schedule" class="tab-pane">
                    <div class="col-lg-12">
                        <section class="card">
                            <div class="mb-3" style="text-align: right;">
                                <a class="btn btn-xs btn-square btn-primary" onclick="new bootstrap.Modal('#modal-add-schedule').show()">{{ __('table.create') }}</a>
                            </div>
                            <table class="table cus-table table-bordered table-striped mb-0" id="table-payment-schedules">
                                <thead>
                                    <tr>
                                        <th>{{ __('table.schedule_code') }}</th>
                                        <th>{{ __('table.due_date') }}</th>
                                        <th>{{ __('table.payment') }}</th>
                                        <th>{{ __('table.paid') }}</th>
                                        <th>{{ __('table.discount') }}</th>
                                        <th>{{ __('table.int') }}</th>
                                        <th>{{ __('table.int_paid') }}</th>
                                        <th>{{ __('table.late') }}</th>
                                        <th>{{ __('table.late_paid') }}</th>
                                        @if(Auth::user()->role_id <= 3)
                                        <th>{{ __('table.actions') }}</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </section>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@if($loan)
<div class="modal fade" id="modal-add-schedule" tabindex="-1" aria-labelledby="modalAddScheduleLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAddScheduleLabel">{{ __('table.add_schedule') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form-add-schedule" method="POST" action="{{ route('schedule.store') }}">
                    @csrf
                    <input type="hidden" name="loan_code" value="{{ $loan->loan_code }}">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="col-form-label">{{ __('table.due_date') }}</label>
                            <input type="date" class="form-control" name="due_date" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="col-form-label">{{ __('table.add_interest') }}</label>
                            <input type="number" class="form-control" name="interest_amount" value="0">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="col-form-label">{{ __('table.add_payment_(capital)') }}</label>
                            <input type="number" class="form-control" name="payment_amount" value="0">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="col-form-label">{{ __('table.add_late') }}</label>
                            <input type="number" class="form-control" name="late_amount" value="0">
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('table.cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('table.submit') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-update-schedule" tabindex="-1" aria-labelledby="modalUpdateScheduleLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalUpdateScheduleLabel">{{ __('table.update_schedule') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form-update-schedule" method="POST" action="{{ route('schedule.store') }}">
                    @csrf
                    <input type="hidden" name="schedule_id" id="update-schedule-id">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="col-form-label">{{ __('table.due_date') }}</label>
                            <input type="date" class="form-control" name="due_date" id="update-schedule-date" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="col-form-label">{{ __('table.interest_amount') }}</label>
                            <input type="number" class="form-control" name="interest_amount" id="update-schedule-interest">
                        </div>
                        <div class="col-md-6">
                            <label class="col-form-label">{{ __('table.interest_paid') }}</label>
                            <input type="number" class="form-control" name="interest_paid_amount" id="update-schedule-interest-paid">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="col-form-label">{{ __('table.payment/capital_amount') }}</label>
                            <input type="number" class="form-control" name="payment_amount" id="update-schedule-payment">
                        </div>
                        <div class="col-md-6">
                            <label class="col-form-label">{{ __('table.payment/capital_paid') }}</label>
                            <input type="number" class="form-control" name="paid_amount" id="update-schedule-paid">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="col-form-label">{{ __('table.payment/capital_discount') }}</label>
                            <input type="number" class="form-control" name="discount_amount" id="update-schedule-discount">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="col-form-label">{{ __('table.late_amount') }}</label>
                            <input type="number" class="form-control" name="late_amount" id="update-schedule-late">
                        </div>
                        <div class="col-md-6">
                            <label class="col-form-label">{{ __('table.late_paid') }}</label>
                            <input type="number" class="form-control" name="late_paid_amount" id="update-schedule-late-paid">
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('table.cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('table.submit') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-add-payment" tabindex="-1" aria-labelledby="modalAddPaymentLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAddPaymentLabel">{{ __('table.add_payment') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form-add-payment">
                    @csrf
                    <input type="hidden" name="loan_code" value="{{ $loan->loan_code }}">

                    {{-- Payment type selector --}}
                    <div class="col-md-12 mb-3">
                        <label class="col-form-label">{{ __('table.payment_type') }}</label>
                        <select class="form-control" id="add-payment-type" onchange="applyAddPaymentType(this.value)">
                            <option value="DEFAULT">Option</option>
                            <option value="CCM">Payment / CCM / Discount</option>
                            <option value="INTEREST">Pay SKIM A Interest</option>
                            <option value="LATE">Pay Late</option>
                            <option value="TOPUP">Top Up</option>
                        </select>
                    </div>

                    {{-- Schedule dropdown — OUTSIDE add-wrap-payment --}}
                    <div class="col-md-12 mb-3" id="add-wrap-schedule" style="display:none">
                        <label class="col-form-label">Schedule</label>
                        <select class="form-control" id="add-input-schedule" onchange="applyScheduleMultiplier(this.value)">
                            <option value="0">-- Select Schedule --</option>
                            @if(($loan->interest_group ?? '') === 'SKIM A')
                                @for ($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            @else
                                @for ($i = 1; $i <= $scheduleCount; $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            @endif
                        </select>
                    </div>

                    {{-- Payment / capital --}}
                    <div class="row mb-3" id="add-wrap-payment">
                        <div class="col-md-6">
                            <label class="col-form-label">{{ __('table.payment/capital_amount') }}</label>
                            <input type="number" class="form-control" id="add-input-payment-amount" name="payment_amount" value="0">
                            <p class="p-note">{{ $loan->interest_group ?? 'No SKIM' }} <br> {{ __('table.outstanding') }}: {{ $loan->balance ?? '0.00' }} &nbsp;&nbsp;&nbsp; {{ __('table.next_payment') }}: {{ $loan?->next_due_amount ?? '' }} <br> {{ __('table.date') }}: {{ now()->format('Y-n-j') }} &nbsp;&nbsp;&nbsp; {{ __('table.due_date') }}: {{ $loan?->next_due_date ?? '' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="col-form-label">{{ __('table.discount') }}</label>
                            <input type="number" class="form-control" name="discount_amount" value="0">
                        </div>
                    </div>

                    {{-- Top-up- capital --}}
                    <div class="row mb-3">
                        <div class="col-md-6" id="add-wrap-topup-capital">
                            <label class="col-form-label">{{ __('table.top_up_capital') }}</label>
                            <input type="number" class="form-control" id="add-input-topup-capital" name="top_up_capital" placeholder="5000.00" step="0.01" value="0">
                        </div>

                        {{-- Top-up --}}
                        <div class="col-md-6" id="add-wrap-topup">
                            <label class="col-form-label">{{ __('table.top_up_amount') }}</label>
                            <input type="number" class="form-control" id="add-input-topup" name="top_up" placeholder="5000.00" step="0.01" value="0">
                        </div>
                    </div>

                    {{-- Late --}}
                    <div class="col-md-12 mb-3" id="add-wrap-late">
                        <label class="col-form-label">{{ __('table.late_amount') }}</label>
                        <input type="number" class="form-control" id="add-input-payment-late" name="late_paid_amount" value="0">
                        <p class="p-note">{{ $loan->late_balance ?? '0.00' }}</p>
                    </div>

                    {{-- Interest --}}
                    <div class="col-md-12 mb-3" id="add-wrap-interest">
                        <label class="col-form-label">{{ __('table.interest_amount') }}</label>
                        <input type="number" class="form-control" id="add-input-payment-interest" name="interest_paid_amount" value="0">
                        <p class="p-note">{{ $loan->interest_balance ?? '0.00' }}</p>
                    </div>

                    {{-- Hidden collection type - auto from loan's interest_group --}}
                    <input type="hidden" name="collection_type" id="add-collection-type" value="{{ $loan->interest_group ?? 'SKIM A' }}">

                    {{-- Hidden payment method --}}
                    <input type="hidden" name="payment_method_id" id="add-payment-method-id" value="">

                    <div class="col-12">
                        <label class="col-form-label">{{ __('table.remark') }}</label>
                        <textarea class="form-control" name="remark" rows="3" placeholder="Enter remarks..."></textarea>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('table.cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('table.submit') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-update-payment" tabindex="-1" aria-labelledby="modalUpdatePaymentLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalUpdatePaymentLabel">{{ __('table.update_payment') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form-update-payment">
                    @csrf
                    <input type="hidden" name="payment_id" id="update-payment-id">

                    {{-- Payment type selector --}}
                    <div class="col-md-12 mb-3">
                        <label class="col-form-label">{{ __('table.payment_type') }}</label>
                        <select class="form-control" id="update-payment-type" onchange="applyUpdatePaymentType(this.value)">
                            <option value="CCM">Payment / CCM / Discount</option>
                            <option value="INTEREST">Pay SKIM A Interest</option>
                            <option value="LATE">Pay Late</option>
                            <option value="TOPUP">Top Up</option>
                        </select>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="col-form-label">{{ __('table.calendar') }}</label>
                        <select class="form-control" id="update-payment-switcher" onchange="switchPayment(this.value)">
                        </select>
                    </div>

                    {{-- CCM only --}}
                    <div class="row mb-3" id="update-wrap-payment">
                        <div class="col-md-6">
                            <label class="col-form-label">{{ __('table.payment/capital_amount') }}</label>
                            <input type="number" class="form-control" id="update-payment-paid" name="payment_amount">
                        </div>
                        <div class="col-md-6">
                            <label class="col-form-label">{{ __('table.discount') }}</label>
                            <input type="number" class="form-control" id="update-payment-discount" name="discount_amount">
                        </div>
                    </div>

                    {{-- Top-up- capital --}}
                    <div class="row mb-3">
                        <div class="col-md-6" id="update-wrap-topup-capital">
                            <label class="col-form-label">{{ __('table.top_up_capital') }}</label>
                            <input type="number" class="form-control" id="update-payment-topup-capital" name="top_up_capital" placeholder="5000.00" step="0.01" value="0">
                        </div>

                        {{-- Top-up --}}
                        <div class="col-md-6" id="update-wrap-topup">
                            <label class="col-form-label">{{ __('table.top_up_amount') }}</label>
                            <input type="number" class="form-control" id="update-payment-topup" name="top_up" placeholder="5000.00" step="0.01" value="0">
                        </div>
                    </div>

                    {{-- greyed for non-CCM --}}
                    <div class="col-md-12 mb-3" id="update-wrap-late">
                        <label class="col-form-label">{{ __('table.late_amount') }}</label>
                        <input type="number" class="form-control" id="update-payment-late" name="late_paid_amount" value="0">
                    </div>

                    {{-- active for INTEREST, greyed for CCM --}}
                    <div class="col-md-12 mb-3" id="update-wrap-interest">
                        <label class="col-form-label">{{ __('table.interest_amount') }}</label>
                        <input type="number" class="form-control" id="update-payment-interest" name="interest_paid_amount">
                    </div>

                    {{-- Hidden collection type --}}
                    <input type="hidden" name="collection_type" id="update-payment-collection" value="">

                    {{-- Hidden payment method --}}
                    <input type="hidden" name="payment_method_id" id="update_payment_method_id" value="">

                    <div class="col-12">
                        <label class="col-form-label">{{ __('table.remark') }}</label>
                        <textarea class="form-control" id="update-payment-remark" name="remark" rows="3" placeholder="Enter remarks..."></textarea>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('table.cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('table.submit') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@section('scripts')
<script>
    let table_schedule, table_payment;
    @if($loan)
    $(document).ready(function () {
        table_schedule = $('#table-payment-schedules').DataTable({
            "processing": true,
            "serverSide": true,
            "fixedHeader": false,
            "ajax": {
                "url": "{{ route('schedule.load_schedule',['loan_code'=>':loan_code']) }}".replace(':loan_code',"{{ $loan->loan_code }}"),
                "type": "GET"
            },
            "order": [
                [0, "asc"]
            ],
            "columns": [
                {
                    "data": "schedule_code"
                },
                {
                    "data": "due_date",
                    "render": function(data, type, row, meta) {
                        if (!data) return '-';
                        const parts = data.substring(0, 10).split('-');
                        return `${parts[2]}-${parts[1]}-${parts[0]}`;
                    }
                },
                {
                    "data": "payment_amount"
                },
                {
                    data: "paid_amount",
                    render: function(data) {
                        let value = parseFloat(data);

                        if (!isNaN(value) && value !== 0) {
                            return `<span style="font-weight:bold">${value.toFixed(2)}</span>`;
                        }

                        return data ? parseFloat(data).toFixed(2) : '-';
                    }
                },
                {
                    "data": "discount_amount",
                    "render": function(data) {
                        let value = parseFloat(data);

                        if (!isNaN(value)) {
                            return `<strong style="color:green">${value.toFixed(2)}</strong>`;
                        }

                        return `<strong style="color:green">0.00</strong>`;
                    }
                },
                {
                    "data": "interest_amount"
                },
                {
                    "data": "interest_paid_amount",
                    "render": function(data) {
                        let value = parseFloat(data);

                        if (!isNaN(value)) {
                            return `<strong>${value.toFixed(2)}</strong>`;
                        }

                        return '-';
                    }
                },
                {
                    "data": "late_amount",
                    "render": function(data) {
                        let value = parseFloat(data);

                        if (!isNaN(value)) {
                            return `<span style="color:orange">${value.toFixed(2)}</span>`;
                        }

                        return '-';
                    }
                },
                {
                    "data": "late_paid_amount",
                    "render": function(data) {
                        let value = parseFloat(data);

                        if (!isNaN(value)) {
                            return `<strong>${value.toFixed(2)}</strong>`;
                        }

                        return '-';
                    }
                },
                @if(Auth::user()->role_id <= 3)
                {
                    "data": null,
                    "render": function(data, type, row, meta) {
                        return`
                            <div class="cus-action-wrapper">
                                <a class="cus-action-icon info" title="Update Schedule" onclick="updateSchedule(${meta.row})"><i class="fas fa-edit"></i></a>
                                <a class="cus-action-icon danger" title="Delete Schedule" onclick="deleteSchedule(${meta.row})"><i class="fas fa-trash-alt"></i></a>
                            </div>
                        `;
                    }
                }
                @endif
            ]
        });

        table_payment = $('#table-payments').DataTable({
            "processing": true,
            "serverSide": true,
            "fixedHeader": false,
            "ordering":   true,
            "order": [[1, "asc"]],
            "columnDefs": [
                { "orderable": false, "targets": [2, 9, 12] },  // installment_calc, deducted_balance, action
                @if(Auth::user()->role_id <= 3)
                { "orderable": false, "targets": [13] },         // edit/delete action column
                @endif
            ],
            "ajax": {
                "url": "{{ route('payment.load_payment',['loan_code'=>':loan_code']) }}".replace(':loan_code',"{{ $loan->loan_code }}"),
                "type": "GET"
            },
            "columns": [
                {
                    "data": "payment_code"
                },
                {
                    "data": "created_at",
                    "render": function(data, type, row, meta) {
                        if (!data) return '-';
                        const parts = data.substring(0, 10).split('-');
                        return `${parts[2]}-${parts[1]}-${parts[0]}`;
                    }
                },
                {
                    "data": "installment_calc",
                    "render": function(data, type, row) {

                        if ((row.collection_type ?? '').trim().toUpperCase() === 'SKIM A') {
                            return 1;
                        }

                        return data ?? '-';
                    }
                },
                {
                    "data": "payment_amount"
                },
                {
                    "data": "discount_amount",
                    "render": function(data) {
                        let value = parseFloat(data);
                        if (isNaN(value) || value == 0) return '-';

                        return `<strong style="color:green">${value}</strong>`;
                    }
                },
                {
                    "data": "interest_paid_amount",
                    "render": function(data) {
                        let value = parseFloat(data);
                        if (isNaN(value) || value == 0) return '-';

                        return `<strong>${value}</strong>`;
                    }
                },
                {
                    "data": "late_paid_amount",
                    "render": function(data) {
                        let value = parseFloat(data);
                        if (isNaN(value) || value == 0) return '-';

                        return `<strong style="color:orange">${value}</strong>`;
                    }
                },
                {
                    "data": "top_up_capital",
                    "render": function(data) {
                        let value = parseFloat(data);

                        if (isNaN(value) || value == 0) {
                            return '-';
                        }

                        return `<strong style="color:red">${value}</strong>`;
                    }
                },
                {
                    "data": "top_up",
                    "render": function(data) {
                        let value = parseFloat(data);

                        if (isNaN(value) || value == 0) {
                            return '-';
                        }

                        return `<strong style="color:red">${value}</strong>`;
                    }
                },
                {
                    "data": "deducted_balance",
                    "render": function(data) {
                        if (data == null) return '-';

                        return parseFloat(data).toFixed(2);
                    }
                },
                {
                    "data": "remark",
                    "defaultContent": "-"
                },
                {
                    "data": "collection_type"
                },
                {
                    "data": null,
                    "render": function(data, type, row, meta) {
                        return `<a href="{{ route('loan.single_loan', ['loan_code' => ':loan_code']) }}" class="info" title="View Detail">${row.loan_code}</a>`.replace(':loan_code',row.loan_code);
                    }
                },
                @if(Auth::user()->role_id <= 3)
                {
                    "data": null,
                    "render": function(data, type, row, meta) {
                        return`
                            <div class="cus-action-wrapper">
                                <a class="cus-action-icon info" title="Update Payment" onclick="updatePayment(${meta.row})"><i class="fas fa-edit"></i></a>
                                <a class="cus-action-icon danger" title="Delete Payment" onclick="deletePayment(${meta.row})"><i class="fas fa-trash-alt"></i></a>
                            </div>
                        `;
                    }
                }
                @endif
            ]
        });
    });
    @endif
    let searchTimeout;
    const searchInput = document.getElementById('input-search');
    const dropdown = document.getElementById('loan-dropdown');

    searchInput.addEventListener('input', function() {
        const query = this.value;
        clearTimeout(searchTimeout);
        if (query.length < 3) {
            dropdown.style.display = 'none';
            return;
        }
        searchTimeout = setTimeout(() => {
            searchLoans(query);
        }, 500);
    });

    function searchLoans(query) {
        fetch(`{{ route('loan.search_loan') }}?search=${encodeURIComponent(query)}`, {
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(loans => {
            dropdown.innerHTML = '';
            if (loans.length === 0) {
                dropdown.innerHTML = '<div class="dropdown-item-text">No loan found</div>';
            } else {
                loans.forEach(loan => {
                    const item = document.createElement('a');
                    item.className = 'dropdown-item';
                    item.href = '#';
                    item.innerHTML = `<strong>${loan.loan_code}</strong>`;
                    item.addEventListener('click', function(e) {
                        e.preventDefault();
                        searchInput.value = loan.loan_code;
                        dropdown.style.display = 'none';
                        console.log(loan.interest_group);
                        setupPaymentMethod(loan);
                    });
                    dropdown.appendChild(item);
                });
            }   
            dropdown.style.display = 'block';
        })
        .catch(error => {
            dropdown.innerHTML = '<div class="dropdown-item-text text-danger">Search failed</div>';
            dropdown.style.display = 'block';
        });
    }

    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });

    function updateSchedule(rowIndex) {
        const data = table_schedule.row(rowIndex).data();
        document.getElementById('update-schedule-id').value = data.id;
        document.getElementById('update-schedule-date').value = data.due_date;
        document.getElementById('update-schedule-late').value = data.late_amount;
        document.getElementById('update-schedule-payment').value = data.payment_amount;
        document.getElementById('update-schedule-interest').value = data.interest_amount;
        document.getElementById('update-schedule-paid').value = data.paid_amount;
        document.getElementById('update-schedule-interest-paid').value = data.interest_paid_amount;
        document.getElementById('update-schedule-late-paid').value = data.late_paid_amount;
        document.getElementById('update-schedule-discount').value = data.discount_amount;
        $('#modal-update-schedule').modal('show');
    }

    function deleteSchedule(rowIndex) {
        const data = table_schedule.row(rowIndex).data();
        function submitDelete(){
            $.ajax({
                url: "{{ route('schedule.delete') }}",
                type: "POST",
                data: {schedule_id:data.id},
                headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}"},
                success: function (response) {
                    if(response.success == true){
                        setReloadSwal('success','',response.message);
                    }
                    else{
                        setDefaultSwal('error','',response.message);
                    }
                },
                error: function (xhr) {
                    setDefaultSwal('error','','There is something wrong, please try again.');
                }
            });
        }
        setConfirmationSwal(
            "Warning",
            "This action will affect the entire loan and cannot be undone. Proceed?",
            'Process',
            'Cancel'
        ).then((result) => {
            if (result.isConfirmed) {
                submitDelete();
            }
        });
    }

    function updatePayment(rowIndex) {
        const data = table_payment.row(rowIndex).data();

        // populate switcher from already-loaded table data
        const switcher = $('#update-payment-switcher');
        switcher.empty();
        table_payment.rows().every(function() {
            const row  = this.data();
            const date = row.created_at ? row.created_at.substring(0, 10) : '-';
            switcher.append(`<option value="${this.index()}" ${this.index() == rowIndex ? 'selected' : ''}>${row.payment_code} — ${date}</option>`);
        });

        document.getElementById('update-payment-id').value         = data.id;
        document.getElementById('update-payment-collection').value = data.collection_type;
        document.getElementById('update-payment-remark').value     = data.remark;

        // Detect type from existing record
        let type = 'CCM';
        if (data.top_up > 0 || data.top_up_capital > 0)                        type = 'TOPUP';
        else if (data.interest_paid_amount > 0 && data.payment_amount == 0)    type = 'INTEREST';
        else if (data.discount_amount > 0 && data.payment_amount == 0)         type = 'DISCOUNT';
        else if (data.late_paid_amount > 0 && data.payment_amount == 0)        type = 'LATE';

        // Hide options based on collection type
        if (data.collection_type === 'SKIM B') {
            $('#update-payment-type option[value="TOPUP"]').hide();
            $('#update-payment-type option[value="INTEREST"]').hide();
            $('#update-payment-type option[value="LATE"]').show();
            if (type === 'TOPUP' || type === 'INTEREST') {
                type = 'CCM';
            }
        } else if (data.collection_type === 'SKIM A') {
            $('#update-payment-type option[value="LATE"]').hide();
            $('#update-payment-type option[value="TOPUP"]').show();
            $('#update-payment-type option[value="INTEREST"]').show();
            if (type === 'LATE') {
                type = 'CCM';
            }
        } else {
            $('#update-payment-type option[value="TOPUP"]').show();
            $('#update-payment-type option[value="INTEREST"]').show();
            $('#update-payment-type option[value="LATE"]').show();
        }

        document.getElementById('update-payment-type').value = type;

        // Apply type first (locks/unlocks fields)
        applyUpdatePaymentType(type);

        // Fill values AFTER applyUpdatePaymentType so they don't get cleared
        document.getElementById('update-payment-paid').value          = data.payment_amount       ?? '';
        document.getElementById('update-payment-interest').value      = data.interest_paid_amount ?? '';
        document.getElementById('update-payment-late').value          = data.late_paid_amount     ?? '';
        document.getElementById('update-payment-discount').value      = data.discount_amount      ?? '';
        document.getElementById('update-payment-topup').value         = data.top_up               ?? '';
        document.getElementById('update-payment-topup-capital').value = data.top_up_capital       ?? '';

        // Pre-select current schedule
        if (type === 'CCM') {
            const currentSchedules = loanFirstPayment > 0
                ? Math.round((data.payment_amount ?? 0) / loanFirstPayment)
                : 0;
            const loanInterestAmount = {{ $loan->next_due_amount ?? 0 }};
            $('#update-input-schedule').val(currentSchedules);
        } else {
            $('#update-input-schedule').val('0');
        }

        setupUpdatePaymentMethod(data.company_code, data.payment_method_id);
        $('#modal-update-payment').modal('show');
    }

    function switchPayment(rowIndex) {
        const data = table_payment.row(rowIndex).data();
        if (!data) return;

        document.getElementById('update-payment-id').value         = data.id;
        document.getElementById('update-payment-collection').value = data.collection_type;
        document.getElementById('update-payment-remark').value     = data.remark ?? '';

        let type = 'CCM';
        if (data.top_up > 0 || data.top_up_capital > 0)                        type = 'TOPUP';
        else if (data.interest_paid_amount > 0 && data.payment_amount == 0)    type = 'INTEREST';
        else if (data.discount_amount > 0 && data.payment_amount == 0)         type = 'DISCOUNT';
        else if (data.late_paid_amount > 0 && data.payment_amount == 0)        type = 'LATE';

        // Hide options based on collection type
        if (data.collection_type === 'SKIM B') {
            $('#update-payment-type option[value="TOPUP"]').hide();
            $('#update-payment-type option[value="INTEREST"]').hide();
            $('#update-payment-type option[value="LATE"]').show();
            if (type === 'TOPUP' || type === 'INTEREST') {
                type = 'CCM';
            }
        } else if (data.collection_type === 'SKIM A') {
            $('#update-payment-type option[value="LATE"]').hide();
            $('#update-payment-type option[value="TOPUP"]').show();
            $('#update-payment-type option[value="INTEREST"]').show();
            if (type === 'LATE') {
                type = 'CCM';
            }
        } else {
            $('#update-payment-type option[value="TOPUP"]').show();
            $('#update-payment-type option[value="INTEREST"]').show();
            $('#update-payment-type option[value="LATE"]').show();
        }

        document.getElementById('update-payment-type').value = type;
        applyUpdatePaymentType(type);

        document.getElementById('update-payment-paid').value          = data.payment_amount       ?? '';
        document.getElementById('update-payment-interest').value      = data.interest_paid_amount ?? '';
        document.getElementById('update-payment-late').value          = data.late_paid_amount     ?? '';
        document.getElementById('update-payment-discount').value      = data.discount_amount      ?? '';
        document.getElementById('update-payment-topup').value         = data.top_up               ?? '';
        document.getElementById('update-payment-topup-capital').value = data.top_up_capital       ?? '';

        // Pre-select current schedule
        if (type === 'CCM') {
            const currentSchedules = loanFirstPayment > 0
                ? Math.round((data.payment_amount ?? 0) / loanFirstPayment)
                : 0;
            $('#update-input-schedule').val(currentSchedules);
        } else {
            $('#update-input-schedule').val('0');
        }

        setupUpdatePaymentMethod(data.company_code, data.payment_method_id);
    }

    @if($loan?->company->company_code)
    document.addEventListener('DOMContentLoaded', function () {
        setupPaymentMethod({
            company_code: "{{ $loan?->company->company_code }}",
            interest_group: "{{ $loan?->interest_group ?? 'SKIM A' }}"
        });
    });
    @endif

    const GREY = 'opacity:0.45; pointer-events:none; user-select:none;';

    function applyUpdatePaymentType(type) {
        const wrapPayment      = document.getElementById('update-wrap-payment');
        const wrapLate         = document.getElementById('update-wrap-late');
        const wrapInterest     = document.getElementById('update-wrap-interest');
        const wrapTopup        = document.getElementById('update-wrap-topup');
        const wrapTopupCapital = document.getElementById('update-wrap-topup-capital');

        const inputPaid         = document.getElementById('update-payment-paid');
        const inputDiscount     = document.getElementById('update-payment-discount');
        const inputLate         = document.getElementById('update-payment-late');
        const inputInterest     = document.getElementById('update-payment-interest');
        const inputTopup        = document.getElementById('update-payment-topup');
        const inputTopupCapital = document.getElementById('update-payment-topup-capital');

        // Reset — grey everything first
        [wrapPayment, wrapLate, wrapInterest, wrapTopup, wrapTopupCapital].forEach(w => w.setAttribute('style', GREY));
        [inputPaid, inputDiscount, inputLate, inputInterest, inputTopup, inputTopupCapital].forEach(i => {
            i.disabled = true;
            i.required = false;
            i.value    = '';
        });

        if (type === 'CCM') {
            wrapPayment.removeAttribute('style');
            inputPaid.disabled    = false;
            inputPaid.required    = true;
            inputDiscount.disabled = false;

        } else if (type === 'INTEREST') {
            wrapInterest.removeAttribute('style');
            inputInterest.disabled = false;
            inputInterest.required = true;

        } else if (type === 'LATE') {
            wrapLate.removeAttribute('style');
            inputLate.disabled = false;
            inputLate.required = true;

        } else if (type === 'TOPUP') {
            wrapTopup.removeAttribute('style');
            wrapTopupCapital.removeAttribute('style');
            inputTopup.disabled        = false;
            inputTopup.required        = false;
            inputTopupCapital.disabled = false;
            inputTopupCapital.required = false;
        }
    }

    const GREY_STYLE_ADD = 'opacity:0.45; pointer-events:none; user-select:none;';

    const loanFirstPayment   = {{ $loan->first_payment ?? 0 }};
    const loanInterest       = {{ $loan->interest ?? 0 }};
    const loanInterestPaid   = {{ $loan->interest_paid ?? 0 }};
    const interestRemaining  = loanInterest - loanInterestPaid;
    const loanInterestAmount = {{ $loan->loan_amount/$loan->interest_rate ?? 0 }};

    function applyScheduleMultiplier(schedule) {
        const multiplier     = parseInt(schedule) || 0;
        const collectionType = document.getElementById('add-collection-type').value ?? '';
        const type           = document.getElementById('add-payment-type').value;

        if (multiplier <= 0) {
            if (type === 'INTEREST') {
                $('#add-input-payment-interest').val('0');
            } else {
                $('#add-input-payment-amount').val('0');
            }
            return;
        }

        if (collectionType === 'SKIM A' && type === 'INTEREST') {
            // For SKIM A interest, multiply by interest installment amount
            $('#add-input-payment-interest').val((loanInterestAmount * multiplier).toFixed(2));
        } else {
            // For SKIM B CCM, multiply by payment installment
            $('#add-input-payment-amount').val((loanFirstPayment * multiplier).toFixed(2));
        }
    }

    function applyAddPaymentType(type) {
        const wrapPayment      = document.getElementById('add-wrap-payment');
        const wrapLate         = document.getElementById('add-wrap-late');
        const wrapInterest     = document.getElementById('add-wrap-interest');
        const wrapTopupCapital = document.getElementById('add-wrap-topup-capital');
        const wrapTopup        = document.getElementById('add-wrap-topup');
        const wrapSchedule     = document.getElementById('add-wrap-schedule');

        const inputPayment      = document.getElementById('add-input-payment-amount');
        const inputLate         = document.getElementById('add-input-payment-late');
        const inputInterest     = document.getElementById('add-input-payment-interest');
        const inputTopupCapital = document.getElementById('add-input-topup-capital');
        const inputTopup        = document.getElementById('add-input-topup');
        const inputDiscount     = document.querySelector('#add-wrap-payment [name="discount_amount"]');
        const inputSchedule     = document.getElementById('add-input-schedule');

        const collectionType = document.getElementById('add-collection-type').value ?? '';
        const isSkimA = collectionType === 'SKIM A';
        const isSkimB = collectionType === 'SKIM B';

        // Reset
        [wrapPayment, wrapLate, wrapInterest, wrapTopup, wrapTopupCapital].forEach(w => w.setAttribute('style', GREY_STYLE_ADD));
        [inputPayment, inputLate, inputInterest, inputTopup, inputTopupCapital, inputDiscount].forEach(i => {
            i.disabled = true;
            i.required = false;
            i.value = 0;
        });
        inputSchedule.value = '0';

        if (type === 'CCM') {
            wrapPayment.removeAttribute('style');
            inputPayment.disabled = false;
            inputPayment.required = true;
            inputDiscount.disabled = false;
            wrapSchedule.style.display = isSkimB ? 'block' : 'none';

        } else if (type === 'INTEREST') {
            wrapInterest.removeAttribute('style');
            inputInterest.disabled = false;
            inputInterest.required = true;
            wrapSchedule.style.display = 'block'; // always show for INTEREST regardless of skim

        } else if (type === 'LATE') {
            wrapLate.removeAttribute('style');
            inputLate.disabled = false;
            inputLate.required = true;
            wrapSchedule.style.display = 'none';

        } else if (type === 'TOPUP') {
            wrapTopup.removeAttribute('style');
            wrapTopupCapital.removeAttribute('style');
            inputTopup.disabled = false;
            inputTopup.required = false;
            inputTopupCapital.disabled = false;
            inputTopupCapital.required = false;
            wrapSchedule.style.display = 'none';

        } else {
            wrapSchedule.style.display = 'none';
        }
    }

    // init on page load
    applyAddPaymentType('DEFAULT');

    function deletePayment(rowIndex) {
        const data = table_payment.row(rowIndex).data();
        function submitDelete(){
            $.ajax({
                url: "{{ route('payment.delete') }}",
                type: "POST",
                data: {payment_id:data.id},
                headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}"},
                success: function (response) {
                    if(response.success == true){
                        setReloadSwal('success','',response.message);
                    }
                    else{
                        setDefaultSwal('error','',response.message);
                    }
                },
                error: function (xhr) {
                    setDefaultSwal('error','','There is something wrong, please try again.');
                }
            });
        }
        setConfirmationSwal(
            "Warning",
            "This action will affect the entire loan and cannot be undone. Proceed?",
            'Process',
            'Cancel'
        ).then((result) => {
            if (result.isConfirmed) {
                submitDelete();
            }
        });
    }

    $('#form-add-schedule').on('submit', function (e) {
        e.preventDefault();
        let form = $(this);
        let formData = new FormData(this);
        $.ajax({
            url: "{{ route('schedule.store') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}"},
            success: function (response) {
                if(response.success == true){
                    setReloadSwal('success','',response.message);
                }
                else{
                    setDefaultSwal('error','',response.message);
                }
            },
            error: function (xhr) {
                setDefaultSwal('There is something wrong, please try again.');
            }
        });
    });

    $('#form-update-schedule').on('submit', function (e) {
        e.preventDefault();
        let form = $(this);
        let formData = new FormData(this);
        $.ajax({
            url: "{{ route('schedule.update') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}"},
            success: function (response) {
                if(response.success == true){
                    setReloadSwal('success','',response.message);
                }
                else{
                    setDefaultSwal('error','',response.message);
                }
            },
            error: function (xhr) {
                setDefaultSwal('error','','There is something wrong, please try again.');
            }
        });
    });

    $('#form-add-payment').on('submit', function (e) {
        e.preventDefault();
        let form = $(this);
        let formData = new FormData(this);
        $.ajax({
            url:  "{{ route('payment.store') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}"},
            success: function (response) {
                if(response.success == true){
                    setReloadSwal('success','',response.message);
                }
                else{
                    setDefaultSwal('error','',response.message);
                }
            },
            error: function (xhr) {
                setDefaultSwal('There is something wrong, please try again.');
            }
        });
    });

    $('#form-update-payment').on('submit', function (e) {
        e.preventDefault();
        let form = $(this);
        let formData = new FormData(this);
        $.ajax({
            url: "{{ route('payment.update') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            header: { 'X-CSRF-TOKEN': "{{ csrf_token() }}"},
            success: function (response) {
                if(response.success == true){
                    setReloadSwal('success','',response.message);
                }
                else{
                    setDefaultSwal('error','',response.message);
                }
            },
            error: function (xhr) {
                setDefaultSwal('error','','There is something wrong, please try again.');
            }
        });
    });

    @if($loan)
    fetch(`{{ route('loan.fetch_profit',['loan_code'=>$loan->loan_code]) }}`, {
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('total-profit').innerHTML = data.total_profit ?? '0.00';
    })
    .catch(error => {
        console.error('Search failed:', error);
    });
    @endif

    function setupPaymentMethod(loan) {
        if (loan) {
            fetch(`{{ route('payment_method.search_payment_methods') }}?company_code=${encodeURIComponent(loan.company_code)}`, {
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            })
            .then(response => response.json())
            .then(methods => {
                if (methods.length > 0) {
                    document.getElementById('add-payment-method-id').value = methods[0].id;
                } else {
                    document.getElementById('add-payment-method-id').value = '';
                }
            })
            .catch(error => {
                document.getElementById('add-payment-method-id').value = '';
            });

            // Auto set collection type from loan's interest_group
            document.getElementById('add-collection-type').value = loan.interest_group ?? 'SKIM A';
        }
    }

    function setupUpdatePaymentMethod(x, y) {
        if (x) {
            fetch(`{{ route('payment_method.search_payment_methods') }}?company_code=${encodeURIComponent(x)}`, {
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            })
            .then(response => response.json())
            .then(methods => {
                if (methods.length > 0) {
                    let matched = methods.find(m => m.id == y);
                    document.getElementById('update_payment_method_id').value = matched ? matched.id : methods[0].id;
                } else {
                    document.getElementById('update_payment_method_id').value = '';
                }
            })
            .catch(error => {
                document.getElementById('update_payment_method_id').value = '';
            });
        } else {
            document.getElementById('update_payment_method_id').value = '';
        }
    }

    document.getElementById('loan-status').addEventListener('change', function() {
        $.ajax({
            url: "{{ route('loan.update_status', $loan->loan_code) }}",
            type: "POST",
            data: { status: this.value },
            headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
            success: function() {
                location.reload();
            },
            error: function(xhr) {
                console.log(xhr.responseText); // check browser console for actual error
                alert('Failed to update status');
            }
        });
    });

    function activateTabFromHash() {
        const hash = window.location.hash || '#overview';
        const validTabs = ['#overview', '#loan', '#schedule', '#payment'];
        const target = validTabs.includes(hash) ? hash : '#overview';

        $('.nav-tabs li').removeClass('active');
        $('.nav-tabs .nav-link').removeClass('active');
        $('.tab-pane').removeClass('active');

        const $link = $(`.nav-tabs .nav-link[data-bs-target="${target}"]`);
        $link.addClass('active');
        $link.closest('li').addClass('active');
        $(target).addClass('active');

        setTimeout(function() {
            if (target === '#payment' && typeof table_payment !== 'undefined' && table_payment) {
                table_payment.columns.adjust().draw();
            }
            if (target === '#schedule' && typeof table_schedule !== 'undefined' && table_schedule) {
                table_schedule.columns.adjust().draw();
            }
        }, 50);
    }

    // On page load
    activateTabFromHash();

    // On tab click, update URL hash
    $('.nav-tabs .nav-link').on('click', function() {
        const target = $(this).attr('data-bs-target');
        history.pushState(null, null, target);
    });

    // Browser back/forward
    window.addEventListener('popstate', function() {
        activateTabFromHash();
    });

    function calculateInterest() {
        let data = {
            loan_amount:    document.getElementById('loan-amount')?.value    ?? 0,
            interest_group: document.getElementById('interest-group')?.value ?? 'SKIM A',
            loan_term:      document.getElementById('loan-term')?.value      ?? null, // ✅ null for SKIM A
            first_payment:  document.getElementById('first-payment')?.value  ?? null,
            last_payment:   document.getElementById('last-payment')?.value   ?? null,
            installment:    document.getElementById('installment')?.value    ?? null,
            _token: '{{ csrf_token() }}'
        };

        fetch("{{ route('loan.calculate_interest') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(r => {
            if (r.success === false && r.errors) {
                let errorMsg = '';
                for (let field in r.errors) {
                    errorMsg += r.errors[field].join(', ') + '\n';
                }
                alert('Validation Errors:\n' + errorMsg);
            } else if (r.success === true) {
                document.getElementById('interest-rate').value = r.data.amount;
            } else {
                console.error('Unexpected response:', r);
            }
        })
        .catch(err => {
            alert('Network error occurred');
        });
    }
</script>

<script>
function calculateCapital() {
    let loanAmount = parseFloat(document.getElementById('loan-amount').value) || 0;
    let processingFee = parseFloat(document.querySelector('input[name="processing_fee"]').value) || 0;

    let capital = loanAmount - processingFee;

    document.getElementById('capital').value = capital.toFixed(2);
}

// trigger on input change
document.addEventListener('DOMContentLoaded', function () {
    const loanAmount = document.getElementById('loan-amount');
    const processingFee = document.querySelector('input[name="processing_fee"]');

    loanAmount.addEventListener('input', calculateCapital);
    processingFee.addEventListener('input', calculateCapital);
});
</script>

<script>
function exportPaymentReport() {
    const table = $('#table-payments').DataTable();
    const rows = table.rows({ search: 'applied' }).data().toArray();

    const headers = [
        '{{ __("table.payment_code") }}',
        '{{ __("table.payment_date") }}',
        '{{ __("table.sched") }}',
        '{{ __("table.paid") }}',
        '{{ __("table.discount") }}',
        '{{ __("table.int_paid") }}',
        '{{ __("table.late_paid") }}',
        '{{ __("table.top_up_cap") }}',
        '{{ __("table.top_up_amt") }}',
        '{{ __("table.balance") }}',
        '{{ __("table.remark") }}',
        '{{ __("table.type") }}',
        '{{ __("table.loan_code") }}',
    ];

    let csv = '\uFEFF' + headers.join(',') + '\n';
    rows.forEach(row => {
        const cols = Array.isArray(row) ? row : Object.values(row);
        const data = cols.slice(0, headers.length).map(val => {
            const clean = String(val ?? '').replace(/<[^>]+>/g, '').replace(/"/g, '""');
            return `"${clean}"`;
        });
        csv += data.join(',') + '\n';
    });

    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const url  = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href     = url;
    link.download = 'payment_report_{{ now()->format("Ymd_His") }}.csv';
    link.click();
    URL.revokeObjectURL(url);
}
</script>

<script>
function updatePaymentTypeOptions() {
    let type = ($('#add-collection-type').val() || '').toUpperCase();

    let $select = $('#add-payment-type');

    // show all first
    $select.find('option').show();

    if (type === 'SKIM B') {
        $select.find('option[value="TOPUP"]').hide();
        $select.find('option[value="INTEREST"]').hide();
    }

    if (type === 'SKIM A') {
        $select.find('option[value="LATE"]').hide();
    }

    // if current selected is hidden, reset to DEFAULT
    if ($select.find('option:selected').is(':hidden')) {
        $select.val('DEFAULT');
    }

    applyAddPaymentType($select.val());
}


// run when modal opens
$('#modal-add-payment').on('shown.bs.modal', function () {
    updatePaymentTypeOptions();
});
</script>

@endsection
