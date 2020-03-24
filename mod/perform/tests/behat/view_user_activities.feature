@totara @mod_perform @javascript
Feature: Viewing an end users outstanding perform activities

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                   |
      | John     | John      | One      | john.one@example.com    |
      | David    | David     | Two      | david.two@example.com   |
      | Harry    | Harry     | Three    | harry.three@example.com |
    And the following "subject instances" exist in "mod_perform" plugin:
      | activity_name                 | subject_username | subject_is_participating | other_participant_username |
      | John is participating subject | John             | true                     | David                      |
      | David is subject              | David            | false                    | Admin                      |
      | John is not participating     | Harry            | true                     | David                      |

  Scenario: Can view and visit activities I'm a participant in that are about me
    Given I log in as "John"
    When I navigate to the outstanding perform activities list page
    Then I should see the tui datatable contains:
      | Activity title                | Status      |
      | John is participating subject | In progress |

    When I click on "John is participating subject" "link"
    Then I should see "John is participating subject" in the ".tui-performUserActivity h2" "css_element"

  Scenario: Can view and visit activities I'm a participant in that are not about me
    Given I log in as "admin"
    When I navigate to the outstanding perform activities list page
    When I click on "Activities about others" "link"
    Then I should see the tui datatable contains:
      | Activity title   | User      | Status      |
      | David is subject | David Two | In progress |

    When I click on "David is subject" "link"
    Then I should see "David is subject" in the ".tui-performUserActivity h2" "css_element"

  Scenario: I can't visit activities that don't exist
    Given I log in as "John"
    When I navigate to the user activity page for id "99999999"
    Then I should see "The requested performance activity could not be found." in the tui "error" notification banner


