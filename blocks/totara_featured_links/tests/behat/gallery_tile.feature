@block @totara @javascript @block_totara_featured_links
  Feature: Tests the behaviour of the gallery tile
    - The first save should take the user to the edit content form saving form here should
      take the user back to the page with the block.
    - Saving a tile from the edit content form should return the user to the edit content form
    - each tile should be displayed in the edit content form.

    Background:
      When I log in as "admin"
      And I follow "Dashboard"
      When I click on "Customise this page" "button"
      And I add the "Featured Links" block
      And I click on "Add Tile" "link"

    Scenario: I add the gallery tile and add a tile
      When I set the following fields to these values:
        | Tile type | Gallery |
      And I click on "Save and Edit content" "button"

      Then I should see "Edit content"
      And I should see "Finished editing"

      When I click on "Add Tile" "link"
      And I set the following fields to these values:
        | URL | www.example.com |
        | Description | default description |
      And I click on "Save changes" "button"

      Then I should see "Edit content"
      And I should see "Finished editing"
      And I should see "default description"

      When I follow "Finished editing"
      And I click on "Stop customising this page" "button"

      Then I should not see "Edit content"
      And I should not see "Finished editing"
      And I should see the "Featured Links" block
      And I should see "default description"
      And "//a[@href='http://www.example.com']" "xpath_element" should exist
      And ".block-totara-featured-links-gallery-subtiles" "css_element" should exist
    
    Scenario: Check that the sub tiles are rendered in the manage content form
      When I set the following fields to these values:
        | Tile type | Gallery |
      And I click on "Save and Edit content" "button"
      And I click on "Add Tile" "link"
      And I set the following fields to these values:
        | URL | www.example.com |
        | Description | default description |
      And I click on "Save changes" "button"
      And I click on "Add Tile" "link"
      And I set the following fields to these values:
        | URL   | www.example.com |
        | Title | title           |
        | Description | default description2 |
      And I click on "Save changes" "button"

      Then I should see "default description"
      And I should see "title"
      And I should see "default description2"

    Scenario: Check that the second time you configure a gallery tile the save button is different
      When I set the following fields to these values:
        | Tile type | Gallery |
      And I click on "Save and Edit content" "button"
      And I click on "Add Tile" "link"
      And I set the following fields to these values:
        | URL | www.example.com |
        | Description | default description |
      And I click on "Save changes" "button"
      And I follow "Finished editing"
      And I click on "div.block-totara-featured-links-edit div.moodle-actionmenu" "css_element"
      And I click on "Configure" "link" in the ".block-totara-featured-links-edit" "css_element"
      Then "Save changes" "button" should exist
      When I click on "Save changes" "button"
      Then I should see the "Featured Links" block

    Scenario: Check canceling the add tile form returns to the previous page
      When I click on "Cancel" "button"
      Then I should see the "Featured Links" block