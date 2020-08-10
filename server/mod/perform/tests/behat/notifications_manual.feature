@totara @perform @mod_perform @mod_perform_notification @javascript @vuejs
Feature: Perform activity notifications - manual relationships
  As an activity administrator
  I should to be able to set that participants within an activity are notified when certain conditions are met

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
      | section_name | relationship        |
      | section 1    | subject             |
      | section 1    | appraiser           |
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
      | component   | id                                                   | string                               | comment    |
      | mod_perform | template_instance_created_subject_subject            | New activity notice                  | English    |
      | mod_perform | template_participant_selection_subject_subject       | Wybierz uczestników                  | Polish     |
      | mod_perform | template_participant_selection_appraiser_subject     | Pasirinkite dalyvius                 | Lithuanian |
      | mod_perform | template_instance_created_appraiser_subject          | Nuwe aktiwiteitskennisgewing         | Afrikaans  |
      | mod_perform | template_instance_created_manager_subject            | Ný tilkynning um virkni              | Icelandic  |
      | mod_perform | template_instance_created_perform_peer_subject       | Uue tegevuse teatis                  | Estonian   |
      | mod_perform | template_instance_created_perform_mentor_subject     | Ny aktivitetsmeddelelse              | Danish     |
    And I log in as "admin"
    And I navigate to the manage perform activities page
    And I follow "Activity test"

  Scenario: mod_perform_notification_101: Select participants notification
    And I switch to "Content" tui tab
    And I click the add participant button
    And I click on the "Manager" tui checkbox
    And I click on the "Peer" tui checkbox
    And I click on the "Mentor" tui checkbox
    And I click on "Done" "button"
    And I close the tui notification toast

    And I switch to "General" tui tab
    And I set the field "Mentor" to "Appraiser"
    And I press "Save changes"
    And I close the tui notification toast

    And I switch to "Notifications" tui tab
    And I toggle the "Participant selection" tui collapsible
    And I click on the "Participant selection notification" tui toggle button
    And I click on "Subject" tui "toggle_button" in the "Participant selection" tui "collapsible"
    And I click on "Appraiser" tui "toggle_button" in the "Participant selection" tui "collapsible"
    And I click on "Manager" tui "toggle_button" in the "Participant selection" tui "collapsible"
    # Also activate the instance creation notification.
    And I toggle the "Participant instance creation" tui collapsible
    And I click on the "Participant instance creation notification" tui toggle button
    And I click on "Subject" tui "toggle_button" in the "Participant instance creation" tui "collapsible"
    And I click on "Appraiser" tui "toggle_button" in the "Participant instance creation" tui "collapsible"
    And I click on "Peer" tui "toggle_button" in the "Participant instance creation" tui "collapsible"
    And I click on "Mentor" tui "toggle_button" in the "Participant instance creation" tui "collapsible"

    And I click on "Activate" "button" in the ".tui-actionCard" "css_element"
    And I confirm the tui confirmation modal
    And I wait for the next second
    And I trigger cron
    And I press the "back" button in the browser
    And I log out

    # user2 should not receive any notifications
    When I log in as "user2"
    And I open the notification popover
    Then I should see "You have no notifications"
    And I log out

    # manager should not receive a notification
    When I log in as "manager"
    And I open the notification popover
    Then I should see "You have no notifications"
    And I log out

    # user1 should receive a notification
    When I log in as "user1"
    And I open the notification popover
    Then I should not see "New activity notice"
    And I should see "Wybierz uczestników" exactly "1" times
    # TODO: Fix the behat steps as follows in TL-25417:
    # Click the "See all" link to visit the notification page
    # Click the "select participants" link in the notification message
    # Then you should see the select participants page
    When I navigate to the outstanding perform activities list page
    And I click on "Select participants" "link" in the ".tui-actionCard" "css_element"
    When I select from the tui taglist in the ".tui-formRow" "css_element":
      | Appra Iser |
    And I click on "Save" "button"
    And I close the tui notification toast
    And I log out

    # appraiser should receive notifications
    When I log in as "appraiser"
    And I open the notification popover
    Then I should not see "Nuwe aktiwiteitskennisgewing"
    And I should see "Pasirinkite dalyvius" exactly "1" times
    # TODO: Fix the behat steps as follows in TL-25417
    When I navigate to the outstanding perform activities list page
    And I click on "Select participants" "link" in the ".tui-actionCard" "css_element"
    When I select from the tui taglist in the ".tui-formRow" "css_element":
      | Mana Ger |
    And I click on "Save" "button"
    And I close the tui notification toast
    And I wait for the next second
    And I trigger cron
    And I press the "back" button in the browser
    And I open the notification popover
    Then I should see "Nuwe aktiwiteitskennisgewing" exactly "1" times
    And I should see "Uue tegevuse teatis" exactly "1" times
    And I log out

    # user1 should receive notifications
    When I log in as "user1"
    And I open the notification popover
    Then I should see "New activity notice" exactly "1" times
    And I log out

    # user2 should not receive any notifications
    When I log in as "user2"
    And I open the notification popover
    Then I should see "You have no notifications"
    And I log out

    # manager should receive a notification
    When I log in as "manager"
    And I open the notification popover
    Then I should see "Ny aktivitetsmeddelelse"
