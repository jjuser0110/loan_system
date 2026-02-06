@extends('layouts.app')

@section('content')
<header class="page-header">
    <h2>{{ __('table.race') }} @if (isset($race)) {{ __('table.edit') }} @else {{ __('table.create') }} @endif</h2>
</header>

@include('layouts.flash-message')

<div class="row">
    <div class="col-lg-12 mb-3">
        <section class="card">
            <form class="theme-form mega-form" enctype="multipart/form-data"
                @if (isset($race))
                    method="post" action="{{ route('race.update', $race) }}"
                @else
                    method="post" action="{{ route('race.store') }}"
                @endif
                onsubmit="return onSubmitForm()">
                @csrf
                @if (isset($race)) @method('PUT') @endif
                <div class="card-body">
                    <h6>{{ __('table.race_information') }}</h6>
                    <div class="mb-3">
                        <label class="col-form-label">{{ __('table.race_name') }}</label>
                        <input class="form-control" type="text" name="race_name" placeholder="Race name" value="{{ $race->race_name ?? '' }}" required>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <a href="{{ route('race.index') }}" class="btn btn-secondary">{{ __('table.back') }}</a>
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