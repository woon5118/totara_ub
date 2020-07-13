@totara @perform @mod_perform @javascript @vuejs
Feature: Perform activity notifications
  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email             |
      | user1    | user      | 1        | user1@example.com |
      | user2    | user      | 2        | user2@example.com |
    And the following "cohorts" exist:
      | name | idnumber |
      | aud1 | aud1     |
    And the following "cohort members" exist:
      | user  | cohort |
      | user1 | aud1   |
    And the following "activities" exist in "mod_perform" plugin:
      | activity_name | activity_type | activity_status | create_section | create_track |
      | Activity test | feedback      | Draft           | false          | false        |
    And the following "activity sections" exist in "mod_perform" plugin:
      | activity_name | section_name |
      | Activity test | section 1    |
    And the following "section relationships" exist in "mod_perform" plugin:
      | section_name | relationship |
      | section 1    | subject      |
    And the following "section elements" exist in "mod_perform" plugin:
      | section_name | element_name |
      | section 1    | short_text   |
    And the following "activity tracks" exist in "mod_perform" plugin:
      | activity_name | track_description |
      | Activity test | track 1           |
    And the following "track assignments" exist in "mod_perform" plugin:
      | track_description | assignment_type | assignment_name |
      | track 1           | cohort          | aud1            |
    And I log in as "admin"
    And I navigate to the manage perform activities page
    And I follow "Activity test"
    And I click on "Notifications" "link" in the ".tui-tabs__tabs" "css_element"

  Scenario: mod_perform_notif001: Instance creation notification
    And I toggle the "Participant instance created" tui collapsible
    And I click on the "Participant instance created notification" tui toggle button
    And I click on "Subject" tui "toggle button" in the "Participant instance created" tui "collapsible"
    And I click on "Activate" "button" in the ".tui-actionCard" "css_element"
    And I confirm the tui confirmation modal
    And I wait "1" seconds
    And I trigger cron
    And I press the "back" button in the browser
    And I log out
    When I log in as "user1"
    And I open the notification popover
    Then I should see "Your \$Activity name\$\/\$Activity type" exactly "1" times
    And I log out
    When I log in as "user2"
    And I open the notification popover
    Then I should see "You have no notifications"

  Scenario: mod_perform_notif002: Instance creation reminder notification
    And I toggle the "Participant instance created reminder" tui collapsible
    And I click on the "Participant instance created reminder notification" tui toggle button
    And I click on "Subject" tui "toggle button" in the "Participant instance created reminder" tui "collapsible"
    And I click on "Add" tui "button" in the "Participant instance created reminder" tui "collapsible"
    And I click on "Add" tui "button" in the "Participant instance created reminder" tui "collapsible"
    And I click on "Add" tui "button" in the "Participant instance created reminder" tui "collapsible"
    And I set the following fields to these values:
      | trigger-instance_created_reminder[0] | 1 |
      | trigger-instance_created_reminder[1] | 2 |
      | trigger-instance_created_reminder[2] | 4 |
      | trigger-instance_created_reminder[3] | 6 |
    And I should see "Activity saved" in the tui "success" notification banner
    And I close the tui notification toast
    And I click on "Activate" "button" in the ".tui-actionCard" "css_element"
    And I confirm the tui confirmation modal
    And I wait "1" seconds
    And I trigger cron
    And I press the "back" button in the browser
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
    Then I should see "Reminder - \$Activity name\$\/\$Activity type" exactly "1" times
    And I click on "Mark all as read" "link" in the "#nav-notification-popover-container" "css_element"

    # 1 day 23 hour
    Given I time travel to "22 hours future" for perform activity notification
    And I reload the page
    And I open the notification popover
    Then I should see "Reminder - \$Activity name\$\/\$Activity type" exactly "1" times

    # 2 day 1 hour
    Given I time travel to "2 hours future" for perform activity notification
    And I reload the page
    And I open the notification popover
    Then I should see "Reminder - \$Activity name\$\/\$Activity type" exactly "2" times
    And I click on "Mark all as read" "link" in the "#nav-notification-popover-container" "css_element"

    # 2 day 23 hour
    Given I time travel to "22 hours future" for perform activity notification
    And I reload the page
    And I open the notification popover
    Then I should see "Reminder - \$Activity name\$\/\$Activity type" exactly "2" times

    # 3 day 23 hour
    Given I time travel to "1 day future" for perform activity notification
    And I reload the page
    And I open the notification popover
    Then I should see "Reminder - \$Activity name\$\/\$Activity type" exactly "2" times

    # 5 day 1 hour (notification is not sent on day 4 because cron is not run)
    Given I time travel to "26 hours future" for perform activity notification
    And I reload the page
    And I open the notification popover
    Then I should see "Reminder - \$Activity name\$\/\$Activity type" exactly "2" times

    # 6 day 1 hour
    Given I time travel to "1 day future" for perform activity notification
    And I reload the page
    And I open the notification popover
    Then I should see "Reminder - \$Activity name\$\/\$Activity type" exactly "3" times
    And I click on "Mark all as read" "link" in the "#nav-notification-popover-container" "css_element"
    And I log out

    # user2 should not receive any notifications
    When I log in as "user2"
    And I open the notification popover
    Then I should see "You have no notifications"
