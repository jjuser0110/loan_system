@extends('layouts.app')
@section('content')
<header class="page-header">
    <h2>{{ __('table.loan_listing_by_interest') }}</h2>
</header>
@include('layouts.flash-message')

<div class="row mb-3">
    <div class="col-md-2">
        <label>{{ __('table.date_field') }}</label>
        <select id="filter_date_field" class="form-control">
            <option value="loan_date">{{ __('table.loan_date') }}</option>
            <option value="next_due_date">{{ __('table.next_date') }}</option>
        </select>
    </div>
    <div class="col-md-2">
        <label>{{ __('table.from_date') }}</label>
        <input type="date" id="filter_from_date" class="form-control">
    </div>
    <div class="col-md-2">
        <label>{{ __('table.to_date') }}</label>
        <input type="date" id="filter_to_date" class="form-control">
    </div>
    <div class="col-md-2">
        <label>{{ __('table.company') }}</label>
        <select id="filter_company" class="form-control">
            <option value="">{{ __('table.all_companies') }}</option>
            @foreach($companies as $company)
                <option value="{{ $company->id }}">
                    {{ $company->company_name }} ({{ $company->company_code }})
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2">
        <label>{{ __('table.interest_group') }}</label>
        <select id="filter_interest_group" class="form-control">
            <option value="">{{ __('table.all_groups') }}</option>
            @foreach($interestGroups as $group)
                <option value="{{ $group }}">{{ $group }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2">
        <label>{{ __('table.status') }}</label>
        <select id="filter_status" class="form-control">
            <option value="">{{ __('table.all_status') }}</option>
            <option value="Active">Active</option>
            <option value="Closed">Closed</option>
            <option value="Overdue">Overdue</option>
        </select>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-4">
        <label>{{ __('table.search') }} ({{ __('table.customer_name') }} / {{ __('table.loan_code') }} / {{ __('table.status') }})</label>
        <input type="text" id="filter_search" class="form-control" placeholder="{{ __('table.search') }}...">
    </div>
    <div class="col-md-4 d-flex align-items-end">
        <button class="btn btn-primary me-2" id="btn-filter">{{ __('table.filter') }}</button>
        <a href="#" id="btn-print" class="btn btn-outline-secondary" target="_blank">
            🖨 {{ __('table.print_export_pdf') }}
        </a>
    </div>
</div>

<div class="row" style="padding-top:0">
    <div class="col-lg-12 mb-3">
        <section class="card">
            <div class="card-body">
                <table class="table cus-table table-bordered table-striped mb-0" id="table-loan-list">
                    <thead>
                        <tr>
                            <th style="display:none">{{ __('table.interest_group') }}</th>
                            <th>{{ __('table.system_code') }}</th>
                            <th>{{ __('table.customer_name') }}</th>
                            <th>{{ __('table.loan_code') }}</th>
                            <th>{{ __('table.user') }}</th>
                            <th>{{ __('table.loan_date') }}</th>
                            <th>{{ __('table.next_date') }}</th>
                            <th>{{ __('table.last_pay') }}</th>
                            <th>{{ __('table.total_to_collect') }}</th>
                            <th>{{ __('table.loan_amt') }}</th>
                            <th>{{ __('table.interest_collect') }}</th>
                            <th>{{ __('table.top_up') }}</th>
                            <th>{{ __('table.process_fee') }}</th>
                            <th>{{ __('table.capital') }}</th>
                            <th>{{ __('table.discount') }}</th>
                            <th>{{ __('table.loan_balance') }}</th>
                            <th>{{ __('table.loan_status') }}</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot>
                        <tr>
                            <th style="display:none"></th>
                            <th colspan="7" class="text-end">{{ __('table.grand_total') }}:</th>
                            <th id="gt_total_to_collect"></th>
                            <th id="gt_loan_amount"></th>
                            <th id="gt_interest_collect"></th>
                            <th id="gt_top_up"></th>
                            <th id="gt_processing_fee"></th>
                            <th id="gt_capital"></th>
                            <th id="gt_discount"></th>
                            <th id="gt_loan_balance"></th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </section>
    </div>
</div>
@endsection

@section('page-js')
<link rel="stylesheet" href="https://cdn.datatables.net/rowgroup/1.4.1/css/rowGroup.dataTables.min.css">
@endsection

@section('scripts')
<script src="https://cdn.datatables.net/rowgroup/1.4.1/js/dataTables.rowGroup.min.js"></script>
<script>
    let table_loan_list;
    const money = v => (parseFloat(v) || 0).toLocaleString('en-MY', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    function currentFilters() {
        return {
            date_field:     $('#filter_date_field').val(),
            from_date:      $('#filter_from_date').val(),
            to_date:        $('#filter_to_date').val(),
            company_id:     $('#filter_company').val(),
            interest_group: $('#filter_interest_group').val(),
            status:         $('#filter_status').val(),
            search:         $('#filter_search').val(),
        };
    }

    function buildPrintUrl() {
        const params = new URLSearchParams(currentFilters());
        return "{{ route('report.loan_list_pdf') }}?" + params.toString();
    }

    $(document).ready(function () {
        table_loan_list = $('#table-loan-list').DataTable({
            processing: true,
            serverSide: false,
            searching: true,
            paging: true,
            info: true,
            order: [[0, 'asc'], [3, 'asc']],   // 0 = hidden interest_group, 3 = loan_code
            columnDefs: [
                { targets: 0, visible: false, searchable: false }
            ],
            rowGroup: {
                dataSrc: 'interest_group',
                startRender: function (rows, group) {
                    const totals = {
                        total_to_collect: 0, loan_amount: 0, interest_collect: 0,
                        top_up: 0, processing_fee: 0, capital: 0, discount: 0, loan_balance: 0,
                    };
                    rows.data().each(function (row) {
                        Object.keys(totals).forEach(k => totals[k] += parseFloat(row[k]) || 0);
                    });
                    return $('<tr/>')
                        .append(`<td colspan="8"><strong>${group}</strong> (${rows.count()} @lang('table.loan_code'))</td>`)
                        .append(`<td><strong>${money(totals.total_to_collect)}</strong></td>`)
                        .append(`<td><strong>${money(totals.loan_amount)}</strong></td>`)
                        .append(`<td><strong>${money(totals.interest_collect)}</strong></td>`)
                        .append(`<td><strong>${money(totals.top_up)}</strong></td>`)
                        .append(`<td><strong>${money(totals.processing_fee)}</strong></td>`)
                        .append(`<td><strong>${money(totals.capital)}</strong></td>`)
                        .append(`<td><strong>${money(totals.discount)}</strong></td>`)
                        .append(`<td><strong>${money(totals.loan_balance)}</strong></td>`)
                        .append(`<td></td>`);
                }
            },
            ajax: {
                url: "{{ route('report.load_loan_list') }}",
                type: 'GET',
                data: function (d) { return Object.assign(d, currentFilters()); }
            },
            columns: [
                { data: 'interest_group' },
                { data: 'system_code' },
                { data: 'customer_name' },
                { data: 'loan_code' },
                { data: 'user' },
                { data: 'loan_date' },
                { data: 'next_due_date' },
                { data: 'last_pay_date' },
                { data: 'total_to_collect', render: money },
                { data: 'loan_amount', render: money },
                { data: 'interest_collect', render: money },
                { data: 'top_up', render: money },
                { data: 'processing_fee', render: money },
                { data: 'capital', render: money },
                { data: 'discount', render: money },
                { data: 'loan_balance', render: money },
                { data: 'status' },
            ],
            footerCallback: function () {
                const api = this.api();
                const totals = {
                    total_to_collect: 0, loan_amount: 0, interest_collect: 0,
                    top_up: 0, processing_fee: 0, capital: 0, discount: 0, loan_balance: 0,
                };
                api.rows({ search: 'applied' }).data().each(function (row) {
                    Object.keys(totals).forEach(k => totals[k] += parseFloat(row[k]) || 0);
                });
                $('#gt_total_to_collect').html(money(totals.total_to_collect));
                $('#gt_loan_amount').html(money(totals.loan_amount));
                $('#gt_interest_collect').html(money(totals.interest_collect));
                $('#gt_top_up').html(money(totals.top_up));
                $('#gt_processing_fee').html(money(totals.processing_fee));
                $('#gt_capital').html(money(totals.capital));
                $('#gt_discount').html(money(totals.discount));
                $('#gt_loan_balance').html(money(totals.loan_balance));
            }
        });

        $('#btn-print').attr('href', buildPrintUrl());
    });

    $('#btn-filter').on('click', function () {
        table_loan_list.ajax.reload();
        $('#btn-print').attr('href', buildPrintUrl());
    });
</script>
@endsection