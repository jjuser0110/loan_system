@extends('layouts.app')
@section('content')
<style>
    #table-loan tbody tr.row-fully-paid td {
        background-color: rgba(255, 0, 0, 0.1) !important;
    }
    #table-loan tbody tr.row-overdue td {
        background-color: rgba(255, 253, 112, 0.35) !important;
    }
    #table-loan tbody tr.row-active td {
        background-color: rgba(0, 200, 0, 0.1) !important;
    }
</style>

<header class="page-header">
    <h2>{{ __('table.loan') }}</h2>
</header>
@include('layouts.flash-message')
<div class="row mb-4" style="padding-top:40px;">
    
    @if(Auth::user()->role_id != 4)

    <div class="col-xl-4">
        <section class="card card-featured-left card-featured-secondary mb-3" style="background-color:#d6eaff;">
            <div class="card-body" style="background: transparent;">
                <div class="widget-summary">
                    <div class="widget-summary-col" style="vertical-align: middle">
                        <div class="summary" style="min-height:1px">
                            <h4 class="title" style="margin-bottom: 5px">{{ __('table.total_stock_a') }}</h4>
                            <div class="info">
                                <strong class="amount">${{ $companies->total_stocka ?? 0.00 }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="col-xl-4">
        <section class="card card-featured-left card-featured-secondary mb-3" style="background-color:#d6eaff;">
            <div class="card-body" style="background: transparent;">
                <div class="widget-summary">
                    <div class="widget-summary-col" style="vertical-align: middle">
                        <div class="summary" style="min-height:1px">
                            <h4 class="title" style="margin-bottom: 5px">{{ __('table.total_stock_b') }}</h4>
                            <div class="info">
                                <strong class="amount">${{ $companies->total_stockb ?? 0.00 }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="col-xl-4">
        <section class="card card-featured-left card-featured-secondary mb-3" style="background-color:#d6eaff;">
            <div class="card-body" style="background: transparent;">
                <div class="widget-summary">
                    <div class="widget-summary-col" style="vertical-align: middle">
                        <div class="summary" style="min-height:1px">
                            <h4 class="title" style="margin-bottom: 5px">{{ __('table.total_stock_bb') }}</h4>
                            <div class="info">
                                <strong class="amount">${{ $companies->total_stockbb ?? 0.00 }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="col-xl-4">
        <section class="card card-featured-left card-featured-secondary mb-3">
            <div class="card-body">
                <div class="widget-summary">
                    <div class="widget-summary-col" style="vertical-align: middle">
                        <div class="summary" style="min-height:1px">
                            <h4 class="title" style="margin-bottom: 5px">{{ __('table.total_loan_amount') }}</h4>
                            <div class="info">
                                <strong class="amount">RM {{ number_format($total_loan_amount,2,'.',',') }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    
    <div class="col-xl-4">
        <section class="card card-featured-left card-featured-secondary mb-3">
            <div class="card-body">
                <div class="widget-summary">
                    <div class="widget-summary-col" style="vertical-align: middle">
                        <div class="summary" style="min-height:1px">
                            <h4 class="title" style="margin-bottom: 5px">{{ __('table.total_capital') }}</h4>
                            <div class="info">
                                <strong class="amount">RM {{ number_format($total_capital,2,'.',',') }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="col-xl-4">
        <section class="card card-featured-left card-featured-secondary mb-3">
            <div class="card-body">
                <div class="widget-summary">
                    <div class="widget-summary-col" style="vertical-align: middle">
                        <div class="summary" style="min-height:1px">
                            <h4 class="title" style="margin-bottom: 5px">{{ __('table.total_outstanding') }}</h4>
                            <div class="info">
                                <strong class="amount">RM {{ number_format($outstanding,2,'.',',') }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    
    @endif
</div>

<style>
    /* ✅ Resizable column handles */
    #table-loan thead th {
        position: relative;
        overflow: hidden;
        white-space: nowrap;
    }
    #table-loan thead th .col-resize-handle {
        position: absolute;
        right: 0;
        top: 0;
        width: 6px;
        height: 100%;
        cursor: col-resize;
        user-select: none;
        z-index: 1;
    }
    #table-loan thead th .col-resize-handle:hover,
    #table-loan thead th .col-resize-handle.resizing {
        border-right: 2px solid #a0a0a0;
    }
</style>

<div class="row" style="padding-top:0">
    <div class="col-lg-12 mb-3">
        <section class="card">
            <div class="card-body">
                <div style="margin-bottom: 10px;">
                    <label style="cursor:pointer; user-select:none;">
                        <input type="checkbox" id="hide-fully-paid" checked>
                        &nbsp;{{ __('table.hide_fully_paid') }}
                    </label>
                </div>
                <table class="table cus-table table-bordered mb-0" id="table-loan">
                    <thead>
                        <tr>
                            <th>{{ __('table.customer') }}</th>
                            <th>{{ __('table.loan_code') }}</th>
                            <th>{{ __('table.company') }}</th>
                            <th>{{ __('table.interest_group') }}</th>
                            <th>{{ __('table.created_date') }}</th>
                            <th>{{ __('table.payment_due') }}</th>
                            <th>{{ __('table.last_payment') }}</th>
                            <th>{{ __('table.loan_amount') }}</th>
                            <th>{{ __('table.capital') }}</th>
                            <th>{{ __('table.paid') }}</th>
                            <th>{{ __('table.outstanding') }}</th>
                            <th>{{ __('table.loan_term') }}</th>
                            <th>{{ __('table.installment') }}</th>
                            <th>{{ __('table.interest_rate') }}</th>
                            <th>{{ __('table.int') }}</th>
                            <th>{{ __('table.late') }}</th>
                            <th>{{ __('table.status') }}</th>
                            <th>{{ __('table.actions') }}</th>
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
<script>
    let table_loan;
    $(document).ready(function() {
        table_loan = $('#table-loan').DataTable({
            "processing": true,
            "serverSide": true,
            "fixedHeader": false,
            "autoWidth": false,
            "lengthMenu": [[10, 100, 500, 1000], ['10', '100', '500', '1000']],
            "ajax": {
                "url": "{{ route('loan.load_loan') }}",
                "type": "GET",
                "data": function(d) {
                    d.hide_fully_paid = $('#hide-fully-paid').is(':checked') ? 1 : 0;
                }
            },
            "order": [[6, "desc"]],
            "columns": [
                {
                    "data": "customer_code",
                    "name": "customer_code",
                    "render": function(data, type, row, meta) {
                        return '<a href="{{ url('customer') }}/' + row.customer_id + '/edit#loan">' + row.customer_code + "<br>" + row.customer_name + '</a>';
                    }
                },
                {
                    "data": "loan_code",
                    "name": "loan_code"
                },
                {
                    "data": "company_code",
                    "name": "company_code",
                    "render": function(data, type, row, meta) {
                        return `<a style="text-decoration:none" onclick="e.preventDefault()">${row.company_code}<br>${row.company_name}</a>`;
                    }
                },
                {
                    "data": "interest_group",
                    "name": "interest_group"
                },
                {
                    "data": "created_at",
                    "name": "created_at",
                    "render": function(data, type, row, meta) {
                        if (!data) return '-';
                        const parts = data.substring(0, 10).split('-');
                        return `${parts[2]}-${parts[1]}-${parts[0]}`;
                    }
                },
                {
                    "data": "next_due_date",
                    "name": "next_due_date",
                    "render": function(data, type, row, meta) {
                        if (!data) return '-';
                        const parts = data.substring(0, 10).split('-');
                        return `${parts[2]}-${parts[1]}-${parts[0]}`;
                    }
                },
                {
                    "data": "updated_at",
                    "name": "updated_at",
                    "defaultContent": "-",
                    "render": function(data, type, row, meta) {
                        if (!data) return '-';
                        const parts = data.substring(0, 10).split('-');
                        return `${parts[2]}-${parts[1]}-${parts[0]}`;
                    }
                },
                {
                    "data": "loan_amount",
                    "name": "loan_amount"
                },
                {
                    "data": "capital",
                    "name": "capital"
                },
                {
                    "data": "paid",
                    "name": "paid",
                    "render": function(data, type, row, meta) {
                        return `<strong>${data}</strong>`;
                    }
                },
                {
                    "data": "outstanding",
                    "name": "outstanding",
                    "render": function(data, type, row, meta) {
                        return `<strong>${data}</strong>`;
                    }
                },
                {
                    "data": "loan_term",
                    "name": "loan_term",
                    "render": function(data, type, row, meta) {
                        return row.interest_group == 'SKIM B' ? row.loan_term : '-';
                    }
                },
                {
                    "data": "installment",
                    "name": "installment",
                    "render": function(data, type, row, meta) {
                        let installment = `${row.installment}`;
                        // if(row.interest_group == "SKIM B"){
                        //     installment = `${row.installment}<br><span style="color:#7c7c7c;font-size:12px">First: ${row.first_payment}</span><br> <span style="color:#7c7c7c;font-size:12px">Last: ${row.last_payment}</span>`;
                        // }
                        return `<strong>${installment}</strong>`;
                    }
                },
                {
                    "data": "interest_rate",
                    "name": "interest_rate",
                    "render": function(data, type, row, meta) {
                        return parseFloat(data).toFixed(2);
                    }
                },
                {
                    "data": "interest_paid",
                    "name": "interest_paid"
                },
                {
                    "data": "late_paid",
                    "name": "late_paid"
                },
                {
                    "data": "status",
                    "name": "status",
                    "render": function(data, type, row, meta) {
                        const green = ['Active'];
                        const red = ['Fully Paid'];
                        const yellow = ['Overdue', 'Bad Debt', 'Blacklist'];
                        let clr = green.includes(data) ? 'green' : red.includes(data) ? 'red' : (yellow.includes(data) ? '#7a6800' : '#7a6800');
                        return `<span style="color:${clr}">${data}</span>`;
                    }
                },
                {
                    "data": null,
                    "name": "actions",
                    "searchable": false,
                    "orderable": false,
                    "render": function(data, type, row, meta) {
                        let url = `
                            <div class="cus-action-wrapper">
                                <a href="{{ route('loan.single_loan', ['loan_code' => ':loan_code']) }}" 
                                class="cus-action-icon info" 
                                title="View Detail">
                                <i class="fas fa-eye"></i>
                                </a>

                                <a href="{{ route('schedule.create', ['loan_code' => ':loan_code']) }}" 
                                class="cus-action-icon" 
                                style="background:#6c757d; color:white;"
                                title="Create Schedule">
                                <i class="fas fa-calendar-alt"></i>
                                </a>
                                
                                <a href="{{ route('payment.create', ['loan_code' => ':loan_code']) }}" 
                                class="cus-action-icon" 
                                style="background:#28a745; color:white;"
                                title="Create Payment">
                                <i class="fas fa-money-check-alt"></i>
                                </a>

                                @if(Auth::user()->role_id == 1)
                                <a class="cus-action-icon danger" 
                                title="Delete Loan" 
                                onclick="deleteLoan(${meta.row})">
                                <i class="fas fa-trash-alt"></i>
                                </a>
                                @endif
                            </div>`;
                        url = url.replaceAll(':loan_code', row.loan_code);
                        return url;
                    }
                }
            ],
            "drawCallback": function() {
                initResizable();

                $('#table-loan tbody tr').each(function() {
                    // ✅ FIX: was nth-child(16), status is the 17th column
                    const statusCell = $(this).find('td:nth-child(17)').text().trim();
                    $(this).removeClass('row-fully-paid row-overdue row-active');

                    if (statusCell === 'Fully Paid') {
                        $(this).addClass('row-fully-paid');
                    } else if (statusCell === 'Overdue') {
                        $(this).addClass('row-overdue');
                    } else if (statusCell === 'Active') {
                        $(this).addClass('row-active');
                    }
                });
            }
        });

        $('#hide-fully-paid').on('change', function() {
            table_loan.draw();
        });

    });

    function initResizable() {
    const table = document.getElementById('table-loan');
    const cols = table.querySelectorAll('thead th');

    cols.forEach(function(col) {
        if (col.querySelector('.col-resize-handle')) return;

        const handle = document.createElement('div');
        handle.classList.add('col-resize-handle');
        col.appendChild(handle);

        let startX, startW, isDragging = false;

        handle.addEventListener('mousedown', function(e) {
            startX = e.pageX;
            startW = col.offsetWidth;
            isDragging = false;
            handle.classList.add('resizing');

            e.stopPropagation();
            e.preventDefault();

            document.addEventListener('mousemove', onMouseMove);
            document.addEventListener('mouseup', onMouseUp);
        });

        function onMouseMove(e) {
            isDragging = true;
            const diff = e.pageX - startX;
            col.style.width = (startW + diff) + 'px';
            col.style.minWidth = (startW + diff) + 'px';
        }

        function onMouseUp() {
            handle.classList.remove('resizing');
            document.removeEventListener('mousemove', onMouseMove);
            document.removeEventListener('mouseup', onMouseUp);

            if (isDragging) {
                document.addEventListener('click', blockClick, true);
            }
        }

        function blockClick(e) {
            e.stopPropagation();
            document.removeEventListener('click', blockClick, true);
        }
    });
}

    fetch(`{{ route('loan.fetch_profit') }}`, {
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('total-profit').innerHTML = data.total_profit ?? '0.00';
    })
    .catch(error => {
        console.error('Search failed:', error);
    });

    function deleteLoan(rowIndex) {
        const data = table_loan.row(rowIndex).data();
        function submitDelete(){
            $.ajax({
                url: "{{ route('loan.delete') }}",
                type: "POST",
                data: {id: data.id},
                headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                success: function (response) {
                    if(response.success == true){
                        setReloadSwal('success', '', response.message);
                    } else {
                        setDefaultSwal('error', '', response.message);
                    }
                },
                error: function (xhr) {
                    setDefaultSwal('error', '', 'There is something wrong, please try again.');
                }
            });
        }
        setConfirmationSwal(
            "Warning",
            "This action will cannot be undone. Proceed?",
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