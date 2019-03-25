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
    And the following "course enrolments" exist:
      | user      | course  | role    |
      | kianbomba | course1 | student |
      | bolobala  | course1 | student |
      | kian      | course1 | student |
    And the following "seminars" exist in "mod_facetoface" plugin:
      | name      | course  | sessionattendance |
      | seminar 1 | course1 | 1                 |
    And the following "seminar events" exist in "mod_facetoface" plugin:
      | facetoface | details |
      | seminar 1  | event 1 |
    And the following "seminar sessions" exist in "mod_facetoface" plugin:
      | eventdetails | start                   | finish                  |
      | event 1      | now -2 days -30 minutes | now -2 days +30 minutes |
      | event 1      | now +2 days             | now +2 days +60 minutes |
    And the following "seminar signups" exist in "mod_facetoface" plugin:
      | user      | eventdetails |
      | kianbomba | event 1      |
      | bolobala  | event 1      |
      | kian      | event 1      |
    And I log in as "admin"

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
