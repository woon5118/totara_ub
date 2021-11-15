@totara @perform @mod_perform @javascript @vuejs
Feature: Job assignment column on user activities list.
  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                    |
      | edward   | Edward    | Eiger    | edward.eiger@example.com |
      | mark     | Mark      | Moolman  | mark.moolman@example.com |
      | jake     | Jake      | Johnson  | jake.johnson@example.com |
      | susan    | Susan     | Steele   | susan.steele@example.com |
    And the following job assignments exist:
      | user   | manager  | appraiser | fullname | idnumber |
      | edward | mark     | jake      | Mark man | Y1k      |
      | edward | susan    | mark      |          | Y2k      |
    And the following "activities" exist in "mod_perform" plugin:
      | activity_name              | activity_type | create_section | create_track | activity_status |
      | All job assignments        | appraisal     | false          | false        | Active          |
      | Specific job assignments   | appraisal     | false          | false        | Active          |
      | Subject assignment         | appraisal     | false          | false        | Active          |
      | Another Subject assignment | appraisal     | false          | false        | Active          |

    And the following "activity sections" exist in "mod_perform" plugin:
      | activity_name              | section_name            |
      | All job assignments        | All job section         |
      | Specific job assignments   | specific section        |
      | Subject assignment         | subject section         |
      | Another Subject assignment | another subject section |

    And the following "cohorts" exist:
      | name | idnumber | description | contextlevel | reference | cohorttype |
      | aud1 | aud1     | Audience 1  | System       | 0         | 1          |
      | aud2 | aud2     | Audience 2  | System       | 0         | 1          |
    And the following "cohort members" exist:
      | user   | cohort |
      | edward | aud1   |
      | edward | aud2   |
      | susan  | aud2   |
      | mark   | aud2   |

    And the following "activity tracks" exist in "mod_perform" plugin:
      | activity_name              | track_description     | subject_instance_generation |
      | All job assignments        | All job track         | ONE_PER_SUBJECT             |
      | Specific job assignments   | specific track        | ONE_PER_JOB                 |
      | Subject assignment         | subject track         | ONE_PER_SUBJECT             |
      | Another Subject assignment | another subject track | ONE_PER_JOB                 |
    And the following "track assignments" exist in "mod_perform" plugin:
      | track_description     | assignment_type | assignment_name |
      | All job track         | cohort          | aud1            |
      | specific track        | cohort          | aud1            |
      | subject track         | cohort          | aud2            |
      | another subject track | cohort          | aud2            |
    And the following "section relationships" exist in "mod_perform" plugin:
      | section_name            | relationship |
      | All job section         | manager      |
      | All job section         | subject      |
      | All job section         | appraiser    |
      | specific section        | subject      |
      | specific section        | appraiser    |
      | subject section         | subject      |
      | another subject section | subject      |
      | another subject section | manager      |
    And the following "section elements" exist in "mod_perform" plugin:
      | section_name            | element_name | title      |
      | All job section         | short_text   | Question 1 |
      | specific section        | short_text   | Question 2 |
      | subject section         | short_text   | Question 3 |
      | another subject section | short_text   | Question 3 |

    And I run the scheduled task "mod_perform\task\expand_assignments_task"
    And I run the scheduled task "mod_perform\task\create_subject_instance_task"

  Scenario: Job assignment column shows when a subject instance with a specific job assignment is listed.
    Given I log in as "mark"
    And I navigate to the outstanding perform activities list page

    # Tests job assignment column isn't shown when all the subject instances are not created per job assignment
    Then I should see the tui datatable contains:
      | Activity title                                | Type      | Overall progress | Your progress   |
      | Another Subject assignment (##today##j F Y##) | Appraisal | Not started      | Not started     |
      | Subject assignment (##today##j F Y##)         | Appraisal | Not started      | Not started     |

    When I click on "Activities about others" "link"

    # Tests job assignment column is shown when any of the subject instances are created per job assignment
    Then I should see the tui datatable contains:
      | Activity title                                | Type      | User         | Job assignment                   | Relationship to user | Overall progress | Your progress            |
      | Another Subject assignment (##today##j F Y##) | Appraisal | Edward Eiger | Mark man                         | Manager              | Not started      | Not started              |
      | Specific job assignments (##today##j F Y##)   | Appraisal | Edward Eiger | Unnamed job assignment (ID: Y2k) | Appraiser            | Not started      | Not started              |
      | All job assignments (##today##j F Y##)        | Appraisal | Edward Eiger | All                              | Manager, Appraiser   | Not started      | Not started, Not started |