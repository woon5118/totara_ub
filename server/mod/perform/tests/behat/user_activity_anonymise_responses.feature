@totara @perform @mod_perform @javascript @vuejs
Feature: User activity anonymise responses

  Background:
    Given the following "users" exist:
      | username | firstname | lastname  | email             |
      | subject  | sam       | Subject   | sean@example.com  |
      | manager  | john      | Manager   | manny@example.com |
      | appraiser| kyla      | Appraiser | ava@example.com   |
    And the following "subject instances" exist in "mod_perform" plugin:
      | activity_name                                   | subject_username | subject_is_participating | other_participant_username | third_participant_username | relationships_can_view      | anonymous_responses |
      | Anonymise responses activity                    | subject          | true                     | manager                    | appraiser                  | manager, appraiser, subject | true                |
      | Activity One                                    | subject          | true                     | manager                    | appraiser                  | manager, appraiser, subject | false               |

  Scenario: I can view anonymise responses
    Given I log in as "subject"
    And I navigate to the outstanding perform activities list page
    And I click on "Anonymise responses activity" "link"
    When I click show others responses
    Then I should not see "Manager response"
    And I should not see "Appraiser response"
    And I should see "Others’ responses"
    And I should see "No response submitted"

  Scenario: manager can submit anonymise responses
    Given I log in as "manager"
    And I navigate to the outstanding perform activities list page
    And I click on "Activities about others" "link"
    And I click on "Anonymise responses activity" "link"
    And I answer "short text" question "Question one" with "Manager Answer one"
    And I answer "short text" question "Question two" with "Manager Answer two"
    And I click on "Submit" "button"
    And I confirm the tui confirmation modal
    And I close the tui notification toast
    And I log out
    When I log in as "subject"
    And I navigate to the outstanding perform activities list page
    And I click on "Anonymise responses activity" "link"
    And I click show others responses
    Then I should not see "Manager response"
    And I should not see "Appraiser response"
    And I should see "Others’ responses"
    And I should see "Manager Answer one"
    And I should see "Manager Answer two"
