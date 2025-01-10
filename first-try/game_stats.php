<?php
// Database connection
$db = new SQLite3('game.db');

$game_id = $_GET['game_id'] ?? '';

if (!$game_id) {
  echo json_encode(['error' => 'Game ID is required']);
  exit;
}

// Fetch game state
$stmt = $db->prepare('SELECT * FROM games WHERE id = :id');
$stmt->bindValue(':id', $game_id, SQLITE3_TEXT);
$result = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

if (!$result) {
  echo json_encode(['error' => 'Game not found']);
  exit;
}

// Return game state
echo json_encode([
  'player1' => $result['player1'],
  'player2' => $result['player2'],
  'results' => json_decode($result['results'], true),
]);
?>