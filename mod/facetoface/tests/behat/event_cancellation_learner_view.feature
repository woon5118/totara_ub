@mod @mod_facetoface @totara @javascript
Feature: Seminar event cancellation learner views
  After seminar events have been cancelled
  As a learner
  I need to see cancellation summaries

  Background:
    Given I am on a totara site
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | One      | teacher1@example.com |
      | learner1 | Learner   | One      | learner1@example.com |
      | learner2 | Learner   | Two      | learner2@example.com |
      | learner3 | Learner   | Three    | learner2@example.com |
      | learner4 | Learner   | Four     | learner2@example.com |

    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |

    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | learner1 | C1     | student        |
      | learner2 | C1     | student        |
      | learner3 | C1     | student        |
      | learner4 | C1     | student        |

    Given I log in as "admin"
    And I navigate to "Global settings" node in "Site administration > Seminars"
    And I click on "Editing Trainer" "text" in the "#admin-facetoface_session_roles" "css_element"
    And I click on "Editing Trainer" "text" in the "#admin-facetoface_session_rolesnotify" "css_element"
    And I press "Save changes"
    And I log out

    Given I log in as "teacher1"
    And I click on "Find Learning" in the totara menu
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Seminar" to section "1" and I fill the form with:
      | Name        | Test Seminar |
      | Description | Test Seminar |
    And I follow "View all events"

    Given I follow "Add a new event"
    And I follow "show-selectdate0-dialog"
    And I set the following fields to these values:
      | sessiontimezone     | Pacific/Auckland |
      | timestart[day]      | 10               |
      | timestart[month]    | 2                |
      | timestart[year]     | 2025             |
      | timestart[hour]     | 9                |
      | timestart[minute]   | 0                |
      | timestart[timezone] | Pacific/Auckland |
      | timefinish[day]     | 10               |
      | timefinish[month]   | 2                |
      | timefinish[year]    | 2025             |
      | timefinish[hour]    | 15               |
      | timefinish[minute]  | 0                |
      | timefinish[timezone]| Pacific/Auckland |
    And I press "OK"
    And I click on "Teacher One" "checkbox"
    And I press "Save changes"

    Given I follow "Add a new event"
    And I follow "show-selectdate0-dialog"
    And I set the following fields to these values:
      | sessiontimezone     | Pacific/Auckland |
      | timestart[day]      | 10               |
      | timestart[month]    | 2                |
      | timestart[year]     | 2026             |
      | timestart[hour]     | 10               |
      | timestart[minute]   | 0                |
      | timestart[timezone] | Pacific/Auckland |
      | timefinish[day]     | 10               |
      | timefinish[month]   | 2                |
      | timefinish[year]    | 2026             |
      | timefinish[hour]    | 16               |
      | timefinish[minute]  | 0                |
      | timefinish[timezone]| Pacific/Auckland |
    And I press "OK"
    And I click on "Teacher One" "checkbox"
    And I press "Save changes"

    Given I follow "Add a new event"
    And I set the following fields to these values:
      | Maximum bookings | 29 |
    And I follow "show-selectdate0-dialog"
    And I fill seminar session with relative date in form data:
      | sessiontimezone     | Pacific/Auckland |
      | timestart[day]      | 0                |
      | timestart[month]    | 0                |
      | timestart[year]     | 0                |
      | timestart[hour]     | 0                |
      | timestart[minute]   | 1                |
      | timestart[timezone] | Pacific/Auckland |
      | timefinish[day]     | 0                |
      | timefinish[month]   | 0                |
      | timefinish[year]    | 0                |
      | timefinish[hour]    | 2                |
      | timefinish[minute]  | 0                |
      | timefinish[timezone]| Pacific/Auckland |
    And I press "OK"
    And I click on "Teacher One" "checkbox"
    And I press "Save changes"

    Given I follow "Add a new event"
    And I set the following fields to these values:
      | Maximum bookings | 35 |
    And I follow "show-selectdate0-dialog"
    And I fill seminar session with relative date in form data:
      | sessiontimezone     | Pacific/Auckland |
      | timestart[day]      | 0                |
      | timestart[month]    | 1                |
      | timestart[year]     | 0                |
      | timestart[hour]     | 0                |
      | timestart[minute]   | 0                |
      | timestart[timezone] | Pacific/Auckland |
      | timefinish[day]     | 0                |
      | timefinish[month]   | 1                |
      | timefinish[year]    | 0                |
      | timefinish[hour]    | 2                |
      | timefinish[minute]  | 0                |
      | timefinish[timezone]| Pacific/Auckland |
    And I press "OK"
    And I click on "Teacher One" "checkbox"
    And I press "Save changes"

  # ----------------------------------------------------------------------------
  Scenario: mod_facetoface_cancel_300: cancelled booking (course view).
    Given I click on "Attendees" "link" in the "10 February 2025" "table_row"
    And I click on "Add users" "option" in the "#menuf2f-actions" "css_element"
    And I click on "Learner One, learner1@example.com" "option"
    And I press "Add"
    And I wait "1" seconds
    And I click on "Learner Two, learner2@example.com" "option"
    And I press "Add"
    And I wait "1" seconds
    And I press "Continue"
    And I press "Confirm"
    And I follow "Go back"

    Given I log out
    And I log in as "admin"
    And I click on "Find Learning" in the totara menu
    And I follow "Course 1"
    And I follow "View all events"
    And I click on "Cancel event" "link" in the "10 February 2025" "table_row"
    And I press "Yes"

    When I log out
    And I log in as "learner1"
    And I click on "Find Learning" in the totara menu
    And I follow "Course 1"
    And I follow "View all events"
    Then I should see "9:00 AM - 3:00 PM Pacific/Auckland" in the "10 February 2025" "table_row"
    And I should see "Event cancelled" in the "10 February 2025" "table_row"
    And I should see "Sign-up unavailable" in the "10 February 2025" "table_row"
    And I should see "10:00 AM - 4:00 PM Pacific/Auckland" in the "10 February 2026" "table_row"
    And I should see "Booking open" in the "10 February 2026" "table_row"
    And I should see "Sign-up" in the "10 February 2026" "table_row"


  # ----------------------------------------------------------------------------
  Scenario: mod_facetoface_cancel_301: cancelled booking (future bookings view).
    Given I click on "Attendees" "link" in the "10 February 2025" "table_row"
    And I click on "Add users" "option" in the "#menuf2f-actions" "css_element"
    And I click on "Learner One, learner1@example.com" "option"
    And I press "Add"
    And I wait "1" seconds
    And I click on "Learner Two, learner2@example.com" "option"
    And I press "Add"
    And I wait "1" seconds
    And I press "Continue"
    And I press "Confirm"
    And I follow "Go back"

    Given I log out
    And I log in as "admin"
    And I click on "Find Learning" in the totara menu
    And I follow "Course 1"
    And I follow "View all events"
    And I click on "Cancel event" "link" in the "10 February 2025" "table_row"
    And I press "Yes"

    When I log out
    And I log in as "learner1"
    And I click on "My Bookings" in the totara menu
    Then I should see "Course 1" in the "Test Seminar" "table_row"
    And I should see "10 February 2025" in the "Test Seminar" "table_row"
    And I should see "9:00 AM Pacific/Auckland" in the "Test Seminar" "table_row"
    And I should see "3:00 PM Pacific/Auckland" in the "Test Seminar" "table_row"
    And I should see "Event Cancelled" in the "Test Seminar" "table_row"


  # ----------------------------------------------------------------------------
  Scenario: mod_facetoface_cancel_302: cancelled booking (past bookings view).
    # --------------------------------------------------------------------------
    # THIS IS A LONG RUNNING SCENARIO: it needs to wait until the final event
    # created 5 mins in the future becomes in the past. Can't set a future time
    # to < 5 mins since the UI only accepts minutes in multiples of 5 mins only!
    # --------------------------------------------------------------------------
    Given I click on "Attendees" "link" in the "0 / 29" "table_row"
    And I click on "Add users" "option" in the "#menuf2f-actions" "css_element"
    And I click on "Learner One, learner1@example.com" "option"
    And I press "Add"
    And I wait "1" seconds
    And I click on "Learner Two, learner2@example.com" "option"
    And I press "Add"
    And I wait "1" seconds
    And I click on "Learner Three, learner2@example.com" "option"
    And I press "Add"
    And I wait "1" seconds
    And I click on "Learner Four, learner2@example.com" "option"
    And I press "Add"
    And I wait "1" seconds
    And I press "Continue"
    And I press "Confirm"
    And I follow "Go back"
    And I click on "Cancel event" "link" in the "4 / 29" "table_row"
    And I press "Yes"

    When I log out
    And I wait "360" seconds
    And I log in as "learner1"
    And I click on "My Bookings" in the totara menu
    And I click on "Past Bookings" "link"
    Then I should see "Course 1" in the "Test Seminar" "table_row"
    Then I should see date "0 day" formatted "%d %B %Y"
    And I should see "Event Cancelled" in the "Test Seminar" "table_row"



  # ----------------------------------------------------------------------------
  Scenario: mod_facetoface_cancel_303: remove cancelled sessions from learner views.
    Given I click on "Attendees" "link" in the "10 February 2025" "table_row"
    And I click on "Add users" "option" in the "#menuf2f-actions" "css_element"
    And I click on "Learner One, learner1@example.com" "option"
    And I press "Add"
    And I wait "1" seconds
    And I click on "Learner Two, learner2@example.com" "option"
    And I press "Add"
    And I wait "1" seconds
    And I press "Continue"
    And I press "Confirm"
    And I follow "Go back"

    Given I log out
    And I log in as "admin"
    And I click on "Find Learning" in the totara menu
    And I follow "Course 1"
    And I follow "View all events"
    And I click on "Cancel event" "link" in the "10 February 2025" "table_row"
    And I press "Yes"

    When I log out
    And I log in as "learner1"
    And I click on "My Bookings" in the totara menu

    # --------------------------------------------------------------------------
    # THIS PART WILL FAIL WITH THE CURRENT SEMINAR CANCELLATION CODE. This is
    # because there does not seem to be a way to enable this mechanism, as per
    # v1.6 specs (background section, 2nd para, #7) which says there should be a
    # way.
    # --------------------------------------------------------------------------
    Given I skip the scenario until issue "TL-9482" lands

    Then I should not see "Course 1" in the "Test Seminar" "table_row"
    And I should not see "10 February 2025" in the "Test Seminar" "table_row"
    And I should not see "9:00 AM Pacific/Auckland" in the "Test Seminar" "table_row"
    And I should not see "3:00 PM Pacific/Auckland" in the "Test Seminar" "table_row"
    And I should not see "Event Cancelled" in the "Test Seminar" "table_row"
