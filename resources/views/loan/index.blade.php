@extends('layouts.app')
@section('content')
<header class="page-header">
    <h2>Loan</h2>
</header>
@include('layouts.flash-message')
<div class="row mb-3" style="padding-top:40px;">
    <div class="col-xl-3">
        <section class="card card-featured-left card-featured-primary mb-3">
            <div class="card-body">
                <div class="widget-summary">
                    <div class="widget-summary-col" style="vertical-align: middle">
                        <div class="summary" style="min-height:1px">
                            <h4 class="title" style="margin-bottom: 5px">Total Profits</h4>
                            <div class="info">
                                <strong class="amount">RM <span style="font-size:1.4rem;vertical-align:unset" id="total-profit">Loading...</span></strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <div class="col-xl-3">
        <section class="card card-featured-left card-featured-secondary mb-3">
            <div class="card-body">
                <div class="widget-summary">
                    <div class="widget-summary-col" style="vertical-align: middle">
                        <div class="summary" style="min-height:1px">
                            <h4 class="title" style="margin-bottom: 5px">Total Capital</h4>
                            <div class="info">
                                <strong class="amount">RM {{ number_format($total_capital,2,'.',',') }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <div class="col-xl-3">
        <section class="card card-featured-left card-featured-secondary mb-3">
            <div class="card-body">
                <div class="widget-summary">
                    <div class="widget-summary-col" style="vertical-align: middle">
                        <div class="summary" style="min-height:1px">
                            <h4 class="title" style="margin-bottom: 5px">Total Outstanding</h4>
                            <div class="info">
                                <strong class="amount">RM {{ number_format($outstanding,2,'.',',') }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
<div class="row" style="padding-top:0">
    <div class="col-lg-12 mb-3">
        <section class="card">
            <div class="card-header" style="text-align: right;">
                <a class="btn btn-xs btn-square btn-primary" href="{{ route('loan.create') }}">Create</a>
            </div>
            <div class="card-body">
                <table class="table cus-table table-bordered table-striped mb-0" id="table-loan">
                    <thead>
                        <tr>
                            <th>Loan Code</th>
                            <th>Customer</th>
                            <th>Company</th>
                            <th>Interest Group</th>
                            <th>Interest Rate</th>
                            <th>Loan Amount</th>
                            <th>Installment</th>
                            <th>Loan Term</th>
                            <th>Capital</th>
                            <th>Created At</th>
                            <th>Action</th>
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
    $(document).ready(function() {
        $('#table-loan').DataTable({
            "processing": true,
            "serverSide": true,
            "fixedHeader": false,
            "ajax": {
                "url": "{{ route('loan.load_loan') }}",
                "type": "GET"
            },
            "order": [
                [2, "desc"]
            ],
            "columns": [
                {
                    "data": "loan_code"
                },
                {
                    "data": "customer_name",
                    "render": function(data, type, row, meta) {
                        return '<a href="{{ url('customer/view') }}/' + row.customer_id + '" target="_blank">' +row.customer_code + "<br>" +row.customer_name+'</a>';
                    }
                },
                {
                    "data": "company_code",
                    "render": function(data, type, row, meta) {
                        return `<a style="text-decoration:none" onclick="e.preventDefault()">${row.company_code}<br>${row.company_name}</a>`;
                    }
                },
                {
                    "data": "interest_group"
                },
                {
                    "data": "interest_rate",
                    "render": function(data, type, row, meta) {
                        return data+"%";
                    }
                },
                {
                    "data": "loan_amount",
                },
                {
                    "data": "installment",
                    "render": function(data, type, row, meta) {
                        let installment = `${row.installment}`;
                        if(row.interest_group == "SKIM B"){
                            installment = `${row.installment}<br><span style="color:#7c7c7c;font-size:12px">First: ${row.first_payment}</span><br> <span style="color:#7c7c7c;font-size:12px">Last: ${row.last_payment}</span> `;
                        }
                        return `<a style="text-decoration:none" onclick="e.preventDefault()">${installment}</a>`;
                    }
                },
                {
                    "data": "loan_term",
                    "render": function(data, type, row, meta) {
                        return row.interest_group == 'SKIM B' ? row.loan_term : '-';
                    }
                },
                {
                    "data": "capital"
                },
                {
                    "data": "created_at",
                    "render": function(data, type, row, meta) {
                        return formatDate(data);
                    }
                },
                {
                    "data": null,
                    "render": function(data, type, row, meta) {
                        
                        let url = `
                            <div class="cus-action-wrapper">
                                <a href="{{ route('loan.single_loan', ['loan_code' => ':loan_code']) }}" target="_blank" class="cus-action-icon info" title="View Detail"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('payment.create', ['loan_code' => ':loan_code']) }}" target="_blank" class="cus-action-icon info" title="Create Payment"><i class="fas fa-money-check-alt"></i></a>
                                <a href="{{ route('schedule.create', ['loan_code' => ':loan_code']) }}" target="_blank" class="cus-action-icon info" title="Create Schedule"><i class="fas fa-calendar-alt"></i></a>
                            </div>
                            `;
                        url = url.replaceAll(':loan_code', row.loan_code);
                        return url;
                    }
                }
            ]
        });
    });

    fetch(`{{ route('loan.fetch_profit') }}`, {
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

</script>
@endsection
