@extends('layouts.app')
@section('content')
<header class="page-header">
    <h2>{{ __('table.payment') }}</h2>
</header>
@include('layouts.flash-message')
<div class="row" style="padding-top:0">
    <div class="col-lg-12 mb-3">
        <section class="card">
            <div class="card-header" style="text-align: right;">
                <a class="btn btn-xs btn-square btn-primary" href="{{route('payment.create')}}">{{ __('table.create') }}</a>
            </div>
            <div class="card-body">
                <table class="table cus-table table-bordered table-striped mb-0" id="table-payment">
                    <thead>
                        <tr>
                            <th>{{ __('table.payment_code') }}</th>
                            <th>{{ __('table.paid') }}</th>
                            <th>{{ __('table.discount') }}</th>
                            <th>{{ __('table.interest_paid') }}</th>
                            <th>{{ __('table.late_paid') }}</th>
                            <th>{{ __('table.bank') }}</th>
                            <th>{{ __('table.collection_type') }}</th>
                            <th>{{ __('table.loan_code') }}</th>
                            @if(Auth::user()->role_id <= 3)
                            <th>{{ __('table.actions') }}</th>
                            @endif
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
                <h5 class="modal-title" id="modalUpdatePaymentLabel">{{ __('table.update_payment') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form-update-payment">
                    @csrf
                    <input type="hidden" name="payment_id" id="update-payment-id">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="col-form-label">{{ __('table.payment/capital_amount') }}</label>
                            <input type="number" class="form-control" id="update-payment-paid" name="payment_amount">
                        </div>
                        <div class="col-md-6">
                            <label class="col-form-label">{{ __('table.discount') }}</label>
                            <input type="number" class="form-control" id="update-payment-discount" name="discount_amount">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="col-form-label">{{ __('table.interest_amount') }}</label>
                            <input type="number" class="form-control" id="update-payment-interest" name="interest_paid_amount">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="col-form-label">{{ __('table.late_amount') }}</label>
                            <input type="number" class="form-control" id="update-payment-late" name="late_paid_amount" value="0">
                        </div>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="col-form-label">{{ __('table.collection') }}</label>
                        <select class="form-control" name="collection_type" id="update-payment-collection">
                            <option value="SKIM A">{{ __('table.skim_A') }}</option>
                            <option value="SKIM B">{{ __('table.skim_B') }}</option>
                        </select>
                    </div>
                    <!-- <div class="col-md-12 mb-3">
                        <label class="col-form-label">Cheque</label>
                        <input type="text" class="form-control" name="cheque" id="update-payment-cheque" autocomplete="off">
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="col-form-label">Bank</label>
                        <input type="text" class="form-control" name="bank" id="update-payment-bank" autocomplete="off">
                    </div> -->
                    <div class="col-md-12 mb-3">
                        <label class="col-form-label">{{ __('table.payment_method') }}</label>
                        <select class="form-control" id="update_payment_method_id" name="payment_method_id" disabled required readonly>
                            <option>{{ __('table.please_insert_loan_code_first') }}</option>
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('table.cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('table.submit') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
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
                    "data": null,
                    "render": function(data, type, row, meta) {
                        return `${row.bank_name}<br>${row.bank_account_no}<br>${row.bank_owner_name}`;
                    }
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
                @if(Auth::user()->role_id <= 3)
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
                @endif
            ]
            });
        });

        function updatePayment(rowIndex) {
            const data = table_payment.row(rowIndex).data();
            console.log(data);
            document.getElementById('update-payment-id').value = data.id;
            document.getElementById('update-payment-paid').value = data.payment_amount;
            document.getElementById('update-payment-interest').value = data.interest_paid_amount;
            document.getElementById('update-payment-late').value = data.late_paid_amount;
            document.getElementById('update-payment-discount').value = data.discount_amount;
            // document.getElementById('update-payment-bank').value = data.bank;
            // document.getElementById('update-payment-cheque').value = data.cheque;
            document.getElementById('update-payment-collection').value = data.collection_type;
              setupUpdatePaymentMethod(data.company_code,data.payment_method_id);
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

        function setupUpdatePaymentMethod(x,y){
            let d = document.getElementById('update_payment_method_id');
            d.disabled = true; // Keep it disabled
            if(x != false){
                fetch(`{{ route('payment_method.search_payment_methods') }}?company_code=${encodeURIComponent(x)}`, {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(methods => {
                    d.innerHTML = "";
                    if (methods.length === 0) {
                        d.innerHTML = '<option>No payment method found.</option>';
                    } else {
                        methods.forEach(method => {
                            d.innerHTML += `<option value="${method.id}" ${y == method.id ? 'selected' : ''}>${method.bank_name} / ${method.account_no} (RM ${formatCredit(method.amount)})</option>`;
                        });
                        // d.disabled = false; // REMOVE THIS LINE - keep it disabled
                    }
                })
                .catch(error => {
                    d.innerHTML = '<option>-- Failed to get methods. --</option>';
                });
            }
            else{ 
                d.innerHTML = "<option>Please select loan first.</option>";
            }
        }
    </script>
@endsection
