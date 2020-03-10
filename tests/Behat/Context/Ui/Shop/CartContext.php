<?php

declare(strict_types=1);

namespace Tests\Nedac\SyliusMinimumOrderValuePlugin\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;
use Tests\Nedac\SyliusMinimumOrderValuePlugin\Behat\Page\Shop\CartSummaryPageInterface;
use Webmozart\Assert\Assert;

final class CartContext implements Context
{
    private CartSummaryPageInterface $cartSummaryPage;

    public function __construct(CartSummaryPageInterface $cartSummaryPage)
    {
        $this->cartSummaryPage = $cartSummaryPage;
    }

    /**
     * @Then I should see a message stating the minimum order value of :minimum and a difference of :difference
     * @param string $minimum
     * @param string $difference
     */
    public function iShouldSeeAMessageStatingTheMinimumOrderValueOfAndADifferenceOf(
        string $minimum,
        string $difference
    ): void {
        Assert::true($this->cartSummaryPage->hasMessageStating($minimum, $difference));
    }

    /**
     * @Then the checkout button is disabled
     */
    public function theCheckoutButtonIsDisabled(): void
    {
        Assert::true($this->cartSummaryPage->isCheckoutButtonDisabled());
    }

    /**
     * @When I click the cart summary widget button
     */
    public function iClickTheCartSummaryWidgetButton(): void
    {
        $this->cartSummaryPage->clickCartSummaryButton();
    }

    /**
     * @Then I should not see the checkout button
     */
    public function iShouldNotSeeTheCheckoutButton(): void
    {
        Assert::true($this->cartSummaryPage->isWidgetCheckoutButtonHidden());
    }

    /**
     * @Then the checkout button is enabled
     */
    public function theCheckoutButtonIsEnabled(): void
    {
        Assert::false($this->cartSummaryPage->isCheckoutButtonDisabled());
    }

    /**
     * @Then I should not see a message stating the minimum order value of :minimum and a difference of :difference
     * @param string $minimum
     * @param string $difference
     */
    public function iShouldNotSeeAMessageStatingTheMinimumOrderValueOfAndADifferenceOf(
        string $minimum,
        string $difference
    ): void {
        Assert::false($this->cartSummaryPage->hasMessageStating($minimum, $difference));
    }

    /**
     * @Then I should see the checkout button
     */
    public function iShouldSeeTheCheckoutButton(): void
    {
        Assert::false($this->cartSummaryPage->isWidgetCheckoutButtonHidden());
    }

    /**
     * @Then I should be redirected to the cart summary page
     */
    public function iShouldBeRedirectedToTheCartSummaryPage(): void
    {
        Assert::true($this->cartSummaryPage->isOpen());
    }
}
