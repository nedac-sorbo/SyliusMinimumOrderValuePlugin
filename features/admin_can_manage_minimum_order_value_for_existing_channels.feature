@minimum_order_value @admin @edit @javascript
Feature:
  As an administrator
  I need to be able to manage the minimum order value
  In order to keep non-profitable orders out the door

  Background:
    Given the store has currency "Euro"
    And the store has locale "English (United States)"
    And the store operates on a channel named "Web Channel" in "EUR" currency
    And there is an administrator "admin@example.com" identified by "sylius"
    And I want to log in
    And I specify the username as "admin@example.com"
    And I specify the password as "sylius"
    And I log in

  Scenario: Editing the minimum order value of a channel that already has a minimum order value set
    And the channel has a minimum order value of "1000"
    And I want to modify a channel "Web Channel"
    When I wait 1 seconds
    Then I should see that the minimum order value enabled toggle is "on"
    And I should see that the minimum order value input is "enabled"
    And I should see that the minimum order value input is "1000.00"
    And I should see that the minimum order value input label is "€"
    When I fill in a minimum order value of "123.45"
    And I save my changes
    Then I should be notified that it has been successfully edited
    And I should see that the minimum order value input is "123.45"

  Scenario: Removing the minimum order value of a channel
    And the channel has a minimum order value of "1000"
    And I want to modify a channel "Web Channel"
    When I wait 1 seconds
    And I set the minimum order value enabled toggle to "off"
    Then I should see that the minimum order value input is "empty"
    And I should see that the minimum order value input is "disabled"
    When I save my changes
    Then I should be notified that it has been successfully edited
    And I should see that the minimum order value input is "empty"
    And I should see that the minimum order value input is "disabled"
    And I should see that the minimum order value enabled toggle is "off"

  Scenario: Setting the minimum order value of a channel that does not have a minimum order value set
    And I want to modify a channel "Web Channel"
    When I wait 1 seconds
    Then I should see that the minimum order value enabled toggle is "off"
    And I should see that the minimum order value input is "disabled"
    And I should see that the minimum order value input is empty
    And I should see that the minimum order value input label is "€"
    When I set the minimum order value enabled toggle to "on"
    And I fill in a minimum order value of "456.78"
    And I save my changes
    And I wait 1 seconds
    Then I should be notified that it has been successfully edited
    And I should see that the minimum order value input is "456.78"
    And I should see that the minimum order value input is "enabled"
    And I should see that the minimum order value enabled toggle is "on"
