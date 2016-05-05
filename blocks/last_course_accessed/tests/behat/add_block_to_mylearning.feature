@totara @block_last_course_accessed @javascript
Feature: User can add and remove block to / from My Learning.

  Background:
    Given I am on a totara site
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | learner1 | Bob1      | Learner1 | learner1@example.com |

  Scenario: Verify the Site Administrator can add and remove the LCA block to / from the My Learning page.
    Given I log in as "admin"

    # Add the block and check it's removed from the available blocks list.
    When I click on "My Learning" in the totara menu
    And I press "Customise this page"
    And I set the field "Add a block" to "Last Course Accessed"
    Then I should not see "Last Course Accessed" in the "Add a block" "select"
    And I should see "Last Course Accessed" in the "Last Course Accessed" "block"

    # Remove the block and check it's added back to the list of available blocks.
    When I click on "Actions" "link" in the "Last Course Accessed" "block"
    And I follow "Delete Last Course Accessed block"
    Then I should see "Are you sure that you want to delete this block titled Last Course Accessed?"
    When I press "Yes"
    Then I should see "Last Course Accessed" in the "Add a block" "select"
    And I should not see "Last Course Accessed" in the "aside" "css_element"

    And I log out

  Scenario: Verify a learner can add and remove the LCA block to / from the My Learning page.
    Given I log in as "learner1"

    # Add the block and check it's removed from the available blocks list.
    When I click on "My Learning" in the totara menu
    And I press "Customise this page"
    And I set the field "Add a block" to "Last Course Accessed"
    Then I should not see "Last Course Accessed" in the "Add a block" "select"
    And I should see "Last Course Accessed" in the "Last Course Accessed" "block"

    # Remove the block and check it's added back to the list of available blocks.
    When I click on "Actions" "link" in the "Last Course Accessed" "block"
    And I follow "Delete Last Course Accessed block"
    Then I should see "Are you sure that you want to delete this block titled Last Course Accessed?"
    When I press "Yes"
    Then I should see "Last Course Accessed" in the "Add a block" "select"
    And I should not see "Last Course Accessed" in the "aside" "css_element"

    And I log out
