@javascript @mod @mod_facetoface @totara
Feature: Check facilitator actions are performed by users with the right permissions
  In order to check users with the right permission could perform action on the facilitator manage/edit pages
  As Admin
  I need to set users with different capabilities and perform facilitator actions as the users

  Background:
    Given I am on a totara site
    And the following "permission overrides" exist:
      | capability                       | permission | role    | contextlevel | reference |
      | totara/core:modconfig            | Allow      | manager | System       |           |
    And the following "users" exist:
      | username  | firstname | lastname | email                |
      | learner1  | learner   | 1        | learner1@example.com |
      | trainer1  | Trainer   | One      | trainer1@example.com |
      | trainer2  | Trainer   | Two      | trainer2@example.com |
      | manager   | Site      | Manager  | manager@example.com  |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "seminars" exist in "mod_facetoface" plugin:
      | name      | course |
      | seminar 1 | C1     |
    And the following "seminar events" exist in "mod_facetoface" plugin:
      | facetoface | details |
      | seminar 1  | event 1 |
    And the following "seminar sessions" exist in "mod_facetoface" plugin:
      | eventdetails | start        | finish       |
      | event 1      | now +2 hours | now +4 hours |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | learner1 | C1     | student        |
      | trainer1 | C1     | editingteacher |
      | trainer2 | C1     | teacher        |
      | manager  | C1     | manager        |
    And the following "seminar signups" exist in "mod_facetoface" plugin:
      | user     | eventdetails |
      | learner1 | event 1      |

    And I log in as "admin"
    And I navigate to "Assign system roles" node in "Site administration > Permissions"
    And I follow "Site Manager"
    And I set the field "Potential users" to "Site Manager (manager@example.com)"
    And I press "Add"
    And I log out

  Scenario: Check manageadhocfacilitators capability for editingteacher role
    Given I log in as "trainer1"
    And I am on "Course 1" course homepage
    And I click on "View all events" "link"
    And I click on "Actions" "button" in the "Upcoming" "table_row"
    And I click on "Edit event" "link" in the "Upcoming" "table_row"
    When I click on "Select facilitators" "link"
    Then I should see "Browse" in the "Choose facilitators" "totaradialogue"
    And I should see "Search" in the "Choose facilitators" "totaradialogue"
    And I should see "Create" in the "Choose facilitators" "totaradialogue"
    And I click on "Create" "link" in the "Choose facilitators" "totaradialogue"
    And I set the following fields to these values:
      | Facilitator Name | Facilitator 2 |
    And I click on "OK" "button" in the "Create new facilitator" "totaradialogue"
    And I should see "Delete" in the "#facilitatorname0_1" "css_element"
    And I should see "Edit facilitator" in the "#facilitatorname0_1" "css_element"
    And I press "Save changes"
    And I log out

  Scenario: Check editingteacher role permission with removed manageadhocfacilitators capability
    Given I log in as "admin"
    And the following "permission overrides" exist:
      | capability                                | permission | role           | contextlevel | reference |
      | mod/facetoface:manageadhocfacilitators    | Prohibit   | editingteacher | Course       | C1        |
    And I log out
    And I log in as "trainer1"
    And I am on "Course 1" course homepage
    And I click on "View all events" "link"
    And I click on "Actions" "button" in the "Upcoming" "table_row"
    And I click on "Edit event" "link" in the "Upcoming" "table_row"
    When I click on "Select facilitators" "link"
    Then I should see "Browse" in the "Choose facilitators" "totaradialogue"
    And I should see "Search" in the "Choose facilitators" "totaradialogue"
    And I should not see "Create" in the "Choose facilitators" "totaradialogue"
    And I click on "Cancel" "button" in the "Choose facilitators" "totaradialogue"
    And I press "Cancel"
    And I log out

  Scenario: Check manageadhocfacilitators capability for editingteacher role
    Given I log in as "trainer2"
    And I am on "Course 1" course homepage
    And I click on "View all events" "link"
    And I click on "Actions" "button" in the "Upcoming" "table_row"
    And I click on "Edit event" "link" in the "Upcoming" "table_row"
    When I click on "Select facilitators" "link"
    Then I should see "Browse" in the "Choose facilitators" "totaradialogue"
    And I should see "Search" in the "Choose facilitators" "totaradialogue"
    And I should see "Create" in the "Choose facilitators" "totaradialogue"
    And I click on "Create" "link" in the "Choose facilitators" "totaradialogue"
    And I set the following fields to these values:
      | Facilitator Name | Facilitator 2 |
    And I click on "OK" "button" in the "Create new facilitator" "totaradialogue"
    And I should see "Delete" in the "#facilitatorname0_1" "css_element"
    And I should see "Edit facilitator" in the "#facilitatorname0_1" "css_element"
    And I press "Save changes"
    And I log out

  Scenario: Check editingteacher role permission with removed manageadhocfacilitators capability
    Given I log in as "admin"
    And the following "permission overrides" exist:
      | capability                                | permission | role    | contextlevel | reference |
      | mod/facetoface:manageadhocfacilitators    | Prohibit   | teacher | Course       | C1        |
    And I log out
    And I log in as "trainer2"
    And I am on "Course 1" course homepage
    And I click on "View all events" "link"
    And I click on "Actions" "button" in the "Upcoming" "table_row"
    And I click on "Edit event" "link" in the "Upcoming" "table_row"
    When I click on "Select facilitators" "link"
    Then I should see "Browse" in the "Choose facilitators" "totaradialogue"
    And I should see "Search" in the "Choose facilitators" "totaradialogue"
    And I should not see "Create" in the "Choose facilitators" "totaradialogue"
    And I click on "Cancel" "button" in the "Choose facilitators" "totaradialogue"
    And I press "Cancel"
    And I log out

  Scenario: Check manageadhocfacilitators/managesitewidefacilitators capabilities for manager
    Given I log in as "manager"
    And I am on "Course 1" course homepage
    And I click on "View all events" "link"
    And I click on "Actions" "button" in the "Upcoming" "table_row"
    And I click on "Edit event" "link" in the "Upcoming" "table_row"
    When I click on "Select facilitators" "link"
    Then I should see "Browse" in the "Choose facilitators" "totaradialogue"
    And I should see "Search" in the "Choose facilitators" "totaradialogue"
    And I should see "Create" in the "Choose facilitators" "totaradialogue"
    And I click on "Create" "link" in the "Choose facilitators" "totaradialogue"
    And I set the following fields to these values:
      | Facilitator Name | Facilitator |
    And I click on "OK" "button" in the "Create new facilitator" "totaradialogue"
    And I should see "Delete" in the "#facilitatorname0_1" "css_element"
    And I should see "Edit facilitator" in the "#facilitatorname0_1" "css_element"
    And I press "Save changes"

    When I navigate to "Facilitators" node in "Seminar administration"
    Then I press "Add a new facilitator"
    And I set the following fields to these values:
      | Facilitator Name | Facilitator 1 |
    And I press "Add a facilitator"
    And I should see "Facilitator 1"

    And I am on "Course 1" course homepage
    And I click on "View all events" "link"
    And I click on "Actions" "button" in the "Upcoming" "table_row"
    And I click on "Edit event" "link" in the "Upcoming" "table_row"
    When I click on "Select facilitators" "link"
    Then I should see "Browse" in the "Choose facilitators" "totaradialogue"
    And I should see "Search" in the "Choose facilitators" "totaradialogue"
    And I should see "Create" in the "Choose facilitators" "totaradialogue"
    And I click on "Facilitator 1" "text" in the "Choose facilitators" "totaradialogue"
    And I click on "OK" "button" in the "Choose facilitators" "totaradialogue"
    And I should see "Delete" in the "#facilitatorname0_2" "css_element"
    And I press "Save changes"

    When I navigate to "Facilitators" node in "Site administration > Seminars"
    Then I press "Add a new facilitator"
    And I set the following fields to these values:
      | Facilitator Name | Facilitator 2 |
    And I press "Add a facilitator"
    And I should see "Facilitator 2"

    And I am on "Course 1" course homepage
    And I click on "View all events" "link"
    And I click on "Actions" "button" in the "Upcoming" "table_row"
    And I click on "Edit event" "link" in the "Upcoming" "table_row"
    When I click on "Select facilitators" "link"
    Then I should see "Browse" in the "Choose facilitators" "totaradialogue"
    And I should see "Search" in the "Choose facilitators" "totaradialogue"
    And I should see "Create" in the "Choose facilitators" "totaradialogue"
    And I click on "Facilitator 2" "text" in the "Choose facilitators" "totaradialogue"
    And I click on "OK" "button" in the "Choose facilitators" "totaradialogue"
    And I should see "Delete" in the "#facilitatorname0_3" "css_element"
    And I press "Save changes"
    And I log out