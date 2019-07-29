@totara @totara_core
Feature: Test vue component

  @javascript
  Scenario: Vue should be able to render a component
    Given I am on a totara site
    And I log in as "admin"
    And I navigate to the "vue_component" fixture in the "totara/core" plugin
    Then I should see "Vue component displayed"
