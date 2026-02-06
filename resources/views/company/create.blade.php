@extends('layouts.app')

@section('content')
<header class="page-header">
    <h2>{{ __('table.company') }} @if (isset($company)) {{ __('table.edit') }} @else {{ __('table.create') }} @endif</h2>
</header>

@include('layouts.flash-message')

<!-- start: page -->
<div class="row">
    <div class="col-lg-12 mb-3">
        <section class="card">
            <form class="theme-form mega-form" enctype="multipart/form-data" @if (isset($company)) method="post" action="{{ route('company.update',$company) }}" @else method="post" action="{{ route('company.store') }}" @endif onsubmit="return onSubmitForm()">
                @csrf
                <div class="card-body">
                    <h6>{{ __('table.account_information') }}</h6>
                    <div class="mb-3">
                        <label class="col-form-label">{{ __('table.company_name') }}</label>
                        <input class="form-control" type="text" name="company_name" placeholder="company name" value="{{$company->company_name??''}}" required>
                    </div>
                    <div class="mb-3">
                        <label class="col-form-label">{{ __('table.company_code') }}</label>
                        <input class="form-control" type="text" name="company_code" placeholder="company code" value="{{$company->company_code??''}}" required>
                    </div>
                    <div class="mb-3">
                        <label class="col-form-label">{{ __('table.branch') }}</label>
                        <select id="branch_id" name="branch_id" data-plugin-selectTwo class="form-control populate" required>
                            <option value="">{{ __('table.choose_a_branch') }}</option>
                            @foreach($branch as $row)
                            <option value="{{$row->id??''}}" <?php echo isset($company)&&$company->branch_id == $row->id?'selected':''?>>{{$row->branch_code??''}}-{{$row->branch_name??''}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <a href="{{route('company.index')}}" class="btn btn-secondary">{{ __('table.back') }}</a>
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
