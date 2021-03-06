@core @core_badges @_file_upload @javascript
Feature: Award badges
  In order to award badges to users for their achievements
  As an admin
  I need to add criteria to badges in the system

  Background:
    Given the "badges" user profile block exists
    And the "coursedetails" user profile block exists

  Scenario: Award profile badge
    Given I log in as "admin"
    And I navigate to "Manage badges" node in "Site administration > Badges"
    And I click on "Add a new badge" "button"
    And I set the following fields to these values:
      | Name | Profile Badge |
      | Description | Test badge description |
    And I upload "badges/tests/behat/badge.png" file to "Image" filemanager
    And I press "Create badge"
    And I set the field "type" to "Profile completion"
    And I expand all fieldsets
    And I set the field "First name" to "1"
    And I set the field "Email address" to "1"
    And I set the field "Phone" to "1"
    And I set the field "id_description" to "Criterion description"
    When I press "Save"
    Then I should see "Profile completion"
    And I should see "First name"
    And I should see "Email address"
    And I should see "Phone"
    And I should see "Criterion description"
    And I should not see "Criteria for this badge have not been set up yet."
    And I press "Enable access"
    And I press "Continue"
    And I open my profile in edit mode
    And I expand all fieldsets
    And I set the field "Phone" to "123456789"
    And I press "Update profile"
    And I follow "Profile" in the user menu
    Then I should see "Profile Badge"
    And I should not see "There are no badges available."

  Scenario: Award site badge
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher | teacher | 1 | teacher1@example.com |
      | student | student | 1 | student1@example.com |
    And I log in as "admin"
    And I navigate to "Manage badges" node in "Site administration > Badges"
    And I click on "Add a new badge" "button"
    And I set the following fields to these values:
      | Name | Site Badge |
      | Description | Site badge description |
    And I upload "badges/tests/behat/badge.png" file to "Image" filemanager
    And I press "Create badge"
    And I set the field "type" to "Manual issue by role"
    And I set the field "Teacher" to "1"
    And I press "Save"
    And I press "Enable access"
    And I press "Continue"
    And I follow "Recipients (0)"
    And I press "Award badge"
    And I set the field "potentialrecipients[]" to "teacher 1 (teacher1@example.com)"
    And I press "Award badge"
    And I set the field "potentialrecipients[]" to "student 1 (student1@example.com)"
    And I press "Award badge"
    When I follow "Site Badge"
    Then I should see "Recipients (2)"
    And I log out
    And I log in as "student"
    And I follow "Profile" in the user menu
    Then I should see "Site Badge"

  Scenario: Award course badge
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
      | student2 | Student | 2 | student2@example.com |
    And the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
      | student2 | C1 | student |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Add a new badge" node in "Course administration > Badges"
    And I follow "Add a new badge"
    And I set the following fields to these values:
      | Name | Course Badge |
      | Description | Course badge description |
    And I upload "badges/tests/behat/badge.png" file to "Image" filemanager
    And I press "Create badge"
    And I set the field "type" to "Manual issue by role"
    And I set the field "Teacher" to "1"
    And I press "Save"
    And I press "Enable access"
    And I press "Continue"
    And I follow "Recipients (0)"
    And I press "Award badge"
    And I set the field "potentialrecipients[]" to "Student 2 (student2@example.com)"
    And I press "Award badge"
    And I set the field "potentialrecipients[]" to "Student 1 (student1@example.com)"
    When I press "Award badge"
    And I follow "Course Badge"
    Then I should see "Recipients (2)"
    And I log out
    And I log in as "student1"
    And I follow "Profile" in the user menu
    And I click on "Course 1" "link" in the ".block_totara_user_profile_category_coursedetails" "css_element"
    And I should see "Course Badge"
    # Student 1 should have both badges also in the Badges navigation section.
    When I follow "Badges"
    Then I should see "Course Badge"
    And I should not see "Manage badges" in the "#region-main" "css_element"
    And I should not see "Add a new badge"
    And I log out
    # Teacher 1 should have access to manage/create badges in the Badges navigation section.
    When I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Badges"
    Then I should see "Course Badge"
    And I should see "Manage badges" in the "#region-main" "css_element"
    And I should see "Add a new badge"
    # Teacher 1 should NOT have access to manage/create site badges in the Site badges section.
    When I am on homepage
    And I click on "Home" in the totara menu
    And I follow "Go to calendar"
    And I click on "Site badges" "link" in the "Front page" "block"
    Then I should see "There are no badges available."
    And I should not see "Manage badges" in the "#region-main" "css_element"
    And I should not see "Add a new badge"

  Scenario: Award badge on activity completion
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | Frist | teacher1@example.com |
      | student1 | Student | First | student1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Edit settings" node in "Course administration"
    And I set the following fields to these values:
      | Enable completion tracking | Yes |
    And I press "Save and display"
    And I turn editing mode on
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Test assignment name |
      | Description | Submit your online text |
    And I follow "Course 1"
    And I navigate to "Add a new badge" node in "Course administration > Badges"
    And I follow "Add a new badge"
    And I set the following fields to these values:
      | Name | Course Badge |
      | Description | Course badge description |
    And I upload "badges/tests/behat/badge.png" file to "Image" filemanager
    And I press "Create badge"
    And I set the field "type" to "Activity completion"
    And I set the field "Test assignment name" to "1"
    And I press "Save"
    And I press "Enable access"
    When I press "Continue"
    And I log out
    And I log in as "student1"
    And I follow "Profile" in the user menu
    And I click on "Course 1" "link" in the ".block_totara_user_profile_category_coursedetails" "css_element"
    Then I should not see "badges"
    And I am on "Course 1" course homepage
    And I set the field "Manual completion of Test assignment name" to "1"
    And I follow "Profile" in the user menu
    And I click on "Course 1" "link" in the ".block_totara_user_profile_category_coursedetails" "css_element"
    Then I should see "Course Badge"
    And I log out
    # Ensure the badge is still awarded and visible after the course has been deleted.
    When I log in as "admin"
    And I navigate to "Courses and categories" node in "Site administration > Courses"
    And I click on "Miscellaneous" "text" in the ".category-listing" "css_element"
    And I go to the courses management page
    And I click on category "Miscellaneous" in the management interface
    And I click on "delete" action for "Course 1" in management course listing
    And I press "Delete"
    Then I should see "C1 has been completely deleted"
    And I log out
    When I log in as "student1"
    And I follow "Profile" in the user menu
    Then I should see "Course Badge"
    When I follow "Course Badge"
    Then I should see "Course Badge"
    And I should see "Warning: This activity is no longer available."

  Scenario: Award badge on course completion
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | Frist | teacher1@example.com |
      | student1 | Student | First | student1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Edit settings" node in "Course administration"
    And I set the following fields to these values:
      | Enable completion tracking | Yes |
    And I press "Save and display"
    And I turn editing mode on
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Test assignment name |
      | Description | Submit your online text |
      | assignsubmission_onlinetext_enabled | 1 |
    And I navigate to "Course completion" node in "Course administration"
    And I set the field "id_overall_aggregation" to "2"
    And I click on "Condition: Activity completion" "link"
    And I set the field "Assignment - Test assignment name" to "1"
    And I press "Save changes"
    And I follow "Course 1"
    And I navigate to "Add a new badge" node in "Course administration > Badges"
    And I follow "Add a new badge"
    And I set the following fields to these values:
      | Name | Course Badge |
      | Description | Course badge description |
    And I upload "badges/tests/behat/badge.png" file to "Image" filemanager
    And I press "Create badge"
    And I set the field "type" to "Course completion"
    And I set the field with xpath ".//*[contains(., 'Minimum grade required')]/ancestor::*[contains(concat(' ', @class, ' '), ' fitem ')]//input[1]" to "0"
    And I press "Save"
    And I press "Enable access"
    When I press "Continue"
    And I log out
    And I log in as "student1"
    And I follow "Profile" in the user menu
    And I click on "Course 1" "link" in the ".block_totara_user_profile_category_coursedetails" "css_element"
    Then I should not see "badges"
    And I am on "Course 1" course homepage
    And I set the field "Manual completion of Test assignment name" to "1"
    And I log out
    # Completion cron won't mark the whole course completed unless the
    # individual criteria was marked completed more than a second ago. So
    # run it twice, first to mark the criteria and second for the course.
    And I run the scheduled task "core\task\completion_regular_task"
    And I wait "1" seconds
    And I run the scheduled task "core\task\completion_regular_task"
    # The student should now see their badge.
    And I log in as "student1"
    And I follow "Profile" in the user menu
    Then I should see "Course Badge"
    And I log out
    # Ensure the badge is still awarded and visible after the course has been deleted.
    When I log in as "admin"
    And I navigate to "Courses and categories" node in "Site administration > Courses"
    And I click on "Miscellaneous" "text" in the ".category-listing" "css_element"
    And I go to the courses management page
    And I click on category "Miscellaneous" in the management interface
    And I click on "delete" action for "Course 1" in management course listing
    And I press "Delete"
    Then I should see "C1 has been completely deleted"
    And I log out
    When I log in as "student1"
    And I follow "Profile" in the user menu
    Then I should see "Course Badge"
    When I follow "Course Badge"
    Then I should see "Course Badge"
    And I should see "Warning: This course is no longer available."

  Scenario: All of the selected roles can award badges
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
      | student2 | Student | 2 | student2@example.com |
    And the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
      | student2 | C1 | student |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    # Create course badge 1.
    And I navigate to "Add a new badge" node in "Course administration > Badges"
    And I follow "Add a new badge"
    And I set the following fields to these values:
      | Name | Course Badge 1 |
      | Description | Course badge description |
    And I upload "badges/tests/behat/badge.png" file to "Image" filemanager
    And I press "Create badge"
    And I set the field "type" to "Manual issue by role"
    And I expand all fieldsets
    # Set to ANY of the roles awards badge.
    And I set the field "Teacher" to "1"
    And I set the field "Any of the selected roles awards the badge" to "1"
    And I press "Save"
    And I press "Enable access"
    And I press "Continue"
    And I follow "Recipients (0)"
    And I press "Award badge"
    # Award course badge 1 to student 1.
    And I set the field "potentialrecipients[]" to "Student 1 (student1@example.com)"
    When I press "Award badge"
    And I follow "Course Badge 1"
    And I follow "Recipients (1)"
    Then I should see "Recipients (1)"
    # Add course badge 2.
    And I navigate to "Add a new badge" node in "Course administration > Badges"
    And I follow "Add a new badge"
    And I set the following fields to these values:
      | Name | Course Badge 2 |
      | Description | Course badge description |
    And I upload "badges/tests/behat/badge.png" file to "Image" filemanager
    And I press "Create badge"
    And I set the field "type" to "Manual issue by role"
    And I expand all fieldsets
    # Set to ALL of the selected roles award badge.
    And I set the field "Teacher" to "1"
    And I set the field "All of the selected roles award the badge" to "1"
    And I press "Save"
    And I press "Enable access"
    And I press "Continue"
    And I follow "Recipients (0)"
    And I press "Award badge"
    # Award course badge 2 to student 2.
    And I set the field "potentialrecipients[]" to "Student 2 (student2@example.com)"
    When I press "Award badge"
    And I follow "Course Badge 2"
    And I follow "Recipients (1)"
    Then I should see "Recipients (1)"
    And I log out
    And I trigger cron
    # Student 1 should have just course badge 1.
    And I log in as "student1"
    And I follow "Profile" in the user menu
    When I click on "Course 1" "link" in the ".block_totara_user_profile_category_coursedetails" "css_element"
    Then I should see "Course Badge 1"
    And I should not see "Course Badge 2"
    And I log out
    # Student 2 should have just course badge 2.
    And I log in as "student2"
    And I follow "Profile" in the user menu
    When I click on "Course 1" "link" in the ".block_totara_user_profile_category_coursedetails" "css_element"
    Then I should see "Course Badge 2"
    Then I should not see "Course Badge 1"

  Scenario: Revoke badge
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
      | student2 | Student | 2 | student2@example.com |
    And the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
      | student2 | C1 | student |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Add a new badge" node in "Course administration > Badges"
    And I follow "Add a new badge"
    And I set the following fields to these values:
      | Name | Course Badge |
      | Description | Course badge description |
    And I upload "badges/tests/behat/badge.png" file to "Image" filemanager
    And I press "Create badge"
    And I set the field "type" to "Manual issue by role"
    And I set the field "Teacher" to "1"
    And I press "Save"
    And I press "Enable access"
    And I press "Continue"
    And I follow "Recipients (0)"
    And I press "Award badge"
    And I set the field "potentialrecipients[]" to "Student 2 (student2@example.com)"
    And I press "Award badge"
    And I set the field "potentialrecipients[]" to "Student 1 (student1@example.com)"
    When I press "Award badge"
    And I follow "Course Badge"
    Then I should see "Recipients (2)"
    And I follow "Recipients (2)"
    And I press "Award badge"
    And I set the field "existingrecipients[]" to "Student 2 (student2@example.com)"
    And I press "Revoke badge"
    And I set the field "existingrecipients[]" to "Student 1 (student1@example.com)"
    When I press "Revoke badge"
    And I follow "Course Badge"
    Then I should see "Recipients (0)"

  @weka @vue
  Scenario: Check badge notifications
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | One      | teacher1@example.com |
      | student  | Student   | One      | student1@example.com |
    And I log in as "admin"
    And I follow "Preferences" in the user menu
    And I follow "Editor preferences"
    And I set the field "Text editor" to "Weka editor"
    And I press "Save changes"
    And I navigate to "Manage badges" node in "Site administration > Badges"
    And I click on "Add a new badge" "button"
    And I set the following fields to these values:
      | Name        | Site Badge             |
      | Description | Site badge description |
    And I upload "badges/tests/behat/badge.png" file to "Image" filemanager
    And I press "Create badge"
    And I set the field "type" to "Manual issue by role"
    And I set the field "Teacher" to "1"
    And I press "Save"
    And I click on "Message" "link" in the "#region-main" "css_element"
    And I activate the weka editor with css "#uid-1"
    And I select the text "been awarded" in the weka editor
    And I replace the selection with "earned" in the weka editor
    And I press "Save changes"
    And I press "Enable access"
    And I press "Continue"
    And I follow "Recipients (0)"
    And I press "Award badge"
    And I set the field "potentialrecipients[]" to "Student One (student1@example.com)"
    And I press "Award badge"
    When I follow "Site Badge"
    Then I should see "Recipients (1)"
    And I log out
    And I log in as "student"
    And I open the notification popover
    Then I should see "Congratulations!"
    And I follow "View full notification"
    And I should see "You have earned the badge"
    And I should not see "{\"type\":\"doc\""
    And I should not see "paragraph"