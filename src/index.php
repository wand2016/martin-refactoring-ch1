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

    $volumeCreditsFor = function ($perf) use ($playFor) {
        $result = 0;
        $result += max($perf['audience'] - 30, 0);
        if ('comedy' === $playFor($perf)['type']) $result += floor($perf['audience'] / 5);
        return $result;
    };

    // ----------------------------------------

    $totalAmount = 0;
    $volumeCredits = 0;
    $result = "Statement for ${invoice['customer']}";
    $format = '$%.2f';

    foreach ($invoice['performances'] as $perf) {
        $volumeCredits += $volumeCreditsFor($perf);

        // print line for this order
        $result .= '  ' . $playFor($perf)['name']. ': ' . sprintf($format, $amountFor($perf) / 100) . "(${perf['audience']} seats)" . PHP_EOL;
        $totalAmount += $amountFor($perf);
    }

    $result .= 'Amount owed is ' . sprintf($format, $totalAmount / 100) . PHP_EOL;
    $result .= "You earned ${volumeCredits} credits" . PHP_EOL;
    return $result;
}
