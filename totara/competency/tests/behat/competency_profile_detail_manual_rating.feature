@totara @totara_competency @pathway_manual @javascript @vuejs
Feature: Test viewing assessment (manual rating) for a user on their competency details page.

  Background:
    Given the following "users" exist:
      | username  | firstname | lastname |
      | user      | Staff     | User     |
      | manager   | Manager   | User     |
      | appraiser | Appraiser | User     |
    And the following job assignments exist:
      | user | idnumber | manager | appraiser |
      | user | 1        | manager | appraiser |
    And a competency scale called "scale" exists with the following values:
      | name                   | description                            | idnumber     | proficient | default | sortorder |
      | Super Competent        | <strong>Is great at doing it.</strong> | super        | 1          | 0       | 1         |
      | Just Barely Competent  | Is okay at doing it.                   | barely       | 1          | 0       | 2         |
      | Incredibly Incompetent | <em>Is rubbish at doing it.</em>       | incompetent  | 0          | 1       | 3         |
    And the following "competency" frameworks exist:
      | fullname                 | idnumber | scale |
      | Competency Framework One | fw       | scale |
    And the following "competency" hierarchy exists:
      | framework | fullname   | idnumber | description                     |
      | fw        | Competency | comp1    | <strong>Competency One</strong> |
    And the following "assignments" exist in "totara_competency" plugin:
      | competency | user_group_type | user_group |
      | comp1      | user            | user       |
    And the following "manual pathways" exist in "totara_competency" plugin:
      | competency | roles                  | sortorder |
      | comp1      | self,manager,appraiser | 1         |
    And the following "manual ratings" exist in "totara_competency" plugin:
      | competency | subject_user | rater_user | role      | scale_value  | comment             | date       |
      | comp1      | user         | manager    | manager   | barely       | My staff is alright | 2020-01-01 |
      | comp1      | user         | appraiser  | appraiser | incompetent  | My appraisee is bad | 2020-01-02 |
    And I run the scheduled task "totara_competency\task\expand_assignments_task"
    And I run the scheduled task "totara_competency\task\competency_aggregation_queue"

  Scenario: View assessments on my own competency
    When I log in as "user"
    And I navigate to the competency profile details page for the "Competency" competency

    # Self-assessment - currently have no rating
    Then I should see "Self-assessment" in the ".tui-collapsible:first-child" "css_element"
    And I should see "Your rating" in the ".tui-collapsible:first-child .tui-pathwayManualAchievementRater" "css_element"
    And I should see "No rating given" in the ".tui-collapsible:first-child .tui-pathwayManualAchievementRater" "css_element"
    And I should see "Add rating" in the ".tui-collapsible:first-child .tui-pathwayManualAchievementRater" "css_element"

    # Add a rating for self
    When the following "manual ratings" exist in "totara_competency" plugin:
      | competency | subject_user | rater_user | role | scale_value | comment         | date       |
      | comp1      | user         | user       | self | super       | I'm super good! | 2020-01-03 |
    And I reload the page

    # Self-assessment - now have a rating
    Then I should see "Staff User" in the ".tui-collapsible:first-child .tui-pathwayManualAchievementRater" "css_element"
    And I should see "3 January 2020" in the ".tui-collapsible:first-child .tui-pathwayManualAchievementRater" "css_element"
    And I should see "Super Competent" in the ".tui-collapsible:first-child .tui-pathwayManualAchievementRater" "css_element"
    When I click on "View comment" "button" in the ".tui-collapsible:first-child .tui-pathwayManualAchievementRater" "css_element"
    Then I should see "I'm super good!" in the tui popover
    When I close the tui popover

    # Rating by an assessor - Manager
    Then I should see "Manager User" in the ".tui-collapsible:last-child .tui-pathwayManualAchievementRater:first-child" "css_element"
    And I should see "1 January 2020" in the ".tui-collapsible:last-child .tui-pathwayManualAchievementRater:first-child" "css_element"
    And I should see "Just Barely Competent" in the ".tui-collapsible:last-child .tui-pathwayManualAchievementRater:first-child" "css_element"
    And I should not see "Add rating" in the ".tui-collapsible:last-child .tui-pathwayManualAchievementRater:first-child" "css_element"
    When I click on "View comment" "button" in the ".tui-collapsible:last-child .tui-pathwayManualAchievementRater:first-child" "css_element"
    Then I should see "My staff is alright" in the tui popover
    When I close the tui popover

    # Rating by an assessor - Appraiser
    Then I should see "Appraiser User" in the ".tui-collapsible:last-child .tui-pathwayManualAchievementRater:last-child" "css_element"
    And I should see "2 January 2020" in the ".tui-collapsible:last-child .tui-pathwayManualAchievementRater:last-child" "css_element"
    And I should see "Incredibly Incompetent" in the ".tui-collapsible:last-child .tui-pathwayManualAchievementRater:last-child" "css_element"
    And I should not see "Add rating" in the ".tui-collapsible:last-child .tui-pathwayManualAchievementRater:last-child" "css_element"
    When I click on "View comment" "button" in the ".tui-collapsible:last-child .tui-pathwayManualAchievementRater:last-child" "css_element"
    Then I should see "My appraisee is bad" in the tui popover

  Scenario: View assessments as manager
    When I log in as "manager"
    And I navigate to the competency profile details page for the "Competency" competency and user "user"

    # Self-assessment - currently have no rating
    Then I should see "Self-assessment" in the ".tui-collapsible:first-child" "css_element"
    And I should see "Staff User" in the ".tui-collapsible:first-child .tui-pathwayManualAchievementRater" "css_element"
    And I should see "No rating given" in the ".tui-collapsible:first-child .tui-pathwayManualAchievementRater" "css_element"
    And I should not see "Add rating" in the ".tui-collapsible:first-child .tui-pathwayManualAchievementRater" "css_element"

    # Rating by an assessor - Manager
    Then I should see "Manager User" in the ".tui-collapsible:last-child .tui-pathwayManualAchievementRater:first-child" "css_element"
    And I should see "1 January 2020" in the ".tui-collapsible:last-child .tui-pathwayManualAchievementRater:first-child" "css_element"
    And I should see "Just Barely Competent" in the ".tui-collapsible:last-child .tui-pathwayManualAchievementRater:first-child" "css_element"
    And I should see "Add rating" in the ".tui-collapsible:last-child .tui-pathwayManualAchievementRater:first-child" "css_element"
    When I click on "View comment" "button" in the ".tui-collapsible:last-child .tui-pathwayManualAchievementRater:first-child" "css_element"
    Then I should see "My staff is alright" in the tui popover
    When I close the tui popover

    # Rating by an assessor - Appraiser
    Then I should see "Appraiser User" in the ".tui-collapsible:last-child .tui-pathwayManualAchievementRater:last-child" "css_element"
    And I should see "2 January 2020" in the ".tui-collapsible:last-child .tui-pathwayManualAchievementRater:last-child" "css_element"
    And I should see "Incredibly Incompetent" in the ".tui-collapsible:last-child .tui-pathwayManualAchievementRater:last-child" "css_element"
    And I should not see "Add rating" in the ".tui-collapsible:last-child .tui-pathwayManualAchievementRater:last-child" "css_element"
    When I click on "View comment" "button" in the ".tui-collapsible:last-child .tui-pathwayManualAchievementRater:last-child" "css_element"
    Then I should see "My appraisee is bad" in the tui popover

  Scenario: View assessments as appraiser
    When I log in as "appraiser"
    And I navigate to the competency profile details page for the "Competency" competency and user "user"

    # Self-assessment - currently have no rating
    Then I should see "Self-assessment" in the ".tui-collapsible:first-child" "css_element"
    And I should see "Staff User" in the ".tui-collapsible:first-child .tui-pathwayManualAchievementRater" "css_element"
    And I should see "No rating given" in the ".tui-collapsible:first-child .tui-pathwayManualAchievementRater" "css_element"
    And I should not see "Add rating" in the ".tui-collapsible:first-child .tui-pathwayManualAchievementRater" "css_element"

    # Rating by an assessor - Manager
    Then I should see "Manager User" in the ".tui-collapsible:last-child .tui-pathwayManualAchievementRater:first-child" "css_element"
    And I should see "1 January 2020" in the ".tui-collapsible:last-child .tui-pathwayManualAchievementRater:first-child" "css_element"
    And I should see "Just Barely Competent" in the ".tui-collapsible:last-child .tui-pathwayManualAchievementRater:first-child" "css_element"
    And I should not see "Add rating" in the ".tui-collapsible:last-child .tui-pathwayManualAchievementRater:first-child" "css_element"
    When I click on "View comment" "button" in the ".tui-collapsible:last-child .tui-pathwayManualAchievementRater:first-child" "css_element"
    Then I should see "My staff is alright" in the tui popover
    When I close the tui popover

    # Rating by an assessor - Appraiser
    Then I should see "Appraiser User" in the ".tui-collapsible:last-child .tui-pathwayManualAchievementRater:last-child" "css_element"
    And I should see "2 January 2020" in the ".tui-collapsible:last-child .tui-pathwayManualAchievementRater:last-child" "css_element"
    And I should see "Incredibly Incompetent" in the ".tui-collapsible:last-child .tui-pathwayManualAchievementRater:last-child" "css_element"
    And I should see "Add rating" in the ".tui-collapsible:last-child .tui-pathwayManualAchievementRater:last-child" "css_element"
    When I click on "View comment" "button" in the ".tui-collapsible:last-child .tui-pathwayManualAchievementRater:last-child" "css_element"
    Then I should see "My appraisee is bad" in the tui popover
