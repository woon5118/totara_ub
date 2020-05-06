@totara @perform @mod_perform @javascript @vuejs
Feature: Activation of activities
  As an activity administrator
  I need to be able to activate activities
  so they can be available to users.

  Background:
    Given I am on a totara site
    And the following "users" exist:
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
    And the following "activities" exist in "mod_perform" plugin:
      | activity_name           | activity_type | activity_status |
      | Active activity         | check-in      | Active          |
      | Empty draft activity    | appraisal     | Draft           |
      | Complete draft activity | feedback      | Draft           |
    And the following "activity sections" exist in "mod_perform" plugin:
      | activity_name           | section_name |
      | Complete draft activity | section 1    |
    And the following "section relationships" exist in "mod_perform" plugin:
      | section_name | relationship |
      | section 1    | subject      |
    And the following "section elements" exist in "mod_perform" plugin:
      | section_name | element_name |
      | section 1    | short_text   |
    And the following "activity tracks" exist in "mod_perform" plugin:
      | activity_name           | track_description |
      | Complete draft activity | track 1           |
    And the following "track assignments" exist in "mod_perform" plugin:
      | track_description | assignment_type | assignment_name |
      | track 1           | cohort          | aud1            |

  Scenario: Activating activities
    When I log in as "admin"
    And I navigate to the manage perform activities page
    Then I should see the tui datatable contains:
      | Name                    | Type      | Status |
      | Active activity         | Check-in  | Active |
      | Empty draft activity    | Appraisal | Draft  |
      | Complete draft activity | Feedback  | Draft  |

    When I open the dropdown menu in the tui datatable row with "Empty draft activity" "Name"
    Then I should see "Activate" option disabled in the dropdown menu

    When I open the dropdown menu in the tui datatable row with "Active activity" "Name"
    Then I should not see "Activate" option in the dropdown menu

    When I open the dropdown menu in the tui datatable row with "Complete draft activity" "Name"
    Then I should see "Activate" option in the dropdown menu

    When I click on "Activate" option in the dropdown menu
    Then I should see "Confirm activity activation"
    And I should see "2 users will be assigned on activation"

    When I click on "Activate" "button"
    Then I should see the tui datatable contains:
      | Name                    | Type      | Status |
      | Active activity         | Check-in  | Active |
      | Empty draft activity    | Appraisal | Draft  |
      | Complete draft activity | Feedback  | Active |
