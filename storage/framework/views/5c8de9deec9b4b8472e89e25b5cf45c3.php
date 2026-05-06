

<?php $__env->startSection('title', trns($route)); ?>

<?php $__env->startSection('content'); ?>
<h4 class="py-3 mb-4">
    <span class="text-muted fw-light">
        <a href="<?php echo e(route('dashboard-analytics')); ?>"><?php echo e(trns('Dashboard')); ?></a> /
    </span> <?php echo e(trns($route)); ?>

</h4>

<style>
    select[name="usersTable_length"] {
        margin-top: 10px;
        margin-bottom: 10px;
    }
    input[type="search"]{
        margin-top: 10px;
        margin-bottom: 10px;
    }


    /* Container for DataTables search */
.dataTables_filter {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 1rem;
}

/* Style the label text "Search:" */
.dataTables_filter label {
  font-weight: 500;
  color: #888;
  font-size: 14px;
  margin: 0;
  display: flex;
  align-items: center;
  gap: 6px;
}

/* Style the search input */
.dataTables_filter input[type="search"] {
  font-family: inherit;
  font-size: 14px;
  padding: 6px 10px;
  border-radius: 6px;
  border: 1px solid #ccc;
  background-color: #fff;
  transition: all 0.3s ease;
}

/* Hover effect */
.dataTables_filter input[type="search"]:hover {
  border-color: #999;
}

/* Focus effect */
.dataTables_filter input[type="search"]:focus {
  border-color: #38caef;
  box-shadow: 0 0 5px rgba(56, 202, 239, 0.4);
  outline: none;
}

    
</style>

<hr class="my-5" />

<!-- Card -->
<div class="card">
  <h5 class="card-header d-flex justify-content-between align-items-center flex-wrap">
    <div class="d-flex align-items-center mb-2 mb-md-0">
      <span class="me-3 fw-bold"><?php echo e(trns($route)); ?></span>
      <ul class="list-unstyled users-list m-0 avatar-group d-flex align-items-center">
        <li class="avatar avatar-xs pull-up me-1">
          <img src="<?php echo e(asset('assets/img/avatars/5.png')); ?>" class="rounded-circle">
        </li>
        <li class="avatar avatar-xs pull-up me-1">
          <img src="<?php echo e(asset('assets/img/avatars/6.png')); ?>" class="rounded-circle">
        </li>
        <li class="avatar avatar-xs pull-up me-1">
          <img src="<?php echo e(asset('assets/img/avatars/7.png')); ?>" class="rounded-circle">
        </li>
      </ul>
    </div>
           <div>
      <button class="btn btn-success" id="bulkStatusUpdate"><?php echo e(trns("Update_Selected")); ?></button>
      <button class="btn btn-danger" id="deleteSelected"><?php echo e(trns("Delete_Selected")); ?></button>
      <a href="<?php echo e(route($route . '.create')); ?>" class="btn btn-primary"><?php echo e(trns('Add_New')); ?></a>
    </div>
  </h5>
   <!-- Delete Selected Modal -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" role="dialog" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?php echo e(trns('confirm_deletion')); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><?php echo e(trns('are_you_sure_you_want_to_delete_selected_items')); ?></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo e(trns('cancel')); ?></button>
                    <button type="button" class="btn btn-danger" id="confirm-delete-btn"><?php echo e(trns('delete')); ?></button>
                </div>
            </div>
        </div>
    </div>

  <div class="card-body">
    <div class="table-responsive text-nowrap">
      <table class="table table-bordered" id="usersTable">
        <thead>
          <tr>
            <th><input type="checkbox" id="select-all"></th>
            <th><?php echo e(trns('Name')); ?></th>
            <th><?php echo e(trns('Email')); ?></th>
            <th><?php echo e(trns('Status')); ?></th>
            <th><?php echo e(trns('image')); ?></th>
            <th><?php echo e(trns('Actions')); ?></th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<!-- DataTables CSS/JS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>

