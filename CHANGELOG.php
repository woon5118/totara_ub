<?php
/*

Totara LMS Changelog

Release Evergreen (27th October 2017):
======================================

Key:           + Evergreen only

Important:

    TL-16017       Issues from Moodle 3.2.5 have been included
    TL-16313       Release packages are now provided through https://subscriptions.totara.community/

                   Release packages are no longer being provided through FetchApp, and can now
                   be accessed through our new Subscription system at
                   https://subscriptions.totara.community/.

                   If you experience any problems accessing packages through this system
                   please open a support request and let us know.

                   Please note that SHA1 checksums for previous Evergreen releases will be
                   different from those provided in the changelog notes at the time of
                   release.
                   The reason for this is that we changed the name of the root directory
                   within the package archives to ensure it is consistent across all products.

Security issues:

    TL-12466       Corrected access restrictions on 360° Feedback files

                   Previously, users may have been able to access 360° Feedback files when
                   they did not have access to the corresponding 360° Feedback itself. This
                   will have included users who were not logged in. To do this, the user would
                   have needed to correctly guess the URL for the file. The access
                   restrictions on these files have now been fixed.

Performance improvement:

    TL-16061       Fixed a problem where duplicating a module caused the course cache to be rebuilt twice
    TL-16161       Reduced load times for the course and category management page when using audience visibility

Improvements:

    TL-8723    +   Updated text area custom field to display hyphen when the field is locked and empty
    TL-11296       Added accessible text when creating/editing profile fields and categories
    TL-12650       Removed HTML table when viewing the print book page
    TL-14936       Added a report setting to control the minimum allowed time for scheduled reports
    TL-15835       Made some minor improvements to program and certification completion editors

                   Changes included:
                    * Improved formatting of date strings in the transaction interface and
                   logs.
                    * Fixed some inaccurate error messages when faults might occur.
                    * The program completion editor will now correctly default to the
                   "invalid" state when there is a problem with the record.

    TL-15871   +   Force users to complete required user profile fields upon login

                   The users will be forced during the login to complete any user profile
                   fields that have been set as required and have not yet been completed for
                   that user.

    TL-15913       Greatly improved the display of the progress bar component and improved the quality of the CSS
    TL-16007   +   Converted warning messages in HR Import to use the notification API
    TL-16069       Improved alignment of question bank table headings
    TL-16170       Externally accessible badge check now uses the correct notify_warning template
    TL-16198       Fixed known problems with MySQL 8.0.3RC
    TL-16260       Invalid request to force password change is automatically deleted if auth plugin does not support changing of passwords

Bug fixes:

    TL-11085       Improved location of help icon when adding a recurring course to a program
    TL-15846       Removed an incorrectly displayed sidebar report filters dropdown from the Basis theme
    TL-15885       Fixed Navigation block problems with course visibility
    TL-15923       Fixed duplicate calendar records for  wait-list user calendar
    TL-15932       Fixed problem of SCORM window size cutting off content
    TL-15997       Fixed saving of new/changed Seminar direct enrolment custom fields
    TL-16124       Fixed Seminar booking confirmation sent to manager when no approval required
    TL-16186       Improved display of icons within bootstrap labels
    TL-16206       Added validation in HR Import to check job assignment useridnumber matches a user idnumber when importing data
    TL-16212       Fixed issue where self completion from within a certificate activity may complete a different activity
    TL-16215       Role assignments granted through the enrol_cohort plugin are now deleted if the plugin is disabled

                   Previously when the cohort enrolment plugin instance was disabled, the
                   roles for the affected users were not deleted from the {{role_assignments
                   table}} even though the log messages seemed to indicate this was the case.
                   This has been corrected with this patch.

                   Note the deletion behavior has always been correct in the original code
                   when the cohort enrolment plugin itself was disabled, However, it needs the
                   cohort enrolment task to be run first (every hour by default) to physically
                   delete the records from the table.

    TL-16223       Fixed a typo in the "cancellationcutoff" session variable
    TL-16224       Prevented orphaned program exceptions from occurring

                   It was possible for program and certification exceptions to become orphaned
                   - no exception showed in the "Exception report" tab, but users were
                   treated as having an exception and were being prevented from progressing.
                   The cause of this problem has now been fixed. After upgrade, use the
                   program and certification completion checkers to identify any records in
                   this state and fix them using one of the two available automated fixes
                   (which were added in TL-15891, in the previous release of Totara).

    TL-16238       Fixed warnings when running HR Import with Organisation and Position sources

                   If "Source contains all records" was set to yes and the deleted columns was
                   included in the uploaded CSV file then warnings would be shown if debugging
                   was enabled. This did not effect the functionality and HR Import still
                   finished successfully when this occurred.

    TL-16242       Scorm loading placeholders are now displayed correctly in RTL languages
    TL-16248       Added an is_array condition for json_decode alt string in the pix_icon class
    TL-16250       Replaced an incorrect usage of the pix_icon template
    TL-16254       Fixed automated course backup not taking audience-based visibility into account
    TL-16258       Fixed uniqueness checks for certification completion history

                   Certification completion history records should always be a unique
                   combination of user, certification, expiry date and completion date.

                   Completion import adhered to this rule, however the process of copying a
                   certification completion to history when the certification window opened
                   did not take the completion date into account. This led to overwriting of
                   the completion date if a history record had a matching expiry date but
                   different completion date. This could also lead to errors during the Update
                   certifications scheduled task.

                   The correct uniqueness rule has been applied consistently to prevent the
                   above behaviour.

    TL-16261       Added missing link to specialised customfield less within the Roots theme
    TL-16267       Fixed permissions error when accessing SCORM activities as a guest
    TL-16274       Fixed an issue when updating user Forum preferences when user's username contains uppercase characters
    TL-16279       Added additional checks when displaying and validating self completion from within an activity
    TL-16288       Checkbox and radio options lists no longer have bold input labels
    TL-16289       Fixed course completion editor link requiring incorrect capability

                   The link no longer requires the 'moodle/course:update' capability. It now
                   only requires the 'totara/completioneditor:editcoursecompletion'
                   capability.

    TL-16291       Fixed course progress display for courses with disabled completion tracking
    TL-16292       Fixed saving of seminar custom fields for all users
    TL-16301       Fixed calendar filtering on seminar room fields
    TL-16392       Fixed namespace of activity completion form

Miscellaneous Moodle fixes:

    TL-16019       MDL-58332: Fixed error when toggling the notification menu in MSSQL

                   When toggling the notification menu in MSSQL an exception would sometimes
                   be thrown.

    TL-16020       MDL-59317: Improved the loading speed of the Messages page
    TL-16021       MDL-52501: Fixed graded assignments with missing submission records on course restoration
    TL-16025       MDL-59431: Added an error log message when an AMD module cannot be loaded.
    TL-16026       MDL-59363: Fixed naming of learners when browsing submitted workshop files
    TL-16027       MDL-54965: Fixed an SQL error when editing a database activity entry after having added a new picture/file field
    TL-16029       MDL-59371: Fixed capabilities allowing default roles to access the Grades Overview report
    TL-16030       MDL-55979: Fixed an error when moving the last quiz question from the last page
    TL-16031       MDL-55912: Set grader details to the correct user
    TL-16035       MDL-59377: Fixed embedded image display in activity descriptions on course page
    TL-16036       MDL-58119: The send_stored_file() function now relies on the send_file() function
    TL-16037       MDL-59527: Fixed race condition when using autocomplete forms
    TL-16038       MDL-59411: When an activity is restricted and student follows a URL Totara should display why it is restricted
    TL-16041       MDL-59109: Auto commit SCORM package setting now import properly from a backup
    TL-16042       MDL-39471: Fixed comment visibility when blind marking
    TL-16043       MDL-59255: Added support for rtmp URLs in core_media_manager
    TL-16045       MDL-59490: Fixed LTI failures due to the resource title being wrapped during communication
    TL-16047       MDL-38129: Fixed a case sensitive failure with user profile fields when using grade export
    TL-16051       MDL-59485: Added missing LTI capabilities
    TL-16055       MDL-58744: Ensured Sticky block region can be overridden
    TL-16056       MDL-58196: Fixed activity restriction based on required passing grade
    TL-16059       MDL-57546: Added YouTube mobile URL support to media filter
    TL-16063       MDL-59473: Fixed Oauth2 Token response causing a redirection loop
    TL-16064       MDL-58928: Added missing ALT attribute on Enrollment "edit" and "delete" icons
    TL-16066       MDL-55364: Improved display of forum discussions table at low resolutions
    TL-16067       MDL-57259: Fixed debug error created by missing set_url() within get_fragment() of the core_external class
    TL-16071       MDL-59662: Fixed typo in mysql_collation CLI script
    TL-16072       MDL-51745: Fixed lesson feedback not using format_text
    TL-16073       MDL-59055: Fixed the incorrect display of manual grade
    TL-16075       MDL-59172: Removed redundant permissions check to moodle/user:viewalldetails
    TL-16077       MDL-59506: Fixed issue with unavailable DB lock factory during installation
    TL-16078       MDL-59737: Changed domain for anonymised users from doesntexist.com to doesntexist.invalid
    TL-16079       MDL-57188: Added missing call to $PAGE->set_url in the course management interface
    TL-16081       MDL-59785: Fixed TOC style Book navigation not marking user complete
    TL-16082       MDL-57611: Fixed capabilities for viewing logs on User's profiles

                   The capabilities report/log:view and report/log:viewtoday now control
                   access to the correct reports.

    TL-16084       MDL-58435: Fixed behat edit section step to work with section 0
    TL-16085       MDL-59836: Fixed autocomplete form element sometimes showing 'No suggestions' before showing correct results
    TL-16087       MDL-55937: Fixed error message when viewing on group submissions by plugin
    TL-16088       MDL-57246: Fixed redirection after attempting to view a forum without permissions
    TL-16089       MDL-59893: Fixed file prefixes in assignment download submissions
    TL-16090       MDL-57775: Fixed encoding errors in the XML-RPC client
    TL-16092       MDL-59826: Added context information to the user profile page
    TL-16093       MDL-59784: Refactored the addblock link to listen earlier
    TL-16094       MDL-59663: Fixed the Expand all link in the front page combo list widget when all categories expanded
    TL-16095       MDL-51827: When users confirm their own self-registration, they are now taken to a page that advises this was successful
    TL-16096       MDL-57412: Fixed course section headers to respect the 'Always link course sections' site setting
    TL-16097       MDL-59790: Fixed data label in chart tooltips
    TL-16099       MDL-59708: Added hooks to file API
    TL-16101       MDL-35290: Fixed bug preventing access to all private files if one file is missing
    TL-16102       MDL-56646: Fixed assignment grade rescaling not working correctly with empty grades
    TL-16103       MDL-59908: Course backups now parse both http and https links
    TL-16104       MDL-59195: Fixed error after using 'Switch to rule' and viewing assignments
    TL-16106       MDL-53936: Fixed URL when navigating within course completion report in course administration
    TL-16107       MDL-59963: Report > Logs pages now correctly set the origin parameter upon pagination
    TL-16109       MDL-59834: Fixed errors when global search tries to index message data for deleted users
    TL-16110       MDL-59815: Fixed definition of risks for user:delete
    TL-16111       MDL-59992: Fixed issue with invalid web service token causing errors in web server logs
    TL-16113       MDL-59198: Fixed compatibility with LTI version 2

Contributions:

    * Nicholas Hoobin at Catalyst AU - TL-16212


Release 10.0 (22nd September 2017):
===================================

Key:           +   Evergreen only

Important:

    TL-12978   +   Include features, improvements and bug fixes from Moodle 3.2

                   This release contains features, improvements and bug fixes from Moodle 3.2
                   By reviewing the changelog you can find out which Moodle issues have been
                   included.
                   Please be aware that not all Moodle changes are included in Totara, we are
                   now selective about what gets included from upstream.

    TL-15905   +   Minimum required version of MS SQL Server is 2014

Security issues:

    TL-12944       Updated Web Service tokens to use cryptographically secure generators

                   Previously, Web Service tokens were generated via a method which would
                   generate a random and hard-to-guess token that was not considered
                   cryptographically secure. New tokens will now be generated using
                   cryptographically secure methods, providing they are available in the
                   server's current version of PHP.

    TL-16116       Added a check for group permissions when viewing course user reports
    TL-16117       Events belonging to activity modules can no longer be manually deleted from the calendar
    TL-16118       Fixed the logic in checking whether users can view course profiles
    TL-16119       Fixed incomplete escaping on the Feedback activity contact form
    TL-16120       Added warning to admins when a development libs directory exists.

New features:

    TL-4156        Added the course completion editor

                   The course completion editor is accessible in Course administration >
                   Course completion, to all users who have the
                   'totara/completioneditor:editcoursecompletion' capability in the course
                   context (default is administrators only). The editor allows you to edit
                   course completion, criteria completion, activity completion and history
                   data, allowing you to put this data into any valid state. It includes
                   transaction logs, which record all changes that are made to these records
                   (both from within the editor and in other areas of Totara, e.g. completion
                   of an activity, or when cron reaggregates completion). It also includes a
                   checker, which can be used to find records which have data in an invalid
                   state.

Improvements:

    TL-853     +   Replaced existing Browse List of Users page with Report builder report

                   This new version of the user report provides the functionality of the
                   original plus all the benefits of a report generated within Report builder.

                   Please note, Mnet is not supported in the new report. You can however,
                   access Mnet functionality through a legacy version of the report.

    TL-8468    +   Added support for activity completion to course completion progress bars

                   Previously a user's progress towards completion of a course were indicated
                   via one of 3 states - not yet started, in progress and completed. This is
                   now replaced with a progressbar that indicates the actual progress towards
                   completion as a percentage.
                   If a user is unable to complete a course due to completion tracking not
                   enabled for the course,  no completion criteria defined for the course, or
                   the specific user's progress not being tracked, an indication of this is
                   shown instead of a progress bar.

                   In previous versions a user could obtain detail on actions required to
                   complete a course by clicking on the course's status bar. This is currently
                   not available but will be provided by the implementation of TL-15920

    TL-8741    +   Corrected handling of unique values for date and menu user profile fields.
    TL-9204    +   Updated 'Manage reports' page to use an embedded report

                   The report builder 'Manage reports' page has been split into two pages, one
                   for user reports and one for embedded reports. There is now a separate
                   capability that can be assigned independently so you can control who can
                   manage embedded reports.

                   In addition there is a new 'Reports' report source and the 'Manage reports'
                   page has been converted to an embedded report, which means it's possible to
                   customise the columns that are shown, add filters and export the list of
                   reports.

    TL-9315    +   Added self evaluations to 360° Feedback
    TL-10228   +   Add Job Assignment HR Import source

                   Job assignments can no longer be imported using the User HR Import source.
                   Data must now be imported using this source instead. Job assignment data
                   should be removed from the User source.

                   IMPORTANT: If users had their own 'HR Import' setting enabled for their
                   user record, all of their job assignments records will have the new 'HR
                   Import' setting enabled after upgrade.

                   This only applies during the upgrade. Updating a user's 'HR Import' setting
                   enabled after upgrade will not alter this field for their job assignments.

                   Consistent with how this setting operates for users and hierarchies, this
                   setting is off by default when adding a job assignment manually. It will be
                   on by default for job assignments created by the new HR Import source. The
                   setting is only visible in a job assignment record once the Job Assignment
                   HR Import source has been enabled.

    TL-10256   +   Removed incorrect HTML label tags (and replaced by aria attributes where appropriate) when viewing grader report
    TL-10918   +   Added additional information about what will be deleted when deleting Job assignments
    TL-11014   +   Improved the handling of single course certifications and programs within the Current Learning block

                   If a Program or the current path of a Certification only contains a single
                   course, then the item will be shown without the ability to expand and will
                   link directly to the Course within it instead of the Program/Certification.

    TL-12661   +   Added an accessible label when search glossary entries
    TL-12666   +   Added accessible text when changing forum digest type
    TL-12788   +   Add scheduled tasks to clean the course and certification completion upload logs

                   # added scheduled tasks to clean the course and certification completion
                   upload logs
                   # added 'Settings' submenu for Upload Completion Records where user can
                   specify the length of time a user want to keep the course/certification
                   completion upload logs information.

    TL-12949   +   Web service tokens no longer shown in Manage token interface

                   Previously, any current web service tokens were shown in the Manage token
                   admin interface. These are now only shown one time when they are created
                   and can not be found again after navigating away from the page.

                   The 'Security keys' page in the user preferences is unaffected by this, as
                   that only shows a user's own tokens and only if they have the correct
                   permission to view it.

    TL-14244       Updated default branding to Totara Learn

                   Changed language strings and logos to use the new product name "Totara
                   Learn" instead of "Totara LMS".

    TL-14275       Users can now cause self completion from within a course activity

                   This ability has been added to all core modules excluding Lesson and Quiz
                   (where a user should at least attempt the activity). Non-core modules will
                   need to be modified to support this functionality

    TL-14280   +   Improved pix mustache helper to support JSON objects
    TL-14394   +   Add global restriction initial display setting for report bulder
    TL-14789   +   Improved readability of label, button, badge and alert UI components.
    TL-14815   +   New report source for individual job assignments
    TL-14825   +   Job assignment filters are now using consistently correlated subqueries
    TL-15012   +   Added a new capability to upload courses
    TL-15056       Added warning notice to the top of delete category page
    TL-15768   +   Seminar enrolment expiration is now processed by a dedicated scheduled task

                   Course enrolments for audience members when memberships change in an
                   audience are now synchronised by a dedicated scheduled task.
                   The timing of this task can be configured in the Scheduled tasks
                   interface.
                   The task itself can be manually executed by running the following as the
                   web server user on the command line:

                       php admin/tool/task/cli/schedule_task.php
                       --execute="\\enrol_cohort\\task\\sync_members"

                   Expiration of Seminar direct enrolments is now processed by a dedicated
                   scheduled task.
                   The timing of this task can be configured in the Scheduled tasks
                   interface.
                   The task itself can be manually executed by running the following as the
                   web server user on the command line:

                       php admin/tool/task/cli/schedule_task.php
                       --execute="\\enrol_totara_facetoface\\task\\process_expirations"

    TL-15773   +   Ensured body and link text colour contrast ratio is at least 3:1 (WCAG 2.0 A compliant)
    TL-15774   +   Improved readability of notifications in theme_basis
    TL-15781   +   Role definition caching now makes use of an MUC application cache
    TL-15834       Improved Datepicker in Totara forms
    TL-15880   +   Removed rolechangedwarning css
    TL-15888   +   Behat and PHPUnit testing does not require superuser privileges for testing with PostgreSQL
    TL-15902   +   Behat configuration improvements

                   Developers can now run vendor/bin/behat without specifying of the
                   configuration file.

                   Chrome is now the default browser and all recommended setting are included
                   by default.

    TL-15908   +   Improved behat error detection
    TL-15935   +   Improved performance and reliability of behat testing
    TL-15965   +   Use BEHAT cookie to limit access to test sites
    TL-15972   +   Fixed brand notification warning in element library
    TL-15974   +   Upload message when editing a course now uses standard bootstrap fade
    TL-15983   +   Improved support for MariaDB 10.3
    TL-15996       Improved test environment init when switching PHP versions
    TL-16009   +   Form elements used for password entry are using standardised unmasking logic
    TL-16146   +   Behat settings can be changed via behat_local.yml file instead of CFG settings
    TL-16148       Improved performance of category management page

Bug fixes:

    TL-11012       Fixed formatting of grade percentage shown in quiz review

                   The configured 'decimal places in grades' value of a quiz is now also used
                   when formatting the grade percentage on the quiz review page. In earlier
                   releases the percentage has always been formatted with 0 decimal points
                   which resulted in confusing results.

                   Administrators and trainers are still responsible for ensuring that the
                   configured 'decimal places in grades' value will not result in confusion
                   for students due to the rounding up of the displayed values.

                   It is advised to use at least 2 decimal places if a student can score a
                   fraction of a point in any question in the quiz.

    TL-14002   +   Report table block reports take up the available width regardless of sidebar filters
    TL-14674   +   Fixed certification membership report not showing "Not assigned" status
    TL-14676       Fixed error when deleting a closed 360 Feedback
    TL-14753       Fixed the display of grades within the course completion report sources
    TL-14996       Disabled multiple selection during manager selection in signup form
    TL-15038       Fixed error when trying to save a search with availability filter in Rooms and Assets reports
    TL-15785       Fixed the display of manager and appraiser filters while creating a saved search
    TL-15844   +   Added scroll bar when viewing mustache template list
    TL-15879       Fixed missing icon from Progress column in Record of Learning in some cases
    TL-15883   +   Ensured Flavours admin UI uses Bootstrap 3 variables
    TL-15884       Fixed an Job assignment error when taking attendance for a Seminar activity
    TL-15891       Added checks and fixes for orphaned program user assignment exceptions

                   Under certain exceptional circumstances, it is possible for a user assigned
                   to a program or certification to have an exception, but that exception does
                   not show up in the 'Exception Report' tab. In this state, the user is
                   unable to continue working on the program, and the exception cannot be
                   resolved. With this patch, the completion checker has been extended to
                   detect this problem, and two triggerable fixes have been provided.

                   To resolve the problem, run the program and certification completion
                   checkers to find all records affected, or edit a completion record, then
                   choose to either assign the users or have the exceptions recalculated. If
                   the 'recalculate exceptions' option is chosen and an exception still
                   applies to a user, then after fixing the problem you can resolve the
                   exceptions as normal in the 'Exception Report' tab.

    TL-15892       Ensured course deletion does not effect awarded course badges
    TL-15897       Fixed some typos in Certification language strings
    TL-15899       Corrected inconsistent validation of Seminar sender address setting
    TL-15900       Fixed manager's manager not updating in dynamic appraisals

                   After upgrade, the next time the "Update learner assignments to appraisals"
                   scheduled task is run, it will update any managers' managers that have
                   changed, where the update is appropriate.

    TL-15919       Fixed missing delete assignment button for active appraisals
    TL-15921       Fixed multiple display of seminar attendees that have been approved more than once
    TL-15936       Fixed detection of non-lowercase authentication plugin names in HR Sync on OSX and Windows
    TL-15937       Added missing appraisal data generator reset
    TL-15938   +   Fixed several date related issues in behat
    TL-15941   +   Fixed a use of count() on an item that doesn't implement countable within assign, lesson and quiz modules
    TL-15946   +   Fixed a use of count() on an item that doesn't implement countable within report builder.
    TL-15966   +   Changed loading icon when uploading a file to a course to a font icon
    TL-15969   +   Added icons to notifications when on the site notifications page
    TL-15971   +   Fixed mform tags issue in element library
    TL-15977       Fixed SCORM cmi.interaction bug
    TL-15985   +   Corrected Behat test regressions introduced by TL-8741.
    TL-16010       Added reset method to hierarchy generator
    TL-16121       Fixed View Details link not working when user is viewing appraisal answers only
    TL-16126       Fixed how choice activity data is reset by certification windows
    TL-16159   +   Added 'msedge' and 'chrome' CSS classes to allow browser targeting

Miscellaneous Moodle fixes:

    TL-16033       MDL-57649: Fixed removing of attached files in question pages of lesson module

                   Fixed bug in lesson activity which did not automatically remove files
                   attached to question pages when those pages were deleted.

    TL-16034   +   Reverted MDL-54849 to return behaviour of next question button in lessons


Release Evergreen (23rd August 2017):
=====================================

Key:           + Evergreen only

Important:

    TL-7753        The gauth authentication plugin has been removed from all versions of Totara

                   The gauth plugin has now been removed from Totara 10, 9.10, 2.9.22, 2.7.30,
                   and 2.6.47.
                   It was removed because the Google OpenID 2.0 API used by this plugin has
                   been shut down.
                   The plugin itself has not worked since April 2015 for this reason.
                   No alternative is available as a brand new plugin would need to be written
                   to use the API's currently provided by Google.

Security issues:

    TL-10753       Prevented viewing of hidden program names in Program completions block ajax

                   Previously, a user visiting an AJAX script for the program completions
                   block could see names of hidden programs if certain values were used in the
                   URL. Names of programs can now only be seen if the user has permission to
                   view them.

    TL-14213       Converted sesskey checks to use timing attack safe function hash_equals()

Improvements:

    TL-7668    +   Improved HR Import External Database source sanity checks and error messages
    TL-9073    +   Minor text improvements in the Seminar activity
    TL-12375   +   Changed colour of state-info-* and brand-info theme variables to allow more easily recognisable information UI elements
    TL-12380   +   Added classes to abstract the execution of external applications

                   A library has been created to abstract the execution of shell commands.
                   This hardens security by only allowing applications to be run that are on a
                   whitelist.
                   On Unix systems, applications can also now be run via the PCNTL module. To
                   enable this, add and enable the PCNTL module to the PHP installation that
                   gets run via the CLI, then add the full path to the php binary to
                   $CFG->pcntl_phpclipath in config.php.
                   Information on using this library in custom plugins can be found at
                   [https://help.totaralearning.com/display/DEV/Command+execution+API]

    TL-12741   +   Course activities and types are now in alphabetical order when using the enhanced catalog

                   This also makes the sort order locale aware (so users using Spanish
                   language will have a different order to those using English)

    TL-12886       Improved formatting when viewing user details within a course
    TL-14096   +   Restricted Basis logo file upload to web images
    TL-14122   +   Only users who can manage company goal assignments will be shown current assignments when viewing a goal
    TL-14216   +   Converted loading icons when editing a course to font icons
    TL-14312   +   Standardised notification colours in Badges
    TL-14368       Added an autosubmit handler to Totara forms
    TL-14405   +   Fixed known compatibility problems with PHP 7.2 in PHPUnit
    TL-14420   +   Allow Reminders to be accessed with the "moodle/course:managereminders" capability only
    TL-14726       Stopped duplicate calls to the core_output_load_template webservice

                   When requesting the same template numerous times in quick succession via
                   JavaScript, the template library was firing duplicate requests to the
                   server. This improvement stops duplicate requests from happening.

    TL-14781       Improved efficiency of job assignment filter joins

                   Previously, job assignment filters were joining to the user table. Now,
                   they can join to the user id in another table, such as the report's base
                   table. If data from the user table is not needed then that join will no
                   longer be needed in order to use the job assignment filters. These changes
                   potentially result in a small performance improvement.

    TL-14790   +   Ensured block action icons conform to WCAG AA for text contrast
    TL-14812   +   Standardised order of Name and Short Name fields in User Profile Fields form
    TL-14971   +   Removed deprecated create_function() calls
    TL-14973   +   Removed the $tryloadifpossible parameter from cache::has()
    TL-14986       Added proficiency achieved date to competencies

                   Added new column called "timeproficient" to both the comp_record and
                   comp_record_history tables, this field defaults to the first time when a
                   user is marked proficient in a competency. There are also new "Date
                   proficiency achieved" columns/filters for the competency report sources,
                   and a date selector on the set competency status form allowing you to edit
                   the field. Please note that this field only works for future proficiencies,
                   but existing ones can be edited via the competency status form.
                   This change has also added a default value when the default competency
                   scale is created, so new installs will include a default value of 'Not
                   competent'.

    TL-14988       Ensured that a competency status is displayed on the Record of Learning even if a learning plan has been deleted
    TL-14991   +   Fixed compatibility issues with MySQL 8.0 alpha
    TL-14992   +   Enhanced the progress_bar output component to allow it to be used as a static progress bar also
    TL-15002       Added navigation links on the Approval plugin edit signup page
    TL-15006       Cleaned up and improved dataroot reset in behat and phpunit tests
    TL-15009       Added new faster static MUC cache for phpunit tests
    TL-15016       Improved the summary of the mod/facetoface:signupwaitlist capability to avoid confusion
    TL-15049   +   Database reset code for phpunit and behat was reimplemented
    TL-15087   +   Improved access control ordering in a couple of embedded reports
    TL-15099   +   Added additional validation checking to update_hierarchy_item function

                   In certain edge cases this function was not updating the hierarchy item and
                   silently failing.

    TL-15755       Unnecessary confirmation related emails are not sent when request is approved automatically in Self-registration with approval
    TL-15757   +   Improved the user experience when editing profile information through connected Totara sites
    TL-15760       Updated hardcoded URLs to point to new community site location

                   Links to the community in code were updated from community.totaralms.com to
                   the new url of totara.community.

    TL-15767   +   Audience enrolment synchronisation is now performed by a dedicated scheduled task

                   Course enrolments for audience members when memberships change in an
                   audience are now synchronised by a dedicated scheduled task.
                   The timing of this task can be configured in the Scheduled tasks
                   interface.
                   The task itself can be manually executed by running the following as the
                   web server user on the command line:

                   php admin/tool/task/cli/schedule_task.php --execute="\\enrol_cohort\\task\\sync_members"

    TL-15803       Added 'Target date' and 'Status' columns to Goal Custom Fields report source

                   This also allows adding these columns to exports of a user's goal
                   information. This can be done by adding these columns to the Goal Custom
                   Fields embedded report.

Bug fixes:

    TL-12459       Prevented the leave page confirmation when approving changes after adding an Audience rule
    TL-12859   +   Fixed HTML in Assignments to use standard CSS classes when viewing buttons
    TL-14148       Fixed static server version caching in database drivers
    TL-14170       Fixed LDAP/user profile custom field sync bug
    TL-14239       The required fields note now appears correctly when a Totara form is loaded via JavaScript
    TL-14316       Fixed the loading of YUI dialogs within Totara dialogs
    TL-14729   +   Prevented a directory check error when configuring HR Import to use a database instead of a CSV file
    TL-14805       Ensured appraisal question field labels display consistently
    TL-14813       Pix to Flex icon conversion now honours custom pix title attributes
    TL-14828       Forum posts only marked as read when full post is displayed
    TL-14935       Ensured that programs and their courses appear within the Current Learning Block when they are within an approved Learning Plan
    TL-14953       Fixed missing JavaScript dependencies in the report table block

                   While the Report Table Block allows the use of embedded report sources, it
                   does not add embedded restrictions (which are only added on pages where the
                   embedded report is displayed already).
                   This means specific embedded restrictions will not be applied in the table
                   and content displayed in block might be different from content displayed on
                   page.
                   For example, Alerts embedded report page will display only user's messages,
                   while the same report in the Report Builder block will display messages for
                   all users. It is better to use non-embedded report sources and saved
                   searches to restrict information displayed.

    TL-14954       Fixed the display of translated month names in date pickers
    TL-14984       Fixed the display of grades in the Record of Learning grades column
    TL-14994       Added missing parameter to job assignments url on the user profile page
    TL-15000       Removed duplicate error messages when approving signups
    TL-15011       Added check for valid hierarchy ids when accessing auth approved signup page with external defaults
    TL-15024       Fixed an error that occurred when exporting assignees and their job assignments for Seminar events
    TL-15025   +   Corrected a spelling mistake in the Reportbuilder date filter help text
    TL-15039       Fixed an SQL error that occurred when searching in filters using just a space
    TL-15040       Fixed the information sent in the attached ical when notifying users that a Seminar's date and details have been changed
    TL-15054       Fixed inconsistent behaviour when changing number of course sections
    TL-15057       ORACLE SQL keywords are now ignored when validating install.xml files
    TL-15080       Fixed context of dynamic audiences rules permission check

                   totara/cohort:managerules permissions were incorrectly checked in System
                   context in some cases instead of in the Category context.

    TL-15083       Updated the capability check in totara_gap_can_edit_aspirational_position to ensure new users can be created without error

                   When a new user is added, their id is -1 until their record has been
                   created. The totara_gap_can_edit_aspirational_position function has been
                   updated to recognise this and to allow for new users to be added.

    TL-15086       Fixed SCORM view page to display content depending on permissions

                   If the user has the mod/scorm:savetrack capability, they can see the info
                   page and enter the SCORM lesson.
                   If the user has the mod/scorm:viewreport capability, they can see the SCORM
                   reports.

    TL-15095       Fixed known compatibility problems with MariaDB 10.2.7
    TL-15097       Added a missing language string used within course reset
    TL-15103       Fixed handling of HTML markup in multilingual authentication instructions
    TL-15303       Fixed element heights set by JavaScript in grader report
    TL-15731       Fixed the display of personal goal text area custom fields in Appraisal snapshots
    TL-15738       Fixed program progress bar in Program Overview report source
    TL-15754   +   Updated totara_plan/view_plan_component template to ensure single buttons are displayed correctly
    TL-15775       Fixed incorrect encoding of language strings in Appraisal dialogs
    TL-15811       Fixed admin tree rendering to handle empty sub items
    TL-15838       Fixed Seminar Message Users to send a message to CC user manager

API changes:

    TL-13990   +   Activity completion caching now uses MUC and not the session
    TL-15812   +   Deprecated TOTARA_JS_PLACEHOLDER usage

                   As all supported browsers support the placeholder HTML attribute, the
                   placeholder JavaScript is no longer required.

Miscellaneous Moodle fixes:

    TL-14833   +   MDL-58780: Removed AS table alias in assignment grading table query
    TL-14838   +   MDL-58920: Fixed multilang support for Calculated questions' name
    TL-14840   +   MDL-58852: Apply multilang filters in choice activity charts
    TL-14842   +   MDL-58916: Fixed context filters when viewing user custom field data
    TL-14845   +   MDL-58756: Made role names in Statistics reports compatible with the Multi-Language filter
    TL-14846   +   MDL-58723: Improved testing of recurring events on the calendar
    TL-14847   +   MDL-58811: Fixed quiz duplication with files in their links

                   Fixed an issue with legacy file.php URLs from moodle 1.9

    TL-14848   +   MDL-57558: auth LDAP now recognizes lowercase attribute names
    TL-14849   +   MDL-58776: Added bootstrap classes to buttons on manage tags page
    TL-14850   +   MDL-58795: Ensure duplicates are not returned when sorting the grader report table
    TL-14851   +   MDL-58947: Fixed label link URLs that are displayed in global search results
    TL-14852   +   MDL-57957: Show feedback file in absence of grade item
    TL-14853   +   MDL-58986: Added bootstrap classes to buttons on quiz comment page
    TL-14854   +   MDL-56617: Disabled grade to pass check if CBM is used in the quiz activity.
    TL-14855   +   MDL-56973: Fixed title being locked when creating a new wiki page if language is forced for a course
    TL-14856   +   MDL-58922: Fixed multilang support for calculatedmulti question name
    TL-14858   +   MDL-58921: Fixed multilang support for Calculated question name
    TL-14860   +   MDL-58577: Fixed multilang support for role names in head of the statistics report
    TL-14862   +   MDL-49040: Fixed incorrect truncation of feedback comment in grader report

                   When quick grading and AJAX were enabled for the grader report if a
                   feedback comment contained a '&' then it would be truncated and only the
                   text before the '&' character would be saved.

    TL-14863   +   MDL-58997: Fixed the mutlilang on group names in the calendar
    TL-14864   +   MDL-49988: Fixed wiki page layout if html contains line breaks
    TL-14865   +   MDL-54887: Improved the formatting of exported multi-lang calendar events
    TL-14870   +   MDL-58900: Fixed incorrect overrides ordering within the assignment module
    TL-14871   +   MDL-58646: Updated PHP CSS parser library to newer version
    TL-14872   +   MDL-59086: Added bootstrap classes to buttons in grader report
    TL-14873   +   MDL-58658: Fixed cache static acceleration when setting empty but not false data
    TL-14875   +   MDL-59154: Lock for all caching builds
    TL-14877   +   MDL-46322: Only list enrolled graders as potential markers
    TL-14880   +   MDL-40015: Fixed 'Duplicate course' web service description
    TL-14882   +   MDL-51691: Feedback comments can now be deleted with save quick grading
    TL-14883   +   MDL-58136: Added a course completion cache
    TL-14885   +   MDL-58991: Ensured statistics report uses same date handling on chart rendering
    TL-14886   +   MDL-58523: Deleting responses can now cause feedback activities to be marked incomplete
    TL-14887   +   MDL-59140: Added "More..." link into "My Courses" navigation block, when not all courses are listed
    TL-14889   +   MDL-59142: Added caching for post-processed CSS in MUC
    TL-14891   +   MDL-44961: Fixed log dates being rolled forward when restoring course backups
    TL-14893   +   MDL-51917: Activities returned by get_criteria are now ordered the same as in the course
    TL-14894   +   MDL-59173: Changed the default of 'params' from null to array in the set_sql function of the table_sql class
    TL-14896   +   MDL-58729: Improved performance of mysql_collation admin script
    TL-14898   +   MDL-58472: Ensured videojs media player is initialised  on first page rendering to avoid failure on slower networks
    TL-14899   +   MDL-59005: Fixed extraction of zip files with Cyrillic file names
    TL-14900   +   MDL-58952: Fixed registration form language default
    TL-14901   +   MDL-59269: Fixed problem uninstalling language packs with numbers in their names
    TL-14903   +   MDL-49484: Fixed header wording for forms when adding/editing pages in a Lesson activity

                   Header text now show the type of page or question you are creating/editing
                   instead of the name of the activity.

    TL-14904   +   MDL-58813: Ensured the web service core_course_create_courses initialises all section records
    TL-14906   +   MDL-40818: Change login requirements on calendar pages to stop automatic guest  logins
    TL-14907   +   MDL-56046: Fixed export to Excel of Quiz reports
    TL-14909   +   MDL-59296: Searches on LatLong fields in Data module can only be for filled values
    TL-14913   +   MDL-59073: Workshop: Prevent submission creation without file/content
    TL-14915   +   MDL-32151: Fixed invalid references to 'nocourseid' language string throughout codebase
    TL-14917   +   MDL-57809: Added NO_OUTPUT_BUFFERING to progress bar output
    TL-14918   +   MDL-59308: Module completion now passes the module context to events when deleted
    TL-14922   +   MDL-58651: logstore_database: Add ability to not send database options
    TL-14923   +   MDL-58286: Fixed check for pagination in ldap enrollment
    TL-14924   +   MDL-59294: Improved markup of login page
    TL-14926   +   MDL-57021: Using password instead of password unmask fields where appropriate

                   The following fields now use 'password' instead of 'password unmask'
                   field:
                    * Entering passwords during self-registration
                    * Entering enrolment keys via the self enrolment and guest enrolment
                   plugins (this applies when end users supply the keys, not course
                   administrators creating them).


Contributions:

    * Barry Oosthuizen at Learning Pool - TL-14122
    * Richard Eastbury at Think Associates - TL-15775
    * Russell England at Kineo USA - TL-15083


Release Evergreen (19th July 2017):
===================================


Important:

    TL-14731       The Intl PHP extension is now required
    TL-14941       Having MySQL configured with mysql_large_prefix is now recommended

Security issues:

    TL-9391        Made file access in programs stricter

                   Restricted File access in programs to:
                    * Users that are not logged in cannot see any files in programs.
                    * Users who are not assigned can only see the summary and overview files
                    * Only users who can view hidden programs can see the files in programs
                   that are not visible

    TL-12940       Applied account lockout threshold when using webservice authentication

                   Previously, the account lockout threshold, for number of incorrect
                   passwords, was not taken into account when webservice authentication was
                   being used. The account lockout functionality now applies to webservice
                   authentication. Please note that this refers to the authentication type
                   that allows users to log in with username and password, not when accessing
                   their account using a webservice token.

    TL-12942       Stopped the supplied passwords being logged in failed web services authentication

                   When web service authentication was used, entries recorded to the logs for
                   failed log in attempts included the supplied password in plain text. This
                   is no longer recorded.

Report Builder improvements:

    TL-2821        Capability to configure a second database connection for Report Builder

                   It is now possible to configure a second database connection for use by
                   Report Builder.
                   The purpose of this secondary connection is so that you can direct the main
                   Report Builder queries at a read-only database clone.
                   The upside of which is that you can isolate the database access related
                   performance cost of Report Builder to an isolated database server.
                   This in turn prevents the expensive report builder queries from being
                   executed on the primary database, hopefully leading to a better user
                   experience on high concurrency sites.
                   These settings should be considered highly advanced.
                   Support cannot be provided on configuring a read only slave, you will need
                   in house expertise to achieve this.
                   Those wishing to use the second database connection can find instructions
                   for it within config-dist.php.

    TL-6834        Improved the performance of Report Builder reports by avoiding unnecessary count queries

                   Previously when displaying a report in the browser the report query would
                   be executed either two or three times.
                   Once to get the filtered count of results.
                   Potentially once more to get the unfiltered count of results.
                   Once to get the first page of data.

                   The report page, and all embedded reports now use a new counted recordset
                   query that gives the first page of data and the filtered count of results
                   in a single query, preventing the need to run the expensive report query to
                   get the filtered count.
                   Additionally TL-14791 prevents the need to run the query to get the
                   unfiltered count unless the site administrator has explicitly requested it
                   and the report creator explicitly turned it on for that report.
                   This reduction of expensive queries greatly improves the performance of
                   viewing a report in the browser.

    TL-14237       Fixed an SQL error when caching a report with Job Assignment fields

                   Removed an issue where caching of a report failed due to the SQL failing.
                   This is only for the User's Position(s), User's Organisation(s), User's
                   Manager(s) and User's Appraiser(s) filters.

    TL-14398       Report Builder source caching is now user specific

                   Previously the Report Builder source cache was shared between users.
                   When scheduled reports were being run this could lead to several issues,
                   notably incorrect results when applying filters, and performance issues.
                   The cache is now user specific. This consumes more memory but fixes the
                   user specific scheduled reports and improves overall performance when
                   generating scheduled reports created by many users.

    TL-14421       Improved the performance of the Site log report source when the event name filter was available

                   The "Event name" filter has been changed from an option selector to a
                   freetext filter improving the performance of the site log report.

    TL-14432       Improved performance when generating report caches for reports with text based columns

                   Previously all fields within a Report Builder cache had an index created
                   upon them.
                   This included both text and blob type fields and duly could lead to
                   degraded performance or even failure when trying to populate a Report
                   Builder cache.
                   As of this release indexes are no longer created for text or blob type
                   columns.
                   This may slow down the export of a full cached report on some databases if
                   the report contains many text or blob columns, but will greatly improve the
                   overall performance of the cache generation and help avoid memory
                   limitations in all databases.

    TL-14724       Improved aggregation of custom fields within Report Builder reports

                   Previously it was not possible to aggregate custom user profile field
                   columns in Report Builder reports.
                   It is now possible, providing the fields are set as visible to everyone.

    TL-14744       Fixed a JavaScript bug within the enhanced course catalog when no filters are available
    TL-14761       New better performing Job columns

                   Several new Job columns have been added to the available user columns in
                   reports that can include user columns.

                   The new Job columns can be found under the "User" option group, the
                   available columns are as follows:

                   * User's Position Name(s)
                   * User's Position ID Numbers(s)
                   * User's Organisation Name(s)
                   * User's Organisation ID Numbers(s)
                   * User's Manager Name(s)
                   * User's Appraiser Name(s)
                   * User's Temporary Manager Name(s)
                   * Job assignments

                   There are already several Job columns available in many sources, however
                   they operate slightly differently and perform very poorly on large sites.
                   The new columns have nearly the same result, but are calculated much more
                   quickly. In testing they were between 70-90% faster than the current
                   columns.

                   There is only one difference between the new and old columns and that is
                   how they are sorted when the user had multiple jobs.
                   The old columns all sorted the information in the column by the Job sort
                   order. This meant that all of the old columns were sorted in the same way
                   and the information aligned across multiple columns.
                   The new columns sort the data alphabetically, which means that when viewing
                   multiple columns the first organisation and the first position may not
                   belong to the same Job.

                   We strongly recommend that all reports use the new columns.
                   This needs to be done manually by changing from the Job columns shown under
                   "All User's Job Assignments" to those appearing under "User".
                   If you must use the old columns please be aware that performance,
                   particularly on MySQL and MSSQL could be a major issue on large sites.

                   The old fields are now deprecated and will be removed after the release of
                   Totara 10.

    TL-14780       Fixed the unnecessary use of LIKE within course category filter multichoice

                   The course category multichoice filter was unnecessarily using like for
                   category path conditions.
                   It can use = and has been converted to do so, improving the overall
                   performance of the report when this filter is in use.

    TL-14791       Report Builder reports no longer show a total count by default

                   The total unfiltered count of records is no longer shown alongside the
                   filtered count in Report Builder reports.
                   If you want this functionality back then you must first turn on "Allow
                   Report Builder reports to show Total Count" at the site level, and then for
                   each report where you want it displayed edit the report and turn on
                   "Display a Total Count of records" (found under the Performance tab).
                   Please be aware that for performance reasons we recommend you leave these
                   settings off.

    TL-14793       Filters which are not compatible with report caching can now prevent report caching

                   Previously filters that were not compatible with report caching, such as
                   those filters using correlated subqueries, could be added to a report and
                   report caching turned on.
                   This either lead to an error or poor performance.
                   When such a filter is in use in a report, report caching is now prevented.

    TL-14816       Added detection of filters that prevent report caching

                   Report Builder now reviews the filters that are being used on a report that
                   is configured to be cached before attempting to generate the cache in order
                   to check if the filter is compatible with caching.
                   If the filter is not compatible with caching then the report will not use
                   caching.
                   This prevents errors being encountered when trying to filter a cached
                   report for filters that are not compatible with caching.

    TL-14824       Improved the performance of the Site logs report source

                   Several columns in the Site logs report source were requiring additional
                   fields that did not perform well, and were not actually required for the
                   display of the columns in the report.
                   These additional fields have been removed, improving the performance of the
                   Site logs report source.

New features:

    TL-11096       New signup with approval authentication plugin

                   Thanks to Learning Pool for providing an initial plugin which informed the
                   design of this piece of work.

                   The new auth_approved plugin is similar to the existing auth_email plugin.
                   However, the auth_approved plugin has an approval process in which the
                   applicant gets a system access only if an approver approves of the signup.
                   The approver is any system user that has the new auth/approved:approve
                   capability. In addition, if the user also has the
                   totara/hierarchy:assignuserposition capability, he can change the
                   organisation/position/manager details that the applicant provided in his
                   signup.

                   The new plugin also has features to bulk approve or reject signups as well
                   as send custom emails to potential system users.

                   Finally, the new plugin also defines a report source that can be used as a
                   basis for custom reports.

Improvements:

    TL-5375        Added partial sync capability for Organisations and Positions to HR Import

                   It is now possible to import a position or organisation file that doesn't
                   contain all records. This is controlled by the "Source contains all
                   records" setting on the settings page for the element.

                   If "Source contains all records" is set to "No" for Organisations or
                   Positions then the deleted column is required in the source. For new
                   installs the default for this setting is "No".

    TL-7648        Ensured that required database source fields are always listed for HR Import
    TL-7699        Multi Select custom fields can now have multiple values set when used via HR Import

                   Multiple values can now be used for adding data to Multi Select' custom
                   fields when used with HR Import. The values need to be separated by a comma
                   (,). Where the value contains a comma, use single quotes (') around the
                   value.

    TL-9342        Time created and time modified are now recorded for Learning Plan Objectives

                   We now record the time a Learning Plan Objective was created, and when it was last
                   modified.
                   Two new columns have been added to Report Builder reports to display this
                   information.
                   Please be aware that this information is only available for Learning Plan Objectives
                   created or modified after upgrading to this version of Totara.

    TL-10216       Added Event start and finish time columns to Seminar Events report source
    TL-11295       Added accessibility link text to the previous program completions column when viewing a user's record of learning
    TL-12391       User Time Modified column output modified to improve accuracy and add a 'no date' filter.

                   A number of Report Builder additions and changes have been made to address
                   some clients requirements and improve the data available. In this change,
                   the behaviour of User Time Modified has been altered to ensure it
                   accurately shows if / when a user has modified their profile. In addition,
                   its corresponding filter has been updated so allow records where no time
                   modified has been set to be added to the report.

    TL-12748       Speed up password hashing when importing users in HR Import
    TL-12887       Prevented date (no timezone) user profile field displaying 'not set' to match the output of other profile fields.
    TL-12960       Drag and drop question images are scaled when they are too big for the available space
    TL-14032       Added supports_news functionality to the demo course format
    TL-14709       Changed manager job selection dialog to optionally disallow new job assignment creation
    TL-14755       Added an environment test for misconfigured MSSQL databases
    TL-14762       Added support for optgroups in Totara form select element
    TL-14771       The length of the Seminar room name is now validated
    TL-14820       Improved unit test performance and coverage for all Reportbuilder sources
    TL-14947       Improved unit test coverage of DB reserved words

Bug fixes:

    TL-12905       Fixed tag columns in report builder so they work with tag collections

                   This restricts the number of tags visible in certain report so they only
                   display the tags that are part of the current collection assigned to the
                   tag area.

    TL-14039       When using Custom events, keyboard interactions no longer do the browser default action
    TL-14336       Removed audience visibility checks for courses added to Learning Plans

                   This change is to bring Learning Plans in line with the behaviour that
                   already exists within Programs and Certifications.

    TL-14341       Fixed page ordering for draft appraisals without stage due dates
    TL-14361       Fixed Seminar direct enrolment not allowing enrolments after upgrade
    TL-14379       Fixed double encoding of report names on "My Reports" page
    TL-14435       Fixed the use of an unexpected recordset when removing Seminar attendees
    TL-14446       Fixed incorrect link to Course using audience visibility when viewing a Program
    TL-14680       Hide manager reservation link when seminar event is cancelled
    TL-14701       Removed unused 'timemodified' form element from learning plan competencies
    TL-14713       Fixed escape character escaping within the "sql_like_escape" database function
    TL-14719       Prevented duplicate form ID attributes from being output on initial load and dynamic dialog forms
    TL-14735       JavaScript pix helper now converts pix icons that only supply the icon name to flex icons
    TL-14741       Fixed a php open_basedir restriction issue when used with HR Import directory check
    TL-14750       Fixed restricted access based on quizzes using the require passing grade completion criteria

                   Previously, quizzes using the completion criteria "require passing grade"
                   were simply being marked as complete instead of as passed/failed. Since
                   they were correctly being marked as complete this had very little effect
                   except for restricted access. If a second activity had restricted access
                   based on the quiz where it required "complete with a passing grade", access
                   was never granted. This patch fixes that going forwards. To avoid making
                   assumptions about users completions, existing completion records have been
                   left alone. These can be manually checked with the upcoming completion
                   editor. In the mean time, if you are using the quiz completion criteria
                   "require passing grade" without the secondary "or all attempts used",
                   changing the access restriction to "Quiz must be marked as complete" will
                   have the same effect.

    TL-14765       Retrieving a counted recordset now works with a wider selection of queries
    TL-14778       Added new strings to the Seminar language pack to ease translation

                   Several strings being used by the Seminar module from the main language
                   have now been copied and are included in the Seminar language files in
                   order to allow them to be translated specifically for Seminar activities.

    TL-14794       Fixed Seminar list under course activity
    TL-14798       Ensured html entities are removed for export in the orderedlist_to_newline display class
    TL-14803       Fixed certificate custom text to support multi-language content
    TL-14804       Fixed issue with null in deleted column when using HR import

                   When importing an element using database HR Import if there is a null in
                   the database column a database write error was thrown. Now a null value
                   will be treated as 0 (not deleted).

    TL-14806       Ensured when enabling or disabling an HR Import element, the notification is not incorrectly displayed multiple times
    TL-14809       Corrected typos within graph custom settings inline help
    TL-14814       Close button in YUI dialogs is fully contained within the header bar
    TL-14929       Fixed the display of available activities if the user holds the viewhiddenactivities capability

                   Previously available and visible activities were shown to the user as
                   hidden (dimmed) if the user held the viewhiddenactivities capability,
                   despite the activity being both visible and available.
                   Activities are now shown as visible correctly when the user can both access
                   them and holds the above mentioned capability.

    TL-14933       Fixed problems with temporary tables when using 4byte unicode collations in MySQL
    TL-14934       Fixed a coding error when using fasthashing for passwords in HR Import
    TL-14990       Fixed the course progress icon Report Builder column
    TL-14993       Prevented all access to the admin pages from the guest user
    TL-15014       Fixed inconsistencies in counted recordsets across all databases

                   The total count result is now consistent across all databases when
                   providing an offset greater than the total number of rows.

    TL-15036       Added missing column type descriptor in the Totara Connect report source

Miscellaneous Moodle fixes:

    TL-11598       MDL-53304: Only show quiz answer "Check" button when it can be available
    TL-14919       MDL-59409: Fixed access control on admin categories
    TL-14920       MDL-56565: Prevented other users' username being displayed when manipulating URLs
    TL-14927       MDL-59456: Fixed a CAS authentication bypass issue when running against an old CAS server

Contributions:

    * Alex Glover at Kineo UK - TL-14341
    * Artur Rietz at Webanywhere - TL-14398
    * Jo Jones at Kineo UK - TL-14432
    * Russell England at Kineo USA - TL-14435
    * Pavel Tsakalidis for proposing the approach used in TL-6834


Release Evergreen (21st June 2017):
===================================


Security issues:

    TL-7289        Added environment check for XML External Entity Expansion

                   On upgrade or install, a check will be made to determine whether the
                   server's environment could be vulnerable to attackers including the
                   contents of external files via entities in user-supplied XML files. A
                   warning will only be shown if a vulnerability is identified. This check is
                   also available via the security report.

New features:

    TL-8169        Added placeholders to Appraisal messages

                   Appraisal messages can now use placeholders that will be replaced with the
                   relevant information immediately prior to sending the message.
                   Please review the inproduct help when creating and editing appraisal
                   messages for a list of available placeholders.

Improvements:

    TL-6009        Added additional columns to the Previous Certifications report source

                   The following columns were added: Status, Renewal status, Progress
                   (displayed as a progress bar)
                   The following filters were added: Status, Renewal status

    TL-6553        Added "Time to complete" columns to the Course Completions report

                   Two new columns have been added to the Course Completions report:
                   * Time to complete (since start date)
                   * Time to complete (since enrol date)

    TL-7693        Changed the notification url when an Evidence Type is added/edited to the list of all Evidence Types
    TL-8939        Added audience member filter to all report sources that have user fields
    TL-9224        Improved consistency of program exception restrictions

                   Previously some Programs code was still being executed on users with
                   exceptions, those places now check for valid user assignments before
                   processing the users. Some places identified were, the program completion
                   cron, the certification window opening cron, and the programs course
                   enrolment plugin.

    TL-9300        Updated the Date/time custom field so that it is not enabled by default

                   Making the Date/time custom fields disabled by default prevents the field
                   from being set inadvertently. When the custom field is marked as required
                   the field will always be enabled and default to the present date.

    TL-9775        Added Behat tests for Dynamic Audience Based Learning Plan creation
    TL-10502       Renamed Record of learning navigation block to "Learning" (from "Learning plans")
    TL-11264       Improved Atto editor autosave messaging and draft revert workflow

                   When a draft is automatically applied to an Editor, there is now a
                   page-level alert to let users know what has happened. In addition, the
                   default arrangement of toolbar icons now includes Undo/Redo which, when a
                   Draft is auto-applied, will toggle between original Database-saved content
                   and the Draft.

    TL-11323       Added HTML labels to inputs when creating and reviewing learning plans
    TL-11325       Added labels to the manage learning plan templates page
    TL-11444       Added table headings when showing current forum subscribers
    TL-12849       Improved alignment of the manage badges table
    TL-14187       New featured links blocks will now display without a border by default
    TL-14271       Fixed dynamic audience performance issue for user profile custom fields
    TL-14288       Added logs relating to program and certification assignment changes
    TL-14367       The login page now allows the configured registration plugin to control the onscreen signup message
    TL-14375       Embedded reports may now define custom required columns
    TL-14383       Improved performance of reportbuilder job assignment content restraints
    TL-14385       Added checks for missing program and certification completion records

                   The program and certification completion checkers have been extended to
                   detect missing and unneeded program and certification completion records.
                   Automated fixes have been provided to allow admins to correct these
                   problems. After upgrade, you should use the completion checker to fix all
                   "Files" category problems which are reported (if any). After all problems
                   on the site have been fixed, if new problems are discovered then they
                   should be reported to Totara support.

    TL-14429       Added support for relative dates in new forms in behat tests
    TL-14430       Converted the Reportbuilder source directory cache into a defined cache
    TL-14445       Added full details link to review items in Appraisals

                   When goals, objectives or competencies are selected for review in an
                   appraisal, a link will now be available which opens the full details of
                   that item in a new window. This link will only be shown if the user has
                   permission to view those details normally outside the appraisal.

                   This feature has only been added for the aforementioned review types so
                   far.

                   When adding items for review for any review questions, these items no
                   longer have their own collapsible header and will instead be collapsible
                   under the entire review question. Non-question elements such as fixed text,
                   fixed image and profile information also no longer have a collapsible
                   header as part of this change.

                   For any custom themes that impact on Appraisals or Feedback 360, it is
                   recommended that you review the appearance of these areas following
                   upgrade.

Bug fixes:

    TL-10374       Fixed an Appraisal bug when trying to add a question without selecting a type
    TL-12672       Fixed a php notice when saving data in location and textarea unique custom fields
    TL-12769       Fixed disabling of multi-select custom fields when set to locked

                   There was an issue with multi-select custom fields when they were set to
                   locked. This would result in only the first check box being disabled or
                   none of the check boxes being disabled (this depended on the browser).

    TL-14048       Fixed a bug resulting in duplicate entries in the "Record of Learning: Courses" report source

                   Previously the "Record of Learning: Courses" report source would show
                   duplicate records if no Learning Plan columns had been added to the
                   report.
                   This has been fixed and the "Record of Learning: Courses" report source now
                   correctly eliminates duplicates.

    TL-14140       Fixed security report check for whether Flash animation is enabled

                   The security report was checking for an outdated config setting when
                   checking whether Flash animation (using swf files) was enabled. The correct
                   config setting is now checked.

                   Flash animation is no longer enabled by default on new installations of
                   Totara, however this is not changed during upgrade for existing sites. If
                   Flash animation is not required on your site, you are encouraged to review
                   the security report and disable Flash animation and/or the Multimedia
                   plugin if they are not required.
                   Flash animations, when enabled, could only be added by trusted users who
                   had capabilities marked with XSS risk.

    TL-14144       Fixed ambiguous id column in course dialog when completion criteria is required
    TL-14161       Fixed location of dropdown arrow when editing tags
    TL-14224       Fixed the instance_config_save method in the featured links block
    TL-14251       Fixed the display order of goal scale values on the my goals page
    TL-14252       Fixed debug error when sending program messages with certain placeholders

                   Previously, if a program message (such as enrolment message) was sent out
                   for a user who was enrolled via multiple methods, and the message used the
                   %completioncriteria% or %duedate% placeholders, a debugging error is
                   thrown. This has now been fixed.

                   The %completioncriteria% placeholder was only designed to work when only
                   one enrolment method is in place for a user. Previously, the criteria
                   substituted into the email when a user did have multiple enrolment methods
                   was chosen randomly. Now the criteria will be taken from the enrolment with
                   the most recent assignment date/time.

    TL-14272       Fixed program and certification course enrolment suspension

                   Due to a recent change, users were being unenrolled from courses after
                   completing the primary certification path, when the courses were not part
                   of recertification. This has now been fixed, and any user enrolments
                   incorrectly suspended will be restored automatically by the "Clean
                   enrolment plugins" scheduled task. This patch also greatly improves the
                   performance of this task.

    TL-14289       Improved the layout when requesting a program extension from inside of a learning plan
    TL-14291       Fixed user unassignment from programs and certifications

                   This patch includes several changes to the way program and certification
                   completion records are handled when users are unassigned. It includes
                   a fix for a problem that could occur when users are reassigned. It also
                   ensures that program and certification completion records are correctly
                   archived when a user is deleted (with the possibility of being undeleted),
                   rather than being left active.

    TL-14301       Fixed validation of date form fields when nested inside a fieldset
    TL-14309       Fixed missing embedded fallback font causing error when viewing certificate
    TL-14315       Added HR Import check to ensure user's country code is two characters in length
    TL-14335       Backup annotation no longer tries to write to the temp table it is currently reading from

                   Backup annotation handling was opening a recordset to a temporary table,
                   annotating over the results and writing to the same table while the
                   recordset was still open.
                   This was causing significant performance issues and occasional failures on
                   MSSQL.
                   Only large complex backups would be affected.
                   This change removes the code sequence responsible replacing it with batch
                   handling for the temp table.

    TL-14350       Fixed invalid program due date when a user is assigned with an exception

                   This patch includes automated fixes which can be triggered in the program
                   and certification completion editors to fix affected records.

    TL-14357       Fixed a problem with the self-enrolment method not allowing unauthenticated users to enrol in a course
    TL-14365       Added missing $PAGE->set_url() calls when setting up a single activity course wiki
    TL-14366       Fixed reference to renamed Feedback module table feedback_tracking
    TL-14369       Auth plugins may now define external setting pages that do not require site config capability
    TL-14371       Added missing use of format_string() in hierarchy filter text
    TL-14381       Ensured the hierarchy filter displays any saved selections on page reload
    TL-14387       Changes to  notification templates now update unchanged notifications
    TL-14389       Improved the handling of incomplete AJAX requests when navigating away from a page
    TL-14390       Fixed inconsistency in icon markup on Report Builder columns when replaced via AJAX

                   The markup of the icons for Delete, Move up and Move down were different
                   when loading the page (after clicking "Save changes") and when the icons
                   were replace via AJAX (eg. when deleting a row).

    TL-14399       Fixed the "Manage searches" button in the Audience view report
    TL-14400       Form selection elements now accept integers in current values
    TL-14401       Removed incorrect link to the user profile in Report builder for missing data
    TL-14402       Type is not added automatically to embedded report columns with default heading
    TL-14411       Fixed reportbuilder exports for reports with embedded parameters
    TL-14414       Fixed auto-update of saved searches list in report table block editing form
    TL-14419       Fixed problems when restoring users to certifications

                   There were some rare circumstances where the incorrect data was being set
                   when a user was reassigned to a certification. The most common problem was
                   that the due date was missing on records that were in the "expired" state.
                   The cause of the various problems has been prevented. Records which have
                   already been affected can be identified using the certification completion
                   checker and corrected using the certification completion editor and/or
                   automated fixes.

    TL-14426       Fixed dialog scroll when adding "Fixed image" questions to an appraisal
    TL-14437       Added an automated fix for expired certifications missing a due date

                   An automated fix has been added to the certification completion editor.
                   When applied to expired completion records which are missing a due date, it
                   automatically sets the date to the latest certification completion history
                   expiry date which is before the current date. If no appropriate history
                   record is found then the due date must be set manually.

    TL-14447       Fixed double html escaping when searching for course names that include special characters
    TL-14672       Fixed permissions check for taking attendance within Seminar events

                   Previously it was not allowed to submit Seminar attendance without
                   mod/facetoface:addattendees or mod/facetoface:removeattendees permission.
                   Now mod/facetoface:takeattendance is enough.

    TL-14686       Fixed a typo in a variable name used in organisation file type custom fields
    TL-14690       Fixed error when creating a plan where a user has multiple jobs with duplicate position competencies.

API changes:

    TL-14413       Added two new methods to the DML to fetch recordsets and a total count at the same time

                   Two new methods have been added to the DML that allow for a recordset to be
                   fetched and simultaneously a total count returned in single query.
                   The two new methods are:
                   * moodle_database::get_counted_recordset_sql
                   * moodle_database::get_counted_records_sql

Miscellaneous Moodle fixes:

    TL-14565       MDL-57658: Fixed calendar unit tests
    TL-14568       MDL-57429: Badges now uses the new openbadges authentication service
    TL-14571       MDL-57994: Fixed "Number of announcements" course setting not reloading correctly
    TL-14572       MDL-57254: Ensured Choice activity checks correctly when results should be displayed
    TL-14573       MDL-57419: Hitting enter no longer sends messages within the messaging interfaces
    TL-14575       MDL-37168: Fixed LTI activity quick edit title
    TL-14576       MDL-58273: Fixed incorrect capability name used when enabling and disabling LDAP enrolment instances
    TL-14579       MDL-58050: Fixes message transaction handling when the user has no messages
    TL-14586       MDL-58257: Fixed course search when search query contains a hyphen
    TL-14588       MDL-58160: Improved the performance of category caching
    TL-14589       MDL-58325: Changes to site text editor settings are recorded in the config log
    TL-14590       MDL-58227: Fixed error when getting most recently completed answers in feedback module
    TL-14592       MDL-58264: Fixed incorrect SQL syntax in question engine
    TL-14594       MDL-41809: Course grade items are now formatted using the course context
    TL-14595       MDL-55499: Forum emails are now formatted using the correct context
    TL-14598       MDL-58180: Ensured Statistics Role names are passed through the format_text function
    TL-14599       MDL-58104: Fixed assignment bug in which attempt settings disappears when switching between attempts in grader interface
    TL-14600       MDL-55939: Removed unnecessary permission check on site course during external service call
    TL-14601       MDL-56370: Added back ability to change answers in Feedback module when not anonymous
    TL-14602       MDL-57858: Fixed assignment bug in a scale grade was not updated
    TL-14603       MDL-55950: Emails regarding completed feedbacks now link to only completed feedbacks
    TL-14605       MDL-58489: Fixed coding bug in the OAuth upgrade token process when the authentication server cannot be reached
    TL-14607       MDL-58461: Upgraded the MathJax library use a fixed CDN version
    TL-14608       MDL-57616: Fixed drag and drop of media files to course page.
    TL-14609       MDL-58555: Included web service name when making ajax requests
    TL-14612       MDL-58171: Fixed use of multilang in Course participation report headers.
    TL-14613       MDL-58244: Improved the logout process when using Shibboleth for authentication
    TL-14616       MDL-58394: Fixed filter processing not respecting sort order in some cases
    TL-14619       MDL-58486: Fixed lingering references to unset user preferences on the current user object
    TL-14622       MDL-58559: Fixed a missing string error in the community block
    TL-14624       MDL-58116: Forum emails are no longer sent by the noreply email address when they shouldn't be
    TL-14626       MDL-58096: Optimised performance of Course statistics report when loading list of courses
    TL-14628       MDL-58278: Fixed assignment bug in which data was not saved when marking workflow state as not released
    TL-14633       MDL-58613: Prevented debug messages from being displayed in Workshop random allocation.
    TL-14634       MDL-58636: Fixed incorrect drag and drop constraint in course management
    TL-14636       MDL-57793: Improved Calendar repeating rule unit tests
    TL-14637       MDL-58556: Fixed LDAP authentication creating forced password change loop
    TL-14638       MDL-58668: Fixed how multi choice answers are processed in Lesson activity
    TL-14639       MDL-54849: Fixed the 'move to next question' option in the Lesson activity
    TL-14640       MDL-58691: Fixed define checking for external_settings used by web services
    TL-14641       MDL-58372: Fixed error when loading files via WebDAV
    TL-14642       MDL-57807: Fixed search in database activity when not selecting an option in a menu field type

                   When performing a search in the database activity leaving the option on
                   "custom-select" which is the default option will result in no results being
                   returned in the search. The change will now correctly return all matching
                   records treating the unselected option for the menu as a wildcard.

    TL-14643       MDL-58698: JavaScript loads from language packs with 2 underscores (eg. en_us_k12)
    TL-14644       MDL-58701: Used proper defaults for serving files when webserver is used in externallib constructor
    TL-14645       MDL-58628: Fixed incorrect values being returned by mod_quiz_get_quizzes_by_courses quiz webservice when quiz is closed
    TL-14650       MDL-55468: Added the option to export analysis to Excel back to the Feedback module
    TL-14651       MDL-57704: Stopped forcing SSLv3 in LTI provider

                   SSLv3 is considered outdated and insecure so we shouldn't be enforcing the
                   use of it.

    TL-14652       MDL-58172: Ensured responses export respects user identity fields setting and viewuseridentity capability
    TL-14653       MDL-58635: Ensured external blog edits belongs to current user.
    TL-14656       MDL-58650: Fixed messages being marked as read when user receives emails about them
    TL-14657       MDL-50670: Fixed some default options not being correctly applied with custom course formats
    TL-14658       MDL-58434: Correct display of user responses to numeric question type in Lessons.
    TL-14659       MDL-35913: Front page layout is set before any output is sent

                   In some rare occurrences, the front page layout was being set after some
                   code had been displayed. This fix ensures the layout is set before any
                   output is sent

    TL-14660       MDL-58772: Prevented anonymous answers from being overwritten in feedback module
    TL-14663       MDL-58514: The assignment submission page and grading table now use consistent override logic
    TL-14664       MDL-56675: Memcache is no longer used as the cache store in known bad configurations

                   There is a compatibility bug between the Memcached extension and the
                   Memcached server.
                   If you are using php-memcached extension > 3.0.1 and Memcached library >
                   1.4.22.
                   In this situation the cache will not be purged when required if the
                   configuration has been configured to facilitate a shared cache.
                   As this could lead to stale caches code has been amended to prevent the
                   memcached cache store from being used in situations where we know it is
                   affected.

    TL-14665       MDL-58431: Fixed error in Lesson activity for the Jump to random content page option
    TL-14670       MDL-58259: Added permission check for adding attachments to forum posts via web services
    TL-14671       MDL-58807: The activity results block now correctly formats the activity name

Contributions:

    * Artur Rietz at Webanywhere - TL-14271
    * Barry Oosthuizen at Learning Pool - TL-14445
    * Eugene Venter at Catalyst NZ - TL-9300, TL-10502
    * Francis Devine at Catalyst NZ - TL-14430
    * Michael Trio at Kineo UK - TL-14357
    * Russell England at Kineo US - TL-14144


Evergreen 20170519 (22nd May 2017):
====================================


Important:

    TL-12803       Ensured the default run times for scheduled tasks are set correctly

                   The default run times for several scheduled tasks were incorrectly configured
                   to run every minute during the specified hour, rather than just once per day.
                   To schedule a task to run once per day at a specific time, both the hour and
                   minute must be specified. The defaults have now been fixed by changing the
                   'minutes' from '*' to '0'. Any scheduled tasks that were using the default
                   schedule have been updated to use the new default. If any of your scheduled
                   tasks intentionally needed to use the old default schedule, or are not using
                   the default schedule, you should manually check that they are configured correctly
                   after running the upgrade.

    TL-14327       "Fileinfo" php extension is now required

                   This was previously required but not enforced by environment checks

    TL-14278       Changed mathjax content delivery network (CDN) from cdn.mathjax.org to cdnjs.cloudflare.com

                   cdn.mathjax.org is being shut down

Security issues:

    TL-14332       Capability moodle/blog:search is checked when blog search is applied in browser url request
    TL-14331       Users are prevented from editing external blog links.
    TL-14333       Added sesskey checks to the course overview block
    TL-14273       Fixed array key and object property name cleaning in fix_utf8() function
    TL-14258       Improved access control of files used in custom fields

                   Previously inconsistent checks were made when accessing files used in custom fields.
                   A brand new segment of API has been added to allow each area to accurately validate
                   access to files used within it, and all custom field areas have been updated to use the new API.

New features:

    TL-13154       New Modal library added
    TL-13417       User tours can now be created within Totara.

                   These tours are experienced by users upon meeting certain criteria such as
                   logging in or holding a certain role, and when browsing specific areas of
                   the site. When encountered they feed the user with information and direct
                   them through elements on the site, or basic navigation.

Improvements:

    TL-12347       Added a Red-amber-green status column and filter to the certifications report sources
    TL-12732       Added accessible text to Seminar Room and Asset availability filter types
    TL-9217        Updated Completion Import tool to use core csv_import_reader class
    TL-6766        Added a new column to the Appraisal status report source to show roles that haven't completed the current active stage
    TL-14277       totara_core\jsend now automatically removes invalid utf-8 characters and null bytes from received data
    TL-14260       Behat no longer gives false failures when text appears in a hidden element and its visible parent element
    TL-14169       Improved display when installing Totara through the web interface
    TL-14112       Forced themes in categories will now apply to programs and certifications
    TL-8318        Added an Enrolment Types column and filter to the Course Completion report source
    TL-12964       Updated the standard course catalog search to allow single character searches

Bug fixes:

    TL-12786       Fixed error when selecting objectives to review in an appraisal

                   When selecting Objectives to review in an appraisal, there is no longer an
                   error when there are only objectives from completed Learning Plans. Objectives
                   from both complete and incomplete Learning Plans are now shown, providing
                   the objectives are assigned to the learner and approved.

    TL-12609       Refactoring and fixing of custom user profile fields and filters in Reportbuilder
    TL-12467       Fixed validation when viewing a course as a guest with self enrolment enabled and
    TL-12415       Fixed the iCalendar cancellation email settings message for Seminars
    TL-9279        Fixed the display of images in Seminar Room and Asset textarea customfields
    TL-14342       Ensured Atto drag & drop content images are responsive by default
    TL-14305       Fixed saving user reports after filtering by position
    TL-14329       Fixed debugging warning when editing forum post
    TL-14284       Fixed missing set_url calls within Appraisal review question AJAX scripts
    TL-14290       Fixed invalid Program due dates in Learning Plans

                   The due date would sometimes show "01/01/1970" rather than being empty. The cause,
                   and existing data, have been fixed.

    TL-14292       Fixed typo in certificate module
    TL-14257       Fix report with graph when Enable report builder graphs is disabled
    TL-14261       Fixed program completion editor not working in some circumstances
    TL-14264       Fixed RTL CSS inheritance in non-less themes

                   Prior to TL-13909, RTL wasn't being inherited correctly in themes that used LESS
                   to compile CSS (such as Roots and Basis). TL-13909 introduced a regression where
                   RTL CSS was not being inherited correctly (as used in Standard Totara Responsive).
                   The theme stack now checks for a stylesheet with a suffix -rtl.css, and if it exists,
                   includes it, otherwise includes the standard stylesheet.
                   (which can use the .dir-rtl body class to specify any RTL specific css)

    TL-14167       Featured Links Block: Fixed spelling of Colour
    TL-14177       Adding an activity to a course uses font icons
    TL-14101       Fixed Report builder saved searches for job assignment filters

                   Previously on upgrade to T9 or higher, saved searches using old position assignment
                   filters were not upgraded, they are now mapped to the corresponding job assignment
                   filter. There was also an issue creating new saved searches based on some job
                   assignment fields which has been fixed as part of this patch.

    TL-14046       Made the course list in user profiles take audience visibility into account
    TL-13931       Fixed JavaScript issue where activity self completion may not work
    TL-14029       Fixed issues with caching requests using the same CURL connection
    TL-14241       Fixed the inline help for course and audience options on the Totara Connect add client form
    TL-13968       Ensured that userids are unique when getting enrolled users

                   This was causing a debugging error when checking permissions of users with multiple roles

    TL-14240       Fixed search tab in appraiser/manager dialog boxes for job assignments report builder filters

Contributions:

    * Kineo UK - TL-14241


Evergreen 20170426 (26th April 2017):
====================================


Important:

    TL-11457       MDL-52139: Include features, improvements and bug fixes from Moodle 3.1

                   This release contains features, improvements and bug fixes from Moodle 3.1
                   By reviewing the changelog you can find out which Moodle issues have been
                   included.
                   Please be aware that not all Moodle changes are included in Totara, we are
                   now selective about what gets included from upstream.

    TL-12853       The TinyMCE editor has been removed from core
    TL-12984       MDL-54676: Include features, improvements and bug fixes from Moodle 3.2

                   This release contains features, improvements and bug fixes from Moodle 3.2
                   By reviewing the changelog you can find out which Moodle issues have been
                   included.
                   Please be aware that not all Moodle changes are included in Totara, we are
                   now selective about what gets included from upstream.

    TL-13086       MDL-49533: The alfresco repository plugin has been removed from core
    TL-13474       MDL-55927: The radius authentication plugin has been removed from core
    TL-13862       MDL-48228: MySQL/MariaDB drivers now require barracuda file format and include support for full
                              unicode utf8mb4_* collations

                   Administrators can use utf8mb4_ collations in config.php to get full
                   unicode compatibility on MySQL servers. This setting must be added to
                   config.php before the installation or after the migration to new setting.
                   At the same time MySQL and MariaDB driver requires Barracuda file format.

    TL-13916       The 'Use HTTPS for logins' setting has been removed

                   It is no longer possible to require login via HTTPS without serving all
                   pages via HTTPS.
                   Those wishing to use HTTPS (highly recommended) need to use it across the
                   whole site.

    TL-13921       The bootstrapbase theme and other deprecated themes have been removed from core

                   The following themes have been removed from core:

                   * bootstrapbase
                   * standardtotararesponsive
                   * customtotararesponsive

    TL-13943       All emails are now sent from the no reply address

                   Previously this was the behaviour when $CFG->emailonlyfromnoreplyaddress
                   was enabled, this setting was removed because it is now always on. Please
                   note it is strongly recommended to use SMTP sending account with the same
                   address as $CFG->noreplyaddress, otherwise emails may get marked as spam or
                   not delivered at all.

    TL-14206       The slasharguments setting has been removed from core

                   Behaviour of the site is now always equivalent to having had this setting
                   turned on.

    TL-14250       Minimum supported version of MS SQL Server was raised to 2012

Security issues:

    TL-12538       MDL-53677: Fixed session key handling within tool_spamcleaner
    TL-12634       MDL-49026: Added functionality to remove web services tokens when a user's password is changed

                   Before this patch web services tokens remained valid when a user's password
                   was changed. Now tokens are removed as a security precaution provided that
                   the config setting 'passwordchangetokendeletion' has been set to true. If
                   it hasn't, users also given the opportunity to clear web service tokens in
                   the change password interface.

New features:

    TL-11319       Added a Featured Links block

                   Added a block which can be added to the front page, dashboards and courses
                   that displays links as tiles. The tiles can have background images or a
                   chosen color and can reference a course or use internal and external links.
                   There are visibility options for each tile allowing them to be hidden and
                   shown as required.

    TL-11565       MDL-31989: Added global search as an experimental feature

                   Global search has arrived as a feature.
                   It requires the use of a separate search platform to provide users with a
                   indexed search of site content.
                   Currently the only supported search platform is Apache Solr.

    TL-11630       MDL-48012: Added a Recycle bin tool for courses and activities

                   When enabled deleted courses and activities will be backed up immediately
                   prior to their deletion and then stored for a configurable period of time.
                   During this window, users with the required permissions will be able to
                   visit the recycle bin and restore the course or activity that they deleted.
                   The restoration will occur via the backup system which will result in the
                   information in the backup being restored.
                   After this window the backup will be automatically cleaned up by a
                   scheduled task.

    TL-11967       MDL-53599: Added support for Redis as a session handler
    TL-11980       MDL-51603: Introduced new data formats and included the Spout library in support

                   This change saw two notable goals achieved.

                   * A new plugin type data formats has been introduced. This function just
                   like tabular exports in Totara and allow data streaming exports across
                   Totara. This both improves performance and allows for new export formats to
                   be more easily integrated.
                   * The introduction of the Spout library. The Spout library is used to
                   export data to common formats including CSV and XLSX. With its inclusion,
                   data formats can utilise it to easily export accurately to common formats.

    TL-11992       MDL-52035: Added a new experimental feature that allows Totara LMS to act like an LTI Provider
    TL-13107       MDL-54606: Added support for Redis as a session cache
    TL-13380       MDL-54682: Added new messaging and notification interfaces

                   Users can now navigate to new messaging and notifications interfaces via
                   icons next to the user menu.

                   The messaging interface allows for live updating of conversations between
                   users and viewing profile details and online status when permissions allow.

    TL-13480       MDL-48468: Added a Redis cache store

Improvements:

    TL-5224        Added course date created column and filter to report sources that include course columns
    TL-10250       Added alt text to icons and buttons within the Atto editor
    TL-10490       Added a Seminar Sign-up link directly into the calendar upcoming events block
    TL-11298       Removed superfluous HTML labels when viewing an Appraisal
    TL-11321       Added labels associated with goal statuses when viewing a user's goals
    TL-11450       MDL-48451: Improved view count in course outline report
    TL-11452       MDL-44598: Added user details information when granting assignment extensions
    TL-11467       MDL-51900: In Single view gradebook report the Tab now moves focus in Grade or Feedback column instead of rows
    TL-11478       MDL-51698: Added breadcrumb trail when indexed by Google

                   If Totara site shows up in a Google (and possibly other search engines)
                   search result, the breadcrumbs will now show in the search result

    TL-11482       MDL-45712: Added result fields to the SCORM Interactions Report
    TL-11483       MDL-52560: Badges navigation is not added under Course administration if badges are disabled
    TL-11503       MDL-52661: Improved accessibility when creating/editing grading aids
    TL-11506       MDL-52309: Grade History report now requires user to click submit button before displaying results
    TL-11507       MDL-372: Added support for pinned discussions in forums
    TL-11508       MDL-42473: Added group support to SCORM
    TL-11509       MDL-50464: Improved themeability of the RSS block
    TL-11511       MDL-52738: Added a previous button to Quiz attempt/review pages
    TL-11512       MDL-52383: Calendar option "calendar_lookahead" can now be set to one year ahead.

                   Users can now set their upcoming events look ahead to maximum one year
                   instead of 90 days.

    TL-11513       MDL-52780: Improved script origin email header to show where exactly message or email sending was triggered
    TL-11514       MDL-36404: Improved accessibility when grading with rubrics
    TL-11515       MDL-52269: Notification added to Assignments that are being marked in blind marking mode.
    TL-11519       MDL-46091: Merged the time-limit and password request into a single popup

                   * The "Are you sure you want to start this quiz now" popup is only shown if
                   the quiz has a time limit.
                    * If the quiz has a password as well as a time limit, the password request
                   and time limit warning are shown on a single popup.

    TL-11520       MDL-48621: Added a notice to the admin notification page if third party code is using Event API handlers
    TL-11521       MDL-35590: Improved aria support in the settings and navigation blocks
    TL-11526       MDL-48439: Improved the highlighting within capabilities overview table cells
    TL-11527       MDL-50620: Improved SCORM mastery score handling

                   Mastery score handling improved as per recommendation in
                   http://scorm.com/blog/2010/09/anatomy-of-scorm-minutiae-mistake/

    TL-11535       MDL-51306: Added option to download all folder files as zip archive
    TL-11536       MDL-52996: Allow Atto customisation for special-purpose plugins

                   When adding an Atto editor to a form, a custom toolbar can be specified.

    TL-11537       MDL-48634: Added option to rescale grades when changing max grade in an activity
    TL-11538       MDL-44087: Forum now observes message notification settings for digest emails
    TL-11539       MDL-52818: Added a new divertallemailsexcept configuration option

                   A new configuration option $CFG->divertallemailsexcept has been added.
                   When used in conjunction with $CFG->divertallemailsto all emails will be
                   diverted unless they appear in $CFG->divertallemailsexcept.
                   More information can be found in config-dist.php.

    TL-11541       MDL-51839: Removed old module gif icons
    TL-11542       MDL-52414: Improved how default Lesson settings are applied upon creation of a new activity instance
    TL-11546       MDL-50385: A new database index was added to the grade history table

                   On some sites with grade history tables the upgrade may take hours, if
                   that is the case it is strongly recommended to use CLI upgrade instead of
                   upgrade via web interface.

    TL-11549       MDL-53077: Added page action to body CSS ID in mod_assign
    TL-11550       MDL-51802: Standardised inline editing for tags and topic titles
    TL-11552       MDL-51214: Naming of Block "Latest news" and Course "News forum" have been changed to a more suitable
                              "Announcements" in several langstrings.
    TL-11553       MDL-34160: The Forum email subject can now be further customised

                   These placeholders can be used in the 'postmailsubject' language string in
                   mod/forum/lang/en/forum.php:
                   * $a->sitefullname
                   * $a->siteshortname
                   * $a->courseshortname
                   * $a->coursefullname
                   * $a->courseidnumber
                   * $a->forumname
                   * $a->subject

    TL-11555       MDL-52990: Added site wide email mustache templates
    TL-11556       MDL-52208: The SOAP webservice handler no longer uses the Zend framework
    TL-11557       MDL-51929: Performance improvements to LTI, Data and Survey modules via new optional parameter in the
                              validate_courses function.
    TL-11561       MDL-52806: Reviewing quiz responses now displays the correct answer
    TL-11562       MDL-49324: Added a progress bar when re-grading courses

                   When there are a number of activities and users enrolled in a course, a
                   progress bar is now displayed when re-grading the course.

    TL-11568       MDL-50887: Added antivirus plugins support
    TL-11569       MDL-50175: Optimised core pix images
    TL-11571       MDL-50794: Allow restriction of attached file types in a workshop
    TL-11572       MDL-51571: Improved the error handling of the LTI service module
    TL-11573       MDL-52346: Cache definitions now include information on whether they can be safely pointed at local storage solutions
    TL-11574       MDL-53072: Added option to choose whether to include suspended users when auto-creating groups
    TL-11575       MDL-52489: Zip file with downloaded assignment submissions now has separate folders for each student
    TL-11576       MDL-53050: Added a highlight to forum posts when viewed after navigating via a URL deeplink
    TL-11580       MDL-53172: Replaces static fields with in-place editable fields, in the form of simple toggles, select menus &
                              string editing.
    TL-11581       MDL-53263: Added anchor to forum reply by post confirmation email link
    TL-11582       MDL-53208: Improved the performance of the Cache API
    TL-11583       MDL-53213: Improved the performance of the database meta information cache
    TL-11597       MDL-50032: Allowed external functions to add themselves to services
    TL-11604       MDL-52522: Added option to rescale overridden grades in grade categories
    TL-11606       MDL-27628: Multiple meta linked courses can now be added to the course enrolment
    TL-11608       MDL-52252: Activities and resources can now be tagged
    TL-11612       MDL-52386: Added support for a suspended field in LDAP authentication
    TL-11620       MDL-48680: Added new SCORM events:  "Submitted SCORM status" and "Submitted SCORM raw score"
    TL-11621       MDL-53301: Improved performance when updating grading weights
    TL-11622       MDL-53252: Ensured Gradebook regrading is skipped if only feedback is changed
    TL-11623       MDL-53102: All outgoing email Message-IDs have been standardised to use the same format
    TL-11624       MDL-48838: Added request cache for grade categories
    TL-11628       MDL-51374: Improved the performance of the database layer when working with temptables

                   Prior to this change the use of temptables lead to the database meta
                   information cache being purged to ensure it was accurate after the temp
                   table changes.
                   The cache which contains information on all tables does not need to be
                   purged, instead in the case of temp tables simply ensuring the cache is
                   accurate to the tables in question is enough.
                   A new temp tables cache has been created and is used exclusively for temp
                   tables.

    TL-11629       MDL-53279: Grade categories are only regraded if they depend on the updated item
    TL-11631       MDL-52869: Allow in-place editability of Course page Activity names.
    TL-11645       MDL-53543: Improved the performance of the grade categories cache
    TL-11650       MDL-53315: Added support for IMAP namespaces in inbound messaging
    TL-11654       MDL-53260: The upgrade CLI script now has a --lang option
    TL-11673       MDL-53209: Feedback activities can now be added to the site frontpage and taken by authenticated users
    TL-11678       MDL-17955: New forum setting 'forum_enabletimedposts' that allows setting of display periods when posting a new
                              forum discussion
    TL-11682       MDL-52954: Improved the Assignment grading interfaces
    TL-11694       MDL-53577: Improved error message when uploaded a file that is too large
    TL-11697       MDL-53571: Converted xpath literal escaping to use behat_context_helper::escape

                   In upgrading to Behat 3 the way in which we escape xpath literal strings
                   changed.
                   We now have to use an escaping class, luckily for us one has been made
                   available and a static shortcut to escape has been created.
                   All strings being used in xpath should be escaped by call
                   behat_context_helper::escape()

    TL-11698       MDL-53440: Provided a save button returning user to course approval list
    TL-11702       MDL-53382: Moved view all link to below the paging bar when viewing course participants
    TL-11712       MDL-45064: Added Preconfigured LTI Tool option to Activity Chooser
    TL-11718       MDL-53309: Improved performance of grade aggregation
    TL-11723       MDL-52490: Added a new option to download selected submissions in assignment grading interface
    TL-11968       MDL-52596: Added a 'maxperpage' site wide setting for mod_assign grading table size
    TL-11971       MDL-48506: The memcached store is now more respectful of other uses of the memcached server
    TL-11974       MDL-51354: Added help pop-up to site log report

                   Help text was added to describe the level options when searching the site
                   logs. Also, the option previously called 'Educational level' is now listed
                   as 'All events'.

    TL-11975       MDL-51267: Improved clarity of the user interface for file and url activity creation
    TL-11981       MDL-34925: The bulk user download now uses the new dataformat plugins for export
    TL-11984       MDL-52781: Improved code to ensure user details are validated consistently
    TL-11996       MDL-52154: Improved LTI administration interface
    TL-11999       MDL-53738: The feedback module is now enabled by default
    TL-12000       MDL-53638: Major refactoring of the Feedback module

                   The feedback module has been refactored, making several significant backend
                   improvements.

                   * Conversion of forms to Moodle forms.
                   * Improved JS confirmation of actions.
                   * Improved the analysis pages.
                   * Cleaned up old and outmoded code.
                   * Improved RTL display of the module.
                   * Fixed several minor bugs.

    TL-12010       MDL-53973: Added activities names to spreadsheets (in XLS and ODS format) during export
    TL-12069       MDL-52253: Added a new default scale: Separate and Connected ways of knowing
    TL-12071       MDL-54128: Added LTI description as a help text to LTI activities in activity chooser
    TL-12073       MDL-54550: Added warning when LTI enrol module enabled without LTI authentication mode
    TL-12091       MDL-54061: Added encoding and separator to assignment offline grading upload form
    TL-12098       MDL-54632: Added option to show LTI tool in activity chooser or only as preconfigured external tool
    TL-12117       MDL-54702: Added icons to LTI activities
    TL-12133       MDL-54909: Set a different background colour from the editable page section in Assignment PDF annotation
    TL-12138       MDL-55027: Fixed access to the LTI provider if the consumer provides a broken image URL
    TL-12143       MDL-55049: Added antivirus scan to files uploaded via webservice
    TL-12174       MDL-55314: Added error message if XML for LTI is broken
    TL-12213       MDL-56369: Improved the detection of problems in client output when running behat
    TL-12217       MDL-56208: Discussion topic form group visibility setting moved out of 'Display period' section
    TL-12265       Improved accessibility when adding/editing custom fields
    TL-12276       Made learning enrolment/assignment instant for self-registered users

                   Self registered users are now added to audiences, courses, programs, and
                   certifications on confirmation.

    TL-12354       Added support for service endpoint calls to methods defined in plugins
    TL-12388       Added new User Last Access (Relative) and User Last Login (Relative) columns and filters to report builder.

                   This change adds two new columns and filters that are available to report
                   sources using user data. User Last Access and User Last Login are already
                   columns available in reports but the new columns use a natural language
                   relative date instead, so feature descriptions such as 'Within the last
                   hour', 'Today at 10:45' and '3 months ago'.

    TL-12390       Added new 'includenotset' option to date filters and applied to Last Login and First Access filters.

                   This change introduces a new option flag for report builder date filters
                   that allows a 'not set' checkbox to be added to a standard date filter.
                   This allows the user to include any records where the field date is blank /
                   not set in the report.

                   The 'not set' option has been turned on for the Last Login and First Access
                   column filters in this change.

    TL-12399       Added option to display border on blocks

                   Added an option to all blocks that allow the user to decide whether or not
                   to show the border on a block. This will also remove padding so the block
                   content is aligned with the outer edge of the block allowing blocks to
                   define their own outer border to avoid double borders. This is achieved by
                   a new column in the block_instances table

                   Added an option in the code for blocks to default to having a border or not
                   which can be overridden on a per-instance basis in the block configuration.

    TL-12513       MDL-52840: Changed default setting for assignsubmission_file max bytes to use site upload limit.
    TL-12533       MDL-56836: SCORM player display mode retained after relogin

                   If a user logged out from the site when a SCORM activity is open in a
                   pop-up window, then after login this popup will be reused instead of
                   opening an additional one.

    TL-12553       MDL-54846: Added support for WAV files and cleaned up audio media support.
    TL-12632       MDL-55581: HTML audio and video "track" tag are whitelisted in HTML purifier
    TL-12653       Removed HTML table in feedback 360 heading
    TL-12654       Removed superfluous label when searching Forum posts
    TL-12657       Associated a HTML label with scale value dropdown when viewing a single personal goal
    TL-12660       Added an accessible label to the add comment text field when JavaScript is turned off
    TL-12726       Added an accessible label when viewing the competencies tab of a users record of learning
    TL-12814       Added missing global $CFG to all autoloaded classes
    TL-12840       Improved admin tree API and performance
    TL-12865       The list of supported browsers was updated to match vendor support status
    TL-12900       Updated the Chat activity to only show absolute dates for next start time
    TL-12911       Added tag area for Audiences

                   Having a tag area for audiences allows the default tag collection to be
                   changed.

    TL-13005       MDL-54590: Added installation instructions for allowed characters in database name
    TL-13012       MDL-54865: Added user profile link to user name in gradebook user report
    TL-13018       MDL-54947: Improved binary data handling in PostgreSQL database driver
    TL-13021       MDL-48944: Added submission completion criteria to survey activity
    TL-13023       MDL-50758: Ensured all correct answers are shown in multi-choice question
    TL-13030       MDL-49029: Added mod/choice:view to allow visibility control over Choice course activities
    TL-13031       MDL-11369: Added Choice start and end events to the course calendar
    TL-13032       MDL-54891: Allowed admin to set defaults and lock settings for Activity results block

                   Admins should be aware that enabling a lock on a setting will only prevent
                   the setting on existing Activity results blocks from being changed, and
                   does not change the settings in those blocks to the default value.
                   Consequently, if a locked setting on an existing block contains a value
                   which is not the default, it cannot be changed to the default. Care should
                   be taken if the intention is to enforce privacy by restricting visibility,
                   such as if the "Privacy of results" setting is to be locked on "Anonymous
                   results".

    TL-13033       MDL-54671: Improved formatting of CSS in the atto editor and progress report
    TL-13035       MDL-53222: Improved UI of global search administration pages
    TL-13042       MDL-14448: Added the mod/lesson:view capability to allow visibility control over Lesson course activities
    TL-13043       MDL-31356: Implemented several small improvements in the IMS Enterprise enrol plugin

                   The following improvements have been made:
                   * Ability to update a course Full Name
                   * Ability to update a course Short Name
                   * Ability to create nested categories during course creation
                   * Ability to update a user record (all fields that the plugin already knew
                   about except username)
                   * Ability to set/update an authentication type for a user (during creation
                   and updates)

    TL-13046       MDL-55251: Added the mod/chat:view capability to allow visibility control over Chat course activities
    TL-13048       MDL-55200: Added the display of coordinates for Drag and Drop Markers question
    TL-13049       MDL-55158: Start and end dates for Database course activities are now shown in the calendar
    TL-13056       MDL-55254: Added the mod/data:view capability to allow visibility control over Data course activities
    TL-13090       MDL-55287: Fixed display when search engine is not enabled on global search admin pages
    TL-13091       MDL-55140: Improved Choice Activity to allow open and close dates to act separately
    TL-13100       MDL-53572: HTTP URL setting for the MathJax filter removed

                   The MathJax filter previously allowed both an HTTP and HTTPS setting for
                   its URL. The HTTP URL was accessed if the Totara site was run over HTTP.
                   This was unnecessary as MathJax can still be retrieved via HTTPS
                   regardless.

                   If the HTTP URL was left as it's default, you will not have to change
                   anything. If it was customised, you will be prompted to set the HTTPS URL
                   following upgrade, given that this will always be used now and may also
                   require a custom value.

    TL-13106       MDL-3782: Improved 'cloze' Embedded answers question type to allow for multiple answers
    TL-13109       MDL-55464: Added the mod/label:view capability to allow visibility control over Label course activities
    TL-13121       MDL-18592: The choice activity now allows teachers to create/change answers on the learner's behalf
    TL-13127       MDL-44712: Improved Multi-SCO completion handing in activity completion
    TL-13133       MDL-53634: Changed per-course forum digest options to use inplace_editable
    TL-13137       MDL-38105: Improved Rubric grading calculation method to allow negative score
    TL-13143       MDL-55236: Allowed assignment subplugins to back up configuration-related files
    TL-13145       MDL-52798: Moved calendar preferences to the user preferences page
    TL-13146       MDL-37669: Added user option to mark posts read, or not, when forum notifications are sent
    TL-13148       MDL-55415: Moved course menu permissions check to new API function
    TL-13151       MDL-55922: Improved static caching performance
    TL-13155       MDL-45752: Added new events when viewing and searching courses
    TL-13156       MDL-55466: Improved alternateloginurl setting to use moodle_url class to allow relative local addresses starting with /
    TL-13159       MDL-55866: Ensured that the editor enable state for database activities is remembered for each instance
    TL-13163       MDL-55916: Updated Maintenance Mode to use HTTP 503
    TL-13164       MDL-51361: Made default settings for course imports configurable
    TL-13171       MDL-55124: Added dbhandlesoptions parameter to not send database options

                   PostgreSQL connections now use advanced options to reduce connection
                   overhead.  These options are not compatible with some connection poolers.

    TL-13176       MDL-55327: Created a duplicate page option for use within the Lesson activity
    TL-13195       MDL-55474: Converted search form to use templates in block_search_forums
    TL-13200       MDL-55495: Made url_select a templatable
    TL-13218       MDL-55701: Converted help icon to a mustache template
    TL-13221       MDL-55594: Added templates for forum advanced search
    TL-13228       MDL-55831: Converted action menu to templates
    TL-13237       MDL-55593: Added aria attributes when dragging and dropping
    TL-13239       MDL-51948: Improved RTL support in admin settings
    TL-13244       MDL-45890: Added additional events for external blogs
    TL-13293       MDL-56270: Added additional chapter information to in-page navigation within the book activity
    TL-13303       MDL-22078: Added the ability to set a course end date
    TL-13305       MDL-45388: Warning shown in the footer if the site is operated with theme designer mode on
    TL-13311       MDL-55746: Allowed a theme to blacklist a set of tags
    TL-13361       MDL-56005: Themes can provide SCSS snippets for inclusion in the final CSS
    TL-13365       MDL-30179: Added the ability to view the grade report as another user
    TL-13372       MDL-54945: Workshop submissions can be exported as a portfolio
    TL-13385       MDL-56295: Deletion of book chapters now uses popup confirmation
    TL-13387       MDL-53752: Improved formatting of chapter numbers in the book activity
    TL-13392       MDL-48629: Changed hyphen separator to arrow for matching quiz answers
    TL-13397       MDL-56100: Added recent activity support to the folder module

                   The recent activity block now shows activity in the folder module.

    TL-13407       MDL-56082: Expose external authentication methods in login block
    TL-13430       MDL-54833: Workshop: Enhance accessibility of the userplan widget
    TL-13434       MDL-56395: Gradebook: Make long item titles more accessible
    TL-13439       MDL-55981: Removed the site:accessallgroups capability from teacher
    TL-13445       MDL-50888: Clam Antivirus can now be run via Unix socket
    TL-13456       MDL-56597: Added bootstrap classes to upcoming maintenance alert
    TL-13477       MDL-55799: inplace_editable: add form-control class to fields
    TL-13486       MDL-56149: Prevented risk icons from wrapping when setting permissions
    TL-13489       MDL-56297: Fixed size of URL field in external blog setup page
    TL-13523       MDL-29795: Added user/group overrides for mod/assign
    TL-13536       MDL-56846: Added bootstrap classes to survey module
    TL-13539       MDL-48498: cURL request addresses can be blacklisted via admin settings
    TL-13550       MDL-56766: Improved calendar export labels to be more descriptive of the fields
    TL-13558       MDL-56725: Improved styles for Database activity module
    TL-13566       MDL-56895: Improved format of portfolio buttons
    TL-13587       MDL-56193: Updated look and feel of enrol users dialog within a course
    TL-13621       MDL-56767: Added bootstrap classes to block_login
    TL-13677       MDL-55324: Videos uploaded using Atto editor now allow for multiple subtitle tracks
    TL-13711       MDL-57127: Increased memory allowance when generating CSS
    TL-13724       MDL-57232: Themes can now control which blocks are protected
    TL-13731       MDL-57171: Used Bootstrap classes for the labels in the Status column of the Server checks/Environment pages
    TL-13789       MDL-57415: Added bootstrap classes to buttons on participants page
    TL-13798       MDL-55915: Several improvements to fullname display when the user holds the viewfullnames capability
    TL-13800       MDL-46782: Start from the first uncompleted SCO when re-entering Multi-SCO SCORM
    TL-13805       MDL-57785: Disabled SCORM nav refresh when nav display is disabled
    TL-13817       MDL-56841: Fixed display of edit and download buttons inline for folder resource
    TL-13819       MDL-55867: Added sort to list of activities in the activity results block
    TL-13831       MDL-57354: Set continue button to render as a primary button
    TL-13837       MDL-57030: Added ability to auto re-run failed behat scenarios
    TL-13861       MDL-51833: Improved performance when checking permissions for event monitoring tool
    TL-13886       MDL-18599: Forum owner is not shown when forum type is single discussion
    TL-13917       Fixed visibility tests for custom fields in Report builder reports to match to logic on profile pages

                   This patch removes 'totara/core:viewhiddenusercustomfielddata' capability
                   and uses standard 'moodle/user:viewalldetails' in report builder for all
                   user custom profile fields.

    TL-14041       Email based authentication plugin is disabled in new installations
    TL-14059       "Assignment upgrade helper" administration page is now hidden

                   The "Assignment upgrade helper" tool was used to convert old assignment
                   activities, it can be still accessed directly via
                   https://yoursite.com/admin/tool/assignmentupgrade/index.php

    TL-14115       Flash animations are now disabled by default on new installs
    TL-14152       Appraisal snapshots created by DOMPDF now have a dedicated CSS stylesheet
    TL-14156       Errors in admin settings use the notification error template
    TL-14186       Added new fancy behat logging in behat dataroot

Bug fixes:

    TL-12613       Fixed plugin audience lock down
    TL-12671       Prevented 'Empty string behaviour in CSV' setting from being ignored in org/pos imports
    TL-12685       Fixed managing of reports that are not available to managers and admins
    TL-12737       Fixed duplicate ID HTML validation error when migrating databases
    TL-12740       Removed duplicate HTML id's when editing course groups
    TL-12969       Fixed problems with course form element validation
    TL-13967       Fixed styling of permissions added via AJAX
    TL-14016       Removed deprecated table parameters when viewing lesson report
    TL-14043       Prevented Atto autosave for not-logged-in users
    TL-14067       Fixed the Message text box auto size adjustment
    TL-14070       Fixed sending a message after another message failed to send
    TL-14078       Added fitem prefix to JS selector for validation handler for required date selectors
    TL-14215       Stopped the add image button in atto trying to load the current page
    TL-14248       Fixed broken MSSQL temp table dropping

API changes:

    TL-10328       Change the behaviour of the timestarted field for programs and certifications

                   Previously the prog_completion.timestarted column was being set when a user
                   was assigned to a program or certification, now it is set on the users
                   first action in the program. There is also a new
                   prog_completion.timecreated column which maintains the old data.

    TL-11455       MDL-52108: Created a new web service 'core_message_delete_message'
    TL-11456       MDL-51830: Added a course section deletion event
    TL-11458       MDL-52074: Added enrol_self_get_instance_info web service function to the mobile service
    TL-11459       MDL-51925: Added new option to get_enrolled_users web service to sort results by different fields
    TL-11460       MDL-52237: Plugins can now extend the user navigation section of the navigation block

                   A new callback has been added that allows any plugin to extend the user
                   section of the navigation blocks.
                   Within code simply define a function called
                   <pluginname>_extend_navigation_user() within your lib.php file.

    TL-11469       MDL-50269: The notify() function has been deprecated and now throws a debugging notice
    TL-11470       MDL-51700: Ajax web service call results are now validated

                   For ajax scripts that need to return dynamic structures developers may use
                   NULL as the return description to side-step the validation. This is not
                   recommended for normal web services because some WS protocols need to
                   know the exact return type and structure.

    TL-11472       MDL-52399: Added core_notes_delete_notes function to the mobile service
    TL-11474       MDL-50550: Added new web service mod_glossary_get_glossaries_by_courses
    TL-11475       MDL-52209: The XML-RPC web service protocol no longer uses Zend
    TL-11476       MDL-52165: Created a new web service 'mod_forum_can_add_discussion' to the Forum module
    TL-11477       MDL-50540: Added a webservice to glossaries to to return all glossaries within the given courses
    TL-11480       MDL-50428: New web service API for mod_scorm_launch_sco
    TL-11481       MDL-52073: Created new web service to return guest enrolment settings
    TL-11484       MDL-51886: Created new web services mod_wiki_view_wiki and mod_wiki_view_page for the Wiki module
    TL-11485       MDL-52556: Updated SCORM get_scorm_scoes function to also return SCO additional data
    TL-11487       MDL-52586: Added the "defaulthomepage" setting to the get_site_info web service.
    TL-11488       MDL-49231: Added a number of new web service functions for mod_glossary

                   The following new web service function have been added:
                       mod_glossary_get_glossaries_by_courses
                       mod_glossary_view_glossary
                       mod_glossary_view_entry
                       mod_glossary_get_entries_by_letter
                       mod_glossary_get_entries_by_date
                       mod_glossary_get_categories
                       mod_glossary_get_entries_by_category
                       mod_glossary_get_authors
                       mod_glossary_get_entries_by_author
                       mod_glossary_get_entries_by_author_id
                       mod_glossary_get_entries_by_search
                       mod_glossary_get_entries_by_term
                       mod_glossary_get_entries_to_approve
                       mod_glossary_get_entry_by_id

    TL-11489       MDL-48985: Removed an obsolete file mod/lesson/reformat.php
    TL-11491       MDL-52210: Removed the ZMF web service handler
    TL-11516       MDL-50268: The get_file_url() function has been deprecated please call moodle_url::make_file_url() instead
    TL-11517       MDL-49291: All core\log\sql_*_reader interfaces and classes have been removed
    TL-11523       MDL-52826: mform validation was moved out of global JS scope
    TL-11532       MDL-52715: Introduction of a new fragments API

                   This change introduces a new fragments API allowing for snippets of HTML
                   and JS to be requested from the server and utilised in JS.
                   Please note the preferred means of generating client site content is still
                   via templates and AMD modules. The fragments API should only ever be used
                   as a last resort.

    TL-11563       MDL-45104: Writing to the legacy log store has been deprecated

                   The legacy log store is in its final lifespan and writing to it has been
                   deprecated.
                   If you are still using the legacy log store we strongly recommend moving
                   away from it as it will be removed in the next major release.

    TL-11570       MDL-53179: Made folder action buttons structure consistent
    TL-11577       MDL-52809: Created new web services for the wiki module to get subwikis
    TL-11578       MDL-50546: Added new web service mod_quiz_get_quizzes_by_courses

                   Web service that lists all quizzes within a course. Hidden activities and
                   some sensitive settings such as password are only available to users with
                   sufficient capabilities.

    TL-11579       MDL-52669: Added new web service mod_quiz_view_quiz

                   Web service allowing a quiz to be marked as complete for the user who is
                   making the request.

    TL-11584       MDL-30811: Integrated new class-based notifications API

                   - Notifications are now output above the main content container by
                   core_renderer not at the top of it by totara_core renderer.
                   - CSS class names used to define notification type when passed to
                   totara_set_notification() e.g. 'notifysuccess' are now stripped

    TL-11595       MDL-52670: Added new web service mod_quiz_get_user_attempts

                   Web service that returns data about attempts made to a given quiz for a
                   given user or all users.

    TL-11596       MDL-52785: Created web services to get users best quiz grade
    TL-11601       MDL-52786: Added new web service mod_quiz_get_combined_review_options

                   Web service that allows a quizzes review options to be viewed.

    TL-11605       MDL-51324: Added a new "course" form element to Moodleforms
    TL-11607       MDL-53314: Added a debugimap configuration option to allow debugging of incoming mail processing

                   For more information on this new setting please refer to config-dist.php

    TL-11611       MDL-46891: Migrated to Behat 3

                   The behat library used for acceptance testing within Totara has been
                   upgraded from 2.5 to 3.
                   This is largely backwards compatible with the exception of the following
                   topics for which you will need to review any custom or third party behat
                   context code.
                   * Returning arrays of Given classes is no longer supported in definitions,
                   please refactor these definitions to use $this->execute() instead.
                   * The API for the TableNode class has changed, notably addRow() has been
                   removed.
                   * Named selectors have been deprecated, if you get debugging notices you
                   will need to change these calls to use either exact or partial selectors
                   instead.

    TL-11613       MDL-52788: New quiz attempts can be started via web services
    TL-11614       MDL-52813: New Web Service mod_quiz_get_attempt_data
    TL-11615       MDL-52830: Added new web service mod_quiz_get_attempt_summary

                   Web service that allows a quiz attempt data to be viewed.

    TL-11617       MDL-51887: Added web service functions to collaborative and individual Wikis.
    TL-11618       MDL-51986: Created web services for wiki get page contents
    TL-11619       MDL-52852: Added new web service mod_quiz_save_attempt

                   This web service enables quiz questions to be attempted.

    TL-11626       MDL-52934: Box.net v1 migration scripts have been removed

                   The box.net version 1 to version 2 migration scripts have been removed from
                   Totara.
                   Support for version 1 was removed several years ago and these scripts have
                   existed for ample time.

    TL-11627       MDL-49934: Added new optional parameter to external function mod_assign_external::get_assignments

                   New Optional parameter 'includenotenrolledcourses' when set to true
                   (default false) will return assignments for courses a user has access to
                   even if they are not enrolled.

    TL-11633       MDL-51867: Allow any plugin to identify a scale as being used

                   The plugin in needs to implement a function
                   <plugin>_scale_used_anywhere($scaleid); for this improvement to work.

    TL-11637       MDL-52868: Added new web service mod_quiz_get_attempt_review

                   This web service allows quiz attempt data to be viewed.

    TL-11641       MDL-52619: Updated the ADODB library to version 5.20.3
    TL-11643       MDL-53458: Updated Mustache JavaScript from 2.1.3 to 2.2.1
    TL-11644       MDL-53465: Upgraded the PHPMailer library to version 5.2.14
    TL-11646       MDL-52888: Added new web services for triggering events in mod_quiz
    TL-11647       MDL-53034: Created web services for quiz feedback to grade
    TL-11652       MDL-52207: The Zend framework has been removed from Totara

                   Previously several modules from the Zend Framework were included in Totara.
                   These have all now been removed.

    TL-11655       MDL-53393: Upgraded the HTML2Text library to version 4.0.1
    TL-11656       MDL-53456: Upgraded the RequireJS library to version 2.1.22
    TL-11657       MDL-53513: Upgrade lessphp to version 1.7.0.10
    TL-11658       MDL-53518: Upgraded the Markdown library to version 1.6.0
    TL-11659       MDL-53519: Updated the CAS library to version 1.3.4
    TL-11660       MDL-53455: Upgraded jQuery Migrate to version 1.4.0
    TL-11664       MDL-53512: Upgraded the Google APIs Client Library to version 1.1.7
    TL-11665       MDL-53181: Added data attribute identifiers to user menu items
    TL-11667       MDL-52767: Added new web service for checking access requirements to quizzes and attempts
    TL-11676       MDL-53467: Updated the S3 repository to use version 0.5.1 of the S3 library
    TL-11684       MDL-53462: Created new web services for site and course badges
    TL-11687       MDL-53104: The moodle/blog:associatemodule and moodle/blog:associatecourse capabilities have been removed

                   These capabilities were previously deprecated and have been unused for a
                   very long time. Their definitions and descriptive strings have now been
                   removed.

    TL-11710       MDL-53703: Added new webservice mod_wiki_get_subwiki_files

                   New web service that allows access to files embedded into wiki pages.

    TL-11995       MDL-53791: Created new web services that enable the editing of wiki pages
    TL-12001       MDL-49414: Removed deprecated web services functions.
    TL-12025       MDL-54032: Stopped plugin external services being defined as core services by default

                   Previously web services defined by plugins were being added as a core
                   service. Now they will only be added as a core service if it is
                   specifically set to using 'services' =>
                   array(MOODLE_OFFICIAL_MOBILE_SERVICE).

    TL-12197       MDL-55910: New dashboard events

                   These are the new events for tracking activity on My pages:
                   * dashboard_viewed
                   * dashboard_reset
                   * dashboards_reset

    TL-12512       MDL-35949: Ensured quiz maximum grade field is compatible with assistive technologies

                   The quiz maximum grade field label explicitly declares and no longer wraps
                   its input

    TL-12823       $CFG->admin is now hardcoded to 'admin' and cannot be modified
    TL-13000       MDL-54800: Updated the core_enrol_get_users_courses web services function to also return the course category
    TL-13007       MDL-54955: Applied external_format_string to course full and short names within web services
    TL-13015       MDL-54104: Added section number to the return of the web services call on core_course_get_content
    TL-13019       MDL-54889: JSHint is no longer used when building JS, we have switched to ESLint
    TL-13022       MDL-54980: Added mod_assign_list_participants to the list of mobile service
    TL-13024       MDL-54943: Added new web service core_course_get_activities_overview
    TL-13025       MDL-55000: Converted edit/tree/functions.js to AMD module grades/edittree_index
    TL-13039       MDL-55162: Added new web service mod_assign_view_assign
    TL-13040       MDL-54801: Added support for multiple ids search web services get_categories
    TL-13088       MDL-54987: Introduce a new chart API and library
    TL-13095       MDL-44369: Added additional events for calendar subscriptions
    TL-13096       MDL-45734: Added additional events for course badges
    TL-13101       MDL-55167: Grunt now lints CSS
    TL-13105       MDL-54941: Better support for file areas in WS functions
    TL-13108       MDL-55061: Added logging events to grade export
    TL-13117       MDL-55239: Added additional events for course badges
    TL-13135       MDL-55372: CSS lint has been removed, it was replaced by stylelint
    TL-13258       MDL-55100: Add web service function get_courses_by_field
    TL-13262       MDL-56172: The CSS optimiser has been removed and is no longer usable by themes
    TL-13264       MDL-55168: Grunt now uses stylelint to check CSS style
    TL-13271       MDL-56009: The RequireJS library has been upgraded to v2.3.2
    TL-13276       MDL-55740: Assignment grade submission and participant info added to experimental mobile service functions
    TL-13277       MDL-55786: Added add_rating web service functions
    TL-13279       MDL-56001: The SimplePie library has been upgraded to v1.4.2
    TL-13282       MDL-56010: Upgraded loglevel.js to 1.4.1 from 1.4.0
    TL-13289       MDL-56011: Upgraded PHP mustache implementation from 2.9.0 to 2.11.1
    TL-13292       MDL-55999: The AdoDB library has been upgraded to v5.20.7
    TL-13309       MDL-56248: Upgraded PHPUnit to 5.5.x
    TL-13364       MDL-53695: Switched the minify library used to shrink CSS and JS

                   The previously used minify library was no longer support.
                   We have now switched over to the MatthiasMullie\Minify library available at
                   https://github.com/matthiasmullie/minify

    TL-13394       MDL-55087: The HTML Purifier library has been upgrade to v4.8.0
    TL-13442       MDL-56017: Updated the MathJax filter to use version 2.7 by default

                   Sites using the previous default of version 2.6-latest will be updated to
                   2.7 automatically during upgrade.

    TL-13443       MDL-46942: Added original course id to course_restored event
    TL-13453       MDL-56334: New IP/ domain validation library for core
    TL-13691       MDL-56586: Themes can now take control of how the "Add block" is displayed
    TL-13941       function resize_image() was renamed to totara_resize_image()

                   This affects 3rd party plugins only, this method was not used in standard
                   Totara distribution.

    TL-14151       Imported latest dompdf 0.8.0 for use it in appraisals
    TL-14202       Deprecated unused faulty function facetoface_eventhandler_role_unassigned_bulk

Miscellaneous Moodle fixes:

    TL-11451       MDL-51664: Improved external_util::validate_courses to prevent double course fetching
    TL-11490       MDL-50916: Fixed information on lesson complete report
    TL-11494       MDL-52491: Prevented XMLRPC server and capabilities being enabled when the Mobile service is enabled
    TL-11510       MDL-52270: Fixed showing blind identities to users with mod/assign:viewblinddetails capability

                   When a trainer has the mod/assign:viewblinddetails capability, both
                   the participant identifier and actual user details are visible when
                   viewing assignments with blind marking turned on.

    TL-11530       MDL-42395: Fixed display of previous assignment submission attempts
    TL-11548       MDL-33663: Improved the error message when negative grades are entered whilst using a marking guide
    TL-11558       MDL-50484: Fixed duplicate ids on pages
    TL-11566       MDL-52397: Fixed assignment feedback change notification
    TL-11603       MDL-52718: Fixed an error in the course Community Finder block to ensure the correct sending of XML-RPC request
    TL-11649       MDL-53207: Display stock avatar instead of own user picture for messages from fake users.
    TL-11670       MDL-45835: Ensured addition of groups to groupings is recorded in course logs.
    TL-11674       MDL-53557: Fixed parsing of numeric bounds
    TL-11679       MDL-53633: Prevents mis-ordering of inline edited options for Forums.
    TL-11681       MDL-53056: Replacing HTML elements now cleans up YUI events

                   Previously, when the templates JavaScript library replaced a DOM element in
                   HTML it did not clean up YUI events. This change causes it to clean up
                   those events

    TL-11724       MDL-47672: Ensured user identity fields are aligned in enrolled users table
    TL-11983       MDL-53864: Fixed computation of averages on MySQL in feedback module
    TL-12009       MDL-53967: Ensure that the getAllKeys method in the Cache API always returns a valid array
    TL-12013       MDL-53994: Fixed a missing include in the Feedback block
    TL-12017       MDL-54006: Removed warnings when importing valid csv via upload users
    TL-12020       MDL-54000: Prevented exception when grading assignments with inline comments
    TL-12024       MDL-54026: Fixed the type specified for some web service functions

                   Some web services functions were specifying an invalid type 'delete'
                   instead of 'write'.

    TL-12027       MDL-54056: Prevented add frequently used comment button appearing when none are available
    TL-12036       MDL-41640: Removed incorrect response time being displayed for anonymous feedback
    TL-12043       MDL-53914: Fixed debug messages when global search is indexing wikis
    TL-12048       MDL-54098: Fixed up uses of require_login() and PAGE->set_context within external functions
    TL-12057       MDL-53293: Dragdrop listeners now destroyed as part of component lifecycle to avoid incrementally duplicated listeners.
    TL-12059       MDL-54121: Correctly applied lockscroll to dialogues
    TL-12089       MDL-53896: Fixed issue in Quiz module when mbstring PHP extension is not enabled
    TL-12105       MDL-54666: Fixed the module grading form from losing data if it was deemed invalid
    TL-12108       MDL-54661: Fixed double escaping of course name in the assignment grading page
    TL-12114       MDL-54756: Fixed the moodle_url::make_file_url regression that made STACK question type fail
    TL-12123       MDL-54859: Prevented debugging messages when creating new page in wiki
    TL-12139       MDL-55028: Fixed incorrect Content-Length header in SOAP WSDL request response

                   Before this patch the Content-Length header would always be set to 1. This
                   fixes the calculation and now uses the correct content length.

    TL-12140       MDL-54991: Fixed invalid response for 'submissiongroup' in Assignment activity
    TL-12142       MDL-54868: Change of encoding behaviour of non-ASCII, UTF-8 encoded characters in XMLRPC web services
    TL-12149       MDL-54795: Fixed JS errors being generated by the Atto auto save feature within the Assignment activity grading interface
    TL-12165       MDL-55245: Fixed ability to edit text in a comment within the assignment module grading interface
    TL-12166       MDL-55225: Corrected behaviour of get_plugins_data to ensure html text is correctly formatted (filtered) via external_format_text.
    TL-12169       MDL-55289: Fixed images and attachments in workshop example submissions
    TL-12170       MDL-55348: Changed Wiki activity section identifiers to be defined as PARAM_RAW.
    TL-12171       MDL-55374: Ensured UTF-8 encoding is used within the Assignment activity 'editpdf' grading.
    TL-12173       MDL-55322: Fixed dragging comments on mobile site in new grading interface in assignment module
    TL-12176       MDL-55246: Fixed an issue submitting files with spaces in names within assignment module
    TL-12182       MDL-55520: Prevented grademax from reverting to 100 when editing activities with grades
    TL-12187       MDL-55385: Prevented PHP warnings when output_buffering ini setting is a string
    TL-12188       MDL-55668: Removed link to user profile when blind marking is enabled in assignment module
    TL-12189       MDL-55717: Removed leave confirmation for inline edit elements

                   No confirmation will be asked when leaving page with inline edit form
                   elements.

    TL-12192       MDL-55707: Prevented infinite loops when regrading
    TL-12193       MDL-54793: Updated webservices xmlrpc to use GET and POST correctly
    TL-12194       MDL-55832: Added filters to multichoice feedback activity questions
    TL-12198       MDL-55873: Change back username fields to use PARAM_RAW
    TL-12201       MDL-55519: Fixed maximum grade being reset when unlocking activity completion criteria
    TL-12206       MDL-55222: Added external_format_string to course names.
    TL-12215       MDL-55630: Excluded users courses from user details in assignments.
    TL-12224       MDL-55930: Prevented 'previous page' button being displayed in sequential mode
    TL-12228       MDL-54852: Fixed leave page warning in assignment grading when changes have been save

                   There is no longer a warning shown about leaving the page when the user has
                   saved their grading feedback.

    TL-12232       MDL-56363: Fixed grading restrictions for groups in assignment module

                   When users are separated into groups and group separation is set in the
                   assignment then in the grading interface the trainers can only access
                   learners in their group (this can be changed via capabilities).

    TL-12480       MDL-55720: Fixed potential PHP error caused by a module's _add_instance function
    TL-12484       MDL-56823: The redis session handler now correctly respects the session timeout setting
    TL-12488       MDL-56831: Fixed unsafe use of YUI module in module:mod_quiz/preflightcheck
    TL-12489       MDL-56899: Prevented DOM parsing warnings being output as errors
    TL-12491       MDL-56942: Fixed PHP error when requiring a self registered user to change their password on first login
    TL-12497       MDL-48055: Added checks for grade visibility in 'Outline' and 'Complete' reports
    TL-12498       MDL-55362: Prevented empty H2 title element on Site Home page when no title/title with empty spaces is used.
    TL-12501       MDL-33960: Fixed page scrolling when viewing LTI in the External Tool module
    TL-12505       MDL-56865: Prevented the Behat error handler from handling exceptions for the Behat utility scripts
    TL-12507       MDL-52186: Stopped processing unenrolments for suspended meta-course enrolments
    TL-12510       MDL-56972: Ensured question categories restored from backups are given a unique stamp
    TL-12511       MDL-57002: Enabled use of special characters in WebDAV download.
    TL-12514       MDL-53964: Made '0' an allowed label name in the 'Drag and Drop Markers' question type
    TL-12517       MDL-52199: Incoming email pickup will now fail rather than stopping quietly

                   This task will now throw an exception when the configuration is incorrectly
                   configured, resulting in the task being marked as failed in the scheduler,
                   rather than being marked as succeeding.

    TL-12519       MDL-56182: Fixed the URL comparison of LTI tool URL's to ensure the correct tool is found
    TL-12522       MDL-56346: Fixed typo in property name used in EditPDF
    TL-12524       MDL-54921: Fixed destination url being lost during self registration
    TL-12527       MDL-57169: Ensured images in course category descriptions are displayed correctly when resorting a course into a category
    TL-12528       MDL-57199: Removed duplicate ID attributes from Quiz Preview fieldsets.
    TL-12529       MDL-56893: Fixed editing grade item when 'Show minimum grade' is disabled and item has grades
    TL-12530       MDL-57209: Fixed undefined index notice when editing a user's profile with no options set
    TL-12532       MDL-56233: Fixed form identifier when mocking a form
    TL-12534       MDL-56759: Improved display of course badges listing table
    TL-12535       MDL-45873: Fixed Database activity to allow "0" entries in the menu field to be recognised as valid
    TL-12539       MDL-56830: Ensured changes to block plugins visibility are recorded to the config log
    TL-12542       MDL-55782: Prevented long names breaking layout of group members form
    TL-12543       MDL-55906: Fixed resetting of filters on assignment module grading page

                   When clearing filters on the assignment module grading page, then
                   navigating away and returning, the cleared filters are now remembered..

    TL-12544       MDL-55809: Fixed preservation of author and license for images attached to glossary items across import/export

                   If the author and license of an image are specified they will now be
                   preserved across import and export actions.

    TL-12545       MDL-56566: Ensured the 'doanything' argument is set in the course overview block so site admin is handled
                              correctly within mod_assign notification
    TL-12547       MDL-56525: Fixed forum posts link on user profile incorrectly showing 'no forum posts'

                   A users forum posts are still shown if the user has been unenrolled from
                   the course which the forum is part of.

    TL-12549       MDL-57074: Improved RTL in the file manager and folder activities
    TL-12551       MDL-57250: Provided admin settings when only one category exists
    TL-12554       MDL-46714: Properly order date & time fields in RTL
    TL-12555       MDL-56810: Fixed Assignment submission conversion problem when learner is unenrolled.
    TL-12556       MDL-57182: Fixed invalid login attempt not displaying correctly in the page footer

                   When using $CFG->displayloginfailures = true in the config.php file the
                   number of invalid login attempts should be displayed in the header and
                   footer. This fixes an issue where the message was missing from the footer.

    TL-12558       MDL-57257: Added validation for numerical input values in the Lesson module
    TL-12559       MDL-57125: Prevented JS error in comment report
    TL-12560       MDL-55062: Ensured 'Upload Users' admin tool does not incorrectly update authentication method when not included
                              in the CSV upload file
    TL-12561       MDL-56912: Fixed non-required question types not submitting if empty in Feedback activity
    TL-12562       MDL-55575: Fixed show all displaying only the first page of glossary items
    TL-12563       MDL-55568: Duplicated chat sessions set to 'Do not publish' are not displayed in upcoming events
    TL-12565       MDL-55715: Separated subscriptions on calendars for different contexts

                   Subscriptions for the same calendar from different contexts (e.g. two
                   different courses) are maintained as separate subscriptions.

    TL-12566       MDL-57402: Fixes error when inserting a section heading after removing a page break.
    TL-12567       MDL-53044: Prevent users from using 'Cancel' to bypass password expiry
    TL-12568       MDL-52098: Fixed audience sync course enrolment method ignoring status
    TL-12573       MDL-57080: Fixed expected completion dates not updating when resetting courses

                   The course completion criteria 'Date' and activity completion criteria
                   'Expected completed on' will be shifted by the offset between the previous
                   and new 'Course start date' when resetting a course.

    TL-12575       MDL-50643: Fixed redirection bug when external SCORM window closes

                   Previously when an external SCORM window was closed, it redirected the
                   Totara main page to the SCORM provider's main page. This has been fixed to
                   redirect to the Totara course page instead.

    TL-12576       MDL-55955: Ensured override events in quizzes are not duplicated
    TL-12580       MDL-49557: Fixed AICC prerequisite handling
    TL-12582       MDL-47198: Fixed intermittent PHP warnings during formatting of header in cURL requests
    TL-12630       MDL-46654: Prevented debug output when user cannot subscribe to forum digests
    TL-12631       MDL-55628: Updated completion cache to use simpledata

                   The completion cache is currently not marked as simpledata. On the course
                   page it is frequently retrieved hundreds of times which results in many
                   calls to the slow unserialise function. By making a slight change to the
                   data format (using arrays instead of objects) we can mark it as simpledata,
                   which will avoid using unserialise.

    TL-12988       MDL-54563: Prevented course completion blocks from being added to non-course pages
    TL-12996       MDL-45762: Fixed error when accessing a conditionally hidden section
    TL-13003       MDL-54654: Fixed invalid styling in some course activities
    TL-13014       MDL-54855: Added missing preventsubmissionnotingroup field in mod_assign_get_assignments
    TL-13113       MDL-55196: Ensured database activity data is pre-processed correctly on import
    TL-13116       MDL-55533: Fixed feedback on 'cloze' Embedded answers question type with only one correct answer
    TL-13140       MDL-53724: Improved padding with dock enabled in bootstrapbase theme stack
    TL-13142       MDL-55288: Fixed behat i_delete_file_from_filemanager to work with file manager in settings.php
    TL-13150       MDL-55122: Removed a duplicated updown variable in enrollib
    TL-13302       MDL-56293: Ensured that the book activity table of contents block is shown on all editing pages
    TL-13326       MDL-55582: Added no results message when there are no results in search on user's message page
    TL-13344       MDL-55583: Prevented enter reloading the page in search when searching a user's messages

                   On the users messages page hitting enter in the search box caused the page
                   to be reloaded. This behavior is now prevented providing a better user
                   experience.

    TL-13368       MDL-56274: Fixed contact tab being incorrectly disabled when all message were deleted on users messaging page
    TL-13370       MDL-56057: Fixed manage global search page incorrectly reporting search areas as being disabled
    TL-13371       MDL-56324: Fixed the next/previous page navigation buttons in editpdf of the Assignment module
    TL-13415       MDL-56444: Fixed failure in logstore_standard_store_testcase::test_events_traversable
    TL-13429       MDL-56538: Fixed pop-out windows of new assignment with "Marking guide" grading method
    TL-13500       MDL-56670: Added padding class to end of lesson activity links to ensure they are displayed correctly
    TL-13501       MDL-56699: Fixed error when clicking on activities in the navigation block
    TL-13502       MDL-56273: Fixed issue where cache purge_all is causing tests to fail
    TL-13511       MDL-56654: Fixed missing RSS link in the blog page
    TL-13514       MDL-56576: Fixed 'requiremodintro' admin setting not saving correctly
    TL-13555       MDL-56855: Removed invalid formats from video JS plugin default
    TL-13559       MDL-56888: Fixed workshop Leap2A portfolio package not fully importing into Mahara
    TL-13569       MDL-56921: Alignment of images is not switched when using Atto editor with a right-to-left language
    TL-13584       MDL-55848: Removed a problematic class_exists check in the Assignment feedback module
    TL-13618       MDL-56870: Fixed viewing deleted activity modules when the course recycle bin is enabled
    TL-13631       MDL-57051: Fixed check_module_updates to not return unchanged files by ignoring folders
    TL-13646       MDL-56986: Removed unnecessary duplicate edit icon for course summary block
    TL-13660       MDL-57093: Fixed styling problems with forum notifications
    TL-13695       MDL-57176: Prevented duplicate blocks being added when required by the theme
    TL-13698       MDL-57174: Ensured edit settings link is displayed for Lesson activities when required.
    TL-13702       MDL-56829: Fixed display of embedded video player in iOS
    TL-13721       MDL-57101: Fixed embedded YouTube videos in AJAX interface
    TL-13730       MDL-56778: Improved RTL when enrolling users
    TL-13752       MDL-57474: Fixed invalid Message-ID header in forum post notifications
    TL-13760       MDL-57532: Added missing bootstrap classes to manage calendar subscription buttons
    TL-13766       MDL-40132: Fixed a fatal error within forms when using a comparison rule
    TL-13768       MDL-57601: Fixed an error in the grade report for ungraded quiz activities without the view hidden permission
    TL-13773       MDL-56271: Fixed recaptcha when used with anonymous feedback
    TL-13775       MDL-57608: Fixed videojs include to be lazy loaded on demand
    TL-13779       MDL-53991: Ensured that deleting an imported course calendar event, deletes only the required events for the current course
    TL-13780       MDL-57374: Pasting text into atto editor no longer causes other formatting to be removed
    TL-13786       MDL-57677: Fixed last forum post user being set to editor rather than post author
    TL-13787       MDL-45821: Fixed checking lesson_status for multi-sco SCORM activities
    TL-13788       MDL-57604: Fixed incorrect user being selected from chooser in Assignment activity
    TL-13792       MDL-57639: Fixed forum_tp_mark_post_read function call to use correct variable name
    TL-13801       MDL-57587: Fixed a bug where feedback images when reviewing a quiz attempt were not showing
    TL-13802       MDL-57660: Fixed selected forum option not being preserved when using forum search
    TL-13807       MDL-36233: Fixed bug where course overview block inconsistently lists "Submissions not graded" link for assignments
    TL-13808       MDL-57296: Fixed errors when collapsing grade categories

                   This fixes errors caused when collapsing grade categories in the grader
                   report as a user without 'moodle/grade:viewhidden' capability.

    TL-13809       MDL-57588: Fixed quiz so grading maintains question flags
    TL-13811       MDL-35978: Updated expandable comments to announce change when toggled
    TL-13833       MDL-50729: Fixed incorrect regrade event being assigned to the wrong user

                   The regrade event is now assigned to the system user.

    TL-13873       MDL-58040: Removed incorrect use of global $PAGE in blocklib.php


Release Evergreen (27th February 2017):
=======================================


Security issues:

    TL-6810        Added sesskey checks to the programs complete course code

Improvements:

    TL-4804        Added additonal default columns to the Seminar Sessions report source

                   The new default columns are 'Event capacity', 'Number of attendees
                   (including waiting approval, approved, and wait-listed)', and 'Places
                   available'

    TL-6011        Added new 'timecreated' and 'timemodified' columns and filters to the Record of Learning Evidence report source
    TL-5604        Added new 'completion date' filter to the Record of Learning Program report source
    TL-6118        Added new 'submission status' column and filter to the Assignment submissions report source
    TL-6210        Added new 'Goal type' column and filter to the Goal Summary report source
    TL-6335        Added new 'time created', 'time updated', and 'updated by'  columns and filters to Seminar report sources
    TL-7049        Added new 'is user assigned' column and filter to program and certificaiton completion report sources
    TL-8126        Added new 'Member count' columns to the Position and Organisation report sources
    TL-9759        Added new 'user status' column to the Seminar Events & Sessions report sources
    TL-11187       Added new content restriction to Seminar report sources that allows content to be restricted based on Seminar session roles
    TL-12416       Added badge description column and filter to the Badges issued report source
    TL-12447       Added UTC 10AM date field
    TL-11277       Multiselect custom fields no longer have a HTML fieldset per option
    TL-11291       Replaced the input button with text when editing a users messaging preferences
    TL-11317       Added labels to the add rule dropdown when editing the rules of a dynamic audience
    TL-11318       Added accessibility labels to Hierarchy framework searches and bulk actions
    TL-12314       Improved HTML validation when searching within a Hierarchy framework
    TL-12594       Added default html clean up to the static_html form element

                   Developers need to use
                   \totara_form\form\element\static_html::set_allow_xss(true) if they want to
                   include JavaScript code in static HTML forms element.

Bug fixes:

    TL-9982        Improved CSS in Learning plan comments for Roots and Basis themes
    TL-8375        Fixed issues with audiences in the table for restricting access to a menu item

                   Added the correct module to the url when rendering the table rows through
                   ajax. Also, when the form is saved, if "Restrict access by audience" is not
                   checked then it will remove all audience restrictions from the database so
                   they will not be incorrectly loaded later.

    TL-9264        Fixed a fatal error encountered in the Audience dialog for Program assignments
    TL-10082       Fixed the display of description images in the 360° Feedback request selection list
    TL-10871       Fixed duplicated error message displayed when creating Seminar sessions with multiple dates
    TL-11062       Seminar events that are in progress are now shown under the upcoming sessions tab

                   Previously events that were in progress were being shown under the previous
                   events tab. This lead to them being easily lost, and after a UX review it
                   was decided that this was indeed the wrong place to put them and they were
                   moved back to the upcoming events until the event has been completed.

                   In the course view page, if "sign-up for multiple events" is disabled, then
                   users who are signed-up will see only the event where they are signed-up to
                   as they won't be able to sign-up for another event within that Seminar. If
                   "sign-up for multiple events" is enabled, then the signed-up users will see
                   all upcoming events ("in progress" and "upcoming" ones).

    TL-11106       Fixed row duplication of Seminar events within the Seminar events report source
    TL-11186       Changed user completion icons into font icons
    TL-11230       Fixed disabled program course enrolments being re-enabled on cron

                   The clean_enrolment_plugins_task scheduled task now suspends and re-enables
                   user enrolments properly

    TL-12252       Disabled selection dialogs for Hierarchy report filters when the filter is set to "is any value"
    TL-12286       Corrected the table class used in Course administration > Competencies
    TL-12298       Fixed RTL CSS flipping in Appraisals

                   Previously there were a number of anomalies when viewing appraisals in
                   right to left languages such as Hebrew. This fixes the CSS so that they are
                   now displayed correctly.

    TL-12341       Removed unnecessary code to prevent page jump on click of action menu

                   Removed a forced jQuery repaint of the action menu which was originally
                   required to work around a Chrome display bug, but which is no longer
                   required.

    TL-12342       Moved the block hide icon to the right in Roots and Basis themes
    TL-12443       Fixed RTL CSS flipping in 360° Feedback

                   Previously there were a number of anomalies when viewing 360° feedback in
                   right to left languages such as Hebrew. This issue alters CSS so that they
                   are now displayed correctly.

    TL-12445       Fixed completion recording for some SCORMs with deep navigation structure (3+ levels)
    TL-12455       Backport TL-11198 - Added support for add-on report builder sources in column tests

                   Add-on developers may now add phpunit_column_test_add_data() and
                   phpunit_column_test_expected_count() methods to their report sources to
                   pass the full phpunit test suit with add-ons installed.

    TL-12458       Fixed the visibility permissions for images in the event details field
    TL-12463       Prevented the submission of text longer than 255 characters on Appraisal and 360° Feedback short text questions
    TL-12464       Fixed a HTML validation issue on the user/preferences.php page
    TL-12465       Fixed the display of multi-lang custom field names on the edit program and certification forms
    TL-12585       Fixed a fatal error when trying to configure the Stats block without having staff
    TL-12593       Fixed double escaping in the select and multiselect forms elements
    TL-12596       Reverted change which caused potential HR Import performance cost

                   A change in TL-12262 made it likely that imported Positions and
                   Organisations in a Hierarchy framework would be processed multiple times,
                   rather than just once each. No data problems were caused, but the
                   additional database operations were unnecessary. That change has been
                   reverted.

    TL-12603       Course reminders are no longer sent to unenrolled users

                   Email reminders for course feedback activities were previously being sent
                   to users who were unenrolled or whose enrolments had been suspended.

    TL-12606       Fixed resending certification course set messages

                   The course set Due, Overdue and Completed messages were only being sent the
                   first time that they were triggered on each certification path. Now, they
                   will be triggered when appropriate on subsequent recertifications,
                   including after a user has expired.

    TL-12616       Fixed the Certification window open transaction log entry

                   It was possible that the Certification window opening log entry was being
                   recorded out of order, could be recorded even if the window open function
                   did not complete successfully, and could contain incorrect data. These
                   problems have now been fixed by splitting the window open log entry into
                   two parts.

    TL-12649       Fixed the rendering of Totara form errors when get_data() is not called
    TL-12656       Remove incorrect quotations from mustache template strings

                   Quotations around template strings have been removed to avoid prevention of
                   key usage in string arrays.

    TL-12680       Made the user menu hide languages when the "Display language menu" setting is disabled

API changes:

    TL-10990       Ensured JS Flex Icon options are equivalent to PHP API

                   The core/templates function renderIcon may alternatively be called with two
                   parameters, the second being a custom data object.

Contributions:

    * Eugene Venter, Catalyst - TL-12596


Release Evergreen 20170125 (25th January 2017):
===============================================


Security issues:

    TL-10773       Added safeguards to protect user anonymity when providing feedback within 360 Feedback
    TL-12322       Improved validation within the 360° Feedback request confirmation form

                   Previously, if a user manipulated the HTML of the form for confirming
                   requests for feedback in 360° Feedback, they could change emails to an
                   invalid format or, in some cases, alter requests they should not have
                   access to.
                   Additional validation following the submission of the confirmation form now
                   prevents this.

    TL-12327       Added a setting to prevent the malicious deletion of files via the Completion Import tool

                   When adding completion records for courses and certifications via CSV, a
                   pathname can be specified instead of uploading a file. After the upload
                   occurs, the target file is deleted. Users with the capability to upload
                   completion records may have been able to delete other files aside from
                   those related to completion import. In some cases they were also being
                   shown the first line of the file. By default, only site managers have the
                   capability to upload completion records.
                   Additionally in order to exploit this the web server would need to have
                   been configured to permit read/write access on the targeted files.

                   There is now a new setting ($CFG->completionimportdir) for specifying how
                   the pathname must begin in order to add completion records with this
                   method. This setting can only be added via the config.php file. When a
                   directory is specified in this setting, files immediately within it, as
                   well as within its subdirectories, can be used for completion import.

                   If the setting is not added, completion imports can no longer be performed
                   via this method. They can still be performed by uploading a file using the
                   file picker.

    TL-12411       MDL-56225: Removed unnecessary parameters when posting to a Forum

                   Previously it was possible to maliciously modify a forum post form
                   submission to fake the author of a forum post due to the presence of a
                   redundant input parameter and poor forum post submission handling.
                   The unused parameter has been removed and the post submission handling
                   improved.

    TL-12412       MDL-57531: Improved email sender handling to prevent PHPMailer vulnerabilities from being exploited
    TL-12413       MDL-57580: Improved type handling within the Assignment module

                   Previously loose type handling when submitting to an assignment activity
                   could potentially be exploited to perform XSS attacks, stricter type
                   handling has been implemented in order to remove this attack vector.

Improvements:

    TL-2276        Added a User's authentication method column and filter to all Report Builder reports that present user information
    TL-6535        Added "HR Import" as a column and filter to the user columns and filters in Reportbuilder
    TL-8552        Added additional filters to the Program Overview report source

                   Program Status and Job assignment filters have been added to the Program
                   Overview report source

    TL-8766        Added a goal description column and filter to the Goal Custom Fields report source
    TL-9016        Added content restrictions to the Goal custom fields report source

                   Content restrictions for restricting records by management, organisation
                   and position have been added to the Goal custom fields report source.

    TL-9211        Added related user filters to the Site logs report source
    TL-10849       Improved the language strings used to describe Program and Certification exception types and actions
    TL-11074       Added additional text to the manager and approver copies of original Seminar notifications
    TL-11099       Added audience membership as a badge criteria

                   This improvement adds new criteria for site-level badges which allows an
                   administrator to award a badge based on membership of an audience / cohort.

    TL-11174       Improved the display and listing of users assigned to an Appraisal

                   * The 'Learners' column on the Appraisal Management page has been changed
                   to 'Assigned' and now show the number of users assigned to the appraisal as
                   well as the number of users that have completed the assignment. For
                   'Closed' Appraisals, the number always equals completed
                   * The Assignments tab for a specific appraisal list the assigned learners
                   for appraisals in the Draft and Active state, but only lists the users that
                   completed the appraisal for Closed appraisals. The table headings reflects
                   what is shown.
                   * The reported number of assigned, completed and cancelled users shown in
                   the reports, as well as the status of the listed users now correlates
                   better to what is shown in the Management and Detail pages.

    TL-11288       Improved the accessibility of course home page actions

                   Accessible text has been added to the following course actions:
                   * Inline editing of an activities summary
                   * Changing an activities group mode

    TL-12261       Improved code exception validation in several unit tests
    TL-12353       Improved program messaging performance

Bug fixes:

    TL-10416       Fixed an error when answering appraisal competency questions as the manager's manager or appraiser
    TL-10945       Prevented loops in management job assignments in HR Import

                   Previously, if a circular management assignment was imported, HR Import
                   would fail without sensible warning. Now, if a circular management is found
                   when importing a manager with HR Import, then one or more of the users
                   forming the circular reference will fail to have their manager assigned,
                   with a notice explaining why. When importing, as many manager assignments
                   as possible will be assigned.

    TL-11150       Fixed an undefined property error in HR Import on the CSV configuration page
    TL-11238       Fixed the Seminar name link column within the Seminar sessions report
    TL-11270       Fixed Course Completion status not being set to "Not yet started" when removing RPL completions

                   Previously, when you removed RPL completion using the Course administration
                   -> Reports -> Course completion report, it would set the record to "In
                   progress", regardless of whether or not the user had actually done anything
                   that warranted being marked as such. If the user had already met the
                   criteria for completion, the record would not be updated until the
                   completion cron task next ran.

                   Now, the records will be set to "Not yet started". Reaggregation occurs
                   immediately, and may update the user to "In progress" or "Complete"
                   depending on their progress. Note that if a course is set to "Mark as In
                   Progress on first view" and the user had previously viewed the course but
                   made no other progress, then their status will still be "Not yet started"
                   after reaggregation.

    TL-11316       Fixed an error when cloning an Appraisal containing aggregated questions
    TL-12243       Fixed a Totara menu issue leading to incorrectly encoded ampersands
    TL-12256       Prevented an incorrect redirect occurring when dismissing a notification from within a modal dialog
    TL-12263       Fixed an issue with the display of assigned users within 360° Feedback

                   The assigned group information is no longer shown for 360° Feedback in the
                   Active or Closed state. In these states, the pages always reflect actual
                   assigned users.

    TL-12277       Corrected an issue where redirects with a message did not have a page URL set
    TL-12280       Fixed a bug preventing block weights being cloned when a dashboard is cloned
    TL-12283       Fixed several issues on the waitlist page when Seminar approval type is changed

                   The waitlist page showed the wrong approval date (1 Jan 1970) and debug
                   messages when a seminar changed its approval type from no approval required
                   to manager approved.

    TL-12284       Fixed an upgrade error due to an incorrectly unique index in the completion import tables on SQL Server

                   Previously, if a site running SQL Server had imported course or
                   certification completions, there could have been an error when trying to
                   upgrade to Totara 9. This has been fixed. Sites that had already
                   successfully upgraded will have the unique index replaced with a non-unique
                   equivalent.

    TL-12287       Ensured Hierarchy 'ID number' field type is set as string in Excel and ODS format exports to avoid incorrect automatic type detection
    TL-12297       Removed options from the Reportbuilder "message type" filter when the corresponding feature is disabled
    TL-12299       Fixed an error on the search page when setting Program assignment relative due dates
    TL-12301       Fixed the replacement of course links from placeholders in notifications when restoring a Seminar

                   Previously when a course URL was embedded in a seminar notification
                   template, it would be changed to a placeholder string when the seminar was
                   backed up. Restoring the seminar would not change the placeholder back to
                   the proper URL. This fix ensures it does.

    TL-12303       Fixed the HTML formatting of Seminar notification templates for third-party emails
    TL-12305       Fixed incorrect wording in Learning Plan help text
    TL-12311       Fixed the "is after" criteria in the "Start date" filter within the Course report source

                   The "is after" start date filter criteria now correctly searching for
                   courses starting immediately after midnight in the users timezone.

    TL-12315       Waitlist notifications are now sent when one message per date is enabled

                   If a Seminar event was created with no dates, people could still sign up
                   and be waitlisted.
                   However, they would only receive a sign up email if the "one message per
                   date" option was off.
                   Now, the system will send the notification regardless of this setting.

    TL-12323       Removed references to the SCORM course format from course format help string
    TL-12325       Fixed the Quick Links block to ensure it decodes URL entities correctly
    TL-12333       Made improvements to the handling of invalid job assignment dates
    TL-12337       Fixed the formatting of event details placeholder in Seminar notifications
    TL-12339       Reverted removal of style causing regression in IE

                   TL-11341 applied a patch for a display issue in Chrome 55.
                   This caused a regression for users of Edge / IE browsers making it
                   difficult and in some cases impossible to click grouped form elements.
                   The Chrome rendering bug has since been addressed.

    TL-12344       Fixed an error message when updating Competency scale values
    TL-12352       Fixed a bug in the cache API when fetching multiple keys having specified MUST_EXIST

                   Previously when fetching multiple entries from a cache, if you specified
                   that the data must exist, in some circumstances the expected exception was
                   not being thrown.
                   Now if MUST_EXIST is provide to cache::get_many() an exception will be
                   thrown if one or more of the requested keys cannot be found.

    TL-12369       Marked class totara_dialog_content_manager as deprecated

                   This class is no longer in use now that Totara has multiple job
                   assignments. Class totara_job_dialog_assign_manager should be used instead.

Miscellaneous Moodle fixes:

    TL-12406       MDL-57100: Prevented javascript exceptions from being displayed during an AJAX request
    TL-12407       MDL-56948: Fixed Assignment bug when viewing a submission with a grade type of "none"
    TL-12408       MDL-57163: Improved the feedback given when trying to install without the curl extension installed
    TL-12409       MDL-57170: Fixed fault in legacy Dropbox API usage
    TL-12410       MDL-57193: Fixed external database authentication where more than 10000 users are imported

Contributions:

    * André Yamin at Kineo NZ - TL-6535
    * David Shaw at Kineo UK - TL-12243
    * Eugene Venter at Catalyst NZ - TL-11099
    * Lee Campbell at Learning Pool - TL-2276


Release Evergreen 20161221 (21st December 2016):
=======================================

Important:

    TL-10980       Totara 10 can be upgraded from Totara 9 only

                   It is important to note that sites running on Totara 2.9 or earlier are
                   required to upgrade through Totara 9.
                   If you are intending to upgrade from Totara 2.9 to Totara 10 you must
                   upgrade to Totara 9 before upgrading to Totara 10.

    TL-10994       Introduction of the Evergreen maturity

                   A new product maturity setting has been introduced, MATURITY_EVERGREEN.
                   This new maturity will be used only for Evergreen releases and should be
                   considered stable.

    TL-11161       Removed Kiwifruit responsive theme

                   As Kiwifruit responsive has been deprecated previously, it has now been
                   removed.

                   If you wish to continue using Kiwifruit responsive (NOTE: it will no longer
                   be supported so there is a high likelyhood things will be broken), please
                   follow these steps (on top of a normal upgrade process):
                   1. Take a backup of theme/kiwifruitresponsive
                   2. Update the Totara code base (this will remove the kiwifruit responsive
                      theme
                   3. Restore theme/kiwifruitresponsive into it's original location
                   4. Run the Totara LMS upgrade script.

                   If step 4 is done before step 3, all settings that were in Kiwifruit
                   responsive will have been removed (and may need to be restored).

    TL-11333       Fixes from Moodle 3.0.7 have been included in this release

                   Information on the issues included from this Moodle release can be found
                   further on in this changelog.

    TL-11369       Date related form elements exportValue() methods were fixed to return non array data by default

                   All custom code using MoodleQuickForm_date_time_selector::exportValue() or
                   \MoodleQuickForm_date_selector::exportValue() must be reviewed and fixed if
                   necessary.

Security issues:

    TL-5254        Improved user verification within the Quick Links block
    TL-11133       Fixed Seminar activities allowing sign up even when restricted access conditions are not met
    TL-11194       Fixed get_users_by_capability() when prohibit permissions used
    TL-11335       MDL-56065: Fixed the update_users web service function
    TL-11336       MDL-53744: Fixed question file access checks
    TL-11338       MDL-56268: Format backtrace to avoid displaying private data within web services

Improvements:

    TL-7221        Added time selectors to Before and After date criteria in dynamic audience rules
    TL-7954        Added customisable manager subjects to program messages

                   This patch adds a new setting to the emails form on the program messages
                   tab. The new text field "manager subject" sits between the "send notice to
                   manager" checkbox and the "notice for manager" text area. This new setting
                   allows you to edit the subject line for managers receiving the email about
                   their staff member. If the field is left blank the message will continue to
                   use the old strings.

    TL-9299        Improved the performance of the program completion scheduled task
    TL-9756        Removed an HTML table when viewing a plan that has been changed after approval
    TL-9849        Replaced the filepicker and filemanager upload icons with flex icons
    TL-10119       Spacing and alignment improved between user and 'burger' menu on mobile devices.
    TL-10254       Improved accessibility when viewing the course user outline report
    TL-10404       Added copy manager setting to Seminar notification templates
    TL-10414       Ensured collapsable section header is no longer displayed when empty within Appraisals
    TL-10670       Implemented position, organisation, job assignment and custom user profile field value sync in Totara Connect

                   Totara Sync can now be configured to synchronise Positions, Organisations,
                   Job Assignments, and custom user profile field values between connected LMS
                   instances.

                   Please be aware of the following limitations:
                   * Custom user profile field values will only be synced if the client site
                     has custom profile fields configured with short names that match exactly
                     those on the server.
                   * Positions, and Organisations items will only be synced if the client site
                     has a framework type with an idnumber matching exactly the type of the
                     position or organisation on the server.

    TL-10833       Added two new settings to control the maximum width and height of graphs within the Report Graph block

                   There are two new settings for the Report Graph block:

                   * Max width
                   * Max height

                   These allow you to control the proportions of the graph that is displayed
                   by this block, ensuring that it is suitably sized for the location of your
                   block.
                   By default the graph will continue to consume the available space, as it
                   has done previously.
                   It should also be noted that the aspect ratio of the graph is maintained.

    TL-10952       Links that should be styled as buttons now look like buttons in Basis & Roots themes
    TL-10971       Improved Feedback activity export formatting

                   The following improvements were made to the exported responses for feedback
                   activities:
                   * Newlines in Long Text responses are no longer replaced with the html <br/> tag
                   * The text wrap attribute is set for all response cells
                   * Long text, Short text and Information responses are no longer exported in bold

    TL-11054       Only the available regions are shown when configuring a block's position on the current page

                   Previously, when configuring blocks, all possible regions were shown when
                   setting the region for a block on the current page. This setting now only
                   has the options that exist on the page

    TL-11056       Added phpunit support for third party modules that use "coursecreator" role
    TL-11075       Improved inline help for Seminar's "Manager and Administrative approval" option
    TL-11117       Removed unused, redundant, legacy hierarchy code
    TL-11121       Added new program completion criteria to site badges
    TL-11145       Newly created learning plans now include competencies from all of a user's job assignments
    TL-11198       Added support for add-on report builder sources in column tests

                   Add-on developers may now add phpunit_column_test_add_data() and
                   phpunit_column_test_expected_count() methods to their report sources to
                   pass the full phpunit test suit with add-ons installed.

    TL-11261       Converted folder and arrow icon in file form control to flex icons
    TL-11273       Removed an unnecessary fieldset surrounding admin options
    TL-11289       Dropping a file onto the course while editing now has alternative text

                   This also converts the image icon to a flex icon.

Bug fixes:

    TL-4912        Fixed the missing archive completion option in course administration menu
    TL-7666        Images used in hierarchy custom fields are now displayed correctly when viewing or reporting on the hierarchy
    TL-9500        Fixed "View full report" link for embedded reports in the Report table block
    TL-9988        Fixed moving hierarchy custom fields when multiple frameworks and custom fields exist
    TL-10054       Ensured that the display of file custom fields in hierarchies link to the file to download
    TL-10101       Removed unnecessary permission checks when accessing hierarchies
    TL-10744       Fixed footer navigation column stacking in the Roots and Basis themes
    TL-10915       Ensured that courses are displayed correctly within the Current Learning block when added via a Certification
    TL-10953       Fixed Learning Plans using the wrong program due date

                   Previously, given some unlikely circumstances, when viewing a program in a
                   learning plan, it was possible that the program due date could have been
                   displaying the due date for one of the course sets instead.

    TL-11000       When calculating the Aggregate rating for appraisal questions, not answered questions and zero values may now be included in aggregate calculations

                   Two new settings have been added to Aggregate rating questions within
                   Appraisals.
                   These can be used in new aggregate rating questions to indicate how the
                   system must handle unanswered questions, as well as questions resulting in
                   a zero score during the calculations.

    TL-11063       Fixed a PHP error in the quiz results statistics processing when a multiple choice answer has been deleted
    TL-11072       Administrative approver can do final approval of seminar bookings in two stage approvals prior to manager
    TL-11076       Fixed the display of the attendee name for Seminar approval requests in the Task/Alert report
    TL-11110       Added validation warning when creating management loops in job assignments

                   Previously, if you tried to assign a manger which would result in a
                   circular management structure, it would fail and show an error message. Now
                   it shows a validation warning explaining the problem.

    TL-11124       Treeview controls in dialogs now display correctly in RTL languages
    TL-11126       Fixed HR Import data validation being skipped in some circumstances

                   If the source was an external database, and the first record in the import
                   contained a null, then the data validation checks on that column were being
                   skipped. This has been fixed, and the data validation checks are now fully
                   covered by automated tests.

    TL-11129       Fixed url parameters not being added in pagination for the enrolled audience search dialog
    TL-11130       Fixed how backup and restore encodes and decodes links in all modules
    TL-11137       Courses, programs and certifications will always show in the Record of Learning if the user has made progress or completed the item

                   The record of learning is intended to list what the user has achieved.
                   Previously, if a user had completed an item of learning, this may sometimes
                   have been excluded due to visibility settings (although not in all cases
                   with standard visibility). The effect of audience visibility settings and
                   available to/from dates have been made consistent with that of standard
                   visibility. The following are now show on their applicable Record of
                   Learning embedded reports, regardless of enrolment status and current
                   visibility of the item elsewhere.

                   Courses:  Any course where a user's status is greater than 'Not yet
                   started'. This includes 'In-progress' and 'Complete'.

                   Programs: Any program where the user's status is greater than 'Incomplete'.
                   In existing Totara code, this will only be complete programs. This applies
                   to the status of the program only and does not take into account program
                   course sets. If just a course set were complete, and not the program, the
                   program would not show on the Record of Learning if it should not otherwise
                   be visible.

                   Certifications: Any certification where the user's status is greater than
                   'Newly assigned'. This includes 'In-progress', 'Certified' and 'Expired'.

    TL-11139       Fixed report builder access permissions for the authenticated user role

                   The authenticated user role was missed out when a report's access
                   restriction was "user role in any context" - even if this role was ticked
                   on the form. The fix now accounts for the authenticated user.

    TL-11148       Fixed suspended course enrolments not reactivating during user program reassignment
    TL-11191       Ensured the calendar block controls are displayed correctly in RTL languages
    TL-11200       Fixed the program enrolment plugin which was not working for certifications when programs had been disabled
    TL-11203       Allowed access to courses via completed programs consistently

                   Previously if a user was complete with a due date they could not access any
                   courses added to the program after completion, but users without a due date
                   could access the new courses. Now any user with a valid program assignment
                   can access the courses regardless of their completion state.

    TL-11208       Fixed unnecessary comma appearing after user's name in Seminar attendee picker

                   When only "ID Number" is selected in the showuseridentity setting and a
                   user does not have an ID number an extra comma was displayed after the
                   user's name in the user picker when adding / removing Seminar attendees.

    TL-11209       Fixed errors in some reports when using report caching and audience visibility
    TL-11213       Fixed undefined index warnings while updating a Seminar event without dates
    TL-11216       Fixed incorrect use of userid when logging a program view from required learning
    TL-11217       Flex icons now use the title attribute correctly
    TL-11237       Deleting unconfirmed users no longer deletes the user record

                   Previously when unconfirmed users were deleted by cron the user record was
                   deleted from the database immediately after the standard deletion routines
                   were run.
                   Because it is possible to include unconfirmed users in dynamic audiences
                   they could end up with traces in the database which may not be cleaned up
                   by the standard deletion routines.
                   The deletion of the user record would then lead to these traces becoming
                   orphaned.
                   This behaviour has been changed to ensure that the user record is never
                   deleted from the database, and that user deletion always equates to the
                   user record being marked as deleted instead.

    TL-11239       Fixed type handling within the role_assign_bulk function leading to users not being assigned in some situations
    TL-11246       Added default sort order of attendees on the Seminar sign-in sheet

                   The sort order was the order in which the attendees was added. This patch
                   adds a default sort order to the embedded report so that users are listed
                   in alphabetical order. Note: for existing sites the sign-in sheet embedded
                   report will need to be reset on the manage reports page (doing this will
                   reset any customisations to this report)

    TL-11263       Loosened cleaning on Program and Certification summary field making it consistent with course summary
    TL-11272       Fixed inaccessible files when viewing locked appraisal questions
    TL-11309       HR Import now converts mixed case usernames to lower case

                   Now when you import a username with mixed case you will receive a warning,
                   the username will be converted to lower case and the user will be
                   imported.
                   This patch brings the behaviour in Totara 9 in line with Totara 2.9.

    TL-11329       Fixed program course sets being marked complete due to ignoring "Minimum score"

                   When a program or certification course set was set to "Some courses" and
                   "0", the "Minimum score" was being ignored. Even if a "Minimum score" was
                   set and was not reached, the course set was being marked complete. Now, if
                   a "Minimum score" is set, users will be required to reach that score before
                   the course set is marked complete, in combination with completing the
                   required number of courses.

                   If your site has a program or certification configured in this way, and you
                   find users who have been incorrectly marked complete, you can use the
                   program or certification completion editor to change the records back to
                   "Incomplete" or "Certified, window is open". You should then wait for the
                   "Program completions" scheduled task (runs daily by default) to calculate
                   which stage of the program the user should be at.

    TL-11331       Fixed HTML and multi language support for general and embedded reports
    TL-11341       Fixed report builder filter display issue in chrome 55

                   Previously there was a CSS statement adding a float to a legend which
                   appears to be ignored by most browsers. With the release of chrome 55, this
                   style was being interpreted.

    TL-12244       Fixed 'Allow extension request' setting not being saved when adding programs and certifications
    TL-12246       Fixed MSSQL query for Course Completion Archive page
    TL-12248       Fixed layout of Totara forms when using RTL languages

API changes:

    TL-8423        Changed course completion to only trigger processing of related programs

                   Previously, course completion caused completion of all of a user's programs
                   and certifications to be re-processed. Now, only programs which contain
                   that course are processed.

    TL-10649       core/block template now uses the same variable for the skip block link
    TL-11225       \totara_form\model::get_current_data(null) now returns all current form data

Miscellaneous Moodle fixes:

    TL-11337       MDL-51347: View notes capability is now checked using the course context
    TL-11339       MDL-55777: We now check libcurl version during installation
    TL-11342       MDL-55632: Tidy up forum post messages
    TL-11343       MDL-55820: Use correct displayattempt default options in SCORM settings
    TL-11344       MDL-55610: Improved cache clearing
    TL-11345       MDL-42041: Added "Turn Editing On" to page body to Book module
    TL-11346       MDL-55874: Fixed html markup in participation report
    TL-11347       MDL-55862: The database module now uses the correct name function for display
    TL-11348       MDL-55505: Fixed editing of previous attempt in Assignment module
    TL-11349       MDL-53893: Fixed awarding of badges with the same criteria
    TL-11351       MDL-55654: Added multilang support for custom profile field names and categories
    TL-11352       MDL-55626: Added desktop-first-column to legacy themes
    TL-11353       MDL-29332: Fixed unique index issue in calculated questions when using MySQL with case insensitive collation
    TL-11358       MDL-55957: Fixed the embedded files serving in Workshop module
    TL-11359       MDL-55987: Prevent some memory related problems when updating final grades in gradebook
    TL-11360       MDL-55988: Prevent autocomplete elements triggering warning on form submission
    TL-11361       MDL-55602: Added redis session handler with locking support
    TL-11362       MDL-56019: Fixed text formatting issue in web services
    TL-11363       MDL-55776: Fixed group related performance regression
    TL-11364       MDL-55876: Invalid low level front page course updates are now prevented
    TL-11368       MDL-55911: Improved Quiz module accessibility
    TL-11371       MDL-56069: Fixed scrolling to questions in Quiz module
    TL-11372       MDL-56136: Improved error handling of file operations during restore
    TL-11373       MDL-56181: Updated short country names
    TL-11374       MDL-56127: Fixed a regression in form element dependencies
    TL-11376       MDL-55861: Fixed displaying of activity names during drag & drop operations
    TL-11379       MDL-52317: Fixed visual issues when inserting oversized images
    TL-11384       MDL-55597: Fixed support for templates in subdirectories
    TL-11385       MDL-51633: Restyled ADD BLOCK to remove max-width in legacy themes
    TL-11386       MDL-51584: Improved performance when re-grading
    TL-11387       MDL-56319: Fixed the handling of default blocks when an empty string is used to specify there should be no default blocks
    TL-11388       MDL-52051: Correct code that relies on the expires_in optional setting within OAuth
    TL-11389       MDL-56050: Fixed missing context warning on the maintenance page
    TL-11390       MDL-36611: Fixed missing context warning when editing outcomes
    TL-11392       MDL-51401: Improved the ordering of roles on the enrolled users screen
    TL-11393       MDL-55345: Fixed links to IP lookup in user profiles
    TL-11394       MDL-56062: Standardised display of grade decimals in Assignment module
    TL-11395       MDL-56345: Fixed alt text for PDF editing in Assignment module
    TL-11396       MDL-56439: Added missing include in course format code
    TL-11397       MDL-56328: Improved activity indentation on the course page in legacy themes
    TL-11398       MDL-56368: Fixed Restrict Access layout issue in legacy themes
    TL-11399       MDL-43796: Fixed Reveal identities issue during restore
    TL-11400       MDL-56131: Added checks to prevent the Choice module becoming locked for a long periods of time
    TL-11401       MDL-55143: Fixed detection of version bumps in phpunit
    TL-11402       MDL-29774: Group membership summaries are now updated on AJAX calls
    TL-11403       MDL-55456: Fixed context warning when assigning roles
    TL-11404       MDL-56275: Removed repository options when adding external blog
    TL-11405       MDL-55858: Removed subscription links when not relevant in Forum module
    TL-11406       MDL-56250: mforms now support multiple validation calls
    TL-11407       MDL-53098: Fixed form validation issue when displaying confirmation
    TL-11408       MDL-56341: Fixed Quote and Str helpers collisions in JS Mustache rendering
    TL-11411       MDL-48350: Fixed action icons placement in docked blocks in legacy themes
    TL-11412       MDL-56347: Added diagnostic output for alt cache store problems in phpunit
    TL-11414       MDL-56354: All debugging calls now fail phpunit execution
    TL-11415       MDL-54112: Fixed Required grading filtering
    TL-11416       MDL-56615: Fixed PHP 7.0.9 warning in Portfolio
    TL-11417       MDL-56673: Fixed minor problems in template library tool
    TL-11418       MDL-47500: Improved SCORM height calculation

                   Please note that Totara already contained a similar patch. This change
                   added minor changes from upstream only.

    TL-11419       MDL-55249: Fixed status in feedback activity reports
    TL-11420       MDL-55883: Fixed calendar events for Lesson module
    TL-11421       MDL-56634: Improved rendering of WS api descriptions
    TL-11423       MDL-54986: Disabled add button for quizzes with existing attempts
    TL-11426       MDL-56748: Fixed a memory leak when resetting MUC
    TL-11427       MDL-56731: Fixed breadcrumb when returning to groups/index.php
    TL-11428       MDL-56765: User preferences are reloaded in new WS sessions
    TL-11429       MDL-53718: Do not show course badges when disabled
    TL-11430       MDL-54916: Improved the performance of empty ZIP file creation
    TL-11431       MDL-56120: Calendar events belonging to disabled modules are now hidden
    TL-11432       MDL-56755: Improved documentation of assign::get_grade_item()
    TL-11433       MDL-56133: Caches are now purged after automatic language pack updates
    TL-11434       MDL-53481: Fixed sql errors within availability restrictions
    TL-11435       MDL-56753: Fixed separate group mode errors
    TL-11436       MDL-56417: Fixed ignore_timeout_hook logic in auth subsystem
    TL-11437       MDL-56623: Added a new lang string for 'addressedto'
    TL-11438       MDL-55994: Fixed warning in RSS feed generation
    TL-11439       MDL-52216: Prevented invalid view modes in Lesson module

Contributions:

    * Eugene Venter at Catalyst NZ - TL-11121
    * Russell England at Kineo USA - TL-11239

*/
