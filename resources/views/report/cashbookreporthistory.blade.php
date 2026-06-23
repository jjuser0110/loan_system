@extends('layouts.app')
@section('content')
<header class="page-header">
    <h2>{{ __('sidebar.cash_book_report_history') }}</h2>
</header>
@include('layouts.flash-message')
<style>
    .row-invalid td {
        background-color: #ffe6e6 !important;
    }
    .row-edit td {
        background-color: rgba(255, 0, 0, 0.1) !important;
    }
    .row-deleted td {
        background-color: rgba(255, 0, 0, 0.1) !important;
    }
</style>

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
                                <th>{{ __('table.collection_type') }}</th>
                                <th>{{ __('table.expenses_name') }}</th>
                                <th>{{ __('table.expenses_description') }}</th>
                                <th>{{ __('table.remark') }}</th>
                                <th>{{ __('table.interest_paid') }}</th>
                                <th>{{ __('table.customer_payment') }}</th>
                                <th>{{ __('table.top_up_capital') }}</th>
                                <th>{{ __('table.new_loan_capital') }}</th>
                                <th>{{ __('table.expenses') }}</th>
                                <th>{{ __('table.account_total') }}</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th colspan="6" class="text-right">Total</th>
                                <th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th>
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
            },
            dataSrc: function (json) {
                window.cashBookOpeningBalance = json.opening_balance || 0;
                return json.data;
            }
        },
        order: [[2, "asc"]],
        rowCallback: function(row, data) {
            let desc = data.description || '';
            if (desc.includes('Payment Created') || desc.includes('Payment Deleted')) {
                $(row).addClass('row-invalid');
            }
            if (desc.endsWith('Edit') || desc.includes('Loan Deleted')) {
                $(row).addClass('row-edit');
            }
        },
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
                    if (data.startsWith('Loan #Loan Deleted')) {
                        return data; // plain text, no link
                    }
                    if (data.startsWith('Loan #')) {
                        let loanCode = data.replace('Loan #', '').trim();
                        return '<a href="{{ url("loan/single_loan") }}/' + loanCode + '#loan">' + data + '</a>';
                    }
                    if (data.startsWith('Expense #')) {
                        return '<a href="{{ url("expense/index") }}">' + data + '</a>';
                    }
                    if (data.startsWith('Payment #') || data.startsWith('Loan TopUp - Payment #')) {
                        let paymentCode = data.replace('Loan TopUp - Payment #', '').replace('Payment #', '').trim();
                        let match = paymentCode.match(/^(.+)-P\d+$/);
                        if (match) {
                            return '<a href="{{ url("loan/single_loan") }}/' + match[1] + '#payment">' + data + '</a>';
                        }
                        return '<a href="{{ url("payment/index") }}">' + data + '</a>';
                    }
                    return data;
                }
            },
            {
                data: "date", name: "date", width: "60px",
                render: function (data) {
                    if (!data) return '-';
                    const p = data.substring(0, 10).split('-');
                    return p[2] + '-' + p[1] + '-' + p[0];
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
                data: "collection_type", name: "collection_type", width: "100px",
                render: function (data) { return data || '-'; }
            },
            {
                data: "expenses_name", name: "expenses_name", width: "160px",
                render: function (data) { return data || '-'; }
            },
            {
                data: "expenses_description", name: "expenses_description", width: "160px",
                render: function (data) { return data || '-'; }
            },
            {
                data: "remark", name: "remark", width: "160px",
                render: function (data) { return data || '-'; }
            },
            {
                data: "interest_paid", name: "interest_paid", width: "100px",
                render: function (data) {
                    let v = parseFloat(data || 0);
                    return '<span style="color:' + (v < 0 ? 'red' : 'green') + '">' + v.toFixed(2) + '</span>';
                }
            },
            {
                data: "customer_payment", name: "customer_payment", width: "80px",
                render: function (data) {
                    let v = parseFloat(data || 0);
                    return '<span style="color:' + (v < 0 ? 'red' : 'green') + '">' + v.toFixed(2) + '</span>';
                }
            },
            {
                data: "top_up_capital", name: "top_up_capital", width: "100px",
                render: function (data) {
                    if (data === null || data === undefined) return '-';
                    let v = parseFloat(data);
                    if (isNaN(v)) return '-';
                    let color = v < 0 ? 'red' : 'green';
                    return '<span style="color:' + color + '">' + v.toFixed(2) + '</span>';
                }
            },
            {
    data: "new_capital_loan", name: "new_capital_loan", width: "80px",
    render: function (data) {
        if (data === null || data === undefined) return '-';
        let v = parseFloat(data);
        if (isNaN(v)) return '-';
        let color = v < 0 ? 'red' : 'green';
        return '<span style="color:' + color + '">' + v.toFixed(2) + '</span>';
    }
},
            {
                data: "expenses", name: "expenses", width: "80px",
                render: function (data) {
                    let v = parseFloat(data || 0);
                    return '<span style="color:' + (v < 0 ? 'red' : 'green') + '">' + v.toFixed(2) + '</span>';
                }
            },
            {
                data: "account_total_amount", name: "account_total_amount", width: "80px",
                render: function (data) {
                    let v = parseFloat(data || 0);
                    return '<span style="color:' + (v < 0 ? 'red' : 'green') + '">' + v.toFixed(2) + '</span>';
                }
            },
        ],
        footerCallback: function () {
            let api     = this.api();
            let allRows = api.rows({ search: 'applied' }).data();
            let openingBalance = window.cashBookOpeningBalance || 0;

            let totIP = 0, totCP = 0, totTUC = 0, totNCL = 0, totEXP = 0, lastAT = 0;

            allRows.each(function (r) {
                totIP  += parseFloat(r.interest_paid    || 0);
                totCP  += parseFloat(r.customer_payment || 0);
                totTUC -= parseFloat(r.top_up_capital   || 0);
                totNCL -= parseFloat(r.new_capital_loan      || 0);
                totEXP += parseFloat(r.expenses         || 0);
            });

            if (allRows.length > 0) {
                lastAT = parseFloat(allRows[allRows.length - 1].account_total_amount || 0);
            }

            function colorValue(v) {
                return '<span style="color:' + (v >= 0 ? 'green' : 'red') + '">' + v.toFixed(2) + '</span>';
            }

            $(api.column(6).footer()).html('<span style="font-weight:bold">Opening Balance: </span>');
            $(api.column(7).footer()).html(colorValue(openingBalance));
            $(api.column(8).footer()).html(colorValue(totIP));
            $(api.column(9).footer()).html(colorValue(totCP));
            $(api.column(10).footer()).html(colorValue(totTUC));
            $(api.column(11).footer()).html(colorValue(totNCL));
            $(api.column(12).footer()).html(colorValue(totEXP));
            $(api.column(13).footer()).html(colorValue(lastAT));
        },
    });

    let sf = sessionStorage.getItem('cbr_from_date');
    let st = sessionStorage.getItem('cbr_to_date');
    let sc = sessionStorage.getItem('cbr_company');
    if (sf) $('#filter_from_date').val(sf);
    if (st) $('#filter_to_date').val(st);
    if (sc) $('#filter_company').val(sc);
    if (sf || st || sc) {
        table_cash_book_reports.ajax.reload(function () {
            $('#btn-download-pdf').prop('disabled', false);
        });
    }

    $('#btn-filter').on('click', function () {
        let from    = $('#filter_from_date').val();
        let to      = $('#filter_to_date').val();
        let company = $('#filter_company').val();
        if (!from && !to && !company) {
            alert("{{ __('table.please_select_filter') }}");
            return;
        }
        sessionStorage.setItem('cbr_from_date', from);
        sessionStorage.setItem('cbr_to_date',   to);
        sessionStorage.setItem('cbr_company',   company);
        table_cash_book_reports.ajax.reload(function () {
            $('#btn-download-pdf').prop('disabled', false);
        });
    });

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
                    let rows = response.data || [];
                    if (!rows.length) { alert('No data to export.'); return; }

                    rows.sort(function (a, b) { return (a.date || '').localeCompare(b.date || ''); });

                    const { jsPDF } = window.jspdf;
                    const doc = new jsPDF({ orientation: 'landscape', unit: 'mm', format: 'a4' });
                    const PW  = doc.internal.pageSize.width;
                    const PH  = doc.internal.pageSize.height;
                    const ML  = 10;

                    const CW = [8, 25, 18, 25, 20, 20, 18, 20, 18, 18, 18, 22];
                    let CL = [], cx = ML;
                    CW.forEach(function (w) { CL.push(cx); cx += w; });
                    const TW = CW.reduce(function (a, b) { return a + b; }, 0);

                    function numColor(colIdx, val) {
                        if (colIdx === 10) return val > 0 ? [200,0,0] : (val < 0 ? [0,140,0] : [0,0,0]);
                        return val < 0 ? [200,0,0] : (val > 0 ? [0,140,0] : [0,0,0]);
                    }

                    let fr = rows[0];
                    let ob = parseFloat(fr.account_total_amount || 0)
                           - parseFloat(fr.customer_payment     || 0)
                           - parseFloat(fr.loan_top_up          || 0)
                           + parseFloat(fr.expenses             || 0);

                    doc.setFontSize(14); doc.setFont('helvetica', 'bold');
                    doc.text('Cash Book Report', PW / 2, 12, { align: 'center' });
                    doc.setFontSize(9); doc.setFont('helvetica', 'normal');
                    let sub = [];
                    if (companyLabel && company) sub.push('Company: ' + companyLabel);
                    if (from) sub.push('From: ' + from);
                    if (to)   sub.push('To: ' + to);
                    if (sub.length) doc.text(sub.join('   |   '), PW / 2, 18, { align: 'center' });
                    doc.setFontSize(8);
                    doc.text('Generated: ' + new Date().toLocaleString(), PW / 2, 23, { align: 'center' });

                    let obY = 27;
                    doc.setFillColor(232, 245, 232); doc.rect(ML, obY, TW, 6, 'F');
                    doc.setDrawColor(180, 210, 180); doc.rect(ML, obY, TW, 6, 'S');
                    doc.setFontSize(7.5); doc.setFont('helvetica', 'bold'); doc.setTextColor(0, 140, 0);
                    doc.text('Opening Balance', ML + 2, obY + 4);
                    doc.text(ob.toFixed(2), CL[11] + CW[11] - 2, obY + 4, { align: 'right' });
                    doc.setTextColor(0, 0, 0); doc.setFont('helvetica', 'normal');

                    let bodyRows = rows.map(function (row, i) {
                        let ip  = parseFloat(row.interest_paid        || 0);
                        let cp  = parseFloat(row.customer_payment     || 0);
                        let tuc = parseFloat(row.top_up_capital       || 0);
                        let ltu = parseFloat(row.loan_top_up          || 0);
                        let exp = parseFloat(row.expenses             || 0);
                        let at  = parseFloat(row.account_total_amount || 0);
                        let d   = row.date ? row.date.substring(0, 10) : '';
                        return [
                            i + 1,
                            row.description          || '-',
                            d,
                            row.customer_name        || '-',
                            row.expenses_name        || '-',
                            row.expenses_description || '-',
                            ip  !== 0 ? ip.toFixed(2)  : '-',
                            cp  !== 0 ? cp.toFixed(2)  : '-',
                            tuc !== 0 ? tuc.toFixed(2) : '-',
                            ltu !== 0 ? ltu.toFixed(2) : '-',
                            exp !== 0 ? exp.toFixed(2) : '-',
                            at.toFixed(2),
                        ];
                    });

                    let totIP  = rows.reduce(function (s, r) { return s + parseFloat(r.interest_paid    || 0); }, 0);
                    let totCP  = rows.reduce(function (s, r) { return s + parseFloat(r.customer_payment || 0); }, 0);
                    let totTUC = rows.reduce(function (s, r) { return s + parseFloat(r.top_up_capital   || 0); }, 0);
                    let totLTU = rows.reduce(function (s, r) { return s + parseFloat(r.loan_top_up      || 0); }, 0);
                    let totEXP = rows.reduce(function (s, r) { return s + parseFloat(r.expenses         || 0); }, 0);
                    let totAT  = parseFloat(rows[rows.length - 1].account_total_amount || 0);

                    doc.autoTable({
                        startY: 34,
                        margin: { left: ML, right: ML },
                        head: [['No', 'Description', 'Date', 'Customer Name', 'Expenses Name', 'Expenses Desc', 'Interest Paid', 'Customer Payment', 'Top Up Capital', 'Loan Top Up', 'Expenses', 'Account Total']],
                        body: bodyRows,
                        theme: 'grid',
                        headStyles: { fillColor: [41, 128, 185], textColor: 255, fontStyle: 'bold', fontSize: 7, halign: 'center' },
                        bodyStyles: { fontSize: 7 },
                        columnStyles: {
                            0:  { halign: 'center', cellWidth: CW[0]  },
                            1:  { cellWidth: CW[1]  },
                            2:  { halign: 'center', cellWidth: CW[2]  },
                            3:  { cellWidth: CW[3]  },
                            4:  { cellWidth: CW[4]  },
                            5:  { cellWidth: CW[5]  },
                            6:  { halign: 'right',  cellWidth: CW[6]  },
                            7:  { halign: 'right',  cellWidth: CW[7]  },
                            8:  { halign: 'right',  cellWidth: CW[8]  },
                            9:  { halign: 'right',  cellWidth: CW[9]  },
                            10: { halign: 'right',  cellWidth: CW[10] },
                            11: { halign: 'right',  cellWidth: CW[11] },
                        },
                        didParseCell: function (data) {
                            if (data.section !== 'body') return;
                            if (![6,7,8,9,10,11].includes(data.column.index)) return;
                            let v = parseFloat(data.cell.raw);
                            if (isNaN(v)) return;
                            data.cell.styles.textColor = numColor(data.column.index, v);
                        },
                        didDrawPage: function () {
                            doc.setFontSize(8); doc.setFont('helvetica', 'normal'); doc.setTextColor(0, 0, 0);
                            doc.text('Page ' + doc.internal.getCurrentPageInfo().pageNumber, ML, PH - 5);
                        },
                    });

                    let fy   = doc.lastAutoTable.finalY;
                    let rowH = 7;
                    if (fy + rowH > PH - 12) {
                        doc.addPage(); fy = 12;
                        doc.setFontSize(8); doc.setFont('helvetica', 'normal'); doc.setTextColor(0, 0, 0);
                        doc.text('Page ' + doc.internal.getNumberOfPages(), ML, PH - 5);
                    }

                    doc.setFillColor(220, 230, 241); doc.rect(ML, fy, TW, rowH, 'F');
                    doc.setDrawColor(150, 170, 200); doc.rect(ML, fy, TW, rowH, 'S');
                    doc.setFontSize(7.5); doc.setFont('helvetica', 'bold'); doc.setTextColor(0, 0, 0);
                    doc.text('Total', CL[4] + 2, fy + 4.8);

                    [[6,totIP],[7,totCP],[8,totTUC],[9,totLTU],[10,totEXP],[11,totAT]].forEach(function (pair) {
                        let c = numColor(pair[0], pair[1]);
                        doc.setTextColor(c[0], c[1], c[2]);
                        doc.text(pair[1].toFixed(2), CL[pair[0]] + CW[pair[0]] - 2, fy + 4.8, { align: 'right' });
                    });
                    doc.setTextColor(0, 0, 0);

                    let fn = 'cash_book_report' + (from ? '_' + from : '') + (to ? '_to_' + to : '');
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

});
</script>
@endsection