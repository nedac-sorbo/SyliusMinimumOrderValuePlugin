<?php

declare(strict_types=1);

namespace Tests\Nedac\SyliusMinimumOrderValuePlugin\Behat\Context;

use Behat\Behat\Context\Context;

final class WaitContext implements Context
{
    /**
     * @When I wait :seconds seconds
     * @param string $seconds
     */
    public function iWaitSeconds(string $seconds): void
    {
        sleep((int) $seconds);
    }
}
