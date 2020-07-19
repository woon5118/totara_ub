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
    And I click multiple answers question element
    And I set the following fields to these values:
      | rawTitle   | Question 1   |
      | answers[0] | Option one   |
      | answers[1] | Option two   |
    And I click multiple answers question add new option
    And I set the following fields to these values:
      |answers[2]        | Option three |
    And I click multiple answers question add new option
    And I set the following fields to these values:
      |answers[3]        | Option four |
    And I click multiple answers question add new option
    And I set the following fields to these values:
      |answers[4]        | Option five |
    When I set the following fields to these values:
      | min | 6 |
    And I save multiple answers question element data
    Then I should see "Invalid. Restriction must not exceed total number of options"
    When I set the following fields to these values:
      | min | 0 |
    Then I should see "Invalid. Restriction must be bigger then 0"
    When I set the following fields to these values:
      | min | -2 |
    Then I should see "Invalid. Restriction must be bigger then 0"
    When I set the following fields to these values:
      | min | 3 |
      | max | 6 |
    And I save multiple answers question element data
    Then I should see "Invalid. Restriction must not exceed total number of options"
    When I set the following fields to these values:
      | max | 0 |
    Then I should see "Invalid. Maximum cannot be smaller than minimum number"
    When I set the following fields to these values:
      | max | -2 |
    Then I should see "Invalid. Maximum cannot be smaller than minimum number"
    When I set the following fields to these values:
      | max | 2 |
    Then I should see "Invalid. Maximum cannot be smaller than minimum number"
    When I set the following fields to these values:
      | max | 4 |
    Then I save multiple answers question element data
    And I close the tui notification toast
    And I close the tui modal
    When I navigate to manage perform activity content page
    Then I should see perform multiple answers question "Question 1" is saved with options "Option one,Option two,Option three,Option four,Option five"
