@totara @perform @mod_perform @javascript @vuejs
Feature: Print view of a multi-section user activity

  Background:
    Given the following "users" exist:
      | username          | firstname         | lastname | email                   |
      | john              | John              | One      | john.one@example.com    |
      | manager           | Manager           | Two      | manager.two@example.com |
    And the following job assignments exist:
      | user  | manager |
      | john  | manager |
    And the following "activities" exist in "mod_perform" plugin:
      | activity_name          | activity_type | create_section | create_track | activity_status | anonymous_responses |
      | Multi section activity | appraisal     | false          | false        | Active          | false               |
    And the following "activity settings" exist in "mod_perform" plugin:
      | activity_name          | close_on_completion | multisection |
      | Multi section activity | yes                 | yes          |
    And the following "activity sections" exist in "mod_perform" plugin:
      | activity_name          | section_name |
      | Multi section activity | Section 1    |
      | Multi section activity | Section 2    |
      | Multi section activity | Section 3    |
    And the following "cohorts" exist:
      | name | idnumber | description | contextlevel | reference | cohorttype |
      | aud1 | aud1     | Audience 1  | System       | 0         | 1          |
    And the following "cohort members" exist:
      | user  | cohort |
      | john  | aud1   |
    And the following "activity tracks" exist in "mod_perform" plugin:
      | activity_name          | track_description |
      | Multi section activity | track 1           |
    And the following "track assignments" exist in "mod_perform" plugin:
      | track_description | assignment_type | assignment_name |
      | track 1           | cohort          | aud1            |
    And the following "section elements" exist in "mod_perform" plugin:
      | section_name | element_name | title      |
      | Section 1    | short_text   | Question 1 |
      | Section 2    | short_text   | Question 2 |
      | Section 3    | short_text   | Question 3 |
    Given the following "section relationships" exist in "mod_perform" plugin:
      | section_name | relationship | can_view | can_answer |
      | Section 1    | subject      | yes      | yes        |
      | Section 1    | manager      | yes      | no         |
      | Section 2    | subject      | yes      | yes        |
      | Section 3    | subject      | yes      | yes        |
      | Section 3    | manager      | no       | yes        |
    And I run the scheduled task "mod_perform\task\expand_assignments_task"
    And I run the scheduled task "mod_perform\task\create_subject_instance_task"

  Scenario: Print view for multiple section
    # Add responses as the subject.
    When I log in as "john"
    And I navigate to the outstanding perform activities list page
    And I click on "Multi section activity" "link"
    And I wait until ".tui-performElementResponse .tui-formField" "css_element" exists
    And I answer "short text" question "Question 1" with "John answer 1"
    And I click on "Submit" "button"
    And I confirm the tui confirmation modal
    And I answer "short text" question "Question 2" with "John answer 2"
    And I click on "Submit" "button"
    And I confirm the tui confirmation modal
    And I answer "short text" question "Question 3" with "John answer 3"
    And I click on "Submit" "button"
    And I confirm the tui confirmation modal
    Then I should see "Section submitted and closed." in the tui success notification toast

    # Check print view as subject user
    When I navigate to the "print" user activity page for performance activity "Multi section activity" where "john" is the subject and "john" is the participant
    Then I should see perform print activity relationship to user "Self"
    And I should see "Section 1" in perform activity print section "1"
    And I should see "Question 1" in perform activity print section "1"
    And I should see "Your response" in perform activity print section "1"
    And I should see "John answer 1" in perform activity print section "1"
    And I should not see "Manager Two" in perform activity print section "1"
    And I should see "Section 2" in perform activity print section "2"
    And I should see "Question 2" in perform activity print section "2"
    And I should see "Your response" in perform activity print section "2"
    And I should see "John answer 2" in perform activity print section "2"
    And I should not see "Manager Two" in perform activity print section "2"
    And I should see "Section 3" in perform activity print section "3"
    And I should see "Question 3" in perform activity print section "3"
    And I should see "Your response" in perform activity print section "3"
    And I should see "John answer 3" in perform activity print section "3"
    And I should see "Manager response" in perform activity print section "3"
    And I should see "Manager Two" in perform activity print section "3"
    And I should see "No response submitted" in perform activity print section "3"

    When I am on homepage
    And I log out
    And I log in as "manager"
    # Respond as manager
    And I navigate to the outstanding perform activities list page
    And I click on "Activities about others" "link"
    And I click on "Multi section activity" "link"
    And I click on "Next section" "button"
    And I wait until ".tui-performElementResponse .tui-formField" "css_element" exists
    And I answer "short text" question "Question 3" with "Manager answer 3"
    And I click on "Submit" "button"
    And I confirm the tui confirmation modal
    Then I should see "Section submitted and closed." in the tui success notification toast

    # Check print view as manager
    When I navigate to the "print" user activity page for performance activity "Multi section activity" where "john" is the subject and "manager" is the participant
    Then I should see perform print activity relationship to user "Manager"
    # Section 1 is view only for manager
    And I should see "Section 1" in perform activity print section "1"
    And I should see "Question 1" in perform activity print section "1"
    And I should see "Subject response" in perform activity print section "1"
    And I should see "John One" in perform activity print section "1"
    And I should see "John answer 1" in perform activity print section "1"
    And I should not see "Your response" in perform activity print section "1"
    # Section 2 should not be displayed because manager is not a participant.
    # Section 3 is respond only for manager so they can only view their own response.
    And I should see "Section 3" in perform activity print section "2"
    And I should see "Question 3" in perform activity print section "2"
    And I should not see "Subject response" in perform activity print section "2"
    And I should see "Your response" in perform activity print section "2"
    And I should see "Manager answer 3" in perform activity print section "2"
