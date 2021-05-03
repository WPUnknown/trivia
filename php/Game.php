<?php

function echoln($string)
{
    echo $string . "\n";
}

class Game
{
    private array $players;
    private array $places;
    private array $purses;
    private array $inPenaltyBox;

    private array $popQuestions;
    private array $scienceQuestions;
    private array $sportsQuestions;
    private array $rockQuestions;

    private int $currentPlayer = 0;
    private bool $isGettingOutOfPenaltyBox;

    public function __construct()
    {
        $this->players = [];
        $this->places = [0];
        $this->purses = [0];
        $this->inPenaltyBox = [0];

        $this->popQuestions = [];
        $this->scienceQuestions = [];
        $this->sportsQuestions = [];
        $this->rockQuestions = [];

        for ($i = 0; $i < 50; $i++) {
            array_push($this->popQuestions, "Pop Question " . $i);
            array_push($this->scienceQuestions, ("Science Question " . $i));
            array_push($this->sportsQuestions, ("Sports Question " . $i));
            array_push($this->rockQuestions, $this->createRockQuestion($i));
        }
    }

    public function add($playerName): bool
    {
        array_push($this->players, $playerName);
        $this->places[$this->howManyPlayers()] = 0;
        $this->purses[$this->howManyPlayers()] = 0;
        $this->inPenaltyBox[$this->howManyPlayers()] = false;

        echoln($playerName . " was added");
        echoln("They are player number " . count($this->players));
        return true;
    }

    public function play(): bool
    {
        if (!$this->isPlayable()) {
            echoln("Can't play the game with just 1 player");
            return false;
        }

        do {
            $this->roll(rand(0, 5) + 1);

            if (rand(0, 9) == 7) {
                $notAWinner = $this->wrongAnswer();
            } else {
                $notAWinner = $this->wasCorrectlyAnswered();
            }
        } while ($notAWinner);

        echoln("Player " . $this->getCurrentPlayerName() . " won the game");

        return true;
    }

    private function createRockQuestion($index): string
    {
        return "Rock Question " . $index;
    }

    private function isPlayable(): bool
    {
        return ($this->howManyPlayers() >= 2);
    }

    private function howManyPlayers(): int
    {
        return count($this->players);
    }

    private function roll($roll)
    {
        echoln($this->getCurrentPlayerName() . " is the current player");
        echoln("They have rolled a " . $roll);

        if ($this->inPenaltyBox[$this->currentPlayer]) {
            if ($roll % 2 != 0) {
                $this->isGettingOutOfPenaltyBox = true;

                echoln($this->getCurrentPlayerName() . " is getting out of the penalty box");
                $this->places[$this->currentPlayer] = $this->places[$this->currentPlayer] + $roll;
                if ($this->places[$this->currentPlayer] > 11) {
                    $this->places[$this->currentPlayer] = $this->places[$this->currentPlayer] - 12;
                }

                echoln(
                    $this->players[$this->currentPlayer]
                    . "'s new location is "
                    . $this->places[$this->currentPlayer]
                );
                echoln("The category is " . $this->currentCategory());
                $this->askQuestion();
            } else {
                echoln($this->getCurrentPlayerName() . " is not getting out of the penalty box");
                $this->isGettingOutOfPenaltyBox = false;
            }
        } else {
            $this->places[$this->currentPlayer] = $this->places[$this->currentPlayer] + $roll;
            if ($this->places[$this->currentPlayer] > 11) {
                $this->places[$this->currentPlayer] = $this->places[$this->currentPlayer] - 12;
            }

            echoln(
                $this->getCurrentPlayerName()
                . "'s new location is "
                . $this->places[$this->currentPlayer]
            );
            echoln("The category is " . $this->currentCategory());
            $this->askQuestion();
        }
    }

    private function askQuestion()
    {
        if ($this->currentCategory() == "Pop") {
            echoln(array_shift($this->popQuestions));
        }
        if ($this->currentCategory() == "Science") {
            echoln(array_shift($this->scienceQuestions));
        }
        if ($this->currentCategory() == "Sports") {
            echoln(array_shift($this->sportsQuestions));
        }
        if ($this->currentCategory() == "Rock") {
            echoln(array_shift($this->rockQuestions));
        }
    }

    private function currentCategory(): string
    {
        if ($this->places[$this->currentPlayer] == 0) {
            return "Pop";
        }
        if ($this->places[$this->currentPlayer] == 4) {
            return "Pop";
        }
        if ($this->places[$this->currentPlayer] == 8) {
            return "Pop";
        }
        if ($this->places[$this->currentPlayer] == 1) {
            return "Science";
        }
        if ($this->places[$this->currentPlayer] == 5) {
            return "Science";
        }
        if ($this->places[$this->currentPlayer] == 9) {
            return "Science";
        }
        if ($this->places[$this->currentPlayer] == 2) {
            return "Sports";
        }
        if ($this->places[$this->currentPlayer] == 6) {
            return "Sports";
        }
        if ($this->places[$this->currentPlayer] == 10) {
            return "Sports";
        }
        return "Rock";
    }

    private function getCurrentPlayerName(): string
    {
        return $this->players[$this->currentPlayer];
    }

    private function wasCorrectlyAnswered(): bool
    {
        if ($this->inPenaltyBox[$this->currentPlayer]) {
            if ($this->isGettingOutOfPenaltyBox) {
                echoln("Answer was correct!!!!");
                $this->purses[$this->currentPlayer]++;
                echoln(
                    $this->getCurrentPlayerName()
                    . " now has "
                    . $this->purses[$this->currentPlayer]
                    . " Gold Coins."
                );

                $winner = $this->didPlayerWin();

                if ($winner) {
                    $this->nextPlayer();
                }

                return $winner;
            } else {
                $this->nextPlayer();
                return true;
            }
        } else {
            echoln("Answer was correct!!!!");
            $this->purses[$this->currentPlayer]++;
            echoln(
                $this->getCurrentPlayerName()
                . " now has "
                . $this->purses[$this->currentPlayer]
                . " Gold Coins."
            );

            $winner = $this->didPlayerWin();

            if ($winner) {
                $this->nextPlayer();
            }

            return $winner;
        }
    }

    private function wrongAnswer(): bool
    {
        echoln("Question was incorrectly answered");
        echoln($this->getCurrentPlayerName() . " was sent to the penalty box");
        $this->inPenaltyBox[$this->currentPlayer] = true;

        $this->nextPlayer();
        return true;
    }

    private function nextPlayer()
    {
        $this->currentPlayer++;
        if ($this->currentPlayer == count($this->players)) {
            $this->currentPlayer = 0;
        }
    }


    private function didPlayerWin(): bool
    {
        return !($this->purses[$this->currentPlayer] == 6);
    }
}
