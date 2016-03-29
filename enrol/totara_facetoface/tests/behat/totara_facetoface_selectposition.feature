@enrol @javascript @totara @enrol_totara_facetoface
Feature: Users can enrol on courses that have position signup enabled and get signed for appropriate sessions
  In order to participate in courses with seminars
  As a user
  I need to sign up to seminars when enrolling on the course

  Background:
    Given I am on a totara site
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@asd.com |
      | student1 | Student | 1 | student1@asd.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
      | Course 2 | C2 | topics |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | teacher1 | C2 | editingteacher |

    And I log in as "admin"
    And I expand "Site administration" node
    And I expand "Plugins" node
    And I expand "Enrolments" node
    And I follow "Manage enrol plugins"
    And I click on "Enable" "link" in the "Face-to-face direct enrolment" "table_row"
    And I expand "Activity modules" node
    And I expand "Face-to-face" node
    And I follow "Global settings"
    And I set the field "Select position on signup" to "checked_checkbox"
    And I press "Save changes"
    And I log out
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Face-to-face" to section "1" and I fill the form with:
      | Name        | Test facetoface name 1       |
      | Description | Test facetoface description 1 |
      | Select position on signup | 1 |
      | Prevent signup if no position is selected or can be found | 0 |
    And I follow "Test facetoface name 1"
    And I follow "Add a new event"
    And I click on "Edit date" "link"
    And I set the following fields to these values:
      | timestart[day]     | 1    |
      | timestart[month]   | 1    |
      | timestart[year]    | 2020 |
      | timestart[hour]    | 11   |
      | timestart[minute]  | 00   |
      | timefinish[day]    | 1    |
      | timefinish[month]  | 1    |
      | timefinish[year]   | 2020 |
      | timefinish[hour]   | 12   |
      | timefinish[minute] | 00   |
    And I click on "OK" "button" in the "Select date" "totaradialogue"
    And I press "Save changes"
    And I log out
    And I log in as "teacher1"
    And I follow "Course 2"
    And I turn editing mode on
    And I add a "Face-to-face" to section "1" and I fill the form with:
      | Name        | Test facetoface name 1       |
      | Description | Test facetoface description 1 |
      | Select position on signup | 1 |
      | Prevent signup if no position is selected or can be found | 1 |
    And I follow "Test facetoface name 1"
    And I follow "Add a new event"
    And I click on "Edit date" "link"
    And I set the following fields to these values:
      | timestart[day]     | 1    |
      | timestart[month]   | 1    |
      | timestart[year]    | 2020 |
      | timestart[hour]    | 11   |
      | timestart[minute]  | 00   |
      | timefinish[day]    | 1    |
      | timefinish[month]  | 1    |
      | timefinish[year]   | 2020 |
      | timefinish[hour]   | 12   |
      | timefinish[minute] | 00   |
    And I click on "OK" "button" in the "Select date" "totaradialogue"
    And I press "Save changes"
    And I log out

  Scenario: Enrol using face to face direct where position asked for but not required
    Given I log in as "teacher1"
    And I follow "Course 1"
    When I add "Face-to-face direct enrolment" enrolment method with:
      | Custom instance name | Test student enrolment |
      | Automatically sign users up to face to face events | 0 |
    And I log out
    And I log in as "student1"
    And I click on "Find Learning" in the totara menu
    And I follow "Course 1"
    And I click on "Sign-up" "link" in the "1 January 2020" "table_row"
    And I press "Sign-up"
    Then I should see "Your booking has been completed."

  Scenario: Enrol using face to face direct where position asked for and required
    Given I log in as "teacher1"
    And I follow "Course 2"
    When I add "Face-to-face direct enrolment" enrolment method with:
      | Custom instance name | Test student enrolment |
      | Automatically sign users up to face to face events | 0 |
    And I log out
    And I log in as "student1"
    And I click on "Find Learning" in the totara menu
    And I follow "Course 2"
    And I click on "Sign-up" "link" in the "1 January 2020" "table_row"
    Then I should see "You must have a suitable position assigned to sign up for this facetoface activity."
