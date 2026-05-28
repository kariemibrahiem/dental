@extends('layouts/contentNavbarLayout')

@section('title', 'Doctors Management')

@section('content')
<h4 class="py-3 mb-4">
    <span class="text-muted fw-light">
        <a href="{{ route('dashboard-analytics') }}">Dashboard</a> /
    </span> Doctors
</h4>

<div class="row">
    <!-- Left Column: Add Doctor -->
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><span class="badge bg-label-primary me-2"><i class="bx bx-user-plus fs-4"></i></span> Add Doctor</h5>
            </div>
            <div class="card-body">
                <form id="addDoctorForm" action="{{ route('doctors.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label" for="doctorName">Doctor Name</label>
                        <input type="text" class="form-control" id="doctorName" name="name" placeholder="Enter Doctor Name" required />
                    </div>
                    <button type="submit" class="btn btn-primary w-100 d-flex align-items-center justify-content-center">
                        <i class="bx bx-save me-1"></i> Save
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Right Column: Doctors List -->
    <div class="col-md-8 mb-4">
        <div class="card h-100">
            <h5 class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-bold"><span class="badge bg-label-info me-2"><i class="bx bx-list-ul fs-4"></i></span> Doctors List</span>
            </h5>
            
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table class="table table-bordered table-striped" id="doctorsTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Doctor Name</th>
                                <th>Created At</th>
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
                <p>Are you sure you want to delete this doctor? Deleting this doctor will also remove all their associated patient records.</p>
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
    const table = $('#doctorsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("doctors.index") }}',
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'name', name: 'name' },
            { data: 'created_at', name: 'created_at' },
            { data: 'action', name: 'action', orderable: false, searchable: false, class: 'text-center' }
        ],
        order: [[1, "asc"]],
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
    $('#addDoctorForm').on('submit', function (e) {
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
                    table.ajax.reload();
                } else {
                    toastr.error(response.message || 'Something went wrong.');
                }
            },
            error: function (xhr) {
                const errors = xhr.responseJSON.errors;
                if (errors && errors.name) {
                    toastr.error(errors.name[0]);
                } else {
                    toastr.error('Failed to add doctor.');
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
                    toastr.error('Failed to delete doctor.');
                    deleteModal.hide();
                }
            });
        });
    });
});
</script>
@endpush
