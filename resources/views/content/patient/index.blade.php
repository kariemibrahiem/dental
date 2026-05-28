@extends('layouts/contentNavbarLayout')

@section('title', 'Patients Management')

@section('content')
<h4 class="py-3 mb-4">
    <span class="text-muted fw-light">
        <a href="{{ route('dashboard-analytics') }}">Dashboard</a> /
    </span> Patients
</h4>

<div class="row">
    <!-- Left Column: Add Patient -->
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><span class="badge bg-label-primary me-2"><i class="bx bx-user-plus fs-4"></i></span> Add Patient</h5>
            </div>
            <div class="card-body">
                <form id="addPatientForm" action="{{ route('patients.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label" for="patientName">Patient Name</label>
                        <input type="text" class="form-control" id="patientName" name="name" placeholder="Enter Patient Name" required />
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="doctorSelect">Select Doctor</label>
                        <select class="form-select" id="doctorSelect" name="doctor_id" required>
                            <option value="">Select Doctor</option>
                            @foreach($doctors as $doctor)
                                <option value="{{ $doctor->id }}">{{ $doctor->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="resultSelect">Diagnosis Result</label>
                        <select class="form-select" id="resultSelect" name="result" required>
                            <option value="Healthy">Healthy</option>
                            <option value="Cavity">Cavity</option>
                            <option value="Infection">Infection</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="patientDate">Date</label>
                        <input type="date" class="form-control" id="patientDate" name="date" value="{{ date('Y-m-d') }}" required />
                    </div>

                    <button type="submit" class="btn btn-primary w-100 d-flex align-items-center justify-content-center">
                        <i class="bx bx-plus me-1"></i> Add
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Right Column: Patients List -->
    <div class="col-md-8 mb-4">
        <div class="card h-100">
            <h5 class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-bold"><span class="badge bg-label-info me-2"><i class="bx bx-list-ul fs-4"></i></span> Patients List</span>
            </h5>
            
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table class="table table-bordered table-striped" id="patientsTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Patient Name</th>
                                <th>Doctor</th>
                                <th>Diagnosis</th>
                                <th>Date</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirm Modal -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this patient record?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirm-delete-btn">Delete</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
$(document).ready(function () {
    const table = $('#patientsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("patients.index") }}',
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'name', name: 'name' },
            { data: 'doctor_name', name: 'doctor_name' },
            { 
                data: 'result', 
                name: 'result',
                render: function (data) {
                    if (data === 'Healthy') {
                        return '<span class="badge bg-label-success">Healthy</span>';
                    } else if (data === 'Cavity') {
                        return '<span class="badge bg-label-warning">Cavity</span>';
                    } else {
                        return '<span class="badge bg-label-danger">Infection</span>';
                    }
                }
            },
            { data: 'date', name: 'date' },
            { data: 'action', name: 'action', orderable: false, searchable: false, class: 'text-center' }
        ],
        order: [[4, "desc"]],
        language: {
            sZeroRecords: "No records found",
            sProcessing: "Processing...",
            sSearch: "Search:",
            oPaginate: {
                sPrevious: "Previous",
                sNext: "Next"
            }
        }
    });

    // Form Submit via AJAX
    $('#addPatientForm').on('submit', function (e) {
        e.preventDefault();
        const form = $(this);
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            success: function (response) {
                if (response.status === 200) {
                    toastr.success(response.message);
                    form.trigger('reset');
                    // Reset date input to today
                    $('#patientDate').val(new Date().toISOString().substring(0, 10));
                    table.ajax.reload();
                } else {
                    toastr.error(response.message || 'Something went wrong.');
                }
            },
            error: function (xhr) {
                const errors = xhr.responseJSON.errors;
                if (errors) {
                    let errMsgs = [];
                    Object.values(errors).forEach(err => errMsgs.push(err[0]));
                    toastr.error(errMsgs.join('<br>'));
                } else {
                    toastr.error('Failed to add patient.');
                }
            }
        });
    });

    // Delete confirmation
    $(document).on('click', '.delete-confirm', function (e) {
        e.preventDefault();
        const deleteUrl = $(this).data('url');
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
        deleteModal.show();

        $('#confirm-delete-btn').off('click').on('click', function () {
            $.ajax({
                url: deleteUrl,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {
                    if (response.status === 200) {
                        toastr.success(response.message);
                        deleteModal.hide();
                        table.ajax.reload();
                    } else {
                        toastr.error(response.message || 'Failed to delete.');
                    }
                },
                error: function () {
                    toastr.error('Failed to delete patient.');
                    deleteModal.hide();
                }
            });
        });
    });
});
</script>
@endpush
