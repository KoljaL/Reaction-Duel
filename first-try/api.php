<?php
// showall errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Initialize SQLite3 database connection
$db = new SQLite3('game.db');

// Function to create a new game
function create_game($player1_name) {
  global $db;
  $game_id = bin2hex(random_bytes(8));  // Generate a random game ID
  $stmt = $db->prepare('INSERT INTO games (id, player1, player2, results) VALUES (:id, :player1, NULL, :results)');
  $stmt->bindValue(':id', $game_id, SQLITE3_TEXT);
  $stmt->bindValue(':player1', $player1_name, SQLITE3_TEXT);
  $stmt->bindValue(':results', json_encode([]), SQLITE3_TEXT);
  $stmt->execute();
  return ['game_id' => $game_id];
}

// Function to join a game
function join_game($game_id, $player2_name) {
  global $db;
  $stmt = $db->prepare('UPDATE games SET player2 = :player2 WHERE id = :id');
  $stmt->bindValue(':player2', $player2_name, SQLITE3_TEXT);
  $stmt->bindValue(':id', $game_id, SQLITE3_TEXT);
  $stmt->execute();
  return ['success' => true];
}

// Function to get the game state
function get_game_state($game_id) {
  global $db;
  $stmt = $db->prepare('SELECT * FROM games WHERE id = :id');
  $stmt->bindValue(':id', $game_id, SQLITE3_TEXT);
  $result = $stmt->execute()->fetchArray(SQLITE3_ASSOC);
  if ($result) {
    return [
      'game_id' => $result['id'],
      'player1' => $result['player1'],
      'player2' => $result['player2'],
      'results' => json_decode($result['results'], true)
    ];
  }
  return ['error' => 'Game not found'];
}

// Function to update game results
function update_game_results($game_id, $results) {
  global $db;
  $stmt = $db->prepare('UPDATE games SET results = :results WHERE id = :id');
  $stmt->bindValue(':results', json_encode($results), SQLITE3_TEXT);
  $stmt->bindValue(':id', $game_id, SQLITE3_TEXT);
  $stmt->execute();
  return ['success' => true];
}

// Routing logic for API requests
header('Content-Type: application/json');
$request_method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents('php://input'), true);

// Handle different API endpoints
if ($request_method == 'POST') {
  if (isset($data['action'])) {
    switch ($data['action']) {
      case 'create_game':
        if (isset($data['player1'])) {
          echo json_encode(create_game($data['player1']));
        } else {
          echo json_encode(['error' => 'Player1 name is required']);
        }
        break;
      case 'join_game':
        if (isset($data['game_id']) && isset($data['player2'])) {
          echo json_encode(join_game($data['game_id'], $data['player2']));
        } else {
          echo json_encode(['error' => 'Game ID and Player2 name are required']);
        }
        break;
      case 'update_results':
        if (isset($data['game_id']) && isset($data['results'])) {
          echo json_encode(update_game_results($data['game_id'], $data['results']));
        } else {
          echo json_encode(['error' => 'Game ID and results are required']);
        }
        break;
      default:
        echo json_encode(['error' => 'Unknown action']);
    }
  }
} elseif ($request_method == 'GET') {
  if (isset($_GET['game_id'])) {
    echo json_encode(get_game_state($_GET['game_id']));
  } else {
    echo json_encode(['error' => 'Game ID is required']);
  }
} else {
  echo json_encode(['error' => 'Invalid request method']);
}
?>