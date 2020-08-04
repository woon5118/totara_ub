@totara @perform @mod_perform @javascript
Feature: Test viewing Performance activity response data

  Background:
    Given the following "users" exist:
      | username    | firstname   | lastname | email                   |
      | user1       | User1       | Last1    | user1@example.com       |
      | user2       | User2       | Last2    | user2@example.com       |
      | user3       | User3       | Last3    | user3@example.com       |
      | user4       | User4       | Last4    | user4@example.com       |
      | manager     | manager     | user     | manager.one@example.com |
      | sitemanager | sitemanager | user     | sitemanager@example.com |
    And the following "role assigns" exist:
      | user        | role    | contextlevel | reference |
      | sitemanager | manager | System       |           |
    And the following job assignments exist:
      | user  | manager | appraiser |
      | user1 | manager |           |
      | user2 | manager |           |
      | user3 |         | manager   |
    And the following "permission overrides" exist:
      | capability                                   | permission | role         | contextlevel | reference |
      | mod/perform:report_on_subject_responses      | Allow      | staffmanager | System       |           |
      | mod/perform:report_on_all_subjects_responses | Allow      | manager      | System       |           |
    And the following "subject instances" exist in "mod_perform" plugin:
      | activity_name                      | subject_username | subject_is_participating | include_questions | include_required_questions | activity_status |
      | Simple optional questions activity | user1            | true                     | true              |                            | Active          |
      | Simple required questions activity | user1            | true                     | true              | true                       | Active          |

  Scenario: A user with the global capability can access the performance activity response data from the admin menu
    Given I log in as "sitemanager"
    And I toggle open the admin quick access menu
    Then I should see "Performance activity response data" in the admin quick access menu
    When I navigate to "Performance activities > Performance activity response data" in site administration
    And I switch to "Browse records by user" tab
    Then I should see "User1"
    And I should see "User4"
    When I switch to "Browse records by content" tab
    # TODO Then I should see all stuff, as granted by the global cap
    Then I should see "by_content"

  Scenario: A user with per-user capabilities can see the correct user performance activity response data
    Given I log in as "manager"
    When I am on "Team" page
    And I click on "view or export" "link"
    And I switch to "Browse records by user" tab
    Then I should see "Subject users: 2 records shown"
    And I should see "User1"
    And I should see "User2"
    And I should not see "User3"
    And I should not see "User4"

  Scenario: A user with per-user capabilities can see the correct content performance activity response data
    Given I log in as "manager"
    When I am on "Team" page
    And I click on "view or export" "link"
    And I switch to "Browse records by content" tab
    # TODO Then I should see only the specific stuff granted by the individual cap
    Then I should see "by_content"

  Scenario: I can navigate to the main performance activity response page from my user profile page
    Given the "miscellaneous" user profile block exists
    And I log in as "sitemanager"
    And I follow "Profile" in the user menu
    And I click on "Performance activity response data (export)" "link"
    Then I should see "Performance activity response data"

  Scenario: I can navigate to a specific user's performance activity response report and the main performance activity response from the teams page
    Given I log in as "manager"
    When I am on "Team" page
    And I click on "Performance data" "link" in the "User2" "table_row"
    Then I should see "Performance data for User2"

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