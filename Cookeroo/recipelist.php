<?php
session_start();
require_once("db.php");

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

$error_message = '';
if (!empty($_SESSION['error'])) {
    $error_message = $_SESSION['error'];
    unset($_SESSION['error']); // Clear it after showing
}

try {
    $conn = new PDO("mysql:host=localhost;dbname=ejo252", "ejo252", "Cannamon101@", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("DB Connection failed: " . $e->getMessage());
}

$sql = "
    SELECT r.recipe_id, r.title, r.created_at, u.email AS creator,
          COUNT(n.note_id) AS note_count,
          MAX(n.timestamp) AS last_note_date
    FROM Recipes r
    JOIN Users u ON r.user_id = u.user_id
    LEFT JOIN Notes n ON r.recipe_id = n.recipe_id
    JOIN Access a ON r.recipe_id = a.recipe_id AND a.user_id = :user_id AND a.status = 1
    GROUP BY r.recipe_id
    ORDER BY r.created_at DESC
";

$stmt = $conn->prepare($sql);
$stmt->execute([':user_id' => $user_id]);
$recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);

function timeAgo($time) {
    $diff = time() - $time;
    if ($diff < 60) return $diff . " seconds ago";
    $diff = round($diff / 60);
    if ($diff < 60) return $diff . " minutes ago";
    $diff = round($diff / 60);
    if ($diff < 24) return $diff . " hours ago";
    $diff = round($diff / 24);
    if ($diff < 7) return $diff . " days ago";
    if ($diff < 30) return round($diff / 7) . " weeks ago";
    if ($diff < 365) return round($diff / 30) . " months ago";
    return round($diff / 365) . " years ago";
}

?>

<!DOCTYPE html>
<html>
<head>
  <title>Cookeroo Recipe List</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
    <div id="header">
        <header class="recipe-list">
            <h1>COOKEROO RECIPES</h1>
         </header>
    </div>
    <div class="top-bar">
      <a class="button" href="logout.php">Sign Out</a>
      <h2>RECIPE LIST</h2>
      <a class="button" href="createrecipe.php">Create New</a>
    </div>
    <main>
      <?php if ($error_message): ?>
      <div class="error-text"><?= htmlspecialchars($error_message) ?></div>
      <?php endif; ?>
      <?php if (count($recipes) === 0): ?>
        <p>You have no recipes to display.</p>
      <?php else: ?>
        <?php foreach ($recipes as $recipe): ?>
          <div id="list">
            <h3>Title: <?= htmlspecialchars($recipe['title']) ?></h3>
            <h4>Creator: @<?= htmlspecialchars($recipe['creator']) ?></h4>
            <h4>Created on: <?= date('m/d/Y, g:i A', strtotime($recipe['created_at'])) ?></h4>
            <h4>Note Count: <?= $recipe['note_count'] ?></h4>
            <h4>Last updated: 
              <?= 
                $recipe['last_note_date'] 
                ? htmlspecialchars(timeAgo(strtotime($recipe['last_note_date']))) 
                : "No notes yet" 
              ?>
            </h4>
            <div class="recipe-options">
              <a class="button" href="viewrecipe.php?id=<?= $recipe['recipe_id'] ?>">View Recipe</a>
              <a class="button" href="manageaccess.php?id=<?= $recipe['recipe_id'] ?>">Manage Access</a>
            </div>
          </div>
          <hr>
        <?php endforeach; ?>
      <?php endif; ?>
    </main>
</body>
</html>


