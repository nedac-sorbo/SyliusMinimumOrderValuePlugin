<?php

declare(strict_types=1);

namespace Tests\Nedac\SyliusMinimumOrderValuePlugin\Application\Entity\Channel;

use Doctrine\ORM\Mapping as ORM;
use Nedac\SyliusMinimumOrderValuePlugin\Model\MinimumOrderValueAwareInterface;
use Nedac\SyliusMinimumOrderValuePlugin\Model\MinimumOrderValueTrait;
use Sylius\Component\Core\Model\Channel as BaseChannel;

/**
 * @ORM\Entity
 * @ORM\Table(name="sylius_channel")
 */
class Channel extends BaseChannel implements MinimumOrderValueAwareInterface
{
    use MinimumOrderValueTrait;
}
