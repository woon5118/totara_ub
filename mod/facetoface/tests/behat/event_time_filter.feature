@mod @mod_facetoface @totara @javascript
Feature: Filter session by event time
  In order to see if events are correctly filtered by event time
  As admin
  I need to create sessions with different status

  Background:
    Given I am on a totara site
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "seminars" exist in "mod_facetoface" plugin:
      | name         | intro        | course  | multisignupamount |
      | Test seminar | Test seminar | C1      | 0                 |
    And the following "seminar events" exist in "mod_facetoface" plugin:
      | facetoface   | details |
      | Test seminar | event 1 |
      | Test seminar | event 2 |
      | Test seminar | event 3 |
    And the following "seminar sessions" exist in "mod_facetoface" plugin:
      | eventdetails | start                | finish               |
      | event 2      | 01-Jan-1999 11:00:00 | 01-Jan-1999 12:00:00 |
      | event 3      | 01-Jan-2050 11:00:00 | 01-Jan-2050 12:00:00 |
    And I log in as "admin"

  Scenario: Check filter sessions by event time
    And I am on "Course 1" course homepage
    And I follow "View all events"
    Then I should see "Wait-listed" in the ".upcomingsessionlist tr:nth-child(2)" "css_element"
    And I should see "Upcoming" in the "1 January 2050" "table_row"
    And I should see "Session over" in the "1 January 1999" "table_row"

    When I set the field "Event:" to "Upcoming"
    Then I should see "Wait-listed" in the ".upcomingsessionlist tr:nth-child(2)" "css_element"
    And I should see "Upcoming" in the "1 January 2050" "table_row"
    And I should not see "Session over" in the ".mod_facetoface__sessionlist" "css_element"

    When I set the field "Event:" to "In progress"
    Then I should see "No results" in the ".mod_facetoface__sessionlist--empty" "css_element"
    And ".mod_facetoface__sessionlist" "css_element" should not exist

    When I set the field "Event:" to "Over"
    Then I should see "Session over" in the "1 January 1999" "table_row"
    And I should not see "Wait-listed" in the ".mod_facetoface__sessionlist" "css_element"
    And I should not see "Upcoming" in the ".mod_facetoface__sessionlist" "css_element"

    When I set the field "Event:" to "All"
    Then I should see "Wait-listed" in the ".upcomingsessionlist tr:nth-child(2)" "css_element"
    And I should see "Upcoming" in the "1 January 2050" "table_row"
    And I should see "Session over" in the "1 January 1999" "table_row"

  Scenario: See if cancelled events become past events
    And I am on "Course 1" course homepage
    And I follow "View all events"

    When I click on "Cancel event" "link" in the "Wait-listed" "table_row"
    And I click on "Yes" "button"
    Then I should see "Cancelled" in the ".previoussessionlist tr:nth-child(2) .mod_facetoface__sessionlist__event-status__event" "css_element"
    And I click on "Cancel event" "link" in the "1 January 2050" "table_row"
    And I click on "Yes" "button"
    Then I should see "Cancelled" in the "1 January 2050" "table_row"

    And "Event:" "select" should not exist
    And I should see "No results" in the ".mod_facetoface__sessionlist--empty" "css_element"

    And I should see "Cancelled" in the ".previoussessionlist tr:nth-child(2) .mod_facetoface__sessionlist__event-status__event" "css_element"
    And I should see "Cancelled" in the "1 January 2050" "table_row"
    And I should not see "Upcoming" in the ".mod_facetoface__sessionlist" "css_element"
