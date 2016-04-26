@mod @mod_facetoface @totara
Feature: Cancellation for session
  In order to allow or not cancellations in seminar sessions
  As a teacher
  I need to create seminar sessions with different settings (always/never/cut-off period)

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
      | Users can sign-up to multiple sessions  | 1                           |
    And I log out

  @javascript
  Scenario: User can cancel their booking at any time until session starts
    Given I log in as "teacher1"
    And I click on "Find Learning" in the totara menu
    And I follow "Course 1"
    And I follow "View all events"
    And I follow "Add a new event"
    And I click on "Edit date" "link"
    And I fill seminar session with relative date in form data:
      | sessiontimezone    | Pacific/Auckland |
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
    And I set the following fields to these values:
      | capacity           | 3                |
    And I click on "At any time" "radio"
    And I press "Save changes"
    And I log out

    When I log in as "student1"
    And I click on "Find Learning" in the totara menu
    And I follow "Course 1"
    And I follow "View all events"
    And I click on the link "Sign-up" in row 1
    And I press "Sign-up"
    Then I should see "Your booking has been completed."
    And I should see "Cancel booking"
    When I click on the link "Cancel booking" in row 1
    And I press "Yes"
    And I should not see "Cancel booking"
    And I log out

  @javascript
  Scenario: User cannot cancel their booking (Never)
    Given I log in as "teacher1"
    And I click on "Find Learning" in the totara menu
    And I follow "Course 1"
    And I follow "View all events"
    And I follow "Add a new event"
    And I click on "Edit date" "link"
    And I fill seminar session with relative date in form data:
      | sessiontimezone    | Pacific/Auckland |
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
    And I set the following fields to these values:
      | capacity           | 3                |
    And I click on "Never" "radio"
    And I press "Save changes"
    And I log out

    When I log in as "student1"
    And I click on "Find Learning" in the totara menu
    And I follow "Course 1"
    And I follow "View all events"
    And I click on the link "Sign-up" in row 1
    And I press "Sign-up"
    Then I should see "Your booking has been completed."
    And I should not see "Cancel booking"
    And I log out

  @javascript
  Scenario: User can cancel their booking if cut-off period is not reached
    Given I log in as "teacher1"
    And I click on "Find Learning" in the totara menu
    And I follow "Course 1"
    And I follow "View all events"
    And I follow "Add a new event"
    And I click on "Edit date" "link"
    And I fill seminar session with relative date in form data:
      | sessiontimezone    | Pacific/Auckland |
      | timestart[day]     | +3               |
      | timestart[month]   | 0                |
      | timestart[year]    | 0                |
      | timestart[hour]    | 0                |
      | timestart[minute]  | 0                |
      | timefinish[day]    | +3               |
      | timefinish[month]  | 0                |
      | timefinish[year]   | 0                |
      | timefinish[hour]   | +1               |
      | timefinish[minute] | 0                |
    And I press "OK"
    And I set the following fields to these values:
      | capacity           | 3                |
    And I click on "Until specified period" "radio"
    And I press "Save changes"
    And I log out

    When I log in as "student1"
    And I click on "Find Learning" in the totara menu
    And I follow "Course 1"
    And I follow "View all events"
    And I click on the link "Sign-up" in row 1
    And I press "Sign-up"
    Then I should see "Your booking has been completed."
    And I should see "Cancel booking"
    And I log out

    And I log in as "teacher1"
    And I click on "Find Learning" in the totara menu
    And I follow "Course 1"
    And I follow "View all events"
    And I click on "Edit event" "link"
    And I click on "Edit date" "link"
    And I fill seminar session with relative date in form data:
      | sessiontimezone    | Pacific/Auckland |
      | timestart[day]     | +1               |
      | timestart[month]   | 0                |
      | timestart[year]    | 0                |
      | timestart[hour]    | -1               |
      | timestart[minute]  | 0                |
      | timefinish[day]    | +1               |
      | timefinish[month]  | 0                |
      | timefinish[year]   | 0                |
      | timefinish[hour]   | -1               |
      | timefinish[minute] | 0                |
    And I press "OK"
    And I set the following fields to these values:
      | cancellationcutoff[number]   | 2      |
      | cancellationcutoff[timeunit] | days   |
    And I press "Save changes"
    And I log out

    When I log in as "student1"
    And I click on "Find Learning" in the totara menu
    And I follow "Course 1"
    And I follow "View all events"
    Then I should not see "Cancel booking"
    And I log out
