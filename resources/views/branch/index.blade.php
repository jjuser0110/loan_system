@extends('layouts.app')

@section('content')
<header class="page-header">
    <h2>Branch</h2>
</header>

@include('layouts.flash-message')

<!-- start: page -->
<div class="row">
    <div class="col-lg-12 mb-3">
        <section class="card">
            <div class="card-header" style="text-align: right;">
                <a class="btn btn-xs btn-square btn-primary" href="{{route('branch.create')}}">Create</a>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped mb-0" id="datatable-default">
                    <thead>
                        <tr>
                            <th>Branch Name</th>
                            <th>Branch Code</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($branch as $s)
                            <tr>
                                <td>{{$s->branch_name??''}}</td>
                                <td>{{$s->branch_code??''}}</td>
                                <td>
                                    <a href="{{ route('branch.edit',$s) }}" title="Edit"><i class="bx bx-edit-alt"></i></a>
                                    <a onclick="if(confirm('Are you sure you want to delete?')){window.location.href='{{ route('branch.destroy',$s) }}'}" title = "Delete" style="cursor:pointer"><i class="bx bx-trash"></i></a>
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