@totara @perform @mod_perform @javascript @vuejs @editor_weka @weka
Feature: Manage performance activity static content

  Background:
    Given the following "activities" exist in "mod_perform" plugin:
      | activity_name        | create_section | create_track | activity_status |
      | Add Element Activity | true           | true         | Draft           |

  Scenario: Save static content elements
    Given I log in as "admin"

    # Add a static content element
    When I navigate to the edit perform activities page for activity "Add Element Activity"
    And I click on "Edit content" "button"
    And I click on "Add element" "button"
    And I click on "Static content" "link"
    And I set the following fields to these values:
      | rawTitle | Static content 1 |
    And I activate the weka editor with css ".tui-weka"
    And I type "I see trees of green, red roses too. I see them bloom for me and you. And I think to myself what a wonderful world." in the weka editor
    And I click on "Done" "button" in the ".tui-performEditSectionContentModal__form" "css_element"
    Then I should see "Element saved."
    And I should see "I see them bloom for me and you."
    When I close the tui modal
    And I close the tui notification toast
    Then I should see "1" in the "other" element summary of the activity section

    # Edit a static content element
    When I click on "Edit content" "button"
    And I click on "Edit element" "button"
    And I activate the weka editor with css ".tui-weka"
    And I type "Changed static content" in the weka editor
    And I click on "Done" "button" in the ".tui-performEditSectionContentModal__form" "css_element"
    Then I should see "Element saved."
    And I should see "Changed static content"
