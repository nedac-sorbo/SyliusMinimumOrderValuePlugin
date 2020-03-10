<?php

declare(strict_types=1);

namespace Tests\Nedac\SyliusMinimumOrderValuePlugin\Behat\Page\Admin\Channel;

use Behat\Mink\Element\NodeElement;
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;
use Sylius\Behat\Behaviour\Toggles;
use Webmozart\Assert\Assert;

final class CreatePage extends SymfonyPage implements CreatePageInterface
{
    use Toggles;

    protected function getToggleableElement(): NodeElement
    {
        $element = $this->getDocument()->findById('nedac-sylius-minimum-order-value-plugin-admin-toggle');
        Assert::notNull($element);

        return $element;
    }

    public function assertMinimumOrderValueEnabledToggleOn(): void
    {
        $this->assertCheckboxState($this->getToggleableElement(), true);
    }

    public function assertMinimumOrderValueEnabledToggleOff(): void
    {
        $this->assertCheckboxState($this->getToggleableElement(), false);
    }

    public function isMinimumOrderValueInputDisabled(): bool
    {
        $element = $this->getDocument()->findById('sylius_channel_minimumOrderValue');
        Assert::notNull($element);

        return $element->hasAttribute('disabled');
    }

    public function isMinimumOrderValueInputEmpty(): bool
    {
        $element = $this->getDocument()->findById('sylius_channel_minimumOrderValue');
        Assert::notNull($element);
        $value = $element->getValue();
        Assert::string($value);

        return '' === $value;
    }

    public function isMinimumOrderValueInputLabelText(string $labelText): bool
    {
        usleep(500000);

        $element = $this->getDocument()->find(
            'css',
            '#nedac-sylius-minimum-order-value-plugin-admin-segment > div.field > div > div'
        );
        Assert::isInstanceOf($element, NodeElement::class);

        return $element->getText() === $labelText;
    }

    public function fillInMinimumOrderValue(string $minimum): void
    {
        $element = $this->getDocument()->findById('sylius_channel_minimumOrderValue');
        Assert::notNull($element);

        $element->setValue($minimum);
    }

    public function isMinimumOrderValueInputValue(string $value): bool
    {
        $element = $this->getDocument()->findById('sylius_channel_minimumOrderValue');
        Assert::notNull($element);
        $elementValue = $element->getValue();
        Assert::string($elementValue);

        return $elementValue === $value;
    }

    public function getRouteName(): string
    {
        return 'sylius_admin_channel_create';
    }
}
