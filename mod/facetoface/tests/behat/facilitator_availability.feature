@mod @mod_facetoface @totara @totara_reportbuilder @javascript
Feature: Seminar facilitator availability
  In order to prevent facilitator conflicts
  As an editing trainer
  I need to see only available facilitators

  Background:
    Given I am on a totara site
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | One      | teacher1@example.com |
      | teacher2 | Teacher   | Two      | teacher2@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | teacher2 | C1     | editingteacher |
    And I log in as "admin"
    And I navigate to "Facilitators" node in "Site administration > Seminars"
    And I press "Add a new facilitator"
    And I set the following fields to these values:
      | Facilitator Name        | facilitator 1 |
      | Allow booking conflicts | 0             |
    And I press "Add a facilitator"
    And I press "Add a new facilitator"
    And I set the following fields to these values:
      | Facilitator Name        | facilitator 2 |
      | Allow booking conflicts | 1             |
    And I press "Add a facilitator"
    And I press "Add a new facilitator"
    And I set the following fields to these values:
      | Facilitator Name        | facilitator 3 |
      | Allow booking conflicts | 0             |
    And I press "Add a facilitator"
    And I click on "Hide" "link" in the "facilitator 3" "table_row"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Seminar" to section "1" and I fill the form with:
      | Name        | Test Seminar 1 |
      | Description | test           |
    And I add a "Seminar" to section "1" and I fill the form with:
      | Name        | Test Seminar 2 |
      | Description | test           |
    And I log out

  Scenario: Time based seminar facilitator conflicts
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test Seminar 1"
    And I follow "Add event"
    And I click on "Edit session" "link"
    And I set the following fields to these values:
      | timestart[day]     | 1    |
      | timestart[month]   | 1    |
      | timestart[year]    | 2025 |
      | timestart[hour]    | 11   |
      | timestart[minute]  | 00   |
      | timefinish[day]    | 1    |
      | timefinish[month]  | 1    |
      | timefinish[year]   | 2025 |
      | timefinish[hour]   | 12   |
      | timefinish[minute] | 00   |
    And I click on "OK" "button" in the "Select date" "totaradialogue"
    And I click on "Select facilitators" "link"
    And I should see "facilitator 1"
    And I should see "facilitator 2"
    And I should not see "facilitator 3"
    And I click on "facilitator 1" "text" in the "Choose facilitators" "totaradialogue"
    And I click on "facilitator 2" "text" in the "Choose facilitators" "totaradialogue"
    And I click on "OK" "button" in the "Choose facilitators" "totaradialogue"
    And I click on "Select facilitators" "link"
    And I should see "facilitator 1"
    And I should see "facilitator 2"
    And I should not see "facilitator 3"
    And I click on "OK" "button" in the "Choose facilitators" "totaradialogue"
    And I press "Add a new session"
    # The UI is not usable much here, we just save this and go back and the last added session will be listed first.
    And I press "Save changes"
    And I click on "Edit event" "link" in the "0 / 10" "table_row"
    And I click on "Edit session" "link"
    And I set the following fields to these values:
      | timestart[day]     | 1    |
      | timestart[month]   | 1    |
      | timestart[year]    | 2026 |
      | timestart[hour]    | 11   |
      | timestart[minute]  | 00   |
      | timefinish[day]    | 1    |
      | timefinish[month]  | 1    |
      | timefinish[year]   | 2026 |
      | timefinish[hour]   | 12   |
      | timefinish[minute] | 00   |
    And I click on "OK" "button" in the "Select date" "totaradialogue"
    And I click on "Select facilitators" "link"
    And I should see "facilitator 1"
    And I should see "facilitator 2"
    And I should not see "facilitator 3"
    And I click on "facilitator 2" "text" in the "Choose facilitators" "totaradialogue"
    And I click on "OK" "button" in the "Choose facilitators" "totaradialogue"
    And I press "Add a new session"
    # The UI is not usable much here, we just save this and go back and the last added session will be listed first.
    And I press "Save changes"
    And I click on "Edit event" "link" in the "0 / 10" "table_row"
    And I click on "Edit session" "link"
    And I set the following fields to these values:
      | timestart[day]     | 1    |
      | timestart[month]   | 1    |
      | timestart[year]    | 2025 |
      | timestart[hour]    | 12   |
      | timestart[minute]  | 00   |
      | timefinish[day]    | 1    |
      | timefinish[month]  | 1    |
      | timefinish[year]   | 2025 |
      | timefinish[hour]   | 13   |
      | timefinish[minute] | 00   |
    And I click on "OK" "button" in the "Select date" "totaradialogue"
    And I click on "Select facilitators" "link"
    And I should see "facilitator 1"
    And I should see "facilitator 2"
    And I should not see "facilitator 3"
    And I click on "facilitator 1" "text" in the "Choose facilitators" "totaradialogue"
    And I click on "OK" "button" in the "Choose facilitators" "totaradialogue"
    And I press "Save changes"
    And I click on "Edit event" "link" in the "0 / 10" "table_row"
    And I should see "facilitator 1" in the "1 January 2025 1:00 PM" "table_row"
    And I should not see "facilitator 2" in the "1 January 2025 1:00 PM" "table_row"
    And I should see "facilitator 1" in the "1 January 2025 11:00 AM" "table_row"
    And I should see "facilitator 2" in the "1 January 2025 11:00 AM" "table_row"
    And I should not see "facilitator 1" in the "January 2026" "table_row"
    And I should see "facilitator 2" in the "January 2026" "table_row"
    And I press "Cancel"

    When I follow "Add event"
    And I set the following fields to these values:
      | Maximum bookings | 20 |
    And I click on "Edit session" "link"
    And I set the following fields to these values:
      | timestart[day]     | 1    |
      | timestart[month]   | 1    |
      | timestart[year]    | 2025 |
      | timestart[hour]    | 10   |
      | timestart[minute]  | 00   |
      | timefinish[day]    | 1    |
      | timefinish[month]  | 1    |
      | timefinish[year]   | 2025 |
      | timefinish[hour]   | 11   |
      | timefinish[minute] | 00   |
    And I click on "OK" "button" in the "Select date" "totaradialogue"
    And I click on "Select facilitators" "link"
    And I should see "facilitator 1"
    And I should see "facilitator 2"
    And I should not see "facilitator 3"
    And I click on "facilitator 1" "text" in the "Choose facilitators" "totaradialogue"
    And I click on "OK" "button" in the "Choose facilitators" "totaradialogue"
    And I press "Save changes"
    And I click on "Edit event" "link" in the "0 / 20" "table_row"
    And I should see "facilitator 1" in the "1 January 2025" "table_row"
    And I should not see "facilitator 2" in the "1 January 2025" "table_row"
    And I press "Cancel"

    When I follow "Add event"
    And I set the following fields to these values:
      | Maximum bookings | 30 |
    And I click on "Edit session" "link"
    And I set the following fields to these values:
      | timestart[day]     | 1    |
      | timestart[month]   | 1    |
      | timestart[year]    | 2025 |
      | timestart[hour]    | 13   |
      | timestart[minute]  | 00   |
      | timefinish[day]    | 1    |
      | timefinish[month]  | 1    |
      | timefinish[year]   | 2025 |
      | timefinish[hour]   | 14   |
      | timefinish[minute] | 00   |
    And I click on "OK" "button" in the "Select date" "totaradialogue"
    And I click on "Select facilitators" "link"
    And I should see "facilitator 1"
    And I should see "facilitator 2"
    And I should not see "facilitator 3"
    And I click on "facilitator 1" "text" in the "Choose facilitators" "totaradialogue"
    And I click on "facilitator 2" "text" in the "Choose facilitators" "totaradialogue"
    And I click on "OK" "button" in the "Choose facilitators" "totaradialogue"
    And I press "Save changes"
    And I click on "Edit event" "link" in the "0 / 30" "table_row"
    And I should see "facilitator 1" in the "1 January 2025" "table_row"
    And I should see "facilitator 2" in the "1 January 2025" "table_row"
    And I press "Cancel"

    When I follow "Add event"
    And I set the following fields to these values:
      | Maximum bookings | 40 |
    And I click on "Edit session" "link"
    And I set the following fields to these values:
      | timestart[day]     | 1    |
      | timestart[month]   | 1    |
      | timestart[year]    | 2026 |
      | timestart[hour]    | 11   |
      | timestart[minute]  | 00   |
      | timefinish[day]    | 1    |
      | timefinish[month]  | 1    |
      | timefinish[year]   | 2026 |
      | timefinish[hour]   | 12   |
      | timefinish[minute] | 00   |
    And I click on "OK" "button" in the "Select date" "totaradialogue"
    And I click on "Select facilitators" "link"
    And I should see "facilitator 1"
    And I should see "facilitator 2"
    And I should not see "facilitator 3"
    And I click on "facilitator 2" "text" in the "Choose facilitators" "totaradialogue"
    And I click on "OK" "button" in the "Choose facilitators" "totaradialogue"
    And I press "Save changes"
    And I click on "Edit event" "link" in the "0 / 40" "table_row"
    And I should not see "facilitator 1" in the "1 January 2026" "table_row"
    And I should see "facilitator 2" in the "1 January 2026" "table_row"
    And I press "Cancel"

    When I follow "Add event"
    And I set the following fields to these values:
      | Maximum bookings | 50 |
    And I click on "Edit session" "link"
    And I set the following fields to these values:
      | timestart[day]     | 1    |
      | timestart[month]   | 1    |
      | timestart[year]    | 2025 |
      | timestart[hour]    | 11   |
      | timestart[minute]  | 00   |
      | timefinish[day]    | 1    |
      | timefinish[month]  | 1    |
      | timefinish[year]   | 2025 |
      | timefinish[hour]   | 12   |
      | timefinish[minute] | 00   |
    And I click on "OK" "button" in the "Select date" "totaradialogue"
    And I click on "Select facilitators" "link"
    Then I should see "facilitator 1 (facilitator unavailable on selected dates)"
    Then I should not see "facilitator 2 (facilitator unavailable on selected dates)"
    When I click on "Cancel" "button" in the "Choose facilitators" "totaradialogue"
    And I press "Cancel"

    And I click on "Edit event" "link" in the "0 / 20" "table_row"
    And I click on "Edit session" "link"
    And I set the following fields to these values:
      | timestart[day]     | 1    |
      | timestart[month]   | 1    |
      | timestart[year]    | 2025 |
      | timestart[hour]    | 11   |
      | timestart[minute]  | 00   |
      | timefinish[day]    | 1    |
      | timefinish[month]  | 1    |
      | timefinish[year]   | 2025 |
      | timefinish[hour]   | 12   |
      | timefinish[minute] | 00   |
    And I click on "OK" "button" in the "Select date" "totaradialogue"
    And I should see "facilitator 1 is already booked"
    When I click on "Cancel" "button" in the "Select date" "totaradialogue"
    And I press "Cancel"

  Scenario: Hiding related seminar facilitator availability
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test Seminar 1"
    And I follow "Add event"
    And I set the following fields to these values:
      | Maximum bookings | 20 |
    And I click on "Edit session" "link"
    And I set the following fields to these values:
      | timestart[day]     | 1    |
      | timestart[month]   | 1    |
      | timestart[year]    | 2025 |
      | timestart[hour]    | 11   |
      | timestart[minute]  | 00   |
      | timefinish[day]    | 1    |
      | timefinish[month]  | 1    |
      | timefinish[year]   | 2025 |
      | timefinish[hour]   | 12   |
      | timefinish[minute] | 00   |
    And I click on "OK" "button" in the "Select date" "totaradialogue"
    And I click on "Select facilitators" "link"
    And I should see "facilitator 1"
    And I should see "facilitator 2"
    And I should not see "facilitator 3"
    And I click on "facilitator 1" "text" in the "Choose facilitators" "totaradialogue"
    And I click on "OK" "button" in the "Choose facilitators" "totaradialogue"
    And I click on "Select facilitators" "link"
    And I should see "facilitator 1"
    And I should see "facilitator 2"
    And I should not see "facilitator 3"
    And I click on "OK" "button" in the "Choose facilitators" "totaradialogue"
    And I press "Save changes"
    And I log out
    And I log in as "admin"
    And I navigate to "Facilitators" node in "Site administration > Seminars"
    And I click on "Hide" "link" in the "facilitator 1" "table_row"
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test Seminar 1"

    When I follow "Add event"
    And I click on "Edit session" "link"
    And I set the following fields to these values:
      | timestart[day]     | 1    |
      | timestart[month]   | 1    |
      | timestart[year]    | 2026 |
      | timestart[hour]    | 11   |
      | timestart[minute]  | 00   |
      | timefinish[day]    | 1    |
      | timefinish[month]  | 1    |
      | timefinish[year]   | 2026 |
      | timefinish[hour]   | 12   |
      | timefinish[minute] | 00   |
    And I click on "OK" "button" in the "Select date" "totaradialogue"
    And I click on "Select facilitators" "link"
    Then I should not see "facilitator 1"
    And I should see "facilitator 2"
    And I should not see "facilitator 3"
    And I click on "Cancel" "button" in the "Choose facilitators" "totaradialogue"
    And I press "Cancel"

    When I click on "Edit event" "link" in the "0 / 20" "table_row"
    And I click on "Select facilitators" "link"
    And I should see "facilitator 1"
    And I should see "facilitator 2"
    And I should not see "facilitator 3"
    And I click on "Cancel" "button" in the "Choose facilitators" "totaradialogue"
    And I press "Add a new session"
    # The UI is not usable much here, we just save this and go back and the last added session will be listed first.
    And I press "Save changes"
    And I click on "Edit event" "link" in the "0 / 20" "table_row"
    And I click on "Edit session" "link"
    And I set the following fields to these values:
      | timestart[day]     | 1    |
      | timestart[month]   | 1    |
      | timestart[year]    | 2026 |
      | timestart[hour]    | 11   |
      | timestart[minute]  | 00   |
      | timefinish[day]    | 1    |
      | timefinish[month]  | 1    |
      | timefinish[year]   | 2026 |
      | timefinish[hour]   | 12   |
      | timefinish[minute] | 00   |
    And I click on "OK" "button" in the "Select date" "totaradialogue"
    And I click on "Select facilitators" "link"
    Then I should see "facilitator 1"
    And I should see "facilitator 2"
    And I should not see "facilitator 3"
    And I click on "facilitator 1" "text" in the "Choose facilitators" "totaradialogue"
    And I click on "OK" "button" in the "Choose facilitators" "totaradialogue"
    And I press "Save changes"
    And I should see "Upcoming events"

  Scenario: Custom seminar facilitator availability
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test Seminar 1"
    And I follow "Add event"
    And I set the following fields to these values:
      | Maximum bookings | 30 |
    And I click on "Edit session" "link"
    And I set the following fields to these values:
      | timestart[day]     | 1    |
      | timestart[month]   | 1    |
      | timestart[year]    | 2025 |
      | timestart[hour]    | 11   |
      | timestart[minute]  | 00   |
      | timefinish[day]    | 1    |
      | timefinish[month]  | 1    |
      | timefinish[year]   | 2025 |
      | timefinish[hour]   | 12   |
      | timefinish[minute] | 00   |
    And I click on "OK" "button" in the "Select date" "totaradialogue"
    And I click on "Select facilitators" "link"
    And I click on "Create" "link" in the "Choose facilitators" "totaradialogue"
    And I set the following fields to these values:
      | Facilitator Name        | Etwas 1 |
      | Allow booking conflicts | 0       |
    And I click on "OK" "button" in the "Create new facilitator" "totaradialogue"

    When  I press "Add a new session"
    # The UI is not usable much here, we just save this and go back and the last added session will be listed first.
    And I press "Save changes"
    And I click on "Edit event" "link" in the "0 / 30" "table_row"
    And I click on "Edit session" "link"
    And I set the following fields to these values:
      | timestart[day]     | 1    |
      | timestart[month]   | 1    |
      | timestart[year]    | 2025 |
      | timestart[hour]    | 12   |
      | timestart[minute]  | 00   |
      | timefinish[day]    | 1    |
      | timefinish[month]  | 1    |
      | timefinish[year]   | 2025 |
      | timefinish[hour]   | 13   |
      | timefinish[minute] | 00   |
    And I click on "OK" "button" in the "Select date" "totaradialogue"
    And I click on "Select facilitators" "link"
    Then I should see "Etwas 1 (Seminar: Test Seminar 1)"
    And I should see "facilitator 1"
    And I should see "facilitator 2"
    And I should not see "facilitator 3"
    And I click on "Etwas 1 (Seminar: Test Seminar 1)" "text" in the "Choose facilitators" "totaradialogue"
    And I click on "OK" "button" in the "Choose facilitators" "totaradialogue"
    And I press "Save changes"
    And I should see "Upcoming events"

    When I follow "Add event"
    And I set the following fields to these values:
      | Maximum bookings | 40 |
    And I click on "Edit session" "link"
    And I set the following fields to these values:
      | timestart[day]     | 1    |
      | timestart[month]   | 1    |
      | timestart[year]    | 2025 |
      | timestart[hour]    | 11   |
      | timestart[minute]  | 00   |
      | timefinish[day]    | 1    |
      | timefinish[month]  | 1    |
      | timefinish[year]   | 2025 |
      | timefinish[hour]   | 12   |
      | timefinish[minute] | 00   |
    And I click on "OK" "button" in the "Select date" "totaradialogue"
    And I click on "Select facilitators" "link"
    Then I should see "Etwas 1 (facilitator unavailable on selected dates) (Seminar: Test Seminar 1)"
    And I should see "facilitator 1"
    And I should see "facilitator 2"
    And I should not see "facilitator 3"
    And I click on "Create" "link" in the "Choose facilitators" "totaradialogue"
    And I set the following fields to these values:
      | Facilitator Name        | Etwas 2 |
      | Allow booking conflicts | 0       |
    And I click on "OK" "button" in the "Create new facilitator" "totaradialogue"
    And I click on "Delete" "link" in the "Etwas 2" "table_row"
    And I press "Save changes"

    When I click on "Edit event" "link" in the "0 / 40" "table_row"
    And I should not see "Etwas 2" in the "1 January 2025" "table_row"
    And I click on "Select facilitators" "link"
    Then I should see "Etwas 1 (facilitator unavailable on selected dates) (Seminar: Test Seminar 1)"
    And I should see "Etwas 2 (Seminar: Test Seminar 1)"
    And I should see "facilitator 1"
    And I should see "facilitator 2"
    And I should not see "facilitator 3"
    And I click on "Cancel" "button" in the "Choose facilitators" "totaradialogue"
    And I press "Cancel"

    When I am on "Course 1" course homepage
    And I follow "Test Seminar 2"
    And I follow "Add event"
    And I click on "Select facilitators" "link"
    Then I should not see "Etwas 1"
    And I should see "Etwas 2 (Seminar: Test Seminar 2)"
    And I should see "facilitator 1"
    And I should see "facilitator 2"
    And I should not see "facilitator 3"
    And I click on "Cancel" "button" in the "Choose facilitators" "totaradialogue"
    And I press "Cancel"
    And I log out

    When I log in as "teacher2"
    And I am on "Course 1" course homepage
    And I follow "Test Seminar 2"
    And I follow "Add event"
    And I click on "Select facilitators" "link"
    Then I should not see "Etwas 1"
    And I should not see "Etwas 2"
    And I should see "facilitator 1"
    And I should see "facilitator 2"
    And I should not see "facilitator 3"
    And I click on "Cancel" "button" in the "Choose facilitators" "totaradialogue"
    And I press "Cancel"

    When I am on "Course 1" course homepage
    And I follow "Test Seminar 1"
    And I follow "Add event"
    And I click on "Select facilitators" "link"
    Then I should see "Etwas 1 (Seminar: Test Seminar 1)"
    And I should not see "Etwas 2"
    And I should see "facilitator 1"
    And I should see "facilitator 2"
    And I should not see "facilitator 3"
    And I click on "Cancel" "button" in the "Choose facilitators" "totaradialogue"
    And I press "Cancel"

  Scenario: Seminar switch site facilitator to not allow conflicts
    Given I log in as "admin"
    And I am on "Course 1" course homepage
    And I follow "Test Seminar 1"
    And I follow "Add event"
    And I set the following fields to these values:
      | Maximum bookings | 20 |
    And I click on "Edit session" "link"
    And I set the following fields to these values:
      | timestart[day]     | 1    |
      | timestart[month]   | 1    |
      | timestart[year]    | 2025 |
      | timestart[hour]    | 11   |
      | timestart[minute]  | 00   |
      | timefinish[day]    | 1    |
      | timefinish[month]  | 1    |
      | timefinish[year]   | 2025 |
      | timefinish[hour]   | 12   |
      | timefinish[minute] | 00   |
    And I click on "OK" "button" in the "Select date" "totaradialogue"
    And I click on "Select facilitators" "link"
    And I click on "facilitator 2" "text" in the "Choose facilitators" "totaradialogue"
    And I click on "OK" "button" in the "Choose facilitators" "totaradialogue"
    And I press "Save changes"
    And I follow "Add event"
    And I set the following fields to these values:
      | Maximum bookings | 30 |
    And I click on "Edit session" "link"
    And I set the following fields to these values:
      | timestart[day]     | 1    |
      | timestart[month]   | 1    |
      | timestart[year]    | 2025 |
      | timestart[hour]    | 11   |
      | timestart[minute]  | 00   |
      | timefinish[day]    | 1    |
      | timefinish[month]  | 1    |
      | timefinish[year]   | 2025 |
      | timefinish[hour]   | 12   |
      | timefinish[minute] | 00   |
    And I click on "OK" "button" in the "Select date" "totaradialogue"
    And I click on "Select facilitators" "link"
    And I click on "facilitator 2" "text" in the "Choose facilitators" "totaradialogue"
    And I click on "OK" "button" in the "Choose facilitators" "totaradialogue"
    When I press "Save changes"

    When I navigate to "Facilitators" node in "Site administration > Seminars"
    And I click on "Edit" "link" in the "facilitator 2" "table_row"
    And I set the following fields to these values:
      | Allow booking conflicts | 0 |
    And I press "Save changes"
    Then I should see "Facilitator has conflicting usage"
    And I press "Cancel"

    When I am on "Course 1" course homepage
    And I follow "Test Seminar 1"
    And I click on "Edit event" "link" in the "0 / 30" "table_row"
    And I click on "Edit session" "link"
    And I set the following fields to these values:
      | timestart[day]     | 1    |
      | timestart[month]   | 1    |
      | timestart[year]    | 2025 |
      | timestart[hour]    | 12   |
      | timestart[minute]  | 00   |
      | timefinish[day]    | 1    |
      | timefinish[month]  | 1    |
      | timefinish[year]   | 2025 |
      | timefinish[hour]   | 13   |
      | timefinish[minute] | 00   |
    And I click on "OK" "button" in the "Select date" "totaradialogue"
    And I press "Save changes"
    And I navigate to "Facilitators" node in "Site administration > Seminars"
    And I click on "Edit" "link" in the "facilitator 2" "table_row"
    And I set the following fields to these values:
      | Allow booking conflicts | 0 |
    And I press "Save changes"
    Then I should not see "Facilitator has conflicting usage"

  Scenario: Seminar switch custom facilitator to not allow conflicts
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test Seminar 1"
    And I follow "Add event"
    And I set the following fields to these values:
      | Maximum bookings | 40 |
    And I click on "Edit session" "link"
    And I set the following fields to these values:
      | timestart[day]     | 1    |
      | timestart[month]   | 1    |
      | timestart[year]    | 2025 |
      | timestart[hour]    | 11   |
      | timestart[minute]  | 00   |
      | timefinish[day]    | 1    |
      | timefinish[month]  | 1    |
      | timefinish[year]   | 2025 |
      | timefinish[hour]   | 12   |
      | timefinish[minute] | 00   |
    And I click on "OK" "button" in the "Select date" "totaradialogue"
    And I click on "Select facilitators" "link"
    And I click on "Create" "link" in the "Choose facilitators" "totaradialogue"
    And I set the following fields to these values:
      | Facilitator Name        | Etwas 1 |
      | Allow booking conflicts | 1       |
    And I click on "OK" "button" in the "Create new facilitator" "totaradialogue"
    And I press "Save changes"
    And I follow "Add event"
    And I set the following fields to these values:
      | Maximum bookings | 50 |
    And I click on "Edit session" "link"
    And I set the following fields to these values:
      | timestart[day]     | 1    |
      | timestart[month]   | 1    |
      | timestart[year]    | 2025 |
      | timestart[hour]    | 11   |
      | timestart[minute]  | 00   |
      | timefinish[day]    | 1    |
      | timefinish[month]  | 1    |
      | timefinish[year]   | 2025 |
      | timefinish[hour]   | 12   |
      | timefinish[minute] | 00   |
    And I click on "OK" "button" in the "Select date" "totaradialogue"
    And I click on "Select facilitators" "link"
    And I click on "Etwas 1 (Seminar: Test Seminar 1)" "link"
    And I click on "OK" "button" in the "Choose facilitators" "totaradialogue"
    And I press "Save changes"

    When I click on "Edit event" "link" in the "0 / 50" "table_row"
    And I click on "Edit facilitator" "link" in the "Etwas 1" "table_row"
    And I set the following fields to these values:
      | Allow booking conflicts | 0 |
    And I click on "OK" "button" in the "Edit facilitator" "totaradialogue"
    Then I should see "Facilitator has conflicting usage" in the "Edit facilitator" "totaradialogue"
    And I click on "Cancel" "button" in the "Edit facilitator" "totaradialogue"
    And I click on "Edit session" "link"
    And I set the following fields to these values:
      | timestart[day]     | 1    |
      | timestart[month]   | 1    |
      | timestart[year]    | 2025 |
      | timestart[hour]    | 12   |
      | timestart[minute]  | 00   |
      | timefinish[day]    | 1    |
      | timefinish[month]  | 1    |
      | timefinish[year]   | 2025 |
      | timefinish[hour]   | 13   |
      | timefinish[minute] | 00   |
    And I click on "OK" "button" in the "Select date" "totaradialogue"
    And I press "Save changes"
    When I click on "Edit event" "link" in the "0 / 50" "table_row"
    And I click on "Edit facilitator" "link" in the "Etwas 1" "table_row"
    And I set the following fields to these values:
      | Allow booking conflicts | 0 |
    And I click on "OK" "button" in the "Edit facilitator" "totaradialogue"
    Then I should not see "Facilitator has conflicting usage"
    And I press "Save changes"

  Scenario: Reportbuilder seminar facilitator availability filter
    Given I log in as "admin"
    And I am on "Course 1" course homepage
    And I follow "Test Seminar 1"
    And I follow "Add event"
    And I set the following fields to these values:
      | Maximum bookings | 20 |
    And I click on "Edit session" "link"
    And I set the following fields to these values:
      | timestart[day]     | 1    |
      | timestart[month]   | 1    |
      | timestart[year]    | 2025 |
      | timestart[hour]    | 11   |
      | timestart[minute]  | 00   |
      | timefinish[day]    | 1    |
      | timefinish[month]  | 1    |
      | timefinish[year]   | 2025 |
      | timefinish[hour]   | 12   |
      | timefinish[minute] | 00   |
    And I click on "OK" "button" in the "Select date" "totaradialogue"
    And I click on "Select facilitators" "link"
    And I click on "facilitator 1" "text" in the "Choose facilitators" "totaradialogue"
    And I click on "OK" "button" in the "Choose facilitators" "totaradialogue"
    And I press "Save changes"
    And I follow "Add event"
    And I set the following fields to these values:
      | Maximum bookings | 30 |
    And I click on "Edit session" "link"
    And I set the following fields to these values:
      | timestart[day]     | 1    |
      | timestart[month]   | 1    |
      | timestart[year]    | 2025 |
      | timestart[hour]    | 13   |
      | timestart[minute]  | 00   |
      | timefinish[day]    | 1    |
      | timefinish[month]  | 1    |
      | timefinish[year]   | 2025 |
      | timefinish[hour]   | 14   |
      | timefinish[minute] | 00   |
    And I click on "OK" "button" in the "Select date" "totaradialogue"
    And I click on "Select facilitators" "link"
    And I click on "facilitator 1" "text" in the "Choose facilitators" "totaradialogue"
    And I click on "OK" "button" in the "Choose facilitators" "totaradialogue"
    And I press "Save changes"
    And I follow "Add event"
    And I set the following fields to these values:
      | Maximum bookings | 30 |
    And I click on "Edit session" "link"
    And I set the following fields to these values:
      | timestart[day]     | 1    |
      | timestart[month]   | 1    |
      | timestart[year]    | 2025 |
      | timestart[hour]    | 15   |
      | timestart[minute]  | 00   |
      | timefinish[day]    | 1    |
      | timefinish[month]  | 1    |
      | timefinish[year]   | 2025 |
      | timefinish[hour]   | 16   |
      | timefinish[minute] | 00   |
    And I click on "OK" "button" in the "Select date" "totaradialogue"
    And I click on "Select facilitators" "link"
    And I click on "facilitator 2" "text" in the "Choose facilitators" "totaradialogue"
    And I click on "OK" "button" in the "Choose facilitators" "totaradialogue"
    And I press "Save changes"
    And I navigate to "Facilitators" node in "Site administration > Seminars"

    # NOTE: We cannot use "Search" button because there is already "Search by" aria button above.

    When I set the following fields to these values:
      | facilitator-facilitatoravailable_enable        | Free between the following times |
      | facilitator-facilitatoravailable_start[day]    | 1                                |
      | facilitator-facilitatoravailable_start[month]  | January                          |
      | facilitator-facilitatoravailable_start[year]   | 2025                             |
      | facilitator-facilitatoravailable_start[hour]   | 10                               |
      | facilitator-facilitatoravailable_start[minute] | 00                               |
      | facilitator-facilitatoravailable_end[day]      | 1                                |
      | facilitator-facilitatoravailable_end[month]    | January                          |
      | facilitator-facilitatoravailable_end[year]     | 2025                             |
      | facilitator-facilitatoravailable_end[hour]     | 11                               |
      | facilitator-facilitatoravailable_end[minute]   | 00                               |
    And I press "submitgroupstandard[addfilter]"
    Then I should see "facilitator 1"
    And I should see "facilitator 2"
    And I should see "facilitator 3"

    When I set the following fields to these values:
      | facilitator-facilitatoravailable_start[day]    | 1                                |
      | facilitator-facilitatoravailable_start[month]  | January                          |
      | facilitator-facilitatoravailable_start[year]   | 2025                             |
      | facilitator-facilitatoravailable_start[hour]   | 10                               |
      | facilitator-facilitatoravailable_start[minute] | 00                               |
      | facilitator-facilitatoravailable_end[day]      | 1                                |
      | facilitator-facilitatoravailable_end[month]    | January                          |
      | facilitator-facilitatoravailable_end[year]     | 2025                             |
      | facilitator-facilitatoravailable_end[hour]     | 11                               |
      | facilitator-facilitatoravailable_end[minute]   | 01                               |
    And I press "submitgroupstandard[addfilter]"
    Then I should not see "facilitator 1"
    And I should see "facilitator 2"
    And I should see "facilitator 3"

    When I set the following fields to these values:
      | facilitator-facilitatoravailable_start[day]    | 1                                |
      | facilitator-facilitatoravailable_start[month]  | January                          |
      | facilitator-facilitatoravailable_start[year]   | 2025                             |
      | facilitator-facilitatoravailable_start[hour]   | 11                               |
      | facilitator-facilitatoravailable_start[minute] | 30                               |
      | facilitator-facilitatoravailable_end[day]      | 1                                |
      | facilitator-facilitatoravailable_end[month]    | January                          |
      | facilitator-facilitatoravailable_end[year]     | 2025                             |
      | facilitator-facilitatoravailable_end[hour]     | 12                               |
      | facilitator-facilitatoravailable_end[minute]   | 30                               |
    And I press "submitgroupstandard[addfilter]"
    Then I should not see "facilitator 1"
    And I should see "facilitator 2"
    And I should see "facilitator 3"

    When I set the following fields to these values:
      | facilitator-facilitatoravailable_start[day]    | 1                                |
      | facilitator-facilitatoravailable_start[month]  | January                          |
      | facilitator-facilitatoravailable_start[year]   | 2025                             |
      | facilitator-facilitatoravailable_start[hour]   | 12                               |
      | facilitator-facilitatoravailable_start[minute] | 59                               |
      | facilitator-facilitatoravailable_end[day]      | 1                                |
      | facilitator-facilitatoravailable_end[month]    | January                          |
      | facilitator-facilitatoravailable_end[year]     | 2025                             |
      | facilitator-facilitatoravailable_end[hour]     | 14                               |
      | facilitator-facilitatoravailable_end[minute]   | 00                               |
    And I press "submitgroupstandard[addfilter]"
    Then I should not see "facilitator 1"
    And I should see "facilitator 2"
    And I should see "facilitator 3"

    When I set the following fields to these values:
      | facilitator-facilitatoravailable_start[day]    | 1                                |
      | facilitator-facilitatoravailable_start[month]  | January                          |
      | facilitator-facilitatoravailable_start[year]   | 2025                             |
      | facilitator-facilitatoravailable_start[hour]   | 10                               |
      | facilitator-facilitatoravailable_start[minute] | 00                               |
      | facilitator-facilitatoravailable_end[day]      | 1                                |
      | facilitator-facilitatoravailable_end[month]    | January                          |
      | facilitator-facilitatoravailable_end[year]     | 2025                             |
      | facilitator-facilitatoravailable_end[hour]     | 14                               |
      | facilitator-facilitatoravailable_end[minute]   | 00                               |
    And I press "submitgroupstandard[addfilter]"
    Then I should not see "facilitator 1"
    And I should see "facilitator 2"
    And I should see "facilitator 3"

    When I set the following fields to these values:
      | facilitator-facilitatoravailable_start[day]    | 1                                |
      | facilitator-facilitatoravailable_start[month]  | January                          |
      | facilitator-facilitatoravailable_start[year]   | 2025                             |
      | facilitator-facilitatoravailable_start[hour]   | 14                               |
      | facilitator-facilitatoravailable_start[minute] | 00                               |
      | facilitator-facilitatoravailable_end[day]      | 1                                |
      | facilitator-facilitatoravailable_end[month]    | January                          |
      | facilitator-facilitatoravailable_end[year]     | 2025                             |
      | facilitator-facilitatoravailable_end[hour]     | 15                               |
      | facilitator-facilitatoravailable_end[minute]   | 00                               |
    And I press "submitgroupstandard[addfilter]"
    Then I should see "facilitator 1"
    And I should see "facilitator 2"
    And I should see "facilitator 3"

    When I set the following fields to these values:
      | facilitator-facilitatoravailable_start[day]    | 1                                |
      | facilitator-facilitatoravailable_start[month]  | January                          |
      | facilitator-facilitatoravailable_start[year]   | 2001                             |
      | facilitator-facilitatoravailable_start[hour]   | 10                               |
      | facilitator-facilitatoravailable_start[minute] | 00                               |
      | facilitator-facilitatoravailable_end[day]      | 1                                |
      | facilitator-facilitatoravailable_end[month]    | January                          |
      | facilitator-facilitatoravailable_end[year]     | 2030                             |
      | facilitator-facilitatoravailable_end[hour]     | 14                               |
      | facilitator-facilitatoravailable_end[minute]   | 00                               |
    And I press "submitgroupstandard[addfilter]"
    Then I should not see "facilitator 1"
    And I should see "facilitator 2"
    And I should see "facilitator 3"
