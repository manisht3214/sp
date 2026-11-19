
<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

requireAdmin('../login.php');

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    http_response_code(400);
    exit('Invalid person ID.');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('POST request required.');
}

$stmt = $pdo->prepare("DELETE FROM people WHERE id = ?");
$stmt->execute([$id]);

header("Location: ../index.php");
exit;
