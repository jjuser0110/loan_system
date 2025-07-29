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
                    <form class="p-3">
                        <h4 class="mb-3 font-weight-semibold text-dark">Work Information</h4>
                        <div class="row mb-2">
                            <div class="form-group col-md-12">
                                <label>Company Name</label>
                                <input type="text" class="form-control" name="" placeholder="Company Name" >
                            </div>
                            <div class="form-group col-md-12">
                                <label>Biz Type</label>
                                <input type="text" class="form-control" name="" placeholder="Business Type" >
                            </div>
                            <div class="form-group col-md-12">
                                <label>Designation</label>
                                <input type="text" class="form-control" name="" placeholder="Designation..." >
                            </div>
                            <div class="form-group col-md-4">
                                <label>Monthly Income</label>
                                <input type="text" class="form-control" name="" placeholder="Monthly Income" >
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
                                <label for="inputZip">Office Telephone</label>
                                <input type="text" class="form-control" id="inputZip">
                            </div>
                            <div class="form-group col-md-6 border-top-0 pt-0">
                                <label for="inputZip">Fax</label>
                                <input type="text" class="form-control" id="inputZip">
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="form-group col-md-6 border-top-0 pt-0">
                                <label for="inputZip">Vehicle No</label>
                                <input type="text" class="form-control" id="inputZip">
                            </div>
                            <div class="form-group col-md-6 border-top-0 pt-0">
                                <label for="inputZip">Vehicle Model</label>
                                <input type="text" class="form-control" id="inputZip">
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="form-group col-md-6 border-top-0 pt-0">
                                <label for="inputState">Employer</label>
                                <select id="inputState" class="form-control">
                                    <option selected>Private</option>
                                    <option>Government</option>
                                    <option>...</option>
                                </select>
                            </div>
                            <div class="form-group col-md-6 border-top-0 pt-0">
                                <label for="inputZip">Job Type</label>
                                <input type="text" class="form-control" id="inputZip">
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="form-group col-md-6 border-top-0 pt-0">
                                <label for="inputZip">Start Working Date</label>
                                <input type="text" class="form-control" id="inputZip">
                            </div>
                            <div class="form-group col-md-6 border-top-0 pt-0">
                                <label for="inputZip">End Working Date</label>
                                <input type="text" class="form-control" id="inputZip">
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="form-group col-md-6 border-top-0 pt-0">
                                <label for="inputZip">Salary Date</label>
                                <input type="text" class="form-control" id="inputZip">
                            </div>
                            <div class="form-group col-md-6 border-top-0 pt-0">
                                <label for="inputZip">2nd Salary Date</label>
                                <input type="text" class="form-control" id="inputZip">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 text-end mt-3">
                                <button class="btn btn-primary modal-confirm">Save</button>
                            </div>
                        </div>

                    </form>
                </div>
                <div id="reference" class="tab-pane">
                    <div class="col-lg-12 mb-3">
                        <section class="card">
                            <div class="card-header" style="text-align: left;">
                                <a class="btn btn-xs btn-square btn-primary" href="#modalForm">Add Reference</a>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered table-striped mb-0" id="datatable-default">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Username</th>
                                            <th>Branch</th>
                                            <th>Company</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                            <tr>
                                                <td>abc123</td>
                                                <td>abc123</td>
                                                <td>abc123</td>
                                                <td>abc123</td>
                                                <td>abc123</td>
                                                <td>abc123</td>
                                            </tr>
                                    </tbody>
                                </table>
                            </div>
                        </section>
                    </div>    
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3">
    </div>

    <!-- Modal Form -->
    <div id="modalForm" class="modal-block modal-block-primary mfp-hide">
        <section class="card">
            <header class="card-header">
                <h2 class="card-title">Reference Form</h2>
            </header>
            <div class="card-body">
                <form>
                    <div class="form-row">
                        <div class="row mb-2">
                            <div class="form-group col-md-6 border-top-0 pt-0">
                                <label for="inputState">Reference</label>
                                <select id="inputState" class="form-control">
                                    <option selected>Spouse</option>
                                    <option>Guarantor</option>
                                    <option>Father</option>
                                    <option>Mother</option>
                                    <option>Brother</option>
                                    <option>Sister</option>
                                </select>
                            </div>
                            <div class="form-group col-md-6 border-top-0 pt-0">
                                <label for="inputZip">Job Type</label>
                                <input type="text" class="form-control" id="inputZip">
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="inputEmail4">Reference New IC</label>
                            <input type="email" class="form-control" id="inputEmail4" placeholder="Email">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="inputEmail4">Reference Name</label>
                            <input type="email" class="form-control" id="inputEmail4" placeholder="Email">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="inputEmail4">Reference New IC</label>
                            <input type="email" class="form-control" id="inputEmail4" placeholder="Email">
                        </div>
                        <div class="form-group col-md-6 mb-3 mb-lg-0">
                            <label for="inputPassword4">Password</label>
                            <input type="password" class="form-control" id="inputPassword4" placeholder="Password">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputAddress">Address</label>
                        <input type="text" class="form-control" id="inputAddress" placeholder="1234 Main St">
                    </div>
                    <div class="form-group">
                        <label for="inputAddress2">Address 2</label>
                        <input type="text" class="form-control" id="inputAddress2" placeholder="Apartment, studio, or floor">
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="inputCity">City</label>
                            <input type="text" class="form-control" id="inputCity">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="inputState">State</label>
                            <select id="inputState" class="form-control">
                                <option selected>Choose...</option>
                                <option>...</option>
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <label for="inputZip">Zip</label>
                            <input type="text" class="form-control" id="inputZip">
                        </div>
                    </div>
                </form>
            </div>
            <footer class="card-footer">
                <div class="row">
                    <div class="col-md-12 text-end">
                        <button class="btn btn-primary modal-confirm">Submit</button>
                        <button class="btn btn-default modal-dismiss">Cancel</button>
                    </div>
                </div>
            </footer>
        </section>
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

        $(document).ready(function() {
            // Initia``lize Magnific Popup for modal
            $('a[href="#modalForm"]').magnificPopup({
                type: 'inline',
                preloader: false,
                modal: true
            });
            
            // Handle modal close
            $(document).on('click', '.modal-dismiss', function (e) {
                e.preventDefault();
                $.magnificPopup.close();
            });
            
            // Handle form submission
            $(document).on('click', '.modal-confirm', function (e) {
                e.preventDefault();
                // Your form submission logic here
                $.magnificPopup.close();
            });
        });
    </script>
    
	<script src="js/examples/examples.modals.js"></script>
@endsection
