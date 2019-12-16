@mod @mod_facetoface @totara @javascript
Feature: Sign up status
  In order to ensure the status displayed for the sign-up is correct
  As admin
  I need to create seminars with different settings

  #  Sign-up status follows certain order. If the first is not met then it will look down the following statuses
  #  to show what corresponds:
  #  1. Event cancelled.
  #  2. Session in progress
  #  3. Session over
  #  4. Booked session
  #  5. Session full
  #  6. Registration not open
  #  7. Registration closed
  Background:
    Given I am on a totara site
    And the following "users" exist:
      | username | firstname | lastname |email                |
      | student1 | Sam1      | Student1 |student1@example.com |
      | student2 | Sam2      | Student2 |student2@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | student1 | C1     | student |
      | student2 | C1     | student |
    And the following "seminars" exist in "mod_facetoface" plugin:
      | name              | course |
      | Test seminar name | C1     |
    And I log in as "admin"
    And I am on "Course 1" course homepage
    And I follow "View all events"

  Scenario: Check session with booking full status is changed when event is cancelled.
    # Create a session with status full and then cancel it.
    Given I follow "Add event"
    And I click on "Edit session" "link"
    And I set the following fields to these values:
      | timestart[day]     | 1                |
      | timestart[month]   | 1                |
      | timestart[year]    | ## next year ## Y ## |
      | timestart[hour]    | 0                |
      | timestart[minute]  | 0                |
      | timefinish[day]    | 1                |
      | timefinish[month]  | 2                |
      | timefinish[year]   | ## next year ## Y ## |
      | timefinish[hour]   | 1                |
      | timefinish[minute] | 0                |
    And I press "OK"
    And I set the following fields to these values:
      | capacity           | 1                |
    And I press "Save changes"

    And I click on the seminar event action "Attendees" in row "#1"
    And I set the field "Attendee actions" to "Add users"
    And I set the following fields to these values:
      | searchtext | Sam |
    And I click on "Search" "button" in the "#region-main" "css_element"
    And I set the field "potential users" to "Sam1 Student1, student1@example.com"
    And I press exact "add"
    And I wait "1" seconds
    And I press "Continue"
    And I press "Confirm"
    Then I should see "Sam1 Student1"
    And I log out

    When I log in as "student2"
    And I am on "Course 1" course homepage
    And I follow "View all events"
    Then I should see "Booking full" in the "1 January" "table_row"
    And I should not see "Cancelled" in the "1 January" "table_row"
    And I log out

    And I log in as "admin"
    And I am on "Course 1" course homepage
    And I follow "View all events"
    And I click on the seminar event action "Cancel event" in row "1 January"
    And I should see "Are you sure you want to cancel this event?"
    And I press "Yes"
    And I should see "Event cancelled" in the ".alert-success" "css_element"
    And I log out

    When I log in as "student2"
    And I am on "Course 1" course homepage
    And I follow "View all events"
    Then I should see "Cancelled" in the "1 January" "table_row"
    And I log out

  Scenario: Cancelled users who cannot sign-up should be given Event info option and no any other option that cannot perform
    And I follow "Add event"
    And I press "Save changes"
    And I navigate to "Global settings" node in "Site administration > Seminars"
    And I click on "s__facetoface_approvaloptions[approval_admin]" "checkbox"
    And I press "Save changes"
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I click on the link "Go to event" in row 1
    And I press "Sign-up"
    And I log out
    And I log in as "admin"
    And I am on "Course 1" course homepage
    And I follow "Test seminar name"
    And I follow "Edit settings"
    And I expand all fieldsets
    And I set the field "Manager and Administrative approval" to "1"
    And I click on "Add approver" "button"
    And I click on "Admin User" "link" in the "Select activity level approvers" "totaradialogue"
    And I click on "Save" "button" in the "Select activity level approvers" "totaradialogue"
    And I click on "Save and display" "button"
    And I click on the seminar event action "Attendees" in row "#1"
    And I set the field "Attendee actions" to "Remove users"
    And I set the field "Current attendees" to "Sam1 Student1, student1@example.com"
    And I press "Remove"
    And I press "Continue"
    And I press "Confirm"
    And I should see "Bulk remove users success"
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I click on the link "Go to event" in row 1
    And I should see "Sign-up unavailable"
    And I should see "Manager and Administrative approval"

  Scenario Outline: Event cancelled should be displayed in the status column regardless the signup period
    Given I follow "Add event"
    And I click on "Delete" "link" in the ".f2fmanagedates" "css_element"
    And I set the following fields to these values:
      | registrationtimestart[enabled]   | <periodopen>  |
      | registrationtimestart[month]     | July          |
      | registrationtimestart[day]       | 30            |
      | registrationtimestart[year]      | ## <startyear> ## Y ## |
      | registrationtimestart[hour]      | 01            |
      | registrationtimestart[minute]    | 00            |
      | registrationtimestart[timezone]  | <startzone>   |
      | registrationtimefinish[enabled]  | <periodclose> |
      | registrationtimefinish[month]    | July          |
      | registrationtimefinish[day]      | 30            |
      | registrationtimefinish[year]     | ## <endyear> ## Y ## |
      | registrationtimefinish[hour]     | 01            |
      | registrationtimefinish[minute]   | 00            |
      | registrationtimefinish[timezone] | <endzone>     |
    And I press "Save changes"
    And I log out

    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I click on "Go to event" "link" in the "Wait-listed" "table_row"
    Then "submitbutton" "button" <signupavailable> exist

    When I follow "View all events"
    Then I should see "<bookingstatus>" in the "Wait-listed" "table_row"
    And I should see date "30 July, <startyear>" formatted "<signupperiodstartformat>" in the "Wait-listed" "table_row"
    And I should see date "30 July, <endyear>" formatted "<signupperiodendformat>" in the "Wait-listed" "table_row"
    And I should see "<signupperiodzone>" in the "Wait-listed" "table_row"
    And I should not see "Cancelled" in the "Wait-listed" "table_row"
    And I log out

    And I log in as "admin"
    And I am on "Course 1" course homepage
    And I follow "View all events"
    And I click on the seminar event action "Cancel event" in row "Wait-listed"
    And I should see "Are you sure you want to cancel this event?"
    And I press "Yes"
    And I should see "Event cancelled" in the ".alert-success" "css_element"
    And I log out

    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "View all events"
    Then I should see "Cancelled" in the "10" "table_row"
    And I should not see "Wait-listed" in the "10" "table_row"
    And I log out

    Examples:
      | periodopen | startyear | startzone        | periodclose | endyear | endzone         | signupavailable | bookingstatus    | signupperiodstartformat    | signupperiodendformat      | signupperiodzone |
      | 1          | -2 year   | Australia/Perth  | 1           | -1 year | Australia/Perth | should not      | Booking closed   | 30 July %Y, 1:00 AM        | 30 July %Y, 1:00 AM        | Australia/Perth  |
      | 1          | -2 year   | Australia/Perth  | 1           | +2 year | Australia/Perth | should          | Booking open     | 30 July %Y, 1:00 AM        | 30 July %Y, 1:00 AM        | Australia/Perth  |
      | 1          | +1 year   | Australia/Perth  | 1           | +2 year | Australia/Perth | should not      | Booking not open | 30 July %Y, 1:00 AM        | 30 July %Y, 1:00 AM        | Australia/Perth  |
      | 1          | +1 year   | Pacific/Honolulu | 1           | +2 year | Pacific/Fiji    | should not      | Booking not open | 30 July %Y, 7:00 PM        | 29 July %Y, 9:00 PM        | Australia/Perth  |
      | 0          | +1 year   | Australia/Perth  | 0           | +2 year | Australia/Perth | should          | Booking open     | -                          | -                          | -                |
      | 1          | +1 year   | Australia/Perth  | 0           | +2 year | Australia/Perth | should not      | Booking not open | After 30 July %Y, 1:00 AM  | -                          | Australia/Perth  |
      | 0          | +1 year   | Australia/Perth  | 1           | +2 year | Australia/Perth | should          | Booking open     | -                          | Before 30 July %Y, 1:00 AM | Australia/Perth  |
