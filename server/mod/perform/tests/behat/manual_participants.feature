@totara @perform @mod_perform @javascript @vuejs
Feature: Allow users to select who will participant in what roles in an activity.

  Background:
    Given the following "users" exist:
      | username  | firstname | lastname |
      | subject   | Subject   | User      |
      | manager   | Manager   | User      |
      | appraiser | Appraiser | User      |
      | colleague | Colleague | User      |
    And the following job assignments exist:
      | user      | manager | appraiser |
      | subject   | manager | appraiser |
      | colleague | manager |           |
    And the following "cohorts" exist:
      | name | idnumber | description | contextlevel | reference | cohorttype |
      | aud1 | aud1     | Audience 1  | System       | 0         | 1          |
    And the following "cohort members" exist:
      | user    | cohort |
      | subject | aud1   |
    When I log in as "admin"
    And I navigate to the manage perform activities page

  Scenario: Create an activity and make users select participants
    # Setup the activity so it can be activated
    When I click on "Add activity" "button"
    And I set the following fields to these values:
      | Activity title | Act1      |
      | Activity type  | Appraisal |
    And I click on "Get started" "button"
    And I click the add participant button
    And I click on the "Subject" tui checkbox
    And I click on the "Manager" tui checkbox
    And I click on the "Appraiser" tui checkbox
    And I click on the "Peer" tui checkbox
    And I click on the "Mentor" tui checkbox
    And I click on the "Reviewer" tui checkbox
    And I click on "Done" "button"
    And I close the tui notification toast
    And I click on "Edit content elements" "button"
    And I click on "Add element" "button"
    And I click on "Short text" "link"
    When I set the following fields to these values:
      | rawTitle   | Question 1   |
    And I click on "Done" "button" in the ".tui-performEditSectionContentModal__form" "css_element"
    And I close the tui modal
    And I close the tui notification toast
    And I click on "Assignments" "link"
    And I click on "Add group" "button"
    And I click on "Audience" "link" in the ".tui-dropdown__menu" "css_element"
    And I toggle the adder picker entry with "aud1" for "Audience name"
    And I save my selections and close the adder
    And I close the tui notification toast
    And I click on "Update instance creation" "button"
    And I confirm the tui confirmation modal
    And I close the tui notification toast

    # General tab should have relationship options
    When I click on "General" "link"
    Then I should see "Selection of participants"
    And I should see "Participants for each relationship below must be manually chosen by the selected role."
    And the following fields match these values:
      | Peer     | Subject |
      | Mentor   | Subject |
      | Reviewer | Subject |
    When I set the following fields to these values:
      | Peer     | Subject   |
      | Mentor   | Manager   |
      | Reviewer | Appraiser |
    And I click on "Save" "button"
    Then I should see "Activity saved" in the tui "success" notification toast
    When I reload the page
    And I click on "General" "link"
    Then the following fields match these values:
      | Peer     | Subject   |
      | Mentor   | Manager   |
      | Reviewer | Appraiser |

    # Activate the activity
    When I click on "Activate" "button"
    And I confirm the tui confirmation modal
    And I run the scheduled task "mod_perform\task\expand_assignments_task"
    And I run the scheduled task "mod_perform\task\create_subject_instance_task"
    And I run the scheduled task "mod_perform\task\create_manual_participant_progress_task"
    And I log out

    # Subject makes selection (appraiser user for the peer relationship)
    When I log in as "subject"
    And I navigate to the outstanding perform activities list page
    Then I should see "You must select participants to take part in performance activities. Note: activities cannot start until this is done" in the ".tui-actionCard" "css_element"
    When I click on "Select participants" "link" in the ".tui-actionCard" "css_element"
    Then I should see "Select participants"
    And I should see "Note: None of these activities can start until participants are selected."
    And I should see "Act1 for Subject User"
    And I should see "Created" in the ".tui-performUserActivitiesSelectParticipants__instance-meta" "css_element"
    And I should see the current date in format "j F Y" in the ".tui-performUserActivitiesSelectParticipants__instance-meta" "css_element"
    # Subject can select themselves
    And I should see the following options in the tui taglist in the ".tui-formRow" "css_element":
      | Appraiser User |
      | Manager User   |
    And I should see "Peer" in the ".tui-formRow" "css_element"
    When I click on "Save" "button"
    Then I should see "You must select at least one user." in the ".tui-formRow" "css_element"
    When I select from the tui taglist in the ".tui-formRow" "css_element":
      | Appraiser User |
    And I click on "Save" "button"
    Then I should see "The participants have been successfully saved." in the tui "success" notification toast
    And I should not see "Act1 for Subject User"
    And I should see "You have no remaining participants to select."
    When I click on "Back to all performance activities" "link"
    Then I should not see "Select participants"
    And I should see "No items to display"
    And I should not see "Act1"
    And I log out

    # Manager makes selection (colleague for mentor relationship)
    When I log in as "manager"
    And I navigate to the outstanding perform activities list page
    And I click on "Select participants" "link" in the ".tui-actionCard" "css_element"
    Then I should see "Mentor" in the ".tui-formRow" "css_element"
    And I should see the following options in the tui taglist in the ".tui-formRow" "css_element":
      | Appraiser User |
      | Colleague User |
      | Manager User   |
    When I select from the tui taglist in the ".tui-formRow" "css_element":
      | Colleague User |
    And I click on "Save" "button"
    And I click on "Back to all performance activities" "link"
    Then I should not see "Select participants"
    When I click on "Activities about others" "link"
    And I should see "No items to display"
    And I should not see "Act1"
    And I log out

    # Appraiser makes selection (manager for reviewer relationship)
    When I log in as "appraiser"
    And I navigate to the outstanding perform activities list page
    And I click on "Select participants" "link" in the ".tui-actionCard" "css_element"
    Then I should see "Reviewer" in the ".tui-formRow" "css_element"
    And I should see the following options in the tui taglist in the ".tui-formRow" "css_element":
      | Appraiser User |
      | Manager User   |
    When I select from the tui taglist in the ".tui-formRow" "css_element":
      | Manager User |
    And I click on "Save" "button"
    And I click on "Back to all performance activities" "link"
    Then I should not see "Select participants"

    # Appraiser was the last person that needed to make a selection, so participant instances should exist now
    When I click on "Activities about others" "link"
    And I should not see "No items to display"
    When I click on "Act1" "button"
    Then I should see "Select relationship to continue"
    And I should see "Appraiser (Not yet started)"
    And I should see "Peer (Not yet started)"
    And I should not see "Mentor"
    And I should not see "Reviewer"
    And the "Appraiser (Not yet started)" radio button is selected
    When I click on "Continue" "button"
    Then I should see perform activity relationship to user "Appraiser"
    When I press the "back" button in the browser
    And I click on "Activities about others" "link"
    And I click on "Act1" "button"
    And I click on the "Peer (Not yet started)" tui radio
    And I click on "Continue" "button"
    Then I should see perform activity relationship to user "Peer"
    And I log out

    # Subject views activity
    When I log in as "subject"
    And I navigate to the outstanding perform activities list page
    And I should not see "No items to display"
    When I click on "Act1" "link"
    Then I should see perform activity relationship to user "Self"
    And I log out

    # Colleague views activity
    When I log in as "colleague"
    And I navigate to the outstanding perform activities list page
    And I click on "Activities about others" "link"
    And I should not see "No items to display"
    When I click on "Act1" "link"
    Then I should see perform activity relationship to user "Mentor"
    And I log out

    # Manager views activity
    When I log in as "manager"
    And I navigate to the outstanding perform activities list page
    And I click on "Activities about others" "link"
    And I should not see "No items to display"
    When I click on "Act1" "button"
    Then I should see "Select relationship to continue"
    And I should see "Manager (Not yet started)"
    And I should see "Reviewer (Not yet started)"
    And I should not see "Mentor"
    And I should not see "Peer"
    And the "Manager (Not yet started)" radio button is selected
    When I click on "Continue" "button"
    Then I should see perform activity relationship to user "Manager"
    When I press the "back" button in the browser
    And I click on "Activities about others" "link"
    And I click on "Act1" "button"
    And I click on the "Reviewer (Not yet started)" tui radio
    And I click on "Continue" "button"
    Then I should see perform activity relationship to user "Reviewer"
