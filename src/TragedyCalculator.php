<?php

namespace App;

use App\PerformanceCalculator;

class TragedyCalculator extends PerformanceCalculator
{
    public function amount()
    {
        $result = 40000;
        if ($this->aPerformance['audience'] > 30) {
            $result += 1000 * ($this->aPerformance['audience'] - 30);
        }
        return $result;
    }
}
