@extends('layouts.app')

@section('content')
<header class="page-header">
    <h2>@if (isset($loan)) Edit @else Create @endif Loan</h2>
</header>
@include('layouts.flash-message')
<div class="row">
    <div class="col-lg-12 mb-3">
        <section class="card">
            <form class="theme-form mega-form" enctype="multipart/form-data" id="form-create-loan">
                @csrf
                <div class="card-body">
                    <h4>Loan Details</h4>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="col-form-label">Customer Code</label>
                            <input type="text" class="form-control" id="customer_search" name="customer_code" value="{{ $customer?->customer_code ?? '' }}" placeholder="C000001" autocomplete="off">
                            <div id="customer_dropdown" class="dropdown-menu col-md-5 col-10" style="display:none; max-height: 200px; overflow-y: auto; padding:0;"></div>
                        </div>

                         <div class="col-md-6 mb-3">
                            <label class="col-form-label">Customer Name</label>
                            <input type="text" class="form-control" id="customer-name" value="{{ $customer?->customer_name ?? '' }}" autocomplete="off" disabled>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="col-md-12 mb-3">
                                <label class="col-form-label">Company Code</label>
                                <input type="text" class="form-control" id="company-code" value="{{ $company?->company_code ?? '' }}" autocomplete="off" disabled>
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <label class="col-form-label">Year / Month</label>
                                <input type="date" class="form-control" name="year_month" required>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="col-form-label">Interest Group</label>
                                <select class="form-control" id="interest-group" name="interest_group" onchange="changeInterestGroup()" required>
                                    <option value="SKIM A">SKIM A</option>
                                    <option value="SKIM B">SKIM B</option>
                                </select>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="col-form-label">Loan Amount</label>
                                <input type="number" class="form-control" id="loan-amount" name="loan_amount" placeholder="10000.00" step="0.01" onchange="updateCapital()" autocomplete="off" required>
                            </div>

                            <div class="row col-md-12 mb-3">
                                <div class="col-8">
                                    <label class="col-form-label">Interest Rate (%)</label>
                                    <input type="number" class="form-control" id="interest-rate" name="interest_rate" placeholder="5.000" step="0.0001" autocomplete="off" required>
                                </div>
                                <div class="col-4">
                                    <label class="col-form-label" style="opacity:0;padding-left:0;width:100%">-</label>
                                    <button type="button" class="btn btn-primary" id="btn-calculate-interest" onclick="calculateInterest()" style="font-size: 12px;">Calculate Interest</button>
                                </div>
                            </div>

                            <div class="col-md-12 mb-3 input-wrapper" style="display:none">
                                <label class="col-form-label">Loan Term</label>
                                <input type="number" class="form-control" id="loan-term" name="loan_term" placeholder="12" autocomplete="off">
                            </div>
                            
                            <div class="row" style="padding-left:calc(var(--bs-gutter-x) * 0.5);padding-right:calc(var(--bs-gutter-x) * 0.5);">
                                <div class="col-12" style="background:#f1f1f1">
                                    <div class="col-md-12 mb-3 input-wrapper" style="display:none">
                                        <label class="col-form-label">First Payment Amount</label>
                                        <input type="number" class="form-control" id="first-payment" name="first_payment" placeholder="0.00" step="0.01"  autocomplete="off" required>
                                    </div>

                                    <div class="col-md-12 mb-3 input-wrapper">
                                        <label class="col-form-label">Installment Amount</label>
                                        <input type="number" class="form-control" id="installment" name="installment" placeholder="0.00" step="0.01"  autocomplete="off" required>
                                    </div>

                                
                                    <div class="col-md-12 mb-3 input-wrapper" style="display:none">
                                        <label class="col-form-label">Last Payment Amount</label>
                                        <input type="number" class="form-control" id="last-payment" name="last_payment" placeholder="0.00" step="0.01"  autocomplete="off" required>
                                    </div>

                                    <div class="col-md-12 mb-3" style="text-align:center">
                                        <button style="min-width:80%;margin:auto" type="button" class="btn btn-primary" id="btn-calculate-payment" onclick="calculatePayment()">Calculate Payment</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="col-md-12 mb-3">
                                <label class="col-form-label">Processing Fee</label>
                                <input type="number" class="form-control" id="processing-fee" name="processing_fee" onchange="updateCapital()" placeholder="100.00" autocomplete="off">
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <label class="col-form-label">Stamp Fee</label>
                                <input type="number" class="form-control" id="stamp-fee" name="stamp_fee" onchange="updateCapital()" placeholder="100.00" autocomplete="off">
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="col-form-label">Capital</label>
                                <input type="number" class="form-control" id="capital" name="capital" placeholder="10000.00" autocomplete="off" disabled>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="col-form-label">Alternate Code</label>
                                <input type="text" class="form-control"  name="alternate_code" placeholder="CODE100001" autocomplete="off">
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="col-form-label">Receipt No</label>
                                <input type="text" class="form-control" name="receipt_no" placeholder="RNO001" autocomplete="off">
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
        const searchInput = document.getElementById('customer_search');
        const dropdown = document.getElementById('customer_dropdown');

        searchInput.addEventListener('input', function() {
            const query = this.value;
            clearTimeout(searchTimeout);
            if (query.length < 1) {
                dropdown.style.display = 'none';
                return;
            }
            searchTimeout = setTimeout(() => {
                searchCustomers(query);
            }, 500);
        });

        function searchCustomers(query) {
            fetch(`{{ route('loan.search_customer') }}?search=${encodeURIComponent(query)}`, {
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(customers => {
                dropdown.innerHTML = '';
                if (customers.length === 0) {
                    dropdown.innerHTML = '<div class="dropdown-item-text">No customers found</div>';
                } else {
                    customers.forEach(customer => {
                        const item = document.createElement('a');
                        item.className = 'dropdown-item';
                        item.href = '#';
                        item.innerHTML = `<strong>${customer.customer_code}</strong><br><small>${customer.customer_name}</small>`;
                        item.addEventListener('click', function(e) {
                            e.preventDefault();
                            searchInput.value = customer.customer_code;
                            document.getElementById('customer-name').value = customer.customer_name;
                            document.getElementById('company-code').value = customer.company_code;
                            dropdown.style.display = 'none';
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

        function updateCapital(){
            let loan_amount = document.getElementById('loan-amount').value ?? 0;
            let processing_fee = document.getElementById('processing-fee').value ?? 0;
            let stamp_fee = document.getElementById('stamp-fee').value ?? 0;
            if(loan_amount > 1){
                document.getElementById('capital').value = Number(loan_amount) - (Number(processing_fee) + Number(stamp_fee));
            }
        }

        function onSubmitForm() {
            var form = document.querySelector('form');
            if (form.checkValidity()) {
                showLoading();
                return true;
            } else {
                return false;
            }
        }

        function calculateInterest(){
              fetch('{{ route('loan.calculate_interest') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    loan_amount: document.getElementById('loan-amount').value,
                    loan_term: document.getElementById('loan-term').value,
                    interest_group: document.getElementById('interest-group').value,
                    first_payment: document.getElementById('first-payment').value,
                    last_payment: document.getElementById('last-payment').value,
                    installment: document.getElementById('installment').value
                })
                })
            .then(response => response.json())
            .then(r => {
                if (r.success === false && r.errors) {
                    let errorMsg = '';
                    for (let field in r.errors) {
                        errorMsg += r.errors[field].join(', ') + '\n';
                    }
                    alert('Validation Errors:\n' + errorMsg);
                    
                } else if (r.success === true) {
                    document.getElementById('interest-rate').value = r.data.amount;
                } else {
                    console.error('Unexpected response format:', r);
                }
            })
            .catch(error => {
                alert('Network error occurred');
            });
        }

        function changeInterestGroup(){
            resetPayment();
            let f = document.getElementById('first-payment');
            let l = document.getElementById('last-payment');
            let t = document.getElementById('loan-term');
            switch(document.getElementById('interest-group').value ?? false){
                case 'SKIM A':
                    f.value = null;
                    l.value = null;
                    t.value = null;
                    f.closest('.input-wrapper').style.display="none";
                    l.closest('.input-wrapper').style.display="none";
                    t.closest('.input-wrapper').style.display="none";
                    break;
                case 'SKIM B':
                    f.closest('.input-wrapper').style.display="block";
                    l.closest('.input-wrapper').style.display="block";
                    t.closest('.input-wrapper').style.display="block";
                    break;
                default:
                    setDefaultSwal('error','',"Invalid interest group.");
            }
        }

        function resetPayment(){
            document.getElementById('first-payment').value = null;
            document.getElementById('last-payment').value = null;
            document.getElementById('installment').value = null;
        }

        function calculatePayment(){
            fetch('{{ route('loan.calculate_loan') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    loan_amount: document.getElementById('loan-amount').value,
                    interest_rate: document.getElementById('interest-rate').value,
                    loan_term: document.getElementById('loan-term').value,
                    interest_group: document.getElementById('interest-group').value
                })
            })
            .then(response => response.json())
            .then(r => {
                if (r.success === false && r.errors) {
                    let errorMsg = '';
                    for (let field in r.errors) {
                        errorMsg += r.errors[field].join(', ') + '\n';
                    }
                    alert('Validation Errors:\n' + errorMsg);
                }
                else if (r.success === true) {
                    document.getElementById('first-payment').value = r.data.amount;
                    document.getElementById('last-payment').value = r.data.amount;
                    document.getElementById('installment').value = r.data.amount;
                }
                else {
                    console.error('Unexpected response format:', r);
                }
            })
            .catch(error => {
                setDefaultSwal('error','','There is something wrong, please try again.');
            });
        }

        $('#form-create-loan').on('submit', function (e) {
            e.preventDefault();
            let form = $(this);
            let formData = new FormData(this);
            $.ajax({
                url:  "{{ route('loan.store') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                header: { 'X-CSRF-TOKEN': "{{ csrf_token() }}"},
                success: function (response) {
                    if(response.success == true){
                        setRedirectSwal('success','',response.message,'{{ route('loan.index') }}');
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
