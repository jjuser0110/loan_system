@extends('layouts.app')

@section('content')
<header class="page-header">
    <h2>{{ __('table.house_ownership') }} @if (isset($house_ownership)) {{ __('table.edit') }} @else {{ __('table.create') }} @endif</h2>
</header>

@include('layouts.flash-message')

<div class="row">
    <div class="col-lg-12 mb-3">
        <section class="card">
            <form class="theme-form mega-form" enctype="multipart/form-data"
                @if (isset($house_ownership))
                    method="post" action="{{ route('house_ownership.update', $house_ownership) }}"
                @else
                    method="post" action="{{ route('house_ownership.store') }}"
                @endif
                onsubmit="return onSubmitForm()">
                @csrf
                @if (isset($house_ownership)) @method('PUT') @endif
                <div class="card-body">
                    <h6>{{ __('table.house_ownership_information') }}</h6>
                    <div class="mb-3">
                        <label class="col-form-label">{{ __('table.house_ownership_type') }}</label>
                        <input class="form-control" type="text" name="house_ownership" placeholder="house ownership type" value="{{ $house_ownership->house_ownership ?? '' }}" required>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <a href="{{ route('house_ownership.index') }}" class="btn btn-secondary">{{ __('table.back') }}</a>
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