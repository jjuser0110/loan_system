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
    
    .action-buttons {
        display: flex;
        gap: 5px;
        align-items: center;
    }
    
    .action-buttons a {
        padding: 5px 8px;
        border-radius: 4px;
        text-decoration: none;
        color: #fff;
        font-size: 12px;
    }
    
    .btn-view { background-color: #28a745; }
    .btn-edit { background-color: #007bff; }
    .btn-delete { background-color: #dc3545; }
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
                 <li class="nav-item">
                    <a class="nav-link" data-bs-target="#loan" href="#loan" data-bs-toggle="tab">Loan</a>
                </li>
                 <!-- <li class="nav-item">
                    <a class="nav-link" data-bs-target="#payment" href="#payment" data-bs-toggle="tab">Payment</a>
                </li> -->
            </ul>
            <div class="tab-content">
                <div id="personal" class="tab-pane active">
                    <form class="p-3" method="POST" action="{{ route('customer.update', $customer->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <h4 class="mb-3 font-weight-semibold text-dark">Personal Information</h4>
                        <div class="row">
                            <div class="col-lg-12 col-md-8 col-sm-12">
                                <div class="row mb-2">
                                    <div class="form-group col-md-6">
                                        <label>System Code</label>
                                        <input type="text" class="form-control" value="{{ $customer->customer_code }}" readonly disabled>
                                    </div>
                                    <div class="form-group col-md-6 border-top-0 pt-0">
                                        <label for="company_code">Company Code <span class="text-danger">*</span></label>
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
                                        <label for="customer_name">Customer Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="customer_name" name="customer_name" placeholder="Customer Name" value="{{ $customer->customer_name }}" required>
                                    </div>
                                    <div class="form-group col-md-6 border-top-0 pt-0">
                                    <label for="nric_number">NRIC Number <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="nric_number" name="nric_number" value="{{ $customer->nric_number }}" required>
                                        <button class="btn btn-outline-secondary" type="button" id="alternativeIdBtn">Alternative ID</button>
                                    </div>
                                </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="form-group col-md-4 border-top-0 pt-0">
                                        <label for="gender">Gender <span class="text-danger">*</span></label>
                                        <select id="gender" name="gender" class="form-control" required>
                                            <option value="">Choose...</option>
                                            <option value="Male" {{ $customer->gender == 'Male' ? 'selected' : '' }}>Male</option>
                                            <option value="Female" {{ $customer->gender == 'Female' ? 'selected' : '' }}>Female</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4 border-top-0 pt-0">
                                        <label for="race">Race <span class="text-danger">*</span></label>
                                        <select id="race" name="race" class="form-control" required>
                                            <option value="">Choose...</option>
                                            @foreach($races as $raceItem)
                                                <option value="{{ $raceItem->race_name }}" {{ $customer->race == $raceItem->race_name ? 'selected' : '' }}>{{ $raceItem->race_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4 border-top-0 pt-0">
                                        <label for="date_of_birth">Date Of Birth <span class="text-danger">*</span></label>
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
                                        <label for="address">Address <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="address1" name="address1" placeholder="Address" value="{{ $customer->address1 }}" required>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="form-group col-md-4 border-top-0 pt-0">
                                        <label for="postcode">Postcode <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="postcode" name="postcode" value="{{ $customer->postcode }}" required>
                                    </div>
                                    <div class="form-group col-md-4 border-top-0 pt-0">
                                        <label for="city">City <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="city" name="city" value="{{ $customer->city }}" required>
                                    </div>
                                    <div class="form-group col-md-4 border-top-0 pt-0">
                                        <label for="state">State <span class="text-danger">*</span></label>
                                        <select id="state" name="state" class="form-control" required>
                                            <option value="">Choose...</option>
                                            @foreach($states as $state)
                                            <option value="{{ $state->state_name }}" {{ $customer->state == $state->state_name ? 'selected' : '' }}>{{ $state->state_name }} </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="form-group col-md-4 border-top-0 pt-0">
                                        <label for="house_ownership">House Ownership <span class="text-danger">*</span></label>
                                        <select id="house_ownership" name="house_ownership" class="form-control" required>
                                            <option value="">Choose...</option>
                                            @foreach($house_ownership as $houseOwnership)
                                                <option value="{{ $houseOwnership->house_ownership }}"
                                                    {{ strtolower($customer->house_ownership ?? '') == strtolower($houseOwnership->house_ownership) ? 'selected' : '' }}>
                                                    {{ $houseOwnership->house_ownership }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4 border-top-0 pt-0">
                                        <label for="warga_negara">Warga Negara <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="warganegara" name="warganegara" value="{{ $customer->warganegara }}" required>
                                    </div>
                                    <div class="form-group col-md-4 border-top-0 pt-0">
                                        <label for="marital_status">Marital Status <span class="text-danger">*</span></label>
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
                                        <label for="email">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" autocomplete="off" value="{{ $customer->email }}" readonly onfocus="this.removeAttribute('readonly');">
                                    </div>
                                    <div class="form-group col-md-4 border-top-0 pt-0">
                                        <label for="mobile">Mobile <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="mobile" name="mobile" value="{{ $customer->mobile }}" required>
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <div class="form-group col">
                                        <label for="remark">Remark</label>
                                        <textarea class="form-control" id="remark" name="remark" rows="3" placeholder="Enter remarks here..." >{{ $customer->remark ?? ''}}</textarea>
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <div class="form-group col">
                                        <label for="image" class="form-label">Upload Image <span class="text-danger">*</span></label>
                                        <div id="imagePreviewContainer" class="mb-3 @if(!$customer->nric_path) d-none @endif">
                                            <div class="position-relative d-inline-block">
                                                <img id="imagePreview" src="{{ $customer->nric_path ? asset('storage/' . $customer->nric_path) : '' }}" alt="Preview" class="img-thumbnail" style="max-width: 300px;">
                                                <button type="button" id="removeImage" class="btn btn-remove-image btn-primary btn-sm position-absolute top-0 end-0 m-2">
                                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-x">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                        <path d="M18 6l-12 12" />
                                                        <path d="M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                        <input type="file" class="form-control" id="image" name="new_nric_image" accept="image/*">
                                        <!-- Hidden input to track if user wants to remove the existing image -->
                                        <input type="hidden" id="removeExistingImage" name="remove_existing_image" value="0">
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
                                <input type="text" class="form-control" name="biz_type" placeholder="Business Type" value="{{ $customer->biz_type }}" required>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="form-group col-md-6">
                                <label>Designation</label>
                                <input type="text" class="form-control" name="designation" placeholder="Designation..." value="{{ $customer->designation }}" required>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="form-group col">
                                <label for="company_address">Company Address</label>
                                <input type="text" class="form-control" name="company_address1" placeholder="Company Address 1" value="{{ $customer->company_address1 }}" required>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="form-group col-md-4 border-top-0 pt-0">
                                <label>Company Postcode</label>
                                <input type="number" class="form-control" name="company_postcode" value="{{ $customer->company_postcode }}">
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
                                <input type="text" class="form-control" name="company_telephone" value="{{ $customer->company_telephone }}">
                            </div>
                            <div class="form-group col-md-4 border-top-0 pt-0">
                                <label>Office Mobile</label>
                                <input type="text" class="form-control" name="company_mobile" value="{{ $customer->company_mobile }}">
                            </div>
                            <div class="form-group col-md-4 border-top-0 pt-0">
                                <label>Fax</label>
                                <input type="text" class="form-control" name="company_fax" value="{{ $customer->company_fax }}">
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
                                   <select name="job_type" class="form-control" required>
                                    <option value="">Choose...</option>
                                    <option value="Fulltime" {{ $customer->job_type == 'Fulltime' ? 'selected' : '' }}>Fulltime</option>
                                    <option value="Contract" {{ $customer->job_type == 'Contract' ? 'selected' : '' }}>Contract</option>
                                    <option value="Partime" {{ $customer->job_type == 'Partime' ? 'selected' : '' }}>Partime</option>
                                    <option value="Other" {{ $customer->job_type == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="form-group col-md-4 border-top-0 pt-0">
                                <label>Monthly Income</label>
                                <input type="number" class="form-control" name="monthly_income" placeholder="Monthly Income" value="{{ $customer->monthly_income }}">
                            </div>
                            <div class="form-group col-md-4 border-top-0 pt-0">
                                <label>Salary Date</label>
                                <input type="date" name="salary_date" class="form-control" value="{{ $customer->salary_date }}">
                            </div>
                            <div class="form-group col-md-4 border-top-0 pt-0">
                                <label>Start Working Date</label>
                                <input type="date" name="start_working_date" class="form-control" value="{{ $customer->start_working_date }}">
                            </div>
                           
                        </div>
                        <div class="row mb-2">
                            <div class="form-group col-md-4 border-top-0 pt-0">
                                <label>Monthly Income 2</label>
                                <input type="number" class="form-control" name="monthly_income_2" placeholder="Monthly Income" value="{{ $customer->monthly_income_2 }}">
                            </div>
                            <div class="form-group col-md-4 border-top-0 pt-0">
                                <label>Salary Date 2</label>
                                <input type="date" name="salary_date_2" class="form-control" value="{{ $customer->salary_date_2 }}">
                            </div>
                             <div class="form-group col-md-4 border-top-0 pt-0">
                                <label>End Working Date</label>
                                <input type="date" name="end_working_date" class="form-control" value="{{ $customer->end_working_date }}">
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
                            <div class="card-header" style="text-align: right;">
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
                                                <div class="action-buttons">
                                                    <a href="javascript:void(0)" onclick="editReference({{ $reference->id }})" class="btn-edit" title="Edit">
                                                        <i class="bx bx-edit"></i>
                                                    </a>
                                                    <a onclick="if(confirm('Are you sure you want to delete?')){window.location.href='{{ route('customer.reference.destroy', $reference->id) }}'}" title="Delete" class="btn-delete" style="cursor:pointer">
                                                        <i class="bx bx-trash"></i>
                                                    </a>
                                                </div>
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
                            <div class="card-header" style="text-align: right;">
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
                                                <td class="remark-display">
                                                    {!! nl2br(e($asset->remark)) !!}
                                                </td>
                                                <td>
                                                    <div class="action-buttons">
                                                        <a href="javascript:void(0)" onclick="editAsset({{ $asset->id }})" class="btn-edit" title="Edit">
                                                            <i class="bx bx-edit"></i>
                                                        </a>
                                                        <a onclick="if(confirm('Are you sure you want to delete?')){window.location.href='{{ route('customer.asset.destroy', $asset->id) }}'}" title="Delete" class="btn-delete" style="cursor:pointer">
                                                            <i class="bx bx-trash"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </section>
                    </div>   
                </div>

                <!-- LOAN TAB -->
                <div id="loan" class="tab-pane">
                    <div class="col-lg-12 mb-3">
                        <section class="card" style="overflow:auto">
                            <div class="card-header" style="text-align: right;">
                                <a class="btn btn-xs btn-square btn-primary" target="_blank" href="{{ route('loan.create',['customer_code'=>$customer->customer_code,'company_code'=>$customer->company_code]) }}">Add Loan</a>
                            </div>
                            <div class="card-body">
                                <table class="table cus-table table-bordered table-striped mb-0" id="table-loan">
                                    <thead>
                                        <tr>
                                            <th>Loan Code</th>
                                            <th>Company</th>
                                            <th>Interest Group</th>
                                            <th>Interest Rate</th>
                                            <th>Loan Amount</th>
                                            <th>Installment</th>
                                            <th>Loan Term</th>
                                            <th>Capital</th>
                                            <th>Created At</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </section>
                    </div>   
                </div>

                <!-- <div id="payment" class="tab-pane">
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
                                                <div class="action-buttons">
                                                    <a href="javascript:void(0)" onclick="editReference({{ $reference->id }})" class="btn-edit" title="Edit">
                                                        <i class="bx bx-edit"></i>
                                                    </a>
                                                    <a onclick="if(confirm('Are you sure you want to delete?')){window.location.href='{{ route('customer.reference.destroy', $reference->id) }}'}" title="Delete" class="btn-delete" style="cursor:pointer">
                                                        <i class="bx bx-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </section>
                    </div>   
                </div> -->

            </div>
        </div>
    </div>

    <!-- Reference Modal Form (Add) -->
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
                            <label>Reference Type <span class="text-danger">*</span></label>
                            <select name="reference_type" class="form-control" required>
                            <option value="" disabled selected>Choose...</option>
                            @foreach($reference_types as $type)
                                <option value="{{ $type->reference_type }}">{{ $type->reference_type }}</option>
                            @endforeach
                        </select>
                        </div>
                        <div class="col-md-6">
                            <label>Reference NRIC <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="new_ic" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label>Reference Name <span class="text-danger">*</span></label>
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
                            <input type="number" class="form-control" name="mobile" placeholder="Mobile number">
                        </div>
                        <div class="col-md-6">
                            <label>Telephone</label>
                            <input type="number" class="form-control" name="telephone" placeholder="Telephone number">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>House Ownership</label>
                            <select id="house_ownership" name="house_ownership" class="form-control">
                                <option value="">Choose...</option>
                                @foreach($house_ownership as $houseOwnership)
                                    <option value="{{ $houseOwnership->house_ownership }}">
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
                            <label>Address 1</label>
                            <input type="text" class="form-control" name="address1" placeholder="Address 1">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label>Address 2</label>
                            <input type="text" class="form-control" name="address2" placeholder="Address 2">
                        </div>
                    </div>
                    
                    <div class="row mb-2">
                        <div class="form-group col-md-4 border-top-0 pt-0">
                            <label>Postcode</label>
                            <input type="number" class="form-control" name="postcode">
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
                                    <option value="{{ $state->state_name }}">
                                        {{ $state->state_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <br>
                    <br>
                    
                    <!-- Work Information -->
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
                            <label>Company Address 1</label>
                            <input type="text" class="form-control" name="company_address1" placeholder="Company Address 1">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label>Company Address 2</label>
                            <input type="text" class="form-control" name="company_address2" placeholder="Company Address 2">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="form-group col-md-4 border-top-0 pt-0">
                            <label>Company Postcode</label>
                            <input type="number" class="form-control" name="company_postcode">
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
                                    <option value="{{ $state->state_name }}">
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

    <!-- Reference Edit Modal -->
    <div id="modalReferenceEditForm" class="modal-block modal-block-primary modal-block-lg mfp-hide">
        <section class="card">
            <header class="card-header">
                <h2 class="card-title">Edit Reference</h2>
            </header>
            <div class="card-body">
                <form method="POST" id="referenceEditForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                    <input type="hidden" id="edit_reference_id" name="reference_id">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>Reference Type <span class="text-danger">*</span></label>
                            <select name="reference_type" id="edit_reference_type" class="form-control" required>
                                <option value="" disabled>Choose...</option>
                                @foreach($reference_types as $type)
                                    <option value="{{ $type->reference_type }}">{{ $type->reference_type }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Reference NRIC <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_new_ic" name="new_ic" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label>Reference Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_name" name="name" placeholder="Reference name" required>
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="form-group col-md-4 border-top-0 pt-0">
                            <label>Gender</label>
                            <select name="gender" id="edit_gender" class="form-control">
                                <option value="">Choose...</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4 border-top-0 pt-0">
                            <label>Race</label>
                            <select name="race" id="edit_race" class="form-control">
                                <option value="">Choose...</option>
                                @foreach($races as $race)
                                    <option value="{{ $race->race_name }}">{{ $race->race_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-4 border-top-0 pt-0">
                            <label>Date Of Birth</label>
                            <input type="date" name="date_of_birth" id="edit_date_of_birth" class="form-control">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>Mobile</label>
                            <input type="number" class="form-control" id="edit_mobile" name="mobile" placeholder="Mobile number">
                        </div>
                        <div class="col-md-6">
                            <label>Telephone</label>
                            <input type="number" class="form-control" id="edit_telephone" name="telephone" placeholder="Telephone number">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>House Ownership</label>
                            <select name="house_ownership" id="edit_house_ownership" class="form-control" required>
                                <option value="">Choose...</option>
                                @foreach($house_ownership as $houseOwnership)
                                    <option value="{{ $houseOwnership->house_ownership }}">
                                        {{ $houseOwnership->house_ownership }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Warga Negara</label>
                            <input type="text" class="form-control" id="edit_warganegara" name="warganegara">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label>Address 1</label>
                            <input type="text" class="form-control" id="edit_address1" name="address1" placeholder="Address 1">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label>Address 2</label>
                            <input type="text" class="form-control" id="edit_address2" name="address2" placeholder="Address 2">
                        </div>
                    </div>
                    
                    <div class="row mb-2">
                        <div class="form-group col-md-4 border-top-0 pt-0">
                            <label>Postcode</label>
                            <input type="number" class="form-control" id="edit_postcode" name="postcode">
                        </div>
                        <div class="form-group col-md-4 border-top-0 pt-0">
                            <label>City</label>
                            <input type="text" class="form-control" id="edit_city" name="city">
                        </div>
                        <div class="form-group col-md-4 border-top-0 pt-0">
                            <label>State</label>
                            <select name="state" id="edit_state" class="form-control">
                                <option value="">Choose...</option>
                                @foreach($states as $state)
                                    <option value="{{ $state->state_name }}">
                                        {{ $state->state_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <!-- Work Information -->
                    <div class="row mb-2">
                        <div class="form-group col-md-6">
                            <label>Company Name</label>
                            <input type="text" class="form-control" id="edit_company_name" name="company_name" placeholder="Company Name">
                        </div>
                        <div class="form-group col-md-6 border-top-0 pt-0">
                            <label>Business Type</label>
                            <input type="text" class="form-control" id="edit_biz_type" name="biz_type" placeholder="Business Type">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="form-group col-md-6">
                            <label>Designation</label>
                            <input type="text" class="form-control" id="edit_designation" name="designation" placeholder="Designation...">
                        </div>
                        <div class="form-group col-md-6 border-top-0 pt-0">
                            <label>Monthly Income</label>
                            <input type="number" class="form-control" id="edit_monthly_income" name="monthly_income" placeholder="Monthly Income">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label>Company Address 1</label>
                            <input type="text" class="form-control" id="edit_company_address1" name="company_address1" placeholder="Company Address 1">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label>Company Address 2</label>
                            <input type="text" class="form-control" id="edit_company_address2" name="company_address2" placeholder="Company Address 2">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="form-group col-md-4 border-top-0 pt-0">
                            <label>Company Postcode</label>
                            <input type="number" class="form-control" id="edit_company_postcode" name="company_postcode">
                        </div>
                        <div class="form-group col-md-4 border-top-0 pt-0">
                            <label>Company City</label>
                            <input type="text" class="form-control" id="edit_company_city" name="company_city">
                        </div>
                        <div class="form-group col-md-4 border-top-0 pt-0">
                            <label>Company State</label>
                            <select name="company_state" id="edit_company_state" class="form-control">
                                <option value="">Choose...</option>
                                @foreach($states as $state)
                                    <option value="{{ $state->state_name }}">
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
                        <button class="btn btn-primary" onclick="submitReferenceEditForm()">Update</button>
                    </div>
                </div>
            </footer>
        </section>
    </div>
    
    <!-- Asset Modal Form (Add) -->
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
                            <textarea class="form-control" name="remark" rows="4" placeholder="Enter remarks here..." style="white-space: pre-wrap;"></textarea>
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

    <!-- Asset Edit Modal -->
    <div id="modalAssetEditForm" class="modal-block modal-block-primary modal-block-sm mfp-hide">
        <section class="card">
            <header class="card-header">
                <h2 class="card-title">Edit Asset</h2>
            </header>
            <div class="card-body">
                <form method="POST" id="assetEditForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                    <input type="hidden" id="edit_asset_id" name="asset_id">
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label>Item</label>
                            <input type="text" class="form-control" id="edit_item" name="item" placeholder="Enter item name" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label>Remark</label>
                            <textarea class="form-control" id="edit_remark" name="remark" rows="4" placeholder="Enter remarks here..." style="white-space: pre-wrap;"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <footer class="card-footer">
                <div class="row">
                    <div class="col-md-12 text-end">
                        <button class="btn btn-secondary modal-dismiss">Cancel</button>
                        <button class="btn btn-primary" onclick="submitAssetEditForm()">Update</button>
                    </div>
                </div>
            </footer>
        </section>
    </div>

</div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tabLinks = document.querySelectorAll('.nav-link[data-bs-toggle="tab"]');

            tabLinks.forEach(function (link) {
                link.addEventListener('shown.bs.tab', function (event) {
                    const hash = event.target.getAttribute('href');
                    history.pushState(null, null, hash);
                });
            });

            const hash = window.location.hash;
            if (hash) {
                const triggerEl = document.querySelector(`.nav-link[href="${hash}"]`);
                if (triggerEl) {
                    const tab = new bootstrap.Tab(triggerEl);
                    tab.show();
                }
            }

            const imageInput = document.getElementById('image');
            const imagePreview = document.getElementById('imagePreview');
            const imagePreviewContainer = document.getElementById('imagePreviewContainer');
            const removeImageBtn = document.getElementById('removeImage');
            const removeExistingImageInput = document.getElementById('removeExistingImage');
            
            // Store original image source
            const originalImageSrc = imagePreview.src;

            // Show preview when new file selected
            imageInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                
                if (file) {
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        imagePreview.src = e.target.result;
                        imagePreviewContainer.classList.remove('d-none');
                        removeExistingImageInput.value = '0'; // Reset remove flag
                    }
                    
                    reader.readAsDataURL(file);
                }
            });

            // Remove image
            removeImageBtn.addEventListener('click', function() {
                imageInput.value = '';
                imagePreview.src = '';
                imagePreviewContainer.classList.add('d-none');
                
                // If there was an original image, mark it for removal
                if (originalImageSrc) {
                    removeExistingImageInput.value = '1';
                }
            });
        });
    </script>

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
            
            // Initialize Magnific Popup for all modals
            $('a[href="#modalReferenceForm"]').magnificPopup({
                type: 'inline',
                preloader: false,
                modal: true
            });
            
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

            $('#table-loan').DataTable({
                "processing": true,
                "serverSide": true,
                "fixedHeader": false,
                "ajax": {
                    "url": "{{ route('loan.load_loan',['customer_code'=>$customer->customer_code]) }}",
                    "type": "GET",
                },
                "order": [
                    [2, "desc"]
                ],
                "columns": [
                    {
                        "data": "loan_code"
                    },
                    {
                        "data": "company_code",
                        "render": function(data, type, row, meta) {
                            return `<a style="text-decoration:none" onclick="e.preventDefault()">${row.company_code}<br>${row.company_name}</a>`;
                        }
                    },
                    {
                        "data": "interest_group"
                    },
                    {
                        "data": "interest_rate",
                        "render": function(data, type, row, meta) {
                            return data+"%";
                        }
                    },
                    {
                        "data": "loan_amount",
                    },
                    {
                        "data": "installment",
                        "render": function(data, type, row, meta) {
                            let installment = `${row.installment}`;
                            if(row.interest_group == "SKIM B"){
                                installment = `${row.installment}<br><span style="color:#7c7c7c;font-size:12px">First: ${row.first_payment}</span><br> <span style="color:#7c7c7c;font-size:12px">Last: ${row.last_payment}</span> `;
                            }
                            return `<a style="text-decoration:none" onclick="e.preventDefault()">${installment}</a>`;
                        }
                    },
                    {
                        "data": "loan_term",
                        "render": function(data, type, row, meta) {
                            return row.interest_group == 'SKIM B' ? row.loan_term : '-';
                        }
                    },
                    {
                        "data": "capital"
                    },
                    {
                        "data": "created_at",
                        "render": function(data, type, row, meta) {
                            return formatDate(data);
                        }
                    },
                    {
                        "data": null,
                        "render": function(data, type, row, meta) {
                            
                            let url = `
                                <div class="cus-action-wrapper">
                                    <a href="{{ route('loan.single_loan', ['loan_code' => ':loan_code']) }}" target="_blank" class="cus-action-icon info" title="View Detail"><i class="fas fa-eye"></i></a>
                                    <a href="{{ route('payment.create', ['loan_code' => ':loan_code']) }}" target="_blank" class="cus-action-icon info" title="Create Payment"><i class="fas fa-money-check-alt"></i></a>
                                    <a href="{{ route('schedule.create', ['loan_code' => ':loan_code']) }}" target="_blank" class="cus-action-icon info" title="Create Schedule"><i class="fas fa-calendar-alt"></i></a>
                                </div>
                                `;
                            url = url.replaceAll(':loan_code', row.loan_code);
                            return url;
                        }
                    }
                ]
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
            form.submit();
        }

        // Reference Functions
        function editReference(id) {
            $.magnificPopup.close();
            
            $.get(`{{ url('customer/reference') }}/${id}/edit`, function(response) {
                // Populate edit form with data
                $('#edit_reference_id').val(response.reference.id);
                $('#edit_reference_type').val(response.reference.reference_type);
                $('#edit_new_ic').val(response.reference.new_ic);
                $('#edit_name').val(response.reference.name);
                $('#edit_gender').val(response.reference.gender);
                $('#edit_race').val(response.reference.race);
                $('#edit_date_of_birth').val(response.reference.date_of_birth);
                $('#edit_mobile').val(response.reference.mobile);
                $('#edit_telephone').val(response.reference.telephone);
                $('#edit_house_ownership').val(response.reference.house_ownership);
                $('#edit_warganegara').val(response.reference.warganegara);
                $('#edit_address1').val(response.reference.address1);
                $('#edit_address2').val(response.reference.address2);
                $('#edit_postcode').val(response.reference.postcode);
                $('#edit_city').val(response.reference.city);
                $('#edit_state').val(response.reference.state);
                $('#edit_company_name').val(response.reference.company_name);
                $('#edit_biz_type').val(response.reference.biz_type);
                $('#edit_designation').val(response.reference.designation);
                $('#edit_monthly_income').val(response.reference.monthly_income);
                $('#edit_company_address1').val(response.reference.company_address1);
                $('#edit_company_address2').val(response.reference.company_address2);
                $('#edit_company_postcode').val(response.reference.company_postcode);
                $('#edit_company_city').val(response.reference.company_city);
                $('#edit_company_state').val(response.reference.company_state);
                
                // Set form action
                $('#referenceEditForm').attr('action', `{{ url('customer/reference') }}/${id}`);
                
                // Open edit modal
                $.magnificPopup.open({
                    items: {
                        src: '#modalReferenceEditForm'
                    },
                    type: 'inline',
                    preloader: false,
                    modal: true
                });
            }).fail(function() {
                alert('Error loading reference data');
            });
        }

        function submitReferenceEditForm() {
            const form = document.getElementById('referenceEditForm');
            if (form.checkValidity()) {
                form.submit();
            } else {
                alert("Please fill all required fields");
            }
        }

        // Asset Functions
        function submitAssetForm() {
            // Get the form data
            const form = document.getElementById('assetForm');
            const formData = new FormData(form);

            if (form.checkValidity()) {
                form.submit();
            } else {
                alert("Please fill all required fields in the asset form");
            }
        }

        function editAsset(id) {
            $.magnificPopup.close();
            
            $.get(`{{ url('customer/asset') }}/${id}/edit`, function(response) {
                // Populate edit form with data
                $('#edit_asset_id').val(response.asset.id);
                $('#edit_item').val(response.asset.item);
                $('#edit_remark').val(response.asset.remark);
                
                // Set form action
                $('#assetEditForm').attr('action', `{{ url('customer/asset') }}/${id}`);
                
                // Open edit modal
                $.magnificPopup.open({
                    items: {
                        src: '#modalAssetEditForm'
                    },
                    type: 'inline',
                    preloader: false,
                    modal: true
                });
            }).fail(function() {
                alert('Error loading asset data');
            });
        }

        function submitAssetEditForm() {
            const form = document.getElementById('assetEditForm');
            if (form.checkValidity()) {
                form.submit();
            } else {
                alert("Please fill all required fields");
            }
        }

        function displayRemark(remark) {
            // Convert line breaks to HTML <br> tags for display
            return remark.replace(/\n/g, '<br>');
        }

        document.getElementById('alternativeIdBtn').addEventListener('click', function() {
            alert('Alternative ID clicked!');
            // You can replace this with modal popup or logic to choose an alternative ID
        });
    </script>

    <script src="js/examples/examples.modals.js"></script>
@endsection