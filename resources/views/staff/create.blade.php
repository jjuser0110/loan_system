@extends('layouts.app')

@section('content')
<header class="page-header">
    <h2>Staff @if (isset($staff)) Edit @else Create @endif</h2>
</header>

@include('layouts.flash-message')

<!-- start: page -->
<div class="row">
    <div class="col-lg-12 mb-3">
        <section class="card">
            <form class="theme-form mega-form" enctype="multipart/form-data" @if (isset($staff)) method="post" action="{{ route('staff.update',$staff) }}" @else method="post" action="{{ route('staff.store') }}" @endif onsubmit="return onSubmitForm()">
                @csrf
                <div class="card-body">
                    <h6>Account Information</h6>
                    <div class="mb-3">
                        <label class="col-form-label">Staff Name</label>
                        <input class="form-control" type="text" name="name" placeholder="name.." value="{{$staff->name??''}}" required>
                    </div>
                    <div class="mb-3">
                        <label class="col-form-label">Staff Username</label>
                        <input class="form-control" type="text" name="username" placeholder="username.." value="{{$staff->username??''}}" required @if (isset($staff)) readonly @endif>
                    </div>
                    <div class="mb-3">
                        <label class="col-form-label">Password</label>
                        <input class="form-control" type="text" name="password" placeholder="password.." value="" @if (!isset($staff)) required @endif>
                        @if(isset($staff))
                        <span style="color:red;font-size:0.8em">**key in to reset password</span>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label class="col-form-label">Company</label>
                        <select id="company_id" name="company_id" data-plugin-selectTwo class="form-control populate" required>
                            <option value="">Choose a Company</option>
                            @foreach($company as $row)
                            <option value="{{$row->id??''}}" <?php echo isset($staff)&&$staff->company_id == $row->id?'selected':''?>>{{$row->company_code??''}}-{{$row->company_name??''}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="col-form-label">Status</label>
                        <select name="is_active" class="form-control" required>
                            <option value="1" <?php echo isset($staff)&&$staff->is_active == 1?'selected':''?>>Active</option>
                            <option value="0" <?php echo isset($staff)&&$staff->is_active == 0?'selected':''?>>Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <a href="{{route('staff.index')}}" class="btn btn-secondary">Back</a>
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
