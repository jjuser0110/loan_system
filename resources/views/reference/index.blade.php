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
            <!-- <div class="card-header" style="text-align: right;">
                <a class="btn btn-xs btn-square btn-primary" href="{{ url('customer/'.($reference->first()->customer_id ?? 0).'/edit#reference') }}">{{ __('table.create') }}</a>
            </div> -->
            <div class="card-body">
                <table class="table table-bordered table-striped mb-0" id="datatable-default">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>{{ __('table.customer_name') }}</th>
                            <th>{{ __('table.relationship') }}</th>
                            <th>{{ __('table.reference_name') }}</th>
                            <th>{{ __('table.mobile') }}</th>
                            <th>{{ __('table.email') }}</th>
                            <th>{{ __('table.created_at') }}</th>
                            <th>{{ __('table.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reference as $s)
                            <tr>
                                <td>{{$s->id??''}}</td>
                                <td>{{$s->customer->customer_name??''}}</td>
                                <td>{{$s->reference_type??''}}</td>
                                <td>{{$s->name??''}}</td>
                                <td>{{$s->mobile??''}}</td>
                                <td>{{$s->email??''}}</td>
                                <td>{{$s->created_at??''}}</td>
                                <td>
                                    <a href="{{ url('customer/'.($s->customer_id ?? 0).'/edit#reference') }}" title="Edit"><i class="bx bx-edit-alt"></i></a>
                                    <a onclick="if(confirm('Are you sure you want to delete?')){window.location.href='{{ route('reference.destroy',$s) }}'}" title = "Delete" style="cursor:pointer"><i class="bx bx-trash"></i></a>
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