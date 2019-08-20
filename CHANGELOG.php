<?php
/*

Totara Learn Changelog

Release Evergreen (22nd August 2019):
=====================================

Key:           + Evergreen only

Important:

    TL-20274   +   Introduced minimum required proficiency setting for competency scales

                   Competency scales now have a value that is considered the minimum a user
                   must achieve to be considered proficient. Values are no longer individually
                   set as proficient or not proficient, but instead will respect this setting
                   on the scale.

                   This will be set for existing scales automatically on upgrade.

                   IMPORTANT: Upgrade will be blocked if the proficient values in the scale
                   are not ordered correctly (where there are non-proficient values that are
                   higher on the scale than proficient ones). If that is the case, sites can
                   be taken back to a release that contains TL-21175 where the proficient
                   setting on individual scale values can be modified in order to fix this.

Security issues:

    TL-8385        Fixed users still having the ability to edit evidence despite lacking the capability

                   Previously when a user did not have the 'Edit one's own site-level
                   evidence' capability, they were still able to edit and delete their own
                   evidence.

                   With this patch, users without the capability are now prevented from
                   editing and deleting their own evidence.

    TL-21743       Prevented invalid email addresses in user upload

                   Prior to this fix validation of user emails uploaded by the site
                   administrator through the upload user administration tool was not
                   consistent with the rest of the platform. Email addresses were validated,
                   but if invalid they were not rejected or fixed, and the invalid email
                   address was saved for the user.

                   This fix ensures that user email address validation is consistent in all
                   parts of the code base.

    TL-21928       Ensured capabilities are checked when creating a course using single activity format

                   When creating a course using the single activity course format, permissions
                   weren't being checked to ensure the user was allowed to create an instance
                   of an activity. Permissions are now checked correctly and users can only
                   create single activity courses using activities they have permission to
                   create.

Performance improvements:

    TL-21841       Improved performance of filtering by organisation in Report builder

Improvements:

    TL-18671       Added Totara 13 environment requirements including new check for 32-bit systems

                   Totara 13 (evergreen) and onwards will now require:
                    * PHP 7.2.10 or higher
                    * PostgreSQL 9.6 or higher
                    * MySQL 5.7.21 or higher
                    * MSSQL Server 2017 or higher

    TL-18786   +   Added support for Chart.js in Report builder
    TL-20924   +   Updated PHPMailer to version 6.0.7
    TL-20996   +   Improved the consistency of sanitisation for user email address
    TL-21098   +   Implemented job assignment GraphQL services and converted the profile page

                   This is a technical improvement, introducing new GraphQL services for job
                   assignments and converting the profile interface list of jobs to use the
                   new services.

                   The following types have been added:
                   * core_user
                   * totara_job_assignment
                   * totara_hierarchy_position
                   * totara_hierarchy_position_type
                   * totara_hierarchy_position_framework
                   * totara_hierarchy_organisation
                   * totara_hierarchy_organisation_type
                   * totara_hierarchy_organisation_framework

                   The following queries have been introduced:
                   * totara_job_my_assignments
                   * totara_job_assignments
                   * totara_job_assignment

                   The following mutations have been introduced:
                   * totara_job_move_assignment
                   * totara_job_sort_assignments
                   * totara_job_delete_assignment
                   * totara_job_create_assignment

    TL-21437       Added button to allow manual downloading of site registration data

                   It is now possible to manually download an encrypted copy of site
                   registration data from the register page, in cases where a site cannot be
                   registered automatically.

    TL-21469       Improved the fade transition functionality in the gallery tile of the Featured links block

                   The fade transition in the gallery tile had a white flash that was quite
                   noticeable. The updates changed the background colour to grey (#666666)
                   from white (#FFFFF) to make it less noticeable.

                   This will require CSS to be regenerated for themes that use LESS
                   inheritance.

    TL-21486   +   Added an 'Edit event' button to seminar event details tab
    TL-21487   +   Added ability to mark seminar event and session attendance at different times

                   The previous 'Mark attendance at' option is now separated into two options
                   - an option as to when you can mark Session Attendance AND a separate
                   option for when you can mark Event Attendance.

    TL-21565       Improved long category name tiles display in the Grid catalogue

                   Previously the category name length affected tile size. This has now been
                   fixed so that tiles for courses in any category are the same width.

                   This will require CSS to be regenerated for themes that use LESS
                   inheritance.

    TL-21569   +   Changed the standard edit icon to a plain pencil icon
    TL-21600   +   Improved the grid items functionality when reducing the browser size

                   Previously grid items had some white space on the right (did not fill up
                   the width). This has now been fixed.

                   This will require CSS to be regenerated for themes that use LESS
                   inheritance.

    TL-21708       Ensured a new resource_link_id is generated for users re-attempting LTI activity

                   Previously, when course completion was archived, LTI submissions were
                   reset, but a new resource_link_id was not generated. This ID is used by
                   external tool providers to ensure users can start a new attempt of the
                   activity. With this change, when completion is archived, historic LTI
                   submission records are stored, which allows the generation of a new
                   resource_link_id for each new attempt.

    TL-21739   +   Added option to display seminar room building and address values in addition to room name
    TL-21772       Added setting to prevent automatic progression of dynamic appraisals with missing roles

                   A new setting 'Dynamic Appraisals Automatic Progression' was added, which
                   is on by default. When on, the previous behaviour is maintained, which
                   causes appraisals to automatically progress to the next stage if one or
                   more required roles are not filled (assuming at least one required role is
                   filled and all filled required roles have completed the stage). When
                   dynamic appraisals is enabled and the new setting is switched off, all
                   required roles need to complete the stage. Empty required roles will need
                   to have users assigned before the stage can be progressed.

Bug fixes:

    TL-8836        Ensured Program course set completion records are cleaned up after deleting a course set

                   Previously when deleting a course set from a program, any related program
                   completion records were not being removed, leading to orphaned records in
                   the prog_completion table. The associated prog_completion records are now
                   removed when a course set is deleted and existing orphaned records are
                   cleaned up by an upgrade.

    TL-20590       Fixed usability problem with group delete control on the quick access menu settings page

                   The ‘X’ icon for deleting an entire menu group was easily misconstrued
                   as an icon to trigger closing of the expanded group accordion. The delete
                   function is now accessed via a text link after clicking a cog icon, which
                   reduces the likelihood of a user inadvertently deleting an entire menu
                   group.

    TL-20951       Ensured program completion records are cleaned up correctly after a program is deleted

                   Records in the tables prog_completion, prog_completion_history and
                   prog_completion_log were being orphaned when the related program was
                   deleted. These records are now removed when the program is deleted.

    TL-21234       Added totara_visibility_where for Audience Based Visibility to Upcoming Certifications block

                   Before this patch, when using Audience Based Visibility, the block would
                   display regardless of how the visibility is set.

                   The block now adheres to visibility either set via Audience Based
                   Visibility or via Show/Hide in the Certification settings.

    TL-21358       Fixed a permission error preventing a user from viewing their own goals in complex hierarchies

                   Prior to this fix if a user had two or more job assignments where they were
                   the manager of, and team member of, another user at the same time, they
                   would encounter a permissions error when they attempted to view their own
                   goals pages.
                   This has now been fixed, and users in this situation can view their own
                   goals.

    TL-21378   +   Updated seminar 'Message users' tab to respect 'User identity' settings when displaying lists of users
    TL-21400       Ensured 'totara/plan:accessanyplan' and 'totara/plan:manageanyplan' capabilities work correctly

                   Previously, if a learning plan template permission was set to 'Deny' for a
                   manager, users with the 'totara/plan:accessanyplan' and
                   'totara/plan:manageanyplan' capabilities were also denied. This patch
                   ensures that these capabilities take precedence over how the learning plan
                   templates permissions have been set.

    TL-21425       Fixed seminar calendar events displaying a user booked message even after a user cancels their booking
    TL-21436   +   Updated seminar date/time columns in Report Builder to use the correct timezone

                   Seminar sessions can be set to display their start and end time in a
                   particular timezone, known as the event timezone. Aside from the start and
                   end time, all other seminar date/time values (such as the signup period
                   start and end time, or the date and time when a user declares interest) use
                   the system timezone.

                   This update causes all seminar-related date/time values, except for the
                   session start and end times, to be displayed using the system timezone.

    TL-21453       Ensure HTML entities display correctly in subject line of sent emails

                   The core_text::entities_to_utf8() function is now being used in the
                   email_to_user() function for the subject of the email.

    TL-21465       Prevented MSSQL Server from locking during some backup and restore operations
    TL-21508       Fixed bug causing ghost certifications to remain in Grid catalogue
    TL-21519       Fixed sort order on 'All appraisals' page

                   Prior to this patch, the 'All appraisals' page had an undefined sort order
                   for appraisals with multiple learners assigned when viewed by a manager.
                   This patch adds alphabetical sorting by learner's name, after the existing
                   sorting by status and appraisal start date.

    TL-21577       Fixed bug preventing seminar signup when a user has an inactive course enrolment
    TL-21581       Added 'debugstringids' configuration setting support to core_string_manager

                   Fixed issue when "Show origin of languages strings" in Development >
                   Debugging is enabled, in some rare cases, not all strings origins were
                   displayed.

    TL-21584       Ensured 'Assigned roles' menu is displayed in program administration to users with correct permissions

                   Previously, someone with a 'moodle/role:assign' capability assigned at the
                   program level had no link in the program administration to assign other
                   roles at that level. This option was displayed to site administrators
                   only.

                   This has been fixed and any user with the 'moodle/role:assign' capability
                   in a program can now assign other roles in the context of that program.

    TL-21585       Fixed a table name collision within the Grid catalogue when using two category filters

                   If the catalogue was configured to display both the category panel filter
                   and the category browse filter, and a user select a category in each, then
                   a fatal error would be encountered due to a table name collision as both
                   filters used the same table alias.

                   Each filter now has a unique table alias.

    TL-21615       Fixed the render_image_icon() function maintained for third-party plugin compatibility
    TL-21617       Fixed bug in completion editor caused by incomplete activity creation

                   Uploading a SCORM file via drag-and-drop on the course homepage creates a
                   record in the course_modules table, which is later updated with the ID of
                   the activity when created. However, an invalid file (or other failure)
                   could cause the activity creation process to abort, leaving a
                   course_modules record with no associated activity.

                   With this release, any orphaned SCORM course_modules records are cleaned
                   up, and the course module deletion code now properly deletes such records.

    TL-21621       Fixed the inconsistent display of information under the 'Answers tolerance parameters' section in the Calculated multichoice question type
    TL-21623       Fixed an issue where forum discussions RSS was incorrectly fetching deleted discussions instead of active ones
    TL-21630       Ensured value in the 'Is user assigned?' column takes exception resolution into account

                   If any user program or certification assignments generated exceptions which
                   have not been resolved, the "Program/Certification Completion" report will
                   display such users as not being currently assigned to the
                   program/certification.

    TL-21631   +   Fixed inconsistent booking status in events and sessions report

                   Previously events with booking status 'closed' were showing as open in the
                   events and session reports, now the 'booking status' column is updated in
                   both reports to reflect the actual booking state.

    TL-21670       Fixed JavaScript error when all available blocks have been added to a page
    TL-21680       Fixed undefined adhoc task execution order

                   Previously, the execution order of adhoc tasks was arbitrary, which could
                   result in random PHPUnit failures. This has been fixed, the execution order
                   is now predictable.

    TL-21681       Fixed event context level checks when purging glossary entries
    TL-21683       Fixed the display of the Grid catalogue when viewing on a mobile screen with no filters applied

                   Previously 'show filters (-1)' was being  displayed on the Grid catalogue
                   when viewing on a mobile screen with no filters applied, now the 'show
                   filters' text is displayed as expected.

    TL-21684       Fixed seminar event roles not being deleted when associated user is deleted
    TL-21698       Fixed learners' ability to request learning items to be added to their learning plans based on the manager-driven workflow
    TL-21707       Fixed seminar 'Allow cancellations until specified period' setting

                   If the seminar 'Allow cancellations' setting was set to 'Until a specified
                   period', learners could still cancel their seminar signups at any time
                   until the start of the event. This has been fixed, and the setting now
                   works as expected.

    TL-21709       Fixed JavaScript initialisation from being incorrectly called twice for the Learning Plan block which resulted in an error
    TL-21727       Fixed missing image on course creation workflow page

                   This patch fixes an image that was missing on the course creation workflow
                   page when a content marketplace was enabled.

    TL-21775       URL validation and cleaning was updated to accept previously rejected URLs

                   Prior to this patch, URL validation code was rejecting some valid URLs,
                   such as the Grid Catalogue URL, with a query string including array
                   parameters.

                   With this patch the featured link block now supports URLs with a query
                   string that has parameter values as an array, such as those used in Grid
                   Catalogue URLs. The same applies to the quick links block that was
                   converted to use the new URL form field with the updated validation.

    TL-21779       Prevented users from signing up for a seminar outside of the designated sign-up period
    TL-21820       Removed an arbitrary limit on the number of course and program custom icons allowed
    TL-21821       Course completion caching was redesigned to be more reliable
    TL-21854       Fixed an issue where some Seminar attendees requiring manager approval could not be approved by their manager

                   When the 'Users Select Manager' setting is enabled for seminars, and a user
                   signing up for a seminar does not select a manager when requesting
                   approval, then a notice with an approval URL is sent to their immediate
                   manager(s).

                   Previously while managers who could approve any booking request would be
                   able to use the URL to approve the request, managers who did not have that
                   capability could not.

                   This has now been fixed.

    TL-21879       Fixed quiz navigation block where clicking on a question link did not scroll to the question on the page that required scrolling
    TL-21886       Fixed typos in the reportbuilder language strings

                   The following language strings were updated:
                   - reportbuilderjobassignmentfilter
                   - reportbuildertag_help
                   - occurredthisfinancialyear
                   - contentdesc_usertemp

API changes:

    TL-19892   +   Abandoned DbUnit extension for PHPUnit has been removed

                   phpunit_ArrayDataSet class no longer extends AbstractDataSet from DbUnit.
                   Any PHPUnit tests in customisations that may be failing due to this change
                   will need to be fixed by the developers.

    TL-21563   +   Removed portfolio_picasa and repository_picasa plugins that have been deprecated by Google

                   In January 2019, Google deprecated its Picasa Web Albums Data API and
                   disabled all associated OAuth scopes. In March 2019, the Picasa Web Albums
                   API was completely turned off. We've removed the associated plugin and
                   repository as they will no longer be functional.

    TL-21711   +   Extracted Report Builder content code into autoloaded classes \totara_reportbuilder\rb\content\*

Contributions:

    * Carlos Jurado at Kineo UK - TL-21615
    * Dustin Brisebois at Lambda Solutions - TL-21617
    * Jo Jones at Kineo UK - TL-21581
    * Michael Geering at Kineo UK - TL-21854


Release Evergreen (17th July 2019):
===================================

Key:           + Evergreen only


API changes:

    TL-20548   +   'runTemplateJS()' now returns an ES6 promise

                   The 'runTemplateJS' function in the core/templates AMD library now returns
                   an ES6 Promise once all UI components have been initialised

    TL-21193   +   Added Laravel-like Query Builder

                   This patch introduces a query builder which abstracts querying the database
                   on top of the DML layer. The query builder is inspired by Laravel’s
                   Query Builder (found here: https://laravel.com/docs/master/queries) and provides a
                   similar feature set. It provides a consistent fluent interface. Internally
                   it uses the DML layer to execute queries so database compatibility and
                   low-level query integrity is ensured. The query builder provides at least
                   the same functionality as the DML layer. It should be possible to
                   substitute existing DML actions with it, as well as cover more complex
                   cases which are only possible via raw SQL queries at the moment.

                   Full documentation is available here:
                   https://help.totaralearning.com/display/DEV/Query+builder

    TL-21222   +   Added support for deferring the creation of foreign keys

                   This improvement extends TL-21024 which added support for enforcing foreign
                   key relationships within install.xml.

                   It is now possible to mark a foreign key relationship as deferred within
                   install.xml, causing the system to skip the creation of the foreign key
                   during installation. The developer is then responsible for creating the
                   foreign key at the right time within an install.php file.

    TL-21230   +   Added a new transaction function in DML which accepts a Closure

                   The new 'transaction()' method accepts a Closure which is automatically
                   wrapped in a transaction. This is an alternative syntax to the traditional
                   transaction handling.

    TL-21240   +   Extracted class 'program_utilities' into its own autoloaded class '\totara_program\utils'
    TL-21256   +   Nested transactions can be safely rolled back

                   Previously transaction rollbacks were not supposed to be used from
                   non-system code and they were not allowed at all in nested transactions.

                   Rollback of individual nested transactions is now fully supported, and it
                   is also not required to supply an exception when rolling back nested or
                   main transactions.

    TL-21288   +   Relative file serving now facilitates file serving options including 'allowxss'
    TL-21327   +   Extracted program exceptions code into autoloaded classes \totara_program\exception\*
    TL-21368   +   Implemented a generic formatter to format fields of objects

                   A formatter can be implemented for an existing object, for example a record
                   from the database. It defines a map using field names for the keys and
                   field format functions for the values. The formatter will get the value
                   from the object, run it through the format function defined in the map and
                   return the formatted value. Currently we support a text (using
                   format_text()), a string (using format_string()) and a date formatter.
                   Custom field formatters can easily be implemented extending the base field
                   formatter.

                   The existing helper functions format_text() and format_date()
                   in \core\webapi\execution_context were deprecated in favour of the new
                   field formatters \totara_core\formatter\field\text_field_formatter
                   and \totara_core\formatter\field\date_field_formatter.

                   Full documentation can be found here:
                   https://help.totaralearning.com/display/DEV/Formatters

    TL-21370       Method resetAfterTest() in PHPUnit tests has been deprecated

                   Since the introduction of parallel PHPUnit testing the order of test
                   execution is no longer defined, which means that tests cannot rely on state
                   (database and file system) to be carried over from one test into another.

                   Existing PHPUnit tests need to be updated to prepare data at the beginning
                   of each test method separately.

Performance improvements:

    TL-21541       The source filter for report builder sources has been optimised

                   Previously the options for this filter were loaded, even when not needed.
                   This was an expensive operation, often done needlessly. The options are now
                   only loaded when absolutely needed.

Improvements:

    TL-17691       Added site policies to the self-registration process

                   To comply with GDPR policies, when self-registration is enabled, new users
                   are now required to accept mandatory site policies before being able to
                   request a new account, as apposed to the users only viewing the site
                   policies after registering and logging in.

    TL-17745       Improved the program assignments user interface to better handle a large number of assignments

                   The previous user interface for program assignments would load every
                   assignment onto a single page, and in some situations where a very large
                   number of assignments were added to a single program or certification the
                   page would time out on load. The page now has a search, and filter, and
                   prevents too many records being loaded at the same time.

    TL-18678   +   Improved course selector form element for changing a 'Recurring Course' in programs content

                   Prior to this change all courses were loaded into a single dropdown, which
                   could lead to performance issues on sites with a large number of courses.
                   This dropdown has now been replaced with the standard course selector
                   dialog already used in selecting courses for program course sets.

    TL-19799   +   Nonfunctional Google Fusion export options were removed
    TL-20418   +   Added seminar attendance 'CSV export for Upload' feature

                   Following on the ability to upload seminar attendance in the last release,
                   it is now possible to download a seminar attendance report that is already
                   correctly formatted for upload.

                   Trainers can use the new 'CSV export for Upload' to mark event attendance,
                   and optionally grade if manual event grading is enabled, in bulk. The file
                   can then be uploaded with no further changes to column layout or header
                   names.

    TL-20425   +   Updated seminar event dashboard and course view

                   This patch contains several improvements to the seminar event dashboard and
                   the course activity view, including:
                    * Added 'Previous events time period' options to be able to display only
                      past events in the specific time period
                    * Redesigned the filter bar with tool-tips and icon
                    * Added new filters: booking status, attendance tracking status
                    * Reverted the change in TL-19928 (February 2019 release); the seminar
                      event dashboard is now back to two tables: one is for upcoming or ongoing
                      events, the other is for past or cancelled events
                    * Redesigned session list table
                    * Rearranged table columns
                    * Broke down event status into three types: event time, event booking
                      status, and user booking status

    TL-20760       Added support for search metadata within Courses, Programs, and Certifications.

                   New text field added to Courses, Programs, and Certifications settings
                   where search keywords can be added. These keywords will not be displayed
                   anywhere on pages but will be used in Full Text Search.

                   By default these fields are empty.

    TL-20761       Added wildcard support for full text search in catalog

                   When asterisk "*" is placed as a last character of a single keyword in
                   catalog it will return all partial matches starting with the given
                   keyword.  Asterisk can be placed only in the end of keyword search (this
                   is limitations of wildcard support in databases) and at this stage only
                   single keywords are supported (no whitespaces).

    TL-20834       Enabled unaccented Full Text Search in catalog

                   PostgreSQL and MS SQL have built in support for accent insensitive full
                   text searches.

                   By default, database configuration is used (typically accent sensitivity is
                   on).

                   To change accent sensitivity of full test searches for either PostgreSQL or
                   MS SQL you can set the
                   following options in config.php:
                   $CFG->dboptions['ftsaccentsensitivity'] = true; // Accent sensitive search
                   $CFG->dboptions['ftsaccentsensitivity'] = false; // Accent insensitive
                   search

                   After changing the accent sensitivity setting you need to run the following
                   scripts in the listed order:
                   php admin/cli/fts_rebuild_indexes.php
                   php admin/cli/fts_repopulate_tables.php

    TL-20886       Added ngram support for MySQL full text search

                   Added support of ngram in MySQL. ngram is a Full Text parser that mainly
                   designed to support Chinese, Japanese, and Korean (CJK) langauges. The
                   ngram parser tokenises a words into a contiguous sequence of n-characters.
                   More information about ngram can be found in MySQL documentation.

                   While it is designed more for CJK languages, it is also useful to parse
                   text on languages that use words concatenation, like German or Swedish.
                   However, it can produce large number of false-positive search results
                   (albeit with lower rating), so doing proper testing after enabling is
                   recommended.

                   This support is not enabled by default. To enable ngram support, add option
                   into your config.php:

                   $CFG->dboptions['ftsngram'] = true;

                   and run  FTS scripts to re-index content:

                   php admin/cli/fts_rebuild_indexes.php
                    php admin/cli/fts_repopulate_tables.php

    TL-21056       Added a warning about incompatible column selection in the report builder

                   In some cases, a combination of columns selected in a report source may
                   have caused unexpected results or a broken report. This usually happened
                   when a column that already relies on the aggregated data internally (e.g.
                   'Course Short Name' in the 'Program Overview' report) was combined with
                   columns aggregated via 'Aggregation or grouping' (e.g. count or comma
                   separated values).

                   Previously, using this type of combination on certain database types would
                   have resulted in an error. This change adds a warning to inform users about
                   the use of any incompatible columns at the time the report is being set up.

    TL-21084   +   Improved seminar session Date/Time format and export for report builder

                   New date columns added:
                    * Session Start Date + Excel/ODS export
                    * Session Finish Date + Excel/ODS export
                    * Session Finish Date/Time (linked to activity) + Excel/ODS export

                   Improved:
                    * Session Start Date/Time added Excel/ODS export
                    * Session Finish Date/Time added Excel/ODS export
                    * Session Start Date/Time (linked to activity) added Excel/ODS export
                    * Session Start Time added Excel/ODS export
                    * Session Finish Time added Excel/ODS export
                    * There is new format for date/time with timezone for report builder:
                      '2 July 2019, 5 PM
                      Timezone: Pacific/Auckland'
                    * All Date/Time columns have a proper ODS/Excel export

    TL-21197   +   SQLSRV SSL connections now support the 'TrustServerCertificate' option

                   TL-21115 introduced the ability to force database connections over SSL.
                   However, SQLSRV required a signed certificate and there was no way to force
                   the TrustServerCertificate connection option through Totara.

                   A new dboption 'trustservercertificate' has been added that is passed
                   through to the 'TrustServerCertificate' option during connection.

    TL-21247       Added configuration, a new CLI script and a scheduled task to execute the 'ANALYZE TABLE' query

                   The new 'analyze_table_task' scheduled task is configured to run every late
                   night.
                    It is required that the task be configured to run at off-peak times on
                   your site.

    TL-21359       Fixed the Atto editor incorrectly applying formatting to previously selected text

                   Fixed an intermittent problem with the Atto editor when formatting was
                   applied to previously selected text instead of the currently selected text.
                   The 'mouse select' functionality works reliably now.

    TL-21422   +   Added a setting to display a seminar description on a course homepage
    TL-21426       New SCORM setting has been added that implements session timeout prevention in SCORM player

                   The new setting "Enable the SCORM player to keep the user session alive" is
                   available under the Admin settings in the SCORM plugin. It can be used in
                   order to prevent unwanted session timeouts during SCORM attempts.

                   Due to the fact that it keeps user session alive while SCORM attempt is in
                   progress, it may be considered a minor security concern and has been added
                   to the Security overview report as such.

    TL-21435   +   Removed typo3 library dependency from the core_text class
    TL-21491   +   Added [seminarname] and [seminardescription] placeholders for Seminar notifications

                   The [seminarname] placeholder has been added to replace the
                   [facetofacename] placeholder, although the system will still support both
                   [seminarname] and [facetofacename] placeholders. An optional placeholder,
                   [seminardescription], has also been added.

Bug fixes:

    TL-18560       Fixed the 'Publish room for use in other sessions' checkbox in the edit custom room dialogue

                   When creating or editing a seminar event, it is possible to create a custom
                   room that can only be used by other events in the same seminar activity.
                   The editing form for these rooms can include a checkbox (if you have
                   sufficient permission) that allows them to be easily converted to sitewide
                   rooms.

                   This checkbox was always checked, and did not work as expected. This has
                   been fixed.

    TL-19054   +   Set notification type when cloning a Report Builder embedded report to a warning instead of an error
    TL-19138       Fixed warning message when deleting a report builder saved search

                   If a report builder saved search is deleted, any scheduled reports that use
                   that saved search are also deleted. The warning message to confirm the
                   deletion of the saved search now also correctly displays any scheduled
                   reports that will also be deleted.

    TL-19324       Fixed a bug within select tree where the drop-down would disappear when clicking the scrollbar

                   Improved the select tree component functionality. The scrollbar within
                   select tree components works reliably now.

    TL-20143       Fixed un-reversable block visiblity change when editing dashboard

                   When editing a dashboard it was possible to change the 'Administration'
                   block (or any other block) to only be visible on that dashboard. Once the
                   change was saved there was no way to change the block to display on 'Any
                   page' again. This patch allows the setting to be changed back.

    TL-20555       Removed Report Builder calls to a non-existent display function 'rb_display_nice_date()'

                   This is only an issue for any 'custom' created report sources that are
                   calling the 'rb_display_prog_date()' or 'rb_display_list_to_newline_date()'
                   display functions directly.

    TL-20960       Fixed the completion editor to schedule the recalculation of completion status if necessary

                   When saving activity completion status in the completion editor, the
                   reaggregate flag was set to schedule reaggregation of the associated course
                   completion record only if:
                    * completion criteria activity is modified in completion editor
                    * and the flag has not been set since the last cron run

                   Added a transaction log about 'reaggregation scheduled' if the conditions
                   above are met.

                   (If the reaggregate flag is set, then the next cron run will pick up the
                   corresponding course completion record, recalculate the completion status
                   and clear the flag.)

    TL-20999   +   Fixed seminar grade input field to respect the course 'grade_decimalpoints' configuration
    TL-21049   +   Fixed improperly removed seminar event roles

                   Seminar refactoring in the previous release created a bug that led to
                   improper deletion of seminar event roles. This, in turn, caused an error
                   when attempting to update seminar events that had unassigned event roles.

                   The bug has been fixed, and improperly deleted roles will be removed
                   correctly on upgrade.

    TL-21055       Fixed the encoding of special HTML characters in tags

                   Prior to this patch, tag names were HTML-encoded before saving, with no
                   provision made to prevent re-encoding. This meant that whenever a course
                   (or program, or certification, or other tag-using component) was edited,
                   any attached tags would be re-encoded and saved as new tags.

                   This behaviour has been fixed. Upgrading to this release will fix any tags
                   that have been encoded multiple times, merging them with their original,
                   un-encoded selves as necessary.

    TL-21074       Fixed logging when restoring a backup including course completion history

                   Prior to the patch, when restoring the completion history, the restore step
                   would log the course completion instead of its history (which was not its
                   responsibility).

                   With this patch, the completion history restore step now logs the
                   completion history.

    TL-21149   +   Images displayed in a static form field no longer cause horizontal scroll

                   This will require CSS to be regenerated for themes that use LESS
                   inheritance.

    TL-21257       Prevented background controls from being active when viewing program assignments
    TL-21261       Fixed the filtering of spaces in the 'Add a block' popover
    TL-21275   +   Fixed recent regression with double encoded entities in report exports

                   Replaced relevant report builder calls to format_string() with  calls to
                   the report builder display class format_string which correctly encodes the
                   string according to the output format.

    TL-21277       Fixed compatibility of Behat integration with ChromeDriver 75 and later
    TL-21290   +   Fixed Report Builder saved searches to be sorted alphabetically in the 'Manage your saved searches' dialog
    TL-21293       Fixed an error with visibility checks in the fetch_and_start_tour() external function

                   Prior to this patch an error was generated when the external function
                   fetch_and_start_tour() was called and the tour should not be shown to the
                   user.

                   The check for whether the tour should be shown to the user or not is now
                   correctly handled by the JavaScript.

    TL-21295       Fixed bug where Grid catalogue course category updates ran interactively instead of as an adhoc task

                   The category update tasks can take a long time to complete when run
                   interactively on sites with many courses or programs.  The updates have
                   been moved to run as adhoc tasks instead.

    TL-21299       Fixed seminar direct enrolment Terms and Conditions link
    TL-21324       Fixed adding approvers to seminar

                   Prior to this patch, when a new approver was added to a seminar instance,
                   the previously added approvers (if any) were removed and replaced with the
                   new one.

                   With this patch, the previously added approvers (if any) will remain
                   without change.

    TL-21328       Fixed exception thrown when user is not assigned to a program in their Learning Plan
    TL-21365   +   Removed duplicate records from the cancelled attendees list for seminar events with multiple sessions
    TL-21384       Fixed export value of the 'Previous completions' column in the 'Record of Learning: Certifications' report source

                   HTML markup is no longer displayed in the export file for this column.

    TL-21398       Fixed bug causing the front page course to be listed in the Grid course catalogue

                   Previously, if the site summary on the front page course was edited, the
                   front page would appear as a learning item in the Grid course catalogue.
                   The front page course should never appear in the catalogue; this has now
                   been fixed.

    TL-21411       Default program and certification images are now overridable by theme
    TL-21412   +   Fixed database query logging when ignoring errors in the database transactions
    TL-21413       Fixed the user 'full name link' report builder column to take admin role into account

                   Prior to this patch, the display function for the 'full name link' report
                   builder columns did not provide a URL for viewing profile at site level.
                   Even though, the actor was able to view the site level profile of another
                   user.

                   With this patch, a profile URL at site level will be produced, if the actor
                   is able to view the site profile of another user.

    TL-21419       Fixed rendering of password fields to ensure they are displayed as mandatory
    TL-21454       Fixed export value of the 'Name' column in the 'Organisations' and 'Positions' report sources

                   HTML markup is no longer displayed in the export file for these columns.

    TL-21464       Fixed custom validation of multi-select custom fields to prevent forms incorrectly failing validation

                   In some cases, validation of multi-select custom fields would try to apply
                   validation to fields that didn't exist in the current form. This caused the
                   form to fail validation without a warning, leading to unexpected behaviour
                   when submitting forms.

    TL-21467       Fixed an issue where the 'User tours' menu item could not be added to the administration drop-down menu
    TL-21468       Added support for completion records archiving in LTI activity module
    TL-21535       Removed display of invalid negative grades when scale grade is selected in the lesson module

                   When the grading scale is used in the lesson module, the value stored in
                   the grade column is the database ID of the scale. This was incorrectly
                   being used to calculate the grade and displayed to the users, when in fact
                   this grade should not have been calculated when using the scale grading
                   option.

    TL-21536   +   Updated the default capabilities of the Trainer and Editing Trainer roles to allow 'mod/facetoface:viewallsessions'

                   Previously the Trainer and Editing Trainer roles were unable to view the
                   seminar 'Event details' page without the 'mod/facetoface:viewallsessions'
                   capability. These roles will now have the capability enabled by default for
                   new installations. Sites upgrading to this release are recommended to
                   manually enable the capability for the roles.

    TL-21543       Ensured correct capability is checked when viewing 'Comments Monitoring' page

                   Previously, viewing 'Comments Monitoring' page in the administration menu
                   checked only the 'moodle/site:viewreports' capability, but accessing the
                   page required an additional 'moodle/comment:delete' capability. This led to
                   inconsistencies where users would see the page in their navigation, but
                   would get an error when trying to access it.

                   This behaviour has now been made consistent, and users with
                   'moodle/site:viewreports' capability can access and view the page without
                   needing to be able to delete the comments. Deleting comments still performs
                   the 'moodle/comment:delete' capability check.

    TL-21564       Fixed an issue with the parameters passed to the check_access_audience_visibility() function

                   This was not replicable within core code. But if a call to
                   check_access_audience_visibility() used an integer instead of an object,
                   the function would try to fetch the expected record from the database using
                   the integer as an id. That database call was incorrectly formatted
                   resulting in an error, this has been fixed.

Contributions:

    * John Phoon at Kineo Pacific  - TL-21564


Release Evergreen (19th June 2019):
===================================

Key:           + Evergreen only

Important:

    TL-21080       Prevented automatic completion of appraisal stages without any populated roles

                   Before this patch, completion of an appraisal stage could lead to automatic
                   completion of the following stage if that contained only unpopulated
                   appraisal roles.
                   With this patch automatic completion of subsequent stages only happens
                   when all populated roles have completed the stage and at least one role
                   (populated or not) has completed the stage.
                   This fixes a change in behaviour introduced in TL-19824.

                   This patch does not change affected appraisals on upgrade. For affected
                   appraisals, completed stages can be manually reset using the stage editing
                   tool in the appraisal administration's "assignments" tab.

Security issues:

    TL-21071       MDL-64708: Removed an open redirect within the audience upload form
    TL-21243       Added sesskey checks to prevent CSRF in several Learning Plan dialogs

API changes:

    TL-14412   +   Deprecated custom notification handling

                   The following functions have been deprecated as part of this:
                    * Function: totara_get_notifications()
                      Alternative: \core\notification::fetch()
                    * Function: Function: totara_set_notification()
                      Alternative: redirect or \core\notification::*()
                    * Function: totara_convert_notification_to_legacy_array() (no alternative)
                    * Function: totara_queue_append() (no alternative)
                    * Function: totara_queue_shift() (no alternative)
                    * Method: \core\notification::add_totara_legacy() (no alternative)

    TL-20362   +   Converted M.totara_plan_course_find from a YUI module to an AMD module
    TL-20363   +   Converted M.totara_plan_program_find from a YUI module to an AMD module
    TL-20364   +   Converted M.totara_plan_competency_find from a YUI module to an AMD module
    TL-20749   +   New "ttr_tablename" syntax is allowed in SQL queries in addition to current {tablename}

                   As well as using

                   {tablename}

                   in an SQL query it is now also possible to use "ttr_tablename".
                   This enables SQL queries to be written that can be processed by code
                   parsers and IDEs.
                   Developers may want to consider using ttr_ as your default database prefix
                   from now on.

    TL-20765   +   Added a new SQL class to improve handling of raw SQL in DML API
    TL-20819   +   Added a new interface for placeholder objects used within get_string() calls

                   Developers can now pass objects which implement core_string_placeholders
                   to the third parameter of get_string. The replace function which these
                   objects provide will be used to perform string placeholder substitution.
                   This allows more powerful and complex placeholder systems to be
                   implemented, in a consistent and reusable way. All values which could
                   previously be passed as the third parameter of get_string are still
                   supported.

    TL-20864   +   Upgraded jQuery to 3.4.1

                   jQuery changelog can be found at
                   https://blog.jquery.com/2019/04/10/jquery-3-4-0-released/

    TL-21024   +   Added support for enforced foreign key consistency

                   Onupdate and ondelete referential integrity actions can now be added to
                   foreign key relations.
                   By default foreign keys are not enforced in any way.
                   During definition of a foreign key using the XMLDB editor you can now
                   choose to enforce referential integrity through set actions.
                   The following actions are available:
                    * 'restrict' blocks violation of foreign keys
                    * 'cascade' propagates deletes
                    * 'setnull' changes value to NULL

    TL-21040   +   Converted report_loglive YUI module to AMD module

                   This removes the original YUI module

    TL-21176   +   Upgraded chart.js library to version 2.8.0
    TL-21177   +   Added 'core/popover:destroy' event to the popover component

Performance improvements:

    TL-20772       Optimised SQL base query to include userid in the rb_source_dp_course report source

                   To improve report performance, if userid is supplied to the report page of
                   the "Record of Learning: Courses" report source, it is now included in the
                   base SQL query.

                   As a side effect, this patch also improves aggregation support for
                   the "Record of Learning: Courses" report source.

                   Previously this report source contained several required columns in order
                   to ensure user visibility was correctly applied. These required columns led
                   to aggregation within the report not working. Thanks to improvements made
                   in Totara 12 this could be refactored so that the required columns are no
                   longer necessary. Visibility is still calculated accurately and aggregation
                   is now working for this report source.

                   Please note that the "Record of Learning: Courses" report source no longer
                   supports caching.

New features:

    TL-18605   +   New framework for Web APIs based on GraphQL and new Ajax API
    TL-20421   +   Seminar event attendance and grades can now be imported via CSV

                   With this feature, accessible from the seminar event 'Take attendance'
                   page, trainers are able to upload a CSV file with attendance information
                   for each event attendee. If event manual grading is enabled, the CSV file
                   may also include grades.

Improvements:

    TL-5660    +   Uploading completion records no longer creates evidence for unrecognised records by default

                   Previously, when uploading course or certification completion data using a
                   CSV file, an evidence record would be created for any row in the file that
                   did not match up exactly with an existing course or certification. The
                   default was to create generic evidence, but other 'Default evidence types'
                   were selectable.

                   The new default 'Default evidence type' setting is 'Do not create
                   evidence'. This will cause unmatched rows to be marked as errors instead of
                   being used to create evidence records.

                   To recreate the old behaviour, set 'Create generic evidence' as the
                   'Default evidence type' for the import.

    TL-20422   +   Moved seminar event and session details to a separate tab

                   Details of a seminar event and its associated sessions, including room and
                   asset information, has been shown to trainers at the top of each seminar
                   management tab ('Attendees', 'Cancellations', 'Take attendance', et
                   cetera). This information was the same from tab to tab, and pushed unique
                   information and functionality down the page.

                   Seminar event and session information has been moved to its own tab, 'Event
                   details', and removed from all other seminar management tabs.

    TL-20423   +   Replaced seminar 'Go back' links with 'View all events' buttons

                   In order to simplify seminar management and improve usability for trainers,
                   the 'Go back' links at the bottom of all seminar management screens have
                   been replaced with buttons that read 'View all events'.

    TL-20476   +   Created new seminar setting 'Passing grade' and added 'Require passing grade' seminar activity completion option

                   Seminar activity completion options have been enhanced to bring seminar in
                   line with other Totara activities like assignment and quiz. Previously,
                   seminar only had a 'Learner must receive a grade to complete this
                   activity' option. This has been replaced by a 'Require grade' option with
                   two choices: 'Yes, any grade' and 'Yes, passing grade'.

                   If 'Yes, passing grade' is chosen, a passing grade must be set for the
                   seminar. The default passing grade can be set globally. Setting the passing
                   grade higher than 0 enables the use of pass/fail marks on the activity
                   completion report.

                   In order to provide backward compatibility with previous seminar activity
                   completion options, the upgrade will set 'Require grade' to 'Yes, any
                   grade' and 'Passing grade' to '0' on any seminar where 'Learner must
                   receive a grade to complete this activity' is enabled. This has the effect
                   of exactly reproducing the previous behaviour.

                   In addition, this patch has fixed two other minor issues:
                    * the facetoface_signups_status.createdby database field was not being
                      updated when taking attendance
                    * archived sign-up data entries were not being excluded from the
                      computation of a seminar grade

    TL-20512       Improved the accessibility of the seminar take attendance form

                   Attached a human-readable aria-label text to form elements.

                   Removed hidden, non-human readable <label> from event attendance dropdown
                   box.

    TL-20546   +   Added 'Event grade' column to 'Seminar Signup' report source
    TL-20575       Added an event for Program and Certification user completion state change via the completion editor

                   An event will now log the old and new completion state when changed for a
                   user using the completion editor for a Program or Certification together
                   with the user who made the change

    TL-20891   +   Improved the consistency of sanitisation for the user identity fields
    TL-20892   +   Improved layout of OAuth2 service providers on the login page
    TL-20918   +   Implemented new DML function set_fields and set_fields_select to update multiple fields in a table
    TL-21036   +   Implemented a CSV for spreadsheets export for Report Builder

                   This new CSV export format is designed for use with spreadsheets.

                   It produces a CSV file that is close to RFC4180 but that has an escape
                   character in front of any data that may be interpreted by the spreadsheet
                   application.

                   We recommend that users use this export format if they have to export to
                   CSV but intend to open the .csv file in a spreadsheet application as it
                   protects them against CSV injection attacks.

                   This export format is not enabled by default. Those wanting to use it must
                   enable it within the "Export Options" setting for Report Builder.

    TL-21115   +   Added new database settings for encryption of database communication

                   Full details on how to configure SSL communication with your database can
                   be found in config-dist.php after upgrade.

    TL-21155   +   Seminar session attendance tracking is now off by default

Bug fixes:

    TL-16324   +   Fixed global search navigation when Solr is enabled and configured

                   Prior to this patch the "Manage global search" page would only be shown in
                   the site administration structure when you were on certain pages.
                   It is now shown consistently when intended.

    TL-20034       Added a new scheduled task to purge orphaned course completion records

                   On large course datasets it was possible for a background cron job to start
                   running before an interactive course delete action had completed. This
                   could result in data integrity issues, e.g. the system having course
                   completion data for a course that no longer exists. A scheduled task has
                   been added to clean up any orphaned course completion data that might
                   exist, by default this task will run once a day at 1:54 am.

    TL-20327   +   Fixed race condition when totara dialogs are not initialised when adding components to a learning plan
    TL-20533       Changed the seminar 'Allow Manager reservations' functionality to allow suspended users to be enrolled into seminar events
    TL-20716       Seminar session date time columns within report builder sources are now accurately described

                   Language strings used to describe the session start and finish date/time
                   columns within seminar report sources have been improved.

    TL-20804   +   Seminar 'Add users' step 2 now respects the showuseridentify config setting

                   Previously, user full name, email address,  username and ID number were
                   displayed in step 2 of the 'Add user' workflow without respecting the
                   'showuseridentity' config setting. Now ID number and username are no longer
                   shown, and display of email address respects the "showuseridentity" config
                   setting.

    TL-20885       Ensured email address validation within HR Import is used when the 'Allow duplicate emails' setting is enabled

                   Prior to this patch, if 'Allow duplicate emails' was set, email address
                   validation was inadvertently being ignored, making it possible for an
                   invalid email address to be set for imported users.

                   This patch ensures the email address is validated correctly, but cannot fix
                   any existing invalid email addresses. If you have been using this setting,
                   it is recommended to manually check any imported user email addresses.

    TL-20925       Fixed a PHP warning that was encountered when redirecting with a message before the session had been started
    TL-20927       Fixed the alignment of the name column within the grader report when the browser is zoomed
    TL-20987   +   Fixed double encoding of user identity fields in the history grader report

                   Any customisations made using the /grade/report/history/users_ajax.php file
                   should check the output of user identity fields after upgrade to ensure
                   proper sanitisation is happening on output.

    TL-21054       Fixed alias name preventing seminar sessions report from correctly applying content filters

                   A bug has been fixed in the seminar sessions report builder source that was
                   causing a system error when trying to join content filters.

    TL-21069       Fixed duplicate 'Event under minimum bookings' notifications after mod_facetoface upgrade

                   The seminar notification for events that do not achieve a minimum number of
                   bookings was implemented in a way that caused it to be sent again (and
                   again) for past seminar events whenever mod_facetoface was upgraded.

                   The 'Event under minimum bookings' notification has been reimplemented as a
                   real seminar notification, with an editable template and the ability to
                   customise it at the activity level. This means outgoing instances of this
                   notification will be tracked to prevent duplicates.

                   Any seminar events that have not started yet, and that are eligible to
                   receive an 'Event under minimum bookings' notification, may receive one
                   final duplicate notification after upgrade to this release.

    TL-21090       The "Booked by" column within the seminar sign-in sheet report source no longer produces a fatal error
    TL-21096       Fixed incorrect classname checks in set_totara_menu_selected()
    TL-21099       The menu of choices custom field filter in report builder now correctly handles "Any value"
    TL-21117   +   Fixed a bug that generated the wrong page URL for seminar session 'Take attendance' page
    TL-21175       Added the ability to fix out of order competency scale values

                   Previously when a competency scale was assigned to a framework, and users
                   had achieved values from that scale, it was not possible to correct any
                   ordering issues involving proficient values being below non-proficient
                   values.

                   Warnings are now shown when proficient values are out of order, and it is
                   possible to change the proficiency settings of these scales to correct this
                   situation.

    TL-21181       Fixed an HR Import Hierarchy circular reference sanity check timeout issue when assigning parents
    TL-21183       Fixed non-escaped characters being used in an SQL like statement during message provider upgrade

                   Prior to this patch, if a developer created a customisation that renamed or
                   deleted a message provider in a plugin, and the key of another message
                   provider in the same plugin began with the same key being removed, then,
                   during upgrade, the default message preference for the other message
                   provider was being deleted. This could have led to an exception when
                   messages based on the other message provider were being sent. Now, only the
                   correct record is being deleted.

    TL-21184       Fixed the display of the feedback activity long text answer text box
    TL-21189       Made the user 'full name link' report builder column take active enrolment into account

                   Prior to this patch, when a user was no longer enrolled in a course, but
                   the records were still stored within the course, report builder would
                   include the course ID in the user's full name link. Unfortunately, if the
                   link was clicked, a fatal error would be produced as the user was no longer
                   enrolled in the course.

                   With this patch, if the viewer is not able to view a user's profile within
                   the course, then there will be no link produced for that user's full name
                   in reports.

    TL-21208       Deleting report builder columns used by disabled graphs is no longer prevented

                   Before this change, if a column was used in a graph then, even if the graph
                   was later disabled, the column could not be deleted until it had been
                   removed from the graph. This resulted in having to re-activate the graph
                   just to remove the column from the data source field.

                   This change has updated the check to determine whether the affected graph
                   is enabled, only preventing deletion of the column when it is.

    TL-21223       The audience name report builder column no longer outputs HTML when exporting to another format

                   Previously the audience name column would always export an HTML link, even
                   when exporting to CSV or Excel.
                   This has been fixed so that the HTML link is only output when producing the
                   report for the web.

    TL-21238       Added validation of seminar signup state classes to ensure that only valid classes are used

                   Seminar signup state transitions rely on the correct PHP classes being
                   loaded at runtime. A validation routine has been added to ensure that unit
                   tests will fail, and developers will receive debugging messages, if a
                   non-existent state class is used in seminar code.

    TL-21239       Fixed a bug within Atto editor where text alignment could not be changed within IE11 or Edge

                   Previously the alignment of text within the Atto editor would fail to
                   change alignment in IE11 or Edge, if the text had already been aligned by
                   another user in a different browser (such as Firefox or Chrome).
                   This has now been fixed so that IE11 and Edge users can change the
                   alignment of text previously aligned in Firefox or Chrome.

    TL-21242       Fixed a bug preventing the modification of job assignments if the assignment name contained a space
    TL-21252   +   Added database table keys skipped during upgrade and migration
    TL-21258       The course progress block now creates the embedded report it requires if it does not already exist

Contributions:

    * Ayman Al Kurdi at iLearn - TL-20772
    * Georgi Dimitrov at LearnChamp - TL-21090
    * Russell England at Kineo - TL-21183


Release Evergreen (22nd May 2019):
==================================

Key:           + Evergreen only

Security issues:

    TL-20730       Course grouping descriptions are now consistently cleaned

                   Prior to this fix grouping descriptions for the most part were consistently
                   cleaned.
                   There was however one use of the description field that was not cleaned in
                   the same way as all other uses.
                   This fix was to make that one use consistent with all other uses.

    TL-20803       Improved the sanitisation of user ID number field for display in various places

                   The user ID number field is treated as raw, unfiltered text, which means
                   that HTML tags are not removed when a user's profile is saved. While it is
                   desirable to treat it that way, for compatibility with systems that might
                   allow HTML entities to be part of user IDs, it is extremely important to
                   properly sanitise ID numbers whenever they are used in output.

                   This patch explicitly sanitises user ID numbers in all places where they
                   are known to be displayed.

                   Even with this patch, admins are strongly encouraged to set the 'Show user
                   identity' setting so that the display of ID number is disabled.

    TL-20822       Applied fix to prevent prototype pollution vulnerability via jQuery

                   Code within jQuery was recently found to be vulnerable to a JavaScript
                   exploit known as prototype pollution if good practices are not adhered to
                   around sanitisation of user input. Totara was not found to be vulnerable to
                   this type of exploit via jQuery. However, a fix has been applied to the
                   version of jQuery we currently use out of caution, and as a safeguard for
                   future changes.

New features:

    TL-20583       Cherry-pick OAuth2 from Moodle

                   Implementation of OAuth2 user authentication for identity providers such as
                   Facebook, Google and Microsoft.

                   Note: Please ensure that the "Allow accounts with same email" setting is
                   disabled when OAuth2 authentication is enabled.

Performance improvements:

    TL-20858       Improved record of learning performance by adding an index to the 'course_completions' table

Improvements:

    TL-7808    +   Added seminar reset functionality to course reset

                   Previously, seminars did not have any code supporting course reset
                   functionality.

                   Now if you attempt to reset a course containing a seminar activity there
                   are options to 'Delete attendees' and 'Delete all events'. Both are ticked
                   by the 'Select default' button, but can be unticked to keep events, or keep
                   events and their attendees, after the course is reset.

    TL-8300    +   Added the ability to order courses within a Program or Certification courseset
    TL-20063   +   Converted seminar take attendance JavaScript from YUI module to AMD module
    TL-20427   +   Improved the usability of downloads for seminar attendees sign-in sheets
    TL-20508       Added a new database option to configure maximum number of IN-clause parameters in SQL queries

                   Previously the maximum number of parameters was always set to 30 000. With
                   this change, it is now possible to override this number via the
                   'maxinparams' dboptions setting in config.php.

    TL-20511       Added aria-label lookup to Behat field label selector

                   Previously, when looking for form field inputs, Behat was only able to look
                   for matching <label> elements. This meant that form fields without a
                   <label> were difficult to select.

                   Behat is now able to check the aria-label attributes of form fields to see
                   if the text matches the requested label. So for example, a step like 'And I
                   set the field "export" to "csv"' will find the first field with either a
                   <label> element or an aria-label attribute that matches 'export', and set
                   it to 'csv'.

                   This means that labels that were only visible to screen readers are
                   replaceable using <input aria-label="label name"> without any changes to
                   behat steps. In addition, steps matching form fields with CSS or XPath
                   could be changed to be more readable, and more robust, provided the form
                   field is uniquely identifiable by aria-label text.

                   This patch could break existing Behat tests. In cases where an input with a
                   matching aria-label attribute appears before a second input with a matching
                   <label> element, the first field will now be matched, whereas before it
                   would have been ignored.

    TL-20656   +   Improved server-side validation of audience rules
    TL-20756   +   Added new custom setting in section links block for the display style of topic link

                   The new custom setting in section links block will allow the course editor
                   to change the display style of topics within this block. By default, it
                   will display the section link as a number. However, the course editor is
                   able to switch to either section 'title only' or 'number and title'.

    TL-20857   +   Added method to clear visible notifications banners via JavaScript
    TL-20872       Clarified explanatory text for the 'Update all activities' setting in seminar notification templates

Bug fixes:

    TL-18946   +   Added missing recipient types and descriptions to seminar notifications

                   Prior to this patch, there were a few notifications in seminar that did not
                   specify the recipient types nor the description of the notification.

                   With this patch, the recipient types and description of notifications are
                   now specified.

    TL-20429       Requests for theme images by Google Image Proxy no longer return SVGs

                   It came to our attention that the Google Image Proxy system used by the
                   likes of Gmail does not support SVG.

                   When serving theme images now, we check if the request is coming from the
                   Google Image Proxy system and return an appropriate version of the image if
                   it is.

    TL-20489       Fixed occasional delay between enrolment via seminar sign-up and learner appearing in the grader report

                   When a learner was enrolled in a course by signing up or being manually
                   added to a seminar, the user sometimes could not immediately see the
                   course, and was not visible in the grader report for the first 50 seconds.

                   This delay has been fixed. Learners enrolled in a course via seminar will
                   be immediately visible in the grader report, and able to see the course.

    TL-20519       Made sure grade override is taken into account when calculating SCORM activity completion

                   Previously, SCORM activity completion relied only on the package tracking
                   data to calculate learner's activity progress. In cases where grades were
                   manually overridden they were not taken into account and the activity would
                   still appear as incomplete. This has now been fixed, and manually added
                   grades are included into the SCORM completion progress calculations where
                   they are required for completing the activity.

    TL-20629   +   Fixed sign-up links on course page that pointed to the wrong URL when seminar direct enrolment was enabled
    TL-20682       Ensured new random questions are created when duplicating quiz activity

                   Previously when a quiz was duplicated via activity/course backup and
                   restore process, random questions in the new quiz were still linked to the
                   random questions in the original quiz. This has now been fixed and the new
                   random questions are created during activity duplication.

    TL-20721       Fixed the grader report not taking hidden access restrictions into account

                   Previously if an activity had an access restriction using 'Member of
                   Audience', and the restriction was set to 'hide entirely' rather than
                   'display greyed out', the activity was not visible on the grader report
                   even if the viewer was part of the audience.

                   The activity will now be correctly displayed on the grader report as long
                   as the restriction is met.

    TL-20767       Removed duplicate settings and unused headings from course default settings
    TL-20787       Fixed grid catalogue to display the tag name in the same case as the value entered by the user

                   Prior to this patch, when tags were configured to be displayed in the grid
                   catalogue, the tag name was displayed in all lowercase.

                   With this patch, the tag name will be displayed in the same case as the
                   value entered by the user.

    TL-20788       Fixed bug causing grid catalogue to display incorrect information for the certification ID number
    TL-20792       Fixed goal user assignment 'timemodified' and 'usermodified' fields not being updated

                   When a user re-met the criteria for a company goal, the 'timemodified' and
                   'usermodified' fields were not being updated. This has been corrected.

    TL-20793   +   Fixed Atto editor to remove attribute required on initialisation
    TL-20805       Fixed course's custom fields to have a unique name for each static element

                   Prior to this patch, when a course had custom fields with the description
                   that was not unique for a static element in the form, then the form would
                   display a debugging message to notify developers that the name of static
                   element was missing.

                   With this patch, each static element now has a unique name associated with
                   it.

    TL-20813       Fixed a bug that displayed the Totara favicon instead of the theme's favicon on new SCORM windows
    TL-20832       Fixed a missing require statement in the unit tests for assignment module reports
    TL-20847   +   Fixed bug that prevented taking seminar session attendance in some cases

                   In the previous release of Totara Evergreen, when the in-memory list of
                   seminar sessions was sorted, it did not maintain an ID-to-session
                   relationship. This caused seminar session attendance to fail with an error
                   because the requested session could not be looked up by ID.

                   With this patch, session IDs in the list are preserved during sorting,
                   allowing the requested session to be found.

    TL-20854   +   Fixed the creation and editing of multi-select cohort rules

                   TL-20547 introduced a regression when editing a multi-select cohort rule
                   where it couldn't be saved. This is now fixed.

    TL-20860       Fixed bug preventing course gallery tile visibility being set by audience rule
    TL-20912       Fixed parsing of program availability date

                   Previously, programs were created with the 'Available until' value set to
                   the beginning of the day (00:00:00), while subsequent editing of a program
                   set the date to the end of the day (23:59:59). This has now been fixed and
                   the dates during program creation and program editing are always set to the
                   end of the selected date (23:59:59).

    TL-20936       Fixed multi-language filtering for course/program/certification tile in the 'Featured links' block

                   Prior to this patch, the multi-language filter was not being applied for
                   the learning tile's heading.

                   With this patch, the multi-language filter is applied.

    TL-20956       Fixed user tours being incorrectly aligned when a using a backdrop
    TL-20998   +   Fixed possible double entity encoding when rendering templates in javascript

                   This was evident in default column names when creating new reports in
                   report builder, but has been fixed in core template to resolve any unfound
                   instances.

    TL-21001   +   Fixed regression in the Report Builder management UI where special characters were incorrectly encoded as entities

API changes:

    TL-20542   +   The phar stream wrapper is now disabled by default during setup

                   Phar is an advanced means of packaging and reading PHP code. It is not used
                   by Totara, and in order to reduce the security surface area of the product
                   we have disabled it by default.

                   If you have a plugin or customisation that requires the phar stream wrapper
                   to be available, we recommend you enable it in code immediately before it
                   is required, and disable it again immediately afterwards.

    TL-20825       Fixed a typo in seminar function name introduced during refactoring

                   Function name 'seminar_event_list::form_seminar()' has been renamed
                   'seminar_event_list::from_seminar()'.

Contributions:

    * Chris Wharton at Catalyst EU - TL-8300
    * Krzysztof Kozubek at Webanywhere - TL-20860
    * Russell England at Kineo USA - TL-20756


Release Evergreen (29th April 2019):
====================================

Key:           + Evergreen only

Important:

    TL-20729   +   All text is now consistently sanitised before being displayed or edited

                   Prior to this change, privileged users could introduce security
                   vulnerabilities through areas such as course summaries, section
                   descriptions and activity introductions.

                   The original purpose of the functionality was to allow content creators to
                   use advanced HTML functionality such as iframes, JavaScript and objects. In
                   some areas it was explicitly allowed to happen. In others, the trusttext
                   system was used to manage who could embed potentially harmful content.

                   This patch includes the following changes:
                    * A new setting 'Disable consistent cleaning' has been introduced. It is
                      set to 'off' by default.
                    * Text in the affected areas will be now be sanitised, both when it is
                      displayed, and when it is loaded into an editor.
                    * The trusttext system will be forced off by default and be disabled
                      unless the new setting is turned on.
                    * SVG images will be served with more appropriate content-disposition
                      headers.

                   The consequence of this change is that by default no user will be able to
                   use the likes of iframes, JavaScript or object tags in the majority of
                   places where they previously could.

                   For those who rely on the old behaviour, the new 'Disable consistent
                   cleaning' setting can be enabled in order to return the old behaviour.
                   However we strongly recommend that you leave this setting off, as when it
                   is turned on the security vulnerabilities will be present. When enabled,
                   this setting will be shown in the security report.

                   Please be aware that there is a data-loss risk for any sites which are
                   upgrading to this release and have relied upon the previous behaviour if
                   they have not enabled the new 'Disable consistent cleaning' setting. After
                   upgrading, unless you enable the legacy behaviour, when a user edits
                   content relying upon this functionality and saves it, they will cause the
                   cleaned version to be saved to the database. Any unallowed HTML tags, or
                   attributes, will have been removed.

                   For more information on this change, and a list of affected areas please
                   refer to our help documentation.
                    [https://help.totaralearning.com/display/DEV/Totara+13+changes+to+content+sanitisation]

Security issues:

    TL-20532       Fixed a file path serialisation issue in TCPDF library

                   Prior to this fix an attacker could trigger a deserialisation of arbitrary
                   data by targeting the phar:// stream wrapped in PHP.
                   In Totara 11, 12 and above The TCPDF library  has been upgraded to version
                   6.2.26.
                   In all older versions the fix from the TCPDF library for this issue has
                   been cherry-picked into Totara.

    TL-20607       Improved HTML sanitisation of Bootstrap tool-tips and popovers

                   An XSS vulnerability was recently identified and fix in the Bootstrap 3
                   library that we use.
                   The vulnerability arose from a lack of sanitisation on attribute values for
                   the popover component.
                   The fix developed by Bootstrap has now been cherry-picked into all affected
                   branches.

    TL-20614       Removed session key from page URL on seminar attendance and cancellation note editing screens
    TL-20615       Fixed external database credentials being passed as URL parameters in HR Import

                   When using the HR Import database sync, the external DB credentials were
                   passed to the server via query parameters in the URL. This meant that these
                   values could be unintentionally preserved in a user's browser history, or
                   network logs.

                   This doesn't pose any risk of compromise to the Totara database, but does
                   leave external databases vulnerable, and any other services that share its
                   credentials.

                   If you have used HR Import's external database import, it is recommended
                   that you update the external database credentials, as well as clear browser
                   histories and remove any network logs that might have captured the
                   parameters.

    TL-20622       Totara form editor now consistently cleans content before loading it into the editor
    TL-20704   +   Improved the format_string() function to prevent XSS when results are not properly encoded in HTML attributes

                   Previously it was possible to enable the use of arbitrary HTML tags in
                   course and activity names. This is a security risk and is no longer
                   allowed.

Improvements:

    TL-17930   +   Added the ability to set a Report Builder saved search as a default view

                   As a Report Builder report curator, a saved search can be set as the report
                   default view.
                   This search will be applied as a default view for everyone who has
                   visibility of the report. Viewers of the report can remove the default or
                   change to another saved search so they have their own saved view.

    TL-19493   +   A link to the component overview screen is now shown when viewing Learning Plan component items

                   A link has been added to the screen for individual Learning Plan component
                   items (e.g., a specific course, program, competency, or objective) that
                   returns the user back to the component overview screen (e.g., all courses,
                   programs, competencies, objectives).

    TL-19808   +   Allowed CSV import of seminar attendees from files without columns for custom fields

                   Seminar attendees can now be imported from CSV files that only have columns
                   for required custom fields or, if there are no required custom fields, from
                   a list of users with no other columns.

    TL-19815   +   Improved performance of replace_all_text() method in DML layer

                   This improved performance of unsupported "DB Search and replace" tool.
                   Instead of blind attempts to search and replace content in all rows, it
                   selects only rows that have searched content first.

    TL-20147       Improved the help text in programs and certifications by specifying that course scores have to be whole numerical values.
    TL-20360       Improved the enrolment type filter for course completion reports

                   Previously the enrolment type filter was a text search against a database
                   value stored for enrolments, this was particularly a problem for audience
                   enrolments since the database value was 'cohort' even though it was
                   displayed as 'Audience Sync'. While the filter worked if you searched on
                   'cohort', this wasn't immediately obvious. This filter has been updated to
                   a multiple-select interface which has options for each enabled enrolment
                   plugin. To maintain all available functionality the multi-select interface
                   for filters has also had its operators updated from "Any/All" to include
                   "Not Any/Not All".

    TL-20402       Decoupled profile editing from administration menu editing

                   Users no longer require 'moodle/user:editownprofile' capability to be able
                   to edit their own administration menu preferences.
                   In order to edit their administration menu preferences they need just the
                   'totara/core:editownquickaccessmenu' capability.

    TL-20407       Added a Basis theme setting to override the colour of submit buttons

                   A new 'Primary button color' setting provides a way to override the
                   background colour of submit buttons in the Basis theme. The appearance of
                   other types of buttons is still controlled by the 'Button color' setting.

                   The 'Preview' buttons on the Basis theme settings form did not work as
                   intended and have been removed. Theme designers are encouraged to use the
                   Element Library to view the effects of theme colour changes immediately
                   after update.

    TL-20441   +   Converted seminar cancellation tab to an embedded report
    TL-20516       Changed ambiguous wording for confirmation button in the appraisal unlock stage page

                   In the appraisal unlock stage page, the confirmation button had potentially
                   confusing text. It was not clear that clicking 'Save changes' without
                   making any changes on the form would still have some effect. This patch
                   changes the wording to 'Apply' instead.

                   Also, the unlock stage interface on the Appraisal Assignments page has been
                   improved.

    TL-20517       Improved compatibility with Solr 7
    TL-20537       Added an event for enabling and disabling authentication methods

                   Prior to this patch, when an admin enabled or disabled an authentication
                   method, there was no event triggered. This patch adds an event there for
                   auditing purposes.

    TL-20554       Improved navigation to user profile page after adding or updating a user

                   Changes have been made to user administration in order to streamline adding
                   and updating users. Prior to this patch, administrators were redirected to
                   the list of users after adding a user, and to the previous screen when
                   editing a user profile. These are not always desired behaviours.

                   'Browse list of users' has been renamed 'Manage users', and 'Add a new
                   user' has been renamed 'Create user'.

                   A 'Save and view' button has been added to the 'Create user' and 'Edit user
                   profile' forms, in order to give administrators the ability to navigate to
                   the new user's profile after creating it. The existing 'Create user' and
                   'Update profile' buttons have been relabelled 'Save and go back', and will
                   take the administrator back to where they were when they clicked to add or
                   edit the user.

    TL-20579   +   Improved deletion confirmation for hierarchy frameworks and items

                   This patch unifies deletion confirmation for hierarchy frameworks and
                   items, as well as adding details about related data to be deleted in the
                   framework confirmation and bulk delete confirmation dialogues.

    TL-20610       Added event triggers for changing site administration group

                   Prior to this patch, when an admin assigned users to or unassigned users
                   from the site administration group, then there was no event to be
                   triggered, and consequently, the system was not able to log the event.

                   This patch introduces a new event triggered by changes to the site
                   administration group, allowing the system to be able to log the event.

    TL-20674       Added a 'scheduled task updated' event to log changes to scheduled tasks
    TL-20695       Added timezone option to the appraisal and feedback 360 date question type

                   The option 'Include timezone as well as time' was added when adding a date
                   picker question to an appraisal or feedback 360. When enabled, the date
                   question will include a timezone selector, defaulting to the user's current
                   time zone. When the appraisal or feedback 360 is saved, other users will
                   see the answer to the date question in the timezone that the user selected,
                   rather their own time zone.

    TL-20705       Improved validation for checkbox audience rules

                   As part of server-side validation of audience rule forms, this now checks
                   that a value has been submitted and that it is either 0 (not checked) or 1
                   (checked).

    TL-20710       Feedback activity UI for editing questions now reflects actual question and page break order

                   Previously, when dragging an item and dropping it outside of appropriate
                   drop zone, the UI would change however the database was not updated to
                   reflect the change. Now when the item is dropped outside of the
                   appropriate drop zone, the item will snap back to the point of origin.

Bug fixes:

    TL-13902       Updated the title for the seminar event 'more info' page for attendees

                   Previously the header title text used on the 'more info' page for a seminar
                   event said 'Sign up for [seminar name]' even if a user was already signed
                   up.

                   This has been fixed to show just the seminar name if the user is an
                   attendee.

    TL-14355       Fixed validation for menu type audience rules

                   Previously audience rules using the menu interface were lacking validation
                   on empty submissions, so if you attempted to save without selecting a value
                   there would be an exception thrown, a broken rule would be added, and you
                   would be redirected away from the page, which meant that you would have to
                   navigate back and remove the rule. Now the form submission is halted and a
                   warning is shown to enter a value.

                   Affected audience rules are:
                    * position type
                    * position menu customfields
                    * organisation type
                    * organisation menu customfields
                    * user menu customfields

    TL-19820       Fixed bugs in quiz 'Review options' marks settings

                   A quiz can be set to hide marks (grade) from learners at various times,
                   using the 'Review options' checkboxes in quiz settings. For example, a quiz
                   can withhold a learner's grade until the quiz has closed.

                   Prior to this patch, the 'Review options' marks setting also affected the
                   recording of activity completion. If marks were hidden from the learner,
                   then activity completion was recorded as 'Complete' when all conditions
                   were met, rather than as 'Complete with pass' or 'Complete with fail'.
                   Activity completion was not updated later if the marks became visible to
                   the learner, and was not consistent with the way grades are recorded:
                   grades are always visible to a trainer, whether learners can see them or
                   not.

                   With this patch, quizzes (or any other activities with grade items hidden
                   from learners) are always marked as 'Complete with pass' or 'Complete with
                   fail' if a grade is required for completion. When learners view the course
                   homepage, activity completion tick marks are modified to hide pass/fail
                   status if the grade is hidden. Trainers will always see the true status.

                   This patch also ensures that grade items are correctly show/hidden
                   according to a quiz's 'Review options' marks settings, with the exception
                   that grades that have already been revealed are not hidden later.

    TL-20148       Fixed a web services error that occurred when the current language resolved to a language that was not installed
    TL-20149       Fixed secondary navbar not showing when browsing third level child page
    TL-20258       Fixed incorrectly appended context links when sending alerts

                   Prior to this patch messages sent as alerts could, in some cases, have
                   superfluous text appended related to context links.

    TL-20338   +   Removed deleted users from seminar views

                   Prior to this patch, when a user record was deleted from the system, all of
                   the user's signup records remained visible in seminar views.

                   With this patch, only users with permission to see deleted users
                   (totara/core:seedeletedusers capability) will be able to see or modify the
                   signups of deleted users.

    TL-20448       Fixed a display issue with conditional access when audience, position, or organisation restrictions were in use

                   Prior to this fix in situations where a restriction set contained an
                   audience, position or organisation restriction the controls for
                   manipulating the restriction set would be hidden, making it impossible to
                   edit the restriction set.

    TL-20466       The approveanyrequest capability is now correctly checked when processing a seminar approval request

                   Users who hold the 'mod/facetoface:approveanyrequest' capability previously
                   would encounter an error when attempting to approve a signup request in a
                   context where they held the capability but did not meet any other required
                   conditions.
                   This has been fixed to ensure that the capability is correctly checked when
                   processing a users approval request.

    TL-20468       The grade overview report now correctly respects audience based visibility
    TL-20475       Fixed seminar grades not being correctly updated when the override flag is removed on a gradebook

                   The third argument of facetoface_update_grades() was changed as follows.
                   In previous releases, the system set NULL as grade if true is passed.
                   From now on, the system sets a default grade if true is passed.
                   The default grade is calculated by using grading method in T13, and the
                   last saved attendance state in T12.

    TL-20482       Fixed 'View dates' link on program/certification assignment page

                   TL-19190 introduced a regression where clicking on the 'View dates' link
                   against a group assignment on the assignments page would display a pop-up
                   with all the users assigned to the program. This has now been fixed and
                   only users from the specific assigned group are displayed.

    TL-20488       Added batch processing of users when being unassigned from or reassigned to a program
    TL-20500       Fixed a bug where a manual data purge of certification assignments and completion did not purge deleted users' records
    TL-20504       Made sure that learning plan access is being checked before sending out comment notifications

                   Previously, any user that interacted with a learning plan by leaving a
                   comment would continue to receive notifications about other users' comments
                   to the plan, even if the user no longer had access to the plan. Now only
                   plan owners, active managers, and users with the
                   'totara/plan:accessanyplan' and 'totara/plan:manageanyplan' capabilities
                   receive notifications about new comments.

    TL-20513   +   Ensured that seminar activity 'View all events' link on course homepage isn't hidden by horizontal scrollbar on Mac OS

                   On Mac OS, the default System Preference is to hide scrollbars until
                   needed. When the scrollbars are shown, they may obscure content or make it
                   difficult to click links that are underneath them. This was sometimes the
                   case with the 'View all events' link under seminar activities on course
                   homepages.

                   The link has been made larger, and padding added, to ensure that it is
                   still clickable if a horizontal scrollbar appears under it.

    TL-20515       Fixed bug that could leave a job assignment linked to seminar signup records after the job assignment was deleted
    TL-20520   +   Fixed saved-search functionality on seminar room and asset embedded reports

                   Added rb_config and $sid to asset and room embedded reports to ensure saved
                   searched can be viewed.

    TL-20522       Fixed IE11 visual bugs and broken buttons when editing the administration menu
    TL-20523       Fixed the display of site logs for legacy seminar status codes
    TL-20526       Check course setting and 'grade:view' capability in course details

                   Previously the report-based course catalogue displayed grades for all
                   completed courses without taking into account the "Show gradebook to
                   learners" course setting or the 'moodle/grade:view' capability of a report
                   viewer. This has now been fixed.

    TL-20534       Fixed a bug preventing grid catalogue filters from properly recognising unicode characters

                   Previously grid catalogue filters were unable to identify courses to list
                   when a course custom multi-select field contained options with unicode
                   characters, e.g. Matěj, Dvořák. This patch fixes the search
                   functionality so that options with unicode characters can be correctly
                   identified.

    TL-20535       Included helptooltip as a dialog-nobind class condition in totara_dialog.js
    TL-20547   +   Fixed JavaScript validation on Moodle forms

                   Previously, when calls were made to $PAGE->get_end_code(false), AMD
                   JavaScript was not being added to the HTML. This has now been corrected.

                   This enables Moodle form validation when editing Appraisals, Audience rules
                   and Seminar times, rooms and assets.

    TL-20568       Fixed misleading 'not answered' text for appraisal questions

                   TL-20052 was supposed to fix this; however that patch was found to address
                   the case when only the learner needed to answer questions. The bug still
                   occurred if the appraisal had a mix of questions and permissions that other
                   roles need to answer.

                   This patch fixes the latter problem.

    TL-20586       Fixed event generation when deleting hierarchy items

                   Prior to the patch the same event was generated for all descendant
                   hierarchy items when deleting an item with children.

                   As a side effect this patch fixes course activity access restrictions based
                   on a position or organisation. Prior to the patch if a child position or
                   organisation was used to restrict access to a course activity and then its
                   parent was deleted, the restriction setup menu for this activity was
                   broken.

    TL-20592       Removed block display when restoring an activity backup

                   Blocks are not displayed while restoring a course backup, because users are
                   expected to move though the restore workflow using the navigation buttons
                   at the bottom of the screen, and because the 'Add a block' feature doesn't
                   work during restore.

                   Because of a bug, blocks had been displayed while restoring an activity
                   backup. This has been fixed, and no blocks should display during any type
                   of multiple-step restore.

                   A renderer bug that resulted in an unclosed <div> tag on the second screen
                   of the restore process has also been fixed.

    TL-20598       Fixed the available actions on seminar attendees pages so they respect the 'mod/facetoface:addattendees' capability

                   Prior to this patch, both the 'add' and 'remove' attendees options were
                   shown in the drop-down menu on the seminar event attendees pages, even if a
                   user only had the 'mod/facetoface:removeattendees' capability.

                   The 'add attendees' option will now only be displayed for users with
                   'mod/facetoface:addattendees' capability.

    TL-20609       Fixed an issue in the main menu where a certain combination of preset rules caused an infinite loop
    TL-20634       Improved security and transparency of seminar 'Message users' feature

                   In previous versions, any user who had the seminar 'Take attendance'
                   capability could use the 'Message users' form to see attendee email
                   addresses and send messages to one or more attendees.

                   'Message users' has been changed to require three permissions in the
                   context of the seminar activity: 'Send messages to any user'
                   (moodle/site:sendmessage), 'Send a message to many people'
                   (moodle/course:bulkmessaging) and 'View attendance list and attendees'
                   (mod/facetoface:viewattendees). These permissions continue to be enabled by
                   default for trainers and editing trainers.

                   Also, when a user views the 'Message users' form, a 'Messages users viewed'
                   event is logged. When the form is used to send messages, a 'Message sent'
                   event is logged.

    TL-20635       Fixed the destination for the 'room name link' column in seminar reports

                   Recent improvements to seminars changed the destination of the links to the
                   rooms edit page, which can only be accessed by certain roles. The link now
                   directs users to a less-restricted 'view details' page again.

    TL-20637       Fixed 'Bulk add attendees' form when signup capability is disabled for learner role

                   When the learner role had the 'Sign-up for an event' capability disabled,
                   it was not possible for an administrator to add a learner to a seminar
                   event. The system now checks the permissions of the person who is
                   performing the action, rather than the permissions of the person being
                   signed up.

    TL-20638       Ensured that quiz question ids are unique when they are rendered on the page

                   Previously, when a quiz question was displayed, the outer div of the
                   question had an id="q123" added. Unfortunately, this id was not unique in
                   all cases which lead to the issues in manual grading where multiple
                   responses for the same question were displayed. This has now been fixed.

    TL-20643       Ensured HR Import checks for unique user profile fields are not performed on empty or null values

                   User custom fields that are set as being unique where the source value is
                   an empty string or null are no longer included in the checks to ensure
                   uniqueness.

                   Previously where multiple records contained empty strings where uniqueness
                   was being enforced, the entire user record was failing and not imported.

    TL-20661       Fixed sending of activation emails for all of manager's appraisals

                   Previously upon appraisal activation, a manager would only receive one
                   email, regardless of how many appraisees they had. This was true even if
                   the activation notification content explicitly included appraisee details,
                   e.g. appraisee full name.

                   This patch fixes this; now the manager gets emails for individual
                   appraisees. However, if the message is a generic one (i.e. one that did not
                   have placeholders to differentiate emails to different people), then they
                   will still only get one email.

                   Note: the one generic email per manager only happens if all the appraisees
                   automatically get a job assignments upon appraisal activation (i.e.
                   multiple job assignments is off). If the appraisee still has to view the
                   appraisal to indicate the job assignment, then the manager will receive
                   multiple generic emails each time their appraisee first views an appraisal.

    TL-20668       Primary admin and web service users are no longer required to provide their required profile fields information
    TL-20670       Fixed infinite recursion when generating API documentation
    TL-20681       Made sure course completion value in the Record of Learning report export doesn't contain HTML
    TL-20683       Fixed totara core upgrade to avoid using the system API

                   Prior to this patch, the upgrade path for evergreen was using system API,
                   which was involving the user session to perform actions. Therefore, it
                   failed to upgrade to evergreen from CLI.

                   With this patch, it is possible to upgrade to evergreen with CLI.

    TL-20685   +   Fixed a bug preventing the export of seminar events
    TL-20689       Fixed the display of submission grade and status in the "Assignment submission" report
    TL-20700       Fixed misleading count of users with role

                   A user can be assigned the same role from different contexts. The Users
                   With Role count was incorrectly double-counting such instances leading to
                   inaccurate totals being displayed. With this fix the system counts only the
                   distinct users per role, not the number of assignments per role.

    TL-20703       Fixed incorrect offset when creating a user tour targeting the main navigation
    TL-20712       Fixed feedback preview with a "pagebreak" item at the top on the page
    TL-20720       Fixed issue with grades been saved as 0.0000 on seminar table

                   Since Totara 12.0, and until Evergreen-20190322, seminar grades have been
                   saved as 0.0000 in the facetoface_signups_status table, regardless of
                   attendance state.

                   Gradebook grades were not affected by this bug.

                   Previous versions correctly set the grade field to null until attendance
                   was taken, and then set it to a grade based on attendance. This patch fixes
                   the regression. In summary:
                    * The correct grade value will always be saved into
                      facetoface_signups_status table, regardless of seminar grade settings
                    * If attendance state is 'Not set' when taking attendance, the grade field
                      will be set to null
                    * Incorrect facetoface_signups_status grade values will be rewritten with
                      a correct value, based on attendance state, during this upgrade (where
                      possible, see exception below)
                    * If the system detects backup data made with any affected version during
                      course or activity restore, the correct grade will be used instead of the
                      backed-up grade

                   Upgrades from Evergreen-20190322 might require some manual intervention,
                   because it is not possible to reliably distinguish grades introduced by the
                   bug from grades that have been set to 0.000 via manual grading.

    TL-20727       Ensure email notifications work correctly in HR Import after upgrade

                   Upgrading to Totara 12 or 13 from Totara 11 or earlier may have stopped
                   email notification from being sent in HR Import. This change ensures that
                   they are sent correctly.

    TL-20747       Restored 'Update all activities' functionality for custom seminar notification templates
    TL-20751       Fixed 'fullname' column option in user columns to return NULL when empty

                   Previously the column returned a space character when no value was
                   available which prevented users from applying "is empty" filter

    TL-20764       Added horizontal scroll bar to user multiselect

                   This will not work in IE11 or Firefox (Due to
                   https://bugzilla.mozilla.org/show_bug.cgi?id=1294313).

    TL-20773       Fixed unit test failure for third-party activity plugins that do not support Totara generators
    TL-20779       Removed redundant database update call in Learning Plan Evidence
    TL-20794       Added missing format value on Seminar 'Download sign-in sheet' hidden field

API changes:

    TL-18699   +   Separated the requested approval state into requested manager approval and requested role approval

                   The requested approval state has been split into two separate states,
                   requested manager approval state, and the requested role approval state.
                   This allows for better control and transitioning when in a requested
                   approval state.

    TL-20021   +   Deprecated event time status functions in facetoface

                   Deprecated functions:
                    * facetoface_allow_user_cancellation()
                    * facetoface_is_adminapprover()
                    * facetoface_get_manager_list()
                    * facetoface_save_customfield_value()
                    * facetoface_get_customfield_value()

                   For more information, see mod/facetoface/upgrade.txt

    TL-20376   +   Deprecated date management functions related to facetoface

                   Deprecated functions:
                    * facetoface_save_dates()
                    * facetoface_session_dates_check()

                   For more information, see mod/facetoface/upgrade.txt

    TL-20377   +   Deprecated notification-related function in mod/facetoface/lib.php

                   Deprecated functions
                    * facetoface_notify_under_capacity()
                    * facetoface_notify_registration_ended()
                    * facetoface_cancel_pending_requests()

                   For more information, see ./mod/facetoface/upgrade.txt

    TL-20378   +   Deprecated environment functions related to facetoface

                   Deprecated functions:
                    * facetoface_get_session()
                    * facetoface_get_env_session()

                   For more information, see mod/facetoface/upgrade.txt

    TL-20380   +   Deprecated export functionality within facetoface

                   Deprecated functions:
                    * facetoface_write_activity_attendance()
                    * facetoface_get_user_customfields()

                   For more information, see mod/facetoface/upgrade.txt

    TL-20381   +   Deprecated trivial facetoface functions

                   Deprecated functions:
                    * facetoface_allow_user_cancellation()
                    * facetoface_is_adminapprover()
                    * facetoface_get_manager_list()
                    * facetoface_save_customfield_value()
                    * facetoface_get_customfield_value()

                   For more information, see mod/facetoface/upgrade.txt

    TL-20383   +   Deprecated seminar's attendees retriever functions

                   Deprecated functions in mod_facetoface:
                    * facetoface_get_attendee()
                    * facetoface_get_requests()
                    * facetoface_get_adminrequests()
                    * facetoface_get_users_by_status()
                    * facetoface_get_cancellations()
                    * facetoface_get_num_attendees()
                    * facetoface_get_user_submission()
                    * facetoface_get_attendees()

                   For more information and the replacements of the deprecated functions, see
                   './mod/facetoface/upgrade.txt'

    TL-20536   +   Added Behat steps for checking emails

                   Developers can now write behat steps that trigger the creation of emails
                   which will be captured and can be examined for accuracy. These are the
                   Behat steps available:
                    * I reset the email sink
                    * the following emails should have been sent
                    * the following emails should not have been sent
                    * I close the email sink

    TL-20572       Improved in-code documentation for the recommends_counted_recordset() method

                   Previously the documentation contained a link to our internal tracked.
                   This has been removed as it is not accessible to those outside of the
                   Totara development team.
                   Additionally performance testing results have been directly added to the
                   base method as defined in the moodle_database class.

Miscellaneous Moodle fixes:

    TL-20467       MDL-57486: Delete items when context already deleted
    TL-15552       MDL-57769: Remove 'numsections' from topics and weeks, allow teachers to create and delete sections as they are needed

                   This patch does not remove the 'numsections' setting from the topics and
                   weeks course formats, but it does make it optional for other course
                   formats. It also implements section management methods expected by
                   third-party course format plugins.

    TL-20490   +   MDL-64971: Ensure that the capability exists when fetching
    TL-20563       MDL-61950: Fixed display of random questions in the statistics calculator in the quiz module

                   Prior to this patch, if a quiz had random questions in it, then viewing the
                   statistics report would sometimes have questions missing from the report.

Contributions:

    * Haitham Gasim - Kineo USA - TL-20794
    * Jo Jones at Kineo UK - TL-19815
    * Kineo UK - TL-20751
    * Think Learning - TL-20764


Release Evergreen (22nd March 2019):
====================================


Key:           + Evergreen only

Important:

    TL-20400   +   Changed default seminar grading method, and added manual grading option to seminar events

                   There is a new 'Grading method' setting for seminars, which determines
                   which grade to use for the overall activity grade when a learner attends
                   multiple seminar events. Choices are 'Highest event grade,' 'Lowest event
                   grade', 'First event grade', and 'Last event grade'.

                   The default seminar grading method has been changed to 'Highest event
                   grade'. Prior to this change, a seminar attendee's grade was based on the
                   last attendance taken. The old behaviour can be replicated in practice by
                   setting the grading method to 'Last event grade'.

                   Trainers now also have the ability to assign arbitrary grades to seminar
                   attendees. When 'Event manual grading' is enabled, a 'Grade' column is
                   added to the event 'Take attendance' form. For each learner, trainers can
                   set attendance, a grade, or both.

Security issues:

    TL-20498       MDL-64651: Prevented links in comments from including the referring URL when followed
    TL-20518       Changed the Secure page layout to use layout/secure.php

                   Previously the secure page layout was using the standard layout PHP file in
                   both Roots and Basis themes and unless otherwise specified, in child
                   themes.

API changes:

    TL-16600   +   Deprecated the rest of facetoface_send_* functions
    TL-19859       Added experimental support for paratest to run PHPUnit tests in parallel
    TL-20331   +   Updated Basis notification icon definitions

                   Previously the notification icon definitions provided by Basis did not
                   include the component. This has now been corrected.

Performance improvements:

    TL-19933       Improved Report Builder counting performance

                   Each database engine now provides a recommendation on whether counted
                   recordsets should be used.

                   A new plugin setting 'Default result fetch method' has been added for those
                   wanting to control the choice manually rather than rely on the database
                   recommendation.

    TL-20212       Improved the performance of Report Builder access checks

Improvements:

    TL-6693    +   Added audience rules for position and organisation multi-select custom fields

                   Previously you could create audience rules based on other position and
                   organisation custom fields (menu of choices, checkboxes etc), but not based
                   on multi-select custom fields. This patch adds a new rule type for
                   multi-select custom fields which has 4 operators
                    * in all of the selected options
                    * in any of the selected options
                    * not in all of the selected options
                    * not in any of the selected options

                   It is worth noting that the in any/all operators will include users that
                   have at least one job assignment that have all/any of the selections,
                   similarly the not in any/all operators will include users that have at
                   least one job assignment that does not have all/any of the selections. None
                   of the operators will include users with no job assignments.

    TL-6695    +   Added new course or program assignment dynamic audience rule

                   This new rule allows you to include or exclude users from an audience based
                   on their enrolment in specified courses or programs.

    TL-8754    +   Added 'Has temporary reports' dynamic audience rule
    TL-17469   +   Added dynamic audience rule for 'Has Indirect Reports'

                   Created a dynamic audience rule based on whether the person has indirect
                   reports.

    TL-19259   +   Added 'Has appraisees' dynamic audience rule
    TL-17209   +   Converted seminar wait-list tab to an embedded report
    TL-20041   +   Added enable/disable course end date to course defaults

                   Added a new setting in the course defaults page to enable/disable the
                   course end date by default when creating a new course.

    TL-20106       Improved the handling of invalid UTF-8 strings in block names

                   Fixed javascript failure when one or more block names are translated using
                   invalid UTF-8 sequences.

    TL-20248   +   Made filters invisible on the Seminar events page when there is nothing to filter
    TL-20305   +   Prevented filters from being changed on the seminar events dashboard while events are loading
    TL-20306       Added a 'Link to approval requests' column to the Seminar Sign-ups report source
    TL-20358       Added the ability to unlock all roles in an appraisal at once

                   Before this change, when an appraisal was unlocked for a specific role in a
                   user's appraisal, all roles could make changes to their answers at the
                   given stage (within the normal appraisal rules), but only the unlocked role
                   was required to mark each stage complete again. With this change, a new
                   option 'All roles' is available, and when selected every role will be
                   required to mark each unlocked stage complete again.

    TL-20390       Improved the clean up of records from the 'prog_user_assignment' table
    TL-20410       MDL-57878: Added expected completion date function
    TL-20428       Updated dompdf to version 0.8.3

Bug fixes:

    TL-19369       Fixed the display of images and videos in the summary of course catalogue items
    TL-19840       Fixed divide by zero errors in report builder grade columns

                   If you uploaded or manually set grades for users, but didn't set up the
                   grades for the associated course, the grade percentage columns in report
                   builder would attempt to divide by zero. The report builder now displays a
                   '-' instead.

    TL-19934       Removed duplicate records from the attendees list for seminar events with multiple sessions

                   Prior to this patch, when a seminar event had more than one session date,
                   then the attendees list of the event would duplicate the attendee records
                   based on the number of session dates of an event.

                   With this patch, the attendees list of seminar event with multiple session
                   dates will not duplicate the attendees record based on the number of
                   session dates, unless the admin adds columns that are related to sessions
                   specifically.

    TL-19962       Made the Auto-fill form element always show the result of the most recent search term

                   Previously there was a chance that the result of a previous search term
                   would override the results of a newer search term when using a Moodle form
                   auto-fill element. This change ensures that more recent results are shown.

    TL-19963       Stopped seminar booking confirmation notifications being sent to managers when unchecked.

                   Seminar session signup notification emails were incorrectly being sent to
                   manager when "Send booking confirmation to new attendees managers" was not
                   selected on the seminar session sign-up confirmation page. The behaviour
                   has been corrected to not send the manager copy of confirmation unless
                   specifically requested to do so.

    TL-19966       Added sanity checks to the course duration setting

                   Previously setting the default course duration to 0 did not disable the
                   course end date, but instead the system had an undocumented implementation
                   where '0' was treated as '365 days'. This change has added validation to
                   the field to prevent zero to prevent the issue, as a result the minimum
                   acceptable default course duration is now at least 1 hour.

    TL-20033       Fixed the SQL pattern for word matching regular expressions in MySQL 8
    TL-20045       Improved the wording of the cohort-type filters in course/program/certification reports

                   * Certifications have been separated from the program-related methods in
                   totara_cohort\rb\source\report_trait.
                   * Column and filter types in totara_cohort\rb\source\report_trait have been
                   changed to better reflect the type of content they belong to. Any reports
                   based on the custom report sources using this trait should be updated.

    TL-20052       Fixed misleading 'not answered' text for appraisal questions

                   With the 'view answer' permission, a manager is able to see a learner's
                   appraisal answers even if he does not need to fill in the appraisal
                   himself.

                   Previously however, not only would he see the learner's answers. he would
                   also see "Not yet answered" for each question he didn't answer. This is
                   misleading because it implied the manager needed to answer questions even
                   though this was not the case.

                   This patch removes that "Not yet answered" text.

    TL-20108       Fixed the removal of users who "declared interest" in a seminar event when the event gets deleted
    TL-20118       Fixed the prevention of Site Manager from managing Site Policies
    TL-20127       Changed the grpconcat_date Report Builder filter to use 'AND' operator when both a before and after date has been set

                   Before this patch an 'OR' operator was being used that gave inconsistent
                   results

    TL-20131       Fixed an error when hierarchy frameworks had more than one user entering data concurrently
    TL-20139       Added unique identifiers to each navigation item so they can be targeted by user tours
    TL-20151       Fixed the display of email addresses with non-standard characters in reports
    TL-20153       Fixed Javascript error when a block has no heading
    TL-20159       Browser local storage is now cleared after upgrade/cache purge
    TL-20210       The seminar 'allow cancellations' setting no longer takes precedence over the remove attendees capability

                   This change restores previous behaviour whereby a user with the
                   'mod/facetoface:removeattendees' capability is able to cancel a users'
                   seminar booking, regardless of what the "Allow cancellations" setting is
                   set to.

    TL-20211       Added a new capability to allow the addition of attendees to a seminar event outside of the sign-up registration period

                   The new capability 'mod/facetoface:surpasssignupperiod' is enabled by
                   default for the editingtrainer and manager roles, on upgrade it will be
                   enabled for any role that currently has the 'mod/facetoface:editevents'
                   capability to maintain current functionality.

    TL-20214       Fixed icons in quiz results page overlaying text
    TL-20222       Fixed duplicate 'ID' SQL failure, when a seminar's event has more than one session date
    TL-20233       Fixed problems with complex company goal assignments

                   Before this patch, there were several problems relating to company goal
                   assignments. These included the 'Include children' hierarchy option not
                   working, and problems relating to users who might be assigned due to
                   several reasons, such as meeting multiple goal assignment criteria, or
                   having multiple job assignments.

                   With this patch, each separate reason that a user is assigned to a company
                   goal is correctly recorded in the database, including those caused by the
                   use of 'Include children'. When a user no longer meets the criteria for
                   assignment, the related assignment record is marked 'old'. When a user
                   again meets the criteria, the old record is changed back into an 'active'
                   record.

    TL-20234       Fixed display of Totara logo in IE11 on Windows 7 & 8
    TL-20245       Ensured program and certification messages are displayed correctly when adding and editing

                   The subject and message content were displaying special characters as HTML
                   entities in the add edit form. These now display correctly.

    TL-20256       Fixed user tours based on URLs with multiple parameters
    TL-20272       Fixed missing permissions check on Menu settings link in quickaccess menu

                   Prior to this patch, the link to edit the quick access menu would be shown
                   to users who didn't have the editownprofile capability. The link is now
                   only displayed if the user has this permission.

    TL-20302       Fixed 'Allow cancellations' form setting for users without 'Configure cancellation' capability when adding an event
    TL-20303       Fixed a bug that prevented attendance export from the seminar events dashboard when a deleted user was in the attendees list
    TL-20318       Fixed the 'edit attendee note' action for seminar events which enable reservations

                   Previously when 'Reserve spaces for team' was enabled but no attendees had
                   been added yet, the attendees list page was still displaying a record with
                   the 'Reserve' status to inform other managers about the number of
                   reservations/bookings used. This allowed the update of the Attendee Note
                   without an associated user, causing an error. This patch hides the update
                   attendee note action until a learner is added.

    TL-20324       Included custom room information in notification emails about cancelled seminar events

                   Prior to this patch, when a seminar event had a custom room assigned to one
                   or more sessions and an admin/editor/trainer cancelled the event, the room
                   information would not be included in the notification emails sent to
                   attendees.

                   With this patch, a custom room's information will be included in emails
                   sent to attendees when an event is cancelled.

    TL-20339       Fixed deletion of multiple goals when a single goal was unassigned from a user

                   When a user is assigned to the same organisation via several job
                   assignments and then simultaneously unassigned from the organisation, the
                   goals assigned to this user via an organisation are converted to individual
                   duplicated goal assignments. Previously, when a single goal was deleted,
                   the duplicate records were deleted as well. After the patch, the individual
                   goal assignments are removed separately.

    TL-20355       Fixed course’s default image display problems by improved handling of stored image source

                   Prior to this patch, when an admin uploaded the default image for course,
                   then the URL (including the domain name of a hosting system) would be
                   stored in the config table. This meant the image could no longer be
                   displayed if the domain name changed.

                   With this patch, the path and filename of the default course image will be
                   stored. The function 'course_get_image' is also changed to cope with the
                   new stored value of the default course image and has another defense layer
                   to make sure that the course default image is always existing in the
                   system.

    TL-20424       Fixed drag-and-drop accessible text showing block contents instead of title
    TL-20426       Fixed incorrect page layout set on the program management page
    TL-20442       MDL-58015: Set organisation identifier correctly for SCORM package displayed in a popup mode
    TL-20453   +   Fixed broken 'Turn editing off' link on the seminar attendees page
    TL-20460       Fixed incorrect notification being sent to trainers who are unassigned from seminar events

                   Previously trainers who were removed from seminar events, received a
                   notification saying that they had been assigned to the event. They will now
                   receive the correct 'unassignment' notification.

Contributions:

    * Learning Pool - TL-20212
    * Michael Trio, Kineo USA - TL-19933
    * Think Learning - TL-20108


Release Evergreen (21st February 2019):
=======================================

Key:           + Evergreen only


New features:

    TL-12692   +   Added the ability to track attendance at the session level of seminars

                   Previously it was only possible to track attendance at the event level of
                   a seminar. With this improvement, attendance can be tracked for each
                   individual session within an event. This includes:

                   * A new seminar setting, 'Session attendance tracking', which allows trainers
                     to record attendance for each session of a seminar event. The recorded
                     session attendance is summarised on the event attendance form, allowing
                     trainers to use it as the basis for setting an overall attendance status
                     for each attendee.

                   * A new seminar setting, 'Mark attendance at', which determines when trainers
                     are allowed to begin taking attendance for an event or session.

                   * A new attendance status, 'Unable to attend', which provides an option for
                     trainers to mark an attendee as not having attended a session or event, but
                     without marking them as a 'no show'. The 'Restrict subsequent sign-ups to'
                     setting now includes 'Unable to attend' as one of its options.

                   * The seminar events dashboard has been consolidated into a single list of
                     sessions and events, with a filter allowing participants to see all events,
                     or only those that are upcoming, in progress, or in the past. 

                   * If 'Session attendance tracking' is enabled, a per-session 'Attendance tracking'
                     column appears on the events dashboard, allowing trainers to see at a glance
                     which sessions are marked or are ready to be marked.


Release Evergreen (14th February 2019):
=======================================

Key:           + Evergreen only

Important:

    TL-20156       Omitted environment tests have been reintroduced

                   It was discovered that several environment tests within Totara core had not
                   been running for sites installing or upgrading to Totara 11 or 12. The
                   following tests have been reintroduced to ensure that during installation
                   and upgrade the following criteria are met:

                   * Linear upgrades - Enforces linear upgrades; a site must upgrade to a
                     higher version of Totara that was released on or after the current version
                     they are running. For instance if you are running Totara 11.11 then you can
                     only upgrade to Totara 12.2 or higher.
                   * XML External entities are not present - Checks to make sure that there
                     are no XML files within libraries that are loading external entities by
                     default.
                   * MySQL engine - Checks that if MySQL is being used that either InnoDB or
                     XtraDB are being used. Other engines are known to cause problems in
                     Totara.
                   * MSSQL required permissions - Ensures that during installation and upgrade
                     on MSSQL the database user has sufficient permissions to complete the
                     operation.
                   * MSSQL read committed snapshots - Ensures that the MSSQL setting "Read
                     committed snapshots" is turned on for the database Totara is using.

    TL-20173       Fixed user cancellation when taking attendance if attendance status is not set

                   When taking seminar attendance, signups for which attendance was not set
                   would get cancelled. If this happened, attendees needed to be re-added and
                   attendance taken for them. This fix keeps attendees in their current state
                   if attendance is not set for them and current state is not attendance
                   related.

API changes:

    TL-20109       Added a default value for $activeusers3mth when calling core_admin_renderer::admin_notifications_page()

                   TL-18789 introduced an additional parameter to
                   core_admin_renderer::admin_notifications_page() which was not indicated and
                   will cause issues with themes that override this function (which
                   bootstrapbase did in Totara 9). This issue adds a default value for this
                   function and also fixes the PHP error when using themes derived off
                   bootstrap base in Totara 9.

Performance improvements:

    TL-19810       Removed unnecessary caching from the URL sanitisation in page redirection code

                   Prior to this fix several functions within Totara, including the redirect
                   function, were using either clean_text() or purify_html() to clean and
                   sanitise URL's that were going to be output. Both functions were designed
                   for larger bodies of text, and as such cached the result after cleaning in
                   order to improve performance. The uses of these functions were leading to
                   cache bloat, that on a large site could be have a noticeable impact upon
                   performance.

                   After this fix, places that were previously using clean_text() or
                   purify_html() to clean URL's now use purify_uri() instead. This function
                   does not cache the result, and is optimised specifically for its purpose.

    TL-20026       Removed an unused index on the 'element' column in the 'scorm_scoes_track' table
    TL-20053       Improved handling of the ignored report sources and ignored embedded reports in Report Builder

                   The Report Builder API has been changed to allow checking whether a report
                   should be ignored without initialising the report. This change is fully
                   backwards compatible, but to benefit from the performance improvement it
                   will require the updating of any custom report sources and embedded reports
                   that override is_ignored() method.

                   For more technical information, please refer to the Report Builder
                   upgrade.txt file.

Improvements:

    TL-8314    +   Improved aggregation support for the program report source

                   Previously the program report source contained several required columns in
                   order to ensure user visibility was correctly applied. These required
                   columns lead to aggregation within the report not working. Thanks to
                   improvements made in Totara 12 this could be refactored so that the
                   required columns are no longer necessary. Visibility is still calculated
                   accurately and aggregation is now working for this report source.

    TL-8315    +   Improved aggregation support for the course report source

                   Previously the course report source contained several required columns in
                   order to ensure user visibility was correctly applied. These required
                   columns lead to aggregation within the report not working. Thanks to
                   improvements made in Totara 12 this could be refactored so that the
                   required columns are no longer necessary. Visibility is still calculated
                   accurately and aggregation is now working for this report source.

                   Please note that the course report source no longer supports caching.

    TL-19824       Added ability to unlock closed appraisal stages

                   It is now possible to let one or more users in a learner's appraisal move
                   back to an earlier stage, allowing them to make changes to answers on
                   stages that may have become locked. An 'Edit current stage' button has been
                   added to the list of assigned learners in the appraisal administration
                   interface. To see this button, users must be granted the new capability
                   'totara/appraisal:unlockstages' (given to site managers by default), and
                   must have permission to view the Assignments tab in appraisal
                   administration (requires 'totara/appraisal:manageappraisals' and
                   'totara/appraisal:viewassignedusers').

    TL-19985       Added a hook to the course catalogue to allow modifying the queried result before rendering the courses
    TL-20051   +   Added new Job Assignment ID number dynamic audience rule

                   This new rule allows you to include or exclude users from an audience based
                   on the idnumber field in their job assignments.

    TL-20132       Menu type dynamic audience rules now allow horizontal scrolling of long content when required

                   When options for a menu dynamic audience rule are sufficiently long enough,
                   the dialog containing them will scroll horizontally to display them.

                   This will require CSS to be regenerated for themes that use LESS
                   inheritance.

    TL-20152       Fixed content width restrictions when selecting badge criteria

                   This will require CSS to be regenerated for themes that use LESS
                   inheritance.

Bug fixes:

    TL-18892       Fixed problem with redisplayed goal question in appraisals

                   Formerly, a redisplayed goal question would display the goal status as a
                   drop-down list - whether or not the user had rights to change/answer the
                   question. However, when the goal was changed, it was ignored. This patch
                   changes the drop-down into a text string when necessary so that it cannot
                   be changed.

    TL-19454       Fixed accordion and add group behaviour on admin menu settings page
    TL-19494       The render_tabobject now respects the linkedwhenselected parameter in the learning plans tab

                   This also links the tab name back to the learning plan component when
                   viewing an individual item in a learning plan.

    TL-19838       SCORM AICC suspend data is now correctly stored

                   This was a regression introduced in Totara 10.0 and it affected all later
                   versions. Suspend data on the affected versions was not correctly recorded,
                   resulting in users returning to an in-progress attempt not being returned
                   to their last location within the activity. This has now been fixed and
                   suspend data is correctly stored and returned.

    TL-19895       Added notification message communicating the outcome when performing a seminar approval via task block

                   Previously, when a manager performed a seminar approval via the task block,
                   there was no feedback to the manager as to whether or not it had been
                   successful.

                   An example of where this could have been problematic was when a seminar
                   event required manager approval and the signup period had closed: the task
                   would be dismissed after the manager had completed the approval process,
                   but they would not be informed that approval had not in fact taken place
                   (due to the signup period being closed).

                   With this patch, a message will now be displayed to the user after
                   attempting to perform an approval, communicating whether the approval was
                   successful or not.

    TL-19916       MySQL Derived merge has been turned off for all versions 5.7.20 / 8.0.4 and lower

                   The derived merge optimisation for MySQL is now forcibly turned off when
                   connecting to MySQL, if the version of MySQL that is running is 5.7.20 /
                   8.0.4 or lower. This was done to work around a known bug  in MySQL which
                   could lead to the wrong results being returned for queries that were using
                   a LEFT join to eliminate rows, this issue was fixed in versions 5.7.21 /
                   8.0.4 of MySQL and above and can be found in their changelogs as issue #26627181:
                    * https://dev.mysql.com/doc/relnotes/mysql/5.7/en/news-5-7-21.html
                    * https://dev.mysql.com/doc/relnotes/mysql/8.0/en/news-8-0-4.html

                   In some cases this can affect performance, so we strongly recommend all
                   sites running MySQL 5.7.20 / 8.0.4 or lower upgrade both Totara, and their
                   version of MySQL.

    TL-19935       Fixed $PAGE->totara_menu_selected not correctly highlighting menu items
    TL-19936       Fixed text display for yes/no options in multiple choice questions

                   Originally, when defining a yes/no multiple choice type question, the page
                   showed 'selected by default' and 'unselect' for each allowed option. This
                   text now only appears when a default option has been selected.

    TL-19938       Fixed database deadlocking issues in job assignments sync

                   Refactored how HR Import processes unchanged job assignment records. Prior
                   to this fix if processing a large number of job assignments through HR
                   Import, the action of removing unchanged records from the process queue
                   could lead to a deadlock situation in the database.

                   The code in question has now been refactored to avoid this deadlock
                   situation, and to greatly improve performance when running an import with
                   hundreds of thousands of job assignments.

    TL-19994       Prevented the featured links title from taking up the full width in IE 11

                   This will require CSS to be regenerated for themes that use LESS
                   inheritance.

    TL-19996       Updated and renamed the 'Progress' column in the 'Record of Learning: Courses' Report Builder report source

                   The 'Progress' column displays progress for a course within a Learning
                   Plan. As this column is related to Learning plans, the 'type' of the column
                   has been moved from 'course_completion' to 'plan' and renamed from
                   'Progress' to 'Course progress'.

                   Please note that if a Learning plan has multiple courses assigned to it,
                   multiple rows will be displayed for the Learning Plan within the 'Record of
                   Learning: Courses' report if there are any 'plan' type columns included.

    TL-19997       Added limit to individual assignment dialog in program assignments
    TL-20008       Allowed users with page editing permissions to add blocks on 'My goals' page

                   Previously the 'Turn editing on' button was not available on the 'My goals'
                   page, preventing users from adding blocks to the page. This has now been
                   fixed.

    TL-20018       Removed exception modal when version tracking script fails to contact community
    TL-20019       Fixed a bug that prevented cancelling a seminar booking when one of a learner's job assignments was deleted
    TL-20102       Fixed certificates not rendering text in RTL languages.
    TL-20113       Fixed the filtering of menu custom fields within report builder reports

                   This is a regression from TL-19739 which was introduced in 11.11, and 12.2
                   last month.

    TL-20128       Fixed 'missing parameter' error in column sorting for the Seminar notification table
    TL-20141       Fixed 'Date started' and 'Date assigned' filters in the program completion report

                   Previously the 'Date assigned' filter was mis-labelled and filtered records
                   based on the 'Date started' column. This filter has now been renamed to
                   'Date started' to correctly reflect the column name. A new 'Date assigned'
                   filter has been added to filter based on the 'Date assigned' column.

    TL-20155       Ensured that site policy content format was only ever set once during upgrade

                   Prior to this fix if the site policy editor upgrade was run multiple times
                   it could lead to site policy text format being incorrectly force to plain
                   text. Multiple upgrades should not be possible, and this issue lead to the
                   discovery of TL-20156.

                   Anyone affected by this will need to edit and reformat their site policy.

    TL-20192       Fixed deletion of seminar event after attendance was taken for learners

                   Previously, attempting to delete a seminar event where attendance for at
                   least one learner had been taken resulted in an error. Now, seminar event
                   deletion will be successful regardless of whether attendance has been taken
                   or not.


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
