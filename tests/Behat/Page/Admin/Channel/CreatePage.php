<?php

declare(strict_types=1);

namespace Tests\Nedac\SyliusMinimumOrderValuePlugin\Behat\Page\Admin\Channel;

use Behat\Mink\Element\NodeElement;
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;
use Webmozart\Assert\Assert;

final class CreatePage extends SymfonyPage implements CreatePageInterface
{
    private function isCheckboxChecked(): bool
    {
        return $this->getSession()->wait(
            500,
            <<<JS
document.getElementById('nedac-sylius-minimum-order-value-plugin-admin-toggle').checked
JS
        );
    }

    public function assertMinimumOrderValueEnabledToggleOn(): void
    {
        Assert::true($this->isCheckboxChecked());
    }

    public function assertMinimumOrderValueEnabledToggleOff(): void
    {
        Assert::false($this->isCheckboxChecked());
    }

    public function isMinimumOrderValueInputState(bool $disabled): bool
    {
        $this->getSession()->executeScript(<<<JS
document.getElementById('sylius_channel_minimumOrderValue').scrollIntoView();
JS
        );

        return $this->getSession()->wait(
            10000,
            sprintf(
                "document.getElementById('sylius_channel_minimumOrderValue').disabled === %s",
                var_export($disabled, true)
            )
        ) === $disabled;
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

    public function toggle(): void
    {
        $this->getSession()->executeScript(<<<JS
const toggle = document.getElementById('nedac-sylius-minimum-order-value-plugin-admin-toggle');
toggle.scrollIntoView();
toggle.click();
JS
        );
    }
}
