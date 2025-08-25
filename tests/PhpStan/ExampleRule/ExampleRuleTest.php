<?php

namespace VendorName\Skeleton\Tests;

use VendorName\Skeleton\PhpStan\ExampleRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

class ExampleRuleTest extends RuleTestCase
{
    protected function getRule(): Rule
    {
        return new ExampleRule();
    }

    /**
     * @test
     */
    public function rule(): void
    {
        $this->analyse([
            __DIR__ . '/Fixtures/fixture.php',
        ], [
//            [
//                'X should not be Y', // asserted error message
//                15, // asserted error line
//            ],
        ]);
    }
}
