<?php
session_start();
require_once("db.php");

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit();
}

$user_id = $_SESSION['user_id'];
$latest = $_GET['latest'] ?? '1970-01-01 00:00:00';

try {
    $conn = new PDO("mysql:host=localhost;dbname=ejo252", "ejo252", "Cannamon101@", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    $sql = "
        SELECT r.recipe_id, r.title, r.created_at, u.email AS creator,
               COUNT(n.note_id) AS note_count
        FROM Recipes r
        JOIN Users u ON r.user_id = u.user_id
        LEFT JOIN Notes n ON r.recipe_id = n.recipe_id
        JOIN Access a ON r.recipe_id = a.recipe_id AND a.user_id = :user_id AND a.status = 1
        WHERE r.created_at > :latest
        GROUP BY r.recipe_id
        ORDER BY r.created_at ASC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':user_id' => $user_id,
        ':latest' => $latest
    ]);

    $newRecipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($newRecipes);

} catch (PDOException $e) {
    echo json_encode([]);
}
?>
