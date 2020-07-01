@totara @perform @mod_perform @javascript @vuejs
Feature: Manage performance activity static content

  Background:
    Given the following "activities" exist in "mod_perform" plugin:
      | activity_name        | create_section | create_track |
      | Add Element Activity | true           | true         |

  Scenario: Save static content elements
    Given I log in as "admin"

    # Add multiple elements
    When I navigate to the edit perform activities page for activity "Add Element Activity"
    And I click on "Edit content" "button"
    And I click on "Add element" "button"
    And I click on "Static content" "link"
    When I set the following fields to these values:
      | rawTitle | Static content 1|
      | rawText | I see trees of green, red roses too. I see them bloom for me and you. And I think to myself what a wonderful world. |
    And I click on "Done" "button" in the ".tui-performEditSectionContentModal__form" "css_element"
    Then I should see "I see them bloom for me and you."
    When I close the tui modal
    And I close the tui notification toast
    And I should see "1" in the "other" element summary of the activity section

