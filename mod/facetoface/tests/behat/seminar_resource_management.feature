@mod @mod_facetoface @totara @javascript
Feature: Seminar resource management
  Background:
    Given I am on a totara site
    And the following "permission overrides" exist:
      | capability                                | permission | role           | contextlevel | reference |
      | mod/facetoface:manageadhocassets          | Allow      | editingteacher | System       |           |
      | mod/facetoface:managesitewideassets       | Allow      | editingteacher | System       |           |
      | mod/facetoface:manageadhocfacilitators    | Allow      | editingteacher | System       |           |
      | mod/facetoface:managesitewidefacilitators | Allow      | editingteacher | System       |           |
      | mod/facetoface:manageadhocrooms           | Allow      | editingteacher | System       |           |
      | mod/facetoface:managesitewiderooms        | Allow      | editingteacher | System       |           |
    And the following "users" exist:
      | username  | firstname | lastname | email                |
      | student1  | Student   | One      | student1@example.com |
      | student2  | Student   | Two      | student2@example.com |
      | trainer1  | Trainer   | First    | trainer1@example.com |
      | trainer2  | Trainer   | Second   | trainer2@example.com |
      | trainer3  | Trainer   | Third    | trainer3@example.com |
      | manager1  | Manager   | Uno      | manager1@example.com |
      | manager2  | Manager   | Dos      | manager2@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "custom seminar fields" exist in "mod_facetoface" plugin:
      | prefix      | datatype | fullname           | shortname | defaultdata |
      | asset       | checkbox | Custom field check | check     | 1           |
      | facilitator | checkbox | Custom field check | check     | 1           |
      | room        | checkbox | Custom field check | check     | 1           |
    And the following "seminars" exist in "mod_facetoface" plugin:
      | name                | intro                             | course |
      | Test seminar name 1 | <p>Test seminar description 1</p> | C1     |
      | Test seminar name 2 | <p>Test seminar description 2</p> | C1     |
    And the following "seminar events" exist in "mod_facetoface" plugin:
      | facetoface          | details  |
      | Test seminar name 1 | event 1a |
      | Test seminar name 1 | event 1b |
      | Test seminar name 1 | event 1c |
      | Test seminar name 2 | event 2a |
    And the following "seminar sessions" exist in "mod_facetoface" plugin:
      | eventdetails | start                | finish               |
      | event 1a     | 28 Feb next year 1pm | 28 Feb next year 5pm |
      | event 1a     | 28 Feb next year 5pm | 28 Feb next year 9pm |
      | event 1b     | 28 Feb next year 4pm | 28 Feb next year 6pm |
      | event 1c     | 28 Feb next year 6pm | 28 Feb next year 9pm |
      | event 2a     | 28 Feb next year 2pm | 28 Feb next year 4pm |
    And the following "global assets" exist in "mod_facetoface" plugin:
      | name                 | allowconflicts | hidden | description      |
      | Light bulb           | 0              | 0      | <p>&#128161;</p> |
      | Pizza                | 1              | 0      | <p>&#127829;</p> |
      | Coffee table         | 0              | 1      | <p>&#9749;</p>   |
    And the following "custom assets" exist in "mod_facetoface" plugin:
      | name                 | allowconflicts | hidden | description      | usercreated |
      | Ad-hoc asset 1       | 0              | 0      | <p>ad hoc!</p>   | trainer1    |
      | Ad-hoc asset 2       | 1              | 0      | <p>ad hoc!</p>   | trainer1    |
      | Ad-hoc asset 3       | 0              | 1      | <p>ad hoc!</p>   | trainer1    |
    And the following "global rooms" exist in "mod_facetoface" plugin:
      | name                 | allowconflicts | hidden | description      | capacity |
      | Fish tank            | 0              | 0      | <p>&#128031;</p> | 314      |
      | Memorial hall        | 1              | 0      | <p>&#128511;</p> | 159      |
      | Dance floor          | 0              | 1      | <p>&#127882;</p> | 265      |
    And the following "custom rooms" exist in "mod_facetoface" plugin:
      | name                 | allowconflicts | hidden | description      | capacity | usercreated |
      | Ad-hoc room 1        | 0              | 0      | <p>ad hoc!</p>   | 358      | trainer1    |
      | Ad-hoc room 2        | 1              | 0      | <p>ad hoc!</p>   | 979      | trainer1    |
      | Ad-hoc room 3        | 0              | 1      | <p>ad hoc!</p>   | 323      | trainer1    |
    And the following "global facilitators" exist in "mod_facetoface" plugin:
      | name                 | allowconflicts | hidden | description      |
      | Volunteer            | 0              | 0      | <p>&#128578;</p> |
      | Teacher assistant    | 1              | 0      | <p>&#129299;</p> |
      | Janitor              | 0              | 1      | <p>&#129529;</p> |
    And the following "custom facilitators" exist in "mod_facetoface" plugin:
      | name                 | allowconflicts | hidden | description      | usercreated |
      | Ad-hoc facilitator 1 | 0              | 0      | <p>ad hoc!</p>   | trainer1    |
      | Ad-hoc facilitator 2 | 1              | 0      | <p>ad hoc!</p>   | trainer1    |
      | Ad-hoc facilitator 3 | 0              | 1      | <p>ad hoc!</p>   | trainer1    |
    And the following "role assigns" exist:
      | user     | role           | contextlevel | reference |
      | trainer1 | editingteacher | System       |           |
      | manager1 | manager        | System       |           |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | trainer2 | C1     | teacher        |
      | manager2 | C1     | staffmanager   |
    And the following "seminar signups" exist in "mod_facetoface" plugin:
      | user     | eventdetails |
      | student1 | event 1a     |
      | student1 | event 2a     |

  Scenario: Ensure the contents of manage resource pages
    And I log in as "admin"
    And I navigate to "Assets" node in "Site administration > Seminars"
    And I expand all fieldsets
    When I set the field "Sitewide" to "Yes"
    And I click on "Search" "button_exact" in the "Search by" "fieldset"
    Then the "facetoface_assets" table should contain the following:
      | Name                 | Allow booking conflicts | Sitewide | Visible |
      | Light bulb           | No                      | Yes      | Yes     |
      | Pizza                | Yes                     | Yes      | Yes     |
      | Coffee table         | No                      | Yes      | No      |
    And I expand all fieldsets
    When I set the field "Sitewide" to "No"
    And I click on "Search" "button_exact" in the "Search by" "fieldset"
    Then the "facetoface_assets" table should contain the following:
      | Name                 | Allow booking conflicts | Sitewide | Visible |
      | Ad-hoc asset 1       | No                      | No       | Yes     |
      | Ad-hoc asset 2       | Yes                     | No       | Yes     |
      | Ad-hoc asset 3       | No                      | No       | No      |

    And I navigate to "Facilitators" node in "Site administration > Seminars"
    And I expand all fieldsets
    When I set the field "Sitewide" to "Yes"
    And I click on "Search" "button_exact" in the "Search by" "fieldset"
    Then the "facetoface_facilitators" table should contain the following:
      | Name                 | Allow booking conflicts | Sitewide | Visible |
      | Volunteer            | No                      | Yes      | Yes     |
      | Teacher assistant    | Yes                     | Yes      | Yes     |
      | Janitor              | No                      | Yes      | No      |
    And I expand all fieldsets
    When I set the field "Sitewide" to "No"
    And I click on "Search" "button_exact" in the "Search by" "fieldset"
    Then the "facetoface_facilitators" table should contain the following:
      | Name                 | Allow booking conflicts | Sitewide | Visible |
      | Ad-hoc facilitator 1 | No                      | No       | Yes     |
      | Ad-hoc facilitator 2 | Yes                     | No       | Yes     |
      | Ad-hoc facilitator 3 | No                      | No       | No      |

    And I navigate to "Rooms" node in "Site administration > Seminars"
    And I expand all fieldsets
    When I set the field "Sitewide" to "Yes"
    And I click on "Search" "button_exact" in the "Search by" "fieldset"
    Then the "facetoface_rooms" table should contain the following:
      | Name                 | Allow booking conflicts | Sitewide | Visible |
      | Fish tank            | No                      | Yes      | Yes     |
      | Memorial hall        | Yes                     | Yes      | Yes     |
      | Dance floor          | No                      | Yes      | No      |
    And I expand all fieldsets
    When I set the field "Sitewide" to "No"
    And I click on "Search" "button_exact" in the "Search by" "fieldset"
    Then the "facetoface_rooms" table should contain the following:
      | Name                 | Allow booking conflicts | Sitewide | Visible |
      | Ad-hoc room 1        | No                      | No       | Yes     |
      | Ad-hoc room 2        | Yes                     | No       | Yes     |
      | Ad-hoc room 3        | No                      | No       | No      |

  Scenario Outline: Ensure the order of fields in ad-hoc seminar resource forms
    And I log in as "trainer1"
    And I am on "Course 1" course homepage
    And I follow "Test seminar name"
    And I click on the seminar event action "Edit event" in row "#1"

    And I click on "Select <collection_type>" "link"
    When I click on "Create" "link" in the "Choose <collection_type>" "totaradialogue"
    Then "Create new <item_type>" "totaradialogue" should exist
    And "Version control" "fieldset" should not exist in the "Create new <item_type>" "totaradialogue"
    And "Add to sitewide list" "field" should not exist in the "Custom fields" "fieldset"
    And I click on "Cancel" "button" in the "Create new <item_type>" "totaradialogue"

    And I click on "Select <collection_type>" "link"
    When I click on "Ad-hoc <item_type> 1" "link" in the "Choose <collection_type>" "totaradialogue"
    And I click on "OK" "button" in the "Choose <collection_type>" "totaradialogue"
    Then I should see "Ad-hoc <item_type> 1" in the "Select <collection_type>" "table_row"
    When I click on "Edit <item_type>" "link" in the "Select <collection_type>" "table_row"
    Then "Edit <item_type>" "totaradialogue" should exist
    And "Add to sitewide list" "field" should not exist in the "Custom fields" "fieldset"
    And I should see "by Trainer First" in the "Version control" "fieldset"
    And I click on "Cancel" "button" in the "Edit <item_type>" "totaradialogue"

    #
    # Chrome dies with [x_x] Aw, Snap!
    #

    And I skip the scenario until issue "TL-23480" lands

    And I click on "Select <collection_type>" "link"
    When I click on "Create" "link" in the "Choose <collection_type>" "totaradialogue"
    Then "Create new <item_type>" "totaradialogue" should exist
    And "Name" "field" should appear before "Allow booking conflicts" "field" in the "Create new <item_type>" "totaradialogue"
    And "Allow booking conflicts" "field" should appear before "Description" "field" in the "Create new <item_type>" "totaradialogue"
    And "Description" "field" should appear before "Custom fields" "fieldset" in the "Create new <item_type>" "totaradialogue"
    And "Custom fields" "fieldset" should appear before "Version control" "fieldset" in the "Create new <item_type>" "totaradialogue"
    And "Custom fields" "fieldset" should appear before "Add to sitewide list" "field" in the "Create new <item_type>" "totaradialogue"
    And I click on "Cancel" "button" in the "Create new <item_type>" "totaradialogue"

    When I click on "Edit <item_type>" "link" in the "Select <collection_type>" "table_row"
    Then "Edit <item_type>" "totaradialogue" should exist
    And "Name" "field" should appear before "Allow booking conflicts" "field" in the "Create new <item_type>" "totaradialogue"
    And "Allow booking conflicts" "field" should appear before "Description" "field" in the "Create new <item_type>" "totaradialogue"
    And "Description" "field" should appear before "Custom fields" "fieldset" in the "Create new <item_type>" "totaradialogue"
    And "Custom fields" "fieldset" should appear before "Version control" "fieldset" in the "Create new <item_type>" "totaradialogue"
    And "Version control" "fieldset" should appear before "Add to sitewide list" "field" in the "Create new <item_type>" "totaradialogue"
    And I click on "Cancel" "button" in the "Edit <item_type>" "totaradialogue"

    Examples:
      | name        | item_type   | collection_type | an_item_type  | column_or_node |
      | Asset       | asset       | assets          | an asset      | Assets         |
      | Facilitator | facilitator | facilitators    | a facilitator | Facilitators   |
      | Room        | room        | rooms           | a room        | Rooms          |
