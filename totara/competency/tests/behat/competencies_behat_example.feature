@totara @totara_competency @javascript
Feature: Example behat scenario for creating data needed for testing competencies.

  Background:
    Given I am on a totara site
    When I log in as "admin"

    # Cohorts (ie Audiences)
    And the following "cohorts" exist:
      | name       | idnumber |
      | Cohort One | cohort1  |

    # Organisations
    And the following "organisation frameworks" exist in "totara_hierarchy" plugin:
      | fullname            | idnumber |
      | Organisation Root 1 | OFW001   |
    And the following "organisations" exist in "totara_hierarchy" plugin:
      | org_framework | fullname       | shortname | idnumber |
      | OFW001        | Organisation 1 | org1      | org1     |

    # Positions
    And the following "position frameworks" exist in "totara_hierarchy" plugin:
      | fullname        | idnumber |
      | Position Root 1 | PFW001   |
    And the following "positions" exist in "totara_hierarchy" plugin:
      | pos_framework | fullname   | shortname | idnumber |
      | PFW001        | Position 1 | pos1      | pos1     |

    # Users and job assignments
    And the following "users" exist:
      | username | firstname | lastname | email             |
      | user1    | User      | One      | user1@example.com |
      | user2    | User      | Two      | user2@example.com |
      | user3    | User      | Three    | user3@example.com |
    And the following job assignments exist:
      | user  | idnumber | manager | appraiser | organisation | position |
      | user1 | 1        | user2   | user3     | org1         | pos1     |

    # Courses and course completions
    And the following "courses" exist:
      | fullname | shortname | enablecompletion |
      | Course 1 | course1   | 1                |
      | Course 2 | course2   | 1                |
      | Course 3 | course3   | 1                |
    And the following "course enrollments and completions" exist in "totara_competency" plugin:
      | user  | course  |
      | user1 | course1 |
      | user1 | course2 |

    # Competencies
    And a competency scale called "scale1" exists with the following values:
      | name                   | description                            | idnumber     | proficient | default | sortorder |
      | Competent              | Can do the task.                       | competent    | 1          | 0       | 1         |
      | Not Competent          | Can't do the task                      | notcompetent | 0          | 1       | 2         |
    And a competency scale called "scale2" exists with the following values:
      | name                   | description                            | idnumber     | proficient | default | sortorder |
      | Super Competent        | <strong>Is great at doing it.</strong> | super        | 1          | 0       | 1         |
      | Just Barely Competent  | Is okay at doing it.                   | barely       | 1          | 0       | 2         |
      | Incredibly Incompetent | <em>Is rubbish at doing it.</em>       | incompetent  | 0          | 1       | 3         |
    And the following "competency" frameworks exist:
      | fullname                 | idnumber | description                    | scale  |
      | Competency Framework One | fw1      | Framework for Competencies     | scale1 |
      | Competency Framework Two | fw2      | Framework for Competencies too | scale2 |
    And the following hierarchy types exist:
      | hierarchy  | idnumber | fullname            |
      | competency | type1    | Competency Type One |
      | competency | type2    | Competency Type Two |
    And the following "competency" hierarchy exists:
      | framework | fullname | idnumber | type  | description                    | parent | assignavailability |
      | fw1       | Comp1    | comp1    | type1 | <strong>Rich Text</strong>     |        | none               |
      | fw2       | Comp2    | comp2    | type2 |                                |        | any                |
      | fw1       | Comp3    | comp3    | type1 |                                | comp1  | self               |
      | fw2       | Comp4    | comp4    | type2 |                                | comp2  | other              |

    # Competency assignments
    And the following "assignments" exist in "totara_competency" plugin:
      | competency | user_group_type | user_group |
      | comp1      | user            | user1      |
      | comp2      | cohort          | cohort1    |
      | comp3      | position        | pos1       |
      | comp4      | organisation    | org1       |
    # Expand the assignments - needed for them to be activated
    And I run the scheduled task "totara_competency\task\expand_assignments_task"

    # Simple Criteria Setup
    And the following "onactivate" exist in "totara_criteria" plugin:
      | idnumber    | competency |
      | onactivate1 | comp3      |
    And the following "coursecompletion" exist in "totara_criteria" plugin:
      | idnumber          | courses                 | number_required |
      | coursecompletion1 | course1,course2,course3 | 2               |
    And the following "criteria group pathways" exist in "totara_competency" plugin:
      | competency  | scale_value  | criteria          | sortorder |
      | comp3       | notcompetent | onactivate1       | 1         |
      | comp3       | competent    | coursecompletion1 | 1         |
    And the default achievement paths exist for the "comp4" competency

    # Complex Criteria Setup
    And the following "childcompetency" exist in "totara_criteria" plugin:
      | idnumber         | competency | number_required |
      | childcompetency1 | comp2      | all             |
    And the following "coursecompletion" exist in "totara_criteria" plugin:
      | idnumber          | courses                 | number_required |
      | coursecompletion2 | course1,course2         | 1               |
      | coursecompletion3 | course1,course2,course3 | all             |
      | coursecompletion4 | course1                 | all             |
    And the following "onactivate" exist in "totara_criteria" plugin:
      | idnumber    | competency |
      | onactivate2 | comp2      |
    And the following "criteria group pathways" exist in "totara_competency" plugin:
      | competency  | scale_value        | criteria                            | sortorder |
      | comp2       | super              | childcompetency1,coursecompletion2  | 1         |
      | comp2       | super              | coursecompletion3,coursecompletion4 | 2         |
      | comp2       | barely             | childcompetency1,coursecompletion2  | 1         |
      | comp2       | incompetent        | onactivate2                         | 1         |

    # Manual rating stuff
    And the following "manual pathways" exist in "totara_competency" plugin:
      | competency | roles                  | sortorder |
      | comp1      | self                   | 2         |
      | comp2      | self,manager,appraiser | 1         |
    And the following "manual ratings" exist in "totara_competency" plugin:
      | competency | subject_user | rater_user | role      | scale_value  | comment             | date       |
      | comp1      | user1        | user1      | self      | competent    | I'm good            | 2020-01-01 |
      | comp2      | user1        | user1      | self      | super        | I'm super good!     |            |
      | comp2      | user1        | user2      | manager   | barely       | My staff is alright | 2020-01-02 |
      | comp2      | user1        | user3      | appraiser | incompetent  | My appraisee is bad |            |

    # Learning plan pathway
    And the following "learning plan pathways" exist in "totara_competency" plugin:
      | competency | sortorder |
      | comp1      | 1         |
      | comp2      | 2         |

    # Run aggregation - this calculates how competent users are for competencies
    When I run the scheduled task "totara_competency\task\competency_aggregation_queue"

    # Needed to view older perform pages - such as assignments, evidence, achievement paths and linked courses etc
    And I set the site theme to "basis"

    # Needed to view pages written in vue - such as competency profile, bulk manual rating, self assignment, competency summary etc
    And I set the site theme to "ventura"

    And I log out

  Scenario: View the competency profile for a user I manage
    Given I log in as "user2"
    When I am on profile page for user "user1"
    And I click on "Competency profile" "link" in the ".userprofile" "css_element"
    Then I should see "Competency profile"
