@totara @perform @mod_perform @javascript
Feature: Test viewing Performance activity response data

  Background:
    Given the following "users" exist:
      | username          | firstname | lastname | email                           |
      | user1             | User      | One      | user.one@example.com            |

    Scenario: Test capability check and tabs
      Given I log in as "admin"
      And I toggle open the admin quick access menu
      Then I should see "Performance activity response data" in the admin quick access menu

      When I click on "Performance activity response data" "link" in the "#quickaccess-popover-content" "css_element"
      Then I should see "Browse records by user"
      And I should see "Browse records by content"

      # TODO: Correct when content is added
      When I switch to "Browse records by user" tab
      Then I should see "by_user"
      When I switch to "Browse records by content" tab
      Then I should see "by_content"
