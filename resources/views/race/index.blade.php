@extends('layouts.app')

@section('content')
<header class="page-header">
    <h2>Race</h2>
</header>

@include('layouts.flash-message')

<!-- start: page -->
<div class="row">
    <div class="col-lg-12 mb-3">
        <section class="card">
            <div class="card-header" style="text-align: right;">
                <a class="btn btn-xs btn-square btn-primary" href="{{route('race.create')}}">Create</a>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped mb-0" id="datatable-default">
                    <thead>
                        <tr>
                            <th>Race Name</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($race as $s)
                            <tr>
                                <td>{{ $s->race_name ?? '' }}</td>
                                <td>
                                    <a href="{{ route('race.edit', $s) }}" title="Edit">
                                        <i class="bx bx-edit-alt"></i>
                                    </a>

                                    <form action="{{ route('race.destroy', $s) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure?')">
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