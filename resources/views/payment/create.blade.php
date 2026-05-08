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
                            <option value="CCM">Payment / CCM</option>
                            <option value="INTEREST">Pay SKIM A Interest</option>
                            <option value="DISCOUNT">Discount Amount</option>
                            <option value="LATE">Pay Late</option>
                            <option value="TOPUP">Top Up</option>
                        </select>
                    </div>

                    <div class="row">
                        {{-- CCM ONLY: payment/capital --}}
                        <div class="col-md-12 col-lg-6 col-xl-6 col-xxl-6 mb-3 id="wrap-payment-amount">
                            <label class="col-form-label">{{ __('table.payment/capital_amount') }}</label>
                            <input type="number" class="form-control" id="input-payment-amount" name="payment_amount" placeholder="10000.00" step="0.01" autocomplete="off">
                            <p class="p-note" id="loan-payment-balance">{{ __('table.outstanding') }}: {{ $loan?->outstanding ?? '' }} &nbsp;&nbsp;&nbsp; {{ __('table.next_payment') }}: {{ $loan?->next_due_amount ?? '' }} <br> {{ __('table.due_date') }}: {{ $loan?->next_due_date ?? '' }}</p>
                        </div>

                        {{-- TOP-UP ONLY: top_up_amount --}}
                        <div class="col-md-12 col-lg-6 col-xl-6 col-xxl-6 mb-3 field-topup">
                            <label class="col-form-label">{{ __('table.top_up_amount') }}</label>
                            <input type="number" class="form-control" id="input-topup-amount" name="top_up" placeholder="5000.00" step="0.01" autocomplete="off">
                            <p class="p-note">{{ __('table.enter_top_up_amount') }}</p>
                        </div>

                        {{-- CCM ONLY: discount --}}
                        <div class="col-md-12 mb-3 field-ccm" id="wrap-discount-amount">
                            <label class="col-form-label">{{ __('table.discount_amount') }}</label>
                            <input type="number" class="form-control" id="input-discount-amount" name="discount_amount" placeholder="1000.00" autocomplete="off">
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

                        {{-- Hidden collection type - auto set from loan's interest_group --}}
                        <input type="hidden" name="collection_type" id="collection_type" value="{{ $loan?->interest_group ?? '' }}">

                        {{-- Hidden payment method --}}
                        <input type="hidden" name="payment_method_id" id="payment_method_id" value="">

                        <div class="col-12">
                            <label class="col-form-label">{{ __('table.remark') }}</label>
                            <textarea class="form-control" id="remark" name="remark" rows="3" placeholder="Enter remarks..."></textarea>
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
        document.getElementById('collection_type').value = '';
        document.getElementById('payment_method_id').value = '';
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

                        // Auto set collection type from loan's interest_group
                        document.getElementById('collection_type').value = loan.interest_group ?? 'SKIM A';

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
        const inputDiscount = document.getElementById('input-discount-amount');

        // Reset all first — grey everything
        [wrapPayment, wrapDiscount, wrapLate, wrapInterest, wrapTopup].forEach(w => {
            w.setAttribute('style', GREY_STYLE);
        });
        [inputPayment, inputLate, inputInterest, inputTopup, inputDiscount].forEach(i => {
            i.disabled = true;
            i.required = false;
            i.value = '';
        });

        // Only unlock the relevant field
        if (type === 'CCM') {
            wrapPayment.removeAttribute('style');
            inputPayment.disabled = false;
            inputPayment.required = true;

        } else if (type === 'INTEREST') {
            wrapInterest.removeAttribute('style');
            inputInterest.disabled = false;
            inputInterest.required = true;

        } else if (type === 'DISCOUNT') {
            wrapDiscount.removeAttribute('style');
            inputDiscount.disabled = false;
            inputDiscount.required = true;

        } else if (type === 'LATE') {
            wrapLate.removeAttribute('style');
            inputLate.disabled = false;
            inputLate.required = true;

        } else if (type === 'TOPUP') {
            wrapTopup.removeAttribute('style');
            inputTopup.disabled = false;
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
        if (x) {
            fetch(`{{ route('payment_method.search_payment_methods') }}?company_code=${encodeURIComponent(x)}`, {
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            })
            .then(r => r.json())
            .then(methods => {
                if (methods.length > 0) {
                    document.getElementById('payment_method_id').value = methods[0].id;
                } else {
                    document.getElementById('payment_method_id').value = '';
                }
            })
            .catch(err => {
                console.error('Failed to get payment methods:', err);
                document.getElementById('payment_method_id').value = '';
            });
        } else {
            document.getElementById('payment_method_id').value = '';
        }
    }

    @if($loan?->customer->customer_code)
    document.addEventListener('DOMContentLoaded', function () {
        setupPaymentMethod("{{ $loan?->company->company_code }}");
    });
    @endif
</script>
@endsection