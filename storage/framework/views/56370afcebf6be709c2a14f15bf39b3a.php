<?php $__env->startSection('title', 'AI Dental Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <!-- Main Welcome & Alerts Col -->
    <div class="col-lg-12 col-md-12 order-0 mb-4">
        <div class="card bg-label-primary border-0 shadow-sm">
            <div class="d-flex align-items-end row">
                <div class="col-sm-7">
                    <div class="card-body">
                        <h5 class="card-title text-primary fw-bold">Welcome back, Administrator! 🎉</h5>
                        <p class="mb-4">
                            The AI Dental clinic system is running smoothly. We detected <span class="fw-bold"><?php echo e($infection_count); ?> critical infection cases</span> that require immediate attention.
                        </p>
                        <a href="<?php echo e(route('patients.index')); ?>" class="btn btn-sm btn-primary">Manage Patients</a>
                    </div>
                </div>
                <div class="col-sm-5 text-center text-sm-left">
                    <div class="card-body pb-0 px-0 px-md-4">
                        <img src="<?php echo e(asset('assets/img/illustrations/man-with-laptop-light.png')); ?>" height="140" alt="View Badge User" data-app-dark-img="illustrations/man-with-laptop-dark.png" data-app-light-img="illustrations/man-with-laptop-light.png">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- KPI Cards Row -->
<div class="row mb-4">
    <!-- Total Patients -->
    <div class="col-lg-2 col-md-4 col-sm-6 mb-4">
        <div class="card text-center h-100 shadow-sm border-0">
            <div class="card-body">
                <div class="badge p-3 bg-label-primary rounded-circle mb-3">
                    <i class="bx bx-group fs-3"></i>
                </div>
                <span class="d-block mb-1 text-muted">Total Patients</span>
                <h3 class="card-title mb-2 fw-bold text-dark"><?php echo e($total_patients); ?></h3>
                <small class="text-success fw-semibold"><i class="bx bx-up-arrow-alt"></i> Active records</small>
            </div>
        </div>
    </div>

    <!-- Active Doctors -->
    <div class="col-lg-2 col-md-4 col-sm-6 mb-4">
        <div class="card text-center h-100 shadow-sm border-0">
            <div class="card-body">
                <div class="badge p-3 bg-label-info rounded-circle mb-3">
                    <i class="bx bx-plus-medical fs-3"></i>
                </div>
                <span class="d-block mb-1 text-muted">Doctors</span>
                <h3 class="card-title mb-2 fw-bold text-dark"><?php echo e($total_doctors); ?></h3>
                <small class="text-success fw-semibold"><i class="bx bx-check"></i> On duty</small>
            </div>
        </div>
    </div>

    <!-- Healthy Cases -->
    <div class="col-lg-2 col-md-4 col-sm-6 mb-4">
        <div class="card text-center h-100 shadow-sm border-0 bg-label-success">
            <div class="card-body">
                <div class="badge p-3 bg-success rounded-circle mb-3 text-white">
                    <i class="bx bx-smile fs-3"></i>
                </div>
                <span class="d-block mb-1 text-success font-weight-bold">Healthy</span>
                <h3 class="card-title mb-2 fw-bold text-success"><?php echo e($healthy_count); ?></h3>
                <small class="text-success fw-semibold">No issues detected</small>
            </div>
        </div>
    </div>

    <!-- Cavity Cases -->
    <div class="col-lg-2 col-md-4 col-sm-6 mb-4">
        <div class="card text-center h-100 shadow-sm border-0 bg-label-warning">
            <div class="card-body">
                <div class="badge p-3 bg-warning rounded-circle mb-3 text-white">
                    <i class="bx bx-meh fs-3"></i>
                </div>
                <span class="d-block mb-1 text-warning font-weight-bold">Cavity</span>
                <h3 class="card-title mb-2 fw-bold text-warning"><?php echo e($cavity_count); ?></h3>
                <small class="text-warning fw-semibold">Mild risk</small>
            </div>
        </div>
    </div>

    <!-- Infection Cases -->
    <div class="col-lg-2 col-md-4 col-sm-6 mb-4">
        <div class="card text-center h-100 shadow-sm border-0 bg-label-danger">
            <div class="card-body">
                <div class="badge p-3 bg-danger rounded-circle mb-3 text-white">
                    <i class="bx bx-sad fs-3"></i>
                </div>
                <span class="d-block mb-1 text-danger font-weight-bold">Infection</span>
                <h3 class="card-title mb-2 fw-bold text-danger"><?php echo e($infection_count); ?></h3>
                <small class="text-danger fw-semibold">High risk / Critical</small>
            </div>
        </div>
    </div>

    <!-- Weather Widget (Fallback) -->
    <div class="col-lg-2 col-md-4 col-sm-6 mb-4">
        <div class="card text-center h-100 shadow-sm border-0">
            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                <div class="badge p-3 bg-label-secondary rounded-circle mb-2">
                    <i class="bx bx-sun fs-3"></i>
                </div>
                <span class="d-block text-muted small">Clinic Area</span>
                <h5 class="mb-0 fw-bold mt-1 text-dark">Egypt, Cairo</h5>
                <small class="text-muted">Clinic Live Feed</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Charts Column Left (Line chart: Daily Patients & Bar Chart: Patients by Doctor) -->
    <div class="col-lg-8 col-md-12 mb-4">
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 fw-bold"><i class="bx bx-line-chart me-1 text-primary"></i> Daily Patients Registration</h5>
                <small class="text-muted">Patients registered per day</small>
            </div>
            <div class="card-body">
                <canvas id="dailyPatientsChart" style="max-height: 250px;"></canvas>
            </div>
        </div>

        <div class="row">
            <!-- Cases Distribution Chart -->
            <div class="col-md-6 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-header">
                        <h5 class="card-title mb-0 fw-bold"><i class="bx bx-pie-chart-alt-2 me-1 text-success"></i> Cases Diagnosis</h5>
                    </div>
                    <div class="card-body d-flex align-items-center justify-content-center">
                        <canvas id="casesDistributionChart" style="max-height: 200px; max-width: 200px;"></canvas>
                    </div>
                </div>
            </div>

            <!-- Patients by Doctor -->
            <div class="col-md-6 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-header">
                        <h5 class="card-title mb-0 fw-bold"><i class="bx bx-bar-chart-alt-2 me-1 text-info"></i> Patients by Doctor</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="patientsByDoctorChart" style="max-height: 200px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Activity & Alerts Column Right -->
    <div class="col-lg-4 col-md-12 mb-4">
        <!-- Critical Alerts -->
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-danger text-white d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0 text-white fw-bold"><i class="bx bx-error-circle me-1"></i> Critical Alerts</h5>
                <span class="badge bg-white text-danger fw-bold"><?php echo e(count($alerts)); ?> cases</span>
            </div>
            <div class="card-body pt-3" style="max-height: 250px; overflow-y: auto;">
                <?php if(count($alerts) > 0): ?>
                    <ul class="list-group list-group-flush">
                        <?php $__currentLoopData = $alerts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $alert): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-bottom">
                                <div>
                                    <h6 class="mb-0 fw-bold text-dark"><?php echo e($alert['name']); ?></h6>
                                    <small class="text-muted">Doctor: <?php echo e($alert['doctor']); ?></small>
                                </div>
                                <span class="badge bg-danger rounded-pill"><?php echo e($alert['result']); ?></span>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="bx bx-badge-check text-success fs-1 mb-2"></i>
                        <p class="mb-0 text-muted">No critical cases detected!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Activity Feed -->
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0 fw-bold"><i class="bx bx-history me-1 text-info"></i> Recent Activity</h5>
                <small class="text-muted">Latest actions</small>
            </div>
            <div class="card-body" style="max-height: 350px; overflow-y: auto;">
                <?php if(count($recent_activities) > 0): ?>
                    <ul class="list-unstyled mb-0">
                        <?php $__currentLoopData = $recent_activities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $act): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="d-flex mb-3 align-items-start pb-2 border-bottom">
                                <div class="avatar flex-shrink-0 me-3">
                                    <span class="avatar-initial rounded bg-label-<?php echo e($act['type'] === 'doctor_deleted' || $act['type'] === 'patient_deleted' ? 'danger' : 'success'); ?>">
                                        <i class="bx <?php echo e($act['type'] === 'doctor_added' || $act['type'] === 'doctor_deleted' ? 'bx-plus-medical' : 'bx-user'); ?>"></i>
                                    </span>
                                </div>
                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                    <div class="me-2">
                                        <p class="mb-0 text-dark small fw-bold"><?php echo e($act['description']); ?></p>
                                        <small class="text-muted"><?php echo e($act['time']); ?></small>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                <?php else: ?>
                    <div class="text-center py-4">
                        <p class="mb-0 text-muted">No recent activity logged.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<!-- Load Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Daily Patients Chart
    const dailyCtx = document.getElementById('dailyPatientsChart').getContext('2d');
    new Chart(dailyCtx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($charts['daily_patients']['labels']); ?>,
            datasets: [{
                label: 'Registered Patients',
                data: <?php echo json_encode($charts['daily_patients']['data']); ?>,
                borderColor: '#696cff',
                backgroundColor: 'rgba(105, 108, 255, 0.1)',
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#696cff',
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                }
            }
        }
    });

    // 2. Cases Distribution Chart
    const casesCtx = document.getElementById('casesDistributionChart').getContext('2d');
    new Chart(casesCtx, {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode($charts['cases_distribution']['labels']); ?>,
            datasets: [{
                data: <?php echo json_encode($charts['cases_distribution']['data']); ?>,
                backgroundColor: ['#28a745', '#ffc107', '#dc3545'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { boxWidth: 12, padding: 8 } }
            }
        }
    });

    // 3. Patients by Doctor Chart
    const docCtx = document.getElementById('patientsByDoctorChart').getContext('2d');
    new Chart(docCtx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($charts['patients_by_doctor']['labels']); ?>,
            datasets: [{
                label: 'Patients per Doctor',
                data: <?php echo json_encode($charts['patients_by_doctor']['data']); ?>,
                backgroundColor: '#03c3ec',
                borderRadius: 4,
                barThickness: 20
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                }
            }
        }
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts/contentNavbarLayout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\projects\dental\resources\views/content/dashboard/dashboards-analytics.blade.php ENDPATH**/ ?>