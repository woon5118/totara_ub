@totara @perform @mod_perform @javascript @vuejs
Feature: Test management of activity participation

  Background:
    Given the following "users" exist:
      | username  | firstname | lastname | email                  |
      | user1     | User      | One      | user.one@example.com   |
      | user2     | User      | Two      | user.two@example.com   |
      | user3     | User      | Three    | user.three@example.com |
      | user4     | User      | Four     | user.four@example.com  |
      | user5     | User      | Five     | user.five@example.com  |
      | manager1  | manager   | One      | manager1@example.com   |
      | manager2  | manager   | Two      | manager2@example.com   |
      | appraiser | appraiser | User     | appraiser@example.com  |
      | other     | other     | User     | other@example.com      |
    And the following job assignments exist:
      | user     | idnumber | manager  | managerjaidnumber | appraiser |
      | manager1 | manage1  |          |                   |           |
      | manager1 | manage2  |          |                   |           |
      | manager2 | manage   |          |                   |           |
      | user1    | job      |          |                   |           |
      | user2    | job      | manager1 | manage1           |           |
      | user3    | job      |          |                   | appraiser |
      | user4    | job      | manager1 | manage1           | appraiser |
      | user5    | job      | manager1 | manage2           |           |
      | user5    | job      | manager2 | manage            |           |

    And the following "subject instances" exist in "mod_perform" plugin:
      | activity_name          | activity_status | subject_username | subject_is_participating | other_participant_username | third_participant_username | number_repeated_instances |
      | 3 participants         | 1               | user1            | true                     |                            |                            | 3                         |
      | 3 participants         | 1               | user2            | true                     | manager1                   |                            | 3                         |
      | 3 participants         | 1               | user3            | true                     | appraiser                  |                            | 3                         |
      | 3 participants         | 1               | user4            | true                     | manager1                   | appraiser                  | 3                         |
      | for manager1           | 1               | user2            | false                    | manager1                   |                            | 1                         |
      | for manager1           | 1               | user3            | false                    | manager1                   |                            | 1                         |
      | for manager1           | 1               | user4            | false                    | manager1                   |                            | 1                         |
      | for manager1           | 1               | user5            | false                    | manager1                   |                            | 1                         |
      | for manager2 appraiser | 1               | user1            | false                    | manager2                   | appraiser                  | 2                         |
      | for manager2 appraiser | 1               | user2            | false                    | manager2                   | appraiser                  | 2                         |
      | for manager2 appraiser | 1               | user3            | false                    | manager2                   | appraiser                  | 2                         |
      | for manager2 appraiser | 1               | user4            | false                    | manager2                   | appraiser                  | 2                         |
      | for manager2 appraiser | 1               | user5            | false                    | manager2                   | appraiser                  | 2                         |

  Scenario: Manage participant tables contain the correct rows
    Given I log in as "admin"
    And I navigate to the manage perform activities page
    Then I should see "3 participants"
    And I should see "for manager1"
    And I should see "for manager2 appraiser"

    When I click on "Manage participation" "link" in the tui datatable row with "3 participants" "Name"
    Then the following should exist in the "perform_restricted_subject_instance" table:
      | Subject name | Instance number | Participants |
      | User Four    | 3               | 3            |
      | User Four    | 2               | 3            |
      | User Four    | 1               | 3            |
      | User One     | 3               | 1            |
      | User One     | 2               | 1            |
      | User One     | 1               | 1            |
      | User Three   | 3               | 2            |
      | User Three   | 2               | 2            |
      | User Three   | 1               | 2            |
      | User Two     | 3               | 2            |
      | User Two     | 2               | 2            |
      | User Two     | 1               | 2            |

    When I click on "Back to all performance activities" "link"
    And I click on "Manage participation" "link" in the tui datatable row with "for manager1" "Name"
    Then the following should exist in the "perform_restricted_subject_instance" table:
      | Subject name | Instance number | Participants |
      | User Five    | 1               | 1            |
      | User Four    | 1               | 1            |
      | User Three   | 1               | 1            |
      | User Two     | 1               | 1            |

    When I click on "Back to all performance activities" "link"
    And I click on "Manage participation" "link" in the tui datatable row with "for manager2 appraiser" "Name"
    Then the following should exist in the "perform_restricted_subject_instance" table:
      | Subject name | Instance number | Participants |
      | User Five    | 1               | 2            |
      | User Five    | 2               | 2            |
      | User Four    | 1               | 2            |
      | User Four    | 2               | 2            |
      | User One     | 1               | 2            |
      | User One     | 2               | 2            |
      | User Three   | 1               | 2            |
      | User Three   | 2               | 2            |
      | User Two     | 1               | 2            |
      | User Two     | 2               | 2            |

    When I click on "2" "link" in the "User Three 99999997" "table_row"
    Then I should see "Participant instances: 2 records shown"
    And the following should exist in the "perform_restricted_participant_instance" table:
      | Participant name | Subject name | Relationship |
      | appraiser User   | User Three   | Appraiser    |
      | manager Two      | User Three   | Manager      |

    When I click on "Show all" "link"
    Then I should see "Participant instances: 20 records shown"

    When I click on "Subject instances" "link"
    And I click on "2" "link" in the "User Three 99999997" "table_row"
    And I click on "1" "link" in the "appraiser User User Three 99999997" "table_row"
    Then I should see "Participant sections: 1 records shown"
    And the following should exist in the "perform_restricted_participant_section" table:
      | Participant name | Section title | Subject name | Relationship |
      | appraiser User   | Part one      | User Three   | Appraiser    |

    When I click on "Show all" "link"
    Then I should see "Participant sections: 10 records shown"

  Scenario: Open/close participant subject instances
    Given I log in as "admin"
    And I navigate to the manage perform activities page
    And I click on "Manage participation" "link" in the tui datatable row with "3 participants" "Name"
    Then the following should exist in the "perform_restricted_subject_instance" table:
      | Subject's full name | Instance number | Participants |
      | User Four           | 1               | 3            |
      | User Four           | 2               | 3            |
      | User Four           | 3               | 3            |
      | User One            | 1               | 1            |
      | User One            | 2               | 1            |
      | User One            | 3               | 1            |
      | User Three          | 1               | 2            |
      | User Three          | 2               | 2            |
      | User Three          | 3               | 2            |
      | User Two            | 1               | 2            |
      | User Two            | 2               | 2            |
      | User Two            | 3               | 2            |
    When I click on "Close" "button" in the "User Three 99999997" "table_row"
    Then I should see "Close subject instance" in the tui modal
    And I confirm the tui confirmation modal
    Then I should see "Subject instance and all its participant instances closed"
    When I click on "Reopen" "button" in the "User Three 99999997" "table_row"
    Then I should see "Reopen subject instance" in the tui modal
    And I confirm the tui confirmation modal
    Then I should see "Subject instance and all its participant instances reopened"
    When I click on "Close" "button" in the "User Three 99999997" "table_row"
    Then I should see "Close subject instance" in the tui modal

  Scenario: Manage participants top level instance/section filtering
    Given I log in as "admin"
    And I navigate to the manage perform activities page
    And I click on "Manage participation" "link" in the tui datatable row with "3 participants" "Name"

    # Click on the participants count for user one, instance 3 (99999999 - 3 = 99999996)
    When I click on "1" "link" in the "User One 99999996" "table_row"
    Then I should see "Showing results for 1 subject instance only"
    And I should not see "User Two"
    And I should not see "User Three"
    And I should see "User One" in the "Subject full name" line of the perform activities instance info card
    And I should see "" in the "Job assignment" line of the perform activities instance info card
    And I should see "3" in the "Instance count" line of the perform activities instance info card
    And I should see "##today##j F Y##" in the "Creation date" line of the perform activities instance info card

    When I click on "Show all" "link"
    Then I should not see "Showing results for 1 subject instance only"
    And I should see "User Two"
    And I should see "User Three"

    When I click on "Subject instances" "link"
    And I click on "1" "link" in the "User One 99999996" "table_row"
    And I click on "1" "link" in the "User One User One 99999998" "table_row"
    Then I should see "Showing results for 1 participant instance only"
    And I should not see "User Two"
    And I should not see "User Three"
    And I should see "User One" in the "Participant full name" line of the perform activities instance info card
    And I should see "User One" in the "Subject full name" line of the perform activities instance info card
    And I should see "Subject" in the "Relationship" line of the perform activities instance info card
    And I should see "##today##j F Y##" in the "Creation date" line of the perform activities instance info card

    When I click on "Show all" "link"
    Then I should not see "Showing results for 1 participant instance only"
    And I should see "User Two"
    And I should see "User Three"
