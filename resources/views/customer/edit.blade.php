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
                    <a class="nav-link active" data-bs-target="#personal" href="#personal" data-bs-toggle="tab">Personal</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-target="#work" href="#work" data-bs-toggle="tab">Work</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-target="#reference" href="#reference" data-bs-toggle="tab">Reference</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-target="#asset" href="#asset" data-bs-toggle="tab">Asset</a>
                </li>
            </ul>
            <div class="tab-content">
                <div id="personal" class="tab-pane active">
                    <form class="p-3" method="POST" action="{{ route('customer.update', $customer->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <h4 class="mb-3 font-weight-semibold text-dark">Personal Information</h4>
                        <div class="row">
                            <!-- Photo Section - 30% width -->
                            <div class="col-lg-3 col-md-4 col-sm-12">
                                <section class="card">
                                    <div class="card-body">
                                        <div class="thumb-info mb-3">
                                            <img id="previewImage"
                                                src="{{ $customer->profile_image ? asset('storage/' . $customer->profile_image) : asset('porto-assets/img/!logged-user.jpg') }}"
                                                class="rounded img-fluid"
                                                alt="Profile Image">
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
                                        <input type="text" class="form-control" name="customer_code" placeholder="Customer Code" value="{{ $customer->customer_code }}" required>
                                    </div>
                                    <div class="form-group col-md-6 border-top-0 pt-0">
                                        <label for="company_code">Company Code</label>
                                        <select id="company_code" name="company_code" class="form-control" required>
                                            <option value="">Choose...</option>
                                            @foreach($company as $row)
                                                <option value="{{ $row->company_code }}"
                                                    {{ old('company_code', $customer->company_code ?? '') == $row->company_code ? 'selected' : '' }}>
                                                    {{ $row->company_code }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="form-group col-md-6">
                                        <label for="customer_name">Customer Name</label>
                                        <input type="text" class="form-control" id="customer_name" name="customer_name" placeholder="Customer Name" value="{{ $customer->customer_name }}" required>
                                    </div>
                                    <div class="form-group col-md-6 border-top-0 pt-0">
                                        <label for="nric_number">NRIC Number</label>
                                        <input type="text" class="form-control" id="nric_number" name="nric_number" value="{{ $customer->nric_number }}" required>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="form-group col-md-4 border-top-0 pt-0">
                                        <label for="gender">Gender</label>
                                        <select id="gender" name="gender" class="form-control" required>
                                            <option value="">Choose...</option>
                                            <option value="Male" {{ $customer->gender == 'Male' ? 'selected' : '' }}>Male</option>
                                            <option value="Female" {{ $customer->gender == 'Female' ? 'selected' : '' }}>Female</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4 border-top-0 pt-0">
                                        <label for="race">Race</label>
                                        <select id="race" name="race" class="form-control" required>
                                            <option value="">Choose...</option>
                                            @foreach($races as $raceItem)
                                                <option value="{{ $raceItem->race_name }}" {{ $customer->race == $raceItem->race_name ? 'selected' : '' }}>{{ $raceItem->race_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4 border-top-0 pt-0">
                                        <label for="date_of_birth">Date Of Birth</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-calendar-alt"></i>
                                            </span>
                                            <input type="date" name="date_of_birth" class="form-control" value="{{ $customer->date_of_birth }}" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="form-group col">
                                        <label for="address">Address</label>
                                        <input type="text" class="form-control" id="address" name="address" placeholder="Address" value="{{ $customer->address }}" required>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="form-group col-md-4 border-top-0 pt-0">
                                        <label for="postcode">Postcode</label>
                                        <input type="text" class="form-control" id="postcode" name="postcode" value="{{ $customer->postcode }}" required>
                                    </div>
                                    <div class="form-group col-md-4 border-top-0 pt-0">
                                        <label for="city">City</label>
                                        <input type="text" class="form-control" id="city" name="city" value="{{ $customer->city }}" required>
                                    </div>
                                    <div class="form-group col-md-4 border-top-0 pt-0">
                                        <label for="state">State</label>
                                        <select id="state" name="state" class="form-control" required>
                                            <option value="">Choose...</option>
                                            @foreach($states as $state)
                                                    <option value="{{ $state->state_name }}"
                                                    {{ strtolower($customer->state ?? '') == strtolower($state->state_name) ? 'selected' : '' }}>
                                                    {{ $state->state_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="form-group col-md-4 border-top-0 pt-0">
                                        <label for="house_ownership">House Ownership</label>
                                        <select id="house_ownership" name="house_ownership" class="form-control" required>
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
                                        <input type="text" class="form-control" id="warganegara" name="warganegara" value="{{ $customer->warganegara }}">
                                    </div>
                                    <div class="form-group col-md-4 border-top-0 pt-0">
                                        <label for="marital_status">Marital Status</label>
                                        <select id="marital_status" name="marital_status" class="form-control" required>
                                            <option value="">Choose...</option>
                                            @foreach($marital_statues as $marital_status)
                                                <option value="{{ $marital_status->marital_status }}" {{ $customer->marital_status == $marital_status->marital_status ? 'selected' : '' }}>{{ $marital_status->marital_status }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="form-group col-md-4 border-top-0 pt-0">
                                        <label for="email">Mail</label>
                                        <input type="email" class="form-control" id="email" name="email" autocomplete="off" value="{{ $customer->email }}" readonly onfocus="this.removeAttribute('readonly');">
                                    </div>
                                    <div class="form-group col-md-4 border-top-0 pt-0">
                                        <label for="telephone">Telephone</label>
                                        <input type="number" class="form-control" id="telephone" name="telephone" value="{{ $customer->telephone }}">
                                    </div>
                                    <div class="form-group col-md-4 border-top-0 pt-0">
                                        <label for="mobile">Mobile</label>
                                        <input type="number" class="form-control" id="mobile" name="mobile" value="{{ $customer->mobile }}" required>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="form-group col">
                                        <label for="remark">Remark</label>
                                        <textarea class="form-control" id="remark" name="remark" rows="3" placeholder="Enter remarks here...">{{ $customer->remark }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 text-end mt-3">
                                <button type="submit" class="btn btn-primary">Update Personal Info</button>
                            </div>
                        </div>
                    </form>
                </div>
                
                <!-- WORK TAB -->
                <div id="work" class="tab-pane">
                    <form class="p-3" method="POST" action="{{ route('customer.work.store', $customer->id) }}">
                        @csrf
                        @method('PUT')
                        
                        <h4 class="mb-3 font-weight-semibold text-dark">Work Information</h4>
                        <div class="row mb-2">
                            <div class="form-group col-md-6">
                                <label>Company Name</label>
                                <input type="text" class="form-control" name="company_name" placeholder="Company Name" value="{{ $customer->company_name }}">
                            </div>
                            <div class="form-group col-md-6 border-top-0 pt-0">
                                <label>Business Type</label>
                                <input type="text" class="form-control" name="biz_type" placeholder="Business Type" value="{{ $customer->biz_type }}">
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="form-group col-md-6">
                                <label>Designation</label>
                                <input type="text" class="form-control" name="designation" placeholder="Designation..." value="{{ $customer->designation }}">
                            </div>
                            <div class="form-group col-md-6 border-top-0 pt-0">
                                <label>Monthly Income</label>
                                <input type="number" class="form-control" name="monthly_income" placeholder="Monthly Income" value="{{ $customer->monthly_income }}">
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="form-group col">
                                <label for="company_address">Company Address</label>
                                <input type="text" class="form-control" name="company_address" placeholder="Company Address" value="{{ $customer->company_address }}">
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="form-group col-md-4 border-top-0 pt-0">
                                <label>Company Postcode</label>
                                <input type="text" class="form-control" name="company_postcode" value="{{ $customer->company_postcode }}">
                            </div>
                            <div class="form-group col-md-4 border-top-0 pt-0">
                                <label>Company City</label>
                                <input type="text" class="form-control" name="company_city" value="{{ $customer->company_city }}">
                            </div>
                            <div class="form-group col-md-4 border-top-0 pt-0">
                                <label>Company State</label>
                                <select name="company_state" class="form-control">
                                    <option value="">Choose...</option>
                                    @foreach($states as $state)
                                        <option value="{{ $state->state_name }}"
                                            {{ strtolower($customer->company_state ?? '') == strtolower($state->state_name) ? 'selected' : '' }}>
                                            {{ $state->state_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="form-group col-md-4 border-top-0 pt-0">
                                <label>Office Telephone</label>
                                <input type="number" class="form-control" name="company_telephone" value="{{ $customer->company_telephone }}">
                            </div>
                            <div class="form-group col-md-4 border-top-0 pt-0">
                                <label>Office Mobile</label>
                                <input type="number" class="form-control" name="company_mobile" value="{{ $customer->company_mobile }}">
                            </div>
                            <div class="form-group col-md-4 border-top-0 pt-0">
                                <label>Fax</label>
                                <input type="number" class="form-control" name="company_fax" value="{{ $customer->company_fax }}">
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="form-group col-md-6 border-top-0 pt-0">
                                <label>Vehicle Number</label>
                                <input type="text" class="form-control" name="vehicle_no" value="{{ $customer->vehicle_no }}">
                            </div>
                            <div class="form-group col-md-6 border-top-0 pt-0">
                                <label>Vehicle Model</label>
                                <input type="text" class="form-control" name="vehicle_model" value="{{ $customer->vehicle_model }}">
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="form-group col-md-6 border-top-0 pt-0">
                                <label>Employer Type</label>
                                <select name="employer" class="form-control" required>
                                    <option value="">Choose...</option>
                                    <option value="Private Sector" {{ $customer->employer == 'Private Sector' ? 'selected' : '' }}>Private Sector</option>
                                    <option value="Government" {{ $customer->employer == 'Government' ? 'selected' : '' }}>Government</option>
                                    <option value="Statutory Body" {{ $customer->employer == 'Statutory Body' ? 'selected' : '' }}>Statutory Body</option>
                                    <option value="Public Listed Company" {{ $customer->employer == 'Public Listed Company' ? 'selected' : '' }}>Public Listed Company</option>
                                    <option value="Multinational Corporation" {{ $customer->employer == 'Multinational Corporation' ? 'selected' : '' }}>Multinational Corporation</option>
                                    <option value="Self-Employed" {{ $customer->employer == 'Self-Employed' ? 'selected' : '' }}>Self-Employed</option>
                                    <option value="NGO" {{ $customer->employer == 'NGO' ? 'selected' : '' }}>Non-Governmental Organization (NGO)</option>
                                    <option value="Military" {{ $customer->employer == 'Military' ? 'selected' : '' }}>Military / Armed Forces</option>
                                    <option value="Retired" {{ $customer->employer == 'Retired' ? 'selected' : '' }}>Retired</option>
                                    <option value="Unemployed" {{ $customer->employer == 'Unemployed' ? 'selected' : '' }}>Unemployed</option>
                                </select>
                            </div>
                            <div class="form-group col-md-6 border-top-0 pt-0">
                                <label>Job Type</label>
                                <input type="text" class="form-control" name="job_type" value="{{ $customer->job_type }}">
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="form-group col-md-6 border-top-0 pt-0">
                                <label>Start Working Date</label>
                                <input type="date" name="start_working_date" class="form-control" value="{{ $customer->start_working_date }}">
                            </div>
                            <div class="form-group col-md-6 border-top-0 pt-0">
                                <label>End Working Date</label>
                                <input type="date" name="end_working_date" class="form-control" value="{{ $customer->end_working_date }}">
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="form-group col-md-6 border-top-0 pt-0">
                                <label>Salary Date</label>
                                <input type="date" name="salary_date" class="form-control" value="{{ $customer->salary_date }}">
                            </div>
                            <div class="form-group col-md-6 border-top-0 pt-0">
                                <label>2nd Salary Date</label>
                                <input type="date" name="2nd_salary_date" class="form-control" value="{{ $customer->{'2nd_salary_date'} }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 text-end mt-3">
                                <button type="submit" class="btn btn-primary">Save Work Info</button>
                            </div>
                        </div>
                    </form>
                </div>
                
                <!-- REFERENCE TAB -->
                <div id="reference" class="tab-pane">
                    <div class="col-lg-12 mb-3">
                        <section class="card">
                            <div class="card-header" style="text-align: left;">
                                <a class="btn btn-xs btn-square btn-primary" href="#modalReferenceForm">Add Reference</a>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered table-striped mb-0" id="datatable-reference">
                                    <thead>
                                        <tr>
                                            <th>Reference Type</th>
                                            <th>NRIC</th>
                                            <th>Name</th>
                                            <th>Mobile</th>
                                            <th>House Ownership</th>
                                            <th>Monthly Income</th>
                                            <th>City</th>
                                            <th>State</th>
                                            <th>Designation</th>
                                            <th>Company Name</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($references as $reference)
                                        <tr>
                                            <td>{{ $reference->reference_type }}</td>
                                            <td>{{ $reference->new_ic }}</td>
                                            <td>{{ $reference->name }}</td>
                                            <td>{{ $reference->mobile }}</td>
                                            <td>{{ $reference->house_ownership }}</td>
                                            <td>{{ $reference->monthly_income }}</td>
                                            <td>{{ $reference->city }}</td>
                                            <td>{{ $reference->state }}</td>
                                            <td>{{ $reference->designation ?? $reference->job }}</td>
                                            <td>{{ $reference->company_name }}</td>
                                            <td>
                                                <a onclick="if(confirm('Are you sure you want to delete?')){window.location.href='{{ route('customer.reference.destroy', $reference->id) }}'}" title="Delete" style="cursor:pointer"><i class="bx bx-trash"></i></a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </section>
                    </div>   
                </div>

                <!-- ASSET TAB -->
                <div id="asset" class="tab-pane">
                    <div class="col-lg-12 mb-3">
                        <section class="card">
                            <div class="card-header" style="text-align: left;">
                                <a class="btn btn-xs btn-square btn-primary" href="#modalAssetForm">Add Asset</a>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered table-striped mb-0" id="datatable-asset">
                                    <thead>
                                        <tr>
                                            <th>Item</th>
                                            <th>Remark</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($assets as $asset)
                                            <tr>
                                                <td>{{ $asset->item }}</td>
                                                <td>{{ $asset->remark }}</td>
                                                <td>
                                                    <a onclick="if(confirm('Are you sure you want to delete?')){window.location.href='{{ route('customer.asset.destroy', $asset->id) }}'}" title="Delete" style="cursor:pointer"><i class="bx bx-trash"></i></a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </section>
                    </div>   
                </div>

            </div>
        </div>
    </div>

    <!-- Reference Modal Form -->
    <div id="modalReferenceForm" class="modal-block modal-block-primary modal-block-lg mfp-hide">
        <section class="card">
            <header class="card-header">
                <h2 class="card-title">Reference Form</h2>
            </header>
            <div class="card-body">
                <form method="POST" action="{{ route('customer.reference.store') }}" id="referenceForm">
                    @csrf
                    <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>Reference Type</label>
                            <select name="reference_type" class="form-control" required>
                            <option value="" disabled selected>Choose...</option>
                            @foreach($reference_types as $type)
                                <option value="{{ $type->reference_type }}">{{ $type->reference_type }}</option>
                            @endforeach
                        </select>
                        </div>
                        <div class="col-md-6">
                            <label>Reference NRIC</label>
                            <input type="text" class="form-control" name="new_ic" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label>Reference Name</label>
                            <input type="text" class="form-control" name="name" placeholder="Reference name" required>
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="form-group col-md-4 border-top-0 pt-0">
                            <label>Gender</label>
                            <select name="gender" class="form-control">
                                <option value="">Choose...</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4 border-top-0 pt-0">
                            <label>Race</label>
                            <select name="race" class="form-control">
                                <option value="">Choose...</option>
                                @foreach($races as $race)
                                    <option value="{{ $race->race_name }}">{{ $race->race_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-4 border-top-0 pt-0">
                            <label>Date Of Birth</label>
                            <input type="date" name="date_of_birth" class="form-control">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>Mobile</label>
                            <input type="number" class="form-control" name="mobile" placeholder="Mobile number" required>
                        </div>
                        <div class="col-md-6">
                            <label>Telephone</label>
                            <input type="number" class="form-control" name="telephone" placeholder="Telephone number">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>House Ownership</label>
                            <select id="house_ownership" name="house_ownership" class="form-control" required>
                                <option value="">Choose...</option>
                                @foreach($house_ownership as $houseOwnership)
                                    <option value="{{ $houseOwnership->house_ownership }}"
                                        {{ strtolower($houseOwnership->house_ownership ?? '') == strtolower($houseOwnership->house_ownership) ? 'selected' : '' }}>
                                        {{ $houseOwnership->house_ownership }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Warga Negara</label>
                            <input type="text" class="form-control" name="warganegara">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label>Address</label>
                            <input type="text" class="form-control" name="address" placeholder="Address">
                        </div>
                    </div>
                    
                    <div class="row mb-2">
                        <div class="form-group col-md-4 border-top-0 pt-0">
                            <label>Postcode</label>
                            <input type="text" class="form-control" name="postcode">
                        </div>
                        <div class="form-group col-md-4 border-top-0 pt-0">
                            <label>City</label>
                            <input type="text" class="form-control" name="city">
                        </div>
                        <div class="form-group col-md-4 border-top-0 pt-0">
                            <label>State</label>
                            <select name="state" class="form-control">
                                <option value="">Choose...</option>
                                @foreach($states as $state)
                                        <option value="{{ $state->state_name }}"
                                        {{ strtolower($reference->state ?? '') == strtolower($state->state_name) ? 'selected' : '' }}>
                                        {{ $state->state_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <!-- Work Information -->
                    <h5 class="mt-4 mb-3">Work Information</h5>
                    <div class="row mb-2">
                        <div class="form-group col-md-6">
                            <label>Company Name</label>
                            <input type="text" class="form-control" name="company_name" placeholder="Company Name">
                        </div>
                        <div class="form-group col-md-6 border-top-0 pt-0">
                            <label>Business Type</label>
                            <input type="text" class="form-control" name="biz_type" placeholder="Business Type">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="form-group col-md-6">
                            <label>Designation</label>
                            <input type="text" class="form-control" name="designation" placeholder="Designation...">
                        </div>
                        <div class="form-group col-md-6 border-top-0 pt-0">
                            <label>Monthly Income</label>
                            <input type="number" class="form-control" name="monthly_income" placeholder="Monthly Income">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label>Company Address</label>
                            <input type="text" class="form-control" name="company_address" placeholder="Company Address">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="form-group col-md-4 border-top-0 pt-0">
                            <label>Company Postcode</label>
                            <input type="text" class="form-control" name="company_postcode">
                        </div>
                        <div class="form-group col-md-4 border-top-0 pt-0">
                            <label>Company City</label>
                            <input type="text" class="form-control" name="company_city">
                        </div>
                        <div class="form-group col-md-4 border-top-0 pt-0">
                            <label>Company State</label>
                            <select name="company_state" class="form-control">
                                <option value="">Choose...</option>
                                @foreach($states as $state)
                                        <option value="{{ $state->state_name }}"
                                        {{ strtolower($reference->company_state ?? '') == strtolower($state->state_name) ? 'selected' : '' }}>
                                        {{ $state->state_name }}
                                    </option>
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
                        <button class="btn btn-primary modal-confirm" onclick="submitReferenceForm()">Submit</button>
                    </div>
                </div>
            </footer>
        </section>
    </div>

    <!-- Asset Modal Form -->
    <div id="modalAssetForm" class="modal-block modal-block-primary modal-block-sm mfp-hide">
        <section class="card">
            <header class="card-header">
                <h2 class="card-title">Asset Form</h2>
            </header>
            <div class="card-body">
                <form method="POST" action="{{ route('customer.asset.store') }}" id="assetForm">
                    @csrf
                    <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label>Item</label>
                            <input type="text" class="form-control" name="item" placeholder="Enter item name" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label>Remark</label>
                            <textarea class="form-control" name="remark" rows="4" placeholder="Enter remarks here..."></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <footer class="card-footer">
                <div class="row">
                    <div class="col-md-12 text-end">
                        <button class="btn btn-secondary modal-dismiss">Cancel</button>
                        <button class="btn btn-primary modal-confirm" onclick="submitAssetForm()">Submit</button>
                    </div>
                </div>
            </footer>
        </section>
    </div>
</div>
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
            // Initialize DataTables for both reference and asset tables
            $('#datatable-reference').DataTable();
            $('#datatable-asset').DataTable();
            
            // Initialize Magnific Popup for reference modal
            $('a[href="#modalReferenceForm"]').magnificPopup({
                type: 'inline',
                preloader: false,
                modal: true
            });
            
            // Initialize Magnific Popup for asset modal
            $('a[href="#modalAssetForm"]').magnificPopup({
                type: 'inline',
                preloader: false,
                modal: true
            });
            
            // Handle modal close
            $(document).on('click', '.modal-dismiss', function (e) {
                e.preventDefault();
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
            const form = document.getElementById('referenceForm');

            if (form.checkValidity()) {
                form.submit();
            } else {
                alert("Please fill all required fields in the reference form");
            }
        }

        function submitAssetForm() {
            const form = document.getElementById('assetForm');

            if (form.checkValidity()) {
                form.submit();
            } else {
                alert("Please fill all required fields in the asset form");
            }
        }
    </script>
	<script src="js/examples/examples.modals.js"></script>
@endsection