@totara @totara_hierarchy @javascript @totara_customfield
Feature: Verify I can see all appropriate fields in the goal custom fields report source.

Background:
  Given I am on a totara site
  And the following "users" exist:
    | username | firstname | lastname | email                |
    | learner1 | Bob1      | learner1 | learner1@example.com |
  And the following "goal" frameworks exist:
    | fullname                 | idnumber |
    | Company Goal Framework 1 | CGF1     |
  And the following "goal" hierarchy exists:
    | framework | fullname       | idnumber | description                              |
    | CGF1      | Company Goal 1 | CG1      | <p>Precise and accurate description!</p> |

  # Add a couple of goals to the admin user.
  And I log in as "admin"
  And I click on "Goals" in the totara menu

  # Add a company goal.
  And I press "Add company goal"
  And I follow "Company Goal 1"
  And I press "Save"

  # Add a personal goal.
  And I press "Add personal goal"
  And I set the following fields to these values:
    | Name        | Personal Goal 1                           |
    | Description | Woolly and imprecise description |
  And I press "Save changes"
  And I log out

Scenario: Verify the basic goal fields can be see in the Goal Custom Fields report.
  Given I log in as "admin"
  And I navigate to "Manage reports" node in "Site administration > Reports > Report builder"
  And I set the following fields to these values:
    | Report Name | Goal Custom Fields report |
    | Source      | Goal Custom Fields        |
  And I press "Create report"

  # Add the description column to the report.
  And I follow "Columns"
  And I set the field "newcolumns" to "Goal Description"
  And I press "Add"
  And I press "Save changes"

  # Add the description filter to the report.
  And I follow "Filters"
  And I set the field "newstandardfilter" to "Goal Description"
  And I press "Add"
  And I press "Save changes"

  # View and check the report contains the right data.
  When I follow "View This Report"
  Then I should see "Goal Custom Fields report: 2 records shown"
  And the following should exist in the "report_goal_custom_fields_report" table:
    | User's Fullname | Goal Name       | Personal or Company  | Goal Type | Goal Description                 |
    | Admin User      | Company Goal 1  | Company              | No Type   | Precise and accurate description |
    | Admin User      | Personal Goal 1 | Personal             | No Type   | Woolly and imprecise description |
