@javascript @mod @mod_facetoface @totara
Feature: Return to previous page after actions in Face-to-face
  In order to use Face-to-face activity comfortably
  As a user
  I need to be automatically returned back to course page or sessions page after action

  Background:
    Given I am on a totara site
    And the following "users" exist:
      | username | firstname | lastname | email               |
      | teacher1 | Terry1    | Teacher1 | teacher1@example.com |
      | student1 | Sam1      | Student1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And I log in as "teacher1"
    And I click on "Find Learning" in the totara menu
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Face-to-face" to section "1" and I fill the form with:
      | Name        | Test facetoface name        |
      | Description | Test facetoface description |
    And I click on "Find Learning" in the totara menu
    And I follow "Course 1"
    And I follow "View all events"
    And I follow "Add a new event"
    And I click on "Save changes" "button"

  Scenario: Course page - Face-to-face edit session actions return to original page
    Given I click on "Find Learning" in the totara menu
    And I follow "Course 1"
    When I click on "Edit" "link" in the "Booking open" "table_row"
    And I click on "Cancel" "button"
    Then I should see "View all events"
    And I should not see "All events in"

    Given I click on "Find Learning" in the totara menu
    And I follow "Course 1"
    When I click on "Edit" "link" in the "Booking open" "table_row"
    And I click on "Save changes" "button"
    Then I should see "View all events"
    And I should not see "All events in"

  Scenario: Sessions page - Face-to-face edit session actions return to original page
    Given I click on "Find Learning" in the totara menu
    And I follow "Course 1"
    And I follow "View all events"
    When I click on "Edit" "link" in the "Booking open" "table_row"
    And I click on "Cancel" "button"
    Then I should see "All events in"
    And I should not see "View all events"

    Given I click on "Find Learning" in the totara menu
    And I follow "Course 1"
    And I follow "View all events"
    When I click on "Edit" "link" in the "Booking open" "table_row"
    And I click on "Save changes" "button"
    Then I should see "All events in"
    And I should not see "View all events"

  Scenario: Course page - Face-to-face cancel session actions return to original page
    Given I click on "Find Learning" in the totara menu
    And I follow "Course 1"
    When I click on "Cancel event" "link" in the "Booking open" "table_row"
    And I click on "No" "button"
    Then I should see "View all events"
    And I should not see "All events in"

    Given I click on "Find Learning" in the totara menu
    And I follow "Course 1"
    When I click on "Cancel event" "link" in the "Booking open" "table_row"
    And I click on "Yes" "button"
    Then I should see "View all events"
    And I should not see "All events in"
    And I should see "Session cancelled"

  Scenario: Sessions page - Face-to-face cancel session actions return to original page
    Given I click on "Find Learning" in the totara menu
    And I follow "Course 1"
    And I follow "View all events"
    When I click on "Cancel event" "link" in the "Booking open" "table_row"
    And I click on "No" "button"
    Then I should see "All events in"
    And I should not see "View all events"

    Given I click on "Find Learning" in the totara menu
    And I follow "Course 1"
    And I follow "View all events"
    When I click on "Cancel event" "link" in the "Booking open" "table_row"
    And I click on "Yes" "button"
    Then I should see "All events in"
    And I should not see "View all events"
    And I should see "Session cancelled"

  Scenario: Course page - Face-to-face clone session actions return to original page
    Given I click on "Find Learning" in the totara menu
    And I follow "Course 1"
    When I click on "Copy" "link" in the "Booking open" "table_row"
    And I click on "Cancel" "button"
    Then I should see "View all events"
    And I should not see "All events in"

    Given I click on "Find Learning" in the totara menu
    And I follow "Course 1"
    When I click on "Copy" "link" in the "Booking open" "table_row"
    And I click on "Save changes" "button"
    Then I should see "View all events"
    And I should not see "All events in"

  Scenario: Sessions page - Face-to-face clone session actions return to original page
    Given I click on "Find Learning" in the totara menu
    And I follow "Course 1"
    And I follow "View all events"
    When I click on "Copy" "link" in the "Booking open" "table_row"
    And I click on "Cancel" "button"
    Then I should see "All events in"
    And I should not see "View all events"

    Given I click on "Find Learning" in the totara menu
    And I follow "Course 1"
    And I follow "View all events"
    When I click on "Copy" "link" in the "Booking open" "table_row"
    And I click on "Save changes" "button"
    Then I should see "All events in"
    And I should not see "View all events"

  Scenario: Course page - Face-to-face delete session actions return to original page
    Given I click on "Find Learning" in the totara menu
    And I follow "Course 1"
    When I click on "Delete" "link" in the "Booking open" "table_row"
    And I click on "Cancel" "button"
    Then I should see "View all events"
    And I should not see "All events in"

    Given I click on "Find Learning" in the totara menu
    And I follow "Course 1"
    When I click on "Delete" "link" in the "Booking open" "table_row"
    And I click on "Continue" "button"
    Then I should see "View all events"
    And I should not see "All events in"
    And I should not see "Booking open"

  Scenario: Sessions page - Face-to-face delete session actions return to original page
    Given I click on "Find Learning" in the totara menu
    And I follow "Course 1"
    And I follow "View all events"
    When I click on "Delete" "link" in the "Booking open" "table_row"
    And I click on "Cancel" "button"
    Then I should see "All events in"
    And I should not see "View all events"

    Given I click on "Find Learning" in the totara menu
    And I follow "Course 1"
    And I follow "View all events"
    When I click on "Delete" "link" in the "Booking open" "table_row"
    And I click on "Continue" "button"
    Then I should see "All events in"
    And I should not see "View all events"
    And I should not see "Booking open"

  Scenario: Course page - Face-to-face singup and cancel actions return to original page
    Given I click on "Find Learning" in the totara menu
    And I follow "Course 1"
    When I click on "Sign-up" "link" in the "Booking open" "table_row"
    And I click on "Cancel" "button"
    Then I should see "View all events"
    And I should not see "All events in"

    Given I click on "Find Learning" in the totara menu
    And I follow "Course 1"
    When I click on "Sign-up" "link" in the "Booking open" "table_row"
    And I click on "Sign-up" "button"
    Then I should see "View all events"
    And I should not see "All events in"

    Given I click on "Find Learning" in the totara menu
    And I follow "Course 1"
    When I click on "Cancel booking" "link" in the "Booked" "table_row"
    And I click on "No" "button"
    Then I should see "View all events"
    And I should not see "All events in"

    Given I click on "Find Learning" in the totara menu
    And I follow "Course 1"
    When I click on "Cancel booking" "link" in the "Booked" "table_row"
    And I click on "Yes" "button"
    Then I should see "View all events"
    And I should not see "All events in"

  Scenario: Sessions page - Face-to-face singup and cancel actions return to original page
    Given I click on "Find Learning" in the totara menu
    And I follow "Course 1"
    And I follow "View all events"
    When I click on "Sign-up" "link" in the "Booking open" "table_row"
    And I click on "Cancel" "button"
    Then I should see "All events in"
    And I should not see "View all events"

    Given I click on "Find Learning" in the totara menu
    And I follow "Course 1"
    And I follow "View all events"
    When I click on "Sign-up" "link" in the "Booking open" "table_row"
    And I click on "Sign-up" "button"
    Then I should see "All events in"
    And I should not see "View all events"

    Given I click on "Find Learning" in the totara menu
    And I follow "Course 1"
    And I follow "View all events"
    When I click on "Cancel booking" "link" in the "Booked" "table_row"
    And I click on "No" "button"
    Then I should see "All events in"
    And I should not see "View all events"

    Given I click on "Find Learning" in the totara menu
    And I follow "Course 1"
    And I follow "View all events"
    When I click on "Cancel booking" "link" in the "Booked" "table_row"
    And I click on "Yes" "button"
    Then I should see "All events in"
    And I should not see "View all events"

  Scenario: Face-to-face attendees back link return to original page - top level only
    Given I click on "Find Learning" in the totara menu
    And I follow "Course 1"
    When I click on "Attendees" "link" in the "Booking open" "table_row"
    And I click on "Go back" "link"
    Then I should see "View all events"
    And I should not see "All events in"

    Given I click on "Find Learning" in the totara menu
    And I follow "Course 1"
    And I follow "View all events"
    When I click on "Attendees" "link" in the "Booking open" "table_row"
    And I click on "Go back" "link"
    Then I should see "All events in"
    And I should not see "View all events"
