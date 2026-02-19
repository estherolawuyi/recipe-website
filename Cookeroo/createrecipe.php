<?php
session_start();
require_once("db.php");

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$errors = [];
$title = "";
$ingredients = "";
$instructions = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST["recipe-title"] ?? "");
    $ingredients = trim($_POST["recipe-ingredients"] ?? "");
    $instructions = trim($_POST["recipe-steps"] ?? "");

    if ($title === "") {
        $errors["recipe-title"] = "Recipe title cannot be blank.";
    } elseif (strlen($title) > 256) {
        $errors["recipe-title"] = "Recipe title cannot exceed 256 characters.";
    }

    if (empty($errors)) {
        try {
            $conn = new PDO("mysql:host=localhost;dbname=ejo252", "ejo252", "Cannamon101@", [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);

            // recipe
            $stmt = $conn->prepare("INSERT INTO Recipes (user_id, title, ingredients, instructions, created_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([$user_id, $title, $ingredients, $instructions]);

            $recipe_id = $conn->lastInsertId();

            // access row
            $stmt = $conn->prepare("INSERT INTO Access (recipe_id, user_id, status) VALUES (?, ?, 1)");
            $stmt->execute([$recipe_id, $user_id]);

            header("Location: recipelist.php");
            exit();
        } catch (PDOException $e) {
            $errors["database"] = "Database error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Cookeroo Create Recipe</title>
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
      <h2>CREATE NEW RECIPE</h2>
      <a class="button" href="recipelist.php">Back to Recipe List</a>
    </div>
    <main>
        <div id="create-container">
            <form class="auth-form" id="create-recipe-form" method="post" action="">
              <div class="form-input-grid">
                <label for="recipe-title">Recipe Title:</label>
                <input type="text" name="recipe-title" id="recipe-title" value="<?= htmlspecialchars($title) ?>" />
                <div id="err-title" class="error-text"><?= $errors["recipe-title"] ?? "" ?></div>

                <label for="recipe-ingredients">Ingredients:</label>
                <textarea name="recipe-ingredients" id="recipe-ingredients" rows="16"><?= htmlspecialchars($ingredients) ?></textarea>
                <div id="err-ingredients" class="error-text"><?= $errors["recipe-ingredients"] ?? "" ?></div>

                <label for="recipe-steps">Instructions:</label>
                <textarea name="recipe-steps" id="recipe-steps" rows="16"><?= htmlspecialchars($instructions) ?></textarea>
                <div id="err-steps" class="error-text"><?= $errors["recipe-steps"] ?? "" ?></div>

                <?php if (isset($errors["database"])): ?>
                    <div class="error-text"><?= htmlspecialchars($errors["database"]) ?></div>
                <?php endif; ?>
              </div>

              <div class="align-right">
                <button type="submit" class="submit">Create Recipe</button>
              </div>
            </form>
        </div>
    </main>
</body>
</html>
