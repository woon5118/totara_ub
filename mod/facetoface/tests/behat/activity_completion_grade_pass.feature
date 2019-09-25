@mod @mod_facetoface @core_grades @javascript
Feature: Seminar activity completion with passing grade
  As an admin/course creator/editing trainer
  I would like to set the the activity completion criteria to have the option to set "require passing grade" like quizzes
  So that there is flexibility within activity completion criteria

  Background:
    Given the following config values are set as admin:
      | grade_decimalpoints | 2 |
    And the following "courses" exist:
      | fullname | shortname | category | enablecompletion |
      | course1  | course1   | 0        | 1                |
    And the following "users" exist:
      | username | firstname | lastname | email             |
      | user1    | One       | Uno      | user1@example.com |
      | user2    | Two       | Duex     | user2@example.com |
      | user3    | Three     | Toru     | user3@example.com |
      | user4    | Four      | Wha      | user4@example.com |
      | user5    | Five      | Cinq     | user5@example.com |
    And the following "course enrolments" exist:
     | user     | course   | role    |
     | user1    | course1  | student |
     | user2    | course1  | student |
     | user3    | course1  | student |
     | user4    | course1  | student |
     | user5    | course1  | student |
    And the following "seminars" exist in "mod_facetoface" plugin:
      | name      | course  |
      | seminar 1 | course1 |
    And the following "seminar events" exist in "mod_facetoface" plugin:
      | facetoface | details |
      | seminar 1  | event 1 |
    And the following "seminar sessions" exist in "mod_facetoface" plugin:
      | eventdetails | start                | finish               |
      | event 1      | 01-Feb-2003 12:00:00 | 01-Feb-2003 13:00:00 |
    And the following "seminar signups" exist in "mod_facetoface" plugin:
      | user  | eventdetails |
      | user1 | event 1      |
      | user2 | event 1      |
      | user3 | event 1      |
      | user4 | event 1      |
      | user5 | event 1      |

    And I log in as "admin"

###################################################################################################

  Scenario: Manual event grading - passing grade is empty
    Given I am on "course1" course homepage
    And I follow "seminar 1"
    And I navigate to "Edit settings" node in "Seminar administration"
    And I set the field "Passing grade" to ""
    When I click on "Save and return to course" "button"
    Then I should see "You must enter a number here."

  Scenario: Manual event grading - Passing grade is '-1'
    Given I am on "course1" course homepage
    And I follow "seminar 1"
    And I navigate to "Edit settings" node in "Seminar administration"
    And I set the field "Passing grade" to "-1"
    When I click on "Save and return to course" "button"
    Then I should see "Passing grade must be between 0 and 100."

  Scenario: Manual event grading - Passing grade is '200'
    Given I am on "course1" course homepage
    And I follow "seminar 1"
    And I navigate to "Edit settings" node in "Seminar administration"
    And I set the field "Passing grade" to "200"
    When I click on "Save and return to course" "button"
    Then I should see "Passing grade must be between 0 and 100."

  Scenario: Manual event grading - Passing grade is 'three'
    Given I am on "course1" course homepage
    And I follow "seminar 1"
    And I navigate to "Edit settings" node in "Seminar administration"
    And I set the field "Passing grade" to "three"
    When I click on "Save and return to course" "button"
    Then I should see "You must enter a number here."

  Scenario: Manual event grading - Passing grade is '4.2195e+1'
    Given I am on "course1" course homepage
    And I follow "seminar 1"
    And I navigate to "Edit settings" node in "Seminar administration"
    And I set the field "Passing grade" to "4.2195e+1"
    When I click on "Save and return to course" "button"
    Then I should see "seminar 1" in the ".modtype_facetoface" "css_element"
    When I follow "seminar 1"
    And I navigate to "Edit settings" node in "Seminar administration"
    Then the field "Passing grade" matches value "42.20"

  Scenario: Manual event grading - require grade is no
    Given I am on "course1" course homepage
    And I follow "seminar 1"
    And I navigate to "Edit settings" node in "Seminar administration"
    And I set the following fields to these values:
      | Manual event grading | 1  |
      | Passing grade        | 42 |
      | Completion tracking  | Show activity as complete when conditions are met |
      | Require grade        | No                                                |
      | completionstatusrequired[90]  | 0 |
      | completionstatusrequired[100] | 0 |
    When I click on "Save and return to course" "button"
    Then I should see "When you select automatic completion, you must also enable at least one requirement (below)."

  Scenario: Auto event grading - require grade is no
    Given I am on "course1" course homepage
    And I follow "seminar 1"
    And I navigate to "Edit settings" node in "Seminar administration"
    And I expand all fieldsets
    And I set the following fields to these values:
      | Manual event grading | 0  |
      | Passing grade        | 42 |
      | Completion tracking  | Show activity as complete when conditions are met |
      | Require grade        | No                                                |
      | completionstatusrequired[90]  | 0 |
      | completionstatusrequired[100] | 0 |
    When I click on "Save and return to course" "button"
    Then I should see "When you select automatic completion, you must also enable at least one requirement (below)."

