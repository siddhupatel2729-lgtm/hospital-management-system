<?php
include 'includes/config.php';

$msg = "";

// Save / Update Patient
if (isset($_POST['save_patient'])) {
    $id = $_POST['patient_id'] ?? 0;
    $name = $_POST['name'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $contact = $_POST['emergency_contact'];
    $history = $_POST['medical_history'];

    if ($id > 0) {
        $conn->query("UPDATE patients SET name='$name', age='$age', gender='$gender', emergency_contact='$contact', medical_history='$history' WHERE id=$id");
        $msg = "Patient record updated!";
    } else {
        $conn->query("INSERT INTO patients (name, age, gender, emergency_contact, medical_history) VALUES ('$name', '$age', '$gender', '$contact', '$history')");
        $msg = "Patient registered successfully!";
    }
}

// Delete Patient
if (isset($_GET['delete'])) {
    $pid = $_GET['delete'];
    $conn->query("DELETE FROM patients WHERE id=$pid");
    $msg = "Patient record deleted!";
}

// Fetch for edit
$edit_pt = null;
if (isset($_GET['edit'])) {
    $eid = $_GET['edit'];
    $edit_pt = $conn->query("SELECT * FROM patients WHERE id=$eid")->fetch_assoc();
}

$patients = $conn->query("SELECT * FROM patients ORDER BY id DESC");

include 'includes/header.php';
?>

<?php if($msg): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo $msg; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row g-4">
    <!-- Form Side -->
    <div class="col-md-4">
        <div class="card p-3 shadow-sm">
            <h5 class="fw-bold"><i class="bi bi-person-add me-2"></i><?php echo $edit_pt ? 'Edit Patient Record' : 'Register Patient'; ?></h5>
            <form method="POST" class="mt-2">
                <input type="hidden" name="patient_id" value="<?php echo $edit_pt['id'] ?? 0; ?>">
                <div class="mb-2"><input type="text" name="name" class="form-control" placeholder="Full Name" value="<?php echo $edit_pt['name'] ?? ''; ?>" required></div>
                <div class="mb-2"><input type="number" name="age" class="form-control" placeholder="Age" value="<?php echo $edit_pt['age'] ?? ''; ?>" required></div>
                <div class="mb-2">
                    <select name="gender" class="form-control" required>
                        <option value="Male" <?php echo ($edit_pt && $edit_pt['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                        <option value="Female" <?php echo ($edit_pt && $edit_pt['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                        <option value="Other" <?php echo ($edit_pt && $edit_pt['gender'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>
                <div class="mb-2"><input type="text" name="emergency_contact" class="form-control" placeholder="Emergency Phone No." value="<?php echo $edit_pt['emergency_contact'] ?? ''; ?>" required></div>
                <div class="mb-2"><textarea name="medical_history" class="form-control" placeholder="Medical History / Symptoms"><?php echo $edit_pt['medical_history'] ?? ''; ?></textarea></div>
                <button type="submit" name="save_patient" class="btn btn-primary w-100"><?php echo $edit_pt ? 'Update Record' : 'Register Patient'; ?></button>
                <?php if($edit_pt): ?>
                    <a href="patients.php" class="btn btn-link text-secondary w-100 text-center mt-1">Cancel Edit</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- Directory Side -->
    <div class="col-md-8">
        <div class="card p-3 shadow-sm">
            <h5 class="fw-bold mb-3"><i class="bi bi-card-checklist me-2"></i>Registered Patients Directory</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Age/Gender</th>
                            <th>Emergency Contact</th>
                            <th>Medical Notes</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($p = $patients->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo $p['id']; ?></td>
                            <td class="fw-bold"><?php echo $p['name']; ?></td>
                            <td><?php echo $p['age']; ?> yrs (<?php echo $p['gender']; ?>)</td>
                            <td><?php echo $p['emergency_contact']; ?></td>
                            <td><small class="text-muted"><?php echo $p['medical_history']; ?></small></td>
                            <td>
                                <a href="patients.php?edit=<?php echo $p['id']; ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                <a href="patients.php?delete=<?php echo $p['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete patient?')"><i class="bi bi-trash"></i></a>
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
