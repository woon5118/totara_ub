@totara @perform @totara_competency @javascript @vuejs
Feature: Competency profile landing page - an overview of their progress towards completing their competency assignments

  Background:
    # Cohorts (ie Audiences)
    Given the following "cohorts" exist:
      | name   | idnumber |
      | Cohort | cohort   |

    # Organisations
    And the following "organisation frameworks" exist in "totara_hierarchy" plugin:
      | fullname          | idnumber |
      | Organisation FW 1 | orgfw    |
    And the following "organisations" exist in "totara_hierarchy" plugin:
      | org_framework | fullname     | shortname | idnumber |
      | orgfw         | Organisation | org       | org      |

    # Positions
    And the following "position frameworks" exist in "totara_hierarchy" plugin:
      | fullname      | idnumber |
      | Position FW 1 | posfw    |
    And the following "positions" exist in "totara_hierarchy" plugin:
      | pos_framework | fullname | shortname | idnumber |
      | posfw         | Position | pos       | pos      |

    # Users and job assignments
    And the following "users" exist:
      | username | firstname | lastname | email             |
      | user1    | User      | One      | user1@example.com |
      | user2    | User      | Two      | user2@example.com |
    And the following job assignments exist:
      | user  | idnumber | manager | organisation | position |
      | user1 | 1        | user2   | org          | pos      |
    And the following "cohort members" exist:
      | user  | cohort |
      | user1 | cohort |

    # Competencies
    And a competency scale called "scale1" exists with the following values:
      | name          | description       | idnumber     | proficient | default | sortorder |
      | Competent     | Can do the task.  | competent    | 1          | 0       | 1         |
      | Not Competent | Can't do the task | notcompetent | 0          | 1       | 2         |
    And the following "competency" frameworks exist:
      | fullname                 | idnumber | description                | scale  |
      | Competency Framework One | fw1      | Framework for Competencies | scale1 |
    And the following "competency" hierarchy exists:
      | fullname                 | idnumber      | framework |
      | 001_Pos_NotProficient    | comp1pos1     | fw1       |
      | 005_Pos_NotProficient    | comp5pos2     | fw1       |
      | 009_Pos_NotProficient    | comp9pos3     | fw1       |
      | 013_Pos_NotProficient    | comp13pos4    | fw1       |
      | 002_Org_Proficient       | comp2org1     | fw1       |
      | 006_Org_NotProficient    | comp6org2     | fw1       |
      | 010_Org_NotProficient    | comp10org3    | fw1       |
      | 014_Org_NotProficient    | comp14org4    | fw1       |
      | 003_Cohort_Proficient    | comp3cohort1  | fw1       |
      | 008_Cohort_Proficient    | comp7cohort2  | fw1       |
      | 011_Cohort_Proficient    | comp11cohort3 | fw1       |
      | 015_Cohort_NotProficient | comp15cohort4 | fw1       |
      | 004_User_Proficient      | comp4user1    | fw1       |
      | 007_User_Proficient      | comp8user2    | fw1       |
      | 012_User_Proficient      | comp12user3   | fw1       |
      | 016_User_Proficient      | comp16user4   | fw1       |

    And the following "assignments" exist in "totara_competency" plugin:
      | competency    | user_group_type | user_group |
      | comp1pos1     | position        | pos        |
      | comp5pos2     | position        | pos        |
      | comp9pos3     | position        | pos        |
      | comp13pos4    | position        | pos        |
      | comp2org1     | organisation    | org        |
      | comp6org2     | organisation    | org        |
      | comp10org3    | organisation    | org        |
      | comp14org4    | organisation    | org        |
      | comp3cohort1  | cohort          | cohort     |
      | comp7cohort2  | cohort          | cohort     |
      | comp11cohort3 | cohort          | cohort     |
      | comp15cohort4 | cohort          | cohort     |
      | comp4user1    | user            | user1      |
      | comp8user2    | user            | user1      |
      | comp12user3   | user            | user1      |
      | comp16user4   | user            | user1      |
    And I run the scheduled task "totara_competency\task\expand_assignments_task"

    # Pathways for reaching proficiency
    And the following "manual pathways" exist in "totara_competency" plugin:
      | competency    | roles |
      | comp1pos1     | self  |
      | comp5pos2     | self  |
      | comp9pos3     | self  |
      | comp13pos4    | self  |
      | comp2org1     | self  |
      | comp6org2     | self  |
      | comp10org3    | self  |
      | comp14org4    | self  |
      | comp3cohort1  | self  |
      | comp7cohort2  | self  |
      | comp11cohort3 | self  |
      | comp15cohort4 | self  |
      | comp4user1    | self  |
      | comp8user2    | self  |
      | comp12user3   | self  |
      | comp16user4   | self  |
    And the following "manual ratings" exist in "totara_competency" plugin:
      | competency    | subject_user | rater_user | role | scale_value  |
      | comp1pos1     | user1        | user1      | self | notcompetent |
      | comp5pos2     | user1        | user1      | self | notcompetent |
      | comp9pos3     | user1        | user1      | self | notcompetent |
      | comp13pos4    | user1        | user1      | self | notcompetent |
      | comp2org1     | user1        | user1      | self | competent    |
      | comp6org2     | user1        | user1      | self | notcompetent |
      | comp10org3    | user1        | user1      | self | notcompetent |
      | comp14org4    | user1        | user1      | self | notcompetent |
      | comp3cohort1  | user1        | user1      | self | competent    |
      | comp7cohort2  | user1        | user1      | self | competent    |
      | comp11cohort3 | user1        | user1      | self | competent    |
      | comp15cohort4 | user1        | user1      | self | notcompetent |
      | comp4user1    | user1        | user1      | self | competent    |
      | comp8user2    | user1        | user1      | self | competent    |
      | comp12user3   | user1        | user1      | self | competent    |
      | comp16user4   | user1        | user1      | self | competent    |
    And I run the scheduled task "totara_competency\task\competency_aggregation_queue"

    When I am on a totara site
    And I log in as "user2"
    And I navigate to the competency profile of user "user1"

  @chartjs
  Scenario: View the charts and filter the competency charts
    # Assignment progress charts
    Then I should see "Current assignment progress"
    And I should see "Position" in the ".tui-competencyProfileCurrentProgress li:nth-child(1)" "css_element"
    And I should see "proficient 0%" in the ".tui-competencyProfileCurrentProgress li:nth-child(1)" "css_element"
    And I should see "Organisation" in the ".tui-competencyProfileCurrentProgress li:nth-child(2)" "css_element"
    And I should see "proficient 25%" in the ".tui-competencyProfileCurrentProgress li:nth-child(2)" "css_element"
    And I should see "Cohort" in the ".tui-competencyProfileCurrentProgress li:nth-child(3)" "css_element"
    And I should see "proficient 75%" in the ".tui-competencyProfileCurrentProgress li:nth-child(3)" "css_element"
    And I should see "Directly assigned" in the ".tui-competencyProfileCurrentProgress li:nth-child(4)" "css_element"
    And I should see "proficient 100%" in the ".tui-competencyProfileCurrentProgress li:nth-child(4)" "css_element"

    # View the competency charts
    Then I should see "Position" in the "li:nth-child(1) .tui-competencyCharts__chart" "css_element"
    And I should see "Organisation" in the "li:nth-child(2) .tui-competencyCharts__chart" "css_element"
    And I should see "Cohort" in the "li:nth-child(3) .tui-competencyCharts__chart" "css_element"
    And I should see "Directly assigned" in the "li:nth-child(4) .tui-competencyCharts__chart" "css_element"

    # Filter the competency charts, only Position should be listed
    When I set the field "Viewing by assignment" to "Position"
    Then I should see "Position" in the ".tui-competencyProfile__filtersBar + div li:nth-child(1) .tui-competencyCharts__chart" "css_element"
    And I should not see "Organisation" in the ".tui-competencyProfile__filtersBar + div" "css_element"
    And I should not see "Cohort" in the ".tui-competencyProfile__filtersBar + div" "css_element"
    And I should not see "Directly assigned" in the ".tui-competencyProfile__filtersBar + div" "css_element"

  Scenario: View the competency list and filter it
    When I change the competency profile to list view
    # Sorted Alphabetically by default
    Then I should see the tui datatable contains:
      | Competency               | Proficient | Achievement level |
      | 001_Pos_NotProficient    | - No       | Not Competent     |
      | 002_Org_Proficient       | Yes        | Competent         |
      | 003_Cohort_Proficient    | Yes        | Competent         |
      | 004_User_Proficient      | Yes        | Competent         |
      | 005_Pos_NotProficient    | - No       | Not Competent     |
      | 006_Org_NotProficient    | - No       | Not Competent     |
      | 007_User_Proficient      | Yes        | Competent         |
      | 008_Cohort_Proficient    | Yes        | Competent         |
      | 009_Pos_NotProficient    | - No       | Not Competent     |
      | 010_Org_NotProficient    | - No       | Not Competent     |
      | 011_Cohort_Proficient    | Yes        | Competent         |
      | 012_User_Proficient      | Yes        | Competent         |
      | 013_Pos_NotProficient    | - No       | Not Competent     |
      | 014_Org_NotProficient    | - No       | Not Competent     |
      | 015_Cohort_NotProficient | - No       | Not Competent     |
      | 016_User_Proficient      | Yes        | Competent         |

    When I set the field "Proficiency status" to "Proficient"
    Then I should see the tui datatable contains:
      | Competency            | Proficient | Achievement level |
      | 002_Org_Proficient    | Yes        | Competent         |
      | 003_Cohort_Proficient | Yes        | Competent         |
      | 004_User_Proficient   | Yes        | Competent         |
      | 007_User_Proficient   | Yes        | Competent         |
      | 008_Cohort_Proficient | Yes        | Competent         |
      | 011_Cohort_Proficient | Yes        | Competent         |
      | 012_User_Proficient   | Yes        | Competent         |
      | 016_User_Proficient   | Yes        | Competent         |

    When I set the field "Proficiency status" to "Not proficient"
    Then I should see the tui datatable contains:
      | Competency               | Proficient | Achievement level |
      | 001_Pos_NotProficient    | - No       | Not Competent     |
      | 005_Pos_NotProficient    | - No       | Not Competent     |
      | 006_Org_NotProficient    | - No       | Not Competent     |
      | 009_Pos_NotProficient    | - No       | Not Competent     |
      | 010_Org_NotProficient    | - No       | Not Competent     |
      | 013_Pos_NotProficient    | - No       | Not Competent     |
      | 014_Org_NotProficient    | - No       | Not Competent     |
      | 015_Cohort_NotProficient | - No       | Not Competent     |

    When I set the field "Proficiency status" to "All"
    And I set the field "Viewing by assignment" to "Organisation"
    Then I should see the tui datatable contains:
      | Competency            | Proficient | Achievement level |
      | 002_Org_Proficient    | Yes        | Competent         |
      | 006_Org_NotProficient | - No       | Not Competent     |
      | 010_Org_NotProficient | - No       | Not Competent     |
      | 014_Org_NotProficient | - No       | Not Competent     |

    When I set the field "Viewing by assignment" to "Current assignments"
    And I set the field "Search" to "01"
    And I wait "2" seconds
    Then I should see the tui datatable contains:
      | Competency               | Proficient | Achievement level |
      | 001_Pos_NotProficient    | - No       | Not Competent     |
      | 010_Org_NotProficient    | - No       | Not Competent     |
      | 011_Cohort_Proficient    | Yes        | Competent         |
      | 012_User_Proficient      | Yes        | Competent         |
      | 013_Pos_NotProficient    | - No       | Not Competent     |
      | 014_Org_NotProficient    | - No       | Not Competent     |
      | 015_Cohort_NotProficient | - No       | Not Competent     |
      | 016_User_Proficient      | Yes        | Competent         |

  Scenario: View archived assignments in competency list
    Given all assignments for the "comp13pos4" competency are archived
    And all assignments for the "comp14org4" competency are archived
    And all assignments for the "comp15cohort4" competency are archived
    And all assignments for the "comp16user4" competency are archived

    When I reload the page
    And I change the competency profile to list view
    Then I should see the tui datatable contains:
      | Competency            | Proficient | Achievement level |
      | 001_Pos_NotProficient | - No       | Not Competent     |
      | 002_Org_Proficient    | Yes        | Competent         |
      | 003_Cohort_Proficient | Yes        | Competent         |
      | 004_User_Proficient   | Yes        | Competent         |
      | 005_Pos_NotProficient | - No       | Not Competent     |
      | 006_Org_NotProficient | - No       | Not Competent     |
      | 007_User_Proficient   | Yes        | Competent         |
      | 008_Cohort_Proficient | Yes        | Competent         |
      | 009_Pos_NotProficient | - No       | Not Competent     |
      | 010_Org_NotProficient | - No       | Not Competent     |
      | 011_Cohort_Proficient | Yes        | Competent         |
      | 012_User_Proficient   | Yes        | Competent         |
    When I set the field "Viewing by assignment" to "Archived assignments"
    Then I should see the tui datatable contains:
      | Competency               | Reason assigned   | Proficient | Achievement level |
      | 013_Pos_NotProficient    | Position          | - No       | Not Competent     |
      | 014_Org_NotProficient    | Organisation      | - No       | Not Competent     |
      | 015_Cohort_NotProficient | Cohort            | - No       | Not Competent     |
      | 016_User_Proficient      | Directly assigned | - No       | Competent         |

  Scenario: View rating scale detail and navigate to detail page for competency
    When I change the competency profile to list view

    # Rating scale popover
    And I click on "Not Competent" "button" in the ".tui-dataTableRow:nth-child(1)" "css_element"
    Then I should see "Rating scale" in the ".tui-dataTableRow:nth-child(1) .tui-popoverFrame" "css_element"
    And I should see "Competent" in the ".tui-competencyRatingScaleOverview__list li:nth-child(1)" "css_element"
    And I should see "This value is proficient" in the ".tui-competencyRatingScaleOverview__list li:nth-child(1)" "css_element"
    And I should see "Not Competent" in the ".tui-competencyRatingScaleOverview__list li:nth-child(2)" "css_element"
    And I should see "This value is not proficient" in the ".tui-competencyRatingScaleOverview__list li:nth-child(2)" "css_element"

    # View competency detail page
    When I click on "001_Pos_NotProficient" "link"
    Then I should see "Back to competency profile"
    And I should see "Competency Details"
    And I should see "001_Pos_NotProficient" in the ".tui-competencyDetail__title" "css_element"

  Scenario: Latest achievement shows latest competency to be proficient
    Given the following "manual ratings" exist in "totara_competency" plugin:
      | competency | subject_user | rater_user | role | scale_value |
      | comp1pos1  | user1        | user1      | self | competent   |
    And I run the scheduled task "totara_competency\task\competency_aggregation_queue"
    Then I should see "Latest achievement" in the ".tui-competencyProfileCurrentProgress__latestAchievement" "css_element"
    And I should see "001_Pos_NotProficient" in the ".tui-competencyProfileCurrentProgress__latestAchievement" "css_element"

    Given the following "manual ratings" exist in "totara_competency" plugin:
      | competency    | subject_user | rater_user | role | scale_value |
      | comp15cohort4 | user1        | user1      | self | competent   |
    And I run the scheduled task "totara_competency\task\competency_aggregation_queue"
    Then I should see "015_Cohort_NotProficient" in the ".tui-competencyProfileCurrentProgress__latestAchievement" "css_element"

  Scenario: View message shows if no active assignments
    Given all assignments for the "position" assignment type are archived
    And all assignments for the "organisation" assignment type are archived
    And all assignments for the "cohort" assignment type are archived
    And all assignments for the "user" assignment type are archived
    When I reload the page
    Then I should see "This user has no current assignments" in the ".tui-competencyProfileCurrentProgress li:nth-child(1)" "css_element"
