@mod @mod_facetoface @totara
Feature: Download a seminar signin sheet
  In order to take attendance
  As a teacher
  I need to download a signin sheet

  Background:
    Given I am on a totara site
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | One      | teacher1@example.com |
      | learner1 | Learner   | One      | learner1@example.com |
      | learner2 | Learner   | Two      | learner2@example.com |
      | learner3 | Learner   | Three    | learner3@example.com |
      | learner4 | Learner   | Four     | learner4@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | learner2 | C1     | student        |
      | learner3 | C1     | student        |
    And I log in as "admin"
    And I click on "Find Learning" in the totara menu
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Seminar" to section "1" and I fill the form with:
      | Name              | Test seminar name        |
      | Description       | Test seminar description |
    And I follow "Test seminar name"
    And I follow "Add a new event"
    And I click on "Edit date" "link"
    And I set the following fields to these values:
      | timestart[day]     | 10   |
      | timestart[month]   | 2    |
      | timestart[year]    | 2030 |
      | timestart[hour]    | 11   |
      | timestart[minute]  | 0    |
      | timefinish[day]    | 10   |
      | timefinish[month]  | 2    |
      | timefinish[year]   | 2030 |
      | timefinish[hour]   | 12   |
      | timefinish[minute] | 0    |
    And I press "OK"
    And I press "Save changes"
    And I click on the link "Attendees" in row 1
    And I click on "Add users" "option" in the "#menuf2f-actions" "css_element"
    And I click on "Learner One, learner1@example.com" "option"
    And I press "Add"
    And I click on "Learner Two, learner2@example.com" "option"
    And I press "Add"
    And I press "Continue"
    And I press "Confirm"
    And I log out

  @javascript
  Scenario: An editing trainer can download the signin sheet
    Given I log in as "teacher1"
    When I click on "Find Learning" in the totara menu
    And I follow "Course 1"
    And I follow "Test seminar name"
    And I click on the link "Attendees" in row 1
    Then "Download sign-in sheet" "button" should be visible
