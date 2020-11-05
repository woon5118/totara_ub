@javascript @mod @mod_facetoface @mod_facetoface_virtual_room @totara
Feature: User sees a seminar virtual room link
  Background:
    Given I am on a totara site
    And the following "users" exist:
      | username     | firstname   | lastname | email                    |
      | learner1     | Learner     | One      | learner1@example.com     |
      | trainer1     | Trainer     | First    | trainer1@example.com     |
      | trainer2     | Trainer     | Second   | trainer2@example.com     |
      | trainer3     | Trainer     | Third    | trainer3@example.com     |
      | manager1     | Manager     | Uno      | manager1@example.com     |
      | manager2     | Manager     | Duex     | manager2@example.com     |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "global rooms" exist in "mod_facetoface" plugin:
      | name                  | url                                         |
      | Virtual meating spice | /mod/facetoface/tests/fixtures/bph4svcr.php |
    And the following "global rooms" exist in "mod_facetoface" plugin:
      | name                     |
      | Session near future      |
      | Session far future       |
      | Session ongoing 1        |
      | Session ongoing 2 future |
      | Session ongoing 2 past   |
      | Session past             |
    And the following "global facilitators" exist in "mod_facetoface" plugin:
      | name              | allowconflicts | hidden | description | username |
      | Teacher assistant | 0              | 0      | Volunteer   | trainer3 |
    And the following "seminars" exist in "mod_facetoface" plugin:
      | name                   | intro | course |
      | Test seminar vanilla   |       | C1     |
      | Test seminar booked    |       | C1     |
      | Test seminar cancelled |       | C1     |
      | Test seminar poofed    |       | C1     |
    And the following "seminar events" exist in "mod_facetoface" plugin:
      | facetoface             | details               | cancelledstatus |
      | Test seminar vanilla   | near future           | 0               |
      | Test seminar vanilla   | far future            | 0               |
      | Test seminar vanilla   | ongoing 1             | 0               |
      | Test seminar vanilla   | ongoing 2             | 0               |
      | Test seminar vanilla   | past                  | 0               |
      | Test seminar booked    | near future booked    | 0               |
      | Test seminar booked    | far future booked     | 0               |
      | Test seminar booked    | ongoing 1 booked      | 0               |
      | Test seminar booked    | ongoing 2 booked      | 0               |
      | Test seminar booked    | past booked           | 0               |
      | Test seminar cancelled | near future cancelled | 1               |
      | Test seminar cancelled | far future cancelled  | 1               |
      | Test seminar cancelled | ongoing 1 cancelled   | 1               |
      | Test seminar cancelled | ongoing 2 cancelled   | 1               |
      | Test seminar cancelled | past cancelled        | 1               |
      | Test seminar poofed    | near future poofed    | 1               |
      | Test seminar poofed    | far future poofed     | 1               |
      | Test seminar poofed    | ongoing 1 poofed      | 1               |
      | Test seminar poofed    | ongoing 2 poofed      | 1               |
      | Test seminar poofed    | past poofed           | 1               |
    And the following "seminar sessions" exist in "mod_facetoface" plugin:
      | eventdetails          | start           | finish              | rooms                                            | facilitators      |
      | near future           | +15 min         | +2 hours            | Session near future      , Virtual meating spice | Teacher assistant |
      | near future booked    | +15 min         | +2 hours            | Session near future      , Virtual meating spice | Teacher assistant |
      | near future cancelled | +15 min         | +2 hours            | Session near future      , Virtual meating spice | Teacher assistant |
      | near future poofed    | +15 min         | +2 hours            | Session near future      , Virtual meating spice | Teacher assistant |
      | far future            | 5 May next year | 5 May next year 1am | Session far future       , Virtual meating spice | Teacher assistant |
      | far future booked     | 5 May next year | 5 May next year 1am | Session far future       , Virtual meating spice | Teacher assistant |
      | far future cancelled  | 5 May next year | 5 May next year 1am | Session far future       , Virtual meating spice | Teacher assistant |
      | far future poofed     | 5 May next year | 5 May next year 1am | Session far future       , Virtual meating spice | Teacher assistant |
      | ongoing 2             | 4 Apr next year | 4 Apr next year 1am | Session ongoing 2 future , Virtual meating spice | Teacher assistant |
      | ongoing 2 booked      | 4 Apr next year | 4 Apr next year 1am | Session ongoing 2 future , Virtual meating spice | Teacher assistant |
      | ongoing 2 cancelled   | 4 Apr next year | 4 Apr next year 1am | Session ongoing 2 future , Virtual meating spice | Teacher assistant |
      | ongoing 2 poofed      | 4 Apr next year | 4 Apr next year 1am | Session ongoing 2 future , Virtual meating spice | Teacher assistant |
      | ongoing 1             | 3 Mar last year | 3 Mar next year 1am | Session ongoing 1        , Virtual meating spice | Teacher assistant |
      | ongoing 1 booked      | 3 Mar last year | 3 Mar next year 1am | Session ongoing 1        , Virtual meating spice | Teacher assistant |
      | ongoing 1 cancelled   | 3 Mar last year | 3 Mar next year 1am | Session ongoing 1        , Virtual meating spice | Teacher assistant |
      | ongoing 1 poofed      | 3 Mar last year | 3 Mar next year 1am | Session ongoing 1        , Virtual meating spice | Teacher assistant |
      | ongoing 2             | 2 Feb last year | 2 Feb last year 1am | Session ongoing 2 past   , Virtual meating spice | Teacher assistant |
      | ongoing 2 booked      | 2 Feb last year | 2 Feb last year 1am | Session ongoing 2 past   , Virtual meating spice | Teacher assistant |
      | ongoing 2 cancelled   | 2 Feb last year | 2 Feb last year 1am | Session ongoing 2 past   , Virtual meating spice | Teacher assistant |
      | ongoing 2 poofed      | 2 Feb last year | 2 Feb last year 1am | Session ongoing 2 past   , Virtual meating spice | Teacher assistant |
      | past                  | 1 Jan last year | 1 Jan last year 1am | Session past             , Virtual meating spice | Teacher assistant |
      | past booked           | 1 Jan last year | 1 Jan last year 1am | Session past             , Virtual meating spice | Teacher assistant |
      | past cancelled        | 1 Jan last year | 1 Jan last year 1am | Session past             , Virtual meating spice | Teacher assistant |
      | past poofed           | 1 Jan last year | 1 Jan last year 1am | Session past             , Virtual meating spice | Teacher assistant |
    And the following "role assigns" exist:
      | user     | role           | contextlevel | reference |
      | trainer1 | editingteacher | System       |           |
      | manager1 | manager        | System       |           |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | learner1 | C1     | student        |
      | trainer2 | C1     | teacher        |
      | trainer3 | C1     | student        |
      | manager2 | C1     | staffmanager   |
    And the following "seminar signups" exist in "mod_facetoface" plugin:
      | user     | eventdetails       | status          |
      | learner1 | near future booked | booked          |
      | learner1 | far future booked  | booked          |
      | learner1 | ongoing 1 booked   | booked          |
      | learner1 | ongoing 2 booked   | booked          |
      | learner1 | past booked        | booked          |
      | learner1 | near future poofed | event_cancelled |
      | learner1 | far future poofed  | event_cancelled |
      | learner1 | ongoing 1 poofed   | event_cancelled |
      | learner1 | ongoing 2 poofed   | event_cancelled |
      | learner1 | past poofed        | event_cancelled |

  Scenario: Look at the virtual room card as admin
    Given the following "standard_report" exist in "totara_reportbuilder" plugin:
      | fullname    | shortname | source           |
      | room report | roomrp    | facetoface_rooms |
    And I log in as "admin"
    When I navigate to "Rooms" node in "Site administration > Seminars"
    And I click on "Virtual meating spice" "link"
    And I click on "Go to room" "button"
    And I switch to "totara_bph4svcr" window
    Then I should see "Behat: Virtual Room Placeholder Page" in the page title
    And I click on "Window close" "button_exact"
    And I switch to the main window
    When I click on "Reports" in the totara menu
    And I follow "room report"
    And I click on "Virtual meating spice" "link"
    And I click on "Go to room" "button"
    And I switch to "totara_bph4svcr" window
    Then I should see "Behat: Virtual Room Placeholder Page" in the page title
    And I click on "Window close" "button_exact"
    And I switch to the main window

  Scenario: Look at the virtual room card as learner
    And I log in as "learner1"
    Given I am on "Test seminar vanilla" seminar homepage
    When I click on "Virtual meating spice" "link" in the "Session near future" "table_row"
    Then I should see "Virtual room is unavailable"
    And I press the "back" button in the browser
    When I click on "Virtual meating spice" "link" in the "Session far future" "table_row"
    Then I should see "Virtual room is unavailable"
    And I press the "back" button in the browser
    When I click on "Virtual meating spice" "link" in the "Session ongoing 1" "table_row"
    Then I should see "Virtual room is unavailable"
    And I press the "back" button in the browser
    When I click on "Virtual meating spice" "link" in the "Session ongoing 2 future" "table_row"
    Then I should see "Virtual room is unavailable"
    And I press the "back" button in the browser
    When I click on "Virtual meating spice" "link" in the "Session ongoing 2 past" "table_row"
    Then I should see "Virtual room is unavailable"
    And I press the "back" button in the browser
    When I click on "Virtual meating spice" "link" in the "Session past" "table_row"
    Then I should see "Virtual room is unavailable"

    Given I am on "Test seminar booked" seminar homepage
    When I click on "Virtual meating spice" "link" in the "Session near future" "table_row"
    And I click on "Join now" "button"
    And I switch to "totara_bph4svcr" window
    Then I should see "Behat: Virtual Room Placeholder Page" in the page title
    And I click on "Window close" "button_exact"
    And I switch to the main window
    And I press the "back" button in the browser
    When I click on "Virtual meating spice" "link" in the "Session far future" "table_row"
    Then I should see "Virtual room will open 15 minutes before next session"
    And I press the "back" button in the browser
    When I click on "Virtual meating spice" "link" in the "Session ongoing 1" "table_row"
    And I click on "Join now" "button"
    And I switch to "totara_bph4svcr" window
    Then I should see "Behat: Virtual Room Placeholder Page" in the page title
    And I click on "Window close" "button_exact"
    And I switch to the main window
    And I press the "back" button in the browser
    When I click on "Virtual meating spice" "link" in the "Session ongoing 2 future" "table_row"
    Then I should see "Virtual room will open 15 minutes before next session"
    And I press the "back" button in the browser
    When I click on "Virtual meating spice" "link" in the "Session ongoing 2 past" "table_row"
    Then I should see "Virtual room is no longer available"
    And I press the "back" button in the browser
    When I click on "Virtual meating spice" "link" in the "Session past" "table_row"
    Then I should see "Virtual room is no longer available"

    Given I am on "Test seminar cancelled" seminar homepage
    When I click on "Virtual meating spice" "link" in the "Session near future" "table_row"
    Then I should see "Virtual room is unavailable"
    And I press the "back" button in the browser
    When I click on "Virtual meating spice" "link" in the "Session far future" "table_row"
    Then I should see "Virtual room is unavailable"
    And I press the "back" button in the browser
    When I click on "Virtual meating spice" "link" in the "Session ongoing 1" "table_row"
    Then I should see "Virtual room is unavailable"
    And I press the "back" button in the browser
    When I click on "Virtual meating spice" "link" in the "Session ongoing 2 future" "table_row"
    Then I should see "Virtual room is unavailable"
    And I press the "back" button in the browser
    When I click on "Virtual meating spice" "link" in the "Session ongoing 2 past" "table_row"
    Then I should see "Virtual room is unavailable"
    And I press the "back" button in the browser
    When I click on "Virtual meating spice" "link" in the "Session past" "table_row"
    Then I should see "Virtual room is unavailable"

    Given I am on "Test seminar poofed" seminar homepage
    When I click on "Virtual meating spice" "link" in the "Session near future" "table_row"
    Then I should see "Virtual room is unavailable"
    And I press the "back" button in the browser
    When I click on "Virtual meating spice" "link" in the "Session far future" "table_row"
    Then I should see "Virtual room is unavailable"
    And I press the "back" button in the browser
    When I click on "Virtual meating spice" "link" in the "Session ongoing 1" "table_row"
    Then I should see "Virtual room is unavailable"
    And I press the "back" button in the browser
    When I click on "Virtual meating spice" "link" in the "Session ongoing 2 future" "table_row"
    Then I should see "Virtual room is unavailable"
    And I press the "back" button in the browser
    When I click on "Virtual meating spice" "link" in the "Session ongoing 2 past" "table_row"
    Then I should see "Virtual room is unavailable"
    And I press the "back" button in the browser
    When I click on "Virtual meating spice" "link" in the "Session past" "table_row"
    Then I should see "Virtual room is unavailable"

  Scenario Outline: Look at the virtual room card as trainer
    And I log in as "<username>"
    Given I am on "Test seminar vanilla" seminar homepage
    When I click on "Virtual meating spice" "link" in the "Session near future" "table_row"
    And I click on "Go to room" "button"
    And I switch to "totara_bph4svcr" window
    Then I should see "Behat: Virtual Room Placeholder Page" in the page title
    And I click on "Window close" "button_exact"
    And I switch to the main window
    And I press the "back" button in the browser
    When I click on "Virtual meating spice" "link" in the "Session far future" "table_row"
    And I click on "Go to room" "button"
    And I switch to "totara_bph4svcr" window
    Then I should see "Behat: Virtual Room Placeholder Page" in the page title
    And I click on "Window close" "button_exact"
    And I switch to the main window
    And I press the "back" button in the browser
    When I click on "Virtual meating spice" "link" in the "Session ongoing 1" "table_row"
    And I click on "Go to room" "button"
    And I switch to "totara_bph4svcr" window
    Then I should see "Behat: Virtual Room Placeholder Page" in the page title
    And I click on "Window close" "button_exact"
    And I switch to the main window
    And I press the "back" button in the browser
    When I click on "Virtual meating spice" "link" in the "Session ongoing 2 future" "table_row"
    And I click on "Go to room" "button"
    And I switch to "totara_bph4svcr" window
    Then I should see "Behat: Virtual Room Placeholder Page" in the page title
    And I click on "Window close" "button_exact"
    And I switch to the main window
    And I press the "back" button in the browser
    When I click on "Virtual meating spice" "link" in the "Session ongoing 2 past" "table_row"
    And I click on "Go to room" "button"
    And I switch to "totara_bph4svcr" window
    Then I should see "Behat: Virtual Room Placeholder Page" in the page title
    And I click on "Window close" "button_exact"
    And I switch to the main window
    And I press the "back" button in the browser
    When I click on "Virtual meating spice" "link" in the "Session past" "table_row"
    And I click on "Go to room" "button"
    And I switch to "totara_bph4svcr" window
    Then I should see "Behat: Virtual Room Placeholder Page" in the page title
    And I click on "Window close" "button_exact"
    And I switch to the main window

    Given I am on "Test seminar booked" seminar homepage
    When I click on "Virtual meating spice" "link" in the "Session near future" "table_row"
    And I click on "Go to room" "button"
    And I switch to "totara_bph4svcr" window
    Then I should see "Behat: Virtual Room Placeholder Page" in the page title
    And I click on "Window close" "button_exact"
    And I switch to the main window
    And I press the "back" button in the browser
    When I click on "Virtual meating spice" "link" in the "Session far future" "table_row"
    And I click on "Go to room" "button"
    And I switch to "totara_bph4svcr" window
    Then I should see "Behat: Virtual Room Placeholder Page" in the page title
    And I click on "Window close" "button_exact"
    And I switch to the main window
    And I press the "back" button in the browser
    When I click on "Virtual meating spice" "link" in the "Session ongoing 1" "table_row"
    And I click on "Go to room" "button"
    And I switch to "totara_bph4svcr" window
    Then I should see "Behat: Virtual Room Placeholder Page" in the page title
    And I click on "Window close" "button_exact"
    And I switch to the main window
    And I press the "back" button in the browser
    When I click on "Virtual meating spice" "link" in the "Session ongoing 2 future" "table_row"
    And I click on "Go to room" "button"
    And I switch to "totara_bph4svcr" window
    Then I should see "Behat: Virtual Room Placeholder Page" in the page title
    And I click on "Window close" "button_exact"
    And I switch to the main window
    And I press the "back" button in the browser
    When I click on "Virtual meating spice" "link" in the "Session ongoing 2 past" "table_row"
    And I click on "Go to room" "button"
    And I switch to "totara_bph4svcr" window
    Then I should see "Behat: Virtual Room Placeholder Page" in the page title
    And I click on "Window close" "button_exact"
    And I switch to the main window
    And I press the "back" button in the browser
    When I click on "Virtual meating spice" "link" in the "Session past" "table_row"
    And I click on "Go to room" "button"
    And I switch to "totara_bph4svcr" window
    Then I should see "Behat: Virtual Room Placeholder Page" in the page title
    And I click on "Window close" "button_exact"
    And I switch to the main window

    Given I am on "Test seminar cancelled" seminar homepage
    When I click on "Virtual meating spice" "link" in the "Session near future" "table_row"
    And I click on "Go to room" "button"
    And I switch to "totara_bph4svcr" window
    Then I should see "Behat: Virtual Room Placeholder Page" in the page title
    And I click on "Window close" "button_exact"
    And I switch to the main window
    And I press the "back" button in the browser
    When I click on "Virtual meating spice" "link" in the "Session far future" "table_row"
    And I click on "Go to room" "button"
    And I switch to "totara_bph4svcr" window
    Then I should see "Behat: Virtual Room Placeholder Page" in the page title
    And I click on "Window close" "button_exact"
    And I switch to the main window
    And I press the "back" button in the browser
    When I click on "Virtual meating spice" "link" in the "Session ongoing 1" "table_row"
    And I click on "Go to room" "button"
    And I switch to "totara_bph4svcr" window
    Then I should see "Behat: Virtual Room Placeholder Page" in the page title
    And I click on "Window close" "button_exact"
    And I switch to the main window
    And I press the "back" button in the browser
    When I click on "Virtual meating spice" "link" in the "Session ongoing 2 future" "table_row"
    And I click on "Go to room" "button"
    And I switch to "totara_bph4svcr" window
    Then I should see "Behat: Virtual Room Placeholder Page" in the page title
    And I click on "Window close" "button_exact"
    And I switch to the main window
    And I press the "back" button in the browser
    When I click on "Virtual meating spice" "link" in the "Session ongoing 2 past" "table_row"
    And I click on "Go to room" "button"
    And I switch to "totara_bph4svcr" window
    Then I should see "Behat: Virtual Room Placeholder Page" in the page title
    And I click on "Window close" "button_exact"
    And I switch to the main window
    And I press the "back" button in the browser
    When I click on "Virtual meating spice" "link" in the "Session past" "table_row"
    And I click on "Go to room" "button"
    And I switch to "totara_bph4svcr" window
    Then I should see "Behat: Virtual Room Placeholder Page" in the page title
    And I click on "Window close" "button_exact"
    And I switch to the main window

    Given I am on "Test seminar poofed" seminar homepage
    When I click on "Virtual meating spice" "link" in the "Session near future" "table_row"
    And I click on "Go to room" "button"
    And I switch to "totara_bph4svcr" window
    Then I should see "Behat: Virtual Room Placeholder Page" in the page title
    And I click on "Window close" "button_exact"
    And I switch to the main window
    And I press the "back" button in the browser
    When I click on "Virtual meating spice" "link" in the "Session far future" "table_row"
    And I click on "Go to room" "button"
    And I switch to "totara_bph4svcr" window
    Then I should see "Behat: Virtual Room Placeholder Page" in the page title
    And I click on "Window close" "button_exact"
    And I switch to the main window
    And I press the "back" button in the browser
    When I click on "Virtual meating spice" "link" in the "Session ongoing 1" "table_row"
    And I click on "Go to room" "button"
    And I switch to "totara_bph4svcr" window
    Then I should see "Behat: Virtual Room Placeholder Page" in the page title
    And I click on "Window close" "button_exact"
    And I switch to the main window
    And I press the "back" button in the browser
    When I click on "Virtual meating spice" "link" in the "Session ongoing 2 future" "table_row"
    And I click on "Go to room" "button"
    And I switch to "totara_bph4svcr" window
    Then I should see "Behat: Virtual Room Placeholder Page" in the page title
    And I click on "Window close" "button_exact"
    And I switch to the main window
    And I press the "back" button in the browser
    When I click on "Virtual meating spice" "link" in the "Session ongoing 2 past" "table_row"
    And I click on "Go to room" "button"
    And I switch to "totara_bph4svcr" window
    Then I should see "Behat: Virtual Room Placeholder Page" in the page title
    And I click on "Window close" "button_exact"
    And I switch to the main window
    And I press the "back" button in the browser
    When I click on "Virtual meating spice" "link" in the "Session past" "table_row"
    And I click on "Go to room" "button"
    And I switch to "totara_bph4svcr" window
    Then I should see "Behat: Virtual Room Placeholder Page" in the page title
    And I click on "Window close" "button_exact"
    And I switch to the main window

    Examples:
      | username |
      | trainer1 |
      | trainer2 |
      | trainer3 |

  Scenario Outline: Look at the virtual room card as manager
    And I log in as "<username>"
    Given I am on "Test seminar vanilla" seminar homepage
    When I click on "Virtual meating spice" "link" in the "Session near future" "table_row"
    Then I should see "Virtual room is unavailable"
    And I press the "back" button in the browser
    When I click on "Virtual meating spice" "link" in the "Session far future" "table_row"
    Then I should see "Virtual room is unavailable"
    And I press the "back" button in the browser
    When I click on "Virtual meating spice" "link" in the "Session ongoing 1" "table_row"
    Then I should see "Virtual room is unavailable"
    And I press the "back" button in the browser
    When I click on "Virtual meating spice" "link" in the "Session ongoing 2 future" "table_row"
    Then I should see "Virtual room is unavailable"
    And I press the "back" button in the browser
    When I click on "Virtual meating spice" "link" in the "Session ongoing 2 past" "table_row"
    Then I should see "Virtual room is unavailable"
    And I press the "back" button in the browser
    When I click on "Virtual meating spice" "link" in the "Session past" "table_row"
    Then I should see "Virtual room is unavailable"
    And I press the "back" button in the browser

    Given I am on "Test seminar booked" seminar homepage
    When I click on "Virtual meating spice" "link" in the "Session near future" "table_row"
    Then I should see "Virtual room is unavailable"
    And I press the "back" button in the browser
    When I click on "Virtual meating spice" "link" in the "Session far future" "table_row"
    Then I should see "Virtual room is unavailable"
    And I press the "back" button in the browser
    When I click on "Virtual meating spice" "link" in the "Session ongoing 1" "table_row"
    Then I should see "Virtual room is unavailable"
    And I press the "back" button in the browser
    When I click on "Virtual meating spice" "link" in the "Session ongoing 2 future" "table_row"
    Then I should see "Virtual room is unavailable"
    And I press the "back" button in the browser
    When I click on "Virtual meating spice" "link" in the "Session ongoing 2 past" "table_row"
    Then I should see "Virtual room is unavailable"
    And I press the "back" button in the browser
    When I click on "Virtual meating spice" "link" in the "Session past" "table_row"
    Then I should see "Virtual room is unavailable"
    And I press the "back" button in the browser

    Given I am on "Test seminar cancelled" seminar homepage
    When I click on "Virtual meating spice" "link" in the "Session near future" "table_row"
    Then I should see "Virtual room is unavailable"
    And I press the "back" button in the browser
    When I click on "Virtual meating spice" "link" in the "Session far future" "table_row"
    Then I should see "Virtual room is unavailable"
    And I press the "back" button in the browser
    When I click on "Virtual meating spice" "link" in the "Session ongoing 1" "table_row"
    Then I should see "Virtual room is unavailable"
    And I press the "back" button in the browser
    When I click on "Virtual meating spice" "link" in the "Session ongoing 2 future" "table_row"
    Then I should see "Virtual room is unavailable"
    And I press the "back" button in the browser
    When I click on "Virtual meating spice" "link" in the "Session ongoing 2 past" "table_row"
    Then I should see "Virtual room is unavailable"
    And I press the "back" button in the browser
    When I click on "Virtual meating spice" "link" in the "Session past" "table_row"
    Then I should see "Virtual room is unavailable"
    And I press the "back" button in the browser

    Given I am on "Test seminar poofed" seminar homepage
    When I click on "Virtual meating spice" "link" in the "Session near future" "table_row"
    Then I should see "Virtual room is unavailable"
    And I press the "back" button in the browser
    When I click on "Virtual meating spice" "link" in the "Session far future" "table_row"
    Then I should see "Virtual room is unavailable"
    And I press the "back" button in the browser
    When I click on "Virtual meating spice" "link" in the "Session ongoing 1" "table_row"
    Then I should see "Virtual room is unavailable"
    And I press the "back" button in the browser
    When I click on "Virtual meating spice" "link" in the "Session ongoing 2 future" "table_row"
    Then I should see "Virtual room is unavailable"
    And I press the "back" button in the browser
    When I click on "Virtual meating spice" "link" in the "Session ongoing 2 past" "table_row"
    Then I should see "Virtual room is unavailable"
    And I press the "back" button in the browser
    When I click on "Virtual meating spice" "link" in the "Session past" "table_row"
    Then I should see "Virtual room is unavailable"
    And I press the "back" button in the browser

    Examples:
      | username |
      | manager1 |
      | manager2 |
