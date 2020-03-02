@totara @totara_competency @javascript @vuejs
Feature: Competency profile detail page - an overview of their progress (or lack of) towards completing a particular competency

  Background:
    Given I am on a totara site
    And the following "competency" frameworks exist:
      | fullname                 | idnumber | description                |
      | Competency Framework One | CFrame   | Framework for Competencies |
    And the following hierarchy types exist:
      | hierarchy  | idnumber    | fullname            |
      | competency | Comp type 1 | Competency Type One |
    And the following "competency" hierarchy exists:
      | framework | fullname    | idnumber | type        | description               | assignavailability |
      | CFrame    | Typing slow | typing 1 | Comp type 1 | The ability to type slow. | any                |
      | CFrame    | Typing fast | typing 2 | Comp type 1 | The ability to type fast. | any                |
    And the following "assignments" exist in "totara_competency" plugin:
      | competency | user_group_type | user_group |
      | sa1        | user            | admin      |
    And I run the scheduled task "totara_competency\task\expand_assignments_task"

  Scenario: I can navigate directly to the details page of a competency not assigned to me
    When I log in as "admin"
    And I navigate to the competency profile details page for the "typing slow" competency

    Then I should see "Typing slow"
    And I should see "The ability to type slow."
    And I should see "There are no active assignments"

  Scenario: I am shown a warning when I navigate directly to the details page of a competency that does not exist
    When I log in as "admin"
    And I navigate to the competency profile details page for competency id "9999"

    Then I should see "Back to your competency profile"
    And I should see "The requested competency does not exist." in the tui "error" notification banner

