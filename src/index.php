<?php

use App\CreateStatementData;

function statement($invoice, $plays){
    $createStatementData = new CreateStatementData();
    return renderPlainText($createStatementData($invoice, $plays));
}

function renderPlainText($data)
{
    $usd = function ($aNumber) {
        $format = '$%.2f';
        return sprintf($format, $aNumber / 100);
    };

    // ----------------------------------------


    $result = "Statement for ${data['customer']}";
    foreach ($data['performances'] as $perf) {
        // print line for this order
        $result .= '  ' . $perf['play']['name'] . ': ' . $usd($perf['amount']) . "(${perf['audience']} seats)" . PHP_EOL;
    }

    $result .= 'Amount owed is ' . $usd($data['totalAmount']) . PHP_EOL;
    $result .= 'You earned ' . $data['totalVolumeCredits'] . ' credits' . PHP_EOL;
    return $result;
}
