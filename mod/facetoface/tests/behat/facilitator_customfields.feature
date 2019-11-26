@mod @mod_facetoface @totara @javascript @totara_customfield
Feature: Check facilitator details with all possible custom fields
  In order to test facilitator details page
  As a site manager
  I need to create an event and facilitator, add custom fields, login as admin and check facilitator details page

  Scenario: View facilitator details page and check all custom fields are properly displayed.
    Given I am on a totara site
    And I log in as "admin"

    # Add images to the private files block to use later
    When I click on "Dashboard" in the totara menu
    And I press "Customise this page"
    And I add the "Private files" block
    And I follow "Manage private files..."
    And I upload "mod/facetoface/tests/fixtures/test.jpg" file to "Files" filemanager
    And I upload "mod/facetoface/tests/fixtures/leaves-green.png" file to "Files" filemanager
    Then I should see "test.jpg"
    And I should see "leaves-green.png"

    When I navigate to "Custom fields" node in "Site administration > Seminars"
    And I click on "Facilitator" "link"

    And I set the field "datatype" to "checkbox"
    And I set the following fields to these values:
      | Full name                   | facilitator checkbox |
      | Short name (must be unique) | checkbox       |
    And I press "Save changes"

    And I set the field "datatype" to "datetime"
    And I set the following fields to these values:
      | Full name                   | facilitator date/time |
      | Short name (must be unique) | datetime        |
      | Include time?               | 1               |
    And I press "Save changes"

    And I set the field "datatype" to "file"
    And I set the following fields to these values:
      | Full name                   | facilitator file |
      | Short name (must be unique) | file       |
    And I press "Save changes"

    And I set the field "datatype" to "location"
    And I set the following fields to these values:
      | Full name                   | facilitator location |
      | Short name (must be unique) | location       |
    And I press "Save changes"

    And I set the field "datatype" to "menu"
    And I set the following fields to these values:
      | Full name                   | facilitator menu of choices |
      | Short name (must be unique) | menuofchoices         |
    And I set the field "Menu options (one per line)" to multiline:
      """
      Apple
      Orange
      Banana
      """
    And I press "Save changes"

    And I set the field "datatype" to "multiselect"
    And I set the following fields to these values:
      | Full name                   | facilitator multi select |
      | Short name (must be unique) | multiselect        |
      | multiselectitem[0][option]  | Tui                |
      | multiselectitem[1][option]  | Moa                |
      | multiselectitem[2][option]  | Tuatara            |
    And I press "Save changes"

    And I set the field "datatype" to "textarea"
    And I set the following fields to these values:
      | Full name                   | facilitator text area |
      | Short name (must be unique) | textarea        |
    And I press "Save changes"

    And I set the field "datatype" to "text"
    And I set the following fields to these values:
      | Full name                   | facilitator text input |
      | Short name (must be unique) | textinput        |
    And I press "Save changes"

    And I set the field "datatype" to "url"
    And I set the following fields to these values:
      | Full name                   | facilitator address |
      | Short name (must be unique) | url           |
    And I press "Save changes"

    Then I should see "facilitator checkbox"
    And I should see "facilitator date/time"
    And I should see "facilitator file"
    And I should see "facilitator location"
    And I should see "facilitator menu of choices"
    And I should see "facilitator multi select"
    And I should see "facilitator text area"
    And I should see "facilitator text input"
    And I should see "facilitator address"

    # Create an facilitator
    When I navigate to "Facilitators" node in "Site administration > Seminars"
    And I press "Add a new facilitator"
    # Set the basic fields.
    And I set the following fields to these values:
      | Facilitator Name              | facilitator 1 |
      | facilitator checkbox          | 1             |
      | facilitator menu of choices   | Orange        |
      | facilitator text area         | Lorem ipsum dolor sit amet, consectetur adipisicing elit |
      | facilitator text input        | Duis aute irure dolor in reprehenderit in voluptate      |
      | customfield_datetime[enabled] | 1    |
      | customfield_datetime[day]     | 2    |
      | customfield_datetime[month]   | 3    |
      | customfield_datetime[year]    | 2020 |
      | customfield_datetime[hour]    | 10   |
      | customfield_datetime[minute]  | 30   |
      | customfield_datetime[enabled] | 1    |
      | customfield_multiselect[2]    | 1    |
      | customfield_url[url]          | http://totaralearning.com |
      | customfield_url[text]         | Totara LMS                |
    # Set a location.
    And I set the field "Address" to multiline:
      """
      Level 8, Totara
      Catalyst House
      150 Willis street
      Te Aro
      Wellington 6011
      """
    And I click on "#id_customfield_locationsize_medium" "css_element"
    And I click on "#id_customfield_locationview_satellite" "css_element"
    And I click on "#id_customfield_locationdisplay_both" "css_element"

    # Add a file to the file custom field.
    And I click on "//div[@id='fitem_id_customfield_file_filemanager']//a[@title='Add...']" "xpath_element"
    And I click on "test.jpg" "link" in the "//div[@aria-hidden='false' and @class='moodle-dialogue-base']" "xpath_element"
    And I click on "Select this file" "button" in the "//div[@aria-hidden='false' and @class='moodle-dialogue-base']" "xpath_element"

    # Image in the textarea custom field
    And I click on "//button[@class='atto_image_button']" "xpath_element" in the "//div[@id='fitem_id_customfield_textarea_editor']" "xpath_element"
    And I click on "Browse repositories..." "button"
    And I click on "leaves-green.png" "link" in the "//div[@aria-hidden='false' and @class='moodle-dialogue-base']" "xpath_element"
    And I click on "Select this file" "button" in the "//div[@aria-hidden='false' and @class='moodle-dialogue-base']" "xpath_element"
    And I set the field "Describe this image for someone who cannot see it" to "Green leaves on customfield text area"
    And I click on "Save image" "button"
    # Create the facilitator.
    And I press "Add a facilitator"

    # Verify that the facilitator was created correctly.
    When I click on "facilitator 1" "link"
    Then I should see "View facilitator"
    And I should see "facilitator 1"
    And I should see "150 Willis street"
    # "Yes" for checkbox
    And I should see "Yes"
    And I should see "Monday, 2 March 2020, 10:30 AM"
    And I should see "test.jpg"
    And I should see "Orange"
    And I should see "Tuatara"
    And I should see "Lorem ipsum dolor sit amet, consectetur adipisicing elit"
    And I should see "Duis aute irure dolor in reprehenderit in voluptate"
    And I should see "Totara LMS"
    And I should see the "Green leaves on customfield text area" image in the "//dd[preceding-sibling::dt[1][. = 'facilitator text area']]" "xpath_element"
    And I should see image with alt text "Green leaves on customfield text area"
