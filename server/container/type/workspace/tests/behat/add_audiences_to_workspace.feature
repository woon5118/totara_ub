@totara @container @container_workspace @engage @javascript
Feature: Add audiences to a workspace
  Background:
    Given I am on a totara site
    And the following "users" exist:
      | username   | firstname | lastname | email             |
      | user_one   | User      | One      | one@example.com   |
      | user_two   | User      | Two      | two@example.com   |
      | user_three | User      | Three    | three@example.com |
      | user_four  | User      | Four     | four@example.com  |
    And the following "role assigns" exist:
      | user     | role    | contextlevel | reference |
      | user_two | manager | System       |           |
    And the following "cohorts" exist:
      | name      | idnumber | contextlevel | reference |
      | Audience1 | aud1     | System       |           |
      | Audience2 | aud2     | System       |           |
    And the following "cohort members" exist:
      | user      | cohort |
      | user_one  | aud1   |
      | user_two  | aud1   |
      | user_four | aud1   |
    And the following "workspaces" exist in "container_workspace" plugin:
      | name          | owner    | summary           |
      | Workspace 101 | user_one | Workspace summary |

  Scenario: Users can add audiences in bulk
    # Log in as admin
    When I log in as "admin"
    When I access the "Workspace 101" workspace
    Then "Admin" "button" should exist
    When I click on "Admin" "button"
    And "Bulk add audience(s)" "link" should exist
    When I log out

    # Log in as the owner
    And I log in as "user_one"
    And I access the "Workspace 101" workspace
    Then "Owner" "button" should exist
    When I click on "Owner" "button"
    Then "Bulk add audience(s)" "link" should not exist
    When I log out

    # Log in as site manager and add audiences
    And  I log in as "user_two"
    When I access the "Workspace 101" workspace
    And "Admin" "button" should exist
    And I click on "Admin" "button"
    And I click on "Bulk add audience(s)" "link"
    And I should see "Select audiences"
    And I should see "Audience1"
    And I should see "Audience2"
    And I toggle the adder picker entry with "Audience1" for "Audience name"
    And I save my selections and close the adder
    Then I should see "Bulk add 2 new members to workspace?"
    And "Add members" "button" should exist
    When I click on "Add members" "button"
    Then I should see "The 'bulk add members' process has successfully started." in the tui success notification toast and close it
    When I click on "Admin" "button"
    And I click on "Bulk add audience(s)" "link"
    And I should see "Audience1"
    And I should see "Audience2"
    And I toggle the adder picker entry with "Audience2" for "Audience name"
    And I save my selections and close the adder
    Then I should see "Your selection will not add any new members to the workspace."
    When I click on "Reselect audiences" "button"
    Then I should not see "Your selection will not add any new members to the workspace."
    And I should see "Select audiences"
    Then I should see the following selected adder picker entries:
      | Audience name | Short name  |
      | Audience2     | aud2        |
