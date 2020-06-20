@totara @totara_plan @totara_rol @totara_evidence @javascript
Feature: Check Evidence visibility in Record of Learning feature visibility

  Background:
    Given the "mylearning" user profile block exists
    And I am on a totara site
    And the following "users" exist in "totara_evidence" plugin:
      | username    | firstname | lastname |
      | user        | Evidence  | User     |
      | manager     | Manager   | User     |
    And the following job assignments exist:
      | user | manager |
      | user | manager |

  Scenario: Verify that Linked Evidence does not show in learning plans if it is disabled
    Given the following "plans" exist in "totara_plan" plugin:
      | user | name     |
      | user | TestPlan |
    And the following "courses" exist:
      | fullname   | shortname  |
      | TestCourse | testcourse |
    And the following "competency" frameworks exist:
      | fullname      | idnumber |
      | TestFramework | CF1      |
    And the following "competency" hierarchy exists:
      | framework | fullname       | idnumber      |
      | CF1       | TestCompetency | testcmpetency |
    And the following "programs" exist in "totara_program" plugin:
      | fullname    | shortname   |
      | TestProgram | testprogram |
    And the following "objectives" exist in "totara_plan" plugin:
      | user | plan     | name          |
      | user | TestPlan | TestObjective |

    When I am on a totara site
    And I log in as "user"
    And I click on "Dashboard" in the totara menu
    And I click on "Learning Plans" "link"
    And I click on "TestPlan" "link" in the "#dp-plans-list-unapproved-plans" "css_element"

    When I click on "Courses" "link" in the ".tabtree" "css_element"
    And I click on "Add courses" "button"
    And I click on "Miscellaneous" "link"
    And I click on "TestCourse" "link"
    And I click on "Save" "button" in the "Add courses" "totaradialogue"
    And I click on "TestCourse" "link"
    Then I should see "Linked Evidence"

    When I click on "Competencies" "link" in the ".tabtree" "css_element"
    And I click on "Add competencies" "button"
    And I click on "TestCompetency" "link"
    And I click on "Continue" "button" in the "Add competencies" "totaradialogue"
    And I click on "TestCompetency" "link"
    Then I should see "Linked Evidence"

    When I click on "Programs" "link" in the ".tabtree" "css_element"
    And I click on "Add programs" "button"
    And I click on "Miscellaneous" "link"
    And I click on "TestProgram" "link"
    And I click on "Save" "button" in the "Add programs" "totaradialogue"
    And I click on "TestProgram" "link"
    Then I should see "Linked Evidence"

    When I click on "Objectives" "link" in the ".tabtree" "css_element"
    And I click on "TestObjective" "link"
    Then I should see "Linked Evidence"

    When I log out
    And I log in as "admin"
    And I navigate to "System information > Advanced features" in site administration
    And I set the field "Enable Evidence" to "Disable"
    And I press "Save changes"
    And I log out

    When I log in as "user"
    And I click on "Dashboard" in the totara menu
    And I click on "Learning Plans" "link"
    And I click on "TestPlan" "link" in the "#dp-plans-list-unapproved-plans" "css_element"

    When I click on "Courses" "link" in the ".tabtree" "css_element"
    And I click on "TestCourse" "link"
    Then I should not see "Linked Evidence"

    When I click on "Competencies" "link" in the ".tabtree" "css_element"
    And I click on "TestCompetency" "link"
    Then I should not see "Linked Evidence"

    When I click on "Objectives" "link" in the ".tabtree" "css_element"
    And I click on "TestObjective" "link"
    Then I should not see "Linked Evidence"

    When I click on "Programs" "link" in the ".tabtree" "css_element"
    And I click on "TestProgram" "link"
    Then I should not see "Linked Evidence"

  Scenario: Verify Record of Learning shows message when evidence is disabled
    Given the following "types" exist in "totara_evidence" plugin:
      | name | location |
      | Type | 1        |
    And the following "evidence" exist in "totara_evidence" plugin:
      | name         | user    | type |
      | Evidence_One | user    | Type |
    When I log in as "user"
    And I click on "Record of Learning" in the totara menu
    Then the following should exist in the "evidence_record_of_learning" table:
      | Name         |
      | Evidence_One |
    When I log out
    And I log in as "manager"
    And I am on profile page for user "user"
    And I click on "Record of Learning" "link" in the ".block_totara_user_profile_category_mylearning" "css_element"
    Then the following should exist in the "evidence_record_of_learning" table:
      | Name         |
      | Evidence_One |
    When I log out
    And I log in as "admin"
    And I navigate to "System information > Advanced features" in site administration
    And I set the field "Enable Evidence" to "Disable"
    And I press "Save changes"
    And I log out
    And I log in as "user"
    And I click on "Record of Learning" in the totara menu
    Then I should see "There are no records to display"
    When I log out
    And I log in as "manager"
    And I am on profile page for user "user"
    And I click on "Record of Learning" "link" in the ".block_totara_user_profile_category_mylearning" "css_element"
    Then I should see "There are no records to display"

  Scenario: Verify Record of Learning shows message when user tries to view evidence of a user they aren't managing
    Given the following "types" exist in "totara_evidence" plugin:
      | name | location |
      | Type | 1        |
    And the following "evidence" exist in "totara_evidence" plugin:
      | name         | user    | type |
      | Evidence_One | user    | Type |
    When I log in as "user"
    And I click on "Record of Learning" in the totara menu
    Then the following should exist in the "evidence_record_of_learning" table:
      | Name         |
      | Evidence_One |
    When I log out
    And I log in as "admin"
    And I set the following system permissions of "Authenticated user" role:
      | totara/evidence:viewanyevidenceonself   | Prohibit |
      | totara/evidence:manageownevidenceonself | Prohibit |
      | totara/evidence:manageanyevidenceonself | Prohibit |
    And I log out
    And I log in as "user"
    And I click on "Record of Learning" in the totara menu
    Then I should see "There are no records to display"

  Scenario: Verify Record of Learning shows message when there are no evidence records to show
    When I log in as "user"
    And I click on "Record of Learning" in the totara menu
    Then I should see "There are no records to display"
    When I log out
    And I log in as "manager"
    And I am on profile page for user "user"
    And I click on "Record of Learning" "link" in the ".block_totara_user_profile_category_mylearning" "css_element"
    Given the following "types" exist in "totara_evidence" plugin:
      | name | location |
      | Type | 1        |
    And the following "evidence" exist in "totara_evidence" plugin:
      | name         | user    | type |
      | Evidence_One | user    | Type |
    And I am on profile page for user "user"
    And I click on "Record of Learning" "link" in the ".block_totara_user_profile_category_mylearning" "css_element"
    Then the following should exist in the "evidence_record_of_learning" table:
      | Name         |
      | Evidence_One |
    When I log out
    And I log in as "user"
    And I click on "Record of Learning" in the totara menu
    Then the following should exist in the "evidence_record_of_learning" table:
      | Name         |
      | Evidence_One |
