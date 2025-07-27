@extends('layouts.app')

@section('content')
<header class="page-header">
    <h2>Customer Details</h2>
</header>

@include('layouts.flash-message')
<div class="row">
    <div class="col-sm-12 col-md-6 col-lg-4 col-xl-3">

        <section class="card">
            <div class="card-body">
                <div class="thumb-info mb-3">
                    <img src="{{asset('porto-assets/img/!logged-user.jpg')}}" class="rounded img-fluid" alt="John Doe">
                </div>
                <div class="clearfix">
                    <input type="file" class="form-control" id="profileImage" name="profileImage" accept="image/*">
                </div>
            </div>
        </section>
        <ul class="simple-card-list mb-3">
            <li class="primary">
                <h3>488</h3>
                <p class="text-light">Nullam quris ris.</p>
            </li>
            <li class="primary">
                <h3>$ 189,000.00</h3>
                <p class="text-light">Nullam quris ris.</p>
            </li>
            <li class="primary">
                <h3>16</h3>
                <p class="text-light">Nullam quris ris.</p>
            </li>
        </ul>
    </div>
    <div class="col-sm-12 col-md-6 col-lg-8 col-xl-9">
        <div class="tabs">
            <ul class="nav nav-tabs">
                <li class="nav-item active">
                    <a class="nav-link active" data-bs-target="#personal" href="#personal" data-bs-toggle="tab">Personal</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-target="#work" href="#work" data-bs-toggle="tab">Work</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-target="#spouse" href="#spouse" data-bs-toggle="tab">Spouse</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-target="#guarantor" href="#guarantor" data-bs-toggle="tab">Guarantor</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-target="#reference" href="#reference" data-bs-toggle="tab">Reference</a>
                </li>
            </ul>
            <div class="tab-content">
                <div id="personal" class="tab-pane active">
                    <form class="p-3">
                        <h4 class="mb-3 font-weight-semibold text-dark">Personal Information</h4>
                        <div class="row mb-2">
                            <div class="form-group col-md-6">
                                <label>Customer Code</label>
                                <input type="text" class="form-control" name="" placeholder="Customer Code" >
                            </div>
                            <div class="form-group col-md-6 border-top-0 pt-0">
                                <label for="inputState">Company Code</label>
                                <select id="inputState" class="form-control">
                                    <option selected>Choose...</option>
                                    <option>...</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="form-group col">
                                <label for="inputAddress">Customer Name</label>
                                <input type="text" class="form-control" id="inputAddress" placeholder="1234 Main St">
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="form-group col">
                                <label for="inputAddress">NRIC</label>
                                <input type="text" class="form-control" id="inputAddress" placeholder="1234 Main St">
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="form-group col-md-4 border-top-0 pt-0">
                                <label for="inputState">Gender</label>
                                <select id="inputState" class="form-control">
                                    <option selected>Choose...</option>
                                    <option>...</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4 border-top-0 pt-0">
                                <label for="inputState">Race</label>
                                <select id="inputState" class="form-control">
                                    <option selected>Choose...</option>
                                    <option>...</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4 border-top-0 pt-0">
                                <label for="inputZip">DOB</label>
                                <input type="text" class="form-control" id="inputZip">
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="form-group col">
                                <label for="inputAddress">Address</label>
                                <input type="text" class="form-control" id="inputAddress" placeholder="1234 Main St">
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="form-group col-md-4 border-top-0 pt-0">
                                <label for="inputZip">Postcode</label>
                                <input type="text" class="form-control" id="inputZip">
                            </div>
                            <div class="form-group col-md-4 border-top-0 pt-0">
                                <label for="inputZip">City</label>
                                <input type="text" class="form-control" id="inputZip">
                            </div>
                            <div class="form-group col-md-4 border-top-0 pt-0">
                                <label for="inputZip">State</label>
                                <input type="text" class="form-control" id="inputZip">
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="form-group col-md-6 border-top-0 pt-0">
                                <label for="inputZip">Warga Negara</label>
                                <input type="text" class="form-control" id="inputZip">
                            </div>
                            <div class="form-group col-md-6 border-top-0 pt-0">
                                <label for="inputZip">Martial Status</label>
                                <input type="text" class="form-control" id="inputZip">
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="form-group col-md-4 border-top-0 pt-0">
                                <label for="inputZip">Email</label>
                                <input type="text" class="form-control" id="inputZip">
                            </div>
                            <div class="form-group col-md-4 border-top-0 pt-0">
                                <label for="inputZip">Tel</label>
                                <input type="text" class="form-control" id="inputZip">
                            </div>
                            <div class="form-group col-md-4 border-top-0 pt-0">
                                <label for="inputZip">Mobile</label>
                                <input type="text" class="form-control" id="inputZip">
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="form-group col">
                                <label for="inputAddress">Remark</label>
                                <textarea class="form-control" id="inputAddress" rows="3" placeholder="Enter remarks here..."></textarea>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 text-end mt-3">
                                <button class="btn btn-primary modal-confirm">Save</button>
                            </div>
                        </div>

                    </form>
                </div>
                <div id="work" class="tab-pane">
                    <p>work</p>
                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitat.</p>
                </div>
                <div id="spouse" class="tab-pane">
                    <p>spouse</p>
                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitat.</p>
                </div>
                <div id="guarantor" class="tab-pane">
                    <p>guarantor</p>
                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitat.</p>
                </div>
                <div id="reference" class="tab-pane">
                    <p>reference</p>
                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitat.</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3">
    </div>
</div>
<!-- end: page -->
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
