<?php

namespace Tests;

use PHPUnit\Framework\TestCase;

class GeneralGameTest extends TestCase
{
    public function testGameWithOnePlayer()
    {
        $aGame = new \Game();

        $aGame->add("Chet");

        $this->assertEquals(false, $aGame->play());
    }

    public function testGameWithMultiplePlayers()
    {
        $aGame = new \Game();

        $aGame->add("Chet");
        $aGame->add("Pat");
        $aGame->add("Sue");

        $this->assertEquals(true, $aGame->play());
    }
}