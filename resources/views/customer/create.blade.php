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
    <h2>{{ __('table.customer_details') }}</h2>
</header>

@include('layouts.flash-message')
<div class="row">
    <div class="col-sm-12 col-md-8 col-lg-9 col-xl-12">
        <div class="tabs">
            <ul class="nav nav-tabs">
                <li class="nav-item active">
                    <a class="nav-link active" data-bs-target="#personal" href="#personal" data-bs-toggle="tab" id="tab-personal">{{ __('table.personal') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#work" data-bs-toggle="tab" onclick="warnAndStayOnPersonal(event)">{{ __('table.work') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#reference" data-bs-toggle="tab" onclick="warnAndStayOnPersonal(event)">{{ __('table.reference') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#asset" data-bs-toggle="tab" onclick="warnAndStayOnPersonal(event)">{{ __('table.asset') }}</a>
                </li>
            </ul>
            <div class="tab-content">
                <div id="personal" class="tab-pane active">
                    <form class="p-3" method="POST" action="{{ route('customer.store') }}" enctype="multipart/form-data">
                        @csrf
                        <h4 class="mb-3 font-weight-semibold text-dark">{{ __('table.personal_information') }}</h4>
                        <div class="row g-5">
                            
                            <!-- Photo Section - 30% width -->
                            <div class="col-lg-3 col-md-12 col-sm-12">
                                <section class="card">
                                    <div class="card-body">
                                        <div class="thumb-info mb-3">
                                            @if(isset($customer) && $customer->profile_image)
                                                <img id="previewImage" src="{{ asset('storage/'.$customer->profile_image) }}" class="rounded img-fluid" alt="Profile Image">
                                            @else
                                                <img id="previewImage" src="{{ asset('porto-assets/img/!logged-user.jpg') }}" class="rounded img-fluid" alt="Profile Image">
                                            @endif
                                        </div>
                                        <div class="clearfix">
                                            <input type="file" class="form-control" id="profileImage" name="profile_image" accept="image/*" onchange="previewPhoto(event)">
                                        </div>
                                    </div>
                                </section>
                                <ul class="simple-card-list mb-3 d-none d-lg-block">
                                    <li class="primary">
                                        <h3>$$</h3>
                                        <p class="text-light">{{ __('table.total_loan') }}</p>
                                    </li>
                                    <li class="primary">
                                        <h3>$$</h3>
                                        <p class="text-light">{{ __('table.total_loan_amount') }}</p>
                                    </li>
                                    <li class="primary">
                                        <h3>$$</h3>
                                        <p class="text-light">{{ __('table.total_outstanding') }}</p>
                                    </li>
                                </ul>
                            </div>

                            <!-- Information Section - 70% width -->
                            <div class="col-lg-9 col-md-12 col-sm-12">
                                <div class="row mb-2">
                                    <div class="form-group col-md-6 border-top-0 pt-0">
                                        <label for="company_code">{{ __('table.company_code') }} <span class="text-danger">*</span></label>
                                        <select id="company_code" name="company_code" class="form-control" required>
                                            <option value="">{{ __('table.choose') }}...</option>
                                            @foreach($company as $row)
                                                <option value="{{ $row->company_code }}">{{ $row->company_code }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="form-group col-md-6">
                                        <label for="customer_name">{{ __('table.customer_name') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="customer_name" name="customer_name" placeholder="Customer Name" required>
                                    </div>
                                    <div class="form-group col-md-6 border-top-0 pt-0">
                                        <label for="nric_number">NRIC {{ __('table.number') }} <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="nric_number" name="nric_number" value="{{ old('nric_number') }}" required>
                                            <button class="btn btn-outline-secondary" type="button" id="uploadIcBtn">{{ __('table.upload_ic') }}</button>
                                        </div>
                                        
                                        <!-- Hidden file input -->
                                        <input type="file" class="d-none" id="nric_image" name="nric_image" accept="image/*">
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="form-group col-md-4 border-top-0 pt-0">
                                        <label for="gender">{{ __('table.gender') }} <span class="text-danger">*</span></label>
                                        <select id="gender" name="gender" class="form-control" required>
                                            <option value="">{{ __('table.choose') }}...</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4 border-top-0 pt-0">
                                        <label for="race">{{ __('table.race') }} <span class="text-danger">*</span></label>
                                        <select id="race" name="race" class="form-control" required>
                                            <option value="">{{ __('table.choose') }}...</option>
                                            @foreach($races as $raceItem)
                                            <option value="{{ $raceItem->race_name }}">{{ $raceItem->race_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4 border-top-0 pt-0">
                                        <label for="date_of_birth">{{ __('table.date_of_birth') }} <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-calendar-alt"></i>
                                            </span>
                                            <input type="date" name="date_of_birth" class="form-control" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="form-group col-md-6 border-top-0 pt-0">
                                        <label for="email">{{ __('table.email') }}</label>
                                        <input type="email" class="form-control" id="email" name="email" autocomplete="off" readonly onfocus="this.removeAttribute('readonly');">
                                    </div>
                                    <div class="form-group col-md-6 border-top-0 pt-0">
                                        <label for="mobile">{{ __('table.mobile') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="mobile" name="mobile" required>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="form-group col">
                                        <label for="address1">{{ __('table.address') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="address1" name="address1" placeholder="Address 1" required>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="form-group col">
                                        <input type="text" class="form-control" id="address2" name="address2" placeholder="Address 2">
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="form-group col-md-4 border-top-0 pt-0">
                                        <label for="postcode">{{ __('table.postcode') }} <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="postcode" name="postcode" required>
                                    </div>
                                    <div class="form-group col-md-4 border-top-0 pt-0">
                                        <label for="city">{{ __('table.city') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="city" name="city" required>
                                    </div>
                                    <div class="form-group col-md-4 border-top-0 pt-0">
                                        <label for="state">{{ __('table.state') }} <span class="text-danger">*</span></label>
                                        <select id="state" name="state" class="form-control" required>
                                            <option value="">{{ __('table.choose') }}...</option>
                                            @foreach($states as $stateItem)
                                                <option value="{{ $stateItem->state_name }}">{{ $stateItem->state_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="form-group col-md-4 border-top-0 pt-0">
                                        <label for="house_ownership">{{ __('table.house_ownership') }} <span class="text-danger">*</span></label>
                                        <select id="house_ownership" name="house_ownership" class="form-control" required>
                                            <option value="">{{ __('table.choose') }}...</option>
                                            @foreach($house_ownership as $houseOwnership)
                                                <option value="{{ $houseOwnership->house_ownership }}"
                                                    {{ strtolower($houseOwnership->house_ownership ?? '') == strtolower($houseOwnership->house_ownership) ? 'selected' : '' }}>
                                                    {{ $houseOwnership->house_ownership }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4 border-top-0 pt-0">
                                        <label for="warga_negara">{{ __('table.warganegara') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="warganegara" name="warganegara" required>
                                    </div>
                                    <div class="form-group col-md-4 border-top-0 pt-0">
                                        <label for="marital_status">{{ __('table.marital_status') }} <span class="text-danger">*</span></label>
                                        <select id="marital_status" name="marital_status" class="form-control" required>
                                            <option value="">{{ __('table.choose') }}...</option>
                                            @foreach($marital_statues as $marital_status)
                                                <option value="{{ $marital_status->marital_status }}">{{ $marital_status->marital_status }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="form-group col">
                                        <label for="remark">{{ __('table.remark') }}</label>
                                        <textarea class="form-control" id="remark" name="remark" rows="6" placeholder="Enter remarks here..."></textarea>
                                    </div>
                                </div>

                                <!-- <div class="row mb-2">
                                    <div class="form-group col">
                                        <label for="image" class="form-label">Upload Image <span class="text-danger">*</span></label>
                                        <div id="imagePreviewContainer" class="mb-3 d-none">
                                            <div class="position-relative d-inline-block">
                                                <img id="imagePreview" src="" alt="Preview" class="img-thumbnail" style="max-width: 300px;">
                                                <button type="button" id="removeImage" class="btn btn-remove-image btn-primary btn-sm position-absolute top-0 end-0 m-2"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-x"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M18 6l-12 12" /><path d="M6 6l12 12" /></svg></button>
                                            </div>
                                        </div>
                                        <input type="file" class="form-control" id="image" name="nric_image" accept="image/*" required>
                                    </div>
                                </div> -->
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 text-end mt-3">
                                <button type="submit" class="btn btn-primary">{{ __('table.save') }}</button>
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
        
        document.getElementById('uploadIcBtn').addEventListener('click', function() {
                document.getElementById('nric_image').click();
            });
       
        document.addEventListener('DOMContentLoaded', function() {
            const imageInput = document.getElementById('image');
            const imagePreview = document.getElementById('imagePreview');
            const imagePreviewContainer = document.getElementById('imagePreviewContainer');
            const removeImageBtn = document.getElementById('removeImage');

            // Show preview when file selected
            imageInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                
                if (file) {
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        imagePreview.src = e.target.result;
                        imagePreviewContainer.classList.remove('d-none');
                    }
                    
                    reader.readAsDataURL(file);
                }
            });

            // Remove image
            removeImageBtn.addEventListener('click', function() {
                imageInput.value = '';
                imagePreview.src = '';
                imagePreviewContainer.classList.add('d-none');
            });
        });

    </script>
	<script src="js/examples/examples.modals.js"></script>
@endsection
