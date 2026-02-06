@extends('layouts.app')
@section('content')
<header class="page-header">
    <h2>{{ __('table.payment_method_logs') }}</h2>
</header>
@include('layouts.flash-message')
<div class="row" style="padding-top:0">
    <div class="col-lg-12 mb-3">
        <section class="card">
            <div class="card-header" style="text-align: left;">
                <a href="{{ route('payment_method.index') }}" style="text-decoration:underline">{{ __('table.back_to_payment_methods') }}</a>
            </div>
            <div class="card-body">
                <table class="table cus-table table-bordered table-striped mb-0" id="table-payment-method-logs">
                    <thead>
                        <tr>
                            <th>{{ __('table.payment_method') }}</th>
                            <th>{{ __('table.details') }}</th>
                            <th>{{ __('table.branch') }}</th>
                            <th>{{ __('table.company') }}</th>
                            <th>{{ __('table.description') }}</th>
                            <th>{{ __('table.amount') }}</th>
                            <th>{{ __('table.amount_before') }}</th>
                            <th>{{ __('table.amount_after') }}</th>
                            <th>{{ __('table.created_at') }}</th>
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
        let table_logs;
        $(document).ready(function() {
            table_logs = $('#table-payment-method-logs').DataTable({
                "processing": true,
                "serverSide": true,
                "fixedHeader": false,
                "ajax": {
                    "url": "{{ route('payment_method.load_payment_method_logs') }}",
                    "type": "GET",
                    "data": function(d) {
                        @if(isset($account_no) && $account_no)
                            d.account_no = "{{ $account_no }}";
                        @endif
                    }
                },
                "order": [
                    [8, "desc"]
                ],
                "columns": [
                {
                    "data": "account_no",
                    "render": function(data, type, row, meta) {
                        return `${row.account_no}<br>${row.owner_name}`;
                    }
                },
                {
                    "data": "details"
                },
                {
                    "data": "branch_name",
                    "render": function(data, type, row, meta) {
                        return `${row.branch_name}<br>(${row.branch_code})`;
                    }
                },
                {
                    "data": "company_name",
                    "render": function(data, type, row, meta) {
                        return `${row.company_name}<br>(${row.company_code})`;
                    }
                },
                {
                    "data": "description"
                },
                {
                    "data": "amount"
                },
                {
                    "data": "prev_amount",
                },
                {
                    "data": "total",
                },
                {
                    "data": "created_at",
                    "render": function(data, type, row, meta) {
                        return `${formatDate(row.created_at)}`;
                    }
                },
            ]
            });
        });
    </script>
@endsection
