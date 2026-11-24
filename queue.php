<?php
include "includes/db.php";

$serving = $conn->query("SELECT queue_number, name FROM queue_entries WHERE status = 'serving' LIMIT 1")->fetch_assoc();
$waiting = $conn->query("SELECT queue_number, name FROM queue_entries WHERE status = 'waiting' AND DATE(created_at) = CURDATE() ORDER BY id ASC");
?>
<!DOCTYPE html>
<html>
<head>
  <title>Clinic Queue</title>
  <meta http-equiv="refresh" content="10">
</head>
<body>
  <h1>Clinic Queue</h1>

  <h2>Now serving</h2>
  <?php if ($serving) { ?>
    <p>#<?php echo $serving["queue_number"]; ?> - <?php echo htmlspecialchars($serving["name"]); ?></p>
  <?php } else { ?>
    <p>No one is being served.</p>
  <?php } ?>

  <h2>Waiting</h2>
  <table border="1" cellpadding="6">
    <tr><th>No.</th><th>Name</th></tr>
    <?php while ($row = $waiting->fetch_assoc()) { ?>
      <tr>
        <td><?php echo $row["queue_number"]; ?></td>
        <td><?php echo htmlspecialchars($row["name"]); ?></td>
      </tr>
    <?php } ?>
  </table>

  <p><a href="checkin.php">Join the queue</a></p>
</body>
</html>