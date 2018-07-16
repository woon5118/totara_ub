<?php
/*

Totara Learn Changelog

Release Evergreen (18th July 2018):
===================================

Key:           + Evergreen only

Security issues:

    TL-17320       Fixed validation issue when checking LTI parameters

                   On a site that has published a course as an LTI tool, a user may have been
                   able to trick the validation system into validating against the wrong
                   values. This could have allowed the user to set parameters to values
                   different to those supplied by the consumer site. This vulnerability has
                   been fixed.

Improvements:

    TL-17668   +   Added support for full text searching

                   This improvement saw the introduction of the following full text search
                   features:
                   * Full text search indexes can now be added to fields within the Totara database.
                   * Full text searches can now be run on these indexes.

                   This functionality will be used by the new catalog to provide better searching.

                   To get the best possible result from full text searches, sites should set
                   the full text search language that will be used in the creation of indexes
                   within their sites config.php file. For more information on how to do this,
                   please refer to the config-dist.php file provided with Totara. All
                   information is under the "FULL TEXT SEARCH" heading.

                   Technical documentation for developers can be found at
                   https://help.totaralearning.com/display/DEV/Full+text+search
                   For those intending to add full text search to their plugins and
                   customisations, we recommend that you read and follow the instructions in
                   the technical documentation. Most importantly always define a new table to
                   use for full text searching, have a cron routine that ensures it is kept up
                   to date, and use event observers to keep it up to date with live changes.

    TL-14714   +   Added onchange support to radio form elements

                   Allow radio groups to use the onchange client action in the Totara forms
                   library.

    TL-14939   +   Made it possible for Report Builder columns to be flagged as deprecated
    TL-14966   +   Added a new conditional access restriction based on time since activity completion

                   Access to an activity can now be restricted based on time since completing
                   another activity.

    TL-16150   +   Added image for course and program tiles in featured links
    TL-16727   +   Moved all report builder functions that added columns, filters and joins from base source in to traits

                   All function that added columns, filters and joins have been deprecated and
                   moved into traits within the report sources associated component.

    TL-17353       Updated the description for "Minimum scheduled report frequency" in the Report Builder general settings
    TL-17494   +   Improved the work flow of adding blocks to editable regions

                   * Removed the existing "Add a block" block.
                   * Each editable region now has a dotted border, when editing is enabled.
                   * Added a "+" icon button to the centre of every block region.
                   * Clicking the "+" button opens a modal dialogue with a list of all
                   available block types and a search input.
                   * The search input provides real-time filtering of the block type list.
                   * Clicking a block name reloads the page and that block will be added to
                   the same region.

    TL-17720       Added 'audience visible' default course option to the upload course tool
    TL-17790       Improved the HTML of the change password page

                   Previously the "Change password" heading was in a legend, this patch moves
                   it to a proper HTML heading.

    TL-17791       Added role HTML attributes to the Totara menu
    TL-17795       Tooltips in the "Current learning" block are now displayed when focused via the tab key
    TL-17891       Changed the Change password page to use the standard page layout

                   This gives the Change password page the standard navigation and blocks

    TL-17905   +   Updated the default value for the 'docroot' setting

                   Previously, error pages included a link to Moodle documentation, which
                   often didn't exist for Totara-specific errors. This change removes the
                   default documentation root so the 'More information about this error' link
                   is no longer shown.

                   If you wish to restore the links, set the docroot back to
                   [http://docs.moodle.org|http://docs.moodle.org/] after upgrading.

Bug fixes:

    TL-14015   +   Deprecated unused totara/core/js/goal.item.js file
    TL-16293       Fixed user profile custom fields "Dropdown Menu" to store non-formatted data

                   This fix has several consequences:
                   1) Whenever special characters (&, <, and >) were used in user custom
                      profile field, it was not found in dynamic audiences. It was fixed
                      by storing unfiltered values on save. Existing values will not be changed.
                   2) Improved multi language support of this custom field, which will display
                      item in user's preferred language (or default language if the user's
                      language is not given in the item).
                   3) Totara "Dropdown Menu" customfield also fixed on save.

                   Existing values that were stored previously, will not be automatically
                   fixed during upgrade. To fix them either:
                   1) Edit instance that holds value (e.g. user profile or seminar event),
                      re-select the value and save.
                   2) Use a special tool that we will provide upon request. This tool can work
                      in two modes: automatic or manual. In automatic mode it will attempt to
                      search filtered values and provide a confirmation form before fixing them.
                      In manual mode it will search for all inconsistent values (values that
                      don't have a relevant menu item in dropdown menu customfield settings)
                      across all supported components and allow you to choose to update them to
                      an existing menu item. To get this tool please request it on support board.

    TL-16795       Added support for backing up and restoring featured links blocks inside of courses

                   Due to a significant improvement in the capability of Gallery tiles in
                   Totara 12, backups created in versions prior to Totara 12 that include a
                   Featured Links Block with Gallery tiles, will not restore fully in Totara
                   12.

    TL-17324       Made completion imports trim leading and trailing spaces from the 'shortname' and 'idnumber' fields

                   Previously leading and trailing spaces on the 'shortname' or 'idnumber'
                   fields, were causing inconsistencies while matching upload data to existing
                   records during course and certification completion uploads. This patch now
                   trims any leading or trailing spaces from these fields while doing the
                   matching.

    TL-17385       Fixed an error when viewing the due date column in program reports that don't allow the display of the total count
    TL-17397       Fixed category level roles being unable to restrict access to category level audiences
    TL-17417   +   Fixed an issue with links not being generated correctly within the totara_message component

                   This was primarily an issue with the "more details" link in messages sent
                   when commenting on a user's learning plan.

    TL-17420       Formatted any dates in program emails based on the recipient's selected language package
    TL-17511       Made sure compound records in reports are not aggregated and added a new jobs counting column
    TL-17531       Fixed user report performance issue when joining job assignments

                   This fix improves performance for certain reports when adding columns from
                   the "All User's job assignments" section. The fix applies to the following
                   report sources:
                    * Appraisal Status
                    * Audience Members
                    * Badges Issued
                    * Competency Status
                    * Competency Status History
                    * Goal Status
                    * Learning Plans
                    * Program Completion
                    * Program Overview
                    * Record of Learning: Recurring Programs
                    * User

    TL-17631       Custom seminar rooms are now able to be viewed and edited within the report builder
    TL-17655       Fixed the prefix auto-fill functionality for Seminar notifications "Body" and "Manager copy prefix" fields

                   Previously while creating or editing seminar notifications, the drop-down
                   selector that pre-populated text fields for notifications using a chosen
                   template's data was only populating the title input. This has been fixed to
                   also pre-populate the "Body" and "Manager copy prefix" message fields.

    TL-17657       Fixed an error causing a debugging message in the facetoface_get_users_by_status() function

                   Previously when the function was called with the include reservations
                   parameter while multiple reservations were available, there were some
                   fields added to the query that were causing a debugging message to be
                   displayed.

    TL-17714       Made sure custom user profile textareas have default values set (where one is supplied) on signup page
    TL-17733       Made sure duplicate user email addresses are validated as duplicate regardless of the text case

                   Previously it was possible to sign up or update email addresses which would
                   duplicate an existing email address, but in a different text case (test vs
                   TEST). Now, we ignore the text case during sign up, email address update,
                   HR sync, and user upload by an administrator. The way user accounts are
                   validated by the authentication methods has not changed.

    TL-17789       Fixed an accessibility issue with an incorrect skip link on the login page
    TL-17818       Fixed the database error when uploading an AICC package via SCORM package activity
    TL-17834       Fixed empty JSON being returned when deleting enrolled learning from a custom report
    TL-17845       Fixed SCORM height issue when side navigation was turned on

                   In some SCORM modules the height of the player was broken when the side
                   navigation was turned on. The height of the player is now calculated
                   correctly with both side and drop down navigation.

    TL-17847       Reduced specificity of fix for TL-17744

                   The June releases of Totara included a fix for heading levels in an HTML
                   block. This increased the specificity of the CSS causing it to override
                   other CSS declarations (this included some in the featured links block).
                   This is now fixed in a different manner, maintaining the
                   existing specificity.

    TL-17858       Fixed the ability to delete blocks from admin settings pages

                   A bug was preventing the deletion of blocks on some admin pages, this
                   affected all pages with a URL in the form of
                   <site>/admin/settings.php?section=<sectionname>.  Blocks on these pages are
                   now removed correctly.

    TL-17868       Fixed a bug which assumed a job must have a manager when messaging attendees of a Seminar

                   Prior to this fix due to a bug in code it was not possible to send a
                   message to Seminar attendees, cc'ing their managers, if the attendee job
                   assignments were tracked, and there was at least one attendee who had a
                   manager, and at least one attendee who had a job assignment which did not
                   have a manager. This has now been fixed.

                   When messaging attendees, having selected to cc their managers, if an
                   attendee does not have a manager the attendee will still receive the
                   message.

    TL-17869       Fixed SQL query in display function in "Pending registrations" report

                   The SQL being used in the display function caused an error in MySQL and
                   MariaDB

    TL-17881       Ensured that Learning plan component settings are also loaded for disabled items

                   When a Learning Plan template has a component that is not enabled, such as
                   courses, linked courses added to competencies, for example, caused a
                   failure in the 'Create learning plans for users in this audience' feature.
                   This was due to settings not being initialised for Learning Plan components
                   that are not enabled, this patch ensures that initialisation of components
                   occurs when they are either enabled or disabled.

    TL-17885       Display seminar assets on reports even when they are being used in an ongoing event

                   When the Asset Availability filter is being used in a report, assets that
                   are available but currently in use (by an ongoing event at the time of
                   searching) should not be excluded from the report. Assets should only be
                   excluded if they are not available between the dates/times specified in the
                   filter.

    TL-17894       Fixed the display of Seminar approval settings when they have been disabled at the system level

                   When an admin disabled an approval option on the seminar global settings
                   page, and there was an existing seminar using the approval option, the
                   approval option would then display as an empty radio selector on that
                   seminar's settings page, and none of the approval options would be
                   displayed as selected. However unless a different approval option was
                   selected the seminar would continue using the disabled option.
                   This patch fixes the display issue by making the previously empty radio
                   selector correctly display the disabled setting's name, and marking it as
                   selected. As before, the disabled approval option can still be used for
                   existing seminars until it is changed to a different setting. When the
                   setting is changed for the seminar the now disabled approval option will no
                   longer be displayed.

Contributions:

    *  Grace Ashton at Kineo.com - TL-17657


Release Evergreen (20th June 2018):
===================================

Key:           + Evergreen only

Security issues:

    TL-10268       Prevented EXCEL/ODS Macro Injection

                   The Excel and Open Document Spreadsheet export functionality allowed the
                   exporting of formulas when they were detected, which could lead to
                   incorrect rendering and security issues on different reports throughout the
                   code base. To prevent exploitation of this functionality, formula detection
                   was removed and standard string type applied instead.

                   The formula type is still in the code base and can still be used, however
                   it now needs to be called directly using the "write_formula" method.

    TL-17424       Improved the validation of the form used to edit block configuration

                   Validation on the fields in the edit block configuration form has been
                   improved, and only fields that the user is permitted to change are passed
                   through this form.
                   The result of logical operators are no longer passed through or relied
                   upon.

    TL-17785       MDL-62275: Improved validation of calculated question formulae

Performance improvements:

    TL-17615       Improved mapping of courses and certifications within the completion import tool

                   Previously all mapping was done in SQL, and was repeated any time the
                   mapping data was needed.
                   On some database engines the SQL would perform poorly when applied to a
                   large data set.
                   This change introduces two new fields to capture the mapping, which is now
                   calculated once and saved for future reference.
                   This should lower resource use on the database when running completion
                   import.

Improvements:

    TL-10651   +   HR Import now handles empty fields consistently

                   Empty fields being imported into HR Import were inconsistently handled
                   across field types, sources and elements. This makes changes to introduce
                   consistency so if a field is left empty in the CSV or database then it will
                   delete the existing data (except if the "Empty string behaviour in CSV"
                   setting is set to "Empty strings are ignored").

                   The main change in behaviour is with empty fields when custom fields are
                   included in the import. Prior to this patch custom fields would sometimes
                   not be erased when an empty field was imported. These should now be erased
                   correctly (for CSV this is only when "Empty strings erase existing data" is
                   set).

    TL-16149   +   Added the ability to have images associated with courses, programs and certifications

                   This improvement saw three notable changes made:

                   1) An image can now be set for courses, programs, and certifications via
                   their respective settings pages.
                   2) An out of the box default image has been added for courses, programs,
                   and certifications.
                   3) The default image for courses, programs, and certifications can be
                   overridden by an admin.

    TL-16893   +   Removed unused content options from the program report source

                   The program report source's "Hide currently unavailable content" setting
                   had no effect and has been removed. The code governing the setting has
                   also been deprecated. The functionality it previously offered is already
                   provided by the Report Builder's visibility controls and capabilities
                   relating to this.

    TL-17288       Missing Seminar notifications can now be restored by a single bulk action

                   During Totara upgrades from earlier versions to T9 and above, existing
                   seminars are missing the new default notification templates. There is
                   existing functionality to restore them by visiting each seminar
                   notification one by one, which will take some time if there are a lot of
                   seminars. This patch introduces new functionality to restore any missing
                   templates for ALL existing seminars at once.

    TL-17414       Improved information around the 'completions archive' functionality

                   It now explicitly expresses that completion data will be permanently
                   deleted and mentions that the data that will be archived is limited to: id,
                   courseid, userid, timecompleted, and grade. It also mentions that this
                   information will be available in the learner's Record of Learning.

    TL-17439   +   Split block configuration settings into two sections

                   The general section contains all the settings common to every block, and
                   the new custom section contains settings specific to the block type.

                   If you have any custom blocks please refer to the blocks/upgrade.txt file
                   for more information.

    TL-17517       Improved the user interface for Course Import when no courses match a search term
    TL-17611       Added a hook to the Last Course Accessed block to allow courses to be excluded from being displayed

                   This hook allows specified courses to be excluded from being displayed in
                   the Last Course Accessed block. If the most recently accessed course is
                   excluded then the next most recently accessed course is displayed.

    TL-17613       Added a hook to the Last Course Accessed block to allow extra data to be passed to template

                   This enables extra data to be passed through to the Last Course Accessed
                   block template so that the display can be more easily modified without
                   changing core code.

    TL-17626       Prevented report managers from seeing performance data without specific capabilities

                   Site managers will no longer have access to the following report columns as
                   a default:

                   Appraisal Answers: Learner's Answers, Learner's Rating Answers, Learner's
                   Score, Manager's Answers, Manager's Rating Answers, Manager's
                   Score, Manager's Manager Answers, Manager's Manager Rating Answers,
                   Manager's Manager Score, Appraiser's Answers, Appraiser's Rating Answers,
                   Appraiser's Score, All Roles' Answers, All Roles' Rating Answers, All
                   Roles' Score.

                   Goals: Goal Name, Goal Description

                   This has been implemented to ensure site managers cannot access users'
                   performance-related personal data. To give site managers access to this
                   data the role must be updated with the following permissions:
                   * totara/appraisal:viewallappraisals
                   * totara/hierarchy:viewallgoals

    TL-17661   +   Enabled missing gzip compression for uncached js files
    TL-17738       Changed data-vocabulary.org URL in metadata to be https

                   This URL is used to provide extra information for navigation breadcrumbs to
                   search engines when your site is indexed.

Bug fixes:

    TL-16908       Made sure evidence files are being cleaned up when evidence is deleted
    TL-16967       Fixed an 'invalidrecordunknown' error when creating Learning Plans for Dynamic Audiences

                   Once the "Automatically assign by organisation" setting was set under the
                   competencies section of Learning Plan templates, and new Learning Plans
                   were created for Dynamic Audiences, a check for the first job assignment of
                   the user was made. This first job assignment must exist otherwise an error
                   was thrown for all users that did not have a job assignment. This has now
                   been fixed and a check for all of the user's job assignments is made
                   rather than just the first one.

    TL-17102       Fixed saved searches not being applied to report blocks
    TL-17289       Made message metadata usage consistent for alerts and blocks
    TL-17364       Fixed displaying profile fields data in the self-registration request report
    TL-17405       Fixed setuplib test case error when test executed separated
    TL-17416       Prevented completion report link appearing in user profile page when user does not have permission to view reports.
    TL-17486       Fixed display issue when using "Hide if there is nothing to display" setting in the report table block

                   If the setting "Hide if there is nothing to display" was set for the report
                   table block then the block would hide even if there was data. The setting
                   now works correctly and only hides the block if the report contains no
                   data.

    TL-17523       Removed the ability to create multiple job assignments via the dialog when multiple jobs is disabled
    TL-17524       Fixed exporting reports as PDF during scheduled tasks when the PHP memory limit is exceeded

                   Generating PDF files as part of a scheduled report previously caused an
                   error and aborted the entire scheduled task if a report had a large data
                   set that exceeded the PDF memory limit. With this patch, the exception is
                   still raised, but the export completes with the exception message in the
                   PDF file notifying the user that they need to change their report. The
                   scheduled task then continues on to the next report to be exported.

    TL-17541       Fixed the help text for a setting in the course completion report

                   The help text for the 'Show only active enrolments' setting in the course
                   completion report was misleading, sounding like completion records for
                   users with removed enrolments were going to be shown on the report. This
                   has now been fixed to reflect the actual behaviour of the setting, which
                   excludes records from removed enrolments.

    TL-17542       Made sure that RPL completion information remains collapsed on the course completion report until it is explicitly expanded
    TL-17590       Added missing parameters to the 'User is a member of audience' filter javascript call
    TL-17601       Made the edit and delete icons in the calendar use Flex icons so they are now Font Awesome icons

                   In Totara 9 the edit and delete buttons for events on calendars were
                   switched over to the new Flex icon API, this was mistakenly overwritten in
                   a later patch. This patch moves the edit and delete buttons back to the
                   Flex icon API as intended.

    TL-17610       Setup cron user and course before each scheduled or adhoc task

                   Before this patch we set the admin user and the course at the beginning of
                   the cron run. Any task could have overridden the user. But if the task did
                   not take care of resetting the user at the end it affected all following
                   tasks, potentially creating unwanted results. Same goes for the course. To
                   avoid any interference we now set the admin user and the default course
                   before each task to make sure all get the same environment.

    TL-17612       Added a warning by the "next page" button when using sequential navigation

                   When the quiz is using sequential navigation, learners are unaware that
                   they cannot navigate back to a question. A warning has been introduced when
                   sequential navigation is in place to make the learner aware of this.

    TL-17622       Fixed validation of custom user profile fields during self-registration
    TL-17628       Prevented access to global report restriction interface when feature is disabled
    TL-17630       Fixed Error in help text when editing seminar notifications

                   in the 'body_help' string replaced [session:room:placeholder] with
                   [session:room:cf_placeholder] as all custom field placeholders have to have
                   the cf_ prefix in the notification.

    TL-17632   +   Ensured that recursion in mustache helpers is prevented when debugging is off
    TL-17633       Removed misleading information in the program/certification extension help text

                   Previously the help text stated "This option will appear before the due
                   date (when it is close)" which was not accurate as the option always
                   appeared during the program/certification enrollment period. This statement
                   has now been removed.

    TL-17645   +   Mustache esc helper now supports full mustache syntax
    TL-17647       Raised MySQL limitation on the amount of questions for Appraisals.

                   Due to MySQL/MariaDB row size limit there could only be about 85 questions
                   of types "text" in one appraisal. Creating appraisals with higher numbers
                   of questions caused an error on activation. Changes have been made to the
                   way the questions are stored so that now it's possible to have up to about
                   186 questions of these types when using MySQL/MariaDB.

                   On the appraisal creation page a warning message has been added that is
                   shown when the limit is about to be exceeded due to the amount of added
                   questions.

                   Also, when this error still occurs on activation, an informative error
                   message will be shown instead of the MySQL error message.

    TL-17656       Fixed notification type validation when creating a new notification

                   When creating a new seminar notification and using the default values, the
                   save process was failing because a notification type default value was
                   missed. Now the default value for the notification type is "Send now"

    TL-17662       Fixed user roles not being added on re-enrolment into course after resetting course
    TL-17702       Fixed display issue when editing forum subscribers
    TL-17711       Fixed message URL in the component alerts
    TL-17716       Fixed HR Import sanity checks for Hierarchy parents when source does not contain all records

                   When the Organisation / Position elements are set to "source does not
                   contain all records" there are sanity checks to ensure that, if an item has
                   a parent, the parent currently exists or will exist before the record is
                   imported.

                   Prior to this patch, only the source records were being used to determine
                   if the parent exists. This only works when the element is set to "source
                   contains all records".

                   This patch ensures that when the element is set to  "source does not
                   contain all records", the sanity check also includes the existing data to
                   determine if a parent exists.

    TL-17722       Fixed issue with HTML entities being stored in Feedback module responses

                   In the Feedback module, if a text area question was being used, some
                   characters were being saved into the database as HTML encoded entities.
                   This resulted in exports and some displays incorrectly showing HTML
                   entities in place of these characters.

    TL-17724       Fixed nonfunctional cleanup script for incorrectly deleted users
    TL-17725   +   Fixed display issue when selecting a course icon

                   When selecting a course icon, if the last icon in a row was selected, the
                   first icon in the following row previously appeared directly below the
                   selected icon.

                   This fix will require LESS recompilation for those themes that use LESS
                   inheritance

    TL-17729       Dialogs no longer overwrite JavaScript strings

                   In some situations it was possible for strings required in JavaScript to be
                   removed. This will no longer happen.

    TL-17730       Added 'alt' text to report cache icon
    TL-17732       Fixed a regression in the Current Learning block caused by TL-16820

                   The export_for_template() function in the course user learning item was
                   incorrectly calling get_owner() when it should have been using has_owner().

    TL-17744       Fixed header tags being the same size as all other text in the HTML block

API changes:

    TL-16918   +   Removed Polyfills required for IE9

                   As of Totara 10, IE9 was no longer supported. This issue removes the
                   polyfills that enabled IE9 to have the same functionality as more modern
                   browsers.

    TL-17746   +   Removed Minified AMD modules with no Source files

                   The following minified AMD JavaScript modules were removed as they are not
                   used and have no source files:
                    * 'block_totara_featured_links/course_dialog'
                    * 'block_totara_featured_links/icon_picker'
                    * 'totara_form/form_clientaction_autosubmit'

Contributions:

    * Jo Jones at Kineo UK - TL-17524


Release Evergreen (14th May 2018):
==================================

Key:           + Evergreen only

Security issues:

    TL-17382       Mustache str, pix, and flex helpers no longer support recursive helpers

                   A serious security issue was found in the way in which the String, Pix
                   icon, and Flex icon Mustache helpers processed variable data.
                   An attacker could craft content that would use this parsing to instantiate
                   unexpected helpers and allow them to access context data they should be
                   able to access, and in some cases to allow them to get malicious JavaScript
                   into pages viewed by other users.
                   Failed attempts to get malicious JavaScript into the page could still lead
                   to parsing issues, encoding issues, and JSON encoding issues. Some of which
                   may lead to other exploits.

                   To fix this all three Mustache helpers have been rewritten with new secure
                   API's.
                   The old API's will continue to function in Totara 11, and below.
                   In this Evergreen release and above the new API's should be used, as the
                   old API's have been deprecated to ensure templates are secure.

                   The API changes are as follows. In all cases all core uses have been
                   converted already.
                   If you are using customisations that make use of mustache templates and any
                   of the following helpers we recommend you review those templates as part of
                   the upgrade process.

                   String helper
                   -------------
                   Old API: {{#str}}Identifier, Component, $a (either a string or json containing user data){{/str}}
                   New API: {{#str}}Identifier, Component, A identifier, A component{{/str}}
                   Change notes:
                   It is no longer allowed to pass JSON encoded data as $a, nor to put user
                   data variables into it.
                   The old API has been deprecated, code using it will continue to work but
                   debugging notices will be generated.
                   Support for the old API will be removed in the future.
                   The new API replaces the $a argument with two new arguments that allow a
                   second string to be specified, allowing for one string to be used within
                   another.
                   Conversion notes:
                   If you are not using $a you don't need to change anything.
                   Otherwise if you need to use user data variables within a string you must
                   now prepare the string and include it within the context data. This will
                   need to be done in the PHP handler, and the JS handler if there is one.
                   You should ensure that you sanitise and clean any user data you are using
                   within a string.

                   Flex icon helper
                   ----------------
                   Old API: {{#flex_icon}}Identifier, JSON data (which can contain user data){{/flex_icon}}
                   New API: {{#flex_icon}}Identifier, Alt identifier, Alt component, classes{{/flex_icon}}
                   Change notes:
                   Providing JSON encoded data is no longer supported. Nor can user data
                   variables be passed as any argument.
                   The old API has been deprecated, code using it will continue to work but
                   debugging notices will be generated.
                   Support for the old API will be removed in the future.
                   Conversion notes:
                   For common uses of the helper the new API should be suitable, and is easily
                   converted to. Alt identifier, and alt component are a string identifier and
                   component that point to the alt string in the language system.
                   Classes is a string of space separated list of classes.
                   If you need to set additional HTML attributes, or use user data in the alt
                   text then you will need to change your template so that it no longer uses
                   the helper, and instead uses the flex icon template as a partial.
                   You can find more information about this in our document on [flex
                   icons|https://help.totaralearning.com/display/DEV/Flexible+Icons+API].

                   Pix icon helper
                   ---------------
                   Old API: {{#pix}}Identifier, Component, Alt text{{/pix}}
                   New API: {{#pix}}Identifier, Component, Alt identifier, Alt component{{/pix}}
                   Change notes:
                   Alt text must now point to a translated string, and can no longer contain
                   user data variables.
                   The new API now accepts a string identifier and component pointing to a
                   translated string to use as alt text.
                   The old API has been deprecated, code using it will continue to work but
                   debugging notices will be generated.
                   Support for the old API will be removed in the future.
                   Conversion notes:
                   If the string is a translated string then conversion to the new API should
                   be simple.
                   If you need to use user data variables within the alt text you must now
                   prepare the string and include it within the context data, and change the
                   template to use the pix icon partial template instead of the helper.

    TL-17436       Added additional validation on caller component when exporting to portfolio
    TL-17440       Added additional validation when exporting forum attachments using portfolio plugins
    TL-17445       Added additional validation when exporting assignments using portfolio plugins
    TL-17527       Seminar attendance can no longer be used to export sensitive user data

                   Previously it was possible for a site administrator to configure Seminar
                   attendance exports to contain sensitive user data, such as a user's hashed
                   password. User fields containing sensitive data can no longer be included
                   in Seminar attendance exports.

Improvements:

    TL-12620       Automated the selection of job assignments upon a users assignment to an appraisal when possible

                   When an appraisal is activated or when learners are dynamically or manually
                   added to an active appraisal, a learner's job assignment is now
                   automatically linked to their appraisal assignment. Before this change, the
                   learner had to open the appraisal for this to happen.

                   This will only come into effect if the setting "Allow multiple job
                   assignments" is turned OFF.

                   If a user has multiple job assignments, this automatic assignment will not
                   apply. If a user has no job assignment, an empty job assignment will still
                   be automatically created.

    TL-16139   +   Added the ability to add icons into static tiles in the featured links block

                   In the edit content form of a featured links block, there is now an option
                   to select an icon that will show in the background at various sizes. The
                   available icons are all from the themes that have been installed.

    TL-16140   +   Added the ability for gallery tiles in the featured links block to contain other tiles

                   Gallery tile content is now based on other tiles rather than a set of
                   images. Each tile in a gallery tile still has all the normal configuration
                   and visibility associated with it, along with an additional meta tile
                   interface for any tile that can contain other tiles. This is so that meta
                   tiles can define that they cannot contain other meta tiles. There is a new
                   database column for parentid added to the block_totara_featured_links_tiles
                   table, this remembers the relationship between the gallery tile and sub
                   tiles.

                   Note: If there are any custom tiles based on the gallery tile then there is
                   a high probability that they will no longer work as they used to, as the
                   templates and structure has changed.

    TL-16143   +   Added more configuration options to the Gallery Tile in the Featured Links block

                   Options Added:
                    * Transition
                       ** Fade
                       ** Slide
                    * Order
                       ** Random
                       ** Sequential
                    * Controls
                       ** Prev/Next (Arrows on side of tile)
                       ** Position indicator (Dots at the bottom)
                    * Autoplay (Whether the gallery tile should automatically move)
                    * Repeat (If the tile should go back to the start when it gets to the end)
                    * Pause on hover (if hovering over the tile then it will stop moving)

                   The switcher.js JavaScript that changes the gallery tile has been rewritten
                   to use the 3rd party library Slick. This caused large changes to the
                   structure of the html as Slick added a number of elements.

    TL-16178   +   Atto autosave notifications now use standardised components

                   This will require themes using less inheritance to re-compile their CSS

    TL-16344       Implemented user data item for the "Self-registration with approval" authentication plugin
    TL-16356       Implemented user data item for the database module
    TL-16738       Implemented user data items for grades

                   The following user data items have been introduced:
                    * Grades - This item takes care of the Gradebook records, supporting both
                      export and purge.
                    * Temp import - This item is a fail-safe cleanup for the tables which are
                      used by grade import script for temporary storage, supporting only purge.
                    * Improved Individual assignments item - This item includes feedback and
                      grades awarded via advanced grading (Guide and Rubric), supporting both
                      purge and export.

    TL-16912   +   Added JavaScript polyfill in IE11 to support basic ECMAScript 6 functionality

                   More information can be found here: https://help.totaralearning.com/display/DEV/ES+6+functionality

    TL-16958       Updated language strings to replace outdated references to system roles

                   This issue is a follow up to TL-16582 with further updates to language
                   strings to ensure any outdated references to systems roles are corrected
                   and consistent, in particular changing student to learner and teacher to
                   trainer.

    TL-17142       Enabled use of the HTML editor when creating site policy statements and added the ability to preview

                   An HTML editor is now used when adding and editing Site Policy statements
                   and translations. A preview function was also added. This enables the
                   policy creator to view how the policy will be rendered to users.

                   Anyone upgrading from an earlier version of Totara 11 who has previously
                   added site policies and wants to use html formatting will need to:
                    * Edit the policy text
                    * The text will still be displayed in a text editor, but you will have an
                      option to change the entered format
                    * Make sure you have a copy of the current text somewhere (copy/paste)
                    * Change the format to "HTML format"
                    * Save and re-open the policy OR Preview and click "Continue editing". The
                      policy text will be shown in the HTML editor but will most likely contain
                      no formatting
                    * Replace the current (unformatted) text by pasting back in the copy of
                      the original text
                    * Save

    TL-17383       Improved the wording and grouping of user data items
    TL-17450   +   Added full width top and bottom block regions to the homepage and dashboard

                   In addition to existing block regions (side-pre, main, side-post), there
                   are now 2 new regions (top, bottom) that can show blocks as well.

                   Note: Just because existing blocks can be shown in these regions does not
                   mean those blocks are suited to these areas. There could be excess space or
                   undesirable aesthetics involved. The best blocks for these new regions are
                   those that can display their information in wide columns, for example
                   tabular data, listings or banners.

Bug fixes:

    TL-6476        Removed the weekday-textual and month-textual options from the data source selector for report builder graphs

                   The is_graphable() method was changed to return false for the
                   weekday-textual and month-textual, stopping them from being selected in the
                   data source of a graph. This will not change existing graphs that contain
                   these fields, however if they are edited then a new data source will have
                   to be chosen. You can still display the weekday or month in a data source
                   by using the numeric form.

    TL-15037       Fixed name_link display function of the "Event name" column for the site log report source

                   The Event name (linked to event source) column in the Site Logs reporting
                   source was not fully restoring the event data.

    TL-17387       Fixed managers not being able to allocate reserved spaces when an event was fully booked
    TL-17442       Ensured that the 'deleted' field is displayed correctly in the list of source fields for HR Import
    TL-17458       Fixed a PHP undefined property notice, $allow_delete within the HR Import source settings
    TL-17471       Fixed Google reCAPTCHA v2 for the "self registration with approval" authentication plugin
    TL-17485       Stopped irrelevant instructions being shown on some of the plan component detail pages

                   The plan header includes instructions about the component and adding a new
                   one. For objectives, competencies, and programs, the instructions were
                   being shown on both the main page, which lists the component items, and the
                   detail page for each item. These instructions were confusing and irrelevant
                   on the details pages so they have been removed.

    TL-17487       Fixed the completion progress bar not updating the percentage correctly in the "Record of Learning: Courses" report
    TL-17509       Fixed the time assigned column for program and certification report sources

                   The time assigned column for the program completion, program overview,
                   certification completion, and certification overview sources previously
                   displayed the data for timestarted, this patch has two main parts:

                   1) Changes the default header of the current column to "Time started" to be
                      consistent with what it displays
                   2) Adds a new column "Time assigned" to the report source that displays the
                      expected data

                   This means that any existing sites that have a report based on one of the
                   affected sources may want to edit the columns for the report and either add
                   or switch over to the new time assigned column.

    TL-17522       Fixed inconsistent styling on the "Add new objective" button in learning plans

                   The padding on the "Add new objective" button was inconsistent with the
                   same button in other components. The missing class has been added to make
                   the styling consistent.

    TL-17528       Removed some duplicated content from the audience member alert notification
    TL-17534       Stopped time being added by the Totara form utc10 date picker

                   TL-16921 introduced the date time pickers of the utc10 totara form element.
                   As an unintended consequence, the time was being added by the input element
                   that caused validation to fail. This patch stops the time being added by
                   the date picker

    TL-17535       Fixed hard-coded links to the community site that were not being redirected properly

Contributions:

    * Marcin Czarnecki at Kineo UK - TL-17387


Release Evergreen (19th April 2018):
====================================

Key:           +   Evergreen only

Important:

    TL-17097       Merged patches from Moodle releases 3.2.6, 3.2.7, and 3.2.8

Improvements:

    TL-16171   +   Improved the warning notification in the Assignments module
    TL-4186    +   Improved the calculation and display of Program and Certification progress

                   The calculation of a user's progress towards completion of a program or
                   certification has been improved to take progress of all involved courses
                   into consideration. This progress is now displayed as a true percentage in
                   a progress bar.

    TL-17261   +   Multiple improvements in the authentication plugins

                   * Authentication plugins are now required to use new settings.php for
                   plugin configuration.
                   * CLI sync scripts were converted to scheduled tasks.
                   * External Database authentication supports PDO.
                   * Shibboleth user may change their passwords.

    TL-14282       Imported ADOdb library v5.20.12
    TL-15739       Imported HTMLPurifier library v4.10.0
    TL-16255       Added a "readonly" state to the Totara reserved custom fields to prevent users from changing the pre-existing seminar custom fields
    TL-16582       Updated language contextual help strings to use terminology consistent with the rest of Totara

                   This change updates the contextual help information displayed against form
                   labels. For example this includes references to System roles, such as
                   student and teacher, have been replaced with learner and trainer.

                   In addition, HTML mark-up has been removed in the affected strings and
                   replaced with Markdown.

    TL-17137       The site policy user consent report now appears in the settings block

                   A user consent report exists for the new site policy tool, however it was
                   never linked to from the current navigation. This user consent report is
                   now linked to from the Settings block, you can find it by navigating to
                   Security > Site policies > User consent report.

    TL-17354       Ordered all user data item groups alphabetically
    TL-16357       Implemented user data item for LTI submissions
    TL-16360       Implemented user data item for glossary entries, comments and ratings
    TL-16367       Implemented user data items for standard and legacy logs
    TL-16773       Implemented user data item for the Community Block
    TL-16775       Implemented user data item for RSS client
    TL-16777       Implemented user data item for the Featured links block
    TL-16840       Implemented user data item for user data export requests
    TL-17227       Implemented user data item for role assignments
    TL-17374       Implemented user data item for Course requests
    TL-16327       Implemented user data items for Report Builder

                   Added items that allow exporting and purging of user-made saved searches
                   (private and public), scheduled reports, and their participation in global
                   report restriction.

    TL-16332       Implemented user data items for Audience memberships

                   Items for exporting and purging a user's audience membership has been
                   added. This is split into two items: Set audience membership and dynamic
                   audience membership.

    TL-16334       Implemented user data items for component and plugin user preference data

                   It is now possible to export and purge user preference data being used by
                   all parts of the system.
                   These preferences store a range of information, all pertaining to the user,
                   and the state of things that they have interacted with on the site, or the
                   decisions that they have made.
                   Some examples are:
                     * What user tours the user has completed, and when.
                     * The admin bookmarks that they have saved.
                     * Their preferences for the course overview block.
                     * Whether they have docked the admin and navigation blocks.
                     * Their preferred display mode for forums.
                     * What regions within a workshop activity they have collapsed.

    TL-16345       Implemented user data item for event monitor subscriptions

                   Implemented user data item for event monitor subscriptions to allow the
                   exporting and purging of user data kept in relation to event monitoring.

    TL-16346       Implemented user data items for Feedback360

                   Feedback360 has two user data items, both implementing export and purge:
                     * The user assignments item, this covers all of a user's assignments to a
                       Feedback360 and all responses to their requests.
                     * The response assignments item, this covers all of a user's responses to
                       other user's Feedback360 requests.

                   It is worth noting that self evaluation responses will be included in both
                   user data items.

    TL-16349       Implemented user data items for Learning Plans and Evidence

                   This allows user data for Learning Plans and Evidence items to be purged
                   and exported.

    TL-16350       Implemented user data items for Appraisals

                   Added five user data items:
                     * "Appraisals" - purge all appraisal data where the user is a learner
                     * "As the learner, excluding hidden answers from other roles" - export all
                       appraisal content that the user can see as a learner
                     * "As the learner, including hidden answers from other roles" - export all
                       appraisal content, including all answers from other roles, regardless of
                       visibility settings, where the user is the learner
                     * "Participation in other users' appraisals" - export all other users'
                       appraisals that the user is currently participating in
                     * "Participation history" - export the history of participation in other
                       users' appraisals

    TL-16365       Implemented user data items for the Wiki module

                   The following user data items have been introduced:
                      * Individual wiki as a whole.
                      * Collaborative wiki files export files uploaded by the user to the collaborative wiki.
                      * Collaborative wiki comments exports\purges user's comments for collaborative wiki pages.
                      * Collaborative wiki versions exports collaborative wiki page versions
                        submitted by the user.

    TL-16736       Implemented user data items for course enrolments

                   Added two user data items that allow exporting and purging:
                     * An item for course enrolment data.
                     * An item for pending enrolments that belong to the Flat file enrolment plugin.

    TL-16739       Implemented user data items for program and certification completion

                   This includes exporting and purging of program and certification
                   assignments, completion records (including completion history and logs). It
                   also includes exceptions, program extensions and the log of program
                   messages sent to the user.

                   Users are unassigned from any program or certification regardless of the
                   assignment type. If users were assigned via audience, position or
                   organisation it's possible that they will be reassigned automatically as
                   soon as the next scheduled task for dynamic user assignment is triggered.

    TL-16877       Implemented user data items for comments and HTML blocks

                   Now it is possible to purge, export and audit the data stored in the
                   comments and HTML blocks.

                   In case of the comments block item, all comments made by users in all
                   created comment blocks are purged or exported. This affects the front page,
                   personal dashboards and courses.

                   In case of the HTML block item, all blocks created by the users in their
                   personal dashboards are purged and exported. HTML blocks in other contexts
                   (front page, courses) are not affected as they are related to the course or
                   the site and not personal to the user.

    TL-16936       Implemented user data item for Competency progress

                   The competency progress item is specifically for the comp_criteria_record table; other
                   competency tables are handled by the competency status item.

    TL-17362       Implemented user data item for portfolios

                   Implemented user data elements for portfolios. This allows the exporting
                   and purging of user data kept in relation to exporting of data to
                   portfolios.

    TL-17373       Implemented user data item for external blogs

                   This user data items takes care of the exporting and purging of external
                   blogs. It includes all external blogs created by the user, including tags
                   assigned to it, all synced posts, and all comments made on the blogs.

    TL-17378       Implemented user data item for the transaction information of the PayPal enrolment plugin

                   When the user enrols via PayPal the transaction details are sent to the IPN
                   endpoint in Totara which records the information in the enrol_paypal
                   table. The user data item takes care of purging, exporting and counting
                   this transaction information.

    TL-16848       Renamed the "Site policies" side menu item in the "Security" section

                   The Security > "Site policies" side menu item has been renamed to "Security
                   settings" to avoid confusion with the new "Site policies" item when GDPR
                   site policies are enabled.

    TL-17384       composer.json now includes PHP version and extension requirements
    TL-17390       Enabled the "Force users to log in to view user pictures" setting by default for new installations to improve privacy
    TL-17403       Removed calls to deprecated table() and cellpadding() functions within forum ratings and external blogs
    TL-10295       Added link validation for report builder rb_display functions

                   In some cases if a param value in rb_display function is empty the function
                   returns the HTML link with empty text which breaks a page's accessibility.

    TL-17024       Added detection of pending upgrades to admin settings related pages
    TL-17268       Upgraded Node.js requirements to v8 LTS
    TL-17280       Improved compatibility for browsers with disabled HTTP referrers
    TL-17170       Included hidden items while updating the sort order of Programs and Certifications
    TL-17321       Added visibility checks to the Program deletion page

                   Previously the deletion of hidden programs was being stopped by an
                   exception in the deletion code, we've fixed the exception and added an
                   explicit check to only allow deletion of programs the user can see. If you
                   have users or roles with the totara/program:deleteprogram capability you
                   might want to consider allowing totara/program:viewhiddenprograms as well.

    TL-17352       PHPUnit and Behat do not show composer suggestions any more to minimise developer confusion
    TL-17357       Unsupported symlinks are now ignored in phpunit tests

Bug fixes:

    TL-14364       Disabled the option to issue a certificate based on the time spent on the course when tracking data is not available

                   The certificate activity has an option which requires a certain amount of
                   time to be spent on a course to receive a certificate. This time is
                   calculated on user actions recorded in the standard log. When the standard
                   log is disabled, the legacy log will be used instead. If both logs are
                   disabled, the option will also be disabled.

                   Please note, if the logs are disabled, and then re-enabled, user actions in
                   the time the logs were disabled will not be recorded. Consequently, actions
                   in this period will not be counted towards time spent on the course.

    TL-16122       Added the 'Enrolments displayed on course page' setting for the Seminar direct enrolment plugin and method

                   Previously the amount of enrolments on the course page was controlled by
                   the 'Events displayed on course page' course setting. Now there are two new
                   settings, one is under "Site administration > Plugins > Enrolments >
                   Seminar direct enrolment plugin" where the admin can set a default value
                   for all courses with the Seminar direct enrolment method. The other is
                   under the Course seminar direct enrolment method where the admin can set a
                   different value. The available options are "All(default), 2, 4, 8, 16" for
                   both settings.

    TL-16461       Fixed the date offset being applied to user completion dates when restoring a course
    TL-16724       Fixed an error while backing up a course containing a deleted glossary

                   This error occurred while attempting to backup a course that contained a
                   URL pointing to a glossary activity that had been deleted in the course
                   description. Deleted glossary items are now skipped during the backup
                   process.

    TL-16821       Removed an error that was stopping redisplay questions in Appraisals from displaying the same question twice
    TL-16839       Ensured that the names of deleted users are not shown in forum ratings
    TL-16853       Fixed bug in DomPDF when using a css file without a @page tag
    TL-16894       Fixed HR import ignoring the default user email preferences when creating new users
    TL-16898       Fixed the seminar booking email with iCal invitation not containing the booking text in some email clients.

                   Some email clients only display the iCal invitation and do not show the
                   email text if the email contains a valid iCal invitation. To handle this
                   the iCal description will now include the booking email text as well as
                   Seminar and Seminar session description.

    TL-16926       Limited the maximum number of selected users in the Report builder job assignment filter

                   Added 'selectionlimit' option to manager field filters, also introduced
                   "$CFG->totara_reportbuilder_filter_selected_managers_limit" to limit the
                   number of selected managers in the report builder job assignment filter
                   dialog. The default value is 25, to make it unlimited, set it to 0.

                   This patch also removed the equals and not-equals options from the job
                   assignment filter when multiple job assignments are not enabled.

    TL-17131       Fixed the user's appraisal snapshots not being deleted when the user is deleted

                   Previously, when an appraisal belonging to a user was deleted (such as when
                   the user was deleted), any related snapshots that had been generated were
                   inadvertently being kept. While these orphaned snapshots could not be
                   accessed through the Totara front end, they could still potentially be
                   accessed through the server's file system.

                   This patch ensures that appraisal snapshots are deleted when the appraisals
                   they belong to are deleted. During upgrade, it also deletes all appraisal
                   snapshots which belong to user appraisal assignments which no longer exist.

    TL-17151       Fixed the positioning of filters and search options in the dropdown selector on the Report builder edit filters page

                   When a filter option or search column was added in an embedded report, and
                   then removed by clicking the delete button, it was not being added back
                   into the right heading within the selectbox. Instead it was added at the
                   end of the selectbox with an untranslated key as a heading, it is now
                   placed back at the end of the correct heading.

    TL-17167       Fixed the 'show blank date records' filter option remaining selected after clearing search in reports
    TL-17226       Ensure that menu items are correctly being marked as selected in the Totara menu

                   Totara menu items are not always being identified as selected when the URL
                   contains query strings. This change insures that they are by comparing
                   against the full URL.

    TL-17231       Fixed the display of RPL course completion data after being restored from the recycle bin

                   Added cache cleaning into the course completion restore step to reflect
                   database changes that were not displayed immediately after course
                   restoration.

    TL-17235       Changed the cancellation string when cancelling a Seminar session to be consistent with other occurrences
    TL-17254       Fixed a custom field error for appraisals when goal review question was set up with "Multiple fields" option
    TL-17264       Stopped a mustache template escaping Identity Providers (IDP) URLs

                   Identity Provider URLs that contained query strings were not linking
                   correctly as html entities were being introduced. Removing the escaping
                   within the mustache template fixes this.

    TL-17267       Fixed the resetting of the 'Automatically cancel reservations' checkbox when updating a Seminar
    TL-17295       Re-implemented the toggle class 'collapsed' functionality for site admin navigation
    TL-17344       Added missing closing dl tag when auditing a user's data
    TL-17351       Removed unwanted line breaks in the manage repositories table
    TL-17358       Fixed notification preference override during Totara Connect sync

                   Changes made to a user's notification preferences on a Totara Connect
                   client site will no longer be overridden during sync.

    TL-17366       Cleaned up several small bugs within the site policy tool

                   Several small bugs and coding style cleanups have been made within the site
                   policy tool. None of these affect the behaviour of the tool, but they will
                   remove a few harmless notices when working with custom translations.

    TL-17386       Fixed the syncing of the suspended flag in Totara Connect

                   When users are synced between a Totara Connect server and client, a user's
                   suspended flag is only changed on the client when a previously
                   deleted/suspended user is restored on the server and then re-synced to the
                   client with the "auth_connect/removeuser" configuration setting set to
                   "Suspend internal user"

    TL-17392       Fixed the seminar events report visibility records when Audience-based visibility is enabled

                   When a course had audience-based visibility enabled and the course
                   visibility was set to anything other than "All users", the seminar events
                   report was still displaying the course to users even when they didn't match
                   the visibility criteria. This has been corrected.

    TL-17406       Fixed the site policy page being displayed if the admin was logged in as a learner

                   Previously if "Site policy" was enabled, the admin user could log in as a
                   learner and be able to consent to the policy instead of the actual user.
                   This patch will stop the display of the site policy page if the admin user
                   logs in as a learner.

    TL-17407       Fixed the message bar disappearing for the admin user when site policy is enabled
    TL-17415       Stopped updating calendar entries for cancelled events when updating the seminar information

                   Previously the system re-created the site, course, and user calendar
                   entries when updating seminar information. This patch added validation to
                   calendar updates for cancelled events.

API changes:

    TL-17347   +   Code related to previously disabled $CFG->loginhttps setting was removed and public API was deprecated
    TL-17372   +   Deprecated footer navigation in the Basis theme

                   The footer menu no longer shows when using Basis as your theme (and themes
                   that include "theme/basis/layout/partials/footer.php"). The functionality
                   that provides this has been deprecated and will be removed in a future
                   version of Totara.

                   If you would like to keep this functionality beyond Totara 12, we recommend
                   you copy the following files into a custom theme that inherits Basis:
                    * theme/basis/templates/page_footer_nav.mustache
                    * theme/basis/classes/renderer.php (2 functions that have been
                   deprecated)
                    * theme/basis/classes/output/page_footer_nav.php
                    * theme/basis/less/totara/page-footer.less

Miscellaneous Moodle fixes:

    TL-16994       MDL-55849: Fixed an issue where reopening a group assignment was creating additional attempts for each group member
    TL-16995       MDL-35849: Added "alert" role HTML attribute to the log in errors

                   This allows screen readers to identify when a user has not logged in
                   correctly

    TL-16996       MDL-60025: Fixed editing a book's chapter did not update timemodified returned by core_course_get_contents
    TL-16997       MDL-59808: Fixed REST simpleserver ignoring the moodlewsrestformat parameter
    TL-16999       MDL-59867: Removed a chance of autocomplete fields using duplicate IDs

                   When there were multiple uses of autocomplete fields, there was a chance
                   that the generated HTML ids were not unique.

    TL-17000       MDL-59399: Advanced settings when adding media to an assignment submission works as expected
    TL-17002       MDL-59929: Improved usability when duplicate email entered during user registration
    TL-17003       MDL-60039: Fixed messaging search areas using 'timecreated' instead of 'timeread' to index search
    TL-17006       MDL-37810: Made sure all roles are displayed in profile and courses if a user has moodle/role:assign capability

                   Users with 'moodle/role:assign' capability now see all roles in user
                   profiles, course participants list and Auto-create group.

    TL-17007       MDL-52131: Made sure question manual comment is respecting comment format in Plain text area editor
    TL-17008       MDL-60105: Fixed global search fatal error when a file in Folder activity is renamed
    TL-17009       MDL-60018: Fixed chatmethod field type in get_chats_by_courses() web services method
    TL-17012       MDL-60167: Fixed hubs registration issues
    TL-17013       MDL-54540: Added allowfullscreen attribute to LTI iFrames to ensure the full screen can be used

                   This change adds attributes to the LTI iframe allowing the content to be
                   viewed in full screen.

    TL-17015       MDL-60121: Fixed enrol plugin backup
    TL-17017       MDL-59645: Fixed Flickr integration
    TL-17019       MDL-59931: Fixed incorrect pagination on Quiz grades results report
    TL-17023       MDL-58790: Replaced a hard coded heading with a language string when editing a quiz
    TL-17025       MDL-60198: Added missing MOODLE_INTERNAL checks in the external functions
    TL-17027       MDL-57228: Fixed error when adding a questions to a quiz with section headings

                   When adding questions to a quiz that has section headings a unique key
                   violation can cause an error to occur if you have a section with a single
                   question in it.

    TL-17028       MDL-60317: Fixed errors in quiz attempts reports
    TL-17030       MDL-60346: Fixed an issue where Solr connection ignored proxy settings
    TL-17032       MDL-60276: Fixed LTI content item so it correctly populates the tool URL when using https
    TL-17033       MDL-60357: Fixed an issue where future modified times of the documents caused search indexing problems
    TL-17034       MDL-59854: Fixed creation of the duplicate forum subscriptions due to the database query race conditions
    TL-17037       MDL-60335: Fixed encoding of non-ASCII site names in blocked hosts
    TL-17038       MDL-60247: Fixed an issue where multilang was not displayed correctly in Random glossary and in HTML block titles
    TL-17040       MDL-60182: Improved location of print icon in glossary in RTL languages
    TL-17041       MDL-60233: Added the use of the s() function to ensure Assignment module web services warnings adhere to the param type PARAM_TEXT
    TL-17042       MDL-58915: Fixed an issue where Solr connection was blocked by cURL restrictions
    TL-17043       MDL-60449: Various language strings improvements in courses and administration
    TL-17044       MDL-60314: Fixed an issue with variable being overridden causing capability not found errors
    TL-17046       MDL-60123: Fixed an issue where assignment grading annotations could not be deselected
    TL-17048       MDL-52653: Fixed increment number of attempts for SCORM 2004 activity

                   Added tracking of 'cmi.completion_status' element that is sent by SCORM
                   2004 activities.

    TL-17050       MDL-60489: Content height changes when using the modal library are now smooth transitions
    TL-17053       MDL-36580: Added encryption of secrets in backup and restore functionality

                   LTI (external tool) activity secret and key are encrypted during backup and
                   decrypted during restore using aes-256-cbc encryption algorithm.
                   Encryption key is stored in the site configuration so backup made with
                   encryption will be restored with lti key and secret on the same site, and
                   without these values on different site.

    TL-17054       MDL-60538: Added new language string in the Lesson module displayed on the final wrong answer
    TL-17055       MDL-60571: Styled "Save and go to next page" as a primary button when manually grading quiz questions
    TL-17057       MDL-51892: Added a proper description of the login errors
    TL-17058       MDL-60535: Improved style of button when adding questions from a question bank to a quiz
    TL-17059       MDL-60162: Fixed an error when downloading quiz attempts reports
    TL-17065       MDL-60360: Improved the help text for the search indicating that changes to the Solr setting requires a full re-index
    TL-17067       MDL-59606: Fixed edge cases in the Quiz reports
    TL-17068       MDL-60377: Made sure text returned by web services is formatted correctly
    TL-17069       MDL-52037: Background of the tooltip for embedded question answer is now the correct size
    TL-17071       MDL-60522: Fixed duplicate tooltips in notifications and messages popovers
    TL-17074       MDL-60607: Fixed message displayed when viewing quiz attempts using separate groups setting

                   This issue occurs when a trainer who is not part of a group is viewing quiz
                   attempts and the "Group Mode" setting is set to "Separate groups". A
                   message was showing "No students enrolled in this course yet", now the
                   message shown is "Sorry, but you need to be part of a group to see this
                   activity". Being part of a group is an existing requirement when separate
                   groups is set.

    TL-17076       MDL-60007: Corrected LTI so delete action without a content type is considered valid.

                   A DELETE operation does not contain any data in the body, and so should not
                   need to have a Content-Type header as no data is sent. However the current
                   LTI Service routing stack will consider a non GET incoming request
                   incorrect if it does not contain a Content-Type. This patch corrects this
                   behaviour.

    TL-17077       MDL-53501: Fixed get_site_info in Webservices failing if usermaxuploadfilesize setting overflows PARAM_INT
    TL-17080       MDL-51945: Fixed update_users web service to stop duplicate emails being sent

                   When updating users using the core_user_update_users webservice duplicate
                   emails for users were being allowed no matter what the "Allow accounts with
                   same email" setting was set to. After this change duplicate emails are only
                   allowed if this setting is turned on.

    TL-17081       MDL-58047: Fixed an issue where sort by last modified (submission) was not sorting as expected in grading of assignments
    TL-17082       MDL-60437: Fixed multilingual HTML block title
    TL-17083       MDL-59858: After closing a modal factory modal, focus goes back to the element that triggered it.
    TL-17084       MDL-60424: Updated the web services upload to allow cross-origin requests (CORS)
    TL-17085       MDL-60671: Switched cron output to use mtrace() function
    TL-17086       MDL-57772: Chat beep doesn't make an audible sound
    TL-17087       MDL-60717: Minor language string improvements in LDAP authentication method
    TL-17088       MDL-60733: Fixed an issue with google_oauth which led to a broken Picasa repository
    TL-17089       MDL-58699: Improved the security of the quiz module while using browser security settings

                   When the "Browser Security" setting is set to "Full screen pop-up with some
                   JavaScript security", the "Attempt quiz" button is no longer visible if a
                   user has JavaScript disabled.

    TL-17092       MDL-60615: Fixed course restore in IMSCC format
    TL-17093       MDL-60550: Added more restrictions in keyword user searches
    TL-17094       MDL-52838: Fixed an undefined variable warning and improved form validation in the Workshop module assessment form
    TL-17095       MDL-60749: Fixed display issue when exporting SCORM interaction report

                   When downloading a SCORM interaction report "&nbsp;" is shown instead of
                   empty string when there is no value.

    TL-17096       MDL-60771: Typecasted scorm score to an integer to avoid debugging error in scorm reports
    TL-17326       MDL-60436: Improved the performance of block loading
    TL-17335       MDL-61269: Set composer license to GPL-3.0-or-later
    TL-17337       MDL-61392: Improved the IPN notifications handling in Paypal enrollment plugin

Contributions:

    * Andrew Davidson at Synergy Learning - TL-17344
    * James Voong from Catalyst - TL-17357
    * Martin Sandberg at Xtractor - TL-17264


Release Evergreen (23rd March 2018):
====================================

Key:           + Evergreen only

Important:

    TL-14114       Added support for Google ReCaptcha v2 (MDL-48501)

                   Google deprecated reCAPTCHA V1 in May 2016 and it will not work for newer
                   sites. reCAPTCHA v1 is no longer supported by Google and continued
                   functionality can not be guaranteed.

    TL-17228       Added description of environment requirements for Totara 12

                   Totara 12 will raise the minimum required version of PostgreSQL from 9.2 to
                   9.4

Security issues:

    TL-17225       Fixed security issues in course restore UI

Improvements:

    TL-9414        Required totara form Checkbox lists are validated in the browser (as opposed to a page reload)
    TL-12393       Added new system role filter for reports using standard user filters
    TL-16157       Improved the layout of progress bars inside the current learning block

                   This will require regeneration of the LESS for themes that use LESS
                   inheritance

    TL-16731   +   Added LESS structure to help maintain consistency with common styles
    TL-16797       Standardised the use of styling in the details of activity access restrictions

                   When some new activity access restrictions were introduced in Totara 11.0,
                   the display of restriction details in the course was not in bold like
                   existing restrictions. This patch corrects the styling.

    TL-16864       Improved the template of Seminar date/time change notifications to accommodate booked and wait-listed users

                   Clarified Seminar notification messages to specifically say that it is
                   related to the session that you are booked on, or are on the wait-list for.
                   Also removed the iCal invitations/cancellations from the templates of users
                   on the wait-list so that there is no confusion, as previously users who
                   were on the wait-list when the date of a seminar was changed received an
                   email saying that the session you are booked on has changed along with an
                   iCal invitation which was misleading.

    TL-16909       Increased the limit for the defaultid column in hierarchy scale database tables

                   Previously the defaultid column in the comp_scale and goal_scale tables was
                   a smallint, however the column contained the id of a corresponding
                   <type>_scale_values record which was a bigint. It is highly unlikely anyone
                   has encountered this limit, unless there are more than 32,000 scale values
                   on your site, however the defaultid column has been updated to remove any
                   possibility of a conflict.

    TL-16914       Added contextual details to the notification about broken audience rules

                   Additional information about broken rules and rule sets are added to email
                   notifications. This information is similar to what is displayed on
                   audiences "Overview" and "Rule Sets" tabs and contains the broken audience
                   name, the rule set with broken rule, and the internal name of the broken
                   rule.

                   This will be helpful to investigate the cause of the notifications if a
                   rule was fixed before administrator visited the audience pages.

    TL-16921       Converted utc10 Totara form field to use the same date picker that the date time field uses

                   This only affects desktop browsers

    TL-17149       Fixed undefined index for the 'Audience visibility' column in Report Builder when there is no course present
    TL-17214   +   InnoDB upgrade tool and deprecated authentication plugins were removed from distribution

                   The following authentication plugins were removed:
                    # auth_fc
                    # auth_imap
                    # auth_nntp
                    # auth_none
                    # auth_pam
                    # auth_pop3

                   The following upgrade tool was removed: tool_innodb

    TL-17232       Made the "Self-registration with approval" authentication type use the standard notification system

                   The "Self-registration with approval" authentication plugin is now using
                   standard notifications instead of alerts, for "unconfirmed request" and
                   "confirmed request awaiting approval" messages. A new notification was also
                   added for "automatically approved request" messages when the "require
                   approval" setting is disabled.

Bug fixes:

    TL-16549       Cancelling a multi-date session results in notifications that do not include the cancelled date

                   Changed the algorithm of iCal UID generation for seminar event dates. This
                   allows reliable dates to be sent for changed\cancelled notifications with
                   an attached iCal file that would update the existing events in the
                   calendar.

    TL-16555       Fixed email recipients not always being displayed for scheduled reports

                   Previously if you disabled a recipient option (audiences, users, emails)
                   existing items would remain on the scheduled report but not be displayed
                   making it impossible to remove them. Existing items are now displayed, but
                   new items can not be added for disabled recipient options.

    TL-16598       Fixed a problem with suspended users and the "ignore empty fields" setting in HR Import

                   When the deleted setting was set to "Suspend internal user", the "Empty
                   strings are ignored" setting was set and the suspend field in a CSV was
                   empty. It resulted in users becoming unsuspended. The suspended field is
                   now disabled and not imported when the deleted setting is "Suspend internal
                   user".

    TL-16820       Fixed the current learning block using the wrong course URL when enabling audience based visibility
    TL-16833       Added the 'Grades' link back into the 'Course Administration' menu
    TL-16838       Stopped reaggregating competencies using the ANY aggregation rule when the user is already proficient
    TL-16856       Fixed text area user profile fields when using Self-registration with approval plugin

                   Using text area user profile fields on the registration page was stopping
                   the user and site administrator from attempting to approve the account.

    TL-16858       Improved the location of the date time picker icon in the Report builder sidebar

                   This will require regeneration of the LESS for themes that use LESS
                   inheritance

    TL-16865       Fixed the length of the uniquedelimiter string used as separator for the MS SQL GROUP_CONCAT_D aggregate function

                   MS SQL Server custom GROUP_CONCAT_* aggregate functions have issues when
                   the delimeter is more than 4 characters.

                   Some report builder sources used 5 character delimiter "\.|./" which caused
                   display issues in report. To fix it, delimeter was changed to 3 characters
                   sequence: "^|:"

    TL-16878       Fixed the role attribute on notification and message icons

                   Previously the notification and message icons used an invalid "aria-role"
                   HTML attribute. This now uses the correct "role" HTML attribute

    TL-16882       Removed the "allocation of spaces" link when a seminar event is in progress
    TL-16920       Fixed the "show blank date records" option in date filters excluding null values

                   Reports that allow filtering records with blank dates were not being
                   retrieved if the date was null

    TL-16922       Fixed multiple enrolment types being displayed per course in the 'Course Completion' report source

                   The "Enrolment Types" column for the "Course Completion" report source was
                   previously displaying all the enrolment methods the user was enrolled via
                   across the whole site. For example if the user was enrolled in one course
                   via the program enrolment plugin, and in another course via manual
                   enrolment, both records in the report would say both "program" and "Manual
                   enrolment". The column now only shows the appropriate enrolment type for
                   the associated course.

    TL-16925       Fixed the calculation of SCORM display size when the Navigation panel is no longer displayed
    TL-17104       Fixed an error when disposing of left-over temporary tables in MS SQL Server 
    TL-17115       Fixed the time assigned column for the Record of Learning : Programs report source

                   The time assigned column was previously displaying the data for
                   timestarted, this patch has three main parts:

                   1) Changes the default header of the current column to "Time started" to be
                   consistent with what it displays
                   2) Adds a new column "Time assigned" to the report source that displays the
                   correct data
                   3) Switches the default column for the embedded report to the new "Time
                   assigned" column

                   This means any new sites will create the embedded report with the new
                   column, but any existing sites that want to display "Time assigned" instead
                   of "Time started" will have to go to Site administration > Reports > Report
                   builder > Manage embedded report and restore default settings for the
                   Record of Learning : Programs embedded report, or manually edit the columns
                   for the report.

    TL-17116       Firefox now shows the focused item in the Atto editor toolbar

                   When using Chrome, Edge and IE11, there is an indication of which toolbar
                   item is focused when using keyboard navigation in the toolbar. This issue
                   adds an indication to Firefox as well.

    TL-17207       Fixed a missing include in user/lib.php for the report table block
    TL-17221       Allowed non-standard JS files to be minified through grunt rather than manually
    TL-17229       Fixed the display of the page while modifying Site administrator role assignments

                   This page had invalid HTML causing all form controls to be in a single
                   column, instead of an add/remove 3 column

    TL-17230       Added a missing file requirement to the company goal userdata items
    TL-17234       Fixed an error while counting the userdata for a quicklinks block relating to a deleted user

                   When a user is deleted their records are removed from the context table,
                   causing the lookup being done by this function to throw a database error.

    TL-17259       Moved the previously hard-coded string 'Add tile' into a language string for Featured links templates

                   There was a hard-coded string in the main template in the Featured links
                   block, this has been shifted into the language strings file so that it can
                   now be translated and customised.

API changes:

    TL-16881   +   Update jQuery to 3.3.1

Contributions:

    * Ben Lobo at Kineo UK - TL-16549
    * Eugene Venter at Catalyst NZ - TL-16922
    * Russell England at Kineo USA - TL-17149

*/
