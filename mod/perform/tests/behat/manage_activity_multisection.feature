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
    When I navigate to the edit perform activities page for activity "Multiple section Activity"
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
    And I click on "Done" "button" in the ".tui-popoverPositioner" "css_element" of the "1" activity section
    And I click on ".tui-performActivitySectionRelationship:nth-of-type(2) .tui-checkbox__label" "css_element" in the "1" activity section
    And I click on "Done" "button" in the ".tui-formBtnGroup" "css_element" of the "1" activity section
    Then I should see "Activity saved" in the tui "success" notification toast
    And I close the tui notification toast

    # Edit relationships for second section.
    When I click on ".tui-performActivitySection__action-edit" "css_element" in the "2" activity section
    And I click the add participant button in "2" activity section
    And I click on the "Manager" tui checkbox in the ".tui-performManageActivityContent__items .tui-performActivitySection:nth-of-type(2) .tui-popoverFrame__content" css element
    And I click on the "Appraiser" tui checkbox in the ".tui-performManageActivityContent__items .tui-performActivitySection:nth-of-type(2) .tui-popoverFrame__content" css element
    And I click on "Done" "button" in the ".tui-popoverPositioner" "css_element" of the "2" activity section
    And I click on ".tui-performActivitySectionRelationship:nth-child(2) .tui-checkbox__label" "css_element" in the "2" activity section
    And I click on "Done" "button" in the ".tui-formBtnGroup" "css_element" of the "2" activity section
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
    When I navigate to the edit perform activities page for activity "Participant set up test"
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

  Scenario: Add section
    When I navigate to the edit perform activities page for activity "Participant set up test"
    Then "Add section" "button" should not be visible
    When I click on ".tui-toggleBtn" "css_element"
    Then I should see "All existing content will be grouped into the first section, along with the existing participant settings" in the tui modal
    When I confirm the tui confirmation modal
    And I close the tui notification toast
    # Only one section should be there
    Then activity section "1" should exist
    And activity section "2" should not exist
    And "Add section" "button" should be visible
    When I click on "Add section" "button"
    # Now the second one exists and is in edit mode - form is displayed
    Then activity section "1" should exist
    And activity section "2" should exist
    # Reload page
    When I navigate to the edit perform activities page for activity "Participant set up test"
    Then "Add section" "button" should be visible
    # Titles should be displayed
    And I should see "Untitled Section" in the "1" activity section
    And I should see "Untitled Section" in the "2" activity section
    # read-only mode - no form displayed
    And "Section title" "field" in the "1" activity section should not exist
    And "Section title" "field" in the "2" activity section should not exist
    When I click on "Add section" "button"
    # Now the third one exists and is in edit mode - form is displayed
    Then activity section "1" should exist
    And activity section "2" should exist
    And activity section "3" should exist
    And "Section title" "field" in the "1" activity section should not exist
    And "Section title" "field" in the "2" activity section should not exist
    And "Section title" "field" in the "3" activity section should exist
    # Titles should be displayed
    And I should see "Untitled Section" in the "1" activity section
    And I should see "Untitled Section" in the "2" activity section
    And I should not see "Untitled Section" in the "3" activity section
    When I click on "Add section" "button"
    # Now the fourth one exists and is in edit mode - form is displayed
    Then activity section "1" should exist
    And activity section "2" should exist
    And activity section "3" should exist
    And activity section "4" should exist
    And "Section title" "field" in the "1" activity section should not exist
    And "Section title" "field" in the "2" activity section should not exist
    And "Section title" "field" in the "3" activity section should exist
    And "Section title" "field" in the "4" activity section should exist
    And "Add section" "button" should be visible
    # Titles should be displayed
    And I should see "Untitled Section" in the "1" activity section
    And I should see "Untitled Section" in the "2" activity section
    And I should not see "Untitled Section" in the "3" activity section
    And I should not see "Untitled Section" in the "4" activity section

  Scenario: Add section above and below
    When I navigate to the edit perform activities page for activity "Participant set up test"
    Then "Add section" "button" should not be visible
    When I click on ".tui-toggleBtn" "css_element"
    Then I should see "All existing content will be grouped into the first section, along with the existing participant settings" in the tui modal
    When I confirm the tui confirmation modal
    And I close the tui notification toast
    # Let's add two more sections
    When I click on "Add section" "button"
    And I click on "Add section" "button"
    Then activity section "1" should exist
    And activity section "2" should exist
    And activity section "3" should exist
    # Reload to make sure we have readonly mode for all
    When I navigate to the edit perform activities page for activity "Participant set up test"
    And I click on ".tui-dropdown" "css_element" in the "1" activity section
    And I click on "Add section above" "link" in the "1" activity section
    # One more section is there
    # The first is now editable which we take as proof that it was added above the previous first one
    Then activity section "4" should exist
    And "Section title" "field" in the "1" activity section should exist
    When I click on "Cancel" "button" in the ".tui-performActivitySection__saveButtons" "css_element" of the "1" activity section
    And I click on ".tui-dropdown" "css_element" in the "2" activity section
    And I click on "Add section below" "link" in the "2" activity section
    # One more section is there
    # The third is now editable which we take as proof that it was added below the previous second
    Then activity section "5" should exist
    And "Section title" "field" in the "3" activity section should exist
    When I click on "Cancel" "button" in the ".tui-performActivitySection__saveButtons" "css_element" of the "3" activity section
    # The last section should not have two dropdown options
    And I click on ".tui-dropdown" "css_element" in the "5" activity section
    Then "Add section above" "link" in the "5" activity section should exist
    Then "Add section below" "link" in the "5" activity section should not exist
    # Reload and check whether all added sections are there
    When I navigate to the edit perform activities page for activity "Participant set up test"
    Then activity section "1" should exist
    And activity section "2" should exist
    And activity section "3" should exist
    And activity section "4" should exist
    And activity section "5" should exist
