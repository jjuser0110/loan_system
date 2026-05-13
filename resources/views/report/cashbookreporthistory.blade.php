@extends('layouts.app')
@section('content')
<header class="page-header">
    <h2>{{ __('sidebar.cash_book_report_history') }}</h2>
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
                                <th>{{ __('table.customer_payment') }}</th>
                                <th>{{ __('table.loan_topup') }}</th>
                                <th>{{ __('table.expenses') }}</th>
                                <th>{{ __('table.account_total') }}</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th colspan="5" class="text-right">Total</th>
                                <th></th>  {{-- customer_payment total --}}
                                <th></th>  {{-- loan_top_up total --}}
                                <th></th>  {{-- expenses total --}}
                                <th></th>  {{-- account_total total --}}
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

$(document).ready(function () {

    // ── DataTable ─────────────────────────────────────────────────────────
    table_cash_book_reports = $('#table-cash-book-reports').DataTable({
        processing:   true,
        serverSide:   true,
        fixedHeader:  false,
        stateSave:    true,
        searching:    false,
        deferLoading: 0,
        lengthMenu:   [[10, 100, 500, 1000], ['10', '100', '500', '1000']],
        dom:          'lrtip',
        ajax: {
            url:  "{{ route('report.load_cash_book_report_history') }}",
            type: "GET",
            data: function (d) {
                d.from_date  = $('#filter_from_date').val();
                d.to_date    = $('#filter_to_date').val();
                d.company_id = $('#filter_company').val();
            }
        },
        order: [[2, "asc"]],
        columns: [
            {
                data: null, name: "id", width: "10px",
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {
                data: "description", name: "description", width: "200px",
                render: function (data) {
                    if (!data || data === '-') return '-';
                    if (data.startsWith('Loan #')) {
                        let loanCode = data.replace('Loan #', '').trim();
                        return '<a href="{{ url("loan/single_loan") }}/' + loanCode + '#loan">' + data + '</a>';
                    }
                    if (data.startsWith('Expense #')) {
                        return '<a href="{{ url("expense/index") }}">' + data + '</a>';
                    }
                    if (data.startsWith('Payment #')) {
                        let paymentCode = data.replace('Payment #', '').trim();
                        let match = paymentCode.match(/^(.+)-P\d+$/);
                        if (match) {
                            let loanCode = match[1];
                            return '<a href="{{ url("loan/single_loan") }}/' + loanCode + '#payment">' + data + '</a>';
                        }
                        return '<a href="{{ url("payment/index") }}">' + data + '</a>';
                    }
                    return data;
                }
            },
            {
                data: "date", name: "date", width: "60px",
                render: function (data, type, row, meta) {
                    if (!data) return '-';
                    const parts = data.substring(0, 10).split('-');
                    return parts[2] + '-' + parts[1] + '-' + parts[0];
                }
            },
            {
                data: "customer_name", name: "customer_name", width: "200px",
                render: function (data, type, row) {
                    return data && row.customer_id
                        ? '<a href="{{ url("customer") }}/' + row.customer_id + '/edit">' + data + '</a>'
                        : (data ?? '-');
                }
            },
            {
                data: "expenses_name", name: "expenses_name", width: "160px",
                render: function (data) { return data ? data : '-'; }
            },
            {
                data: "expenses_description", name: "expenses_description", width: "160px",
                render: function (data) { return data ? data : '-'; }
            },
            {
                data: "customer_payment", name: "customer_payment", width: "50px",
                render: function (data) {
                    let value = data ? parseFloat(data) : 0;
                    let color = value < 0 ? 'red' : 'green';
                    return '<span style="color:' + color + '">' + value.toFixed(2) + '</span>';
                }
            },
            {
                data: "loan_top_up", name: "loan_top_up", width: "50px",
                render: function (data) {
                    let value = data ? parseFloat(data) : 0;
                    let color = value < 0 ? 'red' : 'green';
                    return '<span style="color:' + color + '">' + value.toFixed(2) + '</span>';
                }
            },
            {
                data: "expenses", name: "expenses", width: "50px",
                render: function (data) {
                    let value = data ? parseFloat(data) : 0;
                    // expenses: positive = outflow = red
                    let color = value > 0 ? 'red' : 'green';
                    return '<span style="color:' + color + '">' + value.toFixed(2) + '</span>';
                }
            },
            {
                data: "account_total_amount", name: "account_total_amount", width: "50px",
                render: function (data) {
                    let value = data ? parseFloat(data) : 0;
                    let color = value < 0 ? 'red' : 'green';
                    return '<span style="color:' + color + '">' + value.toFixed(2) + '</span>';
                }
            },
        ],

        footerCallback: function (row, data, start, end, display) {
            let api = this.api();
            let totalPayment   = 0;
            let totalLoanTopUp = 0;
            let totalExpenses  = 0;
            api.rows({ search: 'applied' }).data().each(function (row) {
                totalPayment   += parseFloat(row.customer_payment || 0);
                totalLoanTopUp += parseFloat(row.loan_top_up      || 0);
                totalExpenses  += parseFloat(row.expenses         || 0);
            });
            $(api.column(6).footer()).html(totalPayment.toFixed(2));
            $(api.column(7).footer()).html(totalLoanTopUp.toFixed(2));
            $(api.column(8).footer()).html(totalExpenses.toFixed(2));
        }
    });

    // ── Restore session filters ───────────────────────────────────────────
    let sf = sessionStorage.getItem('cbrh_from_date');
    let st = sessionStorage.getItem('cbrh_to_date');
    let sc = sessionStorage.getItem('cbrh_company');
    if (sf) $('#filter_from_date').val(sf);
    if (st) $('#filter_to_date').val(st);
    if (sc) $('#filter_company').val(sc);
    if (sf || st || sc) {
        table_cash_book_reports.ajax.reload(function () {
            $('#btn-download-pdf').prop('disabled', false);
        });
    }

    // ── Filter ────────────────────────────────────────────────────────────
    $('#btn-filter').on('click', function () {
        let from    = $('#filter_from_date').val();
        let to      = $('#filter_to_date').val();
        let company = $('#filter_company').val();
        if (!from && !to && !company) {
            alert("{{ __('table.please_select_filter') }}");
            return;
        }
        sessionStorage.setItem('cbrh_from_date', from);
        sessionStorage.setItem('cbrh_to_date',   to);
        sessionStorage.setItem('cbrh_company',   company);
        table_cash_book_reports.ajax.reload(function () {
            $('#btn-download-pdf').prop('disabled', false);
        });
    });

    // ── PDF ───────────────────────────────────────────────────────────────
    $('#btn-download-pdf').on('click', function () {
        let from         = $('#filter_from_date').val();
        let to           = $('#filter_to_date').val();
        let company      = $('#filter_company').val();
        let companyLabel = $('#filter_company option:selected').text().trim();
        let $btn         = $(this);

        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Generating...');

        $.ajax({
            url:  "{{ route('report.load_cash_book_report_history') }}",
            type: "GET",
            data: { from_date: from, to_date: to, company_id: company, start: 0, length: 100000 },
            success: function (response) {
                try {
                    let rows = response.data || (Array.isArray(response) ? response : []);
                    if (!rows.length) { alert('No data to export.'); return; }

                    rows.sort(function (a, b) { return (a.date || '').localeCompare(b.date || ''); });

                    const { jsPDF } = window.jspdf;
                    const doc  = new jsPDF({ orientation: 'landscape', unit: 'mm', format: 'a4' });
                    const PW   = doc.internal.pageSize.width;
                    const PH   = doc.internal.pageSize.height;
                    const ML   = 10;
                    const MR   = 10;

                    // Column widths — 9 cols, total ~250, fits in 277
                    // No | Description | Date | Customer Name | Expenses Name | Expenses Desc | Cust Payment | Loan Top Up | Expenses | Account Total
                    const CW = [8, 28, 22, 38, 28, 28, 26, 22, 22, 26];
                    let CL = []; let cx = ML;
                    CW.forEach(function (w) { CL.push(cx); cx += w; });
                    const TW = CW.reduce(function (a, b) { return a + b; }, 0);

                    // Color helper
                    function numColor(colIdx, val) {
                        // col 8 = expenses: positive = outflow = red
                        if (colIdx === 8) {
                            return val > 0 ? [200,0,0] : (val < 0 ? [0,140,0] : [0,0,0]);
                        }
                        return val < 0 ? [200,0,0] : (val > 0 ? [0,140,0] : [0,0,0]);
                    }

                    // Opening balance
                    let fr = rows[0];
                    let ob = parseFloat(fr.account_total_amount || 0)
                           - parseFloat(fr.customer_payment     || 0)
                           - parseFloat(fr.loan_top_up          || 0)
                           + parseFloat(fr.expenses             || 0);

                    // Title
                    doc.setFontSize(14);
                    doc.setFont('helvetica', 'bold');
                    doc.text('Cash Book Report History', PW / 2, 12, { align: 'center' });
                    doc.setFontSize(9);
                    doc.setFont('helvetica', 'normal');
                    let sub = [];
                    if (companyLabel && company) sub.push('Company: ' + companyLabel);
                    if (from) sub.push('From: ' + from);
                    if (to)   sub.push('To: '   + to);
                    if (sub.length) doc.text(sub.join('   |   '), PW / 2, 18, { align: 'center' });
                    doc.setFontSize(8);
                    doc.text('Generated: ' + new Date().toLocaleString(), PW / 2, 23, { align: 'center' });

                    // Opening balance row at fixed y=27
                    let obY = 27;
                    doc.setFillColor(232, 245, 232);
                    doc.rect(ML, obY, TW, 6, 'F');
                    doc.setDrawColor(180, 210, 180);
                    doc.rect(ML, obY, TW, 6, 'S');
                    doc.setFontSize(7.5);
                    doc.setFont('helvetica', 'bold');
                    doc.setTextColor(0, 140, 0);
                    doc.text('Opening Balance', ML + 2, obY + 4);
                    doc.text(ob.toFixed(2), CL[9] + CW[9] - 2, obY + 4, { align: 'right' });
                    doc.setTextColor(0, 0, 0);
                    doc.setFont('helvetica', 'normal');

                    // Build body rows
                    let bodyRows = rows.map(function (row, i) {
                        let d   = row.date ? row.date.substring(0, 10) : '';
                        let cp  = parseFloat(row.customer_payment     || 0);
                        let ltu = parseFloat(row.loan_top_up          || 0);
                        let exp = parseFloat(row.expenses             || 0);
                        let at  = parseFloat(row.account_total_amount || 0);
                        return [
                            i + 1,
                            row.description          || '-',
                            d,
                            row.customer_name        || '-',
                            row.expenses_name        || '-',
                            row.expenses_description || '-',
                            cp  !== 0 ? cp.toFixed(2)  : '-',   // col 6
                            ltu !== 0 ? ltu.toFixed(2) : '-',   // col 7
                            exp !== 0 ? exp.toFixed(2) : '-',   // col 8
                            at.toFixed(2),                       // col 9
                        ];
                    });

                    // Totals
                    let totCP  = rows.reduce(function (s, r) { return s + parseFloat(r.customer_payment || 0); }, 0);
                    let totLTU = rows.reduce(function (s, r) { return s + parseFloat(r.loan_top_up      || 0); }, 0);
                    let totEXP = rows.reduce(function (s, r) { return s + parseFloat(r.expenses         || 0); }, 0);
                    let totAT  = parseFloat(rows[rows.length - 1].account_total_amount || 0);

                    // autoTable — no foot, drawn manually after
                    doc.autoTable({
                        startY: 34,
                        margin: { left: ML, right: MR },
                        head: [[
                            'No', 'Description', 'Date', 'Customer Name',
                            'Expenses Name', 'Expenses Desc',
                            'Customer Payment', 'Loan Top Up', 'Expenses', 'Account Total'
                        ]],
                        body: bodyRows,
                        theme: 'grid',
                        headStyles: {
                            fillColor: [41, 128, 185], textColor: 255,
                            fontStyle: 'bold', fontSize: 7, halign: 'center'
                        },
                        bodyStyles: { fontSize: 7 },
                        columnStyles: {
                            0: { halign: 'center', cellWidth: CW[0] },
                            1: { cellWidth: CW[1] },
                            2: { halign: 'center', cellWidth: CW[2] },
                            3: { cellWidth: CW[3] },
                            4: { cellWidth: CW[4] },
                            5: { cellWidth: CW[5] },
                            6: { halign: 'right',  cellWidth: CW[6] },
                            7: { halign: 'right',  cellWidth: CW[7] },
                            8: { halign: 'right',  cellWidth: CW[8] },
                            9: { halign: 'right',  cellWidth: CW[9] },
                        },
                        didParseCell: function (data) {
                            if (data.section !== 'body') return;
                            if (![6,7,8,9].includes(data.column.index)) return;
                            let v = parseFloat(data.cell.raw);
                            if (isNaN(v)) return;
                            data.cell.styles.textColor = numColor(data.column.index, v);
                        },
                        didDrawPage: function () {
                            doc.setFontSize(8);
                            doc.setFont('helvetica', 'normal');
                            doc.setTextColor(0, 0, 0);
                            doc.text(
                                'Page ' + doc.internal.getCurrentPageInfo().pageNumber,
                                ML, PH - 5
                            );
                        },
                    });

                    // Total row — last page only, drawn manually after table
                    let fy   = doc.lastAutoTable.finalY;
                    let rowH = 7;
                    if (fy + rowH > PH - 12) {
                        doc.addPage();
                        fy = 12;
                        doc.setFontSize(8);
                        doc.setFont('helvetica', 'normal');
                        doc.setTextColor(0, 0, 0);
                        doc.text('Page ' + doc.internal.getNumberOfPages(), ML, PH - 5);
                    }

                    doc.setFillColor(220, 230, 241);
                    doc.rect(ML, fy, TW, rowH, 'F');
                    doc.setDrawColor(150, 170, 200);
                    doc.rect(ML, fy, TW, rowH, 'S');
                    doc.setFontSize(7.5);
                    doc.setFont('helvetica', 'bold');
                    doc.setTextColor(0, 0, 0);
                    doc.text('Total', CL[4] + 2, fy + 4.8);

                    let totals = [[6, totCP], [7, totLTU], [8, totEXP], [9, totAT]];
                    totals.forEach(function (pair) {
                        let ci  = pair[0];
                        let val = pair[1];
                        let c   = numColor(ci, val);
                        doc.setTextColor(c[0], c[1], c[2]);
                        doc.text(val.toFixed(2), CL[ci] + CW[ci] - 2, fy + 4.8, { align: 'right' });
                    });
                    doc.setTextColor(0, 0, 0);

                    // Save
                    let fn = 'cash_book_report_history';
                    if (from) fn += '_' + from;
                    if (to)   fn += '_to_' + to;
                    doc.save(fn + '.pdf');

                } catch (err) {
                    console.error('PDF error:', err);
                    alert('PDF generation error: ' + err.message);
                }
            },
            error: function (xhr, status, error) {
                alert('Failed to fetch data: ' + (xhr.responseJSON?.message || error));
            },
            complete: function () {
                $btn.prop('disabled', false).html('<i class="fas fa-file-pdf"></i> PDF');
            }
        });
    });

}); // end document.ready
</script>
@endsection