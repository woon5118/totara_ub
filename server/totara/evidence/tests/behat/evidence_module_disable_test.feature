@totara @perform @totara_evidence @javascript
Feature: Disable evidence module at site-level

  Background:
    When I log in as "admin"
    And I navigate to "System information > Advanced features" in site administration
    And I set the field "Enable Evidence" to "Enable"
    And I press "Save changes"

  Scenario: Evidence link in the admin menu
    When I toggle open the admin quick access menu
    Then I should see "Evidence" in the admin quick access menu

    # Disable it
    When I navigate to "System information > Advanced features" in site administration
    And I set the field "Enable Evidence" to "Disable"
    And I press "Save changes"

    When I toggle open the admin quick access menu
    Then I should not see "Evidence" in the admin quick access menu

  Scenario: Evidence link in users profile
    Given the "miscellaneous" user profile block exists
    When I am on profile page for user "admin"
    Then I should see "Evidence" in the ".block_totara_user_profile_category_miscellaneous" "css_element"

    # Disable it
    When I navigate to "System information > Advanced features" in site administration
    And I set the field "Enable Evidence" to "Disable"
    And I press "Save changes"

    When I am on profile page for user "admin"
    Then I should not see "Evidence" in the ".userprofile" "css_element"
