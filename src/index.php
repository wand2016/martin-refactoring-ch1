<?php

function statement($invoice, $plays){

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


    $enrichPerformance = function ($aPerformance) use (
        $playFor,
        $amountFor,
        $volumeCreditsFor
    ){
        // PHPの配列は値渡し
        $aPerformance['play'] = $playFor($aPerformance);
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
    return renderPlainText($statementData);
}

function renderPlainText($data)
{
    $usd = function ($aNumber) {
        $format = '$%.2f';
        return sprintf($format, $aNumber / 100);
    };

    $totalVolumeCredits = function () use (
        $data
    ) {
        $volumeCredits = 0;
        foreach ($data['performances'] as $perf) {
            $volumeCredits += $perf['volumeCredits'];
        }
        return $volumeCredits;
    };

    $totalAmount = function () use ($data) {
        $result = 0;
        foreach ($data['performances'] as $perf) {
            $result += $perf['amount'];
        }
        return $result;
    };



    // ----------------------------------------


    $result = "Statement for ${data['customer']}";
    foreach ($data['performances'] as $perf) {
        // print line for this order
        $result .= '  ' . $perf['play']['name'] . ': ' . $usd($perf['amount']) . "(${perf['audience']} seats)" . PHP_EOL;
    }

    $result .= 'Amount owed is ' . $usd($totalAmount()) . PHP_EOL;
    $result .= 'You earned ' . $totalVolumeCredits() . ' credits' . PHP_EOL;
    return $result;
}
