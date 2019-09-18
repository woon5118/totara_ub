@totara @totara_reportbuilder @javascript
Feature: Test my reports page

  Background:
    Given I am on a totara site
    And I log in as "admin"
    # Create a report.
    And I navigate to "Manage user reports" node in "Site administration > Reports"
    And I press "Create report"
    And I set the field "Report Name" to "Custom user report 1"
    And I set the field "Source" to "User"
    When I press "Create report"
    Then I should see "Edit Report 'Custom user report 1'"
    When I set the field "Abstract" to "User report 1 abstract text"
    And I press "Save changes"
    Then I should see "Report Updated"
    # Create another report.
    When I navigate to "Manage user reports" node in "Site administration > Reports"
    And I press "Create report"
    And I set the field "Report Name" to "Custom user report 2"
    And I set the field "Source" to "User"
    And I press "Create report"
    Then I should see "Edit Report 'Custom user report 2'"
    When I set the field "Abstract" to "User report 2 abstract text"
    And I press "Save changes"
    Then I should see "Report Updated"

  Scenario: The my reports page displays reports allowing users to navigate to the report
    Given I click on "Reports" in the totara menu
    Then I should see "Custom user report 1"
    And  I should see "Custom user report 2"
    And "Custom user report 1" "text" should appear before "Custom user report 2" "text"
    When I follow "Custom user report 1"
    Then I should see "Custom user report 1"
    And I should see "2 records shown" in the ".rb-record-count" "css_element"
    When I click on "Reports" in the totara menu
    And I follow "Custom user report 2"
    Then I should see "Custom user report 2"
    And I should see "2 records shown" in the ".rb-record-count" "css_element"
    When I click on "Reports" in the totara menu
    And I click on "//div/a/span[contains(@data-flex-icon, 'view-list')]" "xpath_element"
    Then I should see "Custom user report 1"
    And  I should see "Custom user report 2"
    And "Custom user report 1" "text" should appear before "Custom user report 2" "text"
    When I click on "//div/a/span[contains(@data-flex-icon, 'view-grid')]" "xpath_element"
    Then I should see "Custom user report 1"
    And  I should see "Custom user report 2"
    And "Custom user report 1" "text" should appear before "Custom user report 2" "text"

  Scenario: Reports can be hidden on the my reports page
    # Create a hidden report.
    When I navigate to "Manage user reports" node in "Site administration > Reports"
    And I press "Create report"
    And I set the following fields to these values:
      | Report Name               | Custom user report 3 |
      | Source                    | User                 |
      | Hide on user reports list | 1                    |
    And I press "Create report"
    Then I should see "Edit Report 'Custom user report 3'"
    When I set the field "Abstract" to "User report 2 abstract text"
    And I press "Save changes"
    Then I should see "Report Updated"
    When I click on "Reports" in the totara menu
    Then I should see "Custom user report 1"
    And  I should see "Custom user report 2"
    And  I should not see "Custom user report 3"
    And "Custom user report 1" "text" should appear before "Custom user report 2" "text"

  Scenario: The display of the abstract text can be turned on or off
    Given I click on "Reports" in the totara menu
    Then I should not see "User report 1 abstract text"
    And  I should not see "User report 2 abstract text"
    When I navigate to "General settings" node in "Site administration > Reports"
    And I set the following fields to these values:
      | Show report abstract | 1 |
    And I press "Save changes"
    And I should see "Changes saved"
    And I click on "Reports" in the totara menu
    Then I should see "User report 1 abstract text"
    And  I should see "User report 2 abstract text"
