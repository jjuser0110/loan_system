@extends('layouts.app')

@section('content')
<header class="page-header">
    <h2>{{ __('table.marital_status') }}</h2>
</header>

@include('layouts.flash-message')

<!-- start: page -->
<div class="row">
    <div class="col-lg-12 mb-3">
        <section class="card">
            <div class="card-header" style="text-align: right;">
                <a class="btn btn-xs btn-square btn-primary" href="{{route('marital_status.create')}}">{{ __('table.create') }}</a>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped mb-0" id="datatable-default">
                    <thead>
                        <tr>
                            <th>{{ __('table.marital_status') }}</th>
                            <th>{{ __('table.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($marital_status as $status)
                        <tr>
                            <td>{{ $status->marital_status }}</td>
                            <td>
                                <a href="{{ route('marital_status.edit', $status) }}" title="Edit"><i class="bx bx-edit-alt"></i></a>

                                <form action="{{ route('marital_status.destroy', $status) }}"
                                    method="POST"
                                    style="display:inline;"
                                    onsubmit="return confirm('Are you sure you want to delete this?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Delete" style="border:none; background:none; padding:0; color:red; cursor:pointer;">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </form>
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