###################################################################################################

  Scenario: Manual event grading - require grade is any, passing grade is 0
    Given I am on "course1" course homepage
    And I follow "seminar 1"
    And I navigate to "Edit settings" node in "Seminar administration"
    And I set the following fields to these values:
      | Manual event grading | 1 |
      | Passing grade        | 0 |
      | Completion tracking  | Show activity as complete when conditions are met |
      | Require grade        | Yes, any grade (0–100)                            |
      | completionstatusrequired[90]  | 0 |
      | completionstatusrequired[100] | 0 |
    When I click on "Save and return to course" "button"
    Then I should not see "When you select automatic completion, you must also enable at least one requirement (below)."

    And I expand "Reports" node
    And I follow "Activity completion"
    Then I should see "One Uno, seminar 1: Not completed" in the "One Uno" "table_row"
    And I should see "Two Duex, seminar 1: Not completed" in the "Two Duex" "table_row"
    And I should see "Three Toru, seminar 1: Not completed" in the "Three Toru" "table_row"
    And I should see "Four Wha, seminar 1: Not completed" in the "Four Wha" "table_row"
    And I should see "Five Cinq, seminar 1: Not completed" in the "Five Cinq" "table_row"

    And I am on "course1" course homepage
    And I follow "seminar 1"
    And I follow "Attendee"
    And I follow "Take attendance"

    And I set the field "One Uno's attendance" to "Fully attended"
    And I set the field "Two Duex's attendance" to "Fully attended"
    And I set the field "Three Toru's attendance" to "Partially attended"
    And I set the field "Four Wha's attendance" to "No show"
    And I set the field "Five Cinq's attendance" to "Not set"

    And I set the field "One Uno's event grade" to ""
    And I set the field "Two Duex's event grade" to "0"
    And I set the field "Three Toru's event grade" to "25"
    And I set the field "Four Wha's event grade" to "50"
    And I set the field "Five Cinq's event grade" to "100"

    When I click on "Save attendance" "button"
    Then I should see "Successfully updated attendance" in the ".alert-success" "css_element"

    When I am on "course1" course homepage
    And I expand "Reports" node
    And I follow "Activity completion"
    # Include a day name to distinguish "Completed", "Completed (passed)" and "Completed (failed)"
    Then I should see "One Uno, seminar 1: Not completed" in the "One Uno" "table_row"
    And I should see "Two Duex, seminar 1: Completed Saturday" in the "Two Duex" "table_row"
    And I should see "Three Toru, seminar 1: Completed Saturday" in the "Three Toru" "table_row"
    And I should see "Four Wha, seminar 1: Completed Saturday" in the "Four Wha" "table_row"
    And I should see "Five Cinq, seminar 1: Completed Saturday" in the "Five Cinq" "table_row"

    # Re-take attendance
    When I am on "course1" course homepage
    And I follow "seminar 1"
    And I follow "Attendee"
    And I follow "Take attendance"
    And I set the field "One Uno's event grade" to "100"
    And I set the field "Two Duex's event grade" to ""
    And I set the field "Three Toru's event grade" to "50"
    And I set the field "Four Wha's event grade" to ""
    And I set the field "Five Cinq's event grade" to "0"

    When I click on "Save attendance" "button"
    Then I should see "Successfully updated attendance" in the ".alert-success" "css_element"

    When I am on "course1" course homepage
    And I expand "Reports" node
    And I follow "Activity completion"
    # Include a day name to distinguish "Completed", "Completed (passed)" and "Completed (failed)"
    Then I should see "One Uno, seminar 1: Completed Saturday" in the "One Uno" "table_row"
    And I should see "Two Duex, seminar 1: Not completed" in the "Two Duex" "table_row"
    And I should see "Three Toru, seminar 1: Completed Saturday" in the "Three Toru" "table_row"
    And I should see "Four Wha, seminar 1: Not completed" in the "Four Wha" "table_row"
    And I should see "Five Cinq, seminar 1: Completed Saturday" in the "Five Cinq" "table_row"

  Scenario: Manual event grading - require grade is any, passing grade is 42
    Given I am on "course1" course homepage
    And I follow "seminar 1"
    And I navigate to "Edit settings" node in "Seminar administration"
    And I set the following fields to these values:
      | Manual event grading | 1  |
      | Passing grade        | 42 |
      | Completion tracking  | Show activity as complete when conditions are met |
      | Require grade        | Yes, any grade (0–100)                            |
      | completionstatusrequired[90]  | 0 |
      | completionstatusrequired[100] | 0 |
    When I click on "Save and return to course" "button"
    Then I should not see "When you select automatic completion, you must also enable at least one requirement (below)."

    And I expand "Reports" node
    And I follow "Activity completion"
    Then I should see "One Uno, seminar 1: Not completed" in the "One Uno" "table_row"
    And I should see "Two Duex, seminar 1: Not completed" in the "Two Duex" "table_row"
    And I should see "Three Toru, seminar 1: Not completed" in the "Three Toru" "table_row"
    And I should see "Four Wha, seminar 1: Not completed" in the "Four Wha" "table_row"
    And I should see "Five Cinq, seminar 1: Not completed" in the "Five Cinq" "table_row"

    And I am on "course1" course homepage
    And I follow "seminar 1"
    And I follow "Attendee"
    And I follow "Take attendance"

    And I set the field "One Uno's attendance" to "Fully attended"
    And I set the field "Two Duex's attendance" to "Fully attended"
    And I set the field "Three Toru's attendance" to "Partially attended"
    And I set the field "Four Wha's attendance" to "No show"
    And I set the field "Five Cinq's attendance" to "Not set"

    And I set the field "One Uno's event grade" to ""
    And I set the field "Two Duex's event grade" to "0"
    And I set the field "Three Toru's event grade" to "25"
    And I set the field "Four Wha's event grade" to "50"
    And I set the field "Five Cinq's event grade" to "100"

    When I click on "Save attendance" "button"
    Then I should see "Successfully updated attendance" in the ".alert-success" "css_element"

    When I am on "course1" course homepage
    And I expand "Reports" node
    And I follow "Activity completion"
    # Include a day name to distinguish "Completed", "Completed (passed)" and "Completed (failed)"
    Then I should see "One Uno, seminar 1: Not completed" in the "One Uno" "table_row"
    And I should see "Two Duex, seminar 1: Completed (did not achieve pass grade) Saturday" in the "Two Duex" "table_row"
    And I should see "Three Toru, seminar 1: Completed (did not achieve pass grade) Saturday" in the "Three Toru" "table_row"
    And I should see "Four Wha, seminar 1: Completed (achieved pass grade) Saturday" in the "Four Wha" "table_row"
    And I should see "Five Cinq, seminar 1: Completed (achieved pass grade) Saturday" in the "Five Cinq" "table_row"

    # Re-take attendance
    When I am on "course1" course homepage
    And I follow "seminar 1"
    And I follow "Attendee"
    And I follow "Take attendance"
    And I set the field "One Uno's event grade" to "100"
    And I set the field "Two Duex's event grade" to ""
    And I set the field "Three Toru's event grade" to "50"
    And I set the field "Four Wha's event grade" to ""
    And I set the field "Five Cinq's event grade" to "0"

    When I click on "Save attendance" "button"
    Then I should see "Successfully updated attendance" in the ".alert-success" "css_element"

    When I am on "course1" course homepage
    And I expand "Reports" node
    And I follow "Activity completion"
    # Include a day name to distinguish "Completed", "Completed (passed)" and "Completed (failed)"
    Then I should see "One Uno, seminar 1: Completed (achieved pass grade) Saturday" in the "One Uno" "table_row"
    And I should see "Two Duex, seminar 1: Not completed" in the "Two Duex" "table_row"
    And I should see "Three Toru, seminar 1: Completed (achieved pass grade) Saturday" in the "Three Toru" "table_row"
    And I should see "Four Wha, seminar 1: Not completed" in the "Four Wha" "table_row"
    And I should see "Five Cinq, seminar 1: Completed (did not achieve pass grade) Saturday" in the "Five Cinq" "table_row"

  Scenario: Auto event grading - require grade is any, passing grade is 0
    Given I am on "course1" course homepage
    And I follow "seminar 1"
    And I navigate to "Edit settings" node in "Seminar administration"
    And I set the following fields to these values:
      | Manual event grading | 0 |
      | Passing grade        | 0 |
      | Completion tracking  | Show activity as complete when conditions are met |
      | Require grade        | Yes, any grade (0–100)                            |
      | completionstatusrequired[90]  | 0 |
      | completionstatusrequired[100] | 0 |
    When I click on "Save and return to course" "button"
    Then I should not see "When you select automatic completion, you must also enable at least one requirement (below)."

    And I expand "Reports" node
    And I follow "Activity completion"
    Then I should see "One Uno, seminar 1: Not completed" in the "One Uno" "table_row"
    And I should see "Two Duex, seminar 1: Not completed" in the "Two Duex" "table_row"
    And I should see "Three Toru, seminar 1: Not completed" in the "Three Toru" "table_row"
    And I should see "Four Wha, seminar 1: Not completed" in the "Four Wha" "table_row"
    And I should see "Five Cinq, seminar 1: Not completed" in the "Five Cinq" "table_row"

    And I am on "course1" course homepage
    And I follow "seminar 1"
    And I follow "Attendee"
    And I follow "Take attendance"

    And I set the field "One Uno's attendance" to "Not set"
    And I set the field "Two Duex's attendance" to "No show"
    And I set the field "Three Toru's attendance" to "Unable to attend"
    And I set the field "Four Wha's attendance" to "Partially attended"
    And I set the field "Five Cinq's attendance" to "Fully attended"

    When I click on "Save attendance" "button"
    Then I should see "Successfully updated attendance" in the ".alert-success" "css_element"

    When I am on "course1" course homepage
    And I expand "Reports" node
    And I follow "Activity completion"
    # Include a day name to distinguish "Completed", "Completed (passed)" and "Completed (failed)"
    Then I should see "One Uno, seminar 1: Not completed" in the "One Uno" "table_row"
    And I should see "Two Duex, seminar 1: Completed Saturday" in the "Two Duex" "table_row"
    And I should see "Three Toru, seminar 1: Completed Saturday" in the "Three Toru" "table_row"
    And I should see "Four Wha, seminar 1: Completed Saturday" in the "Four Wha" "table_row"
    And I should see "Five Cinq, seminar 1: Completed Saturday" in the "Five Cinq" "table_row"

    # Re-take attendance
    When I am on "course1" course homepage
    And I follow "seminar 1"
    And I follow "Attendee"
    And I follow "Take attendance"

    And I set the field "One Uno's attendance" to "No show"
    And I set the field "Two Duex's attendance" to "Not set"
    And I set the field "Three Toru's attendance" to "Not set"
    And I set the field "Four Wha's attendance" to "Not set"
    And I set the field "Five Cinq's attendance" to "Not set"

    When I click on "Save attendance" "button"
    Then I should see "Successfully updated attendance" in the ".alert-success" "css_element"

    When I am on "course1" course homepage
    And I expand "Reports" node
    And I follow "Activity completion"
    # Include a day name to distinguish "Completed", "Completed (passed)" and "Completed (failed)"
    Then I should see "One Uno, seminar 1: Completed Saturday" in the "One Uno" "table_row"
    And I should see "Two Duex, seminar 1: Not completed" in the "Two Duex" "table_row"
    And I should see "Three Toru, seminar 1: Not completed" in the "Three Toru" "table_row"
    And I should see "Four Wha, seminar 1: Not completed" in the "Four Wha" "table_row"
    And I should see "Five Cinq, seminar 1: Not completed" in the "Five Cinq" "table_row"

  Scenario: Auto event grading - require grade is any, passing grade is 42
    Given I am on "course1" course homepage
    And I follow "seminar 1"
    And I navigate to "Edit settings" node in "Seminar administration"
    And I set the following fields to these values:
      | Manual event grading | 0  |
      | Passing grade        | 42 |
      | Completion tracking  | Show activity as complete when conditions are met |
      | Require grade        | Yes, any grade (0–100)                            |
      | completionstatusrequired[90]  | 0 |
      | completionstatusrequired[100] | 0 |
    When I click on "Save and return to course" "button"
    Then I should not see "When you select automatic completion, you must also enable at least one requirement (below)."

    And I expand "Reports" node
    And I follow "Activity completion"
    Then I should see "One Uno, seminar 1: Not completed" in the "One Uno" "table_row"
    And I should see "Two Duex, seminar 1: Not completed" in the "Two Duex" "table_row"
    And I should see "Three Toru, seminar 1: Not completed" in the "Three Toru" "table_row"
    And I should see "Four Wha, seminar 1: Not completed" in the "Four Wha" "table_row"
    And I should see "Five Cinq, seminar 1: Not completed" in the "Five Cinq" "table_row"

    And I am on "course1" course homepage
    And I follow "seminar 1"
    And I follow "Attendee"
    And I follow "Take attendance"

    And I set the field "One Uno's attendance" to "Not set"
    And I set the field "Two Duex's attendance" to "No show"
    And I set the field "Three Toru's attendance" to "Unable to attend"
    And I set the field "Four Wha's attendance" to "Partially attended"
    And I set the field "Five Cinq's attendance" to "Fully attended"

    When I click on "Save attendance" "button"
    Then I should see "Successfully updated attendance" in the ".alert-success" "css_element"

    When I am on "course1" course homepage
    And I expand "Reports" node
    And I follow "Activity completion"
    # Include a day name to distinguish "Completed", "Completed (passed)" and "Completed (failed)"
    Then I should see "One Uno, seminar 1: Not completed" in the "One Uno" "table_row"
    And I should see "Two Duex, seminar 1: Completed (did not achieve pass grade) Saturday" in the "Two Duex" "table_row"
    And I should see "Three Toru, seminar 1: Completed (did not achieve pass grade) Saturday" in the "Three Toru" "table_row"
    And I should see "Four Wha, seminar 1: Completed (achieved pass grade) Saturday" in the "Four Wha" "table_row"
    And I should see "Five Cinq, seminar 1: Completed (achieved pass grade) Saturday" in the "Five Cinq" "table_row"

    # Re-take attendance
    When I am on "course1" course homepage
    And I follow "seminar 1"
    And I follow "Attendee"
    And I follow "Take attendance"

    And I set the field "One Uno's attendance" to "No show"
    And I set the field "Two Duex's attendance" to "Not set"
    And I set the field "Three Toru's attendance" to "Not set"
    And I set the field "Four Wha's attendance" to "Not set"
    And I set the field "Five Cinq's attendance" to "Not set"

    When I click on "Save attendance" "button"
    Then I should see "Successfully updated attendance" in the ".alert-success" "css_element"

    When I am on "course1" course homepage
    And I expand "Reports" node
    And I follow "Activity completion"
    # Include a day name to distinguish "Completed", "Completed (passed)" and "Completed (failed)"
    Then I should see "One Uno, seminar 1: Completed (did not achieve pass grade) Saturday" in the "One Uno" "table_row"
    And I should see "Two Duex, seminar 1: Not completed" in the "Two Duex" "table_row"
    And I should see "Three Toru, seminar 1: Not completed" in the "Three Toru" "table_row"
    And I should see "Four Wha, seminar 1: Not completed" in the "Four Wha" "table_row"
    And I should see "Five Cinq, seminar 1: Not completed" in the "Five Cinq" "table_row"

