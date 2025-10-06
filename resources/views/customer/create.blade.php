@extends('layouts.app')
<style>
    .modal-block {
            max-width: 2000px;
            margin: 20px auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        input::-ms-reveal,
        input::-ms-clear,
        input::-webkit-contacts-auto-fill-button {
            display: none !important;
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
                    <a class="nav-link active" data-bs-target="#personal" href="#personal" data-bs-toggle="tab" id="tab-personal">Personal</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#work" data-bs-toggle="tab" onclick="warnAndStayOnPersonal(event)">Work</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#reference" data-bs-toggle="tab" onclick="warnAndStayOnPersonal(event)">Reference</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#asset" data-bs-toggle="tab" onclick="warnAndStayOnPersonal(event)">Asset</a>
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
                                            <input type="file" class="form-control" id="profileImage" name="profile_image" accept="image/*" onchange="previewPhoto(event)">
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
                                        <input type="text" class="form-control" name="customer_code" placeholder="Customer Code">
                                    </div>
                                    <div class="form-group col-md-6 border-top-0 pt-0">
                                        <label for="company_code">Company Code</label>
                                        <select id="company_code" name="company_code" class="form-control">
                                            <option value="">Choose...</option>
                                            @foreach($company as $row)
                                                <option value="{{ $row->company_code }}">{{ $row->company_code }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="form-group col-md-6">
                                        <label for="customer_name">Customer Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="customer_name" name="customer_name" placeholder="Customer Name" required>
                                    </div>
                                    <div class="form-group col-md-6 border-top-0 pt-0">
                                    <label for="nric_number">NRIC Number <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="nric_number" name="nric_number" required>
                                            <button class="btn btn-outline-secondary" type="button" id="alternativeIdBtn">Alternative ID</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="form-group col-md-4 border-top-0 pt-0">
                                        <label for="gender">Gender</label>
                                        <select id="gender" name="gender" class="form-control">
                                            <option value="">Choose...</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4 border-top-0 pt-0">
                                        <label for="race">Race</label>
                                        <select id="race" name="race" class="form-control">
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
                                            <input type="date" name="date_of_birth" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="form-group col">
                                        <label for="address1">Address 1</label>
                                        <input type="text" class="form-control" id="address1" name="address1" placeholder="Address 1">
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="form-group col">
                                        <label for="address1">Address 2</label>
                                        <input type="text" class="form-control" id="address2" name="address2" placeholder="Address 2">
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="form-group col-md-4 border-top-0 pt-0">
                                        <label for="postcode">Postcode</label>
                                        <input type="number" class="form-control" id="postcode" name="postcode">
                                    </div>
                                    <div class="form-group col-md-4 border-top-0 pt-0">
                                        <label for="city">City</label>
                                        <input type="text" class="form-control" id="city" name="city">
                                    </div>
                                    <div class="form-group col-md-4 border-top-0 pt-0">
                                        <label for="state">State</label>
                                        <select id="state" name="state" class="form-control">
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
                                        <select id="house_ownership" name="house_ownership" class="form-control">
                                            <option value="">Choose...</option>
                                            @foreach($house_ownership as $houseOwnership)
                                                <option value="{{ $houseOwnership->house_ownership }}"
                                                    {{ strtolower($houseOwnership->house_ownership ?? '') == strtolower($houseOwnership->house_ownership) ? 'selected' : '' }}>
                                                    {{ $houseOwnership->house_ownership }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4 border-top-0 pt-0">
                                        <label for="warga_negara">Warga Negara</label>
                                        <input type="text" class="form-control" id="warganegara" name="warganegara">
                                    </div>
                                    <div class="form-group col-md-4 border-top-0 pt-0">
                                        <label for="marital_status">Marital Status</label>
                                        <select id="marital_status" name="marital_status" class="form-control">
                                            <option value="">Choose...</option>
                                            @foreach($marital_statues as $marital_status)
                                                <option value="{{ $marital_status->marital_status }}">{{ $marital_status->marital_status }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="form-group col-md-4 border-top-0 pt-0">
                                        <label for="email">Mail</label>
                                        <input type="email" class="form-control" id="email" name="email" autocomplete="off" readonly onfocus="this.removeAttribute('readonly');">
                                    </div>
                                    <div class="form-group col-md-4 border-top-0 pt-0">
                                        <label for="telephone">Telephone</label>
                                        <input type="number" class="form-control" id="telephone" name="telephone">
                                    </div>
                                    <div class="form-group col-md-4 border-top-0 pt-0">
                                        <label for="mobile">Mobile</label>
                                        <input type="number" class="form-control" id="mobile" name="mobile">
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
            </div>
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

        function submitReferenceForm() {
        // Get the form inside the modal
        const form = document.querySelector('#modalForm form');
        form.submit();    
        }

        function warnAndStayOnPersonal(event) {
            event.preventDefault(); // prevent tab switch
            alert('Please complete personal information first.');

            // Force switch to Personal tab
            const tabTrigger = new bootstrap.Tab(document.querySelector('#tab-personal'));
            tabTrigger.show();
        }
    </script>

    <script>
        document.getElementById('alternativeIdBtn').addEventListener('click', function() {
            alert('Alternative ID clicked!');
            // You can replace this with modal popup or logic to choose an alternative ID
        });
    </script>
	<script src="js/examples/examples.modals.js"></script>
@endsection
