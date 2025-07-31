@extends('layouts.app')
<style>
    .modal-block {
            max-width: 2000px;
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
    
    <div class="col-sm-12 col-md-8 col-lg-9 col-xl-12">
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
                    <form class="p-3" method="POST" action="{{ route('customer.store') }}" enctype="multipart/form-data">
                        @csrf
                        <h4 class="mb-3 font-weight-semibold text-dark">Personal Information</h4>
                        <div class="row">
                            <!-- Photo Section - 30% width -->
                            <div class="col-lg-3 col-md-4 col-sm-12">
                                <section class="card">
                                    <div class="card-body">
                                        <div class="thumb-info mb-3">
                                            <img id="previewImage" src="{{ asset('porto-assets/img/!logged-user.jpg') }}" class="rounded img-fluid" alt="Profile Image">
                                        </div>
                                        <div class="clearfix">
                                            <input type="file" class="form-control" id="profileImage" name="profile_image" accept="image/*" onchange="previewPhoto(event)" required>
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
                            
                            <!-- Information Section - 70% width -->
                            <div class="col-lg-9 col-md-8 col-sm-12">
                                <div class="row mb-2">
                                    <div class="form-group col-md-6">
                                        <label>Customer Code</label>
                                        <input type="text" class="form-control" name="customer_code" placeholder="Customer Code" required>
                                    </div>
                                    <div class="form-group col-md-6 border-top-0 pt-0">
                                        <label for="company_code">Company Code</label>
                                        <select id="company_code" name="company_code" class="form-control" required>
                                            <option value="">Choose...</option>
                                            <option value="CC001">CC001</option>
                                            <option value="CC002">CC002</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="form-group col-md-6">
                                        <label for="customer_name">Customer Name</label>
                                        <input type="text" class="form-control" id="customer_name" name="customer_name" placeholder="Customer Name" required>
                                    </div>
                                    <div class="form-group col-md-6 border-top-0 pt-0">
                                        <label for="nric_number">NRIC Number</label>
                                        <input type="text" class="form-control" id="nric_number" name="nric_number" required>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="form-group col-md-4 border-top-0 pt-0">
                                        <label for="gender">Gender</label>
                                        <select id="gender" name="gender" class="form-control" required>
                                            <option value="">Choose...</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4 border-top-0 pt-0">
                                        <label for="race">Race</label>
                                        <select id="race" name="race" class="form-control" required>
                                            <option value="">Choose...</option>
                                            @foreach($races as $raceItem)
                                                <option value="{{ $raceItem->race_name }}">{{ $raceItem->race_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4 border-top-0 pt-0">
                                        <label for="date_of_birth">Date Of Birth</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-calendar-alt"></i>
                                            </span>
                                            <input type="date" name="date_of_birth" class="form-control" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="form-group col">
                                        <label for="address">Address</label>
                                        <input type="text" class="form-control" id="address" name="address" placeholder="Address" required>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="form-group col-md-4 border-top-0 pt-0">
                                        <label for="postcode">Postcode</label>
                                        <input type="text" class="form-control" id="postcode" name="postcode" required>
                                    </div>
                                    <div class="form-group col-md-4 border-top-0 pt-0">
                                        <label for="city">City</label>
                                        <input type="text" class="form-control" id="city" name="city" required>
                                    </div>
                                    <div class="form-group col-md-4 border-top-0 pt-0">
                                        <label for="state">State</label>
                                        <select id="state" name="state" class="form-control" required>
                                            <option value="">Choose...</option>
                                            @foreach($states as $stateItem)
                                                <option value="{{ $stateItem->state_name }}">{{ $stateItem->state_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="form-group col-md-4 border-top-0 pt-0">
                                        <label for="house_ownership">House Ownership</label>
                                        <input type="text" class="form-control" id="house_ownership" name="house_ownership" required>
                                    </div>
                                    <div class="form-group col-md-4 border-top-0 pt-0">
                                        <label for="warga_negara">Warga Negara</label>
                                        <input type="text" class="form-control" id="warganegara" name="warganegara">
                                    </div>
                                    <div class="form-group col-md-4 border-top-0 pt-0">
                                        <label for="marital_status">Marital Status</label>
                                        <select id="marital_status" name="marital_status" class="form-control" required>
                                            <option value="">Choose...</option>
                                            @foreach($marital_statues as $marital_status)
                                                <option value="{{ $marital_status->marital_status }}">{{ $marital_status->marital_status }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="form-group col-md-4 border-top-0 pt-0">
                                        <label for="email">Email</label>
                                        <input type="email" class="form-control" id="email" name="email">
                                    </div>
                                    <div class="form-group col-md-4 border-top-0 pt-0">
                                        <label for="telephone">Telephone</label>
                                        <input type="text" class="form-control" id="telephone" name="telephone">
                                    </div>
                                    <div class="form-group col-md-4 border-top-0 pt-0">
                                        <label for="mobile">Mobile</label>
                                        <input type="text" class="form-control" id="mobile" name="mobile" required>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="form-group col">
                                        <label for="remark">Remark</label>
                                        <textarea class="form-control" id="remark" name="remark" rows="3" placeholder="Enter remarks here..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 text-end mt-3">
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div id="work" class="tab-pane">
                    <form class="p-3">
                        <h4 class="mb-3 font-weight-semibold text-dark">Work Information</h4>
                        <div class="row mb-2">
                            <div class="form-group col-md-6">
                                <label>Company Name</label>
                                <input type="text" class="form-control" name="" placeholder="Company Name" >
                            </div>
                            <div class="form-group col-md-6 border-top-0 pt-0">
                                <label>Biz Type</label>
                                <input type="text" class="form-control" name="" placeholder="Business Type" >
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="form-group col-md-6">
                                <label>Designation</label>
                                <input type="text" class="form-control" name="" placeholder="Designation..." >
                            </div>
                            <div class="form-group col-md-6 border-top-0 pt-0">
                                <label>Monthly Income</label>
                                <input type="number" class="form-control" name="" placeholder="Monthly Income" >
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="form-group col">
                                <label for="inputAddress">Company Address</label>
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
                                <label for="state_id">State</label>
                                <select id="state_id" name="state_id" class="form-control" required>
                                    <option value="" disabled selected>Choose...</option>
                                    @foreach($states as $state)
                                        <option value="{{ $state->id }}">{{ $state->state_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="form-group col-md-4 border-top-0 pt-0">
                                <label for="inputZip">Office Telephone</label>
                                <input type="text" class="form-control" id="inputZip">
                            </div>
                            <div class="form-group col-md-4 border-top-0 pt-0">
                                <label for="inputZip">Office Mobile</label>
                                <input type="text" class="form-control" id="inputZip">
                            </div>
                            <div class="form-group col-md-4 border-top-0 pt-0">
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
                                    <option selected>Choose...</option>
                                    <option>Private</option>
                                    <option>Government</option>
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
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-calendar-alt"></i>
                                    </span>
                                    <input type="text" data-plugin-datepicker class="form-control">
                                </div>
                            </div>
                            <div class="form-group col-md-6 border-top-0 pt-0">
                                <label for="inputZip">End Working Date</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-calendar-alt"></i>
                                    </span>
                                    <input type="text" data-plugin-datepicker class="form-control" placeholder="Optional">
                                </div>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="form-group col-md-6 border-top-0 pt-0">
                                <label for="inputZip">Salary Date</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-calendar-alt"></i>
                                    </span>
                                    <input type="text" data-plugin-datepicker class="form-control">
                                </div>
                            </div>
                            <div class="form-group col-md-6 border-top-0 pt-0">
                                <label for="inputZip">2nd Salary Date</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-calendar-alt"></i>
                                    </span>
                                    <input type="text" data-plugin-datepicker class="form-control">
                                </div>
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
                            <th>Job</th>
                            <th>Company Name</th>
                            <th>House Ownership</th>
                            <th>Monthly Income</th>
                            <th>City</th>
                            <th>State</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Guarantor</td>
                            <td>1234567890</td>
                            <td>Test</td>
                            <td>123456</td>
                            <td>Manager</td>
                            <td>SDNBHDabc123abc123abc123</td>
                            <td>abc123</td>
                            <td>12345</td>
                            <td>city 1</td>
                            <td>state 1</td>
                            <td></td>
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
        <div id="modalForm" class="modal-block modal-block-primary modal-block-lg mfp-hide">
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
                                <select id="state_id" name="state_id" class="form-control" required>
                                    <option value="" disabled selected>Choose...</option>
                                    @foreach($reference_types as $reference_type)
                                        <option value="{{ $reference_type->id }}">{{ $reference_type->reference_type }}</option>
                                    @endforeach
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

                        <div class="row mb-2">
                            <div class="form-group col-md-4 border-top-0 pt-0">
                                <label for="inputState">Gender</label>
                                <select id="inputState" class="form-control">
                                    <option selected>Choose...</option>
                                    <option>Male</option>
                                    <option>Female</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4 border-top-0 pt-0">
                                <label for="state_id">State</label>
                                <select id="state_id" name="state_id" class="form-control" required>
                                    <option value="" disabled selected>Choose...</option>
                                    @foreach($states as $state)
                                        <option value="{{ $state->id }}">{{ $state->state_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-4 border-top-0 pt-0">
                                <label for="inputZip">Date Of Birth</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-calendar-alt"></i>
                                    </span>
                                    <input type="text" data-plugin-datepicker class="form-control">
                                </div>
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

                        <!-- House Ownership and Monthly Income -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="inputZip">House Ownership</label>
                                <input type="text" class="form-control" id="inputZip">
                            </div>
                            <div class="col-md-6">
                                <label for="inputZip">Warga Negara</label>
                                <input type="text" class="form-control" id="inputZip">
                            </div>
                        </div>
                        
                        <!-- Address -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="inputAddress" class="form-label">Address</label>
                                <input type="text" class="form-control" id="inputAddress" placeholder="1234 Main St">
                            </div>
                        </div>
                        
                        <!-- City, State, and Zip -->
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
                                <label for="state_id">State</label>
                                <select id="state_id" name="state_id" class="form-control" required>
                                    <option value="" disabled selected>Choose...</option>
                                    @foreach($states as $state)
                                        <option value="{{ $state->id }}">{{ $state->state_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <!-- Job and Company -->
                        <br><br>
                        <div class="row mb-2">
                            <div class="form-group col-md-6">
                                <label>Company Name</label>
                                <input type="text" class="form-control" name="" placeholder="Company Name" >
                            </div>
                            <div class="form-group col-md-6 border-top-0 pt-0">
                                <label>Biz Type</label>
                                <input type="text" class="form-control" name="" placeholder="Business Type" >
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="form-group col-md-6">
                                <label>Designation</label>
                                <input type="text" class="form-control" name="" placeholder="Designation..." >
                            </div>
                            <div class="form-group col-md-6 border-top-0 pt-0">
                                <label>Monthly Income</label>
                                <input type="number" class="form-control" name="" placeholder="Monthly Income" >
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="inputAddress" class="form-label">Company Address</label>
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
                                <label for="state_id">State</label>
                                <select id="state_id" name="state_id" class="form-control" required>
                                    <option value="" disabled selected>Choose...</option>
                                    @foreach($states as $state)
                                        <option value="{{ $state->id }}">{{ $state->state_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </form>
                </div>
                <footer class="card-footer">
                    <div class="row">
                        <div class="col-md-12 text-end">
                            <button class="btn btn-secondary modal-dismiss">Cancel</button>
                            <button class="btn btn-primary modal-confirm">Submit</button>
                        </div>
                    </div>
                </footer>
            </section>
        </div>
    </div>
</div>
<!-- end: page -->
@endsection

@section('page-js')
    <script src="{{ asset('porto-assets/vendor/select2/js/select2.js') }}"></script>
    <script src="{{ asset('porto-assets/vendor/datatables/media/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('porto-assets/vendor/datatables/media/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('porto-assets/vendor/datatables/extras/TableTools/Buttons-1.4.2/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('porto-assets/vendor/datatables/extras/TableTools/Buttons-1.4.2/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('porto-assets/vendor/datatables/extras/TableTools/Buttons-1.4.2/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('porto-assets/vendor/datatables/extras/TableTools/Buttons-1.4.2/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('porto-assets/vendor/datatables/extras/TableTools/JSZip-2.5.0/jszip.min.js') }}"></script>
    <script src="{{ asset('porto-assets/vendor/datatables/extras/TableTools/pdfmake-0.1.32/pdfmake.min.js') }}"></script>
    <script src="{{ asset('porto-assets/vendor/datatables/extras/TableTools/pdfmake-0.1.32/vfs_fonts.js') }}"></script>
@endsection

@section('scripts')
    <script src="{{ asset('porto-assets/js/examples/examples.datatables.default.js') }}"></script>
    <script src="{{ asset('porto-assets/js/examples/examples.datatables.row.with.details.js') }}"></script>
    <script src="{{ asset('porto-assets/js/examples/examples.datatables.tabletools.js') }}"></script>
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
        
        function previewPhoto(event) {
            const input = event.target;
            const preview = document.getElementById('previewImage');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
	<script src="js/examples/examples.modals.js"></script>
@endsection
