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
    And I click on "Edit content elements" "link_or_button"
    And I add a "Long text" activity content element
    When I set the following fields to these values:
      | rawTitle   | Question 1   |
      | identifier | Identifier 1 |
    And I click on the "responseRequired" tui checkbox
    And I save the activity content element
    Then I should see "Required"
    When I click on the Reporting ID action for question "Question 1"
    Then I should see "Identifier 1"
    And I add a "Long text" activity content element
    And I set the following fields to these values:
      | rawTitle | Question 2 |
    And I save the activity content element
    And I close the tui notification toast
    And I follow "Content (Add Element Activity)"
    Then I should see "1" in the "required" element summary of the activity section
    And I should see "1" in the "optional" element summary of the activity section
    And I should see "0" in the "other" element summary of the activity section
    When I click on "Edit content elements" "link_or_button"
    And I should see "Required"
    When I click on the Reporting ID action for question "Question 1"
    Then I should see "Identifier 1"