<!DOCTYPE html>

<html
        class="light-style layout-menu-fixed"
        lang="<?php echo e(app()->getLocale()); ?>"
        dir="<?php echo e(app()->getLocale() == 'ar' ? 'rtl' : 'ltr'); ?>"
        data-theme="theme-default"
        data-assets-path="<?php echo e(asset('/assets') . '/'); ?>"
        data-base-url="<?php echo e(url('/')); ?>"
        data-framework="laravel"
        data-template="vertical-menu-laravel-template-free"
`
>


<head>


  <style>
    body {
      font-family: 'Cairo', sans-serif;
      overflow-x: hidden !important;
    }
  </style>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

  <title><?php echo $__env->yieldContent('title'); ?> <?php echo e(trns("Matrex")); ?> </title>
  <meta name="description" content="<?php echo e(config('variables.templateDescription') ? config('variables.templateDescription') : ''); ?>" />
  <meta name="keywords" content="<?php echo e(config('variables.templateKeyword') ? config('variables.templateKeyword') : ''); ?>">
  <!-- laravel CRUD token -->
  <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
  <!-- Canonical SEO -->
  <link rel="canonical" href="<?php echo e(config('variables.productPage') ? config('variables.productPage') : ''); ?>">
  <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo e(asset('assets/img/kaiadmin/icon.png')); ?>" />
    <!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <!-- CSS خاص بالـ RTL لو اللغة عربية -->
    <?php if(app()->getLocale() == 'ar'): ?>
        <link rel="stylesheet" href="<?php echo e(asset('assets/css/demo-rtl.css')); ?>">
    <?php endif; ?>

  <!-- Include Styles -->
  <?php echo $__env->make('layouts/sections/styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

  <!-- Include Scripts for customizer, helper, analytics, config -->
  <?php echo $__env->make('layouts/sections/scriptsIncludes', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</head>
<?php echo $__env->yieldPushContent('scripts'); ?> <!-- أضف هذا السطر هنا! -->

<body>
  

  <!-- Layout Content -->
  <?php echo $__env->yieldContent('layoutContent'); ?>
  <!--/ Layout Content -->

  

  <!-- Include Scripts -->
  <?php echo $__env->make('layouts/sections/scripts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>



  <?php $__env->startPush('scripts'); ?>
<!-- Pusher JS -->
<script src="https://js.pusher.com/8.2/pusher.min.js"></script>
<script>
    // Enable pusher logging - remove in production
    Pusher.logToConsole = true;

    var pusher = new Pusher('<?php echo e(env("PUSHER_APP_KEY")); ?>', {
      cluster: '<?php echo e(env("PUSHER_APP_CLUSTER")); ?>',
      encrypted: true
    });

    var channel = pusher.subscribe('my-channel');
    channel.bind('my-event', function(data) {
      toastr.info(data.message);
      console.log(data);
    });
</script>
<?php $__env->stopPush(); ?>

</body>

</html>
<?php /**PATH D:\own\dental\resources\views/layouts/commonMaster.blade.php ENDPATH**/ ?>