@javascript @mod @mod_facetoface @totara
Feature: Add - Remove Face to face attendees
  In order to test the add/remove Face to face attendees
  As admin
  I need to add and remove attendees to/from a face to face session

  Background:
    Given I am on a totara site
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Sam1      | Student1 | student1@example.com |
      | student2 | Sam2      | Student2 | student2@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |

  Scenario: Add users to a face to face session with dates
    Given I log in as "admin"
    And I click on "Find Learning" in the totara menu
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Face-to-face" to section "1" and I fill the form with:
      | Name        | Test facetoface name        |
      | Description | Test facetoface description |
    And I follow "View all sessions"
    And I follow "Add a new session"
    And I set the following fields to these values:
      | datetimeknown         | Yes  |
      | timestart[0][day]     | 1    |
      | timestart[0][month]   | 1    |
      | timestart[0][year]    | 2020 |
      | timestart[0][hour]    | 11   |
      | timestart[0][minute]  | 00   |
      | timefinish[0][day]    | 1    |
      | timefinish[0][month]  | 1    |
      | timefinish[0][year]   | 2020 |
      | timefinish[0][hour]   | 12   |
      | timefinish[0][minute] | 00   |
      | capacity              | 1    |
    And I press "Save changes"

    When I click on "Attendees" "link"
    And I click on "Add users" "option" in the "#menuf2f-actions" "css_element"
    And I click on "Sam1 Student1, student1@example.com" "option"
    And I press "Add"
    And I wait "1" seconds
    And I press "Continue"
    And I press "Confirm"
    Then I should see "Sam1 Student1"

  Scenario: Add and remove users to a face to face session without dates (waitlist)
    Given I log in as "admin"
    And I click on "Find Learning" in the totara menu
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Face-to-face" to section "1" and I fill the form with:
      | Name        | Test facetoface name        |
      | Description | Test facetoface description |
    And I follow "View all sessions"
    And I follow "Add a new session"
    And I set the following fields to these values:
      | datetimeknown         | No   |
      | capacity              | 1    |
    And I press "Save changes"

    When I click on "Attendees" "link"
    And I click on "Add users" "option" in the "#menuf2f-actions" "css_element"
    And I click on "Sam1 Student1, student1@example.com" "option"
    And I press "Add"
    And I wait "1" seconds
    And I press "Continue"
    And I press "Confirm"
    And I click on "Wait-list" "link"
    Then I should see "Sam1 Student1"

  Scenario: Add users by username via textarea
    Given I log in as "admin"
    And I click on "Find Learning" in the totara menu
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Face-to-face" to section "1" and I fill the form with:
      | Name        | Test facetoface name        |
      | Description | Test facetoface description |
    And I follow "View all sessions"
    And I follow "Add a new session"
    And I set the following fields to these values:
      | datetimeknown         | No   |
      | capacity              | 1    |
    And I press "Save changes"

    When I click on "Attendees" "link"
    And I click on "Add users via list of IDs" "option" in the "#menuf2f-actions" "css_element"
    # By default user is expected to separate ID's by newline, but comma is also supported.
    And I set the following fields to these values:
      | User identifier | Username |
      | csvinput        | student1,student2 |
    And I press "Continue"
    And I click on "Change selected users" "link"
    Then the following fields match these values:
      | User identifier | Username |
      | csvinput        | student1,student2 |
    And I press "Continue"
    And I press "Confirm"
    And I click on "Wait-list" "link"
    And I should see "Sam1 Student1"
    And I should see "Sam2 Student2"

  Scenario: Add users by email via textarea
    Given I log in as "admin"
    And I click on "Find Learning" in the totara menu
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Face-to-face" to section "1" and I fill the form with:
      | Name        | Test facetoface name        |
      | Description | Test facetoface description |
    And I follow "View all sessions"
    And I follow "Add a new session"
    And I set the following fields to these values:
      | datetimeknown         | No   |
      | capacity              | 1    |
    And I press "Save changes"

    When I click on "Attendees" "link"
    And I click on "Add users via list of IDs" "option" in the "#menuf2f-actions" "css_element"
    # By default user separate ID's by newline, but comma is also supported.
    And I set the following fields to these values:
      | User identifier | Email address |
      | csvinput        | student1@example.com,student2@example.com |
    And I press "Continue"
    And I click on "Change selected users" "link"
    Then the following fields match these values:
      | User identifier | Email address |
      | csvinput        | student1@example.com,student2@example.com |
    And I press "Continue"
    And I press "Confirm"
    And I click on "Wait-list" "link"
    And I should see "Sam1 Student1"
    And I should see "Sam2 Student2"

  @_file_upload
  Scenario: Add users via file upload and then remove
    Given I log in as "admin"
    And I click on "Find Learning" in the totara menu
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Face-to-face" to section "1" and I fill the form with:
      | Name        | Test facetoface name        |
      | Description | Test facetoface description |
    And I follow "View all sessions"
    And I follow "Add a new session"
    And I fill facetoface session with relative date in form data:
      | datetimeknown         | Yes              |
      | sessiontimezone[0]    | Pacific/Auckland |
      | timestart[0][day]     | +1               |
      | timestart[0][month]   | 0                |
      | timestart[0][year]    | 0                |
      | timestart[0][hour]    | 0                |
      | timestart[0][minute]  | 0                |
      | timefinish[0][day]    | +1               |
      | timefinish[0][month]  | 0                |
      | timefinish[0][year]   | 0                |
      | timefinish[0][hour]   | +1               |
      | timefinish[0][minute] | 00               |
      | capacity              | 2                |
    And I press "Save changes"

    When I click on "Attendees" "link"
    And I click on "Add users via file upload" "option" in the "#menuf2f-actions" "css_element"
    And I upload "mod/facetoface/tests/fixtures/f2f_attendees.csv" file to "Text file" filemanager
    And I press "Continue"
    And I press "Confirm"
    #And I click on "Wait-list" "link"
    And I should see "Sam1 Student1"
    And I should see "Sam2 Student2"

    # Remove users (continue with just added users in Attendees tab)
    #When I click on "All" "link" in the ".mod-facetoface-attendees" "css_element"
    #And I click on "Confirm" "option" in the "#menuf2f-actions" "css_element"
    #And I wait "1" seconds
    #And I press "Yes"
    #And I click on "Attendees" "link"
    When I click on "Remove users" "option" in the "#menuf2f-actions" "css_element"
    And I click on "Sam1 Student1, student1@example.com" "option"
    And I press "Add"
    And I wait "1" seconds
    And I press "Continue"
    And I press "Confirm"
    Then I should see "Sam2 Student2"
    And I should not see "Sam1 Student1"



