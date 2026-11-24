<?php
include "includes/db.php";

$id = (int) ($_GET["id"] ?? 0);

$stmt = $conn->prepare("SELECT queue_number, name, status FROM queue_entries WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$ticket = $stmt->get_result()->fetch_assoc();

if (!$ticket) {
    die("Ticket not found.");
}

$stmt2 = $conn->prepare("SELECT COUNT(*) AS ahead FROM queue_entries WHERE status = 'waiting' AND id < ? AND DATE(created_at) = CURDATE()");
$stmt2->bind_param("i", $id);
$stmt2->execute();
$ahead = $stmt2->get_result()->fetch_assoc()["ahead"];
?>
<!DOCTYPE html>
<html>
<head>
  <title>Your Ticket</title>
  <meta http-equiv="refresh" content="10">
</head>
<body>
  <h1>Your Queue Ticket</h1>
  <p>Name: <?php echo htmlspecialchars($ticket["name"]); ?></p>
  <h2>Your number: <?php echo $ticket["queue_number"]; ?></h2>
  <p>Status: <?php echo $ticket["status"]; ?></p>
  <p>People ahead of you: <?php echo $ahead; ?></p>
  <p><a href="checkin.php">Back to check-in</a></p>
</body>
</html>