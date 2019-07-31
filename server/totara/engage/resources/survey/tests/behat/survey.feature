@totara @engage @totara_engage @engage_survey @javascript
Feature: Vote survey
  As a user
  I need to vote an survey
  So that I can answer the question
  Background:
    Given I am on a totara site
    And I set the site theme to "ventura"

    And the following "users" exist:
      | username | firstname | lastname | email          |
      | user1    | User1      | One      | user1@test.com |
      | user2    | User2      | Two      | user2@test.com |

    And the following "topics" exist in "totara_topic" plugin:
      | name    |
      | Topic 1 |
      | Topic 2 |

    And the following "surveys" exist in "engage_survey" plugin:
      | question       | username | access  | topics           | options                      |
      | Test Survey 1? | user1    | PUBLIC  | Topic 1, Topic 2 |  Option 1, Option 2, Option 3|

    And "engage_survey" "Test Survey 1?" is shared with the following users:
      | sharer | recipient |
      | user1  | admin     |

  Scenario: Edit survey
    Given I log in as "user1"
    When I click on "Your Library" in the totara menu
    Then I should see "No results yet" in the ".tui-surveyCardBody__text" "css_element"
    And I click on "Edit survey" "link"
    And I wait for the next second
    And I set the field "Enter survey question" to "Changing test Survey 1?"
    And I click on "Single answer" "text" in the ".tui-totaraEngage-surveyForm__optionType--single" "css_element"
    And I set the field "Option" to " Option 4"
    And I click on "Save" "button"
    And I should see "Changing test Survey 1?"
    And I should see "Vote" in the ".tui-formBtn" "css_element"
    And I click on "Your resources" "link"
    And I should see "Your resources" in the ".tui-contributionBaseContent__header__title" "css_element"

  Scenario: Vote and bookmark survey
    Given I log in as "admin"
    And I click on "Your Library" in the totara menu
    And I click on "Shared with you" "link"
    And I should see "Test Survey 1?"
    And I click on "Vote" "link"
    And I click on "Bookmark" "button"
    And I click on "Option 1" "text"
    And I click on "Option 2" "text"
    And I click on "Vote" "button"
    And I should see "Total votes: 2"
    And I click on "Shared with you" "link"
    And I should see "Shared with you" in the ".tui-contributionBaseContent__header__title" "css_element"
    And I click on "Saved resources" "link"
    And I should see "Test Survey 1?"
    And I should see "Showing 3 of 3 results" in the ".tui-surveyResultBody__text" "css_element"

  Scenario: Like survey
    Given I log in as "admin"
    And I click on "Your Library" in the totara menu
    And I click on "Shared with you" "link"
    And I should see "Test Survey 1?"
    And I click on "Vote" "link"
    And I click on "Like" "button"
    And I should see "1"
    And I click on "Remove like" "button"
    And I should see "0"
