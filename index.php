RABAN_HR
<!-- index.php -->

<?php
require_once 'db.php';

/* INSERT VEHICLE (prepared statement) */
if (isset($_POST['add_vehicle'])) {

    $vehicle_name = $_POST['vehicle_name'];
    $plate_number = $_POST['plate_number'];
    $vehicle_type = $_POST['vehicle_type'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("INSERT INTO vehicles (vehicle_name, plate_number, vehicle_type, status) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $vehicle_name, $plate_number, $vehicle_type, $status);

    if ($stmt->execute()) {
        echo "<script>alert('Vehicle Added Successfully');</script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

/* UPDATE VEHICLE */
if (isset($_POST['update_vehicle'])) {
    $vehicle_id = (int)$_POST['vehicle_id'];
    $vehicle_name = $_POST['vehicle_name'];
    $plate_number = $_POST['plate_number'];
    $vehicle_type = $_POST['vehicle_type'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE vehicles SET vehicle_name = ?, plate_number = ?, vehicle_type = ?, status = ? WHERE vehicle_id = ?");
    $stmt->bind_param("ssssi", $vehicle_name, $plate_number, $vehicle_type, $status, $vehicle_id);

    if ($stmt->execute()) {
        echo "<script>alert('Vehicle Updated Successfully'); window.location.href = window.location.pathname; </script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

/* DELETE VEHICLE */
if (isset($_POST['delete_vehicle'])) {
    $vehicle_id = (int)$_POST['vehicle_id'];

    $stmt = $conn->prepare("DELETE FROM vehicles WHERE vehicle_id = ?");
    $stmt->bind_param("i", $vehicle_id);

    if ($stmt->execute()) {
        echo "<script>alert('Vehicle Deleted Successfully'); window.location.href = window.location.pathname; </script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

/* If editing, fetch the vehicle to prefill the form */
$editMode = false;
$editData = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM vehicles WHERE vehicle_id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && $res->num_rows) {
        $editData = $res->fetch_assoc();
        $editMode = true;
    }
    $stmt->close();
}

/* FETCH VEHICLES */
$result = $conn->query("SELECT * FROM vehicles");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Vehicle Management System</title>

<style>
:root{
    --bg: #f6f8fb;
    --card: #ffffff;
    --primary: #2563eb;
    --muted: #6b7280;
    --radius: 12px;
}

@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap');

*{box-sizing:border-box}
html,body{height:100%}
body{
    margin:0;
    font-family: Inter, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
    background: linear-gradient(rgba(15,23,42,0.45), rgba(15,23,42,0.45)), url('https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&w=1600&q=80') center/cover fixed no-repeat;
    color:#0f172a;
    -webkit-font-smoothing:antialiased;
}

header{
    background:linear-gradient(90deg,var(--primary),#4f46e5);
    color:white;
    padding:20px 24px;
    display:flex;
    align-items:center;
    gap:14px;
    box-shadow:0 6px 18px rgba(37,99,235,0.12);
}

header .brand{
    font-size:20px;
    font-weight:700;
    letter-spacing:0.2px;
}

header .nav{
    margin-left:auto;
    display:flex;
    gap:12px;
    align-items:center;
}

header .nav-link{
    color:rgba(255,255,255,0.95);
    text-decoration:none;
    padding:8px 12px;
    border-radius:8px;
    background:rgba(255,255,255,0.06);
    font-weight:600;
}
header .nav-link:hover{background:rgba(255,255,255,0.12)}

.container{
    max-width:1100px;
    margin:28px auto;
    padding:0 18px;
}

.form-container, .table-container{
    background:var(--card);
    padding:18px;
    border-radius:var(--radius);
    box-shadow:0 6px 18px rgba(15,23,42,0.06);
    margin-bottom:20px;
}

.form-container h2{margin:0 0 12px 0;color:var(--primary);font-size:18px}
form{display:grid;grid-template-columns:repeat(2,1fr);gap:12px}
@media (max-width:800px){form{grid-template-columns:1fr}}

input, select{
    width:100%;
    padding:12px 14px;
    border:1px solid #e6e9ee;
    border-radius:8px;
    background:transparent;
    font-size:14px;
}

/* Buttons */
.btn{display:inline-block;padding:10px 14px;border-radius:8px;border:none;cursor:pointer;font-weight:600}
.btn-primary{background:var(--primary);color:#fff}
.btn-primary:hover{filter:brightness(.95)}
.btn-danger{background:#ef4444;color:#fff}
.btn-secondary{background:#f3f4f6;color:#111}

button[type=submit]{cursor:pointer}

.table-container h2{margin:0 0 12px 0;color:var(--primary);font-size:18px}
table{width:100%;border-collapse:collapse;border-radius:8px;overflow:hidden;display:block}
table thead{display:block}
table tbody{display:block;max-height:420px;overflow:auto}
table th, table td{padding:12px 14px;text-align:left;min-width:120px}
table thead th{background:linear-gradient(90deg,var(--primary),#4f46e5);color:#fff;font-weight:600}
tbody tr{display:table;width:100%;table-layout:fixed}
tbody tr:nth-child(even){background:#fbfbfd}
tbody tr:hover{background:#f1f5f9}

.status{padding:6px 10px;border-radius:999px;color:#fff;font-size:12px;font-weight:600}
.available{background:#10b981}
.inuse{background:#f59e0b}
.maintenance{background:#ef4444}

.actions a, .actions button{margin-right:8px;text-decoration:none}
.actions a{display:inline-block;padding:6px 10px;border-radius:8px;background:#f3f4f6;color:#111}
.actions button{border:none}

.vehicle-link{color:var(--primary);font-weight:600;text-decoration:underline}

footer{margin-top:18px;text-align:center;padding:14px;color:#fff;background:transparent}
</style>
</head>
<body>

<header>
    <div class="brand">Vehicle Management System</div>
    <nav class="nav">
        <a class="nav-link" href="index.php">Home</a>
        <a class="nav-link" href="#add-vehicle">Add New Vehicle</a>
        <a class="nav-link" href="#vehicle-list">Vehicle List</a>
    </nav>
</header>

<div class="container">

    <!-- FORM -->
    <div class="form-container" id="add-vehicle">
        <h2><?php echo $editMode ? 'Edit Vehicle' : 'Add New Vehicle'; ?></h2>

        <form method="POST">

            <?php if ($editMode): ?>
                <input type="hidden" name="vehicle_id" value="<?php echo htmlspecialchars($editData['vehicle_id']); ?>">
            <?php endif; ?>

            <input type="text" name="vehicle_name" placeholder="Vehicle Name" required value="<?php echo $editMode ? htmlspecialchars($editData['vehicle_name']) : ''; ?>">

            <input type="text" name="plate_number" placeholder="Plate Number" required value="<?php echo $editMode ? htmlspecialchars($editData['plate_number']) : ''; ?>">

            <select name="vehicle_type" required>
                <option value="">Select Type</option>
                <option value="Car" <?php echo ($editMode && $editData['vehicle_type']=='Car') ? 'selected' : ''; ?>>Car</option>
                <option value="Truck" <?php echo ($editMode && $editData['vehicle_type']=='Truck') ? 'selected' : ''; ?>>Truck</option>
                <option value="Bus" <?php echo ($editMode && $editData['vehicle_type']=='Bus') ? 'selected' : ''; ?>>Bus</option>
                <option value="Motorcycle" <?php echo ($editMode && $editData['vehicle_type']=='Motorcycle') ? 'selected' : ''; ?>>Motorcycle</option>
            </select>

            <select name="status" required>
                <option value="">Status</option>
                <option value="Available" <?php echo ($editMode && $editData['status']=='Available') ? 'selected' : ''; ?>>Available</option>
                <option value="In Use" <?php echo ($editMode && $editData['status']=='In Use') ? 'selected' : ''; ?>>In Use</option>
                <option value="Maintenance" <?php echo ($editMode && $editData['status']=='Maintenance') ? 'selected' : ''; ?>>Maintenance</option>
            </select>

            <?php if ($editMode): ?>
                <button type="submit" name="update_vehicle">Update Vehicle</button>
                <a href="<?php echo basename($_SERVER['PHP_SELF']); ?>" style="margin-left:10px;">Cancel</a>
            <?php else: ?>
                <button type="submit" name="add_vehicle">Add Vehicle</button>
            <?php endif; ?>

        </form>
    </div>

    <!-- TABLE -->
    <div class="table-container" id="vehicle-list">
        <h2>Vehicle List</h2>

        <table>

            <tr>
                <th>ID</th>
                <th>Vehicle</th>
                <th>Plate Number</th>
                <th>Type</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>

            <?php
            if($result->num_rows > 0){

                while($row = $result->fetch_assoc()){

                    $statusClass = "";

                    if($row['status'] == "Available"){
                        $statusClass = "available";
                    }
                    elseif($row['status'] == "In Use"){
                        $statusClass = "inuse";
                    }
                    else{
                        $statusClass = "maintenance";
                    }

                    echo "
                    <tr>
                        <td>".$row['vehicle_id']."</td>
                        <td><a class='vehicle-link' href='vehicle_details.php?vehicle_id=".$row['vehicle_id']."'>".$row['vehicle_name']."</a></td>
                        <td>".$row['plate_number']."</td>
                        <td>".$row['vehicle_type']."</td>
                        <td>
                            <span class='status $statusClass'>
                                ".$row['status']."
                            </span>
                        </td>
                        <td>
                            <a href='?edit=".$row['vehicle_id']."' style='margin-right:8px;'>Edit</a>
                            <form method='POST' style='display:inline' onsubmit=\"return confirm('Delete this vehicle?');\">
                                <input type='hidden' name='vehicle_id' value='".$row['vehicle_id']."'>
                                <button type='submit' name='delete_vehicle' style='background:red;color:white;border:none;padding:6px 10px;border-radius:6px;cursor:pointer;'>Delete</button>
                            </form>
                        </td>
                    </tr>
                    ";
                }

            }else{
                echo "
                <tr>
                    <td colspan='6'>No Vehicles Found</td>
                </tr>
                ";
            }
            ?>

        </table>
    </div>

</div>

<footer>
    © 2026 Vehicle Management System
</footer>

</body>
</html>
```
