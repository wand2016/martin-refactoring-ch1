<?php

namespace App;

class PerformanceCalculator
{
    protected $aPerformance;
    protected $aPlay;

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

    public function amount()
    {
        $result = 0;
        switch ($this->play()['type']) {
            case 'tragedy':
                throw new Error('boo');
                break;
            case 'comedy':
                throw new Error('bee');
                break;
            default:
                throw new Error('unknown type: ' . $this->play()['type']);
        }
        return $result;
    }

    public function volumeCredits() {
        $result = 0;
        $result += max($this->aPerformance['audience'] - 30, 0);
        if ('comedy' === $this->play()['type']) $result += floor($this->aPerformance['audience'] / 5);
        return $result;
    }
}
