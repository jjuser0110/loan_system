@extends('layouts.app')

@section('content')
<header class="page-header">
    <h2>Branch @if (isset($branch)) Edit @else Create @endif</h2>
</header>

@include('layouts.flash-message')

<!-- start: page -->
<div class="row">
    <div class="col-lg-12 mb-3">
        <section class="card">
            <form class="theme-form mega-form" enctype="multipart/form-data" @if (isset($branch)) method="post" action="{{ route('branch.update',$branch) }}" @else method="post" action="{{ route('branch.store') }}" @endif onsubmit="return onSubmitForm()">
                @csrf
                <div class="card-body">
                    <h6>Account Information</h6>
                    <div class="mb-3">
                        <label class="col-form-label">Branch Name</label>
                        <input class="form-control" type="text" name="branch_name" placeholder="branch name" value="{{$branch->branch_name??''}}" required>
                    </div>
                    <div class="mb-3">
                        <label class="col-form-label">Branch Code</label>
                        <input class="form-control" type="text" name="branch_code" placeholder="branch code" value="{{$branch->branch_code??''}}" required>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <a href="{{route('branch.index')}}" class="btn btn-secondary">Back</a>
                    <button type="submit" class="btn btn-primary">Submit</button>
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
