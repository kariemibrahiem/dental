<?php $__env->startSection('title', 'Medical Reports'); ?>

<?php $__env->startSection('content'); ?>
<h4 class="py-3 mb-4">
    <span class="text-muted fw-light">
        <a href="<?php echo e(route('dashboard-analytics')); ?>">Dashboard</a> /
    </span> Reports
</h4>

<div class="card shadow-sm border-0">
    <h5 class="card-header d-flex justify-content-between align-items-center flex-wrap">
        <span class="fw-bold"><span class="badge bg-label-primary me-2"><i class="bx bx-file fs-4"></i></span> Patient Medical Reports</span>
        <div>
            <button class="btn btn-danger" id="deleteSelectedBtn"><i class="bx bx-trash me-1"></i> Delete Selected</button>
        </div>
    </h5>

    <div class="card-body">
        <div class="table-responsive text-nowrap">
            <table class="table table-bordered table-striped" id="reportsTable">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="selectAll"></th>
                        <th>#</th>
                        <th>Patient</th>
                        <th>Report Title</th>
                        <th>Description</th>
                        <th>Scan Document</th>
                        <th>Uploaded At</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
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
                <p>Are you sure you want to delete the selected patient report(s)? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirm-delete-btn">Delete</button>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
$(document).ready(function () {
    const table = $('#reportsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '<?php echo e(route("reports.index")); ?>',
        columns: [
            {
                data: 'id',
                orderable: false,
                searchable: false,
                render: function(data) {
                    return `<input type="checkbox" class="row-checkbox" value="${data}">`;
                }
            },
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'patient_name', name: 'patient_name' },
            { data: 'title', name: 'title' },
            { 
                data: 'description', 
                name: 'description',
                render: function(data) {
                    return `<div style="max-width: 250px; white-space: normal; word-break: break-all;">${data}</div>`;
                }
            },
            { data: 'image_path', name: 'image_path', orderable: false, searchable: false, class: 'text-center' },
            { data: 'created_at', name: 'created_at' },
            { data: 'action', name: 'action', orderable: false, searchable: false, class: 'text-center' }
        ],
        order: [[6, "desc"]],
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

    // Select all checkbox
    $('#selectAll').on('change', function() {
        $('.row-checkbox').prop('checked', this.checked);
    });

    $(document).on('change', '.row-checkbox', function() {
        if (!this.checked) {
            $('#selectAll').prop('checked', false);
        }
    });

    // Single Delete
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
                    _token: '<?php echo e(csrf_token()); ?>'
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
                    toastr.error('Failed to delete report.');
                    deleteModal.hide();
                }
            });
        });
    });

    // Bulk Delete
    $('#deleteSelectedBtn').on('click', function() {
        const selectedIds = $('.row-checkbox:checked').map(function() {
            return $(this).val();
        }).get();

        if (selectedIds.length === 0) {
            toastr.warning('Please select at least one report.');
            return;
        }

        const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
        deleteModal.show();

        $('#confirm-delete-btn').off('click').on('click', function () {
            $.ajax({
                url: '<?php echo e(route("reports.destroySelected")); ?>',
                type: 'POST',
                data: {
                    _token: '<?php echo e(csrf_token()); ?>',
                    ids: selectedIds
                },
                success: function (response) {
                    if (response.status === 200) {
                        toastr.success(response.message);
                        deleteModal.hide();
                        $('#selectAll').prop('checked', false);
                        table.ajax.reload();
                    } else {
                        toastr.error(response.message || 'Failed to delete.');
                    }
                },
                error: function () {
                    toastr.error('Failed to delete reports.');
                    deleteModal.hide();
                }
            });
        });
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts/contentNavbarLayout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\projects\dental\resources\views/content/report/index.blade.php ENDPATH**/ ?>