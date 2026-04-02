@extends('layouts.app')

@section('content')
<header class="page-header">
    <h2>{{ __('table.non_expenses_type') }}</h2>
</header>

@include('layouts.flash-message')

<!-- start: page -->
<div class="row">
    <div class="col-lg-12 mb-3">
        <section class="card">
            <div class="card-header" style="text-align: right;">
                <a class="btn btn-xs btn-square btn-primary" href="{{route('non_expenses_type.create')}}">{{ __('table.create') }}</a>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped mb-0" id="datatable-default">
                    <thead>
                        <tr>
                            <th>{{ __('table.title') }}</th>
                            <th>{{ __('table.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($non_expenses_type as $s)
                            <tr>
                                <td>{{ $s->title ?? '' }}</td>
                                <td>
                                    <a href="{{ route('non_expenses_type.edit', $s) }}" title="Edit">
                                        <i class="bx bx-edit-alt"></i>
                                    </a>

                                    <form action="{{ route('non_expenses_type.destroy', $s) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="border:none; background:none; padding:0;">
                                            <i class="bx bx-trash text-danger"></i>
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