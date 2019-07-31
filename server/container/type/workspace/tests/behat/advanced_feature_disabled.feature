@container @workspace @container_workspace @totara @totara_engage @engage
Feature: Workspaces will not appear when the advanced feature is disabled

  Background:
    Given I am on a totara site
    And I enable the "container_workspace" advanced feature
    And the following "users" exist:
      | username | firstname | lastname | email             |
      | user1    | User      | One      | user1@example.com |
      | user2    | User      | Two      | user1@example.com |
    And the following "workspaces" exist in "container_workspace" plugin:
      | name             | summary   | owner |
      | Test Workspace 1 | Workspace | user1 |
    And I log in as "admin"
    And I set the following system permissions of "Authenticated user" role:
      | moodle/user:viewalldetails | Allow |
    And I log out

  Scenario: Disabling the workspace feature will hide the workspace menu items
    Given I log in as "admin"

    When I set the following administration settings values:
      | enablecontainer_workspace | Disable |
    Then I should not see "Collaborate" in the totara menu
    And I should not see "Your Workspaces" in the totara menu
    And I should not see "Find Workspaces" in the totara menu

    When I set the following administration settings values:
      | enablecontainer_workspace | Enable |
    Then I should see "Collaborate" in the totara menu
    And I should see "Your Workspaces" in the totara menu
    And I should see "Find Workspaces" in the totara menu
