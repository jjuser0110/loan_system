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
                <a class="btn btn-xs btn-square btn-primary" onclick="openAddPaymentModal()">{{ __('table.create') }}</a>
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

<div class="modal fade" id="modal-add-payment" tabindex="-1" aria-labelledby="modalAddPaymentLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAddPaymentLabel">{{ __('table.add_payment') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form-add-payment">
                    @csrf
                    <input type="hidden" name="loan_code" id="add-loan-code" value="">

                    {{-- Loan search --}}
                    <div class="col-md-12 mb-3" style="position:relative">
                        <label class="col-form-label">{{ __('table.loan_code') }}</label>
                        <input type="text" id="add-loan-search" class="form-control" placeholder="Type at least 3 characters..." autocomplete="off">
                        <div id="add-loan-dropdown" class="dropdown-menu w-100" style="display:none; max-height:200px; overflow-y:auto; padding:0;"></div>
                    </div>

                    {{-- Payment type selector --}}
                    <div class="col-md-12 mb-3">
                        <label class="col-form-label">{{ __('table.payment_type') }}</label>
                        <select class="form-control" id="add-payment-type" onchange="applyAddPaymentType(this.value)" disabled>
                            <option value="DEFAULT">Option</option>
                            <option value="CCM">Payment / CCM / Discount</option>
                            <option value="INTEREST">Pay SKIM A Interest</option>
                            <option value="LATE">Pay Late</option>
                            <option value="TOPUP">Top Up</option>
                        </select>
                    </div>

                    {{-- Schedule --}}
                    <div class="col-md-12 mb-3" id="add-wrap-schedule" style="display:none">
                        <label class="col-form-label">Schedule</label>
                        <select class="form-control" id="add-input-schedule" onchange="applyScheduleMultiplier(this.value)">
                            <option value="0">-- Select Schedule --</option>
                        </select>
                    </div>

                    {{-- Payment / discount --}}
                    <div class="row mb-3" id="add-wrap-payment">
                        <div class="col-md-6">
                            <label class="col-form-label">{{ __('table.payment/capital_amount') }}</label>
                            <input type="number" class="form-control" id="add-input-payment-amount" name="payment_amount" value="0">
                            <p class="p-note">
                                <span id="display-group">-</span><br>
                                {{ __('table.outstanding') }}: <span id="display-balance">0.00</span> &nbsp;&nbsp;&nbsp;
                                {{ __('table.next_payment') }}: <span id="display-next-amount"></span><br>
                                <span style="color:blue;">{{ __('table.date') }}: <span id="display-pay-date">{{ now()->format('Y-n-j') }}</span></span> &nbsp;&nbsp;&nbsp;
                                {{ __('table.due_date') }}: <span id="display-due-date"></span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="col-form-label">{{ __('table.discount') }}</label>
                            <input type="number" class="form-control" name="discount_amount" value="0">
                        </div>
                    </div>

                    {{-- Top-up --}}
                    <div class="row mb-3">
                        <div class="col-md-6" id="add-wrap-topup-capital">
                            <label class="col-form-label">{{ __('table.top_up_capital') }}</label>
                            <input type="number" class="form-control" id="add-input-topup-capital" name="top_up_capital" placeholder="5000.00" step="0.01" value="0">
                        </div>
                        <div class="col-md-6" id="add-wrap-topup">
                            <label class="col-form-label">{{ __('table.top_up_amount') }}</label>
                            <input type="number" class="form-control" id="add-input-topup" name="top_up" placeholder="5000.00" step="0.01" value="0">
                        </div>
                    </div>

                    {{-- Late --}}
                    <div class="col-md-12 mb-3" id="add-wrap-late">
                        <label class="col-form-label">{{ __('table.late_amount') }}</label>
                        <input type="number" class="form-control" id="add-input-payment-late" name="late_paid_amount" value="0">
                        <p class="p-note" id="display-late-balance">0.00</p>
                    </div>

                    {{-- Interest --}}
                    <div class="col-md-12 mb-3" id="add-wrap-interest">
                        <label class="col-form-label">{{ __('table.interest_amount') }}</label>
                        <input type="number" class="form-control" id="add-input-payment-interest" name="interest_paid_amount" value="0">
                        <p class="p-note" id="display-interest-amount">0.00</p>
                    </div>

                    <input type="hidden" name="collection_type" id="add-collection-type" value="">
                    <input type="hidden" name="payment_method_id" id="add-payment-method-id" value="">

                    <div class="col-md-12 mb-3">
                        <label class="col-form-label">{{ __('table.pay_date') }}</label>
                        <input type="date" class="form-control" id="add-pay-date" name="created_at" value="{{ date('Y-m-d') }}">
                    </div>

                    <div class="col-12">
                        <label class="col-form-label">{{ __('table.remark') }}</label>
                        <textarea class="form-control" name="remark" rows="3" placeholder="Enter remarks..."></textarea>
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
                        <select class="form-control" id="update-payment-type" onchange="applyUpdatePaymentType(this.value)">
                            <option value="CCM">Payment / CCM / Discount</option>
                            <option value="INTEREST">Pay SKIM A Interest</option>
                            <option value="LATE">Pay Late</option>
                            <option value="TOPUP">Top Up</option>
                        </select>
                    </div>

                    {{-- Other payments of the same loan --}}
                    <div class="col-md-12 mb-3">
                        <label class="col-form-label">{{ __('table.select_payment_record') }}</label>
                        <select class="form-control" id="update-payment-switcher" onchange="switchPayment(this.value)"></select>
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

                    {{-- Top-up --}}
                    <div class="row mb-3">
                        <div class="col-md-6" id="update-wrap-topup-capital">
                            <label class="col-form-label">{{ __('table.top_up_capital') }}</label>
                            <input type="number" class="form-control" id="update-payment-topup-capital" name="top_up_capital" placeholder="5000.00" step="0.01" value="0">
                        </div>
                        <div class="col-md-6" id="update-wrap-topup">
                            <label class="col-form-label">{{ __('table.top_up_amount') }}</label>
                            <input type="number" class="form-control" id="update-payment-topup" name="top_up" placeholder="5000.00" step="0.01" value="0">
                        </div>
                    </div>

                    {{-- Late --}}
                    <div class="col-md-12 mb-3" id="update-wrap-late">
                        <label class="col-form-label">{{ __('table.late_amount') }}</label>
                        <input type="number" class="form-control" id="update-payment-late" name="late_paid_amount" value="0">
                    </div>

                    {{-- Interest --}}
                    <div class="col-md-12 mb-3" id="update-wrap-interest">
                        <label class="col-form-label">{{ __('table.interest_amount') }}</label>
                        <input type="number" class="form-control" id="update-payment-interest" name="interest_paid_amount">
                    </div>

                    <input type="hidden" name="collection_type" id="update-payment-collection" value="">
                    <input type="hidden" name="payment_method_id" id="update_payment_method_id" value="">

                    <div class="col-md-12 mb-3">
                        <label class="col-form-label">{{ __('table.pay_date') }}</label>
                        <input type="date" class="form-control" id="update-pay-date" name="created_at" value="{{ date('Y-m-d') }}">
                    </div>

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
                "lengthMenu": [[100, 500, 1000, 100000000], [100, 500, 1000, 'All']],
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
        });

        // ================= UPDATE PAYMENT MODAL =================
        const GREY = 'opacity:0.45; pointer-events:none; user-select:none;';

        let updateCompanyCode = null;   // company of the loan being edited
        let updatePaymentList = [];     // all payments of that loan (for the switcher)

        function updatePayment(rowIndex) {
            const data = table_payment.row(rowIndex).data();
            updateCompanyCode = data.company_code;
            updatePaymentList = [data];

            // switcher starts with the clicked payment, then loads its loan's other payments
            const switcher = $('#update-payment-switcher');
            switcher.html(`<option value="${data.id}" selected>${data.payment_code}</option>`);

            fetch(`{{ route('payment.get_by_loan') }}?loan_code=${encodeURIComponent(data.loan_code)}`, {
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            })
            .then(r => r.json())
            .then(list => {
                if (!Array.isArray(list) || list.length === 0) return;
                updatePaymentList = list;
                switcher.empty();
                list.forEach(p => {
                    switcher.append(`<option value="${p.id}" ${p.id == data.id ? 'selected' : ''}>${p.payment_code}</option>`);
                });
            })
            .catch(() => {});

            fillUpdateForm(data);
            $('#modal-update-payment').modal('show');
        }

        function switchPayment(paymentId) {
            const data = updatePaymentList.find(p => p.id == paymentId);
            if (data) fillUpdateForm(data);
        }

        function fillUpdateForm(data) {
            document.getElementById('update-payment-id').value         = data.id;
            document.getElementById('update-payment-collection').value = data.collection_type;
            document.getElementById('update-pay-date').value           = data.created_at ? data.created_at.substring(0, 10) : '';
            document.getElementById('update-payment-remark').value     = data.remark ?? '';

            // Detect type from existing record
            let type = 'CCM';
            if (data.top_up > 0 || data.top_up_capital > 0)                       type = 'TOPUP';
            else if (data.interest_paid_amount > 0 && data.payment_amount == 0)   type = 'INTEREST';
            else if (data.late_paid_amount > 0 && data.payment_amount == 0)       type = 'LATE';
            // discount-only payments use CCM (it has the discount field)

            // Hide options based on collection type
            if (data.collection_type === 'SKIM B') {
                $('#update-payment-type option[value="TOPUP"]').hide();
                $('#update-payment-type option[value="INTEREST"]').hide();
                $('#update-payment-type option[value="LATE"]').show();
                if (type === 'TOPUP' || type === 'INTEREST') type = 'CCM';
            } else if (data.collection_type === 'SKIM A') {
                $('#update-payment-type option[value="LATE"]').hide();
                $('#update-payment-type option[value="TOPUP"]').show();
                $('#update-payment-type option[value="INTEREST"]').show();
                if (type === 'LATE') type = 'CCM';
            } else {
                $('#update-payment-type option').show();
            }

            document.getElementById('update-payment-type').value = type;
            applyUpdatePaymentType(type);

            // Fill values AFTER applyUpdatePaymentType so they don't get cleared
            document.getElementById('update-payment-paid').value          = data.payment_amount       ?? '';
            document.getElementById('update-payment-interest').value      = data.interest_paid_amount ?? '';
            document.getElementById('update-payment-late').value          = data.late_paid_amount     ?? '';
            document.getElementById('update-payment-discount').value      = data.discount_amount      ?? '';
            document.getElementById('update-payment-topup').value         = data.top_up               ?? '';
            document.getElementById('update-payment-topup-capital').value = data.top_up_capital       ?? '';

            setupUpdatePaymentMethod(updateCompanyCode, data.payment_method_id);
        }

        function applyUpdatePaymentType(type) {
            const wrapPayment      = document.getElementById('update-wrap-payment');
            const wrapLate         = document.getElementById('update-wrap-late');
            const wrapInterest     = document.getElementById('update-wrap-interest');
            const wrapTopup        = document.getElementById('update-wrap-topup');
            const wrapTopupCapital = document.getElementById('update-wrap-topup-capital');

            const inputPaid         = document.getElementById('update-payment-paid');
            const inputDiscount     = document.getElementById('update-payment-discount');
            const inputLate         = document.getElementById('update-payment-late');
            const inputInterest     = document.getElementById('update-payment-interest');
            const inputTopup        = document.getElementById('update-payment-topup');
            const inputTopupCapital = document.getElementById('update-payment-topup-capital');

            // Reset: grey everything first
            [wrapPayment, wrapLate, wrapInterest, wrapTopup, wrapTopupCapital].forEach(w => w.setAttribute('style', GREY));
            [inputPaid, inputDiscount, inputLate, inputInterest, inputTopup, inputTopupCapital].forEach(i => {
                i.disabled = true;
                i.required = false;
                i.value    = '';
            });

            if (type === 'CCM') {
                wrapPayment.removeAttribute('style');
                inputPaid.disabled     = false;
                inputPaid.required     = true;
                inputDiscount.disabled = false;
            } else if (type === 'INTEREST') {
                wrapInterest.removeAttribute('style');
                inputInterest.disabled = false;
                inputInterest.required = true;
            } else if (type === 'LATE') {
                wrapLate.removeAttribute('style');
                inputLate.disabled = false;
                inputLate.required = true;
            } else if (type === 'TOPUP') {
                wrapTopup.removeAttribute('style');
                wrapTopupCapital.removeAttribute('style');
                inputTopup.disabled        = false;
                inputTopupCapital.disabled = false;
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

        // ================= ADD PAYMENT MODAL =================
        const GREY_STYLE_ADD = 'opacity:0.45; pointer-events:none; user-select:none;';
        let addLoan = null;          // currently selected loan info
        let addSearchTimeout;

        function openAddPaymentModal() {
            document.getElementById('form-add-payment').reset();
            addLoan = null;
            document.getElementById('add-loan-code').value = '';
            document.getElementById('add-collection-type').value = '';
            document.getElementById('add-payment-method-id').value = '';
            document.getElementById('add-loan-dropdown').style.display = 'none';
            document.getElementById('add-payment-type').disabled = true;
            document.getElementById('display-group').textContent = '-';
            document.getElementById('display-balance').textContent = '0.00';
            document.getElementById('display-next-amount').textContent = '';
            document.getElementById('display-due-date').textContent = '';
            document.getElementById('display-late-balance').textContent = '0.00';
            document.getElementById('display-interest-amount').textContent = '0.00';
            document.getElementById('display-pay-date').textContent = formatYnj(new Date());
            document.getElementById('add-input-schedule').innerHTML = '<option value="0">-- Select Schedule --</option>';
            applyAddPaymentType('DEFAULT');
            new bootstrap.Modal('#modal-add-payment').show();
        }

        function formatYnj(d) {
            return d.getFullYear() + '-' + (d.getMonth() + 1) + '-' + d.getDate();
        }

                // ---- loan search ----
        const addLoanSearch   = document.getElementById('add-loan-search');
        const addLoanDropdown = document.getElementById('add-loan-dropdown');

        addLoanSearch.addEventListener('input', function () {
            const query = this.value;
            clearTimeout(addSearchTimeout);

            // typing again invalidates previous selection
            if (addLoan) {
                addLoan = null;
                document.getElementById('add-loan-code').value = '';
                document.getElementById('add-payment-method-id').value = '';
                document.getElementById('add-collection-type').value = '';
                document.getElementById('add-payment-type').disabled = true;
                applyAddPaymentType('DEFAULT');
            }

            if (query.trim().length < 3) {
                addLoanDropdown.style.display = 'none';
                return;
            }
            addSearchTimeout = setTimeout(() => searchAddLoans(query), 500);
        });

        // Enter loads the typed code immediately (and does not submit the form)
        addLoanSearch.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                clearTimeout(addSearchTimeout);
                if (this.value.trim().length >= 3) searchAddLoans(this.value);
            }
        });

        function searchAddLoans(query) {
            query = query.trim();

            fetch(`{{ route('loan.search_loan') }}?search=${encodeURIComponent(query)}`, {
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            })
            .then(r => r.json())
            .then(loans => {
                // ignore stale responses (user already typed something else)
                if (addLoanSearch.value.trim() !== query) return;

                // exact match on the typed code -> load it automatically
                const exact = loans.find(l => l.loan_code.toLowerCase() === query.toLowerCase());
                if (exact) {
                    addLoanSearch.value = exact.loan_code;
                    addLoanDropdown.style.display = 'none';
                    loadAddLoanInfo(exact.loan_code);
                    return;
                }

                addLoanDropdown.innerHTML = '';
                if (loans.length === 0) {
                    addLoanDropdown.innerHTML = '<div class="dropdown-item-text">No loan found</div>';
                } else {
                    loans.forEach(loan => {
                        const item = document.createElement('a');
                        item.className = 'dropdown-item';
                        item.href = '#';
                        item.innerHTML = `<strong>${loan.loan_code}</strong>`;
                        item.addEventListener('click', function (e) {
                            e.preventDefault();
                            addLoanSearch.value = loan.loan_code;
                            addLoanDropdown.style.display = 'none';
                            loadAddLoanInfo(loan.loan_code);
                        });
                        addLoanDropdown.appendChild(item);
                    });
                }
                addLoanDropdown.style.display = 'block';
            })
            .catch(() => {
                addLoanDropdown.innerHTML = '<div class="dropdown-item-text text-danger">Search failed</div>';
                addLoanDropdown.style.display = 'block';
            });
        }

        document.addEventListener('click', function (e) {
            if (!addLoanSearch.contains(e.target) && !addLoanDropdown.contains(e.target)) {
                addLoanDropdown.style.display = 'none';
            }
        });

        // ---- load selected loan details ----
        function loadAddLoanInfo(loanCode) {
            fetch(`{{ route('payment.loan_info') }}?loan_code=${encodeURIComponent(loanCode)}`, {
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            })
            .then(r => r.json())
            .then(info => {
                if (!info.success) {
                    setDefaultSwal('error', '', info.message);
                    return;
                }
                addLoan = info;

                document.getElementById('add-loan-code').value = info.loan_code;
                document.getElementById('add-collection-type').value = info.interest_group ?? '';

                document.getElementById('display-group').textContent = info.interest_group ?? 'No SKIM';
                document.getElementById('display-balance').textContent = info.balance ?? '0.00';
                document.getElementById('display-next-amount').textContent = info.next_due_amount ?? '';
                document.getElementById('display-due-date').textContent = info.next_due_date ?? '';
                document.getElementById('display-late-balance').textContent = info.late_balance ?? '0.00';
                document.getElementById('display-interest-amount').textContent = info.interest_amount ?? '0.00';

                // build schedule options
                const max = info.interest_group === 'SKIM A' ? 12 : parseInt(info.schedule_count) || 0;
                let opts = '<option value="0">-- Select Schedule --</option>';
                for (let i = 1; i <= max; i++) opts += `<option value="${i}">${i}</option>`;
                document.getElementById('add-input-schedule').innerHTML = opts;

                // payment method from company
                fetch(`{{ route('payment_method.search_payment_methods') }}?company_code=${encodeURIComponent(info.company_code)}`, {
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                })
                .then(r => r.json())
                .then(methods => {
                    document.getElementById('add-payment-method-id').value = methods.length ? methods[0].id : '';
                })
                .catch(() => { document.getElementById('add-payment-method-id').value = ''; });

                document.getElementById('add-payment-type').disabled = false;
                document.getElementById('add-payment-type').value = 'DEFAULT';
                updatePaymentTypeOptions();
            })
            .catch(() => setDefaultSwal('error', '', 'Failed to load loan details.'));
        }

        // ---- schedule multiplier ----
        function applyScheduleMultiplier(schedule) {
            if (!addLoan) return;
            const multiplier     = parseInt(schedule) || 0;
            const collectionType = document.getElementById('add-collection-type').value ?? '';
            const type           = document.getElementById('add-payment-type').value;

            if (multiplier <= 0) {
                if (type === 'INTEREST') {
                    $('#add-input-payment-interest').val('0');
                } else {
                    $('#add-input-payment-amount').val('0');
                }
                document.getElementById('display-due-date').textContent = '';
                return;
            }

            if (collectionType === 'SKIM A' && type === 'INTEREST') {
                $('#add-input-payment-interest').val((parseFloat(addLoan.interest_amount) * multiplier).toFixed(2));
            } else {
                $('#add-input-payment-amount').val((parseFloat(addLoan.first_payment) * multiplier).toFixed(2));
            }

            if (addLoan.next_due_date) {
                const base = new Date(addLoan.next_due_date);
                base.setMonth(base.getMonth() + multiplier);
                document.getElementById('display-due-date').textContent =
                    base.getFullYear() + '-' +
                    String(base.getMonth() + 1).padStart(2, '0') + '-' +
                    String(base.getDate()).padStart(2, '0');
            }
        }

        // ---- payment type ----
        function applyAddPaymentType(type) {
            const wrapPayment      = document.getElementById('add-wrap-payment');
            const wrapLate         = document.getElementById('add-wrap-late');
            const wrapInterest     = document.getElementById('add-wrap-interest');
            const wrapTopupCapital = document.getElementById('add-wrap-topup-capital');
            const wrapTopup        = document.getElementById('add-wrap-topup');
            const wrapSchedule     = document.getElementById('add-wrap-schedule');

            const inputPayment      = document.getElementById('add-input-payment-amount');
            const inputLate         = document.getElementById('add-input-payment-late');
            const inputInterest     = document.getElementById('add-input-payment-interest');
            const inputTopupCapital = document.getElementById('add-input-topup-capital');
            const inputTopup        = document.getElementById('add-input-topup');
            const inputDiscount     = document.querySelector('#add-wrap-payment [name="discount_amount"]');
            const inputSchedule     = document.getElementById('add-input-schedule');

            const isSkimB = (document.getElementById('add-collection-type').value ?? '') === 'SKIM B';

            [wrapPayment, wrapLate, wrapInterest, wrapTopup, wrapTopupCapital].forEach(w => w.setAttribute('style', GREY_STYLE_ADD));
            [inputPayment, inputLate, inputInterest, inputTopup, inputTopupCapital, inputDiscount].forEach(i => {
                i.disabled = true;
                i.required = false;
                i.value = 0;
            });
            inputSchedule.value = '0';

            if (type === 'CCM') {
                wrapPayment.removeAttribute('style');
                inputPayment.disabled = false;
                inputPayment.required = true;
                inputDiscount.disabled = false;
                wrapSchedule.style.display = isSkimB ? 'block' : 'none';

            } else if (type === 'INTEREST') {
                wrapInterest.removeAttribute('style');
                inputInterest.disabled = false;
                inputInterest.required = true;
                wrapSchedule.style.display = 'block';

            } else if (type === 'LATE') {
                wrapLate.removeAttribute('style');
                inputLate.disabled = false;
                inputLate.required = true;
                wrapSchedule.style.display = 'none';

            } else if (type === 'TOPUP') {
                wrapTopup.removeAttribute('style');
                wrapTopupCapital.removeAttribute('style');
                inputTopup.disabled = false;
                inputTopupCapital.disabled = false;
                wrapSchedule.style.display = 'none';

            } else {
                wrapSchedule.style.display = 'none';
            }
        }

        function updatePaymentTypeOptions() {
            const type = ($('#add-collection-type').val() || '').toUpperCase();
            const $select = $('#add-payment-type');

            $select.find('option').show();
            if (type === 'SKIM B') {
                $select.find('option[value="TOPUP"]').hide();
                $select.find('option[value="INTEREST"]').hide();
            }
            if (type === 'SKIM A') {
                $select.find('option[value="LATE"]').hide();
            }
            if ($select.find('option:selected').is(':hidden')) {
                $select.val('DEFAULT');
            }
            applyAddPaymentType($select.val());
        }

        // ---- pay date display ----
        document.getElementById('add-pay-date').addEventListener('change', function () {
            document.getElementById('display-pay-date').textContent = formatYnj(new Date(this.value));
            const scheduleVal = document.getElementById('add-input-schedule').value;
            if (scheduleVal && scheduleVal != 0) applyScheduleMultiplier(scheduleVal);
        });

        // ---- submit ----
        $('#form-add-payment').on('submit', function (e) {
            e.preventDefault();

            if (!document.getElementById('add-loan-code').value) {
                setDefaultSwal('error', '', 'Please search and select a loan first.');
                return;
            }
            if (!document.getElementById('add-payment-method-id').value) {
                setDefaultSwal('error', '', 'No payment method found for this loan\'s company.');
                return;
            }

            let formData = new FormData(this);
            $.ajax({
                url: "{{ route('payment.store') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                success: function (response) {
                    if (response.success == true) {
                        setReloadSwal('success', '', response.message);
                    } else {
                        setDefaultSwal('error', '', response.message);
                    }
                },
                error: function () {
                    setDefaultSwal('error', '', 'There is something wrong, please try again.');
                }
            });
        });
    </script>
@endsection