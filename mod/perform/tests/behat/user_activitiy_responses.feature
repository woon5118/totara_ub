@totara @perform @mod_perform @javascript @vuejs
Feature: Viewing other responses

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                   |
      | john     | John      | One      | john.one@example.com    |
      | david    | David     | Two      | david.two@example.com   |
      | harry    | Harry     | Three    | harry.three@example.com |
    And the following "subject instances" exist in "mod_perform" plugin:
      | activity_name                 | subject_username | subject_is_participating | other_participant_username | include_required_questions |
      | John is participating subject | john             | true                     | david                      | true                       |
      | David is subject              | david            | false                    | admin                      | true                       |
      | John is not participating     | harry            | true                     | david                      | false                      |

  Scenario: I can respond to my activities and view other non-respond activities
    Given I log in as "john"
    When I navigate to the outstanding perform activities list page
    And I click on "John is participating subject" "link"
    Then I should see "John is participating subject" in the ".tui-performUserActivity h2" "css_element"
    And I should see "Part one"
    And I should see that show others responses is toggled "off"
    And I should see perform activity relationship to user "Self"
    And I should see perform "short text" question "Question one" is unanswered
    And I should see perform "short text" question "Question two" is unanswered
    And I should not see "Manager response"
    And I wait until ".tui-elementResponse .tui-formField" "css_element" exists
    And I answer "short text" question "Question one" with "John Answer one"
    And I answer "short text" question "Question two" with "John Answer two"

    When I click on "Submit" "button"
    Then I should see "Performance activities"
    And I should see "Activity responses saved" in the tui "success" notification toast
    And the "Your activities" tui tab should be active

    When I click on "John is participating subject" "link"
    And I wait until ".tui-otherParticipantResponses" "css_element" exists
    Then I should see that show others responses is toggled "on"
    And I should see "Manager response"
    And I should see "No response submitted"

  Scenario: Manager can respond to other activities and I can view manager responses
    Given I log in as "david"
    When I navigate to the outstanding perform activities list page
    And I click on "Activities about others" "link"
    Then I should see "John is participating subject" in the ".tui-performUserActivities" "css_element"

    When I click on "John is participating subject" "link"
    Then I should see perform activity relationship to user "Manager"
    And I wait until ".tui-elementResponse .tui-formField" "css_element" exists
    And I answer "short text" question "Question one" with "Manager Answer one"
    And I answer "short text" question "Question two" with "Manager Answer two"
    And I click on "Submit" "button"

    Then I should see "Performance activities"
    And I should see "Activity responses saved" in the tui "success" notification toast
    And the "Activities about others" tui tab should be active

    When I click on "Close" "button"
    And I log out
    And I log in as "john"
    And I navigate to the outstanding perform activities list page
    And I click on "John is participating subject" "link"
    Then I should see that show others responses is toggled "off"

    When I click show others responses
    And I wait until ".tui-otherParticipantResponses" "css_element" exists
    Then I should see perform "short text" question "Question one" is answered by "Manager" with "Manager Answer one"
    And I should see perform "short text" question "Question two" is answered by "Manager" with "Manager Answer two"

  Scenario: I can see required and optional questions
    Given I log in as "john"
    When I navigate to the outstanding perform activities list page
    And I click on "John is participating subject" "link"
    And I should see "John is participating subject" in the ".tui-performUserActivity h2" "css_element"
    Then I should see perform "Question one" question is "required"
    And I should see perform "Question two" question is "required"
    When I click on "Submit" "button"
    Then I should see "Question one" has the validation error "You must answer this question"
    And I should see "Question two" has the validation error "You must answer this question"
    And I log out
    Given I log in as "harry"
    When I navigate to the outstanding perform activities list page
    And I click on "John is not participating" "link"
    Then I should see perform "Question one" question is "optional"
    And I should see perform "Question two" question is "optional"
