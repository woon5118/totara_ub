@totara @auth @auth_approved @javascript
Feature: auth_approved: signup with profile fields
  In order to signup in a Totara website
  As an external user
  I need to be able to successfully sign up with profile fields for site access.

  Scenario: Sign up with user profile fields
    Given I log in as "admin"
    And I navigate to "Manage authentication" node in "Site administration > Plugins > Authentication"
    And I click on "Enable" "link" in the "Self-registration with approval" "table_row"
    And I set the following administration settings values:
      | registerauth | Self-registration with approval |
    And I press "Save changes"

    And I navigate to "User profile fields" node in "Site administration > Users > Accounts"
    And I set the following fields to these values:
      | datatype | checkbox |
    And I set the following fields to these values:
      | Short name | checkbox              |
      | Name       | User checkbox profile |
      | signup     | 1                     |
    And I press "Save changes"

    And I set the following fields to these values:
      | datatype | date |
    And I set the following fields to these values:
      | Short name | date              |
      | Name       | User date profile |
      | signup     | 1                 |
    And I press "Save changes"

    And I set the following fields to these values:
      | datatype | datetime |
    And I set the following fields to these values:
      | Short name | datetime              |
      | Name       | User datetime profile |
      | signup     | 1                     |
    And I press "Save changes"

    And I set the following fields to these values:
      | datatype | menu     |
    And I set the following fields to these values:
      | Short name | menu              |
      | Name       | User menu profile |
      | signup     | 1                 |
    And I set the field "Menu options (one per line)" to multiline:
          """
          AAA
          BBB
          CCC
          """
    And I press "Save changes"

    And I set the following fields to these values:
      | datatype | textarea |
    And I set the following fields to these values:
      | Short name | textarea              |
      | Name       | User textarea profile |
      | signup     | 1                     |
    And I press "Save changes"

    And I set the following fields to these values:
      | datatype | text |
    And I set the following fields to these values:
      | Short name | textinput         |
      | Name       | User text profile |
      | signup     | 1                 |
    And I press "Save changes"
    And I log out

    And I follow "Log in"
    And I press "Create new account"
    And I set the following fields to these values:
      | Username      | test1             |
      | Password      | Password_1        |
      | Email address | test1@example.com |
      | First name    | Test              |
      | Surname       | Account           |

      | User checkbox profile           | 1   |
      | profile_field_date[enabled]     | 1   |
      | profile_field_datetime[enabled] | 1   |
      | User menu profile               | BBB |
      | User text profile               | Lorem ipsum dolor sit amet |
      | User textarea profile           | Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. |
    And I press "Request account"
    And I should see "An email should have been sent to your address at test1@example.com"
    When I confirm self-registration request from email "test1@example.com"
    Then I should see "an email should have been sent to your address at test1@example.com with information describing the account approval process"

    When I log in as "admin"
    And I navigate to "Pending requests" node in "Site administration > Plugins > Authentication > Self-registration with approval"
    And I click on "Approve" "link" in the "test1@example.com" "table_row"
    And I press "Approve"
    Then I should see "Account request \"test1@example.com\" was approved"

    When I log out
    And I follow "Log in"
    And I set the following fields to these values:
      | Username      | test1             |
      | Password      | Password_1        |
    And I press "Log in"
    Then I should see "Test Account"