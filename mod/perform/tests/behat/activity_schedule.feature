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
      | scheduleFixed[from] | 2020-01-01 |
      | scheduleFixed[to]   | 2030-12-30 |
    When I save the activity schedule
    Then I should see "Activity schedule saved" in the tui "success" notification toast

    When I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    Then the following fields match these values:
      | scheduleFixed[from] | 2020-01-01 |
      | scheduleFixed[to]   | 2030-12-30 |

  Scenario: Save and view open ended fixed performance activity schedule
    Given I log in as "admin"
    And I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    And I click on "Open-ended" "button"
    And I set the following fields to these values:
      | scheduleFixed[from] | 2020-01-01 |
    When I save the activity schedule
    Then I should see "Activity schedule saved" in the tui "success" notification toast

    When I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    Then the following fields match these values:
      | scheduleFixed[from] | 2020-01-01 |

  Scenario: Check remembered toggling between fixed options
    Given I log in as "admin"
    And I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    And I click on "Open-ended" "button"
    And I set the following fields to these values:
      | scheduleFixed[from] | 2020-01-01 |
    When I save the activity schedule
    Then I should see "Activity schedule saved" in the tui "success" notification toast

    When I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    And I click on "Limited" "button"
    Then the following fields match these values:
      | scheduleFixed[from] | 2020-01-01 |

  Scenario: Check validation messages of fixed activity schedule
    Given I log in as "admin"
    And I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    And I click on "Limited" "button"
    And I set the following fields to these values:
      | scheduleFixed[from] | abc |
      | scheduleFixed[to]   | xyz |
    When I save the activity schedule
    Then I should see "Date required"
    When I set the following fields to these values:
      | scheduleFixed[from] |  |
      | scheduleFixed[to]   |  |
    Then I should see "Date required"
    When I set the following fields to these values:
      | scheduleFixed[from] | 2030-12-30 |
      | scheduleFixed[to]   | 2020-01-01 |
    And I save the activity schedule
    Then I should see "Range end date cannot be before range start date"
    When I click on "Open-ended" "button"
    And I set the following fields to these values:
      | scheduleFixed[from] | abc |
    When I save the activity schedule
    Then I should see "Date required"
    When I set the following fields to these values:
      | scheduleFixed[from] |  |
    Then I should see "Date required"

  Scenario: Save and view limited dynamic performance activity schedule
    Given I log in as "admin"
    And I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    And I click on "Limited" "button"
    And I click on "Dynamic" "button"
    And I set the following fields to these values:
      | scheduleDynamic[count]      | 1     |
      | scheduleDynamic[count_to]   | 4     |
      | scheduleDynamic[unit]       | weeks |
      | scheduleDynamic[direction]  | after |
    When I save the activity schedule
    Then I should see "Activity schedule saved" in the tui "success" notification toast

    When I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    Then the following fields match these values:
      | scheduleDynamic[count]      | 1     |
      | scheduleDynamic[count_to]   | 4     |
      | scheduleDynamic[unit]       | weeks |
      | scheduleDynamic[direction]  | after |

  Scenario: Save and view open ended dynamic performance activity schedule
    Given I log in as "admin"
    And I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    And I click on "Dynamic" "button"
    And I set the following fields to these values:
      | scheduleDynamic[count]      | 1     |
      | scheduleDynamic[unit]       | weeks |
      | scheduleDynamic[direction]  | after |
    Then I should not see "until" in the ".tui-performAssignmentScheduleRelativeDateSelector" "css_element"
    And "input[name='scheduleDynamic[count_to]']" "css_element" should not exist in the ".tui-performAssignmentScheduleRelativeDateSelector" "css_element"

    When I save the activity schedule
    Then I should see "Activity schedule saved" in the tui "success" notification toast

    When I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    Then the following fields match these values:
      | scheduleDynamic[count]      | 1     |
      | scheduleDynamic[unit]       | weeks |
      | scheduleDynamic[direction]  | after |
    And I should not see "until" in the ".tui-performAssignmentScheduleRelativeDateSelector" "css_element"
    And "input[name='scheduleDynamic[count_to]']" "css_element" should not exist in the ".tui-performAssignmentScheduleRelativeDateSelector" "css_element"

  Scenario: Check remembered toggling between dynamic options
    Given I log in as "admin"
    And I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    And I click on "Dynamic" "button"
    And I click on "Open-ended" "button"
    And I set the following fields to these values:
      | scheduleDynamic[count] | 100 |
    When I save the activity schedule
    Then I should see "Activity schedule saved" in the tui "success" notification toast

    When I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    And I click on "Limited" "button"
    Then the following fields match these values:
      | scheduleDynamic[count]      | 100 |
      | scheduleDynamic[count_to]   | 0   |

  Scenario: Check validation messages of dynamic activity schedule
    Given I log in as "admin"
    And I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    And I click on "Limited" "button"
    And I click on "Dynamic" "button"

    When I set the following fields to these values:
      | scheduleDynamic[count]      | 0.3 |
      | scheduleDynamic[count_to]   | 0.4 |
    And I save the activity schedule
    Then I should see "Please enter a valid whole number"

    When I set the following fields to these values:
      | scheduleDynamic[count]      |  |
      | scheduleDynamic[count_to]   |  |
    Then I should see "Required"

    When I set the following fields to these values:
      | scheduleDynamic[count]      | -1 |
      | scheduleDynamic[count_to]   | -1 |
    Then I should see "Number must be 0 or more"

    When I set the following fields to these values:
      | scheduleDynamic[direction]  | after |
      | scheduleDynamic[count]      | 100   |
      | scheduleDynamic[count_to]   | 10    |
    And I save the activity schedule
    Then I should see "Range end date cannot be before range start date"

    When I set the following fields to these values:
      | scheduleDynamic[direction]  | before |
      | scheduleDynamic[count]      | 10     |
      | scheduleDynamic[count_to]   | 100    |
    And I save the activity schedule
    Then I should see "Range end date cannot be before range start date"

    When I click on "Open-ended" "button"
    And I set the following fields to these values:
      | scheduleDynamic[count] |  |
    Then I should see "Required"

    When I set the following fields to these values:
      | scheduleDynamic[count] | -1 |
    Then I should see "Number must be 0 or more"

    # Allow negative -0.
    When I set the following fields to these values:
      | scheduleDynamic[count] | -0 |
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
      | scheduleFixed[from] | 2020-01-01 |
      | scheduleFixed[to]   | 2030-12-30 |

    When I click on "Dynamic" "button"
    And I set the following fields to these values:
      | scheduleDynamic[count]      | 1     |
      | scheduleDynamic[count_to]   | 4     |
      | scheduleDynamic[unit]       | weeks |
      | scheduleDynamic[direction]  | after |

    When I click on "Fixed" "button"
    Then the following fields match these values:
      | scheduleFixed[from] | 2020-01-01 |
      | scheduleFixed[to]   | 2030-12-30 |

    When I click on "Dynamic" "button"
    Then the following fields match these values:
      | scheduleDynamic[count]      | 1     |
      | scheduleDynamic[count_to]   | 4     |
      | scheduleDynamic[unit]       | weeks |
      | scheduleDynamic[direction]  | after |

  Scenario: Check due date is disabled by default and can be enabled
    Given I log in as "admin"
    And I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    Then I should see "Due date disabled"
    And I should see "There is no timeframe governing participation – participants can submit their responses whenever they choose."
    When I click on "Enabled" "button"
    Then I should see "Due date enabled"
    When I save the activity schedule
    Then I should see "Activity schedule saved" in the tui "success" notification toast
    And I reload the page
    And I click on "Assignments" "link"
    Then I should see "Due date enabled"

  Scenario: Check can set a due date
    Given I log in as "admin"
    And I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    And I click on "Enabled" "button"

    # Open & Fixed
    When I click on "Open-ended" "button"
    And I click on "Fixed" "button"
    And I set the following fields to these values:
      | scheduleFixed[from]    | 2020-01-01 |
      | dueDateRelative[count] | 0          |
    And I save the activity schedule
    Then I should see "Due date must be after the creation end date"
    And the following fields match these values:
      | dueDateRelative[count] | 0 |
    When I set the following fields to these values:
      | dueDateRelative[count] | 1 |
    And I save the activity schedule
    Then I should see "Activity schedule saved" in the tui "success" notification toast
    When I reload the page
    And I click on "Assignments" "link"
    Then the following fields match these values:
      | dueDateRelative[count] | 1 |

    # Open & Dynamic
    When I click on "Open-ended" "button"
    And I click on "Dynamic" "button"
    And I set the following fields to these values:
      | scheduleDynamic[count]      | 3     |
      | scheduleDynamic[unit]       | weeks |
      | scheduleDynamic[direction]  | after |
      | dueDateRelative[count]      | 0     |
    And I save the activity schedule
    Then I should see "Due date must be after the creation end date"
    When I set the following fields to these values:
      | dueDateRelative[count] | 2 |
    And I save the activity schedule
    Then I should see "Activity schedule saved" in the tui "success" notification toast
    When I reload the page
    And I click on "Assignments" "link"
    Then the following fields match these values:
      | dueDateRelative[count] | 2 |

    # Limited & Dynamic
    When I click on "Limited" "button"
    And I click on "Dynamic" "button"
    And I set the following fields to these values:
      | scheduleDynamic[count]      | 1     |
      | scheduleDynamic[count_to]   | 4     |
      | scheduleDynamic[unit]       | weeks |
      | scheduleDynamic[direction]  | after |
      | dueDateRelative[count]      | 0     |
    And I save the activity schedule
    Then I should see "Due date must be after the creation end date"
    When I set the following fields to these values:
      | dueDateRelative[count] | 3 |
    And I save the activity schedule
    Then I should see "Activity schedule saved" in the tui "success" notification toast
    When I reload the page
    And I click on "Assignments" "link"
    Then the following fields match these values:
      | dueDateRelative[count] | 3 |

    # Limited & Fixed
    When I click on "Limited" "button"
    And I click on "Fixed" "button"
    And I set the following fields to these values:
      | scheduleFixed[from] | 2020-01-01 |
      | scheduleFixed[to]   | 2030-12-30 |
    And I click on the "true" tui radio in the "dueDateIsFixed" tui radio group
    And I set the following fields to these values:
      | dueDateFixed[from]  | 2030-12-30 |
    And I save the activity schedule
    Then I should see "Due date must be after the creation end date"
    When I set the following fields to these values:
      | dueDateFixed[from]  | 2030-12-31 |
    And I save the activity schedule
    Then I should see "Activity schedule saved" in the tui "success" notification toast
    When I reload the page
    And I click on "Assignments" "link"
    Then the following fields match these values:
      | scheduleFixed[from] | 2020-01-01 |
      | scheduleFixed[to]   | 2030-12-30 |
      | dueDateIsFixed      | true       |
      | dueDateFixed[from]  | 2030-12-31 |
    And I click on the "false" tui radio in the "dueDateIsFixed" tui radio group
    And I set the following fields to these values:
      | dueDateRelative[count] | 0 |
    And I save the activity schedule
    Then I should see "Due date must be after the creation end date"
    When I set the following fields to these values:
      | dueDateRelative[count] | 4 |
    And I save the activity schedule
    Then I should see "Activity schedule saved" in the tui "success" notification toast
    When I reload the page
    And I click on "Assignments" "link"
    Then the following fields match these values:
      | dueDateIsFixed         |   |
      | dueDateRelative[count] | 4 |

  Scenario: Check multiple job assignments schedule settings when multiple job assignments enabled
    Given I log in as "admin"
    And I set the following administration settings values:
      | totara_job_allowmultiplejobs | 1 |
    And I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    When I click on the "ONE_PER_SUBJECT" tui radio in the "additionalSettings[multiple_job_assignment]" tui radio group
    And I save the activity schedule
    Then I should see "Activity schedule saved" in the tui "success" notification toast
    When I reload the page
    And I click on "Assignments" "link"
    Then the following fields match these values:
      | additionalSettings[multiple_job_assignment]   | ONE_PER_SUBJECT |

  Scenario: Check multiple job assignments schedule settings when multiple job assignments disabled
    Given I log in as "admin"
    And I set the following administration settings values:
      | totara_job_allowmultiplejobs | 0 |
    And I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    When I click on "Assignments" "link"
    Then I should not see "Additional settings"
    And I should not see "Multiple job assignments"
