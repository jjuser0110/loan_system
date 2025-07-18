@extends('layouts.app')

@section('content')
<header class="page-header">
    <h2>Bank Create/Edit</h2>
</header>

@include('layouts.flash-message')

<!-- start: page -->
<div class="row">
    <div class="col-lg-12 mb-3">
        <section class="card">
            <form class="theme-form mega-form" enctype="multipart/form-data" @if (isset($bank)) method="post" action="{{ route('bank.update',$bank) }}" @else method="post" action="{{ route('bank.store') }}" @endif onsubmit="return onSubmitForm()">
                @csrf
                <div class="card-body">
                    <h6>Account Information</h6>
                    <div class="mb-3">
                        <label class="col-form-label">Name</label>
                        <input class="form-control" type="text" name="bank_name" placeholder="bank name" value="{{$bank->bank_name??''}}" required>
                    </div>
                    <div class="mb-3">
                        <label class="col-form-label">Short Name</label>
                        <input class="form-control" type="text" name="short_name" placeholder="short name" value="{{$bank->short_name??''}}" required>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <a href="{{route('bank.index')}}" class="btn btn-secondary">Back</a>
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <!-- <button class="btn btn-secondary">Cancel</button> -->
                </div>
            </form>
        </section>
    </div>
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
