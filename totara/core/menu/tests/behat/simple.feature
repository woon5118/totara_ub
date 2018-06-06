@totara @totara_menu
Feature: A basic test of the Totara custom menu
  In order to limit access to menu items
  As a user
  I need to restrict by audience

  Background:
    Given I am on a totara site
    And I log in as "admin"
    And I navigate to "Top navigation" node in "Site administration > Appearance"
    And I click on "Add new menu item" "button"
    And I set the following fields to these values:
      | Parent item              | Top       |
      | Menu title               | Test item |
      | Visibility               | Show      |
      | Menu default url address | /my/      |
    And I click on "Add new menu item" "button"
    And I should see "Test item" in the totara menu

  Scenario: Reset to default
    Given I navigate to "Top navigation" node in "Site administration > Appearance"
    When I click on "Reset menu to default configuration" "button"
    And I click on "Continue" "button"
    Then I should see "Top navigation reset to default configuration"
    And I should not see "Test item" in the totara menu

  Scenario: Change parent
    Given I navigate to "Top navigation" node in "Site administration > Appearance"
    When I click on "Edit" "link" in the "Performance" "table_row"
    And I set the field "Parent item" to "Courses"
    And I click on "Save changes" "button"
    Then I should see "You cannot move this item to the selected parent because it has descendants. Please move this item's descendants first."
    When I set the field "Parent item" to "Find Learning"
    And I click on "Save changes" "button"
    Then I should see "Top navigation updated successfully"

  Scenario: Test visibility using form
    Given I click on "Edit" "link" in the "Test item" "table_row"
    When I set the following fields to these values:
      | Visibility | Hide |
    And I click on "Save changes" "button"
    Then I should not see "Test item" in the totara menu
    When I click on "Edit" "link" in the "Test item" "table_row"
    And I set the following fields to these values:
      | Visibility | Show |
    And I click on "Save changes" "button"
    Then I should see "Test item" in the totara menu

  Scenario: Test visibility using table
    When I click on "Hide" "link" in the "Test item" "table_row"
    Then I should not see "Test item" in the totara menu
    When I click on "Show" "link" in the "Test item" "table_row"
    Then I should see "Test item" in the totara menu

  @javascript
  Scenario: Move menu items
    Given I navigate to "Top navigation" node in "Site administration > Appearance"
    And I click on "Add new menu item" "button"
    And I set the following fields to these values:
      | Parent item              | Top          |
      | Menu title               | Another item |
      | Visibility               | Show         |
      | Menu default url address | /my/         |
    And I click on "Add new menu item" "button"
    And I should see "Another item" in the totara menu
    When I click on "Move up" "link" in the "Another item" "table_row"
    Then "Another item" "link" should appear before "Test item" "link"
    When I navigate to "Top navigation" node in "Site administration > Appearance"
    And I click on "Move down" "link" in the "Another item" "table_row"
    Then "Test item" "link" should appear before "Another item" "link"

  Scenario: Delete menu items
    Given I navigate to "Top navigation" node in "Site administration > Appearance"
    When I click on "Delete" "link" in the "Test item" "table_row"
    And I click on "Continue" "button"
    Then I should not see "Test item" in the totara menu
