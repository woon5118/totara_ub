@totara @mod_appraisal @javascript
Feature: Perform basic appraisals adminstration
  In order to ensure the appraisals works as expected
  As an admin
  I need to create calendar data

  Scenario: Create Appraisal
    Given I log in as "admin"
    And I navigate to "Manage appraisals" node in "Site administration > Appraisals"
    And I press "Create appraisal"
    And I set the following fields to these values:
      | Name | Behat Test Appraisal |
      | Description | This is the behat description|
    And I press "Create appraisal"
    And I press "Add Stage"
    And I set the following fields to these values:
      | Name | Behat Appraisal stage |
      | Description | Behat stage description|
    And I press "Add Stage"
    And I should see "Behat Appraisal stage" in the ".appraisal-stages" "css_element"
    And I click on "Behat Appraisal stage" "link"
    # AJAX updated
    And I press "Add new page"
    # AJAX form load
    And I set the following fields to these values:
      | Name | test page |
    And I press "Add New Page"
    And I should see "test page" in the "#appraisal-page-list" "css_element"
