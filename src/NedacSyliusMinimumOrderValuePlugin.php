<?php

declare(strict_types=1);

namespace Nedac\SyliusMinimumOrderValuePlugin;

use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class NedacSyliusMinimumOrderValuePlugin extends Bundle
{
    use SyliusPluginTrait;
}
