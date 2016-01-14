@totara @totara_cohort
Feature: Test dynamic audience with position titles.
  In order to compute the members of a cohort with dynamic membership
  As an admin
  I should be able to use position title field values for filter rules

  Background:
    Given I am on a totara site
    And the following "position frameworks" exist in "totara_hierarchy" plugin:
      | fullname      | idnumber |
      | Position Root | PFW001   |
    And the following "positions" exist in "totara_hierarchy" plugin:
      | pos_framework | fullname        | idnumber |
      | PFW001        | Dept secretary  | POS001   |
      | PFW001        | Developer       | POS002   |
      | PFW001        | Dept manager    | POS003   |
      | PFW001        | Recruit         | POS004   |
    And the following "users" exist:
      | username | firstname | lastname | email                  |
      | itsec    | Secretary | IT       | secretary1@example.com |
      | itdev    | Developer | IT       | developer1@example.com |
      | itmgr    | Manager   | IT       | manager1@example.com   |
      | finsec   | Secretary | Fin      | secretary2@example.com |
      | findev   | Developer | Fin      | developer2@example.com |
      | finmgr   | Manager   | Fin      | manager2@example.com   |
      | newbie   | Newbie    | Recruit  | recruit@example.com   |
    And the following position assignments exist:
      | user   | position | type      | positiontitle |
      | itsec  | POS001   | primary   | IT Secretary  |
      | itdev  | POS002   | primary   | IT Developer  |
      | itmgr  | POS003   | primary   | IT Manager    |
      | finsec | POS001   | primary   | Fin Secretary |
      | findev | POS002   | primary   | Fin Developer |
      | finmgr | POS003   | primary   | Fin Manager   |
      | newbie | POS004   | primary   |               |
    And the following "cohorts" exist:
      | name         | idnumber | cohorttype |
      | TestAudience | D1       | 2          |

    Given I log in as "admin"
    And I navigate to "Audiences" node in "Site administration > Users > Accounts"
    And I follow "TestAudience"
    And I click on "Rule sets" "link" in the ".tabtree" "css_element"
    And I set the field "addrulesetmenu" to "Position title (fullname)"


  @javascript
  Scenario: cohort_position_title_01: "contains" general values
    When I set the field "equal" to "contains"
    And I set the field "listofvalues" to "Secretary"
    And I click on "Save" "button" in the "Add rule" "totaradialogue"
    Then I should see "Audience rules changed"

    When I press "Approve changes"
    And I click on "Members" "link" in the ".tabtree" "css_element"
    Then I should see "Secretary IT" in the "#cohort_members" "css_element"
    And I should not see "Developer IT" in the "#cohort_members" "css_element"
    And I should not see "Manager IT" in the "#cohort_members" "css_element"
    And I should see "Secretary Fin" in the "#cohort_members" "css_element"
    And I should not see "Developer Fin" in the "#cohort_members" "css_element"
    And I should not see "Manager Fin" in the "#cohort_members" "css_element"
    And I should not see "Newbie Recruit" in the "#cohort_members" "css_element"


  @javascript
  Scenario: cohort_position_title_02: "contains" specific values
    When I set the field "equal" to "contains"
    And I set the field "listofvalues" to "IT Secretary"
    And I click on "Save" "button" in the "Add rule" "totaradialogue"
    Then I should see "Audience rules changed"

    When I press "Approve changes"
    And I click on "Members" "link" in the ".tabtree" "css_element"
    Then I should see "Secretary IT" in the "#cohort_members" "css_element"
    And I should not see "Developer IT" in the "#cohort_members" "css_element"
    And I should not see "Manager IT" in the "#cohort_members" "css_element"
    And I should not see "Secretary Fin" in the "#cohort_members" "css_element"
    And I should not see "Developer Fin" in the "#cohort_members" "css_element"
    And I should not see "Manager Fin" in the "#cohort_members" "css_element"
    And I should not see "Newbie Recruit" in the "#cohort_members" "css_element"


  @javascript
  Scenario: cohort_position_title_03: "contains" mutiple, specific values
    When I set the field "equal" to "contains"
    And I set the field "listofvalues" to "IT Secretary,IT Developer"
    And I click on "Save" "button" in the "Add rule" "totaradialogue"
    Then I should see "Audience rules changed"

    When I press "Approve changes"
    And I click on "Members" "link" in the ".tabtree" "css_element"
    Then I should see "Secretary IT" in the "#cohort_members" "css_element"
    And I should see "Developer IT" in the "#cohort_members" "css_element"
    And I should not see "Manager IT" in the "#cohort_members" "css_element"
    And I should not see "Secretary Fin" in the "#cohort_members" "css_element"
    And I should not see "Developer Fin" in the "#cohort_members" "css_element"
    And I should not see "Manager Fin" in the "#cohort_members" "css_element"
    And I should not see "Newbie Recruit" in the "#cohort_members" "css_element"


  @javascript
  Scenario: cohort_position_title_04: "contains" specific, unknown value
    When I set the field "equal" to "contains"
    And I set the field "listofvalues" to "zzz"
    And I click on "Save" "button" in the "Add rule" "totaradialogue"
    Then I should see "Audience rules changed"

    When I press "Approve changes"
    And I click on "Members" "link" in the ".tabtree" "css_element"
    Then I should see "There are no records in this report"


  @javascript
  Scenario: cohort_position_title_05: "does not contain" specific, unknown values
    When I set the field "equal" to "does not contain"
    And I set the field "listofvalues" to "zzz"
    And I click on "Save" "button" in the "Add rule" "totaradialogue"
    Then I should see "Audience rules changed"

    When I press "Approve changes"
    And I click on "Members" "link" in the ".tabtree" "css_element"
    Then I should see "Secretary IT" in the "#cohort_members" "css_element"
    And I should see "Developer IT" in the "#cohort_members" "css_element"
    And I should see "Manager IT" in the "#cohort_members" "css_element"
    And I should see "Secretary Fin" in the "#cohort_members" "css_element"
    And I should see "Developer Fin" in the "#cohort_members" "css_element"
    And I should see "Manager Fin" in the "#cohort_members" "css_element"
    And I should see "Newbie Recruit" in the "#cohort_members" "css_element"


  @javascript
  Scenario: cohort_position_title_06: "does not contain" mutiple, specific values
    When I set the field "equal" to "does not contain"
    And I set the field "listofvalues" to "Fin Secretary,IT Developer"
    And I click on "Save" "button" in the "Add rule" "totaradialogue"
    Then I should see "Audience rules changed"

    When I press "Approve changes"
    And I click on "Members" "link" in the ".tabtree" "css_element"
    Then I should see "Secretary IT" in the "#cohort_members" "css_element"
    And I should not see "Developer IT" in the "#cohort_members" "css_element"
    And I should see "Manager IT" in the "#cohort_members" "css_element"
    And I should not see "Secretary Fin" in the "#cohort_members" "css_element"
    And I should see "Developer Fin" in the "#cohort_members" "css_element"
    And I should see "Manager Fin" in the "#cohort_members" "css_element"
    And I should see "Newbie Recruit" in the "#cohort_members" "css_element"


  @javascript
  Scenario: cohort_position_title_07: "does not contain" general values
    When I set the field "equal" to "does not contain"
    And I set the field "listofvalues" to "IT"
    And I click on "Save" "button" in the "Add rule" "totaradialogue"
    Then I should see "Audience rules changed"

    When I press "Approve changes"
    And I click on "Members" "link" in the ".tabtree" "css_element"
    Then I should not see "Secretary IT" in the "#cohort_members" "css_element"
    And I should not see "Developer IT" in the "#cohort_members" "css_element"
    And I should not see "Manager IT" in the "#cohort_members" "css_element"
    And I should see "Secretary Fin" in the "#cohort_members" "css_element"
    And I should see "Developer Fin" in the "#cohort_members" "css_element"
    And I should see "Manager Fin" in the "#cohort_members" "css_element"
    And I should see "Newbie Recruit" in the "#cohort_members" "css_element"


  @javascript
  Scenario: cohort_position_title_08: "does not contain" specific values
    When I set the field "equal" to "does not contain"
    And I set the field "listofvalues" to "Fin Manager"
    And I click on "Save" "button" in the "Add rule" "totaradialogue"
    Then I should see "Audience rules changed"

    When I press "Approve changes"
    And I click on "Members" "link" in the ".tabtree" "css_element"
    Then I should see "Secretary IT" in the "#cohort_members" "css_element"
    And I should see "Developer IT" in the "#cohort_members" "css_element"
    And I should see "Manager IT" in the "#cohort_members" "css_element"
    And I should see "Secretary Fin" in the "#cohort_members" "css_element"
    And I should see "Developer Fin" in the "#cohort_members" "css_element"
    And I should not see "Manager Fin" in the "#cohort_members" "css_element"
    And I should see "Newbie Recruit" in the "#cohort_members" "css_element"


  @javascript
  Scenario: cohort_position_title_09: "equals" specific values
    When I set the field "equal" to "is equal to"
    And I set the field "listofvalues" to "Fin Developer"
    And I click on "Save" "button" in the "Add rule" "totaradialogue"
    Then I should see "Audience rules changed"

    When I press "Approve changes"
    And I click on "Members" "link" in the ".tabtree" "css_element"
    Then I should not see "Secretary IT" in the "#cohort_members" "css_element"
    And I should not see "Developer IT" in the "#cohort_members" "css_element"
    And I should not see "Manager IT" in the "#cohort_members" "css_element"
    And I should not see "Secretary Fin" in the "#cohort_members" "css_element"
    And I should see "Developer Fin" in the "#cohort_members" "css_element"
    And I should not see "Manager Fin" in the "#cohort_members" "css_element"
    And I should not see "Newbie Recruit" in the "#cohort_members" "css_element"


  @javascript
  Scenario: cohort_position_title_10: "equals" unknown value
    When I set the field "equal" to "is equal to"
    And I set the field "listofvalues" to "zzz"
    And I click on "Save" "button" in the "Add rule" "totaradialogue"
    Then I should see "Audience rules changed"

    When I press "Approve changes"
    And I click on "Members" "link" in the ".tabtree" "css_element"
    Then I should see "There are no records in this report"


  @javascript
  Scenario: cohort_position_title_11: "equals" multiple, specific values
    When I set the field "equal" to "is equal to"
    And I set the field "listofvalues" to "IT Manager,Fin Manager"
    And I click on "Save" "button" in the "Add rule" "totaradialogue"
    Then I should see "Audience rules changed"

    When I press "Approve changes"
    And I click on "Members" "link" in the ".tabtree" "css_element"
    Then I should not see "Secretary IT" in the "#cohort_members" "css_element"
    And I should not see "Developer IT" in the "#cohort_members" "css_element"
    And I should see "Manager IT" in the "#cohort_members" "css_element"
    And I should not see "Secretary Fin" in the "#cohort_members" "css_element"
    And I should not see "Developer Fin" in the "#cohort_members" "css_element"
    And I should see "Manager Fin" in the "#cohort_members" "css_element"
    And I should not see "Newbie Recruit" in the "#cohort_members" "css_element"


  @javascript
  Scenario: cohort_position_title_12: "not equals" multiple, specific values
    When I set the field "equal" to "is not equal to"
    And I set the field "listofvalues" to "IT Manager,Fin Manager"
    And I click on "Save" "button" in the "Add rule" "totaradialogue"
    Then I should see "Audience rules changed"

    When I press "Approve changes"
    And I click on "Members" "link" in the ".tabtree" "css_element"
    Then I should see "Secretary IT" in the "#cohort_members" "css_element"
    And I should see "Developer IT" in the "#cohort_members" "css_element"
    And I should not see "Manager IT" in the "#cohort_members" "css_element"
    And I should see "Secretary Fin" in the "#cohort_members" "css_element"
    And I should see "Developer Fin" in the "#cohort_members" "css_element"
    And I should not see "Manager Fin" in the "#cohort_members" "css_element"
    And I should see "Newbie Recruit" in the "#cohort_members" "css_element"


  @javascript
  Scenario: cohort_position_title_13: "not equals" specific, unknown values
    When I set the field "equal" to "is not equal to"
    And I set the field "listofvalues" to "zzz"
    And I click on "Save" "button" in the "Add rule" "totaradialogue"
    Then I should see "Audience rules changed"

    When I press "Approve changes"
    And I click on "Members" "link" in the ".tabtree" "css_element"
    Then I should see "Secretary IT" in the "#cohort_members" "css_element"
    And I should see "Developer IT" in the "#cohort_members" "css_element"
    And I should see "Manager IT" in the "#cohort_members" "css_element"
    And I should see "Secretary Fin" in the "#cohort_members" "css_element"
    And I should see "Developer Fin" in the "#cohort_members" "css_element"
    And I should see "Manager Fin" in the "#cohort_members" "css_element"
    And I should see "Newbie Recruit" in the "#cohort_members" "css_element"


  @javascript
  Scenario: cohort_position_title_14: "not equals" specific value
    When I set the field "equal" to "is not equal to"
    And I set the field "listofvalues" to "Fin Developer"
    And I click on "Save" "button" in the "Add rule" "totaradialogue"
    Then I should see "Audience rules changed"

    When I press "Approve changes"
    And I click on "Members" "link" in the ".tabtree" "css_element"
    Then I should see "Secretary IT" in the "#cohort_members" "css_element"
    And I should see "Developer IT" in the "#cohort_members" "css_element"
    And I should see "Manager IT" in the "#cohort_members" "css_element"
    And I should see "Secretary Fin" in the "#cohort_members" "css_element"
    And I should not see "Developer Fin" in the "#cohort_members" "css_element"
    And I should see "Manager Fin" in the "#cohort_members" "css_element"
    And I should see "Newbie Recruit" in the "#cohort_members" "css_element"


  @javascript
  Scenario: cohort_position_title_15: "empty" value
    When I set the field "equal" to "is empty"
    And I set the field "listofvalues" to "aaa"
    And I click on "Save" "button" in the "Add rule" "totaradialogue"
    Then I should see "Audience rules changed"

    When I press "Approve changes"
    And I click on "Members" "link" in the ".tabtree" "css_element"
    Then I should not see "Secretary IT" in the "#cohort_members" "css_element"
    And I should not see "Developer IT" in the "#cohort_members" "css_element"
    And I should not see "Manager IT" in the "#cohort_members" "css_element"
    And I should not see "Secretary Fin" in the "#cohort_members" "css_element"
    And I should not see "Developer Fin" in the "#cohort_members" "css_element"
    And I should not see "Manager Fin" in the "#cohort_members" "css_element"
    And I should see "Newbie Recruit" in the "#cohort_members" "css_element"


  @javascript
  Scenario: cohort_position_title_16: "starts with" specific value
    When I set the field "equal" to "starts with"
    And I set the field "listofvalues" to "IT Manager"
    And I click on "Save" "button" in the "Add rule" "totaradialogue"
    Then I should see "Audience rules changed"

    When I press "Approve changes"
    And I click on "Members" "link" in the ".tabtree" "css_element"
    Then I should not see "Secretary IT" in the "#cohort_members" "css_element"
    And I should not see "Developer IT" in the "#cohort_members" "css_element"
    And I should see "Manager IT" in the "#cohort_members" "css_element"
    And I should not see "Secretary Fin" in the "#cohort_members" "css_element"
    And I should not see "Developer Fin" in the "#cohort_members" "css_element"
    And I should not see "Manager Fin" in the "#cohort_members" "css_element"
    And I should not see "Newbie Recruit" in the "#cohort_members" "css_element"


  @javascript
  Scenario: cohort_position_title_17: "starts with" unknown value
    When I set the field "equal" to "starts with"
    And I set the field "listofvalues" to "zzz"
    And I click on "Save" "button" in the "Add rule" "totaradialogue"
    Then I should see "Audience rules changed"

    When I press "Approve changes"
    And I click on "Members" "link" in the ".tabtree" "css_element"
    Then I should see "There are no records in this report"


  @javascript
  Scenario: cohort_position_title_18: "starts with" multiple values
    When I set the field "equal" to "starts with"
    And I set the field "listofvalues" to "IT,Fin"
    And I click on "Save" "button" in the "Add rule" "totaradialogue"
    Then I should see "Audience rules changed"

    When I press "Approve changes"
    And I click on "Members" "link" in the ".tabtree" "css_element"
    Then I should see "Secretary IT" in the "#cohort_members" "css_element"
    And I should see "Developer IT" in the "#cohort_members" "css_element"
    And I should see "Manager IT" in the "#cohort_members" "css_element"
    And I should see "Secretary Fin" in the "#cohort_members" "css_element"
    And I should see "Developer Fin" in the "#cohort_members" "css_element"
    And I should see "Manager Fin" in the "#cohort_members" "css_element"
    And I should not see "Newbie Recruit" in the "#cohort_members" "css_element"


  @javascript
  Scenario: cohort_position_title_19: "ends with" specific value
    When I set the field "equal" to "ends with"
    And I set the field "listofvalues" to "IT Secretary"
    And I click on "Save" "button" in the "Add rule" "totaradialogue"
    Then I should see "Audience rules changed"

    When I press "Approve changes"
    And I click on "Members" "link" in the ".tabtree" "css_element"
    Then I should see "Secretary IT" in the "#cohort_members" "css_element"
    And I should not see "Developer IT" in the "#cohort_members" "css_element"
    And I should not see "Manager IT" in the "#cohort_members" "css_element"
    And I should not see "Secretary Fin" in the "#cohort_members" "css_element"
    And I should not see "Developer Fin" in the "#cohort_members" "css_element"
    And I should not see "Manager Fin" in the "#cohort_members" "css_element"
    And I should not see "Newbie Recruit" in the "#cohort_members" "css_element"


  @javascript
  Scenario: cohort_position_title_20: "ends with" unknown value
    When I set the field "equal" to "ends with"
    And I set the field "listofvalues" to "zzz"
    And I click on "Save" "button" in the "Add rule" "totaradialogue"
    Then I should see "Audience rules changed"

    When I press "Approve changes"
    And I click on "Members" "link" in the ".tabtree" "css_element"
    Then I should see "There are no records in this report"


  @javascript
  Scenario: cohort_position_title_21: "ends with" multiple values
    When I set the field "equal" to "ends with"
    And I set the field "listofvalues" to "Secretary,Manager"
    And I click on "Save" "button" in the "Add rule" "totaradialogue"
    Then I should see "Audience rules changed"

    When I press "Approve changes"
    And I click on "Members" "link" in the ".tabtree" "css_element"
    Then I should see "Secretary IT" in the "#cohort_members" "css_element"
    And I should not see "Developer IT" in the "#cohort_members" "css_element"
    And I should see "Manager IT" in the "#cohort_members" "css_element"
    And I should see "Secretary Fin" in the "#cohort_members" "css_element"
    And I should not see "Developer Fin" in the "#cohort_members" "css_element"
    And I should see "Manager Fin" in the "#cohort_members" "css_element"
    And I should not see "Newbie Recruit" in the "#cohort_members" "css_element"