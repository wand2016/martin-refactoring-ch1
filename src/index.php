<?php

function statement($invoice, $plays)
{
    $amountFor = function ($perf, $play)
    {
        $result = 0;
        switch ($play['type']) {
            case 'tragedy':
                $result = 40000;
                if ($perf['audience'] > 30) {
                    $result += 1000 * ($perf['audience'] - 30);
                }
                break;
            case 'comedy':
                $result = 30000;
                if ($perf['audience'] > 20) {
                    $result += 10000 + 500 * ($perf['audience'] - 20);
                }
                $result += 300 * $perf['audience'];
                break;
            default:
                throw new Error("unknown type: ${$play['type']}");
        }

        return $result;
    };

    $totalAmount = 0;
    $volumeCredits = 0;
    $result = "Statement for ${invoice['customer']}";
    $format = '$%.2f';

    foreach ($invoice['performances'] as $perf) {
        $play = $plays[$perf['playID']];
        $thisAmount = $amountFor($perf, $play);

        // add volume credits
        $volumeCredits += max($perf['audience'] - 30, 0);
        // add extra credit for every ten comedy attendees
        if ('comedy' === $play['type']) $volumeCredits += floor($perf['audience'] / 5);

        // print line for this order
        $result .= "  ${play['name']}: " . sprintf($format, $thisAmount / 100) . "(${perf['audience']} seats)" . PHP_EOL;
        $totalAmount += $thisAmount;
    }

    $result .= 'Amount owed is ' . sprintf($format, $totalAmount / 100) . PHP_EOL;
    $result .= "You earned ${volumeCredits} credits" . PHP_EOL;
    return $result;
}
