<?php

use App\CreateStatementData;

function statement($invoice, $plays)
{
    $createStatementData = new CreateStatementData();
    return renderPlainText($createStatementData($invoice, $plays));
}

function renderPlainText($data)
{
    $result = "Statement for ${data['customer']}";
    foreach ($data['performances'] as $aPerformance) {
        // print line for this order
        $result .= '  ' . $aPerformance['play']['name'] . ': ' . usd($aPerformance['amount']) . "(${aPerformance['audience']} seats)" . PHP_EOL;
    }

    $result .= 'Amount owed is ' . usd($data['totalAmount']) . PHP_EOL;
    $result .= 'You earned ' . $data['totalVolumeCredits'] . ' credits' . PHP_EOL;
    return $result;
}

// stub
function renderHtml($data)
{
    return '';
}

function usd($aNumber)
{
    $format = '$%.2f';
    return sprintf($format, $aNumber / 100);
}
