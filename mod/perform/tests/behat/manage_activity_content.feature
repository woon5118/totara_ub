@totara @perform @mod_perform @javascript @vuejs
Feature: Adding, Updating, Removing activity elements.

  Background:
    Given the following "activities" exist in "mod_perform" plugin:
      | activity_name        | create_section | create_track | activity_status |
      | Add Element Activity | true           | true         | Draft           |

  Scenario: Save multiple elements to activity content.
    Given I log in as "admin"
    And I navigate to the manage perform activities page

    # Add multiple elements
    When I click on "Add Element Activity" "link"
    And I click on "Content" "link" in the ".tui-tabs__tabs" "css_element"
    And I click on "Edit content elements" "button"
    And I click on "Add element" "button"
    And I click on "Short text" "link"
    Then the focused element is "[name=rawTitle]" "css_element"

    When I set the following fields to these values:
      | rawTitle   | Question 1   |
      | identifier | Identifier 1 |
    And I click on "Done" "button" in the ".tui-performEditSectionContentModal__form" "css_element"
    Then I should see "Element saved." in the tui "success" notification toast
    When I close the tui notification toast
    And I click on "Add element" "button"
    And I click on "Short text" "link"
    Then the focused element is "[name=rawTitle]" "css_element"

    When I set the following fields to these values:
      | rawTitle   | Question 2   |
      | identifier | Identifier 2 |
    And I click on "Done" "button" in the ".tui-performEditSectionContentModal__form" "css_element"
    And I click on "Add element" "button"
    And I click on "Short text" "link"
    Then the focused element is "[name=rawTitle]" "css_element"

    When I set the following fields to these values:
      | rawTitle | Question 3 |
    And I click on "Done" "button" in the ".tui-performEditSectionContentModal__form" "css_element"
    Then I should see "Element saved." in the tui "success" notification toast
    When I close the tui notification toast
    And I close the tui modal
    And I click on "Edit content elements" "button"
    Then I should see "Question 1"
    And I should see "Question 2"
    And I should see "Question 3"
    When I click on identifier icon for question "Question 1"
    Then I should see "Identifier 1"
    And I close popovers
    And I click on identifier icon for question "Question 2"
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
    And I close the tui notification toast
    And I close the tui modal
    And I click on "Edit content elements" "button"
    Then I should see "Test 1"
    And I should see "Test 2"
    And I should see "Test 3"
    When I click on identifier icon for question "Test 1"
    Then I should see "Identifier A"
    And I close popovers
    When I click on identifier icon for question "Test 3"
    Then I should see "Identifier C"
    And I close popovers
    And I should not see identifier icon for question "Test 2"

    # Delete element while editing
    When I click on edit icon for question "Test 1"
    And I click on "Actions" "button"
    And I click on "Delete" "link"

    # Deletion confirmation modal.
    Then I should see "Confirm delete element" in the tui modal
    And I should see "This cannot be undone." in the tui modal
    When I close the tui modal
    Then I should not see "Element deleted."
    When I click on "Actions" "button"
    And I click on "Delete" "link"
    And I confirm the tui confirmation modal
    Then I should see "Element deleted." in the tui "success" notification toast
    And I close the tui notification toast

    # Unsaved changes dialog should not be triggered
    And I close the tui modal
    And I click on "Edit content elements" "button"
    Then I should not see "Test 1"
    And I should see "Test 2"
    And I should see "Test 3"

    # Delete using icon when not in edit mode
    When I click on delete icon for question "Test 2"
    Then I should see "Confirm delete element" in the tui modal
    And I should see "This cannot be undone." in the tui modal
    And I confirm the tui confirmation modal
    Then I should see "Element deleted." in the tui "success" notification toast
    And I close the tui notification toast

    # Only one element should remain
    Then I should see "Test 3"
    And I should not see "Test 1"
    And I should not see "Test 2"

    # Confirmation should be shown when closing whilst still editing
    When I click on edit icon for question "Test 3"
    And I close the tui modal
    Then I should see "Unsaved changes will be lost" in the tui modal
    And I should see "You currently have unsaved changes that will be lost if you close this content editor. Cancel to go back and save individual content elements. Close to discard the changes." in the tui modal
    When I close the tui modal
    Then I should see "Add element"
    When I close the tui modal
    And I confirm the tui confirmation modal
    Then I should not see "Add element"

    # Changes should be permanent
    And I click on "Edit content elements" "button"
    Then I should see "Test 3"
    And I should not see "Test 1"
    And I should not see "Test 2"
