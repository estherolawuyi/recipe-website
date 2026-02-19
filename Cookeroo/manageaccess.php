<?php
session_start();
require_once("db.php");

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid recipe ID.");
}
$recipe_id = (int)$_GET['id'];

try {
    $conn = new PDO("mysql:host=localhost;dbname=ejo252", "ejo252", "Cannamon101@", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("DB Connection failed: " . $e->getMessage());
}

$stmt = $conn->prepare("SELECT title FROM Recipes WHERE recipe_id = ?");
$stmt->execute([$recipe_id]);
$recipe = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$recipe) {
    die("Recipe not found.");
}

$stmt = $conn->prepare("SELECT user_id, email, avatar_url FROM Users ORDER BY email");
$stmt->execute();
$allUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("SELECT user_id, status FROM Access WHERE recipe_id = ?");
$stmt->execute([$recipe_id]);
$currentAccessRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$currentAccess = [];
foreach ($currentAccessRows as $row) {
    $currentAccess[$row['user_id']] = $row['status'];
}
// submit form
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $grantedUserIds = $_POST['access'] ?? []; 
    if (!is_array($grantedUserIds)) $grantedUserIds = [];

    $grantedUserIds = array_map('intval', $grantedUserIds);

    foreach ($allUsers as $user) {
        $uid = $user['user_id'];
        $shouldHaveAccess = in_array($uid, $grantedUserIds);
        $currentStatus = $currentAccess[$uid] ?? null;

        if ($currentStatus === null && $shouldHaveAccess) {
            $insert = $conn->prepare("INSERT INTO Access (recipe_id, user_id, status) VALUES (?, ?, 1)");
            $insert->execute([$recipe_id, $uid]);
        } elseif ($currentStatus !== null) {
            $newStatus = $shouldHaveAccess ? 1 : 0;
            if ($currentStatus != $newStatus) {
                $update = $conn->prepare("UPDATE Access SET status = ? WHERE recipe_id = ? AND user_id = ?");
                $update->execute([$newStatus, $recipe_id, $uid]);
            }
        }
    }

    header("Location: manageaccess.php?id=$recipe_id");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Cookeroo Manage Access: <?= htmlspecialchars($recipe['title']) ?></title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
  <div id="header">
    <header class="recipe-list">
      <h1>COOKEROO RECIPES</h1>
    </header>
  </div>

  <div class="top-bar">
    <a class="button" href="login.php">Sign Out</a>
    <h2>MANAGE ACCESS: <?= htmlspecialchars($recipe['title']) ?></h2>
    <a class="button" href="recipelist.php">Back to Recipe List</a>
  </div>

  <main>
    <div id="create-container">
      <form class="auth-form" id="manage-access-form" method="post" action="">
        <div class="form-input-grid">
          <label>Select users to grant or revoke access:</label>

          <?php foreach ($allUsers as $user): 
              $checked = isset($currentAccess[$user['user_id']]) && $currentAccess[$user['user_id']] == 1;
          ?>
          <div class="user">
            <img src="<?= htmlspecialchars($user['avatar_url'] ?: 'images/default-avatar.png') ?>" class="avatar" alt="Avatar of @<?= htmlspecialchars($user['email']) ?>" />
            <div class="user-info">
              <strong>@<?= htmlspecialchars($user['email']) ?></strong>
            </div>
            <input type="checkbox" name="access[]" id="select-<?= htmlspecialchars($user['email']) ?>" value="<?= (int)$user['user_id'] ?>" <?= $checked ? "checked" : "" ?> />
          </div>
          <?php endforeach; ?>

        </div>

        <div class="align-right" style="margin-top: 20px;">
          <button type="submit" class="submit">Save Access</button>
        </div>
      </form>
    </div>
  </main>
</body>
</html>
