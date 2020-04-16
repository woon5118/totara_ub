@totara @perform @mod_perform
Feature: Disable performance activities feature at site-level

  Background:
    When I log in as "admin"

  @javascript
  Scenario: Disable in the advanced features page
    When I toggle open the admin quick access menu
    Then I should see "Activity Management" in the admin quick access menu

    When I navigate to "System information > Advanced features" in site administration
    And I set the field "Enable Performance Activities" to "Disable"
    And I press "Save changes"

    When I toggle open the admin quick access menu
    Then I should not see "Activity Management" in the admin quick access menu

  Scenario: Hide performance activities link in users profile
    When I am on profile page for user "admin"
    Then I should see "Performance activities" in the ".userprofile" "css_element"
    When I disable the "performance_activities" advanced feature
    And I reload the page
    Then I should not see "Performance activities" in the ".userprofile" "css_element"

  @javascript
  Scenario: Hide report source in user reports interface
    When I navigate to "Reports > Manage user reports" in site administration
    And I click on "Create report" "button"
    And I set the field with xpath "//input[@id='search_input']" to "Perform"
    And I click on "button.tw-selectSearchText__btn" "css_element"
    And I wait for pending js
    Then I should see "Performance Subject Instance (Perform)" in the ".totara_reportbuilder__createreport_list" "css_element"
    And I should see "Participant Instance (Perform)" in the ".totara_reportbuilder__createreport_list" "css_element"

    When I disable the "performance_activities" advanced feature
    And I reload the page
    And I set the field with xpath "//input[@id='search_input']" to "Perform"
    And I click on "button.tw-selectSearchText__btn" "css_element"
    And I wait for pending js
    Then I should not see "Performance Subject Instance (Perform)" in the ".totara_reportbuilder__createreport_list" "css_element"
    And I should not see "Participant Instance (Perform)" in the ".totara_reportbuilder__createreport_list" "css_element"
