@extends('layouts.app')

@section('content')
<header class="page-header">
    <h2>{{ __('table.marital_status') }} @if (isset($marital_status)) {{ __('table.edit') }} @else {{ __('table.create') }} @endif</h2>
</header>

@include('layouts.flash-message')

<div class="row">
    <div class="col-lg-12 mb-3">
        <section class="card">
            <form class="theme-form mega-form" enctype="multipart/form-data"
                @if (isset($marital_status))
                    method="post" action="{{ route('marital_status.update', $marital_status) }}"
                @else
                    method="post" action="{{ route('marital_status.store') }}"
                @endif
                onsubmit="return onSubmitForm()">
                @csrf
                @if (isset($marital_status)) @method('PUT') @endif
                <div class="card-body">
                    <h6>{{ __('table.marital_status_information') }}</h6>
                    <div class="mb-3">
                        <label class="col-form-label">{{ __('table.marital_status') }}</label>
                        <input class="form-control" type="text" name="marital_status" placeholder="Marital Status" value="{{ $marital_status->marital_status ?? '' }}" required>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <a href="{{ route('marital_status.index') }}" class="btn btn-secondary">{{ __('table.back') }}</a>
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
</script>
@endsection