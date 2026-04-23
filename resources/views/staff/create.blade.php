@extends('layouts.app')

@section('content')
<header class="page-header">
    <h2>{{ __('table.staff') }} @if (isset($staff)) {{ __('table.edit') }} @else {{ __('table.create') }} @endif</h2>
</header>

@include('layouts.flash-message')

<!-- start: page -->
<div class="row">
    <div class="col-lg-12 mb-3">
        <section class="card">
            <form class="theme-form mega-form" enctype="multipart/form-data" @if (isset($staff)) method="post" action="{{ route('staff.update',$staff) }}" @else method="post" action="{{ route('staff.store') }}" @endif onsubmit="return onSubmitForm()">
                @csrf
                <div class="card-body">
                    <h6>{{ __('table.account_information') }}</h6>
                    <div class="mb-3">
                        <label class="col-form-label">{{ __('table.staff_name') }}</label>
                        <input class="form-control" type="text" name="name" placeholder="name.." value="{{$staff->name??''}}" required>
                    </div>
                    <div class="mb-3">
                        <label class="col-form-label">{{ __('table.staff_username') }}</label>
                        <input class="form-control" type="text" name="username" placeholder="username.." value="{{$staff->username??''}}" required @if (isset($staff)) readonly @endif>
                    </div>
                    <div class="mb-3">
                        <label class="col-form-label">{{ __('table.password') }}</label>
                        <input class="form-control" type="text" name="password" placeholder="password.." value="" @if (!isset($staff)) required @endif>
                        @if(isset($staff))
                        <span style="color:red;font-size:0.8em">**{{ __('table.key_in_to_reset_password') }}</span>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label class="col-form-label">{{ __('table.company') }}</label>
                        <select id="company_id" name="company_id" data-plugin-selectTwo class="form-control populate" required>
                            <option value="">{{ __('table.choose_a_company') }}</option>
                            @foreach($company as $row)
                            <option value="{{$row->id??''}}" <?php echo isset($staff)&&$staff->company_id == $row->id?'selected':''?>>{{$row->company_code??''}}-{{$row->company_name??''}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="col-form-label">{{ __('table.status') }}</label>
                        <select name="is_active" class="form-control" required>
                            <option value="1" <?php echo isset($staff)&&$staff->is_active == 1?'selected':''?>>{{ __('table.active') }}</option>
                            <option value="0" <?php echo isset($staff)&&$staff->is_active == 0?'selected':''?>>{{ __('table.inactive') }}</option>
                        </select>
                    </div>

                    <h6>{{ __('table.restriction') }}</h6>
                    <div class="mb-3">
                        <label class="col-form-label">{{ __('table.allowed_login_hours') }}</label>
                        <div class="d-flex align-items-center gap-2">
                            <input class="form-control" type="time" name="login_time_start" id="login_time_start"
                                   style="max-width:150px;"
                                   value="{{ isset($staff) && $staff->login_time_start ? \Carbon\Carbon::parse($staff->login_time_start)->format('H:i') : '06:00' }}"
                            <span class="px-2">to</span>
                            <input class="form-control" type="time" name="login_time_end" id="login_time_end"
                                   style="max-width:150px;"
                                   value="{{ isset($staff) && $staff->login_time_end ? \Carbon\Carbon::parse($staff->login_time_end)->format('H:i') : '18:00' }}"
                        </div>
                        <span style="color:grey;font-size:0.8em">**{{ __('table.leave_blank_for_no_restriction') }}</span>
                        @error('login_time_end')
                            <div style="color:red;font-size:0.8em">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="col-form-label">{{ __('table.allowed_login_outside_hours') }}</label>
                        <div class="d-flex align-items-center gap-2">
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" name="allow_outside_hours" id="allow_outside_hours"
                                       value="1" {{ isset($staff) && $staff->allow_outside_hours ? 'checked' : '' }}>
                            </div>
                            <span id="toggle-label" style="font-size:0.85em;color:grey;">
                            @if(isset($staff) && $staff->allow_outside_hours)
                                ✅ {{ __('table.can_login_anytime') }}
                            @else
                                🔒 {{ __('table.restricted_to_set_hours_only') }}
                            @endif
                        </span>
                        </div>
                    </div>

                </div>
                <div class="card-footer text-end">
                    <a href="{{route('staff.index')}}" class="btn btn-secondary">{{ __('table.back') }}</a>
                    <button type="submit" class="btn btn-primary">{{ __('table.submit') }}</button>
                </div>
            </form>
        </section>
    </div>
</div>
@endsection

@section('scripts')
    <script>
        function onSubmitForm() {
            var form = document.querySelector('form');
            if (form.checkValidity()) {
                showLoading();
                return true;
            } else {
                return false;
            }
        }

        // Toggle label update
        document.getElementById('allow_outside_hours').addEventListener('change', function () {
            document.getElementById('toggle-label').textContent = this.checked
                ? '✅ {{ __('table.can_login_anytime') }}'
                : '🔒 {{ __('table.restricted_to_set_hours_only') }}';
        });
    </script>
@endsection