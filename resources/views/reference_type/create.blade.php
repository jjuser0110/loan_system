@extends('layouts.app')

@section('content')
<header class="page-header">
    <h2>{{ __('table.reference_type') }} @if (isset($reference_type)) {{ __('table.edit') }} @else {{ __('table.create') }} @endif</h2>
</header>

@include('layouts.flash-message')

<div class="row">
    <div class="col-lg-12 mb-3">
        <section class="card">
            <form class="theme-form mega-form" enctype="multipart/form-data"
                @if (isset($reference_type))
                    method="post" action="{{ route('reference_type.update', $reference_type) }}"
                @else
                    method="post" action="{{ route('reference_type.store') }}"
                @endif
                onsubmit="return onSubmitForm()">
                @csrf
                @if (isset($reference_type)) @method('PUT') @endif
                <div class="card-body">
                    <h6>{{ __('table.reference_type_information') }}</h6>
                    <div class="mb-3">
                        <label class="col-form-label">{{ __('table.reference_type') }}</label>
                        <input class="form-control" type="text" name="reference_type" placeholder="Reference type" value="{{ $reference_type->reference_type ?? '' }}" required>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <a href="{{ route('reference_type.index') }}" class="btn btn-secondary">{{ __('table.back') }}</a>
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