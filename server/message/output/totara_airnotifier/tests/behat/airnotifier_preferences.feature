@message_totara_airnotifier @javascript
Feature: Message Totara AirNotifier preferences
  In order to modify Totara AirNotifier message preferences
  As an admin
  I can navigate to and set the preferences

  Scenario: Admin navigates to preferences page and sets preferences
    Given I log in as "admin"
    And I navigate to "Plugins > Message outputs > Manage message outputs" in site administration
    Then I should see "Not configured" in the "Totara AirNotifier" "table_row"
    When I navigate to "Plugins > Message outputs > Totara AirNotifier" in site administration
    And I set the following fields to these values:
      | AirNotifier App Name | kia-ora          |
      | AirNotifier App Code | 0123456789abcdef |
    And I click on "Save changes" "button"
    Then the field "AirNotifier Server URL" matches value "https://push.totaralearning.com"
    And the field "AirNotifier App Name" matches value "kia-ora"
    And the field "AirNotifier App Code" matches value "0123456789abcdef"
    And I navigate to "Plugins > Message outputs > Manage message outputs" in site administration
    Then I should not see "Not configured" in the "Totara AirNotifier" "table_row"
    And I should see "Output enabled" in the "Totara AirNotifier" "table_row"
    When I navigate to "Plugins > Message outputs > Default message outputs" in site administration
    Then I should see "Totara AirNotifier"
    When I open the notification popover
    And I follow "Notification preferences"
    Then I should see "Totara AirNotifier"
