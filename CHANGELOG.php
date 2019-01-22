<?php
/*

Totara Learn Changelog

Release Evergreen (24th January 2019):
======================================

Key:           + Evergreen only

Security issues:

    TL-19900       Applied fixes for Bootstrap XSS issues

                   Bootstrap recently included security fixes in their latest set of releases.
                   To avoid affecting functionality using the current versions of Bootstrap,
                   only the security fixes have been applied rather than upgrading the version
                   of Bootstrap used.

                   It is expected that there was no exploit that could be carried out in
                   Totara due to this vulnerability, as the necessary user input does not go
                   into the affected attributes when using Bootstrap components. However we
                   have applied these fixes to minimise the risk of becoming vulnerable in the
                   future.

                   The Bootstrap library is used by the Roots theme.

    TL-19965       Corrected the encoding applied to long text feedback answers

                   Answers to long text questions for the feedback module may not have been
                   correctly encoded in some previous versions of Totara. The correct encoding
                   is applied where necessary on upgrade and is now also applied when a user
                   submits their answer.

New features:

    TL-7394    +   Added a new dynamic audience rule based on historic course completion dates

                   This new rule closely resembles the existing course completion rules, but
                   instead of comparing the user's current completion it checks the rule
                   against any archived completions in the course_completion_history table.

    TL-9209    +   Added a new dynamic audience rule based on user creation dates

                   This rule allows you to define an audience based on the 'timecreated'
                   column of a user's database record. Like existing date time rules, this can
                   either be compared to an entered date/time, or to the current time when the
                   rule is reaggregated.

Performance improvements:

    TL-4241        Converted the bulk query into chunk queries, within loading the list of users to be added/removed from an audience

Improvements:

    TL-8308    +   The certification report source now better supports aggregation

                   Previously the certification report source contained several required
                   columns in order to ensure user visibility was correctly applied.
                   These required columns led to aggregation within the report not working.
                   Thanks to improvements made in Totara 12 this could be refactored so that
                   the required columns were no longer required.
                   Visibility is still calculated accurately and aggregation is now working
                   for this report source.

    TL-17311   +   Converted seminar CSS to use LESS

                   This will require CSS to be regenerated for themes that use LESS
                   inheritance.

    TL-18759       Improved the display of user's enrolment status

                   Added clarification to the Status field on the course enrolments page. If
                   editing a user's enrolment while the corresponding enrolment module is
                   disabled, the status will now be displayed as 'Effectively suspended'.

    TL-19306       Added CSV delimiter setting for attendee bulk upload

                   1) Added an admin setting on event global settings that determines the
                      sitewide default CSV delimiter for seminar with the following options:
                      * Automatic <-- default for t13
                      * , (comma) <-- default for t12, this is a current default setting, for
                                      case a client using Totara API.
                      * ; (semi-colon)
                      * : (colon)
                      * \t (tab)
                   2) Added a CSV file delimiter under CSV file encoding setting with the same
                      options as above defaulting to the selection

    TL-19666       Extended functionality for the 'Allow user's conflict' option on seminar event attendees

                   Prior to this patch, the 'Allow user's conflict' option was only applied on
                   the seminar event roles to bypass the conflict check. However it was not
                   applied to the attendees of the seminar event. With this patch the
                   functionality is now applied for attendees as well.

    TL-19721       Made help text for uploading seminar attendees from file more intuitive

                   The help text displayed when adding users to a seminar event via file
                   upload was worded in a way that made it difficult to understand. There was
                   also a formatting issue causing additional fields in the bulleted list to
                   be indented too far.

                   The string 'scvtextfile_help' was deprecated, and replaced by a new string,
                   'csvtextfile_help', to make it clear that only one of the three possible
                   user-identifying fields (username, idnumber, or email) should be used and
                   that all columns must be present in the file.

                   Additionally, the code that renders the upload form was modified so that
                   all listed fields have the same list indent level.

    TL-19823       Updated appraisal summaries to show the actual user who completed a stage

                   The actual user who completes an appraisal stage is now recorded and shown
                   when viewing the appraisal summary. This shows when a user was 'logged in
                   as' another user and completed the stage on their behalf. This also
                   continues to show the original user who participated in the appraisal, even
                   after a job assignment change results in a change to which users fulfill
                   those participant roles at the time the appraisal summary is viewed.

    TL-19825       Added 'login as' real name column to the logstore report source
    TL-19848       Upgraded PHPUnit to version 7.5

                   This patch upgrades the PHPUnit version to 7.5. Two major versions lie in
                   between the last version and this upgrade.

                   The following backwards compatibility issues have to be addressed in custom
                   code:
                   1) All PHPUnit classes are now namespaced, i.e. 'PHPUnit_Framework_TestCase' is now 'PHPUnit\Framework\TestCase'
                   2) The following previously deprecated methods got removed:
                      * getMock(),
                      * getMockWithoutInvokingTheOriginalConstructor(),
                      * setExpectedException(),
                      * setExpectedExceptionRegExp(),
                      * hasPerformedExpectationsOnOutput()
                   3) The risky check for useless tests is now active by default.

                   The phpunit.xml configuration 'beStrictAboutTestsThatDoNotTestAnything' was
                   set to 'false' to keep the previous behaviour to not show risky tests by
                   default.

                   To make the transition easier all methods removed in PHPUnit were added in
                   the base_testcase class and the functionality is proxied to new methods of
                   PHPUnit. These methods now trigger a debugging message to help developers
                   to migrate their tests to the new methods.

                   Old class names were added to renamedclasses.php to support migration to
                   new namespaced classes.

                   More information about the upgrade to 7.5:
                    * [https://phpunit.de/announcements/phpunit-6.html]
                    * [https://phpunit.de/announcements/phpunit-7.html]

    TL-19852       Fixed the wording of the 'Try another question like this one' button in the quiz module

                   The "Try another question like this one" button has been renamed into "Redo
                   question". Help text for the "Allow redo within an attempt" quiz setting
                   has been updated to clarify its behaviour.

    TL-19896       The maximum width of Select HTML elements within a Totara dialogue is now limited by the size of the dialogue
    TL-19904       Added appraisal page and stage completion events for logging
    TL-19909       Removed limit on the number of options available when creating a dynamic audience rule based on a User profile field

                   When creating a dynamic audience rule by choosing one or more values of a
                   text input User profile field, there was a limit of 2500 options to choose
                   from.

                   This was an arbitrary limit, and has been removed.

                   Note that very large numbers of options (more than ~50,000) may have an
                   effect on browser performance during the selection process. Selecting a
                   large number of options (more than ~10,000 selections) may cause the
                   receiving script to run out of memory.

Bug fixes:

    TL-4458        Added multi-language support for position organisation custom field names in audience rules
    TL-14099   +   Fixed a bug in course completion determination when multiple enrolments were present

                   In the case when a user had multiple enrolments in the same course, and
                   course completion was determined by how many days the user was enrolled in
                   the course, the cron job that updated course completions would fail.

                   With this fix, multiple enrolments will be processed by cron as expected.

    TL-18732       Changed enrolment message sending for programs to be more consistent

                   If a program (or certification) is created with required course sets (all
                   optional) the program is marked as complete straight away for any assigned
                   users. Previously the enrolment message would not be sent to users in this
                   case. We now send the enrolment message to users even if the program is
                   complete.

    TL-18892       Fixed problem with redisplayed goal question in appraisals

                   Formerly, a redisplayed goal question would display the goal status as a
                   drop-down list - whether or not the user had rights to change/answer the
                   question. However, when the goal was changed, it was ignored. This patch
                   changes the drop-down into a text string when necessary so that it cannot
                   be changed.

    TL-19471       Fixed unavailable programs not showing in user's Record of Learning items when the user had started the program
    TL-19489       Ignore embedded reports for report-based catalog when the feature is off
    TL-19691       Expired images now have the expired badge stamped on top

                   This will require CSS to be regenerated for themes that use LESS
                   inheritance.

    TL-19728       Fixed the sending of duplicate emails on appraisal activation
    TL-19739       Fixed select filters when page switching on embedded reports with default values set

                   Previously if an embedded report had defined a default value for a select
                   filter, then changing that filter to 'is any value'  and hitting search
                   would correctly show all results, however if the report has multiple pages
                   then switching to any other page in the report would revert back to the
                   default value. The filter change in now maintained across pages.

    TL-19782       Fixed javascript regression in audience's 'visible learning'

                   Prior to this patch: the AJAX request was being sent twice to the server
                   when deleting a course from an audience's 'visible learning'. It caused the
                   second request to be regarded as an invalid request, because the first one
                   had already been processed and the record successfully deleted.

                   After this patch: the event will be triggered once in audience's 'visible
                   learning', and it will send only one AJAX request.

    TL-19791       Fixed an issue with audiences in course access restrictions

                   Previously the audience restrictions did not work when searching for
                   audience names which contained non-alphanumeric characters.

    TL-19797       Fixed minimum bookings notification being sent for cancelled events
    TL-19804       Fixed an issue where overridden grades were not reset during completion archiving
    TL-19811       Fixed a seminar's custom room not appearing in search results from a different seminar

                   Prior to this patch: A custom room (unpublished room) that had been used in
                   a seminar's event would appear in a search result from a query of a
                   different seminar.

                   With this patch: The custom room (unpublished room) will not appear in the
                   search result of a different seminar.

    TL-19813       Fixed a regression caused by TL-17450

                   TL-17450 caused a regression in the position of the Quiz and Lesson
                   activity menu blocks that made them appear full width. This undoes the
                   unintentional change in layout for these two activities.

    TL-19822       Fixed encoding of search text in catalogue

                   There was a problem which caused accented characters to be passed to the
                   server in an incorrect format when entered into the search text box in the
                   grid catalogue. This resulted in search not working correctly and has been
                   fixed.

    TL-19828       Fixed sanity check for external mssql database that checks that the specified table exists
    TL-19844       Fixed the position of the quick access menu for RTL languages

                   This will require CSS to be regenerated for themes that use LESS
                   inheritance.

    TL-19845       Fixed RTL when using gallery tiles in the featured links block

                   This will require CSS to be regenerated for themes that use LESS
                   inheritance.

    TL-19846       Fixed typo that caused Appraisal detail report to throw a fatal error
    TL-19847       Fixed removing attendees of past seminar events

                   User could not be removed from past events using the 'Attendees' tab. This
                   is fixed now, however, the user who performs the action will need to have
                   the 'mod/facetoface:signuppastevents' permission to do this.

    TL-19849       Fixed bug in report builder that prevented graphing of grade percentages

                   User reports created in Totara 10 and 11 allowed the Course Completion
                   'Grade' column to be displayed as a graph at the top of the report.

                   Prior to this patch, this behaviour was prevented in Totara 12. It is now
                   possible to graph these grades again.

    TL-19856       Fixed missing data attributes bug affecting search functionality for seminar rooms and assets
    TL-19857       Fixed toggling of restrictions on quiz questions

                   An invalid flex icon was being specified so when toggling restrictions on
                   quiz questions the icon would disappear and be replaced with the alt text
                   and then switch to a different icon set. Toggling the restriction is now
                   consistent and works as expected.

    TL-19865       Fixed sort order for question scale values in user data export for review questions
    TL-19866       Fixed date assigned shown on the program detail page

                   When a user is assigned to a program that they would have completed in the
                   past due to the courses in that program being complete, the date they were
                   assigned to the program was incorrectly displayed. Previously this date was
                   the date they completed the program (in the past). This now displays as the
                   actual date they were assigned, which is consistent with the 'Date
                   assigned' column in the Program record of learning report.

    TL-19871       Fixed bug that placed top-level site course in catalogue.

                   The Totara homepage is a special course that can hold activities. Adding an
                   activity to it caused it to be listed in the Find Learning catalogue, with
                   a blank tile.

                   This has been fixed by preventing the site from being included in the
                   catalogue when an activity is added or removed from the homepage, and by
                   excluding courses with the 'site' format from the catalogue's periodic cron
                   update script.

                   If you have a blank tile in the catalogue because of this issue, it will be
                   removed on the next hourly cron run.

    TL-19872       Fixed a PHP debug message when a quick access menu group has been deleted
    TL-19873       Fixed PHP error in the report with a 'course (multi line)' filter in the saved search where selected course has been deleted
    TL-19877       Fixed bug where multi-framework rules were flagged as deleted in Audiences dynamic rules
    TL-19894       Added batch processing of users when being assigned to a Program
    TL-19903       Fixed removing value of hierarchy multi select custom fields using HR Import

                   When syncing Positions or Organisations and changing the value of a
                   multi-select custom field, if a field was left blank then it would
                   incorrectly be ignored instead of removing the value (adhering to the empty
                   field behaviour setting). Empty fields for this type of custom field now
                   remove the existing value as expected.

    TL-19908       Fixed a debug notice being generated when adding deferred Program assignments
    TL-19912       Fixed bug that prevented learners from accessing the catalogue when the Miscellaneous category was hidden
    TL-19917       Fixed wrong table reference in the main menu
    TL-19922       Enabled Rooms/Assets 'Manage searches' buttons

                   When managing rooms or assets, it is possible to save a search for
                   rooms/assets by name and/or availability, and to share those searches with
                   other managers. In order to edit or delete saved searches, the manager
                   clicks on a "Manage searches" button.

                   Prior to this patch, clicking the button did nothing. The button now works
                   correctly, opening the Manage searches dialogue.

    TL-19923       Fixed due date format in "Competency update" emails

                   When a manager changes the due date of a competency in a learner's Learning
                   plan, the email sent to the learner now contains the correct dates.

    TL-19947       Increased the limit on number of choices available in autocomplete menu when restricting an activity by audience
    TL-19953       Fixed missing icon for appraisal previews

                   This was supposed to be fixed in TL-19780 but it still failed in IE11
                   because of the way IE behaves with missing icons compared to other
                   browsers.

                   This has now been fixed so that IE also displays the preview correctly.

    TL-19961       Removed exception in HR Import clean_fields() function when a field is not used

                   Fields can be present in HR Import source CSV file that are not required
                   and are outside of the list of possible fields to import. We do not need to
                   clean these fields as they are not used and have removed the execution
                   generated.

    TL-19982       Fixed duplication of seminar booking approval request message when learner has both manager and temporary manager set
    TL-20007       Fixed an error with audience rules relying on a removed user-defined field value

                   This affected the 'choose' type of audience rules on text input user custom
                   fields. If a user-defined input value was used in the rule definition, and
                   that value was then subsequently removed as a field input, a fatal error
                   was thrown when viewing the audience. This is now handled gracefully,
                   rather than displaying an object being used as an array error the missing
                   value can now be removed from the rule.

Contributions:

    * Jamie Kramer, Elearning Experts - TL-7394


Release Evergreen (19th December 2018):
=======================================

Key:           + Evergreen only

Important:

    TL-17182       Fixed the use of the "moodle/course:viewhiddencourses" capability in report builder reports

                   Previously, users with "moodle/course:viewhiddencourses" capability could
                   not see hidden courses and related content with enabled "Audience
                   visibility" consistently in Report Builder reports (including embedded
                   reports). This permission was largely applicable in System or Course
                   context but had no effect in Course category and other context levels.

                   Also this rule had no effect when Course Audience-based Visibility was set
                   to "Enrolled users only" or "Enrolled users and members of the selected
                   audiences".

                   Now, each course-related record is checked against this capability in the
                   course and all parent contexts regardless of Audience-based Visibility
                   setting.

Security issues:

    TL-19593       Improved handling of seminar attendee export fields

                   Validation was improved for fields that are set by a site admin to be
                   included when exporting seminar attendance, making user information that
                   can be exported consistent with other parts of the application.

                   Permissions checks are now also made to ensure that the user exporting has
                   permission to access the information of each user in the report.

Improvements:

    TL-19292       Added behat test coverage to content marketplace filters
    TL-19442       Enable course completion via RPL in Programs when the course is not visible to the learner

                   Previously when a course was not visible to the learner it could not be
                   marked as complete in the required learning UI. Now users with permission
                   to mark courses as complete can grant RPL even if the course is not
                   available to the learner.

    TL-19448       Modified grid catalogue search placeholder text
    TL-19647       Changed the title of an email sent out to confirm trainer for waitlisted seminar event

                   Prior to this patch: When a trainer was added into a waitlisted seminar
                   event, an email would be sent out to the trainer. The title of the email
                   was confusing because it included 'unknown date' and 'unknown time' (due to
                   waitlisted event).

                   With this patch: These keywords 'unknown date' and 'unknown time' are no
                   longer in the title of confirmation email sent out to the trainer. Instead,
                   a string 'location and time to be announced later' appears in the title.
                   This is achieved by the introduction of new placeholder "[eventperiod]"
                   that converts to "[starttime]-[finishtime], [sessiondate]" when date is
                   present and to "location and time to be announced later" when date is not
                   present.

                   To update existing notifications, replace placeholders
                   "[starttime]-[finishtime], [sessiondate]" with "[eventperiod]" manually.

Bug fixes:

    TL-18858       Fixed mismatching date format patterns in the Excel writer

                   Previously when exporting report builder reports to Excel, any dates that
                   were not otherwise explicitly formatted would be displayed in the mm/dd/yy
                   format, regardless of the user's locale. These dates are now formatted to a
                   default state so that they are displayed as per the user's operating system
                   locale when opening the Excel file.

    TL-18892       Fixed problem with redisplayed goal question in appraisals

                   Formerly, a redisplayed goal question would display the goal status as a
                   drop-down list - whether or not the user had rights to change/answer the
                   question. However, when the goal was changed, it was ignored. This patch
                   changes the drop-down into a text string when necessary so that it cannot
                   be changed.

    TL-18903       Deprecated the facetoface_fromaddress setting as all emails are now sent from the no reply address

                   The TL-13922 changes were required to deprecate the facetoface_fromaddress
                   setting.

    TL-19305       Fixed manager allocations on full seminar events

                   Previously when managers allocated users to a full seminar event, they
                   could end up in the "Approval required" state instead of being wait-listed
                   or overbooking the event.

    TL-19311       Added event observers for course restoration to update the course format

                   Prior to this patch, when uploading a course using "Restore from this
                   course after upload" where the existing and uploaded course formats differ,
                   there was no action to update the course activities based on its format.
                   After this patch, the course activities will be updated, via the event
                   observer.

    TL-19373       Added two new seminar date columns which support export

                   The new columns are "Local Session Start Date/Time" and "Local Session
                   Finish Date/Time" and they support exporting to Excel and Open Document
                   formats.

    TL-19481       Fixed the course restoration process for seminar event multi-select customfields

                   Previously during course restoration, the seminar event multi-select
                   customfield was losing the value(s) if there was more than one value
                   selected.

    TL-19485       Made tables scrollable when on iOS

                   This will require CSS to be regenerated for themes that use LESS
                   inheritance.

    TL-19507       Expand and collapse icons in the current learning block are now displayed correctly in IE11

                   Previously when someone using IE11 was viewing the current learning block
                   with a program inside it, the expand and collapse icons were not displayed
                   if there was more than one course in the program.

                   This will require CSS to be regenerated for themes that use LESS
                   inheritance

    TL-19579       Enabled multi-language support on the maintenance mode message
    TL-19615       Fixed a permission error when a user tried to edit a seminar calendar event
    TL-19679       Removed remaining references to cohorts changing to audiences
    TL-19690       Fixed bug on Seminar Cancellations tab that caused Time Signed Up to be 1 January 1970 for some users

                   When a Seminar event that required manager approval was cancelled,
                   attendees awaiting approval would show 1 January 1970 in the Time Signed Up
                   column of the Attendees View Cancellations tab.

                   The Time Signed Up for attendees awaiting approval when the event was
                   cancelled is now the date and time that attendance was requested.

    TL-19692       Fixed a naming error for an undefined user profile datatype in the observer class unit tests
    TL-19693       Role names now wrap when assigning them to a user inside a course

                   This will require CSS to be regenerated for themes that use LESS
                   inheritance.

    TL-19694       Fixed a capability notification for the launch of SCORM content

                   This fixed a small regression from TL-19014 where a notification about
                   requiring the 'mod/scorm:launch' capability was being displayed when it
                   should not have been.

    TL-19696       Fixed the handling of calendar events when editing the calendar display settings of a seminar with multiple sessions

                   Previously with Seminar *Calendar display settings = None* and if the
                   seminar with multiple events was updated, the user calendar seminar dates
                   were hidden and the user couldn't see the seminar event in the calendar.

    TL-19698       Fixed appraisal preview regression from TL-16015

                   TL-16015 caused a regression in which previewing the questions in an
                   appraisal displayed the text "Not yet answered". This patch fixes this and
                   now the actual UI control appears; e.g. for a file question, it is a file
                   picker, and for a date question, it is a date entry field.

                   Note that although values can be "entered" into the UI controls, nothing is
                   saved when closing the preview window.

    TL-19726       Fixed the string identifier that has been declared incorrectly for facetoface's notification scheduling
    TL-19760       Fixed multi-language support for custom headings in Report Builder
    TL-19778       Fixed an error in seminar report filters when generating SQL for a search

                   Prior to this patch: the columns relating to the filters could not be added
                   because these columns were in the wrong place, they would only be added if
                   the GLOBAL setting (facetoface_hidecost) of the seminar was set to FALSE.
                   Therefore it was causing the sql error due to the columns and sql joins not
                   being found.

                   With this patch: the columns are now put in the correct place, and these
                   columns will no longer be affected by the GLOBAL setting
                   facetoface_hidecost.

    TL-19779       Fixed an error when signing up to a seminar event that requires approval with no job assignment and temporary managers disabled

Contributions:

    * Ghada El-Zoghbi at Catalyst AU - TL-19692
    * Learning Pool - TL-19779
    * Michael Dunstan at Androgogic - TL-19292
*/
