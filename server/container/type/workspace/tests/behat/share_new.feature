@container @workspace @container_workspace @totara @totara_engage @engage @javascript
Feature: Contribute new resource and share with workspace
  As a user
  I want to create and share new resources and playlists with a workspace

  Background:
    Given I am on a totara site
    And I set the site theme to "ventura"

    And the following "users" exist:
      | username | firstname | lastname | email             |
      | user1    | User      | One      | user1@example.com |
      | user2    | User      | Two      | user2@example.com |

    And the following "topics" exist in "totara_topic" plugin:
      | name   |
      | Topic1 |

    And the following "workspaces" exist in "container_workspace" plugin:
      | name             | summary   | owner |
      | Test Workspace 1 | Worskpace | user1 |

  Scenario: Test creating new resource and automatically sharing with workspace
    Given I log in as "user1"
    And I click on "Your Workspaces" in the totara menu
    And I click on "Test Workspace 1" "link" in the ".tui-workspaceMenu" "css_element"
    And I click on "Library" "link" in the ".tui-tabs__tabs" "css_element"
    And I click on "Contribute" "button"

    # Create new resource.
    When I follow "Resource"
    And I set the field "Enter resource title" to "Test Article 1"
    And I activate the weka editor with css ".tui-articleForm__description"
    And I set the weka editor to "New article"
    And I wait for the next second
    And I click on "Next" "button"
    And I wait for the next second

    # Confirm that we can see the automatically added workspace recipient.
    Then I should see "Everyone"
    And I should see "Test Workspace 1" in the ".tui-sharedRecipientsSelector" "css_element"

    # Create the resource.
    When I click on "5 to 10 mins" "text"
    And I click on "Expand Tag list" "button" in the ".tui-topicsSelector" "css_element"
    And I click on "Topic1" option in the dropdown menu
    And I click on "Done" "button"
    And I wait for the next second

    # Workspace library should show the new article.
    Then I should see "Test Article 1" in the ".tui-contributionBaseContent__cards" "css_element"

    # Confirm that the share shows in the shared board for the article after update.
    When I view article "Test Article 1"
    And I press "Share"
    And I wait for the next second
    And I click on "Show" "button" in the ".tui-sharedBoard" "css_element"
    Then I should see "Test Workspace 1" in the ".tui-sharedBoard__content" "css_element"

    # Create new playlist.
    When I click on "Your Workspaces" in the totara menu
    And I click on "Test Workspace 1" "link" in the ".tui-workspaceMenu" "css_element"
    And I click on "Library" "link" in the ".tui-tabs__tabs" "css_element"
    And I click on "Contribute" "button"
    And I follow "Playlist"
    Then the "Next" "button" should be disabled

    When I set the field "Enter playlist title" to "Playlist1"
    And I activate the weka editor with css ".tui-playlistForm__description-textArea"
    And I type "Some description" in the weka editor
    And I wait for the next second
    Then the "Next" "button" should be enabled

    When I click on "Next" "button"
    And I wait for the next second
    And I should see "Only you"
    And I should see "Limited people"
    And I click on "Everyone" "text" in the ".tui-accessSelector" "css_element"
    And I click on "Expand Tag list" "button" in the ".tui-topicsSelector" "css_element"
    And I click on "Topic1" option in the dropdown menu
    Then the "Done" "button" should be enabled
    And I click on "Done" "button"

    # Confirm playlist is linked to workspace
    When I click on "Your Workspaces" in the totara menu
    And I click on "Test Workspace 1" "link" in the ".tui-workspaceMenu" "css_element"
    And I click on "Library" "link" in the ".tui-tabs__tabs" "css_element"
    Then I should see "Playlist1" in the ".tui-totaraPlaylist-playlistCard__title" "css_element"