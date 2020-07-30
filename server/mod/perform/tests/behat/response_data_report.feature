@totara @perform @mod_perform @javascript @vuejs
Feature: Viewing and responding to perform activities

  Background:
    Given the following "users" exist:
      | username          | firstname | lastname | email                              |
      | john              | John      | One      | john.one@example.com               |

  Scenario: I can navigate to users specific report from profile pages
    Given the "miscellaneous" user profile block exists
    And I log in as "admin"
    And I click on "[aria-label='Show admin menu window']" "css_element"
    And I click on "Users" "link" in the "#quickaccess-popover-content" "css_element"
    And I click on "John One" "link"

    When I click on "Performance activity response data (export)" "link"
    Then I should see "Performance data for John One"