@extends('layouts.app')
@section('content')
<header class="page-header">
    <h2>Payment</h2>
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
                <a class="btn btn-xs btn-square btn-primary" href="{{route('payment.create')}}">Create</a>
            </div>
            <div class="card-body">
                <table class="table cus-table table-bordered table-striped mb-0" id="table-payment">
                    <thead>
                        <tr>
                            <th>Payment Code</th>
                            <th>Paid</th>
                            <th>Discount</th>
                            <th>Interest Paid</th>
                            <th>Late Paid</th>
                            <th>Bank/Cheque</th>
                            <th>Collection Type</th>
                            <th>Loan Code</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </section>
    </div>
</div>

<div class="modal fade" id="modal-update-payment" tabindex="-1" aria-labelledby="modalUpdatePaymentLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalUpdatePaymentLabel">Update Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form-update-payment">
                    @csrf
                    <input type="hidden" name="payment_id" id="update-payment-id">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="col-form-label">Payment / Capital Amount</label>
                            <input type="number" class="form-control" id="update-payment-paid" name="payment_amount">
                        </div>
                        <div class="col-md-6">
                            <label class="col-form-label">Discount</label>
                            <input type="number" class="form-control" id="update-payment-discount" name="discount_amount">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="col-form-label">Interest Amount</label>
                            <input type="number" class="form-control" id="update-payment-interest" name="interest_paid_amount">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="col-form-label">Late Amount</label>
                            <input type="number" class="form-control" id="update-payment-late" name="late_paid_amount" value="0">
                        </div>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="col-form-label">Collection</label>
                        <select class="form-control" name="collection_type" id="update-payment-collection"required>
                            <option value="Collection A">Collection A</option>
                            <option value="Collection B">Collection B</option>
                        </select>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="col-form-label">Cheque</label>
                        <input type="text" class="form-control" name="cheque" id="update-payment-cheque" autocomplete="off">
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="col-form-label">Bank</label>
                        <input type="text" class="form-control" name="bank" id="update-payment-bank" autocomplete="off">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
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
        let table_payment;
        $(document).ready(function() {
            table_payment = $('#table-payment').DataTable({
                "processing": true,
                "serverSide": true,
                "fixedHeader": false,
                "ajax": {
                    "url": "{{ route('payment.load_payment') }}",
                    "type": "GET"
                },
                "order": [
                    [0, "desc"]
                ],
                "columns": [
                {
                    "data": "payment_code"
                },
                {
                    "data": "payment_amount"
                },
                {
                    "data": "discount_amount"
                },
                {
                    "data": "interest_paid_amount"
                },
                {
                    "data": "late_paid_amount"
                },
                {
                    "data": "bank"
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
            ]
            });
        });

        function updatePayment(rowIndex) {
            const data = table_payment.row(rowIndex).data();
            document.getElementById('update-payment-id').value = data.id;
            document.getElementById('update-payment-paid').value = data.payment_amount;
            document.getElementById('update-payment-interest').value = data.interest_paid_amount;
            document.getElementById('update-payment-late').value = data.late_paid_amount;
            document.getElementById('update-payment-discount').value = data.discount_amount;
            document.getElementById('update-payment-bank').value = data.bank;
            document.getElementById('update-payment-cheque').value = data.cheque;
            document.getElementById('update-payment-collection').value = data.collection_type;
            $('#modal-update-payment').modal('show');
        }

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
    </script>
@endsection
