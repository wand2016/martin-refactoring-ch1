<?php

namespace App;

use App\PerformanceCalculator;
use App\TragedyCalculator;
use App\ComedyCalculator;

class CreateStatementData
{
    private function createPerformanceCalculator(
        $aPerformance,
        $aPlay
    ){
        switch($aPlay['type']) {
            case 'tragedy':
                return new TragedyCalculator(
                    $aPerformance,
                    $aPlay
                );
            case 'comedy':
                return new ComedyCalculator(
                    $aPerformance,
                    $aPlay
                );
            default:
                throw new Error('unknown type: ' . $aPlay['type']);
        }
    }

    public function __invoke($invoice, $plays)
    {
        $playFor = function ($perf) use ($plays) {
            return $plays[$perf['playID']];
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
            $playFor
        ) {
            $performanceCalculator = $this->createPerformanceCalculator(
                $aPerformance,
                $playFor($aPerformance)
            );

            $aPerformance['play'] = $performanceCalculator->play();
            $aPerformance['amount'] = $performanceCalculator->amount();
            $aPerformance['volumeCredits'] = $performanceCalculator->volumeCredits();
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
