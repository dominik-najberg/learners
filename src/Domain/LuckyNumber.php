<?php

namespace App\Domain;

use Exception;

class LuckyNumber
{
    public function generate(int $max = 100): int
    {
        try {
            return random_int(0, $max);
        } catch (Exception $e) {
            return 0;
        }
    }
}