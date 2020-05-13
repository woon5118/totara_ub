@totara @perform @mod_perform @javascript @vuejs
Feature: Test performance activity embedded reports

  Background:
    Given the following "activities" exist in "mod_perform" plugin:
      | activity_name | description      | activity_type |
      | My Activity   | My Test Activity | feedback      |

  Scenario: I can not see performance activity embedded report without ids
    Given I log in as "admin"

    And I navigate to the manage perform activities page
    And I click on "Participation reporting" "link"
    And I should see "My Activity"

    And I navigate to "Manage embedded reports" node in "Site administration > Reports"
    When I click on "View" "link" in the "Performance Subject Instance (Perform)" "table_row"
    Then I should see "This report page can only show details for a single activity at a time - to select which activity's report to view,"

    And I navigate to "Manage embedded reports" node in "Site administration > Reports"
    When I click on "View" "link" in the "Participant Instance (Perform)" "table_row"
    Then I should see "This report page can only show details for a single subject instance at a time - to select which subject instance's report to view,"
