@extends('layouts.app')

@section('content')
<header class="page-header">
    <h2>{{ __('table.reference') }}</h2>
</header>

@include('layouts.flash-message')

<!-- start: page -->
<div class="row">
    <div class="col-lg-12 mb-3">
        <section class="card">
            <div class="card-body">
                <table class="table table-bordered table-striped mb-0" id="table-reference">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>{{ __('table.customer_name') }}</th>
                            <th>{{ __('table.relationship') }}</th>
                            <th>NRIC</th>
                            <th>{{ __('table.reference_name') }}</th>
                            <th>{{ __('table.designation') }}</th>
                            <th>{{ __('table.mobile') }}</th>
                            <th>{{ __('table.city') }}</th>
                            <th>{{ __('table.state') }}</th>
                            <th>{{ __('table.email') }}</th>
                            <th>{{ __('table.created_at') }}</th>
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
    let table_reference;
    $(document).ready(function() {
        table_reference = $('#table-reference').DataTable({
            "processing": true,
            "serverSide": true,
            "fixedHeader": false,
            "lengthMenu": [[10, 100, 500, 1000], ['10', '100', '500', '1000']],
            "pageLength": 10,
            "ajax": {
                "url": "{{ route('reference.fetch') }}",
                "type": "GET"
            },
            "order": [[0, "desc"]],
            "columns": [
                { "data": "id" },
                {
                    "data": "customer_name",
                    "render": function(data, type, row) {
                        return '<a href="{{ url('customer') }}/' + row.customer_id + '/edit#reference">' + (row.customer_name ?? '') + '</a>';
                    }
                },
                { "data": "reference_type" },
                { "data": "new_ic" },
                { "data": "name" },
                { "data": "designation" },
                { "data": "mobile" },
                { "data": "city" },
                { "data": "state" },
                { "data": "email" },
                {
                    "data": "created_at",
                    "render": function(data) {
                        if (!data) return '-';
                        const parts = data.substring(0, 10).split('-');
                        return `${parts[2]}-${parts[1]}-${parts[0]}`;
                    }
                },
                {
                    "data": null,
                    "render": function(data, type, row) {
                        let actions = `<a href="{{ url('customer') }}/${row.customer_id}/edit#reference" title="Edit"><i class="bx bx-edit-alt"></i></a>`;
                        @if(Auth::user()->role_id != 4)
                        actions += ` &nbsp; <a onclick="if(confirm('Are you sure you want to delete?')){window.location.href='{{ route('reference.destroy', '') }}/${row.id}'}" title="Delete" style="cursor:pointer"><i class="bx bx-trash"></i></a>`;
                        @endif
                        return actions;
                    }
                }
            ],
            "drawCallback": function() {
                // Add spacing below the entries dropdown
                $('div.dataTables_length').css('margin-bottom', '15px');
            }
        });
    });
</script>
@endsection