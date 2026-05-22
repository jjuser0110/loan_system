@extends('layouts.app')
@section('content')
<header class="page-header">
    <h2>{{ __('table.customer_payment_report') }}</h2>
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
    <div class="col-md-3">
        <label>{{ __('table.search') }}</label>
        <input type="text" id="filter_search" class="form-control" placeholder="{{ __('table.search') }}...">
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-6 d-flex align-items-end gap-2">
        <button class="btn btn-primary" id="btn-skim-all">
            {{ __('table.skim_A') }} &amp; {{ __('table.skim_B') }}
        </button>
        <button class="btn btn-outline-primary" id="btn-skim-a">
            {{ __('table.skim_A') }}
        </button>
        <button class="btn btn-outline-primary" id="btn-skim-b">
            {{ __('table.skim_B') }}
        </button>
    </div>
    <div class="col-md-6 d-flex align-items-end justify-content-end gap-2">
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
                <table class="table cus-table table-bordered table-striped mb-0" id="table-customer-payment">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('table.payment_code') }}</th>
                            <th>{{ __('table.customer_name') }}</th>
                            <th>{{ __('table.collection_type') }}</th>
                            <th>{{ __('table.pay_date') }}</th>
                            <th>{{ __('table.payment') }}</th>
                            <th>{{ __('table.late_paid_amount') }}</th>
                            <th>{{ __('table.interest_paid_amount') }}</th>
                            <th>{{ __('table.discount_amount') }}</th>
                            <th>{{ __('table.top_up_capital') }}</th>
                            <th>{{ __('table.top_up') }}</th>
                            <th>{{ __('table.total_pay') }}</th>
                            <th>{{ __('table.outstd') }}</th>
                            <th>{{ __('table.total') }}</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot>
                        <tr>
                            <th colspan="5" class="text-right">Total</th>
                            <th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </section>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>

