<?php
require_once 'db.php';

if (!isset($_GET['vehicle_id'])) {
    header('Location: index.php');
    exit;
}

$vehicle_id = (int)$_GET['vehicle_id'];

$stmt = $conn->prepare("SELECT * FROM vehicles WHERE vehicle_id = ?");
$stmt->bind_param("i", $vehicle_id);
$stmt->execute();
$res = $stmt->get_result();

if (!$res || $res->num_rows === 0) {
    echo "<p>Vehicle not found. <a href=\"index.php\">Back</a></p>";
    exit;
}

$vehicle = $res->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Vehicle Details</title>
<style>
body{font-family:Inter,Arial,Helvetica,sans-serif;background:#f6f8fb;padding:24px}
.card{max-width:760px;margin:32px auto;background:#fff;padding:20px;border-radius:12px;box-shadow:0 8px 24px rgba(15,23,42,0.08)}
.card h2{margin-top:0;color:#111}
.row{display:flex;gap:12px;margin:10px 0}
.label{width:140px;color:#6b7280;font-weight:600}
.value{flex:1;font-weight:700}
.back{display:inline-block;margin-top:16px;padding:8px 12px;background:#f3f4f6;border-radius:8px;text-decoration:none;color:#111}
</style>
</head>
<body>
<div class="card">
    <h2>Vehicle Details</h2>
    <div class="row">
        <div class="label">ID</div>
        <div class="value"><?php echo htmlspecialchars($vehicle['vehicle_id']); ?></div>
    </div>
    <div class="row">
        <div class="label">Name</div>
        <div class="value"><?php echo htmlspecialchars($vehicle['vehicle_name']); ?></div>
    </div>
    <div class="row">
        <div class="label">Plate Number</div>
        <div class="value"><?php echo htmlspecialchars($vehicle['plate_number']); ?></div>
    </div>
    <div class="row">
        <div class="label">Type</div>
        <div class="value"><?php echo htmlspecialchars($vehicle['vehicle_type']); ?></div>
    </div>
    <div class="row">
        <div class="label">Status</div>
        <div class="value"><?php echo htmlspecialchars($vehicle['status']); ?></div>
    </div>

    <a class="back" href="index.php">← Back to list</a>
</div>
</body>
</html>
