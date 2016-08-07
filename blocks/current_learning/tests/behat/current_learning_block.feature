@totara @block @block_current_learning
Feature: Test Current Learning block

  Background:
    Given I am on a totara site
    And the following "users" exist:
      | username | firstname  | lastname  | email                |
      | learner1 | firstname1 | lastname1 | learner1@example.com |

  Scenario: Learner has Current Learning block on Dashboard by default
    Given I log in as "learner1"
    And I click on "Dashboard" in the totara menu
    Then I should see "You do not have any current learning. For previously completed learning see your Record of Learning"

  @javascript
  Scenario: Learner can remove and readd Current Learning block on Dashboard
    Given the following "programs" exist in "totara_program" plugin:
      | fullname                | shortname |
      | Test Program 1          | program1  |
    And the following "program assignments" exist in "totara_program" plugin:
      | user      | program  |
      | learner1  | program1 |
    And the following "courses" exist:
      | fullname | shortname | enablecompletion |
      | Course 1 | course1  | 1                |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | learner1 | course1| student        |
    And I log in as "learner1"
    And I click on "Dashboard" in the totara menu
    And I press "Customise this page"
    When I click on "Actions" "link" in the "Current Learning" "block"
    And I follow "Delete Current Learning block"
    When I press "Yes"
    And I add the "Current Learning" block
    And I configure the "Current Learning" block
    And I expand all fieldsets
    And I set the following fields to these values:
      | Default region | content |
      | Default weight | -10     |
    And I press "Save changes"
    Then I should see "Course 1" in the "Current Learning" "block"
# TODO: TL-9821 find out why is the failing program test here
#    And I should see "Test Program 1" in the "Current Learning" "block"

  @javascript
  Scenario: Learner expands accordian for a program within the Current Learning block
    Given the following "programs" exist in "totara_program" plugin:
      | fullname                | shortname |
      | Test Program 1          | program1  |
    And the following "program assignments" exist in "totara_program" plugin:
      | user  | program  |
      | learner1 | program1 |

