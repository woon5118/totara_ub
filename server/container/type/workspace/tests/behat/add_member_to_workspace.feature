@totara @container @container_workspace @engage
Feature: Add users to a workspace
  Background:
    Given I am on a totara site
    And the following "users" exist:
      | username   | firstname | lastname | email             |
      | user_one   | User      | One      | one@example.com   |
      | user_two   | User      | Two      | two@example.com   |
      | user_three | User      | Three    | three@example.com |
    And the following "workspaces" exist in "container_workspace" plugin:
      | name          | owner    | summary           |
      | Workspace 101 | admin    | Workspace summary |
      | Workspace 102 | user_one | Workspace summary |

  @javascript
  Scenario: Workspace owner search for non member users.
    Given I am on a totara site
    And I log in as "admin"
    When I click on "Your Workspaces" in the totara menu
    Then I should see "Workspace 101"
    And "Owner" "button" should exist
    And I click on "Owner" "button"
    And I click on "Add members" "link"
    And I should see "User One"
    And I should see "User Two"
    And I should see "User Three"
    And I set the field "Filter users" to "user one"
    Then I should not see "User Two"
    And I should not see "User Three"
    And I should see "User One"
    And I set the field "Filter users" to "two"
    Then I should not see "User One"
    And I should not see "User Three"
    And I should see "User Two"
    And I set the field "Filter users" to "three@example.com"
    Then I should not see "User One"
    And I should see "User Three"
    And I should not see "User Two"

  @javascript
  Scenario: Add members should not be available if capability is removed.
    Given I log in as "admin"
    And I set the following system permissions of "Workspace owner" role:
      | container/workspace:addmember | Prohibit |
    And I log out
    And I log in as "user_one"
    When I click on "Your Workspaces" in the totara menu
    Then I should see "Workspace 102"
    And "Owner" "button" should exist
    When I click on "Owner" "button"
    Then I should not see "Add members"

    # Admin should still see the option to add members
    When I log out
    And I log in as "admin"
    When I click on "Your Workspaces" in the totara menu
    Then I should see "Workspace 101"
    And "Owner" "button" should exist
    When I click on "Owner" "button"
    Then I should see "Add members"