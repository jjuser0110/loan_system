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
    <div class="col-md-6 d-flex align-items-end justify-content-end">
        <button class="btn btn-primary w-100" id="btn-filter">
            {{ __('table.filter') }}
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
                            <th>{{ __('table.top_up') }}</th>
                            <th>{{ __('table.total_pay') }}</th>
                            <th>{{ __('table.balance') }}</th>
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
    let currentSkim = '';
    let table_customer_payment;

    function setSkimFilter(skim) {
        currentSkim = skim;
        $('#btn-skim-all').removeClass('btn-primary btn-outline-primary').addClass(skim === ''       ? 'btn-primary' : 'btn-outline-primary');
        $('#btn-skim-a').removeClass('btn-primary btn-outline-primary').addClass(skim === 'SKIM A'  ? 'btn-primary' : 'btn-outline-primary');
        $('#btn-skim-b').removeClass('btn-primary btn-outline-primary').addClass(skim === 'SKIM B'  ? 'btn-primary' : 'btn-outline-primary');
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
            processing:  true,
            serverSide:  true,
            fixedHeader: false,
            lengthMenu:  [[10, 100, 500, 1000], ['10', '100', '500', '1000']],
            searching:   false,
            stateSave:   true,
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
                { data: "payment_code",    name: "payment_code" },
                { data: "customer_name",   name: "customer_name" },
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
                        return `<span style="color:${v < 0 ? 'red' : 'green'}">${v.toFixed(2)}</span>`;
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
                    data: "top_up",
                    render: function (data) {
                        let v = data ? parseFloat(data) : 0;
                        return v > 0 ? `<span style="color:blue">${v.toFixed(2)}</span>` : '-';
                    }
                },
                {
                    data: "running_payment",
                    render: function (data) {
                        let v = data ? parseFloat(data) : 0;
                        return `<span style="color:green">${v.toFixed(2)}</span>`;
                    }
                },
                {
                    data: "deducted_balance",
                    render: function (data) {
                        let v = data !== null ? parseFloat(data) : 0;
                        return `<span style="color:${v < 0 ? 'red' : 'green'}">${v.toFixed(2)}</span>`;
                    }
                },
            ],
        });

        if (savedFrom || savedTo || savedCompany) {
            table_customer_payment.ajax.reload();
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

            table_customer_payment.ajax.reload();
        });

        $('#filter_search').on('keydown', function (e) {
            if (e.key === 'Enter') table_customer_payment.ajax.reload();
        });

        $('#btn-skim-all').on('click', function () { setSkimFilter(''); });
        $('#btn-skim-a').on('click',   function () { setSkimFilter('SKIM A'); });
        $('#btn-skim-b').on('click',   function () { setSkimFilter('SKIM B'); });
    });
</script>
@endsection