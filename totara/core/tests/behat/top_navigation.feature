@core @javascript
Feature: Totara Top Navigation
  In order to navigate the site
  As a user
  I need to be able to use the Top Navigation Menu

  Background:
    Given I am on a totara site
    And I log in as "admin"
    And I navigate to "Top navigation" node in "Site administration > Appearance"
    And I click on "Add new menu item" "button"
    And I set the following fields to these values:
      | Parent item              | Courses         |
      | Menu title               | 3rd Level item  |
      | Visibility               | Show            |
      | Menu default url address | /admin/user.php |
    And I click on "Add new menu item" "button"

  Scenario Outline: Navigation menu expanding and collapsing works on top level
    # Toggling navigation and waiting for a second is only necessary for small window size
    When I change viewport size to "<window_size>"
    And I <toggle_nav_action>
    And I wait "<wait_seconds>" seconds

    # Click on "Find learning" to open drop-down menu.
    And I click on "Find Learning" in the totara menu
    Then Totara menu item "Find Learning" should be expanded
    And Totara menu item "Find Learning" should not be highlighted
    And I should see "Courses" in the totara menu drop down list
    And I should not see "3rd Level item" in the totara menu

    # Click on "Find learning" again to close drop-down menu.
    When I click on "Find Learning" in the totara menu
    Then Totara menu item "Find Learning" should not be expanded
    And Totara menu item "Find Learning" should not be highlighted
    And I should not see "Courses" in the totara menu

    # Open the same drop-down menu again.
    When I click on "Find Learning" in the totara menu
    Then Totara menu item "Find Learning" should be expanded
    And Totara menu item "Find Learning" should not be highlighted
    And I should see "Courses" in the totara menu drop down list
    And I should not see "3rd Level item" in the totara menu

    # Expand sub-item in the drop-down menu.
    When I click on "Courses" in the totara menu
    Then Totara menu item "Find Learning" should be expanded
    And Totara menu item "Courses" should be expanded
    And I should see "3rd Level item" in the totara menu drop down list

    # Collapse and expand the whole drop-down again and verify the sub-item is also collapsed.
    When I click on "Find Learning" in the totara menu
    Then Totara menu item "Find Learning" should not be expanded
    When I click on "Find Learning" in the totara menu
    Then Totara menu item "Find Learning" should be expanded
    And Totara menu item "Courses" should not be expanded

    # Expand and collapse sub-item in the drop-down menu.
    When I click on "Courses" in the totara menu
    Then Totara menu item "Find Learning" should be expanded
    And Totara menu item "Courses" should be expanded
    And I should see "3rd Level item" in the totara menu
    When I click on "Courses" in the totara menu
    Then Totara menu item "Find Learning" should be expanded
    And Totara menu item "Courses" should not be expanded
    And I should not see "3rd Level item" in the totara menu

    # Expand sub-item again and click on it.
    When I click on "Courses" in the totara menu
    And I start watching to see if a new page loads
    And I click on "3rd Level item" in the totara menu
    Then a new page should have loaded since I started watching

    Examples:
      | window_size | toggle_nav_action                             | wait_seconds |
      | small       | click on "Toggle navigation" "link_or_button" | 1            |
      | medium      | wait "0" seconds                              | 0            |

  Scenario: Navigation menu expanding and collapsing works on second level
    # Click on "Certifications" to load a page with second level navigation displayed.
    When I click on "Find Learning" in the totara menu
    And I start watching to see if a new page loads
    And I click on "Certifications" in the totara menu
    Then a new page should have loaded since I started watching
    And Totara sub menu item "Certifications" should be highlighted
    And Totara sub menu item "Courses" should not be highlighted
    And I should see "Courses" in the totara sub menu
    And I should not see "3rd Level item" in the totara sub menu

    # Expand and collapse second level drop-down.
    When I click on "Courses" in the totara sub menu
    Then Totara sub menu item "Courses" should be expanded
    And I should see "3rd Level item" in the totara sub menu drop down list
    When I click on "Courses" in the totara sub menu
    Then Totara sub menu item "Courses" should not be expanded
    And I should not see "3rd Level item" in the totara sub menu drop down list

    # Expand again and click the link.
    When I click on "Courses" in the totara sub menu
    And I start watching to see if a new page loads
    And I click on "3rd Level item" in the totara sub menu
    Then a new page should have loaded since I started watching
