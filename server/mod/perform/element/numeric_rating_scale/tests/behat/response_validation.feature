@totara @perform @mod_perform @perform_element @javascript @vuejs
Feature: Numeric rating scale response validation

  Scenario: Numeric rating scale shows validation error when no response is submitted, but can save a draft
    Given the following "users" exist:
      | username | firstname | lastname | email             |
      | user1    | user      | 1        | user1@example.com |
    And the following "cohorts" exist:
      | name | idnumber |
      | aud1 | aud1     |
    And the following "cohort members" exist:
      | user  | cohort |
      | user1 | aud1   |
    And the following "activities" exist in "mod_perform" plugin:
      | activity_name | activity_type | activity_status |
      | Activity One  | check-in      | Draft           |
    And the following "activity tracks" exist in "mod_perform" plugin:
      | activity_name | track_description |
      | Activity One  | track 1           |
    When I log in as "admin"
    And I navigate to the manage perform activities page
    And I click on "Activity One" "link"
    And I click on "Assignments" "link"
    And I click on "Add group" "button"
    And I click on "Audience" "link" in the ".tui-dropdown__menu" "css_element"
    And I toggle the adder picker entry with "aud1" for "Audience name"
    And I save my selections and close the adder
    And I reload the page
    And I click the add responding participant button
    And I select "Subject" in the responding participants popover
    And I click on "Edit content elements" "link_or_button"

    And I add a "Numeric rating scale" activity content element
    And I set the following fields to these values:
      | rawTitle     | Numeric rating scale |
      | lowValue     | 1                    |
      | defaultValue | 2                    |
      | highValue    | 3                    |
    And I click on the "responseRequired" tui checkbox
    And I save the activity content element
    And I follow "Content (Activity One)"

    And I click on "Activate" "button"
    And I confirm the tui confirmation modal
    And I run the scheduled task "mod_perform\task\expand_assignments_task"
    And I run the scheduled task "mod_perform\task\create_subject_instance_task"
    And I run the scheduled task "mod_perform\task\create_manual_participant_progress_task"
    And I log out
    And I log in as "user1"
    And I navigate to the outstanding perform activities list page
    And I click on "Activity One" "link"

    # Can save as draft without doing anything
    When I click on "Save as draft" "button"
    And I should see "Numeric rating scale" has no validation errors
    And I should see "Draft saved" in the tui success notification toast

    # Must actually make a selection in order to submit
    When I click on "Submit" "button"
    Then I should see "Numeric rating scale" has the validation error "Required"
    When I click on ".tui-range__input" "css_element"
    And I click on "Submit" "button"
    And I confirm the tui confirmation modal
    And I should see "Section submitted" in the tui success notification toast
