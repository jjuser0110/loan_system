@extends('layouts.app')
@section('content')
<header class="page-header">
    <h2>{{ __('table.cash_book_report') }}</h2>
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
    <br>

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
                <table class="table cus-table table-bordered table-striped mb-0" id="table-cash-book-reports">
                    <thead>
                        <tr>
                            <th>{{ __('table.company') }}</th>
                            <th>{{ __('table.branch') }}</th>
                            <th>{{ __('table.date') }}</th>
                            <th>{{ __('table.description') }}</th>
                            <th>{{ __('table.loan_topup') }}</th>
                            <th>{{ __('table.payment') }}</th>
                            <th>{{ __('table.expenses') }}</th>
                            <th>{{ __('table.account_total') }}</th>
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
    let table_cash_book_reports;
    
    function formatDate(dateString) {
        if (!dateString) return '';
        let date = new Date(dateString);
        return date.toLocaleDateString();
    }
    
    $(document).ready(function() {
        table_cash_book_reports = $('#table-cash-book-reports').DataTable({
            processing: true,
            serverSide: true,
            fixedHeader: false,
            searching: false,
            deferLoading: 0,
            ajax: {
                url: "{{ route('report.load_cash_book_reports') }}",
                type: "GET",
                data: function(d) {
                    d.from_date = $('#filter_from_date').val();
                    d.to_date = $('#filter_to_date').val();
                    d.company_id = $('#filter_company').val();
                }
            },
            order: [[2, "desc"]],
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
                    "data": "date",
                    "name": "date",
                    "render": function(data) {
                        return data ? data.substring(0, 10) : '';
                    }
                },
                {
                    "data": "description",
                    "name": "description",
                    "render": function(data) {
                        return data ? data : '-';
                    }
                },
                {
                    "data": "loan_top_up",
                    "name": "loan_top_up",
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
                }
            ]
        });
    });

    $('#btn-filter').on('click', function() {
        let from = $('#filter_from_date').val();
        let to = $('#filter_to_date').val();
        let company = $('#filter_company').val();

        if (!from && !to && !company) {
            alert("{{ __('table.please_select_filter') }}");
            return;
        }

        table_cash_book_reports.ajax.reload();
    });
</script>
@endsection