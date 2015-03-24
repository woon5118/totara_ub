@totara @totara_plan
Feature: Learner creates learning plan with objectives

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

  And I create a basic learning plan called "learner1 Learning Plan" for "learner1"

@javascript
Scenario: Test the learner can add and remove objectives from their learning plan prior to approval.

  # Login as the learner and navigate to the learning plan.
  Given I log in as "learner1"
  And I focus on "My Learning" "link"
  And I follow "Learning Plans"
  And I click on "learner1 Learning Plan" "link"

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

  # Delete a competency to make sure it's removed properly.
  When I click on "Delete" "link" in the "#objectivelist_r2_c6" "css_element"
  Then I should see "Are you sure you want to delete this objective?"
  When I press "Continue"
  Then I should not see "Objective 3" in the "#dp-component-update-table" "css_element"

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
  When I set the field "reasonfordecision" to "Nice plan!"
  And I press "Approve"
  Then I should see "You are viewing firstname1 lastname1's plan"
  And I should see "Plan \"learner1 Learning Plan\" has been approved"
