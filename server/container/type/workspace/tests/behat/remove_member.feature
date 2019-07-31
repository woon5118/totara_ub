@totara @totara_engage @container @container_workspace @engage
Feature: Removing members from the workspace
  Background:
    Given I am on a totara site
    And I set the site theme to "ventura"
    And the following "users" exist:
      | username | firstname | lastname | email           |
      | user_one | User      | One      | one@example.com |
      | user_two | User      | Two      | two@example.com |

    # This is for temporary solution
    And I log in as "admin"
    And I set the following system permissions of "Authenticated user" role:
      | moodle/user:viewalldetails | Allow |
    And I log out

  @javascript
  Scenario: Workspace owner remove member
    Given I log in as "user_one"
    And I click on "Your Workspaces" in the totara menu
    And I click on "Create a workspace" "button"
    And I set the field "Workspace name" to "Workspace 101"
    When I click on "Submit" "button"
    Then I should see "Members (1)"
    And I log out
    And I log in as "user_two"
    And I click on "Find Workspaces" in the totara menu
    And I follow "Workspace 101"
    And I click on "Join workspace" "button"
    And I log out
    And I log in as "user_one"
    When I click on "Your Workspaces" in the totara menu
    Then I should see "Members (2)"
    And I click on "Members (2)" "link"
    And I should see "User Two"
    And I click on "More actions for member User Two" "button"
    And I should see "Remove"
    When I click on "Remove" "link"
    Then I should see "Are you sure you want to remove User Two from this workspace?"
    And I click on "Remove" "button"
    Then I should not see "User Two"
