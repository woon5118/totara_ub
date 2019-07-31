@totara @totara_playlist @engage @totara_engage
Feature: Edit playlist instance
  Background:
    Given I am on a totara site
    And I set the site theme to "ventura"
    And the following "users" exist:
      | username | firstname | lastname | email           |
      | userone  | User      | One      | one@example.com |

    And the following "playlists" exist in "totara_playlist" plugin:
      | name         | username | access  |
      | Playlist 101 | userone  | PRIVATE |

  @javascript
  Scenario: User edit the playlist's title
    Given I log in as "userone"
    And I click on "Your Library" in the totara menu
    When I follow "Playlist 101"
    Then I should see "Playlist 101"
    And I click on "Edit playlist title" "button"
    And I set the field "playlist title" to "Playlist 102"
    When I click on "Done" "button" in the ".tui-playlistTitleForm" "css_element"
    Then I should not see "Playlist 101"
    And I should see "Playlist 102"