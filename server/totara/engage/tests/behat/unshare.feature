@totara @totara_engage @javascript @engage
Feature: Unshare resource
  As a user
  I need to unlink shared resources
  So I can keep my library clean

  Background:
    Given I am on a totara site
    And I set the site theme to "ventura"

    And the following "topics" exist in "totara_topic" plugin:
      | name    |
      | Topic 1 |

    And the following "users" exist:
      | username | firstname | lastname | email             |
      | user1    | User      | One      | user1@example.com |
      | user2    | User      | Two      | user2@example.com |
      | user3    | User      | Three      | user3@example.com |

    And the following "articles" exist in "engage_article" plugin:
      | name           | username | content       | format       | access | topics  |
      | Test Article 1 | user1    | Test Article  | FORMAT_PLAIN | PUBLIC | Topic 1 |

    And "engage_article" "Test Article 1" is shared with the following users:
      | sharer | recipient |
      | user1  | user2     |
      | user1  | user3     |

  Scenario: I unlink article from share with me page
    Given I log in as "user2"
    And I click on "Your Library" in the totara menu
    And I click on "Shared with you" "link"
    Then I should see "Test Article 1" in the ".tui-contributionBaseContent__cards" "css_element"
    When I click on "Remove from Shared with you" "button"
    Then I should not see "Test Article 1" in the ".tui-contributionBaseContent__cards" "css_element"
    And I log out
    And I log in as "user3"
    And I click on "Your Library" in the totara menu
    And I click on "Shared with you" "link"
    And I click on "Test Article 1" "link"
    When I click on "Reshare resource" "button"
    Then I should see "Shared with 1 people and 0 workspace(s)" in the ".tui-engageSharedBoardForm__label" "css_element"
