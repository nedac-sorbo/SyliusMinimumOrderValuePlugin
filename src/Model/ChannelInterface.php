<?php

declare(strict_types=1);

namespace Nedac\SyliusMinimumOrderValuePlugin\Model;

use Sylius\Component\Core\Model\ChannelInterface as BaseChannelInterface;

interface ChannelInterface extends BaseChannelInterface, MinimumOrderValueAwareInterface
{
}
