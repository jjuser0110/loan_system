@extends('layouts.app')
@section('content')
<header class="page-header">
    <h2>{{ __('table.cash_book_report') }}</h2>
</header>
@include('layouts.flash-message')
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
                <option value="{{ $company->id }}">
                    {{ $company->company_name }} ({{ $company->company_code }})
                </option>
            @endforeach
        </select>
    </div>
    <br>

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
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table cus-table table-bordered table-striped mb-0" id="table-cash-book-reports">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>{{ __('table.description') }}</th>
                                <th>{{ __('table.date') }}</th>
                                <th>{{ __('table.customer_name') }}</th>
                                <th>{{ __('table.expenses_name') }}</th>
                                <th>{{ __('table.expenses_description') }}</th>
                                <th>{{ __('table.interest_paid') }}</th>
                                <th>{{ __('table.customer_payment') }}</th>
                                <th>{{ __('table.top_up_capital') }}</th>
                                <th>{{ __('table.loan_topup') }}</th>
                                <th>{{ __('table.expenses') }}</th>
                                <th>{{ __('table.account_total') }}</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th colspan="6" class="text-right">Total</th>
                                <th></th>  {{-- interest_paid --}}
                                <th></th>  {{-- customer_payment --}}
                                <th></th>  {{-- top_up_capital --}}
                                <th></th>  {{-- loan_top_up --}}
                                <th></th>  {{-- expenses --}}
                                <th></th>  {{-- account_total (last balance) --}}
                            </tr>
                        </tfoot>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>

