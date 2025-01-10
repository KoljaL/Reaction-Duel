<?php
// Database connection
$db = new SQLite3('game.db');

$game_id = $_POST['game_id'] ?? '';
$player2 = $_POST['player2'] ?? '';

if (!$game_id || !$player2) {
  echo json_encode(['error' => 'Game ID and Player 2 name are required']);
  exit;
}

// Check if game exists
$stmt = $db->prepare('SELECT * FROM games WHERE id = :id');
$stmt->bindValue(':id', $game_id, SQLITE3_TEXT);
$result = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

if (!$result) {
  echo json_encode(['error' => 'Game not found']);
  exit;
}

// Update Player 2
$stmt = $db->prepare('UPDATE games SET player2 = :player2 WHERE id = :id');
$stmt->bindValue(':player2', $player2, SQLITE3_TEXT);
$stmt->bindValue(':id', $game_id, SQLITE3_TEXT);
$stmt->execute();

echo json_encode(['success' => true]);
?>