@totara @perform @mod_perform @javascript @vuejs
Feature: Visibility of my responses

  Background:
    Given the following "users" exist:
      | username | firstname | lastname  | email             |
      | sean     | Sean      | Subject   | sean@example.com  |
      | manny    | Manny     | Manager   | manny@example.com |
      | ava      | Ava       | Appraiser | ava@example.com   |
    And the following "subject instances" exist in "mod_perform" plugin:
      | activity_name                              | subject_username | subject_is_participating | other_participant_username | third_participant_username | relationships_can_view      |
      | No one can view responses                  | sean             | true                     | manny                      | ava                        |                             |
      | Everyone can view responses                | sean             | true                     | manny                      | ava                        | manager, appraiser, subject |
      | Subject only can view responses            | sean             | true                     | manny                      | ava                        | subject                     |
      | Managers only can view responses           | sean             | true                     | manny                      | ava                        | manager                     |
      | Appraisers only can view responses         | sean             | true                     | manny                      | ava                        | appraiser                   |
      | Managers and Appraisers can view responses | sean             | true                     | manny                      | ava                        | manager, appraiser          |

  Scenario: Viewing responses as the subject
    Given I log in as "sean"

    When I navigate to the outstanding perform activities list page
    And I click on "No one can view responses" "link"
    Then I should see "No one can view responses" in the ".tui-performUserActivity h2" "css_element"
    And I should see "Your responses are not visible to other participants"
    And I should not see the show others responses toggle

    When I navigate to the outstanding perform activities list page
    And I click on "Everyone can view responses" "link"
    Then I should see "Everyone can view responses" in the ".tui-performUserActivity h2" "css_element"
    And I should see "Your responses (once submitted) are visible to:" in the perform activity response visibility description
    And I should see "your Managers, your Appraisers" in the perform activity response visibility description
    And I should see that show others responses is toggled "off"

    When I navigate to the outstanding perform activities list page
    And I click on "Subject only can view responses" "link"
    Then I should see "Subject only can view responses" in the ".tui-performUserActivity h2" "css_element"
    And I should see "Your responses are not visible to other participants" in the perform activity response visibility description
    And I should see that show others responses is toggled "off"

    When I navigate to the outstanding perform activities list page
    And I click on "Managers only can view responses" "link"
    Then I should see "Managers only can view responses" in the ".tui-performUserActivity h2" "css_element"
    And I should see "Your responses (once submitted) are visible to:" in the perform activity response visibility description
    And I should see "your Managers" in the perform activity response visibility description
    And I should not see the show others responses toggle

    When I navigate to the outstanding perform activities list page
    And I click on "Appraisers only can view responses" "link"
    Then I should see "Appraisers only can view responses" in the ".tui-performUserActivity h2" "css_element"
    And I should see "Your responses (once submitted) are visible to:" in the perform activity response visibility description
    And I should see "your Appraisers" in the perform activity response visibility description
    And I should not see the show others responses toggle

    When I navigate to the outstanding perform activities list page
    And I click on "Managers and Appraisers can view responses" "link"
    Then I should see "Managers and Appraisers can view responses" in the ".tui-performUserActivity h2" "css_element"
    And I should see "Your responses (once submitted) are visible to:" in the perform activity response visibility description
    And I should see "your Managers, your Appraisers" in the perform activity response visibility description
    And I should not see the show others responses toggle

  Scenario: Viewing responses as a non subject participant
    Given I log in as "manny"

    When I navigate to the outstanding perform activities list page
    And I click on "Activities about others" "link"
    And I click on "No one can view responses" "link"
    Then I should see "No one can view responses" in the ".tui-performUserActivity h2" "css_element"
    And I should see "Your responses are not visible to other participants" in the perform activity response visibility description
    And I should not see the show others responses toggle

    When I navigate to the outstanding perform activities list page
    And I click on "Activities about others" "link"
    And I click on "Everyone can view responses" "link"
    Then I should see "Everyone can view responses" in the ".tui-performUserActivity h2" "css_element"
    And I should see "Your responses (once submitted) are visible to:" in the perform activity response visibility description
    And I should see "the Employee, the employee's Managers, the employee's Appraisers" in the perform activity response visibility description
    And I should see that show others responses is toggled "off"

    When I navigate to the outstanding perform activities list page
    And I click on "Activities about others" "link"
    And I click on "Subject only can view responses" "link"
    Then I should see "Subject only can view responses" in the ".tui-performUserActivity h2" "css_element"
    And I should see "Your responses (once submitted) are visible to:" in the perform activity response visibility description
    And I should see "the Employee" in the perform activity response visibility description
    And I should not see the show others responses toggle

    When I navigate to the outstanding perform activities list page
    And I click on "Activities about others" "link"
    And I click on "Managers only can view responses" "link"
    Then I should see "Managers only can view responses" in the ".tui-performUserActivity h2" "css_element"
    And I should see "Your responses (once submitted) are visible to:" in the perform activity response visibility description
    And I should see "the employee's Managers" in the perform activity response visibility description
    And I should see that show others responses is toggled "off"

    When I navigate to the outstanding perform activities list page
    And I click on "Activities about others" "link"
    And I click on "Appraisers only can view responses" "link"
    Then I should see "Appraisers only can view responses" in the ".tui-performUserActivity h2" "css_element"
    And I should see "Your responses (once submitted) are visible to:" in the perform activity response visibility description
    And I should see "the employee's Appraisers" in the perform activity response visibility description
    And I should not see the show others responses toggle

    When I navigate to the outstanding perform activities list page
    And I click on "Activities about others" "link"
    And I click on "Managers and Appraisers can view responses" "link"
    Then I should see "Managers and Appraisers can view responses" in the ".tui-performUserActivity h2" "css_element"
    And I should see "Your responses (once submitted) are visible to:" in the perform activity response visibility description
    And I should see "the employee's Managers, the employee's Appraisers" in the perform activity response visibility description
    And I should see that show others responses is toggled "off"
