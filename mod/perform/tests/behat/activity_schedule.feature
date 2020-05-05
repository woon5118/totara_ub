@totara @perform @mod_perform @javascript @vuejs
Feature: Define track schedules to perform activities
  As an activity administrator
  I need to be able to define track schedules to individual perform activities

  Background:
    Given I am on a totara site
    Given the following "activities" exist in "mod_perform" plugin:
      | activity_name    | description      | activity_type | create_track |
      | My Test Activity | My Test Activity | feedback      | true         |

  Scenario: Save and view limited performance activity schedule
    Given I log in as "admin"
    And I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    And I click on "Limited" "button"
    And I set the following fields to these values:
      |closed[from]  | 1/1/2020 |
      |closed[to]    | 1/1/2021 |
    When I save the activity schedule
    And I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    Then the following fields match these values:
      | closed[from] | 1/1/2020 |
      | closed[to]   | 1/1/2021 |

  Scenario: Save and view open ended performance activity schedule
    Given I log in as "admin"
    And I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    And I click on "Open-ended" "button"
    And I set the following fields to these values:
      |open[from]  | 1/1/2020 |
    When I save the activity schedule
    And I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    Then the following fields match these values:
      |open[from]  | 1/1/2020 |

  Scenario: Check remembered toggling between option
    Given I log in as "admin"
    And I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    And I click on "Open-ended" "button"
    And I set the following fields to these values:
      |open[from]  | 1/1/2020 |
    When I save the activity schedule
    And I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    And I click on "Limited" "button"
    Then the following fields match these values:
      |closed[from]   | 1/1/2020 |

  Scenario: Check validation messages of activity schedule
    Given I log in as "admin"
    And I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    And I click on "Limited" "button"
    And I set the following fields to these values:
      |closed[from]  | abc |
      |closed[to]    | xyz |
    When I save the activity schedule
    Then I should see "Date required"
    When I set the following fields to these values:
      |closed[from]  |  |
      |closed[to]    |  |
    Then I should see "Required"
    When I set the following fields to these values:
      |closed[from]  |  10/2/2020|
      |closed[to]    |  5/2/2020|
    And I save the activity schedule
    Then I should see "Range end date cannot be before range start date"
    When I click on "Open-ended" "button"
    And I set the following fields to these values:
      |open[from]  | abc |
    When I save the activity schedule
    Then I should see "Date required"
    When I set the following fields to these values:
      |open[from]  |  |
    Then I should see "Required"