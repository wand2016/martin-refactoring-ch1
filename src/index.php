<?php

function statement($invoice, $plays)
{
    $playFor = function ($perf) use ($plays) {
        return $plays[$perf['playID']];
    };

    $amountFor = function ($aPerformance) use ($playFor)
    {
        $result = 0;
        switch ($playFor($aPerformance)['type']) {
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
                throw new Error('unknown type: ' . $playFor($aPerformance)['type']);
        }

        return $result;
    };

    $volumeCreditsFor = function ($aPerformance) use ($playFor) {
        $result = 0;
        $result += max($aPerformance['audience'] - 30, 0);
        if ('comedy' === $playFor($aPerformance)['type']) $result += floor($aPerformance['audience'] / 5);
        return $result;
    };

    $usd = function($aNumber) {
        $format = '$%.2f';
        return sprintf($format, $aNumber / 100);
    };

    $totalVolumeCredits = function() use (
        $invoice,
        $volumeCreditsFor
    ) {
        $volumeCredits = 0;
        foreach ($invoice['performances'] as $perf) {
            $volumeCredits += $volumeCreditsFor($perf);
        }
        return $volumeCredits;
    };

    // ----------------------------------------


    $result = "Statement for ${invoice['customer']}";
    foreach ($invoice['performances'] as $perf) {
        // print line for this order
        $result .= '  ' . $playFor($perf)['name']. ': ' . $usd($amountFor($perf)) . "(${perf['audience']} seats)" . PHP_EOL;
    }
    $totalAmount = 0;
    foreach ($invoice['performances'] as $perf) {
        $totalAmount += $amountFor($perf);
    }


    $result .= 'Amount owed is ' . $usd($totalAmount) . PHP_EOL;
    $result .= 'You earned '. $totalVolumeCredits() . ' credits' . PHP_EOL;
    return $result;
}
