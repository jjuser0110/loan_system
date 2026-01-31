@extends('layouts.app')

@section('content')
<header class="page-header">
    <h2>Create New Payment</h2>
</header>
@include('layouts.flash-message')
<div class="row">
    <div class="col-md-12 col-lg-12 col-xl-8 col-xxl-6">
        <section class="card">
            <form class="theme-form mega-form" enctype="multipart/form-data" id="form-create-payment">
                @csrf
                <div class="card-body">
                    <h4>New Payment</h4>
                    <div class="row">
                        <div class="col-md-12 col-lg-6 col-xl-6 col-xxl-6">
                            <label class="col-form-label">Loan Code</label>
                            <input type="text" class="form-control" id="loan-search" name="loan_code" value="{{ $loan?->loan_code ?? '' }}" placeholder="L000001" autocomplete="off">
                            <div id="loan-dropdown" class="dropdown-menu col-md-5 col-10" style="display:none; max-height: 200px; overflow-y: auto; padding:0;"></div>
                        </div>

                        <div class="col-md-12 col-lg-6 col-xl-6 col-xxl-6 mb-3">
                            <label class="col-form-label">System Code / Name</label>
                            <input type="text" class="form-control" id="customer-code" value="{{ $loan?->customer->customer_code ? $loan->customer->customer_code.'/' : '' }} {{ $loan?->customer->customer_name ?? '' }}" autocomplete="off" disabled>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 col-lg-6 col-xl-12 col-xxl-6">
                            <div class="col-md-12 mb-3">
                                <label class="col-form-label">Payment / Capital Amount</label>
                                <input type="number" class="form-control" id="input-payment-amount" name="payment_amount" placeholder="10000.00" step="0.01" autocomplete="off" required>
                                <p class="p-note" id="loan-payment-balance">{{ $loan?->balance ?? ''}}</p>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="col-form-label">Discount Amount</label>
                                <input type="number" class="form-control" name="discount_amount" placeholder="1000.00" autocomplete="off">
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="col-form-label">Pay Late Charge</label>
                                <input type="number" class="form-control" id="input-payment-late" name="late_paid_amount" placeholder="10000.00" step="0.01"autocomplete="off">
                                 <p class="p-note" id="loan-late-balance">{{ $loan?->late_balance ?? ''}}</p>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="col-form-label">Pay Interest</label>
                                <input type="number" class="form-control" id="input-payment-interest" name="interest_paid_amount" placeholder="10000.00" step="0.01" autocomplete="off">
                                <p class="p-note" id="loan-interest-balance">{{ $loan?->interest_balance ?? ''}}</p>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="col-form-label">Collection</label>
                                <select class="form-control" name="collection_type" required>
                                    <option value="SKIM A">SKIM A</option>
                                    <option value="SKIM B">SKIM B</option>
                                </select>
                            </div>
                            <!-- 
                            <div class="col-md-12 mb-3">
                                <label class="col-form-label">Cheque</label>
                                <input type="text" class="form-control" name="cheque" autocomplete="off">
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="col-form-label">Bank</label>
                                <input type="text" class="form-control" name="bank" autocomplete="off">
                            </div> -->

                            <div class="col-md-12 mb-3">
                                <label class="col-form-label">Payment Method</label>
                                <select class="form-control" id="payment_method_id" name="payment_method_id" disabled required>
                                    <option>Please insert loan code first</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <a href="{{route('loan.index')}}" class="btn btn-secondary">Back</a>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </section>
    </div>
</div>
@endsection

@section('scripts')
    <script>
        let searchTimeout;
        const searchInput = document.getElementById('loan-search');
        const dropdown = document.getElementById('loan-dropdown');
        let selected = false;
        searchInput.addEventListener('input', function() {
            const query = this.value;
            document.getElementById('customer-code').value = ``;
            document.getElementById('loan-payment-balance').innerHTML = ``;
            document.getElementById('loan-late-balance').innerHTML = ``;
            document.getElementById('loan-interest-balance').innerHTML = ``;
            selected = false;
            clearTimeout(searchTimeout);
            if (query.length < 1) {
                dropdown.style.display = 'none';
                return;
            }
            searchTimeout = setTimeout(() => {
                searchLoan(query);
            }, 500);
        });

        function searchLoan(query) {
            fetch(`{{ route('loan.search_loan') }}?search=${encodeURIComponent(query)}`, {
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
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
                        item.addEventListener('click', function(e) {
                            e.preventDefault();
                            searchInput.value = loan.loan_code;
                            selected = loan;
                            document.getElementById('customer-code').value = `${loan.customer_code} / ${loan.customer_name}`;
                            document.getElementById('loan-payment-balance').innerHTML = `Outstanding: ${loan.total_payment_balance}`;
                            document.getElementById('loan-late-balance').innerHTML = `Outstanding: ${loan.total_late_balance}`;
                            document.getElementById('loan-interest-balance').innerHTML = `Outstanding: ${loan.total_interest_balance}`;
                            dropdown.style.display = 'none';
                            setupPaymentMethod(loan.company_code);
                        });
                        dropdown.appendChild(item);
                    });
                }   
                dropdown.style.display = 'block';
            })
            .catch(error => {
                console.error('Search failed:', error);
                dropdown.innerHTML = '<div class="dropdown-item-text text-danger">Search failed</div>';
                dropdown.style.display = 'block';
            });
        }

        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.style.display = 'none';
            }
        });

        function onSubmitForm() {
            var form = document.querySelector('form');
            if (form.checkValidity()) {
                showLoading();
                return true;
            } else {
                return false;
            }
        }

        $('#form-create-payment').on('submit', function (e) {
            e.preventDefault();
            let form = $(this);
            let formData = new FormData(this);
            $.ajax({
                url:  "{{ route('payment.store') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}"},
                success: function (response) {
                    if(response.success == true){
                        setRedirectSwal("success", '', response.message, "{{ route('payment.index') }}");
                    }
                    else{
                        setDefaultSwal("error", "", response.message);
                    }
                },
                error: function (xhr) {
                    setDefaultSwal("error", "", 'There is something wrong, please try again.');
                }
            });
        });

        function setupPaymentMethod(x){
            let d = document.getElementById('payment_method_id');
            d.disabled = true;
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
                                d.innerHTML += `<option value="${method.id}">${method.bank_name} / ${method.account_no} (RM ${formatCredit(method.amount)})</option>`;
                            });
                            d.disabled = false;
                        }   
                    })
                    .catch(error => {
                         d.innerHTML = '<option>-- Failed to get methods. --</option>';
                    });
            }
            else{ 
               d.innerHTML = "<option>Please select customer first</option>";
            }
        }
        @if($loan?->customer->customer_code )
        document.addEventListener('DOMContentLoaded', function(){
            setupPaymentMethod("{{ $loan?->company->company_code }}")
        })
        @endif
    </script>
@endsection
