<?php

declare(strict_types=1);

namespace Tests\Nedac\SyliusMinimumOrderValuePlugin\Behat\Page\Shop;

interface CartSummaryPageInterface
{
    public function hasMessageStating(string $minimum, string $difference): bool;
    public function isCheckoutButtonDisabled(): bool;
    public function clickCartSummaryButton(): void;
    public function isWidgetCheckoutButtonHidden(): bool;
    public function isOpen(): bool;
}
