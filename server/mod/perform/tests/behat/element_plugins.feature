@totara @perform @mod_perform
Feature: I can create and user performance activities with any and every element type

  Background:
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
      | activity_name        | activity_type | activity_status |
      | One of every element | check-in      | Draft           |
    And the following "activity tracks" exist in "mod_perform" plugin:
      | activity_name        | track_description |
      | One of every element | track 1           |
    When I log in as "admin"

  @javascript @vuejs
  Scenario Outline: Build then save empty responses for every element type
    When I navigate to the manage perform activities page
    Then I should see the tui datatable contains:
      | Name                 | Type     | Status |
      | One of every element | Check-in | Draft  |

    When I click on "One of every element" "link"
    And I click the add responding participant button
    And I select "Subject" in the responding participants popover
    And I click on "Edit content elements" "link_or_button"
    And <builder_step>
    And I close the tui modal

    And I click on "Assignments" "link"
    And I click on "Add group" "button"
    And I click on "Audience" "link" in the ".tui-dropdown__menu" "css_element"
    And I toggle the adder picker entry with "aud1" for "Audience name"
    And I save my selections and close the adder

    And I click on "Activate" "button"
    And I confirm the tui confirmation modal
    And I run the scheduled task "mod_perform\task\expand_assignments_task"
    And I run the scheduled task "mod_perform\task\create_subject_instance_task"
    And I run the scheduled task "mod_perform\task\create_manual_participant_progress_task"
    And I log out

    And I log in as "user1"
    And I navigate to the outstanding perform activities list page
    Then I should see the tui datatable contains:
      | Activity title       | Type     | Overall progress | Your progress   |
      | One of every element | Check-in | Not yet started  | Not yet started |

    When I click on "One of every element" "link"
    And I click on "<save_button>" "button"
    And <validation_assertion_step>
    And <fix_validation_step>
    And I click on "<save_button>" "button"
    And <confirm_step>
    Then I should see "<toast_assertion>" in the tui success notification toast

    Examples:
      | builder_step                                                                           | save_button   | validation_assertion_step                                               | fix_validation_step                          | confirm_step                         | toast_assertion    |
      | I add one of every element type in the mod perform form builder and make them required | Save as draft | I should see "Numeric rating scale" has no validation errors            | I should see ""                              | I should see ""                      | Draft saved        |
      | I add one of every element type in the mod perform form builder                        | Submit        | I should see "Numeric rating scale" has the validation error "Required" | I click on ".tui-range__input" "css_element" | I confirm the tui confirmation modal | Section submitted. |