<script>
    let table_cash_book_reports;

    $(document).ready(function() {
        table_cash_book_reports = $('#table-cash-book-reports').DataTable({
            processing: true,
            serverSide: true,
            fixedHeader: false,
            stateSave: true,
            searching: false,
            deferLoading: 0,
            lengthMenu: [[10, 100, 500, 1000], ['10', '100', '500', '1000']],
            dom: 'lrtip',
            ajax: {
                url: "{{ route('report.load_cash_book_reports') }}",
                type: "GET",
                data: function(d) {
                    d.from_date = $('#filter_from_date').val();
                    d.to_date = $('#filter_to_date').val();
                    d.company_id = $('#filter_company').val();
                }
            },
            order: [[2, "asc"]],
            columns: [
                {
                    data: null,
                    name: "id",
                    width: "10px",
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: "description",
                    name: "description",
                    width: "200px",
                    render: function(data) {
                        if (!data || data === '-') return '-';

                        if (data.startsWith('Loan #')) {
                            let loanCode = data.replace('Loan #', '').trim();
                            return '<a href="{{ url("loan/single_loan") }}/' + loanCode + '#loan">' + data + '</a>';
                        }

                        if (data.startsWith('Expense #')) {
                            return '<a href="{{ url("expense/index") }}">' + data + '</a>';
                        }

                        if (data.startsWith('Payment #')) {
                            // Extract payment code e.g. S260009-001-P003
                            let paymentCode = data.replace('Payment #', '').trim();

                            // Extract loan code: remove last -PXXX part e.g. S260009-001
                            let match = paymentCode.match(/^(.+)-P\d+$/);
                            if (match) {
                                let loanCode = match[1];
                                return '<a href="{{ url("loan/single_loan") }}/' + loanCode + '#payment">' + data + '</a>';
                            }

                            // Fallback if pattern doesn't match
                            return '<a href="{{ url("payment/index") }}">' + data + '</a>';
                        }

                        return data;
                    }
                },
                {
                    data: "date",
                    name: "date",
                    width: "60px",
                    "render": function(data, type, row, meta) {
                        if (!data) return '-';
                        const parts = data.substring(0, 10).split('-');
                        return `${parts[2]}-${parts[1]}-${parts[0]}`;
                    }
                },
                {
                    data: "customer_name",
                    name: "customer_name",
                    width: "200px",
                    render: function(data, type, row) {
                        return data && row.customer_id
                            ? '<a href="{{ url("customer") }}/' + row.customer_id + '/edit">' + data + '</a>'
                            : (data ?? '-');
                    }
                },
                {
                    data: "expenses_name",
                    name: "expenses_name",
                    width: "160px",
                    render: function(data) {
                        return data ? data : '-';
                    }
                },
                {
                    data: "expenses_description",
                    name: "expenses_description",
                    width: "160px",
                    render: function(data) {
                        return data ? data : '-';
                    }
                },
                {
                    data: "interest_paid",
                    name: "interest_paid",
                    width: "160px",
                    render: function(data) {
                        return data ? parseFloat(data).toFixed(2) : '-';
                    }
                },
                {
                    data: "customer_payment",
                    name: "customer_payment",
                    width: "50px",  
                    render: function(data) {
                        let value = data ? parseFloat(data) : 0;
                        let color = value < 0 ? 'red' : 'green';

                        return `<span style="color:${color}">${value.toFixed(2)}</span>`;
                    }
                },
                {
                    data: "top_up_amount",
                    name: "top_up_amount",
                    width: "160px",
                    render: function(data) {
                        return data ? data : '-';
                    }
                },
                {
                    data: "loan_top_up",
                    name: "loan_top_up",
                    width: "50px",
                    render: function(data) {
                        let value = data ? parseFloat(data) : 0;
                        let color = value < 0 ? 'red' : 'green';

                        return `<span style="color:${color}">${value.toFixed(2)}</span>`;
                    }
                },
                {
                    data: "expenses",
                    name: "expenses",
                    width: "50px",
                    render: function(data) {
                        let value = data ? parseFloat(data) : 0;
                        let color = value < 0 ? 'red' : 'green';

                        return `<span style="color:${color}">${value.toFixed(2)}</span>`;
                    }
                },
                {
                    data: "account_total_amount",
                    name: "account_total_amount",
                    width: "50px",
                    render: function(data) {
                        let value = data ? parseFloat(data) : 0;
                        let color = value < 0 ? 'red' : 'green';

                        return `<span style="color:${color}">${value.toFixed(2)}</span>`;
                    }
                },
            ],

            footerCallback: function(row, data, start, end, display) {
                let api = this.api();

                let totalPayment   = 0;
                let totalLoanTopUp = 0;
                let totalExpenses  = 0;
                let lastAccountTotal = 0;

                let allRows = api.rows({ search: 'applied' }).data();

                allRows.each(function(row) {
                    totalPayment   += parseFloat(row.customer_payment || 0);
                    totalLoanTopUp += parseFloat(row.loan_top_up      || 0);
                    totalExpenses  += parseFloat(row.expenses         || 0);
                });

                // Show last row's account_total_amount as final balance
                if (allRows.length > 0) {
                    lastAccountTotal = parseFloat(allRows[allRows.length - 1].account_total_amount || 0);
                }

                $(api.column(7).footer()).html(totalPayment.toFixed(2));
                $(api.column(9).footer()).html(totalLoanTopUp.toFixed(2));
                $(api.column(10).footer()).html(totalExpenses.toFixed(2));
                $(api.column(11).footer()).html(lastAccountTotal.toFixed(2)); // ← last balance
            },
        });
    });

    $('#btn-filter').on('click', function() {
        let from    = $('#filter_from_date').val();
        let to      = $('#filter_to_date').val();
        let company = $('#filter_company').val();

        if (!from && !to && !company) {
            alert("{{ __('table.please_select_filter') }}");
            return;
        }

        // Save to sessionStorage
        sessionStorage.setItem('cbr_from_date', from);
        sessionStorage.setItem('cbr_to_date', to);
        sessionStorage.setItem('cbr_company', company);

        table_cash_book_reports.ajax.reload(function() {
            $('#btn-download-pdf').prop('disabled', false);
        });
    });

    // Restore filter values on page load
    $(document).ready(function() {
        let from    = sessionStorage.getItem('cbr_from_date');
        let to      = sessionStorage.getItem('cbr_to_date');
        let company = sessionStorage.getItem('cbr_company');

        if (from)    $('#filter_from_date').val(from);
        if (to)      $('#filter_to_date').val(to);
        if (company) $('#filter_company').val(company);

        // Auto trigger filter if saved values exist
        if (from || to || company) {
            table_cash_book_reports.ajax.reload(function() {
                $('#btn-download-pdf').prop('disabled', false);
            });
        }
    });

    $('#btn-download-pdf').on('click', function() {
        let from     = $('#filter_from_date').val();
        let to       = $('#filter_to_date').val();
        let company  = $('#filter_company').val();
        let companyLabel = $('#filter_company option:selected').text().trim();

        let $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Generating...');

        $.ajax({
            url: "{{ route('report.load_cash_book_reports') }}",
            type: "GET",
            data: {
                from_date:  from,
                to_date:    to,
                company_id: company,
                start:      0,
                length:     100000
            },
            success: function(response) {
                if (!response || (!response.data && !Array.isArray(response))) {
                    alert('Unexpected response from server.');
                    return;
                }

                // Sort rows by date ASC so running balance is correct
                let rows = (response.data || (Array.isArray(response) ? response : []));
                rows.sort(function(a, b) {
                    return (a.date || '').localeCompare(b.date || '');
                });

                const { jsPDF } = window.jspdf;
                const doc = new jsPDF({ orientation: 'landscape', unit: 'mm', format: 'a4' });

                // Title block
                doc.setFontSize(14);
                doc.setFont('helvetica', 'bold');
                doc.text('Cash Book Report', 148, 15, { align: 'center' });

                doc.setFontSize(9);
                doc.setFont('helvetica', 'normal');
                let subLines = [];
                if (companyLabel && company) subLines.push('Company: ' + companyLabel);
                if (from) subLines.push('From: ' + from);
                if (to)   subLines.push('To: ' + to);
                if (subLines.length) doc.text(subLines.join('   |   '), 148, 21, { align: 'center' });

                doc.setFontSize(8);
                doc.text('Generated: ' + new Date().toLocaleString(), 148, 26, { align: 'center' });

                // Build table rows with running balance per day
                // Group by date to compute daily running total
                let runningTotal = 0;
                let currentDate  = null;
                let dailyNet     = 0;

                let tableRows = rows.map(function(row, index) {
                    let rowDate       = row.date ? row.date.substring(0, 10) : '';
                    let custPayment   = parseFloat(row.customer_payment     || 0);
                    let loanTopUp     = parseFloat(row.loan_top_up          || 0);
                    let expenses      = parseFloat(row.expenses             || 0);
                    let account_total = parseFloat(row.account_total_amount || 0);

                    // Running balance: add inflows, subtract outflows
                    // Inflows: customer_payment + loan_top_up
                    // Outflows: expenses
                    runningTotal += custPayment + loanTopUp - expenses;

                    return [
                        index + 1,
                        row.description         || '-',
                        rowDate,
                        row.customer_name       || '-',
                        row.expenses_name       || '-',
                        row.expenses_description || '-',
                        custPayment.toFixed(2),
                        loanTopUp.toFixed(2),
                        expenses.toFixed(2),
                        account_total.toFixed(2),
                        runningTotal.toFixed(2)
                    ];
                });

                // Totals footer
                let totalCustPayment = rows.reduce((s, r) => s + parseFloat(r.customer_payment     || 0), 0);
                let totalLoanTopUp   = rows.reduce((s, r) => s + parseFloat(r.loan_top_up          || 0), 0);
                let totalExpenses    = rows.reduce((s, r) => s + parseFloat(r.expenses             || 0), 0);
                let lastRow          = rows[rows.length - 1];
                let totalAccount     = lastRow ? parseFloat(lastRow.account_total_amount || 0) : 0;

                doc.autoTable({
                    startY: 30,
                    head: [[
                        'No', 'Description', 'Date', 'Customer Name',
                        'Expenses Name', 'Expenses Description', 'Customer Payment', 'Loan Top Up', 'Expenses', 'Account Total'
                    ]],
                    body: tableRows,
                    foot: [[
                        '', '', '', '', 'Total', '',
                        totalCustPayment.toFixed(2),
                        totalLoanTopUp.toFixed(2),
                        totalExpenses.toFixed(2),
                        totalAccount.toFixed(2)
                    ]],
                    theme: 'grid',
                    didParseCell: function(data) {
                        if (data.section === 'body') {
                            let value = parseFloat(data.cell.raw || 0);
                            if (data.column.index === 6) {
                                if (value < 0) data.cell.styles.textColor = [255, 0, 0];
                                else if (value > 0) data.cell.styles.textColor = [0, 128, 0];
                            }
                            if (data.column.index === 7) {
                                if (value < 0) data.cell.styles.textColor = [255, 0, 0];
                                else if (value > 0) data.cell.styles.textColor = [0, 128, 0];
                            }
                            if (data.column.index === 8) {
                                if (value < 0) data.cell.styles.textColor = [255, 0, 0];
                                else if (value > 0) data.cell.styles.textColor = [0, 128, 0];
                            }
                            if (data.column.index === 9) {
                                if (value < 0) data.cell.styles.textColor = [255, 0, 0];
                                else if (value > 0) data.cell.styles.textColor = [0, 128, 0];
                            }
                        }
                        if (data.section === 'foot') {
                            let value = parseFloat(data.cell.raw || 0);
                            if ([6,7,8,9].includes(data.column.index)) {
                                if (value < 0) data.cell.styles.textColor = [255, 0, 0];
                                else if (value > 0) data.cell.styles.textColor = [0, 128, 0];
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
                        1: { cellWidth: 25 },                      // Description
                        2: { halign: 'center', cellWidth: 20 },   // Date
                        3: { cellWidth: 30 },                      // Customer Name
                        4: { cellWidth: 25 },                      // Expenses Name
                        5: { cellWidth: 25 },                      // Expenses Description
                        6: { halign: 'right',  cellWidth: 24 },   // Customer Payment
                        7: { halign: 'right',  cellWidth: 20 },   // Loan Top Up
                        8: { halign: 'right',  cellWidth: 20 },   // Expenses
                        9: { halign: 'right',  cellWidth: 24 }    // Account Total
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

                let filename = 'cash_book_report';
                if (from) filename += '_' + from;
                if (to)   filename += '_to_' + to;
                filename += '.pdf';

                doc.save(filename);
            },
            error: function(xhr, status, error) {
                alert('Failed to fetch data: ' + (xhr.responseJSON?.message || error));
            },
            complete: function() {
                $btn.prop('disabled', false).html('<i class="fas fa-file-pdf"></i> PDF');
            }
        });
    });
</script>
@endsection