@totara @perform @mod_perform @perform_element @javascript @vuejs
Feature: Manage performance activity multiple choice-answers elements

  Background:
    Given the following "activities" exist in "mod_perform" plugin:
      | activity_name | create_section | create_track |
      | Activity one  | true           | true         |

  Scenario: Save multiple choice multiple answers elements to activity content
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
      | identifier | Identifier 1 |
    And I save multiple answers question element data
    And I click multiple answers question element
    And I set the following fields to these values:
      | rawTitle   | Question 2   |
      | answers[0] | Option three |
      | answers[1] | Option four  |
    And I save multiple answers question element data
    And I click multiple answers question element
    And I set the following fields to these values:
      | rawTitle   | Question 3  |
      | answers[0] | Option five |
      | answers[1] | Option six  |
    And I save multiple answers question element data
    When I close the tui notification toast
    And I close the tui modal
    When I navigate to manage perform activity content page
    Then I should see perform multiple answers question "Question 1" is saved with options "Option one,Option two"
    And I should see perform multiple answers question "Question 2" is saved with options "Option three,Option four"
    And I should see perform multiple answers question "Question 3" is saved with options "Option five,Option six"
    When I click on identifier icon for question "Question 1"
    Then I should see "Identifier 1"

  Scenario: Save multiple choice multiple answers elements with more options
    Given I log in as "admin"
    And I navigate to the manage perform activities page

    And I click on "Activity one" "link"
    And I navigate to manage perform activity content page
    And I click multiple answers question element
    And I set the following fields to these values:
      | rawTitle   | Question 1 |
      | answers[0] | Option one |
      | answers[1] | Option two |
    And I click multiple answers question add new option
    And I set the following fields to these values:
      |answers[2]        | Option three |
    And I save multiple answers question element data
    And I close the tui notification toast
    And I close the tui modal
    And I navigate to manage perform activity content page
    Then I should see perform multiple answers question "Question 1" is saved with options "Option one,Option two,Option three"

  Scenario: Delete multiple answers elements options
    Given I log in as "admin"
    And I navigate to the manage perform activities page

    And I click on "Activity one" "link"
    And I navigate to manage perform activity content page
    And I click multiple answers question element
    And I set the following fields to these values:
      | rawTitle   | Question 1 |
      | answers[0] | Option one |
      | answers[1] | Option two |
    And I click multiple answers question add new option
    And I set the following fields to these values:
      | answers[2] | Option three |
    And I delete multiple answers question option
    And I save multiple answers question element data
    And I close the tui notification toast
    And I close the tui modal
    And I navigate to manage perform activity content page
    Then I should see perform multiple answers question "Question 1" is saved with options "Option one,Option two"

  Scenario: Save multiple answers elements shows validation
    Given I log in as "admin"
    And I navigate to the manage perform activities page

    And I click on "Activity one" "link"
    And I navigate to manage perform activity content page
    And I click multiple answers question element
    And I set the following fields to these values:
      | rawTitle | Question 1 |
    And I save multiple answers question element data
    And I should see "Required"

  Scenario: Save required and optional multiple answers elements
    Given I log in as "admin"
    And I navigate to the manage perform activities page
    And I click on "Activity one" "link"
    Then I should see "0" in the "required" element summary of the activity section
    And I should see "0" in the "optional" element summary of the activity section
    And I should see "0" in the "other" element summary of the activity section
    # Add multiple elements
    And I navigate to manage perform activity content page
    And I click multiple answers question element
    When I set the following fields to these values:
      | rawTitle   | Question 1 |
      | answers[0] | Option one |
      | answers[1] | Option two |
    And I click on the "responseRequired" tui checkbox
    And I save multiple answers question element data
    Then I should see "Required"
    And I click multiple answers question element
    When I set the following fields to these values:
      | rawTitle   | Question 2 |
      | answers[0] | Option one |
      | answers[1] | Option two |
    And I save multiple answers question element data
    Then I should see "Optional"
    When I close the tui notification toast
    And I close the tui modal
    Then I should see "1" in the "required" element summary of the activity section
    And I should see "1" in the "optional" element summary of the activity section
    And I should see "0" in the "other" element summary of the activity section
    When I navigate to manage perform activity content page
    Then I should see "Optional"
    And I should see "Required"
