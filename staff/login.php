<?php
session_start();
include "../includes/db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT id, password_hash FROM staff WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user && password_verify($password, $user["password_hash"])) {
        session_regenerate_id(true);
        $_SESSION["staff_id"] = $user["id"];
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Wrong username or password.";
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Staff Login</title></head>
<body>
  <h1>Staff Login</h1>
  <?php if ($error) { echo "<p>" . $error . "</p>"; } ?>
  <form method="post">
    <input type="text" name="username" placeholder="Username" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <button type="submit">Log in</button>
  </form>
</body>
</html>