@extends('layouts.app')

@section('content')
<header class="page-header">
    <h2>{{ __('table.customer') }}</h2>
</header>

@include('layouts.flash-message')

<!-- start: page -->
<div class="row">
    <div class="col-lg-12 mb-3">
        <section class="card">
            <div class="card-header" style="text-align: right;">
                <a class="btn btn-xs btn-square btn-primary" href="{{route('customer.create')}}">{{ __('table.create') }}</a>
            </div>
            <div class="card-body">
                <table class="table cus-table table-bordered table-striped mb-0" id="table-customer">
                    <thead>
                        <tr>
                            <th>{{ __('table.customer') }}</th>
                            <th>NRIC</th>
                            <th>{{ __('table.mobile') }}</th>
                            <th>{{ __('table.customer_company') }}</th>
                            <th>{{ __('table.address') }}</th>
                            <th>{{ __('table.email') }}</th>
                            @if(Auth::user()->role_id == 1)
                            <th>{{ __('table.company') }}</th>
                            <th>{{ __('table.branch') }}</th>
                            @endif
                            <th>{{ __('table.stats') }}</th>
                            <th>{{ __('table.created_at') }}</th>
                            <th>{{ __('table.action') }}</th>
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
    let table_customer;
    $(document).ready(function() {
        table_customer = $('#table-customer').DataTable({
            "processing": true,
            "serverSide": true,
            "fixedHeader": false,
            "ajax": {
                "url": "{{ route('customer.fetch') }}",
                "type": "GET"
            },
            "order": [
                [0, "desc"]
            ],
            "columns": [
                {
                    "data": "customer_code",
                    "render": function(data, type, row, meta) {
                        return '<a href="{{ url('customer') }}/' + row.id + '/edit">' +row.customer_code + "<br>" +row.customer_name+'</a>';
                    }
                },
                {
                    "data": "nric_number"
                },
                {
                    "data": "mobile"
                },
                {
                    "data": "customer_company"
                },
                {
                    "data": "address1",
                    "render": function(data, type, row, meta) {
                        return (row.address1 ?? "-") + "<br>" + (row.postcode ?? "-") + "<br>" + (row.city ?? "-");
                    }
                },
                {
                    "data": "email"
                },
                @if(Auth::user()->role_id == 1)
                {
                    "data": "branch_name",
                    "render": function(data, type, row, meta) {
                        return row.branch_code + "<br>" + row.branch_name;
                    }
                },
                {
                    "data": "company_name",
                    "render": function(data, type, row, meta) {
                        return row.company_code + "<br>" + row.company_name;
                    }
                },
                @endif
                {
                    "data": "status",
                    "render": function(data, type, row, meta) {
                        const green = ['active', 'fully_paid'];
                        const red = ['overdue', 'bad_debt', 'blacklist'];
                        
                        let clr = green.includes(data) ? 'green' : (red.includes(data) ? 'red' : '#000000');
                        let label = data.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
                        
                        return `<span style="color:${clr}">${label}</span>`;
                    }
                },
                {
                    "data": "created_at",
                    "render": function(data, type, row, meta) {
                        return formatDate(row.created_at);
                    }
                },
                {
                    "data": null,
                    "render": function(data, type, row, meta) {
                        let a = `
                            <div class="cus-action-wrapper">
                                <a class="cus-action-icon info" title="Update Customer" href="{{ route('customer.edit', ['customer' => ':customer']) }}"><i class="fas fa-edit"></i></a>
                                @if(Auth::user()->role_id == 1)
                                <a class="cus-action-icon danger" title="Delete Customer" onclick="deleteCustomer(${meta.row})"><i class="fas fa-trash-alt"></i></a>
                                @endif
                            </div>
                        `;
                        a = a.replaceAll(':customer', row.id);
                        return a;
                    }
                }
            ]
        });
    });

    function deleteCustomer(rowIndex) {
        const data = table_customer.row(rowIndex).data();
        function submitDelete(){
            $.ajax({
                url: "{{ route('customer.delete') }}",
                type: "POST",
                data: {customer_id:data.id},
                headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}"},
                success: function (response) {
                    if(response.success == true){
                        setReloadSwal('success','',response.message);
                    }
                    else{
                        setDefaultSwal('error','',response.message);
                    }
                },
                error: function (xhr) {
                    setDefaultSwal('error','','There is something wrong, please try again.');
                }
            });
        }
        setConfirmationSwal(
            "Warning",
            "This action cannot be undone. Proceed?",
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
