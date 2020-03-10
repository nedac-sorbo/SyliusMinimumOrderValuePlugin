<?php

declare(strict_types=1);

namespace Tests\Nedac\SyliusMinimumOrderValuePlugin\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Doctrine\Common\Persistence\ObjectManager;
use Nedac\SyliusMinimumOrderValuePlugin\Model\ChannelInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Tests\Nedac\SyliusMinimumOrderValuePlugin\Behat\Page\Admin\Channel\CreatePageInterface;
use Webmozart\Assert\Assert;

final class ChannelContext implements Context
{
    private CreatePageInterface $createPage;
    private SharedStorageInterface $sharedStorage;
    private ObjectManager $channelManager;

    public function __construct(
        CreatePageInterface $createPage,
        SharedStorageInterface $sharedStorage,
        ObjectManager $channelManager
    ) {
        $this->createPage = $createPage;
        $this->sharedStorage = $sharedStorage;
        $this->channelManager = $channelManager;
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

    /**
     * @When I wait :seconds seconds
     * @param string $seconds
     */
    public function iWaitSeconds(string $seconds): void
    {
        sleep((int) $seconds);
    }

    /**
     * @Given the channel has a minimum order value of :minimum
     * @param string $minimum
     */
    public function theChannelHasAMinimumOrderValueOf(string $minimum): void
    {
        /** @var ChannelInterface $channel */
        $channel = $this->sharedStorage->get('channel');
        $channel->setMinimumOrderValue(((int) ((float) $minimum) * 100));

        $this->channelManager->flush();
    }
}
