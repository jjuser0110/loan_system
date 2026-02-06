@extends('layouts.app')

@section('content')
<header class="page-header">
    <h2>{{ __('table.company') }}</h2>
</header>

@include('layouts.flash-message')

<!-- start: page -->
<div class="row">
    <div class="col-lg-12 mb-3">
        <section class="card">
            <div class="card-header" style="text-align: right;">
                <a class="btn btn-xs btn-square btn-primary" href="{{route('company.create')}}">{{ __('table.create') }}</a>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped mb-0" id="table-company">
                    <thead>
                        <tr>
                            <th>{{ __('table.company_code') }}</th>
                            <th>{{ __('table.company_name') }}</th>
                            <th>{{ __('table.branch') }}</th>
                            <th>{{ __('table.stock_a') }}</th>
                            <th>{{ __('table.stock_b') }}</th>
                            <th>{{ __('table.stock_bb') }}</th>
                            <th>{{ __('table.amount') }}</th>
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
    $(document).ready(function() {
        $('#table-company').DataTable({
            "processing": true,
            "serverSide": true,
            "fixedHeader": false,
            "ajax": {
                "url": "{{ route('company.load_company') }}",
                "type": "GET"
            },
            "order": [
                [0, "desc"]
            ],
            "columns": [
                {
                    "data": "company_code"
                },
                {
                    "data": "company_name"
                },
                {
                    "data": "branch.branch_name"
                },
                {
                    "data": "stocka",
                     "render": function(data, type, row, meta) {
                        return formatCredit(row.stocka)
                     }
                },
                {
                    "data": "stockb",
                     "render": function(data, type, row, meta) {
                        return formatCredit(row.stockb)
                     }
                },
                {
                    "data": "stockbb",
                     "render": function(data, type, row, meta) {
                        return formatCredit(row.stockbb)
                     }
                },
                {
                    "data": "total_amount",
                     "render": function(data, type, row, meta) {
                        return formatCredit(row.total_amount ?? 0)
                     }
                },
                {
                    "data": null,
                    "render": function(data, type, row, meta) {
                        
                        let url = `
                            <div class="cus-action-wrapper">
                                <a href="{{ route('company.edit', ['company' => ':company']) }}" class="cus-action-icon info" title="Update Company" target="_blank"><i class="fas fa-edit"></i></a>
                                <a onclick="if(confirm('Are you sure you want to delete?')){window.location.href='{{ route('company.destroy',1) }}'}" title = "Delete" style="cursor:pointer"><i class="bx bx-trash"></i></a>
                            </div>
                            `;
                        url = url.replaceAll(':company', row.id);
                        return url;
                    }
                }
            ]
        });
    });
</script>
@endsection