<?php

declare(strict_types=1);

namespace Tests\Nedac\SyliusMinimumOrderValuePlugin\Behat\Page\Shop;

use Behat\Mink\Element\DocumentElement;
use Behat\Mink\Element\NodeElement;
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;
use Webmozart\Assert\Assert;

final class CartSummaryPage extends SymfonyPage implements CartSummaryPageInterface
{
    public function hasMessageStating(string $minimum, string $difference): bool
    {
        $messageElement = $this->getDocument()->find(
            'xpath',
            'descendant::*[@data-test-minimum-order-value-plugin-message]'
        );

        if (null === $messageElement) {
            return false;
        }

        $text = $messageElement->getText();

        return false !== strpos($text, $minimum) && false !== strpos($text, $difference);
    }

    public function isCheckoutButtonDisabled(): bool
    {
        $checkoutButton = $this->getDocument()->find(
            'xpath',
            'descendant::*[@data-test-checkout-button]'
        );
        Assert::notNull($checkoutButton);

        return $checkoutButton->hasClass('disabled');
    }

    public function clickCartSummaryButton(): void
    {
        $cartButton = $this->getDocument()->findById('sylius-cart-button');
        Assert::notNull($cartButton);

        $cartButton->click();
    }

    public function isWidgetCheckoutButtonHidden(): bool
    {
        /** @var NodeElement|null $popup */
        $popup = $this->getDocument()->waitFor('1000', function (DocumentElement $current): ?NodeElement {
            return $current->findById('nedac-sylius-minimum-order-value-plugin-popup');
        });
        Assert::notNull($popup);

        return !$popup->hasLink('Checkout');
    }

    public function getRouteName(): string
    {
        return 'sylius_shop_cart_summary';
    }
}
