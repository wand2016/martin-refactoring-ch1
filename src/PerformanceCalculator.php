<?php

namespace App;

class PerformanceCalculator
{
    private $aPerformance;
    private $aPlay;

    public function __construct(
        $aPerformance,
        $aPlay
    )
    {
        $this->aPerformance = $aPerformance;
        $this->aPlay = $aPlay;
    }

    public function play()
    {
        return $this->aPlay;
    }
}
