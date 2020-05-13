@totara @perform @mod_perform @javascript @vuejs
Feature: Adding and removing participant to a perform activity section

  Background:
    Given the following "activities" exist in "mod_perform" plugin:
      | activity_name           | create_section | create_track |
      | Participant set up test | true           | true         |
    And I log in as "admin"
    And I navigate to the manage perform activities page
    When I click on "Participant set up test" "link"
    Then the "Content" tui tab should be active
    And I should see no perform activity participants
    And the "Save changes" "button" should be disabled in the ".mod-perform-activitySection .tui-formBtnGroup" "css_element"
    And the "Cancel" "button" should be disabled in the ".mod-perform-activitySection .tui-formBtnGroup" "css_element"

  Scenario: Add and remove single participant to a section
    When I click the add perform activity participant button
    Then the following fields match these values:
      | Subject   | 0 |
      | Manager   | 0 |
      | Appraiser | 0 |

    When I click on the "Subject" tui checkbox
    And I click on "Done" "button"

    When I click the add perform activity participant button
    Then the "input[name=Subject]" "css_element" should be disabled in the ".mod-perform-activitySection .tui-popoverFrame" "css_element"
    Then the following fields match these values:
      | Subject   | 1 |
      | Manager   | 0 |
      | Appraiser | 0 |

    When I click on "Cancel" "button" in the ".mod-perform-activitySection .tui-popoverFrame" "css_element"
    Then I should see "Subject" as the perform activity participants
    And the "Save changes" "button" should be enabled
    And the "Cancel" "button" should be enabled

    When I click on "Save changes" "button" in the ".mod-perform-activitySection .tui-formBtnGroup" "css_element"
    Then I should see "Activity saved" in the tui "success" notification toast
    And the "Save changes" "button" should be disabled in the ".mod-perform-activitySection .tui-formBtnGroup" "css_element"
    And the "Cancel" "button" should be disabled in the ".mod-perform-activitySection .tui-formBtnGroup" "css_element"

    When I remove "Subject" as a perform activity participant
    And the "Save changes" "button" should be enabled in the ".mod-perform-activitySection .tui-formBtnGroup" "css_element"
    And the "Cancel" "button" should be enabled in the ".mod-perform-activitySection .tui-formBtnGroup" "css_element"

    When I close the tui notification toast
    When I click on "Save changes" "button" in the ".mod-perform-activitySection .tui-formBtnGroup" "css_element"
    And the "Save changes" "button" should be disabled in the ".mod-perform-activitySection .tui-formBtnGroup" "css_element"
    And the "Cancel" "button" should be disabled in the ".mod-perform-activitySection .tui-formBtnGroup" "css_element"

  Scenario: Add and remove all participants to a section
    When I click the add perform activity participant button
    Then the following fields match these values:
      | Subject   | 0 |
      | Manager   | 0 |
      | Appraiser | 0 |

    When I click on the "Subject" tui checkbox
    And I click on the "Manager" tui checkbox
    And I click on "Done" "button"
    Then I should see "Subject, Manager" as the perform activity participants

    When I click the add perform activity participant button
    Then the "input[name=Subject]" "css_element" should be disabled in the ".mod-perform-activitySection .tui-popoverFrame" "css_element"
    And the "input[name=Manager]" "css_element" should be disabled in the ".mod-perform-activitySection .tui-popoverFrame" "css_element"
    And the "input[name=Appraiser]" "css_element" should be enabled in the ".mod-perform-activitySection .tui-popoverFrame" "css_element"
    And the following fields match these values:
      | Subject   | 1 |
      | Manager   | 1 |
      | Appraiser | 0 |

    When I click on the "Appraiser" tui checkbox
    And I click on "Done" "button"

    Then I should see "Subject, Manager, Appraiser" as the perform activity participants
    And I should see the add perform activity participant button is disabled
    And the "Save changes" "button" should be enabled
    And the "Cancel" "button" should be enabled

    When I click on "Save changes" "button" in the ".mod-perform-activitySection .tui-formBtnGroup" "css_element"
    Then I should see "Activity saved" in the tui "success" notification toast
    And the "Save changes" "button" should be disabled in the ".mod-perform-activitySection .tui-formBtnGroup" "css_element"
    And the "Cancel" "button" should be disabled in the ".mod-perform-activitySection .tui-formBtnGroup" "css_element"

    When I remove "Subject" as a perform activity participant
    When I remove "Manager" as a perform activity participant
    When I remove "Appraiser" as a perform activity participant
    And the "Save changes" "button" should be enabled in the ".mod-perform-activitySection .tui-formBtnGroup" "css_element"
    And the "Cancel" "button" should be enabled in the ".mod-perform-activitySection .tui-formBtnGroup" "css_element"

    When I click on "Save changes" "button" in the ".mod-perform-activitySection .tui-formBtnGroup" "css_element"
    And the "Save changes" "button" should be disabled in the ".mod-perform-activitySection .tui-formBtnGroup" "css_element"
    And the "Cancel" "button" should be disabled in the ".mod-perform-activitySection .tui-formBtnGroup" "css_element"

    When I click the add perform activity participant button
    And I click on the "Subject" tui checkbox
    Then I should see the add perform activity participant button

  Scenario: Canceling participant changes
    When I click the add perform activity participant button
    And I click on the "Subject" tui checkbox
    And I click on "Cancel" "button"

    Then I should see no perform activity participants
    And the "Save changes" "button" should be disabled in the ".mod-perform-activitySection .tui-formBtnGroup" "css_element"
    And the "Cancel" "button" should be disabled in the ".mod-perform-activitySection .tui-formBtnGroup" "css_element"

    When I click the add perform activity participant button
    Then the following fields match these values:
      | Subject   | 1 |
      | Manager   | 0 |
      | Appraiser | 0 |
    And the "input[name=Subject]" "css_element" should be enabled in the ".mod-perform-activitySection .tui-popoverFrame" "css_element"
    And the "input[name=Manager]" "css_element" should be enabled in the ".mod-perform-activitySection .tui-popoverFrame" "css_element"
    And the "input[name=Appraiser]" "css_element" should be enabled in the ".mod-perform-activitySection .tui-popoverFrame" "css_element"

    When I click on "Done" "button"
    Then I should see "Subject" as the perform activity participants
    And the "Save changes" "button" should be enabled in the ".mod-perform-activitySection .tui-formBtnGroup" "css_element"
    And the "Cancel" "button" should be enabled in the ".mod-perform-activitySection .tui-formBtnGroup" "css_element"

    When I click on "Cancel" "button" in the ".mod-perform-activitySection .tui-formBtnGroup" "css_element"
    Then I should see no perform activity participants
    And the "Save changes" "button" should be disabled in the ".mod-perform-activitySection .tui-formBtnGroup" "css_element"
    And the "Cancel" "button" should be disabled in the ".mod-perform-activitySection .tui-formBtnGroup" "css_element"