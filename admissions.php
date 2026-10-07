<?php
include 'includes/config.php';

$msg = "";

// Admit Patient
if (isset($_POST['admit_patient'])) {
    $pid = $_POST['patient_id'];
    $doc_id = $_POST['doctor_id'];
    $ward_id = $_POST['ward_id'];
    $bed_no = $_POST['bed_number'];
    $adm_date = $_POST['admission_date'];

    $ward_check = $conn->query("SELECT available_beds FROM wards WHERE id=$ward_id")->fetch_assoc();
    if ($ward_check['available_beds'] > 0) {
        $conn->query("INSERT INTO admissions (patient_id, doctor_id, ward_id, bed_number, admission_date) VALUES ('$pid', '$doc_id', '$ward_id', '$bed_no', '$adm_date')");
        $conn->query("UPDATE wards SET available_beds = available_beds - 1 WHERE id=$ward_id");
        $msg = "Patient admitted & bed assigned!";
    } else {
        $msg = "Error: Selected ward has no available beds!";
    }
}

// Discharge Patient
if (isset($_GET['discharge_id'])) {
    $adm_id = $_GET['discharge_id'];
    $ward_id = $_GET['ward_id'];
    $today = date('Y-m-d');

    $conn->query("UPDATE admissions SET status='Discharged', discharge_date='$today' WHERE id=$adm_id");
    $conn->query("UPDATE wards SET available_beds = available_beds + 1 WHERE id=$ward_id");
    $msg = "Patient discharged & bed released!";
}

// Filtering Logic
$where = "WHERE a.status='Admitted'";
if (isset($_GET['filter_ward']) && $_GET['filter_ward'] != '') {
    $wcat = $_GET['filter_ward'];
    $where .= " AND w.category='$wcat'";
}
if (isset($_GET['filter_doc']) && $_GET['filter_doc'] != '') {
    $did = $_GET['filter_doc'];
    $where .= " AND a.doctor_id=$did";
}

$admissions_list = $conn->query("SELECT a.*, p.name AS patient_name, s.name AS doctor_name, w.ward_name, w.category FROM admissions a JOIN patients p ON a.patient_id = p.id JOIN staff s ON a.doctor_id = s.id JOIN wards w ON a.ward_id = w.id $where ORDER BY a.id DESC");

include 'includes/header.php';
?>

<?php if($msg): ?>
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <?php echo $msg; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row g-4">
    <!-- Form Side -->
    <div class="col-md-4">
        <div class="card p-3 shadow-sm">
            <h5 class="fw-bold"><i class="bi bi-hospital me-2"></i>Admit Patient</h5>
            <form method="POST" class="mt-2">
                <div class="mb-2">
                    <label class="form-label mb-1">Patient</label>
                    <select name="patient_id" class="form-control" required>
                        <?php $pts = $conn->query("SELECT * FROM patients"); while($p = $pts->fetch_assoc()) { echo "<option value='".$p['id']."'>".$p['name']."</option>"; } ?>
                    </select>
                </div>
                <div class="mb-2">
                    <label class="form-label mb-1">Primary Doctor</label>
                    <select name="doctor_id" class="form-control" required>
                        <?php $docs = $conn->query("SELECT * FROM staff WHERE role='Doctor' AND status='Active'"); while($d = $docs->fetch_assoc()) { echo "<option value='".$d['id']."'>".$d['name']." (".$d['specialization'].")</option>"; } ?>
                    </select>
                </div>
                <div class="mb-2">
                    <label class="form-label mb-1">Ward / Room</label>
                    <select name="ward_id" class="form-control" required>
                        <?php $wds = $conn->query("SELECT * FROM wards WHERE available_beds > 0"); while($w = $wds->fetch_assoc()) { echo "<option value='".$w['id']."'>".$w['ward_name']." (".$w['category']." - ".$w['available_beds']." free)</option>"; } ?>
                    </select>
                </div>
                <div class="mb-2">
                    <label class="form-label mb-1">Bed Number</label>
                    <input type="text" name="bed_number" class="form-control" placeholder="e.g. ICU-01 or B-12" required>
                </div>
                <div class="mb-2">
                    <label class="form-label mb-1">Admission Date</label>
                    <input type="date" name="admission_date" value="<?php echo date('Y-m-d'); ?>" class="form-control" required>
                </div>
                <button type="submit" name="admit_patient" class="btn btn-warning w-100 fw-bold">Allocate Bed & Admit</button>
            </form>
        </div>
    </div>

    <!-- Filter & Table Side -->
    <div class="col-md-8">
        <div class="card p-3 shadow-sm mb-3">
            <form method="GET" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <select name="filter_ward" class="form-control">
                        <option value="">Filter by Ward Category</option>
                        <option value="ICU">ICU</option>
                        <option value="General Ward">General Ward</option>
                        <option value="Private">Private</option>
                    </select>
                </div>
                <div class="col-md-5">
                    <select name="filter_doc" class="form-control">
                        <option value="">Filter by Doctor</option>
                        <?php $docs2 = $conn->query("SELECT * FROM staff WHERE role='Doctor'"); while($d2 = $docs2->fetch_assoc()) { echo "<option value='".$d2['id']."'>".$d2['name']."</option>"; } ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>
        </div>

        <div class="card p-3 shadow-sm">
            <h5 class="fw-bold mb-3"><i class="bi bi-person-check me-2"></i>Currently Admitted Patients</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Patient</th>
                            <th>Doctor</th>
                            <th>Ward & Bed</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($a = $admissions_list->fetch_assoc()): ?>
                        <tr>
                            <td class="fw-bold"><?php echo $a['patient_name']; ?></td>
                            <td><?php echo $a['doctor_name']; ?></td>
                            <td><span class="badge bg-secondary"><?php echo $a['ward_name']; ?> (<b><?php echo $a['bed_number']; ?></b>)</span></td>
                            <td><?php echo $a['admission_date']; ?></td>
                            <td>
                                <a href="admissions.php?discharge_id=<?php echo $a['id']; ?>&ward_id=<?php echo $a['ward_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Discharge patient and release bed?')">Discharge</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
