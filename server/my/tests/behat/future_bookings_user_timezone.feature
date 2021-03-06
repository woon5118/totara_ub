@javascript @mod @mod_facetoface @totara @totara_reportbuilder
Feature: My Future Bookings seminar sessions report overview
  In order to see all student future bookings
  As an admin
  I need to create an user with different timezone and see user future bookings

  Background:
    Given I am on a totara site
    And the following "users" exist:
      | username | firstname | lastname | email                   | timezone         |
      | alice    | Alice     | Smith    | alice.smith@example.com | America/New_York |
    And the following "courses" exist:
      | fullname | shortname | category | enablecompletion |
      | Course 1 | C1        | 0        | 1                |
    And the following "course enrolments" exist:
      | user  | course | role    |
      | alice | C1     | student |
    And the following "activities" exist:
      | activity   | name            | course | idnumber | multiplesessions |
      | facetoface | Seminar TL-9395 | C1     | S9395    | 1                |

    And I log in as "admin"
    And I am on "Course 1" course homepage
    And I follow "Seminar TL-9395"

    And I follow "Add event"
    And I click on "Edit session" "link"
    And I set the following fields to these values:
      | sessiontimezone      | Europe/Prague   |
      | timestart[day]       | 2               |
      | timestart[month]     | 5               |
      | timestart[year]      | ## next year ## Y ## |
      | timestart[hour]      | 1               |
      | timestart[minute]    | 15              |
      | timestart[timezone]  | Europe/Prague   |
      | timefinish[day]      | 2               |
      | timefinish[month]    | 5               |
      | timefinish[year]     | ## next year ## Y ## |
      | timefinish[hour]     | 3               |
      | timefinish[minute]   | 45              |
      | timefinish[timezone] | Europe/Prague   |
    And I click on "OK" "button" in the "Select date" "totaradialogue"
    And I press "Save changes"

    And I follow "Add event"
    And I click on "Edit session" "link"
    And I set the following fields to these values:
      | timestart[day]       | 2               |
      | timestart[month]     | 4               |
      | timestart[year]      | ## next year ## Y ## |
      | timestart[hour]      | 1               |
      | timestart[minute]    | 15              |
      | timefinish[day]      | 2               |
      | timefinish[month]    | 4               |
      | timefinish[year]     | ## next year ## Y ## |
      | timefinish[hour]     | 3               |
      | timefinish[minute]   | 45              |
    And I click on "OK" "button" in the "Select date" "totaradialogue"
    And I press "Save changes"

    And I click on the seminar event action "Attendees" in row "Australia/Perth"
    And I set the field "Attendee actions" to "Add users"
    And I set the field "potential users" to "Alice Smith, alice.smith@example.com"
    And I press "Add"
    And I press "Continue"
    And I press "Confirm"
    And I wait until "Alice Smith" "text" exists
    And I click on "View all events" "link"

    And I click on the seminar event action "Attendees" in row "Europe/Prague"
    And I set the field "Attendee actions" to "Add users"
    And I set the field "potential users" to "Alice Smith, alice.smith@example.com"
    And I press "Add"
    And I press "Continue"
    And I press "Confirm"
    And I wait until "Alice Smith" "text" exists
    And I log out

  @javascript
  Scenario: Login as a student and check My future bookings event timezones
    And I log in as "alice"
    And I am on "Dashboard" page
    And I click on "Bookings" "link"
    And I should see "America/New_York"
    And I should see "Europe/Prague"

