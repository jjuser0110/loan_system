@extends('layouts.app')
@section('content')
<header class="page-header">
    <h2>Payment Method Logs</h2>
</header>
@include('layouts.flash-message')
<div class="row" style="padding-top:0">
    <div class="col-lg-12 mb-3">
        <section class="card">
            <div class="card-header" style="text-align: left;">
                <a href="{{ route('payment_method.index') }}" style="text-decoration:underline">Back to Payment Methods</a>
            </div>
            <div class="card-body">
                <table class="table cus-table table-bordered table-striped mb-0" id="table-payment-method-logs">
                    <thead>
                        <tr>
                            <th>Payment Method</th>
                            <th>Details</th>
                            <th>Branch</th>
                            <th>Company</th>
                            <th>Description</th>
                            <th>Amount</th>
                            <th>Amount Before</th>
                            <th>Amount After</th>
                            <th>Created At</th>
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
                    "type": "GET"
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
