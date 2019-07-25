@mod @mod_facetoface @javascript
Feature: Verify various conditions of the seminar event dashboard
  Background:
    Given I am on a totara site
    And the following config values are set as admin:
      | facetoface_previouseventstimeperiod | 0 |

    And the following "users" exist:
      | username  | firstname | lastname | email                 |
      | learner1  | learner   | 1        | learner1@example.com  |
      | learner2  | learner   | 2        | learner2@example.com  |
      | learner3  | learner   | 3        | learner3@example.com  |
      | learner4  | learner   | 4        | learner4@example.com  |
      | learner5  | learner   | 5        | learner5@example.com  |
      | learner6  | learner   | 6        | learner6@example.com  |
      | learner7  | learner   | 7        | learner7@example.com  |
      | learner8  | learner   | 8        | learner8@example.com  |
      | learner9  | learner   | 9        | learner9@example.com  |
      | learner10 | learner   | 10       | learner10@example.com |
      | learner11 | learner   | 11       | learner11@example.com |
      | learner12 | learner   | 12       | learner12@example.com |
      | learner13 | learner   | 13       | learner13@example.com |
      | learner14 | learner   | 14       | learner14@example.com |
      | learner15 | learner   | 15       | learner15@example.com |
      | learner16 | learner   | 16       | learner16@example.com |
      | teacher   | tea       | cher     | teacher@example.com   |

    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |

    And the following "course enrolments" exist:
      | user      | course  | role           |
      | learner1  | C1      | student        |
      | learner2  | C1      | student        |
      | learner3  | C1      | student        |
      | learner4  | C1      | student        |
      | learner5  | C1      | student        |
      | learner6  | C1      | student        |
      | learner7  | C1      | student        |
      | learner8  | C1      | student        |
      | learner9  | C1      | student        |
      | learner10 | C1      | student        |
      | learner11 | C1      | student        |
      | learner12 | C1      | student        |
      | learner13 | C1      | student        |
      | learner14 | C1      | student        |
      | learner15 | C1      | student        |
      | learner16 | C1      | student        |
      | teacher   | C1      | editingteacher |

    And the following "seminars" exist in "mod_facetoface" plugin:
      | name      | course  | multiplesessions | sessionattendance | attendancetime |
      | Seminar 1 | C1      | 1                | 5                 | 1              |

    And the following "global rooms" exist in "mod_facetoface" plugin:
      | name   |
      | Room 1 |
      | Room 2 |
      | Room 3 |
      | Room 4 |
      | Room 5 |
      | Room 6 |

    # capacity: 1 = booking full, 2 = booking open, 3 or more = used as a key for wait-listed/cancelled events
    And the following "seminar events" exist in "mod_facetoface" plugin:
      | facetoface   | details                         | capacity | registrationtimestart | registrationtimefinish |
      | Seminar 1    | event waitlist                  | 2        |                    @0 |                     @0 |
      | Seminar 1    | event waitlist full             | 1        |                    @0 |                     @0 |
      | Seminar 1    | event waitlist cancelled        | 3        |                    @0 |                     @0 |
      | Seminar 1    | event future open               | 2        |                    @0 |                     @0 |
      | Seminar 1    | event future full               | 1        |                    @0 |                     @0 |
      | Seminar 1    | event future cancelled          | 4        |                    @0 |                     @0 |
      | Seminar 1    | event future and past open      | 2        |                    @0 |                     @0 |
      | Seminar 1    | event future and past taken     | 1        |                    @0 |                     @0 |
      | Seminar 1    | event future and past cancelled | 5        |                    @0 |                     @0 |
      | Seminar 1    | event past open full            | 1        |                    @0 |                     @0 |
      | Seminar 1    | event past taken                | 2        |                    @0 |                     @0 |
      | Seminar 1    | event past cancelled            | 1        |                    @0 |                     @0 |
      | Seminar 1    | event present open              | 2        |                    @0 |                     @0 |
      | Seminar 1    | event present taken             | 2        |                    @0 |                     @0 |
      | Seminar 1    | event present taken full        | 1        |                    @0 |                     @0 |
      | Seminar 1    | event present cancelled         | 6        |                    @0 |                     @0 |
      | Seminar 1    | event waitlist signup open      | 7        | 13 January  LAST year |   13 January next year |
      | Seminar 1    | event waitlist signup not open  | 8        | 14 February next year |                     @0 |
      | Seminar 1    | event waitlist signup closed    | 9        |                    @0 |   15 March   LAST year |

    # Cancelled events will be adjusted with magic
    And the following "seminar sessions" exist in "mod_facetoface" plugin:
      | eventdetails                    | start                           | finish                          | rooms  |
      | event future open               |  1 January   next year  +1 hour |  1 January   next year  +2 hour | Room 1 |
      | event future full               |  2 February  next year  +2 hour |  2 February  next year  +3 hour | Room 2 |
      | event future cancelled          |  3 March     next year  +3 hour |  3 March     next year  +4 hour | Room 1 |
      | event future and past open      |  4 April     next year  +4 hour |  4 April     next year  +5 hour | Room 2 |
      | event future and past open      |  5 May       LAST year  +5 hour |  5 May       LAST year  +6 hour | Room 1 |
      | event future and past taken     |  6 June      next year  +6 hour |  6 June      next year  +7 hour | Room 2 |
      | event future and past taken     |  7 July      LAST year  +7 hour |  7 July      LAST year  +8 hour | Room 1 |
      | event future and past cancelled |  8 August    next year  +8 hour |  8 August    next year  +9 hour | Room 2 |
      | event future and past cancelled |      09-Sep-2050 09:00:00       |      09-Sep-2050 10:00:00       | Room 1 |
      | event past open full            | 10 October   LAST year +10 hour | 10 October   LAST year +11 hour | Room 2 |
      | event past taken                | 11 November  LAST year +11 hour | 11 November  LAST year +12 hour | Room 1 |
      | event past cancelled            |      12-Dec-2050 12:00:00       |      12-Dec-2050 13:00:00       | Room 2 |
      | event present open              |          now  -1 hour           |           now +1 hour           | Room 3 |
      | event present taken             |          now  -2 hours          |           now +2 hours          | Room 4 |
      | event present taken full        |          now  -3 hours          |           now +3 hours          | Room 5 |
      | event present cancelled         |      30-Jan-2050 00:00:00       |      31-Jan-2050 00:00:00       | Room 6 |

    And the following "seminar signups" exist in "mod_facetoface" plugin:
      | user      | eventdetails                    |
      | learner1  | event waitlist                  |
      | learner2  | event waitlist full             |
      | learner3  | event waitlist cancelled        |
      | learner4  | event future open               |
      | learner5  | event future full               |
      | learner6  | event future cancelled          |
      | learner7  | event future and past open      |
      | learner8  | event future and past taken     |
      | learner9  | event future and past cancelled |
      | learner10 | event past open full            |
      | learner11 | event past taken                |
      | learner12 | event past cancelled            |
      | learner13 | event present open              |
      | learner14 | event present taken             |
      | learner15 | event present taken full        |
      | learner16 | event present cancelled         |

    Given I log in as "admin"
    And I am on "Course 1" course homepage
    And I follow "View all events"

    # Take session attendance
    # Better to create a generator for attendance tracking ???
    When I click on "Take attendance" "link" in the "7:00 AM - 8:00 AM" "table_row"
    And I set the field "learner 8's attendance" to "Unable to attend"
    And I click on "Save attendance" "button"
    Then I should see "Successfully updated attendance"
    And I follow "View all events"

    When I click on "Take attendance" "link" in the "11:00 AM - 12:00 PM" "table_row"
    And I set the field "learner 11's attendance" to "No show"
    And I click on "Save attendance" "button"
    Then I should see "Successfully updated attendance"
    And I follow "View all events"

    When I click on "Take attendance" "link" in the "Room 4" "table_row"
    And I set the field "learner 14's attendance" to "Fully attended"
    And I click on "Save attendance" "button"
    Then I should see "Successfully updated attendance"
    And I follow "View all events"

    When I click on "Take attendance" "link" in the "Room 5" "table_row"
    And I set the field "learner 15's attendance" to "Partially attended"
    And I click on "Save attendance" "button"
    Then I should see "Successfully updated attendance"
    And I follow "View all events"

    # Cancel events
    # Better to add cancellation support to a generator ???
    When I click on "Cancel event" "link" in the "0 / 3 (1 Wait-listed)" "table_row"
    And I click on "Yes" "button"
    Then I should see "Event cancelled" in the ".alert" "css_element"

    When I click on "Cancel event" "link" in the "3:00 AM - 4:00 AM" "table_row"
    And I click on "Yes" "button"
    Then I should see "Event cancelled" in the ".alert" "css_element"

    When I click on "Cancel event" "link" in the "8:00 AM - 9:00 AM" "table_row"
    And I click on "Yes" "button"
    Then I should see "Event cancelled" in the ".alert" "css_element"
    And I use magic to adjust the seminar event "start" from "09/09/2050 09:00" "Australia/Perth" to "09/09/2009 09:00"
    And I use magic to adjust the seminar event "end" from "09/09/2050 10:00" "Australia/Perth" to "09/09/2009 10:00"

    When I click on "Cancel event" "link" in the "12:00 PM - 1:00 PM" "table_row"
    And I click on "Yes" "button"
    Then I should see "Event cancelled" in the ".alert" "css_element"
    And I use magic to adjust the seminar event "start" from "12/12/2050 12:00" "Australia/Perth" to "12/12/2012 12:00"
    And I use magic to adjust the seminar event "end" from "12/12/2050 13:00" "Australia/Perth" to "12/12/2012 13:00"

    When I click on "Cancel event" "link" in the "Room 6" "table_row"
    And I click on "Yes" "button"
    Then I should see "Event cancelled" in the ".alert" "css_element"
    And I use magic to adjust the seminar event "start" from "30/01/2050 00:00" "Australia/Perth" to "01/01/2010 00:00"
    And I use magic to adjust the seminar event "end" from "31/01/2050 00:00" "Australia/Perth" to "01/01/2050 00:00"
    And I log out

  Scenario: Check the integrity of table cells on the seminar event dashboard
    Given I log in as "teacher"
    And I am on "Course 1" course homepage
    And I follow "View all events"

    # event waitlist
    And I should see "Booking open" in the "0 / 2 (1 Wait-listed)" "table_row"

    # event waitlist full
    And I should see "Booking full" in the "0 / 1 (1 Wait-listed)" "table_row"

    # event waitlist cancelled
    And I should see "Cancelled" in the "0 / 3 (1 Wait-listed)" "table_row"

    # event future open
    And I should see "Upcoming" in the "1:00 AM - 2:00 AM" "table_row"
    And I should see "Booking open" in the "1:00 AM - 2:00 AM" "table_row"
    And I should see "Will open at session start time" in the "1:00 AM - 2:00 AM" "table_row"

    # event future full
    And I should see "Upcoming" in the "2:00 AM - 3:00 AM" "table_row"
    And I should see "Booking full" in the "2:00 AM - 3:00 AM" "table_row"
    And I should see "Will open at session start time" in the "2:00 AM - 3:00 AM" "table_row"

    # event future cancelled
    And I should see "Cancelled" in the "3:00 AM - 4:00 AM" "table_row"
    And I should not see "Upcoming" in the "3:00 AM - 4:00 AM" "table_row"
    And I should not see "Over" in the "3:00 AM - 4:00 AM" "table_row"
    And I should see "Room 1" in the "3:00 AM - 4:00 AM" "table_row"
    And I should not see "Will open at session start time" in the "3:00 AM - 4:00 AM" "table_row"
    And I should not see "Take attendance" in the "3:00 AM - 4:00 AM" "table_row"
    And I should not see "Attendance saved" in the "3:00 AM - 4:00 AM" "table_row"

    # event future and past open (future)
    And I should not see "In progress" in the "4:00 AM - 5:00 AM" "table_row"
    And I should see "Upcoming" in the "4:00 AM - 5:00 AM" "table_row"
    And I should see "Will open at session start time" in the "4:00 AM - 5:00 AM" "table_row"

    # event future and past open (past)
    And I should see "Session over" in the "5:00 AM - 6:00 AM" "table_row"
    And I should not see "Booking open" in the "5:00 AM - 6:00 AM" "table_row"
    And I should see "In progress" in the "5:00 AM - 6:00 AM" "table_row"
    And I should see "Take attendance" in the "5:00 AM - 6:00 AM" "table_row"

    # event future and past taken (future)
    And I should not see "In progress" in the "6:00 AM - 7:00 AM" "table_row"
    And I should not see "Booking full" in the "6:00 AM - 7:00 AM" "table_row"
    And I should see "Upcoming" in the "6:00 AM - 7:00 AM" "table_row"
    And I should see "Will open at session start time" in the "6:00 AM - 7:00 AM" "table_row"

    # event future and past taken (past)
    And I should see "In progress" in the "7:00 AM - 8:00 AM" "table_row"
    And I should see "Booking full" in the "7:00 AM - 8:00 AM" "table_row"
    And I should see "Session over" in the "7:00 AM - 8:00 AM" "table_row"
    And I should see "Attendance saved" in the "7:00 AM - 8:00 AM" "table_row"

    # event future and past cancelled (future)
    And I should see "Cancelled" in the "8:00 AM - 9:00 AM" "table_row"
    And I should not see "Upcoming" in the "8:00 AM - 9:00 AM" "table_row"
    And I should not see "Over" in the "8:00 AM - 9:00 AM" "table_row"
    And I should see "Room 2" in the "8:00 AM - 9:00 AM" "table_row"
    And I should not see "Will open at" in the "8:00 AM - 9:00 AM" "table_row"
    And I should not see "Take attendance" in the "8:00 AM - 9:00 AM" "table_row"
    And I should not see "Attendance saved" in the "8:00 AM - 9:00 AM" "table_row"

    # event future and past cancelled (past)
    And I should see "Cancelled" in the "9:00 AM - 10:00 AM" "table_row"
    And I should see "Room 1" in the "9:00 AM - 10:00 AM" "table_row"
    And I should not see "Will open at" in the "9:00 AM - 10:00 AM" "table_row"
    And I should not see "Take attendance" in the "9:00 AM - 10:00 AM" "table_row"
    And I should not see "Attendance saved" in the "9:00 AM - 10:00 AM" "table_row"

    # event past open full
    And I should see "Over" in the "10:00 AM - 11:00 AM" "table_row"
    And I should see "Booking full" in the "10:00 AM - 11:00 AM" "table_row"
    And I should see "Session over" in the "10:00 AM - 11:00 AM" "table_row"
    And I should see "Take attendance" in the "10:00 AM - 11:00 AM" "table_row"

    # event past taken
    And I should see "Over" in the "11:00 AM - 12:00 PM" "table_row"
    And I should not see "Booking open" in the "11:00 AM - 12:00 PM" "table_row"
    And I should not see "Booking full" in the "11:00 AM - 12:00 PM" "table_row"
    And I should see "Session over" in the "11:00 AM - 12:00 PM" "table_row"
    And I should see "Attendance saved" in the "11:00 AM - 12:00 PM" "table_row"

    # event past cancelled
    And I should see "Cancelled" in the "12:00 PM - 1:00 PM" "table_row"
    And I should not see "Over" in the "12:00 PM - 1:00 PM" "table_row"
    And I should see "Room 2" in the "12:00 PM - 1:00 PM" "table_row"
    And I should not see "Session over" in the "12:00 PM - 1:00 PM" "table_row"
    And I should not see "Will open at session start time" in the "12:00 PM - 1:00 PM" "table_row"
    And I should not see "Take attendance" in the "12:00 PM - 1:00 PM" "table_row"
    And I should not see "Attendance saved" in the "12:00 PM - 1:00 PM" "table_row"

    # event present open
    And I should see "In progress" in the "Room 3" "table_row"
    And I should see "Session in progress" in the "Room 3" "table_row"
    And I should see "Take attendance" in the "Room 3" "table_row"

    # event present taken
    And I should see "In progress" in the "Room 4" "table_row"
    And I should see "Session in progress" in the "Room 4" "table_row"
    And I should see "Attendance saved" in the "Room 4" "table_row"

    # event present taken full
    And I should see "In progress" in the "Room 5" "table_row"
    And I should see "Booking full" in the "Room 5" "table_row"
    And I should see "Session in progress" in the "Room 5" "table_row"
    And I should see "Attendance saved" in the "Room 5" "table_row"

    # event present cancelled
    And I should see "Cancelled" in the "1 / 6" "table_row"
    And I should not see "In progress" in the "1 / 6" "table_row"
    And I should not see "Over" in the "1 / 6" "table_row"
    And I should see "Room 6" in the "1 / 6" "table_row"
    And I should not see "Session in progress" in the "1 / 6" "table_row"
    And I should not see "Session over" in the "1 / 6" "table_row"
    And I should not see "Will open at session start time" in the "1 / 6" "table_row"
    And I should not see "Take attendance" in the "1 / 6" "table_row"
    And I should not see "Attendance saved" in the "1 / 6" "table_row"

    # event waitlist signup open
    And I should see "Booking open" in the "0 / 7" "table_row"
    And I should not see "After" in the "0 / 7" "table_row"
    And I should not see "Before" in the "0 / 7" "table_row"

    # event waitlist signup not open
    And I should see "Booking not open" in the "0 / 8" "table_row"
    And I should see "After" in the "0 / 8" "table_row"
    And I should not see "Before" in the "0 / 8" "table_row"

    # event waitlist signup closed
    And I should see "Booking closed" in the "0 / 9" "table_row"
    And I should not see "After" in the "0 / 9" "table_row"
    And I should see "Before" in the "0 / 9" "table_row"

  Scenario: Check filter sessions thoroughly
    Given I log in as "admin"
    And I am on "Course 1" course homepage
    And I follow "View all events"

    # Note that behat will see a text one more time if an <option> label matches the text
    And I should see "Room 1" exactly "7" times
    And I should see "Room 2" exactly "7" times
    And I should see "Room 3" exactly "2" times
    And I should see "Room 4" exactly "2" times
    And I should see "Room 5" exactly "2" times
    And I should see "Room 6"
    And I should see "Cancelled" exactly "11" times
    And I should not see "Reset" in the ".mod_facetoface__filter" "css_element"
    And I should not see "Showing events form last"
    And I should not see "Showing all previous events"
    And ".mod_facetoface__sessionlist__show-previous" "css_element" should not exist

    When I set the field "Event:" to "Upcoming"
    Then I should see "Upcoming" exactly "6" times
    Then I should see "Wait-listed" exactly "7" times
    Then I should see "In progress" exactly "1" times
    Then I should see "Over" exactly "1" times
    And I click on "Reset" "link"

    When I set the field "Booking:" to "Open"
    Then I should see "Booking open" exactly "3" times
    And I should not see "Booking full"
    And I should not see "Booking not open"
    And I should not see "Booking closed"
    And I click on "Reset" "link"

    When I set the field "Room:" to "Room 1"
    Then I should see "Room 1" exactly "7" times
    And I should see "Room 2" exactly "4" times
    And I should see "Room 3" exactly "1" times
    And I should see "Room 4" exactly "1" times
    And I should see "Room 5" exactly "1" times
    And I should see "Room 6"
    And I click on "Reset" "link"

    When I set the field "Advanced:" to "Take attendance"
    Then I should see "Take attendance" exactly "4" times
    And I should see "Attendance saved" exactly "1" times
    And I should not see "Cancelled"

    When I set the field "Advanced:" to "Attendance saved"
    Then I should see "Take attendance" exactly "1" times
    And I should see "Attendance saved" exactly "5" times
    And I should not see "Cancelled"
    And I click on "Reset" "link"

    When I set the field "Event:" to "In progress"
    Then I should see "Room 1" exactly "3" times
    And I should see "Room 2" exactly "3" times
    And I should see "Room 3" exactly "2" times
    And I should see "Room 4" exactly "2" times
    And I should see "Room 5" exactly "2" times
    And I should see "Room 6"
    And I should see "Over" exactly "1" times
    And I should see "In progress" exactly "6" times
    And I should see "Session in progress" exactly "3" times
    And I should see "Session over" exactly "2" times
    And I should not see "Booking open"
    And I should not see "Cancelled"

    When I set the field "Booking:" to "Open"
    Then ".mod_facetoface__sessionlist__table" "css_element" should not exist

    When I set the field "Event:" to "Upcoming"
    And I set the field "Room:" to "Room 1"
    Then I should see "Room 1" exactly "2" times
    And I should not see "Wait-listed"

    When I set the field "Advanced:" to "Take attendance"
    Then I should see "Room 1" exactly "1" times

    When I click on "Reset" "link"
    Then I should see "Room 1" exactly "7" times
    And I should see "Room 2" exactly "7" times
    And I should see "Cancelled" exactly "11" times

    When the following config values are set as admin:
      | facetoface_previouseventstimeperiod | 1000 |
    And I am on "Course 1" course homepage
    And I follow "View all events"

    Then I should see "Showing events form last 1000 days"
    And I should not see "Cancelled"
    When I click on "show all" "link"
    Then I should see "Cancelled" exactly "11" times

  Scenario: Check each learner's seminar event dashboard
    Given I log in as "learner1"
    And I am on "Course 1" course homepage
    When I follow "View all events"
    Then I should not see "Take attendance"
    When I set the field "Booking:" to "My booked events"
    Then I should see "(On waitlist)" in the "Wait-listed" "table_row"
    And I should see "(On waitlist)" in the "Booking open" "table_row"
    And I log out

    Given I log in as "learner2"
    And I am on "Course 1" course homepage
    When I follow "View all events"
    Then I should not see "Take attendance"
    When I set the field "Booking:" to "My booked events"
    Then I should see "(On waitlist)" in the "Wait-listed" "table_row"
    And I should see "(On waitlist)" in the "Booking full" "table_row"
    And I log out

    Given I log in as "learner3"
    And I am on "Course 1" course homepage
    When I follow "View all events"
    Then I should not see "Take attendance"
    When I set the field "Booking:" to "My booked events"
    Then ".mod_facetoface__sessionlist__table" "css_element" should not exist
    And I log out

    Given I log in as "learner4"
    And I am on "Course 1" course homepage
    When I follow "View all events"
    Then I should not see "Take attendance"
    When I set the field "Booking:" to "My booked events"
    Then I should see "(Booked)" in the "Upcoming" "table_row"
    And I should see "(Booked)" in the "Booking open" "table_row"
    And I log out

    Given I log in as "learner5"
    And I am on "Course 1" course homepage
    When I follow "View all events"
    Then I should not see "Take attendance"
    When I set the field "Booking:" to "My booked events"
    Then I should see "(Booked)" in the "Upcoming" "table_row"
    And I should see "(Booked)" in the "Booking full" "table_row"
    And I log out

    Given I log in as "learner6"
    And I am on "Course 1" course homepage
    When I follow "View all events"
    Then I should not see "Take attendance"
    When I set the field "Booking:" to "My booked events"
    Then ".mod_facetoface__sessionlist__table" "css_element" should not exist
    And I log out

    Given I log in as "learner7"
    And I am on "Course 1" course homepage
    When I follow "View all events"
    Then I should not see "Take attendance"
    When I set the field "Booking:" to "My booked events"
    Then I should see "(Booked)" in the "In progress" "table_row"
    And I log out

    Given I log in as "learner8"
    And I am on "Course 1" course homepage
    When I follow "View all events"
    Then I should not see "Take attendance"
    When I set the field "Booking:" to "My booked events"
    Then I should see "(Booked)" in the "In progress" "table_row"
    And I should see "(Booked)" in the "Booking full" "table_row"
    And I log out

    Given I log in as "learner9"
    And I am on "Course 1" course homepage
    When I follow "View all events"
    Then I should not see "Take attendance"
    When I set the field "Booking:" to "My booked events"
    Then ".mod_facetoface__sessionlist__table" "css_element" should not exist
    And I log out

    Given I log in as "learner10"
    And I am on "Course 1" course homepage
    When I follow "View all events"
    Then I should not see "Take attendance"
    When I set the field "Booking:" to "My booked events"
    When I set the field "Booking:" to "My booked events"
    Then I should see "Session over" in the "10:00 AM - 11:00 AM" "table_row"
    And I log out

    Given I log in as "learner11"
    And I am on "Course 1" course homepage
    When I follow "View all events"
    Then I should not see "Take attendance"
    When I set the field "Booking:" to "My booked events"
    Then I should see "Session over" in the "11:00 AM - 12:00 PM" "table_row"
    And I log out

    Given I log in as "learner12"
    And I am on "Course 1" course homepage
    When I follow "View all events"
    Then I should not see "Take attendance"
    When I set the field "Booking:" to "My booked events"
    Then ".mod_facetoface__sessionlist__table" "css_element" should not exist
    And I log out

    Given I log in as "learner13"
    And I am on "Course 1" course homepage
    When I follow "View all events"
    Then I should not see "Take attendance"
    When I set the field "Booking:" to "My booked events"
    Then I should see "(Booked)" in the "In progress" "table_row"
    And I log out

    Given I log in as "learner14"
    And I am on "Course 1" course homepage
    When I follow "View all events"
    Then I should not see "Take attendance"
    When I set the field "Booking:" to "My booked events"
    Then I should see "(Booked)" in the "In progress" "table_row"
    And I log out

    Given I log in as "learner15"
    And I am on "Course 1" course homepage
    When I follow "View all events"
    Then I should not see "Take attendance"
    When I set the field "Booking:" to "My booked events"
    Then I should see "(Booked)" in the "In progress" "table_row"
    And I should see "(Booked)" in the "Booking full" "table_row"
    And I log out

    Given I log in as "learner16"
    And I am on "Course 1" course homepage
    When I follow "View all events"
    Then I should not see "Take attendance"
    When I set the field "Booking:" to "My booked events"
    Then ".mod_facetoface__sessionlist__table" "css_element" should not exist
    And I log out
