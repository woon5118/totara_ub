@mod @mod_facetoface @totara
Feature: Facetoface session date management
  In order to set up a session
  As an administrator
  I need to be able to use timezones

  Background:
    Given I am on a totara site
    And the following "users" exist:
      | username | firstname | lastname | email                | timezone        |
      | teacher1 | Terry     | Teacher  | teacher1@example.com | Australia/Perth |
      | teacher2 | Herry     | Tutor    | teacher2@example.com | Europe/Prague   |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | teacher2 | C1     | editingteacher |
    # TODO: add custom room "Room 1"

  @javascript
  Scenario:
    Given I log in as "teacher1"
    And I click on "Find Learning" in the totara menu
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Face-to-face" to section "1" and I fill the form with:
      | Name        | Test facetoface name        |
      | Description | Test facetoface description |
    And I follow "View all events"
    And I follow "Add a new event"
    And I click on "Edit date" "link"
    And the field "sessiontimezone" matches value "User timezone"
    And I set the following fields to these values:
      | sessiontimezone      | Pacific/Auckland |
      | timestart[day]       | 2                |
      | timestart[month]     | 1                |
      | timestart[year]      | 2020             |
      | timestart[hour]      | 3                |
      | timestart[minute]    | 0                |
      | timestart[timezone]  | Europe/Prague    |
      | timefinish[day]      | 2                |
      | timefinish[month]    | 1                |
      | timefinish[year]     | 2020             |
      | timefinish[hour]     | 4                |
      | timefinish[minute]   | 0                |
      | timefinish[timezone] | Europe/Prague    |
    And I press "OK"
    # TODO: Select room "Room 1"
    And I press "Add a new date"
    And I click on "Edit date" "link" in the ".f2fmanagedates .lastrow" "css_element"
    And I set the following fields to these values:
      | sessiontimezone      | User timezone |
      | timestart[day]       | 3             |
      | timestart[month]     | 2             |
      | timestart[year]      | 2021          |
      | timestart[hour]      | 9             |
      | timestart[minute]    | 0             |
      | timestart[timezone]  | Europe/London |
      | timefinish[day]      | 3             |
      | timefinish[month]    | 2             |
      | timefinish[year]     | 2021          |
      | timefinish[hour]     | 11            |
      | timefinish[minute]   | 0             |
      | timefinish[timezone] | Europe/Prague |
    And I press "OK"
    # TODO: Select room "Room 1"
    When I press "Save changes"
    Then I should see "3:00 PM - 4:00 PM Pacific/Auckland" in the "Room 1" "table_row"
    And I should see "5:00 PM - 6:00 PM Australia/Perth" in the "Room 1" "table_row"

    When I click on "Edit" "link" in the "Room 1" "table_row"
    And I click on "Edit date" "link"
    Then the following fields match these values:
      | sessiontimezone      | Pacific/Auckland |
      | timestart[day]       | 2                |
      | timestart[month]     | January          |
      | timestart[year]      | 2020             |
      | timestart[hour]      | 15               |
      | timestart[minute]    | 00               |
      | timestart[timezone]  | Pacific/Auckland |
      | timefinish[day]      | 2                |
      | timefinish[month]    | January          |
      | timefinish[year]     | 2020             |
      | timefinish[hour]     | 16               |
      | timefinish[minute]   | 00               |
      | timefinish[timezone] | Pacific/Auckland |
    And I press "OK"
    And I click on "Edit date" "link" in the ".f2fmanagedates .lastrow" "css_element"
    Then the following fields match these values:
      | sessiontimezone      | User timezone    |
      | timestart[day]       | 3                |
      | timestart[month]     | February         |
      | timestart[year]      | 2021             |
      | timestart[hour]      | 17               |
      | timestart[minute]    | 00               |
      | timestart[timezone]  | Australia/Perth  |
      | timefinish[day]      | 3                |
      | timefinish[month]    | February         |
      | timefinish[year]     | 2021             |
      | timefinish[hour]     | 18               |
      | timefinish[minute]   | 00               |
      | timefinish[timezone] | Australia/Perth  |
    And I press "OK"
    When I press "Add a new date"
    And I click on "Edit date" "link" in the ".f2fmanagedates .lastrow" "css_element"
    Then the following fields match these values:
      | sessiontimezone      | Pacific/Auckland |
      | timestart[timezone]  | Pacific/Auckland |
      | timefinish[timezone] | Pacific/Auckland |

    And I set the following fields to these values:
      | timestart[day]       | 4             |
      | timestart[month]     | 3             |
      | timestart[year]      | 2022          |
      | timestart[hour]      | 1             |
      | timestart[minute]    | 00            |
      | timefinish[day]      | 4             |
      | timefinish[month]    | 3             |
      | timefinish[year]     | 2022          |
      | timefinish[hour]     | 2             |
      | timefinish[minute]   | 00            |
      | sessiontimezone      | Europe/Prague |
    And I press "OK"

    When I press "Save changes"
    Then I should see "3:00 AM - 4:00 AM Europe/Prague" in the "Room 1" "table_row"
    And I should see "5:00 PM - 6:00 PM Australia/Perth" in the "Room 1" "table_row"
    And I should see "1:00 AM - 2:00 AM Pacific/Auckland" in the "Room 1" "table_row"

    When I log out
    And I log in as "teacher2"
    And I click on "Find Learning" in the totara menu
    And I follow "Course 1"
    And I follow "Test facetoface name"
    Then I should see "3:00 AM - 4:00 AM Europe/Prague" in the "Room 1" "table_row"
    And I should see "10:00 AM - 11:00 AM Europe/Prague" in the "Room 1" "table_row"
    And I should see "1:00 AM - 2:00 AM Pacific/Auckland" in the "Room 1" "table_row"

    When I log out
    And I log in as "admin"
    And I set the following administration settings values:
      | facetoface_displaysessiontimezones | 0 |
    And I log out
    And I log in as "teacher1"
    And I click on "Find Learning" in the totara menu
    And I follow "Course 1"
    And I follow "Test facetoface name"
    Then I should see "10:00 AM - 11:00 AM " in the "Room 1" "table_row"
    And I should see "5:00 PM - 6:00 PM " in the "Room 1" "table_row"
    And I should see "8:00 PM - 9:00 PM" in the "Room 1" "table_row"
