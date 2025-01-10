<?php

// showall errors 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// Database connection
$db = new SQLite3('game.db');

// Generate unique game ID
$game_id = bin2hex(random_bytes(4)); // 8-character random string
$player1 = $_POST['player1'] ?? '';
if (!$player1) {
  echo json_encode(['error' => 'Player 1 name is required']);
  exit;
}

// Insert into database
$stmt = $db->prepare('INSERT INTO games (id, player1, player2, results) VALUES (:id, :player1, NULL, :results)');
$stmt->bindValue(':id', $game_id, SQLITE3_TEXT);
$stmt->bindValue(':player1', $player1, SQLITE3_TEXT);
$stmt->bindValue(':results', json_encode([]), SQLITE3_TEXT);
$stmt->execute();

echo json_encode(['game_id' => $game_id, 'link' => "https://dev.rasal.de/reaction/game.php?gameID=$game_id"]);
?>