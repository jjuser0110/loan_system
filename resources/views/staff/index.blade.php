@extends('layouts.app')

@section('content')
<header class="page-header">
    <h2>{{ __('table.staff') }}</h2>
</header>

@include('layouts.flash-message')

<!-- start: page -->
<div class="row">
    <div class="col-lg-12 mb-3">
        <section class="card">
            <div class="card-header" style="text-align: right;">
                <a class="btn btn-xs btn-square btn-primary" href="{{ route('staff.create') }}">{{ __('table.create') }}</a>
            </div>
            <div class="card-body">
                <table class="table cus-table table-bordered table-striped mb-0" id="datatable-default">
                    <thead>
                        <tr>
                            <th>{{ __('table.name') }}</th>
                            <th>{{ __('table.username') }}</th>
                            <th>{{ __('table.branch') }}</th>
                            <th>{{ __('table.company') }}</th>
                            <th>{{ __('table.allowed_login_time') }}</th>
                            <th>{{ __('table.status') }}</th>
                            <th>{{ __('table.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($staff as $s)
                            <tr>
                                <td>{{ $s->name ?? '' }}</td>
                                <td>{{ $s->username ?? '' }}</td>
                                <td>{{ $s->branch->branch_name ?? '' }}</td>
                                <td>{{ $s->company->company_name ?? '' }}</td>

                                <td>
                                    @if($s->login_time_start && $s->login_time_end)
                                        <span class="d-block small">
                                            <i class="bx bx-time-five text-primary"></i>
                                            {{ \Carbon\Carbon::parse($s->login_time_start)->format('h:i A') }}
                                            –
                                            {{ \Carbon\Carbon::parse($s->login_time_end)->format('h:i A') }}
                                        </span>
                                        @if($s->allow_outside_hours)
                                            <span class="badge badge-info" style="font-size:10px;" title="Outside Hours Allowed">
                                                <i class="bx bx-unlock"></i> {{ __('table.outside_hours_allowed') }}
                                            </span>
                                        @else
                                            <span class="badge badge-warning" style="font-size:10px;" title="Time Restricted">
                                                <i class="bx bx-lock-alt"></i> {{ __('table.time_restricted') }}
                                            </span>
                                        @endif
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>

                                {{-- Active Status --}}
                                <td>
                                    @if(isset($s) && $s->is_active == 1)
                                        <span style="color:green">{{ __('table.active') }}</span>
                                    @else
                                        <span style="color:red">{{ __('table.inactive') }}</span>
                                    @endif
                                </td>

                                {{-- Actions --}}
                                <td>
                                    <a href="{{ route('staff.edit', $s) }}" title="Edit">
                                        <i class="bx bx-edit-alt"></i>
                                    </a>
                                    <a onclick="if(confirm('Are you sure you want to delete?')){window.location.href='{{ route('staff.destroy', $s) }}'}"
                                       title="Delete" style="cursor:pointer">
                                        <i class="bx bx-trash"></i>
                                    </a>
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