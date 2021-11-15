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
    And I set the "scheduleFixed[from]" tui date selector to "-1 day"
    And I set the "scheduleFixed[from]" tui date selector timezone to "UTC"
    And I set the "scheduleFixed[to]" tui date selector to "+1 day"

    When I save the activity schedule
    Then I should see "Instances are not created until after an activity is activated, so no users will be affected by the changes" in the tui modal

    When I click on "Confirm" "button"
    And I wait until the page is ready
    Then I should see "Changes applied and activity has been updated" in the tui success notification toast

    When I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    Then the "scheduleFixed[from]" tui date selector should be set to "-1 day"
    And the "scheduleFixed[from]" tui date selector timezone should be set to "UTC"
    And the "scheduleFixed[to]" tui date selector should be set to "+1 day"

  Scenario: Save and view open ended fixed performance activity schedule
    Given I log in as "admin"
    And I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    And I click on "Open-ended" "button"
    And I set the "scheduleFixed[from]" tui date selector to "-1 day"
    And I set the "scheduleFixed[from]" tui date selector timezone to "UTC"

    When I save the activity schedule
    And I click on "Confirm" "button"
    And I wait until the page is ready
    Then I should see "Changes applied and activity has been updated" in the tui success notification toast

    When I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"

    Then the "scheduleFixed[from]" tui date selector should be set to "-1 day"
    And the "scheduleFixed[from]" tui date selector timezone should be set to "UTC"

  Scenario: Check remembered toggling between fixed options
    Given I log in as "admin"
    And I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"

    When I click on "Assignments" "link"
    And I click on "Open-ended" "button"
    And I set the "scheduleFixed[from]" tui date selector to "-1 day"
    And I set the "scheduleFixed[from]" tui date selector timezone to "UTC"
    And I save the activity schedule
    And I click on "Confirm" "button"
    And I wait until the page is ready
    Then I should see "Changes applied and activity has been updated" in the tui success notification toast

    When I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"

    Then the "scheduleFixed[from]" tui date selector should be set to "-1 day"
    And the "scheduleFixed[from]" tui date selector timezone should be set to "UTC"

  Scenario: Check validation messages of fixed activity schedule
    Given I log in as "admin"
    And I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    And I click on "Limited" "button"

    When I set the "scheduleFixed[from]" tui date selector to "-1 day"
    And I set the "scheduleFixed[from]" tui date selector timezone to "UTC"
    And I set the "scheduleFixed[to]" tui date selector to "-2 days"
    And I save the activity schedule
    Then I should see "Range end date cannot be before range start date"

     # Make sure the validation for limited range doesn't apply (this used to be a bug).
    When I click on "Limited" "button"
    And I set the "scheduleFixed[from]" tui date selector to "-10 days"
    And I set the "scheduleFixed[to]" tui date selector to "-30 days"
    And I click on "Open-ended" "button"
    And I save the activity schedule
    And I click on "Confirm" "button"
    And I wait until the page is ready
    Then I should see "Changes applied and activity has been updated" in the tui success notification toast

    When I close the tui notification toast
    And I click on "Limited" "button"
    And I set the "scheduleFixed[from]" tui date selector to "-1 day"
    And I set the "scheduleFixed[to]" tui date selector to "-1 day"
    And I save the activity schedule
    And I click on "Confirm" "button"
    And I wait until the page is ready
    Then I should see "Changes applied and activity has been updated" in the tui success notification toast

  Scenario: Save and view limited dynamic performance activity schedule
    Given I log in as "admin"
    And I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    And I click on "Limited" "button"
    And I click on "Relative" "button"
    And I click on the "AFTER" tui radio in the "scheduleDynamic[fromDirection]" tui radio group
    And I click on the "AFTER" tui radio in the "scheduleDynamic[toDirection]" tui radio group
    And I set the following fields to these values:
      | scheduleDynamic[fromOffset][after][value] | 1                  |
      | scheduleDynamic[fromOffset][after][range] | weeks              |
      | scheduleDynamic[toOffset][after][value]   | 4                  |
      | scheduleDynamic[toOffset][after][range]   | weeks              |
      | scheduleDynamic[dynamic_source]           | User creation date |
    When I save the activity schedule
    And I click on "Confirm" "button"
    And I wait until the page is ready
    Then I should see "Changes applied and activity has been updated" in the tui success notification toast

    When I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    And I click on the "AFTER" tui radio in the "scheduleDynamic[fromDirection]" tui radio group
    And I click on the "AFTER" tui radio in the "scheduleDynamic[toDirection]" tui radio group
    Then the following fields match these values:
      | scheduleDynamic[fromOffset][after][value]  | 1        |
      | scheduleDynamic[fromOffset][after][range]  | weeks    |
      | scheduleDynamic[toOffset][after][value]    | 4        |
      | scheduleDynamic[toOffset][after][range]    | weeks    |
      | scheduleDynamic[dynamic_source]            | User creation date |

  Scenario: Save and view open ended dynamic performance activity schedule
    Given I log in as "admin"
    And I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    And I click on "Relative" "button"
    And I click on the "AFTER" tui radio in the "scheduleDynamic[fromDirection]" tui radio group
    And I set the following fields to these values:
      | scheduleDynamic[fromOffset][after][value]   | 1                  |
      | scheduleDynamic[fromOffset][after][range]   | weeks              |
      | scheduleDynamic[dynamic_source]             | User creation date |
    And I click on the "scheduleDynamic[useAnniversary]" tui checkbox
    Then "input[name='scheduleDynamic[toDirection]']" "css_element" should not exist in the ".tui-assignmentScheduleCreationRange__form" "css_element"

    When I save the activity schedule
    And I click on "Confirm" "button"
    And I wait until the page is ready
    Then I should see "Changes applied and activity has been updated" in the tui success notification toast

    When I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    And I click on the "AFTER" tui radio in the "scheduleDynamic[fromDirection]" tui radio group
    Then the following fields match these values:
      | scheduleDynamic[fromOffset][after][value]   | 1                  |
      | scheduleDynamic[fromOffset][after][range]   | weeks              |
      | scheduleDynamic[dynamic_source]             | User creation date |
      | scheduleDynamic[useAnniversary]             | 1                  |
    And "input[name='scheduleDynamic[toDirection]']" "css_element" should not exist in the ".tui-assignmentScheduleCreationRange__form" "css_element"

  Scenario: Check remembered toggling between dynamic options
    Given I log in as "admin"
    And I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    And I click on "Relative" "button"
    And I click on "Open-ended" "button"

    And I click on the "BEFORE" tui radio in the "scheduleDynamic[fromDirection]" tui radio group
    And I set the following fields to these values:
      | scheduleDynamic[fromOffset][before][value] | 100                |
      | scheduleDynamic[dynamic_source]            | User creation date |
    When I save the activity schedule
    And I click on "Confirm" "button"
    And I wait until the page is ready
    Then I should see "Changes applied and activity has been updated" in the tui success notification toast

    When I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    And I click on "Limited" "button"
    Then the following fields match these values:
      | scheduleDynamic[fromOffset][before][value] | 100 |
      | scheduleDynamic[toOffset][before][value]   | 1   |

    Then I save the activity schedule
    And I click on "Confirm" "button"

  Scenario: Check validation messages of dynamic activity schedule
    Given I log in as "admin"
    And I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    And I click on "Limited" "button"
    And I click on "Relative" "button"

    When I set the following fields to these values:
      | scheduleDynamic[fromOffset][before][value] | 0.3 |
      | scheduleDynamic[toOffset][before][value] | 0.4 |
    And I save the activity schedule
    Then I should see "Please enter a valid whole number"

    When I set the following fields to these values:
      | scheduleDynamic[fromOffset][before][value] | |
      | scheduleDynamic[toOffset][before][value]   | |
    Then I should see "Number must be 1 or more"

    When I set the following fields to these values:
      | scheduleDynamic[fromOffset][before][value] | -1 |
      | scheduleDynamic[toOffset][before][value]   | -1 |
    Then I should see "Number must be 1 or more"

    When I click on the "AFTER" tui radio in the "scheduleDynamic[fromDirection]" tui radio group
    And I click on the "AFTER" tui radio in the "scheduleDynamic[toDirection]" tui radio group
    And I set the following fields to these values:
      | scheduleDynamic[fromOffset][after][value]     | 100                |
      | scheduleDynamic[toOffset][after][value]       | 10                 |
      | scheduleDynamic[dynamic_source] | User creation date |
    And I save the activity schedule
    Then I should see "Range end date cannot be before range start date"

    When I click on the "BEFORE" tui radio in the "scheduleDynamic[fromDirection]" tui radio group
    And I click on the "BEFORE" tui radio in the "scheduleDynamic[toDirection]" tui radio group
    When I set the following fields to these values:
      | scheduleDynamic[fromOffset][before][value]     | 10                 |
      | scheduleDynamic[toOffset][before][value]       | 100                |
      | scheduleDynamic[dynamic_source]                | User creation date |
    And I save the activity schedule
    Then I should see "Range end date cannot be before range start date"

    When I click on "Open-ended" "button"
    And I set the following fields to these values:
      | scheduleDynamic[fromOffset][before][value] |  |
    Then I should see "Number must be 1 or more"

    When I set the following fields to these values:
      | scheduleDynamic[fromOffset][before][value] | -1 |
    Then I should see "Number must be 1 or more"

    When I set the following fields to these values:
      | scheduleDynamic[fromOffset][before][value] | 1 |
    Then I should not see the "Number must be 1 or more" block

    When I save the activity schedule
    And I click on "Confirm" "button"
    And I wait until the page is ready
    Then I should see "Changes applied and activity has been updated" in the tui success notification toast

  Scenario: Check remembered toggling between fixed and dynamic options
    Given I log in as "admin"
    And I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    And I click on "Limited" "button"
    Then I set the "scheduleFixed[from]" tui date selector to "-1 day"
    And I set the "scheduleFixed[to]" tui date selector to "+1 day"

    When I click on "Relative" "button"
    And I click on the "AFTER" tui radio in the "scheduleDynamic[fromDirection]" tui radio group
    And I click on the "AFTER" tui radio in the "scheduleDynamic[toDirection]" tui radio group
    And I set the following fields to these values:
      | scheduleDynamic[fromOffset][after][value]    | 1                  |
      | scheduleDynamic[fromOffset][after][range]     | weeks              |
      | scheduleDynamic[toOffset][after][value]       | 4                  |
      | scheduleDynamic[toOffset][after][range]       | weeks              |
      | scheduleDynamic[dynamic_source]               | User creation date |
    And I click on the "scheduleDynamic[useAnniversary]" tui checkbox

    When I click on "Fixed" "button"

    Then the "scheduleFixed[from]" tui date selector should be set to "-1 day"
    And the "scheduleFixed[to]" tui date selector should be set to "+1 day"

    When I click on "Relative" "button"
    Then the following fields match these values:
      | scheduleDynamic[fromOffset][after][value]  | 1                  |
      | scheduleDynamic[fromOffset][after][range]  | weeks              |
      | scheduleDynamic[toOffset][after][value]    | 4                  |
      | scheduleDynamic[toOffset][after][range]    | weeks              |
      | scheduleDynamic[dynamic_source]            | User creation date |
      | scheduleDynamic[useAnniversary]            | 1                  |

    Then I save the activity schedule
    And I click on "Confirm" "button"

  Scenario: Check due date is disabled by default and can be enabled
    Given I log in as "admin"
    And I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    Then I should see "Due date disabled"
    And I should see "Participants are not expected to submit their responses by a certain date."
    When I click on "Enabled" "button"
    Then I should see "Due date enabled"
    And I save the activity schedule
    And I click on "Confirm" "button"
    And I wait until the page is ready
    Then I should see "Changes applied and activity has been updated" in the tui success notification toast
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
    And I set the "scheduleFixed[from]" tui date selector to "-1 day"
    And I set the following fields to these values:
      | dueDateOffset[value] | 0 |
    And I save the activity schedule
    Then I should see "Due date must be after the creation end date"
    And the following fields match these values:
      | dueDateOffset[value] | 0 |
    When I set the following fields to these values:
      | dueDateOffset[value] | 1 |
    And I save the activity schedule
    And I click on "Confirm" "button"
    And I wait until the page is ready
    Then I should see "Changes applied and activity has been updated" in the tui success notification toast
    When I reload the page
    And I click on "Assignments" "link"
    Then the following fields match these values:
      | dueDateOffset[value] | 1 |

    # Open & Dynamic
    When I click on "Open-ended" "button"
    And I click on "Relative" "button"
    And I click on the "AFTER" tui radio in the "scheduleDynamic[fromDirection]" tui radio group
    And I set the following fields to these values:
      | scheduleDynamic[fromOffset][after][value]  | 3                  |
      | scheduleDynamic[fromOffset][after][range]  | weeks              |
      | scheduleDynamic[dynamic_source]            | User creation date |
      | dueDateOffset[value]                       | 0                  |
    And I save the activity schedule
    Then I should see "Due date must be after the creation end date"
    When I set the following fields to these values:
      | dueDateOffset[value] | 2 |
    And I save the activity schedule
    And I click on "Confirm" "button"
    And I wait until the page is ready
    Then I should see "Changes applied and activity has been updated" in the tui success notification toast
    When I reload the page
    And I click on "Assignments" "link"
    Then the following fields match these values:
      | dueDateOffset[value] | 2 |

    # Limited & Dynamic
    When I click on "Limited" "button"
    And I click on "Relative" "button"
    And I click on the "AFTER" tui radio in the "scheduleDynamic[fromDirection]" tui radio group
    And I click on the "AFTER" tui radio in the "scheduleDynamic[toDirection]" tui radio group
    And I set the following fields to these values:
      | scheduleDynamic[fromOffset][after][value]  | 3                  |
      | scheduleDynamic[fromOffset][after][range]  | weeks              |
      | scheduleDynamic[toOffset][after][value]    | 4                  |
      | scheduleDynamic[toOffset][after][range]    | weeks              |
      | scheduleDynamic[dynamic_source]            | User creation date |
      | dueDateOffset[value]                  | 0                  |
    And I save the activity schedule
    Then I should see "Due date must be after the creation end date"
    When I set the following fields to these values:
      | dueDateOffset[value] | 3 |
    And I save the activity schedule
    And I click on "Confirm" "button"
    And I wait until the page is ready
    Then I should see "Changes applied and activity has been updated" in the tui success notification toast
    When I reload the page
    And I click on "Assignments" "link"
    Then the following fields match these values:
      | dueDateOffset[value] | 3 |

    # Limited & Fixed
    When I click on "Limited" "button"
    And I click on "Fixed" "button"
    When I set the "scheduleFixed[from]" tui date selector to "-1 day"
    And I set the "scheduleFixed[to]" tui date selector to "+ 1 day"
    And I click on the "fixed" tui radio in the "dueDateType" tui radio group
    And I set the "fixedDueDate" tui date selector to "+1 day"
    And I save the activity schedule
    Then I should see "Due date must be after the creation end date"
    When I set the "fixedDueDate" tui date selector to "+2 day"
    And I save the activity schedule
    And I click on "Confirm" "button"
    And I wait until the page is ready
    Then I should see "Changes applied and activity has been updated" in the tui success notification toast
    When I reload the page
    And I click on "Assignments" "link"

    When I set the "scheduleFixed[from]" tui date selector to "-1 day"
    And I set the "scheduleFixed[to]" tui date selector to "+1 day"
      | dueDateType  | fixed |
    And I set the "fixedDueDate" tui date selector to "+1 day"
    And I click on the "relative" tui radio in the "dueDateType" tui radio group
    And I set the following fields to these values:
      | dueDateOffset[relative][value] | 0 |
    And I save the activity schedule
    Then I should see "Due date must be after the creation end date"
    When I set the following fields to these values:
      | dueDateOffset[relative][value] | 4 |
    And I save the activity schedule
    And I click on "Confirm" "button"
    And I wait until the page is ready
    Then I should see "Changes applied and activity has been updated" in the tui success notification toast
    When I reload the page
    And I click on "Assignments" "link"
    Then the following fields match these values:
      | dueDateType                    | relative |
      | dueDateOffset[relative][value] | 4        |

  Scenario: Check job assignment-based additional schedule settings
    Given I log in as "admin"
    And I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    And I should see "Job assignment-based instances"
    When I click on the "ONE_PER_SUBJECT" tui radio in the "additionalSettings[subjectInstanceGeneration]" tui radio group
    And I save the activity schedule
    And I click on "Confirm" "button"
    And I wait until the page is ready
    Then I should see "Changes applied and activity has been updated" in the tui success notification toast
    When I reload the page
    And I click on "Assignments" "link"
    Then the following fields match these values:
      | additionalSettings[subjectInstanceGeneration] | ONE_PER_SUBJECT |

  Scenario: Check repeating is disabled by default and can be enabled
    Given I log in as "admin"
    And I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    Then I should see "Frequency: Once"
    And I should see "Users will receive a maximum of 1 instance each (or maximum of 1 per job, depending on job assignment setting)"
    When I click on "Repeating" "button"
    Then I should see "Frequency: Repeating"
    When I save the activity schedule
    And I click on "Confirm" "button"
    And I wait until the page is ready
    Then I should see "Changes applied and activity has been updated" in the tui success notification toast
    And I reload the page
    And I click on "Assignments" "link"
    Then I should see "Frequency: Repeating"

  Scenario: Save and view repeating performance activity schedule
    Given I log in as "admin"
    And I navigate to the manage perform activities page
    And I click on "My Test Activity" "link"
    And I click on "Assignments" "link"
    And I click on "Repeating" "button"

    And I click on the "completeOffset" tui radio in the "repeatingValues[repeatingType]" tui radio group
    # When setting these fields in a single step, the count is not set - probably due to timing issues
    And I set the following fields to these values:
      | repeatingValues[repeatingOffset][completeOffset][value] | 2     |
      | repeatingValues[repeatingOffset][completeOffset][range] | weeks |
    And I save the activity schedule
    And I click on "Confirm" "button"
    And I wait until the page is ready
    Then I should see "Changes applied and activity has been updated" in the tui success notification toast

    When I reload the page
    And I click on "Assignments" "link"
    # The radio group for repeatingType is special, it does not have a proper label
    # to identify it with.
    Then the following fields match these values:
      | repeatingValues[repeatingOffset][completeOffset][value] | 2     |
      | repeatingValues[repeatingOffset][completeOffset][range] | weeks |

    And I click on the "true" tui radio in the "repeatingValues[repeatingIsLimited]" tui radio group
    And I set the following fields to these values:
      | repeatingValues[repeatingLimit][value] | 4 |
    And I save the activity schedule
    And I click on "Confirm" "button"
    And I wait until the page is ready
    Then I should see "Changes applied and activity has been updated" in the tui success notification toast

    When I reload the page
    And I click on "Assignments" "link"
    # The radio group for repeatingType is special, it does not have a proper label
    # to identify it with.
    Then the following fields match these values:
      | repeatingValues[repeatingLimit][value] | 4    |

    # Limited / Open-ended display text
    When I click on "Limited" "button"
    Then I should see "none (repeat until creation period ends)"
    When I click on "Open-ended" "button"
    Then I should see "none (repeat indefinitely)"

    Then I save the activity schedule
    And I click on "Confirm" "button"

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
    And I click on "Relative" "button"
    And I click on the "AFTER" tui radio in the "scheduleDynamic[fromDirection]" tui radio group
    And I set the following fields to these values:
      | scheduleDynamic[fromOffset][after][value]  | 3                  |
      | scheduleDynamic[fromOffset][after][range]  | weeks              |
      | scheduleDynamic[dynamic_source] | Date one |
    And I save the activity schedule
    And I click on "Confirm" "button"
    And I wait until the page is ready
    Then I should see "Changes applied and activity has been updated" in the tui success notification toast

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
    And I click on "Relative" "button"
    And I click on the "AFTER" tui radio in the "scheduleDynamic[fromDirection]" tui radio group
    And I set the following fields to these values:
      | scheduleDynamic[fromOffset][after][value]        | 3                                   |
      | scheduleDynamic[fromOffset][after][range]        | weeks                               |
      | scheduleDynamic[dynamic_source]                  | Completion date of another activity |
      | scheduleDynamic[dynamicCustomSettings][activity] | Activity two                        |
    And I save the activity schedule
    And I click on "Confirm" "button"
    And I wait until the page is ready
    Then I should see "Changes applied and activity has been updated" in the tui success notification toast

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
    And I click on "Relative" "button"
    And I click on the "AFTER" tui radio in the "scheduleDynamic[fromDirection]" tui radio group
    And I set the following fields to these values:
      | scheduleDynamic[fromOffset][after][value]        | 2                                   |
      | scheduleDynamic[fromOffset][after][range]        | weeks                               |
      | scheduleDynamic[dynamic_source]                  | Job assignment start date           |
    And I save the activity schedule
    Then I should see "This setting cannot be disabled while “Job assignment start date” is in use"
    When I click on the "Enabled" tui radio in the "subjectInstanceGeneration" tui radio group
    And I save the activity schedule
    And I click on "Confirm" "button"
    And I wait until the page is ready
    Then I should see "Changes applied and activity has been updated" in the tui success notification toast
    When I reload the page
    And I click on "Assignments" "link"
    Then the following fields match these values:
      | scheduleDynamic[dynamic_source]                  | Job assignment start date |
