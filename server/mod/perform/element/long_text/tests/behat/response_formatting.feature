@totara @perform @mod_perform @perform_element @javascript @vuejs
Feature: Long text element response correctly processes text

  Background:
    Given I am on a totara site
    And the following "users" exist:
      | username | firstname | lastname | email                   |
      | john     | John      | One      | john.one@example.com    |
    And the following "cohorts" exist:
      | name | idnumber |
      | aud1 | aud1     |
    And the following "cohort members" exist:
      | user | cohort |
      | john | aud1   |

    # Enabling multi-language filters for headings and content.
    And the multi-language content filter is enabled

    And the following "activities" exist in "mod_perform" plugin:
      | activity_name | create_section | create_track | activity_status |
      | activity1     | false          | false        | Draft           |
    And the following "activity tracks" exist in "mod_perform" plugin:
      | activity_name | track_description | due_date_offset |
      | activity1     | track1            | 3, DAY          |
    And the following "track assignments" exist in "mod_perform" plugin:
      | track_description | assignment_type | assignment_name |
      | track1            | cohort          | aud1            |
    And the following "activity sections" exist in "mod_perform" plugin:
      | activity_name | section_name |
      | activity1     | section1     |
    And the following "section relationships" exist in "mod_perform" plugin:
      | section_name | relationship |
      | section1     | subject      |
    And the following "section elements" exist in "mod_perform" plugin:
      | section_name | element_name | title     |
      | section1     | long_text    | question1 |

    And I log in as "admin"
    And I navigate to the edit perform activities page for activity "activity1"
    And I click on the "On completion" tui toggle button
    And I click on "Activate" tui "button" in the "draft state" tui "action_card"
    And I confirm the tui confirmation modal
    And I run the scheduled task "mod_perform\task\expand_assignments_task"
    And I run the scheduled task "mod_perform\task\create_subject_instance_task"
    And I log out

  Scenario: Performance activity long text response data is formatted correctly in various circumstances
    When I log in as "john"
    And I navigate to the outstanding perform activities list page
    And I click on "activity1" "link"
    And I wait until ".tui-performElementResponse .tui-formField" "css_element" exists
    And I answer "long text" question "question1" with "<h1>This is a test heading</h1>With some <strong>capital</strong> text<script>alert(1234);</script><&\'"
    And I click on "Save as draft" "button"
    And I click on "Cancel" "button"
    And I click on "activity1" "link"
    And I wait until ".tui-performElementResponse .tui-formField" "css_element" exists
    Then the following fields match these values:
      | question1 | <h1>This is a test heading</h1>With some <strong>capital</strong> text<script>alert(1234);</script><&\' |

    When I click on "Submit" "button"
    And I confirm the tui confirmation modal
    And I click on "activity1" "link"
    Then I should see "This is a test heading" in the ".tui-longTextElementParticipantResponse__answer h1" "css_element"
    And I should see "capital" in the ".tui-longTextElementParticipantResponse__answer strong" "css_element"
    And I should see "With some capital text<&\'"
    And I should not see "1234"