###################################################################################################

  Scenario: Manual event grading - require grade is pass, passing grade is 0
    Given I am on "course1" course homepage
    And I follow "seminar 1"
    And I navigate to "Edit settings" node in "Seminar administration"
    And I set the following fields to these values:
      | Manual event grading | 1 |
      | Passing grade        | 0 |
      | Completion tracking  | Show activity as complete when conditions are met |
      | Require grade        | Yes, passing grade                                |
      | completionstatusrequired[90]  | 0 |
      | completionstatusrequired[100] | 0 |
    When I click on "Save and return to course" "button"
    Then I should not see "When you select automatic completion, you must also enable at least one requirement (below)."

    And I expand "Reports" node
    And I follow "Activity completion"
    Then I should see "One Uno, seminar 1: Not completed" in the "One Uno" "table_row"
    And I should see "Two Duex, seminar 1: Not completed" in the "Two Duex" "table_row"
    And I should see "Three Toru, seminar 1: Not completed" in the "Three Toru" "table_row"
    And I should see "Four Wha, seminar 1: Not completed" in the "Four Wha" "table_row"
    And I should see "Five Cinq, seminar 1: Not completed" in the "Five Cinq" "table_row"

    And I am on "course1" course homepage
    And I follow "seminar 1"
    And I follow "Attendee"
    And I follow "Take attendance"

    And I set the field "One Uno's attendance" to "Fully attended"
    And I set the field "Two Duex's attendance" to "Fully attended"
    And I set the field "Three Toru's attendance" to "Partially attended"
    And I set the field "Four Wha's attendance" to "No show"
    And I set the field "Five Cinq's attendance" to "Not set"

    And I set the field "One Uno's event grade" to ""
    And I set the field "Two Duex's event grade" to "0"
    And I set the field "Three Toru's event grade" to "25"
    And I set the field "Four Wha's event grade" to "50"
    And I set the field "Five Cinq's event grade" to "100"

    When I click on "Save attendance" "button"
    Then I should see "Successfully updated attendance" in the ".alert-success" "css_element"

    When I am on "course1" course homepage
    And I expand "Reports" node
    And I follow "Activity completion"
    # Include a day name to distinguish "Completed", "Completed (passed)" and "Completed (failed)"
    Then I should see "One Uno, seminar 1: Not completed" in the "One Uno" "table_row"
    And I should see "Two Duex, seminar 1: Completed Saturday" in the "Two Duex" "table_row"
    And I should see "Three Toru, seminar 1: Completed Saturday" in the "Three Toru" "table_row"
    And I should see "Four Wha, seminar 1: Completed Saturday" in the "Four Wha" "table_row"
    And I should see "Five Cinq, seminar 1: Completed Saturday" in the "Five Cinq" "table_row"

    # Re-take attendance
    When I am on "course1" course homepage
    And I follow "seminar 1"
    And I follow "Attendee"
    And I follow "Take attendance"
    And I set the field "One Uno's event grade" to "55"
    And I set the field "Two Duex's event grade" to ""
    And I set the field "Three Toru's event grade" to "0"
    And I set the field "Four Wha's event grade" to "22"
    And I set the field "Five Cinq's event grade" to ""

    When I click on "Save attendance" "button"
    Then I should see "Successfully updated attendance" in the ".alert-success" "css_element"

    When I am on "course1" course homepage
    And I expand "Reports" node
    And I follow "Activity completion"
    # Include a day name to distinguish "Completed", "Completed (passed)" and "Completed (failed)"
    Then I should see "One Uno, seminar 1: Completed Saturday" in the "One Uno" "table_row"
    And I should see "Two Duex, seminar 1: Not completed" in the "Two Duex" "table_row"
    And I should see "Three Toru, seminar 1: Completed Saturday" in the "Three Toru" "table_row"
    And I should see "Four Wha, seminar 1: Completed Saturday" in the "Four Wha" "table_row"
    And I should see "Five Cinq, seminar 1: Not completed" in the "Five Cinq" "table_row"

  Scenario: Manual event grading - require grade is pass, passing grade is 42
    Given I am on "course1" course homepage
    And I follow "seminar 1"
    And I navigate to "Edit settings" node in "Seminar administration"
    And I set the following fields to these values:
      | Manual event grading | 1  |
      | Passing grade        | 42 |
      | Completion tracking  | Show activity as complete when conditions are met |
      | Require grade        | Yes, passing grade                                |
      | completionstatusrequired[90]  | 0 |
      | completionstatusrequired[100] | 0 |
    When I click on "Save and return to course" "button"
    Then I should not see "When you select automatic completion, you must also enable at least one requirement (below)."

    And I expand "Reports" node
    And I follow "Activity completion"
    Then I should see "One Uno, seminar 1: Not completed" in the "One Uno" "table_row"
    And I should see "Two Duex, seminar 1: Not completed" in the "Two Duex" "table_row"
    And I should see "Three Toru, seminar 1: Not completed" in the "Three Toru" "table_row"
    And I should see "Four Wha, seminar 1: Not completed" in the "Four Wha" "table_row"
    And I should see "Five Cinq, seminar 1: Not completed" in the "Five Cinq" "table_row"

    And I am on "course1" course homepage
    And I follow "seminar 1"
    And I follow "Attendee"
    And I follow "Take attendance"

    And I set the field "One Uno's attendance" to "Fully attended"
    And I set the field "Two Duex's attendance" to "Fully attended"
    And I set the field "Three Toru's attendance" to "Partially attended"
    And I set the field "Four Wha's attendance" to "No show"
    And I set the field "Five Cinq's attendance" to "Not set"

    And I set the field "One Uno's event grade" to ""
    And I set the field "Two Duex's event grade" to "0"
    And I set the field "Three Toru's event grade" to "25"
    And I set the field "Four Wha's event grade" to "50"
    And I set the field "Five Cinq's event grade" to "100"

    When I click on "Save attendance" "button"
    Then I should see "Successfully updated attendance" in the ".alert-success" "css_element"

    When I am on "course1" course homepage
    And I expand "Reports" node
    And I follow "Activity completion"
    # Include a day name to distinguish "Completed", "Completed (passed)" and "Completed (failed)"
    Then I should see "One Uno, seminar 1: Not completed" in the "One Uno" "table_row"
    And I should see "Two Duex, seminar 1: Not completed" in the "Two Duex" "table_row"
    And I should see "Three Toru, seminar 1: Not completed" in the "Three Toru" "table_row"
    And I should see "Four Wha, seminar 1: Completed (achieved pass grade) Saturday" in the "Four Wha" "table_row"
    And I should see "Five Cinq, seminar 1: Completed (achieved pass grade) Saturday" in the "Five Cinq" "table_row"

    # Re-take attendance
    When I am on "course1" course homepage
    And I follow "seminar 1"
    And I follow "Attendee"
    And I follow "Take attendance"
    And I set the field "One Uno's event grade" to "55"
    And I set the field "Two Duex's event grade" to ""
    And I set the field "Three Toru's event grade" to "0"
    And I set the field "Four Wha's event grade" to "22"
    And I set the field "Five Cinq's event grade" to ""

    When I click on "Save attendance" "button"
    Then I should see "Successfully updated attendance" in the ".alert-success" "css_element"

    When I am on "course1" course homepage
    And I expand "Reports" node
    And I follow "Activity completion"
    # Include a day name to distinguish "Completed", "Completed (passed)" and "Completed (failed)"
    Then I should see "One Uno, seminar 1: Completed (achieved pass grade) Saturday" in the "One Uno" "table_row"
    And I should see "Two Duex, seminar 1: Not completed" in the "Two Duex" "table_row"
    And I should see "Three Toru, seminar 1: Not completed" in the "Three Toru" "table_row"
    And I should see "Four Wha, seminar 1: Not completed" in the "Four Wha" "table_row"
    And I should see "Five Cinq, seminar 1: Not completed" in the "Five Cinq" "table_row"

  Scenario: Auto event grading - require grade is pass, passing grade is 0
    Given I am on "course1" course homepage
    And I follow "seminar 1"
    And I navigate to "Edit settings" node in "Seminar administration"
    And I set the following fields to these values:
      | Manual event grading | 0 |
      | Passing grade        | 0 |
      | Completion tracking  | Show activity as complete when conditions are met |
      | Require grade        | Yes, passing grade                                |
      | completionstatusrequired[90]  | 0 |
      | completionstatusrequired[100] | 0 |
    When I click on "Save and return to course" "button"
    Then I should not see "When you select automatic completion, you must also enable at least one requirement (below)."

    And I expand "Reports" node
    And I follow "Activity completion"
    Then I should see "One Uno, seminar 1: Not completed" in the "One Uno" "table_row"
    And I should see "Two Duex, seminar 1: Not completed" in the "Two Duex" "table_row"
    And I should see "Three Toru, seminar 1: Not completed" in the "Three Toru" "table_row"
    And I should see "Four Wha, seminar 1: Not completed" in the "Four Wha" "table_row"
    And I should see "Five Cinq, seminar 1: Not completed" in the "Five Cinq" "table_row"

    And I am on "course1" course homepage
    And I follow "seminar 1"
    And I follow "Attendee"
    And I follow "Take attendance"

    And I set the field "One Uno's attendance" to "Not set"
    And I set the field "Two Duex's attendance" to "No show"
    And I set the field "Three Toru's attendance" to "Unable to attend"
    And I set the field "Four Wha's attendance" to "Partially attended"
    And I set the field "Five Cinq's attendance" to "Fully attended"

    When I click on "Save attendance" "button"
    Then I should see "Successfully updated attendance" in the ".alert-success" "css_element"

    When I am on "course1" course homepage
    And I expand "Reports" node
    And I follow "Activity completion"
    # Include a day name to distinguish "Completed", "Completed (passed)" and "Completed (failed)"
    Then I should see "One Uno, seminar 1: Not completed" in the "One Uno" "table_row"
    And I should see "Two Duex, seminar 1: Completed Saturday" in the "Two Duex" "table_row"
    And I should see "Three Toru, seminar 1: Completed Saturday" in the "Three Toru" "table_row"
    And I should see "Four Wha, seminar 1: Completed Saturday" in the "Four Wha" "table_row"
    And I should see "Five Cinq, seminar 1: Completed Saturday" in the "Five Cinq" "table_row"

    # Re-take attendance
    When I am on "course1" course homepage
    And I follow "seminar 1"
    And I follow "Attendee"
    And I follow "Take attendance"

    And I set the field "One Uno's attendance" to "Partially attended"
    And I set the field "Two Duex's attendance" to "Not set"
    And I set the field "Three Toru's attendance" to "Not set"
    And I set the field "Four Wha's attendance" to "No show"
    And I set the field "Five Cinq's attendance" to "Not set"

    When I click on "Save attendance" "button"
    Then I should see "Successfully updated attendance" in the ".alert-success" "css_element"

    When I am on "course1" course homepage
    And I expand "Reports" node
    And I follow "Activity completion"
    # Include a day name to distinguish "Completed", "Completed (passed)" and "Completed (failed)"
    Then I should see "One Uno, seminar 1: Completed Saturday" in the "One Uno" "table_row"
    And I should see "Two Duex, seminar 1: Not completed" in the "Two Duex" "table_row"
    And I should see "Three Toru, seminar 1: Not completed" in the "Three Toru" "table_row"
    And I should see "Four Wha, seminar 1: Completed Saturday" in the "Four Wha" "table_row"
    And I should see "Five Cinq, seminar 1: Not completed" in the "Five Cinq" "table_row"

  Scenario: Auto event grading - require grade is pass, passing grade is 42
    Given I am on "course1" course homepage
    And I follow "seminar 1"
    And I navigate to "Edit settings" node in "Seminar administration"
    And I set the following fields to these values:
      | Manual event grading | 0  |
      | Passing grade        | 42 |
      | Completion tracking  | Show activity as complete when conditions are met |
      | Require grade        | Yes, passing grade                                |
      | completionstatusrequired[90]  | 0 |
      | completionstatusrequired[100] | 0 |
    When I click on "Save and return to course" "button"
    Then I should not see "When you select automatic completion, you must also enable at least one requirement (below)."

    And I expand "Reports" node
    And I follow "Activity completion"
    Then I should see "One Uno, seminar 1: Not completed" in the "One Uno" "table_row"
    And I should see "Two Duex, seminar 1: Not completed" in the "Two Duex" "table_row"
    And I should see "Three Toru, seminar 1: Not completed" in the "Three Toru" "table_row"
    And I should see "Four Wha, seminar 1: Not completed" in the "Four Wha" "table_row"
    And I should see "Five Cinq, seminar 1: Not completed" in the "Five Cinq" "table_row"

    And I am on "course1" course homepage
    And I follow "seminar 1"
    And I follow "Attendee"
    And I follow "Take attendance"

    And I set the field "One Uno's attendance" to "Not set"
    And I set the field "Two Duex's attendance" to "No show"
    And I set the field "Three Toru's attendance" to "Unable to attend"
    And I set the field "Four Wha's attendance" to "Partially attended"
    And I set the field "Five Cinq's attendance" to "Fully attended"

    When I click on "Save attendance" "button"
    Then I should see "Successfully updated attendance" in the ".alert-success" "css_element"

    When I am on "course1" course homepage
    And I expand "Reports" node
    And I follow "Activity completion"
    # Include a day name to distinguish "Completed", "Completed (passed)" and "Completed (failed)"
    Then I should see "One Uno, seminar 1: Not completed" in the "One Uno" "table_row"
    And I should see "Two Duex, seminar 1: Not completed" in the "Two Duex" "table_row"
    And I should see "Three Toru, seminar 1: Not completed" in the "Three Toru" "table_row"
    And I should see "Four Wha, seminar 1: Completed (achieved pass grade)" in the "Four Wha" "table_row"
    And I should see "Five Cinq, seminar 1: Completed (achieved pass grade)" in the "Five Cinq" "table_row"

    # Re-take attendance
    When I am on "course1" course homepage
    And I follow "seminar 1"
    And I follow "Attendee"
    And I follow "Take attendance"

    And I set the field "One Uno's attendance" to "Partially attended"
    And I set the field "Two Duex's attendance" to "Not set"
    And I set the field "Three Toru's attendance" to "Not set"
    And I set the field "Four Wha's attendance" to "Not set"
    And I set the field "Five Cinq's attendance" to "No show"

    When I click on "Save attendance" "button"
    Then I should see "Successfully updated attendance" in the ".alert-success" "css_element"

    When I am on "course1" course homepage
    And I expand "Reports" node
    And I follow "Activity completion"
    # Include a day name to distinguish "Completed", "Completed (passed)" and "Completed (failed)"
    Then I should see "One Uno, seminar 1: Completed (achieved pass grade)" in the "One Uno" "table_row"
    And I should see "Two Duex, seminar 1: Not completed" in the "Two Duex" "table_row"
    And I should see "Three Toru, seminar 1: Not completed" in the "Three Toru" "table_row"
    And I should see "Four Wha, seminar 1: Not completed" in the "Four Wha" "table_row"
    And I should see "Five Cinq, seminar 1: Not completed" in the "Five Cinq" "table_row"
