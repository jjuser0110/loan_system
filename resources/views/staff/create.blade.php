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
                </div>
                <div class="card-footer text-end">
                    <a href="{{route('staff.index')}}" class="btn btn-secondary">{{ __('table.back') }}</a>
                    <button type="submit" class="btn btn-primary">{{ __('table.submit') }}</button>
                    <!-- <button class="btn btn-secondary">Cancel</button> -->
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
    </script>
@endsection
