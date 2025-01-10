<?php
// Database connection
$db = new SQLite3('game.db');

$game_id = $_POST['game_id'] ?? '';
$round_data = $_POST['round_data'] ?? ''; // JSON containing round details

if (!$game_id || !$round_data) {
  echo json_encode(['error' => 'Game ID and round data are required']);
  exit;
}

// Fetch current results
$stmt = $db->prepare('SELECT results FROM games WHERE id = :id');
$stmt->bindValue(':id', $game_id, SQLITE3_TEXT);
$result = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

if (!$result) {
  echo json_encode(['error' => 'Game not found']);
  exit;
}

$current_results = json_decode($result['results'], true);
$current_results[] = json_decode($round_data, true);

// Update results
$stmt = $db->prepare('UPDATE games SET results = :results WHERE id = :id');
$stmt->bindValue(':results', json_encode($current_results), SQLITE3_TEXT);
$stmt->bindValue(':id', $game_id, SQLITE3_TEXT);
$stmt->execute();

echo json_encode(['success' => true]);
?>