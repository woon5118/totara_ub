@block @totara @block_totara_user_profile @javascript
Feature: Add User Profile blocks in a Default profile page
  In order to have one or multiple User Profile blocks in a Profile page
  As a admin
  I need to be able to create and change such blocks

  Background:
    Given I am on a totara site
    And the following "users" exist:
      | username | firstname  | lastname  | email                |
      | learner1 | firstname1 | lastname1 | learner1@example.com |

  Scenario: Adding User detail block in a Default profile page
    Given I log in as "admin"
    And I navigate to "Default profile page" node in "Site administration > Users"
    And I press "Blocks editing on"
    And I add the "User Profile" block
    And I configure the "User Profile" block
    And I expand all fieldsets
    And I set the following fields to these values:
      | Override default block title | Yes          |
      | Block title                  | User information |
      | Display User Profile category| User details |
    When I press "Save changes"
    Then I should see "User information"
    Then I should see "User details"

  Scenario: Adding Reports block in a Default profile page
    Given I log in as "admin"
    And I navigate to "Default profile page" node in "Site administration > Users"
    And I press "Blocks editing on"
    And I configure the "User Profile" block
    And I expand all fieldsets
    And I set the following fields to these values:
      | Display User Profile category| Reports |
    When I press "Save changes"
    Then I should see "Reports"
    When I click on "Reset profile for all users" "button"
    And I log out

    And I log in as "learner1"
    And I follow "Profile" in the user menu
    Then I should see "Reports"

  Scenario: Removing ability to see fields in User Profile block
    Given I log in as "admin"
    And I navigate to "Default profile page" node in "Site administration > Users"
    And I press "Blocks editing on"
    And I configure the "User Profile" block
    And I expand all fieldsets
    And I click on "config_editprofile" "checkbox"
    And I click on "config_email" "checkbox"
    When I press "Save changes"
    And I log out

    And I log in as "learner1"
    And I follow "Profile" in the user menu
    Then I should not see "Edit Profile"
    Then I should not see "Email address"


