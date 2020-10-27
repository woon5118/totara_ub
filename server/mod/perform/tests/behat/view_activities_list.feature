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
      | Title | My Test Activity             |
      | Description    | My Test Activity description |
      | Type  | Feedback                     |
    When I click on "Create" "button"
    And I navigate to the manage perform activities page
    Then I should see the tui datatable contains:
      | Name             | Type     | Status |
      | My Test Activity | Feedback | Draft |
    # For activities created by the user the reporting link should be there
    And "Participation reporting" "link" should not exist in the ".tui-performActivityActions" "css_element"

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
    # Now the user should see the link to the report page in the second row but not the first
    And "Participation reporting" "link" should not exist in the ".tui-dataTableRow:nth-child(1) .tui-performActivityActions" "css_element"
    And "Participation reporting" "link" should exist in the ".tui-dataTableRow:nth-child(2) .tui-performActivityActions" "css_element"
    Then I log out

  Scenario: Activities are paginated.
    Given the following "users" exist:
      | username | firstname | lastname | email             |
      | user1    | user      | 1        | user1@example.com |
      | user2    | user      | 2        | user2@example.com |
    And the following "cohorts" exist:
      | name | idnumber |
      | aud1 | aud1     |
    And the following "cohort members" exist:
      | user  | cohort |
      | user1 | aud1   |
      | user2 | aud1   |
    Given the following "activities" exist in "mod_perform" plugin:
      | activity_name        | activity_type | activity_status | create_track | create_section |
      | Perform activity 10  | feedback      | Draft           | false        | false          |
      | Perform activity 20  | feedback      | Active          | true         | true           |
      | Perform activity 30  | feedback      | Active          | true         | true           |
      | Perform activity 40  | feedback      | Active          | true         | true           |
      | Perform activity 50  | feedback      | Active          | true         | true           |
      | Perform activity 60  | feedback      | Active          | true         | true           |
      | Perform activity 70  | feedback      | Active          | true         | true           |
      | Perform activity 80  | feedback      | Active          | true         | true           |
      | Perform activity 90  | feedback      | Active          | true         | true           |
      | Perform activity 100 | feedback      | Active          | true         | true           |
      | Perform activity 110 | feedback      | Active          | true         | true           |
      | Perform activity 120 | feedback      | Active          | true         | true           |
      | Perform activity 130 | feedback      | Active          | true         | true           |
      | Perform activity 140 | feedback      | Active          | true         | true           |
      | Perform activity 150 | feedback      | Active          | true         | true           |
      | Perform activity 160 | feedback      | Active          | true         | true           |
      | Perform activity 170 | feedback      | Active          | true         | true           |
      | Perform activity 180 | feedback      | Active          | true         | true           |
      | Perform activity 190 | feedback      | Active          | true         | true           |
      | Perform activity 200 | feedback      | Active          | true         | true           |
      | Perform activity 210 | feedback      | Active          | true         | true           |
      | Perform activity 220 | feedback      | Active          | true         | true           |
      | Perform activity 230 | feedback      | Active          | true         | true           |
      | Perform activity 240 | feedback      | Active          | true         | true           |
      | Perform activity 250 | feedback      | Active          | true         | true           |
      | Perform activity 260 | feedback      | Active          | true         | true           |
      | Perform activity 270 | feedback      | Active          | true         | true           |
      | Perform activity 280 | feedback      | Active          | true         | true           |
    And the following "activity sections" exist in "mod_perform" plugin:
      | activity_name       | section_name |
      | Perform activity 10 | section 1    |
    And the following "section relationships" exist in "mod_perform" plugin:
      | section_name | relationship |
      | section 1    | subject      |
    And the following "section elements" exist in "mod_perform" plugin:
      | section_name | element_name |
      | section 1    | short_text   |
    And the following "activity tracks" exist in "mod_perform" plugin:
      | activity_name       | track_description |
      | Perform activity 10 | track 1           |
    And the following "track assignments" exist in "mod_perform" plugin:
      | track_description | assignment_type | assignment_name |
      | track 1           | cohort          | aud1            |

    Given I log in as "admin"
    When I navigate to the manage perform activities page
    Then I should see "Perform activity 10"
    And I should not see "Perform activity 190"
    When I open the dropdown menu in the tui datatable row with "Perform activity 10" "Name"
    And I click on "Activate" option in the dropdown menu
    Then I should see "Confirm activity activation" in the tui modal
    When I click on "Activate" "button"
    Then I should see "Perform activity 180"
    And I should not see "Perform activity 190"

    When I open the dropdown menu in the tui datatable row with "Perform activity 100" "Name"
    And I click on "Delete" option in the dropdown menu
    Then I should see "Confirm activity deletion" in the tui modal
    When I click on "Delete" "button"
    Then I should not see "Perform activity 100"
    And I should see "Perform activity 190"

    When I click on "Load more" "button"
    Then I should see "Perform activity 200"
    And I should see "Perform activity 210"
    And I should see "Perform activity 220"
    And I should see "Perform activity 230"
    And I should see "Perform activity 240"
    And I should see "Perform activity 250"
    And I should see "Perform activity 260"
    And I should see "Perform activity 270"
    And I should see "Perform activity 280"
    Then I should not see "Load more"
