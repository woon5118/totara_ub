@totara @perform @mod_perform @javascript
Feature: Test viewing Performance activity response data

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                   |
      | user1    | User      | One      | user.one@example.com    |
      | john     | John      | One      | john.one@example.com    |
      | manager  | manager   | user     | manager.one@example.com |
    And the following "role assigns" exist:
      | user    | role    | contextlevel | reference |
      | manager | manager | System       |           |
    And the following job assignments exist:
      | user | manager |
      | john | manager |
    And the following "permission overrides" exist:
      | capability                              | permission | role         | contextlevel | reference |
      | mod/perform:report_on_subject_responses | Allow      | staffmanager | System       |           |
    And the following "subject instances" exist in "mod_perform" plugin:
      | activity_name                      | subject_username | subject_is_participating | include_questions | include_required_questions | activity_status |
      | Simple optional questions activity | john             | true                     | true              |                            | Active          |
      | Simple required questions activity | john             | true                     | true              | true                       | Active          |

  Scenario: Test capability check and tabs
    Given I log in as "admin"
    And I toggle open the admin quick access menu
    Then I should see "Performance activity response data" in the admin quick access menu

    When I click on "Performance activity response data" "link" in the "#quickaccess-popover-content" "css_element"
    Then I should see "Browse records by user"
    And I should see "Browse records by content"

      # TODO: Correct when content is added
    When I switch to "Browse records by user" tab
    Then I should see "by_user"
    When I switch to "Browse records by content" tab
    Then I should see "by_content"

  Scenario: I can navigate to the main page from my user profile page
    Given the "miscellaneous" user profile block exists
    And I log in as "admin"
    And I follow "Profile" in the user menu

    When I click on "Performance activity response data (export)" "link"
    Then I should see "Performance activity response data"

  Scenario: I can navigate to users specific report and the main page from the teams page
    Given I log in as "manager"
    And I am on "Team" page

    When I click on "Performance data" "link"
    Then I should see "Performance data for John One"

    When I am on "Team" page
    Then I should see "Their current and historical performance records are available for you to view or export"

    When I click on "view or export" "link"
    Then I should see "Performance activity response data"

  Scenario: I can preview elements
    Given I log in as "manager"

    # First check the optional questions activity.
    When I navigate to the mod perform response data report for "Simple optional questions activity" activity
    And I click on "Preview" "button" in the "Question one" "table_row"
    Then I should see "Question one" in the ".tui-modalContent__content" "css_element"
    Then I should see "(optional)" in the ".tui-modalContent__content" "css_element"
    And the following fields match these values:
      | [answer_text] |  |

    When I click on "Close" "button"
    And I click on "Preview" "button" in the "Question two" "table_row"
    Then I should see "Question two" in the ".tui-modalContent__content" "css_element"
    Then I should see "(optional)" in the ".tui-modalContent__content" "css_element"
    And the following fields match these values:
      | [answer_text] |  |

    # Now check the required questions activity.
    When I navigate to the mod perform response data report for "Simple required questions activity" activity
    And I click on "Preview" "button" in the "Question one" "table_row"
    Then I should see "Question one" in the ".tui-modalContent__content" "css_element"
    Then I should see "*" in the ".tui-modalContent__content" "css_element"
    And the following fields match these values:
      | [answer_text] |  |

    When I click on "Close" "button"
    And I click on "Preview" "button" in the "Question two" "table_row"
    Then I should see "Question two" in the ".tui-modalContent__content" "css_element"
    Then I should see "*" in the ".tui-modalContent__content" "css_element"
    And the following fields match these values:
      | [answer_text] |  |