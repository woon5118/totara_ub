@javascript @editor @editor_weka @weka
Feature: Test basic Weka functionality

  Scenario: Create resource
    Given I am on a totara site
    And I log in as "admin"
    And I set the site theme to "ventura"
    And I navigate to the "weka_basic" fixture in the "lib/editor/weka" plugin
    And I set the weka editor with css ".tui-fixture-wekaBasic" to "First sample content"
    And I set the weka editor with css ".tui-fixture-wekaBasic" to "Second sample content "
    And I activate the weka editor with css ".tui-fixture-wekaBasic"
    And I click on the "Bold" toolbar button in the weka editor
    And I type "yee-haw" in the weka editor
    And I click on the "Bold" toolbar button in the weka editor
    And I type "what" in the weka editor
    And I select the text "haw" in the weka editor
    And I click on the "Bold" toolbar button in the weka editor
    And I click on the "Insert Link" toolbar button in the weka editor
    And I set the field "URL" to "https://test.totaralms.com/exttests/test.jpg"
    And I click on "Done" "button" in the ".tui-modal" "css_element"
    And I click on "haw" "link"
    And I click on "Display as embedded media" "menuitem"
    And I select the "test.jpg" "linkMedia" in the weka editor
    And I delete the selected node in the weka editor
    And I move the cursor to the end of the weka editor
    And I click on the "Insert Link" toolbar button in the weka editor
    And I set the field "URL" to "https://test.totaralms.com/exttests/test.jpg"
    And I click on "Done" "button" in the ".tui-modal" "css_element"
    And I activate the menu of the "test.jpg" "linkMedia" in the weka editor
    And I click on "Display as text" "menuitem"
    And I click on "test.jpg" "link"
    And I click on "Display as embedded media" "menuitem"
    And I press enter in the weka editor
    And I click on the block menu in the weka editor
    And I click on "Heading" "menuitem"
    And I type "This is a heading" in the weka editor
    And I press backspace in the weka editor
    And I press enter in the weka editor
    And I type "Hello @g" in the weka editor
    And I click on "Guest user" "menuitem"
    And I should see "Hello" in the weka editor
    And I should see "is a headin" in the weka editor
    And I should not see "is a heading" in the weka editor
    And I should see "test.jpg" "linkMedia" in the weka editor
    And I should not see "foo.jpg" "linkMedia" in the weka editor
    And I should see "Guest user" "mention" in the weka editor
