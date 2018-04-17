@core @core_block @totara @javascript
Feature: Configure block header visibility
  In order to configure blocks header visibility
  As a admin
  I need to show and hide blocks header on a page

  Background:
    When I log in as "admin"
    And I follow "Dashboard"
    And I click on "Customise this page" "button"
    And I add the "HTML" block

  Scenario: configure that block has no header
    When I configure the "(new HTML block)" block
    And I expand all fieldsets
    Then I should see "Header"
    And I set the field "Block title (no title if blank)" to "New Title"
    And I set the field "Content" to "some content"
    And I set the field with xpath "//*[@id='display_with_header_hide']" to "0"
    And I click on "Save changes" "button"
    When I press "Stop customising this page"
    Then I should not see "New Title" in the ".block_html" "css_element"

  Scenario: configure that block has header
    When I configure the "(new HTML block)" block
    And I expand all fieldsets
    Then I should see "Header"
    And I set the field "Block title (no title if blank)" to "New Title"
    And I set the field "Content" to "some content"
    And I click on "Save changes" "button"
    When I press "Stop customising this page"
    Then I should see "New Title" in the ".block_html" "css_element"
