@totara @perform @mod_perform @javascript @vuejs
Feature: Managing an activity with multiple sections

  Background:
    Given the following "activities" exist in "mod_perform" plugin:
      | activity_name             | create_section | create_track |
      | Participant set up test   | true           | true         |
      | Multiple section Activity | true           | true         |
    And the following "activity sections" exist in "mod_perform" plugin:
      | activity_name             | section_name |
      | Multiple section Activity | Section B    |
    And I log in as "admin"
    And I navigate to the manage perform activities page

  Scenario: Manage participants for an activity with multiple sections.
    Given I navigate to the manage perform activities page
    When I click on "Multiple section Activity" "link"
    And I click on ".tui-toggleBtn" "css_element"
    Then I should see "All existing content will be grouped into the first section, along with the existing participant settings" in the tui modal
    And I confirm the tui confirmation modal
    Then I should see "Activity saved" in the tui "success" notification toast
    And I close the tui notification toast

    # Done & cancel buttons not visible
    Then I should see "Done"
    And I should see "Cancel"

    # First Section
    When I click the add participant button in "1" activity section
    Then the following fields match these values:
      | Subject   | 0 |
      | Manager   | 0 |
      | Appraiser | 0 |
    And I click on the "Subject" tui checkbox in the ".tui-performManageActivityContent__items .tui-performActivitySection:nth-of-type(1) .tui-popoverFrame__content" css element
    And I click on the "Appraiser" tui checkbox in the ".tui-performManageActivityContent__items .tui-performActivitySection:nth-of-type(1) .tui-popoverFrame__content" css element
    And I click on "Done" "button" in the ".tui-popoverPositioner" css element of the "1" activity section
    And I click on ".tui-performActivitySectionRelationship:nth-of-type(2) .tui-checkbox__label" css element in the "1" activity section
    And I click on "Done" "button" in the ".tui-formBtnGroup" css element of the "1" activity section
    Then I should see "Activity saved" in the tui "success" notification toast
    And I close the tui notification toast

    # Edit relationships for second section.
    When I click on ".tui-performActivitySection__action-edit" css element in the "2" activity section
    And I click the add participant button in "2" activity section
    And I click on the "Manager" tui checkbox in the ".tui-performManageActivityContent__items .tui-performActivitySection:nth-of-type(2) .tui-popoverFrame__content" css element
    And I click on the "Appraiser" tui checkbox in the ".tui-performManageActivityContent__items .tui-performActivitySection:nth-of-type(2) .tui-popoverFrame__content" css element
    And I click on "Done" "button" in the ".tui-popoverPositioner" css element of the "2" activity section
    And I click on ".tui-performActivitySectionRelationship:nth-child(2) .tui-checkbox__label" css element in the "2" activity section
    And I click on "Done" "button" in the ".tui-formBtnGroup" css element of the "2" activity section
    Then I should see "Activity saved" in the tui "success" notification toast
    And I close the tui notification toast

    # Subject added to first section and not second.
    And I should see "Subject" in the "1" activity section
    And I should not see "Subject" in the "2" activity section
    # Manager added to second section and not first.
    And I should see "Manager" in the "2" activity section
    And I should not see "Manager" in the "1" activity section
    # Appraiser is available for both sections
    And I should see "Appraiser*" in the "1" activity section
    And I should see "Appraiser*" in the "2" activity section

  Scenario: Toggle multisection states
    Given I navigate to the manage perform activities page
    When I click on "Participant set up test" "link"
    And I click on ".tui-toggleBtn" "css_element"
    Then I should see "All existing content will be grouped into the first section, along with the existing participant settings" in the tui modal
    And I confirm the tui confirmation modal
    Then I should see "Activity saved" in the tui "success" notification toast
    And I close the tui notification toast
    Then I should see "Done"
    And I should see "Cancel"
    # Activity section in read-only mode
    When I click on "Cancel" "button" in the ".tui-performActivitySection__saveButtons" "css_element"
    Then I should see "(*Can view others' responses)"

    When I click on ".tui-toggleBtn" "css_element"
    Then I should see "All sections' content will be merged and section headings removed. Participant settings will be removed. This cannot be undone." in the tui modal
    And I confirm the tui confirmation modal
    Then I should see "Activity saved" in the tui "success" notification toast
    And I close the tui notification toast
    And I should not see "(*Can view others' responses)"
