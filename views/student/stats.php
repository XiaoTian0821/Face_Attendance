<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm"><div class="card-body text-center">
            <div class="fs-2 fw-bold text-primary"><?php echo (int)$stats['total']; ?></div>
            <small class="text-muted">Total Days</small>
        </div></div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm"><div class="card-body text-center">
            <div class="fs-2 fw-bold text-success"><?php echo (int)$stats['present']; ?></div>
            <small class="text-muted">Present</small>
        </div></div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm"><div class="card-body text-center">
            <div class="fs-2 fw-bold text-warning"><?php echo (int)$stats['late']; ?></div>
            <small class="text-muted">Late</small>
        </div></div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm"><div class="card-body text-center">
            <div class="fs-2 fw-bold text-info"><?php echo (int)$stats['leave']; ?></div>
            <small class="text-muted">On Leave</small>
        </div></div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <h6 class="mb-3">Attendance Percentage</h6>
        <div class="progress" style="height:28px">
            <div class="progress-bar bg-success" style="width:<?php echo $stats['percentage']; ?>%">
                <?php echo $stats['percentage']; ?>%
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white"><h6 class="mb-0">Monthly Breakdown (Last 6 Months)</h6></div>
    <div class="card-body">
        <canvas id="statsChart" style="max-height:250px"></canvas>
        <?php if (empty($monthlyData)): ?>
            <p class="text-center text-muted py-3">No monthly data available.</p>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
<?php if (!empty($monthlyData)): ?>
const mData = <?php echo json_encode($monthlyData); ?>;
const ctx = document.getElementById('statsChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: mData.map(r => r['month']),
        datasets: [
            { label: 'Present', data: mData.map(r => r['present_days']||0), backgroundColor: '#198754' },
            { label: 'Late',    data: mData.map(r => r['late_days']||0),     backgroundColor: '#ffc107' },
            { label: 'Leave',   data: mData.map(r => r['leave_days']||0),    backgroundColor: '#0dcaf0' }
        ]
    },
    options: {
        responsive: true,
        scales: { x: { stacked: true }, y: { stacked: true, beginAtZero: true } },
        plugins: { legend: { position: 'bottom' } }
    }
});
<?php endif; ?>
</script>
