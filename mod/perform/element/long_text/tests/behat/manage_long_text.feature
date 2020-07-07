@totara @perform @mod_perform @javascript @vuejs
Feature: Manage performance activity long text elements

  Background:
    Given the following "activities" exist in "mod_perform" plugin:
      | activity_name        | create_section | create_track | activity_status |
      | Add Element Activity | true           | true         | Draft           |

  Scenario: Save required and optional long text elements
    Given I log in as "admin"

    # Add multiple elements
    When I navigate to the edit perform activities page for activity "Add Element Activity"
    And I click on "Edit content elements" "button"
    And I click on "Add element" "button"
    And I click on "Long text" "link"
    When I set the following fields to these values:
      | rawTitle   | Question 1   |
      | identifier | Identifier 1 |
    And I click on the "responseRequired" tui checkbox
    And I click on "Done" "button" in the ".tui-performEditSectionContentModal__form" "css_element"
    Then I should see "Required"
    When I click on identifier icon for question "Question 1"
    Then I should see "Identifier 1"
    When I click on "Add element" "button"
    And I click on "Long text" "link"
    And I set the following fields to these values:
      | rawTitle | Question 2 |
    And I click on "Done" "button" in the ".tui-performEditSectionContentModal__form" "css_element"
    Then I should see "Optional"
    And I close the tui notification toast
    And I close the tui modal
    Then I should see "1" in the "required" element summary of the activity section
    And I should see "1" in the "optional" element summary of the activity section
    And I should see "0" in the "other" element summary of the activity section
    When I click on "Edit content elements" "button"
    Then I should see "Optional"
    And I should see "Required"
    When I click on identifier icon for question "Question 1"
    Then I should see "Identifier 1"