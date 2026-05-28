<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\dashboard\Analytics;
use App\Http\Controllers\Admin\layouts\WithoutMenu;
use App\Http\Controllers\Admin\layouts\WithoutNavbar;
use App\Http\Controllers\Admin\layouts\Fluid;
use App\Http\Controllers\Admin\layouts\Container;
use App\Http\Controllers\Admin\layouts\Blank;
use App\Http\Controllers\Admin\pages\AccountSettingsAccount;
use App\Http\Controllers\Admin\pages\AccountSettingsNotifications;
use App\Http\Controllers\Admin\pages\AccountSettingsConnections;
use App\Http\Controllers\Admin\pages\MiscError;
use App\Http\Controllers\Admin\pages\MiscUnderMaintenance;
use App\Http\Controllers\Admin\authentications\LoginBasic;
use App\Http\Controllers\Admin\authentications\RegisterBasic;
use App\Http\Controllers\Admin\authentications\ForgotPasswordBasic;
use App\Http\Controllers\Admin\cards\CardBasic;
use App\Http\Controllers\Admin\user_interface\Accordion;
use App\Http\Controllers\Admin\user_interface\Alerts;
use App\Http\Controllers\Admin\user_interface\Badges;
use App\Http\Controllers\Admin\user_interface\Buttons;
use App\Http\Controllers\Admin\user_interface\Carousel;
use App\Http\Controllers\Admin\user_interface\Collapse;
use App\Http\Controllers\Admin\user_interface\Dropdowns;
use App\Http\Controllers\Admin\user_interface\Footer;
use App\Http\Controllers\Admin\user_interface\ListGroups;
use App\Http\Controllers\Admin\user_interface\Modals;
use App\Http\Controllers\Admin\user_interface\Navbar;
use App\Http\Controllers\Admin\user_interface\Offcanvas;
use App\Http\Controllers\Admin\user_interface\PaginationBreadcrumbs;
use App\Http\Controllers\Admin\user_interface\Progress;
use App\Http\Controllers\Admin\user_interface\Spinners;
use App\Http\Controllers\Admin\user_interface\TabsPills;
use App\Http\Controllers\Admin\user_interface\Toasts;
use App\Http\Controllers\Admin\user_interface\TooltipsPopovers;
use App\Http\Controllers\Admin\user_interface\Typography;
use App\Http\Controllers\Admin\extended_ui\PerfectScrollbar;
use App\Http\Controllers\Admin\extended_ui\TextDivider;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\Admin\icons\Boxicons;
use App\Http\Controllers\Admin\form_elements\BasicInput;
use App\Http\Controllers\Admin\form_elements\InputGroups;
use App\Http\Controllers\Admin\form_layouts\VerticalForm;
use App\Http\Controllers\Admin\form_layouts\HorizontalForm;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\Admin\tables\Basic as TablesBasic;
use App\Http\Controllers\Admin\UserController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

// (No public homepage added here; frontend routes live in routes/front.php)

// Main Page Route


Route::post("reset-password", [ForgotPasswordController::class, 'resetPassword'])->name("reset-password");


Route::get("check-otp", [ForgotPasswordController::class, 'showCheckOtp'])->name("show-check-otp");


Route::post("verify-otp", [ForgotPasswordController::class, 'CheckOtp'])->name("verify-otp");



