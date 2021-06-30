<?php

declare(strict_types=1);

namespace Tests\Nedac\SyliusMinimumOrderValuePlugin\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Behat\MinkExtension\Context\RawMinkContext;
use Tests\Nedac\SyliusMinimumOrderValuePlugin\Behat\Page\Admin\Channel\CreatePageInterface;
use Webmozart\Assert\Assert;

final class ChannelContext extends RawMinkContext implements Context
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
            Assert::true($this->createPage->isMinimumOrderValueInputState(true));
        } elseif ('enabled' === $state) {
            Assert::false($this->createPage->isMinimumOrderValueInputState(false));
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
        if ('on' === $onOrOff || 'off' === $onOrOff) {
            $this->createPage->toggle();
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

    /**
     * @When I add the channel
     */
    public function iAddTheChannel(): void
    {
        $this->createPage->addTheChannel();
    }

    /**
     * @Then I follow :link and Leave
     */
    public function iFollowAndLeave(string $link): void
    {
        $this->createPage->iFollowAndLeave($link);
    }

    /**
     * @When I wait :seconds seconds
     */
    public function iWaitSeconds(string $seconds): void
    {
        sleep((int) $seconds);
    }

    /**
     * @BeforeScenario
     */
    public function resetSession(): void
    {
        $this->getSession()->reset();
    }
}
