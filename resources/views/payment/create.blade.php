@extends('layouts.app')

@section('content')
<header class="page-header">
    <h2>{{ __('table.create_new_payment') }}</h2>
</header>
@include('layouts.flash-message')
<div class="row">
    <div class="col-md-12 col-lg-12 col-xl-8 col-xxl-6">
        <section class="card">
            <form class="theme-form mega-form" enctype="multipart/form-data" id="form-create-payment">
                @csrf
                <div class="card-body">
                    <h4>{{ __('table.new_payment') }}</h4>

                    {{-- Loan search row --}}
                    <div class="row">
                        <div class="col-md-12 col-lg-6 col-xl-6 col-xxl-6">
                            <label class="col-form-label">{{ __('table.loan_code') }}</label>
                            <input type="text" class="form-control" id="loan-search" name="loan_code" value="{{ $loan?->loan_code ?? '' }}" placeholder="L000001" autocomplete="off">
                            <div id="loan-dropdown" class="dropdown-menu col-md-5 col-10" style="display:none; max-height:200px; overflow-y:auto; padding:0;"></div>
                        </div>
                        <div class="col-md-12 col-lg-6 col-xl-6 col-xxl-6 mb-3">
                            <label class="col-form-label">{{ __('table.system_code/name') }}</label>
                            <input type="text" class="form-control" id="customer-code" value="{{ $loan?->customer->customer_code ? $loan->customer->customer_code.'/' : '' }} {{ $loan?->customer->customer_name ?? '' }}" autocomplete="off" disabled>
                        </div>
                    </div>

                    {{-- Payment type selector --}}
                    <div class="col-md-12 mb-3">
                        <label class="col-form-label">{{ __('table.payment_type') }}</label>
                        <select class="form-control" id="payment_type" name="payment_type" required>
                            <option value="CCM">CCM</option>
                            <option value="INTEREST">{{ __('table.interest') }}</option>
                            <option value="TOPUP">{{ __('table.top_up') }}</option>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-12 col-lg-6 col-xl-12 col-xxl-6">

                            {{-- TOP-UP ONLY: top_up_amount --}}
                            <div class="col-md-12 mb-3 field-topup" style="display:none;">
                                <label class="col-form-label">{{ __('table.top_up_amount') }}</label>
                                <input type="number" class="form-control" id="input-topup-amount" name="top_up" placeholder="5000.00" step="0.01" autocomplete="off">
                                <p class="p-note">{{ __('table.enter_top_up_amount') }}</p>
                            </div>

                            {{-- CCM ONLY: payment/capital --}}
                            <div class="col-md-12 mb-3 field-ccm" id="wrap-payment-amount">
                                <label class="col-form-label">{{ __('table.payment/capital_amount') }}</label>
                                <input type="number" class="form-control" id="input-payment-amount" name="payment_amount" placeholder="10000.00" step="0.01" autocomplete="off">
                                <p class="p-note" id="loan-payment-balance">{{ __('table.outstanding') }}: {{ $loan?->outstanding ?? '' }} &nbsp;&nbsp;&nbsp; {{ __('table.next_payment') }}: {{ $loan?->next_due_amount ?? '' }} <br> {{ __('table.due_date') }}: {{ $loan?->next_due_date ?? '' }}</p>
                            </div>

                            {{-- CCM ONLY: discount --}}
                            <div class="col-md-12 mb-3 field-ccm" id="wrap-discount-amount">
                                <label class="col-form-label">{{ __('table.discount_amount') }}</label>
                                <input type="number" class="form-control" name="discount_amount" placeholder="1000.00" autocomplete="off">
                            </div>

                            {{-- GREYED for non-CCM: late charge --}}
                            <div class="col-md-12 mb-3 field-ccm-grey" id="wrap-late-amount">
                                <label class="col-form-label">{{ __('table.pay_late_charge') }}</label>
                                <input type="number" class="form-control" id="input-payment-late" name="late_paid_amount" placeholder="10000.00" step="0.01" autocomplete="off">
                                <p class="p-note" id="loan-late-balance">{{ $loan?->late_balance ?? '' }}</p>
                            </div>

                            {{-- INTEREST type: interest field active; CCM: greyed --}}
                            <div class="col-md-12 mb-3" id="wrap-interest-amount">
                                <label class="col-form-label">{{ __('table.pay_interest') }}</label>
                                <input type="number" class="form-control" id="input-payment-interest" name="interest_paid_amount" placeholder="10000.00" step="0.01" autocomplete="off">
                                <p class="p-note" id="loan-interest-balance">{{ $loan?->interest_balance ?? '' }}</p>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="col-form-label">{{ __('table.collection') }}</label>
                                <select class="form-control" name="collection_type" required>
                                    <option value="SKIM A">{{ __('table.skim_A') }}</option>
                                    <option value="SKIM B">{{ __('table.skim_B') }}</option>
                                </select>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="col-form-label">{{ __('table.payment_method') }}</label>
                                <select class="form-control" id="payment_method_id" name="payment_method_id" disabled required>
                                    <option>{{ __('table.please_insert_loan_code_first') }}</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="col-form-label">{{ __('table.remark') }}</label>
                                <textarea class="form-control" id="remark" name="remark" rows="3" placeholder="Enter remarks..."></textarea>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <a href="{{ route('loan.index') }}" class="btn btn-secondary">{{ __('table.back') }}</a>
                    <button type="submit" class="btn btn-primary">{{ __('table.submit') }}</button>
                </div>
            </form>
        </section>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // ─── Loan search ────────────────────────────────────────────────
    let searchTimeout;
    const searchInput = document.getElementById('loan-search');
    const dropdown    = document.getElementById('loan-dropdown');
    let selected = false;

    searchInput.addEventListener('input', function () {
        const query = this.value;
        document.getElementById('customer-code').value = '';
        document.getElementById('loan-payment-balance').innerHTML = '';
        document.getElementById('loan-late-balance').innerHTML    = '';
        document.getElementById('loan-interest-balance').innerHTML = '';
        selected = false;
        clearTimeout(searchTimeout);
        if (query.length < 1) { dropdown.style.display = 'none'; return; }
        searchTimeout = setTimeout(() => searchLoan(query), 500);
    });

    function searchLoan(query) {
        fetch(`{{ route('loan.search_loan') }}?search=${encodeURIComponent(query)}`, {
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        })
        .then(r => r.json())
        .then(loans => {
            dropdown.innerHTML = '';
            if (loans.length === 0) {
                dropdown.innerHTML = '<div class="dropdown-item-text">No loan found</div>';
            } else {
                loans.forEach(loan => {
                    const item = document.createElement('a');
                    item.className = 'dropdown-item';
                    item.href = '#';
                    item.innerHTML = `<strong>${loan.loan_code}</strong>`;
                    item.addEventListener('click', function (e) {
                        e.preventDefault();
                        searchInput.value = loan.loan_code;
                        selected = loan;
                        document.getElementById('customer-code').value = `${loan.customer_code} / ${loan.customer_name}`;
                        document.getElementById('loan-payment-balance').innerHTML =
                            `Outstanding: ${loan.outstanding ?? ''} &nbsp;&nbsp;&nbsp; Next Payment: ${loan.next_due_amount ?? ''} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Due Date: ${loan.next_due_date ?? ''}`;
                        document.getElementById('loan-late-balance').innerHTML    = `Outstanding: ${loan.total_late_balance}`;
                        document.getElementById('loan-interest-balance').innerHTML = `Outstanding: ${loan.total_interest_balance}`;
                        dropdown.style.display = 'none';
                        setupPaymentMethod(loan.company_code);
                    });
                    dropdown.appendChild(item);
                });
            }
            dropdown.style.display = 'block';
        })
        .catch(err => {
            console.error('Search failed:', err);
            dropdown.innerHTML = '<div class="dropdown-item-text text-danger">Search failed</div>';
            dropdown.style.display = 'block';
        });
    }

    document.addEventListener('click', function (e) {
        if (!searchInput.contains(e.target) && !dropdown.contains(e.target))
            dropdown.style.display = 'none';
    });

    // ─── Payment type toggle ─────────────────────────────────────────
    const GREY_STYLE = 'opacity:0.45; pointer-events:none; user-select:none;';

    function applyPaymentType(type) {
        const wrapPayment  = document.getElementById('wrap-payment-amount');
        const wrapDiscount = document.getElementById('wrap-discount-amount');
        const wrapLate     = document.getElementById('wrap-late-amount');
        const wrapInterest = document.getElementById('wrap-interest-amount');
        const wrapTopup    = document.querySelector('.field-topup');

        const inputPayment  = document.getElementById('input-payment-amount');
        const inputLate     = document.getElementById('input-payment-late');
        const inputInterest = document.getElementById('input-payment-interest');
        const inputTopup    = document.getElementById('input-topup-amount');

        // reset all first
        [wrapPayment, wrapDiscount, wrapLate, wrapInterest, wrapTopup].forEach(w => w.removeAttribute('style'));
        [inputPayment, inputLate, inputInterest, inputTopup].forEach(i => {
            i.disabled = false;
            i.required = false;
        });

        if (type === 'CCM') {
            wrapTopup.setAttribute('style', GREY_STYLE);
            inputTopup.disabled = true;
            inputTopup.value = '';

            inputPayment.required = true;

            wrapInterest.setAttribute('style', GREY_STYLE);
            inputInterest.disabled = true;
            inputInterest.value = '';

        } else if (type === 'INTEREST') {
            wrapTopup.setAttribute('style', GREY_STYLE);
            inputTopup.disabled = true;
            inputTopup.value = '';

            wrapPayment.setAttribute('style', GREY_STYLE);
            wrapDiscount.setAttribute('style', GREY_STYLE);
            inputPayment.disabled = true;
            inputPayment.value = '';

            wrapLate.setAttribute('style', GREY_STYLE);
            inputLate.disabled = true;
            inputLate.value = '';

            inputInterest.required = true;

        } else if (type === 'TOPUP') {
            wrapPayment.setAttribute('style', GREY_STYLE);
            wrapDiscount.setAttribute('style', GREY_STYLE);
            inputPayment.disabled = true;
            inputPayment.value = '';

            wrapLate.setAttribute('style', GREY_STYLE);
            inputLate.disabled = true;
            inputLate.value = '';

            wrapInterest.setAttribute('style', GREY_STYLE);
            inputInterest.disabled = true;
            inputInterest.value = '';

            inputTopup.required = true;
        }
    }

    document.getElementById('payment_type').addEventListener('change', function () {
        applyPaymentType(this.value);
    });

    // Init on page load
    applyPaymentType('CCM');

    // ─── Form submit ─────────────────────────────────────────────────
    $('#form-create-payment').on('submit', function (e) {
        e.preventDefault();
        let formData = new FormData(this);
        $.ajax({
            url: "{{ route('payment.store') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
            success: function (response) {
                if (response.success === true) {
                    setRedirectSwal("success", '', response.message, "{{ route('payment.index') }}");
                } else {
                    setDefaultSwal("error", "", response.message);
                }
            },
            error: function () {
                setDefaultSwal("error", "", 'There is something wrong, please try again.');
            }
        });
    });

    // ─── Payment method setup ────────────────────────────────────────
    function setupPaymentMethod(x) {
        let d = document.getElementById('payment_method_id');
        d.disabled = true;
        if (x) {
            fetch(`{{ route('payment_method.search_payment_methods') }}?company_code=${encodeURIComponent(x)}`, {
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            })
            .then(r => r.json())
            .then(methods => {
                d.innerHTML = '';
                if (methods.length === 0) {
                    d.innerHTML = '<option>No payment method found.</option>';
                } else {
                    methods.forEach(m => {
                        d.innerHTML += `<option value="${m.id}">${m.bank_name} / ${m.account_no} (RM ${formatCredit(m.amount)})</option>`;
                    });
                    d.disabled = false;
                }
            })
            .catch(() => { d.innerHTML = '<option>-- Failed to get methods. --</option>'; });
        } else {
            d.innerHTML = '<option>Please select customer first</option>';
        }
    }

    @if($loan?->customer->customer_code)
    document.addEventListener('DOMContentLoaded', function () {
        setupPaymentMethod("{{ $loan?->company->company_code }}");
    });
    @endif
</script>
@endsection