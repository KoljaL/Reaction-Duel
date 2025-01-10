<?php
$game_id = $_GET['gameID'] ?? '';

if (!$game_id) {
  echo 'Game not found.';
  exit;
}

// Fetch game data from the database
$db = new SQLite3('game.db');
$stmt = $db->prepare('SELECT * FROM games WHERE id = :id');
$stmt->bindValue(':id', $game_id, SQLITE3_TEXT);
$result = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

if (!$result) {
  echo 'Game not found.';
  exit;
}

$player1 = $result['player1'];
$player2 = $result['player2'];

if (!$player2) {
  // Player 2 has not joined yet
  $waitingForPlayer2 = true;
} else {
  // Player 2 has joined, start the game
  $waitingForPlayer2 = false;
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
      display: none;
    }
  </style>
</head>

<body>
  <h1>Reaction Game</h1>
  <p><strong>Player 1:</strong> <?= htmlspecialchars($player1) ?></p>

  <?php if ($waitingForPlayer2): ?>
    <!-- Player 2's Join Form -->
    <div id="joinForm">
      <p><strong>Player 2:</strong> Enter your name to join the game</p>
      <input type="text" id="player2Name" placeholder="Player 2 Name">
      <button id="joinGameButton">Join Game</button>
    </div>
  <?php else: ?>
    <!-- Player 1 can start the game if Player 2 has joined -->
    <p><strong>Player 2:</strong> <?= htmlspecialchars($player2) ?></p>
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

    // Join Game (Player 2)
    document.getElementById('joinGameButton').addEventListener('click', function () {
      const player2Name = document.getElementById('player2Name').value;

      if (!player2Name) {
        alert('Please enter a name for Player 2.');
        return;
      }

      fetch('join_game.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `game_id=<?= htmlspecialchars($game_id) ?>&player2=${encodeURIComponent(player2Name)}`
      })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            document.getElementById('joinForm').style.display = 'none';
            document.getElementById('waitingMessage').innerText = "Player 2 has joined. Waiting for Player 1 to start the round...";
            document.getElementById('startButton').style.display = 'block';
          } else {
            alert('Error joining the game.');
          }
        });
    });

    // Start Round (Player 1)
    document.getElementById('startButton').addEventListener('click', function () {
      // Start round for Player 1
      document.getElementById('waitingMessage').innerText = "Wait for the button to appear...";
      this.style.display = 'none';
      playerTurn = 1;
      startRound();
    });

    // Reaction Time (Both Players)
    document.getElementById('reactionButton').addEventListener('click', function () {
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
      fetch(`get_game_state.php?game_id=<?= htmlspecialchars($game_id) ?>`)
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