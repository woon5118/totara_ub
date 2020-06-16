@totara @perform @mod_perform @javascript @vuejs
Feature: Adding, Updating, Removing activity elements.

  Background:
    Given the following "activities" exist in "mod_perform" plugin:
      | activity_name        | create_section | create_track |
      | Add Element Activity | true           | true         |

  Scenario: Save multiple elements to activity content.
    Given I log in as "admin"
    And I navigate to the manage perform activities page

    # Add multiple elements
    When I click on "Add Element Activity" "link"
    And I click on "Content" "link" in the ".tui-tabs__tabs" "css_element"
    And I click on "Edit content elements" "button"
    And I click on "Add element" "button"
    And I click on "Questions" "button"
    And I click on "Short text" "link"
    Then the focused element is "[name=rawTitle]" "css_element"

    When I set the following fields to these values:
      | rawTitle   | Question 1   |
      | identifier | Identifier 1 |
    And I click on "Done" "button" in the ".tui-performEditSectionContentModal__form" "css_element"
    And I click on "Add element" "button"
    And I click on "Questions" "button"
    And I click on "Short text" "link"
    Then the focused element is "[name=rawTitle]" "css_element"

    When I set the following fields to these values:
      | rawTitle   | Question 2   |
      | identifier | Identifier 2 |
    And I click on "Done" "button" in the ".tui-performEditSectionContentModal__form" "css_element"
    And I click on "Add element" "button"
    And I click on "Questions" "button"
    And I click on "Short text" "link"
    Then the focused element is "[name=rawTitle]" "css_element"

    When I set the following fields to these values:
      | rawTitle | Question 3 |
    And I click on "Done" "button" in the ".tui-performEditSectionContentModal__form" "css_element"
    And I click on "Submit" "button"
    And I close the tui notification toast
    And I click on "Edit content elements" "button"
    Then I should see "Question 1"
    And I should see "Question 2"
    And I should see "Question 3"
    When I click on identifier icon for question "Question 1"
    Then I should see "Identifier 1"
    When I click on identifier icon for question "Question 2"
    Then I should see "Identifier 2"
    And I close popovers
    And I should not see identifier icon for question "Question 3"

    # Update multiple elements and save.
    When I click on edit icon for question "Question 1"
    Then the focused element is "[name=rawTitle]" "css_element"

    And the following fields match these values:
      | rawTitle   | Question 1   |
      | identifier | Identifier 1 |
    When I set the following fields to these values:
      | rawTitle   | Test 1       |
      | identifier | Identifier A |
    And I click on "Done" "button" in the ".tui-performEditSectionContentModal__form" "css_element"
    And I click on edit icon for question "Question 2"
    Then the focused element is "[name=rawTitle]" "css_element"

    When I set the following fields to these values:
      | rawTitle   | Test 2 |
      | identifier |        |
    And I click on "Done" "button" in the ".tui-performEditSectionContentModal__form" "css_element"
    And I click on edit icon for question "Question 3"
    Then the focused element is "[name=rawTitle]" "css_element"

    When I set the following fields to these values:
      | rawTitle   | Test 3       |
      | identifier | Identifier C |
    And I click on "Done" "button" in the ".tui-performEditSectionContentModal__form" "css_element"
    And I click on "Submit" "button"
    And I close the tui notification toast
    And I click on "Edit content elements" "button"
    Then I should see "Test 1"
    And I should see "Test 2"
    And I should see "Test 3"
    When I click on identifier icon for question "Test 1"
    Then I should see "Identifier A"
    When I click on identifier icon for question "Test 3"
    Then I should see "Identifier C"
    And I close popovers
    And I should not see identifier icon for question "Test 2"

    # Delete multiple elements.
    When I click on edit icon for question "Test 1"
    And I click on "Actions" "button"
    And I click on "Delete" "link"
    And I click on edit icon for question "Test 2"
    And I click on "Actions" "button"
    And I click on "Delete" "link"
    And I click on "Submit" "button"
    And I close the tui notification toast
    And I click on "Edit content elements" "button"
    Then I should see "Test 3"
    And I should not see "Test 1"
    And I should not see "Test 2"
