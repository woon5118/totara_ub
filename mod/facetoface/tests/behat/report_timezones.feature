@mod @mod_facetoface @totara
Feature: Facetoface timezones in reports
  In order to no confuse users with timezones
  As an administrator
  I need to be able to disable face-to-face timezones in report

  @javascript
  Scenario: Test timezones in facetoface sessions report
    Given I am on a totara site
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | user1    | First     | User     | user1@example.com    |
      | user2    | Second    | User     | user2@example.com    |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user  | course | role    |
      | user1 | C1     | student |
      | user2 | C1     | student |

    And I log in as "admin"
    And I click on "Find Learning" in the totara menu
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Face-to-face" to section "1" and I fill the form with:
      | Name        | Test facetoface name        |
      | Description | Test facetoface description |
    And I follow "Test facetoface name"
    And I follow "Add a new event"
    And I click on "Edit date" "link"
    And I set the following fields to these values:
      | sessiontimezone      | Europe/Prague   |
      | timestart[day]       | 2               |
      | timestart[month]     | 1               |
      | timestart[year]      | 2020            |
      | timestart[hour]      | 1               |
      | timestart[minute]    | 15              |
      | timestart[timezone]  | Australia/Perth |
      | timefinish[day]      | 2               |
      | timefinish[month]    | 1               |
      | timefinish[year]     | 2020            |
      | timefinish[hour]     | 3               |
      | timefinish[minute]   | 45              |
      | timefinish[timezone] | Australia/Perth |
    And I press "OK"
    # TODO create custom room names "Room 1"
    And I press "Save changes"
    And I follow "Add a new event"
    And I click on "Edit date" "link"
    And I set the following fields to these values:
      | sessiontimezone      | User timezone   |
      | timestart[day]       | 4               |
      | timestart[month]     | 2               |
      | timestart[year]      | 2021            |
      | timestart[hour]      | 1               |
      | timestart[minute]    | 0               |
      | timestart[timezone]  | Australia/Perth |
      | timefinish[day]      | 4               |
      | timefinish[month]    | 2               |
      | timefinish[year]     | 2021            |
      | timefinish[hour]     | 2               |
      | timefinish[minute]   | 30              |
      | timefinish[timezone] | Australia/Perth |
    And I press "OK"
    # TODO create custom room names "Room 2"
    And I press "Save changes"
    And I should see "6:15 PM - 8:45 PM Europe/Prague" in the "Room 1" "table_row"
    And I should see "1 January 2020" in the "Room 1" "table_row"
    And I should see "1:00 AM - 2:30 AM Australia/Perth" in the "Room 2" "table_row"
    And I should see "4 February 2021" in the "Room 2" "table_row"
    And I click on "Attendees" "link" in the "Room 1" "table_row"
    And I click on "Add users" "option" in the "#menuf2f-actions" "css_element"
    And I click on "First User, user1@example.com" "option"
    And I press "Add"
    And I press "Continue"
    And I press "Confirm"
    And I wait until "First User" "text" exists
    And I click on "Go back" "link"
    And I click on "Attendees" "link" in the "Room 2" "table_row"
    And I click on "Add users" "option" in the "#menuf2f-actions" "css_element"
    And I click on "Second User, user2@example.com" "option"
    And I press "Add"
    And I press "Continue"
    And I press "Confirm"
    And I wait until "Second User" "text" exists

    And I navigate to "Manage reports" node in "Site administration > Reports > Report builder"
    And I set the field "Report Name" to "F2F sessions"
    And I set the field "Source" to "Face-to-face events"
    And I press "Create report"
    And I click on "Columns" "link" in the ".tabtree" "css_element"
    And I add the "Event Finish Time" column to the report
    And I add the "Event Start Time" column to the report
    #And I add the "Event Finish Time" column to the report
    And I add the "Session Start (linked to activity)" column to the report

    When I navigate to my "F2F sessions" report
    Then I should see "1 January 2020" in the "First User" "table_row"
    And I should see "6:15 PM Europe/Prague" in the "First User" "table_row"
    And I should see "8:45 PM Europe/Prague" in the "First User" "table_row"
    And I should see "4 February 2021" in the "Second User" "table_row"
    And I should see "1:00 AM Australia/Perth" in the "Second User" "table_row"
    And I should see "2:30 AM Australia/Perth" in the "Second User" "table_row"
    And I should not see "2 January 2020"

    When I am on homepage
    And I set the following administration settings values:
      | facetoface_displaysessiontimezones | 0 |
    And I navigate to my "F2F sessions" report
    Then I should see "2 January 2020" in the "First User" "table_row"
    And I should see "01:15" in the "First User" "table_row"
    And I should see "03:45" in the "First User" "table_row"
    And I should see "4 February 2021" in the "Second User" "table_row"
    And I should see "01:00" in the "Second User" "table_row"
    And I should see "02:30" in the "Second User" "table_row"
    And I should not see "Prague"
    And I should not see "Perth"
    And I should not see "1 January 2020"

  @javascript
  Scenario: Test timezones in facetoface summary report
    Given I am on a totara site
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |

    And I log in as "admin"
    And I click on "Find Learning" in the totara menu
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Face-to-face" to section "1" and I fill the form with:
      | Name        | Test facetoface 1 name        |
      | Description | Test facetoface 1 description |
    And I follow "Test facetoface 1 name"
    And I follow "Add a new event"
    And I click on "Edit date" "link"
    And I set the following fields to these values:
      | sessiontimezone      | Europe/Prague   |
      | timestart[day]       | 2               |
      | timestart[month]     | 1               |
      | timestart[year]      | 2020            |
      | timestart[hour]      | 1               |
      | timestart[minute]    | 15              |
      | timestart[timezone]  | Australia/Perth |
      | timefinish[day]      | 2               |
      | timefinish[month]    | 1               |
      | timefinish[year]     | 2020            |
      | timefinish[hour]     | 3               |
      | timefinish[minute]   | 45              |
      | timefinish[timezone] | Australia/Perth |
    And I press "OK"
    # TODO create custom room names "Room 1"
    And I press "Save changes"
    And I click on "Find Learning" in the totara menu
    And I follow "Course 1"
    And I add a "Face-to-face" to section "1" and I fill the form with:
      | Name        | Test facetoface 2 name        |
      | Description | Test facetoface 2 description |
    And I follow "Test facetoface 2 name"
    And I follow "Add a new event"
    And I click on "Edit date" "link"
    And I set the following fields to these values:
      | sessiontimezone      | User timezone   |
      | timestart[day]       | 4               |
      | timestart[month]     | 2               |
      | timestart[year]      | 2021            |
      | timestart[hour]      | 1               |
      | timestart[minute]    | 0               |
      | timestart[timezone]  | Australia/Perth |
      | timefinish[day]      | 4               |
      | timefinish[month]    | 2               |
      | timefinish[year]     | 2021            |
      | timefinish[hour]     | 2               |
      | timefinish[minute]   | 30              |
      | timefinish[timezone] | Australia/Perth |
    And I press "OK"
    # TODO create custom room names "Room 1"
    And I press "Save changes"

    And I navigate to "Manage reports" node in "Site administration > Reports > Report builder"
    And I set the field "Report Name" to "F2F summary"
    And I set the field "Source" to "Face-to-face Summary"
    And I press "Create report"
    And I click on "Columns" "link" in the ".tabtree" "css_element"
    And I add the "Session Start" column to the report
    And I add the "Session Start Date/Time (linked to attendees page)" column to the report

    When I navigate to my "F2F summary" report
    Then I should see "1 January 2020" in the "Test facetoface 1 name" "table_row"
    And I should see "4 February 2021" in the "Test facetoface 2 name" "table_row"
    And I should see "Europe/Prague" in the "Test facetoface 1 name" "table_row"
    And I should see "Australia/Perth" in the "Test facetoface 2 name" "table_row"
    And I should not see "2 January 2020"

    When I am on homepage
    And I set the following administration settings values:
      | facetoface_displaysessiontimezones | 0 |
    And I navigate to my "F2F summary" report
    Then I should see "2 January 2020" in the "Test facetoface 1 name" "table_row"
    And I should see "4 February 2021" in the "Test facetoface 2 name" "table_row"
    And I should not see "Prague"
    And I should not see "Perth"
    And I should not see "1 January 2020"
