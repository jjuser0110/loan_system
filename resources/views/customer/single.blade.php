@extends('layouts.app')
<style>
    .loan-clickable:hover {
        color:blue;
    }

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
    <h2>{{ __('table.customer_details') }}</h2> 
</header>
@include('layouts.flash-message')
<div class="row">
    <section class="card">
        <form class="theme-form mega-form" action="{{ route('customer.single_customer') }}" method="get">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <label class="col-form-label">{{ __('table.customer') }} NRIC</label>
                        <div id="search-wrapper">
                            <input type="text" id="input-search" class="form-control" name="nric_number" value="{{ request('nric_number') }}">
                            <button type="submit" class="btn btn-primary" id="btn-search"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>
</div>
<div class="row" style="padding-top:0;"> 
    <div class="col-sm-12 col-xl-12">
        <div id="loan" class="tab-pane-active">
            <div class="col-lg-12">
                <section class="card cus-display-only">
                    @if($success && isset($customer))
                    <form class="theme-form mega-form">
                        @csrf
                        <div class="card-body">
                            <!-- Customer Info -->
                            <div class="row">
                                <div class="col-md-6">
                                    <h4 class="cus-header">{{ __('table.customer') }}</h4>
                                    <div class="row">
                                        <div class="col-xs-12 col-lg-4 mb-3">
                                            <label class="col-form-label">{{ __('table.customer_code') }}</label>
                                            <input type="text" class="form-control" value="{{ $customer->customer_code }}" disabled>
                                        </div>
                                        <div class="col-xs-12 col-lg-4 mb-3">
                                            <label class="col-form-label">{{ __('table.customer_name') }}</label>
                                            <input type="text" class="form-control" value="{{ $customer->customer_name }}" disabled>
                                        </div>
                                        <div class="col-xs-12 col-lg-4 mb-3">
                                            <label class="col-form-label">NRIC {{ __('table.number') }}</label>
                                            <input type="text" class="form-control" value="{{ $customer->nric_number }}" disabled>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <h4 class="cus-header">{{ __('table.company') }}</h4>
                                    <div class="row">
                                        <div class="col-xs-12 col-lg-4 mb-3">
                                            <label class="col-form-label">{{ __('table.company_code') }}</label>
                                            <input type="text" class="form-control" value="{{ $customer->company->company_code ?? '' }}" disabled>
                                        </div>
                                        <div class="col-xs-12 col-lg-4 mb-3">
                                            <label class="col-form-label">{{ __('table.company_name') }}</label>
                                            <input type="text" class="form-control" value="{{ $customer->company->company_name ?? '' }}" disabled>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Loans -->
                            <h4 class="cus-header">{{ __('table.loans') }}</h4>
                            @foreach($loans as $loan)
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-sm-6 col-lg-3 mb-3">
                                                <label class="col-form-label">{{ __('table.loan_code') }}</label>
                                                <input type="text" class="form-control loan-clickable" value="{{ $loan->loan_code }}" readonly
                                                    data-url="{{ url('loan/single_loan/'.$loan->loan_code) }}">
                                            </div>
                                            <div class="col-sm-6 col-lg-3 mb-3">
                                                <label class="col-form-label">{{ __('table.loan_amount') }}</label>
                                                <input type="text" class="form-control" value="{{ $loan->loan_amount }}" disabled>
                                            </div>
                                            <div class="col-sm-6 col-lg-3 mb-3">
                                                <label class="col-form-label">{{ __('table.status') }}</label>
                                                <input type="text" class="form-control" value="{{ $loan->status }}" disabled>
                                            </div>
                                            <div class="col-sm-6 col-lg-3 mb-3">
                                                <label class="col-form-label">{{ __('table.created_at') }}</label>
                                                <input type="text" class="form-control" value="{{ $loan->created_at }}" disabled>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                        </div>
                    </form>
                    @elseif(!$success)
                        <p style="text-align:center">{{ $error }}</p>
                    @endif
                </section>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.loan-clickable').forEach(function(input) {
            input.style.cursor = 'pointer';
            input.addEventListener('click', function() {
                window.location.href = input.dataset.url;
            });
        });
    });
</script>

@endsection