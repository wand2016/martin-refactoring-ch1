<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    /**
     * @test
     * @dataProvider dataProvider
     */
    public function statement_正しい結果を得る(
        array $invoice,
        array $plays,
        string $statementExpected
    ) {
        $output = statement($invoice, $plays);

        $this->assertSame(
            $statementExpected,
            $output
        );
    }

    function dataProvider()
    {
        return [
            [
                [
                    'customer' => 'BigCo',
                    'performances' => [
                        [
                            'playID' => 'hamlet',
                            'audience' => 55,
                        ],
                        [
                            'playID' => 'as-like',
                            'audience' => 35,
                        ],
                        [
                            'playID' => 'othello',
                            'audience' => 40,
                        ],
                    ],
                ],
                [
                    'hamlet' => [
                        'name' => 'Hamlet',
                        'type' => 'tragedy',
                    ],
                    'as-like' => [
                        'name' => 'As You Like It',
                        'type' => 'comedy',
                    ],
                    'othello' => [
                        'name' => 'Othello',
                        'type' => 'tragedy',
                    ],
                ],
                <<< EOL
Statement for BigCo  Hamlet: $650.00(55 seats)
  As You Like It: $580.00(35 seats)
  Othello: $500.00(40 seats)
Amount owed is $1730.00
You earned 47 credits

EOL
            ]
        ];
    }
}
