@totara @totara_reportbuilder @totara_cohort @mod_facetoface @javascript
Feature: Test the visibility to see the seminar events report depending on the course audience visibility setting
  In order to test the visibility
  As an admin
  I need to create a course with audience visibility setting, create seminar with event, create seminar event report

  Background:
    Given I am on a totara site
    And I log in as "admin"
    And I set the following administration settings values:
      | Enable audience-based visibility | 1 |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Sam1      | Student1 | student1@example.com |
    And the following "courses" exist:
      | fullname      | shortname | category |
      | Course 17392A | C17392A   | 0        |
      | Course 17392B | C17392B   | 0        |
    And the following "activities" exist:
      | activity   | name           | course  | idnumber |
      | facetoface | Seminar 17392A | C17392A | S17392A  |
      | facetoface | Seminar 17392B | C17392B | S17392B  |
    And the following "standard_report" exist in "totara_reportbuilder" plugin:
      | fullname       | shortname      | source            | accessmode |
      | Seminar Events | seminar_events | facetoface_events | 0          |

    And I am on "Course 17392A" course homepage
    And I follow "Seminar 17392A"
    And I follow "Add event"
    And I press "Save changes"

    And I am on "Course 17392B" course homepage
    And I follow "Seminar 17392B"
    And I follow "Add event"
    And I press "Save changes"
    And I log out

  Scenario: Learner see Seminar events report with different course visibility
    # Learner see all seminars with visibility All users for all courses
    Given I log in as "student1"
    And I click on "Reports" in the totara menu
    And I follow "Seminar Events"
    And I should see "2 records shown" in the ".rb-record-count" "css_element"
    And I should see "Course 17392A"
    And I should see "Seminar 17392A"
    And I should see "Course 17392B"
    And I should see "Seminar 17392B"
    And I log out

    #  Learner see seminar with visibility All users and should not see a seminar with visibility No users
    And I log in as "admin"
    And I am on "Course 17392B" course homepage
    And I navigate to "Edit settings" node in "Course administration"
    And I set the field "Visibility" to "No users"
    And I press "Save and display"
    And I log out

    And I log in as "student1"
    And I click on "Reports" in the totara menu
    And I follow "Seminar Events"
    And I should see "1 record shown" in the ".rb-record-count" "css_element"
    And I should see "Course 17392A"
    And I should see "Seminar 17392A"
    And I should not see "Course 17392B"
    And I should not see "Seminar 17392B"
    And I log out
