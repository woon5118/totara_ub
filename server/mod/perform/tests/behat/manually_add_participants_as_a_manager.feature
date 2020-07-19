@totara @perform @mod_perform @javascript @vuejs
Feature: Manually add participants as a manager

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                   |
      | john     | John      | One      | john.one@example.com    |
      | harry    | Harry     | Three    | harry.three@example.com |
    And the following "subject instances" exist in "mod_perform" plugin:
      | activity_name         | subject_username | subject_is_participating | other_participant_username |
      | Subject and manager   | john             | true                     | harry                      |
      | Subject only activity | john             | true                     |                            |

  Scenario: A manager can manually add participants to a subject instance
    Given I log in as "admin"

    When I navigate to the perform manage participation subject instances report for activity "Subject and manager"
    And I click on "Add participants" "link"
    Then I should see "Subject and manager for John One"
    And the "Manager" tui form row should contain ""
    And I enter "2" into "Manager" the tui form row

    When I click on "Save" "button"
    Then I should see "Confirm create participant instances" in the ".tui-modalContent" "css_element"
    And I should see "1 additional participant instance will be generated" in the ".tui-modalContent" "css_element"

    When I click on "Create" "button"
    Then I should see "1 participant instance created" in the tui "success" notification toast
    And I should see "Manage participation: “Subject and manager”"

  Scenario: No participants can be manually added when subject is the only relationship
    Given I log in as "admin"

    When I navigate to the perform manage participation subject instances report for activity "Subject only activity"
    And I click on "Add participants" "link"
    Then I should see "Subject only activity for John One"
    And I should see "There are no relationships available for adding additional participants to this subject instance."