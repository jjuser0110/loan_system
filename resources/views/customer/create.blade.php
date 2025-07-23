@extends('layouts.app')

@section('content')
<header class="page-header">
    <h2>Customer @if (isset($customer)) Edit @else Create @endif</h2>
</header>

@include('layouts.flash-message')

<!-- start: page -->
<div class="row">
    <div class="col-lg-12 mb-3">
        <section class="card">
            <form class="theme-form mega-form" enctype="multipart/form-data" @if (isset($customer)) method="post" action="{{ route('customer.update',$customer) }}" @else method="post" action="{{ route('customer.store') }}" @endif onsubmit="return onSubmitForm()">
                @csrf
                <div class="card-body">
                    <h6>Customer Information</h6>
                    <div class="row">
                        <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                            <label class="col-form-label">Company</label>
                            <select id="company_id" name="company_id" data-plugin-selectTwo class="form-control populate" required>
                                <option value="">Choose a Company</option>
                                @foreach($company as $row)
                                <option value="{{$row->id??''}}" <?php echo isset($customer)&&$customer->company_id == $row->id?'selected':''?>>{{$row->company_code??''}}-{{$row->company_name??''}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                            <label class="col-form-label">Cust. Name</label>
                            <input class="form-control" type="text" name="name" placeholder="name.." value="{{$customer->name??''}}" required>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                            <label class="col-form-label">Cust. Name</label>
                            <input class="form-control" type="text" name="name" placeholder="name.." value="{{$customer->name??''}}" required>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                            <label class="col-form-label">Status</label>
                            <select name="is_active" class="form-control" required>
                                <option value="1" <?php echo isset($customer)&&$customer->is_active == 1?'selected':''?>>Active</option>
                                <option value="0" <?php echo isset($customer)&&$customer->is_active == 0?'selected':''?>>Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <a href="{{route('customer.index')}}" class="btn btn-secondary">Back</a>
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
