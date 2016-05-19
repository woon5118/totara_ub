@mod @totara @mod_facetoface
Feature: Add a seminar with select position
  In order to run a seminar
  As a teacher
  I need to create a seminar activity

  Background:
    Given I am on a totara site
    And the following "users" exist:
      | username | firstname | lastname | email               |
      | teacher1 | Terry1    | Teacher1 | teacher1@moodle.com |
      | student1 | Sam1      | Student1 | student1@moodle.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And I log in as "admin"
    And I navigate to "Global settings" node in "Site administration > Seminars"
    And I set the following fields to these values:
      | Select position on signup | 1 |
    And I press "Save changes"
    And I log out

  @javascript
  Scenario: Add and configure a seminar activity with a single session and position asked for but not mandated then sign up as user with no pos
    When I log in as "teacher1"
    And I click on "Find Learning" in the totara menu
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Seminar" to section "1" and I fill the form with:
      | Name        | Test seminar name        |
      | Description | Test seminar description |
      | Select position on signup | 1             |
    And I follow "View all events"
    And I follow "Add a new event"
    And I click on "Edit date" "link"
    And I set the following fields to these values:
      | timestart[day]       | 1    |
      | timestart[month]     | 1    |
      | timestart[year]      | 2020 |
      | timestart[hour]      | 11   |
      | timestart[minute]    | 0    |
      | timefinish[day]      | 1    |
      | timefinish[month]    | 1    |
      | timefinish[year]     | 2020 |
      | timefinish[hour]     | 12   |
      | timefinish[minute]   | 0    |
    And I press "OK"
    And I press "Save changes"
    And I should see "1 January 2020"
    And I log out
    And I log in as "student1"
    And I click on "Find Learning" in the totara menu
    And I follow "Course 1"
    And I follow "View all events"
    And I follow "Sign-up"
    And I press "Sign-up"
    Then I should see "Your booking has been completed."

  @javascript
  Scenario: Add and configure a seminar activity with a single session and position asked for but not mandated then sign in as user with two positions and check attendee list reflects this and the selected position can be updated
    Given the following "position" frameworks exist:
      | fullname      | idnumber |
      | PosHierarchy1 | FW001    |
    And the following "position" hierarchy exists:
      | framework | idnumber | fullname   |
      | FW001     | POS001   | Position1  |
      | FW001     | POS002   | Position2  |
    And the following position assignments exist:
      | user     | position | type      |
      | student1 | POS001   | primary   |
      | student1 | POS002   | secondary |
    And I log in as "teacher1"
    And I click on "Find Learning" in the totara menu
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Seminar" to section "1" and I fill the form with:
      | Name        | Test seminar name        |
      | Description | Test seminar description |
      | Select position on signup | 1             |
    And I follow "View all events"
    And I follow "Add a new event"
    And I click on "Edit date" "link"
    And I set the following fields to these values:
      | timestart[day]       | 1    |
      | timestart[month]     | 1    |
      | timestart[year]      | 2020 |
      | timestart[hour]      | 11   |
      | timestart[minute]    | 0    |
      | timefinish[day]      | 1    |
      | timefinish[month]    | 1    |
      | timefinish[year]     | 2020 |
      | timefinish[hour]     | 12   |
      | timefinish[minute]   | 0    |
    And I press "OK"
    And I press "Save changes"
    And I should see "1 January 2020"
    And I log out
    When I log in as "student1"
    And I click on "Find Learning" in the totara menu
    And I follow "Course 1"
    And I follow "View all events"
    And I follow "Sign-up"
    And I set the following fields to these values:
      | Select a position | Secondary position (Position2) |
    And I press "Sign-up"
    And I log out
    And I log in as "teacher1"
    And I click on "Find Learning" in the totara menu
    And I follow "Course 1"
    And I follow "View all events"
    And I follow "Attendees"
    And I should see "Position2"
    And I log out
    And I log in as "admin"
    And I click on "Find Learning" in the totara menu
    And I follow "Course 1"
    And I follow "View all events"
    And I follow "Attendees"
    And I should see "Position2"
    And I click on ".attendee-edit-position" "css_element"
    And I set the following fields to these values:
      | selectposition | Primary position (Position1) |
    And I press "Update position"
    And I should see "Position1"

  @javascript
  Scenario: Add and configure a seminar activity with a single session and position asked for and mandated then try to sign up as user with no pos
    When I log in as "teacher1"
    And I click on "Find Learning" in the totara menu
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Seminar" to section "1" and I fill the form with:
      | Name        | Test seminar name        |
      | Description | Test seminar description |
      | Select position on signup | 1             |
      | Prevent signup if no position is selected or can be found | 1             |
    And I follow "View all events"
    And I follow "Add a new event"
    And I click on "Edit date" "link"
    And I set the following fields to these values:
      | timestart[day]       | 1    |
      | timestart[month]     | 1    |
      | timestart[year]      | 2020 |
      | timestart[hour]      | 11   |
      | timestart[minute]    | 0    |
      | timefinish[day]      | 1    |
      | timefinish[month]    | 1    |
      | timefinish[year]     | 2020 |
      | timefinish[hour]     | 12   |
      | timefinish[minute]   | 0    |
    And I press "OK"
    And I press "Save changes"
    And I should see "1 January 2020"
    And I log out
    And I log in as "student1"
    And I click on "Find Learning" in the totara menu
    And I follow "Course 1"
    And I follow "View all events"
    And I follow "Sign-up"
    Then I should see "You must have a suitable position assigned to sign up for this seminar activity"

  @javascript
  Scenario: Add and configure a seminar activity with a single session and position asked for then sign in as user with two positions and check user shown to correct manager.
    Given the following "position" frameworks exist:
      | fullname      | idnumber |
      | PosHierarchy1 | FW001    |
    And the following "position" hierarchy exists:
      | framework | idnumber | fullname   |
      | FW001     | POS001   | Position1  |
      | FW001     | POS002   | Position2  |
    And the following position assignments exist:
      | user     | position | type      |
      | student1 | POS001   | primary   |
      | student1 | POS002   | secondary |
    And I log in as "teacher1"
    And I click on "Find Learning" in the totara menu
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Seminar" to section "1" and I fill the form with:
      | Name        | Test seminar name        |
      | Description | Test seminar description |
      | Select position on signup | 1             |
    And I follow "View all events"
    And I follow "Add a new event"
    And I click on "Edit date" "link"
    And I set the following fields to these values:
      | timestart[day]       | 1    |
      | timestart[month]     | 1    |
      | timestart[year]      | 2020 |
      | timestart[hour]      | 11   |
      | timestart[minute]    | 0    |
      | timefinish[day]      | 1    |
      | timefinish[month]    | 1    |
      | timefinish[year]     | 2020 |
      | timefinish[hour]     | 12   |
      | timefinish[minute]   | 0    |
    And I press "OK"
    And I press "Save changes"
    And I should see "1 January 2020"
    And I log out
    When I log in as "student1"
    And I click on "Find Learning" in the totara menu
    And I follow "Course 1"
    And I follow "View all events"
    And I follow "Sign-up"
    And I set the following fields to these values:
      | Select a position | Secondary position (Position2) |
    And I press "Sign-up"
    And I log out
    And I log in as "teacher1"
    And I click on "Find Learning" in the totara menu
    And I follow "Course 1"
    And I follow "View all events"
    And I follow "Attendees"
    Then I should see "Position2"
    And I log out

    And I log in as "admin"
    And I navigate to "Manage reports" node in "Site administration > Reports > Report builder"
    And I set the field "Report Name" to "F2F sessions"
    And I set the field "Source" to "Seminar Sign-ups"
    And I press "Create report"
    And I click on "Columns" "link" in the ".tabtree" "css_element"
    And I add the "Position on sign up" column to the report
    And I add the "Position Type on sign up" column to the report

    When I navigate to my "F2F sessions" report
    Then I should see "Position2"
    And I should see "Secondary position"
