@totara @totara_flavour
Feature: Flavours overview and activation
  In order to use use flavours
  As an admin
  I need to be able to see flavour overview

  Scenario: Verify only enterprise flavour is displayed by default
    Given I log in as "admin"
    When I navigate to "Feature overview" node in "Site administration"
    Then I should see "Enterprise" in the "table.flavour-overview-table" "css_element"
    And I should not see "Professional" in the "table.flavour-overview-table" "css_element"

  Scenario: Verify enterprise and professional flavours are displayed when professional is active
    Given I log in as "admin"
    And flavour "professional" is active
    When I navigate to "Feature overview" node in "Site administration"
    Then I should see "Enterprise" in the "table.flavour-overview-table" "css_element"
    And I should see "Professional" in the "table.flavour-overview-table" "css_element"
