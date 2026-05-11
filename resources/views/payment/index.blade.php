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
                            <th>{{ __('table.payment_date') }}</th>
                            <th>{{ __('table.sched') }}</th>
                            <th>{{ __('table.paid') }}</th>
                            <th>{{ __('table.discount') }}</th>
                            <th>{{ __('table.int_paid') }}</th>
                            <th>{{ __('table.late_paid') }}</th>
                            <th>{{ __('table.top_up_cap') }}</th>
                            <th>{{ __('table.top_up_amt') }}</th>
                            <th>{{ __('table.balance') }}</th>
                            <th>{{ __('table.remark') }}</th>
                            <th>{{ __('table.type') }}</th>
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

                    {{-- Payment type selector --}}
                    <div class="col-md-12 mb-3">
                        <label class="col-form-label">{{ __('table.payment_type') }}</label>
                        <select class="form-control" id="update-payment_type" name="payment_type" required>
                            <option value="CCM">Payment / CCM</option>
                            <option value="INTEREST">Pay SKIM A Interest</option>
                            <option value="DISCOUNT">Discount Amount</option>
                            <option value="LATE">Pay Late</option>
                            <option value="TOPUP">Top Up</option>
                        </select>
                    </div>

                    {{-- CCM only --}}
                    <div class="row mb-3" id="update-wrap-payment">
                        <div class="col-md-6">
                            <label class="col-form-label">{{ __('table.payment/capital_amount') }}</label>
                            <input type="number" class="form-control" id="update-payment-paid" name="payment_amount">
                        </div>
                        <div class="col-md-6">
                            <label class="col-form-label">{{ __('table.discount') }}</label>
                            <input type="number" class="form-control" id="update-payment-discount" name="discount_amount">
                        </div>
                    </div>

                    {{-- TOP-UP Capital only --}}
                    <div class="col-md-12 mb-3" id="update-wrap-topup-capital">
                        <label class="col-form-label">{{ __('table.top_up_capital') }}</label>
                        <input type="number" class="form-control" id="update-topup-capital" name="top_up_capital" placeholder="5000.00" step="0.01">
                    </div>

                    {{-- TOP-UP only --}}
                    <div class="col-md-12 mb-3" id="update-wrap-topup-amount">
                        <label class="col-form-label">{{ __('table.top_up_amount') }}</label>
                        <input type="number" class="form-control" id="update-payment-topup" name="top_up" placeholder="5000.00" step="0.01">
                    </div>

                    {{-- greyed for non-CCM --}}
                    <div class="col-md-12 mb-3" id="update-wrap-late">
                        <label class="col-form-label">{{ __('table.late_amount') }}</label>
                        <input type="number" class="form-control" id="update-payment-late" name="late_paid_amount" value="0">
                    </div>

                    {{-- active for INTEREST, greyed for CCM --}}
                    <div class="col-md-12 mb-3" id="update-wrap-interest">
                        <label class="col-form-label">{{ __('table.interest_amount') }}</label>
                        <input type="number" class="form-control" id="update-payment-interest" name="interest_paid_amount">
                    </div>

                    {{-- Hidden collection type --}}
                    <input type="hidden" name="collection_type" id="update-payment-collection" value="">

                    {{-- Hidden payment method --}}
                    <input type="hidden" name="payment_method_id" id="update_payment_method_id" value="">

                    <div class="col-12">
                        <label class="col-form-label">{{ __('table.remark') }}</label>
                        <textarea class="form-control" id="update-payment-remark" name="remark" rows="3" placeholder="Enter remarks..."></textarea>
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
                "lengthMenu": [[10, 100, 500, 1000], ['10', '100', '500', '1000']],
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
                    "data": "created_at",
                    "render": function(data, type, row, meta) {
                        if (!data) return '-';
                        const parts = data.substring(0, 10).split('-');
                        return `${parts[2]}-${parts[1]}-${parts[0]}`;
                    }
                },
                {
                    "data": "payment_code",
                    "render": function(data) {
                        if (!data) return '-';
                        let match = data.match(/P(\d+)$/);
                        if (!match) return '-';
                        return parseInt(match[1], 10);
                    }
                },
                {
                    "data": "payment_amount"
                },
                {
                    "data": "discount_amount",
                    "render": function(data) {
                        let value = parseFloat(data);
                        if (isNaN(value) || value == 0) return '-';
                        return `<strong style="color:green">${value}</strong>`;
                    }
                },
                {
                    "data": "interest_paid_amount",
                    "render": function(data) {
                        let value = parseFloat(data);
                        if (isNaN(value) || value == 0) return '-';
                        return `<strong>${value}</strong>`;
                    }
                },
                {
                    "data": "late_paid_amount",
                    "render": function(data) {
                        let value = parseFloat(data);
                        if (isNaN(value) || value == 0) return '-';
                        return `<strong style="color:orange">${value}</strong>`;
                    }
                },
                {
                    "data": "top_up_capital",
                    "render": function(data) {
                        let value = parseFloat(data);
                        if (isNaN(value) || value == 0) return '-';
                        return `<strong style="color:orange">${value}</strong>`;
                    }
                },
                {
                    "data": "top_up",
                    "render": function(data) {
                        let value = parseFloat(data);
                        if (isNaN(value) || value == 0) return '-';
                        return `<strong style="color:orange">${value}</strong>`;
                    }
                },
                {
                    "data": "deducted_balance",
                    "render": function(data) {
                        if (data == null) return '-';
                        return parseFloat(data).toFixed(2);
                    }
                },
                {
                    "data": "remark",
                    "defaultContent": "-"
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

            // Event listener for modal payment type change
            document.getElementById('update-payment_type').addEventListener('change', function () {
                applyUpdatePaymentType(this.value);
            });
        });

        function updatePayment(rowIndex) {
            const data = table_payment.row(rowIndex).data();

            document.getElementById('update-payment-id').value         = data.id;
            document.getElementById('update-payment-collection').value = data.collection_type;
            document.getElementById('update-payment-remark').value     = data.remark;

            // Detect type from existing record
            let type = 'CCM';
            if (data.top_up > 0)                                                   type = 'TOPUP';
            else if (data.interest_paid_amount > 0 && data.payment_amount == 0)   type = 'INTEREST';
            else if (data.discount_amount > 0 && data.payment_amount == 0)        type = 'DISCOUNT';
            else if (data.late_paid_amount > 0 && data.payment_amount == 0)       type = 'LATE';

            document.getElementById('update-payment_type').value = type;

            // Apply type first (this locks/unlocks fields)
            applyUpdatePaymentType(type);

            // Then fill in values AFTER applyUpdatePaymentType so they don't get cleared
            document.getElementById('update-payment-paid').value       = data.payment_amount     ?? '';
            document.getElementById('update-payment-interest').value   = data.interest_paid_amount ?? '';
            document.getElementById('update-payment-late').value       = data.late_paid_amount   ?? '';
            document.getElementById('update-payment-discount').value   = data.discount_amount    ?? '';
            document.getElementById('update-payment-topup').value      = data.top_up             ?? '';
            document.getElementById('update-topup-capital').value      = data.top_up_capital       ?? '';

            setupUpdatePaymentMethod(data.company_code, data.payment_method_id);
            $('#modal-update-payment').modal('show');
        }

        const GREY = 'opacity:0.45; pointer-events:none; user-select:none;';

        function applyUpdatePaymentType(type) {
            const wrapPayment      = document.getElementById('update-wrap-payment');
            const wrapLate         = document.getElementById('update-wrap-late');
            const wrapInterest     = document.getElementById('update-wrap-interest');
            const wrapTopupCap     = document.getElementById('update-wrap-topup-capital');  // ← updated
            const wrapTopupAmount  = document.getElementById('update-wrap-topup-amount');   // ← updated

            const inputPaid        = document.getElementById('update-payment-paid');
            const inputDiscount    = document.getElementById('update-payment-discount');
            const inputLate        = document.getElementById('update-payment-late');
            const inputInterest    = document.getElementById('update-payment-interest');
            const inputTopup       = document.getElementById('update-payment-topup');
            const inputTopupCap    = document.getElementById('update-topup-capital');       // ← updated

            // Reset — grey everything first
            [wrapPayment, wrapLate, wrapInterest, wrapTopupCap, wrapTopupAmount].forEach(w => w.setAttribute('style', GREY));
            [inputPaid, inputDiscount, inputLate, inputInterest, inputTopup, inputTopupCap].forEach(i => {
                i.disabled = true;
                i.required = false;
                i.value    = '';
            });

            if (type === 'CCM') {
                wrapPayment.removeAttribute('style');
                inputPaid.disabled    = false;
                inputPaid.required    = true;
                inputDiscount.disabled = false;

            } else if (type === 'INTEREST') {
                wrapInterest.removeAttribute('style');
                inputInterest.disabled = false;
                inputInterest.required = true;

            } else if (type === 'DISCOUNT') {
                wrapPayment.removeAttribute('style');
                inputDiscount.disabled = false;
                inputDiscount.required = true;
                inputPaid.disabled     = true;

            } else if (type === 'LATE') {
                wrapLate.removeAttribute('style');
                inputLate.disabled = false;
                inputLate.required = true;

            } else if (type === 'TOPUP') {
                wrapTopupCap.removeAttribute('style');     // ← both shown for TOPUP
                wrapTopupAmount.removeAttribute('style');  // ← both shown for TOPUP
                inputTopupCap.disabled  = false;
                inputTopup.disabled     = false;
                inputTopup.required     = true;
            }
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
                        } else {
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
            let formData = new FormData(this);
            $.ajax({
                url: "{{ route('payment.update') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}"},
                success: function (response) {
                    if(response.success == true){
                        setReloadSwal('success','',response.message);
                    } else {
                        setDefaultSwal('error','',response.message);
                    }
                },
                error: function (xhr) {
                    setDefaultSwal('error','','There is something wrong, please try again.');
                }
            });
        });

        function setupUpdatePaymentMethod(x, y) {
            if (x) {
                fetch(`{{ route('payment_method.search_payment_methods') }}?company_code=${encodeURIComponent(x)}`, {
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                })
                .then(response => response.json())
                .then(methods => {
                    if (methods.length > 0) {
                        // Use existing payment method id if matched, else first
                        let matched = methods.find(m => m.id == y);
                        document.getElementById('update_payment_method_id').value = matched ? matched.id : methods[0].id;
                    } else {
                        document.getElementById('update_payment_method_id').value = '';
                    }
                })
                .catch(error => {
                    document.getElementById('update_payment_method_id').value = '';
                });
            } else {
                document.getElementById('update_payment_method_id').value = '';
            }
        }
    </script>
@endsection