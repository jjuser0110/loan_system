@extends('layouts.app')
@section('content')
<header class="page-header">
    <h2>Payment Method</h2>
</header>
@include('layouts.flash-message')
<div class="row" style="padding-top:0">
    <div class="col-lg-12 mb-3">
        <section class="card">
            <div class="card-header" style="display:flex;justify-content:space-between;align-items:center">
                <a href="{{ route('payment_method.logs') }}" style="text-decoration:underline">View Logs</a>
                <a class="btn btn-xs btn-square btn-primary" onclick="event.preventDefault();$('#modal-create-payment-method').modal('show')">Create</a>
            </div>
            <div class="card-body">
                <table class="table cus-table table-bordered table-striped mb-0" id="table-payment-method">
                    <thead>
                        <tr>
                            <th>Bank</th>
                            <th>Account No</th>
                            <th>Name</th>
                            <th>Branch</th>
                            <th>Company</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </section>
    </div>
</div>

<div class="modal fade" id="modal-create-payment-method" tabindex="-1" aria-labelledby="modalCreatePaymentMethodLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCreatePaymentMethodLabel">Create Payment Method</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form-create-payment-method">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="col-form-label">Bank</label>
                            <select class="form-control" name="bank_id" required>
                                @foreach($banks as $bank)
                                <option value="{{ $bank->id }}">{{ $bank->bank_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="col-form-label">Company</label>
                            <select class="form-control" name="company_id" required>
                                @foreach($companies as $company)
                                <option value="{{ $company->id }}">{{ $company->company_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="col-form-label">Account No</label>
                            <input type="number" class="form-control" name="account_no" required>
                        </div>
                        <div class="col-md-6">
                            <label class="col-form-label">Owner Name</label>
                            <input type="text" class="form-control"name="owner_name" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6 mb-3">
                            <label class="col-form-label">Status</label>
                            <select class="form-control" name="status">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
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

<div class="modal fade" id="modal-update-payment-method" tabindex="-1" aria-labelledby="modalUpdatePaymentMethodLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalUpdatePaymentMethodLabel">Update Payment Method</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form-update-payment-method">
                    @csrf
                    <input type="hidden" id="update-payment-method-id" name="payment_method_id">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="col-form-label">Bank</label>
                            <select class="form-control" id="update-payment-method-bank-id" name="bank_id" required>
                                @foreach($banks as $bank)
                                <option value="{{ $bank->id }}">{{ $bank->bank_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="col-form-label">Company</label>
                            <select class="form-control" id="update-payment-method-company-id" name="company_id" required>
                                @foreach($companies as $company)
                                <option value="{{ $company->id }}">{{ $company->company_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="col-form-label">Account No</label>
                            <input type="number" class="form-control" id="update-payment-method-account-no" name="account_no" required>
                        </div>
                        <div class="col-md-6">
                            <label class="col-form-label">Owner Name</label>
                            <input type="text" class="form-control" id="update-payment-method-owner-name" name="owner_name" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6 mb-3">
                            <label class="col-form-label">Status</label>
                            <select class="form-control" id="update-payment-method-status" name="status">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
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

<div class="modal fade" id="modal-update-payment-method-credit" tabindex="-1" aria-labelledby="modalUpdatePaymentMethodCreditLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalUpdatePaymentMethodCreditLabel">Update Payment Method Credit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form-update-payment-method-credit">
                    @csrf
                    <input type="hidden" id="update-payment-method-credit-id" name="payment_method_id">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="col-form-label">Amount</label>
                            <input type="number" class="form-control" id="update-payment-method-credit-amount" name="amount" required>
                        </div>
                    </div>
                     <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="col-form-label">Remark</label>
                            <input type="text" class="form-control" id="update-payment-method-credit-remark" name="remark">
                        </div>
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
            table_payment = $('#table-payment-method').DataTable({
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
                },
                {
                    "data": "is_active",
                    "render": function(data, type, row, meta) {
                        return `<span style="color:${row.is_active == 1 ? '#009400' : 'red'}">${row.is_active == 1 ? 'Active' : 'Inactive'}</span>`;
                    }
                },
                {
                    "data": null,
                    "render": function(data, type, row, meta) {
                        return`
                            <div class="cus-action-wrapper">
                                <a class="cus-action-icon info" title="Update Payment" onclick="updatePaymentMethod(${meta.row},${row.company_id},${row.bank_id})"><i class="fas fa-edit"></i></a>
                                <a class="cus-action-icon success" title="Delete Payment" onclick="updatePaymentMethodCredit(${meta.row})"><i class="fa fa-usd"></i></a>
                            </div>
                        `;
                    }
                }
            ]
            });
        });

        $('#form-create-payment-method').on('submit', function (e) {
            e.preventDefault();
            let form = $(this);
            let formData = new FormData(this);
            $.ajax({
                url: "{{ route('payment_method.store') }}",
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

        $('#form-update-payment-method').on('submit', function (e) {
            e.preventDefault();
            let form = $(this);
            let formData = new FormData(this);
            $.ajax({
                url: "{{ route('payment_method.update') }}",
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

        $('#form-update-payment-method-credit').on('submit', function (e) {
            e.preventDefault();
            let form = $(this);
            let formData = new FormData(this);
            $.ajax({
                url: "{{ route('payment_method.update_credit') }}",
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

        function updatePaymentMethod(rowIndex,company_id,bank_id) {
            const data = table_payment.row(rowIndex).data();
            document.getElementById('update-payment-method-id').value = data.id;
            document.getElementById('update-payment-method-company-id').value = company_id;
            document.getElementById('update-payment-method-bank-id').value = bank_id;
            document.getElementById('update-payment-method-account-no').value = data.account_no;
            document.getElementById('update-payment-method-owner-name').value = data.owner_name;
            document.getElementById('update-payment-method-status').value = data.is_active;
            $('#modal-update-payment-method').modal('show');
        }

        function updatePaymentMethodCredit(rowIndex) {
            const data = table_payment.row(rowIndex).data();
            document.getElementById('update-payment-method-credit-id').value = data.id;
            document.getElementById('update-payment-method-credit-amount').value = null;
            document.getElementById('update-payment-method-credit-remark').value = null;
            $('#modal-update-payment-method-credit').modal('show');
        }
    </script>
@endsection
