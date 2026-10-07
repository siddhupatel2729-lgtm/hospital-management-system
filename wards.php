<?php
include 'includes/config.php';

$msg = "";

if (isset($_POST['add_ward'])) {
    $wname = $_POST['ward_name'];
    $cat = $_POST['category'];
    $beds = $_POST['total_beds'];

    $conn->query("INSERT INTO wards (ward_name, category, total_beds, available_beds) VALUES ('$wname', '$cat', '$beds', '$beds')");
    $msg = "Ward added successfully!";
}

$wards = $conn->query("SELECT * FROM wards ORDER BY id DESC");

include 'includes/header.php';
?>

<?php if($msg): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo $msg; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card p-3 shadow-sm">
            <h5 class="fw-bold"><i class="bi bi-plus-square me-2"></i>Add New Ward / Room</h5>
            <form method="POST" class="mt-2">
                <div class="mb-2"><input type="text" name="ward_name" class="form-control" placeholder="Ward / Room Name" required></div>
                <div class="mb-2">
                    <select name="category" class="form-control" required>
                        <option value="ICU">ICU</option>
                        <option value="General Ward">General Ward</option>
                        <option value="Private">Private Room</option>
                    </select>
                </div>
                <div class="mb-2"><input type="number" name="total_beds" class="form-control" placeholder="Bed Capacity" required></div>
                <button type="submit" name="add_ward" class="btn btn-success w-100">Create Ward</button>
            </form>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card p-3 shadow-sm">
            <h5 class="fw-bold mb-3"><i class="bi bi-door-open-fill me-2"></i>Ward Capacity & Real-Time Availability</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Ward Name</th>
                            <th>Category</th>
                            <th>Total Capacity</th>
                            <th>Available Beds</th>
                            <th>Occupied Beds</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($w = $wards->fetch_assoc()): 
                            $badge = ($w['category'] == 'ICU') ? 'badge-icu' : (($w['category'] == 'General Ward') ? 'badge-general' : 'badge-private');
                        ?>
                        <tr>
                            <td class="fw-bold"><?php echo $w['ward_name']; ?></td>
                            <td><span class="badge <?php echo $badge; ?>"><?php echo $w['category']; ?></span></td>
                            <td><?php echo $w['total_beds']; ?> Beds</td>
                            <td><span class="badge bg-success"><?php echo $w['available_beds']; ?> Free</span></td>
                            <td><span class="badge bg-warning text-dark"><?php echo $w['total_beds'] - $w['available_beds']; ?> Occupied</span></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
