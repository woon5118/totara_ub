@totara @perform @totara_hierarchy @totara_competency @core_course @javascript @vuejs
Feature: Test competencies are updated when linked courses are deleted

  Scenario: Delete course linked to a single competency
    Given I am on a totara site
    And I disable the "competency_assignment" advanced feature
    And the following "competency" frameworks exist:
      | fullname             | idnumber | description                |
      | Competency Framework | CFrame   | Framework for Competencies |
    And the following "competency" hierarchy exists:
      | framework | fullname       | idnumber | description                           |
      | CFrame    | Competency101  | Comp101  | Competency with linked courses        |
    And the following "courses" exist:
      | fullname | shortname | format | enablecompletion |
      | Test 1   | tst1      | topics | 1                |
      | Test M   | tst2      | topics | 1                |
    And I log in as "admin"

    # link courses.
    When I navigate to "Manage competencies" node in "Site administration > Competencies"
    And I click on "Competency Framework" "link"
    And I click on "Competency101" "link"
    And I click on ".tui-competencyOverviewLinkedCourses__header_edit" "css_element"
    And I click on "Add linked courses" "button"
    And I click on ".tw-list__cell_select_checkbox" "css_element" in the ".modal" "css_element"
    And I click on "Save changes" "button" in the ".modal" "css_element"
    Then "Test 1" "text" should exist in the ".tw-list__row[data-tw-list-row=2]" "css_element"
    And "Test M" "text" should exist in the ".tw-list__row[data-tw-list-row=3]" "css_element"
    Then I click on "Save changes" "button" in the ".tw-editLinkedCourses" "css_element"

    # Check pre-conditions; the courses are linked.
    Then I click on "Competency Framework" "link"
    Then "2" "link" should exist in the "Competency101" "table_row"
    And I click on "2" "link" in the "Competency101" "table_row"
    Then "Test 1" "link" should exist in the ".tui-competencyOverviewLinkedCourses__list_row:first-child" "css_element"
    And "Test M" "link" should exist in the ".tui-competencyOverviewLinkedCourses__list_row:last-child" "css_element"

    # Delete a linked course.
    When I navigate to "Courses and categories" node in "Site administration > Courses"
    And I click on "Miscellaneous" "text" in the ".category-listing" "css_element"
    And I go to the courses management page
    And I click on category "Miscellaneous" in the management interface
    And I click on "delete" action for "Test 1" in management course listing
    And I press "Delete"
    And I should see "tst1 has been completely deleted"
    Then I navigate to "Manage competencies" node in "Site administration > Competencies"
    And I click on "Competency Framework" "link"

    # Post-condition checks; the deleted course is unlinked.
    Then "1" "link" should exist in the "Competency101" "table_row"
    And I click on "Competency101" "link"
    Then "Test 1" "link" should not exist
    And "Test M" "link" should exist in the ".tui-competencyOverviewLinkedCourses__list_row:last-child" "css_element"


  Scenario: Delete course linked to a multiple competencies
    Given I am on a totara site
    And the following "competency" frameworks exist:
      | fullname             | idnumber | description                |
      | Competency Framework | CFrame   | Framework for Competencies |
    And the following "competency" hierarchy exists:
      | framework | fullname       | idnumber | description                           |
      | CFrame    | Competency101  | Comp101  | Competency with linked courses        |
      | CFrame    | Competency102  | Comp102  | Second Competency with linked courses |
    And the following "courses" exist:
      | fullname | shortname | format | enablecompletion |
      | Test 1   | tst1      | topics | 1                |
      | Test M   | tst2      | topics | 1                |
    And I log in as "admin"

    # Link the courses to the first competency.
    When I navigate to "Manage competencies" node in "Site administration > Competencies"
    And I click on "Competency Framework" "link"
    And I click on "Competency101" "link"
    And I click on ".tui-competencyOverviewLinkedCourses__header_edit" "css_element"
    And I click on "Add linked courses" "button"
    And I click on ".tw-list__cell_select_checkbox" "css_element" in the ".modal" "css_element"
    And I click on "Save changes" "button" in the ".modal" "css_element"
    Then "Test 1" "text" should exist in the ".tw-list__row[data-tw-list-row=2]" "css_element"
    And "Test M" "text" should exist in the ".tw-list__row[data-tw-list-row=3]" "css_element"
    Then I click on "Save changes" "button" in the ".tw-editLinkedCourses" "css_element"

    # Link the courses to the second competency.
    When I navigate to "Manage competencies" node in "Site administration > Competencies"
    And I click on "Competency Framework" "link"
    And I click on "Competency102" "link"
    And I click on ".tui-competencyOverviewLinkedCourses__header_edit" "css_element"
    And I click on "Add linked courses" "button"
    And I click on ".tw-list__cell_select_checkbox" "css_element" in the ".modal" "css_element"
    And I click on "Save changes" "button" in the ".modal" "css_element"
    Then "Test 1" "text" should exist in the ".tw-list__row[data-tw-list-row=2]" "css_element"
    And "Test M" "text" should exist in the ".tw-list__row[data-tw-list-row=3]" "css_element"
    Then I click on "Save changes" "button" in the ".tw-editLinkedCourses" "css_element"

    # Check pre-conditions; the courses are linked.
    Then I click on "Competency Framework" "link"
    Then "2" "link" should exist in the "Competency101" "table_row"
    And I click on "2" "link" in the "Competency101" "table_row"
    Then "Test 1" "link" should exist in the ".tui-competencyOverviewLinkedCourses__list_row:first-child" "css_element"
    And "Test M" "link" should exist in the ".tui-competencyOverviewLinkedCourses__list_row:last-child" "css_element"

    Then I click on "Competency Framework" "link"
    Then "2" "link" should exist in the "Competency102" "table_row"
    And I click on "2" "link" in the "Competency102" "table_row"
    Then "Test 1" "link" should exist in the ".tui-competencyOverviewLinkedCourses__list_row:first-child" "css_element"
    And "Test M" "link" should exist in the ".tui-competencyOverviewLinkedCourses__list_row:last-child" "css_element"

    # Delete a linked course.
    When I navigate to "Courses and categories" node in "Site administration > Courses"
    And I click on "Miscellaneous" "text" in the ".category-listing" "css_element"
    And I go to the courses management page
    And I click on category "Miscellaneous" in the management interface
    And I click on "delete" action for "Test M" in management course listing
    And I press "Delete"
    And I should see "tst2 has been completely deleted"

    # Post-condition checks; the deleted course is unlinked.
    Then I navigate to "Manage competencies" node in "Site administration > Competencies"
    And I click on "Competency Framework" "link"
    And "1" "link" should exist in the "Competency101" "table_row"
    And I click on "Competency101" "link"
    And "Test M" "link" should not exist
    And "Test 1" "link" should exist in the ".tui-competencyOverviewLinkedCourses__list_row:last-child" "css_element"

    Then I click on "Competency Framework" "link"
    And "1" "link" should exist in the "Competency102" "table_row"
    And I click on "Competency102" "link"
    And "Test M" "link" should not exist
    And "Test 1" "link" should exist in the ".tui-competencyOverviewLinkedCourses__list_row:last-child" "css_element"
