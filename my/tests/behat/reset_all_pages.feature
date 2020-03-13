@core @core_my @block @javascript
Feature: Reset all personalised pages to default
  In order to reset everyone's personalised pages
  As an admin
  I need to press a button on the pages to customise the default pages

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | student3 | Student | 3 | student3@example.com |

  Scenario: Reset profile for all users
    Given I log in as "admin"

    And I navigate to "Users > Manage users" in site administration
    And I follow "Student 3"
    And I press "Customise this page"
    And I add the "Calendar" block
    And I log out

    And I log in as "student3"
    And I follow "Profile" in the user menu
    And I should see "Calendar"
    And I log out

    And I log in as "admin"
    And I navigate to "Default profile page" node in "Site administration > Users"
    And I press "Blocks editing on"
    And I add the "Latest announcements" block
    And I log out

    And I log in as "student3"
    And I follow "Profile" in the user menu
    And I should see "Calendar"
    And I should not see "Latest announcements"
    And I log out

    And I log in as "admin"
    And I navigate to "Default profile page" node in "Site administration > Users"
    When I press "Reset profile for all users"
    And I should see "All profile pages have been reset to default."
    And I log out

    And I log in as "student3"
    And I follow "Profile" in the user menu
    And I should see "Latest announcements"
    And I should not see "Calendar"
    And I log out
