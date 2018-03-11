<?php
/*

Totara Learn Changelog

Release 11.0 (12th March 2018):
==============================

Key:           +   Totara 11.0 only

Important:

    TL-9352        New site registration form

                   In this release we have added a site registration page under Site
                   administration > Totara registration. Users with the 'site:config'
                   capability will be redirected to the page after upgrade until registration
                   has been completed.

                   Please ensure you have the registration code available for each site before
                   you upgrade. Partners can obtain the registration code for their customers'
                   sites via the Subscription Portal. Direct subscribers will receive their
                   registration code directly from Totara Learning.

                   For more information see the help documentation:

                   https://help.totaralearning.com/display/TLE/Totara+registration

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

    TL-17166       Added support for March 1, 2018 PostgreSQL releases

                   PostgreSQL 10.3, 9.6.8, 9.5.12, 9.4.17 and 9.3.22 which were released 1st
                   March 2018 were found to not be compatible with Totara Learn due to the way
                   in which indexes were read by the PostgreSQL driver in Learn.
                   The method for reading indexes has been updated to ensure that Totara Learn
                   is compatible with PostgreSQL.

                   If you have upgraded PostgreSQL or are planning to you will need to upgrade
                   Totara Learn at the same time.

    TL-16198       Fixed compatibility issues with MySQL 8.0.3RC
    TL-16937   +   The following entry concerns functionality, plugins, and settings that are going
                   to be deprecated or removed in Totara Learn 12.0. If you are using any of the items
                   on this list please get in touch with us now.

                     * MNET server and client functionality will be deprecated in Totara Learn 12, and removed after its release.
                     * The following authentication plugins will be removed in Totara Learn 12.
                       If anyone is using them and wishes to continue doing so they will need
                       to re-install the plugins during upgrade.
                         * FirstClass server authentication
                         * IMAP authentication
                         * None authentication
                         * PAM authentication
                     * The email signup authentication plugin will be deprecated in Totara Learn 12.
                       All sites using this plugin should start using the Approved authentication
                       plugin instead. This plugin provides the same functionality, more features
                       that can be turned on, is better performing, and is more secure.
                     * The assignment upgrade tool will be removed in Totara Learn 12.
                       Those using the old assignment module can continue to do so, however there
                       will be no migration path from the old assignment module to the new assignment
                       module after Totara Learn 11.
                     * The InnoDB migration tool will be removed in Totara Learn 12.
                       This tool is no longer necessary as all sites using MySQL are required to be
                       running InnoDB on Totara 9 and above already.
                     * The slasharguments admin setting which was disabled in Totara Learn 10 will be removed in Totara Learn 12.
                       This includes the removal of support for file URL rewriting by the webserver.
                     * The loginhttps admin setting which was disabled in Totara Learn 10 will be removed in Totara Learn 12.
                     * The trusttext admin setting and accompanying functionality will be removed in Totara Learn 12.
                       It is commonly misunderstood and its use introduces security risks that cannot be mitigated.
                     * Previously deprecated config.html files within authentication plugins will no longer be supported
                       and uses of them will be cleaned up.
                     * The Portfolio functionality and all accompanying portfolio plugins will be deprecated in Totara Learn 12
                       and removed in a future version.

                   In addition the following system requirement changes will come in to effect for Totara Learn 12.

                     * 64bit PHP will become a recommendation and will be checked during installation and upgrade.
                       Those running 32bit versions of PHP will be shown a warning.
                     * PostgreSQL 9.4 will be required for Totara Learn 12.
                       Those running earlier versions will be required to upgrade.

New features:

    TL-9004    +   New report source: scheduled reports

                   This new report source provides details about existing scheduled reports,
                   their frequencies and recipients.

                   A new capability "totara/reportbuilder:managescheduledreports" has been
                   defined to allow people to edit and delete scheduled reports (NB: the
                   scheduling and recipients, not the linked reports themselves). Note that
                   users with the new capability should also be given the
                   "moodle/cohort:view" and "moodle/user:viewdetails" capabilities so that
                   they can add audiences and individuals as recipients of scheduled reports.

    TL-16589   +   Added new progress wizard form component

                   The progress wizard is a new form grouping which allows large forms to be broken
                   down into smaller stages providing an improved user journey. Only the current
                   stage is displayed but there is a visual indication of how many stages there are
                   in total and where the current stage sits within the journey. The user can always
                   navigate back to any previously completed stage by interacting with the wizard.
                   It is also optional if the user is allowed to skip stages or if they have to
                   complete them in the set order.

    TL-16433   +   Added tool to manage terms and conditions and obtain user consent

                   In order to facilitate GDPR subscriber compliance, a new admin tool is now
                   available that allows the site administrator to create, edit,
                   review/preview and delete terms and conditions.
                   Each term and condition can have one or more consent related user
                   confirmation which may or may not be required.

                   The tool is not enabled by default, but can be enabled through
                   the "enablesitepolicies" configuration setting.

                   If enabled, users will be required to view and consent to any current terms
                   and conditions that they have not viewed and consented to before.
                   If the user doesn't accept all required terms and conditions they will be
                   logged out.

    TL-16747   +   Added the user data management plugin

                   This plugin allows users and administrators to manage users' data. A new
                   collection of links is located under "Site administration -> Users -> User
                   data management". Here you can manage global user data settings, configure
                   purge and export profiles, see logs of purges and exports that have been
                   scheduled or performed, and manage deleted users.

                   Note that deleted users are no longer listed under "Site administration ->
                   Users -> Accounts -> Browse list of users". To manage deleted users
                   (including undelete), you need to go to "Site administration -> Users ->
                   User data management -> Deleted user accounts".

                   Purge profiles can be configured by administrators, and allow them to
                   specify which data will be deleted. The purge profiles can be applied to
                   users, deleting the data. Purge profiles can be applied to users manually.
                   Users can also be configured to have a specific purge profile automatically
                   applied on the condition that they are suspended or deleted, and site
                   defaults for these actions can also be configured. Note that existing
                   behaviour when users are suspended or deleted is not affected - the data
                   listed on the delete user confirmation page will still be deleted,
                   regardless of any purge profile which might apply to the user.

                   Export profiles can be configured by administrators, and allow them to
                   specify which data can be exported. When granted
                   the "totara/userdata:exportself" capability, users will then be able to
                   run an export of their own data, which will create a downloadable file
                   containing the specified data. Export must first be enabled in "Site
                   administration -> Users -> User data management -> Settings".

                   This new feature provides sites with tools which will support them becoming
                   GDPR compliant. By configuring purge profiles and purging data, sites can
                   comply to GDPR rules which indicate what data must be removed and
                   which must be retained, given their particular circumstances. By
                   configuring export profiles and giving users the capability to perform the
                   exports, sites can comply to GDPR rules which indicate what data must be
                   made available to users, and exclude data which is inappropriate given
                   their particular circumstances.

                   This initial release of the user data plugin contains many user data items
                   (which each specify one type of data which can be deleted or exported),
                   but is not a comprehensive collection. The sample of user data items
                   shipped with this version, along with the core user data system, will
                   provide third party developers with examples to start developing their own
                   user data items. More user data items will be released in this branch over
                   the next few releases. The intention is to provide user data items to
                   allow purge and export of all user data which might be required to be
                   deleted or exported to obtain GDPR compliance, before the GDPR rules come
                   into effect.
                   For more information on the technical implementation of user data purge and
                   export see
                   https://help.totaralearning.com/display/DEV/User+data+developer+documentation

Report Builder improvements:

    TL-7553        Improved Report Builder exports to use column headers more compatible with Microsoft Excel CSV files
    TL-11305   +   Creating/modifying/deleting scheduled reports now generate events

                   New event classes are: scheduled_report_created, scheduled_report_updated
                   and scheduled_report_deleted.

                   These events are also viewable in the system logs under
                   site administration > reports > logs.

    TL-14936       Added a report setting to control the minimum allowed frequency for scheduled reports

                   The new setting "Minimum scheduled report frequency" on the Site administration > Reports >
                   Report builder > General settings page allows you to select the minimum frequency for
                   scheduled reports, the current options are:
                     * Every X minutes
                     * Every X hours
                     * Daily
                     * Weekly
                     * Monthly

                   For example if you selected "Daily" as the minimum, then users setting up or editing scheduled
                   reports would only see that option and the less frequent options (i.e. weekly and monthly).

    TL-15027   +   Added a new capability to control who can create scheduled reports

                   There is a new "totara/reportbuilder:createscheduledreports" capability
                   that allows a user to create scheduled reports. If a user does not have
                   this capability, they will not see the "Scheduled Reports" section (ie with
                   the "Create scheduled report" button) when they go to the "Reports" page
                   via the Totara menubar.

                   Note the capability is separate and NOT related to the
                   "totara/reportbuilder:managescheduledreports" capability; that capability
                   allows users to see, edit or delete all scheduled reports in the system.

    TL-15895   +   Added the 'Send to self' option to Email settings for Scheduled reports
    TL-15896   +   Added a Report Builder administration setting to control what scheduled report email options are available
    TL-15962   +   Removed disabled embedded reports from embedded reports list.

                   Some functional areas can be disabled, for example, Record of Learning. If
                   they contain embedded reports, these reports will no longer be listed in
                   the main embedded report list.
    TL-16241       Fixed breadcrumb trail when viewing a user's completion report
    TL-16494       Improved embedded reports test coverage
    TL-16624       Improved exported course progress values within two Report Builder sources

                   The 'Record of Learning: Courses' and 'Course Completion' report sources
                   have been updated to enable a user's progress towards course completion to
                   be exported as a percentage.
    TL-16653       Report builder now shows an empty graph instead of an error message when zero values are returned
    TL-16684   +   Removed database queries from rb_display functions in cohort association report sources
    TL-16690       Added a hook for cache invalidation in Report graph block
    TL-16866   +   New Report builder graph setting "remove_empty_series"

                   Note that this setting works for orientation with data series in columns
                   only. It is also not compatible with pie charts.

    TL-16910   +   Unused group_concat emulation was removed from Report Builder installation code

General improvements:

    TL-17098   +   Improved the privacy, security and usability of the course backup/restore process
    TL-1512    +   Changed Google Fusion export to open in a new window
    TL-9277        Added additional options when selecting the maximum Feedback activity reminder time
    TL-11296       Added accessible text when creating/editing profile fields and categories
    TL-12650       Removed HTML table when viewing the print book page
    TL-12805   +   Archived Seminar attendance within certifications no longer prevents future signups

                   Previously, multiple attendance needed to be turned on in Seminars
                   contained within certifications. This is no longer required, and the
                   warning has been removed. Note that users will now be able to sign up to
                   Seminars after course reset (when the recertification window opens) even if
                   the Seminar is not a requirement for course completion and multiple
                   attendance is turned off.

    TL-14745   +   The recent activity block and recent activity page now show the same activity
    TL-16551   +   Converted the activity restriction icons into flex icons
    TL-14963   +   Added an Organisation assignment restriction for conditional activity access

                   Access to an activity can now be restricted based on the Organisation that
                   a learner has been assigned to via Job Assignments.

    TL-14964   +   Added a Position assignment restriction for conditional activity access

                   Access to an activity can now be restricted based on the Position that a
                   learner has been assigned to via Job Assignments.

    TL-14965   +   Added an Audience membership restriction for conditional activity access

                   Access to an activity can now be restricted based on the Audiences that a
                   learner has membership in.

    TL-15091   +   Added a language restriction for conditional activity access

                   Access to an activity can now be restricted based on the user's language.

    TL-15044   +   Updated Menu type custom fields to display a hyphen when the field is locked and empty
    TL-8723    +   Updated text area custom fields to display a hyphen when the field is locked and empty
    TL-15061   +   Improved the styling of delete and combine tags buttons

                   Previously these were using the Bootstrap 2 CSS class names for buttons, they have now
                   been updated to the Bootstrap 3 CSS class names.

    TL-15832   +   Updated xpath when matching against HTML tables using Behat to allow non-exact matches
    TL-15835       Made some minor improvements to program and certification completion editors

                   Changes included:
                    * Improved formatting of date strings in the transaction interface and logs.
                    * Fixed some inaccurate error messages when faults might occur.
                    * The program completion editor will now correctly default to the
                      "invalid" state when there is a problem with the record.

    TL-15856   +   Improved the styling of the modal JavaScript library

                   These were previously styled similar to that of the YUI dialogues, they are
                   now styled similar to Bootstrap 3 modals.

    TL-15871   +   Force users to complete required user profile fields upon login

                   The users will be forced during the login to complete any user profile
                   fields that have been set as required and have not yet been completed for
                   that user.

    TL-15907       Improved how evidence custom field data is saved when importing completion history
    TL-15913       Improved the display of the progress bar component and improved the quality of the CSS
    TL-15920       Activities required for course completion are now shown in the progress bar popover

                   When a user clicks on the progress bar for a specific course, the activities
                   required to complete the course are now listed in a popover.

    TL-15992       A warning message is now shown when a Quiz may require more random questions than there are questions available

                   When creating or editing a quiz, warning messages are now shown when adding
                   random questions from categories that don't contain enough questions. It is
                   only a warning to highlight the risk and doesn't prevent the course
                   administrator from creating the quiz.

                   If a learner attempts to take a quiz with insufficient questions,
                   the system behaves as before.

    TL-15995   +   Improved the indexes on the Custom Fields *_info_data tables to ensure best performance and create consistency
    TL-16007   +   Converted warning messages in HR Import to use the notification API
    TL-16069       Improved the alignment of question bank table headings
    TL-16137   +   The Background image for tiles in the featured links block can now be set to fill or fit in the tile
    TL-16138   +   The Featured links block now allows the tile shape to be configured (portrait, landscape and full width)
    TL-16141   +   Added Program and Certification tiles to the Featured links block.
    TL-16142   +   Added Progress bars to Course Tiles in the Featured links block
    TL-16152   +   Improved the layout of the Recent Learning block by removing a layout table
    TL-16154       Improved the CSS of the Last Course Accessed block, increasing the width of the progress bar

                   This will require CSS to be regenerated for themes that use LESS inheritance

    TL-16170       Externally accessible badge check now uses the correct notify_warning template
    TL-16176   +   Converted the maintenance countdown timer to use the correct notification template and AMD module
    TL-16207   +   Removed support for obsolete "mssql" database driver

                   Totara Learn 11 requires PHP 7.1, the old MSSQL driver is supported in PHP
                   5.6 and below only, It is not available in PHP 7.1.
                   The official sqlsrv driver is available for PHP 7.1, and is supported on
                   all operating systems.
                   Anyone using the old MSSQL driver should upgrade to the sqlsrv driver when
                   they upgrade their server environment.

    TL-16209   +   Removed fieldset headers from 360° Feedback questions
    TL-16252   +   Added a new setting that allows persistent logins

                   When enabled then a "Remember login" option will appear on the login page.

                   Any user logging in can check this box to enable a persistent login,
                   meaning that they won't get timed-out and have to log in again.

    TL-16256       Allowed appraisal messages to be set to "0 days" before or after event

                   Some immediate appraisal messages were causing performance issues when
                   sending to a lot of users.
                   This improvement allows you to set almost immediate messages that will send
                   on the next cron run after the action was triggered to avoid any
                   performance hits. The appraisal closure messages have also been changed to
                   work this way since they don't have any scheduling options.

    TL-16260       Invalid request to force password change is automatically deleted if auth plugin does not support changing of passwords
    TL-16372       Added support for utf8mb4 collations with full Unicode support
    TL-16373       Added screen reader text to the block actions menu
    TL-16380   +   Hide Competencies in Learning plans when the Framework they are within is hidden

                   When competencies are individually hidden via the competency management page, they
                   are automatically hidden in any learning plans. This change ensures that when
                   competency frameworks are hidden, all of the competencies within the framework are
                   also hidden within learning plans.

    TL-16427       Added more information about the delay before items appear in the recycle bin

                   * A message is displayed in the deletion confirmation dialog.
                   * A message is displayed when viewing the recycle bin if there are
                   activities or resources that are yet to be processed.

    TL-16432       Course completion history records are now included in course backups and can be restored
    TL-16441   +   Fixed signup information being displayed in attendees pages
    TL-16452   +   Dashboard, course and report name fields have been increased up to 1333 characters
    TL-16478   +   Removed unnecessary CSS in Totara plan
    TL-16479       Fixed inconsistent use of terminology in Seminars
    TL-16485   +   Converted Hierarchies CSS to LESS
    TL-16383   +   Converted Dynamic audience CSS to LESS

                   This will require CSS to be regenerated for themes that use LESS
                   inheritance

    TL-16487   +   Standardised HTML in the Statistics block
    TL-16488   +   Set alert notifications for Totara Messages to enabled by default
    TL-16489   +   Standardised HTML in the Alerts block
    TL-16497   +   Updated the Quicklinks block to use standard HTML elements
    TL-16503   +   Improved the HTML markup consistency within the Tasks block
    TL-16505   +   Updated the Report table block to use LESS instead of CSS
    TL-16506   +   Updated the Report graph block to use LESS instead of CSS
    TL-16508   +   Standardised HTML and CSS in the my learning navigation block
    TL-16509   +   Dashboard block now uses LESS instead of CSS and standard HTML
    TL-16520   +   Added tags functionality to programs and certifications
    TL-16524   +   Fixed redirect after a user confirms new account creation via email-based self registration

                   If a user clicks a link to a page within Totara but is not logged in they
                   are redirected to the login page. If a user then creates a new account via
                   email-based self registration they are redirected to the home page after
                   confirming account. This patch ensures they are redirected back to the page
                   they originally requested.

    TL-16622       Mustache string helper now accepts a variable for the string key

                   Previously when using the string helper in a mustache template, the key for
                   the string needed to be known when creating the template. This improvement
                   allows the key for the string to be added as a parameter for the template.

    TL-16627       A user's current course completion record can now be deleted

                   Using the course completion editor, it is now possible to delete a user's
                   current course completion record. This is only possible if the user is no
                   longer assigned to the course.

    TL-16632       Admin categories are no longer links by default

                   If you want to change this you can do so by searching for
                   linkadmincategories in the site administration block.

    TL-16651   +   Added support for context variables in modal library
    TL-16694       All SCORM reports were altered to use recommended enrolment subquery for listing of users

                   Please note this patch may change the results of scorm reports, only
                   enrolled users with mod/scorm:savetrack capability are now displayed there.

    TL-16696   +   Added email footer string with context URL to alert messages

                   Some system alerts were missing URL to page with relevant details of the
                   event. Now they are added in the message footer (when message is displayed
                   in HTML format).

    TL-16746   +   Added support for help icons next to checkboxes options
    TL-16867   +   Added password expiration settings to accounts created via Self-registration with approval
    TL-16919   +   Added profile locking options to "Self-registration with approval" plugin

Performance improvements:

    TL-14071   +   Replaced calls to 'dirname' with '__DIR__' to improve performance
    TL-16061       Fixed a problem where duplicating a module caused the course cache to be rebuilt twice
    TL-16161       Reduced load times for the course and category management page when using audience visibility
    TL-16189       Moved audience learning plan creation from immediate execution onto adhoc task.

                   Before this change, when learning plans were created via an audience, they
                   would be created immediately. This change moves the plan creation to an
                   adhoc task that is executed on the next cron run. This reduces any risk of
                   database problems and the task failing.

    TL-16314       Wrapped the Report builder create cache query in a transaction to relax locks on tables during cache regeneration in MySQL

                   Report Builder uses CREATE TABLE SELECT query to database in order to
                   generate cache which might take long time to execute for big data sets.

                   In MySQL this query by default is executed in REPEATABLE READ isolation
                   level and might lock certain tables included in the query. This leads to
                   reduced performance, timeouts, and deadlocks of other areas that use same
                   tables.

                   To improve performance and avoid deadlocks this query is now wrapped into
                   transaction, which will set READ COMMITTED isolation level and relax locks
                   during cache generation.

                   This will have no effect in other database engines.

    TL-16437       Changed column type from text to char in block_totara_featured_links_tiles table

API changes:

    TL-15798   +   Report Builder filters can now have default values

                   Default values for a filter are now an option when defining embedded
                   reports or when defining the default reports through the
                   define_defaultfilters method.

                   The only thing that needs to be added is the defaultvalue option as an
                   array with the corresponding filter options.
                   Please note that values are saved when creating the reports which usually
                   happens at installation time.

                   For a real example please check the "rb_system_browse_users_embedded"
                   embedded report which "User Status" filter is now set to "Active users
                   only".

    TL-16217   +   Removed deprecated custom menu functionality

                   Please use Site administration > Appearance > Main menu instead

    TL-16378   +   Hub functionality has been deprecated

                   Community hub functionality has been deprecated in this release, and will
                   be removed altogether in the next major release.

                   The links to the community hub registration and the publish course page
                   have been removed. The pages can still be accessed directly
                   ('/admin/registration/index.php' and
                   '/course/publish/index.php?id=COURSEID'). The block 'Community finder' will
                   still be visible after upgrading an existing Totara Learn 11 installation.
                   On a fresh installation however the block will be deactivated by default.
                   There is the option to reactivate the block in the administration
                   interface.

    TL-16448   +   Report Builder transformation display names are now collected through a method

                   Previously Report Builder transformations were expected to have a string
                   within totara_core.
                   The string used for transformations is now fetched through a method that
                   can be overridden by the transformation.
                   This allows strings to be co-located with their translations, and no longer
                   requires non-core developers to make core changes when introducing
                   transformations.

    TL-16745   +   Imported Font Awesome 4.7.0
    TL-16525       Fixed linting errors when copying Basis to create another theme

                   Themes that were copied prior to this issue being resolved will need to
                   adjust both theme/<themename>/bootswatch/bootswatch.less
                   and theme/<themename>/bootswatch/variables.less to conform with lint rules
                   (these have been updated in basis to pass lint rules)

    TL-16677   +   Removed deprecated rb_display_* functions

Contributions:

    * Barry Oosthuizen at Learning Pool - TL-9277
    * Dmitrii Metelkin at Catalyst AU - TL-16448
    * Eugene Venter at Catalyst NZ - TL-16524, TL-16696

*/
