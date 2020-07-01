@theme_setup
Feature:
  As a visitor
  I want it to be clear that I cannot order when the minimum order value is not reached
  In order to not have a negative experience

  Scenario: Setup channel with theme
    Given the store has currency "Euro"
    And the store has locale "English (United States)"
    And the store operates on a channel named "Web Channel" in "EUR" currency
    And the channel has a minimum order value of "15.00"
    And the store classifies its products as "Fruits"
    And the store has "nedac/bootstrap-theme" theme
    And channel "Web Channel" uses "nedac/bootstrap-theme" theme
