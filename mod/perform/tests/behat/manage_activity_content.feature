@totara @perform @mod_perform @javascript @vuejs
Feature: Adding, Updating, Removing activity elements.

  Background:
    Given the following "activities" exist in "mod_perform" plugin:
      | activity_name        | create_section |
      | Add Element Activity | true           |

  Scenario: Save multiple elements to activity content.
    Given I log in as "admin"
    And I navigate to the manage perform activities page

    #Add multiple elements
    And I click on "Add Element Activity" "link"
    And I click on "Content" "link" in the ".tui-tabs__tabs" "css_element"
    And I click on "Edit content" "button"
    Then I click on "Add element" "button"
    And I click on "Questions" "button"
    And I click on "Short text" "link"
    And I set the following fields to these values:
      |name        | Question 1 |
    And I click on "Done" "button" in the ".tui-performEditSectionContentModal__form" "css_element"
    Then I click on "Add element" "button"
    And I click on "Questions" "button"
    And I click on "Short text" "link"
    And I set the following fields to these values:
      |name        | Question 2 |
    And I click on "Done" "button" in the ".tui-performEditSectionContentModal__form" "css_element"
    Then I click on "Add element" "button"
    And I click on "Questions" "button"
    And I click on "Short text" "link"
    And I set the following fields to these values:
      |name        | Question 3 |
    And I click on "Done" "button" in the ".tui-performEditSectionContentModal__form" "css_element"
    Then I click on "Submit" "button"
    When I click on "Edit content" "button"
    Then I should see "Question 1"
    And I should see "Question 2"
    And I should see "Question 3"

    #Update multiple elements and save.
    When I click on "Question 1" "button"
    And I set the following fields to these values:
      |name        | Test 1 |
    And I click on "Done" "button" in the ".tui-performEditSectionContentModal__form" "css_element"
    Then I click on "Question 2" "button"
    And I set the following fields to these values:
      |name        | Test 2 |
    And I click on "Done" "button" in the ".tui-performEditSectionContentModal__form" "css_element"
    Then I click on "Question 3" "button"
    And I set the following fields to these values:
      |name        | Test 3 |
    And I click on "Done" "button" in the ".tui-performEditSectionContentModal__form" "css_element"
    Then I click on "Submit" "button"
    When I click on "Edit content" "button"
    Then I should see "Test 1"
    And I should see "Test 2"
    And I should see "Test 3"

    #Delete multiple elements.
    When I click on "Delete" "button" in the ".tui-performEditSectionContentModal__form > div:nth-of-type(1)" "css_element"
    When I click on "Delete" "button" in the ".tui-performEditSectionContentModal__form > div:nth-of-type(1)" "css_element"
    And I click on "Submit" "button"
    When I click on "Edit content" "button"
    Then I should see "Test 3"
    And I should not see "Test 1"
    And I should not see "Test 2"
