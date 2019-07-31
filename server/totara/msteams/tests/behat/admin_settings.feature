@totara @totara_msteams @javascript
Feature: Customise admin settings
  As an admin
  I would like to have access to a frontend admin settings interface
  So that I can enable and configure the integration to suit the organisation

  Background:
    Given I am on a totara site
    And I log in as "admin"
    And I am on homepage
    And I navigate to "Microsoft Teams integration" node in "Site administration > Microsoft Teams"

  Scenario: Check maximum string length validation
    When I set the field "Short name" to "it's a very long short app name"
    And I press "Save changes"
    Then I should see "Some settings were not changed due to an error"
    And I should see "Maximum of 30 characters"

    When I set the field "Short name" to "Totara"
    And I set the field "Full name" to "this is a really really long full application name that is excruciatingly longer than 100 characters."
    And I press "Save changes"
    Then I should see "Some settings were not changed due to an error"
    And I should see "Maximum of 100 characters"

  Scenario: Manifest download
    When I set the following fields to these values:
      | Short name      | Mistletoetara                        |
      | Full name       |                                      |
      | Manifest app ID | 31415926-5358-9793-2384-626433832795 |
    And I press "Save changes"
    Then I should see "Changes saved"
    And I should not see "Some settings were not changed due to an error"

    When I navigate to "Totara app installation" node in "Site administration > Microsoft Teams"
    Then I should see "One or more settings are not correctly set"
    And I should see "Failed" in the "Allow frame embedding" "table_row"
    And I should see "Skipped" in the "Allow public access to catalogue" "table_row"

    When I follow "Allow frame embedding"
    And I set the field "Allow frame embedding" to "1"
    And I press "Save changes"
    And I am on homepage
    And I navigate to "Totara app installation" node in "Site administration > Microsoft Teams"
    Then I should see "All settings have been verified, you can download the manifest file"
    And I should see "OK" in the "Allow frame embedding" "table_row"
    But I should see "Skipped" in the "Allow public access to catalogue" "table_row"

    When I follow "Allow public access to catalogue"
    And I set the field "Allow public access to catalogue" to "1"
    And I press "Save changes"
    And I am on homepage
    And I navigate to "Totara app installation" node in "Site administration > Microsoft Teams"
    Then I should see "All settings have been verified, you can download the manifest file"
    And I should see "OK" in the "Allow frame embedding" "table_row"
    And I should see "OK" in the "Allow public access to catalogue" "table_row"

    When I click on "Download manifest file" "link_or_button"
    # Then I should successfully download a file
