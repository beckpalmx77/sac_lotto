<?php
include('../includes/Header.php');
// ตรวจสอบว่าเป็นคำสั่งที่ถูกต้อง
if (isset($_POST['action']) && !empty($_POST['service'])) {
    $action = $_POST['action'];
    $service = $_POST['service'];

    // ตัวแปรสำหรับคำสั่ง shell
    $command = "";

    switch ($action) {
        case 'start':
            $command = "sudo systemctl start " . escapeshellarg($service);
            break;
        case 'stop':
            $command = "sudo systemctl stop " . escapeshellarg($service);
            break;
        case 'restart':
            $command = "sudo systemctl restart " . escapeshellarg($service);
            break;
        case 'status':
            $command = "sudo systemctl status " . escapeshellarg($service);
            break;
        default:
            echo "Invalid action!";
            exit;
    }

    // รันคำสั่ง shell
    $output = shell_exec($command);

    // แสดงผลลัพธ์
    echo "<pre>$output</pre>";
} else {
    echo "Invalid input.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control Service</title>
    <!-- ลิงค์ไปยัง Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Control Service</h2>
    <form method="POST">
        <div class="mb-3">
            <label for="service" class="form-label">Service Name:</label>
            <input type="text" id="service" name="service" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="action" class="form-label">Action:</label>
            <select id="action" name="action" class="form-select">
                <option value="start">Start</option>
                <option value="stop">Stop</option>
                <option value="restart">Restart</option>
                <option value="status">Status</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Execute</button>
    </form>
</div>

<!-- ลิงค์ไปยัง Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
