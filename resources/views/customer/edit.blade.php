@extends('layouts.app')
<style>
    #table-loan tbody tr.row-fully-paid td {
        background-color: rgba(255, 0, 0, 0.1) !important;
    }
    #table-loan tbody tr.row-overdue td {
        background-color: rgba(255, 253, 112, 0.35) !important;
    }
    #table-loan tbody tr.row-active td {
        background-color: rgba(0, 200, 0, 0.1) !important;
    }

    #datatable-asset thead th {
        position: relative;
        min-width: 50px;
        user-select: none;
    }

    #datatable-asset .col-resize-handle {
        position: absolute;
        right: 0;
        top: 0;
        bottom: 0;
        width: 8px;
        cursor: col-resize;
        z-index: 9999;
        background: transparent;
    }

    #datatable-asset .col-resize-handle:hover {
        background: rgba(0, 136, 204, 0.4);
    }

    #table-loan thead th {
        position: relative;
        min-width: 50px;
        user-select: none;
    }

    #table-loan .col-resize-handle {
        position: absolute;
        right: 0;
        top: 0;
        bottom: 0;
        width: 8px;
        cursor: col-resize;
        z-index: 99999; 
        background: transparent;
        pointer-events: auto !important;
    }

    #table-loan .col-resize-handle:hover {
        background: rgba(0, 136, 204, 0.4);
    }

    #datatable-reference thead th {
        position: relative;
        min-width: 50px;
        user-select: none; /* Prevent text selection while dragging */
    }
    #datatable-reference .col-resize-handle {
        position: absolute;
        right: 0;
        top: 0;
        bottom: 0;
        width: 8px;
        cursor: col-resize;
        z-index: 9999;
        background: transparent;
    }
    #datatable-reference .col-resize-handle:hover {
        background: rgba(0, 136, 204, 0.4);
    }

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

    #table-loan th:nth-child(2)  { width: 10px;  min-width: 10px;  } /* Comp */
    #table-loan th:nth-child(12) { width: 10px;  min-width: 10px;  } /* LT */
    #table-loan th:nth-child(13) { width: 10px;  min-width: 10px;  } /* Install */
    #table-loan th:nth-child(14) { width: 10px;  min-width: 10px;  } /* Rate */
    #table-loan th:nth-child(15) { width: 10px;  min-width: 10px;  } /* Int */
    #table-loan th:nth-child(16) { width: 10px;  min-width: 10px;  } /* Late */
    #table-loan th:nth-child(17) { width: 10px;  min-width: 10px;  } /* Status */
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
                    <a class="nav-link active" data-bs-target="#personal" href="#personal" data-bs-toggle="tab">{{ __('table.personal') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-target="#work" href="#work" data-bs-toggle="tab">{{ __('table.work') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-target="#document" href="#document" data-bs-toggle="tab">{{ __('table.document') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-target="#reference" href="#reference" data-bs-toggle="tab">{{ __('table.reference') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-target="#asset" href="#asset" data-bs-toggle="tab">{{ __('table.asset') }}</a>
                </li>
                 <li class="nav-item">
                    <a class="nav-link" data-bs-target="#loan" href="#loan" data-bs-toggle="tab">{{ __('table.loan') }}</a>
                </li>
                 <!-- <li class="nav-item">
                    <a class="nav-link" data-bs-target="#payment" href="#payment" data-bs-toggle="tab">Payment</a>
                </li> -->
            </ul>
            <div class="tab-content">
                <div id="personal" class="tab-pane active">
                    @php
                        $isLocked = auth()->user()->role_id == 4;
                    @endphp
                    <form class="p-3" method="POST" action="{{ route('customer.update', $customer->id) }}" enctype="multipart/form-data" onsubmit="return showLoading();">
                        @csrf
                        @method('PUT')
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
                                            <input type="file" class="form-control" id="profileImage" name="new_profile_image" accept="image/*" onchange="previewPhoto(event)">
                                        </div>
                                    </div>
                                </section>
                                <ul class="simple-card-list mb-3 d-none d-lg-block">
                                    <li class="primary">
                                        <h3>{{ $total_loan_count }}</h3>
                                        <p class="text-light">{{ __('table.total_loan') }}</p>
                                    </li>
                                    <li class="primary">
                                        <h3>$ {{ number_format($total_loan_amount, 2) }}</h3>
                                        <p class="text-light">{{ __('table.total_loan_amount') }}</p>
                                    </li>
                                    <li class="primary">
                                        <h3>$ {{ number_format($total_outstanding, 2) }}</h3>
                                        <p class="text-light">{{ __('table.total_outstanding') }}</p>
                                    </li>
                                </ul>
                            </div>

                            <div class="col-lg-9 col-md-8 col-sm-12">
                                <div class="row mb-2">
                                    <div class="form-group col-md-6">
                                        <label>{{ __('table.system_code') }}</label>
                                        <input type="text" class="form-control" value="{{ $customer->customer_code }}" readonly disabled>
                                    </div>
                                    <div class="form-group col-md-6 border-top-0 pt-0">
                                        <label for="company_code">{{ __('table.company_code') }} <span class="text-danger">*</span></label>
                                        <select id="company_code" name="company_code" class="form-control" required {{ $isLocked ? 'disabled' : '' }}>
                                            <option value="">{{ __('table.choose') }}...</option>
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
                                        <label for="customer_name">{{ __('table.customer_name') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="customer_name" name="customer_name" placeholder="Customer Name" value="{{ $customer->customer_name }}" required {{ $isLocked ? 'readonly disabled' : '' }}>
                                    </div>
                                    <div class="form-group col-md-6 border-top-0 pt-0">
                                        <label for="nric_number">NRIC {{ __('table.number') }} <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="nric_number" name="nric_number" value="{{ $customer->nric_number }}" required {{ $isLocked ? 'readonly disabled' : '' }}>
                                            <button class="btn btn-outline-secondary" type="button" id="uploadIcBtn">{{ __('table.upload_ic') }}</button>
                                        </div>
                                        
                                        <!-- Hidden file input - now accepts images and PDFs -->
                                        <input type="file" class="d-none" id="nric_image" name="new_nric_image" accept="image/*,.pdf">

                                        @if($customer->nric_path)
                                            <div class="mt-1">
                                                <a href="{{ asset('storage/' . $customer->nric_path) }}" class="text-primary">
                                                    <i class="fas fa-eye"></i> {{ __('table.view_ic') }}
                                                </a>
                                            </div>
                                        @endif
                                        
                                        <!-- Show selected file name -->
                                        <div id="selectedFileName" class="mt-1 text-muted small" style="display: none;"></div>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="form-group col-md-4 border-top-0 pt-0">
                                        <label for="gender">{{ __('table.gender') }} <span class="text-danger">*</span></label>
                                        <select id="gender" name="gender" class="form-control" required {{ $isLocked ? 'disabled' : '' }}>
                                            <option value="">{{ __('table.choose') }}...</option>
                                            <option value="Male" {{ $customer->gender == 'Male' ? 'selected' : '' }}>Male</option>
                                            <option value="Female" {{ $customer->gender == 'Female' ? 'selected' : '' }}>Female</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4 border-top-0 pt-0">
                                        <label for="race">{{ __('table.race') }} <span class="text-danger">*</span></label>
                                        <select id="race" name="race" class="form-control" required {{ $isLocked ? 'disabled' : '' }}>
                                            <option value="">{{ __('table.choose') }}...</option>
                                            @foreach($races as $raceItem)
                                                <option value="{{ $raceItem->race_name }}" {{ $customer->race == $raceItem->race_name ? 'selected' : '' }}>{{ $raceItem->race_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4 border-top-0 pt-0">
                                        <label for="date_of_birth">{{ __('table.date_of_birth') }} <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-calendar-alt"></i>
                                            </span>
                                            <input type="date" name="date_of_birth" class="form-control" value="{{ $customer->date_of_birth }}" required {{ $isLocked ? 'readonly disabled' : '' }}>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="form-group col-md-6 border-top-0 pt-0">
                                        <label for="email">{{ __('table.email') }}</label>
                                        <input type="email" class="form-control" id="email" name="email" autocomplete="off" value="{{ $customer->email }}" readonly onfocus="this.removeAttribute('readonly');">
                                    </div>
                                    <div class="form-group col-md-6 border-top-0 pt-0">
                                        <label for="mobile">{{ __('table.mobile') }}</label>
                                        <input type="text" class="form-control" id="mobile" name="mobile" value="{{ $customer->mobile }}" required>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="form-group col">
                                        <label for="address">{{ __('table.address') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="address1" name="address1" value="{{ $customer->address1 }}">
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="form-group col">
                                        <input type="text" class="form-control" id="address2" name="address2" value="{{ $customer->address2 }}">
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="form-group col-md-4 border-top-0 pt-0">
                                        <label for="postcode">{{ __('table.postcode') }} <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="postcode" name="postcode" value="{{ $customer->postcode }}" required>
                                    </div>
                                    <div class="form-group col-md-4 border-top-0 pt-0">
                                        <label for="city">{{ __('table.city') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="city" name="city" value="{{ $customer->city }}" required>
                                    </div>
                                    <div class="form-group col-md-4 border-top-0 pt-0">
                                        <label for="state">{{ __('table.state') }} <span class="text-danger">*</span></label>
                                        <select id="state" name="state" class="form-control" required>
                                            <option value="">{{ __('table.choose') }}...</option>
                                            @foreach($states as $state)
                                            <option value="{{ $state->state_name }}" {{ $customer->state == $state->state_name ? 'selected' : '' }}>{{ $state->state_name }} </option>
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
                                                    {{ strtolower($customer->house_ownership ?? '') == strtolower($houseOwnership->house_ownership) ? 'selected' : '' }}>
                                                    {{ $houseOwnership->house_ownership }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4 border-top-0 pt-0">
                                        <label for="warga_negara">{{ __('table.warganegara') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="warganegara" name="warganegara" value="{{ $customer->warganegara }}" required>
                                    </div>
                                    <div class="form-group col-md-4 border-top-0 pt-0">
                                        <label for="marital_status">{{ __('table.marital_status') }} <span class="text-danger">*</span></label>
                                        <select id="marital_status" name="marital_status" class="form-control" required>
                                            <option value="">{{ __('table.choose') }}...</option>
                                            @foreach($marital_statues as $marital_status)
                                                <option value="{{ $marital_status->marital_status }}" {{ $customer->marital_status == $marital_status->marital_status ? 'selected' : '' }}>{{ $marital_status->marital_status }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="form-group col">
                                        <label for="remark">{{ __('table.remark') }}</label>
                                        <textarea class="form-control" id="remark" name="remark" rows="6">{{ $customer->remark ?? '' }}</textarea>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="form-group col-md-4 border-top-0 pt-0">
                                        <label for="status">Status</label>
                                        <select id="status" name="status" class="form-control" required onchange="updateStatusColor(this)"
                                            style="color: {{ $customer->status === 'active' ? 'green' : ($customer->status === 'overdue' ? '#7a6800' : ($customer->status === 'fully_paid' ? 'red' : 'orange')) }}">
                                            <option value="active" style="color: green;" {{ $customer->status=='active'?'selected':'' }}>Active</option>
                                            <option value="overdue" style="color: #7a6800;" {{ $customer->status=='overdue'?'selected':'' }}>Overdue</option>
                                            <option value="bad_debt" style="color: orange;" {{ $customer->status=='bad_debt'?'selected':'' }}>Bad Debt</option>
                                            <option value="blacklist" style="color: orange;" {{ $customer->status=='blacklist'?'selected':'' }}>Blacklist</option>
                                            <option value="fully_paid" style="color: red;" {{ $customer->status=='fully_paid'?'selected':'' }}>Fully Paid</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 text-end mt-3">
                                <button type="submit" class="btn btn-primary">{{ __('table.update_personal_info') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
                
                <!-- WORK TAB -->
                <div id="work" class="tab-pane">
                    <form class="p-3" method="POST" action="{{ route('customer.work.store', $customer->id) }}" onsubmit="return showLoading();">
                        @csrf
                        @method('PUT')
                        
                        <h4 class="mb-3 font-weight-semibold text-dark">{{ __('table.work_information') }}</h4>
                        <div class="row mb-2">
                            <div class="form-group col-md-6">
                                <label>{{ __('table.company_name') }}<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="company_name" placeholder="Company Name" value="{{ $customer->company_name }}" required>
                            </div>
                            <div class="form-group col-md-6 border-top-0 pt-0">
                                <label>{{ __('table.monthly_income') }}<span class="text-danger">*</span></label>
                                <input type="double" class="form-control" name="monthly_income" placeholder="Monthly Income" value="{{ $customer->monthly_income }}" required>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="form-group col-md-6">
                                <label>{{ __('table.designation') }}<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="designation" placeholder="Designation..." value="{{ $customer->designation }}" required>
                            </div>
                            <div class="form-group col-md-6 border-top-0 pt-0">
                                <label>{{ __('table.employer_type') }}<span class="text-danger">*</span></label>
                                <select name="employer" class="form-control" required>
                                    <option value="">{{ __('table.choose') }}...</option>
                                    @foreach($employer_types as $employer_type)
                                        <option value="{{ $employer_type->employer_type }}" {{ $customer->employer == $employer_type->employer_type ? 'selected' : '' }}>
                                            {{ $employer_type->employer_type }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="form-group col">
                                <label for="company_address">{{ __('table.company_address') }}<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="company_address1" placeholder="Company Address" value="{{ $customer->company_address1 }}" required>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="form-group col-md-4 border-top-0 pt-0">
                                <label>{{ __('table.company_postcode') }}<span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="company_postcode" value="{{ $customer->company_postcode }}" required>
                            </div>
                            <div class="form-group col-md-4 border-top-0 pt-0">
                                <label>{{ __('table.company_city') }}<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="company_city" value="{{ $customer->company_city }}" required>
                            </div>
                            <div class="form-group col-md-4 border-top-0 pt-0">
                                <label>{{ __('table.company_state') }}<span class="text-danger">*</span></label>
                                <select name="company_state" class="form-control" required>
                                    <option value="">{{ __('table.choose') }}...</option>
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
                                <label>{{ __('table.salary_date') }}</label>
                                <input type="string" name="salary_date" class="form-control" value="{{ $customer->salary_date }}">
                            </div>
                            <div class="form-group col-md-4 border-top-0 pt-0">
                                <label>{{ __('table.start_working_date') }}<span class="text-danger">*</span></label>
                                <input type="date" name="start_working_date" class="form-control" value="{{ $customer->start_working_date }}" required>
                            </div>
                           
                        </div>
                        <div class="row mb-2">
                            <div class="form-group col">
                                <label for="remark">{{ __('table.remark') }}</label>
                                <textarea class="form-control" id="work_remark" name="work_remark" rows="6" placeholder="Enter remarks here..." >{{ $customer->work_remark ?? ''}}</textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 text-end mt-3">
                                <button type="submit" class="btn btn-primary">{{ __('table.save_work_info') }}</button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- DOCUMENTS TAB -->
                <div id="document" class="tab-pane">
                    <div class="p-3">

                        <!-- Header -->
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="mb-0 font-weight-semibold text-dark">
                                <i class="bx bx-file me-1"></i> {{ __('table.document') }}
                            </h4>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalUploadDocument">
                                <i class="bx bx-upload me-1"></i> {{ __('table.upload_file') }}
                            </button>
                        </div>

                        <!-- Documents Grid -->
                        <div id="documents_grid" class="row g-3">
                            <div class="col-12 text-center text-muted py-5" id="doc_empty_state">
                                <i class="bx bx-loader-alt bx-spin fs-1 d-block mb-2"></i>
                                {{ __('table.loading_document') }}...
                            </div>
                        </div>

                    </div>
                </div>

                <!-- ===== UPLOAD MODAL ===== -->
                <div class="modal fade" id="modalUploadDocument" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">
                                    <i class="bx bx-upload me-1"></i> {{ __('table.upload_document') }}
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">{{ __('table.file') }} <span class="text-danger">*</span></label>
                                    <input type="file" id="doc_file" class="form-control"
                                        accept=".jpg,.jpeg,.png,.gif,.webp,.pdf">
                                    <small class="text-muted">{{ __('table.allowed') }}: PDF, JPG, PNG, GIF, WEBP — Max 10MB</small>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">{{ __('table.remark') }}</label>
                                    <input type="text" id="doc_remark" class="form-control"
                                        placeholder="Optional remark...">
                                </div>
                                <!-- Progress -->
                                <div id="upload_progress" style="display:none;">
                                    <div class="progress mb-1">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated w-100"></div>
                                    </div>
                                    <small class="text-muted">{{ __('table.uploading,_please_wait') }}...</small>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('table.cancel') }}</button>
                                <button type="button" class="btn btn-primary" id="btn_upload" onclick="uploadDocument()">
                                    <i class="bx bx-upload me-1"></i> {{ __('table.upload') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ===== IMAGE LIGHTBOX MODAL ===== -->
                <div class="modal fade" id="imageLightbox" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-xl modal-dialog-centered">
                        <div class="modal-content bg-dark">
                            <div class="modal-header border-0">
                                <span class="text-white fw-semibold" id="lightbox_title"></span>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body text-center position-relative px-5 py-3">
                                <button class="btn btn-outline-light position-absolute top-50 start-0 translate-middle-y ms-2"
                                        style="z-index:10" onclick="lightboxNav(-1)">
                                    <i class="bx bx-chevron-left fs-3"></i>
                                </button>
                                <img id="lightbox_img"
                                    src=""
                                    class="img-fluid rounded"
                                    style="max-height:75vh; object-fit:contain; cursor:zoom-in; transition: transform 0.15s ease;"
                                    draggable="false"
                                    ondragstart="return false"
                                    onselectstart="return false"
                                    oncontextmenu="return false"
                                    ondblclick="lightboxToggleZoom(this)">
                                <button class="btn btn-outline-light position-absolute top-50 end-0 translate-middle-y me-2"
                                        style="z-index:10" onclick="lightboxNav(1)">
                                    <i class="bx bx-chevron-right fs-3"></i>
                                </button>
                            </div>
                            <div class="modal-footer border-0 justify-content-center">
                                <span class="text-white-50 me-3" id="lightbox_counter"></span>
                                <a id="lightbox_download" href="#" class="btn btn-sm btn-outline-light">
                                    <i class="bx bx-download me-1"></i> {{ __('table.download') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- REFERENCE TAB -->
                <div id="reference" class="tab-pane">
                    <div class="col-lg-12 mb-3">
                        <section class="card">
                            <div class="card-header" style="text-align: right;">
                                <a class="btn btn-xs btn-square btn-primary" href="#modalReferenceForm">{{ __('table.add_reference') }}</a>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered table-striped mb-0" id="datatable-reference">
                                    <thead>
                                        <tr>
                                            <th>{{ __('table.reference_type') }}</th>
                                            <th>NRIC</th>
                                            <th>{{ __('table.name') }}</th>
                                            <th>{{ __('table.mobile') }}</th>
                                            <th>{{ __('table.houseownership') }}</th>
                                            <th>{{ __('table.monthly_income') }}</th>
                                            <th>{{ __('table.city') }}</th>
                                            <th>{{ __('table.state') }}</th>
                                            <th>{{ __('table.designation') }}</th>
                                            <th>{{ __('table.company_name') }}</th>
                                            <th>{{ __('table.actions') }}</th>
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
                                                    @if(Auth::user()->role_id != 4)
                                                    <a onclick="if(confirm('Are you sure you want to delete?')){window.location.href='{{ route('customer.reference.destroy', $reference->id) }}'}" title="Delete" class="btn-delete" style="cursor:pointer">
                                                        <i class="bx bx-trash"></i>
                                                    </a>
                                                    @endif
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
                                <a class="btn btn-xs btn-square btn-primary" href="#modalAssetForm">{{ __('table.add_asset') }}</a>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered table-striped mb-0" id="datatable-asset">
                                    <thead>
                                        <tr>
                                            <th>{{ __('table.item') }}</th>
                                            <th>{{ __('table.remark') }}</th>
                                            <th>{{ __('table.actions') }}</th>
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

                            <div class="row" style="padding-top:10px; margin-bottom:10px;">

                                {{-- TOTAL LOAN AMOUNT --}}
                                <div class="col-xl-3">
                                    <section class="card card-featured-left card-featured-primary mb-0" style="background-color:#cce8f4; border-radius:6px;">
                                        <div class="card-body" style="background-color:transparent;">
                                            <div class="widget-summary">
                                                <div class="widget-summary-col" style="vertical-align: middle">
                                                    <div class="summary" style="min-height:1px">
                                                        <h4 class="title" style="margin-bottom:5px">
                                                            {{ __('table.total_loan_amount') }}
                                                        </h4>
                                                        <div class="info">
                                                            <strong class="amount">
                                                                RM <span style="font-size:1.4rem" id="total-loan-amount">0.00</span>
                                                            </strong>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </section>
                                </div>

                                {{-- TOTAL Outstanding --}}
                                <div class="col-xl-3">
                                    <section class="card card-featured-left card-featured-primary mb-0" style="background-color:#cce8f4; border-radius:6px;">
                                        <div class="card-body" style="background-color:transparent;">
                                            <div class="widget-summary">
                                                <div class="widget-summary-col" style="vertical-align: middle">
                                                    <div class="summary" style="min-height:1px">
                                                        <h4 class="title" style="margin-bottom:5px">
                                                            {{ __('table.total_outstanding') }}
                                                        </h4>
                                                        <div class="info">
                                                            <strong class="amount">
                                                                RM <span style="font-size:1.4rem" id="total-outstanding">0.00</span>
                                                            </strong>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </section>
                                </div>

                                {{-- TOTAL BALANCE --}}
                                <div class="col-xl-3">
                                    <section class="card card-featured-left card-featured-primary mb-0" style="background-color:#cce8f4; border-radius:6px;">
                                        <div class="card-body" style="background-color:transparent;">
                                            <div class="widget-summary">
                                                <div class="widget-summary-col" style="vertical-align: middle">
                                                    <div class="summary" style="min-height:1px">
                                                        <h4 class="title" style="margin-bottom:5px">
                                                            {{ __('table.total_balance') }}
                                                        </h4>
                                                        <div class="info">
                                                            <strong class="amount">
                                                                RM <span style="font-size:1.4rem" id="total-balance">0.00</span>
                                                            </strong>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </section>
                                </div>
                                
                                {{-- TOTAL PROFIT --}}
                                <div class="col-xl-3">
                                    <section class="card card-featured-left card-featured-primary mb-0">
                                        <div class="card-body">
                                            <div class="widget-summary">
                                                <div class="widget-summary-col" style="vertical-align: middle">
                                                    <div class="summary" style="min-height:1px">
                                                        <h4 class="title" style="margin-bottom:5px">
                                                            {{ __('table.total_profits') }}
                                                        </h4>
                                                        <div class="info">
                                                            <strong class="amount">
                                                                RM <span style="font-size:1.4rem" id="total-profit">0.00</span>
                                                            </strong>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </section>
                                </div>

                            </div>

                            {{-- ADD LOAN BUTTON --}}
                            <div class="card-header" style="text-align:right;background-color:transparent;">
                                <a class="btn btn-xs btn-square btn-primary"
                                href="{{ route('loan.create',['customer_code'=>$customer->customer_code,'company_code'=>$customer->company_code]) }}">
                                    {{ __('table.add_loan') }}
                                </a>
                            </div>

                            {{-- LOAN TABLE --}}
                            <div class="card-body">
                                <div style="margin-bottom: 10px;">
                                    <label style="cursor:pointer; user-select:none;">
                                        <input type="checkbox" id="hide-fully-paid" checked>
                                        &nbsp;{{ __('table.hide_fully_paid') }}
                                    </label>
                                </div>
                                <table class="table cus-table table-bordered mb-0" id="table-loan">
                                    <thead>
                                        <tr>
                                            <th>{{ __('table.loan_code') }}</th>
                                            <th>{{ __('table.company') }}</th>
                                            <th>{{ __('table.interest_group') }}</th>
                                            <th>{{ __('table.created_date') }}</th>
                                            <th>{{ __('table.payment_due') }}</th>
                                            <th>{{ __('table.last_payment') }}</th>
                                            <th>{{ __('table.loan_amount') }}</th>
                                            <th>{{ __('table.capital') }}</th>
                                            <th>{{ __('table.paid') }}</th>
                                            <th>{{ __('table.outstanding') }}</th>
                                            <th>{{ __('table.balance') }}</th>
                                            <th>{{ __('table.loan_term') }}</th>
                                            <th>{{ __('table.installment') }}</th>
                                            <th>{{ __('table.interest_rate') }}</th>
                                            <th>{{ __('table.int') }}</th>
                                            <th>{{ __('table.late') }}</th>
                                            <th>{{ __('table.status') }}</th>
                                            <th>{{ __('table.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>

                        </section>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Reference Modal Form (Add) -->
    <div id="modalReferenceForm" class="modal-block modal-block-primary modal-block-lg mfp-hide">
        <section class="card">
            <header class="card-header">
                <h2 class="card-title">{{ __('table.reference_form') }}</h2>
            </header>
            <div class="card-body">
                <form method="POST" action="{{ route('customer.reference.store') }}" id="referenceForm" onsubmit="return showLoading();">
                    @csrf
                    <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>{{ __('table.reference_type') }} <span class="text-danger">*</span></label>
                            <select name="reference_type" class="form-control" required>
                            <option value="" disabled selected>{{ __('table.choose') }}...</option>
                            @foreach($reference_types as $type)
                                <option value="{{ $type->reference_type }}">{{ $type->reference_type }}</option>
                            @endforeach
                        </select>
                        </div>
                        <div class="col-md-6">
                            <label>{{ __('table.reference') }} NRIC</label>
                            <input type="text" class="form-control" name="new_ic">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label>{{ __('table.reference_name') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" placeholder="Reference name" required>
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="form-group col-md-4 border-top-0 pt-0">
                            <label>{{ __('table.gender') }}</label>
                            <select name="gender" class="form-control">
                                <option value="">{{ __('table.choose') }}...</option>
                                <option value="Male">{{ __('table.male') }}</option>
                                <option value="Female">{{ __('table.female') }}</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4 border-top-0 pt-0">
                            <label>{{ __('table.race') }}</label>
                            <select name="race" class="form-control">
                                <option value="">{{ __('table.choose') }}...</option>
                                @foreach($races as $race)
                                    <option value="{{ $race->race_name }}">{{ $race->race_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-4 border-top-0 pt-0">
                            <label>{{ __('table.date_of_birth') }}</label>
                            <input type="date" name="date_of_birth" class="form-control">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label>{{ __('table.mobile') }} / {{ __('table.telephone') }}</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <select class="form-control" name="phone_type" style="border-radius: 4px 0 0 4px;">
                                        <option value="mobile">Mobile</option>
                                        <option value="telephone">Telephone</option>
                                    </select>
                                </div>
                                <input type="number" class="form-control" name="phone_number" placeholder="Enter number">
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>{{ __('table.house_ownership') }}</label>
                            <select id="house_ownership" name="house_ownership" class="form-control">
                                <option value="">{{ __('table.choose') }}...</option>
                                @foreach($house_ownership as $houseOwnership)
                                    <option value="{{ $houseOwnership->house_ownership }}">
                                        {{ $houseOwnership->house_ownership }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>{{ __('table.warganegara') }}</label>
                            <input type="text" class="form-control" name="warganegara">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label>{{ __('table.address_1') }}</label>
                            <input type="text" class="form-control" name="address1" placeholder="Address 1">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label>{{ __('table.address_2') }}</label>
                            <input type="text" class="form-control" name="address2" placeholder="Address 2">
                        </div>
                    </div>
                    
                    <div class="row mb-2">
                        <div class="form-group col-md-4 border-top-0 pt-0">
                            <label>{{ __('table.postcode') }}</label>
                            <input type="number" class="form-control" name="postcode">
                        </div>
                        <div class="form-group col-md-4 border-top-0 pt-0">
                            <label>{{ __('table.city') }}</label>
                            <input type="text" class="form-control" name="city">
                        </div>
                        <div class="form-group col-md-4 border-top-0 pt-0">
                            <label>{{ __('table.state') }}</label>
                            <select name="state" class="form-control">
                                <option value="">{{ __('table.choose') }}...</option>
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
                            <label>{{ __('table.company_name') }}</label>
                            <input type="text" class="form-control" name="company_name" placeholder="Company Name">
                        </div>
                        <div class="form-group col-md-6 border-top-0 pt-0">
                            <label>{{ __('table.business_type') }}</label>
                            <input type="text" class="form-control" name="biz_type" placeholder="Business Type">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="form-group col-md-6">
                            <label>{{ __('table.designation') }}</label>
                            <input type="text" class="form-control" name="designation" placeholder="Designation...">
                        </div>
                        <div class="form-group col-md-6 border-top-0 pt-0">
                            <label>{{ __('table.monthly_income') }}</label>
                            <input type="double" class="form-control" name="monthly_income" placeholder="Monthly Income">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label>{{ __('table.company_address_1') }}</label>
                            <input type="text" class="form-control" name="company_address1" placeholder="Company Address 1">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label>{{ __('table.company_address_2') }}</label>
                            <input type="text" class="form-control" name="company_address2" placeholder="Company Address 2">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="form-group col-md-4 border-top-0 pt-0">
                            <label>{{ __('table.company_postcode') }}</label>
                            <input type="number" class="form-control" name="company_postcode">
                        </div>
                        <div class="form-group col-md-4 border-top-0 pt-0">
                            <label>{{ __('table.company_city') }}</label>
                            <input type="text" class="form-control" name="company_city">
                        </div>
                        <div class="form-group col-md-4 border-top-0 pt-0">
                            <label>{{ __('table.company_state') }}</label>
                            <select name="company_state" class="form-control">
                                <option value="">{{ __('table.choose') }}...</option>
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
                        <button class="btn btn-secondary modal-dismiss">{{ __('table.cancel') }}</button>
                        <button class="btn btn-primary modal-confirm" onclick="submitReferenceForm()">{{ __('table.submit') }}</button>
                    </div>
                </div>
            </footer>
        </section>
    </div>

    <!-- Reference Edit Modal -->
    <div id="modalReferenceEditForm" class="modal-block modal-block-primary modal-block-lg mfp-hide">
        <section class="card">
            <header class="card-header">
                <h2 class="card-title">{{ __('table.edit_reference') }}</h2>
            </header>
            <div class="card-body">
                <form method="POST" id="referenceEditForm" onsubmit="return showLoading();">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                    <input type="hidden" id="edit_reference_id" name="reference_id">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>{{ __('table.reference_type') }} <span class="text-danger">*</span></label>
                            <select name="reference_type" id="edit_reference_type" class="form-control" required>
                                <option value="" disabled>{{ __('table.choose') }}...</option>
                                @foreach($reference_types as $type)
                                    <option value="{{ $type->reference_type }}">{{ $type->reference_type }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>{{ __('table.reference') }} NRIC</label>
                            <input type="text" class="form-control" id="edit_new_ic" name="new_ic">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label>{{ __('table.reference_name') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_name" name="name" placeholder="Reference name" required>
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="form-group col-md-4 border-top-0 pt-0">
                            <label>{{ __('table.gender') }}</label>
                            <select name="gender" id="edit_gender" class="form-control">
                                <option value="">{{ __('table.choose') }}...</option>
                                <option value="Male">{{ __('table.male') }}</option>
                                <option value="Female">{{ __('table.female') }}</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4 border-top-0 pt-0">
                            <label>{{ __('table.race') }}</label>
                            <select name="race" id="edit_race" class="form-control">
                                <option value="">{{ __('table.choose') }}...</option>
                                @foreach($races as $race)
                                    <option value="{{ $race->race_name }}">{{ $race->race_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-4 border-top-0 pt-0">
                            <label>{{ __('table.date_of_birth') }}</label>
                            <input type="date" name="date_of_birth" id="edit_date_of_birth" class="form-control">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label>{{ __('table.mobile') }} / {{ __('table.telephone') }}</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <select class="form-control" name="phone_type" style="border-radius: 4px 0 0 4px;">
                                        <option value="mobile">Mobile</option>
                                        <option value="telephone">Telephone</option>
                                    </select>
                                </div>
                                <input type="number" class="form-control" name="phone_number" placeholder="Enter number">
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>{{ __('table.house_ownership') }}</label>
                            <select name="house_ownership" id="edit_house_ownership" class="form-control">
                                <option value="">{{ __('table.choose') }}...</option>
                                @foreach($house_ownership as $houseOwnership)
                                    <option value="{{ $houseOwnership->house_ownership }}">
                                        {{ $houseOwnership->house_ownership }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>{{ __('table.warganegara') }}</label>
                            <input type="text" class="form-control" id="edit_warganegara" name="warganegara">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label>{{ __('table.address_1') }}</label>
                            <input type="text" class="form-control" id="edit_address1" name="address1" placeholder="Address 1">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label>{{ __('table.address_2') }}</label>
                            <input type="text" class="form-control" id="edit_address2" name="address2" placeholder="Address 2">
                        </div>
                    </div>
                    
                    <div class="row mb-2">
                        <div class="form-group col-md-4 border-top-0 pt-0">
                            <label>{{ __('table.postcode') }}</label>
                            <input type="number" class="form-control" id="edit_postcode" name="postcode">
                        </div>
                        <div class="form-group col-md-4 border-top-0 pt-0">
                            <label>{{ __('table.city') }}</label>
                            <input type="text" class="form-control" id="edit_city" name="city">
                        </div>
                        <div class="form-group col-md-4 border-top-0 pt-0">
                            <label>{{ __('table.state') }}</label>
                            <select name="state" id="edit_state" class="form-control">
                                <option value="">{{ __('table.choose') }}...</option>
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
                            <label>{{ __('table.company_name') }}</label>
                            <input type="text" class="form-control" id="edit_company_name" name="company_name" placeholder="Company Name">
                        </div>
                        <div class="form-group col-md-6 border-top-0 pt-0">
                            <label>{{ __('table.business_type') }}</label>
                            <input type="text" class="form-control" id="edit_biz_type" name="biz_type" placeholder="Business Type">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="form-group col-md-6">
                            <label>{{ __('table.designation') }}</label>
                            <input type="text" class="form-control" id="edit_designation" name="designation" placeholder="Designation...">
                        </div>
                        <div class="form-group col-md-6 border-top-0 pt-0">
                            <label>{{ __('table.monthly_income') }}</label>
                            <input type="double" class="form-control" id="edit_monthly_income" name="monthly_income" placeholder="Monthly Income">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label>{{ __('table.company_address_1') }}</label>
                            <input type="text" class="form-control" id="edit_company_address1" name="company_address1" placeholder="Company Address 1">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label>{{ __('table.company_address_2') }}</label>
                            <input type="text" class="form-control" id="edit_company_address2" name="company_address2" placeholder="Company Address 2">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="form-group col-md-4 border-top-0 pt-0">
                            <label>{{ __('table.company_postcode') }}</label>
                            <input type="number" class="form-control" id="edit_company_postcode" name="company_postcode">
                        </div>
                        <div class="form-group col-md-4 border-top-0 pt-0">
                            <label>{{ __('table.company_postcode') }}</label>
                            <input type="text" class="form-control" id="edit_company_city" name="company_city">
                        </div>
                        <div class="form-group col-md-4 border-top-0 pt-0">
                            <label>{{ __('table.company_state') }}</label>
                            <select name="company_state" id="edit_company_state" class="form-control">
                                <option value="">{{ __('table.choose') }}...</option>
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
                        <button class="btn btn-secondary modal-dismiss">{{ __('table.cancel') }}</button>
                        <button class="btn btn-primary" onclick="submitReferenceEditForm()">{{ __('table.update') }}</button>
                    </div>
                </div>
            </footer>
        </section>
    </div>
    
    <!-- Asset Modal Form (Add) -->
    <div id="modalAssetForm" class="modal-block modal-block-primary modal-block-sm mfp-hide">
        <section class="card">
            <header class="card-header">
                <h2 class="card-title">{{ __('table.asset_form') }}</h2>
            </header>
            <div class="card-body">
                <form method="POST" action="{{ route('customer.asset.store') }}" id="assetForm" onsubmit="return showLoading();">
                    @csrf
                    <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label>{{ __('table.item') }}</label>
                            <input type="text" class="form-control" name="item" placeholder="Enter item name" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label>{{ __('table.remark') }}</label>
                            <textarea class="form-control" name="remark" rows="4" placeholder="Enter remarks here..." style="white-space: pre-wrap;"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <footer class="card-footer">
                <div class="row">
                    <div class="col-md-12 text-end">
                        <button class="btn btn-secondary modal-dismiss">{{ __('table.cancel') }}</button>
                        <button class="btn btn-primary modal-confirm" onclick="submitAssetForm()">{{ __('table.submit') }}</button>
                    </div>
                </div>
            </footer>
        </section>
    </div>

    <!-- Asset Edit Modal -->
    <div id="modalAssetEditForm" class="modal-block modal-block-primary modal-block-sm mfp-hide">
        <section class="card">
            <header class="card-header">
                <h2 class="card-title">{{ __('table.edit_asset') }}</h2>
            </header>
            <div class="card-body">
                <form method="POST" id="assetEditForm" onsubmit="return showLoading();">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                    <input type="hidden" id="edit_asset_id" name="asset_id">
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label>{{ __('table.item') }}</label>
                            <input type="text" class="form-control" id="edit_item" name="item" placeholder="Enter item name" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label>{{ __('table.remark') }}</label>
                            <textarea class="form-control" id="edit_remark" name="remark" rows="4" placeholder="Enter remarks here..." style="white-space: pre-wrap;"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <footer class="card-footer">
                <div class="row">
                    <div class="col-md-12 text-end">
                        <button class="btn btn-secondary modal-dismiss">{{ __('table.cancel') }}</button>
                        <button class="btn btn-primary" onclick="submitAssetEditForm()">{{ __('table.update') }}</button>
                    </div>
                </div>
            </footer>
        </section>
    </div>

</div>
@endsection

@section('scripts')
    <script>
        function updateStatusColor(select) {
            const colors = {
                'active': 'green',
                'overdue': '#7a6800',
                'fully_paid': 'red',
                'bad_debt': 'orange',
                'blacklist': 'orange'
            };
            select.style.color = colors[select.value] || '#000000';
        }
        
        document.getElementById('uploadIcBtn').addEventListener('click', function() {
            document.getElementById('nric_image').click();
        });

        document.getElementById('nric_image').addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const fileName = this.files[0].name;
                const fileSize = (this.files[0].size / 1024 / 1024).toFixed(2); // MB
                
                // Show selected file
                const fileNameDiv = document.getElementById('selectedFileName');
                fileNameDiv.textContent = `Selected: ${fileName} (${fileSize} MB)`;
                fileNameDiv.style.display = 'block';
                
                // Validate file size (max 2MB)
                if (this.files[0].size > 2048 * 1024) {
                    alert('File size must not exceed 2MB');
                    this.value = '';
                    fileNameDiv.style.display = 'none';
                    return;
                }
                
                // Validate file type - now includes PDF
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'application/pdf'];
                if (!allowedTypes.includes(this.files[0].type)) {
                    alert('Only JPG, JPEG, PNG, WEBP, and PDF files are allowed');  // Updated message
                    this.value = '';
                    fileNameDiv.style.display = 'none';
                    return;
                }
                
                // Auto-submit the form
                if (confirm('Upload this IC file?')) {
                    this.form.submit();
                } else {
                    this.value = '';
                    fileNameDiv.style.display = 'none';
                }
            }
        });

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
        $('#datatable-reference').DataTable();
        $('#datatable-asset').DataTable();
        
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
        
        $(document).on('click', '.modal-dismiss', function (e) {
            e.preventDefault();
            $.magnificPopup.close();
        });

        let table_loan = $('#table-loan').DataTable({
            "processing": true,
            "serverSide": true,
            "fixedHeader": false,
            "ajax": {
                "url": "{{ route('loan.load_loan',['customer_code'=>$customer->customer_code]) }}",
                "type": "GET",
                "data": function(d) {
                    d.hide_fully_paid = $('#hide-fully-paid').is(':checked') ? 1 : 0;
                },
                "dataSrc": function(json){
                    $('#total-profit').text(parseFloat(json.total_profit).toFixed(2));
                    $('#total-outstanding').text(parseFloat(json.total_outstanding).toFixed(2));
                    $('#total-loan-amount').text(parseFloat(json.total_loan_amount).toFixed(2));
                    $('#total-balance').text(parseFloat(json.total_balance).toFixed(2));
                    return json.data;
                }
            },
            "order": [[2, "desc"]],
            "rowCallback": function(row, data) {
                if (data.deleted_at) {
                    $(row).find('td').css('background-color', 'rgba(192, 57, 43, 0.15)');
                } else if (data.status === 'Fully Paid') {
                    $(row).find('td').css('background-color', 'rgba(255, 0, 0, 0.1)');
                } else if (data.status === 'Overdue') {
                    $(row).find('td').css('background-color', 'rgba(255, 220, 50, 0.25)');
                } else if (data.status === 'Active') {
                    $(row).find('td').css('background-color', 'rgba(0, 180, 0, 0.08)');
                } else {
                    $(row).find('td').css('background-color', '');
                }
            },
            "initComplete": function() {
                makeLoanTableResizable(table_loan);
            },
            "columns": [
                { "data": "loan_code" },
                {
                    "data": "company_code",
                    "render": function(data, type, row, meta) {
                        return `<a style="text-decoration:none" onclick="e.preventDefault()">${row.company_code}<br>${row.company_name}</a>`;
                    }
                },
                { "data": "interest_group" },
                {
                    "data": "year_month",
                    "render": function(data, type, row, meta) {
                        if (!data) return '-';
                        const parts = data.substring(0, 10).split('-');
                        return `${parts[2]}-${parts[1]}-${parts[0]}`;
                    }
                },
                {
                    "data": "next_due_date",
                    "render": function(data, type, row, meta) {
                        if (!data) return '-';
                        const parts = data.substring(0, 10).split('-');
                        return `${parts[2]}-${parts[1]}-${parts[0]}`;
                    }
                },
                {
                    "data": "latest_payment_date",
                    "defaultContent": "-",
                    "render": function(data, type, row, meta) {
                        if (!data) return '-';
                        const parts = data.substring(0, 10).split('-');
                        return `${parts[2]}-${parts[1]}-${parts[0]}`;
                    }
                },
                { "data": "loan_amount" },
                { "data": "capital" },
                {
                    "data": "paid",
                    "render": function(data, type, row, meta) {
                        return `<strong>${data}</strong>`;
                    }
                },
                {
                    "data": "outstanding",
                    "render": function(data, type, row, meta) {
                        return `<strong>${data}</strong>`;
                    }
                },
                {
                    "data": "balance",
                    "render": function(data, type, row, meta) {
                        return `<strong>${data}</strong>`;
                    }
                },
                {
                    "data": "loan_term",
                    "render": function(data, type, row, meta) {
                        return row.interest_group == 'SKIM B' ? row.loan_term : '-';
                    }
                },
                {
                    "data": "installment",
                    "render": function(data, type, row, meta) {
                        return `<strong>${row.installment}</strong>`;
                    }
                },
                {
                    "data": "interest_rate",
                    "render": function(data, type, row, meta) {
                        return parseFloat(data).toFixed(2);
                    }
                },
                { "data": "interest_paid" },
                { "data": "late_paid" },
                {
                    "data": "status",
                    "render": function(data, type, row, meta) {
                        if (row.deleted_at) {
                            return `<span style="color:#c0392b; font-weight:700">Deleted</span>`;
                        }

                        const colors = {
                            'Active':     '#1a7a36',
                            'Fully Paid': '#a32d2d',
                            'Overdue':    '#7a6800',
                            'Bad Debt':   '#7a6800',
                            'Blacklist':  '#7a6800',
                        };
                        let clr = colors[data] || '#7a6800';
                        return `<span style="color:${clr}; font-weight:500">${data}</span>`;
                    }
                },
                {
                    "data": null,
                    "render": function(data, type, row, meta) {
                        let url = `
                        <div class="cus-action-wrapper">
                            <a href="{{ route('loan.single_loan', ['loan_code' => ':loan_code']) }}"
                            class="cus-action-icon"
                            style="background-color: #17a2b8; color: white;"
                            title="View Detail">
                            <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('schedule.create', ['loan_code' => ':loan_code']) }}"
                            class="cus-action-icon"
                            style="background-color: #6c757d; color: white;"
                            title="Create Schedule">
                            <i class="fas fa-calendar-alt"></i>
                            </a>
                            <a href="{{ route('payment.create', ['loan_code' => ':loan_code']) }}"
                            class="cus-action-icon"
                            style="background-color: #28a745; color: white;"
                            title="Create Payment">
                            <i class="fas fa-money-check-alt"></i>
                            </a>
                            @if(Auth::user()->role_id == 1)
                            <a class="cus-action-icon danger" 
                            title="Delete Loan" 
                            onclick="deleteLoan(${row.id})">
                            <i class="fas fa-trash-alt"></i>
                            </a>
                            @endif
                        </div>
                        `;
                        url = url.replaceAll(':loan_code', row.loan_code);
                        return url;
                    }
                }
            ],
        });

        $('#hide-fully-paid').on('change', function() {
            table_loan.draw();
        });

        makeLoanTableResizable(table_loan);
        makeReferenceTableResizable();
        makeAssetTableResizable();
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

        function deleteLoan(id) {
            function submitDelete(){
                $.ajax({
                    url: "{{ route('loan.delete') }}",
                    type: "POST",
                    data: {id: id},
                    headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                    success: function (response) {
                        if(response.success == true){
                            setReloadSwal('success', '', response.message);
                        } else {
                            setDefaultSwal('error', '', response.message);
                        }
                    },
                    error: function (xhr) {
                        setDefaultSwal('error', '', 'There is something wrong, please try again.');
                    }
                });
            }
            setConfirmationSwal(
                "Warning",
                "This action will cannot be undone. Proceed?",
                'Process',
                'Cancel'
            ).then((result) => {
                if (result.isConfirmed) {
                    submitDelete();
                }
            });
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

    <script>
        const DOCS_INDEX  = "{{ route('customer.documents.index', $customer->id) }}";
        const DOCS_UPLOAD = "{{ route('customer.documents.store', $customer->id) }}";
        const CSRF        = "{{ csrf_token() }}";

        let allDocuments   = [];
        let lightboxImages = [];
        let lightboxIndex  = 0;
        let docsLoaded     = false;
        let isDragging = false;
        let startX, startY;
        let translateX = 0;
        let translateY = 0;

        $(document).ready(function () {

            // If direct open with #document
            if (window.location.hash === '#document') {
                loadDocumentsOnce();

                const tabTrigger = document.querySelector('[href="#document"]');
                if (tabTrigger) {
                    new bootstrap.Tab(tabTrigger).show();
                }
            }

        });

        $(document).on('shown.bs.tab', function (e) {
            const target = $(e.target).attr('href') || $(e.target).attr('data-bs-target');

            if (target === '#document') {
                loadDocumentsOnce();
            }
        });

        // 🔥 Prevent multiple calls
        function loadDocumentsOnce() {
            if (docsLoaded) return;
            docsLoaded = true;
            loadDocuments();
        }

        function loadDocuments() {
            const grid = document.getElementById('documents_grid');

            grid.innerHTML = `
                <div class="col-12 text-center text-muted py-5">
                    <i class="bx bx-loader-alt bx-spin fs-1 d-block mb-2"></i>
                    Loading documents...
                </div>`;

            fetch(DOCS_INDEX, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
            })
            .then(r => r.json())
            .then(data => {
                allDocuments   = data.documents;
                lightboxImages = allDocuments.filter(d => d.is_image);
                renderDocuments(allDocuments);
            })
            .catch(() => {
                docsLoaded = false; // allow retry

                grid.innerHTML = `
                    <div class="col-12 text-center text-danger py-5">
                        <i class="bx bx-error-circle fs-1 d-block mb-2"></i>
                        Failed to load. 
                        <button class="btn btn-sm btn-outline-primary mt-2 d-block mx-auto" 
                                onclick="loadDocumentsOnce()">Retry</button>
                    </div>`;
            });
        }

        function renderDocuments(docs) {
            const grid = document.getElementById('documents_grid');
            const USER_ROLE_ID = {{ auth()->user()->role_id ?? 0 }};
            let role_id = USER_ROLE_ID;

            if (docs.length === 0) {
                grid.innerHTML = `
                    <div class="col-12 text-center text-muted py-5">
                        <i class="bx bx-folder-open fs-1 d-block mb-2"></i>
                        {{ __('table.no_documents_uploaded') }}<br>
                        <small>{{ __('table.click_upload_file') }}</small>
                    </div>`;
                return;
            }

            grid.innerHTML = docs.map(doc => {

                const thumb = doc.is_image
                    ? `<img src="${doc.url}"
                            class="rounded mb-2"
                            style="height:120px;width:100%;object-fit:cover;cursor:pointer;"
                            onclick="openLightbox(${doc.id})">`
                    : `<div class="d-flex align-items-center justify-content-center rounded bg-light mb-2"
                            style="height:120px;">
                            <i class="bx bxs-file-pdf text-danger" style="font-size:4rem;"></i>
                    </div>`;

                return `
                <div class="col-sm-6 col-md-4 col-lg-3" id="doc_card_${doc.id}">
                    <div class="card h-100 border shadow-sm">
                        <div class="card-body p-2">
                            ${thumb}
                            <div class="fw-semibold text-truncate small" title="${doc.file_name}">
                                ${doc.file_name}
                            </div>

                            <div class="mt-1 position-relative">
                                <input type="text"
                                    class="form-control form-control-sm pe-4"
                                    value="${doc.remark ?? ''}"
                                    placeholder="Add remark..."
                                    onchange="updateRemark(${doc.id}, this)">

                                <span id="remark_tick_${doc.id}"
                                    style="position:absolute; right:8px; top:50%; transform:translateY(-50%);
                                    font-size:24px; color:green; display:none;">
                                    ✔
                                </span>
                            </div>

                            <div class="text-muted mt-1" style="font-size:10px;">
                                ${doc.created_at}
                            </div>
                        </div>

                        <div class="card-footer p-1 d-flex justify-content-end gap-1 bg-white border-top">
                            ${doc.is_image
                                ? `<button class="btn btn-xs btn-outline-secondary"
                                        onclick="openLightbox(${doc.id})">
                                        <i class="bx bx-show"></i>
                                </button>`
                                : `<a href="${doc.url}" target="_blank"
                                        class="btn btn-xs btn-outline-secondary">
                                        <i class="bx bx-show"></i>
                                </a>`
                            }

                            <a href="${doc.download_url}"
                            class="btn btn-xs btn-outline-primary">
                                <i class="bx bx-download"></i>
                            </a>

                            ${role_id != 4 ? `
                            <button class="btn btn-xs btn-outline-danger"
                                    onclick="deleteDocument(${doc.id}, '${doc.delete_url}')">
                                <i class="bx bx-trash"></i>
                            </button>
                            ` : ''}
                        </div>
                    </div>
                </div>`;
            }).join('');
        }

        function uploadDocument() {
            const fileInput = document.getElementById('doc_file');
            const remark    = document.getElementById('doc_remark').value;
            const btn       = document.getElementById('btn_upload');

            if (!fileInput.files.length) {
                alert('Please select a file to upload.');
                return;
            }

            const formData = new FormData();
            formData.append('_token', CSRF);
            formData.append('file', fileInput.files[0]);
            formData.append('remark', remark);

            btn.disabled = true;
            btn.innerHTML = '<i class="bx bx-loader-alt bx-spin me-1"></i> Uploading...';
            document.getElementById('upload_progress').style.display = 'block';

            fetch(DOCS_UPLOAD, { method: 'POST', body: formData })
            .then(r => r.json())
            .then(data => {

                btn.disabled = false;
                btn.innerHTML = '<i class="bx bx-upload me-1"></i> Upload';
                document.getElementById('upload_progress').style.display = 'none';

                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('modalUploadDocument')).hide();
                    fileInput.value = '';
                    document.getElementById('doc_remark').value = '';

                    docsLoaded = false; // 🔥 allow reload
                    loadDocumentsOnce();
                } else {
                    alert(data.message || 'Upload failed.');
                }

            })
            .catch(() => {
                btn.disabled = false;
                btn.innerHTML = '<i class="bx bx-upload me-1"></i> Upload';
                document.getElementById('upload_progress').style.display = 'none';
                alert('Upload error. Please try again.');
            });
        }

        function deleteDocument(id, url) {
            if (!confirm('Are you sure you want to delete this document?')) return;

            fetch(url, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('doc_card_' + id)?.remove();

                    allDocuments   = allDocuments.filter(d => d.id !== id);
                    lightboxImages = lightboxImages.filter(d => d.id !== id);

                    if (allDocuments.length === 0) renderDocuments([]);
                }
            });
        }

        let lightboxScale = 1;

        function openLightbox(id) {
            lightboxIndex = lightboxImages.findIndex(d => d.id === id);
            if (lightboxIndex === -1) return;

            resetLightboxZoom();
            showLightboxAt(lightboxIndex);
            new bootstrap.Modal(document.getElementById('imageLightbox')).show();
        }

        function showLightboxAt(index) {
            const doc = lightboxImages[index];

            document.getElementById('lightbox_img').src = doc.url;
            document.getElementById('lightbox_title').textContent = doc.file_name;
            document.getElementById('lightbox_download').href = doc.download_url;
            document.getElementById('lightbox_counter').textContent =
                `${index + 1} / ${lightboxImages.length}`;
        }

        function lightboxNav(dir) {
            lightboxIndex =
                (lightboxIndex + dir + lightboxImages.length) % lightboxImages.length;

            resetLightboxZoom();
            showLightboxAt(lightboxIndex);
        }

        function resetLightboxZoom() {
            lightboxScale = 1;
            translateX = 0;
            translateY = 0;

            const img = document.getElementById('lightbox_img');

            let isDragging = false;
            let startX = 0;
            let startY = 0;

            img.addEventListener('mousedown', function(e) {
                if (lightboxScale <= 1) return;

                isDragging = true;
                startX = e.clientX - translateX;
                startY = e.clientY - translateY;

                img.style.cursor = 'grabbing';

                e.preventDefault();
            });

            document.addEventListener('mousemove', function(e) {
                if (!isDragging) return;

                translateX = e.clientX - startX;
                translateY = e.clientY - startY;

                img.style.transform =
                    `scale(${lightboxScale}) translate(${translateX}px, ${translateY}px)`;
            });

            document.addEventListener('mouseup', function() {
                isDragging = false;
                img.style.cursor = lightboxScale > 1 ? 'grab' : 'zoom-in';
            });
        }

        function lightboxToggleZoom(img) {
            lightboxScale = lightboxScale > 1 ? 1 : 2;
            img.style.transform = `scale(${lightboxScale})`;
            img.style.cursor = lightboxScale > 1 ? 'grab' : 'zoom-in';
        }

        document.getElementById('lightbox_img').addEventListener('wheel', function(e) {
            e.preventDefault();
            lightboxScale += e.deltaY < 0 ? 0.1 : -0.1;
            lightboxScale = Math.min(Math.max(lightboxScale, 0.5), 5);
            this.style.transform = `scale(${lightboxScale}) translate(${translateX}px, ${translateY}px)`;
            this.style.cursor = lightboxScale > 1 ? 'grab' : 'zoom-in';
        }, { passive: false });

        function updateRemark(id, inputEl) {
            const remark = inputEl.value;
            const tick   = document.getElementById('remark_tick_' + id);

            fetch(`/customer/{{ $customer->id }}/documents/${id}/remark`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': CSRF,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ remark })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    // ✅ show tick
                    tick.style.display = 'inline';

                    // hide after 1.5s
                    setTimeout(() => {
                        tick.style.display = 'none';
                    }, 1500);
                } else {
                    alert('Failed to update remark');
                }
            })
            .catch(() => {
                alert('Error updating remark');
            });
        }

        function makeReferenceTableResizable() {
            $('#datatable-reference thead th').each(function() {
                $(this).css('position', 'relative');

                if ($(this).find('.col-resize-handle').length > 0) return;

                const handle = $('<div class="col-resize-handle">').appendTo(this);

                handle.on('mousedown', function(e) {
                    const th = $(this).parent();
                    const startX = e.pageX;
                    const startWidth = th.outerWidth();

                    // Disable pointer events on ALL th during drag
                    $('#datatable-reference thead th').css('pointer-events', 'none');
                    // But keep pointer events on handles so mousemove still works
                    $('#datatable-reference .col-resize-handle').css('pointer-events', 'auto');

                    $(document).on('mousemove.colresize-ref', function(e) {
                        const newWidth = startWidth + (e.pageX - startX);
                        if (newWidth > 50) {
                            th.css('width', newWidth + 'px');
                            th.css('min-width', newWidth + 'px');
                        }
                    });

                    $(document).on('mouseup.colresize-ref', function() {
                        $(document).off('mousemove.colresize-ref mouseup.colresize-ref');

                        // Re-enable pointer events after a short delay
                        // so the mouseup click doesn't trigger sort
                        setTimeout(function() {
                            $('#datatable-reference thead th').css('pointer-events', '');
                        }, 200);
                    });

                    e.preventDefault();
                    e.stopImmediatePropagation();
                });

                handle.on('click', function(e) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                });
            });
        }

        function makeLoanTableResizable(dtInstance) {
            $('#table-loan thead th').each(function() {
                $(this).css('position', 'relative');

                if ($(this).find('.col-resize-handle').length > 0) return;

                const handle = $('<div class="col-resize-handle">').appendTo(this);

                handle.on('mousedown', function(e) {
                    const th = $(this).parent();
                    const startX = e.pageX;
                    const startWidth = th.outerWidth();

                    dtInstance.settings()[0].aoColumns.forEach(function(col) {
                        col.bSortable = false;
                    });

                    $(window).on('mousemove.colresize-loan', function(e) {
                        const newWidth = startWidth + (e.pageX - startX);
                        if (newWidth > 50) {
                            th.css('width', newWidth + 'px');
                            th.css('min-width', newWidth + 'px');
                        }
                    });

                    $(window).on('mouseup.colresize-loan', function() {
                        $(window).off('mousemove.colresize-loan mouseup.colresize-loan');
                        setTimeout(function() {
                            dtInstance.settings()[0].aoColumns.forEach(function(col) {
                                col.bSortable = true;
                            });
                        }, 200);
                    });

                    e.preventDefault();
                    e.stopImmediatePropagation();
                });

                handle.on('click', function(e) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                });
            });
        }

        function makeAssetTableResizable() {
            $('#datatable-asset thead th').each(function() {
                $(this).css('position', 'relative');

                if ($(this).find('.col-resize-handle').length > 0) return;

                const handle = $('<div class="col-resize-handle">').appendTo(this);

                handle.on('mousedown', function(e) {
                    const th = $(this).parent();
                    const startX = e.pageX;
                    const startWidth = th.outerWidth();

                    $('#datatable-asset thead th').css('pointer-events', 'none');
                    $('#datatable-asset .col-resize-handle').css('pointer-events', 'auto');

                    $(window).on('mousemove.colresize-asset', function(e) {
                        const newWidth = startWidth + (e.pageX - startX);
                        if (newWidth > 50) {
                            th.css('width', newWidth + 'px');
                            th.css('min-width', newWidth + 'px');
                        }
                    });

                    $(window).on('mouseup.colresize-asset', function() {
                        $(window).off('mousemove.colresize-asset mouseup.colresize-asset');
                        setTimeout(function() {
                            $('#datatable-asset thead th').css('pointer-events', '');
                        }, 200);
                    });

                    e.preventDefault();
                    e.stopImmediatePropagation();
                });

                handle.on('click', function(e) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                });
            });
        }
    </script>
@endsection