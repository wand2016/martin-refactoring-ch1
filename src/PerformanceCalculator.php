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

    public function amount()
    {
        $result = 0;
        switch ($this->play()['type']) {
            case 'tragedy':
                $result = 40000;
                if ($this->aPerformance['audience'] > 30) {
                    $result += 1000 * ($this->aPerformance['audience'] - 30);
                }
                break;
            case 'comedy':
                $result = 30000;
                if ($this->aPerformance['audience'] > 20) {
                    $result += 10000 + 500 * ($this->aPerformance['audience'] - 20);
                }
                $result += 300 * $this->aPerformance['audience'];
                break;
            default:
                throw new Error('unknown type: ' . $this->play()['type']);
        }
        return $result;
    }
}
