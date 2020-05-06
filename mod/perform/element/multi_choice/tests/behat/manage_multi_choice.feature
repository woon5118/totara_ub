@totara @perform @mod_perform @perform_element @javascript @vuejs
Feature: Manage performance activity multiple choice elements

  Background:
    Given the following "activities" exist in "mod_perform" plugin:
      | activity_name        | create_section | create_track |
      | Activity one         | true           | true         |

  Scenario: Save multiple choice elements to activity content
    Given I log in as "admin"
    And I navigate to the manage perform activities page

    #Add multiple elements
    And I click on "Activity one" "link"
    And I navigate to manage perform activity content page
    And I click multi choice question element
    And I set the following fields to these values:
      |name        | Question 1 |
      |answers[0]  | Option one |
      |answers[1]  | Option two |
    And I save multi choice question element data
    And I click multi choice question element
    And I set the following fields to these values:
      |name        | Question 2 |
      |answers[0]  | Option three |
      |answers[1]  | Option four |
    And I save multi choice question element data
    And I click multi choice question element
    And I set the following fields to these values:
      |name        | Question 3 |
      |answers[0]  | Option five |
      |answers[1]  | Option six |
    And I save multi choice question element data
    And I click on "Submit" "button"
    When I navigate to manage perform activity content page
    Then I should see perform multi choice question "Question 1" is saved with options "Option one,Option two"
    And I should see perform multi choice question "Question 2" is saved with options "Option three,Option four"
    And I should see perform multi choice question "Question 3" is saved with options "Option five,Option six"

  Scenario: Save multiple choice elements with more options
    Given I log in as "admin"
    And I navigate to the manage perform activities page

    And I click on "Activity one" "link"
    And I navigate to manage perform activity content page
    And I click multi choice question element
    And I set the following fields to these values:
      |name        | Question 1 |
      |answers[0]  | Option one |
      |answers[1]  | Option two |
    And I click multi choice question add new option
    And I set the following fields to these values:
      |answers[2]        | Option three |
    And I save multi choice question element data
    And I click on "Submit" "button"
    And I navigate to manage perform activity content page
    Then I should see perform multi choice question "Question 1" is saved with options "Option one,Option two,Option three"

  Scenario: Delete multiple choice elements options
    Given I log in as "admin"
    And I navigate to the manage perform activities page

    And I click on "Activity one" "link"
    And I navigate to manage perform activity content page
    And I click multi choice question element
    And I set the following fields to these values:
      |name        | Question 1 |
      |answers[0]  | Option one |
      |answers[1]  | Option two |
    And I click multi choice question add new option
    And I set the following fields to these values:
      |answers[2]        | Option three |
    And I delete multi choice question option
    And I save multi choice question element data
    And I click on "Submit" "button"
    And I navigate to manage perform activity content page
    Then I should see perform multi choice question "Question 1" is saved with options "Option one,Option two"

  Scenario: Save multiple choice elements shows validation
    Given I log in as "admin"
    And I navigate to the manage perform activities page

    And I click on "Activity one" "link"
    And I navigate to manage perform activity content page
    And I click multi choice question element
    And I set the following fields to these values:
      |name        | Question 1 |
    And I save multi choice question element data
    And I should see "Required"

