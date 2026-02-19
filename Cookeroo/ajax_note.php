<?php
session_start();
require_once("db.php");

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];
$recipe_id = $_POST['recipe_id'] ?? null;
$noteText = trim($_POST['noteText'] ?? '');

if (!$recipe_id || !is_numeric($recipe_id)) {
    echo json_encode(['error' => 'Invalid recipe ID']);
    exit();
}

if ($noteText === '' || strlen($noteText) > 1300) {
    echo json_encode(['error' => 'Note is empty or too long']);
    exit();
}

try {
    $conn = new PDO("mysql:host=localhost;dbname=ejo252", "ejo252", "Cannamon101@", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    $stmt = $conn->prepare("SELECT 1 FROM Access WHERE recipe_id = ? AND user_id = ? AND status = 1");
    $stmt->execute([$recipe_id, $user_id]);
    if (!$stmt->fetch()) {
        echo json_encode(['error' => 'No access to this recipe']);
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO Notes (recipe_id, user_id, note, timestamp) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$recipe_id, $user_id, $noteText]);

    $note_id = $conn->lastInsertId();
    $stmt = $conn->prepare("SELECT n.note_id, n.note, n.timestamp, u.email, u.avatar_url 
                            FROM Notes n 
                            JOIN Users u ON n.user_id = u.user_id 
                            WHERE n.note_id = ?");
    $stmt->execute([$note_id]);
    $newNote = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([$newNote]);

} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
