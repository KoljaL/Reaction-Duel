<?php
// Start session to manage game state
session_start();

$game_id = $_GET['gameID'] ?? '';
$player2Name = $_POST['player2'] ?? '';
$player1Name = $_POST['player1'] ?? '';
$playerTurn = 1; // 1 for Player 1, 2 for Player 2

// Handle game creation (Player 1)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($player1Name) && empty($game_id)) {
  $game_id = bin2hex(random_bytes(8));  // Generate a random game ID
  $db = new SQLite3('game.db');
  $stmt = $db->prepare('INSERT INTO games (id, player1, player2, results) VALUES (:id, :player1, NULL, :results)');
  $stmt->bindValue(':id', $game_id, SQLITE3_TEXT);
  $stmt->bindValue(':player1', $player1Name, SQLITE3_TEXT);
  $stmt->bindValue(':results', json_encode([]), SQLITE3_TEXT);
  $stmt->execute();

  // Redirect to the same page with the game ID
  header("Location: ?gameID=$game_id&invite=1");
  exit();
}

// Fetch game data if a game ID is provided
$db = new SQLite3('game.db');
if ($game_id) {
  $stmt = $db->prepare('SELECT * FROM games WHERE id = :id');
  $stmt->bindValue(':id', $game_id, SQLITE3_TEXT);
  $result = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

  if ($result) {
    $player1 = $result['player1'];
    $player2 = $result['player2'];
    $results = json_decode($result['results'], true);
  }
}

// handle invite link
if ($game_id && isset($_GET['invite'])) {
  $gameUrl = "https://dev.rasal.de/reaction/index.php?gameID=$game_id";
}

// Handle Player 2 joining the game
if ($game_id && $player2Name && !$player2) {
  $stmt = $db->prepare('UPDATE games SET player2 = :player2 WHERE id = :id');
  $stmt->bindValue(':player2', $player2Name, SQLITE3_TEXT);
  $stmt->bindValue(':id', $game_id, SQLITE3_TEXT);
  $stmt->execute();
  header("Location: ?gameID=$game_id");  // Reload the page to show the updated game state
  exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Game - Reaction Test</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      padding: 20px;
    }

    #reactionButton {
      display: none;
      padding: 20px;
      font-size: 24px;
      background-color: #4CAF50;
      color: white;
      border: none;
      cursor: pointer;
    }

    #waitingMessage {
      font-size: 18px;
    }

    #resultMessage {
      font-size: 18px;
    }

    #startButton {
      padding: 20px;
      font-size: 24px;
      background-color: #008CBA;
      color: white;
      border: none;
      cursor: pointer;
    }

    #joinForm {
      display: block;
    }
  </style>
</head>

<body>
  <h1>Reaction Game</h1>

  <?php if (!$game_id): ?>
    <!-- Game Creation Form for Player 1 -->
    <h2>Create a New Game</h2>
    <form method="POST">
      <input type="text" name="player1" placeholder="Player 1 Name" required>
      <button type="submit">Create Game</button>
    </form>

  <?php elseif (!$player2 && $game_id): ?>
    <div id="gameLink">
      <h2>Game Created!</h2>
      <p>Share this link with Player 2:</p>
      <input type="text" id="gameUrl" readonly value="<?= $gameUrl ?>" />
    </div>
  <?php elseif (!$player2): ?>
    <!-- Player 2 Join Form -->
    <h2>Game ID: <?= htmlspecialchars($game_id) ?></h2>
    <p><strong>Player 1:</strong> <?= htmlspecialchars($player1) ?></p>
    <div id=" joinForm">
      <p><strong>Player 2:</strong> Enter your name to join the game</p>
      <form method="POST">
        <input type="text" name="player2" placeholder="Player 2 Name" required>
        <button type="submit">Join Game</button>
      </form>
    </div>
  <?php else: ?>
    <!-- Game in Progress -->
    <h2>Game ID: <?= htmlspecialchars($game_id) ?></h2>
    <p><strong>Player 1:</strong> <?= htmlspecialchars($player1) ?> vs. <strong>Player 2:</strong> <?= htmlspecialchars($player2) ?></p>
    <div id="waitingMessage">Waiting for Player 1 to start the round...</div>
    <button id="startButton">Start Round</button>
    <button id="reactionButton">Click Now!</button>
    <div id="resultMessage"></div>
  <?php endif; ?>

  <script>
    let round = 0;
    let playerTurn = 1; // 1 for Player 1, 2 for Player 2
    let roundStartTime = 0;
    let reactionTimes = [];

    // Player 1 starts the round
    document.getElementById('startButton')?.addEventListener('click', function () {
      // Start round for Player 1
      document.getElementById('waitingMessage').innerText = "Wait for the button to appear...";
      this.style.display = 'none';
      playerTurn = 1;
      startRound();
    });

    // Reaction Time (Both Players)
    document.getElementById('reactionButton')?.addEventListener('click', function () {
      const reactionTime = Date.now() - roundStartTime;
      reactionTimes.push({player: playerTurn, time: reactionTime});

      // Show reaction time and switch to the other player's turn
      document.getElementById('resultMessage').innerText = `Player ${playerTurn}'s reaction time: ${reactionTime} ms`;
      playerTurn = (playerTurn === 1) ? 2 : 1;

      // Reset button for next player
      document.getElementById('reactionButton').style.display = 'none';
      setTimeout(() => {
        if (playerTurn === 1) {
          startRound();
        } else {
          document.getElementById('waitingMessage').innerText = "Waiting for Player 1 to start the next round...";
        }
      }, 1000);
    });

    function startRound() {
      setTimeout(() => {
        roundStartTime = Date.now();
        document.getElementById('reactionButton').style.display = 'block';
        document.getElementById('waitingMessage').innerText = "Click the button as fast as you can!";
      }, Math.random() * 3000 + 1000); // Random delay between 1 and 4 seconds
    }

    // Periodically update the game state
    setInterval(() => {
      fetchGameState();
    }, 1000);

    function fetchGameState() {
      fetch(`game_stats.php?game_id=<?= htmlspecialchars($game_id) ?>`)
        .then(response => response.json())
        .then(data => {
          if (data.results) {
            // Handle the results (you can display them or handle logic based on rounds)
          }
        });
    }
  </script>
</body>

</html>