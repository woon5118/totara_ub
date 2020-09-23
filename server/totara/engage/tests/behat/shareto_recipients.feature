@totara @engage @totara_engage
Feature: Share items to recipients
  Background:
    Given I am on a totara site
    And I set the site theme to "ventura"
    And I enable the "engage_resources" advanced feature

    And the following "users" exist:
      | username | firstname | lastname | email             |
      | user1    | User      | One      | one@example.com   |
      | user2    | User      | two      | two@example.com   |
      | user3    | User      | three    | three@example.com |

    And the following "topics" exist in "totara_topic" plugin:
      | name   |
      | Topic1 |

    And the following "articles" exist in "engage_article" plugin:
      | name           | username | content       | access     | topics |
      | Test Article 1 | user1    | Test Filters  | RESTRICTED | Topic1 |

  @javascript
  Scenario: View list of recipients from people picker
    Given I log in as "admin"
    And I click on "Your Library" in the totara menu
    And I view article "Test Article 1"
    Then I should see "Test Article 1"

    When I press "Share"
    And I click on "Expand Tag list" "button" in the ".tui-engageSharedRecipientsSelector" "css_element"
    Then I should see "User three"
    And  I should see "User two"