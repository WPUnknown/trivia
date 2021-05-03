<?php

class Game
{
    private int $winningScore = 6;

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

    private array $logs = [];

    public function __construct(int $winningScore = 6)
    {
        $this->winningScore = $winningScore;

        $this->players = [];
        $this->places = [0];
        $this->purses = [0];
        $this->inPenaltyBox = [0];

        $this->popQuestions = [];
        $this->scienceQuestions = [];
        $this->sportsQuestions = [];
        $this->rockQuestions = [];

        $this->generateQuestions();
    }

    public function add($playerName): bool
    {
        array_push($this->players, $playerName);
        $this->places[$this->howManyPlayers()] = 0;
        $this->purses[$this->howManyPlayers()] = 0;
        $this->inPenaltyBox[$this->howManyPlayers()] = false;

        $this->addLogEntry($playerName . " was added");
        $this->addLogEntry("They are player number " . count($this->players));
        return true;
    }

    public function play(): bool
    {
        if (!$this->isPlayable()) {
            $this->addLogEntry("Can't play the game with just 1 player");
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

        $this->addLogEntry("Player " . $this->getCurrentPlayerName() . " won the game");

        return true;
    }

    public function printLogs()
    {
        echo implode("\n", $this->logs);
    }

    private function generateQuestions()
    {
        for ($i = 0; $i < 50; $i++) {
            array_push($this->popQuestions, $this->createQuestion('Pop Question', $i));
            array_push($this->scienceQuestions, $this->createQuestion('Science Question', $i));
            array_push($this->sportsQuestions, $this->createQuestion('Sports Question', $i));
            array_push($this->rockQuestions, $this->createQuestion('Rock Question', $i));
        }
    }

    private function createQuestion(string $type, int $index): string
    {
        return $type . " " . $index;
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
        $this->addLogEntry($this->getCurrentPlayerName() . " is the current player");
        $this->addLogEntry("They have rolled a " . $roll);

        if ($this->inPenaltyBox[$this->currentPlayer]) {
            if ($roll % 2 != 0) {
                $this->isGettingOutOfPenaltyBox = true;

                $this->addLogEntry($this->getCurrentPlayerName() . " is getting out of the penalty box");
                $this->places[$this->currentPlayer] = $this->getCurrentPlacePlayer($this->currentPlayer) + $roll;
                if ($this->getCurrentPlacePlayer($this->currentPlayer) > 11) {
                    $this->places[$this->currentPlayer] = $this->getCurrentPlacePlayer($this->currentPlayer) - 12;
                }

                $this->addLogEntry(
                    $this->players[$this->currentPlayer]
                    . "'s new location is "
                    . $this->getCurrentPlacePlayer($this->currentPlayer)
                );
                $this->addLogEntry("The category is " . $this->currentCategory());
                $this->askQuestion();
            } else {
                $this->addLogEntry($this->getCurrentPlayerName() . " is not getting out of the penalty box");
                $this->isGettingOutOfPenaltyBox = false;
            }
        } else {
            $this->places[$this->currentPlayer] = $this->getCurrentPlacePlayer($this->currentPlayer) + $roll;
            if ($this->getCurrentPlacePlayer($this->currentPlayer) > 11) {
                $this->places[$this->currentPlayer] = $this->getCurrentPlacePlayer($this->currentPlayer) - 12;
            }

            $this->addLogEntry(
                $this->getCurrentPlayerName()
                . "'s new location is "
                . $this->getCurrentPlacePlayer($this->currentPlayer)
            );
            $this->addLogEntry("The category is " . $this->currentCategory());
            $this->askQuestion();
        }
    }

    private function askQuestion()
    {
        if ($this->currentCategory() == "Pop") {
            $this->addLogEntry(array_shift($this->popQuestions));
        }
        if ($this->currentCategory() == "Science") {
            $this->addLogEntry(array_shift($this->scienceQuestions));
        }
        if ($this->currentCategory() == "Sports") {
            $this->addLogEntry(array_shift($this->sportsQuestions));
        }
        if ($this->currentCategory() == "Rock") {
            $this->addLogEntry(array_shift($this->rockQuestions));
        }
    }

    private function currentCategory(): string
    {
        $currentPlace = $this->getCurrentPlacePlayer($this->currentPlayer) % 4;

        switch ($currentPlace) {
            case 0:
                return 'Pop';
            case 1:
                return 'Science';
            case 2:
                return 'Sports';
        }

        return "Rock";
    }

    private function getCurrentPlayerName(): string
    {
        return $this->players[$this->currentPlayer];
    }

    private function getCurrentPlacePlayer(int $player): int
    {
        return $this->places[$player];
    }

    private function wasCorrectlyAnswered(): bool
    {
        if ($this->inPenaltyBox[$this->currentPlayer]) {
            if ($this->isGettingOutOfPenaltyBox) {
                $this->addLogEntry("Answer was correct!!!!");
                $this->purses[$this->currentPlayer]++;
                $this->addLogEntry(
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
            $this->addLogEntry("Answer was correct!!!!");
            $this->purses[$this->currentPlayer]++;
            $this->addLogEntry(
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
        $this->addLogEntry("Question was incorrectly answered");
        $this->addLogEntry($this->getCurrentPlayerName() . " was sent to the penalty box");
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
        return !($this->purses[$this->currentPlayer] == $this->winningScore);
    }

    private function addLogEntry(string $line)
    {
        $this->logs[] = $line;
    }
}
