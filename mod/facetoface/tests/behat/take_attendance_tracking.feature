@mod @mod_facetoface @javascript
Feature: Take attendance tracking general
  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | course1  | course1   | 0        |
    And the following "users" exist:
      | username  | firstname | lastname | email         | idnumber |
      | kianbomba | kian      | bomba    | k@example.com | bomba    |
      | bolobala  | bolo      | bala     | b@example.com | bolo     |
      | kian      | loc       | nguyen   | l@example.com | loc      |
    And I log in as "admin"
    And I am on "course1" course homepage with editing mode on
    And I add a "Seminar" to section "1" and I fill the form with:
      | Name                        | seminar 1 |
      | Session attendance tracking | 1         |
    And I turn editing mode off
    And I follow "seminar 1"
    And I follow "Add event"
    And I click on "Add a new session" "button"
    And I click on "Edit session" "link"
    And I fill seminar session with relative date in form data:
      | timestart[day]     | -2  |
      | timestart[hour]    | -30 |
      | timefinish[minute] | +30 |
      | timefinish[day]    | -2  |
    And I click on "OK" "button" in the "Select date" "totaradialogue"

    # There is something wrong with the totara dialog box within behat tests here, as the button
    # was not found for second dialog box when pop up. Therefore, we reload the page here
    And I click on "Save changes" "button"
    And I click on "Edit event" "link"

    # Second date selector here, as clicking on the link in row {number} does not work well
    And I click on "#show-selectdate1-dialog" "css_element"
    And I fill seminar session with relative date in form data:
      | timestart[day]     | +2               |
      | timefinish[minute] | +60              |
      | timefinish[day]    | +2               |
    And I click on "OK" "button" in the "Select date" "totaradialogue"

    And I click on "Save changes" "button"
    And I follow "Attendees"
    And I set the field "Attendee actions" to "add"
    And I set the field "potential users" to "bolo bala, b@example.com"
    And I click on "Add" "button"
    And I set the field "potential users" to "kian bomba, k@example.com"
    And I click on "Add" "button"
    And I set the field "potential users" to "loc nguyen, l@example.com"
    And I click on "Add" "button"
    And I click on "Continue" "button"
    And I click on "Confirm" "button"

  # Expect to not able to take attendance here, because all the sessions are not finished yet,
  # and the mark attendance time is set to end time
  Scenario: Take attendance tracking for event when mark attendance tracking is set for end time
    Given I am on "course1" course homepage
    And I follow "seminar 1"
    And I follow "Attendee"
    When I follow "Take attendance"
    Then I should see "2 session(s) (1 upcoming; 1 over)"
    And the ".bulkactions" "css_element" should be disabled
    And the ".mod_facetoface__take-attendance__status-picker" "css_element" should be disabled
    And I should see "2" in the "bolo bala" "table_row"
    And I should see "2" in the "kian bomba" "table_row"
    And I should see "2" in the "loc nguyen" "table_row"

  Scenario: Take attendance tracking for session when mark attendance tracking is set for end time
    Given I am on "course1" course homepage
    And I follow "seminar 1"
    And I follow "Attendee"
    And I follow "Take attendance"
    And I set the field "Take attendance:" to "1"
    And I click on "Partially attended" "option" in the "bolo bala" "table_row"
    When I click on "Save attendance" "button"
    Then I should see "Partially attended" in the "bolo bala" "table_row"
    And I should see "Not set" in the "kian bomba" "table_row"
    And I should see "Not set" in the "loc nguyen" "table_row"
    When I set the field "Take attendance:" to "0"
    Then I should see "1" in the "bolo bala" "table_row"
    And I should see "2" in the "kian bomba" "table_row"
    And I should see "2" in the "loc nguyen" "table_row"
    And I should see "Not set" in the "kian bomba" "table_row"
    And I should see "Not set" in the "bolo bala" "table_row"
    And I should see "Not set" in the "loc nguyen" "table_row"

  Scenario: Take attendance tracking for event when mark attendance tracking is set for any time
    Given I am on "course1" course homepage
    And I follow "seminar 1"
    And I follow "Edit settings"
    And I set the field "Mark attendance at" to "2"
    And I click on "Save and display" "button"
    And I follow "Attendee"
    And I follow "Take attendance"
    And I click on "Partially attended" "option" in the "bolo bala" "table_row"
    When I click on "Save attendance" "button"
    Then I should see "Partially attended" in the "bolo bala" "table_row"
    And I should see "Not set" in the "kian bomba" "table_row"
    And I should see "Not set" in the "loc nguyen" "table_row"

  Scenario: Take attendance tracking for event, when session attendance is not enabled
    Given I am on "course1" course homepage
    And I follow "seminar 1"
    And I follow "Edit settings"
    And I set the following fields to these values:
      | Mark attendance at          | 2 |
      | Session attendance tracking | 0 |
    And I click on "Save and display" "button"
    And I follow "Attendee"
    When I follow "Take attendance"
    Then I should not see "2 session(s) (1 upcoming; 1 over)"
    And "Take attendance" "field" should not exist

    And I should not see "2" in the "bolo bala" "table_row"
    And I should not see "2" in the "kian bomba" "table_row"
    And I should not see "2" in the "loc nguyen" "table_row"

    And I click on "All" "option"
    And I click on "Fully attended" "option" in the "#menubulkattendanceop" "css_element"
    When I click on "Save attendance" "button"
    Then I should see "Fully attended" in the "bolo bala" "table_row"
    And I should see "Fully attended" in the "kian bomba" "table_row"
    And I should see "Fully attended" in the "loc nguyen" "table_row"
