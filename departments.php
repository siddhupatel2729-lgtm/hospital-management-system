<?php
include 'includes/config.php';

$msg = "";

// Add Department
if (isset($_POST['add_dept'])) {
    $dname = $_POST['dept_name'];
    $block = $_POST['block_floor'];
    $hod = $_POST['hod_name'];
    $conn->query("INSERT INTO departments (dept_name, block_floor, hod_name) VALUES ('$dname', '$block', '$hod')");
    $msg = "Department added successfully!";
}

// Add or Update Staff
if (isset($_POST['save_staff'])) {
    $id = $_POST['staff_id'] ?? 0;
    $dept_id = $_POST['department_id'];
    $name = $_POST['name'];
    $role = $_POST['role'];
    $spec = $_POST['specialization'];
    $shift = $_POST['shift'];

    if ($id > 0) {
        $conn->query("UPDATE staff SET department_id='$dept_id', name='$name', role='$role', specialization='$spec', shift='$shift' WHERE id=$id");
        $msg = "Staff record updated!";
    } else {
        $conn->query("INSERT INTO staff (department_id, name, role, specialization, shift) VALUES ('$dept_id', '$name', '$role', '$spec', '$shift')");
        $msg = "New staff registered successfully!";
    }
}

// Delete Staff
if (isset($_GET['delete'])) {
    $sid = $_GET['delete'];
    $conn->query("DELETE FROM staff WHERE id=$sid");
    $msg = "Staff record removed!";
}

// Edit Fetch
$edit_staff = null;
if (isset($_GET['edit'])) {
    $eid = $_GET['edit'];
    $edit_staff = $conn->query("SELECT * FROM staff WHERE id=$eid")->fetch_assoc();
}

$depts = $conn->query("SELECT * FROM departments");
$staff_list = $conn->query("SELECT s.*, d.dept_name FROM staff s JOIN departments d ON s.department_id = d.id ORDER BY d.dept_name, s.name");

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
        <!-- Add Dept Card -->
        <div class="card p-3 shadow-sm mb-4">
            <h5 class="fw-bold"><i class="bi bi-building-add me-2"></i>Add Department</h5>
            <form method="POST" class="mt-2">
                <div class="mb-2"><input type="text" name="dept_name" class="form-control" placeholder="Dept Name" required></div>
                <div class="mb-2"><input type="text" name="block_floor" class="form-control" placeholder="Block / Floor" required></div>
                <div class="mb-2"><input type="text" name="hod_name" class="form-control" placeholder="HOD Name" required></div>
                <button type="submit" name="add_dept" class="btn btn-outline-primary w-100 btn-sm">Add Dept</button>
            </form>
        </div>

        <!-- Add/Edit Staff Card -->
        <div class="card p-3 shadow-sm">
            <h5 class="fw-bold"><i class="bi bi-person-gear me-2"></i><?php echo $edit_staff ? 'Edit Staff Details' : 'Register Staff'; ?></h5>
            <form method="POST" class="mt-2">
                <input type="hidden" name="staff_id" value="<?php echo $edit_staff['id'] ?? 0; ?>">
                <div class="mb-2">
                    <select name="department_id" class="form-control" required>
                        <option value="">Select Department</option>
                        <?php 
                        $depts2 = $conn->query("SELECT * FROM departments");
                        while($d = $depts2->fetch_assoc()): 
                            $sel = ($edit_staff && $edit_staff['department_id'] == $d['id']) ? 'selected' : '';
                        ?>
                            <option value="<?php echo $d['id']; ?>" <?php echo $sel; ?>><?php echo $d['dept_name']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-2"><input type="text" name="name" class="form-control" placeholder="Full Name" value="<?php echo $edit_staff['name'] ?? ''; ?>" required></div>
                <div class="mb-2">
                    <select name="role" class="form-control" required>
                        <option value="Doctor" <?php echo ($edit_staff && $edit_staff['role'] == 'Doctor') ? 'selected' : ''; ?>>Doctor</option>
                        <option value="Nurse" <?php echo ($edit_staff && $edit_staff['role'] == 'Nurse') ? 'selected' : ''; ?>>Nurse</option>
                    </select>
                </div>
                <div class="mb-2"><input type="text" name="specialization" class="form-control" placeholder="Specialization" value="<?php echo $edit_staff['specialization'] ?? ''; ?>" required></div>
                <div class="mb-2">
                    <select name="shift" class="form-control" required>
                        <option value="Morning" <?php echo ($edit_staff && $edit_staff['shift'] == 'Morning') ? 'selected' : ''; ?>>Morning Shift</option>
                        <option value="Evening" <?php echo ($edit_staff && $edit_staff['shift'] == 'Evening') ? 'selected' : ''; ?>>Evening Shift</option>
                        <option value="Night" <?php echo ($edit_staff && $edit_staff['shift'] == 'Night') ? 'selected' : ''; ?>>Night Shift</option>
                    </select>
                </div>
                <button type="submit" name="save_staff" class="btn btn-primary w-100"><?php echo $edit_staff ? 'Update Staff' : 'Save Staff'; ?></button>
                <?php if($edit_staff): ?>
                    <a href="departments.php" class="btn btn-link text-secondary w-100 text-center mt-1">Cancel Edit</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- Table Side -->
    <div class="col-md-8">
        <div class="card p-3 shadow-sm">
            <h5 class="fw-bold mb-3"><i class="bi bi-people-fill me-2"></i>Medical Staff Directory</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Department</th>
                            <th>Name</th>
                            <th>Role & Spec</th>
                            <th>Shift</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($s = $staff_list->fetch_assoc()): ?>
                        <tr>
                            <td><span class="badge bg-secondary"><?php echo $s['dept_name']; ?></span></td>
                            <td class="fw-bold"><?php echo $s['name']; ?></td>
                            <td><?php echo $s['role']; ?> (<?php echo $s['specialization']; ?>)</td>
                            <td><span class="badge bg-outline-info text-dark border"><?php echo $s['shift']; ?></span></td>
                            <td>
                                <a href="departments.php?edit=<?php echo $s['id']; ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                <a href="departments.php?delete=<?php echo $s['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Remove staff member?')"><i class="bi bi-trash"></i></a>
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
