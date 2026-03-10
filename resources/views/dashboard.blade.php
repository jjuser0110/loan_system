@extends('layouts.app')

@section('css')
<style>
    .cus-card{
        height: 90px;
        display: flex;
        justify-content: center;
        gap: 5px;
        flex-direction: column
    }

    .cus-card .title{
        margin: 0;
        font-size: 0.9rem;
        color: #333;
        font-weight: 500;
        line-height: 1.5;
    }

    .cus-card .amount{
        margin-right: .2em;
        font-weight: 600;
        color: #333;
        vertical-align: middle;
        font-size: 1.4rem;
    }

    .dataTables_filter, .dataTables_length{
        display: none;
    }

    .table-header{
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px
    }

    .table-header h4{
        font-weight: 600;
        font-size: 16px;
        margin: 0
    }

    .table-header a{
       font-size: 12px;
       text-decoration: underline
    }

    .pagination a{
        font-size: 11px;
        padding: 5px;
        line-height: 1;
    }

    #table-overdue_info, #table-incoming_info{
        display:none
    }
</style>
@endsection

@section('content')
<header class="page-header">
    <h2>{{ __('table.dashboard') }}</h2>
</header>

@include('layouts.flash-message')
<!-- start: page -->
<div class="row">
    <div class="col-lg-12">
        <div class="row mb-3">
            <div class="col-xxl-4 col-xl-6 col-lg-6">
                <section class="card card-featured-left card-featured-primary mb-3">
                    <div class="card-body">
                        <div class="widget-summary">
                            <div class="widget-summary-col widget-summary-col-icon">
                                <div class="summary-icon bg-primary">
                                    <i class="fas fa-dollar-sign"></i>
                                </div>
                            </div>
                            <div class="cus-card">
                                <h4 class="title">{{ __('table.total_stock_a') }}</h4>
                                <div class="info">
                                    <strong class="amount">${{ $companies->total_stocka ?? 0.00 }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            <div class="col-xxl-4 col-xl-6 col-lg-6">
                <section class="card card-featured-left card-featured-secondary mb-3">
                    <div class="card-body">
                        <div class="widget-summary">
                            <div class="widget-summary-col widget-summary-col-icon">
                                <div class="summary-icon bg-secondary">
                                    <i class="fas fa-dollar-sign"></i>
                                </div>
                            </div>
                            <div class="cus-card">
                                <h4 class="title">{{ __('table.total_stock_b') }}</h4>
                                <div class="info">
                                    <strong class="amount">${{ $companies->total_stockb ?? 0.00 }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            <div class="col-xxl-4 col-xl-6 col-lg-6">
                <section class="card card-featured-left card-featured-tertiary mb-3">
                    <div class="card-body">
                        <div class="widget-summary">
                            <div class="widget-summary-col widget-summary-col-icon">
                                <div class="summary-icon bg-tertiary">
                                    <i class="fas fa-dollar-sign"></i>
                                </div>
                            </div>
                            <div class="cus-card">
                                <h4 class="title">{{ __('table.total_stock_bb') }}</h4>
                                <div class="info">
                                    <strong class="amount">${{ $companies->total_stockbb ?? 0.00 }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            <div class="col-xxl-4 col-xl-6 col-lg-6">
                <section class="card card-featured-left card-featured-quaternary mb-3">
                    <div class="card-body">
                        <div class="widget-summary">
                            <div class="widget-summary-col widget-summary-col-icon">
                                <div class="summary-icon bg-secondary">
                                    <i class="fas fa-dollar-sign"></i>
                                </div>
                            </div>
                            <div class="cus-card">
                                <h4 class="title">{{ __('table.total_amount') }}</h4>
                                <div class="info">
                                    <strong class="amount">${{ $companies->amount ?? 0.00}}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <div class="col-xxl-4 col-xl-6 col-lg-6">
                <section class="card card-featured-left card-featured-quaternary mb-3">
                    <div class="card-body">
                        <div class="widget-summary">
                            <div class="widget-summary-col widget-summary-col-icon">
                                <div class="summary-icon bg-tertiary">
                                    <i class="fas fa-dollar-sign"></i>
                                </div>
                            </div>
                            <div class="cus-card">
                                <h4 class="title">{{ __('table.total_loan_amount') }}</h4>
                                <div class="info">
                                    <strong class="amount" id="total-loan-amount">{{ __('table.loading') }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <div class="col-xxl-4 col-xl-6 col-lg-6">
                <section class="card card-featured-left card-featured-quaternary mb-3">
                    <div class="card-body">
                        <div class="widget-summary">
                            <div class="widget-summary-col widget-summary-col-icon">
                                <div class="summary-icon bg-primary">
                                    <i class="fas fa-dollar-sign"></i>
                                </div>
                            </div>
                            <div class="cus-card">
                                <h4 class="title">{{ __('table.total_capital') }}</h4>
                                <div class="info">
                                    <strong class="amount" id="total-capital-amount">{{ __('table.loading') }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <div class="col-xxl-4 col-xl-6 col-lg-6">
                <section class="card card-featured-left card-featured-quaternary mb-3">
                    <div class="card-body">
                        <div class="widget-summary">
                            <div class="widget-summary-col widget-summary-col-icon">
                                <div class="summary-icon bg-tertiary">
                                    <i class="fas fa-dollar-sign"></i>
                                </div>
                            </div>
                            <div class="cus-card">
                                <h4 class="title">{{ __('table.total_expenses') }}</h4>
                                <div class="info">
                                    <strong class="amount" id="total-expenses-amount">{{ __('table.loading') }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <div class="col-xxl-4 col-xl-6 col-lg-6">
                <section class="card card-featured-left card-featured-quaternary mb-3">
                    <div class="card-body">
                        <div class="widget-summary">
                            <div class="widget-summary-col widget-summary-col-icon">
                                <div class="summary-icon bg-primary">
                                    <i class="fas fa-dollar-sign"></i>
                                </div>
                            </div>
                            <div class="cus-card">
                                <h4 class="title">{{ __('table.total_outstanding') }}</h4>
                                <div class="info">
                                    <strong class="amount" id="total-outstanding-amount">{{ __('table.loading') }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <div class="col-xxl-4 col-xl-6 col-lg-6">
                <section class="card card-featured-left card-featured-quaternary mb-3">
                    <div class="card-body">
                        <div class="widget-summary">
                            <div class="widget-summary-col widget-summary-col-icon">
                                <div class="summary-icon bg-secondary">
                                    <i class="fas fa-dollar-sign"></i>
                                </div>
                            </div>
                            <div class="cus-card">
                                <h4 class="title">{{ __('table.total_profit') }}</h4>
                                <div class="info">
                                    <strong class="amount" id="total-profit-amount">{{ __('table.loading') }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <div class="col-xxl-4 col-xl-6 col-lg-6">
                <section class="card card-featured-left card-featured-quaternary mb-3">
                    <div class="card-body">
                        <div class="widget-summary">
                            <div class="widget-summary-col widget-summary-col-icon">
                                <div class="summary-icon bg-quaternary">
                                    <i class="fas fa-dollar-sign"></i>
                                </div>
                            </div>
                            <div class="cus-card">
                                <h4 class="title">{{ __('table.total_account_amount') }}</h4>
                                <div class="info">
                                    <strong class="amount" id="total-payment-method-amount">{{ __('table.loading') }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>

    
    <div class="row" class="col-lg-9">
        <div class="col-lg-9">
            <div class="col-lg-12 mb-3">
                <section class="card">
                
                    <div class="card-body">
                        <div class="table-header" >
                        <h4>{{ __('table.companies') }}</h4>
                        <a href="{{route('company.index')}}">{{ __('table.go_to_company') }}</a>
                    </div>
                        <table class="table table-bordered table-striped mb-0" id="table-company">
                            <thead>
                                <tr>
                                    <th>{{ __('table.company_(code)') }}</th>
                                    <th>{{ __('table.stock_a') }}</th>
                                    <th>{{ __('table.stock_b') }}</th>
                                    <th>{{ __('table.stock_bb') }}</th>
                                    <th>{{ __('table.amount') }}</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </section>
            </div>

            <div class="col-lg-12 mb-3">
                <section class="card">
                    <div class="card-body">
                        <div class="table-header" >
                        <h4>{{ __('table.bank_account') }}</h4>
                        <a href="{{route('payment_method.index')}}">{{ __('table.go_to_payment_method') }}</a>
                    </div>
                        <table class="table table-bordered table-striped mb-0" id="table-payment-method">
                            <thead>
                                <tr>
                                    <th>{{ __('table.bank') }}</th>
                                    <th>{{ __('table.account_no') }}</th>
                                    <th>{{ __('table.name') }}</th>
                                    <th>{{ __('table.branch') }}</th>
                                    <th>{{ __('table.company') }}</th>
                                    <th>{{ __('table.amount') }}</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </section>
            </div>
        </div>

        <div class="col-lg-3">
            <div class="col-lg-12 mb-3">
                <section class="card">
                    <div class="card-body">
                        <div class="table-header" >
                            <h4>{{ __('table.overdue_loan') }}</h4>
                            <a href="{{route('loan.index')}}">{{ __('table.go_to_loan') }}</a>
                        </div>
                        <table class="table table-bordered table-striped mb-0" id="table-overdue">
                            <thead>
                                <tr>
                                    <th>{{ __('table.loan_code') }}</th>
                                    <th>{{ __('table.date') }}</th>
                                    <th>{{ __('table.amount') }}</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </section>
            </div>

            <div class="col-lg-12 mb-3">
                <section class="card">
                    <div class="card-body">
                        <div class="table-header" >
                            <h4>{{ __('table.incoming_loan') }}</h4>
                            <a href="{{route('loan.index')}}">{{ __('table.go_to_loan') }}</a>
                        </div>
                        <table class="table table-bordered table-striped mb-0" id="table-incoming">
                            <thead>
                                <tr>
                                    <th>{{ __('table.loan_code') }}</th>
                                    <th>{{ __('table.date') }}</th>
                                    <th>{{ __('table.amount') }}</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>
<!-- end: page -->

@endsection
@section('page-js')
@endsection
@section('scripts')
<script>
    $(document).ready(function() {
        $('#table-company').DataTable({
            "processing": true,
            "serverSide": true,
            "fixedHeader": false,
            "ajax": {
                "url": "{{ route('company.load_company') }}",
                "type": "GET"
            },
            "order": [
                [0, "desc"]
            ],
            "columns": [
                {
                    "data": "company_code",
                    "render": function(data, type, row, meta) {
                        return `${row.company_name} (${row.company_code})`
                     }
                },
                {
                    "data": "stocka",
                     "render": function(data, type, row, meta) {
                        return formatCredit(row.stocka)
                     }
                },
                {
                    "data": "stockb",
                     "render": function(data, type, row, meta) {
                        return formatCredit(row.stockb)
                     }
                },
                {
                    "data": "stockbb",
                     "render": function(data, type, row, meta) {
                        return formatCredit(row.stockbb)
                     }
                },
                {
                    "data": "total_amount",
                     "render": function(data, type, row, meta) {
                        return formatCredit(row.total_amount ?? 0)
                     }
                }
            ]
        });

        $('#table-payment-method').DataTable({
            "processing": true,
            "serverSide": true,
            "fixedHeader": false,
            "ajax": {
                "url": "{{ route('payment_method.load_payment_method') }}",
                "type": "GET"
            },
            "order": [
                [0, "desc"]
            ],
            "columns": [
                {
                    "data": "bank_name"
                },
                {
                    "data": "account_no"
                },
                {
                    "data": "owner_name"
                },
                {
                    "data": "branch_name"
                },
                {
                    "data": "company_name"
                },
                {
                    "data": "amount"
                }
            ]
        });

        $('#table-incoming').DataTable({
            "processing": true,
            "serverSide": true,
            "fixedHeader": false,
            "ajax": {
                "url": "{{ route('loan.load_incoming_loan') }}",
                "type": "GET"
            },
            "order": [
                [1, "desc"]
            ],
            "columns": [
                {
                    "data": "loan_code",
                    "render": function(data, type, row, meta) {
                        return `<a href="{{ route('loan.single_loan', ['loan_code' => ':loan_code']) }}" class="info" title="View Detail">${row.loan_code}</a>`.replace(':loan_code',row.loan_code);
                    }
                },
                {
                    "data": "next_due_date"
                },
                {
                    "data": "next_due_amount"
                }
            ]
        });

        $('#table-overdue').DataTable({
            "processing": true,
            "serverSide": true,
            "fixedHeader": false,
            "ajax": {
                "url": "{{ route('loan.load_overdue_loan') }}",
                "type": "GET"
            },
            "order": [
                [1, "desc"]
            ],
            "columns": [
                 {
                    "data": "loan_code",
                    "render": function(data, type, row, meta) {
                        return `<a href="{{ route('loan.single_loan', ['loan_code' => ':loan_code']) }}" class="info" title="View Detail">${row.loan_code}</a>`.replace(':loan_code',row.loan_code);
                    }
                },
                {
                    "data": "next_due_date"
                },
                {
                    "data": "next_due_amount"
                }
            ]
        });

        // LOAD LOAN AMOUNT
        fetch(`{{ route('loan.fetch_loan_amount') }}`, {
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('total-loan-amount').innerHTML = '$'+data.total_loan_amount ?? '0.00';
        })
        .catch(error => {
            console.error('Search failed:', error);
        });

        // PAYMENT METHOD AMOUNT
        fetch(`{{ route('payment_method.load_payment_method_total_amount') }}`, {
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('total-payment-method-amount').innerHTML = '$'+data.total_payment_method_amount ?? '0.00';
        })
        .catch(error => {
            console.error('Search failed:', error);
        });


        // LOAD CAPITAL
        fetch(`{{ route('loan.fetch_capital') }}`, {
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('total-capital-amount').innerHTML = '$'+data.total_capital ?? '0.00';
        })
        .catch(error => {
            console.error('Search failed:', error);
        });

        // LOAD EXPENSES
        fetch(`{{ route('expense.fetch_expense') }}`, {
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('total-expenses-amount').innerHTML = '$'+(data.total_expenses ?? '0.00');
        })
        .catch(error => {
            console.error('Search failed:', error);
        });

        // LOAD OUTSTANDING
        fetch(`{{ route('loan.fetch_outstanding') }}`, {
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('total-outstanding-amount').innerHTML = '$'+data.total_outstanding ?? '0.00';
        })
        .catch(error => {
            console.error('Search failed:', error);
        });

        // LOAD PROFIT
        fetch(`{{ route('loan.fetch_profit') }}`, {
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('total-profit-amount').innerHTML = '$'+data.total_profit ?? '0.00';
        })
        .catch(error => {
            console.error('Search failed:', error);
        });
    });
</script>
@endsection
