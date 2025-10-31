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

@section('page-js')
    <script src="{{ asset('porto-assets/vendor/select2/js/select2.js') }}"></script>
    <script src="{{ asset('porto-assets/vendor/datatables/media/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('porto-assets/vendor/datatables/media/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('porto-assets/vendor/datatables/extras/TableTools/Buttons-1.4.2/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('porto-assets/vendor/datatables/extras/TableTools/Buttons-1.4.2/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('porto-assets/vendor/datatables/extras/TableTools/Buttons-1.4.2/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('porto-assets/vendor/datatables/extras/TableTools/Buttons-1.4.2/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('porto-assets/vendor/datatables/extras/TableTools/JSZip-2.5.0/jszip.min.js') }}"></script>
    <script src="{{ asset('porto-assets/vendor/datatables/extras/TableTools/pdfmake-0.1.32/pdfmake.min.js') }}"></script>
    <script src="{{ asset('porto-assets/vendor/datatables/extras/TableTools/pdfmake-0.1.32/vfs_fonts.js') }}"></script>
@endsection
@section('scripts')
    <script src="{{ asset('porto-assets/js/examples/examples.datatables.default.js') }}"></script>
    <script src="{{ asset('porto-assets/js/examples/examples.datatables.row.with.details.js') }}"></script>
    <script src="{{ asset('porto-assets/js/examples/examples.datatables.tabletools.js') }}"></script>
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
