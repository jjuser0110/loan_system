@extends('layouts.app')

@section('content')
<header class="page-header">
    <h2>{{ __('table.bank') }}</h2>
</header>

@include('layouts.flash-message')

<!-- start: page -->
<div class="row">
    <div class="col-lg-12 mb-3">
        <section class="card">
            <div class="card-header" style="text-align: right;">
                <a class="btn btn-xs btn-square btn-primary" href="{{route('bank.create')}}">{{ __('table.create') }}</a>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped mb-0" id="datatable-default">
                    <thead>
                        <tr>
                            <th>{{ __('table.bank_name') }}</th>
                            <th>{{ __('table.bank_short_name') }}</th>
                            <th>{{ __('table.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bank as $s)
                            <tr>
                                <td>{{$s->bank_name??''}}</td>
                                <td>{{$s->short_name??''}}</td>
                                <td>
                                    <a href="{{ route('bank.edit',$s) }}" title="Edit"><i class="bx bx-edit-alt"></i></a>
                                    <a onclick="if(confirm('Are you sure you want to delete?')){window.location.href='{{ route('bank.destroy',$s) }}'}" title = "Delete" style="cursor:pointer"><i class="bx bx-trash"></i></a>
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