@javascript @mod @mod_facetoface @mod_facetoface_virtual_room @totara @totara_core_virtualmeeting
Feature: Validation in a seminar virtual room meeting
  Background:
    Given I am on a totara site
    And the following "users" exist:
      | username     | firstname | lastname | email                | alternatename |
      | trainer1     | Trainer   | First    | trainer1@example.com |               |
      | creator      | Creator   | Host     | creator@example.com  | Bobby         |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "custom rooms" exist in "mod_facetoface" plugin:
      | name         |
      | Virtual Room |
    And the following "seminars" exist in "mod_facetoface" plugin:
      | name            | intro | course |
      | Virtual seminar |       | C1     |
    And the following "seminar events" exist in "mod_facetoface" plugin:
      | facetoface      | details       |
      | Virtual seminar | Virtual event |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | trainer1 | C1     | teacher |
    And the following "seminar sessions" exist in "mod_facetoface" plugin:
      | eventdetails  | start      | finish     | rooms        | sessiontimezone  |
      | Virtual event | +3 day 6am | +3 day 9am | Virtual Room | Indian/Christmas |
    # Disable custom fields so behat will not poke Google Maps
    Given I log in as "admin"
    And I navigate to "Custom fields" node in "Site administration > Seminars"
    And I switch to "Room" tab
    And I click on "Hide" "link" in the "Building" "table_row"
    And I click on "Hide" "link" in the "Location" "table_row"
    And I log out

  Scenario: mod_facetoface_virtualmeeting_002: Connect button
    Given I log in as "trainer1"
    And I am on "Virtual seminar" seminar homepage
    And I click on the seminar event action "Edit event" in row "Christmas"

    When I click on "Virtual Room" "link" in the "Christmas" "table_row"
    Then the "Connect" "button_exact" should be disabled
    And I click on "OK" "button_exact" in the "Edit room" "totaradialogue"
    When I click on "Virtual Room" "link" in the "Christmas" "table_row"
    And I set the field "Add virtual room link" to "Custom virtual room link"
    Then the "Connect" "button_exact" should be disabled
    And I set the field "Virtual room link" to "/mod/facetoface/tests/fixtures/bph4svcr.php"
    And I click on "OK" "button_exact" in the "Edit room" "totaradialogue"

    When I click on "Virtual Room" "link" in the "Christmas" "table_row"
    And I set the field "Add virtual room link" to "PoC App"
    Then the "Connect" "button_exact" should be disabled
    And I click on "OK" "button_exact" in the "Edit room" "totaradialogue"

    When I click on "Virtual Room" "link" in the "Christmas" "table_row"
    Then the "id_plugin" "select_exact" should be disabled

  Scenario: mod_facetoface_virtualmeeting_003: Virtual meeting link
    Given I log in as "trainer1"
    And I am on "Virtual seminar" seminar homepage
    And I click on the seminar event action "Edit event" in row "Christmas"
    And I click on "Virtual Room" "link" in the "Christmas" "table_row"
    When I set the field "Add virtual room link" to "Custom virtual room link"
    And I click on "OK" "button_exact" in the "Edit room" "totaradialogue"
    Then I should see "You must supply a value here"
    When I set the field "Virtual room link" to "invalid.url/format"
    And I click on "OK" "button_exact" in the "Edit room" "totaradialogue"
    Then I should see "Invalid URL format"
    When I set the field "Virtual room link" to "http://example.com"
    And I click on "OK" "button_exact" in the "Edit room" "totaradialogue"
    And I press "Save changes"
    Then I should see "Virtual seminar" in the page title

  Scenario: mod_facetoface_virtualmeeting_004: Connect button
    Given I log in as "trainer1"
    And I am on "Virtual seminar" seminar homepage
    And I click on the seminar event action "Edit event" in row "Christmas"

    When I click on "Virtual Room" "link" in the "Christmas" "table_row"
    And I set the field "Add virtual room link" to "PoC User"
    Then the "Connect" "button_exact" should be enabled
    When I click on "OK" "button_exact" in the "Edit room" "totaradialogue"
    Then I should see "Authorisation required"

    And I click on "Connect" "button" in the "Edit room" "totaradialogue"
    And I wait "1" seconds
    And I switch to "virtualmeeting_connect" window
    And I set the following fields to these values:
      | Username | creator |
      | Password | creator |
    And I click on "Log in" "button"
    And I switch to the main window
    And I wait "1" seconds
    Then I should see "Connected as Bobby (creator@example.com)"
    And I click on "OK" "button_exact" in the "Edit room" "totaradialogue"
    And I press "Save changes"
    Then I should see "Virtual seminar" in the page title

