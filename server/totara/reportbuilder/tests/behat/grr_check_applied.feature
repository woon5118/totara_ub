@totara @totara_reportbuilder @javascript
Feature: Check global report restrictions default settings
  In order to use Global report restrictions
  As a user
  I need to confirm the correct behavior

  Background:
    Given I am on a totara site
    And I enable the "appraisals" advanced feature
    And I log in as "admin"
    And I set the following administration settings values:
      | Enable report restrictions | 1 |

  Scenario: Check default embedded report status
    Given I navigate to "Manage embedded reports" node in "Site administration > Reports"
    And I click on "Edit this report" "button"
    And I switch to "Columns" tab
    And I add the "Global report restrictions" column to the report
    And I follow "Manage embedded reports"
  # Only check embedded reports that are likely to appear on the first page (to avoid having to navigate or increase page size)
    And I should see "No" in the "Alerts (View)" "table_row"
    And I should see "No" in the "Appraisal Detail (legacy) (View)" "table_row"
    And I should see "No" in the "Appraisal Status (legacy) (View)" "table_row"
    And I should see "No" in the "Audience Admin Screen (View)" "table_row"
    And I should see "No" in the "Audience Orphaned Users (View)" "table_row"
    And I should see "No" in the "Audience members (View)" "table_row"
    And I should see "No" in the "Completion import: Certification status (View)" "table_row"
    And I should see "No" in the "Completion import: Course status (View)" "table_row"
    And I should see "No" in the "Goal Status (View)" "table_row"
    And I should see "No" in the "Goal Status History (View)" "table_row"
    And I should see "No" in the "Goal Summary (View)" "table_row"

  Scenario: Check default created report status
    Given the following "standard_report" exist in "totara_reportbuilder" plugin:
      | fullname        | shortname              | source         |
      | Audience report | report_audience_report | cohort_members |
    Given I navigate to "Manage user reports" node in "Site administration > Reports"
    And I follow "Audience report"
    And I switch to "Content" tab
    Then the field "Global report restrictions" matches value "1"
    When I follow "Manage user reports"
    And I click on "Edit this report" "button"
    And I switch to "Columns" tab
    And I add the "Global report restrictions" column to the report
    And I follow "View This Report"
    Then I should see "Yes" in the "Audience report (View)" "table_row"
    When I follow "Audience report"
    And I switch to "Content" tab
    And I set the field "Global report restrictions" to "0"
    And I press "Save changes"
    And I follow "Manage user reports"
    Then I should see "No" in the "Audience report (View)" "table_row"
