

<?php $__env->startSection('title', trns("Dashboard")); ?>

<?php $__env->startSection('vendor-style'); ?>
<link rel="stylesheet" href="<?php echo e(asset('assets/vendor/libs/apex-charts/apex-charts.css')); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('vendor-script'); ?>
<script src="<?php echo e(asset('assets/vendor/libs/apex-charts/apexcharts.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-script'); ?>
<script src="<?php echo e(asset('assets/js/dashboards-analytics.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<style>
  .layout-page{
    overflow-x: hidden !important;
  }
  .layout-wrapper{
    overflow-x: hidden !important;
  }
  
  .analytics-dashboard-skin {
    background: #f8f9fa;
  }
  
  .kpi-card {
    border: none;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
    padding: 24px;
    margin-bottom: 16px;
  }
  
  .kpi-card-mint {
    background: #e8f5f0;
  }
  
  .kpi-card-sky {
    background: #e0f4ff;
  }
  
  .kpi-card-cyan {
    background: #d0f4ff;
  }
  
  .kpi-card-yellow {
    background: #fff9e6;
  }
  
  .kpi-number {
    font-size: 36px;
    font-weight: 700;
    color: #1a1a1a;
    margin: 12px 0;
  }
  
  .kpi-label {
    font-size: 12px;
    color: #666;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 8px;
  }
  
  .kpi-subtext {
    font-size: 13px;
    color: #0da574;
    font-weight: 500;
  }
  
  .kpi-icon {
    font-size: 24px;
    margin-bottom: 8px;
  }
  
  .table-project {
    font-size: 13px;
  }
  
  .progress-bar-thin {
    height: 6px;
    border-radius: 3px;
  }
  
  .status-badge {
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 500;
    display: inline-block;
  }
  
  .status-on-track {
    background: #e0f4ff;
    color: #0da574;
  }
  
  .status-delayed {
    background: #ffe0e0;
    color: #d32f2f;
  }
  
  .status-at-risk {
    background: #fff9e6;
    color: #f0ad4e;
  }
  
  .status-completed {
    background: #e8f5f0;
    color: #0da574;
  }
</style>

