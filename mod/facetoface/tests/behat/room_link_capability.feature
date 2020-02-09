@javascript @mod @mod_facetoface @totara
Feature: Check seminar room link setting
  In order to allow users to see the room link
  As an admin
  I need to set the users with different settings

  Background:
    Given I am on a totara site
    And the following "users" exist:
      | username     | firstname   | lastname | email                     |
      | student1     | Student     | One      | student1@example.com      |
      | trainer1     | Trainer     | One      | trainer1@example.com      |
      | facilitator1 | Facilitator | One      | facilitator1@example.com  |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user         | course | role           |
      | student1     | C1     | student        |
      | trainer1     | C1     | editingteacher |
      | facilitator1 | C1     | teacher        |
    And the following "seminars" exist in "mod_facetoface" plugin:
      | name           | course |
      | Test Seminar 1 | C1     |
    And the following "seminar events" exist in "mod_facetoface" plugin:
      | facetoface      | details |
      | Test Seminar 1  | event 1 |
    And the following "seminar sessions" exist in "mod_facetoface" plugin:
      | eventdetails | start           | finish                  |
      | event 1      | now +15 minutes | now +60 minutes         |
    And the following "seminar signups" exist in "mod_facetoface" plugin:
      | user     | eventdetails |
      | student1 | event 1      |

    And I log in as "admin"
    And I navigate to "Rooms" node in "Site administration > Seminars"
    And I press "Add a new room"
    And I set the following fields to these values:
      | Name                         | Room 1          |
      | Room capacity                | 10              |
      | Allow room booking conflicts | 0               |
      | Room link                    | http://example.com?id=12345 |
      | Building                     | Some Building 1 |
      | Address                      | 123 Main Street |
    And I press "Add a room"

    And I am on "Course 1" course homepage
    And I click on "View all events" "link"
    And I click on "Actions" "button" in the "Upcoming" "table_row"
    And I click on "Edit event" "link" in the "Upcoming" "table_row"
    And I click on "Select room" "link"
    And I click on "Room 1" "text" in the "Choose rooms" "totaradialogue"
    And I click on "OK" "button" in the "Choose rooms" "totaradialogue"
    And I press "Save changes"
    And I log out

  Scenario: Testing 15min before session and user is booked
    Given I log in as "student1"
    And I follow "Course 1"
    And I click on "View all events" "link"
    And I should see "Join now" in the "Upcoming" "table_row"
    And "//table[@id='mod_facetoface_upcoming_events_table']/tbody/tr/td[contains(@class, 'mod_facetoface__sessionlist__room')]/ul/li/span/a[contains(@class, 'roomurl')]" "xpath_element" should exist
    And I log out

  Scenario: Testing 15min before session and teacher with mod/facetoface:joinanyvirtualroom capability
    Given I log in as "trainer1"
    And I follow "Course 1"
    And I click on "View all events" "link"
    And I should see "Join now" in the "Upcoming" "table_row"
    And "//table[@id='mod_facetoface_upcoming_events_table']/tbody/tr/td[contains(@class, 'mod_facetoface__sessionlist__room')]/ul/li/span/a[contains(@class, 'roomurl')]" "xpath_element" should exist
    And I log out

    And I log in as "admin"
    And the following "permission overrides" exist:
      | capability                        | permission | role           | contextlevel | reference |
      | mod/facetoface:joinanyvirtualroom | Prohibit   | editingteacher | Course       | C1        |
    And I log out

    And I log in as "trainer1"
    And I am on "Course 1" course homepage
    And I click on "View all events" "link"
    And I should not see "Join now" in the "Upcoming" "table_row"
    And "//table[@id='mod_facetoface_upcoming_events_table']/tbody/tr/td[contains(@class, 'mod_facetoface__sessionlist__room')]/ul/li/span/a[contains(@class, 'roomurl')]" "xpath_element" should not exist
    And I log out

  Scenario: Testing 15min before session and user is facilitator
    Given I log in as "admin"
    And the following "permission overrides" exist:
      | capability                        | permission | role    | contextlevel | reference |
      | mod/facetoface:joinanyvirtualroom | Prohibit   | teacher | Course       | C1        |
    And I log out

    And I log in as "facilitator1"
    And I am on "Course 1" course homepage
    And I click on "View all events" "link"
    And I should not see "Join now" in the "Upcoming" "table_row"
    And "//table[@id='mod_facetoface_upcoming_events_table']/tbody/tr/td[contains(@class, 'mod_facetoface__sessionlist__room')]/ul/li/span/a[contains(@class, 'roomurl')]" "xpath_element" should not exist
    And I log out

    And I log in as "admin"
    And I navigate to "Facilitators" node in "Site administration > Seminars"
    And I press "Add a new facilitator"
    And I set the field "facilitatortype" to "0"
    And I click on "Select user..." "button"
    And I click on "Facilitator One" "text" in the "Choose facilitator" "totaradialogue"
    And I click on "OK" "button" in the "Choose facilitator" "totaradialogue"
    And I set the following fields to these values:
      | Facilitator Name | Facilitator 1 |
    And I press "Add a facilitator"
    And I should see "Facilitator 1"

    And I am on "Course 1" course homepage
    And I click on "View all events" "link"
    And I click on "Actions" "button" in the "Upcoming" "table_row"
    And I click on "Edit event" "link" in the "Upcoming" "table_row"
    When I click on "Select facilitators" "link"
    And I click on "Facilitator 1" "text" in the "Choose facilitators" "totaradialogue"
    And I click on "OK" "button" in the "Choose facilitators" "totaradialogue"
    And I press "Save changes"
    And I log out

    And I log in as "facilitator1"
    And I am on "Course 1" course homepage
    And I click on "View all events" "link"
    And I should see "Join now" in the "Upcoming" "table_row"
    And "//table[@id='mod_facetoface_upcoming_events_table']/tbody/tr/td[contains(@class, 'mod_facetoface__sessionlist__room')]/ul/li/span/a[contains(@class, 'roomurl')]" "xpath_element" should exist
    And I log out
