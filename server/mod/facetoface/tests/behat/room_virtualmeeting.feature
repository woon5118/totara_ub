@javascript @mod @mod_facetoface @mod_facetoface_virtual_room @totara @totara_core_virtualmeeting
Feature: User sees a seminar virtual room meeting
  Background:
    Given I am on a totara site
    And the following "users" exist:
      | username     | firstname | lastname | email                |
      | learner1     | Learner   | One      | learner1@example.com |
      | trainer1     | Trainer   | First    | trainer1@example.com |
      | trainer2     | Trainer   | Second   | trainer2@example.com |
      | creator      | Creator   | Host     | creator@example.com  |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "custom rooms" exist in "mod_facetoface" plugin:
      | name      | url                                         |
      | Countdown | /mod/facetoface/tests/fixtures/bph4svcr.php |
    And the following "seminars" exist in "mod_facetoface" plugin:
      | name              | intro | course |
      | Virtual gathering |       | C1     |
    And the following "seminar events" exist in "mod_facetoface" plugin:
      | facetoface        | details   |
      | Virtual gathering | Party     |
      | Virtual gathering | Countdown |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | learner1 | C1     | student |
      | trainer1 | C1     | teacher |
      | trainer2 | C1     | teacher |
    And the following "seminar sessions" exist in "mod_facetoface" plugin:
      | eventdetails | start                 | finish                | rooms     | starttimezone    | finishtimezone   | sessiontimezone  |
      | Party        | 24 Dec next year  6pm | 24 Dec next year 9pm  |           | Europe/Prague    | Europe/Prague    | Europe/Prague    |
      | Party        | 24 Dec next year 10pm | 25 Dec next year 12am |           | Pacific/Auckland | Pacific/Auckland | Pacific/Auckland |
      | Party        | 25 Dec next year  5pm | 25 Dec next year 11pm |           | America/Toronto  | America/Toronto  | America/Toronto  |
      | Countdown    | 31 Dec next year 11pm |  1 Jan +2 years 1am   | Countdown | Australia/Perth  | Australia/Perth  | Australia/Perth  |
    And the following "seminar signups" exist in "mod_facetoface" plugin:
      | user     | eventdetails | status |
      | learner1 | Party        | booked |
      | learner1 | Countdown    | booked |
      | trainer1 | Party        | booked |
      | trainer2 | Party        | booked |
    # Disable custom fields so behat will not poke Google Maps
    Given I log in as "admin"
    And I navigate to "Custom fields" node in "Site administration > Seminars"
    And I switch to "Room" tab
    And I click on "Hide" "link" in the "Building" "table_row"
    And I click on "Hide" "link" in the "Location" "table_row"
    And I log out

  Scenario: mod_facetoface_virtualmeeting_001: Create virtual meeting worldwide
    Given I log in as "trainer1"
    And I am on "Virtual gathering" seminar homepage
    And I click on the seminar event action "Edit event" in row "Pacific/Auckland"

    And I click on "Select rooms" "link" in the "Pacific/Auckland" "table_row"
    And I click on "Create" "link_exact" in the "[aria-describedby='selectrooms0-dialog']" "css_element"
    And I set the following fields to these values:
      | Name                  | Party night |
      | Capacity              | 100         |
      | Add virtual room link | PoC App     |
    And I click on "OK" "button_exact" in the "[aria-describedby='editcustomroom0-dialog']" "css_element"

    And I click on "Select rooms" "link" in the "Europe/Prague" "table_row"
    And I click on "Create" "link_exact" in the "[aria-describedby='selectrooms1-dialog']" "css_element"
    And I set the following fields to these values:
      | Name                  | Live music |
      | Capacity              | 200        |
      | Add virtual room link | PoC User   |
    And I click on "Connect" "button" in the "[aria-describedby='editcustomroom1-dialog']" "css_element"
    And I wait "1" seconds
    And I switch to "totara_virtualmeeting_poc_login" window
    And I set the following fields to these values:
      | Username | creator |
      | Password | creator |
    And I click on "Log in" "button"
    And I switch to the main window
    And I wait "1" seconds
    And I click on "OK" "button_exact" in the "[aria-describedby='editcustomroom1-dialog']" "css_element"

    And I click on "Select rooms" "link" in the "America/Toronto" "table_row"
    And I click on "Party night (Capacity: 100)" "text"
    And I click on "OK" "button_exact" in the "[aria-describedby='selectrooms2-dialog']" "css_element"
    And I press "Save changes"

    When I click on "Party night" "link" in the "Pacific/Auckland" "table_row"
    Then I should see "Virtual room is unavailable"
    And I press the "back" button in the browser

    When I click on "Live music" "link" in the "Europe/Prague" "table_row"
    Then I should see "Virtual room is unavailable"
    And I press the "back" button in the browser

    When I click on "Party night" "link" in the "America/Toronto" "table_row"
    Then I should see "Virtual room is unavailable"
    And I press the "back" button in the browser

    Given I run all adhoc tasks

    When I click on "Party night" "link" in the "Pacific/Auckland" "table_row"
    And I follow "Join as attendee"
    And I switch to "totara_virtualmeeting_poc_meet" window
    Then the following fields match these values:
      | Age    | 1                                                    |
      | Start  | ## 24 Dec next year  9am ## D, d M Y H:i:s \G\M\T ## |
      | Finish | ## 24 Dec next year 11am ## D, d M Y H:i:s \G\M\T ## |
    And I click on "Window close" "button_exact"
    And I switch to the main window
    And I press the "back" button in the browser

    When I click on "Live music" "link" in the "Europe/Prague" "table_row"
    And I follow "Join as attendee"
    And I switch to "totara_virtualmeeting_poc_meet" window
    Then the following fields match these values:
      | Age    | 1                                                    |
      | Start  | ## 24 Dec next year  5pm ## D, d M Y H:i:s \G\M\T ## |
      | Finish | ## 24 Dec next year  8pm ## D, d M Y H:i:s \G\M\T ## |
    And I click on "Window close" "button_exact"
    And I switch to the main window
    And I press the "back" button in the browser

    When I click on "Party night" "link" in the "America/Toronto" "table_row"
    And I follow "Join as attendee"
    And I switch to "totara_virtualmeeting_poc_meet" window
    Then the following fields match these values:
      | Age    | 1                                                    |
      | Start  | ## 25 Dec next year 10pm ## D, d M Y H:i:s \G\M\T ## |
      | Finish | ## 26 Dec next year  4am ## D, d M Y H:i:s \G\M\T ## |
    And I click on "Window close" "button_exact"
    And I switch to the main window
    And I press the "back" button in the browser

    When I click on "Countdown" "link" in the "Australia/Perth" "table_row"
    And I follow "Go to room"
    And I switch to "totara_bph4svcr" window
    Then I should see "Behat: Virtual Room Placeholder Page" in the page title
    And I click on "Window close" "button_exact"
    And I switch to the main window
    And I press the "back" button in the browser
