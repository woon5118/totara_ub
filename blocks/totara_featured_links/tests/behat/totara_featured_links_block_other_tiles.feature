@block @totara @javascript @block_totara_featured_links
  Feature: Tests adding the functionality of the other tiles packed with the block
    There are other tiles that come with the featured links block other than the static tile
      - Test the content form works
      - Test that it is displayed as expected
      - Test that the correct values are passed accross

  Background:
    When I log in as "admin"
    And I follow "Dashboard"
    And I click on "Customise this page" "button"
    And I add the "Featured Links" block
    And I click on "Add Tile" "link"

    Scenario: Check Course Tile content form always has to have a course
      When I start watching to see if a new page loads
      And I set the following fields to these values:
        | Tile type | Course Tile |
      Then a new page should have loaded since I started watching
      When I click on "Select Course" "button"
      And I click on "Cancel" "button" in the "Select Course" "totaradialogue"
      And I click on "Save changes" "button"
      Then I should see "Please Select a course"

    Scenario: Check Course Tile selecting a course
      When I create a course with:
        | Course full name  | Course 1 |
        | Course short name | C1 |
      And I follow "Dashboard"
      And I click on "Add Tile" "link"
      And I set the following fields to these values:
        | Tile type | Course Tile |
      And I click on "Select Course" "button"
      And I click on "Miscellaneous" "link" in the "Select Course" "totaradialogue"
      And I click on "Course 1" "link" in the "Select Course" "totaradialogue"
      And I click on "OK" "button" in the "Select Course" "totaradialogue"
      And I click on "Save changes" "button"
      Then "Course 1" "text" should exist in the ".block_totara_featured_links" "css_element"

    Scenario: Gallery tile content
      When I start watching to see if a new page loads
      And I set the following fields to these values:
        | Tile type | Gallery Tile |
      And I set the following Totara form fields to these values:
        | URL | www.example.com |
        | Title | this is a title |
        | Description | this is a description |
      Then a new page should have loaded since I started watching
      When I set the following Totara form fields to these values:
        | Interval (seconds) | 12 |
      And I click on "Save changes" "button"

    Scenario: values passed correctly to and from Gallery
      When I set the following Totara form fields to these values:
        | URL | www.example.com |
        | Title | this is the title |
        | Description | this is the description |
        | Alternate text | this is the alt text |
      And I click on "Save changes" "button"
      And I click on "div.block-totara-featured-links-edit div.moodle-actionmenu" "css_element"
      And I click on "Content" "link"
      And I set the following fields to these values:
        | Tile type | Gallery Tile |
      Then the following fields match these values:
        | URL | http://www.example.com |
        | Title | this is the title |
        | Description | this is the description |
        | Alternate text | this is the alt text |
      When I set the following fields to these values:
        | URL | www.example2.com |
        | Title | this is the title2 |
        | Description | this is the description2 |
        | Alternate text | this is the alt text2 |
      And I click on "Save changes" "button"
      And I click on "div.block-totara-featured-links-edit div.moodle-actionmenu" "css_element"
      And I click on "Content" "link"
      And I set the following fields to these values:
        | Tile type | Static Tile |
      Then the following fields match these values:
        | URL | http://www.example2.com |
        | Title | this is the title2 |
        | Description | this is the description2 |
        | Alternate text | this is the alt text2 |







