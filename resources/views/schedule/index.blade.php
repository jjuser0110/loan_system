@extends('layouts.app')
@section('content')
<header class="page-header">
    <h2>Payment Schedules</h2>
</header>
@include('layouts.flash-message')
<div class="row">
    <section class="card">
        <form class="theme-form mega-form" action="{{ route('loan.single_loan') }}" method="get">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8 mb-3" style="display:flex;flex-wrap:nowrap;gap:5px;">
                        <div class="col-md-2">
                            <label class="col-form-label" style="padding:0">Start Date</label>
                            <input type="date" class="form-control cus-input" id="start-date" onchange="reloadTable()" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-2">
                            <label class="col-form-label" style="padding:0">End Date</label>
                            <input type="date" class="form-control" id="end-date" onchange="reloadTable()" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="col-md-4" style="display:flex;flex-wrap:nowrap; gap: 5px;justify-content:flex-end;text-align:right">
                        <div>
                            <label class="col-form-label" style="padding:0">Total due amount</label>
                            <h2 style="margin:0">RM <span id="total-due-amount">0.00</span></h2>
                        </div>
                    </div>
                </div>
            </div>
         </form>
    </section>
</div>

<div class="row">
    <div class="col-lg-12 mb-3">
        <section class="card">
            <div class="card-header" style="text-align:right">
                <a class="btn btn-xs btn-square btn-primary" href="{{route('schedule.create')}}">Create</a>
            </div>
            <div class="card-body">
                <table class="table cus-table table-bordered table-striped mb-0" id="table-payment-schedules">
                    <thead>
                        <tr>
                            <th>Schedule Code</th>
                            <th>Due Date</th>
                            <th>Payment</th>
                            <th>Paid</th>
                            <th>Discount</th>
                            <th>Interest</th>
                            <th>Interest Paid</th>
                            <th>Late</th>
                            <th>Late Paid</th>
                            <th>Loan Code</th>
                            @if(Auth::user()->role_id <= 3)
                            <th>Action</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </section>
    </div>
</div>

<div class="modal fade" id="modal-update-schedule" tabindex="-1" aria-labelledby="modalUpdateScheduleLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalUpdateScheduleLabel">Update Schedule</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form-update-schedule">
                    @csrf
                    <input type="hidden" name="schedule_id" id="update-schedule-id">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="col-form-label">Due Date</label>
                            <input type="date" class="form-control" name="due_date" id="update-schedule-date" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="col-form-label">Interest Amount</label>
                            <input type="number" class="form-control" name="interest_amount" id="update-schedule-interest">
                        </div>
                        <div class="col-md-6">
                            <label class="col-form-label">Interest Paid</label>
                            <input type="number" class="form-control" name="interest_paid_amount" id="update-schedule-interest-paid">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="col-form-label">Payment/Capital Amount</label>
                            <input type="number" class="form-control" name="payment_amount" id="update-schedule-payment">
                        </div>
                        <div class="col-md-6">
                            <label class="col-form-label">Payment/Capital Paid</label>
                            <input type="number" class="form-control" name="paid_amount" id="update-schedule-paid">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="col-form-label">Payment/Capital Discount</label>
                            <input type="number" class="form-control" name="discount_amount" id="update-schedule-discount">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="col-form-label">Late Amount</label>
                            <input type="number" class="form-control" name="late_amount" id="update-schedule-late">
                        </div>
                        <div class="col-md-6">
                            <label class="col-form-label">Late Paid</label>
                            <input type="number" class="form-control" name="late_paid_amount" id="update-schedule-late-paid">
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let table;
    let time = 'all';
    $(document).ready(function() {
        table = $('#table-payment-schedules').DataTable({
            "processing": true,
            "serverSide": true,
            "fixedHeader": false,
            "ajax": {
                "url": "{{ route('schedule.load_schedule') }}",
                "type": "GET",
                'data': function(d){
                    d.start_date = document.getElementById('start-date').value;
                    d.end_date = document.getElementById('end-date').value;
                },
                "dataSrc": function(json) {
                    $('#total-due-amount').html(formatCredit(json.total_due_amount));
                    return json.data;
                }
            },
            "order": [
                [2, "desc"]
            ],
            "columns": [
                {
                    "data": "schedule_code"
                },
                {
                    "data": "due_date"
                },
                {
                    "data": "payment_amount"
                },
                {
                    "data": "paid_amount"
                },
                {
                    "data": "discount_amount"
                },
                {
                    "data": "interest_amount"
                },
                {
                    "data": "interest_paid_amount"
                },
                {
                    "data": "late_amount"
                },
                {
                    "data": "late_paid_amount"
                },
                {
                    "data": null,
                    "render": function(data, type, row, meta) {
                        return `<a href="{{ route('loan.single_loan', ['loan_code' => ':loan_code']) }}" class="info" title="View Detail">${row.loan_code}</a>`.replace(':loan_code',row.loan_code);
                    }
                },
                @if(Auth::user()->role_id <= 3)
                {
                    "data": null,
                    "render": function(data, type, row, meta) {
                        return`
                            <div class="cus-action-wrapper">
                                <a class="cus-action-icon info" title="Update Schedule" onclick="updateSchedule(${meta.row})"><i class="fas fa-edit"></i></a>
                                <a class="cus-action-icon danger" title="Delete Schedule" onclick="deleteSchedule(${meta.row})"><i class="fas fa-trash-alt"></i></a>
                            </div>
                        `;
                    }
                }
                @endif
            ]
        });
    });
    
    function reloadTable(){
        table.ajax.reload();
    }

     function updateSchedule(rowIndex) {
        const data = table.row(rowIndex).data();
        document.getElementById('update-schedule-id').value = data.id;
        document.getElementById('update-schedule-date').value = data.due_date;
        document.getElementById('update-schedule-late').value = data.late_amount;
        document.getElementById('update-schedule-payment').value = data.payment_amount;
        document.getElementById('update-schedule-interest').value = data.interest_amount;
        document.getElementById('update-schedule-paid').value = data.paid_amount;
        document.getElementById('update-schedule-interest-paid').value = data.interest_paid_amount;
        document.getElementById('update-schedule-late-paid').value = data.late_paid_amount;
        document.getElementById('update-schedule-discount').value = data.discount_amount;
        $('#modal-update-schedule').modal('show');
    }

    function deleteSchedule(rowIndex) {
        const data = table.row(rowIndex).data();
        function submitDelete(){
            $.ajax({
                url: "{{ route('schedule.delete') }}",
                type: "POST",
                data: {schedule_id:data.id},
                headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}"},
                success: function (response) {
                    if(response.success == true){
                        setReloadSwal('success','',response.message);
                    }
                    else{
                        setDefaultSwal('error','',response.message);
                    }
                },
                error: function (xhr) {
                    setDefaultSwal('error','','There is something wrong, please try again.');
                }
            });
        }
        setConfirmationSwal(
            "Warning",
            "This action will affect the entire loan and cannot be undone. Proceed?",
            'Process',
            'Cancel'
        ).then((result) => {
            if (result.isConfirmed) {
                submitDelete();
            }
        });
    }

     $('#form-update-schedule').on('submit', function (e) {
        e.preventDefault();
        let form = $(this);
        let formData = new FormData(this);
        $.ajax({
            url: "{{ route('schedule.update') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}"},
            success: function (response) {
                if(response.success == true){
                    setReloadSwal('success','',response.message);
                }
                else{
                    setDefaultSwal('error','',response.message);
                }
            },
            error: function (xhr) {
                setDefaultSwal('error','','There is something wrong, please try again.');
            }
        });
    });

</script>
@endsection
