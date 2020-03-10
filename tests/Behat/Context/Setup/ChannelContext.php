<?php

declare(strict_types=1);

namespace Tests\Nedac\SyliusMinimumOrderValuePlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\Common\Persistence\ObjectManager;
use Nedac\SyliusMinimumOrderValuePlugin\Model\ChannelInterface;
use Sylius\Behat\Service\SharedStorageInterface;

final class ChannelContext implements Context
{
    private SharedStorageInterface $sharedStorage;
    private ObjectManager $channelManager;

    public function __construct(SharedStorageInterface $sharedStorage, ObjectManager $channelManager)
    {
        $this->sharedStorage = $sharedStorage;
        $this->channelManager = $channelManager;
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
