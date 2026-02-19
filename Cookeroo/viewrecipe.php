<?php
session_start();
require_once("db.php");

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$errors = [];

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

$stmt = $conn->prepare("
    SELECT 1 FROM Access 
    WHERE recipe_id = ? AND user_id = ? AND status = 1
");
$stmt->execute([$recipe_id, $user_id]);
if (!$stmt->fetch()) {
    $_SESSION['error'] = "You do not have access to this recipe.";    
    header("Location: recipelist.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $noteText = trim($_POST["noteText"] ?? "");

    if ($noteText === "") {
        $errors['noteText'] = "Note cannot be blank.";
    } elseif (strlen($noteText) > 1300) {
        $errors['noteText'] = "Note cannot exceed 1300 characters.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO Notes (recipe_id, user_id, note, timestamp) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$recipe_id, $user_id, $noteText]);

        // Reload the page so the new note appears
        header("Location: viewrecipe.php?id=$recipe_id");
        exit();
    }
}

$stmt = $conn->prepare("SELECT * FROM Recipes WHERE recipe_id = ?");
$stmt->execute([$recipe_id]);
$recipe = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$recipe) {
    die("Recipe not found.");
}

$stmt = $conn->prepare("SELECT n.*, u.email, u.avatar_url FROM Notes n JOIN Users u ON n.user_id = u.user_id WHERE n.recipe_id = ? ORDER BY n.timestamp DESC");
$stmt->execute([$recipe_id]);
$notes = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>

<head>
  <title>Cookeroo View Recipe</title>
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
      <h2>Title: <?= htmlspecialchars($recipe['title']) ?> | Created on: <?= date('Y-m-d, g:i A', strtotime($recipe['created_at'])) ?></h2>
      <a class="button" href="recipelist.php">Back to Recipe List</a>
    </div>

    <main>
      <div id="view-recipe">
        <h3>Ingredients</h3>
        <ul>
          <?php
          foreach (explode("\n", $recipe['ingredients']) as $ingredient) {
              echo '<li>' . htmlspecialchars(trim($ingredient)) . '</li>';
          }
          ?>
        </ul>

        <h3>Instructions</h3>
        <ol>
          <?php
          foreach (explode("\n", $recipe['instructions']) as $step) {
              echo '<li>' . htmlspecialchars(trim($step)) . '</li>';
          }
          ?>
        </ol>
      </div>

      <div id="cooking-notes">
        <h4 class="notes">COOKING NOTES</h4>

        <?php if (count($notes) === 0): ?>
          <p>Add note: </p>
        <?php else: ?>
          <?php foreach ($notes as $note): ?>
            <div class="note">
              <img src="<?= htmlspecialchars($note['avatar_url'] ?: 'images/default-avatar.png') ?>" class="avatar" alt="User Avatar" />
              <div class="note-content">
                <strong>@<?= htmlspecialchars($note['email']) ?></strong>
                <p class="timestamp"><?= date('Y-m-d, g:i A', strtotime($note['timestamp'])) ?></p>
                <p><?= nl2br(htmlspecialchars($note['note'])) ?></p>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>

        <div id="add-note">
          <h5>Add Note:</h5>
          <form id="noteForm" method="post" action="">
            <textarea id="noteText" name="noteText" rows="4" cols="50"><?= htmlspecialchars($_POST['noteText'] ?? '') ?></textarea><br /><br />
            <div id="err-note" class="error-text"><?= $errors['noteText'] ?? '' ?></div>
            <button type="submit">Save changes</button>
          </form>
        </div>

        <div id="noteCounter" class="charCounter">
          <span id="charCount"><?= strlen($_POST['noteText'] ?? '') ?></span>/<span id="charMax">1300</span> characters
          <span id="charOver" class="char-over"></span>
        </div>
      </div>
    </main>

    <script src="js/eventHandlers.js"></script>
</body>
</html>
