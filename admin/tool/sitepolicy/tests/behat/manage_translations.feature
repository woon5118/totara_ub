@javascript @tool @tool_sitepolicy @totara @language_packs
Feature: Manage sitepolicy version translations
  As an admin
  I want to manage multiple translations of a site policy

  Background:
    Given I am on a totara site
    And  I log in as "admin"
    And I set the following administration settings values:
      | Enable site policies | 1 |

    And I navigate to "Language packs" node in "Site administration > Language"
    And I set the field "Available language packs" to "fr"
    And I press "Install selected language pack(s)"
    And I wait until "Language pack 'fr' was successfully installed" "text" exists
    And I set the field "Available language packs" to "nl"
    And I press "Install selected language pack(s)"
    And I wait until "Language pack 'nl' was successfully installed" "text" exists

    And I log out

  Scenario: Add a new translation to a sitepolicy version
    Given the following "multiversionpolicies" exist in "tool_sitepolicy" plugin:
      | hasdraft | numpublished | allarchived | title    | languages | langprefix | statement          | numoptions | consentstatement       | providetext | withholdtext | mandatory |
      | 1        | 1            | 0           | Policy 1 | en        |            | Policy 1 statement | 1          | P1 - Consent statement | Yes         | No           | first     |

    And I log in as "admin"
    And I navigate to "Site policies" node in "Site administration > Security"
    Then the "generaltable" table should contain the following:
      | Name     | Status    |
      | Policy 1 | Draft     |
    And I should see "1 new version (draft)" in the "Policy 1" "table_row"

    When I follow "Policy 1"
    Then I should see "Manage \"Policy 1\" policy"
    And the "generaltable" table should contain the following:
      | Version  | Status    | # Translations |
      | 2        | Draft     | 1 View         |
      | 1        | Published | 1 View         |
    And "Continue editing new version" "button" should exist

    When I click on "View" "link" in the "Published" "table_row"
    Then I should see "Manage \"Policy 1\" translations"
    And the "generaltable" table should contain the following:
      | Language          | Status   | Options |
      | English (primary) | Complete | -       |
    And I should not see "Add translation"

    When I follow "English (primary)"
    Then I should see "Policy 1 statement"
    And I should see "P1 - Consent statement 1"
    And I should see "Manage translations" in the ".breadcrumb-nav" "css_element"

    When I click on "Manage translations" "link" in the ".breadcrumb-nav" "css_element"
    Then I should see "Manage \"Policy 1\" translations"
    And I should see "Back to all versions"

    When I follow "Back to all versions"
    Then I should see "Manage \"Policy 1\" policy"

    When I click on "View" "link" in the "Draft" "table_row"
    Then I should see "Manage \"Policy 1\" translations"
    And the "generaltable" table should contain the following:
      | Language          | Status   | Options |
      | English (primary) | Complete | Edit    |
    And I should see "Add translation"

    When I select "nl" from the "language" singleselect
    And I set the following fields to these values:
      | Title                     | Beleid 1            |
      | Policy statement          | Beleidsverklaring   |
      | statements__statement[0]  | P1 - Stem jy saam?  |
      | statements__provided[0]   | Ja                  |
      | statements__withheld[0]   | Nee                 |
      | whatsnew                  | Iets het verander   |
    And I press "Save"
    Then I should see "Dutch; Flemish translation of \"Policy 1\" has been saved"
    And I should see "Manage \"Policy 1\" translations"
    And the "generaltable" table should contain the following:
      | Language          | Status   |
      | English (primary) | Complete |
      | Dutch; Flemish    | Complete |
    And I should see "Edit" in the "English (primary)" "table_row"
    And I should see "Edit" in the "Dutch; Flemish" "table_row"
    And I should see "Delete" in the "Dutch; Flemish" "table_row"


  Scenario: Add a new option to a multilingual sitepolicy
    Given the following "multiversionpolicies" exist in "tool_sitepolicy" plugin:
      | hasdraft | numpublished | allarchived | title    | languages | langprefix | statement          | numoptions | consentstatement       | providetext | withholdtext | mandatory |
      | 1        | 0            | 0           | Policy 2 | en,nl,fr  | ,nl ,fr    | Policy 2 statement | 1          | P2 - Consent statement | Yes         | No           | first     |
    And I log in as "admin"
    And I navigate to "Site policies" node in "Site administration > Security"
    Then the "generaltable" table should contain the following:
      | Name     | Revisions | Status    |
      | Policy 2 | 1         | Draft |

    When I follow "Policy 2"
    Then the "generaltable" table should contain the following:
      | Version  | Status | # Translations |
      | 1        | Draft  | 3 View         |
    And "Continue editing new version" "button" should exist

    When I press "Continue editing new version"
    Then I should see "Edit version 1 of \"Policy 2\""
    When I press "Save"

    Then I should see "Manage \"Policy 2\" policy"
    And I should see "Version (1) has been saved"

    When I click on "View" "link" in the "Draft" "table_row"
    Then the "generaltable" table should contain the following:
      | Language          | Status   |
      | English (primary) | Complete |
      | Dutch; Flemish    | Complete |
      | French            | Complete |
    And I should see "Edit" in the "English (primary)" "table_row"
    And I should see "Edit" in the "Dutch; Flemish" "table_row"
    And I should see "Delete" in the "Dutch; Flemish" "table_row"
    And I should see "Edit" in the "French" "table_row"
    And I should see "Delete" in the "French" "table_row"

    When I click on "Edit" "link" in the "English (primary)" "table_row"
    Then I should see "Edit version 1 of \"Policy 2\""
    And "Remove" "button" should exist
    And "Add statement" "button" should exist

    When I press "Add statement"
    And I set the following fields to these values:
      | statements__statement[1]  | Another consent statement  |
      | statements__provided[1]   | Agree                      |
      | statements__withheld[1]   | Disagree                   |
    And I press "Save"
    Then I should see "Version (1) has been saved"
    And I should see "Manage \"Policy 2\" translations"
    And the "generaltable" table should contain the following:
      | Language          | Status   |
      | English (primary) | Complete |
      | Dutch; Flemish    | Incomplete |
      | French            | Incomplete |

    When I follow "Back to all versions"
    Then I should see "Manage \"Policy 2\" policy"
    And I should see "You cannot publish this draft because you have incomplete translations"
    And the "generaltable" table should contain the following:
      | Version  | Status | # Translations |
      | 1        | Draft  | 3 View         |
    And I should see "Incomplete translations" in the "Draft" "table_row"
    And "Publish" "link" should not exist

    When I click on "View" "link" in the "Draft" "table_row"
    And I click on "Edit" "link" in the "French" "table_row"
    Then I should see "Translate \"Policy 2\" to French"
    And "Remove" "button" should not exist
    And "Add statement" "button" should not exist

    When I set the following fields to these values:
      | statements__statement[1]  | Une autre déclaration de consentement  |
      | statements__provided[1]   | Accepter                               |
      | statements__withheld[1]   | Pas d'accord                           |
    And I press "Save"
    Then I should see "Manage \"Policy 2\" translations"
    And I should see "French translation of \"Policy 2\" has been saved"
    And the "generaltable" table should contain the following:
      | Language          | Status     |
      | English (primary) | Complete   |
      | Dutch; Flemish    | Incomplete |
      | French            | Complete   |

    When I click on "Edit" "link" in the "English (primary)" "table_row"
    And I press "statements_remove[1]"
    And I press "Yes"
    And I press "Save"
    Then I should see "Manage \"Policy 2\" translations"
    And I should see "Version (1) has been saved"
    And the "generaltable" table should contain the following:
      | Language          | Status     |
      | English (primary) | Complete   |
      | Dutch; Flemish    | Complete   |
      | French            | Complete   |

    When I follow "Back to all versions"
    Then the "generaltable" table should contain the following:
      | Version | Status  | # Translations |
      | 1       | Draft   | 3 View         |
    And I should not see "Incomplete translations" in the "Draft" "table_row"
    And "Publish" "link" should exist in the "Draft" "table_row"
