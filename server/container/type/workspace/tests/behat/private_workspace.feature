@totara @engage @container_workspace @container @javascript
Feature: Private workspace workflow
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

  Scenario: Create private workspace
    Given I am on a totara site
    And I log in as "user_one"
    And I click on "Your Workspaces" in the totara menu
    And I click on "Create a workspace" "button"
    And I set the field "Workspace name" to "This is private workspace"
    And I click on "Private" "text" in the ".tui-radioGroup" "css_element"
    When I click on "Submit" "button"
    Then I should see "This is private workspace"
    And I should see "Private workspace"

  Scenario: Request to join private workspace
    Given the following "workspaces" exist in "container_workspace" plugin:
      | name               | owner    | private | summary                             |
      | User one workspace | user_one | 1       | This is user's one privateworkspace |

    And I am on a totara site
    And I log in as "user_one"
    And I click on "Your Workspaces" in the totara menu
    When I follow "Members"
    Then I should not see "Requests to join"
    And I log out
    And I log in as "user_two"
    And I click on "Find Workspaces" in the totara menu
    And I follow "User one workspace"
    And I should see "Request to join"
    And I should not see "Cancel request"
    When I click on "Request to join" "button"
    Then I should see "Cancel request"
    And I should not see "Request to join"
    And I log out
    And I log in as "user_one"
    And I click on "Your Workspaces" in the totara menu
    And I follow "Members"
    And I should not see "2 members"
    And I should see "1 member"
    And I should see "Requests to join"
    And I should see "User Two"
    And I should see "Approve"
    And I should see "Decline"
    And I should not see "Approved"
    When I click on "Approve member request User Two" "button"
    Then I should see "Approved"

    # Reload
    And I click on "Your Workspaces" in the totara menu
    And I follow "Members"
    And I should see "2 members"