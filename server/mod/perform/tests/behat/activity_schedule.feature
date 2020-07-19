@totara @perform @mod_perform @javascript @vuejs
Feature: Define track schedules to perform activities
  As an activity administrator
  I need to be able to define track schedules to individual perform activities

  Background:
    Given I am on a totara site
    Given the following "activities" exist in "mod_perform" plugin:
      | activity_name    | description      | activity_type | create_track | activity_status |
      | My Test Activity | My Test Activity | feedback      | true         | Draft           |

  Scenario: Save and view limited fixed performance activity schedule
    Given I log in as "admin"
    And I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    And I click on "Limited" "button"
    And I set the "scheduleFixed[from]" tui date selector to "1 January 2020"
    And I set the "scheduleFixed[from]" tui date selector timezone to "UTC"
    And I set the "scheduleFixed[to]" tui date selector to "30 December 2030"

    When I save the activity schedule
    Then I should see "Instances are not created until after an activity is activated, so no users will be affected by the changes" in the tui modal

    When I click on "Confirm" "button"
    And I wait until the page is ready
    Then I should see "Changes applied and activity has been updated" in the tui "success" notification toast

    When I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    Then the "scheduleFixed[from]" tui date selector should be set to "1 January 2020"
    And the "scheduleFixed[from]" tui date selector timezone should be set to "UTC"
    And the "scheduleFixed[to]" tui date selector should be set to "30 December 2030"

  Scenario: Save and view open ended fixed performance activity schedule
    Given I log in as "admin"
    And I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    And I click on "Open-ended" "button"
    And I set the "scheduleFixed[from]" tui date selector to "1 January 2020"
    And I set the "scheduleFixed[from]" tui date selector timezone to "UTC"

    When I save the activity schedule
    And I click on "Confirm" "button"
    And I wait until the page is ready
    Then I should see "Changes applied and activity has been updated" in the tui "success" notification toast

    When I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"

    Then the "scheduleFixed[from]" tui date selector should be set to "1 January 2020"
    And the "scheduleFixed[from]" tui date selector timezone should be set to "UTC"

  Scenario: Check remembered toggling between fixed options
    Given I log in as "admin"
    And I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"

    When I click on "Assignments" "link"
    And I click on "Open-ended" "button"
    And I set the "scheduleFixed[from]" tui date selector to "1 January 2020"
    And I set the "scheduleFixed[from]" tui date selector timezone to "UTC"
    And I save the activity schedule
    And I click on "Confirm" "button"
    And I wait until the page is ready
    Then I should see "Changes applied and activity has been updated" in the tui "success" notification toast

    When I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    And I click on "Limited" "button"

    Then the "scheduleFixed[from]" tui date selector should be set to "1 January 2020"
    And the "scheduleFixed[from]" tui date selector timezone should be set to "UTC"

  Scenario: Check validation messages of fixed activity schedule
    Given I log in as "admin"
    And I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    And I click on "Limited" "button"

    When I set the "scheduleFixed[from]" tui date selector to "2 January 2020"
    And I set the "scheduleFixed[from]" tui date selector timezone to "UTC"
    And I set the "scheduleFixed[to]" tui date selector to "1 January 2020"
    And I save the activity schedule
    Then I should see "Range end date cannot be before range start date"

     # Make sure the validation for limited range doesn't apply (this used to be a bug).
    When I click on "Limited" "button"
    And I set the "scheduleFixed[from]" tui date selector to "30 June 2020"
    And I set the "scheduleFixed[to]" tui date selector to "3 January 2020"
    And I click on "Open-ended" "button"
    And I save the activity schedule
    And I click on "Confirm" "button"
    And I wait until the page is ready
    Then I should see "Changes applied and activity has been updated" in the tui "success" notification toast

    When I close the tui notification toast
    And I click on "Limited" "button"
    And I set the "scheduleFixed[from]" tui date selector to "1 January 2020"
    And I set the "scheduleFixed[to]" tui date selector to "1 January 2020"
    And I save the activity schedule
    And I click on "Confirm" "button"
    And I wait until the page is ready
    Then I should see "Changes applied and activity has been updated" in the tui "success" notification toast

  Scenario: Save and view limited dynamic performance activity schedule
    Given I log in as "admin"
    And I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    And I click on "Limited" "button"
    And I click on "Dynamic" "button"
    And I set the following fields to these values:
      | scheduleDynamic[from_count]     | 1                  |
      | scheduleDynamic[from_unit]      | weeks              |
      | scheduleDynamic[from_direction] | after              |
      | scheduleDynamic[to_count]       | 4                  |
      | scheduleDynamic[to_unit]        | weeks              |
      | scheduleDynamic[to_direction]   | after              |
      | scheduleDynamic[dynamic_source] | User creation date |
    When I save the activity schedule
    And I click on "Confirm" "button"
    And I wait until the page is ready
    Then I should see "Changes applied and activity has been updated" in the tui "success" notification toast

    When I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    Then the following fields match these values:
      | scheduleDynamic[from_count]     | 1                  |
      | scheduleDynamic[from_unit]      | weeks              |
      | scheduleDynamic[from_direction] | after              |
      | scheduleDynamic[to_count]       | 4                  |
      | scheduleDynamic[to_unit]        | weeks              |
      | scheduleDynamic[to_direction]   | after              |
      | scheduleDynamic[dynamic_source] | User creation date |

  Scenario: Save and view open ended dynamic performance activity schedule
    Given I log in as "admin"
    And I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    And I click on "Dynamic" "button"
    And I set the following fields to these values:
      | scheduleDynamic[from_count]     | 1                  |
      | scheduleDynamic[from_unit]      | weeks              |
      | scheduleDynamic[from_direction] | after              |
      | scheduleDynamic[dynamic_source] | User creation date |
    And I click on the "scheduleDynamic[use_anniversary]" tui checkbox
    Then I should not see "until" in the ".tui-performAssignmentScheduleRelativeDateSelector" "css_element"
    And "input[name='scheduleDynamic[to_count]']" "css_element" should not exist in the ".tui-performAssignmentScheduleRelativeDateSelector" "css_element"

    When I save the activity schedule
    And I click on "Confirm" "button"
    And I wait until the page is ready
    Then I should see "Changes applied and activity has been updated" in the tui "success" notification toast

    When I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    Then the following fields match these values:
      | scheduleDynamic[from_count]      | 1                  |
      | scheduleDynamic[from_unit]       | weeks              |
      | scheduleDynamic[from_direction]  | after              |
      | scheduleDynamic[dynamic_source]  | User creation date |
      | scheduleDynamic[use_anniversary] | 1                  |
    And I should not see "until" in the ".tui-performAssignmentScheduleRelativeDateSelector" "css_element"
    And "input[name='scheduleDynamic[to_count]']" "css_element" should not exist in the ".tui-performAssignmentScheduleRelativeDateSelector" "css_element"

  Scenario: Check remembered toggling between dynamic options
    Given I log in as "admin"
    And I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    And I click on "Dynamic" "button"
    And I click on "Open-ended" "button"
    And I set the following fields to these values:
      | scheduleDynamic[from_count]     | 100                |
      | scheduleDynamic[dynamic_source] | User creation date |
    When I save the activity schedule
    And I click on "Confirm" "button"
    And I wait until the page is ready
    Then I should see "Changes applied and activity has been updated" in the tui "success" notification toast

    When I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    And I click on "Limited" "button"
    Then the following fields match these values:
      | scheduleDynamic[from_count] | 100 |
      | scheduleDynamic[to_count]   | 0   |

  Scenario: Check validation messages of dynamic activity schedule
    Given I log in as "admin"
    And I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    And I click on "Limited" "button"
    And I click on "Dynamic" "button"

    When I set the following fields to these values:
      | scheduleDynamic[from_count] | 0.3 |
      | scheduleDynamic[to_count]   | 0.4 |
    And I save the activity schedule
    Then I should see "Please enter a valid whole number"

    When I set the following fields to these values:
      | scheduleDynamic[from_count] |  |
      | scheduleDynamic[to_count]   |  |
    Then I should see "Required"

    When I set the following fields to these values:
      | scheduleDynamic[from_count] | -1 |
      | scheduleDynamic[to_count]   | -1 |
    Then I should see "Number must be 0 or more"

    When I set the following fields to these values:
      | scheduleDynamic[from_count]     | 100                |
      | scheduleDynamic[from_direction] | after              |
      | scheduleDynamic[to_count]       | 10                 |
      | scheduleDynamic[to_direction]   | after              |
      | scheduleDynamic[dynamic_source] | User creation date |
    And I save the activity schedule
    Then I should see "Range end date cannot be before range start date"

    When I set the following fields to these values:
      | scheduleDynamic[from_count]     | 10                 |
      | scheduleDynamic[from_direction] | before             |
      | scheduleDynamic[to_count]       | 100                |
      | scheduleDynamic[to_direction]   | before             |
      | scheduleDynamic[dynamic_source] | User creation date |
    And I save the activity schedule
    Then I should see "Range end date cannot be before range start date"

    When I click on "Open-ended" "button"
    And I set the following fields to these values:
      | scheduleDynamic[from_count] |  |
    Then I should see "Required"

    When I set the following fields to these values:
      | scheduleDynamic[from_count] | -1 |
    Then I should see "Number must be 0 or more"

    # Allow negative -0.
    When I set the following fields to these values:
      | scheduleDynamic[from_count] | -0 |
    Then I should not see the "Number must be 0 or more" block

    When I save the activity schedule
    And I click on "Confirm" "button"
    And I wait until the page is ready
    Then I should see "Changes applied and activity has been updated" in the tui "success" notification toast

  Scenario: Check remembered toggling between fixed and dynamic options
    Given I log in as "admin"
    And I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    And I click on "Limited" "button"
    Then I set the "scheduleFixed[from]" tui date selector to "1 January 2020"
    And I set the "scheduleFixed[to]" tui date selector to "30 December 2030"

    When I click on "Dynamic" "button"
    And I set the following fields to these values:
      | scheduleDynamic[from_count]     | 1                  |
      | scheduleDynamic[from_unit]      | weeks              |
      | scheduleDynamic[from_direction] | after              |
      | scheduleDynamic[to_count]       | 4                  |
      | scheduleDynamic[to_unit]        | weeks              |
      | scheduleDynamic[to_direction]   | after              |
      | scheduleDynamic[dynamic_source] | User creation date |
    And I click on the "scheduleDynamic[use_anniversary]" tui checkbox

    When I click on "Fixed" "button"

    Then the "scheduleFixed[from]" tui date selector should be set to "1 January 2020"
    And the "scheduleFixed[to]" tui date selector should be set to "30 December 2030"

    When I click on "Dynamic" "button"
    Then the following fields match these values:
      | scheduleDynamic[from_count]      | 1                  |
      | scheduleDynamic[from_unit]       | weeks              |
      | scheduleDynamic[from_direction]  | after              |
      | scheduleDynamic[to_count]        | 4                  |
      | scheduleDynamic[to_unit]         | weeks              |
      | scheduleDynamic[to_direction]    | after              |
      | scheduleDynamic[dynamic_source]  | User creation date |
      | scheduleDynamic[use_anniversary] | 1                  |

  Scenario: Check due date is disabled by default and can be enabled
    Given I log in as "admin"
    And I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    Then I should see "Due date disabled"
    And I should see "There is no timeframe governing participation – participants can submit their responses whenever they choose."
    When I click on "Enabled" "button"
    Then I should see "Due date enabled"
    And I save the activity schedule
    And I click on "Confirm" "button"
    And I wait until the page is ready
    Then I should see "Changes applied and activity has been updated" in the tui "success" notification toast
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
    And I set the "scheduleFixed[from]" tui date selector to "1 January 2020"
    And I set the following fields to these values:
      | dueDateOffset[from_count] | 0 |
    And I save the activity schedule
    Then I should see "Due date must be after the creation end date"
    And the following fields match these values:
      | dueDateOffset[from_count] | 0 |
    When I set the following fields to these values:
      | dueDateOffset[from_count] | 1 |
    And I save the activity schedule
    And I click on "Confirm" "button"
    And I wait until the page is ready
    Then I should see "Changes applied and activity has been updated" in the tui "success" notification toast
    When I reload the page
    And I click on "Assignments" "link"
    Then the following fields match these values:
      | dueDateOffset[from_count] | 1 |

    # Open & Dynamic
    When I click on "Open-ended" "button"
    And I click on "Dynamic" "button"
    And I set the following fields to these values:
      | scheduleDynamic[from_count]     | 3                  |
      | scheduleDynamic[from_unit]      | weeks              |
      | scheduleDynamic[from_direction] | after              |
      | scheduleDynamic[dynamic_source] | User creation date |
      | dueDateOffset[from_count]       | 0                  |
    And I save the activity schedule
    Then I should see "Due date must be after the creation end date"
    When I set the following fields to these values:
      | dueDateOffset[from_count] | 2 |
    And I save the activity schedule
    And I click on "Confirm" "button"
    And I wait until the page is ready
    Then I should see "Changes applied and activity has been updated" in the tui "success" notification toast
    When I reload the page
    And I click on "Assignments" "link"
    Then the following fields match these values:
      | dueDateOffset[from_count] | 2 |

    # Limited & Dynamic
    When I click on "Limited" "button"
    And I click on "Dynamic" "button"
    And I set the following fields to these values:
      | scheduleDynamic[from_count]     | 1                  |
      | scheduleDynamic[from_unit]      | weeks              |
      | scheduleDynamic[from_direction] | after              |
      | scheduleDynamic[to_count]       | 4                  |
      | scheduleDynamic[to_unit]        | weeks              |
      | scheduleDynamic[to_direction]   | after              |
      | scheduleDynamic[dynamic_source] | User creation date |
      | dueDateOffset[from_count]       | 0                  |
    And I save the activity schedule
    Then I should see "Due date must be after the creation end date"
    When I set the following fields to these values:
      | dueDateOffset[from_count] | 3 |
    And I save the activity schedule
    And I click on "Confirm" "button"
    And I wait until the page is ready
    Then I should see "Changes applied and activity has been updated" in the tui "success" notification toast
    When I reload the page
    And I click on "Assignments" "link"
    Then the following fields match these values:
      | dueDateOffset[from_count] | 3 |

    # Limited & Fixed
    When I click on "Limited" "button"
    And I click on "Fixed" "button"
    When I set the "scheduleFixed[from]" tui date selector to "1 January 2020"
    And I set the "scheduleFixed[to]" tui date selector to "30 December 2030"
    And I click on the "true" tui radio in the "dueDateIsFixed" tui radio group
    And I set the "dueDateFixed" tui date selector to "30 December 2030"
    And I save the activity schedule
    Then I should see "Due date must be after the creation end date"
    When I set the "dueDateFixed" tui date selector to "31 December 2030"
    And I save the activity schedule
    And I click on "Confirm" "button"
    And I wait until the page is ready
    Then I should see "Changes applied and activity has been updated" in the tui "success" notification toast
    When I reload the page
    And I click on "Assignments" "link"

    When I set the "scheduleFixed[from]" tui date selector to "1 January 2020"
    And I set the "scheduleFixed[to]" tui date selector to "30 December 2030"
    Then the following fields match these values:
      | dueDateIsFixed | true |
    And I set the "dueDateFixed" tui date selector to "31 December 2030"
    And I click on the "false" tui radio in the "dueDateIsFixed" tui radio group
    And I set the following fields to these values:
      | dueDateOffset[from_count] | 0 |
    And I save the activity schedule
    Then I should see "Due date must be after the creation end date"
    When I set the following fields to these values:
      | dueDateOffset[from_count] | 4 |
    And I save the activity schedule
    And I click on "Confirm" "button"
    And I wait until the page is ready
    Then I should see "Changes applied and activity has been updated" in the tui "success" notification toast
    When I reload the page
    And I click on "Assignments" "link"
    Then the following fields match these values:
      | dueDateIsFixed            | false |
      | dueDateOffset[from_count] | 4     |

  Scenario: Check job assignment-based additional schedule settings
    Given I log in as "admin"
    And I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    And I should see "Job assignment-based instances"
    When I click on the "ONE_PER_SUBJECT" tui radio in the "additionalSettings[subject_instance_generation]" tui radio group
    And I save the activity schedule
    And I click on "Confirm" "button"
    And I wait until the page is ready
    Then I should see "Changes applied and activity has been updated" in the tui "success" notification toast
    When I reload the page
    And I click on "Assignments" "link"
    Then the following fields match these values:
      | additionalSettings[subject_instance_generation] | ONE_PER_SUBJECT |

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
    And I click on "Confirm" "button"
    And I wait until the page is ready
    Then I should see "Changes applied and activity has been updated" in the tui "success" notification toast
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
      | repeatingOffset[AFTER_COMPLETION][from_count] | 2 |
    And I set the following fields to these values:
      | repeatingOffset[AFTER_COMPLETION][from_unit] | weeks |
    And I save the activity schedule
    And I click on "Confirm" "button"
    And I wait until the page is ready
    Then I should see "Changes applied and activity has been updated" in the tui "success" notification toast

    When I reload the page
    And I click on "Assignments" "link"
    Then the following fields match these values:
      | repeatingType                                      | AFTER_COMPLETION |
      | repeatingOffset[AFTER_COMPLETION][from_count] | 2                |
      | repeatingOffset[AFTER_COMPLETION][from_unit]  | weeks            |

    And I click on the "true" tui radio in the "repeatingIsLimited" tui radio group
    And I set the following fields to these values:
      | repeatingLimit | 4 |
    And I save the activity schedule
    And I click on "Confirm" "button"
    And I wait until the page is ready
    Then I should see "Changes applied and activity has been updated" in the tui "success" notification toast

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
      | scheduleDynamic[from_count]     | 3        |
      | scheduleDynamic[from_unit]      | weeks    |
      | scheduleDynamic[from_direction] | after    |
      | scheduleDynamic[dynamic_source] | Date one |
    And I save the activity schedule
    And I click on "Confirm" "button"
    And I wait until the page is ready
    Then I should see "Changes applied and activity has been updated" in the tui "success" notification toast

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

  Scenario: Another activity dynamic schedule
    Given the following "activities" exist in "mod_perform" plugin:
      | activity_name    | description      | activity_type | create_track |
      | Activity one     | Activity one     | feedback      | true         |
      | Activity two     | MActivity two    | feedback      | true         |
    And I log in as "admin"
    When I navigate to the manage perform activities page
    And I click on "Activity one" "link"
    And I click on "Assignments" "link"
    When I click on "Open-ended" "button"
    And I click on "Dynamic" "button"
    And I set the following fields to these values:
      | scheduleDynamic[from_count]                      | 3                                   |
      | scheduleDynamic[from_unit]                       | weeks                               |
      | scheduleDynamic[from_direction]                  | after                               |
      | scheduleDynamic[dynamic_source]                  | Completion date of another activity |
      | scheduleDynamic[dynamicCustomSettings][activity] | Activity two                        |
    And I save the activity schedule
    And I click on "Confirm" "button"
    And I wait until the page is ready
    Then I should see "Changes applied and activity has been updated" in the tui "success" notification toast

    When I reload the page
    And I click on "Assignments" "link"
    Then the following fields match these values:
      | scheduleDynamic[dynamic_source]                  | Completion date of another activity |
      | scheduleDynamic[dynamicCustomSettings][activity] | Activity two                        |

  Scenario: Job assignment start date dynamic schedule
    Given the following "activities" exist in "mod_perform" plugin:
      | activity_name    | description      | activity_type | create_track |
      | Activity one     | Activity one     | feedback      | true         |
    And I log in as "admin"
    When I navigate to the manage perform activities page
    And I click on "Activity one" "link"
    And I click on "Assignments" "link"
    When I click on "Open-ended" "button"
    And I click on "Dynamic" "button"
    And I set the following fields to these values:
      | scheduleDynamic[from_count]                      | 2                          |
      | scheduleDynamic[from_unit]                       | weeks                      |
      | scheduleDynamic[from_direction]                  | after                      |
      | scheduleDynamic[dynamic_source]                  | Job assignment start date  |
    And I save the activity schedule
    Then I should see "This setting cannot be disabled while “Job assignment start date” is in use"
    When I click on the "Enabled" tui radio in the "subject_instance_generation" tui radio group
    And I save the activity schedule
    And I click on "Confirm" "button"
    And I wait until the page is ready
    Then I should see "Changes applied and activity has been updated" in the tui "success" notification toast
    When I reload the page
    And I click on "Assignments" "link"
    Then the following fields match these values:
      | scheduleDynamic[dynamic_source]                  | Job assignment start date |

