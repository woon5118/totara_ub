@totara @engage @totara_engage @engage_article @javascript
Feature: Like article

  Background:
    Given I am on a totara site
    And I set the site theme to "ventura"

    And I log in as "admin"
    And I set the following system permissions of "Authenticated user" role:
      | moodle/user:viewalldetails | Allow |
    And I log out

    And the following "topics" exist in "totara_topic" plugin:
      | name    |
      | Topic 1 |

    And the following "users" exist:
      | username | firstname | lastname | email             |
      | user1    | User      | One      | user1@example.com |
      | user2    | User      | Two      | user2@example.com |

    And the following "articles" exist in "engage_article" plugin:
      | name           | username | content       | format       | access | topics  |
      | Test Article 1 | user1    | Test Aticle 1 | FORMAT_PLAIN | PUBLIC | Topic 1 |

    And "engage_article" "Test Article 1" is shared with the following users:
      | sharer | recipient |
      | user1  | user2     |

  Scenario: Owner can not like article
    Given I log in as "user1"
    And I view article "Test Article 1"
    And I click on "Like" "button"
    And I should see "0"

  Scenario: Recipient can like and remove the like
    Given I log in as "user2"
    And I view article "Test Article 1"
    And I click on "Like" "button"
    And I should see "1"
    And I click on "Remove like" "button"
    And I should see "0"