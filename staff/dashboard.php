<?php
session_start();
if (!isset($_SESSION["staff_id"])) {
    header("Location: login.php");
    exit;
}
include "../includes/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST["action"] ?? "";
    $id = (int) ($_POST["id"] ?? 0);

    if ($action == "call_next") {
        // finish anyone currently being served
        $conn->query("UPDATE queue_entries SET status = 'done' WHERE status = 'serving'");
        // call the oldest waiting patient from today
        $conn->query("UPDATE queue_entries SET status = 'serving' WHERE status = 'waiting' AND DATE(created_at) = CURDATE() ORDER BY id ASC LIMIT 1");
    } elseif ($action == "skip" && $id > 0) {
        $stmt = $conn->prepare("UPDATE queue_entries SET status = 'skipped' WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    } elseif ($action == "done" && $id > 0) {
        $stmt = $conn->prepare("UPDATE queue_entries SET status = 'done' WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }
    header("Location: dashboard.php");
    exit;
}

$serving = $conn->query("SELECT id, queue_number, name FROM queue_entries WHERE status = 'serving' LIMIT 1")->fetch_assoc();
$waiting = $conn->query("SELECT id, queue_number, name, reason FROM queue_entries WHERE status = 'waiting' AND DATE(created_at) = CURDATE() ORDER BY id ASC");
?>
<!DOCTYPE html>
<html>
<head>
  <title>Staff Dashboard</title>
  <meta http-equiv="refresh" content="15">
</head>
<body>
  <h1>Staff Dashboard</h1>
  <p><a href="logout.php">Log out</a></p>

  <h2>Now serving</h2>
  <?php if ($serving) { ?>
    <p>#<?php echo $serving["queue_number"]; ?> - <?php echo htmlspecialchars($serving["name"]); ?></p>
    <form method="post" style="display:inline">
      <input type="hidden" name="action" value="done">
      <input type="hidden" name="id" value="<?php echo $serving["id"]; ?>">
      <button type="submit">Done</button>
    </form>
    <form method="post" style="display:inline">
      <input type="hidden" name="action" value="skip">
      <input type="hidden" name="id" value="<?php echo $serving["id"]; ?>">
      <button type="submit">Skip</button>
    </form>
  <?php } else { ?>
    <p>No one is being served.</p>
  <?php } ?>

  <form method="post">
    <input type="hidden" name="action" value="call_next">
    <button type="submit">Call next</button>
  </form>

  <h2>Waiting list</h2>
  <table border="1" cellpadding="6">
    <tr><th>No.</th><th>Name</th><th>Reason</th></tr>
    <?php while ($row = $waiting->fetch_assoc()) { ?>
      <tr>
        <td><?php echo $row["queue_number"]; ?></td>
        <td><?php echo htmlspecialchars($row["name"]); ?></td>
        <td><?php echo htmlspecialchars($row["reason"] ?? ""); ?></td>
      </tr>
    <?php } ?>
  </table>
</body>
</html>