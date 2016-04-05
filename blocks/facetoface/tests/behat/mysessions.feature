@javascript @totara @block_facetoface
Feature: Confirm Sessions show up in my face to face sessions
  In order for the my sessions page is correct
  As an admin
  I need to be able to see and create sessions

  Background:
    Given I am on a totara site
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | learner1  | Learner | 1 | learner@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | learner1 | C1 | student |
    And I log in as "admin"
    And I click on "Find Learning" in the totara menu
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Face-to-face" to section "1" and I fill the form with:
      | Name | Test session |
      | Description | Test session |
    And I follow "Test session"
    And I follow "Add a new event"
    And I click on "Edit date" "link"
    And I set the following fields to these values:
      | timestart[day]     | 2    |
      | timestart[month]   | 1    |
      | timestart[year]    | 2020 |
      | timestart[hour]    | 11   |
      | timestart[minute]  | 00   |
      | timefinish[day]    | 2    |
      | timefinish[month]  | 1    |
      | timefinish[year]   | 2020 |
      | timefinish[hour]   | 12   |
      | timefinish[minute] | 00   |
    And I click on "OK" "button" in the "Select date" "totaradialogue"
    And I set the following fields to these values:
      | Capacity                | 20           |
      | Details                 | some details |
    And I click on "Save changes" "button"
    And I follow "Add a new event"
    And I click on "Edit date" "link"
    And I set the following fields to these values:
      | timestart[day]     | 2    |
      | timestart[month]   | 1    |
      | timestart[year]    | 2020 |
      | timestart[hour]    | 11   |
      | timestart[minute]  | 00   |
      | timefinish[day]    | 2    |
      | timefinish[month]  | 1    |
      | timefinish[year]   | 2020 |
      | timefinish[hour]   | 12   |
      | timefinish[minute] | 00   |
    And I click on "OK" "button" in the "Select date" "totaradialogue"
    And I set the following fields to these values:
      | Capacity                | 20                |
      | Details                 | some more details |
    And I click on "Save changes" "button"
    And I follow "C1"
    And I add a "Face-to-face" to section "2" and I fill the form with:
      | Name        | Test session 2              |
      | Description | Test session 2 description  |
    And I follow "Test session 2"
    And I follow "Add a new event"
    And I click on "Edit date" "link"
    And I set the following fields to these values:
      | timestart[day]     | 1    |
      | timestart[month]   | 2    |
      | timestart[year]    | 2020 |
      | timestart[hour]    | 11   |
      | timestart[minute]  | 00   |
      | timefinish[day]    | 1    |
      | timefinish[month]  | 2    |
      | timefinish[year]   | 2020 |
      | timefinish[hour]   | 12   |
      | timefinish[minute] | 00   |
    And I click on "OK" "button" in the "Select date" "totaradialogue"
    And I set the following fields to these values:
      | Capacity                | 30             |
      | Details                 | 1 some details |
    And I click on "Save changes" "button"
    And I follow "Add a new event"
    And I click on "Edit date" "link"
    And I set the following fields to these values:
      | timestart[day]     | 2    |
      | timestart[month]   | 2    |
      | timestart[year]    | 2020 |
      | timestart[hour]    | 11   |
      | timestart[minute]  | 00   |
      | timefinish[day]    | 2    |
      | timefinish[month]  | 2    |
      | timefinish[year]   | 2020 |
      | timefinish[hour]   | 12   |
      | timefinish[minute] | 00   |
    And I click on "OK" "button" in the "Select date" "totaradialogue"
    And I set the following fields to these values:
      | Capacity                | 30                  |
      | Details                 | 2 some more details |
    And I click on "Save changes" "button"
    And I add the "Face-to-face" block

  Scenario: Test filters
    Given I follow "Upcoming events"
    And I set the following fields to these values:
      | from[day]   | 1    |
      | from[month] | 1    |
      | from[year]  | 2019 |
      | to[enabled] | 0    |
    And I click on "Apply" "button"
    And I should see "Test session"
    And I should see "Test session 2"
    And I set the following fields to these values:
      | to[enabled] | 1    |
      | to[day]     | 1    |
      | to[month]   | 2    |
      | to[year]    | 2020 |
    And I click on "Apply" "button"
    And I should see "Test session"
    And I should not see "Test session 2"
