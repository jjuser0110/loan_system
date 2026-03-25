@extends('layouts.app')

@section('content')
<header class="page-header">
    <h2>{{ __('table.reference') }}</h2>
</header>

@include('layouts.flash-message')

<div class="row">
    <div class="col-lg-12 mb-3">
        <section class="card">
            <div class="card-body">
                <table class="table cus-table table-bordered table-striped mb-0" id="table-reference">
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
<style>
    #table-reference thead th {
        position: relative;
        min-width: 50px;
    }
    .col-resize-handle {
        position: absolute;
        right: 0;
        top: 0;
        bottom: 0;
        width: 8px;
        cursor: col-resize;
        z-index: 9999;
        background: transparent;
    }
    .col-resize-handle:hover {
        background: rgba(0, 136, 204, 0.4);
    }
</style>

<script>
    let table_reference;

    function makeResizable(tableId, dtInstance) {
        $('#' + tableId + ' thead th').each(function() {
            $(this).css('position', 'relative');

            const handle = $('<div class="col-resize-handle">').appendTo(this);

            handle.on('mousedown', function(e) {
                const th = $(this).parent();
                const startX = e.pageX;
                const startWidth = th.outerWidth();

                dtInstance.settings()[0].aoColumns.forEach(function(col) {
                    col.bSortable = false;
                });

                $(document).on('mousemove.colresize', function(e) {
                    const newWidth = startWidth + (e.pageX - startX);
                    if (newWidth > 50) {
                        th.css('width', newWidth + 'px');
                        th.css('min-width', newWidth + 'px');
                    }
                });

                $(document).on('mouseup.colresize', function() {
                    $(document).off('mousemove.colresize mouseup.colresize');
                    setTimeout(function() {
                        dtInstance.settings()[0].aoColumns.forEach(function(col) {
                            col.bSortable = true;
                        });
                    }, 200);
                });

                e.preventDefault();
                e.stopImmediatePropagation();
            });

            handle.on('click', function(e) {
                e.preventDefault();
                e.stopImmediatePropagation();
            });
        });
    }

    $(document).ready(function() {
        table_reference = $('#table-reference').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 10,
            ajax: {
                url: "{{ route('reference.fetch') }}",
                type: "GET",
                error: function(xhr) {
                    console.log('Ajax error:', xhr.responseText);
                }
            },
            order: [[1, "desc"]],
            columns: [
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: "customer_name",
                    render: function(data, type, row) {
                        return '<a href="{{ url('customer') }}/' + row.customer_id + '/edit#reference">' + (data ?? '') + '</a>';
                    }
                },
                { data: "reference_type" },
                { data: "new_ic" },
                { data: "name" },
                { data: "designation" },
                { data: "mobile" },
                { data: "city" },
                { data: "state" },
                {
                    data: "created_at",
                    render: function(data) {
                        if (!data) return '-';
                        const parts = data.substring(0, 10).split('-');
                        return `${parts[2]}-${parts[1]}-${parts[0]}`;
                    }
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        let actions = `<a href="{{ url('customer') }}/${row.customer_id}/edit#reference"><i class="bx bx-edit-alt"></i></a>`;
                        @if(Auth::user()->role_id != 4)
                        actions += ` &nbsp; <a onclick="if(confirm('Are you sure?')){window.location.href='{{ route('reference.destroy', '') }}/${row.id}'}"><i class="bx bx-trash"></i></a>`;
                        @endif
                        return actions;
                    }
                }
            ],
            "initComplete": function() {
                makeResizable('table-reference', table_reference); 
            }
        });
    });
</script>
@endsection