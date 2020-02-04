<?php

namespace App\Application;

use App\Domain\LuckyNumber;

class LuckyNumberService
{
    const MAX_NUMBER = 100;

    /** @var LuckyNumber */
    private $luckyNumber;

    public function __construct(LuckyNumber $luckyNumber)
    {
        $this->luckyNumber = $luckyNumber;
    }

    public function getLuckyNumberDTO(): LuckyNumberDTO
    {
        $number = $this->luckyNumber->generate(self::MAX_NUMBER);

        return new LuckyNumberDTO($number);
    }
}