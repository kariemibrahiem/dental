<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <!-- ! Hide app brand if navbar-full -->
    <div class="app-brand demo">
        <a href="<?php echo e(url('/')); ?>" class="app-brand-link">
            <span class="app-brand-logo demo">
                <?php echo $__env->make('_partials.macros', ['width' => 25, 'withbg' => 'var(--bs-primary)'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </span>
            <span class="app-brand-text demo menu-text fw-bold ms-2"><?php echo e(config('variables.templateName')); ?></span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <?php
        $menuData = include resource_path('views/layouts/sections/menu/verticalMenu.php');
    ?>

    <div class="menu-inner-shadow"></div>
    <ul class="menu-inner py-1 mb-4" style="margin-bottom: 50px">

        <?php $__currentLoopData = $menuData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            
            <?php if(isset($menu->menuHeader)): ?>
                <li class="menu-header small text-uppercase">
                    <span class="menu-header-text"><?php echo e(trns($menu->menuHeader)); ?></span>
                </li>
            <?php else: ?>
                <?php
                    $currentRouteName = Route::currentRouteName();
                    $activeClass = '';

                    // Check top-level menu
                    if ($currentRouteName === $menu->url) {
                        $activeClass = 'active';
                    }

                    // Check if menu has submenu
                    if (isset($menu->submenu)) {
                        foreach ($menu->submenu as $submenu) {
                            if (
                                $currentRouteName === $submenu->url ||
                                str_starts_with($currentRouteName, $submenu->slug)
                            ) {
                                $activeClass = 'active open'; // top-level menu highlighted
                            }
                        }
                    }
                ?>

                <?php if($menu->permissions === 'dashboard' || (auth()->check() && auth()->user()->can($menu->permissions))): ?>
                    <li class="menu-item <?php echo e($activeClass); ?>">
                        <a href="<?php echo e(isset($menu->url) && Route::has($menu->url) ? route($menu->url) : 'javascript:void(0);'); ?>"
                            class="<?php echo e(isset($menu->submenu) ? 'menu-link menu-toggle' : 'menu-link'); ?>"
                            <?php if(!empty($menu->target)): ?> target="_blank" <?php endif; ?>>
                            <?php if(isset($menu->icon)): ?>
                                <i class="<?php echo e($menu->icon); ?>"></i>
                            <?php endif; ?>
                            <div><?php echo e(trns($menu->name)); ?></div>
                            <?php if(isset($menu->badge)): ?>
                                <div class="badge bg-<?php echo e($menu->badge[0]); ?> rounded-pill ms-auto"><?php echo e($menu->badge[1]); ?></div>
                            <?php endif; ?>
                        </a>

                        <?php if(isset($menu->submenu)): ?>
                            <ul class="menu-sub">
                                <?php $__currentLoopData = $menu->submenu; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $submenu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $subActiveClass =
                                            $currentRouteName === $submenu->url ||
                                            str_starts_with($currentRouteName, $submenu->slug)
                                                ? 'active'
                                                : '';
                                    ?>
                                    <?php if(auth()->user()->can($submenu->permissions)): ?>
                                        <li class="menu-item <?php echo e($subActiveClass); ?>">
                                            <a href="<?php echo e(Route::has($submenu->url) ? route($submenu->url) : 'javascript:void(0);'); ?>" class="menu-link">
                                                <div><?php echo e(trns($submenu->name)); ?></div>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                <?php endif; ?>

            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <li style="margin-bottom: 50px">

        </li>
</ul>
</aside>
<?php /**PATH D:\projects\dental\resources\views/layouts/sections/menu/verticalMenu.blade.php ENDPATH**/ ?>