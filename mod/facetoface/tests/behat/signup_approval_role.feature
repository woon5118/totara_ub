@mod @mod_facetoface @totara
Feature: Signup Role Approval
  In order to signup to classroom connect
  As a learner
  I need to request approval from a session role

  Background:
    Given I am on a totara site
    And the following "users" exist:
      | username    | firstname | lastname | email              |
      | sysapprover | Terry     | Ter      | terry@example.com  |
      | actapprover | Larry     | Lar      | larry@example.com  |
      | teacher     | Freddy    | Fred     | freddy@example.com |
      | trainer     | Benny     | Ben      | benny@example.com  |
      | manager     | Cassy     | Cas      | cassy@example.com  |
      | jimmy       | Jimmy     | Jim      | jimmy@example.com  |
      | timmy       | Timmy     | Tim      | timmy@example.com  |
      | sammy       | Sammy     | Sam      | sammy@example.com  |
      | sally       | Sally     | Sal      | sally@example.com  |
    And the following "courses" exist:
      | fullname                 | shortname | category |
      | Classroom Connect Course | CCC       | 0        |
    And the following "course enrolments" exist:
      | user    | course | role           |
      | teacher | CCC    | editingteacher |
      | trainer | CCC    | teacher        |
      | jimmy   | CCC    | student        |
      | timmy   | CCC    | student        |
      | sammy   | CCC    | student        |
      | sally   | CCC    | student        |
    And the following position assignments exist:
      | user  | manager |
      | jimmy | manager |
      | timmy | manager |
      | sammy | manager |
    And I log in as "admin"
    And I navigate to "General Settings" node in "Site administration > Plugins > Activity modules > Face-to-face"
    And I click on "s__facetoface_session_roles_4"
    And I press "Save changes"
    And I click on "s__facetoface_approvaloptions[approval_role_4]" "checkbox"
    And I press "Save changes"
    And I click on "Find Learning" in the totara menu
    And I follow "Classroom Connect Course"
    And I turn editing mode on
    And I add a "Face-to-face" to section "1" and I fill the form with:
      | Name                | Classroom Connect       |
      | Description         | Classroom Connect Tests |
      | approvaloptions     | approval_admin          |
    And I follow "View all sessions"
    And I follow "Add a new session"
    And I set the following fields to these values:
      | datetimeknown         | Yes  |
      | timestart[0][day]     | 1    |
      | timestart[0][month]   | 1    |
      | timestart[0][year]    | 2020 |
      | timestart[0][hour]    | 10   |
      | timestart[0][minute]  | 00   |
      | timefinish[0][day]    | 1    |
      | timefinish[0][month]  | 1    |
      | timefinish[0][year]   | 2020 |
      | timefinish[0][hour]   | 12   |
      | timefinish[0][minute] | 00   |
      | capacity              | 10   |
  And I press "Save changes"

  @javascript
  Scenario: Student signs up a with no roles assigned
    When I log in as "student1"
    And I click on "Find Learning" in the totara menu
    And I follow "Course 1"
    And I should see "Sign-up"
    And I follow "Sign-up"
    And I should see "Manager Approval"
    # And I click request approval
    And I should see "You are currently not assigned to a manager in the system. Please contact the site administrator."
    # TODO - should this throw errors?


  @javascript
  Scenario: Student gets approved through role approval
    When I log in as "jimmy"
    And I click on "Find Learning" in the totara menu
    And I follow "Classroom Connect Course"
    And I should see "Sign-up"
    And I follow "Sign-up"
    And I should see "Admin Approval"
    And I log out

    And I log in as "manager"
    # TODO - and I confirm I can't see anything
    # TODO - and I no longer have any staff on the approval page
    And I log out

    And I log in as "trainer"
    # TODO - and I approve jimmy
    And I log out

    And I log in as "admin"
    # TODO - and I check jimmy is an attendee

