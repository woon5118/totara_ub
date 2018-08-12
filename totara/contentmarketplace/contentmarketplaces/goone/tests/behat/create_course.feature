@totara_contentmarketplace @contentmarketplace_goone @javascript @_switch_window
Feature: Create a course from the Go1 content marketplace
  As an admin
  I should be able to navigate the content marketplace and create a course

  Background:
    Given I am on a totara site
    And I log in as "admin"
    And I navigate to "Setup Content Marketplaces" node in "Site administration > Content Marketplace"
    And I should see "Enable" in the ".contentmarketplace_goone" "css_element"
    When I click on "Enable" "link" in the ".contentmarketplace_goone" "css_element"
    And I switch to "setup" window
    And the following should exist in the "state" table:
      | full_name       | Admin User         |
      | email           | moodle@example.com |
      | users_total     | 1                  |
    And I click on "Authorize Totara" "button"
    And I switch to the main window
    And I click on "Continue" "button"
    And I click on "Save and explore GO1" "button"

  Scenario: Single Multiactivity go1 course can be created
    When I click on "[for='selection-1080067']" "css_element"
    Then I should see "1 item selected"
    When I click on "Create course" "link"
    Then I should see "1 item selected"
    And I should see "Better Smarter Richer Demo Course Better Smarter Richer Demo Course"
    Then I should see the following Totara form fields having these values:
      | Course full name  | Better Smarter Richer Demo Course Better Smarter Richer Demo Course |
      | Course short name | Better Smarter Richer Demo Course Better Smarter Richer Demo Course |
    When I set the following Totara form fields to these values:
      | Course short name | bsr |
    And I click on "Create and view course" "button"
    Then I should see "bsr" in the ".breadcrumb" "css_element"
    And I should see "Better Smarter Richer Demo Course Better Smarter Richer Demo Course" in the "#section-1" "css_element"

  Scenario: Single activity go1 course can be created
    When I click on "[for='selection-1080067']" "css_element"
    Then I should see "1 item selected"
    When I click on "Create course" "link"
    Then I should see "1 item selected"
    And I should see "Better Smarter Richer Demo Course Better Smarter Richer Demo Course"
    Then I should see the following Totara form fields having these values:
      | Course full name  | Better Smarter Richer Demo Course Better Smarter Richer Demo Course |
      | Course short name | Better Smarter Richer Demo Course Better Smarter Richer Demo Course |
    When I set the following Totara form fields to these values:
      | Course short name                                                 | bsr |
      | Create a new single activity course for the selected content item | 2   |
    And I click on "Create and view course" "button"
    Then I should see "bsr" in the ".breadcrumb" "css_element"
    And I should see "Better Smarter Richer Demo Course Better Smarter Richer Demo Course"

  Scenario: Multiactivity go1 course can be created
    # Select assortment of courses
    When I click on "[for='selection-1080067']" "css_element"
    And I click on "[for='selection-971893']" "css_element"
    And I click on "[for='selection-972943']" "css_element"
    And I click on "[for='selection-973003']" "css_element"
    And I click on "[for='selection-1004684']" "css_element"
    Then I should see "5 items selected"

    When I click on "Create course" "link"
    Then I should see "5 items selected"
    And I should see "Better Smarter Richer Demo Course Better Smarter Richer Demo Course"
    And I should see "The Fall and Rise of Jerusalem"
    And I should see "Natural Attenuation of Groundwater Contaminants: New Paradigms, Technologies, and Applications"
    And I should see "PowerPoint 2016 - Adding Media"

    When I click on "Remove" "link"
    Then I should see "4 items selected"
    And I should not see "Better Smarter Richer Demo Course Better Smarter Richer Demo Course"

    When I set the following Totara form fields to these values:
      | Course full name  | GO1 test course |
      | Course short name | g1tc            |
    And I click on "Create and view course" "button"
    Then I should see "g1tc" in the ".breadcrumb" "css_element"
    And I should see "The Fall and Rise of Jerusalem" in the "#section-1" "css_element"
    And I should see "Natural Attenuation of Groundwater Contaminants: New Paradigms, Technologies, and Applications" in the "#section-1" "css_element"
    And I should see "PowerPoint 2016 - Adding Media" in the "#section-1" "css_element"

  Scenario: Multiple single activity go1 courses can be created
    # Select assortment of courses
    When I click on "[for='selection-1080067']" "css_element"
    And I click on "[for='selection-971893']" "css_element"
    And I click on "[for='selection-972943']" "css_element"
    And I click on "[for='selection-973003']" "css_element"
    And I click on "[for='selection-1004684']" "css_element"
    Then I should see "5 items selected"

    When I click on "Create course" "link"
    Then I should see "5 items selected"
    And I should see "Better Smarter Richer Demo Course Better Smarter Richer Demo Course"
    And I should see "The Fall and Rise of Jerusalem"
    And I should see "Natural Attenuation of Groundwater Contaminants: New Paradigms, Technologies, and Applications"
    And I should see "PowerPoint 2016 - Adding Media"

    When I set the following Totara form fields to these values:
      | Create a new single activity course for each selected content item | 2 |
    And I click on "Create 5 courses" "button"
    And I should see "5 new courses have been created"
    And I should see "Better Smarter Richer Demo Course Better Smarter Richer Demo Course"
    And I should see "The Fall and Rise of Jerusalem"
    And I should see "Natural Attenuation of Groundwater Contaminants: New Paradigms, Technologies, and Applications"
    And I should see "PowerPoint 2016 - Adding Media"
