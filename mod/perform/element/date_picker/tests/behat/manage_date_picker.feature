@totara @perform @mod_perform @perform_element @javascript @vuejs
Feature: Manage performance activity date picker elements

  Background:
    Given the following "activities" exist in "mod_perform" plugin:
      | activity_name | create_section | create_track | activity_status |
      | Activity one  | true           | true         | Draft           |

  Scenario: Save date picker elements to activity content
    Given I log in as "admin"
    And I navigate to the manage perform activities page

    #Add multiple elements
    And I click on "Activity one" "link"
    And I navigate to manage perform activity content page
    And I click date picker question element
    And I set the following fields to these values:
      | rawTitle   | Question 1   |
      | identifier | Identifier 1 |
    And I save date picker question element data
    And I click date picker question element
    And I set the following fields to these values:
      | rawTitle   | Question 2   |
    And I save date picker question element data
    And I close the tui notification toast
    And I close the tui modal
    When I navigate to manage perform activity content page
    When I click on identifier icon for question "Question 1"
    Then I should see "Identifier 1"

  Scenario: Save required and optional date picker elements
    Given I log in as "admin"
    And I navigate to the manage perform activities page
    And I click on "Activity one" "link"
    Then I should see "0" in the "required" element summary of the activity section
    And I should see "0" in the "optional" element summary of the activity section
    And I should see "0" in the "other" element summary of the activity section
    # Add multiple elements
    And I navigate to manage perform activity content page
    And I click date picker question element
    When I set the following fields to these values:
      | rawTitle   | Question 1 |
    And I click on the "responseRequired" tui checkbox
    And I save date picker question element data
    Then I should see "Required"
    And I click date picker question element
    When I set the following fields to these values:
      | rawTitle   | Question 2 |
    And I save date picker question element data
    Then I should see "Optional"
    When I close the tui notification toast
    And I close the tui modal
    Then I should see "1" in the "required" element summary of the activity section
    And I should see "1" in the "optional" element summary of the activity section
    And I should see "0" in the "other" element summary of the activity section
    When I navigate to manage perform activity content page
    Then I should see "Optional"
    And I should see "Required"
