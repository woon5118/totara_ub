@totara @perform @mod_perform @mod_perform_notification @javascript @vuejs
Feature: Perform activity notifications
  Background:
    Given I am on a totara site
    And the following "users" exist:
      | username  | firstname | lastname | email                 |
      | user1     | User      | One      |     user1@example.com |
      | user2     | User      | Two      |     user2@example.com |
      | user3     | User      | Three    |     user3@example.com |
      | manager   | Mana      | Ger      |   manager@example.com |
      | appraiser | Appra     | Iser     | appraiser@example.com |
    And the following job assignments exist:
      | user      | manager | appraiser |
      | user1     | manager | appraiser |
      | user2     | manager |           |
    And the following "cohorts" exist:
      | name | idnumber |
      | aud1 | aud1     |
    And the following "cohort members" exist:
      | user  | cohort |
      | user1 | aud1   |
      | user3 | aud1   |
    And the following "activities" exist in "mod_perform" plugin:
      | activity_name | activity_type | activity_status | create_section | create_track |
      | Activity test | feedback      | Draft           | false          | false        |
    And the following "activity sections" exist in "mod_perform" plugin:
      | activity_name | section_name |
      | Activity test | section 1    |
    And the following "section relationships" exist in "mod_perform" plugin:
      | section_name | relationship |
      | section 1    | subject      |
      | section 1    | manager      |
      | section 1    | appraiser    |
    And the following "section elements" exist in "mod_perform" plugin:
      | section_name | element_name |
      | section 1    | short_text   |
    And the following "activity tracks" exist in "mod_perform" plugin:
      | activity_name | track_description | due_date_offset |
      | Activity test | track 1           | 2, WEEK         |
    And the following "track assignments" exist in "mod_perform" plugin:
      | track_description | assignment_type | assignment_name |
      | track 1           | cohort          | aud1            |
    And the following "language customisation" exist in "tool_customlang" plugin:
      | component   | id                                                   | string                               |
      | mod_perform | template_instance_created_subject_subject            | New activity notice                  |
      | mod_perform | template_instance_created_reminder_subject_subject   | Te manatu mō te whakarite            |
      | mod_perform | template_due_date_reminder_subject_subject           | Si avvicina la scadenza              |
      | mod_perform | template_due_date_subject_subject                    | Notificación de fecha de vencimiento |
      | mod_perform | template_overdue_reminder_subject_subject            | Försenad påminnelse                  |
      # | mod_perform | template_completion_subject_subject                  | Ukončení činnosti                    |
      | mod_perform | template_instance_created_appraiser_subject          | Nuwe aktiwiteitskennisgewing         |
      | mod_perform | template_instance_created_reminder_appraiser_subject | Herinnering aan activiteit           |
      | mod_perform | template_due_date_reminder_appraiser_subject         | A határidő közeledik                 |
      | mod_perform | template_due_date_appraiser_subject                  | Iraungitze data jakinaraztea         |
      | mod_perform | template_overdue_reminder_appraiser_subject          | Spomenuté oneskorenie                |
      # | mod_perform | template_completion_appraiser_subject                | Faaliyetin tamamlanması              |
    And I log in as "admin"
    And I navigate to the manage perform activities page
    And I follow "Activity test"
    And I switch to "Notifications" tui tab

  Scenario: mod_perform_notification_001: Instance creation notification
    And I toggle the "Participant instance creation" tui collapsible
    And I click on the "Participant instance creation notification" tui toggle button
    And I click on "Subject" tui "toggle_button" in the "Participant instance creation" tui "collapsible"
    And I click on "Appraiser" tui "toggle_button" in the "Participant instance creation" tui "collapsible"
    And I click on "Activate" "button" in the ".tui-actionCard" "css_element"
    And I confirm the tui confirmation modal
    And I wait "1" seconds
    And I trigger cron
    And I press the "back" button in the browser
    And I log out
    When I log in as "user1"
    And I open the notification popover
    Then I should see "New activity notice" exactly "1" times
    And I log out

    # appraiser should receive as many notifications as user1 does
    When I log in as "appraiser"
    And I open the notification popover
    Then I should see "Nuwe aktiwiteitskennisgewing" exactly "1" times
    And I log out

    # user2 should not receive any notifications
    When I log in as "user2"
    And I open the notification popover
    Then I should see "You have no notifications"
    And I log out

    # manager should not receive any notifications
    When I log in as "manager"
    And I open the notification popover
    Then I should see "You have no notifications"

  Scenario: mod_perform_notification_002: Instance creation reminder
    And I toggle the "Participant instance creation reminder" tui collapsible
    And I click on the "Participant instance creation reminder notification" tui toggle button
    And I click on "Subject" tui "toggle_button" in the "Participant instance creation reminder" tui "collapsible"
    And I click on "Appraiser" tui "toggle_button" in the "Participant instance creation reminder" tui "collapsible"
    And I click on "Add" tui "button" in the "Participant instance creation reminder" tui "collapsible"
    And I click on "Add" tui "button" in the "Participant instance creation reminder" tui "collapsible"
    And I click on "Add" tui "button" in the "Participant instance creation reminder" tui "collapsible"
    And I click on "Add" tui "button" in the "Participant instance creation reminder" tui "collapsible"
    And I set the following fields to these values:
      | trigger-instance_created_reminder[0] | 1 |
      | trigger-instance_created_reminder[1] | 2 |
      | trigger-instance_created_reminder[2] | 3 |
      | trigger-instance_created_reminder[3] | 4 |
      | trigger-instance_created_reminder[4] | 6 |
    And I should see "Activity saved" in the tui "success" notification banner
    And I close the tui notification toast
    And I click on "Activate" "button" in the ".tui-actionCard" "css_element"
    And I confirm the tui confirmation modal
    And I wait "1" seconds
    And I trigger cron
    And I press the "back" button in the browser

    # Delete an event trigger after activation
    And I switch to "Notifications" tui tab
    And I click on ".tui-repeater__row:nth-child(3) button[title=Delete]" "css_element"
    And I should see "Activity saved" in the tui "success" notification banner
    And I close the tui notification toast
    And I log out

    # 0 day 0 hour
    When I log in as "user1"
    And I open the notification popover
    Then I should see "You have no notifications"

    # 0 day 23 hour
    Given I time travel to "23 hours future" for perform activity notification
    And I reload the page
    And I open the notification popover
    Then I should see "You have no notifications"

    # 1 day 1 hour
    Given I time travel to "2 hours future" for perform activity notification
    And I reload the page
    And I open the notification popover
    Then I should see "Te manatu mō te whakarite" exactly "1" times

    # 1 day 23 hour
    Given I time travel to "22 hours future" for perform activity notification
    And I reload the page
    And I open the notification popover
    Then I should see "Te manatu mō te whakarite" exactly "1" times

    # 2 day 1 hour
    Given I time travel to "2 hours future" for perform activity notification
    And I reload the page
    And I open the notification popover
    Then I should see "Te manatu mō te whakarite" exactly "2" times

    # 2 day 23 hour
    Given I time travel to "22 hours future" for perform activity notification
    And I reload the page
    And I open the notification popover
    Then I should see "Te manatu mō te whakarite" exactly "2" times

    # 3 day 23 hour
    Given I time travel to "1 day future" for perform activity notification
    And I reload the page
    And I open the notification popover
    Then I should see "Te manatu mō te whakarite" exactly "2" times

    # 5 day 1 hour (notification is not sent on day 4 because cron is not run)
    Given I time travel to "26 hours future" for perform activity notification
    And I reload the page
    # And pause to check the time for perform activity notification
    And I open the notification popover
    Then I should see "Te manatu mō te whakarite" exactly "2" times

    # 6 day 1 hour
    Given I time travel to "1 day future" for perform activity notification
    And I reload the page
    And I open the notification popover
    Then I should see "Te manatu mō te whakarite" exactly "3" times
    And I log out

    # appraiser should receive as many notifications as user1 does
    When I log in as "appraiser"
    And I open the notification popover
    Then I should see "Herinnering aan activiteit" exactly "3" times
    And I log out

    # user2 should not receive any notifications
    When I log in as "user2"
    And I open the notification popover
    Then I should see "You have no notifications"
    And I log out

    # manager should not receive any notifications
    When I log in as "manager"
    And I open the notification popover
    Then I should see "You have no notifications"

  Scenario: mod_perform_notification_003: Due date approaching reminder
    And I toggle the "Due date approaching reminder" tui collapsible
    And I click on the "Due date approaching reminder notification" tui toggle button
    And I click on "Subject" tui "toggle_button" in the "Due date approaching reminder" tui "collapsible"
    And I click on "Appraiser" tui "toggle_button" in the "Due date approaching reminder" tui "collapsible"
    And I click on "Add" tui "button" in the "Due date approaching reminder" tui "collapsible"
    And I click on "Add" tui "button" in the "Due date approaching reminder" tui "collapsible"
    And I click on "Add" tui "button" in the "Due date approaching reminder" tui "collapsible"
    And I click on "Add" tui "button" in the "Due date approaching reminder" tui "collapsible"
    And I set the following fields to these values:
      | trigger-due_date_reminder[0] | 1 |
      | trigger-due_date_reminder[1] | 2 |
      | trigger-due_date_reminder[2] | 3 |
      | trigger-due_date_reminder[3] | 4 |
      | trigger-due_date_reminder[4] | 6 |
    And I should see "Activity saved" in the tui "success" notification banner
    And I close the tui notification toast
    # Explicitly check the due date setting in case someone messes up with the generator
    When I switch to "Assignments" tui tab
    Then the following fields match these values:
      | dueDateOffset[from_count] | 2     |
      | dueDateOffset[from_unit]  | weeks |
    And I click on "Activate" "button" in the ".tui-actionCard" "css_element"
    And I confirm the tui confirmation modal
    And I wait "1" seconds
    And I trigger cron
    And I press the "back" button in the browser

    # Delete an event trigger after activation
    And I switch to "Notifications" tui tab
    And I click on ".tui-repeater__row:nth-child(3) button[title=Delete]" "css_element"
    And I should see "Activity saved" in the tui "success" notification banner
    And I close the tui notification toast
    And I log out

    # day 0
    Given I time travel to "1 hour future" for perform activity notification
    When I log in as "user1"
    And I open the notification popover
    Then I should see "You have no notifications"

    # day 5
    Given I time travel to "5 days future" for perform activity notification
    And I reload the page
    And I open the notification popover
    Then I should see "You have no notifications"

    # day 7
    Given I time travel to "2 days future" for perform activity notification
    And I reload the page
    And I open the notification popover
    Then I should see "You have no notifications"

    # day 8 (6 days before due)
    Given I time travel to "1 day future" for perform activity notification
    And I reload the page
    And I open the notification popover
    # And pause to check the time for perform activity notification
    Then I should see "Si avvicina la scadenza" exactly "1" times

    # day 9 (5 days before due)
    Given I time travel to "1 day future" for perform activity notification
    And I reload the page
    And I open the notification popover
    Then I should see "Si avvicina la scadenza" exactly "1" times

    # day 10 (4 days before due)
    Given I time travel to "1 day future" for perform activity notification
    And I reload the page
    And I open the notification popover
    Then I should see "Si avvicina la scadenza" exactly "2" times

    # day 11 (3 days before due)
    Given I time travel to "1 day future" for perform activity notification
    And I reload the page
    And I open the notification popover
    Then I should see "Si avvicina la scadenza" exactly "2" times

    # day 12 (2 days before due)
    Given I time travel to "1 day future" for perform activity notification
    And I reload the page
    And I open the notification popover
    Then I should see "Si avvicina la scadenza" exactly "3" times

    # day 13 (1 days before due)
    Given I time travel to "1 day future" for perform activity notification
    And I reload the page
    And I open the notification popover
    Then I should see "Si avvicina la scadenza" exactly "4" times

    # day 14 (due date)
    Given I time travel to "1 days future" for perform activity notification
    And I reload the page
    And I open the notification popover
    Then I should see "Si avvicina la scadenza" exactly "4" times

    # day 15 (overdue)
    Given I time travel to "1 days future" for perform activity notification
    And I reload the page
    And I open the notification popover
    Then I should see "Si avvicina la scadenza" exactly "4" times
    And I log out

    # appraiser should receive as many notifications as user1 does
    When I log in as "appraiser"
    And I open the notification popover
    Then I should see "A határidő közeledik" exactly "4" times
    And I log out

    # user2 should not receive any notifications
    When I log in as "user2"
    And I open the notification popover
    Then I should see "You have no notifications"
    And I log out

    # manager should not receive any notifications
    When I log in as "manager"
    And I open the notification popover
    Then I should see "You have no notifications"

  Scenario: mod_perform_notification_004: On due date reminder
    And I toggle the "On due date reminder" tui collapsible
    And I click on the "On due date reminder notification" tui toggle button
    And I click on "Subject" tui "toggle_button" in the "On due date reminder" tui "collapsible"
    And I click on "Appraiser" tui "toggle_button" in the "On due date reminder" tui "collapsible"
    # Explicitly check the due date setting in case someone messes up with the generator
    When I switch to "Assignments" tui tab
    Then the following fields match these values:
      | dueDateOffset[from_count] | 2     |
      | dueDateOffset[from_unit]  | weeks |
    And I click on "Activate" "button" in the ".tui-actionCard" "css_element"
    And I confirm the tui confirmation modal
    And I trigger cron
    And I press the "back" button in the browser
    And I log out

    # day 0
    Given I time travel to "1 hour future" for perform activity notification
    When I log in as "user1"
    And I open the notification popover
    Then I should see "You have no notifications"

    # day 2
    Given I time travel to "2 days future" for perform activity notification
    And I reload the page
    And I open the notification popover
    Then I should see "You have no notifications"

    # day 5
    Given I time travel to "3 days future" for perform activity notification
    And I reload the page
    And I open the notification popover
    Then I should see "You have no notifications"

    # day 10
    Given I time travel to "5 days future" for perform activity notification
    And I reload the page
    And I open the notification popover
    Then I should see "You have no notifications"

    # day 12 (2 day before due)
    Given I time travel to "2 days future" for perform activity notification
    And I reload the page
    And I open the notification popover
    Then I should see "You have no notifications"

    # day 13, 23:00 (1 day before due)
    Given I time travel to "midnight future" for perform activity notification
    And I time travel to "23 hours future" for perform activity notification
    # And pause to check the time for perform activity notification
    And I reload the page
    And I open the notification popover
    Then I should see "You have no notifications"

    # day 14, 01:00 (due date)
    Given I time travel to "2 hours future" for perform activity notification
    And I reload the page
    And I open the notification popover
    Then I should see "Notificación de fecha de vencimiento" exactly "1" times

    # day 15 (overdue)
    Given I time travel to "1 days future" for perform activity notification
    And I reload the page
    And I open the notification popover
    Then I should see "Notificación de fecha de vencimiento" exactly "1" times

    # day 365 (overdue)
    Given I time travel to "350 days future" for perform activity notification
    And I reload the page
    And I open the notification popover
    Then I should see "Notificación de fecha de vencimiento" exactly "1" times
    And I log out

    # appraiser should receive as many notifications as user1 does
    When I log in as "appraiser"
    And I open the notification popover
    Then I should see "Iraungitze data jakinaraztea" exactly "1" times
    And I log out

    # user2 should not receive any notifications
    When I log in as "user2"
    And I open the notification popover
    Then I should see "You have no notifications"
    And I log out

    # manager should not receive any notifications
    When I log in as "manager"
    And I open the notification popover
    Then I should see "You have no notifications"

  Scenario: mod_perform_notification_005: Overdue reminder
    And I toggle the "Overdue reminder" tui collapsible
    And I click on the "Overdue reminder notification" tui toggle button
    And I click on "Subject" tui "toggle_button" in the "Overdue reminder" tui "collapsible"
    And I click on "Appraiser" tui "toggle_button" in the "Overdue reminder" tui "collapsible"
    And I click on "Add" tui "button" in the "Overdue reminder" tui "collapsible"
    And I click on "Add" tui "button" in the "Overdue reminder" tui "collapsible"
    And I click on "Add" tui "button" in the "Overdue reminder" tui "collapsible"
    And I click on "Add" tui "button" in the "Overdue reminder" tui "collapsible"
    And I set the following fields to these values:
      | trigger-overdue_reminder[0] | 1 |
      | trigger-overdue_reminder[1] | 2 |
      | trigger-overdue_reminder[2] | 3 |
      | trigger-overdue_reminder[3] | 4 |
      | trigger-overdue_reminder[4] | 6 |
    And I should see "Activity saved" in the tui "success" notification banner
    And I close the tui notification toast
    # Explicitly check the due date setting in case someone messes up with the generator
    When I switch to "Assignments" tui tab
    Then the following fields match these values:
      | dueDateOffset[from_count] | 2     |
      | dueDateOffset[from_unit]  | weeks |
    And I click on "Activate" "button" in the ".tui-actionCard" "css_element"
    And I confirm the tui confirmation modal
    And I wait "1" seconds
    And I trigger cron
    And I press the "back" button in the browser

    # Delete an event trigger after activation
    And I switch to "Notifications" tui tab
    And I click on ".tui-repeater__row:nth-child(3) button[title=Delete]" "css_element"
    And I should see "Activity saved" in the tui "success" notification banner
    And I close the tui notification toast
    And I log out

    # day 0
    Given I time travel to "1 hour future" for perform activity notification
    When I log in as "user1"
    And I open the notification popover
    Then I should see "You have no notifications"

    # day 13 (1 day  before due)
    Given I time travel to "13 days future" for perform activity notification
    And I reload the page
    And I open the notification popover
    Then I should see "You have no notifications"

    # day 14 (due date)
    Given I time travel to "1 days future" for perform activity notification
    And I reload the page
    And I open the notification popover
    Then I should see "You have no notifications"

    # day 15 (1 day overdue)
    Given I time travel to "1 days future" for perform activity notification
    And I reload the page
    And I open the notification popover
    Then I should see "Försenad påminnelse" exactly "1" times

    # day 16 (2 day overdue)
    Given I time travel to "1 days future" for perform activity notification
    And I reload the page
    And I open the notification popover
    Then I should see "Försenad påminnelse" exactly "2" times

    # day 17 (3 day overdue)
    Given I time travel to "1 days future" for perform activity notification
    And I reload the page
    And I open the notification popover
    Then I should see "Försenad påminnelse" exactly "2" times

    # day 18 (4 day overdue)
    Given I time travel to "1 days future" for perform activity notification
    And I reload the page
    And I open the notification popover
    Then I should see "Försenad påminnelse" exactly "3" times

    # day 19 (5 day overdue)
    Given I time travel to "1 days future" for perform activity notification
    And I reload the page
    And I open the notification popover
    Then I should see "Försenad påminnelse" exactly "3" times

    # day 20 (6 day overdue)
    Given I time travel to "1 days future" for perform activity notification
    And I reload the page
    And I open the notification popover
    Then I should see "Försenad påminnelse" exactly "4" times

    # day 21 (7 day overdue)
    Given I time travel to "1 days future" for perform activity notification
    And I reload the page
    And I open the notification popover
    Then I should see "Försenad påminnelse" exactly "4" times
    And I log out

    # appraiser should receive as many notifications as user1 does
    When I log in as "appraiser"
    And I open the notification popover
    Then I should see "Spomenuté oneskorenie" exactly "4" times
    And I log out

    # user2 should not receive any notifications
    When I log in as "user2"
    And I open the notification popover
    Then I should see "You have no notifications"
    And I log out

    # manager should not receive any notifications
    When I log in as "manager"
    And I open the notification popover
    Then I should see "You have no notifications"
