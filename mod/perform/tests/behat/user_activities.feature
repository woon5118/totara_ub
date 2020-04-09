@totara @perform @mod_perform @javascript @vuejs
Feature: Viewing and responding to perform activities

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

  Scenario: Can view and respond to activities I'm a participant in that are about me
    Given I log in as "john"
    When I navigate to the outstanding perform activities list page
    Then I should see the tui datatable contains:
      | Activity title                | Your progress   | Overall activity progress |
      | John is participating subject | Not yet started | In progress               |

    When I click on "John is participating subject" "link"
    Then I should see "John is participating subject" in the ".tui-performUserActivity h2" "css_element"
    And I should see "Part one"
    And I should see perform "short text" question "Question one" is unanswered
    And I should see perform "short text" question "Question two" is unanswered

    When I click on "Submit" "button"
    Then I should see "Question one" has the validation error "Required"
    Then I should see "Question two" has the validation error "Required"

    When I answer "short text" question "Question one" with "My first answer"
    And I answer "short text" question "Question two" with "1025" characters
    And I click on "Submit" "button"
    Then I should see "Question two" has the validation error "Please enter at no more than 1024 characters"

    When I answer "short text" question "Question two" with "1024" characters
    And I click on "Submit" "button"
    Then I should see "Activity responses saved" in the tui "success" notification toast
    And I should see "Question two" has no validation errors

    When I navigate to the outstanding perform activities list page
    Then I should see the tui datatable contains:
      | Activity title                | Your progress | Overall activity progress |
      | John is participating subject | Complete      | In progress               |

  Scenario: Can view and and respond to activities I'm a participant in but are not about me
    Given I log in as "admin"
    When I navigate to the outstanding perform activities list page
    And I click on "Activities about others" "link"
    Then I should see the tui datatable contains:
      | Activity title   | User      | Your progress   | Overall activity progress |
      | David is subject | David Two | Not yet started | In progress               |

    When I click on "David is subject" "link"
    Then I should see "David is subject" in the ".tui-performUserActivity h2" "css_element"
    And I should see "Part one"
    And I should see perform "short text" question "Question one" is unanswered
    And I should see perform "short text" question "Question two" is unanswered

    When I answer "short text" question "Question one" with "My first answer"
    And I answer "short text" question "Question two" with "My second answer"
    And I click on "Submit" "button"

    Then I should see "Activity responses saved" in the tui "success" notification toast
    And I should see "Question one" has no validation errors
    And I should see "Question two" has no validation errors

    When I navigate to the outstanding perform activities list page
    And I click on "Activities about others" "link"
    Then I should see the tui datatable contains:
      | Activity title   | User      | Your progress | Overall activity progress |
      | David is subject | David Two | Complete      | In progress               |

  Scenario: I can't visit activities that don't exist
    Given I log in as "john"
    When I navigate to the user activity page for id "99999999"
    Then I should not see "Submit"
    Then I should not see "Cancel"
    Then I should see "The requested performance activity could not be found." in the tui "error" notification banner
