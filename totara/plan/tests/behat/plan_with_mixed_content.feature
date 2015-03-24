@totara @totara_plan
Feature: Learner creates learning plan with mixed content

  Background:
    Given I am on a totara site
    And the following "users" exist:
      | username | firstname  | lastname  | email                |
      | learner1 | firstname1 | lastname1 | learner1@example.com |
      | manager2 | firstname2 | lastname2 | manager2@example.com |
    And the following "position" frameworks exist:
      | fullname             | idnumber |
      | Position Framework 1 | PF1      |
    And the following "position" hierarchy exists:
      | framework | idnumber | fullname   |
      | PF1       | P1       | Position 1 |
    And the following position assignments exist:
      | user     | position | manager  |
      | learner1 | P1       | manager2 |
    And the following "courses" exist:
      | fullname | shortname | enablecompletion |
      | Course 1 | Course 1  | 1                |
      | Course 2 | Course 2  | 1                |
      | Course 3 | Course 3  | 1                |
    And the following "competency" frameworks exist:
      | fullname               | idnumber | description           |
      | Competency Framework 1 | CF1      | Framework description |
    And the following "competency" hierarchy exists:
      | framework | fullname     | idnumber | description            |
      | CF1       | Competency 1 | C1       | Competency description |
      | CF1       | Competency 2 | C2       | Competency description |
      | CF1       | Competency 3 | C3       | Competency description |
    And the following "programs" exist in "totara_program" plugin:
      | fullname  | shortname |
      | Program 1 | P1   |
      | Program 2 | P2   |
      | Program 3 | P3   |

    And I create a basic learning plan called "learner1 Learning Plan" for "learner1"

  @javascript
  Scenario: Test the learner can add content to their learning plan prior to approval.

    # Login as the learner and navigate to the learning plan.
    Given I log in as "learner1"
    And I focus on "My Learning" "link"
    And I follow "Learning Plans"
    And I click on "learner1 Learning Plan" "link"

    # Add some courses to the plan.
    And I click on "Courses" "link" in the "#dp-plan-content" "css_element"
    And I click on "Add courses" "button"
    And I click on "Miscellaneous" "link"
    And I click on "Course 1" "link"
    And I click on "Course 2" "link"
    And I click on "Course 3" "link"

    # Check the selected courses appear in the plan.
    When I click on "Save" "button" in the "Add courses" "totaradialogue"
    Then I should see "Course 1" in the "#dp-component-update-table" "css_element"
    And I should see "Course 2" in the "#dp-component-update-table" "css_element"
    And I should see "Course 3" in the "#dp-component-update-table" "css_element"

    # Add some competencies to the plan.
    And I click on "Competencies" "link" in the "#dp-plan-content" "css_element"
    And I press "Add competencies"
    And I click on "Competency 1" "link"
    And I click on "Competency 2" "link"
    And I click on "Competency 3" "link"

    # Check the selected competency appear in the plan.
    When I click on "Continue" "button" in the "Add competencies" "totaradialogue"
    Then I should see "Competency 1" in the ".dp-plan-component-items" "css_element"
    And I should see "Competency 2" in the ".dp-plan-component-items" "css_element"
    And I should see "Competency 3" in the ".dp-plan-component-items" "css_element"

      # Add some objectives to the plan.
    And I click on "Objectives" "link" in the "#dp-plan-content" "css_element"

    # Create a new objective.Scenario:
    And I create an objective called "Objective 1"
    And I create an objective called "Objective 2"
    When I create an objective called "Objective 3"

    # Check the objective names appear in the plan.
    Then I should see "Objective 1" in the ".dp-plan-component-items" "css_element"
    And I should see "Objective 2" in the ".dp-plan-component-items" "css_element"
    And I should see "Objective 3" in the ".dp-plan-component-items" "css_element"

    # Add some programs to the plan.
    And I click on "Programs" "link" in the "#dp-plan-content" "css_element"
    And I press "Add programs"
    And I click on "Miscellaneous" "link"
    And I click on "Program 1" "link"
    And I click on "Program 2" "link"
    And I click on "Program 3" "link"

    # Check the selected competency appear in the plan.
    When I click on "Save" "button" in the "Add programs" "totaradialogue"
    Then I should see "Program 1" in the ".dp-plan-component-items" "css_element"
    And I should see "Program 2" in the ".dp-plan-component-items" "css_element"
    And I should see "Program 3" in the ".dp-plan-component-items" "css_element"

    # Send the plan to the manager for approval.
    When I press "Send approval request"
    Then I should see "Approval request sent for plan \"learner1 Learning Plan\""
    And I should see "This plan has not yet been approved (Approval Requested)"
    And I log out

    # As the manager, access the learners plans.
    When I log in as "manager2"
    And I click on "My Team" in the totara menu
    And I click on "Plans" "link" in the "#team_members_r0" "css_element"

    # Access the learners plans and verify it hasn't been approved.
    And I click on "learner1 Learning Plan" "link"
    Then I should see "You are viewing firstname1 lastname1's plan"
    And I should see "This plan has not yet been approved"

    # Approve the plan.
    When I set the field "reasonfordecision" to "Nice plan!"
    And I press "Approve"
    Then I should see "You are viewing firstname1 lastname1's plan"
    And I should see "Plan \"learner1 Learning Plan\" has been approved"

  @javascript
  Scenario: Test the learner can iteratively add content to their learning and request approval.

    # Login as the learner and navigate to the learning plan.
    Given I log in as "learner1"
    And I focus on "My Learning" "link"
    And I follow "Learning Plans"
    And I click on "learner1 Learning Plan" "link"

    # Send the plan to the manager for approval.
    When I press "Send approval request"
    Then I should see "Approval request sent for plan \"learner1 Learning Plan\""
    And I should see "This plan has not yet been approved (Approval Requested)"
    And I log out

    # As the manager, access the learners plans.
    When I log in as "manager2"
    And I click on "My Team" in the totara menu
    And I click on "Plans" "link" in the "#team_members_r0" "css_element"

    # Access the learners plans and verify it hasn't been approved.
    When I click on "learner1 Learning Plan" "link"
    Then I should see "You are viewing firstname1 lastname1's plan"
    And I should see "This plan has not yet been approved"

    # Approve the plan.
    When I set the field "reasonfordecision" to "It's a start. Please add some content!"
    And I press "Approve"
    Then I should see "You are viewing firstname1 lastname1's plan"
    And I should see "Plan \"learner1 Learning Plan\" has been approved"
    And I log out

    When I log in as "learner1"
    And I focus on "My Learning" "link"
    And I follow "Learning Plans"
    And I click on "learner1 Learning Plan" "link"

    # Add some courses to the plan.
    And I click on "Courses" "link" in the "#dp-plan-content" "css_element"
    And I click on "Add courses" "button"
    And I click on "Miscellaneous" "link"
    And I click on "Course 1" "link"
    And I click on "Course 2" "link"

    # Check the selected courses appear in the plan.
    And I click on "Save" "button" in the "Add courses" "totaradialogue"
    Then I should see "Course 1" in the "#dp-component-update-table" "css_element"
    And I should see "Course 2" in the "#dp-component-update-table" "css_element"

    # Add some objectives to the plan.
    When I click on "Objectives" "link" in the "#dp-plan-content" "css_element"

    # Create a new objective.
    And I create an objective called "Objective 1"

    # Check the objective names appear in the plan.
    Then I should see "Objective 1" in the ".dp-plan-component-items" "css_element"

    # Send the plan to the manager for approval.
    When I press "Send approval request"
    Then I should see "Approval request sent for plan \"learner1 Learning Plan\""
    And I should see "This plan contains the following pending items:" in the ".plan_box" "css_element"
    And I should see "2 Courses" in the ".plan_box" "css_element"
    And I should see "1 Objective" in the ".plan_box" "css_element"
    And I log out

    # As the manager, access the learners plans.
    When I log in as "manager2"
    And I click on "My Team" in the totara menu
    And I click on "Plans" "link" in the "#team_members_r0" "css_element"

    # Access the learners plans and verify it hasn't been approved.
    When I click on "learner1 Learning Plan" "link"
    Then I should see "You are viewing firstname1 lastname1's plan"
    And I should see "This plan contains new items that require your approval"

    # Review and approve the pending items.
    When I press "Review"
    And I set the field "menuapprove_course1" to "Approve"
    And I set the field "menuapprove_course2" to "Approve"
    And I set the field "menuapprove_objective1" to "Approve"
    And I press "Update Settings"
    # Currently there is no response, but I would expect something like the following. Bug raised: T-14320.
    #     Then I should see "Plan \"learner1 Learning Plan\" has been updated"
    And I log out

    When I log in as "learner1"
    And I focus on "My Learning" "link"
    And I follow "Learning Plans"
    And I click on "learner1 Learning Plan" "link"

    # Add a programs to the plan before the manager approves it.
    And I click on "Programs" "link" in the "#dp-plan-content" "css_element"
    And I press "Add programs"
    And I click on "Miscellaneous" "link"
    And I click on "Program 1" "link"

    # Check the selected competency appear in the plan.
    When I click on "Save" "button" in the "Add programs" "totaradialogue"
    Then I should see "Program 1" in the ".dp-plan-component-items" "css_element"
    And I should see "This plan has a draft item:" in the ".plan_box" "css_element"
    And I should see "1 Program" in the ".plan_box" "css_element"

    When I press "Send approval request"
    Then I should see "Approval request sent for plan \"learner1 Learning Plan\""
    And I log out

    # As the manager, access the learners plans.
    When I log in as "manager2"
    And I click on "My Team" in the totara menu
    And I click on "Plans" "link" in the "#team_members_r0" "css_element"

    # Access the learners plans and verify it hasn't been approved.
    When I click on "learner1 Learning Plan" "link"
    Then I should see "You are viewing firstname1 lastname1's plan"
    And I should see "This plan contains a new item that requires your approval"

    When I press "Review"
    And I set the field "menuapprove_program1" to "Approve"
    And I press "Update Settings"
    # Currently there is no response, but I would expect something like the following. Bug raised: T-14320.
    #     Then I should see "Plan \"learner1 Learning Plan\" has been updated"
    And I log out
