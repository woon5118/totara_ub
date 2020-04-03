@totara @totara_competency @javascript
Feature: Archive user assignments on competency details page.

  Background:
    Given I am on a totara site
    When I log in as "admin"
    And the following "users" exist:
      | username | firstname | lastname | email               |
      | teacher  | Lesson    | Teacher  | teacher@example.com |
      | student  | Learn     | Student  | student@example.com |
    And the following "organisation frameworks" exist in "totara_hierarchy" plugin:
      | fullname       | idnumber |
      | High Framework | HSCH1    |
    And the following "organisations" exist in "totara_hierarchy" plugin:
      | org_framework | fullname       | shortname | idnumber |
      | HSCH1         | High School 1  | org1      | org1     |
    And the following "position frameworks" exist in "totara_hierarchy" plugin:
      | fullname        | idnumber |
      | Position Root 1 | PFW001   |
    And the following "positions" exist in "totara_hierarchy" plugin:
      | pos_framework | fullname   | shortname | idnumber |
      | PFW001        | Position 1 | pos1      | pos1     |
    And the following job assignments exist:
      | user    | idnumber | manager  | organisation | position |
      | student | 1        | teacher  | org1         | pos1     |

    And a competency scale called "Sample scale" exists with the following values:
      | name         | description  | idnumber     | proficient | default | sortorder |
      | Beginner     | Start        | start        | 0          | 1       | 1         |
      | Intermediate | Experienced  | middle       | 0          | 0       | 2         |
      | World-class  | Veteran      | best         | 1          | 0       | 3         |
    And the following "competency" frameworks exist:
      | fullname         | idnumber | description                    | scale        |
      | Sample framework | sam1     | Framework for Competencies     | Sample scale |
    And the following hierarchy types exist:
      | hierarchy  | idnumber | fullname            |
      | competency | type1    | Competency Type One |
      | competency | type2    | Competency Type Two |
    And the following "competency" hierarchy exists:
      | framework  | fullname  | idnumber | type  | description  | assignavailability |
      | sam1       | Comp 1    | comp1    | type1 | Lorem        | any                |
      | sam1       | Comp 2    | comp2    | type1 | Ipsum        | any                |
      | sam1       | Comp 3    | comp3    | type2 | Dixon        | any                |
    And I log out
    And I log in as "teacher"

    #Assign competency to student. Self and Other assigned.
    And the following "assignments" exist in "totara_competency" plugin:
      | competency | user_group_type | user_group   | type  |
      | comp1      | user            | student      | other |
    And I run the scheduled task "totara_competency\task\expand_assignments_task"
    And I log out
    And I log in as "student"
    And the following "assignments" exist in "totara_competency" plugin:
      | competency | user_group_type | user_group   | type  |
      | comp1      | user            | student      | self  |
    And I run the scheduled task "totara_competency\task\expand_assignments_task"
    And I log out

  Scenario: Archive self-assigned and other-assigned competency as manager
    Given I log in as "teacher"
    When I am on profile page for user "student"
    And I click on "Competency profile" "link" in the ".userprofile" "css_element"
    And I change the competency profile to list view
    And I click on "Comp 1" "link"
    And I select "Self-assigned" from the "select_assignment" singleselect
    Then I should see "Archive this assignment"
    And I click on "Archive this assignment" "button"
    Then I should see "Confirm archiving of assignment"
    And I click on "OK" "button"
    Then I should not see "Self-assigned"

    When I select "Directly assigned by Admin User (Manager)" from the "select_assignment" singleselect
    Then I should see "Archive this assignment"
    And I click on "Archive this assignment" "button"
    Then I should see "Confirm archiving of assignment"
    And I click on "OK" "button"
    Then I should not see "Directly assigned by Admin User (Manager)"

  Scenario: Hide Archive directly-assigned competency button for User profile
    Given I log in as "student"
    When I am on profile page for user "student"
    And I click on "Competency profile" "link" in the ".userprofile" "css_element"
    And I change the competency profile to list view
    And I click on "Comp 1" "link"
    And I select "Directly assigned by Admin User (Manager)" from the "select_assignment" singleselect
    Then I should not see "Archive this assignment"

    When I select "Self-assigned" from the "select_assignment" singleselect
    Then I should see "Archive this assignment"
    And I click on "Archive this assignment" "button"
    Then I should see "Confirm archiving of assignment"
    And I click on "OK" "button"
    Then I should not see "Self-assigned"
