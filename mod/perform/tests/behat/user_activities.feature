@totara @perform @mod_perform @javascript @vuejs
Feature: Viewing and responding to perform activities

  Background:
    Given the following "users" exist:
      | username          | firstname | lastname | email                              |
      | john              | John      | One      | john.one@example.com               |
      | david             | David     | Two      | david.two@example.com              |
      | harry             | Harry     | Three    | harry.three@example.com            |
      | manager-appraiser | combined  | Three    | manager-appraiser.four@example.com |
    And the following "subject instances" exist in "mod_perform" plugin:
      | activity_name                 | subject_username | subject_is_participating | other_participant_username |
      | John is participating subject | john             | true                     | david                      |
      | David is subject              | david            | false                    | john                       |
      | John is not participating     | harry            | true                     | david                      |
    And the following "subject instances with single user manager-appraiser" exist in "mod_perform" plugin:
      | activity_name                 | subject_username | manager_appraiser_username |
      | single user manager-appraiser | john             | manager-appraiser          |

  Scenario: Can view and respond to activities I'm a participant in that are about me
    Given I log in as "john"
    When I navigate to the outstanding perform activities list page
    Then I should see the tui datatable contains:
      | Activity title                | Type      | Overall progress | Your progress   |
      | single user manager-appraiser | Appraisal | Not yet started  | Not yet started |
      | John is participating subject | Appraisal | Not yet started  | Not yet started |

    When I click on "John is participating subject" "link"
    Then I should see "John is participating subject" in the ".tui-performUserActivity h2" "css_element"
    And I should see that show others responses is toggled "off"
    And I should see perform "short text" question "Question one" is unanswered
    And I should see perform "short text" question "Question two" is unanswered

    When I answer "short text" question "Question one" with "My first answer"
    And I answer "short text" question "Question two" with "1025" characters
    And I click on "Submit" "button"
    And I close the tui notification toast
    Then I should see "Question two" has the validation error "Please enter at no more than 1024 characters"

    When I answer "short text" question "Question two" with "1024" characters
    And I click on "Submit" "button"
    And I confirm the tui confirmation modal

    Then I should see "Performance activities"
    And the "Your activities" tui tab should be active
    And I should see "Section submitted" in the tui "success" notification toast
    And I should see the tui datatable contains:
      | Activity title                | Type      | Overall progress | Your progress  |
      | single user manager-appraiser | Appraisal | Not yet started  | Not yet started|
      | John is participating subject | Appraisal | In progress      | Complete       |

  Scenario: Can view and and respond to activities I'm a participant in but are not about me
    Given I log in as "john"
    When I navigate to the outstanding perform activities list page
    And I click on "Activities about others" "link"
    Then I should see the tui datatable contains:
      | Activity title   | Type      | User     | Overall progress | Your progress   |
      | David is subject | Appraisal |David Two | Not yet started  | Not yet started |

    When I click on "David is subject" "link"
    Then I should see "David is subject" in the ".tui-participantContent__header" "css_element"
    And I should see that show others responses is toggled "off"
    And I should see perform "short text" question "Question one" is unanswered
    And I should see perform "short text" question "Question two" is unanswered

    When I answer "short text" question "Question one" with "My first answer"
    And I answer "short text" question "Question two" with "My second answer"
    Then I should see "Question one" has no validation errors
    And I should see "Question two" has no validation errors

    When I click on "Submit" "button"
    And I confirm the tui confirmation modal
    Then I should see "Performance activities"
    And I should see "Section submitted." in the tui "success" notification toast
    And the "Activities about others" tui tab should be active
    And I should see the tui datatable contains:
      | Activity title   | Type      | User      | Overall progress | Your progress |
      | David is subject | Appraisal | David Two | Complete         | Complete      |

  Scenario: Can view and and respond to activities I have multiple roles in
    Given I log in as "manager-appraiser"
    When I navigate to the outstanding perform activities list page
    And I click on "Activities about others" "link"
    Then I should see the tui datatable contains:
      | Activity title                | Type      | User     | Relationship to user | Overall progress | Your progress   |
      | single user manager-appraiser | Appraisal | John One | Manager, Appraiser   | Not yet started  | Not yet started |

    When I click on "single user manager-appraiser" "button"
    Then I should see "Select relationship to continue" in the ".tui-modalContent" "css_element"
    And the "Manager (Not yet started)" radio button is selected
    And the "Appraiser (Not yet started)" radio button is not selected

    When I click on "Continue" "button"
    Then I should see "single user manager-appraiser" in the ".tui-performUserActivity h2" "css_element"
    And I should see perform activity relationship to user "Manager"
    And I should see that show others responses is toggled "off"
    And I should see perform "short text" question "Question one" is unanswered

    When I answer "short text" question "Question one" with "My first answer as manager"
    And I click on "Submit" "button"
    And I confirm the tui confirmation modal
    Then I should see "Performance activities"
    And I should see "Section submitted." in the tui "success" notification toast

    When I click on "single user manager-appraiser" "button"
    Then I should see "Select relationship to continue" in the ".tui-modalContent" "css_element"
    And the "Manager (Complete)" radio button is selected
    And the "Appraiser (Not yet started)" radio button is not selected

    When I click on the "Appraiser (Not yet started)" tui radio
    And I click on "Continue" "button"
    Then I should see "single user manager-appraiser" in the ".tui-performUserActivity h2" "css_element"
    And I should see perform activity relationship to user "Appraiser"
    And I should see that show others responses is toggled "off"
    And I should see perform "short text" question "Question one" is unanswered

    When I answer "short text" question "Question one" with "My first answer as appraiser"
    And I click on "Submit" "button"
    And I confirm the tui confirmation modal
    Then I should see "Performance activities"
    And I should see "Section submitted." in the tui "success" notification toast

    When I click on "single user manager-appraiser" "button"
    Then I should see "Select relationship to continue" in the ".tui-modalContent" "css_element"
    And the "Manager (Complete)" radio button is selected
    And the "Appraiser (Complete)" radio button is not selected

    When I click on "Continue" "button"
    Then I should see "single user manager-appraiser" in the ".tui-performUserActivity h2" "css_element"
    And I should see perform activity relationship to user "Manager"
    And I should see that show others responses is toggled "on"
    And I should see "Appraiser response"
    And I should see "My first answer as appraiser"

  Scenario: First access of a section changes both my progress and overall progress to 'In Progress'
    Given I log in as "john"
    When I navigate to the outstanding perform activities list page
    Then I should see the tui datatable contains:
      | Activity title                | Type      | Overall progress | Your progress   |
      | single user manager-appraiser | Appraisal | Not yet started  | Not yet started |
      | John is participating subject | Appraisal | Not yet started  | Not yet started |

    When I click on "John is participating subject" "link"
    Then I should see "John is participating subject" in the ".tui-participantContent__header" "css_element"

    When I navigate to the outstanding perform activities list page
    Then I should see the tui datatable contains:
      | Activity title                | Type      | Overall progress | Your progress   |
      | single user manager-appraiser | Appraisal | Not yet started  | Not yet started |
      | John is participating subject | Appraisal | In progress      | In progress     |
