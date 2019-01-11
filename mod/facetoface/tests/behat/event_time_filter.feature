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
    And I log in as "admin"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Seminar" to section "1" and I fill the form with:
      | Name                                 | Test seminar |
      | Description                          | Test seminar |
      | How many times the user can sign-up? | Unlimited    |
    And I follow "View all events"
    And I follow "Add event"
    And I click on "Delete" "link"
    And I press "Save changes"

    And I follow "Add event"
    And I click on "Edit session" "link"
    And I set the following fields to these values:
      | timestart[day]     | 1    |
      | timestart[month]   | 1    |
      | timestart[year]    | 1999 |
      | timestart[hour]    | 11   |
      | timestart[minute]  | 00   |
      | timefinish[day]    | 1    |
      | timefinish[month]  | 1    |
      | timefinish[year]   | 1999 |
      | timefinish[hour]   | 12   |
      | timefinish[minute] | 00   |
    And I press "OK"
    And I press "Save changes"

    And I follow "Add event"
    And I click on "Edit session" "link"
    And I set the following fields to these values:
      | timestart[day]     | 1    |
      | timestart[month]   | 1    |
      | timestart[year]    | 2050 |
      | timestart[hour]    | 11   |
      | timestart[minute]  | 00   |
      | timefinish[day]    | 1    |
      | timefinish[month]  | 1    |
      | timefinish[year]   | 2050 |
      | timefinish[hour]   | 12   |
      | timefinish[minute] | 00   |
    And I press "OK"
    And I press "Save changes"

  Scenario: Check filter sessions by event time
    And I am on "Course 1" course homepage
    And I follow "View all events"
    Then I should see "Wait-listed" in the ".mod_facetoface__sessionlist" "css_element"
    And I should see "Upcoming" in the "1 January 2050" "table_row"
    And I should see "Session over" in the "1 January 1999" "table_row"

    When I click on "Upcoming events" "option"
    Then I should see "Wait-listed" in the ".mod_facetoface__sessionlist" "css_element"
    And I should see "Upcoming" in the "1 January 2050" "table_row"
    And I should not see "Session over" in the ".mod_facetoface__sessionlist" "css_element"

    When I click on "Events in progress" "option"
    Then I should see "No events" in the ".mod_facetoface__sessionlist--empty" "css_element"
    And ".mod_facetoface__sessionlist" "css_element" should not exist

    When I click on "Past events" "option"
    Then I should see "Session over" in the "1 January 1999" "table_row"
    And I should not see "Wait-listed" in the ".mod_facetoface__sessionlist" "css_element"
    And I should not see "Upcoming" in the ".mod_facetoface__sessionlist" "css_element"

    When I click on "All events" "option"
    Then I should see "Wait-listed" in the ".mod_facetoface__sessionlist" "css_element"
    And I should see "Upcoming" in the "1 January 2050" "table_row"
    And I should see "Session over" in the "1 January 1999" "table_row"

  Scenario: See if cancelled events become past events
    And I am on "Course 1" course homepage
    And I follow "View all events"

    When I click on "Cancel event" "link" in the "Wait-listed" "table_row"
    And I click on "Yes" "button"
    Then I should see "Cancelled" in the "Event cancelled" "table_row"
    And I click on "Cancel event" "link" in the "1 January 2050" "table_row"
    And I click on "Yes" "button"
    Then I should see "Cancelled" in the "1 January 2050" "table_row"

    When I click on "Upcoming events" "option"
    Then I should see "No events" in the ".mod_facetoface__sessionlist--empty" "css_element"
    And ".mod_facetoface__sessionlist" "css_element" should not exist

    When I click on "Events in progress" "option"
    Then I should see "No events" in the ".mod_facetoface__sessionlist--empty" "css_element"
    And ".mod_facetoface__sessionlist" "css_element" should not exist

    When I click on "Past events" "option"
    Then I should see "Cancelled" in the "Event cancelled" "table_row"
    And I should see "Cancelled" in the "Wait-listed" "table_row"
    And I should see "Cancelled" in the "1 January 2050" "table_row"
    And I should not see "Upcoming" in the ".mod_facetoface__sessionlist" "css_element"
