@totara @perform @mod_perform @javascript @vuejs
Feature: Define track schedules to perform activities
  As an activity administrator
  I need to be able to define track schedules to individual perform activities

  Background:
    Given I am on a totara site
    Given the following "activities" exist in "mod_perform" plugin:
      | activity_name    | description      | activity_type | create_track |
      | My Test Activity | My Test Activity | feedback      | true         |

  Scenario: Save and view limited fixed performance activity schedule
    Given I log in as "admin"
    And I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    And I click on "Limited" "button"
    And I set the following fields to these values:
      | fixed[fixed_from] | 2020-01-01 |
      | fixed[fixed_to]   | 2030-12-30 |
    When I save the activity schedule
    Then I should see "Activity schedule saved" in the tui "success" notification toast

    When I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    Then the following fields match these values:
      | fixed[fixed_from] | 2020-01-01 |
      | fixed[fixed_to]   | 2030-12-30 |

  Scenario: Save and view open ended fixed performance activity schedule
    Given I log in as "admin"
    And I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    And I click on "Open-ended" "button"
    And I set the following fields to these values:
      | fixed[fixed_from] | 2020-01-01 |
    When I save the activity schedule
    Then I should see "Activity schedule saved" in the tui "success" notification toast

    When I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    Then the following fields match these values:
      | fixed[fixed_from] | 2020-01-01 |

  Scenario: Check remembered toggling between fixed options
    Given I log in as "admin"
    And I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    And I click on "Open-ended" "button"
    And I set the following fields to these values:
      | fixed[fixed_from] | 2020-01-01 |
    When I save the activity schedule
    Then I should see "Activity schedule saved" in the tui "success" notification toast

    When I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    And I click on "Limited" "button"
    Then the following fields match these values:
      | fixed[fixed_from] | 2020-01-01 |

  Scenario: Check validation messages of fixed activity schedule
    Given I log in as "admin"
    And I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    And I click on "Limited" "button"
    And I set the following fields to these values:
      | fixed[fixed_from] | abc |
      | fixed[fixed_to]   | xyz |
    When I save the activity schedule
    Then I should see "Date required"
    When I set the following fields to these values:
      | fixed[fixed_from] |  |
      | fixed[fixed_to]   |  |
    Then I should see "Date required"
    When I set the following fields to these values:
      | fixed[fixed_from] | 2030-12-30 |
      | fixed[fixed_to]   | 2020-01-01 |
    And I save the activity schedule
    Then I should see "Range end date cannot be before range start date"
    When I click on "Open-ended" "button"
    And I set the following fields to these values:
      | fixed[fixed_from] | abc |
    When I save the activity schedule
    Then I should see "Date required"
    When I set the following fields to these values:
      | fixed[fixed_from] |  |
    Then I should see "Date required"

  Scenario: Save and view limited dynamic performance activity schedule
    Given I log in as "admin"
    And I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    And I click on "Limited" "button"
    And I click on "Dynamic" "button"
    And I set the following fields to these values:
      | dynamic[dynamic_count_from] | 1     |
      | dynamic[dynamic_count_to]   | 4     |
      | dynamic[dynamic_unit]       | weeks |
      | dynamic[dynamic_direction]  | after |
    When I save the activity schedule
    Then I should see "Activity schedule saved" in the tui "success" notification toast

    When I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    Then the following fields match these values:
      | dynamic[dynamic_count_from] | 1     |
      | dynamic[dynamic_count_to]   | 4     |
      | dynamic[dynamic_unit]       | weeks |
      | dynamic[dynamic_direction]  | after |

  Scenario: Save and view open ended dynamic performance activity schedule
    Given I log in as "admin"
    And I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    And I click on "Dynamic" "button"
    And I set the following fields to these values:
      | dynamic[dynamic_count_from] | 1     |
      | dynamic[dynamic_unit]       | weeks |
      | dynamic[dynamic_direction]  | after |
    Then I should not see "until" in the ".tui_performAssignmentSchedule__narrative-inputs" "css_element"
    And "input[name='dynamic[dynamic_count_to]']" "css_element" should not exist in the ".tui_performAssignmentSchedule__narrative-inputs" "css_element"

    When I save the activity schedule
    Then I should see "Activity schedule saved" in the tui "success" notification toast

    When I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    Then the following fields match these values:
      | dynamic[dynamic_count_from] | 1     |
      | dynamic[dynamic_unit]       | weeks |
      | dynamic[dynamic_direction]  | after |
    And I should not see "until" in the ".tui_performAssignmentSchedule__narrative-inputs" "css_element"
    And "input[name='dynamic[dynamic_count_to]']" "css_element" should not exist in the ".tui_performAssignmentSchedule__narrative-inputs" "css_element"

  Scenario: Check remembered toggling between dynamic options
    Given I log in as "admin"
    And I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    And I click on "Dynamic" "button"
    And I click on "Open-ended" "button"
    And I set the following fields to these values:
      | dynamic[dynamic_count_from] | 100 |
    When I save the activity schedule
    Then I should see "Activity schedule saved" in the tui "success" notification toast

    When I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    And I click on "Limited" "button"
    Then the following fields match these values:
      | dynamic[dynamic_count_from] | 100 |
      | dynamic[dynamic_count_to]   | 0   |

  Scenario: Check validation messages of dynamic activity schedule
    Given I log in as "admin"
    And I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    And I click on "Limited" "button"
    And I click on "Dynamic" "button"

    When I set the following fields to these values:
      | dynamic[dynamic_count_from] | 0.3 |
      | dynamic[dynamic_count_to]   | 0.4 |
    And I save the activity schedule
    Then I should see "Please enter a valid whole number"

    When I set the following fields to these values:
      | dynamic[dynamic_count_from] |  |
      | dynamic[dynamic_count_to]   |  |
    Then I should see "Required"

    When I set the following fields to these values:
      | dynamic[dynamic_count_from] | -1 |
      | dynamic[dynamic_count_to]   | -1 |
    Then I should see "Number must be 0 or more"

    When I set the following fields to these values:
      | dynamic[dynamic_direction]  | after |
      | dynamic[dynamic_count_from] | 100   |
      | dynamic[dynamic_count_to]   | 10    |
    And I save the activity schedule
    Then I should see "Range end date cannot be before range start date"

    When I set the following fields to these values:
      | dynamic[dynamic_direction]  | before |
      | dynamic[dynamic_count_from] | 10     |
      | dynamic[dynamic_count_to]   | 100    |
    And I save the activity schedule
    Then I should see "Range end date cannot be before range start date"

    When I click on "Open-ended" "button"
    And I set the following fields to these values:
      | dynamic[dynamic_count_from] |  |
    Then I should see "Required"

    When I set the following fields to these values:
      | dynamic[dynamic_count_from] | -1 |
    Then I should see "Number must be 0 or more"

    # Allow negative -0.
    When I set the following fields to these values:
      | dynamic[dynamic_count_from] | -0 |
    Then I should not see the "Number must be 0 or more" block

    When I save the activity schedule
    Then I should see "Activity schedule saved" in the tui "success" notification toast

  Scenario: Check remembered toggling between fixed and dynamic options
    Given I log in as "admin"
    And I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    And I click on "Limited" "button"
    And I set the following fields to these values:
      | fixed[fixed_from] | 2020-01-01 |
      | fixed[fixed_to]   | 2030-12-30 |

    When I click on "Dynamic" "button"
    And I set the following fields to these values:
      | dynamic[dynamic_count_from] | 1     |
      | dynamic[dynamic_count_to]   | 4     |
      | dynamic[dynamic_unit]       | weeks |
      | dynamic[dynamic_direction]  | after |

    When I click on "Fixed" "button"
    Then the following fields match these values:
      | fixed[fixed_from] | 2020-01-01 |
      | fixed[fixed_to]   | 2030-12-30 |

    When I click on "Dynamic" "button"
    Then the following fields match these values:
      | dynamic[dynamic_count_from] | 1     |
      | dynamic[dynamic_count_to]   | 4     |
      | dynamic[dynamic_unit]       | weeks |
      | dynamic[dynamic_direction]  | after |