<?php

declare(strict_types=1);

namespace Tests\Nedac\SyliusMinimumOrderValuePlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\ORM\EntityManagerInterface;
use Nedac\SyliusMinimumOrderValuePlugin\Model\ChannelInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Webmozart\Assert\Assert;

final class ChannelContext implements Context
{
    private SharedStorageInterface $sharedStorage;
    private EntityManagerInterface $channelManager;
    private ChannelRepositoryInterface $repository;

    public function __construct(
        SharedStorageInterface $sharedStorage,
        EntityManagerInterface $channelManager,
        ChannelRepositoryInterface $repository
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->channelManager = $channelManager;
        $this->repository = $repository;
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

    /**
     * @Given I'm using channel :name
     */
    public function imUsingChannel(string $name): void
    {
        $channel = $this->repository->findOneBy(['name' => 'Web Channel']);
        Assert::notNull($channel);

        $this->sharedStorage->set('channel', $channel);
    }
}
