@mod @mod_facetoface @totara
Feature: Face to face signup periods
  In order to verify Face to Face signup periods
  As a f2fadmin
  I need to set various dates

  Background:
    Given I am on a totara site
    And the following "users" exist:
      | username | firstname | lastname | email               |
      | student1 | Stu       | Dent     | student@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | student1 | C1     | student |
    And I log in as "admin"
    And I click on "Find Learning" in the totara menu
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Face-to-face" to section "1" and I fill the form with:
      | Name        | Test facetoface name        |
      | Description | Test facetoface description |
    And I follow "View all sessions"

  @javascript
  Scenario Outline: Sign up students regardless of sign in period status
    Given I follow "Add a new session"
    And I set the following fields to these values:
      | Enable sign up period open       | <periodopen>  |
      | registrationtimestart[day]       | 30            |
      | registrationtimestart[month]     | June          |
      | registrationtimestart[year]      | <startyear>   |
      | registrationtimestart[hour]      | 01            |
      | registrationtimestart[minute]    | 00            |
      | registrationtimestart[timezone]  | <startzone>   |
      | Enable sign up period close      | <periodclose> |
      | registrationtimefinish[day]      | 30            |
      | registrationtimefinish[month]    | June          |
      | registrationtimefinish[year]     | <endyear>     |
      | registrationtimefinish[hour]     | 01            |
      | registrationtimefinish[minute]   | 00            |
      | registrationtimefinish[timezone] | <endzone>     |
    And I press "Save changes"
    And I click on "Attendees" "link"
    And I set the field "f2f-actions" to "Add users"
    And I click on "student@example.com" "option"
    And I click on "Add" "button"
    And I click on "Continue" "button"
    And I click on "Confirm" "button"
    And I click on "Wait-list" "link" in the ".tabtree" "css_element"
    And I should see "Stu Dent"

  Examples:
    | periodopen | startyear | startzone        | periodclose | endyear | endzone          |
    | 1          | 2014      | Pacific/Auckland | 1           | 2015    | Pacific/Auckland |
    | 1          | 2014      | Pacific/Auckland | 1           | 2030    | Pacific/Auckland |
    | 1          | 2029      | Pacific/Auckland | 1           | 2030    | Pacific/Auckland |
    | 1          | 2029      | Pacific/Honolulu | 1           | 2030    | Pacific/Fiji     |
    | 0          | 2029      | Pacific/Auckland | 0           | 2030    | Pacific/Auckland |
    | 1          | 2029      | Pacific/Auckland | 0           | 2030    | Pacific/Auckland |
    | 0          | 2029      | Pacific/Auckland | 1           | 2030    | Pacific/Auckland |


  Scenario Outline: Test signup period validation
    Given I follow "Add a new session"
    And I set the following fields to these values:
      | Enable sign up period open       | <periodopen>       |
      | registrationtimestart[day]       | <periodstartday>   |
      | registrationtimestart[month]     | June               |
      | registrationtimestart[year]      | 2030               |
      | registrationtimestart[hour]      | <periodstarthour>  |
      | registrationtimestart[minute]    | 00                 |
      | registrationtimestart[timezone]  | <periodstartzone>  |
      | Enable sign up period close      | <periodclose>      |
      | registrationtimefinish[day]      | <periodendday>     |
      | registrationtimefinish[month]    | June               |
      | registrationtimefinish[year]     | 2030               |
      | registrationtimefinish[hour]     | <periodendhour>    |
      | registrationtimefinish[minute]   | 00                 |
      | registrationtimefinish[timezone] | <periodendzone>    |
      | datetimeknown                    | Yes                |
      | timestart[0][day]                | <sessionstartday>  |
      | timestart[0][month]              | June               |
      | timestart[0][year]               | 2030               |
      | timestart[0][hour]               | <sessionstarthour> |
      | timestart[0][minute]             | 00                 |
      | timestart[0][timezone]           | <sessionstartzone> |
      | timefinish[0][day]               | <sessionendday>    |
      | timefinish[0][month]             | June               |
      | timefinish[0][year]              | 2030               |
      | timefinish[0][hour]              | <sessionendhour>   |
      | timefinish[0][minute]            | 00                 |
      | timefinish[0][timezone]          | <sessionendzone>   |
    And I press "Save changes"
    Then I should see "<message>"

  Examples:
    | periodopen | periodstartday | periodstarthour | periodstartzone  | periodclose | periodendday | periodendhour | periodendzone    | sessionstartday | sessionstarthour | sessionstartzone | sessionendday | sessionendhour | sessionendzone   | message                                                    | description unused |
    | 1          | 1              | 01              | Pacific/Auckland | 1           | 15           | 01            | Pacific/Auckland | 20              | 09               | Pacific/Auckland | 20            | 10             | Pacific/Auckland | Upcoming sessions                                          | Normal case |
    | 1          | 16             | 01              | Pacific/Auckland | 1           | 15           | 01            | Pacific/Auckland | 20              | 09               | Pacific/Auckland | 20            | 10             | Pacific/Auckland | Signup period start time must be before signup finish time | Clear start signup > end signup |
    | 1          | 15             | 01              | Pacific/Auckland | 1           | 15           | 01            | Pacific/Auckland | 20              | 09               | Pacific/Auckland | 20            | 10             | Pacific/Auckland | Signup period start time must be before signup finish time | Start signup = End Signup |
    | 1          | 1              | 01              | Pacific/Auckland | 1           | 15           | 01            | Pacific/Auckland | 10              | 09               | Pacific/Auckland | 20            | 10             | Pacific/Auckland | Upcoming sessions                                          | session date inside signup range |
    | 1          | 12             | 01              | Pacific/Auckland | 1           | 15           | 01            | Pacific/Auckland | 10              | 09               | Pacific/Auckland | 10            | 10             | Pacific/Auckland | Signup period start time must be before Session start      | Clear session start before signup start |
    | 1          | 10             | 09              | Pacific/Auckland | 1           | 15           | 01            | Pacific/Auckland | 10              | 09               | Pacific/Auckland | 10            | 10             | Pacific/Auckland | Signup period start time must be before Session start      | Signup start = session start |
    | 1          | 1              | 01              | Pacific/Auckland | 1           | 20           | 09            | Pacific/Auckland | 20              | 09               | Pacific/Auckland | 20            | 10             | Pacific/Auckland | Upcoming sessions                                          | End signup = session start |
    # And now for some timezone fun
    | 1          | 15             | 01              | Europe/London    | 1           | 15           | 13            | Pacific/Auckland | 20              | 09               | Pacific/Auckland | 20            | 10             | Pacific/Auckland | Upcoming sessions                                          | Normal case |
    | 1          | 15             | 02              | Europe/London    | 1           | 15           | 13            | Pacific/Auckland | 20              | 09               | Pacific/Auckland | 20            | 10             | Pacific/Auckland | Signup period start time must be before signup finish time | Start signup = End Signup |
    | 1          | 15             | 03              | Europe/London    | 1           | 15           | 13            | Pacific/Auckland | 20              | 09               | Pacific/Auckland | 20            | 10             | Pacific/Auckland | Signup period start time must be before signup finish time | Clear start signup > end signup |
    | 1          | 15             | 01              | Europe/London    | 1           | 15           | 23            | Pacific/Auckland | 15              | 12               | Pacific/Auckland | 20            | 13             | Pacific/Auckland | Upcoming sessions                                          | Normal case |
    | 1          | 15             | 02              | Europe/London    | 1           | 15           | 23            | Pacific/Auckland | 15              | 12               | Pacific/Auckland | 20            | 13             | Pacific/Auckland | Signup period start time must be before Session start      | Start signup = start session |
    | 1          | 15             | 03              | Europe/London    | 1           | 15           | 23            | Pacific/Auckland | 15              | 12               | Pacific/Auckland | 20            | 13             | Pacific/Auckland | Signup period start time must be before Session start      | Start signup > start session |
    | 1          | 15             | 13              | Pacific/Auckland | 1           | 15           | 01            | Europe/London    | 20              | 09               | Pacific/Auckland | 20            | 10             | Pacific/Auckland | Signup period start time must be before signup finish time | Normal case |
    | 1          | 15             | 14              | Pacific/Auckland | 1           | 15           | 01            | Europe/London    | 20              | 09               | Pacific/Auckland | 20            | 10             | Pacific/Auckland | Signup period start time must be before signup finish time | Start signup = End Signup |
    | 1          | 15             | 15              | Pacific/Auckland | 1           | 15           | 01            | Europe/London    | 20              | 09               | Pacific/Auckland | 20            | 10             | Pacific/Auckland | Signup period start time must be before signup finish time | Clear start signup > end signup |
    | 1          | 15             | 13              | Pacific/Auckland | 1           | 20           | 01            | Pacific/Auckland | 15              | 01               | Europe/London    | 20            | 10             | Pacific/Auckland | Signup period start time must be before Session start      | Clear session start before signup start |
    | 1          | 15             | 14              | Pacific/Auckland | 1           | 20           | 01            | Pacific/Auckland | 15              | 01               | Europe/London    | 20            | 10             | Pacific/Auckland | Signup period start time must be before Session start      | Signup start = session start |
    | 1          | 15             | 15              | Pacific/Auckland | 1           | 20           | 01            | Pacific/Auckland | 15              | 01               | Europe/London    | 20            | 10             | Pacific/Auckland | Upcoming sessions                                          | Signup start > session start |

  Scenario Outline: Check the correct text is displayed in various states when there is a signup period
    Given I follow "Add a new session"
    And I set the following fields to these values:
      | Enable sign up period open       | <periodopen>  |
      | registrationtimestart[day]       | 30            |
      | registrationtimestart[month]     | June          |
      | registrationtimestart[year]      | <startyear>   |
      | registrationtimestart[hour]      | 01            |
      | registrationtimestart[minute]    | 00            |
      | registrationtimestart[timezone]  | <startzone>   |
      | Enable sign up period close      | <periodclose> |
      | registrationtimefinish[day]      | 30            |
      | registrationtimefinish[month]    | June          |
      | registrationtimefinish[year]     | <endyear>     |
      | registrationtimefinish[hour]     | 01            |
      | registrationtimefinish[minute]   | 00            |
      | registrationtimefinish[timezone] | <endzone>     |
    And I press "Save changes"
    And I log out

    When I log in as "student1"
    And I click on "Find Learning" in the totara menu
    And I follow "Course 1"
    Then I should see "<signupavailable>"

    When I follow "View all sessions"
    Then I should see "<bookingstatus>"
    And I should see "<signupperiod>"

  Examples:
    | periodopen | startyear | startzone        | periodclose | endyear | endzone          | signupavailable     | bookingstatus               | signupperiod                                                                   |
    | 1          | 2014      | Pacific/Auckland | 1           | 2015    | Pacific/Auckland | Sign-up unavailable | Signup period is now closed | 30 June 2014 1:00 AM Pacific/Auckland to 30 June 2014 1:00 AM Pacific/Auckland |
    | 1          | 2014      | Pacific/Auckland | 1           | 2030    | Pacific/Auckland | Join waitlist       | Booking open                | 30 June 2014 1:00 AM Pacific/Auckland to 30 June 2030 1:00 AM Pacific/Auckland |
    | 1          | 2029      | Pacific/Auckland | 1           | 2030    | Pacific/Auckland | Sign-up unavailable | Signup period not open      | 30 June 2029 1:00 AM Pacific/Auckland to 30 June 2030 1:00 AM Pacific/Auckland |
    | 1          | 2029      | Pacific/Honolulu | 1           | 2030    | Pacific/Fiji     | Sign-up unavailable | Signup period not open      | 30 June 2029 1:00 AM Pacific/Honolulu to 30 June 2030 1:00 AM Pacific/Fiji     |
    | 0          | 2029      | Pacific/Auckland | 0           | 2030    | Pacific/Auckland | Join waitlist       | Booking open                | Booking Open                                                                   |
    | 1          | 2029      | Pacific/Auckland | 0           | 2030    | Pacific/Auckland | Sign-up unavailable | Signup period not open      | After 30 June 2029 1:00 AM Pacific/Auckland                                    |
    | 0          | 2029      | Pacific/Auckland | 1           | 2030    | Pacific/Auckland | Join waitlist       | Booking open                | Before 30 June 2030 1:00 AM Pacific/Auckland                                   |




