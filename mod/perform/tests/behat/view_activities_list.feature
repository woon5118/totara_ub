@totara @perform @mod_perform @javascript @vuejs
Feature: Manage performance activity page
  As an activity administrator
  I need to be able to manage activities
  so that I can change them according to the needs.

  As an activity creator
  I need to be able to create activities

  Background:
    Given I am on a totara site
    And the following "users" exist:
      | username | firstname | lastname | email                   |
      | john     | John      | One      | john.one@example.com    |
      | jack     | Jack      | Rabbit   | jack.rabbit@example.com |
    And the following "activities" exist in "mod_perform" plugin:
      | activity_name   | description        | activity_type | activity_status |
      | Draft activity  | A draft activity   | feedback      | Draft           |
      | Active activity | An active activity | feedback      | Active          |

  Scenario: Admin can access the activity management page
    Given I log in as "admin"
    When I navigate to the manage perform activities page
    # The admin should always see all existing activities
    Then I should see the tui datatable contains:
      | Name            | Type     | Status |
      | Draft activity  | Feedback | Draft  |
      | Active activity | Feedback | Active |
    And "Participation reporting" "link" should exist

  Scenario: User can access the activity management page and create an activity given the right capabilities
    Given I log in as "admin"
    # To create an activity you need both create capabilities, in mod_perform and in the container
    And I set the following system permissions of "Authenticated user" role:
      | mod/perform:view_manage_activities | Allow |
    And I log out

    Given I log in as "john"
    When I navigate to the manage perform activities page
    Then I should see "No items to display"
    And I should not see "Add activity"
    And I log out

    Given I log in as "admin"
    # To create an activity you need both create capabilities, in mod_perform and in the container
    And I set the following system permissions of "Authenticated user" role:
      | mod/perform:create_activity        | Allow |
      | container/perform:create           | Allow |
    And I log out

    Given I log in as "john"
    When I navigate to the manage perform activities page
    Then I should see "No items to display"
    And I should see "Add activity"

    When I click on "Add activity" "button"
    Given I set the following fields to these values:
      | Activity title | My Test Activity             |
      | Description    | My Test Activity description |
      | Activity type  | Feedback                     |
    When I click on "Get started" "button"
    And I navigate to the manage perform activities page
    Then I should see the tui datatable contains:
      | Name             | Type     | Status |
      | My Test Activity | Feedback | Draft |
    # For activities created by the user the reporting link should be there
    And "Participation reporting" "link" should exist in the ".tui-performActivityActions__actionIcons" "css_element"

  Scenario: User can access the activity management page and manage activities given the right capabilities
    Given I log in as "admin"
    # To create an activity you need both create capabilities, in mod_perform and in the container
    And I set the following system permissions of "Authenticated user" role:
      | mod/perform:view_manage_activities | Allow |
      | mod/perform:manage_activity      | Allow |
    And I log out

    Given I log in as "john"
    When I navigate to the manage perform activities page
    Then I should see the tui datatable contains:
      | Name             | Type     | Status |
      | Draft activity  | Feedback | Draft  |
      | Active activity | Feedback | Active |
    # For activities created by the user the reporting link should be there
    And "Participation reporting" "link" should not exist
    Then I log out

    Given I log in as "admin"
    # To be able to see the link to the reporting the user needs to right capability
    And I set the following system permissions of "Authenticated user" role:
      | mod/perform:view_participation_reporting | Allow |
    And I log out

    Given I log in as "jack"
    When I navigate to the manage perform activities page
    Then I should see the tui datatable contains:
      | Name             | Type     | Status |
      | Draft activity   | Feedback | Draft  |
      | Active activity  | Feedback | Active |
    # Now the user should see the link to the report page
    And "Participation reporting" "link" should exist in the ".tui-performActivityActions__actionIcons" "css_element"
    Then I log out
