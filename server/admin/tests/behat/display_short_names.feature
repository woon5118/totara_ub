@core @core_admin @javascript
Feature: Display extended course names
  In order to display more info about the courses
  As an admin
  I need to display courses short names along with courses full names

  Background:
    Given I am on a totara site
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course fullname | C_shortname | 0 |
    And I log in as "admin"
    And I am on site homepage
    And I turn editing mode on
    And I add the "Courses" block

  Scenario: Courses list without extended course names (default value)
    Then I should see "Course fullname"
    And I should not see "C_shortname Course fullname"

  Scenario: Courses list with extended course names
    Given I navigate to "Courses > Course settings" in site administration
    And I set the field "Display extended course names" to "1"
    When I press "Save changes"
    And I am on site homepage
    Then I should see "C_shortname Course fullname"
