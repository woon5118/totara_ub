@totara @perform @mod_perform @perform_element @javascript @vuejs
Feature: Manage performance activity multiple choice-answers elements with admin response restriction

  Background:
    Given the following "activities" exist in "mod_perform" plugin:
      | activity_name | create_section | create_track | activity_status |
      | Activity one  | true           | true         | Draft           |

  Scenario: Test multiple choice multiple answers elements with restrictions
    Given I log in as "admin"
    And I navigate to the manage perform activities page

    #Add multiple elements
    And I click on "Activity one" "link"
    And I navigate to manage perform activity content page
    And I add a "Multiple choice: multi-select" activity content element
    And I set the following fields to these values:
      | rawTitle   | Question 1   |
      | options[0][value] | Option one   |
      | options[1][value] | Option two   |
    And I click multiple answers question add new option
    And I set the following fields to these values:
      |options[2][value]        | Option three |
    And I click multiple answers question add new option
    And I set the following fields to these values:
      |options[3][value]        | Option four |
    And I click multiple answers question add new option
    And I set the following fields to these values:
      |options[4][value]        | Option five |
    When I set the following fields to these values:
      | min | 6 |
    And I save the activity content element
    Then I should see "Number must be 5 or less"
    When I set the following fields to these values:
      | min | -2 |
    And I save the activity content element
    Then I should see "Number must be 0 or more"
    When I set the following fields to these values:
      | min | 3 |
      | max | 6 |
    And I save the activity content element
    Then I should see "Number must be 5 or less"
    When I set the following fields to these values:
      | max | 0 |
    And I save the activity content element
    Then I should see "Number must be 0 or less"
    When I set the following fields to these values:
      | max | -2 |
    And I save the activity content element
    Then I should see "Number must be 0 or more"
    When I set the following fields to these values:
      | max | 2 |
    And I save the activity content element
    Then I should see "Number must be 2 or less"
    When I set the following fields to these values:
      | max | 4 |
    And I save the activity content element
    And I close the tui notification toast
    And I close the tui modal
    When I navigate to manage perform activity content page
    Then I should see perform "checkbox" question "Question 1" is saved with options "Option one,Option two,Option three,Option four,Option five"
