@totara @perform @mod_perform @javascript @vuejs
Feature: Long text element supports multi-lang filters in titles

  Background:
    Given I am on a totara site
    And I log in as "admin"
    And the following "users" exist:
      | username | firstname | lastname | email                   |
      | john     | John      | One      | john.one@example.com    |
    And the following "subject instances" exist in "mod_perform" plugin:
      | activity_name                 | subject_username | subject_is_participating |
      | John is participating subject | john             | true                     |
    # Enabling multi-language filters for headings and content.
    And the multi-language content filter is enabled

  Scenario: Set multi-lang text as question title for long text element type and make sure it's displayed correctly
    Given I navigate to the edit perform activities page for activity "John is participating subject"

    # Adding a new item
    And I navigate to manage perform activity content page
    And I click on "Add element" "button"
    And I click on "Questions" "button"
    And I click on "Long text" "link"
    And I set the following fields to these values:
      | rawTitle | <span lang="en" class="multilang">it's an English question</span><span lang="de" class="multilang">deutsche Frage</span> |
    And I click on "Done" "button" in the ".tui-performEditSectionContentModal__form" "css_element"
    # Currently a changed text won't be filtered until saved
    Then I should see "<span lang=\"en\" class=\"multilang\">it's an English question</span><span lang=\"de\" class=\"multilang\">deutsche Frage</span>"
    When I click on "Submit" "button"
    And I close the tui notification toast
    And I click on "Edit content elements" "button"
    Then "rawTitle" "field" should not be visible
    And I should see "it's an English question"
    And I should not see "deutsche Frage"
    When I click on edit icon for question "it's an English question"
    Then "rawTitle" "field" should be visible
    And the following fields match these values:
      | rawTitle | <span lang="en" class="multilang">it's an English question</span><span lang="de" class="multilang">deutsche Frage</span> |
    When I set the following fields to these values:
      | rawTitle | <span lang="en" class="multilang">changed & updated</span><span lang="de" class="multilang">geaendert & gespeichert</span> |
    And I click on "Done" "button" in the ".tui-performEditSectionContentModal__form" "css_element"
    # Currently a changed text won't be filtered until saved
    Then I should see "<span lang=\"en\" class=\"multilang\">changed & updated</span><span lang=\"de\" class=\"multilang\">geaendert & gespeichert</span>"
    When I click on "Submit" "button"
    And I close the tui notification toast
    And I click on "Edit content elements" "button"
    Then I should see "changed & updated"
    When I click on "Submit" "button"
    And I close the tui notification toast
    # Going back to edit mode and saving without changes should not change anything
    And I click on "Edit content elements" "button"
    And I click on "Submit" "button"
    And I close the tui notification toast
    And I click on "Edit content elements" "button"
    Then I should see "changed & updated"
    And I should not see "geaendert & gespeichert"
    And I close the tui notification toast
    When I click on "Cancel" "button" in the ".tui-performEditSectionContentModal__form .tui-formBtnGroup" "css_element"
    And I close the tui notification toast

    # Test the user side of things
    And I log out
    And I log in as "john"
    And I navigate to the outstanding perform activities list page
    And I click on "John is participating subject" "link"
    Then I should see "John is participating subject" in the ".tui-performUserActivity h2" "css_element"
    And I should see "Part one"
    And I should see "changed & updated"