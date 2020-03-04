<?php

declare(strict_types=1);

namespace Nedac\SyliusMinimumOrderValuePlugin\Model;

interface MinimumOrderValueAwareInterface
{
    public function getMinimumOrderValue(): ?int;
    public function setMinimumOrderValue(?int $minimum): void;
}
