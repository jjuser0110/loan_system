@extends('layouts.app')
@section('content')
<header class="page-header">
    <h2>{{ __('table.daily_report') }}</h2>
</header>
@include('layouts.flash-message')
<div class="row mb-3">
    <div class="col-md-3">
        <label>{{ __('table.from_date') }}</label>
        <input type="date" id="filter_from_date" class="form-control">
    </div>

    <div class="col-md-3">
        <label>{{ __('table.to_date') }}</label>
        <input type="date" id="filter_to_date" class="form-control">
    </div>

    <div class="col-md-3">
        <label>{{ __('table.company') }}</label>
        <select id="filter_company" class="form-control">
            <option value="">{{ __('table.select_company') }}</option>
            @foreach($companies as $company)
                <option value="{{ $company->id }}">
                    {{ $company->company_name }} ({{ $company->company_code }})
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3 d-flex align-items-end">
        <button class="btn btn-primary w-100" id="btn-filter">
            {{ __('table.filter') }}
        </button>
    </div>
</div>
<div class="row" style="padding-top:0">
    <div class="col-lg-12 mb-3">
        <section class="card">
            <div class="card-body">
                <table class="table cus-table table-bordered table-striped mb-0" id="table-daily-reports">
                    <thead>
                        <tr>
                            <th>{{ __('table.company') }}</th>
                            <th>{{ __('table.branch') }}</th>
                            <th>{{ __('table.stock_a') }}</th>
                            <th>{{ __('table.stock_b') }}</th>
                            <th>{{ __('table.stock_bb') }}</th>
                            <th>{{ __('table.company_amount') }}</th>
                            <th>{{ __('table.loan_topup') }}</th>
                            <th>{{ __('table.payment') }}</th>
                            <th>{{ __('table.expenses') }}</th>
                            <th>{{ __('table.account_total') }}</th>
                            <th>{{ __('table.created_date') }}</th>
                            <th>{{ __('table.closing_date') }}</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </section>
    </div>
</div>

@endsection

@section('scripts')
<script>
    let table_daily_reports;
    
    $(document).ready(function() {

        // ── Restore saved filters ────────────────────────────────────
        let savedFrom    = sessionStorage.getItem('dr_from_date');
        let savedTo      = sessionStorage.getItem('dr_to_date');
        let savedCompany = sessionStorage.getItem('dr_company');

        if (savedFrom)    $('#filter_from_date').val(savedFrom);
        if (savedTo)      $('#filter_to_date').val(savedTo);
        if (savedCompany) $('#filter_company').val(savedCompany);

        // ── Init DataTable ───────────────────────────────────────────
        table_daily_reports = $('#table-daily-reports').DataTable({
            processing: true,
            serverSide: true,
            fixedHeader: false,
            lengthMenu: [[10, 100, 500, 1000], ['10', '100', '500', '1000']],
            searching: false,
            stateSave: true,
            deferLoading: 0,
            ajax: {
                url: "{{ route('report.load_daily_reports') }}",
                type: "GET",
                data: function(d) {
                    d.from_date  = $('#filter_from_date').val();
                    d.to_date    = $('#filter_to_date').val();
                    d.company_id = $('#filter_company').val();
                }
            },
            order: [[10, "desc"]],
            columns: [
                {
                    "data": "company_name",
                    "name": "company_name",
                    "render": function(data, type, row) {
                        return row.company_name + '<br>(' + row.company_code + ')';
                    }
                },
                {
                    "data": "branch_name",
                    "name": "branch_name",
                    "render": function(data, type, row) {
                        return row.branch_name + '<br>(' + row.branch_code + ')';
                    }
                },
                {
                    "data": "stock_a",
                    "name": "stock_a",
                    "render": function(data) {
                        return data ? parseFloat(data).toFixed(2) : '0.00';
                    }
                },
                {
                    "data": "stock_b",
                    "name": "stock_b",
                    "render": function(data) {
                        return data ? parseFloat(data).toFixed(2) : '0.00';
                    }
                },
                {
                    "data": "stock_bb",
                    "name": "stock_bb",
                    "render": function(data) {
                        return data ? parseFloat(data).toFixed(2) : '0.00';
                    }
                },
                {
                    "data": "company_amount",
                    "name": "company_amount",
                    "render": function(data) {
                        return data ? parseFloat(data).toFixed(2) : '0.00';
                    }
                },
                {
                    "data": "loan_topup",
                    "name": "loan_topup",
                    "render": function(data) {
                        return data ? parseFloat(data).toFixed(2) : '0.00';
                    }
                },
                {
                    "data": "payment",
                    "name": "payment",
                    "render": function(data) {
                        return data ? parseFloat(data).toFixed(2) : '0.00';
                    }
                },
                {
                    "data": "expenses",
                    "name": "expenses",
                    "render": function(data) {
                        return data ? parseFloat(data).toFixed(2) : '0.00';
                    }
                },
                {
                    "data": "account_total_amount",
                    "name": "account_total_amount",
                    "render": function(data) {
                        return data ? parseFloat(data).toFixed(2) : '0.00';
                    }
                },
                {
                    "data": "created_date",
                    "name": "created_date",
                    "render": function(data, type, row, meta) {
                        if (!data) return '-';
                        const parts = data.substring(0, 10).split('-');
                        return `${parts[2]}-${parts[1]}-${parts[0]}`;
                    }
                },
                {
                    "data": "closing_date",
                    "name": "closing_date",
                    "render": function(data, type, row, meta) {
                        if (!data) return '-';
                        const parts = data.substring(0, 10).split('-');
                        return `${parts[2]}-${parts[1]}-${parts[0]}`;
                    }
                }
            ]
        });

        // ── Auto reload if saved filters exist ───────────────────────
        if (savedFrom || savedTo || savedCompany) {
            table_daily_reports.ajax.reload();
        }
    });

    // ── Filter button ────────────────────────────────────────────────
    $('#btn-filter').on('click', function() {
        let from    = $('#filter_from_date').val();
        let to      = $('#filter_to_date').val();
        let company = $('#filter_company').val();

        if (!from && !to && !company) {
            alert("{{ __('table.please_select_filter') }}");
            return;
        }

        // Save to sessionStorage
        sessionStorage.setItem('dr_from_date', from);
        sessionStorage.setItem('dr_to_date',   to);
        sessionStorage.setItem('dr_company',   company);

        table_daily_reports.ajax.reload();
    });
</script>
@endsection