<?php
include "includes/db.php";
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $phone = $_POST["phone"];
    $reason = $_POST["reason"];

    $result = $conn->query("SELECT COALESCE(MAX(queue_number), 0) + 1 AS next_no FROM queue_entries WHERE DATE(created_at) = CURDATE()");
    $next = $result->fetch_assoc()["next_no"];

    $stmt = $conn->prepare("INSERT INTO queue_entries (queue_number, name, phone, reason) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $next, $name, $phone, $reason);
    $stmt->execute();

    $message = "You are number " . $next . " in the queue.";
}
?>
<!DOCTYPE html>
<html>
<head><title>Clinic Check-in</title></head>
<body>
  <h1>Clinic Check-in</h1>
  <p><?php echo $message; ?></p>
  <form method="post">
    <input type="text" name="name" placeholder="Full name" required><br>
    <input type="text" name="phone" placeholder="Phone number" required><br>
    <input type="text" name="reason" placeholder="Reason for visit"><br>
    <button type="submit">Join queue</button>
  </form>
</body>
</html>