@totara @totara_core @ndl
Feature: Test reaggregating completion data when changing course completion settings
  In order to test course completion settings
  I must log in as admin and configure the courses
  Then log in as a learner and complete a course completion criteria
  Then log in as admin and change the course completion settings, deleting the existing data
  Then run the scheduled task
  Then log in as the learner and check that progress has been reaggregated

  @javascript
  Scenario: course completion criteria are changed, deleting existing data
    Given I am on a totara site
    # Create users, courses and enrolments.
    And the following "users" exist:
    | username |
    | user1    |
    | user2    |
    | user3    |
    And the following "courses" exist:
    | fullname | shortname | summary          | format | enablecompletion | completionstartonenrol |
    | Course 1 | C1        | Course summary 1 | topics | 1                | 1                      |
    | Course 2 | C2        | Course summary 2 | topics | 1                | 0                      |
    | Course 3 | C3        | Course summary 3 | topics | 1                | 1                      |
    And the following "course enrolments" exist:
    | user  | course | role    |
    | user1 | C1     | student |
    | user2 | C1     | student |
    | user3 | C1     | student |
    | user1 | C2     | student |
    | user2 | C2     | student |
    | user3 | C2     | student |
    | user1 | C3     | student |
    | user2 | C3     | student |
    | user3 | C3     | student |
    # Create Courses 1 Assignment 1.
    Then I log in as "admin"
    And I follow "Course 1"
    And I press "Turn editing on"
    And I wait until the page is ready
    And I click on "Add an activity or resource" "link"
    And I click on "module_assign" "radio"
    And I press "Add"
    And I set the following fields to these values:
    | Assignment name | Assignment 1             |
    | Description     | Assignment 1 description |
    And I press "Save and return to course"
    And I add the "Self completion" block
    # Set completion for Course 1 to Assignment 1 AND Manual self completion (will delete and remove self completion).
    Then I navigate to "Course completion" node in "Course administration"
    And I click on "Condition: Activity completion" "link"
    And I click on "Assignment 1" "checkbox"
    And I click on "Condition: Manual self completion" "link"
    And I click on "criteria_self_value" "checkbox"
    And I press "Save changes"
    # Create Course 2 Assignment 2.
    Then I click on "Home" "link"
    And I follow "Course 2"
    And I wait until the page is ready
    And I click on "Add an activity or resource" "link"
    And I click on "module_assign" "radio"
    And I press "Add"
    And I set the following fields to these values:
    | Assignment name | Assignment 2             |
    | Description     | Assignment 2 description |
    And I press "Save and return to course"
    And I add the "Self completion" block
    # Set completion for Course 2 to Assignment 2 AND Manual self completion (will delete and make no change).
    Then I navigate to "Course completion" node in "Course administration"
    And I click on "Condition: Activity completion" "link"
    And I click on "Assignment 2" "checkbox"
    And I click on "Condition: Manual self completion" "link"
    And I click on "criteria_self_value" "checkbox"
    And I press "Save changes"
    # Create Course 3 Assignment 3.
    Then I click on "Home" "link"
    And I follow "Course 3"
    And I wait until the page is ready
    And I click on "Add an activity or resource" "link"
    And I click on "module_assign" "radio"
    And I press "Add"
    And I set the following fields to these values:
    | Assignment name | Assignment 3             |
    | Description     | Assignment 3 description |
    And I press "Save and return to course"
    And I add the "Self completion" block
    # Set completion for Course 3 to Assignment 3 only (will not delete and add self completion).
    Then I navigate to "Course completion" node in "Course administration"
    And I click on "Condition: Activity completion" "link"
    And I click on "Assignment 3" "checkbox"
    And I press "Save changes"
    # Complete all three courses as user1.
    Then I log out
    And I log in as "user1"
    And I follow "Course 1"
    And I press "Mark as complete: Assignment 1"
    And I click on "Complete course" "link"
    And I press "Yes"
    And I should see "You have already completed this course"
    Then I click on "Home" "link"
    And I follow "Course 2"
    And I press "Mark as complete: Assignment 2"
    And I click on "Complete course" "link"
    And I press "Yes"
    And I should see "You have already completed this course"
    Then I click on "Home" "link"
    And I follow "Course 3"
    And I press "Mark as complete: Assignment 3"
    # Confirm the status of the courses for user1.
    Then I focus on "My Learning" "link"
    And I follow "Record of Learning"
    Then I should see "Complete" in the "#plan_courses #plan_courses_r0 span.completion-complete" "css_element"
    And I should see "Complete" in the "#plan_courses #plan_courses_r1 span.completion-complete" "css_element"
    And I should see "Complete" in the "#plan_courses #plan_courses_r2 span.completion-complete" "css_element"
    # Complete all three assignments (but not manual self completion) as user2.
    Then I log out
    And I log in as "user2"
    And I follow "Course 1"
    And I press "Mark as complete: Assignment 1"
    Then I click on "Home" "link"
    And I follow "Course 2"
    And I press "Mark as complete: Assignment 2"
    Then I click on "Home" "link"
    And I follow "Course 3"
    And I press "Mark as complete: Assignment 3"
    # Confirm the status of the courses for user2.
    Then I focus on "My Learning" "link"
    And I follow "Record of Learning"
    Then I should see "In progress" in the "#plan_courses #plan_courses_r0 span.completion-inprogress" "css_element"
    And I should see "In progress" in the "#plan_courses #plan_courses_r1 span.completion-inprogress" "css_element"
    And I should see "Complete" in the "#plan_courses #plan_courses_r2 span.completion-complete" "css_element"
    # Complete manual self completion (but not assignments) as user3.
    Then I log out
    And I log in as "user3"
    And I follow "Course 1"
    And I click on "Complete course" "link"
    And I press "Yes"
    And I should see "You have already marked yourself as complete in this course"
    Then I click on "Home" "link"
    And I follow "Course 2"
    And I click on "Complete course" "link"
    And I press "Yes"
    And I should see "You have already marked yourself as complete in this course"
    # Confirm the status of the courses for user3.
    Then I focus on "My Learning" "link"
    And I follow "Record of Learning"
    Then I should see "In progress" in the "#plan_courses #plan_courses_r0 span.completion-inprogress" "css_element"
    And I should see "In progress" in the "#plan_courses #plan_courses_r1 span.completion-inprogress" "css_element"
    And "#plan_courses #plan_courses_r2 span" "css_element" should not exist
    # For course 1, unlock with delete and remove Manual self completion. Assignment completion will reaggregate.
    Then I log out
    And I log in as "admin"
    Then I follow "Course 1"
    And I navigate to "Course completion" node in "Course administration"
    And I press "Unlock criteria and delete existing completion data"
    And I click on "criteria_self_value" "checkbox"
    And I press "Save changes"
    # For course 2, just unlock with delete and save again. Manual self completion data will be lost.
    Then I click on "Home" "link"
    And I follow "Course 2"
    And I navigate to "Course completion" node in "Course administration"
    And I press "Unlock criteria and delete existing completion data"
    And I press "Save changes"
    # For course 3, unlock without delete, remove assignment and add Manual self completion. Previous completions are kept.
    Then I click on "Home" "link"
    And I follow "Course 3"
    And I navigate to "Course completion" node in "Course administration"
    And I press "Unlock criteria without deleting"
    And I click on "Assignment 3" "checkbox"
    And I click on "Condition: Manual self completion" "link"
    And I click on "criteria_self_value" "checkbox"
    And I press "Save changes"
    # Confirm the status of the courses for user1. Cron hasn't been run yet, so no reaggregation has occurred.
    Then I log out
    And I log in as "user1"
    And I focus on "My Learning" "link"
    And I follow "Record of Learning"
    Then I should see "Not yet started" in the "#plan_courses #plan_courses_r0 span.completion-notyetstarted" "css_element"
    And I should see "Not yet started" in the "#plan_courses #plan_courses_r1 span.completion-notyetstarted" "css_element"
    And I should see "Complete" in the "#plan_courses #plan_courses_r2 span.completion-complete" "css_element"
    # Confirm the status of the courses for user2. Cron hasn't been run yet, so no reaggregation has occurred.
    Then I log out
    And I log in as "user2"
    And I focus on "My Learning" "link"
    And I follow "Record of Learning"
    Then I should see "Not yet started" in the "#plan_courses #plan_courses_r0 span.completion-notyetstarted" "css_element"
    And I should see "Not yet started" in the "#plan_courses #plan_courses_r1 span.completion-notyetstarted" "css_element"
    And I should see "Complete" in the "#plan_courses #plan_courses_r2 span.completion-complete" "css_element"
    # Confirm the status of the courses for user3. Cron hasn't been run yet, so no reaggregation has occurred.
    Then I log out
    And I log in as "user3"
    And I focus on "My Learning" "link"
    And I follow "Record of Learning"
    Then I should see "Not yet started" in the "#plan_courses #plan_courses_r0 span.completion-notyetstarted" "css_element"
    And I should see "Not yet started" in the "#plan_courses #plan_courses_r1 span.completion-notyetstarted" "css_element"
    And "#plan_courses #plan_courses_r2 span" "css_element" should not exist
    # Run cron to cause reaggregation.
    Then I run the "\core\task\completion_cron_task" task
    # Confirm the status of the courses for user1.
    Then I log out
    And I log in as "user1"
    And I focus on "My Learning" "link"
    And I follow "Record of Learning"
    Then I should see "Complete" in the "#plan_courses #plan_courses_r0 span.completion-complete" "css_element"
    # TL-6593 The next line should show "In progress".
    And I should see "Not yet started" in the "#plan_courses #plan_courses_r1 span.completion-notyetstarted" "css_element"
    And I should see "Complete" in the "#plan_courses #plan_courses_r2 span.completion-complete" "css_element"
    # Confirm the status of the courses for user2.
    Then I log out
    And I log in as "user2"
    And I focus on "My Learning" "link"
    And I follow "Record of Learning"
    Then I should see "Complete" in the "#plan_courses #plan_courses_r0 span.completion-complete" "css_element"
    # TL-6593 The next line should show "In progress".
    And I should see "Not yet started" in the "#plan_courses #plan_courses_r1 span.completion-notyetstarted" "css_element"
    And I should see "Complete" in the "#plan_courses #plan_courses_r2 span.completion-complete" "css_element"
    # Confirm the status of the courses for user3.
    Then I log out
    And I log in as "user3"
    And I focus on "My Learning" "link"
    And I follow "Record of Learning"
    Then I should see "Not yet started" in the "#plan_courses #plan_courses_r0 span.completion-notyetstarted" "css_element"
    # TL-6593 The next line is correct.
    And I should see "Not yet started" in the "#plan_courses #plan_courses_r1 span.completion-notyetstarted" "css_element"
    And I should see "Not yet started" in the "#plan_courses #plan_courses_r2 span.completion-notyetstarted" "css_element"
