<?php

$slqite_setup = <<<SQL
CREATE TABLE games (
    id TEXT PRIMARY KEY,
    player1 TEXT NOT NULL,
    player2 TEXT,
    results TEXT
);
SQL;

// create a new database
$db = new SQLite3('game.db');

// create a new table
$db->exec($slqite_setup);

// veryfiy the table was created
if ($db->querySingle('SELECT count(*) FROM games') == 0) {
  echo 'Table created successfully!';
} else {
  echo 'Table creation failed!';
}