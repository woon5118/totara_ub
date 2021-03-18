@totara @engage @container_workspace @container @javascript
Feature: General workspace workflow
  Background:
    Given I am on a totara site
    And I set the site theme to "ventura"
    And the following "users" exist:
      | username | firstname | lastname | email           |
      | user_one | User      | One      | one@example.com |

  Scenario: Authenticated user can not create workspace
    Given I log in as "user_one"
    And I click on "Your Workspaces" in the totara menu
    And ".tui-contributeWorkspace__button" "css_element" should exist
    When I click on "Create a workspace" "button"
    Then I should see "Create a workspace"
    And I log out

    And I log in as "admin"
    When I set the following system permissions of "Authenticated user" role:
      | container/workspace:createhidden  | Prohibit |
      | container/workspace:createprivate | Prohibit |
      | container/workspace:create        | Prohibit |
    Then I log out

    And I log in as "user_one"
    And I click on "Your Workspaces" in the totara menu
    And ".tui-contributeWorkspace__button" "css_element" should not exist