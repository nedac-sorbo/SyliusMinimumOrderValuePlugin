<?php

declare(strict_types=1);

namespace Tests\Nedac\SyliusMinimumOrderValuePlugin\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Tests\Nedac\SyliusMinimumOrderValuePlugin\Behat\Page\Admin\Channel\CreatePageInterface;
use Webmozart\Assert\Assert;

final class ChannelContext implements Context
{
    private CreatePageInterface $createPage;

    public function __construct(CreatePageInterface $createPage)
    {
        $this->createPage = $createPage;
    }

    /**
     * @Then I should see that the minimum order value enabled toggle is :onOrOff
     * @param string $onOrOff
     * @throws \Exception
     */
    public function iShouldSeeThatTheMinimumOrderValueEnabledToggleIs(string $onOrOff): void
    {
        if ('off' === $onOrOff) {
            $this->createPage->assertMinimumOrderValueEnabledToggleOff();
        } elseif ('on' === $onOrOff) {
            $this->createPage->assertMinimumOrderValueEnabledToggleOn();
        } else {
            throw new \Exception('Unsupported');
        }
    }

    /**
     * @Then I should see that the minimum order value input is :state
     * @param string $state
     * @throws \Exception
     */
    public function iShouldSeeThatTheMinimumOrderValueInputIs(string $state): void
    {
        if ('disabled' === $state) {
            Assert::true($this->createPage->isMinimumOrderValueInputDisabled());
        } elseif ('enabled' === $state) {
            Assert::false($this->createPage->isMinimumOrderValueInputDisabled());
        } elseif ('empty' === $state) {
            Assert::true($this->createPage->isMinimumOrderValueInputEmpty());
        } else {
            Assert::true($this->createPage->isMinimumOrderValueInputValue($state));
        }
    }

    /**
     * @Then I should see that the minimum order value input label is :value
     * @param string $value
     */
    public function iShouldSeeThatTheMinimumOrderValueInputLabelIs(string $value): void
    {
        Assert::true($this->createPage->isMinimumOrderValueInputLabelText($value));
    }

    /**
     * @When I set the minimum order value enabled toggle to :onOrOff
     * @param string $onOrOff
     * @throws \Exception
     */
    public function iSetTheMinimumOrderValueEnabledToggleTo(string $onOrOff): void
    {
        if ('on' === $onOrOff) {
            $this->createPage->enable();
        } elseif ('off' === $onOrOff) {
            $this->createPage->disable();
        } else {
            throw new \Exception('Unsupported!');
        }
    }

    /**
     * @When I fill in a minimum order value of :minimum
     * @param string $minimum
     */
    public function iFillInAMinimumOrderValueOf(string $minimum): void
    {
        $this->createPage->fillInMinimumOrderValue($minimum);
    }
}
