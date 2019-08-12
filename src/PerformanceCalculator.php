<?php

namespace App;

abstract class PerformanceCalculator
{
    protected $aPerformance;
    protected $aPlay;

    public function __construct(
        $aPerformance,
        $aPlay
    ) {
        $this->aPerformance = $aPerformance;
        $this->aPlay = $aPlay;
    }

    public function play()
    {
        return $this->aPlay;
    }

    public function amount()
    {
        throw new Error('subclass responsibility');
    }

    public function volumeCredits()
    {
        return max($this->aPerformance['audience'] - 30, 0);
    }
}
