@mod @mod_facetoface @totara @javascript
Feature: Hide elements on the page based on sessions available
  If there are no sessions for a seminar then some elements should be hidden
  and the filter should also have conditional options

  Background:
    Given I am on a totara site
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And I log in as "admin"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Seminar" to section "1" and I fill the form with:
      | Name                                 | Test seminar in progress |
      | Description                          | Test seminar in progress |
      | How many times the user can sign-up? | Unlimited                |

  Scenario: Check that there is no event time filter and export form when there are no events
    Given I am on "Course 1" course homepage
    And I follow "View all events"
    Then I should see "No events"
    And I should not see "Event time"
    And I should not see "Export attendance"

  Scenario: Adding one event should display the export form and event filter
    Given I am on "Course 1" course homepage
    And I follow "View all events"
    And I follow "Add event"
    And I click on "Edit session" "link"
    And I fill seminar session with relative date in form data:
      | sessiontimezone    | Pacific/Auckland |
      | timestart[day]     | -2               |
      | timestart[month]   | 0                |
      | timestart[year]    | 0                |
      | timestart[hour]    | 0                |
      | timestart[minute]  | 0                |
      | timefinish[day]    | -2               |
      | timefinish[month]  | 0                |
      | timefinish[year]   | 0                |
      | timefinish[hour]   | +1               |
      | timefinish[minute] | 0                |
    And I press "OK"
    And I press "Save changes"
    Then I should see "Event time"
    And I should see "Export attendance"