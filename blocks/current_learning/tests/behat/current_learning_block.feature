@totara @block_current_learning
Feature: Add current learning block to page

    Background:
        Given I am on a totara site
        And the following "users" exist:
            | username | firstname  | lastname  | email                |
            | learner1 | firstname1 | lastname1 | learner1@example.com |

    @javascript
    Scenario: Learner adds block (but learner doesn't have any learning) to their my learning page.
        Given I log in as "learner1"
        And I click on "My Learning" in the totara menu
        And I press "Customise this page"
        And I add the "Current Learning" block
        Then I should see "You do not have any current learning. For previously completed learning see your Record of Learning"


    @javascript
    Scenario: Learner adds block to their my learning page.
        Given the following "programs" exist in "totara_program" plugin:
            | fullname                | shortname |
            | Test Program 1          | program1  |
        And the following "program assignments" exist in "totara_program" plugin:
            | user      | program  |
            | learner1  | program1 |
        And the following "courses" exist:
            | fullname | shortname | enablecompletion |
            | Course 1 | course1  | 1                |
        And the following "course enrolments" exist:
            | user     | course | role           |
            | learner1 | course1| student        |
        And I log in as "learner1"
        And I click on "My Learning" in the totara menu
        And I press "Customise this page"
        And I add the "Current Learning" block
        And I configure the "Current Learning" block
        And I expand all fieldsets
        And I set the following fields to these values:
        | Default region | content |
        | Default weight | -10     |
        And I press "Save changes"
        Then I should see "Course 1" in the ".block_current_learning" "css_element"
        And I should see "Test Program 1" in the ".block_current_learning" "css_element"

    @javascript
    Scenario: Learner expands accordian for a program within the block.
        Given the following "programs" exist in "totara_program" plugin:
            | fullname                | shortname |
            | Test Program 1          | program1  |
        And the following "program assignments" exist in "totara_program" plugin:
            | user  | program  |
            | learner1 | program1 |

