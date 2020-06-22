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
    # Make sure the validation for limited range doesn't apply (this used to be a bug).
    When I set the following fields to these values:
      | scheduleFixed[from] | 2030-12-30 |
    And I save the activity schedule
    Then I should see "Activity schedule saved" in the tui "success" notification toast
    When I close the tui notification toast
    And I click on "Limited" "button"
    And I save the activity schedule
    Then I should see "Range end date cannot be before range start date"

  Scenario: Save and view limited dynamic performance activity schedule
    Given I log in as "admin"
    And I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    And I click on "Limited" "button"
    And I click on "Dynamic" "button"
    And I set the following fields to these values:
      | scheduleDynamic[count]           | 1                  |
      | scheduleDynamic[count_to]        | 4                  |
      | scheduleDynamic[unit]            | weeks              |
      | scheduleDynamic[direction]       | after              |
      | scheduleDynamic[dynamic_source] | User creation date |
    When I save the activity schedule
    Then I should see "Activity schedule saved" in the tui "success" notification toast

    When I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    Then the following fields match these values:
      | scheduleDynamic[count]           | 1                  |
      | scheduleDynamic[count_to]        | 4                  |
      | scheduleDynamic[unit]            | weeks              |
      | scheduleDynamic[direction]       | after              |
      | scheduleDynamic[dynamic_source] | User creation date |

  Scenario: Save and view open ended dynamic performance activity schedule
    Given I log in as "admin"
    And I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    And I click on "Dynamic" "button"
    And I set the following fields to these values:
      | scheduleDynamic[count]           | 1                  |
      | scheduleDynamic[unit]            | weeks              |
      | scheduleDynamic[direction]       | after              |
      | scheduleDynamic[dynamic_source] | User creation date |
    Then I should not see "until" in the ".tui-performAssignmentScheduleRelativeDateSelector" "css_element"
    And "input[name='scheduleDynamic[count_to]']" "css_element" should not exist in the ".tui-performAssignmentScheduleRelativeDateSelector" "css_element"

    When I save the activity schedule
    Then I should see "Activity schedule saved" in the tui "success" notification toast

    When I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    Then the following fields match these values:
      | scheduleDynamic[count]           | 1                  |
      | scheduleDynamic[unit]            | weeks              |
      | scheduleDynamic[direction]       | after              |
      | scheduleDynamic[dynamic_source] | User creation date |
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
      | scheduleDynamic[count]           | 100                |
      | scheduleDynamic[dynamic_source] | User creation date |
    When I save the activity schedule
    Then I should see "Activity schedule saved" in the tui "success" notification toast

    When I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    And I click on "Limited" "button"
    Then the following fields match these values:
      | scheduleDynamic[count]    | 100 |
      | scheduleDynamic[count_to] | 0   |

  Scenario: Check validation messages of dynamic activity schedule
    Given I log in as "admin"
    And I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    And I click on "Limited" "button"
    And I click on "Dynamic" "button"

    When I set the following fields to these values:
      | scheduleDynamic[count]    | 0.3 |
      | scheduleDynamic[count_to] | 0.4 |
    And I save the activity schedule
    Then I should see "Please enter a valid whole number"

    When I set the following fields to these values:
      | scheduleDynamic[count]    |  |
      | scheduleDynamic[count_to] |  |
    Then I should see "Required"

    When I set the following fields to these values:
      | scheduleDynamic[count]    | -1 |
      | scheduleDynamic[count_to] | -1 |
    Then I should see "Number must be 0 or more"

    When I set the following fields to these values:
      | scheduleDynamic[direction]       | after              |
      | scheduleDynamic[count]           | 100                |
      | scheduleDynamic[count_to]        | 10                 |
      | scheduleDynamic[dynamic_source] | User creation date |
    And I save the activity schedule
    Then I should see "Range end date cannot be before range start date"

    When I set the following fields to these values:
      | scheduleDynamic[direction]       | before             |
      | scheduleDynamic[count]           | 10                 |
      | scheduleDynamic[count_to]        | 100                |
      | scheduleDynamic[dynamic_source] | User creation date |
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
      | scheduleDynamic[count]           | 1                  |
      | scheduleDynamic[count_to]        | 4                  |
      | scheduleDynamic[unit]            | weeks              |
      | scheduleDynamic[direction]       | after              |
      | scheduleDynamic[dynamic_source] | User creation date |

    When I click on "Fixed" "button"
    Then the following fields match these values:
      | scheduleFixed[from] | 2020-01-01 |
      | scheduleFixed[to]   | 2030-12-30 |

    When I click on "Dynamic" "button"
    Then the following fields match these values:
      | scheduleDynamic[count]           | 1                  |
      | scheduleDynamic[count_to]        | 4                  |
      | scheduleDynamic[unit]            | weeks              |
      | scheduleDynamic[direction]       | after              |
      | scheduleDynamic[dynamic_source] | User creation date |

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
      | scheduleDynamic[count]           | 3                  |
      | scheduleDynamic[unit]            | weeks              |
      | scheduleDynamic[direction]       | after              |
      | scheduleDynamic[dynamic_source] | User creation date |
      | dueDateRelative[count]           | 0                  |
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
      | scheduleDynamic[count]           | 1                  |
      | scheduleDynamic[count_to]        | 4                  |
      | scheduleDynamic[unit]            | weeks              |
      | scheduleDynamic[direction]       | after              |
      | scheduleDynamic[dynamic_source] | User creation date |
      | dueDateRelative[count]           | 0                  |
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
      | dueDateFixed[from] | 2030-12-30 |
    And I save the activity schedule
    Then I should see "Due date must be after the creation end date"
    When I set the following fields to these values:
      | dueDateFixed[from] | 2030-12-31 |
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
      | dueDateIsFixed         | false |
      | dueDateRelative[count] | 4     |

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
      | additionalSettings[multiple_job_assignment] | ONE_PER_SUBJECT |

  Scenario: Check multiple job assignments schedule settings when multiple job assignments disabled
    Given I log in as "admin"
    And I set the following administration settings values:
      | totara_job_allowmultiplejobs | 0 |
    And I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    When I click on "Assignments" "link"
    Then I should not see "Additional settings"
    And I should not see "Multiple job assignments"

  Scenario: Check repeating is disabled by default and can be enabled
    Given I log in as "admin"
    And I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    Then I should see "Repeating disabled"
    And I should see "Users will receive a maximum of 1 instance each"
    When I click on "Repeating" "button"
    Then I should see "Repeating enabled"
    When I save the activity schedule
    Then I should see "Activity schedule saved" in the tui "success" notification toast
    And I reload the page
    And I click on "Assignments" "link"
    Then I should see "Repeating enabled"

  Scenario: Save and view repeating performance activity schedule
    Given I log in as "admin"
    And I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    And I click on "Repeating" "button"

    And I click on the "AFTER_COMPLETION" tui radio in the "repeatingType" tui radio group
    # When setting these fields in a single step, the count is not set - probably due to timing issues
    And I set the following fields to these values:
      | repeatingRelativeDates[AFTER_COMPLETION][count] | 2 |
    And I set the following fields to these values:
      | repeatingRelativeDates[AFTER_COMPLETION][unit] | weeks |
    And I save the activity schedule
    Then I should see "Activity schedule saved" in the tui "success" notification toast
    When I reload the page
    And I click on "Assignments" "link"
    Then the following fields match these values:
      | repeatingType                                   | AFTER_COMPLETION |
      | repeatingRelativeDates[AFTER_COMPLETION][count] | 2                |
      | repeatingRelativeDates[AFTER_COMPLETION][unit]  | weeks            |

    And I click on the "true" tui radio in the "repeatingIsLimited" tui radio group
    And I set the following fields to these values:
      | repeatingLimit | 4 |
    And I save the activity schedule
    Then I should see "Activity schedule saved" in the tui "success" notification toast
    When I reload the page
    And I click on "Assignments" "link"
    Then the following fields match these values:
      | repeatingIsLimited | true |
      | repeatingLimit     | 4    |

    # Limited / Open-ended display text
    When I click on "Limited" "button"
    Then I should see "until the creation period ends, with"
    When I click on "Open-ended" "button"
    Then I should see "no maximum limit (repeating continues indefinitely)"

  Scenario: User custom field dynamic schedule
    Given I log in as "admin"
    And I navigate to "User profile fields" node in "Site administration > Users"
    When I set the following fields to these values:
      | datatype | datetime |
    And I set the following fields to these values:
      | Name                       | Date one |
      | Short name                 | date1    |
      | Should the data be unique? | Yes      |
    And I press "Save changes"
    Then I should see "Date one"
    When I set the following fields to these values:
      | datatype | datetime |
    And I set the following fields to these values:
      | Name                       | Date two |
      | Short name                 | date2    |
      | Should the data be unique? | Yes      |
    And I press "Save changes"
    Then I should see "Date two"
    When I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    When I click on "Open-ended" "button"
    And I click on "Dynamic" "button"
    And I set the following fields to these values:
      | scheduleDynamic[count]           | 3                  |
      | scheduleDynamic[unit]            | weeks              |
      | scheduleDynamic[direction]       | after              |
      | scheduleDynamic[dynamic_source] | Date one           |
    And I save the activity schedule
    Then I should see "Activity schedule saved" in the tui "success" notification toast
    When I reload the page
    And I click on "Assignments" "link"
    Then the following fields match these values:
      | scheduleDynamic[dynamic_source] | Date one |
    When I navigate to "User profile fields" node in "Site administration > Users"
    And I click on "Delete" "link" in the "Date one" "table_row"
    Then I should not see "Date one"
    When I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    Then the following fields match these values:
      | scheduleDynamic[dynamic_source] | Date one (deleted) |
