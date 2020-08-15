@totara @perform @mod_perform @perform_element @javascript @vuejs
Feature: Manage performance activity custom rating scale elements

  Background:
    Given the following "activities" exist in "mod_perform" plugin:
      | activity_name | create_section | create_track | activity_status |
      | Activity one  | true           | true         | Draft           |

  Scenario: Save custom rating scale elements to activity content
    Given I log in as "admin"
    And I navigate to the manage perform activities page

    #Add multiple elements
    And I click on "Activity one" "link"
    And I navigate to manage perform activity content page
    And I click custom rating scale question element
    And I set the following fields to these values:
      | rawTitle   | Question 1   |
      | answers[0][text]  | Option one   |
      | answers[0][score] | 1            |
      | answers[1][text]  | Option two   |
      | answers[1][score] | 2            |
      | identifier | Identifier 1 |
    And I save custom rating scale question element data
    And I click custom rating scale question element
    And I set the following fields to these values:
      | rawTitle   | Question 2   |
      | answers[0][text]  | Option three |
      | answers[0][score] | 3            |
      | answers[1][text]  | Option four |
      | answers[1][score] | 4            |
    And I save custom rating scale question element data
    And I click custom rating scale question element
    And I set the following fields to these values:
      | rawTitle   | Question 3 |
      | answers[0][text] | Option five |
      | answers[0][score] | 5           |
      | answers[1][text] | Option six  |
      | answers[1][score] | 6           |
    And I save custom rating scale question element data
    And I close the tui notification toast
    And I close the tui modal
    When I navigate to manage perform activity content page
    Then I should see perform custom rating scale question "Question 1" is saved with options "Option one (score: 1),Option two (score: 2)"
    And I should see perform custom rating scale question "Question 2" is saved with options "Option three (score: 3),Option four (score: 4)"
    And I should see perform custom rating scale question "Question 3" is saved with options "Option five (score: 5),Option six (score: 6)"
    When I click on identifier icon for question "Question 1"
    Then I should see "Identifier 1"

  Scenario: Save custom rating scale elements with more options
    Given I log in as "admin"
    And I navigate to the manage perform activities page

    And I click on "Activity one" "link"
    And I navigate to manage perform activity content page
    And I click custom rating scale question element
    And I set the following fields to these values:
      | rawTitle   | Question 1 |
      | answers[0][text] | Option one |
      | answers[0][score]| 1          |
      | answers[1][text] | Option two |
      | answers[1][score]| 2          |
    And I click custom rating scale question add new option
    And I set the following fields to these values:
      |answers[2][text]  | Option three |
      |answers[2][score] | 3            |
    And I save custom rating scale question element data
    And I close the tui notification toast
    And I close the tui modal
    And I navigate to manage perform activity content page
    Then I should see perform custom rating scale question "Question 1" is saved with options "Option one (score: 1),Option two (score: 2),Option three (score: 3)"

  Scenario: Delete custom rating scale elements options
    Given I log in as "admin"
    And I navigate to the manage perform activities page

    And I click on "Activity one" "link"
    And I navigate to manage perform activity content page
    And I click custom rating scale question element
    And I set the following fields to these values:
      | rawTitle   | Question 1 |
      | answers[0][text] | Option one |
      | answers[0][score]| 1          |
      | answers[1][text] | Option two |
      | answers[1][score]| 2          |
    And I click custom rating scale question add new option
    And I set the following fields to these values:
      |answers[2][text]  | Option three |
      |answers[2][score] | 3            |
    And I delete custom rating scale question option
    And I save custom rating scale question element data
    And I close the tui notification toast
    And I close the tui modal
    And I navigate to manage perform activity content page
    Then I should see perform custom rating scale question "Question 1" is saved with options "Option one (score: 1),Option two (score: 2)"

  Scenario: Save custom rating scale elements shows validation
    Given I log in as "admin"
    And I navigate to the manage perform activities page

    And I click on "Activity one" "link"
    And I navigate to manage perform activity content page
    And I click custom rating scale question element
    And I set the following fields to these values:
      | rawTitle | Question 1 |
    And I save custom rating scale question element data
    And I should see "Required"

  Scenario: Save required and optional custom rating scale elements
    Given I log in as "admin"
    And I navigate to the manage perform activities page
    And I click on "Activity one" "link"
    Then I should see "0" in the "required" element summary of the activity section
    And I should see "0" in the "optional" element summary of the activity section
    And I should see "0" in the "other" element summary of the activity section
    # Add multiple elements
    And I navigate to manage perform activity content page
    And I click custom rating scale question element
    When I set the following fields to these values:
      | rawTitle   | Question 1 |
      | answers[0][text] | Option one |
      | answers[0][score]| 1          |
      | answers[1][text] | Option two |
      | answers[1][score]| 2          |
    And I click on the "responseRequired" tui checkbox
    And I save custom rating scale question element data
    Then I should see "Required"
    And I click custom rating scale question element
    When I set the following fields to these values:
      | rawTitle   | Question 2 |
      | answers[0][text] | Option one |
      | answers[0][score]| 1          |
      | answers[1][text] | Option two |
      | answers[1][score]| 2          |
    And I save custom rating scale question element data
    When I close the tui notification toast
    And I close the tui modal
    Then I should see "1" in the "required" element summary of the activity section
    And I should see "1" in the "optional" element summary of the activity section
    And I should see "0" in the "other" element summary of the activity section

