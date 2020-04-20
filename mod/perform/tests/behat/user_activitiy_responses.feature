@totara @perform @mod_perform @javascript @vuejs
Feature: Viewing other responses

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                   |
      | john     | John      | One      | john.one@example.com    |
      | david    | David     | Two      | david.two@example.com   |
      | harry    | Harry     | Three    | harry.three@example.com |
    And the following "subject instances" exist in "mod_perform" plugin:
      | activity_name                 | subject_username | subject_is_participating | other_participant_username |
      | John is participating subject | john             | true                     | david                      |
      | David is subject              | david            | false                    | admin                      |
      | John is not participating     | harry            | true                     | david                      |

  Scenario: I can respond to my activities and view other non-respond activities
    Given I log in as "john"
    When I navigate to the outstanding perform activities list page
    And I click on "John is participating subject" "link"
    Then I should see "John is participating subject" in the ".tui-performUserActivity h2" "css_element"
    And I should see "Part one"
    And I should see perform activity relationship to user "Self"
    And I should see perform "short text" question "Question one" is unanswered
    And I should see perform "short text" question "Question two" is unanswered
    And I should not see "Manager response"
    And I answer "short text" question "Question one" with "John Answer one"
    And I answer "short text" question "Question two" with "John Answer two"

    And I click on "Submit" "button"
    Then I should see "Activity responses saved" in the tui "success" notification toast

    And I click show others responses
    And I should see "Manager response"
    And I should see "No response submitted"

  Scenario: Manager can respond to other activities and I can view manager responses
    Given I log in as "david"
    When I navigate to the outstanding perform activities list page
    And I click on "Activities about others" "link"
    Then I should see "John is participating subject" in the ".tui-performUserActivities" "css_element"
    And I click on "John is participating subject" "link"
    And I should see perform activity relationship to user "Manager"
    And I answer "short text" question "Question one" with "Manager Answer one"
    And I answer "short text" question "Question two" with "Manager Answer two"
    And I click on "Submit" "button"
    Then I should see "Activity responses saved" in the tui "success" notification toast
    And I click on "Close" "button"
    And I log out

    When I log in as "john"
    When I navigate to the outstanding perform activities list page
    And I click on "John is participating subject" "link"
    And I click show others responses
    And I should see perform "short text" question "Question one" is answered by "Manager" with "Manager Answer one"
    And I should see perform "short text" question "Question two" is answered by "Manager" with "Manager Answer two"
