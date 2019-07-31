@javascript @totara_engage @engage_article @totara @engage
Feature: Bookmark article
  As a user
  I need to bookmark an article
  So that I can easily navigate to it in the future

  Background:
    Given I am on a totara site
    And I set the site theme to "ventura"

    And the following "topics" exist in "totara_topic" plugin:
      | name    |
      | Topic 1 |
      | Topic 2 |

    And the following "users" exist:
      | username | firstname | lastname | email          |
      | user1    | User      | One      | user1@test.com |

    And the following "articles" exist in "engage_article" plugin:
      | name           | username | content       | access | topics  |
      | Test Article 1 | user1    | Test Bookmark | PUBLIC | Topic 1 |
      | Test Article 2 | user1    | Test Bookmark | PUBLIC | Topic 2 |

    And "engage_article" "Test Article 1" is shared with the following users:
      | sharer | recipient |
      | user1  | admin     |

  Scenario: Test bookmarking a public article
    Given I log in as "admin"
    And I view article "Test Article 2"
    And I click on "Bookmark" "button"
    And I click on "Your Library" in the totara menu
    And I click on "Saved resources" "link" in the ".tui-navigationPanel__menu" "css_element"
    Then I should see "Test Article 2" in the ".tui-contributionBaseContent__cards" "css_element"

  Scenario: Test bookmarking a shared article
    And I log in as "admin"
    And I click on "Your Library" in the totara menu

    When I click on "Saved resources" "link" in the ".tui-navigationPanel__menu" "css_element"
    Then I should not see "Test Article 1" in the ".tui-contributionBaseContent__cards" "css_element"

    When I click on "Shared with you" "link" in the ".tui-navigationPanel__menu" "css_element"
    And I click on "Bookmark" "button" in the ".tui-engageArticle-articleCard" "css_element"
    And I click on "Saved resources" "link" in the ".tui-navigationPanel__menu" "css_element"
    Then I should see "Test Article 1" in the ".tui-contributionBaseContent__cards" "css_element"

    When I click on "Unbookmark" "button" in the ".tui-engageArticle-articleCard" "css_element"
    And I wait for the next second
    Then I should not see "Test Article 1" in the ".tui-contributionBaseContent__cards" "css_element"