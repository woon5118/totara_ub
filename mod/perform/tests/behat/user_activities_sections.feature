@totara @perform @mod_perform @javascript @vuejs
Feature: Viewing the section list in the user activities view and navigating to a particular section

  Background:
    Given the following "users" exist:
      | username          | firstname | lastname | email                              |
      | john              | John      | One      | john.one@example.com               |
      | david             | David     | Two      | david.two@example.com              |
      | harry             | Harry     | Three    | harry.three@example.com            |
      | manager-appraiser | combined  | Three    | manager-appraiser.four@example.com |
    And the following job assignments exist:
      | user | manager           | appraiser         |
      | john | manager-appraiser | manager-appraiser |
      | john | david             | harry             |
    And the following "activities" exist in "mod_perform" plugin:
      | activity_name             | create_section | create_track | activity_status |
      | Multiple section Activity | false          | false        | Active          |
    And the following "activity settings" exist in "mod_perform" plugin:
      | activity_name             | close_on_completion | multisection |
      | Multiple section Activity | yes                 | yes          |
    And the following "activity sections" exist in "mod_perform" plugin:
      | activity_name             | section_name |
      | Multiple section Activity | Section 1    |
      | Multiple section Activity | Section 2    |
      | Multiple section Activity | Section 3    |
    And the following "cohorts" exist:
      | name | idnumber | description | contextlevel | reference | cohorttype |
      | aud1 | aud1     | Audience 1  | System       | 0         | 1          |
    And the following "cohort members" exist:
      | user  | cohort |
      | john  | aud1   |
      | david | aud1   |
    And the following "activity tracks" exist in "mod_perform" plugin:
      | activity_name             | track_description |
      | Multiple section Activity | track 1           |
    And the following "track assignments" exist in "mod_perform" plugin:
      | track_description | assignment_type | assignment_name |
      | track 1           | cohort          | aud1            |
    And the following "section relationships" exist in "mod_perform" plugin:
      | section_name | relationship |
      | Section 1    | subject      |
      | Section 1    | manager      |
      | Section 1    | appraiser    |
      | Section 2    | manager      |
      | Section 3    | subject      |
    And the following "section elements" exist in "mod_perform" plugin:
      | section_name | element_name | title      |
      | Section 1    | short_text   | Question 1 |
      | Section 2    | short_text   | Question 2 |
      | Section 3    | short_text   | Question 3 |
    And I run the scheduled task "mod_perform\task\expand_assignments_task"
    And I run the scheduled task "mod_perform\task\create_subject_instance_task"
    # Now add a second activity, this makes sure that the activities are in the right order
    Given the following "activities" exist in "mod_perform" plugin:
      | activity_name             | create_section | create_track | activity_status |
      | Single section Activity   | false          | false        | Active          |
    And the following "activity settings" exist in "mod_perform" plugin:
      | activity_name             | close_on_completion | multisection |
      | Single section Activity   | yes                 | no           |
    And the following "activity sections" exist in "mod_perform" plugin:
      | activity_name             | section_name |
      | Single section Activity   | Section 4    |
    And the following "activity tracks" exist in "mod_perform" plugin:
      | activity_name             | track_description |
      | Single section Activity   | track 2           |
    And the following "track assignments" exist in "mod_perform" plugin:
      | track_description | assignment_type | assignment_name |
      | track 2           | cohort          | aud1            |
    And the following "section relationships" exist in "mod_perform" plugin:
      | section_name | relationship |
      | Section 4    | subject      |
      | Section 4    | manager      |
    And the following "section elements" exist in "mod_perform" plugin:
      | section_name | element_name | title      |
      | Section 4    | short_text   | Question 4 |
    And I run the scheduled task "mod_perform\task\expand_assignments_task"
    And I run the scheduled task "mod_perform\task\create_subject_instance_task"

  Scenario: List and complete sections as the subject with a single section
    Given I log in as "john"
    When I navigate to the outstanding perform activities list page
    Then I should see the tui datatable contains:
      | Activity title            | Your progress   | Overall activity progress |
      | Single section Activity   | Not yet started | Not yet started           |
      | Multiple section Activity | Not yet started | Not yet started           |
    When I toggle expanding row "1" of the tui datatable
    Then I should not see "Section 4" under the expanded row of the tui datatable
    And I should not see "Untitled section" under the expanded row of the tui datatable
    And I should see the tui datatable in the ".tui-performUserActivityListSection:nth-child(1)" "css_element" contains:
      | Relationship to user | User           | Section progress |
      | Subject              | You            | Not yet started  |
      | Manager              | David Two      | Not yet started  |
      | Manager              | combined Three | Not yet started  |
    When I click on "Single section Activity" "link_or_button"
    Then I should see "Single section Activity" in the ".tui-participantContent__header" "css_element"
    And I should see "Question 4"
    And I should see perform activity relationship to user "Self"
    When I click on "Cancel" "button"
    Then I should see the tui datatable contains:
      | Activity title            | Your progress   | Overall activity progress |
      | Single section Activity   | In progress     | In progress               |
      | Multiple section Activity | Not yet started | Not yet started           |
    When I toggle expanding row "1" of the tui datatable
    Then I should see the tui datatable in the ".tui-performUserActivityListSection:nth-child(1)" "css_element" contains:
      | Relationship to user | User           | Section progress |
      | Subject              | You            | In progress      |
      | Manager              | David Two      | Not yet started  |
      | Manager              | combined Three | Not yet started  |
    When I click on "Single section Activity" "link_or_button"
    Then I should see "Single section Activity" in the ".tui-participantContent__header" "css_element"
    And I should see "Question 4"
    And I should see perform activity relationship to user "Self"
    When I click on "Submit" "button"
    And I confirm the tui confirmation modal
    And I close the tui notification toast
    Then I should see the tui datatable contains:
      | Activity title            | Your progress   | Overall activity progress |
      | Single section Activity   | Complete        | In progress               |
      | Multiple section Activity | Not yet started | Not yet started           |
    When I toggle expanding row "1" of the tui datatable
    Then I should see the tui datatable in the ".tui-performUserActivityListSection:nth-child(1)" "css_element" contains:
      | Relationship to user | User           | Section progress |
      | Subject              | You            | Complete         |
      | Manager              | David Two      | Not yet started  |
      | Manager              | combined Three | Not yet started  |

  Scenario: List and complete sections as the subject with multiple sections
    Given I log in as "john"
    When I navigate to the outstanding perform activities list page
    Then I should see the tui datatable contains:
      | Activity title            | Your progress   | Overall activity progress |
      | Single section Activity   | Not yet started | Not yet started           |
      | Multiple section Activity | Not yet started | Not yet started           |
    When I toggle expanding row "2" of the tui datatable
    Then I should see "Section 1" under the expanded row of the tui datatable
    And I should see "Section 2" under the expanded row of the tui datatable
    And I should see "Section 3" under the expanded row of the tui datatable
    And I should see the tui datatable in the ".tui-performUserActivityListSection:nth-child(1)" "css_element" contains:
      | Relationship to user | User           | Section progress |
      | Subject              | You            | Not yet started  |
      | Manager              | David Two      | Not yet started  |
      | Manager              | combined Three | Not yet started  |
      | Appraiser            | Harry Three    | Not yet started  |
      | Appraiser            | combined Three | Not yet started  |
    And I should see the tui datatable in the ".tui-performUserActivityListSection:nth-child(2)" "css_element" contains:
      | Relationship to user | User           | Section progress |
      | Manager              | David Two      | Not yet started  |
      | Manager              | combined Three | Not yet started  |
    And I should see the tui datatable in the ".tui-performUserActivityListSection:nth-child(3)" "css_element" contains:
      | Relationship to user | User           | Section progress |
      | Subject              | You            | Not yet started  |
    And "Section 1" "link_or_button" should exist in the ".tui-performUserActivityListSection:nth-child(1) .tui-performUserActivityListSection__header" "css_element"
    And "Section 2" "link_or_button" should not exist in the ".tui-performUserActivityListSection:nth-child(2) .tui-performUserActivityListSection__header" "css_element"
    And I should see "Section 2" in the ".tui-performUserActivityListSection:nth-child(2) .tui-performUserActivityListSection__header" "css_element"
    And "Section 3" "link_or_button" should exist in the ".tui-performUserActivityListSection:nth-child(3) .tui-performUserActivityListSection__header" "css_element"
    When I click on "Section 1" "link_or_button" in the ".tui-performUserActivityListSection:nth-child(1) .tui-performUserActivityListSection__header" "css_element"
    Then I should see "Multiple section Activity" in the ".tui-participantContent__header" "css_element"
    Then I should see "Section 1" in the ".tui-participantContent__sectionHeading-title" "css_element"
    And I should see "Question 1"
    And I should see perform activity relationship to user "Self"
    When I click on "Cancel" "button"
    Then I should see the tui datatable contains:
      | Activity title            | Your progress   | Overall activity progress |
      | Single section Activity   | Not yet started | Not yet started           |
      | Multiple section Activity | In progress     | In progress               |
    When I toggle expanding row "2" of the tui datatable
    Then I should see the tui datatable in the ".tui-performUserActivityListSection:nth-child(1)" "css_element" contains:
      | Relationship to user | User           | Section progress |
      | Subject              | You            | In progress      |
      | Manager              | David Two      | Not yet started  |
      | Manager              | combined Three | Not yet started  |
      | Appraiser            | Harry Three    | Not yet started  |
      | Appraiser            | combined Three | Not yet started  |
    And I should see the tui datatable in the ".tui-performUserActivityListSection:nth-child(2)" "css_element" contains:
      | Relationship to user | User           | Section progress |
      | Manager              | David Two      | Not yet started  |
      | Manager              | combined Three | Not yet started  |
    And I should see the tui datatable in the ".tui-performUserActivityListSection:nth-child(3)" "css_element" contains:
      | Relationship to user | User           | Section progress |
      | Subject              | You            | Not yet started  |
    When I click on "Section 3" "link_or_button" in the ".tui-performUserActivityListSection:nth-child(3) .tui-performUserActivityListSection__header" "css_element"
    Then I should see "Multiple section Activity" in the ".tui-participantContent__header" "css_element"
    Then I should see "Section 3" in the ".tui-participantContent__sectionHeading-title" "css_element"
    And I should see "Question 3"
    And I should see perform activity relationship to user "Self"
    When I click on "Submit" "button"
    And I confirm the tui confirmation modal
    And I close the tui notification toast
    Then I should see the tui datatable contains:
      | Activity title            | Your progress   | Overall activity progress |
      | Single section Activity   | Not yet started | Not yet started           |
      | Multiple section Activity | In progress     | In progress               |
    When I toggle expanding row "2" of the tui datatable
    Then I should see the tui datatable in the ".tui-performUserActivityListSection:nth-child(1)" "css_element" contains:
      | Relationship to user | User           | Section progress |
      | Subject              | You            | In progress      |
      | Manager              | David Two      | Not yet started  |
      | Manager              | combined Three | Not yet started  |
      | Appraiser            | Harry Three    | Not yet started  |
      | Appraiser            | combined Three | Not yet started  |
    And I should see the tui datatable in the ".tui-performUserActivityListSection:nth-child(2)" "css_element" contains:
      | Relationship to user | User           | Section progress |
      | Manager              | David Two      | Not yet started  |
      | Manager              | combined Three | Not yet started  |
    And I should see the tui datatable in the ".tui-performUserActivityListSection:nth-child(3)" "css_element" contains:
      | Relationship to user | User           | Section progress |
      | Subject              | You            | Complete         |

  Scenario: List and complete sections as the manager
    Given I log in as "manager-appraiser"
    When I navigate to the outstanding perform activities list page
    And I click on "Activities about others" "link"
    Then I should see the tui datatable contains:
      | Activity title            | User     | Relationship to user | Your progress   | Overall activity progress |
      | Single section Activity   | John One | Manager              | Not yet started | Not yet started           |
      | Multiple section Activity | John One | Manager, Appraiser   | Not yet started | Not yet started           |
    When I toggle expanding row "2" of the tui datatable
    Then I should see "Section 1" under the expanded row of the tui datatable
    And I should see "Section 2" under the expanded row of the tui datatable
    And I should see "Section 3" under the expanded row of the tui datatable
    And I should see the tui datatable in the ".tui-performUserActivityListSection:nth-child(1)" "css_element" contains:
      | Relationship to user | User           | Section progress |
      | Subject              | John One       | Not yet started  |
      | Manager              | David Two      | Not yet started  |
      | Manager              | You            | Not yet started  |
      | Appraiser            | Harry Three    | Not yet started  |
      | Appraiser            | You            | Not yet started  |
    And I should see the tui datatable in the ".tui-performUserActivityListSection:nth-child(2)" "css_element" contains:
      | Relationship to user | User           | Section progress |
      | Manager              | David Two      | Not yet started  |
      | Manager              | You            | Not yet started  |
    And I should see the tui datatable in the ".tui-performUserActivityListSection:nth-child(3)" "css_element" contains:
      | Relationship to user | User           | Section progress |
      | Subject              | John One       | Not yet started  |
    And "Section 1" "link_or_button" should exist in the ".tui-performUserActivityListSection:nth-child(1) .tui-performUserActivityListSection__header" "css_element"
    And "Section 2" "link_or_button" should exist in the ".tui-performUserActivityListSection:nth-child(2) .tui-performUserActivityListSection__header" "css_element"
    And "Section 3" "link_or_button" should not exist in the ".tui-performUserActivityListSection:nth-child(3) .tui-performUserActivityListSection__header" "css_element"
    And I should see "Section 3" in the ".tui-performUserActivityListSection:nth-child(3) .tui-performUserActivityListSection__header" "css_element"
    When I click on "Section 1" "link_or_button" in the ".tui-performUserActivityListSection:nth-child(1) .tui-performUserActivityListSection__header" "css_element"
    Then I should see "Select relationship to continue" in the ".tui-modalContent" "css_element"
    And the "Manager (Not yet started)" radio button is selected
    And the "Appraiser (Not yet started)" radio button is not selected
    When I click on the "Appraiser (Not yet started)" tui radio
    And I click on "Continue" "button"
    Then I should see "Multiple section Activity" in the ".tui-participantContent__header" "css_element"
    And I should see "Section 1" in the ".tui-participantContent__sectionHeading-title" "css_element"
    And I should see "Question 1"
    And I should see perform activity relationship to user "Appraiser"
    When I click on "Cancel" "button"
    Then I should see the tui datatable contains:
      | Activity title            | User     | Relationship to user | Your progress   | Overall activity progress |
      | Single section Activity   | John One | Manager              | Not yet started | Not yet started           |
      | Multiple section Activity | John One | Manager, Appraiser   | In progress     | In progress               |
    When I toggle expanding row "2" of the tui datatable
    Then I should see the tui datatable in the ".tui-performUserActivityListSection:nth-child(1)" "css_element" contains:
      | Relationship to user | User           | Section progress |
      | Subject              | John One       | Not yet started  |
      | Manager              | David Two      | Not yet started  |
      | Manager              | You            | Not yet started  |
      | Appraiser            | Harry Three    | Not yet started  |
      | Appraiser            | You            | In progress      |
    And I should see the tui datatable in the ".tui-performUserActivityListSection:nth-child(2)" "css_element" contains:
      | Relationship to user | User           | Section progress |
      | Manager              | David Two      | Not yet started  |
      | Manager              | You            | Not yet started  |
    And I should see the tui datatable in the ".tui-performUserActivityListSection:nth-child(3)" "css_element" contains:
      | Relationship to user | User           | Section progress |
      | Subject              | John One       | Not yet started  |
    When I click on "Section 2" "link_or_button" in the ".tui-performUserActivityListSection:nth-child(2) .tui-performUserActivityListSection__header" "css_element"
    Then I should see "Multiple section Activity" in the ".tui-participantContent__header" "css_element"
    And I should see "Section 2" in the ".tui-participantContent__sectionHeading-title" "css_element"
    And I should see "Question 2"
    And I should see perform activity relationship to user "Manager"
    When I click on "Submit" "button"
    And I confirm the tui confirmation modal
    And I close the tui notification toast
    Then I should see the tui datatable contains:
      | Activity title            | User     | Relationship to user | Your progress   | Overall activity progress |
      | Single section Activity   | John One | Manager              | Not yet started | Not yet started           |
      | Multiple section Activity | John One | Manager, Appraiser   | In progress     | In progress               |
    When I toggle expanding row "2" of the tui datatable
    Then I should see the tui datatable in the ".tui-performUserActivityListSection:nth-child(1)" "css_element" contains:
      | Relationship to user | User           | Section progress |
      | Subject              | John One       | Not yet started  |
      | Manager              | David Two      | Not yet started  |
      | Manager              | You            | Not yet started  |
      | Appraiser            | Harry Three    | Not yet started  |
      | Appraiser            | You            | In progress      |
    And I should see the tui datatable in the ".tui-performUserActivityListSection:nth-child(2)" "css_element" contains:
      | Relationship to user | User           | Section progress |
      | Manager              | David Two      | Not yet started  |
      | Manager              | You            | Complete         |
    And I should see the tui datatable in the ".tui-performUserActivityListSection:nth-child(3)" "css_element" contains:
      | Relationship to user | User           | Section progress |
      | Subject              | John One       | Not yet started  |