<script>
    let currentSkim = '';
    let table_customer_payment;

    function setSkimFilter(skim) {
        currentSkim = skim;
        $('#btn-skim-all').removeClass('btn-primary btn-outline-primary').addClass(skim === ''      ? 'btn-primary' : 'btn-outline-primary');
        $('#btn-skim-a').removeClass('btn-primary btn-outline-primary').addClass(skim === 'SKIM A' ? 'btn-primary' : 'btn-outline-primary');
        $('#btn-skim-b').removeClass('btn-primary btn-outline-primary').addClass(skim === 'SKIM B' ? 'btn-primary' : 'btn-outline-primary');
        if (table_customer_payment) table_customer_payment.ajax.reload();
    }

    $(document).ready(function () {

        let savedFrom    = sessionStorage.getItem('cpr_from_date');
        let savedTo      = sessionStorage.getItem('cpr_to_date');
        let savedCompany = sessionStorage.getItem('cpr_company');

        if (savedFrom)    $('#filter_from_date').val(savedFrom);
        if (savedTo)      $('#filter_to_date').val(savedTo);
        if (savedCompany) $('#filter_company').val(savedCompany);

        table_customer_payment = $('#table-customer-payment').DataTable({
            processing:   true,
            serverSide:   true,
            fixedHeader:  false,
            lengthMenu:   [[10, 100, 500, 1000], ['10', '100', '500', '1000']],
            searching:    false,
            stateSave:    true,
            deferLoading: 0,
            ajax: {
                url:  "{{ route('report.load_customer_payment_report') }}",
                type: "GET",
                data: function (d) {
                    d.from_date       = $('#filter_from_date').val();
                    d.to_date         = $('#filter_to_date').val();
                    d.company_id      = $('#filter_company').val();
                    d.collection_type = currentSkim;
                    d.search          = { value: $('#filter_search').val() };
                }
            },
            order: [[4, "desc"]],
            columns: [
                {
                    data: null,
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    },
                    orderable: false,
                    width: "10px"
                },
                { 
                    data: "payment_code", 
                    name: "payment_code",
                    render: function(data, type, row) {
                        if (!data) return '-';
                        const loanCode = data.replace(/-P\d+$/, '');
                        return `<a href="/loan/single_loan/${loanCode}#payment" style="text-decoration:none">${data}</a>`;
                    }
                },
                { 
                    data: "customer_name", 
                    name: "customer_name",
                    render: function(data, type, row) {
                        if (!data) return '-';
                        return `<a href="/customer/${row.customer_id}/edit" style="text-decoration:none">${data}</a>`;
                    }
                },
                { data: "collection_type", name: "collection_type" },
                {
                    data: "pay_date",
                    name: "pay_date",
                    render: function (data) {
                        if (!data) return '-';
                        const parts = data.substring(0, 10).split('-');
                        return `${parts[2]}-${parts[1]}-${parts[0]}`;
                    }
                },
                {
                    data: "payment_amount",
                    render: function (data) {
                        let v = data ? parseFloat(data) : 0;
                        return `<span style="color:${v < 0 ? 'red' : 'green'}">${v.toFixed(2)}</span>`;
                    }
                },
                {
                    data: "late_paid_amount",
                    render: function (data) {
                        let v = data ? parseFloat(data) : 0;
                        return `<span style="color:${v < 0 ? 'orange' : 'orange'}">${v.toFixed(2)}</span>`;
                    }
                },
                {
                    data: "interest_paid_amount",
                    render: function (data) {
                        let v = data ? parseFloat(data) : 0;
                        return `<span style="color:${v < 0 ? 'red' : 'green'}">${v.toFixed(2)}</span>`;
                    }
                },
                {
                    data: "discount_amount",
                    render: function (data) {
                        let v = data ? parseFloat(data) : 0;
                        return `<span style="color:${v < 0 ? 'red' : 'green'}">${v.toFixed(2)}</span>`;
                    }
                },
                {
                    data: "top_up_capital",
                    render: function (data) {
                        let v = data ? parseFloat(data) : 0;
                        return v > 0 ? `<span style="color:red">${v.toFixed(2)}</span>` : '-';
                    }
                },
                {
                    data: "top_up",
                    render: function (data) {
                        let v = data ? parseFloat(data) : 0;
                        return v > 0 ? `<span style="color:red">${v.toFixed(2)}</span>` : '-';
                    }
                },
                {
                    data: "running_payment",
                    render: function (data) {
                        let v = data !== null ? parseFloat(data) : 0;
                        return `<span style="color:${v < 0 ? 'red' : 'green'}">${v.toFixed(2)}</span>`;
                    }
                },
                {
                    data: "outstanding_balance",
                    render: function (data) {
                        let v = data !== null ? parseFloat(data) : 0;
                        return `<span style="color:${v < 0 ? 'red' : 'green'}">${v.toFixed(2)}</span>`;
                    }
                },
                {
                    data: "total_paid_amount",
                    render: function (data) {
                        let v = data !== null ? parseFloat(data) : 0;
                        return `<span style="color:${v < 0 ? 'red' : 'green'}">${v.toFixed(2)}</span>`;
                    }
                },
            ],
            footerCallback: function () {
                let api     = this.api();
                let allRows = api.rows({ search: 'applied' }).data();

                let totPayment      = 0;
                let totLate         = 0;
                let totInterest     = 0;
                let totDiscount     = 0;
                let totTopUpCapital = 0;
                let totTopUp        = 0;
                let totRunning      = 0;
                let totOutstanding     = 0;
                let totTotalPaid    = 0;

                allRows.each(function (r) {
                    totPayment      += parseFloat(r.payment_amount      || 0);
                    totLate         += parseFloat(r.late_paid_amount    || 0);
                    totInterest     += parseFloat(r.interest_paid_amount|| 0);
                    totDiscount     += parseFloat(r.discount_amount     || 0);
                    totTopUpCapital += parseFloat(r.top_up_capital      || 0);
                    totTopUp        += parseFloat(r.top_up              || 0);
                    totRunning      += parseFloat(r.running_payment     || 0);
                    totOutstanding     += parseFloat(r.outstanding_balance    || 0);
                    totTotalPaid    += parseFloat(r.total_paid_amount   || 0);
                });

                function colorValue(v) {
                    return '<span style="color:' + (v >= 0 ? 'green' : 'red') + '"><strong>' + v.toFixed(2) + '</strong></span>';
                }

                $(api.column(0).footer()).html('<strong>Total</strong>');
                $(api.column(5).footer()).html(colorValue(totPayment));
                $(api.column(6).footer()).html('<span style="color:orange"><strong>' + totLate.toFixed(2) + '</strong></span>');
                $(api.column(7).footer()).html(colorValue(totInterest));
                $(api.column(8).footer()).html(colorValue(totDiscount));
                $(api.column(9).footer()).html('<span style="color:red"><strong>' + totTopUpCapital.toFixed(2) + '</strong></span>');
                $(api.column(10).footer()).html('<span style="color:red"><strong>' + totTopUp.toFixed(2) + '</strong></span>');
                $(api.column(11).footer()).html(colorValue(totRunning));
                // $(api.column(12).footer()).html(colorValue(totOutstanding));
                $(api.column(13).footer()).html(colorValue(totTotalPaid));
            },
        });

        if (savedFrom || savedTo || savedCompany) {
            table_customer_payment.ajax.reload(function() {
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

            sessionStorage.setItem('cpr_from_date', from);
            sessionStorage.setItem('cpr_to_date',   to);
            sessionStorage.setItem('cpr_company',   company);

            table_customer_payment.ajax.reload(function() {
                $('#btn-download-pdf').prop('disabled', false);
            });
        });

        $('#filter_search').on('keydown', function (e) {
            if (e.key === 'Enter') table_customer_payment.ajax.reload();
        });

        $('#btn-skim-all').on('click', function () { setSkimFilter(''); });
        $('#btn-skim-a').on('click',   function () { setSkimFilter('SKIM A'); });
        $('#btn-skim-b').on('click',   function () { setSkimFilter('SKIM B'); });

        $('#btn-download-pdf').on('click', function () {
            let from         = $('#filter_from_date').val();
            let to           = $('#filter_to_date').val();
            let company      = $('#filter_company').val();
            let companyLabel = $('#filter_company option:selected').text().trim();
            let searchVal    = $('#filter_search').val();

            let $btn = $(this);
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Generating...');

            $.ajax({
                url:  "{{ route('report.load_customer_payment_report') }}",
                type: "GET",
                data: {
                    from_date:       from,
                    to_date:         to,
                    company_id:      company,
                    collection_type: currentSkim,
                    search:          { value: searchVal },
                    start:           0,
                    length:          100000
                },
                success: function (response) {
                    if (!response || !response.data) {
                        alert('Unexpected response from server.');
                        return;
                    }

                    let rows = response.data;

                    const { jsPDF } = window.jspdf;
                    const doc = new jsPDF({ orientation: 'landscape', unit: 'mm', format: 'a4' });

                    // Title
                    doc.setFontSize(14);
                    doc.setFont('helvetica', 'bold');
                    doc.text('Customer Payment Report', 148, 15, { align: 'center' });

                    doc.setFontSize(9);
                    doc.setFont('helvetica', 'normal');
                    let subLines = [];
                    if (companyLabel && company) subLines.push('Company: ' + companyLabel);
                    if (currentSkim)             subLines.push('Skim: ' + currentSkim);
                    if (from)                    subLines.push('From: ' + from);
                    if (to)                      subLines.push('To: ' + to);
                    if (subLines.length) doc.text(subLines.join('   |   '), 148, 21, { align: 'center' });

                    doc.setFontSize(8);
                    doc.text('Generated: ' + new Date().toLocaleString(), 148, 26, { align: 'center' });

                    // Totals
                    let totalPayment  = 0;
                    let totalLate     = 0;
                    let totalInterest = 0;
                    let totalDiscount = 0;
                    let totalTopUpCap = 0;
                    let totalTopUp    = 0;
                    let totalTotalPay = 0; // ← rename from totalRunning

                    let tableRows = rows.map(function (row, index) {
                        let payment  = parseFloat(row.payment_amount       || 0);
                        let late     = parseFloat(row.late_paid_amount     || 0);
                        let interest = parseFloat(row.interest_paid_amount || 0);
                        let discount = parseFloat(row.discount_amount      || 0);
                        let topUpCap = parseFloat(row.top_up_capital       || 0);
                        let topUp    = parseFloat(row.top_up               || 0);
                        let running  = parseFloat(row.running_payment      || 0);
                        let balance  = parseFloat(row.deducted_balance     || 0);

                        totalPayment  += payment;
                        totalLate     += late;
                        totalInterest += interest;
                        totalDiscount += discount;
                        totalTopUpCap += topUpCap;
                        totalTopUp    += topUp;
                        totalTotalPay += payment + topUp - topUpCap; // ← same logic as running_payment per row

                        let dateStr = '-';
                        if (row.pay_date) {
                            const parts = row.pay_date.substring(0, 10).split('-');
                            dateStr = `${parts[2]}-${parts[1]}-${parts[0]}`;
                        }

                        return [
                            index + 1,
                            row.payment_code    || '-',
                            row.customer_name   || '-',
                            row.collection_type || '-',
                            dateStr,
                            payment.toFixed(2),
                            late.toFixed(2),
                            interest.toFixed(2),
                            discount.toFixed(2),
                            topUpCap > 0 ? topUpCap.toFixed(2) : '-',
                            topUp    > 0 ? topUp.toFixed(2)    : '-',
                            running.toFixed(2),
                            balance.toFixed(2),
                        ];
                    });

                    doc.autoTable({
                        startY: 30,
                        head: [[
                            '#', 'Payment Code', 'Customer Name', 'Type', 'Date',
                            'Payment', 'Late', 'Interest', 'Discount',
                            'Top Up Cap', 'Top Up', 'Total Pay', 'Balance'
                        ]],
                        body: tableRows,
                        foot: [[
                            '', '', '', '', 'Total',
                            totalPayment.toFixed(2),
                            totalLate.toFixed(2),
                            totalInterest.toFixed(2),
                            totalDiscount.toFixed(2),
                            totalTopUpCap > 0 ? totalTopUpCap.toFixed(2) : '-',
                            totalTopUp    > 0 ? totalTopUp.toFixed(2)     : '-',
                            totalTotalPay.toFixed(2),  // ← sum of payment+topup-topUpCap
                            ''                         // ← balance left blank, it's a running value
                        ]],
                        theme: 'grid',
                        didParseCell: function (data) {
                            if (data.section === 'body' || data.section === 'foot') {
                                let colorCols = [5, 6, 7, 8, 11, 12];
                                if (colorCols.includes(data.column.index)) {
                                    let value = parseFloat(data.cell.raw || 0);
                                    if (value < 0)      data.cell.styles.textColor = [255, 0, 0];
                                    else if (value > 0) data.cell.styles.textColor = [0, 128, 0];
                                }
                                // top_up_cap (9) and top_up (10) red if > 0
                                if ([9, 10].includes(data.column.index)) {
                                    let value = parseFloat(data.cell.raw || 0);
                                    if (value > 0) data.cell.styles.textColor = [255, 0, 0];
                                }
                            }
                        },
                        headStyles: {
                            fillColor: [41, 128, 185],
                            textColor: 255,
                            fontStyle: 'bold',
                            fontSize:  7,
                            halign:    'center'
                        },
                        footStyles: {
                            fillColor: [236, 240, 241],
                            textColor: [0, 0, 0],
                            fontStyle: 'bold',
                            fontSize:  7.5
                        },
                        bodyStyles: { fontSize: 7 },
                        columnStyles: {
                            0:  { halign: 'center', cellWidth: 6  },
                            1:  { cellWidth: 25 },
                            2:  { cellWidth: 30 },
                            3:  { halign: 'center', cellWidth: 16 },
                            4:  { halign: 'center', cellWidth: 18 },
                            5:  { halign: 'right',  cellWidth: 18 },
                            6:  { halign: 'right',  cellWidth: 14 },
                            7:  { halign: 'right',  cellWidth: 14 },
                            8:  { halign: 'right',  cellWidth: 14 },
                            9:  { halign: 'right',  cellWidth: 16 },
                            10: { halign: 'right',  cellWidth: 14 },
                            11: { halign: 'right',  cellWidth: 18 },
                            12: { halign: 'right',  cellWidth: 18 },
                        },
                        didDrawPage: function (data) {
                            doc.setFontSize(8);
                            doc.setFont('helvetica', 'normal');
                            doc.text(
                                'Page ' + doc.internal.getCurrentPageInfo().pageNumber,
                                data.settings.margin.left,
                                doc.internal.pageSize.height - 8
                            );
                        }
                    });

                    let filename = 'customer_payment_report';
                    if (from) filename += '_' + from;
                    if (to)   filename += '_to_' + to;
                    filename += '.pdf';

                    doc.save(filename);
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