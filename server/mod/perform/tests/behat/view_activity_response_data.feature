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

  Scenario: I can navigate to users specific report from the teams pages
    Given I log in as "manager"
    And I click on "Team" in the totara menu

    When I click on "Performance data" "link"
    Then I should see "Performance data for John One"