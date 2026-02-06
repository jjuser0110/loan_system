@extends('layouts.app')
@section('content')
<header class="page-header">
    <h2>{{ __('table.expenses') }}</h2>
</header>
@include('layouts.flash-message')
<div class="row" style="padding-top:0">
    <div class="col-lg-12 mb-3">
        <section class="card">
            <div class="card-header" style="text-align: right;">
                <a class="btn btn-xs btn-square btn-primary" onclick="createExpense()">{{ __('table.create') }}</a>
            </div>
            <div class="card-body">
                <table class="table cus-table table-bordered table-striped mb-0" id="table-expense">
                    <thead>
                        <tr>
                            <th>{{ __('table.expense_code') }}</th>
                            <th>{{ __('table.title') }}</th>
                            <th>{{ __('table.description') }}</th>
                            <th>{{ __('table.amount') }}</th>
                            <th>{{ __('table.date') }}</th>
                            <th>{{ __('table.company') }}</th>
                            <th>{{ __('table.bank') }}</th>
                            <th>{{ __('table.created_at') }}</th>
                            <th>{{ __('table.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </section>
    </div>
</div>

<div class="modal fade" id="modal-create-expenses" tabindex="-1" aria-labelledby="modalCreateExpensesLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalUpdateExpenseLabel">{{ __('table.create_expense') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form-create-expense">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="col-form-label">{{ __('table.title') }}</label>
                            <input type="text" class="form-control" id="create-expense-title" name="expense_title" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="col-form-label">{{ __('table.description') }}</label>
                            <textarea class="form-control" id="create-expense-description" name="expense_description" rows="3" placeholder="Enter description here..." required></textarea>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="form-group col-md-12 border-top-0 pt-0">
                            <label for="date">{{ __('table.date') }}</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-calendar-alt"></i>
                                </span>
                                <input type="date" name="date" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="col-form-label">{{ __('table.amount') }}</label>
                            <input type="number" class="form-control" name="amount" value="0">
                        </div>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="col-form-label">{{ __('table.company') }}</label>
                        <select class="form-control" name="company" id="create-expense-company" onchange="setupPaymentMethod(this.value)" required>
                            @if(!$companies)
                            @else
                                <option value="">-- {{ __('table.option') }} --</option>
                                @foreach($companies as $company)
                                <option value="{{ $company->company_code }}">{{ $company->company_name }}/{{ $company->company_code }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="col-form-label">{{ __('table.payment_method') }}</label>
                        <select class="form-control" id="create-payment-method-id" name="payment_method_id" disabled required>
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

<div class="modal fade" id="modal-update-expenses" tabindex="-1" aria-labelledby="modalUpdateExpensesLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalUpdateExpenseLabel">{{ __('table.update_expense') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form-update-expense">
                    @csrf
                    <input type="hidden" name="id" id="update-expense-id">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="col-form-label">{{ __('table.title') }}</label>
                            <input type="text" class="form-control" id="update-expense-title" name="expense_title" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="col-form-label">{{ __('table.description') }}</label>
                            <textarea class="form-control" id="update-expense-description" name="expense_description" rows="3" placeholder="Enter description here..." required></textarea>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="form-group col-md-12 border-top-0 pt-0">
                            <label for="date">{{ __('table.date') }}</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-calendar-alt"></i>
                                </span>
                                <input type="date" name="date" id="update-expense-date" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="col-form-label">{{ __('table.amount') }}</label>
                        <input type="number" id="update-expense-amount" class="form-control" name="amount" value="0">
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="col-form-label">{{ __('table.company') }}</label>
                        <select class="form-control" name="company" id="update-expense-company" onchange="setupUpdatePaymentMethod(this.value)" required>
                            @if(!$companies)
                            @else
                                <option value="">-- {{ __('table.option') }} --</option>
                                @foreach($companies as $company)
                                <option value="{{ $company->company_code }}">{{ $company->company_name }}/{{ $company->company_code }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="col-form-label">{{ __('table.payment_method') }}</label>
                        <select class="form-control" id="update-payment-method-id" name="payment_method_id" disabled required>
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
        let table_expense;
        $(document).ready(function() {
            table_expense = $('#table-expense').DataTable({
                "processing": true,
                "serverSide": true,
                "fixedHeader": false,
                "ajax": {
                    "url": "{{ route('expense.load_expense') }}",
                    "type": "GET"
                },
                "order": [
                    [0, "desc"]
                ],
                "columns": [
                    {
                        "data": "expense_code"
                    },
                    {
                        "data": "expense_title"
                    },
                    {
                        "data": "expense_description"
                    },
                    {
                        "data": "amount"
                    },
                    {
                        "data": "date"
                    },
                    {
                        "data": "company",
                        "render": function(data, type, row, meta){
                            return `${row.company_name}<br>${row.company_code}`;
                        }
                    },
                    {
                        "data": "bank",
                        "render": function(data, type, row, meta){
                            return `${row.bank_name}<br>${row.bank_account_no}<br>${row.bank_owner_name}`;
                        }
                    },
                    {
                        "data": "created_at",
                        "render": function(data, type, row, meta){
                            return formatDate(row.created_at);
                        }
                    },
                    {
                        "data": null,
                        "render": function(data, type, row, meta) {
                            return`
                                <div class="cus-action-wrapper">
                                    <a class="cus-action-icon info" title="Update Expense" onclick="updateExpense(${meta.row})"><i class="fas fa-edit"></i></a>
                                    <a class="cus-action-icon danger" title="Delete Expense" onclick="deleteExpense(${meta.row})"><i class="fas fa-trash-alt"></i></a>
                                </div>
                            `;
                        }
                    }
                ]
            });
        });

        function createExpense(){
            $('#modal-create-expenses').modal('show');  
        }

        function updateExpense(rowIndex) {
            const data = table_expense.row(rowIndex).data();
            document.getElementById('update-expense-id').value = data.id;
            document.getElementById('update-expense-title').value = data.expense_title;
            document.getElementById('update-expense-description').value = data.expense_description;
            document.getElementById('update-expense-date').value = data.date;
            document.getElementById('update-expense-amount').value = data.amount;
            document.getElementById('update-expense-company').value = data.company_code;
            setupUpdatePaymentMethod(data.company_code,data.payment_method_id);
            $('#modal-update-expenses').modal('show');
        }

        function setupPaymentMethod(x){
            let d = document.getElementById('create-payment-method-id');
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
                d.innerHTML = "<option>Please select company first.</option>";
            }
        }

        function setupUpdatePaymentMethod(x,y){
            let d = document.getElementById('update-payment-method-id');
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
                            d.innerHTML += `<option value="${method.id}" ${y == method.id ? 'selected' : ''}>${method.bank_name} / ${method.account_no} (RM ${formatCredit(method.amount)})</option>`;
                        });
                        d.disabled = false;
                    }
                })
                .catch(error => {
                    d.innerHTML = '<option>-- Failed to get methods. --</option>';
                });
            }
            else{ 
                d.innerHTML = "<option>Please select company first.</option>";
            }
        }

        $('#form-create-expense').on('submit', function (e) {
            e.preventDefault();
            let form = $(this);
            let formData = new FormData(this);
            $.ajax({
                url: "{{ route('expense.store') }}",
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

         $('#form-update-expense').on('submit', function (e) {
            e.preventDefault();
            let form = $(this);
            let formData = new FormData(this);
            $.ajax({
                url: "{{ route('expense.update') }}",
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

        function deleteExpense(rowIndex) {
            const data = table_expense.row(rowIndex).data();
            function submitDelete(){
                $.ajax({
                    url: "{{ route('expense.delete') }}",
                    type: "POST",
                    data: {id:data.id},
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
                "This action cannot be undone. Proceed?",
                'Process',
                'Cancel'
            ).then((result) => {
                if (result.isConfirmed) {
                    submitDelete();
                }
            });
        }
    </script>
@endsection
