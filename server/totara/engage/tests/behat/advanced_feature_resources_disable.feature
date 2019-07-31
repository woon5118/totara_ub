@totara_engage @totara @engage
Feature: Don't show engage features when resources are disabled

  Background:
    Given I am on a totara site
    And I set the site theme to "ventura"
    And I enable the "engage_resources" advanced feature

  Scenario: Resource/Survey/Playlist cards should not show when resources advanced feature is disabled
    Given I log in as "admin"
    Then I should see "Your Library" in the totara menu

    When I set the following administration settings values:
      | enableengage_resources | Disable |
    Then I should not see "Your Library" in the totara menu