@totara @totara_customfield
Feature: Administrators can add a custom menu of choices field to complete during course creation
  In order for the custom field to appear during course creation
  As admin
  I need to select the multi-select custom field and add the relevant settings

  @javascript
  Scenario: Create a menu of choice custom field
    Given I log in as "admin"
    When I navigate to "Custom fields" node in "Site administration > Courses"
    Then I should see "Create a new custom field"

    When I set the field "datatype" to "Menu of choices"
    Then I should see "Editing custom field: Menu of choices"

    When I set the following fields to these values:
      | fullname                    | Custom Menu of choices Field   |
      | shortname                   | custommenu                     |
    And I set the field "param1" to multiline
      """
      option 1
      option 2
      option 3
      """
    And I set the field "defaultdata" to "option 2"
    And I press "Save changes"
    Then I should see "Course custom fields"

    When I go to the courses management page
    And I click on "Create new course" "link"
    Then I should see "Add a new course"

    When I expand all fieldsets
    Then I should see "Custom Menu of choices Field"
    And the following fields match these values:
      | customfield_custommenu            | option 2   |

    When I set the following fields to these values:
      | fullname                          | Course One |
      | shortname                         | course1    |
      | customfield_custommenu            | option 3   |
    And I press "Save and display"
    Then I should see "course1"

    When I navigate to "Edit settings" node in "Course administration"
    And I expand all fieldsets
    Then the following fields match these values:
      | customfield_custommenu            | option 3   |

  @javascript
  Scenario: Create a menu of choice custom field with multilang filter turn on but with the use of multilang content
    Given I log in as "admin"
    And I navigate to "Manage filters" node in "Site administration > Plugins > Filters"
    And I click on "On" "option" in the "Multi-Language Content" "table_row"
    And I click on "Content and headings" "option" in the "Multi-Language Content" "table_row"

    When I navigate to "Custom fields" node in "Site administration > Courses"
    Then I should see "Create a new custom field"

    When I set the field "datatype" to "Menu of choices"
    Then I should see "Editing custom field: Menu of choices"

    When I set the following fields to these values:
      | fullname                    | Custom Menu of choices Field   |
      | shortname                   | custommenu                     |
    And I set the field "param1" to multiline
      """
      option 1
      option 2
      option 3
      """
    And I set the field "defaultdata" to "option 2"
    And I press "Save changes"
    Then I should see "Course custom fields"

    When I go to the courses management page
    And I click on "Create new course" "link"
    Then I should see "Add a new course"

    When I expand all fieldsets
    Then I should see "Custom Menu of choices Field"
    And the following fields match these values:
      | customfield_custommenu            | option 2   |

    When I set the following fields to these values:
      | fullname                          | Course One |
      | shortname                         | course1    |
      | customfield_custommenu            | option 3   |
    And I press "Save and display"
    Then I should see "course1"

    When I navigate to "Edit settings" node in "Course administration"
    And I expand all fieldsets
    Then the following fields match these values:
      | customfield_custommenu            | option 3   |

  @javascript
  Scenario: Create a menu of choice custom field with multilang filter turn on the use of multilang content
    Given I log in as "admin"
    And I navigate to "Manage filters" node in "Site administration > Plugins > Filters"
    And I click on "On" "option" in the "Multi-Language Content" "table_row"
    And I click on "Content and headings" "option" in the "Multi-Language Content" "table_row"

    Given I navigate to "Custom fields" node in "Site administration > Courses"
    Then I should see "Create a new custom field"

    When I set the field "datatype" to "Menu of choices"
    Then I should see "Editing custom field: Menu of choices"

    When I set the following fields to these values:
      | fullname                    | Custom Menu of choices Field   |
      | shortname                   | custommenu                     |
    And I set the field "param1" to multiline
      """
<span lang="en" class="multilang">Option 1 (English)</span><span lang="de" class="multilang">Option 1 (German)</span><span lang="it" class="multilang">Option 1 (Italian)</span>
<span lang="en" class="multilang">Option 2 (English)</span><span lang="de" class="multilang">Option 2 (German)</span><span lang="it" class="multilang">Option 2 (Italian)</span>
<span lang="en" class="multilang">Option 3 (English)</span><span lang="de" class="multilang">Option 3 (German)</span><span lang="it" class="multilang">Option 3 (Italian)</span>
      """

    And I set the field "defaultdata" to "<span lang=\"en\" class=\"multilang\">Option 2 (English)</span><span lang=\"de\" class=\"multilang\">Option 2 (German)</span><span lang=\"it\" class=\"multilang\">Option 2 (Italian)</span>"
    And I press "Save changes"
    Then I should see "Course custom fields"

    When I go to the courses management page
    And I click on "Create new course" "link"
    Then I should see "Add a new course"

    When I expand all fieldsets
    Then I should see "Custom Menu of choices Field"
    And the following fields match these values:
      | customfield_custommenu            | Option 2 (English)   |

    When I set the following fields to these values:
      | fullname                          | Course One           |
      | shortname                         | course1              |
      | customfield_custommenu            | Option 3 (English)   |
    And I press "Save and display"
    Then I should see "course1"

    When I navigate to "Edit settings" node in "Course administration"
    And I expand all fieldsets
    Then the following fields match these values:
      | customfield_custommenu            | Option 3 (English)   |

