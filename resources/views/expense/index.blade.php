@extends('layouts.app')
@section('content')
<header class="page-header">
    <h2>{{ __('table.expenses') }}</h2>
</header>
@include('layouts.flash-message')

<!-- Filter Row -->
<div class="row mb-3">
    <div class="col-md-3">
        <label>{{ __('table.from_date') }}</label>
        <input type="date" id="filter_from_date" class="form-control">
    </div>
    <div class="col-md-3">
        <label>{{ __('table.to_date') }}</label>
        <input type="date" id="filter_to_date" class="form-control">
    </div>
    <div class="col-md-3">
        <label>{{ __('table.company') }}</label>
        <select id="filter_company" class="form-control">
            <option value="">{{ __('table.select_company') }}</option>
            @foreach($companies as $company)
                <option value="{{ $company->company_code }}">
                    {{ $company->company_name }} ({{ $company->company_code }})
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3 d-flex align-items-end gap-2">
        <button class="btn btn-primary w-100" id="btn-filter">
            {{ __('table.filter') }}
        </button>
        <button class="btn btn-danger w-100" id="btn-download-pdf" disabled>
            <i class="fas fa-file-pdf"></i> PDF
        </button>
    </div>
</div>

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

<!-- Create Modal -->
<div class="modal fade" id="modal-create-expenses" tabindex="-1" aria-labelledby="modalCreateExpensesLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('table.create_expense') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form-create-expense">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="col-form-label">{{ __('table.title') }}</label>
                            <select class="form-control" id="create-expense-title" name="expense_title" required>
                                <option value="">-- {{ __('table.option') }} --</option>
                                @foreach($expenseTypes as $type)
                                    <option value="{{ $type->title }}">{{ $type->title }}</option>
                                @endforeach
                            </select>
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
                                <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                <input type="date" name="date" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="col-form-label">{{ __('table.amount_in') }} (-)</label>
                            <input type="number" step="0.01" class="form-control" name="amount_in" value="0" min="0" id="create-amount-in">
                        </div>
                        <div class="col-md-6">
                            <label class="col-form-label">{{ __('table.amount_out') }} (+)</label>
                            <input type="number" step="0.01" class="form-control" name="amount_out" value="0" min="0" id="create-amount-out">
                        </div>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="col-form-label">{{ __('table.company') }}</label>
                        <select class="form-control" name="company" id="create-expense-company" onchange="setupPaymentMethod(this.value)" required>
                            <option value="">-- {{ __('table.option') }} --</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->company_code }}">{{ $company->company_name }}/{{ $company->company_code }}</option>
                            @endforeach
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

<!-- Update Modal -->
<div class="modal fade" id="modal-update-expenses" tabindex="-1" aria-labelledby="modalUpdateExpensesLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('table.update_expense') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form-update-expense">
                    @csrf
                    <input type="hidden" name="id" id="update-expense-id">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="col-form-label">{{ __('table.title') }}</label>
                            <select class="form-control" id="update-expense-title" name="expense_title" required>
                                <option value="">-- {{ __('table.option') }} --</option>
                                @foreach($expenseTypes as $type)
                                    <option value="{{ $type->title }}">{{ $type->title }}</option>
                                @endforeach
                            </select>
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
                                <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                <input type="date" name="date" id="update-expense-date" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="col-form-label">{{ __('table.amount_in') }} (-)</label>
                            <input type="number" step="0.01" class="form-control" name="amount_in" value="0" min="0" id="update-amount-in">
                        </div>
                        <div class="col-md-6">
                            <label class="col-form-label">{{ __('table.amount_out') }} (+)</label>
                            <input type="number" step="0.01" class="form-control" name="amount_out" value="0" min="0" id="update-amount-out">
                        </div>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="col-form-label">{{ __('table.company') }}</label>
                        <select class="form-control" name="company" id="update-expense-company" onchange="setupUpdatePaymentMethod(this.value)" required>
                            <option value="">-- {{ __('table.option') }} --</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->company_code }}">{{ $company->company_name }}/{{ $company->company_code }}</option>
                            @endforeach
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>

