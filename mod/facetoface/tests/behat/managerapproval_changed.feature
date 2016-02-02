@mod @mod_facetoface @totara
Feature: Face-to-face Approval required
  In order to test user's status code when Face-to-face is changed from approval required to not
  As a manager
  I need to change approval required value

  Background:
    Given I am on a totara site
    And the following "users" exist:
      | username | firstname | lastname | email               |
      | student1 | Sam1      | Student1 | student1@example.com |
      | student2 | Sam2      | Student2 | student2@example.com |
      | student3 | Sam3      | Student3 | student3@example.com |
      | student4 | Sam4      | Student4 | student4@example.com |
      | student5 | Sam5      | Student5 | student5@example.com |
      | student6 | Sam6      | Student6 | student6@example.com |

    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
      | student3 | C1     | student        |
      | student4 | C1     | student        |
      | student5 | C1     | student        |
      | student6 | C1     | student        |

    And I log in as "admin"

   # Set manager for Sam1 Student1
    And I navigate to "Browse list of users" node in "Site administration > Users > Accounts"
    And I click on "Sam1 Student1" "link"
    And I click on "Primary position" "link"
    And I press "Choose manager"
    And I click on "Admin User" "link" in the "Choose manager" "totaradialogue"
    And I click on "OK" "button" in the "Choose manager" "totaradialogue"
    And I press "Update position"

    # Set manager for Sam2 Student2
    And I navigate to "Browse list of users" node in "Site administration > Users > Accounts"
    And I click on "Sam2 Student2" "link"
    And I click on "Primary position" "link"
    And I press "Choose manager"
    And I click on "Admin User" "link" in the "Choose manager" "totaradialogue"
    And I click on "OK" "button" in the "Choose manager" "totaradialogue"
    And I press "Update position"

    # Set manager for Sam3 Student3
    And I navigate to "Browse list of users" node in "Site administration > Users > Accounts"
    And I click on "Sam3 Student3" "link"
    And I click on "Primary position" "link"
    And I press "Choose manager"
    And I click on "Admin User" "link" in the "Choose manager" "totaradialogue"
    And I click on "OK" "button" in the "Choose manager" "totaradialogue"
    And I press "Update position"

    # Set manager for Sam4 Student4
    And I navigate to "Browse list of users" node in "Site administration > Users > Accounts"
    And I click on "Sam4 Student4" "link"
    And I click on "Primary position" "link"
    And I press "Choose manager"
    And I click on "Admin User" "link" in the "Choose manager" "totaradialogue"
    And I click on "OK" "button" in the "Choose manager" "totaradialogue"
    And I press "Update position"

    # Set manager for Sam5 Student5
    And I navigate to "Browse list of users" node in "Site administration > Users > Accounts"
    And I click on "Sam5 Student5" "link"
    And I click on "Primary position" "link"
    And I press "Choose manager"
    And I click on "Admin User" "link" in the "Choose manager" "totaradialogue"
    And I click on "OK" "button" in the "Choose manager" "totaradialogue"
    And I press "Update position"

    # Set manager for Sam6 Student6
    And I navigate to "Browse list of users" node in "Site administration > Users > Accounts"
    And I click on "Sam6 Student6" "link"
    And I click on "Primary position" "link"
    And I press "Choose manager"
    And I click on "Admin User" "link" in the "Choose manager" "totaradialogue"
    And I click on "OK" "button" in the "Choose manager" "totaradialogue"
    And I press "Update position"

  @javascript
  Scenario: Update user's status code depending from session capacity when Face-to-face approval required is changed to false
    And I click on "Find Learning" in the totara menu
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Face-to-face" to section "1" and I fill the form with:
      | Name              | Test facetoface name        |
      | Description       | Test facetoface description |
      | Approval required | 1                           |
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
      | capacity              | 4    |
    And I press "Save changes"

    When I click on "Attendees" "link"
    And I click on "Add/remove attendees" "option" in the "#menuf2f-actions" "css_element"
    And I click on "Sam1 Student1, student1@example.com" "option"
    And I press "Add"
    And I wait "1" seconds
    And I press "Save"

    And I click on "Add/remove attendees" "option" in the "#menuf2f-actions" "css_element"
    And I click on "Sam2 Student2, student2@example.com" "option"
    And I press "Add"
    And I wait "1" seconds
    And I press "Save"

    And I click on "Add/remove attendees" "option" in the "#menuf2f-actions" "css_element"
    And I click on "Sam3 Student3, student3@example.com" "option"
    And I press "Add"
    And I press "Save"

    And I click on "Add/remove attendees" "option" in the "#menuf2f-actions" "css_element"
    And I click on "Sam4 Student4, student4@example.com" "option"
    And I press "Add"
    And I press "Save"

    And I click on "Add/remove attendees" "option" in the "#menuf2f-actions" "css_element"
    And I click on "Sam5 Student5, student5@example.com" "option"
    And I press "Add"
    And I press "Save"

    And I click on "Add/remove attendees" "option" in the "#menuf2f-actions" "css_element"
    And I click on "Sam6 Student6, student6@example.com" "option"
    And I press "Add"
    And I press "Save"

    When I follow "Approval required"
    Then I should see "Sam1 Student1"
    And I should see "Sam2 Student2"
    And I should see "Sam3 Student3"
    And I should see "Sam4 Student4"
    And I should see "Sam5 Student5"
    And I should see "Sam6 Student6"

    Then I select to approve "Sam1 Student1"
    And I select to approve "Sam2 Student2"
    And I press "Update requests"
    When I follow "Attendees"
    Then I should see "Sam1 Student1" in the "table.mod-facetoface-attendees" "css_element"
    And I should see "Sam2 Student2" in the "table.mod-facetoface-attendees" "css_element"

    Then I navigate to "Edit settings" node in "Facetoface administration"
    And I set the following fields to these values:
      | Approval required | 0 |
    And I press "Save and display"

    When I click on "Attendees" "link"
    Then I should see "Sam1 Student1" in the "table.mod-facetoface-attendees" "css_element"
    And I should see "Sam2 Student2" in the "table.mod-facetoface-attendees" "css_element"
    And I should see "Sam3 Student3" in the "table.mod-facetoface-attendees" "css_element"
    And I should see "Sam4 Student4" in the "table.mod-facetoface-attendees" "css_element"
    And I should not see "Sam5 Student5" in the "table.mod-facetoface-attendees" "css_element"
    And I should not see "Sam6 Student6" in the "table.mod-facetoface-attendees" "css_element"

    When I follow "Wait-list"
    Then I should not see "Sam1 Student1" in the "table.waitlist" "css_element"
    And I should not see "Sam2 Student2" in the "table.waitlist" "css_element"
    And I should not see "Sam3 Student3" in the "table.mod-facetoface-attendees" "css_element"
    And I should not see "Sam4 Student4" in the "table.mod-facetoface-attendees" "css_element"
    And I should see "Sam5 Student5" in the "table.waitlist" "css_element"
    And I should see "Sam6 Student6" in the "table.waitlist" "css_element"
