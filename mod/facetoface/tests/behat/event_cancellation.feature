@mod @mod_facetoface @totara
Feature: Seminar event cancellation basic
  In order to cancel the whole event
  As a teacher
  I need to be to switch event to cancelled status

  Background:
    Given I am on a totara site
    And the following "users" exist:
      | username     | firstname | lastname  | email                 |
      | teacher1     | Terry3    | Teacher   | teacher@example.com   |
      | student1     | Sam1      | Student1  | student1@example.com  |
      | student2     | Sam2      | Student2  | student2@example.com  |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
    And I log in as "teacher1"
    And I click on "Find Learning" in the totara menu
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Seminar" to section "1" and I fill the form with:
      | Name                                    | Test seminar name        |
      | Description                             | Test seminar description |

  @javascript
  Scenario: Cancel and delete the whole seminar event
    And I follow "View all events"
    And I follow "Add a new event"
    And I set the field "Maximum bookings" to "20"
    And I click on "Edit date" "link"
    And I fill seminar session with relative date in form data:
      | timestart[day]     | +1               |
      | timestart[month]   | 0                |
      | timestart[year]    | 0                |
      | timestart[hour]    | 0                |
      | timestart[minute]  | 0                |
      | timefinish[day]    | +1               |
      | timefinish[month]  | 0                |
      | timefinish[year]   | 0                |
      | timefinish[hour]   | +1               |
      | timefinish[minute] | 0                |
    And I press "OK"
    And I press "Save changes"
    And I follow "Add a new event"
    And I set the field "Maximum bookings" to "30"
    And I click on "Edit date" "link"
    And I fill seminar session with relative date in form data:
      | timestart[day]     | +2               |
      | timestart[month]   | 0                |
      | timestart[year]    | 0                |
      | timestart[hour]    | 0                |
      | timestart[minute]  | 0                |
      | timefinish[day]    | +2               |
      | timefinish[month]  | 0                |
      | timefinish[year]   | 0                |
      | timefinish[hour]   | +1               |
      | timefinish[minute] | 0                |
    And I press "OK"
    And I press "Save changes"

    When I click on "Cancel event" "link" in the "0 / 30" "table_row"
    And I should see "Canceling event in"
    And I should see "Are you completely sure you want to cancel this event?"
    And I press "Yes"
    Then I should see "Event cancelled" in the ".alert-success" "css_element"
    And I should see "Event cancelled" in the "0 / 30" "table_row"
    And I should not see "Edit event" in the "0 / 30" "table_row"
    And I should see "Booking open" in the "0 / 20" "table_row"

    When I click on "Delete event" "link" in the "0 / 30" "table_row"
    And I should see "Deleting event in"
    And I press "Continue"
    Then I should not see "0 / 30"
