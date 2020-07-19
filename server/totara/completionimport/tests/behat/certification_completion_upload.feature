@totara @totara_completion_upload @totara_evidence @javascript @_file_upload
Feature: Verify certification completion data can be successfully uploaded.

  Background:
    Given the "mylearning" user profile block exists
    And I am on a totara site
    And the following "users" exist:
      | username | firstname  | lastname  | email                |
      | learner1 | Bob1       | Learner1  | learner1@example.com |

    And the following "certifications" exist in "totara_program" plugin:
      | fullname        | shortname | idnumber |
      | Certification 1 | Cert1     | 1        |

  Scenario: Verify a successful simple certification completion upload.
    Given I log in as "admin"
    When I navigate to "Upload Completion Records" node in "Site administration > Courses > Upload Completion Records"
    And I upload "totara/completionimport/tests/behat/fixtures/certification_completion_1.csv" file to "Certification CSV file to upload" filemanager
    And I set the field "Upload certification Create evidence" to "1"
    And I set the field "Upload certification Import action" to "Certify uncertified users"
    And I click on "Save" "button" in the ".totara_completionimport__uploadcertification_form" "css_element"
    Then I should see "Certification completion file successfully imported"
    And I should see "2 Records imported pending processing"
    And I run all adhoc tasks

    When I navigate to "Manage users" node in "Site administration > Users"
    And I follow "Bob1 Learner1"
    And I click on "Record of Learning" "link" in the ".block_totara_user_profile_category_mylearning" "css_element"
    And I switch to "Certifications" tab
    Then I should see "Certified" in the "Certification 1" "table_row"

    When I follow "Other Evidence"
    And I follow "Completed certification : thisisevidence"
    Then I should see the evidence item fields contain:
      | Certification short name | thisisevidence    |
      | Certification ID number  | notacertification |
      | Completion date          | 1 January 2015    |
      | Due date                 | 1 January 2016    |
      | Import ID                | 2                 |

  Scenario: Verify a successful certification completion upload specifying that no evidence should be created.
    Given I log in as "admin"
    When I navigate to "Upload Completion Records" node in "Site administration > Courses > Upload Completion Records"
    And I upload "totara/completionimport/tests/behat/fixtures/certification_completion_1.csv" file to "Certification CSV file to upload" filemanager
    And I set the field "Upload certification Create evidence" to "0"
    And I set the field "Upload certification Import action" to "Certify uncertified users"
    And I click on "Save" "button" in the ".totara_completionimport__uploadcertification_form" "css_element"
    Then I should see "Certification completion file successfully imported"
    And I should see "2 Records imported pending processing"
    And I run all adhoc tasks

    When I navigate to "Manage users" node in "Site administration > Users"
    And I follow "Bob1 Learner1"
    And I click on "Record of Learning" "link" in the ".block_totara_user_profile_category_mylearning" "css_element"
    And I switch to "Certifications" tab
    Then I should see "Certified" in the "Certification 1" "table_row"
    And I should not see "Other Evidence"
    When I click on "Record of Learning" in the totara menu
    Then I should see "There are no records"

  Scenario: Verify a certification completion import csv with incorrect columns shows an error
    Given I log in as "admin"
    When I navigate to "Upload Completion Records" node in "Site administration > Courses > Upload Completion Records"
    And I upload "totara/completionimport/tests/behat/fixtures/certification_completion_badcolumns.csv" file to "Certification CSV file to upload" filemanager
    And I set the field "Upload certification Import action" to "Certify uncertified users"
    And I click on "Save" "button" in the ".totara_completionimport__uploadcertification_form" "css_element"
    Then I should see "There were errors while importing the certifications"
    And I should see "Unknown column 'badcolumn'"
    And I should see "Missing required column 'duedate'"
    And I should see "No records were imported"
