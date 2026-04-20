<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <link rel="icon" type="image/x-icon" href="<?php echo e(asset('web/img/favicon.ico')); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <style>
        /* Google font - Work Sans */
        @import url('https://fonts.googleapis.com/css2?family=Work+Sans:ital,wght@0,100..900;1,100..900&display=swap');
    </style>

    <!-- Profile template styles -->
    <link href="<?php echo e(asset('web/css/bootstrap/bootstrap.min.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('web/font-awesome/css/font-awesome.min.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('web/css/devicons/css/devicons.min.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('web/css/simple-line-icons/css/simple-line-icons.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('web/css/style.css')); ?>" rel="stylesheet">

    <!-- Site overrides -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/web.css')); ?>">

</head>

<body class="">
    <?php echo $__env->yieldContent('content'); ?>
    <!-- Profile template scripts -->
    <script src="<?php echo e(asset('web/js/jquery/jquery.min.js')); ?>"></script>
    <script src="<?php echo e(asset('web/js/bootstrap/bootstrap.min.js')); ?>"></script>
    <script src="<?php echo e(asset('web/js/jquery-easing/jquery.easing.min.js')); ?>"></script>
    <script src="<?php echo e(asset('web/js/custom.js')); ?>"></script>

    <script src="<?php echo e(asset('assets/js/web.js')); ?>" defer></script>
    <script type="module" src="https://unpkg.com/cally"></script>
</body>

</html>
<?php /**PATH D:\projects\dental\resources\views/web/layouts/app.blade.php ENDPATH**/ ?>