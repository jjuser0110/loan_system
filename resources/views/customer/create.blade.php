@extends('layouts.app')
<style>
    .modal-block {
            max-width: 1000px;
            margin: 20px auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
    </style>

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
                                            <th>Reference</th>
                                            <th>New IC</th>
                                            <th>Name</th>
                                            <th>Mobile</th>
                                            <th>Telephone</th>
                                            <th>Job</th>
                                            <th>Company</th>
                                            <th>House Ownership</th>
                                            <th>Monthly Income</th>
                                            <th>City</th>
                                            <th>State</th>
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
                        <!-- Reference and Reference New IC -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="inputReference" class="form-label">Reference</label>
                                <select id="inputReference" class="form-select">
                                    <option selected>Spouse</option>
                                    <option>Guarantor</option>
                                    <option>Father</option>
                                    <option>Mother</option>
                                    <option>Brother</option>
                                    <option>Sister</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="inputReferenceIC" class="form-label">Reference New IC</label>
                                <input type="text" class="form-control" id="inputReferenceIC">
                            </div>
                        </div>
                        
                        <!-- Reference Name -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="inputReferenceName" class="form-label">Reference Name</label>
                                <input type="text" class="form-control" id="inputReferenceName" placeholder="Reference name">
                            </div>
                        </div>
                        
                        <!-- Telephone and Mobile -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="inputMobile" class="form-label">Mobile</label>
                                <input type="tel" class="form-control" id="inputMobile" placeholder="Mobile number">
                            </div>
                            <div class="col-md-6">
                                <label for="inputTelephone" class="form-label">Telephone</label>
                                <input type="tel" class="form-control" id="inputTelephone" placeholder="Telephone number">
                            </div>
                        </div>
                        
                        <!-- Job and Company -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="inputJob" class="form-label">Job</label>
                                <input type="text" class="form-control" id="inputJob" placeholder="Job title">
                            </div>
                            <div class="col-md-6">
                                <label for="inputCompany" class="form-label">Company</label>
                                <input type="text" class="form-control" id="inputCompany" placeholder="Company name">
                            </div>
                        </div>
                        
                        <!-- House Ownership and Monthly Income -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="inputHouseOwnership" class="form-label">House Ownership</label>
                                <input type="text" class="form-control" id="inputHouseOwnership" placeholder="House ownership status">
                            </div>
                            <div class="col-md-6">
                                <label for="inputMonthlyIncome" class="form-label">Monthly Income</label>
                                <input type="number" class="form-control" id="inputMonthlyIncome" placeholder="Monthly income">
                            </div>
                        </div>
                        
                        <!-- Address -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="inputAddress" class="form-label">Address</label>
                                <input type="text" class="form-control" id="inputAddress" placeholder="1234 Main St">
                            </div>
                        </div>
                        
                        <!-- Address 2 -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="inputAddress2" class="form-label">Address 2</label>
                                <input type="text" class="form-control" id="inputAddress2" placeholder="Apartment, studio, or floor">
                            </div>
                        </div>
                        
                        <!-- City, State, and Zip -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="inputCity" class="form-label">City</label>
                                <input type="text" class="form-control" id="inputCity" placeholder="City">
                            </div>
                            <div class="col-md-4">
                                <label for="inputState" class="form-label">State</label>
                                <select id="inputState" class="form-select">
                                    <option selected>Choose...</option>
                                    <option>Johor</option>
                                    <option>Kedah</option>
                                    <option>Kelantan</option>
                                    <option>Melaka</option>
                                    <option>Negeri Sembilan</option>
                                    <option>Pahang</option>
                                    <option>Perak</option>
                                    <option>Perlis</option>
                                    <option>Penang</option>
                                    <option>Sabah</option>
                                    <option>Sarawak</option>
                                    <option>Selangor</option>
                                    <option>Terengganu</option>
                                    <option>Kuala Lumpur</option>
                                    <option>Labuan</option>
                                    <option>Putrajaya</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="inputZip" class="form-label">Zip</label>
                                <input type="text" class="form-control" id="inputZip" placeholder="12345">
                            </div>
                        </div>
                    </form>
                </div>
                <footer class="card-footer">
                    <div class="row">
                        <div class="col-md-12 text-end">
                            <button class="btn btn-primary modal-confirm">Submit</button>
                            <button class="btn btn-secondary modal-dismiss">Cancel</button>
                        </div>
                    </div>
                </footer>
            </section>
        </div>
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
