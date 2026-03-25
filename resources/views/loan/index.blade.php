@extends('layouts.app')
@section('content')
<header class="page-header">
    <h2>{{ __('table.loan') }}</h2>
</header>
@include('layouts.flash-message')
<div class="row mb-4" style="padding-top:40px;">
    
    @if(Auth::user()->role_id != 4)
    <div class="col-xl-4">
        <section class="card card-featured-left card-featured-primary mb-3">
            <div class="card-body">
                <div class="widget-summary">
                    <div class="widget-summary-col" style="vertical-align: middle">
                        <div class="summary" style="min-height:1px">
                            <h4 class="title" style="margin-bottom: 5px">{{ __('table.total_profits') }}</h4>
                            <div class="info">
                                <strong class="amount">RM <span style="font-size:1.4rem;vertical-align:unset" id="total-profit">{{ __('table.loading') }}...</span></strong>
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
                            <h4 class="title" style="margin-bottom: 5px">{{ __('table.total_balance') }}</h4>
                            <div class="info">
                                <strong class="amount">RM {{ number_format($total_balance,2,'.',',') }}</strong>
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
                <table class="table cus-table table-bordered table-striped mb-0" id="table-loan">
                    <thead>
                        <tr>
                            <th>{{ __('table.loan_code') }}</th>
                            <th>{{ __('table.company') }}</th>
                            <th>{{ __('table.interest_group') }}</th>
                            <th>{{ __('table.loan_date') }}</th>
                            <th>{{ __('table.due_date') }}</th>
                            <th>{{ __('table.last_pay_date') }}</th>
                            <th>{{ __('table.loan_amount') }}</th>
                            <th>{{ __('table.capital') }}</th>
                            <th>{{ __('table.paid') }}</th>
                            <th>{{ __('table.outstanding') }}</th>
                            <th>{{ __('table.loan_term') }}</th>
                            <th>{{ __('table.installment') }}</th>
                            <th>{{ __('table.interest_rate') }}</th>
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
            "autoWidth": false, // ✅ required for manual resizing
            "lengthMenu": [[10, 100, 500, 1000], ['10', '100', '500', '1000']],
            "ajax": {
                "url": "{{ route('loan.load_loan') }}",
                "type": "GET"
            },
            "order": [[0, "desc"]],
            "columns": [
                { "data": "loan_code" },
                {
                    "data": "company_code",
                    "render": function(data, type, row, meta) {
                        return `<a style="text-decoration:none" onclick="e.preventDefault()">${row.company_code}<br>${row.company_name}</a>`;
                    }
                },
                { "data": "interest_group" },
                {
                    "data": "created_at",
                    "render": function(data, type, row, meta) {
                        if (!data) return '-';
                        const parts = data.substring(0, 10).split('-');
                        return `${parts[2]}-${parts[1]}-${parts[0]}`;
                    }
                },
                {
                    "data": "next_due_date",
                    "render": function(data, type, row, meta) {
                        if (!data) return '-';
                        const parts = data.substring(0, 10).split('-');
                        return `${parts[2]}-${parts[1]}-${parts[0]}`;
                    }
                },
                {
                    "data": "updated_at",
                    "defaultContent": "-",
                    "render": function(data, type, row, meta) {
                        if (!data) return '-';
                        const parts = data.substring(0, 10).split('-');
                        return `${parts[2]}-${parts[1]}-${parts[0]}`;
                    }
                },
                { "data": "loan_amount" },
                { "data": "capital" },
                { "data": "paid" },
                { "data": "outstanding" },
                {
                    "data": "loan_term",
                    "render": function(data, type, row, meta) {
                        return row.interest_group == 'SKIM B' ? row.loan_term : '-';
                    }
                },
                {
                    "data": "installment",
                    "render": function(data, type, row, meta) {
                        let installment = `${row.installment}`;
                        if(row.interest_group == "SKIM B"){
                            installment = `${row.installment}<br><span style="color:#7c7c7c;font-size:12px">First: ${row.first_payment}</span><br><span style="color:#7c7c7c;font-size:12px">Last: ${row.last_payment}</span>`;
                        }
                        return `<a style="text-decoration:none" onclick="e.preventDefault()">${installment}</a>`;
                    }
                },
                {
                    "data": "interest_rate",
                    "render": function(data, type, row, meta) {
                        return data + "%";
                    }
                },
                {
                    "data": "status",
                    "render": function(data, type, row, meta) {
                        const green = ['Active', 'Fully Paid'];
                        const red = ['Overdue', 'Bad Debt', 'Blacklist'];
                        let clr = green.includes(data) ? 'green' : (red.includes(data) ? 'red' : '#000000');
                        return `<span style="color:${clr}">${data}</span>`;
                    }
                },
                {
                    "data": null,
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
            }
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