<div class="analytics-dashboard-skin">
<div class="row">
  <div class="col-lg-8 mb-4 order-0">
    <!-- KPI Cards Row 1 -->
    <div class="row g-3 mb-4">
      <div class="col-md-6">
        <div class="kpi-card kpi-card-mint">
          <div class="d-flex justify-content-between align-items-flex-start">
            <div>
              <div class="kpi-label"><?php echo e(trns('Total Projects')); ?></div>
              <div class="kpi-number">6</div>
              <div class="kpi-subtext">2 Completed</div>
            </div>
            <div class="kpi-icon">💼</div>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="kpi-card kpi-card-sky">
          <div class="d-flex justify-content-between align-items-flex-start">
            <div>
              <div class="kpi-label"><?php echo e(trns('Task')); ?></div>
              <div class="kpi-number">132</div>
              <div class="kpi-subtext">28 Completed</div>
            </div>
            <div class="kpi-icon">📋</div>
          </div>
        </div>
      </div>
    </div>
    
    <div class="row g-3 mb-4">
      <div class="col-md-6">
        <div class="kpi-card kpi-card-yellow">
          <div class="d-flex justify-content-between align-items-flex-start">
            <div>
              <div class="kpi-label"><?php echo e(trns('Members')); ?></div>
              <div class="kpi-number">8</div>
              <div class="kpi-subtext">2 Completed</div>
            </div>
            <div class="kpi-icon">👥</div>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="kpi-card kpi-card-mint">
          <div class="d-flex justify-content-between align-items-flex-start">
            <div>
              <div class="kpi-label"><?php echo e(trns('Productivity')); ?></div>
              <div class="kpi-number">76%</div>
              <div class="kpi-subtext">26% Increased</div>
            </div>
            <div class="kpi-icon">🎯</div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Active Projects Table -->
    <div class="card" style="border: none; border-radius: 12px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);">
      <div class="card-body p-4">
        <h5 class="mb-4" style="font-size: 16px; font-weight: 600; color: #1a1a1a;">Active Projects</h5>
        
        <div class="table-responsive">
          <table class="table table-borderless table-project">
            <thead>
              <tr style="border-bottom: 1px solid #e0e0e0;">
                <th style="color: #666; font-weight: 600; font-size: 12px; text-transform: uppercase;"><?php echo e(trns('Name')); ?></th>
                <th style="color: #666; font-weight: 600; font-size: 12px; text-transform: uppercase;"><?php echo e(trns('Progress')); ?></th>
                <th style="color: #666; font-weight: 600; font-size: 12px; text-transform: uppercase;"><?php echo e(trns('Status')); ?></th>
                <th style="color: #666; font-weight: 600; font-size: 12px; text-transform: uppercase;"><?php echo e(trns('Assigned')); ?></th>
                <th style="color: #666; font-weight: 600; font-size: 12px; text-transform: uppercase;"><?php echo e(trns('Actions')); ?></th>
              </tr>
            </thead>
            <tbody>
              <tr style="border-bottom: 1px solid #f0f0f0;">
                <td style="padding: 16px 0;">
                  <div style="font-weight: 500; color: #1a1a1a;">Website Redesign</div>
                  <div style="font-size: 11px; color: #999;">Jan 30, 2025</div>
                </td>
                <td style="padding: 16px 0;">
                  <div style="max-width: 80px;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                      <div style="width: 100%; background: #f0f0f0; border-radius: 3px; height: 6px;">
                        <div style="width: 65%; background: #00bcd4; height: 6px; border-radius: 3px;"></div>
                      </div>
                      <span style="font-size: 12px; color: #666;">65%</span>
                    </div>
                  </div>
                </td>
                <td style="padding: 16px 0;">
                  <span class="status-badge status-on-track">On Track</span>
                </td>
                <td style="padding: 16px 0;">
                  <div style="display: flex; margin-left: -8px;">
                    <img src="<?php echo e(asset('assets/img/avatars/1.png')); ?>" alt="User" style="width: 32px; height: 32px; border-radius: 50%; border: 2px solid white; margin-left: -8px; background: #c3e9ed;">
                    <img src="<?php echo e(asset('assets/img/avatars/2.png')); ?>" alt="User" style="width: 32px; height: 32px; border-radius: 50%; border: 2px solid white; margin-left: -8px; background: #d4f1d4;">
                  </div>
                </td>
                <td style="padding: 16px 0;">
                  <i class="bx bx-dots-vertical-rounded" style="cursor: pointer;"></i>
                </td>
              </tr>
              
              <tr style="border-bottom: 1px solid #f0f0f0;">
                <td style="padding: 16px 0;">
                  <div style="font-weight: 500; color: #1a1a1a;">Marketing Campaign</div>
                  <div style="font-size: 11px; color: #999;">Feb 10, 2025</div>
                </td>
                <td style="padding: 16px 0;">
                  <div style="max-width: 80px;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                      <div style="width: 100%; background: #f0f0f0; border-radius: 3px; height: 6px;">
                        <div style="width: 20%; background: #ff6b6b; height: 6px; border-radius: 3px;"></div>
                      </div>
                      <span style="font-size: 12px; color: #666;">20%</span>
                    </div>
                  </div>
                </td>
                <td style="padding: 16px 0;">
                  <span class="status-badge status-delayed">Delayed</span>
                </td>
                <td style="padding: 16px 0;">
                  <div style="display: flex; margin-left: -8px;">
                    <img src="<?php echo e(asset('assets/img/avatars/3.png')); ?>" alt="User" style="width: 32px; height: 32px; border-radius: 50%; border: 2px solid white; margin-left: -8px; background: #ffe0e0;">
                  </div>
                </td>
                <td style="padding: 16px 0;">
                  <i class="bx bx-dots-vertical-rounded" style="cursor: pointer;"></i>
                </td>
              </tr>
              
              <tr style="border-bottom: 1px solid #f0f0f0;">
                <td style="padding: 16px 0;">
                  <div style="font-weight: 500; color: #1a1a1a;">Mobile App Development</div>
                  <div style="font-size: 11px; color: #999;">Mar 1, 2025</div>
                </td>
                <td style="padding: 16px 0;">
                  <div style="max-width: 80px;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                      <div style="width: 100%; background: #f0f0f0; border-radius: 3px; height: 6px;">
                        <div style="width: 45%; background: #ffc107; height: 6px; border-radius: 3px;"></div>
                      </div>
                      <span style="font-size: 12px; color: #666;">45%</span>
                    </div>
                  </div>
                </td>
                <td style="padding: 16px 0;">
                  <span class="status-badge status-at-risk">At Risk</span>
                </td>
                <td style="padding: 16px 0;">
                  <div style="display: flex; margin-left: -8px;">
                    <img src="<?php echo e(asset('assets/img/avatars/4.png')); ?>" alt="User" style="width: 32px; height: 32px; border-radius: 50%; border: 2px solid white; margin-left: -8px; background: #fff9e6;">
                  </div>
                </td>
                <td style="padding: 16px 0;">
                  <i class="bx bx-dots-vertical-rounded" style="cursor: pointer;"></i>
                </td>
              </tr>
              
              <tr style="border-bottom: 1px solid #f0f0f0;">
                <td style="padding: 16px 0;">
                  <div style="font-weight: 500; color: #1a1a1a;">Customer Portal Upgrade</div>
                  <div style="font-size: 11px; color: #999;">Feb 15, 2025</div>
                </td>
                <td style="padding: 16px 0;">
                  <div style="max-width: 80px;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                      <div style="width: 100%; background: #f0f0f0; border-radius: 3px; height: 6px;">
                        <div style="width: 89%; background: #00bcd4; height: 6px; border-radius: 3px;"></div>
                      </div>
                      <span style="font-size: 12px; color: #666;">89%</span>
                    </div>
                  </div>
                </td>
                <td style="padding: 16px 0;">
                  <span class="status-badge status-on-track">On Track</span>
                </td>
                <td style="padding: 16px 0;">
                  <div style="display: flex; margin-left: -8px;">
                    <img src="<?php echo e(asset('assets/img/avatars/5.png')); ?>" alt="User" style="width: 32px; height: 32px; border-radius: 50%; border: 2px solid white; margin-left: -8px; background: #d4f1d4;">
                  </div>
                </td>
                <td style="padding: 16px 0;">
                  <i class="bx bx-dots-vertical-rounded" style="cursor: pointer;"></i>
                </td>
              </tr>
              
              <tr>
                <td style="padding: 16px 0;">
                  <div style="font-weight: 500; color: #1a1a1a;">Product Launch</div>
                  <div style="font-size: 11px; color: #999;">Jan 29, 2025</div>
                </td>
                <td style="padding: 16px 0;">
                  <div style="max-width: 80px;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                      <div style="width: 100%; background: #f0f0f0; border-radius: 3px; height: 6px;">
                        <div style="width: 100%; background: #4caf50; height: 6px; border-radius: 3px;"></div>
                      </div>
                      <span style="font-size: 12px; color: #666;">100%</span>
                    </div>
                  </div>
                </td>
                <td style="padding: 16px 0;">
                  <span class="status-badge status-completed">Completed</span>
                </td>
                <td style="padding: 16px 0;">
                  <div style="display: flex; margin-left: -8px;">
                    <img src="<?php echo e(asset('assets/img/avatars/6.png')); ?>" alt="User" style="width: 32px; height: 32px; border-radius: 50%; border: 2px solid white; margin-left: -8px; background: #e8f5f0;">
                  </div>
                </td>
                <td style="padding: 16px 0;">
                  <i class="bx bx-dots-vertical-rounded" style="cursor: pointer;"></i>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        
        <div style="text-align: center; padding: 16px 0;">
          <a href="#" style="color: #00bcd4; text-decoration: none; font-weight: 500; font-size: 13px;">View All Projects</a>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-4 col-md-4 order-1">
    <!-- Task Progress Card -->
    <div class="card mb-4" style="border: none; border-radius: 12px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);">
      <div class="card-body p-4">
        <h5 style="font-size: 16px; font-weight: 600; color: #1a1a1a; margin-bottom: 24px;"><?php echo e(trns('Task Progress')); ?></h5>
        
        <div style="text-align: center; margin-bottom: 24px;">
          <div style="font-size: 48px; font-weight: 700; color: #1a1a1a;">64%</div>
        </div>
        
        <div style="margin-bottom: 20px;">
          <div style="display: flex; gap: 4px; height: 6px; border-radius: 3px; overflow: hidden;">
            <div style="flex: 24%; background: #00bcd4;"></div>
            <div style="flex: 35%; background: #4caf50;"></div>
            <div style="flex: 41%; background: #ff9800;"></div>
          </div>
        </div>
        
        <div style="display: flex; justify-content: space-between; padding-top: 12px; border-top: 1px solid #f0f0f0;">
          <div style="text-align: center; flex: 1;">
            <div style="font-size: 12px; color: #999; margin-bottom: 4px;">24%</div>
          </div>
          <div style="text-align: center; flex: 1;">
            <div style="font-size: 12px; color: #999; margin-bottom: 4px;">35%</div>
          </div>
          <div style="text-align: center; flex: 1;">
            <div style="font-size: 12px; color: #999; margin-bottom: 4px;">41%</div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Stat Cards Row -->
    <div class="row g-3 mb-4">
      <div class="col-4">
        <div class="card" style="border: none; border-radius: 12px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08); text-align: center; padding: 20px;">
          <div style="width: 48px; height: 48px; border-radius: 50%; background: #e8f5f0; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px;">
            <i class="bx bx-check-circle" style="color: #0da574; font-size: 24px;"></i>
          </div>
          <div style="font-size: 20px; font-weight: 700; color: #1a1a1a; margin-bottom: 4px;">8</div>
          <div style="font-size: 11px; color: #999;">Completed</div>
        </div>
      </div>
      <div class="col-4">
        <div class="card" style="border: none; border-radius: 12px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08); text-align: center; padding: 20px;">
          <div style="width: 48px; height: 48px; border-radius: 50%; background: #e0f4ff; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px;">
            <i class="bx bx-loader-circle" style="color: #00bcd4; font-size: 24px;"></i>
          </div>
          <div style="font-size: 20px; font-weight: 700; color: #1a1a1a; margin-bottom: 4px;">12</div>
          <div style="font-size: 11px; color: #999;">In-Progress</div>
        </div>
      </div>
      <div class="col-4">
        <div class="card" style="border: none; border-radius: 12px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08); text-align: center; padding: 20px;">
          <div style="width: 48px; height: 48px; border-radius: 50%; background: #ffe0e0; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px;">
            <i class="bx bx-error-circle" style="color: #d32f2f; font-size: 24px;"></i>
          </div>
          <div style="font-size: 20px; font-weight: 700; color: #1a1a1a; margin-bottom: 4px;">14</div>
          <div style="font-size: 11px; color: #999;">Up Coming</div>
        </div>
      </div>
    </div>
    
    <!-- AI Assistant Card -->
    <div class="card mb-4" style="border: none; border-radius: 12px; background: linear-gradient(135deg, #0da574 0%, #00bcd4 100%); color: white; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12); padding: 24px; text-align: center;">
      <div style="font-size: 48px; margin-bottom: 16px;">🤖</div>
      <h5 style="font-size: 18px; font-weight: 600; margin-bottom: 12px; color: white;"><?php echo e(trns('How AI assist will')); ?><br><?php echo e(trns('help you')); ?>?</h5>
      <button class="btn" style="background: white; color: #0da574; font-weight: 600; padding: 8px 16px; border-radius: 6px; border: none; cursor: pointer; font-size: 12px;"><?php echo e(trns('Start AI')); ?></button>
    </div>
    
    <!-- Project Budget Card -->
    <div class="card" style="border: none; border-radius: 12px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);">
      <div class="card-body p-4">
        <div style="font-size: 12px; color: #999; text-transform: uppercase; margin-bottom: 8px;"><?php echo e(trns('Project Budget')); ?></div>
        <div style="font-size: 12px; color: #666; margin-bottom: 16px;">Budget Allocation Overview:</div>
        <div style="font-size: 32px; font-weight: 700; color: #1a1a1a;">$50,000</div>
      </div>
    </div>
  </div>

  <!--/ Total Revenue -->
  <div class="col-12 w-100 order-3 order-md-2">
    <div class="row w-100">
  <!-- Teachers -->
  <div class="col-3 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="card-title d-flex align-items-start justify-content-between">
          <div class="avatar flex-shrink-0">
            <i class="bx bx-chalkboard" style="font-size: 1.75rem;"></i>
          </div>
          <div class="dropdown-menu-end" >
              
            </div>
        </div>
        <span><?php echo e(trns("teachers")); ?></span>
        
      </div>
    </div>
  </div>

  <!-- Families -->
  <div class="col-3 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="card-title d-flex align-items-start justify-content-between">
          <div class="avatar flex-shrink-0">
            <i class="bx bx-user" style="font-size: 1.75rem;"></i>
          </div>
          <div class=" dropdown-menu-end">
              
            </div>
        </div>
        <span><?php echo e(trns("families")); ?></span>
        
      </div>
    </div>
  </div>

  <!-- Payments -->
  <div class="col-3 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="card-title d-flex align-items-start justify-content-between">
          <div class="avatar flex-shrink-0">
            <img src="<?php echo e(asset('assets/img/icons/unicons/paypal.png')); ?>" alt="Payments" class="rounded">
          </div>
          <div class=" dropdown-menu-end">
              
            </div>
        </div>
        <span class="d-block mb-1"><?php echo e(trns("Payments")); ?></span>
        
      </div>
    </div>
  </div>

  <!-- Transactions -->
  <div class="col-3 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="card-title d-flex align-items-start justify-content-between">
          <div class="avatar flex-shrink-0">
            <img src="<?php echo e(asset('assets/img/icons/unicons/cc-primary.png')); ?>" alt="Transactions" class="rounded">
          </div>
          <div class=" dropdown-menu-end">
              
            </div>
        </div>
        <span class="fw-semibold d-block mb-1"><?php echo e(trns("Transactions")); ?></span>
        
      </div>
    </div>
  </div>