Route::group(['prefix' => LaravelLocalization::setLocale()], function () {


    Route::group(["middleware" => "auth:admin"], function () {
        Route::get('/admin', [Analytics::class, 'index'])->name('dashboard-analytics');
        Route::get('change_language/{id}', [SettingController::class, 'changeLanguage'])->name('change_language');
        Route::post('/user/updateColumnSelected', [UserController::class, 'updateColumnSelected'])->name('users.updateColumnSelected');
        Route::resource("users", UserController::class);


        Route::get('/admins/profile', [\App\Http\Controllers\Admin\AdminController::class, 'profile'])->name('admins.profile');
        Route::resource('admins', \App\Http\Controllers\Admin\AdminController::class);
        Route::post('/admins/updateColumnSelected', [\App\Http\Controllers\Admin\AdminController::class, 'updateColumnSelected'])->name('admins.updateColumnSelected');
        Route::post('/admins/destroySelected', [\App\Http\Controllers\Admin\AdminController::class, 'deleteSelected'])->name('admins.destroySelected');


        Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
        Route::post('/users/updateColumnSelected', [\App\Http\Controllers\Admin\UserController::class, 'updateColumnSelected'])
            ->name('users.updateColumnSelected');
        Route::post('/users/destroySelected', [\App\Http\Controllers\Admin\UserController::class, 'deleteSelected'])
            ->name('users.destroySelected');

        Route::resource('Role', \App\Http\Controllers\Admin\RoleController::class);
        Route::post('/Role/updateColumnSelected', [\App\Http\Controllers\Admin\RoleController::class, 'updateColumnSelected'])
            ->name('Role.updateColumnSelected');
        Route::post('/Role/destroySelected', [\App\Http\Controllers\Admin\RoleController::class, 'destroySelected'])
            ->name('Role.destroySelected');



        Route::resource('doctors', \App\Http\Controllers\Admin\DoctorController::class);
        Route::post('/doctors/destroySelected', [\App\Http\Controllers\Admin\DoctorController::class, 'deleteSelected'])
            ->name('doctors.destroySelected');

        Route::resource('patients', \App\Http\Controllers\Admin\PatientController::class);
        Route::post('/patients/destroySelected', [\App\Http\Controllers\Admin\PatientController::class, 'deleteSelected'])
            ->name('patients.destroySelected');

        Route::resource('reports', \App\Http\Controllers\Admin\ReportController::class);
        Route::post('/reports/destroySelected', [\App\Http\Controllers\Admin\ReportController::class, 'destroySelected'])
            ->name('reports.destroySelected');


        Route::resource('settings', \App\Http\Controllers\Admin\SettingController::class);
    });

















    // layout
    Route::get('/layouts/without-menu', [WithoutMenu::class, 'index'])->name('layouts-without-menu');
    Route::get('/layouts/without-navbar', [WithoutNavbar::class, 'index'])->name('layouts-without-navbar');
    Route::get('/layouts/fluid', [Fluid::class, 'index'])->name('layouts-fluid');
    Route::get('/layouts/container', [Container::class, 'index'])->name('layouts-container');
    Route::get('/layouts/blank', [Blank::class, 'index'])->name('layouts-blank');

    // pages
    Route::get('/pages/account-settings-account', [AccountSettingsAccount::class, 'index'])->name('pages-account-settings-account');
    Route::get('/pages/account-settings-notifications', [AccountSettingsNotifications::class, 'index'])->name('pages-account-settings-notifications');
    Route::get('/pages/account-settings-connections', [AccountSettingsConnections::class, 'index'])->name('pages-account-settings-connections');
    Route::get('/pages/misc-error', [MiscError::class, 'index'])->name('pages-misc-error');
    Route::get('/pages/misc-under-maintenance', [MiscUnderMaintenance::class, 'index'])->name('pages-misc-under-maintenance');

    // authentication
    Route::get('/auth/login-basic', [LoginBasic::class, 'index'])->name('auth-login-basic');
    Route::get("admin.logout", [LoginBasic::class, "logout"])->name("admin.logout");
    Route::post('/auth/login', [LoginBasic::class, 'login'])->name('login');
    Route::get('/auth/register-basic', [RegisterBasic::class, 'index'])->name('auth-register-basic');
    Route::get('/auth/forgot-password-basic', [ForgotPasswordBasic::class, 'index'])->name('auth-reset-password-basic');

    // cards
    Route::get('/cards/basic', [CardBasic::class, 'index'])->name('cards-basic');

    // User Interface
    Route::get('/ui/accordion', [Accordion::class, 'index'])->name('ui-accordion');
    Route::get('/ui/alerts', [Alerts::class, 'index'])->name('ui-alerts');
    Route::get('/ui/badges', [Badges::class, 'index'])->name('ui-badges');
    Route::get('/ui/buttons', [Buttons::class, 'index'])->name('ui-buttons');
    Route::get('/ui/carousel', [Carousel::class, 'index'])->name('ui-carousel');
    Route::get('/ui/collapse', [Collapse::class, 'index'])->name('ui-collapse');
    Route::get('/ui/dropdowns', [Dropdowns::class, 'index'])->name('ui-dropdowns');
    Route::get('/ui/footer', [Footer::class, 'index'])->name('ui-footer');
    Route::get('/ui/list-groups', [ListGroups::class, 'index'])->name('ui-list-groups');
    Route::get('/ui/modals', [Modals::class, 'index'])->name('ui-modals');
    Route::get('/ui/navbar', [Navbar::class, 'index'])->name('ui-navbar');
    Route::get('/ui/offcanvas', [Offcanvas::class, 'index'])->name('ui-offcanvas');
    Route::get('/ui/pagination-breadcrumbs', [PaginationBreadcrumbs::class, 'index'])->name('ui-pagination-breadcrumbs');
    Route::get('/ui/progress', [Progress::class, 'index'])->name('ui-progress');
    Route::get('/ui/spinners', [Spinners::class, 'index'])->name('ui-spinners');
    Route::get('/ui/tabs-pills', [TabsPills::class, 'index'])->name('ui-tabs-pills');
    Route::get('/ui/toasts', [Toasts::class, 'index'])->name('ui-toasts');
    Route::get('/ui/tooltips-popovers', [TooltipsPopovers::class, 'index'])->name('ui-tooltips-popovers');
    Route::get('/ui/typography', [Typography::class, 'index'])->name('ui-typography');

    // extended ui
    Route::get('/extended/ui-perfect-scrollbar', [PerfectScrollbar::class, 'index'])->name('extended-ui-perfect-scrollbar');
    Route::get('/extended/ui-text-divider', [TextDivider::class, 'index'])->name('extended-ui-text-divider');

    // icons
    Route::get('/icons/boxicons', [Boxicons::class, 'index'])->name('icons-boxicons');

    // form elements
    Route::get('/forms/basic-inputs', [BasicInput::class, 'index'])->name('forms-basic-inputs');
    Route::get('/forms/input-groups', [InputGroups::class, 'index'])->name('forms-input-groups');

    // form layouts
    Route::get('/form/layouts-vertical', [VerticalForm::class, 'index'])->name('form-layouts-vertical');
    Route::get('/form/layouts-horizontal', [HorizontalForm::class, 'index'])->name('form-layouts-horizontal');

    // tables
    Route::get('/tables/basic', [TablesBasic::class, 'index'])->name('tables-basic');
});
