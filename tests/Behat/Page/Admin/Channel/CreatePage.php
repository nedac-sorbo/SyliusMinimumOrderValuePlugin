<?php

declare(strict_types=1);

namespace Tests\Nedac\SyliusMinimumOrderValuePlugin\Behat\Page\Admin\Channel;

use Behat\Mink\Element\NodeElement;
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;
use WebDriver\Exception\JavaScriptError;
use Webmozart\Assert\Assert;

final class CreatePage extends SymfonyPage implements CreatePageInterface
{
    private function isCheckboxChecked(): bool
    {
        return $this->getSession()->wait(
            5000,
            <<<JS
    null !== document.getElementById('nedac-sylius-minimum-order-value-plugin-admin-toggle') ?
      document.getElementById('nedac-sylius-minimum-order-value-plugin-admin-toggle').checked :
      false;
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
        $this->getSession()->executeScript(sprintf(
            "const el = document.getElementById('sylius_channel_minimumOrderValue');" .
            " el.value = '%s'; el.scrollIntoView();",
            $minimum
        ));
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
        $session = $this->getSession();
        if (
            ! $session->wait(
                10000,
                "document.getElementById('nedac-sylius-minimum-order-value-plugin-admin-toggle') !== null"
            )
        ) {
            throw new \Exception("Toggle didn't appear!");
        }

        sleep(1);

        $session->executeScript(<<<JS
const toggle = document.getElementById('nedac-sylius-minimum-order-value-plugin-admin-toggle');
toggle.scrollIntoView();
toggle.click();
JS
        );
    }

    public function addTheChannel(): void
    {
        $this->getSession()->executeScript(
            "document.querySelector(" .
            "'#content > div.ui.segment > form > div.ui.buttons > button'" .
            ").click()"
        );
    }

    public function iFollowAndLeave(string $link): void
    {
        $this->getSession()->getPage()->clickLink($link);
        $this->getSession()->getDriver()->acceptAlert();
    }
}
