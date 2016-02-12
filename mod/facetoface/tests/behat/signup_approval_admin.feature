@mod @mod_facetoface @totara
Feature: Signup Admin Approval
  In order to signup to a classroom connect
  As a learner
  I need to request approval from the manager and an admin

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
      | manager | CCC    | teacher        |
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
    And I click on "s__facetoface_approvaloptions[approval_none]" "checkbox"
    And I click on "s__facetoface_approvaloptions[approval_self]" "checkbox"
    And I click on "s__facetoface_approvaloptions[approval_manager]" "checkbox"
    And I click on "s__facetoface_approvaloptions[approval_admin]" "checkbox"
    And I press "Save changes"
    And I click on "Find Learning" in the totara menu
    And I follow "Classroom Connect Course"
    And I turn editing mode on
    And I add a "Face-to-face" to section "1" and I fill the form with:
      | Name              | Classroom Connect       |
      | Description       | Classroom Connect Tests |
      | approvaloptions   | approval_admin          |
    And I follow "View all sessions"
    And I navigate to "Edit settings" node in "Facetoface administration"
    And I expand all fieldsets
    And I click on "addapprovaladmins" "button"
    And I click on "Larry Lar" "link" in the "Select activity level approvers" "totaradialogue"
    And I click on "Save" "button" in the "Select activity level approvers" "totaradialogue"
    And I press "Save and display"
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
      #      | Enable sign up period open       | <periodopen>  |
      #      | registrationtimestart[day]       | 30            |
      #      | registrationtimestart[month]     | June          |
      #      | registrationtimestart[year]      | <startyear>   |
      #      | registrationtimestart[hour]      | 01            |
      #      | registrationtimestart[minute]    | 00            |
      #      | registrationtimestart[timezone]  | <startzone>   |
      | capacity              | 10   |
    And I press "Save changes"
    And I log out

  @javascript
  Scenario: Student signs up with no manager assigned
    When I log in as "sally"
    And I click on "Find Learning" in the totara menu
    And I follow "Classroom Connect Course"
    And I should see "Sign-up"
    And I follow "Sign-up"
    And I should see "Admin Approval"
    And I press "Request approval"
    Then I should see "This Face-to-face requires manager approval, you are currently not assigned to a manager in the system. Please contact the site administrator."

  @javascript
  Scenario: Student signs up with no manager assigned with manager select enabled
    When I log in as "admin"
    And I navigate to "General Settings" node in "Site administration > Plugins > Activity modules > Face-to-face"
    And I click on "s__facetoface_managerselect" "checkbox"
    And I press "Save changes"
    And I log out

    And I log in as "sally"
    And I click on "Find Learning" in the totara menu
    And I follow "Classroom Connect Course"
    And I should see "Sign-up"
    And I follow "Sign-up"
    And I should see "Admin Approval"
    And I press "Request approval"
    Then I should see "This Face-to-face requires manager approval, please select a manager to request approval"

    And I press "Choose manager"
    And I click on "Cassy Cas" "link" in the "Select manager" "totaradialogue"
    And I click on "OK" "button" in the "Select manager" "totaradialogue"
    And I press "Request approval"
    Then I should see "Your booking has been completed but requires approval from your manager"

    And I log out
    And I log in as "manager"
    And I click on "My Learning" in the totara menu
    And I click on "View all tasks" "link"
    And I click on "Attendees" "link" in the "1 January 2020" "table_row"
    # Then I should see "Sally Sal"
    # And I approve Sally
    # And I check she is on the attendees list
    And I pause

  @javascript @RUNME
  Scenario: Student gets approved through both steps of the 2 stage approval
    When I log in as "jimmy"
    And I click on "Find Learning" in the totara menu
    And I follow "Classroom Connect Course"
    And I should see "Sign-up"
    And I follow "Sign-up"
    And I should see "Admin Approval"
    And I press "Request approval"
    And I log out

    And I log in as "manager"
    And I click on "My Learning" in the totara menu
    Then I should see "Face-to-face booking admin request"
    And I click on "View all tasks" "link"
    And I click on "Attendees" "link" in the "1 January 2020" "table_row"
    Then I should see "Jimmy Jim" in the ".lastrow" "css_element"

    And I click on "requests[8]" "radio" in the ".lastrow .lastcol" "css_element"
    And I pause
    # And I approve jimmy
    # Then I no longer have any staff on the approval page
    And I log out

    And I log in as "actapprover"
    And I click on "My Learning" in the totara menu
    Then I should see "Face-to-face booking admin request"
    And I click on "View all tasks" "link"
    And I click on "Attendees" "link" in the "1 January 2020" "table_row"
    Then I should see "Jimmy Jim"

    And I pause
    # And I approve jimmy
    # Then I no longer have any staff on the approval page
    And I log out

    And I log in as "admin"
    And I click on "My Learning" in the totara menu
    Then I should see "Face-to-face booking admin request"
    And I click on "View all tasks" "link"
    And I click on "Attendees" "link" in the "1 January 2020" "table_row"