</div>


    </div>
  </div>
</div>
<div class="row" <?php if(app()->getLocale() == 'ar'): ?> style="margin-top: 25rem;" <?php endif; ?>>
  <!-- Order Statistics -->
<div class="col-md-6 col-lg-4 col-xl-4 order-0 mb-4">
  <div class="card h-100">
    <div class="card-header d-flex align-items-center justify-content-between pb-0">
      <div class="card-title mb-0">
        <h5 class="m-0 me-2"><?php echo e(trns("Order Statistics")); ?></h5>
        
      </div>
    </div>
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="d-flex flex-column align-items-center gap-1">
          
          <span><?php echo e(trns("Total Orders completed")); ?></span>
        </div>
        <canvas id="ordersChart" style="max-width: 150px; max-height: 150px;"></canvas>

      </div>

      <ul class="p-0 m-0">
        <!-- Pending -->
        <li class="d-flex mb-4 pb-1">
          <div class="avatar flex-shrink-0 me-3">
            <span class="avatar-initial rounded bg-label-primary">
              <i class='bx bx-time-five'></i>

            </span>
          </div>
          <div class="d-flex w-100 align-items-center justify-content-between flex-nowrap gap-2">
            <div class="me-2 flex-grow-1 text-truncate">
              <h6 class="mb-0"><?php echo e(trns("pending")); ?></h6>
              <small class="text-muted"><?php echo e(trns("orders waiting for payment")); ?></small>
            </div>
            <div class="user-progress flex-shrink-0">
              
            </div>
          </div>
        </li>

        <!-- Refunded -->
        <li class="d-flex mb-4 pb-1">
          <div class="avatar flex-shrink-0 me-3">
            <span class="avatar-initial rounded bg-label-success">
              <i class='bx bx-undo'></i>

            </span>
          </div>
          <div class="d-flex w-100 align-items-center justify-content-between flex-nowrap gap-2">
            <div class="me-2 flex-grow-1 text-truncate">
              <h6 class="mb-0"><?php echo e(trns("refunded")); ?></h6>
              <small class="text-muted"><?php echo e(trns("orders that have been refunded")); ?></small>
            </div>
            <div class="user-progress flex-shrink-0">
              
            </div>
          </div>
        </li>

        <!-- Cancelled -->
        <li class="d-flex mb-4 pb-1">
          <div class="avatar flex-shrink-0 me-3">
            <span class="avatar-initial rounded bg-label-info"><i class='bx bx-x-circle'></i>
