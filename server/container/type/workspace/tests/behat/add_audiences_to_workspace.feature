@totara @container @container_workspace @engage @javascript
Feature: Add audiences to a workspace
  Background:
    Given I am on a totara site
    And the following "users" exist:
      | username   | firstname | lastname | email             |
      | user_one   | User      | One      | one@example.com   |
      | user_two   | User      | Two      | two@example.com   |
      | user_three | User      | Three    | three@example.com |
    And the following "role assigns" exist:
      | user     | role    | contextlevel | reference |
      | user_two | manager | System       |           |
    And the following "cohorts" exist:
      | name      | idnumber | contextlevel | reference |
      | Audience1 | aud1     | System       |           |
      | Audience2 | aud2     | System       |           |
    And the following "cohort members" exist:
      | user     | cohort |
      | user_one | aud1   |
      | user_two | aud1   |
    And the following "workspaces" exist in "container_workspace" plugin:
      | name          | owner    | summary           |
      | Workspace 101 | user_one | Workspace summary |

  Scenario: Admi user can add audiences
    When I log in as "admin"
    And I click on "Find Workspaces" in the totara menu
    Then I should see "Workspace 101"
    When I click on "Workspace 101" "link"
    And "Admin" "button" should exist
    When I click on "Admin" "button"
    And "Bulk add audience(s)" "link" should exist

  Scenario: Owner without audience viewing capability can't add audiences
    When I log in as "user_one"
    And I click on "Your Workspaces" in the totara menu
    Then I should see "Workspace 101"
    And "Owner" "button" should exist
    When I click on "Owner" "button"
    Then "Bulk add audience(s)" "link" should not exist

  Scenario: Site admin can add audiences
    When I log in as "user_two"
    When I click on "Find Workspaces" in the totara menu
    Then I should see "Workspace 101"
    When I click on "Workspace 101" "link"
    And "Admin" "button" should exist
    And I click on "Admin" "button"
    And I click on "Bulk add audience(s)" "link"
    And I should see "Select audiences"
    And I should see "Audience1"
    And I should see "Audience2"
    And I click on "Add" "button"
    # TODO: Add more steps with TL-28825, TL-28826 and TL-28827
