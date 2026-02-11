@extends('layouts.app')
@section('content')
<header class="page-header">
    <h2>{{ __('table.cash_book_report') }}</h2>
</header>
@include('layouts.flash-message')
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
            "processing": true,
            "serverSide": true,
            "fixedHeader": false,
            "ajax": {
                "url": "{{ route('report.load_cash_book_reports') }}",
                "type": "GET",
                "error": function(xhr, error, code) {
                    console.error('DataTables AJAX Error:');
                    console.error('Status:', xhr.status);
                    console.error('Response:', xhr.responseText);
                    console.error('Error:', error);
                    console.error('Code:', code);
                    
                    alert('Error loading data. Please check console for details.');
                }
            },
            "order": [
                [2, "desc"] // Order by date column
            ],
            "columns": [
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
                        return data ? formatDate(data) : '';
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
</script>
@endsection