@javascript @mod @mod_facetoface @totara
Feature: Manage custom assets by non-admin user
  In order to test that non-admin user
  As a editing teacher
  I need to create and edit custom assets

  Background:
    Given I am on a totara site
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | One      | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |

  Scenario: Add edit seminar custom asset as editing teacher
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Seminar" to section "1" and I fill the form with:
      | Name        | Test seminar name        |
      | Description | Test seminar description |
    And I follow "Test seminar name"
    And I follow "Add event"
    And I click on "Select assets" "link"
    And I click on "Create" "link"
    And I should see "Create new asset" in the "Create new asset" "totaradialogue"
    And I set the following fields to these values:
      | Asset name              | Asset 1 |
      | Allow booking conflicts | 1       |
      | Asset description       | Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. |
    And I should not see "Publish for reuse"
    When I click on "OK" "button" in the "Create new asset" "totaradialogue"
    Then I should see "Asset 1"

    # Edit
    And I click on "Edit asset" "link"
    And I should see "Edit asset" in the "Edit asset" "totaradialogue"
    And I set the following fields to these values:
      | Asset name | Asset updated |
    And I should not see "Publish for reuse"
    When I click on "OK" "button" in the "Edit asset" "totaradialogue"
    Then I should see "Asset updated"

  Scenario: Confirm images work when adding an asset through a totaradialogue
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Seminar" to section "1" and I fill the form with:
      | Name        | Test seminar name        |
      | Description | Test seminar description |
    And I follow "Test seminar name"
    And I follow "Add event"
    And I click on "Select assets" "link"
    And I click on "Create" "link"
    And I should see "Create new asset" in the "Create new asset" "totaradialogue"
    And I set the following fields to these values:
      | Asset name              | Asset 1 |
      | Allow booking conflicts | 1       |
      | Asset description       | Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. |
    And I click on "Image" "button" in the "Create new asset" "totaradialogue"
    And I click on "Browse repositories..." "button"
    And I click on "Wikimedia" "link"
    And I set the field "Search for:" to "dog"
    And I press "Submit"
    And I click on "dog" "text"
    And I press "Select this file"
    And I set the field "Describe this image for someone who cannot see it" to "hello"
    And I press "Save image"
    And I set the field "Asset name" to "woof"
    And I click on "OK" "button" in the "Create new asset" "totaradialogue"
    Then I should see "woof"

  Scenario: Confirm images load when viewing added assets
    Given I log in as "admin"
    And I click on "Find Learning" in the totara menu
    And I click on "Course 1" "text"
    And I follow "Go to course"
    And I click on "Turn editing on" "button"
    And I add a "Seminar" to section "1" and I fill the form with:
      | Name        | Test seminar name        |
      | Description | Test seminar description |
    And I follow "Test seminar name"
    And I follow "Add event"
    And I click on "Select assets" "link"
    And I click on "Create" "link"
    And I should see "Create new asset" in the "Create new asset" "totaradialogue"
    And I set the following fields to these values:
      | Asset name                        | Asset 1 |
      | Allow booking conflicts           | 1       |
      | Publish for reuse by other events | 1       |
      | Asset description | Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. |
    And I click on "Image" "button" in the "Create new asset" "totaradialogue"
    And I click on "Browse repositories..." "button"
    And I click on "Wikimedia" "link"
    And I set the field "Search for:" to "dog"
    And I press "Submit"
    And I click on "dog" "text"
    And I press "Select this file"
    And I set the field "Describe this image for someone who cannot see it" to "hello"
    And I press "Save image"
    And I set the field "Asset name" to "woof"
    And I click on "OK" "button" in the "Create new asset" "totaradialogue"
    Then I should see "woof"
    And I press "Save changes"
    When I navigate to "Assets" node in "Site administration > Seminars"
    And I click on "Details" "link" in the "woof" "table_row"
    Then I should see image with alt text "hello"

  Scenario: Confirm the custom asset is available after event cancellation
    Given the following "seminars" exist in "mod_facetoface" plugin:
      | name          | course |
      | Seminar 21495 | C1     |

    And I log in as "teacher1"

    And I am on "Course 1" course homepage with editing mode on
    And I follow "Seminar 21495"
    And I follow "Add event"
    And I click on "Select assets" "link"
    And I click on "Create" "link"
    And I should see "Create new asset" in the "Create new asset" "totaradialogue"
    And I set the following fields to these values:
      | Asset name        | Asset 1 |
      | Asset description | Lorem ipsum dolor sit amet, consectetur adipisicing elit |
    And I should not see "Publish for reuse"
    When I click on "OK" "button" in the "Create new asset" "totaradialogue"
    Then I should see "Asset 1"
    And I press "Save changes"
    And "Cancel event" "link" should exist in the "Upcoming" "table_row"

    When I click on "Cancel event" "link" in the "Upcoming" "table_row"
    Then I should see "Cancelling event in Seminar 21495"
    And I press "Yes"

    And I follow "Add event"
    And I click on "Select assets" "link"
    And I click on "Asset 1" "link" in the "Choose assets" "totaradialogue"
    And I click on "OK" "button" in the "Choose assets" "totaradialogue"
    And I press "Save changes"
    And I click on "Go to event" "link" in the "Upcoming" "table_row"
    Then I should see "Sign-up" in the ".mod_facetoface__eventinfo__sidebar__signup" "css_element"
