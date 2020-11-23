@core @theme @theme_ventura @javascript
Feature: Theme settings basic validations
  Theme settings should work as expected
  As a user
  I need to confirm that I can navigate to theme settings and see all the different elements

  Background:
    Given I log in as "admin"
    And I am on site homepage
    And I navigate to "Ventura" node in "Site administration > Appearance > Themes"

  Scenario: Confirm Brand tab has all the required elements
    When I click on "Brand" "link"
    Then I should see "Logo" in the ".tui-tabContent" "css_element"
    And the URL for image nested in ".tui-formImageUpload:aria-label-extension=Logo" should match "/theme\/image.php\/ventura\/totara_core\/[0-9]+\/logo/"
    And I should see "Logo alternative text" in the ".tui-tabContent" "css_element"
    And I should see "Favicon" in the ".tui-tabContent" "css_element"
    And the URL for image nested in ".tui-formImageUpload:aria-label-extension=Favicon" should match "/theme\/image.php\/ventura\/theme\/[0-9]+\/favicon/"

  Scenario: Confirm Colours tab has all the required elements
    When I click on "Colours" "link"
    And I click on "More colours" "button"
    Then the field "Primary brand colour" matches value "#4b7e2b"
    Then the field "Accent colour" matches value "#99ac3a"
    And the field "Header background colour" matches value "#ffffff"
    And the field "Header text colour" matches value "#262626"
    And the field "Page text colour" matches value "#262626"

  Scenario: Confirm Images tab has all the required elements
    When I click on "Images" "link"
    And the field "Display login page image" matches value "1"
    And the field "Login alternative text" matches value "Totara Login"
    And the URL for image nested in ".tui-formImageUpload:aria-label-extension=Login page default image" should match "/theme\/image.php\/ventura\/totara_core\/[0-9]+\/default_login/"
    And the URL for image nested in ".tui-formImageUpload:aria-label-extension=Course default image" should match "/theme\/image.php\/ventura\/core\/[0-9]+\/course_defaultimage/"
    And the URL for image nested in ".tui-formImageUpload:aria-label-extension=Program default image" should match "/theme\/image.php\/ventura\/totara_program\/[0-9]+\/defaultimage/"
    And the URL for image nested in ".tui-formImageUpload:aria-label-extension=Certification default image" should match "/theme\/image.php\/ventura\/totara_certification\/[0-9]+\/defaultimage/"
    And the URL for image nested in ".tui-formImageUpload:aria-label-extension=Resource default image" should match "/theme\/image.php\/ventura\/engage_article\/[0-9]+\/default/"
    And the URL for image nested in ".tui-formImageUpload:aria-label-extension=Workspace default image" should match "/theme\/image.php\/ventura\/container_workspace\/[0-9]+\/default_space/"