</span>
          </div>
          <div class="d-flex w-100 align-items-center justify-content-between flex-nowrap gap-2">
            <div class="me-2 flex-grow-1 text-truncate">
              <h6 class="mb-0"><?php echo e(trns("cancelled")); ?></h6>
              <small class="text-muted"><?php echo e(trns("orders that have been cancelled")); ?></small>
            </div>
            <div class="user-progress flex-shrink-0">
              
            </div>
          </div>
        </li>
      </ul>
    </div>
  </div>
</div>
<!--/ Order Statistics -->




<!-- Expense Overview -->

<!--/ Expense Overview -->



  <!-- Transactions -->
  <!-- Transactions -->
<div class="col-md-6 col-lg-4 order-2 mb-4">
  <div class="card h-100">
    <div class="card-header d-flex align-items-center justify-content-between">
      <h5 class="card-title m-0 me-2"><?php echo e(trns("orders")); ?></h5>
      
    </div>

    <div class="card-body">
      <!-- Single icon at the top -->
      <div class="d-flex p-4 pt-3">
        <div class="avatar flex-shrink-0 me-3">
          <img src="<?php echo e(asset('assets/img/icons/unicons/cc-success.png')); ?>" alt="Transactions" class="rounded">
        </div>
        <div>
          <small class="text-muted d-block"><?php echo e(trns('Last 5 Orders')); ?></small>
        </div>
      </div>

      <ul class="list-unstyled mb-0">
         
      </ul>
    </div>
  </div>
</div>




  <!-- Transactions -->
  <!-- Transactions -->
<div class="col-md-6 col-lg-4 order-2 mb-4">
  <div class="card h-100">
    <div class="card-header d-flex align-items-center justify-content-between">
      <h5 class="card-title m-0 me-2"><?php echo e(trns("Transactions")); ?></h5>
      
    </div>

    <div class="card-body">
      <!-- Single icon at the top -->
      <div class="d-flex p-4 pt-3">
        <div class="avatar flex-shrink-0 me-3">
          <img src="<?php echo e(asset('assets/img/icons/unicons/cc-success.png')); ?>" alt="Transactions" class="rounded">
        </div>
        <div>
          <small class="text-muted d-block"><?php echo e(trns('Last 5 Transactions')); ?></small>
        </div>
      </div>

      <ul class="list-unstyled mb-0">
        
      </ul>
    </div>
  </div>
</div>


  <!--/ Transactions -->
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/contentNavbarLayout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\projects\dental\resources\views/content/dashboard/dashboards-analytics.blade.php ENDPATH**/ ?>