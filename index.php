<?php
include 'includes/config.php';

// Analytics Queries
$doc_count = $conn->query("SELECT COUNT(*) as c FROM staff WHERE role='Doctor' AND status='Active'")->fetch_assoc()['c'];
$nurse_count = $conn->query("SELECT COUNT(*) as c FROM staff WHERE role='Nurse' AND status='Active'")->fetch_assoc()['c'];
$active_patients = $conn->query("SELECT COUNT(*) as c FROM admissions WHERE status='Admitted'")->fetch_assoc()['c'];

$bed_stats = $conn->query("SELECT SUM(total_beds) as total, SUM(available_beds) as avail FROM wards")->fetch_assoc();
$total_beds = $bed_stats['total'] ?? 0;
$avail_beds = $bed_stats['avail'] ?? 0;
$occupied_beds = $total_beds - $avail_beds;

$today_admissions = $conn->query("SELECT COUNT(*) as c FROM admissions WHERE admission_date = CURDATE()")->fetch_assoc()['c'];
$today_discharges = $conn->query("SELECT COUNT(*) as c FROM admissions WHERE discharge_date = CURDATE()")->fetch_assoc()['c'];

include 'includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-grid-1x2-fill text-primary me-2"></i>Executive Dashboard</h2>
    <span class="badge bg-primary fs-6"><?php echo date('F d, Y'); ?></span>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card stat-card bg-primary text-white p-3 shadow-sm">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-uppercase mb-1 opacity-75">Medical Staff</h6>
                    <h3 class="mb-0"><?php echo $doc_count; ?> Doctors / <?php echo $nurse_count; ?> Nurses</h3>
                </div>
                <i class="bi bi-person-badge fs-1 opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card bg-danger text-white p-3 shadow-sm">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-uppercase mb-1 opacity-75">Admitted Patients</h6>
                    <h3 class="mb-0"><?php echo $active_patients; ?></h3>
                </div>
                <i class="bi bi-heart-pulse fs-1 opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card bg-success text-white p-3 shadow-sm">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-uppercase mb-1 opacity-75">Available / Total Beds</h6>
                    <h3 class="mb-0"><?php echo $avail_beds; ?> / <?php echo $total_beds; ?></h3>
                </div>
                <i class="bi bi-journal-check fs-1 opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card bg-warning text-dark p-3 shadow-sm">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-uppercase mb-1 opacity-75">Today's Movement</h6>
                    <h4 class="mb-0">Adm: <?php echo $today_admissions; ?> | Dis: <?php echo $today_discharges; ?></h4>
                </div>
                <i class="bi bi-arrow-left-right fs-1 opacity-50"></i>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-8">
        <div class="card p-4 shadow-sm">
            <h5 class="fw-bold mb-3"><i class="bi bi-clock-history me-2"></i>Recent Patient Admissions</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Patient</th>
                            <th>Doctor</th>
                            <th>Ward & Bed</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $recent = $conn->query("SELECT a.*, p.name AS patient_name, s.name AS doctor_name, w.ward_name FROM admissions a JOIN patients p ON a.patient_id = p.id JOIN staff s ON a.doctor_id = s.id JOIN wards w ON a.ward_id = w.id WHERE a.status='Admitted' ORDER BY a.id DESC LIMIT 5");
                        while($r = $recent->fetch_assoc()):
                        ?>
                        <tr>
                            <td class="fw-bold"><?php echo $r['patient_name']; ?></td>
                            <td><?php echo $r['doctor_name']; ?></td>
                            <td><span class="badge bg-secondary"><?php echo $r['ward_name']; ?> (Bed: <?php echo $r['bed_number']; ?>)</span></td>
                            <td><?php echo $r['admission_date']; ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card p-4 shadow-sm">
            <h5 class="fw-bold mb-3"><i class="bi bi-lightning-charge me-2"></i>Quick Operations</h5>
            <div class="d-grid gap-2">
                <a href="patients.php" class="btn btn-primary"><i class="bi bi-person-plus me-2"></i>Register New Patient</a>
                <a href="admissions.php" class="btn btn-success"><i class="bi bi-plus-circle me-2"></i>Admit Patient & Assign Bed</a>
                <a href="departments.php" class="btn btn-outline-dark"><i class="bi bi-people me-2"></i>Manage Medical Staff</a>
                <a href="wards.php" class="btn btn-outline-secondary"><i class="bi bi-hospital me-2"></i>Ward Occupancy Status</a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
