@totara @perform @mod_perform @javascript @vuejs
Feature: Create and update activity general info fields
  As an activity administrator
  I need to be able to update general activity fields.

  Background:
    Given I am on a totara site
    And the following "activities" exist in "mod_perform" plugin:
      | activity_name       | description                     | activity_type | create_track |
      | My Test Activity #1 | My Test Activity #1 description | check-in      | true         |

  Scenario: Populate the general info fields for a new activity
    Given I log in as "admin"
    And I navigate to the manage perform activities page
    And I click on "Add activity" "button"
    And I set the following fields to these values:
      | Activity title | My Test Activity #2             |
      | Description    | My Test Activity #2 description |
      | Activity type  | Feedback                        |

    When I click on "Get started" "button"
    And I navigate to the manage perform activities page
    Then I should see the tui datatable contains:
      | Name                | Type     | Status |
      | My Test Activity #1 | Check-in | Active |
      | My Test Activity #2 | Feedback | Draft |

  Scenario: Edit the general info fields for an existing activity
    Given I log in as "admin"
    And I navigate to the manage perform activities page

    When I click on "My Test Activity #1" "link"
    Then the following fields match these values:
      | Activity title | My Test Activity #1             |
      | Description    | My Test Activity #1 description |
    And "//span[contains(., 'Check-in')]" "xpath_element" should exist

    When I set the following fields to these values:
      | Activity title | My Test Activity #3             |
      | Description    | My Test Activity #3 description |
    And I click on "Save changes" "button"
    And I navigate to the manage perform activities page
    Then I should see the tui datatable contains:
      | Name                | Type     | Status |
      | My Test Activity #3 | Check-in | Active |
