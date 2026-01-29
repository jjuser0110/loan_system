@extends('layouts.app')

@section('content')
<header class="page-header">
    <h2>Staff</h2>
</header>

@include('layouts.flash-message')

<!-- start: page -->
<div class="row">
    <div class="col-lg-12 mb-3">
        <section class="card">
            <div class="card-header" style="text-align: right;">
                <a class="btn btn-xs btn-square btn-primary" href="{{route('staff.create')}}">Create</a>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped mb-0" id="datatable-default">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Username</th>
                            <th>Branch</th>
                            <th>Company</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($staff as $s)
                            <tr>
                                <td>{{$s->name??''}}</td>
                                <td>{{$s->username??''}}</td>
                                <td>{{$s->branch->branch_name??''}}</td>
                                <td>{{$s->company->company_name??''}}</td>
                                <td><?php echo isset($s)&&$s->is_active == 1?'<span style="color:green">Active</span>':'<span style="color:red">Inactive</span>'?></td>
                                <td>
                                    <a href="{{ route('staff.edit',$s) }}" title="Edit"><i class="bx bx-edit-alt"></i></a>
                                    <a onclick="if(confirm('Are you sure you want to delete?')){window.location.href='{{ route('staff.destroy',$s) }}'}" title = "Delete" style="cursor:pointer"><i class="bx bx-trash"></i></a>
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