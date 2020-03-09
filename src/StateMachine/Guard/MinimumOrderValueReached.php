<?php

declare(strict_types=1);

namespace Nedac\SyliusMinimumOrderValuePlugin\StateMachine\Guard;

use Nedac\SyliusMinimumOrderValuePlugin\Model\ChannelInterface;
use Sylius\Bundle\ShopBundle\Calculator\OrderItemsSubtotalCalculatorInterface;
use Sylius\Component\Core\Model\OrderInterface;

final class MinimumOrderValueReached
{
    private OrderItemsSubtotalCalculatorInterface $calculator;

    public function __construct(OrderItemsSubtotalCalculatorInterface $calculator)
    {
        $this->calculator = $calculator;
    }

    public function isMinimumOrderValueReached(OrderInterface $order): bool
    {
        /** @var ChannelInterface $channel */
        $channel = $order->getChannel();
        $minimumAmount = $channel->getMinimumOrderValue();
        if (null === $minimumAmount) {
            // No minimum is configured
            return true;
        }

        return $this->calculator->getSubtotal($order) >= $minimumAmount;
    }
}
