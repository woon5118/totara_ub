@block @block_online_users @javascript
Feature: The online users block allow you to see who is currently online on dashboard
  There should be some commonality for the users to show up
  In order to use the online users block on the dashboard
  As a user
  I can view the online users block on my dashboard

  Background:
    Given I disable the "engage_resources" advanced feature
    And I disable the "container_workspace" advanced feature
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
      | student2 | Student   | 2        | student2@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user | course | role           |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student        |
      | student2 | C1 | student        |

  Scenario: View the online users block on the dashboard and see myself
    Given I log in as "teacher1"
    And I am on "Dashboard" page
    And I press "Customise this page"
    And I add the "Online users" block
    Then I should see "Teacher 1" in the "Online users" "block"

  Scenario: View the online users block on the dashboard and see other logged in users
    Given I log in as "student2"
    And I log out
    And I log in as "student1"
    And I log out
    When  I log in as "teacher1"
    And I am on "Dashboard" page
    And I press "Customise this page"
    And I add the "Online users" block
    Then I should see "Teacher 1" in the "Online users" "block"
    And I should see "Student 1" in the "Online users" "block"
    And I should see "Student 2" in the "Online users" "block"
