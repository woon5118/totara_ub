@totara @totara_reportbuilder @javascript
Feature: Filter default value works as expected
  In order to see if defult value for filter works as expected
  As an admin
  I need to go to browse user report and check the default value is set correctly and can be reset

  Background:
    Given I am on a totara site
    And the following "users" exist:
      | username | firstname | lastname | email             |
      | user1    | User      | One      | user1@example.com |
      | user2    | User      | Two      | user2@example.com |
      | user3    | User      | Three    | user3@example.com |
      | user4    | User      | Four     | user4@example.com |
    And the following config values are set as admin:
      | authdeleteusers | partial |
    And I log in as "admin"
    And I navigate to "Browse list of users" node in "Site administration > Users > Accounts"
    And I should see "User One"
    And I should see "User Two"
    And I should see "User Three"
    And I should see "User Four"
    And I follow "Delete User One"
    And I press "Delete"
    And I follow "Suspend User Two"

  Scenario: Test default value to active only is correctly set and I see the right information
    When I navigate to "Browse list of users" node in "Site administration > Users > Accounts"
    Then I should see "User Three"
    And I should see "User Four"
    And I should not see "User One"
    And I should not see "User Two"
    And the field "user-deleted" matches value "Active users only"

  Scenario: Changing default value for filter is possible
    Given I navigate to "Browse list of users" node in "Site administration > Users > Accounts"
    And the field "user-deleted" matches value "Active users only"

    # Changing to deleted users only
    When I set the field "user-deleted" to "Deleted users only"
    Then the field "user-deleted" matches value "Deleted users only"
    And I click on "Search" "button" in the ".fitem_actionbuttons" "css_element"
    And I should see "User One"
    And I should not see "User Two"
    And I should not see "User Three"
    And I should not see "User Four"

    # Changing to suspended users only
    When I set the field "user-deleted" to "Suspended users only"
    Then the field "user-deleted" matches value "Suspended users only"
    And I click on "Search" "button" in the ".fitem_actionbuttons" "css_element"
    And I should see "User Two"
    And I should not see "User One"
    And I should not see "User Three"
    And I should not see "User Four"

    # Changing to Any value
    When I set the field "user-deleted" to "any value"
    Then the field "user-deleted" matches value "any value"
    And I click on "Search" "button" in the ".fitem_actionbuttons" "css_element"
    And I should see "User One"
    And I should see "User Two"
    And I should see "User Three"
    And I should see "User Four"

  Scenario: Clear filter is working
    Given I navigate to "Browse list of users" node in "Site administration > Users > Accounts"
    And the field "user-deleted" matches value "Active users only"
    When I click on "Clear" "button" in the ".fitem_actionbuttons" "css_element"
    Then the field "user-deleted" matches value "any value"
    And I should see "User One"
    And I should see "User Two"
    And I should see "User Three"
    And I should see "User Four"
