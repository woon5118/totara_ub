@totara @perform @totara_competency @competency_achievement @javascript
Feature: Manage Competency achievement paths

  Background:
    Given I am on a totara site
    And a competency scale called "ggb" exists with the following values:
      | name    | description          | idnumber       | proficient | default | sortorder |
      | Great   | Is great at doing it | great          | 1          | 0       | 1         |
      | Good    | Is ok at doing it    | good           | 0          | 0       | 2         |
      | Bad     | Has no idea          | bad            | 0          | 1       | 3         |
    And the following "competency" frameworks exist:
      | fullname             | idnumber | description                    | scale  |
      | Competency Framework | fw1      | Framework for Competencies     | ggb    |
    And the following "competency" hierarchy exists:
      | framework | fullname   | idnumber | description       | parent |
      | fw1       | Parent     | parent   | Parent competency |        |
      | fw1       | Child1     | child1   | First child       | parent |
      | fw1       | Child2     | child2   | Second child      | parent |
      | fw1       | Another    | another  | Some other        |        |
    And the following "courses" exist:
      | fullname    | shortname | enablecompletion |
      | Course 1    | course1   | 1                |
      | Course 2    | course2   | 1                |
      | Course 3    | course3   | 1                |
      | No tracking | notrack   | 0                |

  Scenario: Add multiple achievement paths for a competency
    Given I log in as "admin"
    And I navigate to the competency achievement paths page for the "Parent" competency
    Then I should see "No achievement paths added"
    And the "Apply changes" "button" should be disabled

    When I add a "manual" pathway
    And I wait for pending js
    Then I should see "manual" pathway "before" criteria groups

    When I add a "manual" pathway
    And I wait for pending js
    And I click on "Add raters" "button" in "manual" pathway "2" "before" criteria groups
    And I toggle the legacy adder list entry "Self" in "Select raters"
    And I save my legacy selections and close the "Select raters" adder
    And I wait for pending js
    Then I should see "Self" in "manual" pathway "2" "before" criteria groups

    When I click on "Add raters" "button" in "manual" pathway "1" "before" criteria groups
    And I toggle the legacy adder list entry "Manager" in "Select raters"
    And I save my legacy selections and close the "Select raters" adder
    And I wait for pending js
    Then I should see "Manager" in "manual" pathway "1" "before" criteria groups

    When I add a "singlevalue" pathway
    Then I should see the following singlevalue scale values:
      | name  |
      | Great |
      | Good  |
      | Bad   |
    And the "Criteria-based paths" "option" should be disabled in the "[data-tw-editachievementpaths-add-pathway]" "css_element"

    When I add a "learning_plan" pathway
    And I wait for pending js
    Then I should see "learning_plan" pathway "after" criteria groups
    And the "Learning plan" "option" should be disabled in the "[data-tw-editachievementpaths-add-pathway]" "css_element"

    When I add a criteria group with "coursecompletion" criterion to "Good" scalevalue
    And I wait for pending js
    And I toggle criterion detail of "coursecompletion" criterion "1" in criteria group "1" in "Good" scalevalue
    And I click on "Add courses" "button" in "coursecompletion" criterion "1" in criteria group "1" in "Good" scalevalue
    And I toggle the legacy adder list entry "Course 1" in "Select courses"
    And I save my legacy selections and close the "Select courses" adder
    And I wait for pending js
    Then I should see "Course 1" in "coursecompletion" criterion "1" in criteria group "1" in "Good" scalevalue

    When I click on "Apply changes" "button"
    And I wait for pending js
    Then I should see "Changes applied successfully"

    # Reload to ensure all saved and retrieved correctly
    And I navigate to the competency achievement paths page for the "Parent" competency
    Then I should see "Manager" in "manual" pathway "1" "before" criteria groups
    And I should see "Manager" in "manual" pathway "1" "before" criteria groups
    And I should see "learning_plan" pathway "after" criteria groups
    And the "Learning plan" "option" should be disabled in the "[data-tw-editachievementpaths-add-pathway]" "css_element"
    And I should see "coursecompletion" criterion in criteria group "1" in "Good" scalevalue
    When I toggle criterion detail of "coursecompletion" criterion "1" in criteria group "1" in "Good" scalevalue
    Then I should see "Course 1" in "coursecompletion" criterion "1" in criteria group "1" in "Good" scalevalue



