@core @theme @theme_basis @javascript
Feature: I should be able to still do essential stuff using basis theme
  Since ventura, basis theme should still be able to do essential stuff
  As a user
  I need to confirm that basis is still working as expected

  Background:
    Given I set the site theme to "basis"
    And I log in as "admin"
    And I am on site homepage

  Scenario: Confirm that we can navigate to a course without any issues
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And I am on "Course 1" course homepage
    Then I should see "Topic 1" in the "Course 1" "block"