<?php

declare(strict_types=1);

namespace Nedac\SyliusMinimumOrderValuePlugin\Model;

use Doctrine\ORM\Mapping as ORM;

trait MinimumOrderValueTrait
{
    /**
     * @ORM\Column(name="minimum_order_value", type="integer", nullable=true)
     */
    private ?int $minimumOrderValue = null;

    public function getMinimumOrderValue(): ?int
    {
        return $this->minimumOrderValue;
    }

    public function setMinimumOrderValue(?int $minimumOrderValue): void
    {
        $this->minimumOrderValue = $minimumOrderValue;
    }
}
