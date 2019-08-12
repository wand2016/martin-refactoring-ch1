<?php

namespace App;

use App\PerformanceCalculator;

class CreateStatementData
{
    public function __invoke($invoice, $plays)
    {
        $amountFor = function ($aPerformance) {
            $result = 0;
            switch ($aPerformance['play']['type']) {
                case 'tragedy':
                    $result = 40000;
                    if ($aPerformance['audience'] > 30) {
                        $result += 1000 * ($aPerformance['audience'] - 30);
                    }
                    break;
                case 'comedy':
                    $result = 30000;
                    if ($aPerformance['audience'] > 20) {
                        $result += 10000 + 500 * ($aPerformance['audience'] - 20);
                    }
                    $result += 300 * $aPerformance['audience'];
                    break;
                default:
                    throw new Error('unknown type: ' . $aPerformance['play']['type']);
            }

            return $result;
        };

        $playFor = function ($perf) use ($plays) {
            return $plays[$perf['playID']];
        };

        $volumeCreditsFor = function ($aPerformance) {
            $result = 0;
            $result += max($aPerformance['audience'] - 30, 0);
            if ('comedy' === $aPerformance['play']['type']) $result += floor($aPerformance['audience'] / 5);
            return $result;
        };

        $totalVolumeCredits = function ($data) {
            return array_reduce(
                $data['performances'],
                function ($accumulator, $aPerformance) {
                    return $accumulator + $aPerformance['volumeCredits'];
                },
                0
            );
        };

        $totalAmount = function ($data) {
            return array_reduce(
                $data['performances'],
                function ($accumulator, $aPerformance) {
                    return $accumulator + $aPerformance['amount'];
                },
                0
            );
        };


        $enrichPerformance = function ($aPerformance) use (
            $playFor,
            $amountFor,
            $volumeCreditsFor
        ) {
            $performanceCalculator = new PerformanceCalculator(
                $aPerformance,
                $playFor($aPerformance)
            );

            $aPerformance['play'] = $performanceCalculator->play();
            $aPerformance['amount'] = $amountFor($aPerformance);
            $aPerformance['volumeCredits'] = $volumeCreditsFor($aPerformance);
            return $aPerformance;
        };

        $statementData = [];
        $statementData['customer'] = $invoice['customer'];
        $statementData['performances'] = array_map(
            $enrichPerformance,
            $invoice['performances']
        );
        $statementData['totalVolumeCredits'] = $totalVolumeCredits($statementData);
        $statementData['totalAmount'] = $totalAmount($statementData);

        return $statementData;
    }
}
