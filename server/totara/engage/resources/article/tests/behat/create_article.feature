@totara @totara_engage @engage_article @engage @javascript
Feature: Create engage resource
  Background:
    Given I am on a totara site
    And I set the site theme to "ventura"
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | userone  | User      | One      | user.one@example.com |

  Scenario: Create a private article
    When I log in as "userone"
    And I click on "Your Library" in the totara menu
    And I click on "Contribute" "button"
    Then the "Next" "button" should be disabled
    When I set the field "Enter resource title" to "Article 1010"
    And I activate the weka editor with css ".tui-engageArticleForm__description-formRow"
    And I type "Create article" in the weka editor
    And I wait for the next second
    And I click on "Next" "button"
    And I wait for the next second
    Then the "Done" "button" should be enabled
    And I should see "Everyone"
    And I should see "Limited people"
    When I click on "Only you" "text" in the ".tui-accessSelector" "css_element"
    Then the "Done" "button" should be enabled
    And I click on "Done" "button"

  Scenario: Switching tabs triggers unsaved changes warning
    When I log in as "userone"
    And I click on "Your Library" in the totara menu
    And I click on "Contribute" "button"
    Then the "Resource" tui tab should be active

    # Switching between tabs doesn't trigger a warning when there is no unsaved change.
    When I switch to "Survey" tui tab
    Then the "Survey" tui tab should be active
    When I switch to "Resource" tui tab
    Then the "Resource" tui tab should be active

    # Having unsaved resource content should trigger the warning modal.
    When I set the field "Enter resource title" to "Test article"
    And I switch to "Survey" tui tab
    Then I should see "Discard draft?" in the tui modal
    And I should see "You will lose your entered data if you continue." in the tui modal
    When I close the tui modal
    Then the "Resource" tui tab should be active
    When I switch to "Survey" tui tab
    And I confirm the tui confirmation modal
    Then the "Survey" tui tab should be active

    # Modal also works for survey content.
    When I set the field "Enter survey question" to "Test survey question"
    And I switch to "Resource" tui tab
    Then I should see "Discard draft?" in the tui modal
