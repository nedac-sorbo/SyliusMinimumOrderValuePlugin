@minimum_order_value @admin @create @javascript
Feature:
  As an administrator
  I need to be able to manage the minimum order value
  In order to keep non-profitable orders out the door

  Background:
    Given the store has currency "Euro"
    And the store has currency "US Dollar"
    And the store has locale "English (United States)"
    And there is an administrator "admin@example.com" identified by "sylius"
    And I want to log in
    And I specify the username as "admin@example.com"
    And I specify the password as "sylius"
    And I log in
    And I want to create a new channel
    And I specify its code as "MOBILE"
    And I name it "Mobile channel"

  Scenario: Adding a new channel without a minimum order value
    When I choose "Euro" as the base currency
    And I choose "English (United States)" as a default locale
    Then I should see that the minimum order value enabled toggle is "off"
    And I should see that the minimum order value input is "disabled"
    And I should see that the minimum order value input is empty
    And I should see that the minimum order value input label is "€"
    When I add the channel
    Then I should be notified that it has been successfully created
    And I should see that the minimum order value input is "empty"
    And the channel "Mobile channel" should appear in the registry

  Scenario: Adding a channel with a minimum order value
    When I choose "US Dollar" as the base currency
    And I choose "English (United States)" as a default locale
    And I set the minimum order value enabled toggle to "on"
    Then I should see that the minimum order value enabled toggle is "on"
    And I should see that the minimum order value input is "enabled"
    When I fill in a minimum order value of "1000"
    And I add the channel
    Then I should be notified that it has been successfully created
    And I should see that the minimum order value input is "1000.00"
    And the channel "Mobile channel" should appear in the registry

  Scenario: Multiple currencies and changing the default (currency label should update)
    When I choose "US Dollar" as the base currency
    And I choose "English (United States)" as a default locale
    Then I should see that the minimum order value enabled toggle is "off"
    And I should see that the minimum order value input is "disabled"
    And I should see that the minimum order value input is "empty"
    And I should see that the minimum order value input label is "$"
    And I follow "Cancel" and Leave

  Scenario: Toggle enables or clears the input
    When I choose "US Dollar" as the base currency
    And I choose "English (United States)" as a default locale
    And I set the minimum order value enabled toggle to "on"
    Then I should see that the minimum order value enabled toggle is "on"
    And I should see that the minimum order value input is "enabled"
    When I fill in a minimum order value of "1000"
    And I set the minimum order value enabled toggle to "off"
    Then I should see that the minimum order value enabled toggle is "off"
    And I should see that the minimum order value input is "disabled"
    And I should see that the minimum order value input is empty
    And I follow "Cancel" and Leave
