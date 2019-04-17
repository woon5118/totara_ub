@mod @mod_facetoface @javascript
Feature: Event manual grading
  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | course1  | course1   | 0        |
    And the following "users" exist:
      | username | firstname | lastname | email             |
      | user1    | One       | Uno      | user1@example.com |
      | user2    | Two       | Duex     | user2@example.com |
      | user3    | Three     | Toru     | user3@example.com |
      | user4    | Four      | Wha      | user4@example.com |
      | user5    | Five      | Cinq     | user5@example.com |
      | user6    | Six       | Sechs    | user6@example.com |
    And the following "course enrolments" exist:
     | user     | course   | role    |
     | user1    | course1  | student |
     | user2    | course1  | student |
     | user3    | course1  | student |
     | user4    | course1  | student |
     | user5    | course1  | student |
    And the following "seminars" exist in "mod_facetoface" plugin:
      | name      | course  | sessionattendance | attendancetime | eventgradingmanual | eventgradingmethod |
      | seminar 1 | course1 | 1                 | 2              | 1                  | 2                  |
    And the following "seminar events" exist in "mod_facetoface" plugin:
      | facetoface | details |
      | seminar 1  | event 1 |
    And the following "seminar sessions" exist in "mod_facetoface" plugin:
      | eventdetails | start       | finish      |
      | event 1      | now -2 days | now -1 days |
    And the following "seminar signups" exist in "mod_facetoface" plugin:
      | user  | eventdetails |
      | user1 | event 1      |
      | user2 | event 1      |
      | user3 | event 1      |
      | user4 | event 1      |
      | user5 | event 1      |

    And I log in as "admin"

  Scenario: Take attendance while manual event grading is off
    Given I am on "course1" course homepage
    And I follow "seminar 1"
    And I navigate to "Edit settings" node in "Seminar administration"
    And I click on "Grade" "link" in the "#id_gradesheader" "css_element"
    And I set the field "Manual event grading" to "0"
    And I click on "Save and display" "button"
    And I follow "Attendee"
    And I follow "Take attendance"
    And I click on "Fully attended" "option" in the "One Uno" "table_row"
    And I click on "Partially attended" "option" in the "Two Duex" "table_row"
    And I click on "Unable to attend" "option" in the "Three Toru" "table_row"
    And I click on "No show" "option" in the "Four Wha" "table_row"
    And I click on "Not set" "option" in the "Five Cinq" "table_row"
    And I click on "Save attendance" "button"

    And I should see "Successfully updated attendance" in the ".alert-success" "css_element"
    And I click on "Close" "button" in the ".alert-success" "css_element"

    And I navigate to "Grades" node in "Course administration"

    When I follow "Grader report"
    Then I should see "100.00" in the "One Uno" "table_row"
    And I should see "50.00" in the "Two Duex" "table_row"
    And I should see "0.00" in the "Three Toru" "table_row"
    And I should see "0.00" in the "Four Wha" "table_row"
    And I should see "-" in the "Five Cinq" "table_row"
    And I should not see "Six Sechs" in the "#user-grades" "css_element"

  Scenario: Take attendance and leave event grades as blank
    Given I am on "course1" course homepage
    And I follow "seminar 1"
    And I follow "Attendee"
    When I follow "Take attendance"

    # (Then|And) the field "Event grade" in the "(user_name)" "table_row" matches value "(event_grade)"
    Then the field "One Uno's event grade" matches value ""
    And the field "Two Duex's event grade" matches value ""
    And the field "Three Toru's event grade" matches value ""
    And the field "Four Wha's event grade" matches value ""
    And the field "Five Cinq's event grade" matches value ""

    And I click on "Fully attended" "option" in the "One Uno" "table_row"
    And I click on "Partially attended" "option" in the "Two Duex" "table_row"
    And I click on "Unable to attend" "option" in the "Three Toru" "table_row"
    And I click on "No show" "option" in the "Four Wha" "table_row"
    And I click on "Not set" "option" in the "Five Cinq" "table_row"
    When I click on "Save attendance" "button"

    # (Then|And) I should see "(attendance_state)" "select_value" in the "(user_name)" "table_row"
    Then the field with xpath "//table//tbody//tr[contains(.,'One Uno')]//select[@class='mod_facetoface__take-attendance__status-picker']" matches value "Fully attended"
    And the field with xpath "//table//tbody//tr[contains(.,'Two Duex')]//select[@class='mod_facetoface__take-attendance__status-picker']" matches value "Partially attended"
    And the field with xpath "//table//tbody//tr[contains(.,'Three Toru')]//select[@class='mod_facetoface__take-attendance__status-picker']" matches value "Unable to attend"
    And the field with xpath "//table//tbody//tr[contains(.,'Four Wha')]//select[@class='mod_facetoface__take-attendance__status-picker']" matches value "No show"
    And the field with xpath "//table//tbody//tr[contains(.,'Five Cinq')]//select[@class='mod_facetoface__take-attendance__status-picker']" matches value "Not set"

    And the field "One Uno's event grade" matches value ""
    And the field "Two Duex's event grade" matches value ""
    And the field "Three Toru's event grade" matches value ""
    And the field "Four Wha's event grade" matches value ""
    And the field "Five Cinq's event grade" matches value ""

    And I should see "Successfully updated attendance" in the ".alert-success" "css_element"
    And I click on "Close" "button" in the ".alert-success" "css_element"

    And I navigate to "Grades" node in "Course administration"

    When I follow "Grader report"
    Then I should not see ".00" in the "One Uno" "table_row"
    And I should not see ".00" in the "Two Duex" "table_row"
    And I should not see ".00" in the "Three Toru" "table_row"
    And I should not see ".00" in the "Four Wha" "table_row"
    And I should not see ".00" in the "Five Cinq" "table_row"
    And I should not see "Six Sechs" in the "#user-grades" "css_element"

  Scenario: Take attendance and manually fill event grades
    Given I am on "course1" course homepage
    And I follow "seminar 1"
    And I follow "Attendee"
    And I follow "Take attendance"

    And I set the field "One Uno's event grade" to "12"
    And I set the field "Two Duex's event grade" to "34"
    And I set the field "Three Toru's event grade" to "56"
    And I set the field "Four Wha's event grade" to "78"
    And I set the field "Five Cinq's event grade" to ""
    When I click on "Save attendance" "button"

    Then the field "One Uno's event grade" matches value "12"
    And the field "Two Duex's event grade" matches value "34"
    And the field "Three Toru's event grade" matches value "56"
    And the field "Four Wha's event grade" matches value "78"
    And the field "Five Cinq's event grade" matches value ""

    And I should see "Successfully updated attendance" in the ".alert-success" "css_element"
    And I click on "Close" "button" in the ".alert-success" "css_element"

    And I navigate to "Grades" node in "Course administration"

    When I follow "Grader report"
    Then I should see "12.00" in the "One Uno" "table_row"
    And I should see "34.00" in the "Two Duex" "table_row"
    And I should see "56.00" in the "Three Toru" "table_row"
    And I should see "78.00" in the "Four Wha" "table_row"
    And I should not see ".00" in the "Five Cinq" "table_row"
    And I should not see "Six Sechs" in the "#user-grades" "css_element"

  Scenario: Take attendance and manually fill invalid event grades
    Given I am on "course1" course homepage
    And I follow "seminar 1"
    And I follow "Attendee"
    And I follow "Take attendance"

    And I set the field "One Uno's event grade" to "forty-two"
    And I set the field "Two Duex's event grade" to "-1"
    And I set the field "Three Toru's event grade" to "999"
    And I set the field "Four Wha's event grade" to "五十六"
    And I set the field "Five Cinq's event grade" to "２４"
    When I click on "Save attendance" "button"

    And I should see "Event grade value \"forty-two\" has to be between 0 and 100" in the ".alert-danger" "css_element"
    And I should see "Event grade value \"-1\" has to be between 0 and 100" in the ".alert-danger" "css_element"
    And I should see "Event grade value \"999\" has to be between 0 and 100" in the ".alert-danger" "css_element"
    And I should see "Event grade value \"五十六\" has to be between 0 and 100" in the ".alert-danger" "css_element"
    And I should see "Event grade value \"２４\" has to be between 0 and 100" in the ".alert-danger" "css_element"
    And I click on "Close" "button" in the ".alert-danger" "css_element"

    When I click on "Save attendance" "button"
    Then I should see "Successfully updated attendance" in the ".alert-success" "css_element"
    And I click on "Close" "button" in the ".alert-success" "css_element"
