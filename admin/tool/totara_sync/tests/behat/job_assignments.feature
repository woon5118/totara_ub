@_file_upload @javascript @tool @tool_totara_sync @totara @totara_job
Feature: Use job assignments feature in HR sync
  In order to test HR import of users with job assignments
  I must log in as an admin and import from a CSV file

  Background:
    Given I log in as "admin"
    And I navigate to "Manage authentication" node in "Site administration > Plugins > Authentication"
    And I set the following fields to these values:
      | User deletion | Keep username, email and ID number |
    And I press "Save changes"
    And I navigate to "General settings" node in "Site administration > HR Import"
    And I set the following fields to these values:
        | File Access | Upload Files |
    And I press "Save changes"
    And I navigate to "Manage elements" node in "Site administration > HR Import > Elements"
    And I "Enable" the "User" HR Import element
    And I navigate to "User" node in "Site administration > HR Import > Elements"
    And I set the following fields to these values:
      | Source | CSV |
    And I press "Save changes"
    And I navigate to "CSV" node in "Site administration > HR Import > Sources > User"
    And I should see "\"firstname\""
    And I should see "\"lastname\""
    And I should see "\"email\""
    And I should not see "\"jobassignmentidnumber\""
    And I should not see "\"managerjobassignmentidnumber\""
    And I press "Save changes"

  Scenario: Configure HR import source without multiple jobs enabled
    Given I navigate to "User" node in "Site administration > HR Import > Elements"
    And I set the following fields to these values:
      | Multiple job assignments | 0 |
    And I press "Save changes"
    And I navigate to "CSV" node in "Site administration > HR Import > Sources > User"
    And I should not see "\"jobassignmentidnumber\""
    And I should not see "\"managerjobassignmentidnumber\""
    And I set the following fields to these values:
      | Manager | 1 |
    And I press "Save changes"
    And I should not see "\"jobassignmentidnumber\""
    And I should not see "\"managerjobassignmentidnumber\""
    And I should see "\"manageridnumber\""
    And I set the following fields to these values:
      | Manager's job assignment | 1 |
    And I press "Save changes"
    And I should see "\"managerjobassignmentidnumber\""
    And I should see "\"manageridnumber\""
    And I set the following fields to these values:
      | Manager | 0 |
    And I press "Save changes"
    And I should not see "\"managerjobassignmentidnumber\""
    And I should not see "\"manageridnumber\""

  Scenario: Configure HR import source with multiple job assignments enabled
    Given I navigate to "User" node in "Site administration > HR Import > Elements"
    And I set the following fields to these values:
      | Multiple job assignments | 1 |
    And I press "Save changes"
    And I navigate to "CSV" node in "Site administration > HR Import > Sources > User"
    And I should see "\"jobassignmentidnumber\""
    And I should not see "\"managerjobassignmentidnumber\""
    And I set the following fields to these values:
      | Manager | 1 |
    When I press "Save changes"
    Then I should see "\"jobassignmentidnumber\""
    And I should see "\"managerjobassignmentidnumber\""
    And I should see "\"manageridnumber\""

  Scenario: Upload CSV with multiple job assignments enabled without managers
    And I navigate to "User" node in "Site administration > HR Import > Elements"
    And I set the following fields to these values:
      | Multiple job assignments | 1 |
    And I press "Save changes"
    And I navigate to "Upload HR Import files" node in "Site administration > HR Import > Sources"
    And I upload "admin/tool/totara_sync/tests/fixtures/users_ja_ok.csv" file to "CSV" filemanager
    And I press "Upload"
    And I should see "HR Import files uploaded successfully"
    And I navigate to "Run HR Import" node in "Site administration > HR Import"
    And I press "Run HR Import"
    And I should see "Running HR Import cron...Done!"
    And I navigate to "HR Import Log" node in "Site administration > HR Import"
    And I should not see "Error" in the "#totarasynclog" "css_element"

    When I navigate to "Browse list of users" node in "Site administration > Users > Accounts"
    And I click on "manager01 manager01" "link"
    Then I should see "Unnamed job assignment (ID: m1)"
    And I should not see "Unnamed job assignment (ID: m2)"
    And I should not see "Unnamed job assignment (ID: m3)"
    And I navigate to "Browse list of users" node in "Site administration > Users > Accounts"
    And I click on "manager04 manager04" "link"
    And I should see "Unnamed job assignment (ID: m2)"
    And I should not see "Unnamed job assignment (ID: m1)"
    And I should not see "Unnamed job assignment (ID: m1)"
    And I navigate to "Browse list of users" node in "Site administration > Users > Accounts"
    And I click on "learner11 learner11" "link"
    And I should see "Unnamed job assignment (ID: l1)"

  Scenario: Upload and then update CSV with multiple job assignments enabled with managers
    And I navigate to "User" node in "Site administration > HR Import > Elements"
    And I set the following fields to these values:
      | Multiple job assignments | 1 |
    And I press "Save changes"
    And I navigate to "CSV" node in "Site administration > HR Import > Sources > User"
    And I set the following fields to these values:
      | Appraiser | 1 |
      | Manager   | 1 |
    And I press "Save changes"
    And I navigate to "Upload HR Import files" node in "Site administration > HR Import > Sources"
    And I upload "admin/tool/totara_sync/tests/fixtures/users_ja_ok.csv" file to "CSV" filemanager
    And I press "Upload"
    And I should see "HR Import files uploaded successfully"
    And I navigate to "Run HR Import" node in "Site administration > HR Import"
    And I press "Run HR Import"
    And I should see "Running HR Import cron...Done!"
    And I navigate to "HR Import Log" node in "Site administration > HR Import"
    And I should not see "Error" in the "#totarasynclog" "css_element"

    # Check
    And I navigate to "Browse list of users" node in "Site administration > Users > Accounts"
    And I click on "manager01 manager01" "link"
    And I should see "Unnamed job assignment (ID: m1)"
    And I should see "Unnamed job assignment (ID: m2)"
    And I should see "Unnamed job assignment (ID: m3)"
    And I navigate to "Browse list of users" node in "Site administration > Users > Accounts"
    And I click on "manager04 manager04" "link"
    And I should see "Unnamed job assignment (ID: m1)"
    And I should see "Unnamed job assignment (ID: m2)"
    And I should see "Unnamed job assignment (ID: m3)"
    And I navigate to "Browse list of users" node in "Site administration > Users > Accounts"
    And I click on "learner11 learner11" "link"
    And I click on "Unnamed job assignment (ID: l1)" "link"
    And I should see "appraiser02 appraiser02"
    And I should see "manager03 manager03 (manager03@example.com) - Unnamed job assignment (ID: m2)"

    # Upload update
    And I navigate to "Upload HR Import files" node in "Site administration > HR Import > Sources"
    And I upload "admin/tool/totara_sync/tests/fixtures/users_ja_ok_update.csv" file to "CSV" filemanager
    And I press "Upload"
    And I should see "HR Import files uploaded successfully"
    And I navigate to "Run HR Import" node in "Site administration > HR Import"
    And I press "Run HR Import"
    And I should see "Running HR Import cron...Done!"
    And I navigate to "HR Import Log" node in "Site administration > HR Import"
    And I should not see "Error" in the "#totarasynclog" "css_element"

    # Check
    And I navigate to "Browse list of users" node in "Site administration > Users > Accounts"
    And I click on "learner11 learner11" "link"
    And I click on "Unnamed job assignment (ID: l1)" "link"
    And I should see "appraiser02 appraiser02"
    And I should see "manager03 manager03 (manager03@example.com) - Unnamed job assignment (ID: m2)"
    And I navigate to "Browse list of users" node in "Site administration > Users > Accounts"
    And I click on "learner11 learner11" "link"
    And I click on "Unnamed job assignment (ID: l2)" "link"
    And I should see "appraiser05 appraiser05"
    And I should see "manager05 manager05 (manager05@example.com) - Unnamed job assignment (ID: m3)"

  Scenario: Upload and then update CSV with multiple assignments disabled
    And I navigate to "User" node in "Site administration > HR Import > Elements"
    And I set the following fields to these values:
      | Multiple job assignments | 0 |
    And I press "Save changes"
    And I navigate to "CSV" node in "Site administration > HR Import > Sources > User"
    And I set the following fields to these values:
      | Appraiser      | 1 |
      | Manager        | 1 |
      | Job assignment | 1 |
    And I press "Save changes"
    And I navigate to "Upload HR Import files" node in "Site administration > HR Import > Sources"
    And I upload "admin/tool/totara_sync/tests/fixtures/users_ja_ok.csv" file to "CSV" filemanager
    And I press "Upload"
    And I should see "HR Import files uploaded successfully"
    And I navigate to "Run HR Import" node in "Site administration > HR Import"
    And I press "Run HR Import"
    And I should see "Running HR Import cron...Done!"
    And I navigate to "HR Import Log" node in "Site administration > HR Import"
    And I should not see "Error" in the "#totarasynclog" "css_element"

    # Check
    And I navigate to "Browse list of users" node in "Site administration > Users > Accounts"
    And I click on "learner11 learner11" "link"
    And I should see "Unnamed job assignment (ID: l1)"
    And I click on "Unnamed job assignment (ID: l1)" "link"
    And I should see "appraiser02 appraiser02"
    And I should see "manager03 manager03 (manager03@example.com) - Unnamed job assignment (ID: m1)"

    # Upload update
    And I navigate to "Upload HR Import files" node in "Site administration > HR Import > Sources"
    And I upload "admin/tool/totara_sync/tests/fixtures/users_ja_ok_update.csv" file to "CSV" filemanager
    And I press "Upload"
    And I should see "HR Import files uploaded successfully"
    And I navigate to "Run HR Import" node in "Site administration > HR Import"
    And I press "Run HR Import"
    And I should see "Running HR Import cron...Done!"
    And I navigate to "HR Import Log" node in "Site administration > HR Import"
    And I should not see "Error" in the "#totarasynclog" "css_element"

    # Check
    And I navigate to "Browse list of users" node in "Site administration > Users > Accounts"
    And I click on "learner11 learner11" "link"
    And I should see "Unnamed job assignment (ID: l1)"
    And I click on "Unnamed job assignment (ID: l1)" "link"
    And I should see "appraiser05 appraiser05"
    And I should see "manager05 manager05 (manager05@example.com) - Unnamed job assignment (ID: m5)"

  Scenario: Upload CSV with manageridnumber data missing while managerjobassignmentidnumber is given should fail
    Given I navigate to "CSV" node in "Site administration > HR Import > Sources > User"
    And I set the following fields to these values:
      | Manager                  | 1 |
      | Manager's job assignment | 1 |
    And I press "Save changes"
    And I navigate to "Upload HR Import files" node in "Site administration > HR Import > Sources"
    And I upload "admin/tool/totara_sync/tests/fixtures/users_ja_managerid_fail.csv" file to "CSV" filemanager
    And I press "Upload"
    And I should see "HR Import files uploaded successfully"
    And I navigate to "Run HR Import" node in "Site administration > HR Import"
    And I press "Run HR Import"
    And I should see "there have been some problems"
    And I navigate to "HR Import Log" node in "Site administration > HR Import"
    And I should see "Error" in the "#totarasynclog" "css_element"
    And I should see "Manager's id number cannot be empty."

  Scenario: Upload CSV with jobassignmentidnumber data missing and multiple jobs enabled should fail
    Given I navigate to "User" node in "Site administration > HR Import > Elements"
    And I set the following fields to these values:
      | Multiple job assignments | 1 |
    And I press "Save changes"
    And I navigate to "CSV" node in "Site administration > HR Import > Sources > User"
    And I set the following fields to these values:
      | Manager                  | 1 |
    And I press "Save changes"
    And I navigate to "Upload HR Import files" node in "Site administration > HR Import > Sources"
    And I upload "admin/tool/totara_sync/tests/fixtures/users_ja_jaid_missing.csv" file to "CSV" filemanager
    And I press "Upload"
    And I should see "HR Import files uploaded successfully"
    And I navigate to "Run HR Import" node in "Site administration > HR Import"
    And I press "Run HR Import"
    And I should see "there have been some problems"
    And I navigate to "HR Import Log" node in "Site administration > HR Import"
    And I should see "Error" in the "#totarasynclog" "css_element"
    And I should see "Job assignment cannot be empty."

  Scenario: Upload CSV with jobassignmentidnumber data missing and multiple jobs disabled should pass
    Given I navigate to "User" node in "Site administration > HR Import > Elements"
    And I set the following fields to these values:
      | Multiple job assignments | 0 |
    And I press "Save changes"
    And I navigate to "CSV" node in "Site administration > HR Import > Sources > User"
    And I set the following fields to these values:
      | Manager                  | 1 |
      | Manager's job assignment | 1 |
    And I press "Save changes"
    And I navigate to "Upload HR Import files" node in "Site administration > HR Import > Sources"
    And I upload "admin/tool/totara_sync/tests/fixtures/users_ja_jaid_missing.csv" file to "CSV" filemanager
    And I press "Upload"
    And I should see "HR Import files uploaded successfully"
    And I navigate to "Run HR Import" node in "Site administration > HR Import"
    And I press "Run HR Import"
    And I should see "Running HR Import cron...Done!"
    And I navigate to "HR Import Log" node in "Site administration > HR Import"
    And I should not see "Error" in the "#totarasynclog" "css_element"

  Scenario: Upload incorrect CSV with jobassignmentidnumber column missing and multiple jobs enabled
    Given I navigate to "User" node in "Site administration > HR Import > Elements"
    And I set the following fields to these values:
      | Multiple job assignments | 1 |
    And I press "Save changes"
    And I navigate to "CSV" node in "Site administration > HR Import > Sources > User"
    And I set the following fields to these values:
      | Manager                  | 1 |
    And I press "Save changes"
    And I navigate to "Upload HR Import files" node in "Site administration > HR Import > Sources"
    And I upload "admin/tool/totara_sync/tests/fixtures/users_ja_no_jaid_column.csv" file to "CSV" filemanager
    And I press "Upload"
    And I should see "HR Import files uploaded successfully"
    And I navigate to "Run HR Import" node in "Site administration > HR Import"
    And I press "Run HR Import"
    And I should see "there have been some problems"
    And I navigate to "HR Import Log" node in "Site administration > HR Import"
    And I should see "Error" in the "#totarasynclog" "css_element"