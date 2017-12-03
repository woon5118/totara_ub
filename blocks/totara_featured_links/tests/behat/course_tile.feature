@block @totara @javascript @block_totara_featured_links
Feature: Test the course tile specific options work as expected
  As a user I should be able to:
    - select a course for the tile to display information about
    - add a progressbar to a course tile

  Background:
    When I log in as "admin"
    And I follow "Dashboard"
    And I click on "Customise this page" "button"
    And I add the "Featured Links" block
    And I click on "Add Tile" "link"

  Scenario: Check Course Tile content form always has to have a course
    When I start watching to see if a new page loads
    And I set the following fields to these values:
      | Tile type | Course Tile |
    Then a new page should have loaded since I started watching
    When I click on "Select Course" "button"
    And I click on "Cancel" "button" in the "Select Course" "totaradialogue"
    And I click on "Save changes" "button"
    Then I should see "Please Select a course"

  Scenario: Check Course Tile selecting a course
    When I create a course with:
      | Course full name  | Course 1 |
      | Course short name | C1 |
    And I follow "Dashboard"
    And I click on "Add Tile" "link"
    And I set the following fields to these values:
      | Tile type | Course Tile |
    And I click on "Select Course" "button"
    And I click on "Miscellaneous" "link" in the "Select Course" "totaradialogue"
    And I click on "Course 1" "link" in the "Select Course" "totaradialogue"
    And I click on "OK" "button" in the "Select Course" "totaradialogue"
    And I click on "Save changes" "button"
    Then "Course 1" "text" should exist in the ".block_totara_featured_links" "css_element"

  Scenario: Check Course Tile can show progress
    Given the following "courses" exist:
      | fullname  | shortname  | enablecompletion |
      | Course 1  | course1    | 1                |
    And the following "course enrolments" exist:
      | user     | course   | role |
      | admin | course1  | student |
    And I click on "Find Learning" in the totara menu
    And I follow "Course 1"
    And I navigate to "Course completion" node in "Course administration"
    And I expand all fieldsets
    And I set the field "Enable" to "1"
    And I press "Save changes"

    When I follow "Dashboard"
    And I click on "Add Tile" "link"
    And I set the following fields to these values:
      | Tile type | Course Tile |
    And I click on "Select Course" "button"
    And I click on "Miscellaneous" "link" in the "Select Course" "totaradialogue"
    And I click on "Course 1" "link" in the "Select Course" "totaradialogue"
    And I click on "OK" "button" in the "Select Course" "totaradialogue"
    And I set the field "Show progress" to "1"
    And I click on "Save changes" "button"

    Then I should see "Course 1" in the "Featured Links" "block"
    And ".progress" "css_element" in the "Featured Links" "block" should be visible