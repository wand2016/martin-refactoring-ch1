<?php

namespace App;

use App\PerformanceCalculator;

class CreateStatementData
{
    public function __invoke($invoice, $plays)
    {
        $playFor = function ($perf) use ($plays) {
            return $plays[$perf['playID']];
        };

        $amountFor = function ($aPerformance) use (
            $playFor
        ){
            return (new PerformanceCalculator(
                $aPerformance,
                $playFor($aPerformance)
            ))->amount();
        };

        $volumeCreditsFor = function ($aPerformance) use ($playFor) {
            return (new PerformanceCalculator(
                $aPerformance,
                $playFor($aPerformance)
            ))->volumeCredits();
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
