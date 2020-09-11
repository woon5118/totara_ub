@totara @totara_engage @engage_article @engage
Feature: Update article
  Background:
    Given I am on a totara site
    And I set the site theme to "ventura"

    And the following "topics" exist in "totara_topic" plugin:
      | name    |
      | Topic 1 |

    And the following "users" exist:
      | username | firstname | lastname | email          |
      | user1    | User      | One      | user1@example.com |
      | user2    | User      | Two      | user2@example.com |

    And the following "articles" exist in "engage_article" plugin:
      | name           | username | content       | format        | access     | topics  |
      | Test Article 1 | user1    | Test Article  | FORMAT_PLAIN  | PUBLIC     | Topic 1 |
      | Test Article 2 | user1    | Test Article2 | FORMAT_PLAIN  | PUBLIC     | Topic 1 |
      | Test Article 3 | user1    | Test Article3 | FORMAT_PLAIN  | PRIVATE    | Topic 1 |
      | Test Article 4 | user2    | Test Article3 | FORMAT_PLAIN  | RESTRICTED | Topic 1 |

    And "engage_article" "Test Article 1" is shared with the following users:
      | sharer | recipient |
      | user1  | user2     |

    And "engage_article" "Test Article 2" is shared with the following users:
      | sharer | recipient |
      | user1  | user2     |

  @javascript
  Scenario: Update resource by owner
    Given I log in as "user1"
    And I view article "Test Article 1"
    And I click on "Edit Test Article 1 title" "button" with keyboard
    And I set the field "Enter resource title" to "Updated test article 1"
    And I should see "Done"
    And I press "Done"
    And I should see "Updated test article 1"
    And I click on "Edit Updated test article 1 content" "button" with keyboard
    And I wait for the next second
    And I activate the weka editor with css ".tui-editArticleContentForm__editor"
    And I type "Edit article" in the weka editor
    And I wait for the next second
    And I click on "Done" "button"

  @javascript
  Scenario: Update resource without permission
    Given I log in as "user2"
    And I view article "Test Article 1"
    And I click on "//h3[contains(text(),'Test Article 1')]/parent::*[button[contains(@title, 'Edit')]]" "xpath_element"
    And I should not see "Done"
    And I click on "//div[contains(text(),'Test Article')]/parent::*[button[contains(@title, 'Edit')]]" "xpath_element"
    And I should not see "Done"
    And I view article "Test Article 2"
    And I should see "Test Article 2"
    And I click on "//h3[contains(text(),'Test Article 2')]/parent::*[button[contains(@title, 'Edit')]]" "xpath_element"
    And I should not see "Done"
    And I should see "Test Article"
    And I click on "//div[contains(text(),'Test Article')]/parent::*[button[contains(@title, 'Edit')]]" "xpath_element"
    And I should not see "Done"

  @javascript
  Scenario: Admin can update/delete user's resource

    #View public resource
    Given I log in as "admin"
    And I view article "Test Article 1"
    And I should see "Share"
    When I click on "Actions" "button"
    Then I should see "Delete"
    And I click on "Delete" "link"
    And I close the tui modal
    When I click on "Share" "button"
    Then I should see "Only you"
    And I should see "Limited people"
    And I should see "Everyone"
    And I click on "Cancel" "button"

    # View private resource
    And I view article "Test Article 3"
    And I should see "Share"
    When I click on "Actions" "button"
    Then I should see "Delete"
    And I click on "Delete" "link"
    And I close the tui modal
    When I click on "Share" "button"
    Then I should see "Only you"
    And I should see "Limited people"
    And I should see "Everyone"
    And I click on "Cancel" "button"

    # View restrict resource
    And I view article "Test Article 4"
    And I should see "Share"
    When I click on "Actions" "button"
    Then I should see "Delete"
    And I click on "Delete" "link"
    And I close the tui modal
    When I click on "Share" "button"
    Then I should see "Only you"
    And I should see "Limited people"
    And I should see "Everyone"
    And I click on "Cancel" "button"