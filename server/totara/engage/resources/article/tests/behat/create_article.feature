@totara @totara_engage @engage_article @engage
Feature: Create engage resource
  Background:
    Given I am on a totara site
    And I set the site theme to "ventura"
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | userone  | User      | One      | user.one@example.com |

  @javascript
  Scenario: Create a private article
    Given I log in as "userone"
    And I click on "Your Library" in the totara menu
    Then I click on "Contribute" "button"
    And the "Next" "button" should be disabled
    And I set the field "Enter resource title" to "Article 1010"
    And I activate the weka editor with css ".tui-articleForm__description__formRow"
    And I type "Create article" in the weka editor
    And I wait for the next second
    And I click on "Next" "button"
    And I wait for the next second
    And the "Done" "button" should be disabled
    And I should see "Everyone"
    And I should see "Limited people"
    And I click on "Only you" "text" in the ".tui-accessSelector" "css_element"
    And the "Done" "button" should be enabled
    And I click on "Done" "button"