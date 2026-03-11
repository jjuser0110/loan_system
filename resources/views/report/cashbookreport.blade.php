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
                <table class="table cus-table table-bordered table-striped mb-0" id="table-cash-book-reports">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>{{ __('table.description') }}</th>
                            <th>{{ __('table.date') }}</th>
                            <th>{{ __('table.customer_name') }}</th>
                            <th>{{ __('table.expenses_name') }}</th>
                            <th>{{ __('table.loan_topup') }}</th>
                            <th>{{ __('table.expenses') }}</th>
                            <th>{{ __('table.account_total') }}</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </section>
    </div>
</div>

@endsection

@section('scripts')
{{-- jsPDF + AutoTable for client-side PDF generation --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>

<script>
    let table_cash_book_reports;

    $(document).ready(function() {
        table_cash_book_reports = $('#table-cash-book-reports').DataTable({
            processing: true,
            serverSide: true,
            fixedHeader: false,
            searching: false,
            deferLoading: 0,
            ajax: {
                url: "{{ route('report.load_cash_book_reports') }}",
                type: "GET",
                data: function(d) {
                    d.from_date = $('#filter_from_date').val();
                    d.to_date = $('#filter_to_date').val();
                    d.company_id = $('#filter_company').val();
                }
            },
            order: [[2, "desc"]],
            columns: [
                {
                    data: null,
                    name: "id",
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: "description",
                    name: "description",
                    render: function(data) {
                        return data ? data : '-';
                    }
                },
                {
                    data: "date",
                    name: "date",
                    render: function(data) {
                        return data ? data.substring(0, 10) : '';
                    }
                },
                {
                    data: "customer_name",
                    name: "customer_name",
                    render: function(data) {
                        return data ? data : '-';
                    }
                },
                {
                    data: "expenses_name",
                    name: "expenses_name",
                    render: function(data) {
                        return data ? data : '-';
                    }
                },
                {
                    data: "loan_top_up",
                    name: "loan_top_up",
                    render: function(data) {
                        return data ? parseFloat(data).toFixed(2) : '0.00';
                    }
                },
                {
                    data: "expenses",
                    name: "expenses",
                    render: function(data) {
                        return data ? parseFloat(data).toFixed(2) : '0.00';
                    }
                },
                {
                    data: "account_total_amount",
                    name: "account_total_amount",
                    render: function(data) {
                        return data ? parseFloat(data).toFixed(2) : '0.00';
                    }
                }
            ]
        });
    });

    // Enable PDF button after filter is applied
    $('#btn-filter').on('click', function() {
        let from = $('#filter_from_date').val();
        let to = $('#filter_to_date').val();
        let company = $('#filter_company').val();

        if (!from && !to && !company) {
            alert("{{ __('table.please_select_filter') }}");
            return;
        }

        table_cash_book_reports.ajax.reload(function() {
            $('#btn-download-pdf').prop('disabled', false);
        });
    });

    // PDF download — fetches ALL filtered data (no pagination limit)
    $('#btn-download-pdf').on('click', function() {
        let from     = $('#filter_from_date').val();
        let to       = $('#filter_to_date').val();
        let company  = $('#filter_company').val();
        let companyLabel = $('#filter_company option:selected').text().trim();

        // Show loading state
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
                length:     10000
            },
            success: function(response) {
                if (!response || (!response.data && !Array.isArray(response))) {
                    console.error('Unexpected response format:', response);
                    alert('Unexpected response from server. Check console for details.');
                    return;
                }
                let rows = response.data || (Array.isArray(response) ? response : []);

                const { jsPDF } = window.jspdf;
                const doc = new jsPDF({ orientation: 'landscape', unit: 'mm', format: 'a4' });

                // ── Title block ──────────────────────────────────────────
                doc.setFontSize(14);
                doc.setFont('helvetica', 'bold');
                doc.text('Cash Book Report', 148, 15, { align: 'center' });

                doc.setFontSize(9);
                doc.setFont('helvetica', 'normal');
                let subLines = [];
                if (companyLabel && company) subLines.push('Company: ' + companyLabel);
                if (from)                    subLines.push('From: ' + from);
                if (to)                      subLines.push('To: ' + to);
                if (subLines.length)         doc.text(subLines.join('   |   '), 148, 21, { align: 'center' });

                doc.setFontSize(8);
                doc.text('Generated: ' + new Date().toLocaleString(), 148, 26, { align: 'center' });

                // ── Table ────────────────────────────────────────────────
                let tableRows = rows.map(function(row, index) {
                    return [
                        index + 1,
                        row.description      || '-',
                        row.date ? row.date.substring(0, 10) : '',
                        row.customer_name    || '-',
                        row.expenses_name    || '-',
                        row.loan_top_up      ? parseFloat(row.loan_top_up).toFixed(2)          : '0.00',
                        row.expenses         ? parseFloat(row.expenses).toFixed(2)              : '0.00',
                        row.account_total_amount ? parseFloat(row.account_total_amount).toFixed(2) : '0.00'
                    ];
                });

                // Totals footer row
                let totalLoanTopUp = rows.reduce((s, r) => s + parseFloat(r.loan_top_up || 0), 0);
                let totalExpenses  = rows.reduce((s, r) => s + parseFloat(r.expenses    || 0), 0);
                let totalAccount   = rows.reduce((s, r) => s + parseFloat(r.account_total_amount || 0), 0);

                doc.autoTable({
                    startY: 30,
                    head: [[
                        'No', 'Description', 'Date', 'Customer Name',
                        'Expenses Name', 'Loan Top Up', 'Expenses', 'Account Total'
                    ]],
                    body: tableRows,
                    foot: [[
                        '', '', '', '', 'Total',
                        totalLoanTopUp.toFixed(2),
                        totalExpenses.toFixed(2),
                        totalAccount.toFixed(2)
                    ]],
                    theme: 'grid',
                    headStyles: {
                        fillColor: [41, 128, 185],
                        textColor: 255,
                        fontStyle: 'bold',
                        fontSize: 8,
                        halign: 'center'
                    },
                    footStyles: {
                        fillColor: [236, 240, 241],
                        textColor: [0, 0, 0],
                        fontStyle: 'bold',
                        fontSize: 8
                    },
                    bodyStyles: { fontSize: 8 },
                    columnStyles: {
                        0: { halign: 'center', cellWidth: 10 },
                        2: { halign: 'center', cellWidth: 24 },
                        5: { halign: 'right',  cellWidth: 26 },
                        6: { halign: 'right',  cellWidth: 22 },
                        7: { halign: 'right',  cellWidth: 28 }
                    },
                    didDrawPage: function(data) {
                        // Page number footer
                        doc.setFontSize(8);
                        doc.setFont('helvetica', 'normal');
                        doc.text(
                            'Page ' + doc.internal.getCurrentPageInfo().pageNumber,
                            data.settings.margin.left,
                            doc.internal.pageSize.height - 8
                        );
                    }
                });

                // Build filename
                let filename = 'cash_book_report';
                if (from) filename += '_' + from;
                if (to)   filename += '_to_' + to;
                filename += '.pdf';

                doc.save(filename);
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                console.error('Response:', xhr.responseText);
                alert('Failed to fetch data: ' + (xhr.responseJSON?.message || error || 'Unknown error') + '\nCheck the browser console for details.');
            },
            complete: function() {
                $btn.prop('disabled', false).html('<i class="fas fa-file-pdf"></i> PDF');
            }
        });
    });
</script>
@endsection