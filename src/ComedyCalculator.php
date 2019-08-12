<?php

namespace App;

use App\PerformanceCalculator;

class ComedyCalculator extends PerformanceCalculator
{
    public function amount()
    {
        $result = 30000;
        if ($this->aPerformance['audience'] > 20) {
            $result += 10000 + 500 * ($this->aPerformance['audience'] - 20);
        }
        $result += 300 * $this->aPerformance['audience'];
        return $result;
    }

    public function volumeCredits()
    {
        return parent::volumeCredits() + floor($this->aPerformance['audience'] / 5);
    }
}
