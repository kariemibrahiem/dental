

<?php $__env->startSection('title', 'Login Basic - Pages'); ?>

<?php $__env->startSection('page-style'); ?>
<link rel="stylesheet" href="<?php echo e(asset('assets/vendor/css/pages/page-auth.css')); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-xxl">
  <div class="authentication-wrapper authentication-basic container-p-y">
    <div class="authentication-inner">
      <div class="card">
        <div class="card-body">
          <!-- Logo -->
          <div class="app-brand justify-content-center">
            <a href="<?php echo e(url('/')); ?>" class="app-brand-link gap-2">
              <span class="app-brand-logo demo"><?php echo $__env->make('_partials.macros',["width"=>25,"withbg"=>'var(--bs-primary)'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></span>
              <span class="app-brand-text demo text-body fw-bold"><?php echo e(config('variables.templateName')); ?></span>
            </a>
          </div>
          <!-- /Logo -->

          <h4 class="mb-2">Welcome to <?php echo e(config('variables.templateName')); ?>! 👋</h4>

          <form id="formAuthentication" class="mb-3" action="<?php echo e(route('login')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <div class="mb-3">
              <label for="email" class="form-label">Email or Username</label>
              <input type="text" class="form-control" id="email" name="email_username" placeholder="Enter your email or username" autofocus>
              <div class="invalid-feedback" id="error-email_username"></div>
            </div>

            <div class="mb-3 form-password-toggle">
              <div class="d-flex justify-content-between">
                <label class="form-label" for="password">Password</label>
                <a href="<?php echo e(url('auth/forgot-password-basic')); ?>">
                  <small>Forgot Password?</small>
                </a>
              </div>
              <div class="input-group input-group-merge">
                <input type="password" id="password" class="form-control" name="password" placeholder="********">
                <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
              </div>
              <div class="invalid-feedback" id="error-password"></div>
            </div>

            <div class="mb-3">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="remember" name="remember">
                <label class="form-check-label" for="remember">Remember Me</label>
              </div>
            </div>

            <div id="login-errors" class="alert alert-danger d-none"></div>

            <div class="mb-3">
              <button class="btn btn-primary d-grid w-100" type="submit">Sign in</button>
            </div>
          </form>

        </div>
      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-script'); ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(function(){
    $('#formAuthentication').on('submit', function(e){
        e.preventDefault();

        // reset errors
        $(".invalid-feedback").text('');
        $("input").removeClass("is-invalid");
        $("#login-errors").addClass("d-none").html('');

        $.ajax({
            url: $(this).attr('action'),
            method: "POST",
            data: $(this).serialize(),
            success: function(response){
              console.log("test");
                if(response.status === 200){
                    toastr.success(response.message);
                    setTimeout(function(){
                        window.location.href = <?php echo json_encode(route("dashboard-analytics"), 15, 512) ?>;
                    }, 800);
                }
            },
            error: function(xhr){
                let res = xhr.responseJSON;

                if(res.status === 422 && res.errors){
                    $.each(res.errors, function(field, messages){
                        $("#error-" + field).text(messages[0]);
                        $("input[name='"+field+"']").addClass("is-invalid");
                    });
                } else if(res.status === 401 && res.errors){
                    $.each(res.errors, function(field, messages){
                        $("#error-" + field).text(messages[0]);
                        $("input[name='"+field+"']").addClass("is-invalid");
                    });
                    toastr.error("بيانات الدخول غير صحيحة");
                } else {
                    toastr.error(res.message || "حدث خطأ غير متوقع");
                }
            }
        });
    });
});

</script>

<!-- toastr CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

<!-- toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/blankLayout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\projects\dental\resources\views/content/authentications/auth-login-basic.blade.php ENDPATH**/ ?>