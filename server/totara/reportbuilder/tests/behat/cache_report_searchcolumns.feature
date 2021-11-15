@totara @totara_reportbuilder @javascript
Feature: Caching works as expected when adding search columns
  In order to check cache report builder is working when adding search columns
  As a admin
  I need to be able set up caching and add search columns as filters

  Background:
    Given this test is skipped if tables cannot be created from select
    And I log in as "admin"
    And I set the following administration settings values:
      | Enable report caching | 1 |

  Scenario: Report Builder caching works with search-columns when there is no data for "Custom Seminar Sessions Report"
    Given the following "standard_report" exist in "totara_reportbuilder" plugin:
      | fullname                       | shortname                             | source             |
      | Custom Seminar Sessions Report | report_custom_seminar_sessions_report | facetoface_summary |
    And I navigate to "Manage user reports" node in "Site administration > Reports"
    And I follow "Custom Seminar Sessions Report"
    And I switch to "Filters" tab
    And I select "Building" from the "newsearchcolumn" singleselect
    And I press "Add"
    And I switch to "Performance" tab
    And I click on "Enable Report Caching" "text"
    And I click on "Generate Now" "text"
    And I click on "Save changes" "button"
    And I should see "Last cached"
    And I should not see "Not cached yet"
