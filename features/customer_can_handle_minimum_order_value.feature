@minimum_order_value @visitor @javascript
Feature:
  As a visitor
  I want it to be clear that I cannot order when the minimum order value is not reached
  In order to not have a negative experience

  Background:
    Given the store has currency "Euro"
    And the store has locale "English (United States)"
    And the store operates on a channel named "Web Channel" in "EUR" currency
    And the channel has a minimum order value of "15.00"
    And the store classifies its products as "Fruits"

  Scenario: Checkout button is disabled and message is displayed on cart summary page when the minimum is not reached
    And the store has a product "Banana" with code "banana"
    And this product belongs to "Fruits"
    And this product's price is "€1.50"
    And I add 2 of them to my cart
    And the store has a product "Pineapple" with code "pineapple"
    And this product belongs to "Fruits"
    And this product's price is "€5.00"
    And I add 2 of them to my cart
    When I see the summary of my cart
    Then I should see a message stating the minimum order value of "€15.00" and a difference of "€2.00"
    And the checkout button is disabled

  Scenario: Checkout button is enabled and message is not displayed on cart summary page when the minimum is reached
    And the store has a product "Pineapple" with code "pineapple"
    And this product belongs to "Fruits"
    And this product's price is "€5.00"
    And I add 3 of them to my cart
    When I see the summary of my cart
    Then I should not see a message stating the minimum order value of "€15.00" and a difference of "€2.00"
    And the checkout button is enabled

  Scenario: Checkout button is hidden in cart summary widget when the minimum is not reached
    And the store has a product "Banana" with code "banana"
    And this product belongs to "Fruits"
    And this product's price is "€1.50"
    And I add 2 of them to my cart
    And the store has a product "Pineapple" with code "pineapple"
    And this product belongs to "Fruits"
    And this product's price is "€5.00"
    And I add 2 of them to my cart
    When I click the cart summary widget button
    Then I should not see the checkout button

  Scenario: Checkout button is visible in cart summary widget when the minimum is reached
    And the store has a product "Pineapple" with code "pineapple"
    And this product belongs to "Fruits"
    And this product's price is "€5.00"
    And I add 4 of them to my cart
    When I click the cart summary widget button
    Then I should see the checkout button

  Scenario: The checkout routes are redirected to the cart summary page when the minimum is not reached
    And the store has a product "Banana" with code "banana"
    And this product belongs to "Fruits"
    And this product's price is "€1.50"
    And I add 2 of them to my cart
    When I try to open checkout addressing page
    Then I should be redirected to the cart summary page
    When I try to open checkout shipping page
    Then I should be redirected to the cart summary page
    When I try to open checkout payment page
    Then I should be redirected to the cart summary page
    When I try to open checkout complete page
    Then I should be redirected to the cart summary page

  Scenario: It is possible to start the checkout when the minimum is reached
    And the store has a product "Pineapple" with code "pineapple"
    And this product belongs to "Fruits"
    And this product's price is "€5.00"
    And I add 5 of them to my cart
    Then I am at the checkout addressing step
