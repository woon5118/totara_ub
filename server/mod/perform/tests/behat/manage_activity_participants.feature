@totara @perform @mod_perform @javascript @vuejs
Feature: Adding and removing participant to a perform activity section

  Background:
    Given the following "activities" exist in "mod_perform" plugin:
      | activity_name             | create_section | create_track | activity_status |
      | Participant set up test   | true           | true         | Draft           |
      | Multiple section Activity | true           | true         | Draft           |
    And the following "activity sections" exist in "mod_perform" plugin:
      | activity_name             | section_name |
      | Multiple section Activity | Section B    |
    And I log in as "admin"
    And I navigate to the manage perform activities page

  Scenario: Add and remove single participant to a section
    When I click on "Participant set up test" "link"
    Then the "Content" tui tab should be active
    And I should see no perform activity participants
    When I click the add participant button
    Then the following fields match these values:
      | Subject   | 0 |
      | Manager   | 0 |
      | Appraiser | 0 |
    When I click on the "Subject" tui checkbox
    And I click on "Done" "button"
    Then I should see "Activity saved" in the tui "success" notification toast
    And I close the tui notification toast
    When I click the add participant button
    Then the "input[name=Subject]" "css_element" should be disabled in the ".tui-performActivitySection .tui-popoverFrame" "css_element"
    Then the following fields match these values:
      | Subject   | 1 |
      | Manager   | 0 |
      | Appraiser | 0 |
    Then I should see "Subject" as the perform activity participants
    When I remove "Subject" as a perform activity participant
    Then I should see "Activity saved" in the tui "success" notification toast

  Scenario: Add and remove all participants to a section
    When I click on "Participant set up test" "link"
    Then the "Content" tui tab should be active
    And I should see no perform activity participants
    When I click the add participant button
    Then the following fields match these values:
      | Subject   | 0 |
      | Manager   | 0 |
      | Appraiser | 0 |

    When I click on the "Subject" tui checkbox
    And I click on the "Manager" tui checkbox
    And I click on "Done" "button"
    Then I should see "Activity saved" in the tui "success" notification toast
    And I close the tui notification toast
    Then I should see "Subject, Manager" as the perform activity participants

    When I click the add participant button
    Then the "input[name=Subject]" "css_element" should be disabled in the ".tui-performActivitySection .tui-popoverFrame" "css_element"
    And the "input[name=Manager]" "css_element" should be disabled in the ".tui-performActivitySection .tui-popoverFrame" "css_element"
    And the "input[name=Appraiser]" "css_element" should be enabled in the ".tui-performActivitySection .tui-popoverFrame" "css_element"
    And the following fields match these values:
      | Subject   | 1 |
      | Manager   | 1 |
      | Appraiser | 0 |
      | Peer      | 0 |
      | Mentor    | 0 |
      | Reviewer  | 0 |

    When I click on the "Appraiser" tui checkbox
    And I click on "Done" "button"
    Then I should see "Activity saved" in the tui "success" notification toast
    And I close the tui notification toast
    And I should see "Subject, Manager, Appraiser" as the perform activity participants
    When I remove "Subject" as a perform activity participant
    And I close the tui notification toast
    When I remove "Manager" as a perform activity participant
    And I close the tui notification toast
    When I remove "Appraiser" as a perform activity participant
    Then I should see "Activity saved" in the tui "success" notification toast
    And I close the tui notification toast
    When I click the add participant button
    And I click on the "Subject" tui checkbox
    Then I should see the add participant button

  Scenario: Managing participants for activity with one section auto saves.
    Given I navigate to the manage perform activities page
    When I click on "Participant set up test" "link"
    And I should not see "Done"
    And I should not see "Cancel"
    And I click the add participant button
    Then the following fields match these values:
      | Subject   | 0 |
      | Manager   | 0 |
      | Appraiser | 0 |

    When I click on the "Subject" tui checkbox
    When I click on the "Manager" tui checkbox
    When I click on the "Appraiser" tui checkbox
    When I click on the "Peer" tui checkbox
    When I click on the "Mentor" tui checkbox
    When I click on the "Reviewer" tui checkbox
    And I click on "Done" "button"
    Then I should see "Activity saved" in the tui "success" notification toast
    And I close the tui notification toast
    And I should see "Subject, Manager, Appraiser, Peer, Mentor, Reviewer" as the perform activity participants
    And I should see the add participant button is disabled

    # Toggle can view other responses for Subject
    And I click on ".tui-performActivitySectionRelationship:nth-of-type(1) .tui-checkbox__label" "css_element"
    Then I should see "Activity saved" in the tui "success" notification toast
    And I close the tui notification toast

    # Toggle can view other responses for Manager
    And I click on ".tui-performActivitySectionRelationship:nth-of-type(2) .tui-checkbox__label" "css_element"
    Then I should see "Activity saved" in the tui "success" notification toast
    And I close the tui notification toast

    # Toggle can view other responses for Appraiser
    And I click on ".tui-performActivitySectionRelationship:nth-of-type(3) .tui-checkbox__label" "css_element"
    Then I should see "Activity saved" in the tui "success" notification toast
    And I close the tui notification toast

    # Remove a participant
    When I remove "Subject" as a perform activity participant
    Then I should see "Activity saved" in the tui "success" notification toast
    And I close the tui notification toast
    And I should see "Manager, Appraiser" as the perform activity participants
