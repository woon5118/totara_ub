@totara @totara_engage @totara_playlist @engage
Feature: Create playlist
  Background:
    Given I am on a totara site
    And I set the site theme to "ventura"
    And the following "users" exist:
      | username | firstname | lastname | email           |
      | user_one | User      | One      | one@example.com |

  @javascript
  Scenario: User create a playlist
    Given I log in as "user_one"
    And I click on "Your Library" in the totara menu
    When I click on "Contribute playlist" "button"
    Then the "Next" "button" should be disabled
    And I set the field "Enter playlist title" to "Playlist1"
    And I activate the weka editor with css ".tui-playlistForm__description-textArea"
    And I type "Some description" in the weka editor
    Then the "Next" "button" should be enabled
    And I click on "Next" "button"
    And I wait for the next second
    And I should see "Everyone"
    And I should see "Limited people"
    And I click on "Only you" "text" in the ".tui-accessSelector" "css_element"
    And the "Done" "button" should be enabled
    And I click on "Done" "button"

  @javascript
  Scenario: User create a playlist with hashtag
    Given I log in as "user_one"
    And I click on "Your Library" in the totara menu
    When I click on "Contribute playlist" "button"
    Then the "Next" "button" should be disabled
    And I set the field "Enter playlist title" to "Playlist1"
    And I activate the weka editor with css ".tui-playlistForm__description-textArea"
    And I type "#hashtag " in the weka editor
    Then the "Next" "button" should be enabled
    And I click on "Next" "button"
    And I wait for the next second
    And I should see "Everyone"
    And I should see "Limited people"
    And I click on "Only you" "text" in the ".tui-accessSelector" "css_element"
    And the "Done" "button" should be enabled
    When I click on "Done" "button"
    Then I should see "#hashtag"