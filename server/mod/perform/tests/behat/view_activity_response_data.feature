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
    Then the "Select activity" select box should contain "Simple optional questions activity"
    And the "Select activity" select box should contain "Simple required questions activity"
    And I set the following fields to these values:
      | Select activity | Simple optional questions activity |
    When I click on "Load records" "button" in the ".tui-performReportPerformanceResponseByContent__activity" "css_element"
    Then I should see "Performance data for Simple optional questions activity"

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
    Then I should not see "Select reporting IDs"
    And the "Select activity" select box should contain "Simple optional questions activity"
    And I set the following fields to these values:
      | Select activity | Simple optional questions activity |
    When I click on "Load records" "button" in the ".tui-performReportPerformanceResponseByContent__activity" "css_element"
    Then I should see "Performance data for Simple optional questions activity"

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

  Scenario: I can see question data and view question previews
    Given I log in as "manager"

    # First check the optional questions activity.
    When I navigate to the mod perform response data report for "Simple optional questions activity" activity
    Then I should see "2 records selected"
    And the following should exist in the "element_performance_reporting" table:
      | Question text | Section title | Element type | Responding relationships | Required | Reporting ID |
      | Question one  | Part one      | Short text   | 1                        | No       |              |
      | Question two  | Part one      | Short text   | 1                        | No       |              |

    When I click on "Export all" "button"
    Then I should see "Export performance response records" in the tui modal
    And I should see "The selected records will be exported to CSV" in the tui modal

    When I click on "Cancel" "button" in the ".tui-modal" "css_element"
    Then I should not see "Export performance response records"

    When I set the following fields to these values:
      | section-involved_relationships | Appraiser |
    And I click on "submitgroupstandard[addfilter]" "button"

    # Action card should not be visible
    Then I should not see "records selected"
    Then I should not see "Export all"

    # And there should be no rows
    Then I should not see "Question one"
    Then I should not see "Question two"

    When I set the following fields to these values:
      | section-involved_relationships | Subject |
    And I click on "submitgroupstandard[addfilter]" "button"
    Then I should see "2 records selected"
    And the following should exist in the "element_performance_reporting" table:
      | Question text | Section title | Element type | Responding relationships | Required | Reporting ID |
      | Question one  | Part one      | Short text   | 1                        | No       |              |
      | Question two  | Part one      | Short text   | 1                        | No       |              |

    When I click on "Preview" "button" in the "Question one" "table_row"
    Then I should see "Question one" in the tui modal
    And I should see "(optional)" in the tui modal
    And the following fields match these values:
      | [answer_text] |  |

    When I click on "Close" "button"
    And I click on "Preview" "button" in the "Question two" "table_row"
    Then I should see "Question two" in the tui modal
    And I should see "(optional)" in the tui modal
    And the following fields match these values:
      | [answer_text] |  |

    # Now check the required questions activity.
    When I navigate to the mod perform response data report for "Simple required questions activity" activity
    Then I should see "2 records selected"
    And the following should exist in the "element_performance_reporting" table:
      | Question text | Section title | Element type | Responding relationships | Required | Reporting ID |
      | Question one  | Part one      | Short text   | 1                        | Yes      |              |
      | Question two  | Part one      | Short text   | 1                        | Yes      |              |

    When I click on "Preview" "button" in the "Question one" "table_row"
    Then I should see "Question one" in the tui modal
    And I should see "*" in the tui modal
    And the following fields match these values:
      | [answer_text] |  |

    When I click on "Close" "button"
    And I click on "Preview" "button" in the "Question two" "table_row"
    Then I should see "Question two" in the tui modal
    And I should see "*" in the tui modal
    And the following fields match these values:
      | [answer_text] |  |

  Scenario: I can see the subject instance for subject report when I have sufficient permission
    Given I log in as "sitemanager"

    When I navigate to the mod perform subject instance report for user "user1"
    Then I should see "Performance data for User1 Last1: 2 records shown"
    And the following should exist in the "subject_instance_performance_reporting" table:
      | Activity name                      | Instance number | Participants | Progress    | Availability |
      | Simple optional questions activity | 1               | 1            | Not started | Open         |
      | Simple required questions activity | 1               | 1            | Not started | Open         |

    When I navigate to the mod perform subject instance report for user "user3"
    Then I should see "Performance data for User3 Last3: 0 records shown"

    When I log out
    And I log in as "manager"
    And I navigate to the mod perform subject instance report for user "user1"
    Then I should see "Performance data for User1 Last1: 2 records shown"
    And I should see "2 records selected"

    When I navigate to the mod perform subject instance report for user "user2"
    # Export action card should not be visible
    Then I should not see "records selected"
    And I should not see "Export all"

    When I navigate to the mod perform subject instance report for user "user3"
    Then I should see "You cannot report on this subject user because you do not have permission"
