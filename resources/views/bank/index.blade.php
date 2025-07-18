@extends('layouts.app')

@section('content')
<header class="page-header">
    <h2>Bank</h2>
</header>

@include('layouts.flash-message')

<!-- start: page -->
<div class="row">
    <div class="col-lg-12 mb-3">
        <section class="card">
            <div class="card-header" style="text-align: right;">
                <a class="btn btn-xs btn-square btn-primary" href="{{route('bank.create')}}">Create</a>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped mb-0" id="datatable-default">
                    <thead>
                        <tr>
                            <th>Bank Name</th>
                            <th>Bank Short Name</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bank as $s)
                            <tr>
                                <td>{{$s->bank_name??''}}</td>
                                <td>{{$s->short_name??''}}</td>
                                <td>
                                    <a href="{{ route('bank.edit',$s) }}" title="Edit"><i class="bx bx-edit-alt"></i></a>
                                    <!-- <a onclick="if(confirm('Are you sure you want to delete?')){window.location.href='{{ route('bank.destroy',$s) }}'}" title = "Delete" style="cursor:pointer"><i class="bx bx-trash"></i></a> -->
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>
@endsection

@section('page-js')
    <script src="{{ asset('porto-assets/vendor/select2/js/select2.js') }}"></script>
    <script src="{{ asset('porto-assets/vendor/datatables/media/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('porto-assets/vendor/datatables/media/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('porto-assets/vendor/datatables/extras/TableTools/Buttons-1.4.2/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('porto-assets/vendor/datatables/extras/TableTools/Buttons-1.4.2/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('porto-assets/vendor/datatables/extras/TableTools/Buttons-1.4.2/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('porto-assets/vendor/datatables/extras/TableTools/Buttons-1.4.2/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('porto-assets/vendor/datatables/extras/TableTools/JSZip-2.5.0/jszip.min.js') }}"></script>
    <script src="{{ asset('porto-assets/vendor/datatables/extras/TableTools/pdfmake-0.1.32/pdfmake.min.js') }}"></script>
    <script src="{{ asset('porto-assets/vendor/datatables/extras/TableTools/pdfmake-0.1.32/vfs_fonts.js') }}"></script>
@endsection
@section('scripts')
    <script src="{{ asset('porto-assets/js/examples/examples.datatables.default.js') }}"></script>
    <script src="{{ asset('porto-assets/js/examples/examples.datatables.row.with.details.js') }}"></script>
    <script src="{{ asset('porto-assets/js/examples/examples.datatables.tabletools.js') }}"></script>
@endsection
