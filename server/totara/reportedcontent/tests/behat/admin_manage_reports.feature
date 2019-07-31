@totara @totara_engage @engage @totara_reportedcontent @javascript
Feature: Admin can remove or approve comments that have been reported.

  Background:
    Given I am on a totara site
    And I set the site theme to "ventura"
    And the following "users" exist:
      | username | firstname | lastname | email             |
      | user1    | User1     | One      | user1@example.com |
      | user2    | User2     | Two      | user2@example.com |
    And the following "topics" exist in "totara_topic" plugin:
      | name    |
      | Topic 1 |
    And the following "articles" exist in "engage_article" plugin:
      | name      | username | content      | format       | access | topics  |
      | Article 1 | user1    | Test Article | FORMAT_PLAIN | PUBLIC | Topic 1 |
    And the following "playlists" exist in "totara_playlist" plugin:
      | name       | username | summary       | access | topics  |
      | Playlist 1 | user1    | Test Playlist | PUBLIC | Topic 1 |
    And the following "comments" exist in "totara_comment" plugin:
      | name       | username | component       | area    | content          |
      | Article 1  | user1    | engage_article  | comment | article comment  |
      | Playlist 1 | user1    | totara_playlist | comment | playlist comment |
    And I log in as "admin"
    And I set the following system permissions of "Authenticated user" role:
      | moodle/user:viewalldetails | Allow |
    And I log out

    # Create the reports
    And I log in as "user2"
    And I view article "Article 1"
    And I click on "Comments" "link"
    And I click on "[aria-label='Menu trigger']" "css_element"
    And I click on "Report" "link"

    And I view playlist "Playlist 1"
    And I click on "Comments" "link"
    And I click on "[aria-label='Menu trigger']" "css_element"
    And I click on "Report" "link"
    # This step handles the toast interfering with the logout link
    And I view playlist "Playlist 1"
    And I log out

  Scenario: As an admin, I can choose to allow reported comments to remain.
    Given I log in as "admin"
    And I navigate to "Manage embedded reports" node in "Site administration > Reports"
    And I set the field "report-name" to "Inappropriate content"
    And I press "id_submitgroupstandard_addfilter"
    And I follow "Inappropriate content"

    When I follow "View This Report"
    Then I should see "article comment"
    And I should see "playlist comment"

    # Approve both
    When I click on "#reportedcontent tr:nth-child(1) [data-action='approve']" "css_element"
    And I click on "#reportedcontent tr:nth-child(2) [data-action='approve']" "css_element"
    Then I should see "Allowed"

    # Filter on "Allowed" and make sure our results show up
    When I set the field "reportedcontent-status" to "2"
    And I press "id_submitgroupstandard_addfilter"
    Then I should see "article comment"
    And I should see "playlist comment"

    # Go look at the playlist & article to see if the comment exists still
    When I view article "Article 1"
    And I click on "Comments" "link"
    Then I should see "article comment"

    When I view playlist "Playlist 1"
    And I click on "Comments" "link"
    Then I should see "playlist comment"

  Scenario: As an admin, I can choose to remove reported comments.
    Given I log in as "admin"
    And I navigate to "Manage embedded reports" node in "Site administration > Reports"
    And I set the field "report-name" to "Inappropriate content"
    And I press "id_submitgroupstandard_addfilter"
    And I follow "Inappropriate content"

    When I follow "View This Report"
    Then I should see "article comment"
    And I should see "playlist comment"

    # Remove both the reports
    When I click on "#reportedcontent tr:nth-child(1) [data-action='remove']" "css_element"
    And I press "Confirm"

    # Modal goes funky with behat, so refresh the page for the second remove
    And I press "id_submitgroupstandard_addfilter"
    And I click on "#reportedcontent tr:nth-child(1) [data-action='remove']" "css_element"
    And I press "Confirm"
    Then I should see "Removed"

    # Filter on "Removed" and make sure our results show up
    When I set the field "reportedcontent-status" to "1"
    And I press "id_submitgroupstandard_addfilter"
    Then I should see "article comment"
    And I should see "playlist comment"

    # Go look at the playlist & article to see if the comment exists still
    When I view article "Article 1"
    And I click on "Comments" "link"
    Then I should not see "article comment"
    And I should see "This comment has been removed."

    When I view playlist "Playlist 1"
    And I click on "Comments" "link"
    Then I should not see "playlist comment"
    And I should see "This comment has been removed."