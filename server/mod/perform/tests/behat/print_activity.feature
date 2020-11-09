@totara @perform @mod_perform @javascript @vuejs
Feature: Print view of a single-section user activity

  Background:
    Given the following "users" exist:
      | username          | firstname        | lastname | email                               |
      | john              | John             | One      | john.one@example.com                |
      | david             | David            | Two      | david.two@example.com               |
      | manager-appraiser | Managerappraiser | Three    | manager-appraiser.three@example.com |
      | appraiser         | Appraiser        | Four     | appraiser.four@example.com          |
    And the following job assignments exist:
      | user  | manager           | appraiser         |
      | john  | manager-appraiser | manager-appraiser |
      | david | john              | appraiser         |
    And the following "activities" exist in "mod_perform" plugin:
      | activity_name           | activity_type | create_section | create_track | activity_status | anonymous_responses |
      | Single section activity | appraisal     | false          | false        | Active          | false               |
    And the following "activity settings" exist in "mod_perform" plugin:
      | activity_name           | close_on_completion | multisection |
      | Single section activity | yes                 | no           |
    And the following "activity sections" exist in "mod_perform" plugin:
      | activity_name           | section_name   |
      | Single section activity | Single section |
    And the following "cohorts" exist:
      | name | idnumber | description | contextlevel | reference | cohorttype |
      | aud1 | aud1     | Audience 1  | System       | 0         | 1          |
    And the following "cohort members" exist:
      | user  | cohort |
      | john  | aud1   |
      | david | aud1   |
    And the following "activity tracks" exist in "mod_perform" plugin:
      | activity_name           | track_description |
      | Single section activity | track 1           |
    And the following "track assignments" exist in "mod_perform" plugin:
      | track_description | assignment_type | assignment_name |
      | track 1           | cohort          | aud1            |
    And the following "section elements" exist in "mod_perform" plugin:
      | section_name   | element_name | title      |
      | Single section | short_text   | Question 1 |
    Given the following "section relationships" exist in "mod_perform" plugin:
      | section_name   | relationship | can_view | can_answer |
      | Single section | subject      | yes      | yes        |
      | Single section | manager      | no       | yes        |
      | Single section | appraiser    | yes      | no         |
    And I run the scheduled task "mod_perform\task\expand_assignments_task"
    And I run the scheduled task "mod_perform\task\create_subject_instance_task"

  Scenario: Print view for single section with user having single relationships
    # Check without any response.
    When I log in as "david"
    And I navigate to the "print" user activity page for performance activity "Single section activity" where "david" is the subject and "david" is the participant
    Then "Print" "button" should be visible
    And I should see perform activity relationship to user "yourself"
    And I should see "Appraisal"
    And I should see "Single section activity"
    And I should see "Question 1"
    And I should see "Your response"
    # Empty form field should be displayed for logged in user when no response has been given yet.
    And ".tui-formField" "css_element" should exist in the ".tui-performElementResponse" "css_element"
    And I should see "Manager response"
    And I should see "John One"
    And I should see "No response submitted"
    And I should not see "Appraiser response"
    # Add a response as the subject.
    When I navigate to the outstanding perform activities list page
    And I click on "Single section activity" "link"
    And I wait until ".tui-performElementResponse .tui-formField" "css_element" exists
    And I answer "short text" question "Question 1" with "David answer one"
    When I click on "Save as draft" "button"
    Then I should see "Draft saved" in the tui success notification toast

    When I click on "Cancel" "button"
    And I navigate to the "print" user activity page for performance activity "Single section activity" where "david" is the subject and "david" is the participant
    Then the field with xpath "//*[@name='sectionElements[1][response]']" matches value "David answer one"

    When I navigate to the outstanding perform activities list page
    And I click on "Single section activity" "link"
    And I click on "Submit" "button"
    And I confirm the tui confirmation modal
    And I should see "Section submitted and closed." in the tui success notification toast
    And I navigate to the "print" user activity page for performance activity "Single section activity" where "david" is the subject and "david" is the participant
    # No form field should be displayed any more.
    Then ".tui-formField" "css_element" should not exist in the ".tui-participantContentPrint" "css_element"
    # Instead response version of the question element should be shown.
    And ".tui-participantFormResponseDisplay" "css_element" should exist in the ".tui-participantContentPrint" "css_element"

    And I should see "David answer one"
    And I should see "Manager response"
    And I should see "John One"
    And I should see "No response submitted"

    # Check manager's view (response only, can't see other's responses)
    When I am on homepage
    And I log out
    And I log in as "john"
    And I navigate to the "print" user activity page for performance activity "Single section activity" where "david" is the subject and "john" is the participant
    Then I should see perform activity relationship to user "Manager"
    And I should not see "David answer one"
    And I should see "Your response"
    # Empty form field should be displayed.
    And ".tui-formField" "css_element" should exist in the ".tui-performElementResponse" "css_element"
    And I should not see "Appraiser Four"

    # Check appraiser's view (view only)
    When I am on homepage
    And I log out
    And I log in as "appraiser"
    And I navigate to the "print" user activity page for performance activity "Single section activity" where "david" is the subject and "appraiser" is the participant
    Then I should see perform activity relationship to user "Appraiser"
    And I should not see "Your response"
    And I should see "David answer one"
    And I should see "Manager response"
    And I should see "John One"
    And I should see "No response submitted"

  Scenario: Print view for single section with user having multiple relationships
    # Add a response as the subject.
    When I log in as "john"
    And I navigate to the outstanding perform activities list page
    And I click on "Single section activity" "link"
    And I wait until ".tui-performElementResponse .tui-formField" "css_element" exists
    And I answer "short text" question "Question 1" with "John answer one"
    And I click on "Submit" "button"
    And I confirm the tui confirmation modal
    And I should see "Section submitted and closed." in the tui success notification toast
    And I log out

    # Check manager-appraiser's view.
    When I log in as "manager-appraiser"
    And I navigate to the outstanding perform activities list page
    And I click on "Activities about others" "link"
    And I toggle expanding row "1" of the tui datatable
    And I click on "Print activity" "button"
    Then I should see "Select relationship to continue" in the ".tui-modalContent" "css_element"
    # Check as manager.
    When I click on the "Manager (Not yet started)" tui radio
    And I click on "Continue" "button"
    Then I should see "Your response"
    # Empty form field should be displayed for logged in user when no response has been given yet.
    And ".tui-formField" "css_element" should exist in the ".tui-performElementResponse" "css_element"
    And I should not see "Subject response"
    And I should not see "Appraiser response"

    # Check as appraiser (view-only).
    When I navigate to the outstanding perform activities list page
    And I click on "Activities about others" "link"
    And I toggle expanding row "1" of the tui datatable
    And I click on "Print activity" "button"
    Then I should see "Select relationship to continue" in the ".tui-modalContent" "css_element"
    When I click on the "Appraiser (View only)" tui radio
    And I click on "Continue" "button"
    Then I should not see "Your response"
    And I should see "Manager response"
    And I should see "Managerappraiser Three"
    And I should see "No response submitted"