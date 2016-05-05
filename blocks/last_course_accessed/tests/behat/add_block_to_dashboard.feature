@totara @block_last_course_accessed @javascript
Feature: User can add and remove block to / from dashboard.

  Background:
    Given I am on a totara site
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | learner1 | Bob1      | Learner1 | learner1@example.com |
    And the following "cohorts" exist:
      | name       | idnumber | description            | contextlevel | reference |
      | Audience 1 | A1       | Audience 1 description | System       | 0         |

    # Allow the homepage to use a dashboard.
    When I log in as "admin"
    And I navigate to "Navigation" node in "Site administration > Appearance"
    And I set the field "Default home page for users" to "Totara dashboard"
    And I press "Save changes"
    Then I should see "Changes saved"

    # Set up the dashboard.
    When I navigate to "Dashboards" node in "Site administration > Appearance"
    And I press "Create dashboard"
    And I set the field "Name" to "My Dashboard"
    And I set the field "Published" to "1"
    And I press "Assign new audiences"
    And I follow "Audience 1"
    And I press "OK"
    And I press "Create dashboard"
    Then I should see "Dashboard saved"

    # Create an audience that we can allocate to the dashboard.
    When I navigate to "Audiences" node in "Site administration > Users > Accounts"
    And I follow "Audience 1"
    And I follow "Edit members"
    And I set the field "Potential users" to "Admin User (moodle@example.com)"
    And I press "Add"
    And I set the field "Potential users" to "Bob1 Learner1 (learner1@example.com)"
    And I press "Add"
    And I follow "Members"
    Then I should see "Admin User"
    Then I should see "Bob1 Learner1"
    And I log out

  Scenario: Verify the Site Administrator can add and remove the LCA block to / from a dashboard.
    Given I log in as "admin"

    # Add the block and check it's removed from the available blocks list.
    When I press "Customize dashboard"
    And I set the field "Add a block" to "Last Course Accessed"
    Then I should not see "Last Course Accessed" in the "Add a block" "select"
    And I should see "Last Course Accessed" in the "Last Course Accessed" "block"

    # Remove the block and check it's added back to the list of available blocks.
    When I click on "Actions" "link" in the "Last Course Accessed" "block"
    And I follow "Delete Last Course Accessed block"
    Then I should see "Are you sure that you want to delete this block titled Last Course Accessed?"
    When I press "Yes"
    Then I should see "Last Course Accessed" in the "Add a block" "select"
    And I should not see "Last Course Accessed" in the "aside" "css_element"

    And I log out

  Scenario: Verify a learner can add and remove the LCA block to / from a dashboard.
    Given I log in as "learner1"

    # Add the block and check it's removed from the available blocks list.
    When I press "Customize dashboard"
    And I set the field "Add a block" to "Last Course Accessed"
    Then I should not see "Last Course Accessed" in the "Add a block" "select"
    And I should see "Last Course Accessed" in the "Last Course Accessed" "block"

    # Remove the block and check it's added back to the list of available blocks.
    When I click on "Actions" "link" in the "Last Course Accessed" "block"
    And I follow "Delete Last Course Accessed block"
    Then I should see "Are you sure that you want to delete this block titled Last Course Accessed?"
    When I press "Yes"
    Then I should see "Last Course Accessed" in the "Add a block" "select"
    And I should not see "Last Course Accessed" in the "aside" "css_element"

    And I log out
