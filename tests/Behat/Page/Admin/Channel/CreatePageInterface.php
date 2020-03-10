<?php

declare(strict_types=1);

namespace Tests\Nedac\SyliusMinimumOrderValuePlugin\Behat\Page\Admin\Channel;

interface CreatePageInterface
{
    public function assertMinimumOrderValueEnabledToggleOn(): void;
    public function assertMinimumOrderValueEnabledToggleOff(): void;
    public function isMinimumOrderValueInputDisabled(): bool;
    public function isMinimumOrderValueInputEmpty(): bool;
    public function isMinimumOrderValueInputLabelText(string $labelText): bool;
    public function enable(): void;
    public function disable(): void;
    public function fillInMinimumOrderValue(string $minimum): void;
    public function isMinimumOrderValueInputValue(string $value): bool;
}
