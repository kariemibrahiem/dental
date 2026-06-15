<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <!-- ! Hide app brand if navbar-full -->
    <div class="app-brand demo">
        <a href="{{ url('/') }}" class="app-brand-link">
            <span class="app-brand-logo demo">
                @include('_partials.macros', ['width' => 25, 'withbg' => 'var(--bs-primary)'])
            </span>
            <span class="app-brand-text demo menu-text fw-bold ms-2">{{ config('variables.templateName') }}</span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    @php
        $menuData = include resource_path('views/layouts/sections/menu/verticalMenu.php');
        $adminUser = \Illuminate\Support\Facades\Auth::guard('admin')->user();
        $adminCanAccess = function ($item) use ($adminUser) {
            if (!$adminUser || !isset($item->permissions)) {
                return false;
            }

            return $adminUser->hasRole('super_admin') || $adminUser->can($item->permissions);
        };
    @endphp

    <div class="menu-inner-shadow"></div>
    <ul class="menu-inner py-1 mb-4" style="margin-bottom: 50px">

        @foreach ($menuData as $menu)
            {{-- menu headers --}}
            @if (isset($menu->menuHeader))
                <li class="menu-header small text-uppercase">
                    <span class="menu-header-text">{{ trns($menu->menuHeader) }}</span>
                </li>
            @else
                @php
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
                @endphp

                @if ($menu->permissions === 'dashboard' || $adminCanAccess($menu))
                    <li class="menu-item {{ $activeClass }}">
                        <a href="{{ isset($menu->url) && Route::has($menu->url) ? route($menu->url) : 'javascript:void(0);' }}"
                            class="{{ isset($menu->submenu) ? 'menu-link menu-toggle' : 'menu-link' }}"
                            @if (!empty($menu->target)) target="_blank" @endif>
                            @isset($menu->icon)
                                <i class="{{ $menu->icon }}"></i>
                            @endisset
                            <div>{{ trns($menu->name) }}</div>
                            @isset($menu->badge)
                                <div class="badge bg-{{ $menu->badge[0] }} rounded-pill ms-auto">{{ $menu->badge[1] }}</div>
                            @endisset
                        </a>

                        @isset($menu->submenu)
                            <ul class="menu-sub">
                                @foreach ($menu->submenu as $submenu)
                                    @php
                                        $subActiveClass =
                                            $currentRouteName === $submenu->url ||
                                            str_starts_with($currentRouteName, $submenu->slug)
                                                ? 'active'
                                                : '';
                                    @endphp
                                    @if ($adminCanAccess($submenu))
                                        <li class="menu-item {{ $subActiveClass }}">
                                            <a href="{{ Route::has($submenu->url) ? route($submenu->url) : 'javascript:void(0);' }}" class="menu-link">
                                                <div>{{ trns($submenu->name) }}</div>
                                            </a>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        @endisset
                    </li>
                @endif

            @endisset
        @endforeach
        <li style="margin-bottom: 50px">

        </li>
</ul>
</aside>