<script>
    let table_expense;

    $(document).ready(function() {
        table_expense = $('#table-expense').DataTable({
            "processing": true,
            "serverSide": true,
            "fixedHeader": false,
            "lengthMenu": [[10, 100, 500, 1000], ['10', '100', '500', '1000']],
            "ajax": {
                "url": "{{ route('expense.load_expense') }}",
                "type": "GET",
                "data": function(d) {
                    d.from_date  = $('#filter_from_date').val();
                    d.to_date    = $('#filter_to_date').val();
                    d.company    = $('#filter_company').val();
                }
            },
            "order": [[0, "desc"]],
            "columns": [
                { "data": "expense_code" },
                { "data": "expense_title" },
                { "data": "expense_description" },
                {
                    data: "amount",
                    render: function(data) {
                        let color = data < 0 ? 'red' : 'green';
                        return `<span style="color:${color}">${data}</span>`;
                    }
                },
                {
                    "data": "date",
                    "render": function(data) {
                        if (!data) return '-';
                        const parts = data.substring(0, 10).split('-');
                        return `${parts[2]}-${parts[1]}-${parts[0]}`;
                    }
                },
                {
                    "data": "company",
                    "render": function(data, type, row) {
                        return `${row.company_name}<br>${row.company_code}`;
                    }
                },
                {
                    "data": "bank",
                    "render": function(data, type, row) {
                        return `${row.bank_name}<br>${row.bank_account_no}<br>${row.bank_owner_name}`;
                    }
                },
                {
                    "data": "created_at",
                    "render": function(data) {
                        if (!data) return '-';
                        const parts = data.substring(0, 10).split('-');
                        return `${parts[2]}-${parts[1]}-${parts[0]}`;
                    }
                },
                {
                    "data": null,
                    "render": function(data, type, row, meta) {
                        return `
                            <div class="cus-action-wrapper">
                                <a class="cus-action-icon info" title="Update Expense" onclick="updateExpense(${meta.row})"><i class="fas fa-edit"></i></a>
                                <a class="cus-action-icon danger" title="Delete Expense" onclick="deleteExpense(${meta.row})"><i class="fas fa-trash-alt"></i></a>
                            </div>
                        `;
                    }
                }
            ]
        });

        // Restore filter from sessionStorage
        let from    = sessionStorage.getItem('exp_from_date');
        let to      = sessionStorage.getItem('exp_to_date');
        let company = sessionStorage.getItem('exp_company');

        if (from)    $('#filter_from_date').val(from);
        if (to)      $('#filter_to_date').val(to);
        if (company) $('#filter_company').val(company);

        if (from || to || company) {
            table_expense.ajax.reload(function() {
                $('#btn-download-pdf').prop('disabled', false);
            });
        }
    });

    // Filter button
    $('#btn-filter').on('click', function() {
        let from    = $('#filter_from_date').val();
        let to      = $('#filter_to_date').val();
        let company = $('#filter_company').val();

        sessionStorage.setItem('exp_from_date', from);
        sessionStorage.setItem('exp_to_date', to);
        sessionStorage.setItem('exp_company', company);

        table_expense.ajax.reload(function() {
            $('#btn-download-pdf').prop('disabled', false);
        });
    });

    // PDF button
    $('#btn-download-pdf').on('click', function() {
        let from         = $('#filter_from_date').val();
        let to           = $('#filter_to_date').val();
        let company      = $('#filter_company').val();
        let companyLabel = $('#filter_company option:selected').text().trim();

        let $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Generating...');

        $.ajax({
            url: "{{ route('expense.load_expense') }}",
            type: "GET",
            data: {
                from_date:  from,
                to_date:    to,
                company:    company,
                start:      0,
                length:     100000
            },
            success: function(response) {
                if (!response || !response.data) {
                    alert('No data found.');
                    return;
                }

                let rows = response.data;
                rows.sort(function(a, b) {
                    return (a.date || '').localeCompare(b.date || '');
                });

                const { jsPDF } = window.jspdf;
                const doc = new jsPDF({ orientation: 'landscape', unit: 'mm', format: 'a4' });

                // Title
                doc.setFontSize(14);
                doc.setFont('helvetica', 'bold');
                doc.text('Expenses Report', 148, 15, { align: 'center' });

                doc.setFontSize(9);
                doc.setFont('helvetica', 'normal');
                let subLines = [];
                if (companyLabel && company) subLines.push('Company: ' + companyLabel);
                if (from) subLines.push('From: ' + from);
                if (to)   subLines.push('To: ' + to);
                if (subLines.length) doc.text(subLines.join('   |   '), 148, 21, { align: 'center' });

                doc.setFontSize(8);
                doc.text('Generated: ' + new Date().toLocaleString(), 148, 26, { align: 'center' });

                let totalAmount = 0;

                let tableRows = rows.map(function(row, index) {
                    let amount = parseFloat(row.amount || 0);
                    totalAmount += amount;

                    let dateFormatted = '-';
                    if (row.date) {
                        const parts = row.date.substring(0, 10).split('-');
                        dateFormatted = `${parts[2]}-${parts[1]}-${parts[0]}`;
                    }

                    return [
                        index + 1,
                        row.expense_code        || '-',
                        row.expense_title       || '-',
                        row.expense_description || '-',
                        dateFormatted,
                        (row.company_name || '-') + ' / ' + (row.company_code || '-'),
                        (row.bank_name || '-') + '\n' + (row.bank_account_no || '-') + '\n' + (row.bank_owner_name || '-'),
                        amount.toFixed(2),   // <-- moved to last
                    ];
                });

                doc.autoTable({
                    startY: 30,
                    head: [[
                        'No', 'Code', 'Title', 'Description',
                        'Date', 'Company', 'Bank', 'Amount'
                    ]],
                    body: tableRows,
                    foot: [[
                        '', '', '', '',
                        '', '', 'Total',
                        totalAmount.toFixed(2)
                    ]],
                    theme: 'grid',
                    didParseCell: function(data) {
                        if (data.section === 'body' && data.column.index === 7) {
                            let value = parseFloat(data.cell.raw || 0);

                            if (value < 0) {
                                data.cell.styles.textColor = [255, 0, 0]; // red
                            } else if (value > 0) {
                                data.cell.styles.textColor = [0, 128, 0]; // green
                            }
                        }

                        if (data.section === 'foot' && data.column.index === 7) {
                            let value = totalAmount;

                            if (value < 0) {
                                data.cell.styles.textColor = [255, 0, 0];
                            } else if (value > 0) {
                                data.cell.styles.textColor = [0, 128, 0];
                            }
                        }
                    },
                    headStyles: {
                        fillColor: [41, 128, 185],
                        textColor: 255,
                        fontStyle: 'bold',
                        fontSize: 7.5,
                        halign: 'center'
                    },
                    footStyles: {
                        fillColor: [236, 240, 241],
                        textColor: [0, 0, 0],
                        fontStyle: 'bold',
                        fontSize: 8
                    },
                    bodyStyles: { fontSize: 7.5 },
                    columnStyles: {
                        0: { halign: 'center', cellWidth: 8  },   // No
                        1: { cellWidth: 22 },                      // Code
                        2: { cellWidth: 28 },                      // Title
                        3: { cellWidth: 50 },                      // Description
                        4: { halign: 'center', cellWidth: 22 },   // Date
                        5: { cellWidth: 38 },                      // Company
                        6: { cellWidth: 38 },                      // Bank
                        7: { halign: 'right', cellWidth: 22 },    // Amount
                    },
                    didDrawPage: function(data) {
                        doc.setFontSize(8);
                        doc.setFont('helvetica', 'normal');
                        doc.text(
                            'Page ' + doc.internal.getCurrentPageInfo().pageNumber,
                            data.settings.margin.left,
                            doc.internal.pageSize.height - 8
                        );
                    }
                });

                let filename = 'expenses_report';
                if (from) filename += '_' + from;
                if (to)   filename += '_to_' + to;
                filename += '.pdf';

                doc.save(filename);
            },
            error: function(xhr) {
                alert('Failed to fetch data: ' + (xhr.responseJSON?.message || 'Unknown error'));
            },
            complete: function() {
                $btn.prop('disabled', false).html('<i class="fas fa-file-pdf"></i> PDF');
            }
        });
    });

    function createExpense() {
        $('#modal-create-expenses').modal('show');
    }

    function updateExpense(rowIndex) {
        const data = table_expense.row(rowIndex).data();
        document.getElementById('update-expense-id').value          = data.id;
        document.getElementById('update-expense-title').value       = data.expense_title;
        document.getElementById('update-expense-description').value = data.expense_description;
        document.getElementById('update-expense-date').value        = data.date;
        document.getElementById('update-expense-company').value     = data.company_code;

        // Populate amount_in and amount_out from saved amount
        let amount = parseFloat(data.amount || 0);
        if (amount >= 0) {
            document.getElementById('update-amount-out').value  = amount;
            document.getElementById('update-amount-in').value = 0;
        } else {
            document.getElementById('update-amount-out').value  = 0;
            document.getElementById('update-amount-in').value = Math.abs(amount);
        }

        setupUpdatePaymentMethod(data.company_code, data.payment_method_id);
        $('#modal-update-expenses').modal('show');
    }

    function setupPaymentMethod(x) {
        let d = document.getElementById('create-payment-method-id');
        d.disabled = true;
        if (x) {
            fetch(`{{ route('payment_method.search_payment_methods') }}?company_code=${encodeURIComponent(x)}`, {
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
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
            .catch(() => { d.innerHTML = '<option>-- Failed to get methods. --</option>'; });
        } else {
            d.innerHTML = "<option>Please select company first.</option>";
        }
    }

    function setupUpdatePaymentMethod(x, y) {
        let d = document.getElementById('update-payment-method-id');
        d.disabled = true;
        if (x) {
            fetch(`{{ route('payment_method.search_payment_methods') }}?company_code=${encodeURIComponent(x)}`, {
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
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
            .catch(() => { d.innerHTML = '<option>-- Failed to get methods. --</option>'; });
        } else {
            d.innerHTML = "<option>Please select company first.</option>";
        }
    }

    $('#form-create-expense').on('submit', function(e) {
        e.preventDefault();
        let formData = new FormData(this);
        $.ajax({
            url: "{{ route('expense.store') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
            success: function(response) {
                if (response.success == true) {
                    setReloadSwal('success', '', response.message);
                } else {
                    setDefaultSwal('error', '', response.message);
                }
            },
            error: function() {
                setDefaultSwal('error', '', 'There is something wrong, please try again.');
            }
        });
    });

    $('#form-update-expense').on('submit', function(e) {
        e.preventDefault();
        let formData = new FormData(this);
        $.ajax({
            url: "{{ route('expense.update') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
            success: function(response) {
                if (response.success == true) {
                    setReloadSwal('success', '', response.message);
                } else {
                    setDefaultSwal('error', '', response.message);
                }
            },
            error: function() {
                setDefaultSwal('error', '', 'There is something wrong, please try again.');
            }
        });
    });

    function deleteExpense(rowIndex) {
        const data = table_expense.row(rowIndex).data();
        function submitDelete() {
            $.ajax({
                url: "{{ route('expense.delete') }}",
                type: "POST",
                data: { id: data.id },
                headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                success: function(response) {
                    if (response.success == true) {
                        setReloadSwal('success', '', response.message);
                    } else {
                        setDefaultSwal('error', '', response.message);
                    }
                },
                error: function() {
                    setDefaultSwal('error', '', 'There is something wrong, please try again.');
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