<!-- Toastr -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
$(document).ready(function () {
    const table = $('#usersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '<?php echo e(route($route . ".index")); ?>',
        columns: [
        {
                data: 'id',
                orderable: false,
                searchable: false,
                render: function(data) {
                    return `<input type="checkbox" class="row-checkbox" value="${data}">`;
                }
            },
            { data: 'name', name: 'name' },
            { data: 'email', name: 'email' },
            { data: 'status', name: 'status' },
            { data: 'image', name: 'image' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],

        order: [[1, "DESC"]],
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

    $('#select-all').on('click', function () {
        const rows = table.rows({ search: 'applied' }).nodes();
        $('input[type="checkbox"].row-checkbox', rows).prop('checked', this.checked);
    });

    $('#usersTable tbody').on('change', 'input.row-checkbox', function () {
        if (!this.checked) {
            $('#select-all').prop('checked', false);
        }
    });

    $('#bulkStatusUpdate').on('click', function () {
        const selectedIds = [];
        $('input.row-checkbox:checked').each(function () {
            selectedIds.push($(this).val());
        });

        if (selectedIds.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: '<?php echo e(trns("No selection")); ?>',
                text: '<?php echo e(trns("Please select at least one user.")); ?>',
                confirmButtonText: '<?php echo e(trns("OK")); ?>'
            });
            return;
        }


        $.ajax({
            type: 'POST',
            url: '<?php echo e(route($route . ".updateColumnSelected")); ?>',
            data: {
                _token: '<?php echo e(csrf_token()); ?>',
                ids: selectedIds,
                status: 'Active' // or 1, depending on your backend
            },
            success: function (data) {
                if (data.status === 200) {
                    toastr.success("Updated Successfully");
                    table.ajax.reload();
                } else {
                    toastr.error("Something went wrong");
                }
            },
            error: function () {
                toastr.error("AJAX Error");
            }
        });
    });

       // Bulk Delete
      $('#deleteSelected').on('click', function () {
          const selected = $('.row-checkbox:checked').map(function() {
              return $(this).val();
          }).get();

          if(selected.length === 0){
              alert('<?php echo e(trns("Please select at least one user.")); ?>');
              return;
          }

          let deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
          deleteModal.show();

          $('#confirm-delete-btn').off('click').on('click', function() {
              $.ajax({
                  url: '<?php echo e(route($route . ".destroySelected")); ?>',
                  type: 'POST',
                  data: {
                      _token: '<?php echo e(csrf_token()); ?>',
                      ids: selected
                  },
                  success: function(response) {
                      if(response.status === 200){
                          toastr.success('<?php echo e(trns("deleted_successfully")); ?>');
                          deleteModal.hide();
                          $('#select-all').prop('checked', false);
                          $('.row-checkbox').prop('checked', false);
                          table.ajax.reload();
                      } else {
                          toastr.error('<?php echo e(trns("something_went_wrong")); ?>');
                      }
                  },
                  error: function() {
                      toastr.error('<?php echo e(trns("something_went_wrong")); ?>');
                      deleteModal.hide();
                  }
              });
          });
      });
});

function deleteUser(id) {
    if (!confirm("Are you sure you want to delete this user?")) return;
    // Implement delete logic or AJAX here
    toastr.info("Delete functionality not implemented");
}
</script>


<script>
        // for status
        $(document).on('click', '.statusBtn', function() {
            let id = $(this).data('id');

            var val = $(this).is(':checked') ? 1 : 0;

            let ids = [id];




            $.ajax({
                type: 'POST',
                url: '<?php echo e(route($route . ".updateColumnSelected")); ?>',
                data: {
                    "_token": "<?php echo e(csrf_token()); ?>",
                    'ids': ids,
                },
                success: function(data) {
                    if (data.status === 200) {
                        if (val !== 0) {
                            toastr.success('Success', "");
                            $("#usersTable").DataTable().ajax.reload();
                        } else {
                            toastr.warning('Success', "");
                        }
                    } else {
                        toastr.error('Error', "");
                    }
                },
                error: function() {
                    toastr.error('Error', "<?php echo e(trns('something_went_wrong')); ?>");
                }
            });
        });



        $(document).on("change", "#statusSelection", function() {
            let status = $(this).val();
            let table = $('#dataTable').DataTable();

            table.rows().every(function() {
                var row = this.node();
                var checkbox = $(row).find('.statusBtn');
                var shouldShow = false;

                if (status === 'show all') shouldShow = true;
                else if (status === 'active') shouldShow = checkbox.is(':checked');
                else if (status === 'inactive') shouldShow = !checkbox.is(':checked');

                $(row).toggle(shouldShow);
            });
        });
    </script>



<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts/contentNavbarLayout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\projects\dental\resources\views/content/users/index.blade.php ENDPATH**/ ?>