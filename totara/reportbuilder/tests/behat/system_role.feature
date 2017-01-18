@totara @totara_reportbuilder @javascript
Feature: Verify the User System Role column and filter functions correctly on User report source.

  Background:
    Given I am on a totara site
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | learner1 | Bob1      | Learner1 | learner1@example.com |
      | learner2 | Bob2      | Learner2 | learner2@example.com |
      | learner3 | Bob3      | Learner3 | learner3@example.com |
      | learner4 | Bob4      | Learner4 | learner4@example.com |

    When I log in as "admin"
    And I navigate to "Manage reports" node in "Site administration > Reports > Report builder"
    And I set the following fields to these values:
      | Report Name | My User Report |
      | Source      | User           |
    And I press "Create report"
    Then I should see "Edit Report 'My User Report'"

    When I switch to "Columns" tab
    And I set the field "newcolumns" to "User System Role"
    And I press "Save changes"
    Then I should see "Columns updated"

    When I switch to "Filters" tab
    And I set the field "newstandardfilter" to "User System Role"
    And I press "Save changes"
    Then I should see "Filters updated"

    When I navigate to "Assign system roles" node in "Site administration > Users > Permissions"
    And I follow "Site Manager"
    And I set the field "Potential users" to "Bob1 Learner1 (learner1@example.com)"
    And I press "Add"
    Then I should see "Bob1 Learner1 (learner1@example.com)" in the "#removeselect" "css_element"

    When I set the field "Assign another role" to "Course creator (0)"
    And I set the field "Potential users" to "Bob2 Learner2 (learner2@example.com)"
    And I press "Add"
    And I set the field "Potential users" to "Bob3 Learner3 (learner3@example.com)"
    And I press "Add"
    Then I should see "Bob2 Learner2 (learner2@example.com)" in the "#removeselect" "css_element"
    And I should see "Bob3 Learner3 (learner3@example.com)" in the "#removeselect" "css_element"

    When I set the field "Assign another role" to "Staff Manager (0)"
    And I set the field "Potential users" to "Bob3 Learner3 (learner3@example.com)"
    And I press "Add"
    And I set the field "Potential users" to "Bob4 Learner4 (learner4@example.com)"
    And I press "Add"
    Then I should see "Bob3 Learner3 (learner3@example.com)" in the "#removeselect" "css_element"
    And I should see "Bob4 Learner4 (learner4@example.com)" in the "#removeselect" "css_element"

    # Check the role have been created as required.
    When I navigate to "Manage reports" node in "Site administration > Reports > Report builder"
    And I follow "My User Report"
    And I follow "View This Report"
    Then I should see "My User Report: 6 records shown"
    And I should see "Site Manager" in the "Bob1 Learner1" "table_row"
    And I should see "Course creator" in the "Bob2 Learner2" "table_row"
    And I should see "Course creator, Staff Manager" in the "Bob3 Learner3" "table_row"
    And I should see "Staff Manager" in the "Bob4 Learner4" "table_row"

  Scenario: Verify User System User filter with no role selected returns no result.

    Given I click on "Assigned" "radio"
    When I press "id_submitgroupstandard_addfilter"
    Then I should see "Guest user" in the "guest" "table_row"
    And I should see "Admin User" in the "admin" "table_row"
    And I should see "Site Manager" in the "Bob1 Learner1" "table_row"
    And I should see "Course creator" in the "Bob2 Learner2" "table_row"
    And I should see "Course creator, Staff Manager" in the "Bob3 Learner3" "table_row"
    And I should see "Staff Manager" in the "Bob4 Learner4" "table_row"

    When I click on "Not assigned" "radio"
    And I press "id_submitgroupstandard_addfilter"
    Then I should see "My User Report: 6 records shown"
    And I should see "Guest user" in the "guest" "table_row"
    And I should see "Admin User" in the "admin" "table_row"
    And I should see "Site Manager" in the "Bob1 Learner1" "table_row"
    And I should see "Course creator" in the "Bob2 Learner2" "table_row"
    And I should see "Course creator, Staff Manager" in the "Bob3 Learner3" "table_row"
    And I should see "Staff Manager" in the "Bob4 Learner4" "table_row"

    When I press "Save this search"
    Then I should see "Create a saved search"
    And I should see "No role selected"

    When I set the field "Search Name" to "No role selected"
    And I press "Save changes"
    Then I should see "My User Report: 6 records shown"
    And I should see "No role selected" in the "sid" "select"

  Scenario: Verify User System User filter with 'any role' option selected.

    Given I click on "Assigned" "radio"
    When I set the field "user-roleid" to "Any role"
    And I press "id_submitgroupstandard_addfilter"
    Then I should see "My User Report: 4 of 6 records shown"
    And I should see "Site Manager" in the "Bob1 Learner1" "table_row"
    And I should see "Course creator" in the "Bob2 Learner2" "table_row"
    And I should see "Course creator, Staff Manager" in the "Bob3 Learner3" "table_row"
    And I should see "Staff Manager" in the "Bob4 Learner4" "table_row"

    When I press "Save this search"
    Then I should see "Create a saved search"
    And I should see "Assigned any role"

    When I set the field "Search Name" to "Assigned any role"
    And I press "Save changes"
    Then I should see "My User Report: 4 of 6 records shown"
    And I should see "Assigned any role" in the "sid" "select"

    When I click on "Not assigned" "radio"
    And I set the field "user-roleid" to "Any role"
    And I press "id_submitgroupstandard_addfilter"
    Then I should see "My User Report: 2 of 6 records shown"
    And I should see "Guest user" in the "guest" "table_row"
    And I should see "Admin User" in the "admin" "table_row"

    When I press "Save this search"
    Then I should see "Create a saved search"
    And I should see "Not assigned any role"

    When I set the field "Search Name" to "Not assigned any role"
    And I press "Save changes"
    Then I should see "My User Report: 2 of 6 records shown"
    And I should see "Not assigned any role" in the "sid" "select"

  Scenario: Verify User System Role filter with 'assigned' role works.

    # Check the Site Manager search result.
    Given I click on "Assigned" "radio"
    When I set the field "user-roleid" to "Site Manager"
    And I press "id_submitgroupstandard_addfilter"
    Then I should see "My User Report: 1 of 6 records shown"
    And I should see "Site Manager" in the "Bob1 Learner1" "table_row"
    And I should not see "Course creator" in the "#report_my_user_report" "css_element"
    And I should not see "Staff Manager" in the "#report_my_user_report" "css_element"

    # Check the Course Creator search result.
    When I set the field "user-roleid" to "Course creator"
    And I press "id_submitgroupstandard_addfilter"
    Then I should see "My User Report: 2 of 6 records shown"
    And I should see "Course creator" in the "Bob2 Learner2" "table_row"
    And I should see "Course creator, Staff Manager" in the "Bob3 Learner3" "table_row"
    And I should not see "Site Manager" in the "#report_my_user_report" "css_element"
    # Check against the user for this case because it's more accurate.
    # This will be a user with only Staff Manager and no other role.
    And I should not see "Bob4 Learner4" in the "#report_my_user_report" "css_element"

    # Check the Staff Manager search result.
    When I set the field "user-roleid" to "Staff Manager"
    And I press "id_submitgroupstandard_addfilter"
    Then I should see "My User Report: 2 of 6 records shown"
    And I should see "Course creator, Staff Manager" in the "Bob3 Learner3" "table_row"
    And I should see "Staff Manager" in the "Bob4 Learner4" "table_row"
    And I should not see "Site Manager" in the "#report_my_user_report" "css_element"

    When I press "Save this search"
    Then I should see "Create a saved search"
    And I should see "Assigned role 'Staff Manager'"

    When I set the field "Search Name" to "Assigned role 'Staff Manager'"
    And I press "Save changes"
    Then I should see "My User Report: 2 of 6 records shown"
    And I should see "Assigned role 'Staff Manager'" in the "sid" "select"

  Scenario: Verify User System Role filter with 'not assigned' a role works.

    # Check the Site Manager search result.
    Given I click on "Not assigned" "radio"
    When I set the field "user-roleid" to "Site Manager"
    And I press "id_submitgroupstandard_addfilter"
    Then I should see "My User Report: 5 of 6 records shown"
    And I should see "Guest user" in the "guest" "table_row"
    And I should see "Admin User" in the "admin" "table_row"
    And I should see "Course creator" in the "Bob2 Learner2" "table_row"
    And I should see "Course creator, Staff Manager" in the "Bob3 Learner3" "table_row"
    And I should see "Staff Manager" in the "Bob4 Learner4" "table_row"
    And I should not see "Site Manager" in the "#report_my_user_report" "css_element"

    # Check the Course Creator search result.
    When I set the field "user-roleid" to "Course creator"
    And I press "id_submitgroupstandard_addfilter"
    Then I should see "My User Report: 4 of 6 records shown"
    And I should see "Guest user" in the "guest" "table_row"
    And I should see "Admin User" in the "admin" "table_row"
    And I should see "Site Manager" in the "Bob1 Learner1" "table_row"
    And I should see "Staff Manager" in the "Bob4 Learner4" "table_row"
    And I should not see "Course creator" in the "#report_my_user_report" "css_element"

    # Check the Staff Manager search result.
    When I set the field "user-roleid" to "Staff Manager"
    And I press "id_submitgroupstandard_addfilter"
    Then I should see "My User Report: 4 of 6 records shown"
    And I should see "Guest user" in the "guest" "table_row"
    And I should see "Admin User" in the "admin" "table_row"
    And I should see "Site Manager" in the "Bob1 Learner1" "table_row"
    And I should see "Course creator" in the "Bob2 Learner2" "table_row"
    And I should not see "Staff Manager" in the "#report_my_user_report" "css_element"

    When I press "Save this search"
    Then I should see "Create a saved search"
    And I should see "Not assigned role 'Staff Manager'"

    When I set the field "Search Name" to "Not assigned role 'Staff Manager'"
    And I press "Save changes"
    Then I should see "My User Report: 4 of 6 records shown"
    And I should see "Not assigned role 'Staff Manager'" in the "sid" "select"
