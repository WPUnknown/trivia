<?php

include __DIR__ . '/Game.php';

$notAWinner = false;

$aGame = new Game();

$aGame->add("Chet");
$aGame->add("Pat");
$aGame->add("Sue");

$aGame->play();
$aGame->printLogs();
  
