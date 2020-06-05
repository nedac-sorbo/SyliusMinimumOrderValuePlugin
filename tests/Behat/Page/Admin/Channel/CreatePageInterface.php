<?php

declare(strict_types=1);

namespace Tests\Nedac\SyliusMinimumOrderValuePlugin\Behat\Page\Admin\Channel;

interface CreatePageInterface
{
    public function assertMinimumOrderValueEnabledToggleOn(): void;
    public function assertMinimumOrderValueEnabledToggleOff(): void;
    public function isMinimumOrderValueInputState(bool $disabled): bool;
    public function isMinimumOrderValueInputEmpty(): bool;
    public function isMinimumOrderValueInputLabelText(string $labelText): bool;
    public function toggle(): void;
    public function fillInMinimumOrderValue(string $minimum): void;
    public function isMinimumOrderValueInputValue(string $value): bool;
}
