<?php

require __DIR__ . '/Game.php';

$aGame = new Game();

$aGame->add("Chet");
$aGame->add("Pat");
$aGame->add("Sue");

$aGame->play();
$aGame->printLogs();
