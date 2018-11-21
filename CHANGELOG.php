<?php
/*

Totara Learn Changelog

Release candidate 1 (22nd November 2018):
=========================================

Key:           + Evergreen only

Important:

    TL-18925   +   Workflow API changes

                   This commit makes the following API changes:
                    * Workflow files must now include manager component in their path
                    * Workflow types no longer define an abstract workflow class to be
                      extended - workflows now extend the base workflow directly
                    * $workflow->get_manager_class() is now final instead of abstract
                    * $workflow_manager->get_all_workflow_classes() is now final instead of
                      abstract
                    * Workflow settings moved from totara/workflow to component which defined
                      the manager
                    * Structure of workflow settings has changed from using class name as
                      setting name to a comma separated list of enabled workflows per workflow
                      manager
                    * $workflow->split_classname() now also returns $managercomponent and the
                      order of variables in the returned array has changed
                    * New final methods for checking and setting workflow settings created
                      within base workflow manager class

Security issues:

    TL-19028       SCORM package download protection is now on by default

                   Previously this setting was off by default.
                   Turning it on ensures that sites are more secure by default.

    TL-19365       CSRF protection was added to the login page, and HTML blocks on user pages now prevent self-XSS

                   Cross-site request forgery is now prevented on the login page. This means
                   that alternate login pages cannot be supported anymore and as such this
                   feature was deprecated. The change may also interfere with incorrectly
                   designed custom authentication plugins.

                   Previously configured alternate login pages would not work after upgrade;
                   if attempting to log in on the alternate page, users would be directed to
                   the regular login page and presented with an error message asking them to
                   retry log in, where it will be successful. To keep using vulnerable
                   alternate login pages, the administrator would need to disable CSRF
                   protection on the login page in config.php.

New features:

    TL-17752   +   New course, program and certification catalogue

                   Implemented a new modern media-rich catalogue focused on improving user
                   experience while browsing for content.

                   The new catalogue is intended as a replacement for the 'Enhanced catalogue'
                   which has been renamed 'Report based catalogue'.

                   Improvements include:
                    * One area to search for courses, programs and certifications
                    * Ability to search learning items by tile or list views
                    * Flexibility for administrators to configure display of different metadata
                    * Ability to show icons related to the learning item
                    * Ability to show learning item images
                    * Ability to search by tags
                    * Ability to promote recommended training
                    * Search beyond title and description using tags, metadata, summary, etc
                    * Ability to share the url of a search criteria

                   Please note that after upgrading cron must be run in order to populate the
                   catalogue.

    TL-17941   +   New administration menu

                   The new quick access menu is a replacement for the old Site Administration
                   menu and is customisable for each user. The menu will only be available if
                   a user has capabilities to perform one or more administration tasks.

Improvements:

    TL-5964    +   Added settings to seminars that improve the control over multiple signups

                   This change introduces three new settings to both the settings form and the
                   activity defaults admin page for seminars. These new settings are:

                   1)  How many times the user can sign-up? - This setting replaces the old
                   'multiple signups enabled', it allows you to choose values between 1-10 or
                   unlimited. To maintain current behaviour for existing sites, they will have
                   this set to 1 if 'multiple signups enabled' was not ticked, or unlimited if
                   it was ticket. Note: cancelled or declined sign-ups are not considered as
                   part of this setting, neither are sign-ups that have been archived by
                   certifications.

                   2) Restrict subsequent sign-ups to - This setting restricts subsequent
                   sign-ups to the seminar based on the state of the current sign-up, the
                   options are the attendance states 'fully attended', 'partially attended',
                   and 'no show'. Selecting any of these options will restrict users to a
                   single concurrent sign-up, until the attendance has been taken for that
                   event. Not selecting any of these options will allow users to have as many
                   concurrent sign-up as they want, up to the limit specified by the setting
                   above.

                   3) Clear expired waitlists - If enabled waitlisted sign-ups to seminar
                   events will be cancelled by a cron task after the event has begun, allowing
                   those users to sign up for another seminar event. Along with this setting
                   there is also a new notification added to seminars, the 'Waitlisted sign-up
                   expired' notification. This can be used to inform users that their sign-up
                   has been automatically cancelled, and prompt them to go and sign-up to
                   another event.

    TL-7918    +   Added a new dynamic audience rule for user's certification status
    TL-10852   +   Improved footer appearance to fill bottom of the page

                   This will require CSS to be regenerated for themes that use LESS
                   inheritance.

    TL-12253   +   Removed completionstartonenrol setting from course settings screen
    TL-17872   +   Add audience based content restriction
    TL-18686   +   Optimised the performance of dynamic audiences

                   With this patch, the scheduled task (Dynamic Audiences update) is now
                   sorting audiences in order of their dependencies on other audiences.
                   Audiences that depend on other audiences will be updated after their
                   dependencies updates.

                   This allows faster and more consistent propagation of audience changes
                   (ideally in one task run).

    TL-18728   +   Included a loading UI for "add block" when an option has been selected

                   The "add block" popover now provides a visual clue that something is
                   happening once an option has been selected. Previously on slow renders it
                   wasn't clear any action was happening in the background.

    TL-18840   +   Added a new dynamic audience rule for user's certification completion date
    TL-18931   +   Improved the behaviour of filters in the content marketplace

                   Behaviour of content marketplace filters was improved by adding support for
                   searching within faceted filters, making the loading state more obvious as
                   well as improving the behaviour when making multiple rapid changes.

    TL-18963       Improved the help text for the 'Enable messaging system' setting on the advanced settings page
    TL-18995   +   Added a new block to link administrators of new sites to the Totara Community
    TL-19002   +   Changed the legacy programs/certifications catalogue UI to be consistent with course catalogue as a model

                   Changes are made for the legacy programs/certifications catalogue UI (it
                   uses one base code) to be consistent with course catalogue as a model
                   when enhanced catalogue is disabled
                    # Search box is moved to the top-left of the catalogue page
                    # Added 16px margin-bottom space for the top-left search box
                    # Search box label is removed
                    # The "Add new program/certification" button is moved to center of the page
                    # Course/program/certification titles font is changed from H3 to standard font
                    # Programs/Certifications dropdown box with the categories/sub-categories
                      options is moved to the right of the page
                    # Fixed program/certifications breadcrumbs
                    # Fixed if program has any associated overview files
                    # Fixed behat test after new UI applied



    TL-19045   +   Centered login panel vertically
    TL-19098   +   Automatic report builder data grouping was deprecated and affected report sources were rewritten to use subqueries
    TL-19108   +   Removed unnecessary API calls within the GO1 content marketplace plugin
    TL-19109   +   Removed course events from GO1 content marketplace

                   Previously the GO1 content marketplace included one tile per event for
                   courses with a date-based event. Those event items have now been filtered
                   out so each course only appears once in the catalog.

    TL-19111   +   Removed obsolete non-functional support for report builder report and source groups
    TL-19145       Improved terminology for non-graded assignment strings
    TL-19184   +   Improved the appearance of seminar's notification form to resolve the confusion of notification's recipients

                   Prior to this patch, on a creating new seminar's notification page, the
                   label 'All booked' within the recipients section was misaligned, causing
                   confusion.

                   After the patch, the label 'All booked' has been changed into 'All (past
                   and present booked)'.
                   Furthermore, there is an improvement on form's UI, in which the 'Booked
                   type' option is no longer a checkbox, but a selection element instead.

    TL-19188   +   Added optional help icon to search text component

                   Allowed for an optional help icon to be included before the search input.
                   A pop-over dialogue is displayed when clicking the icon.

    TL-19264   +   Switched to using standardised URL querystring parameters for the multi select component
    TL-19288   +   Increased z-index of YUI dialogs to match other dialogs
    TL-19322   +   Added additional UX options to the select tree component

                   Extended the select tree component to also support the following features:

                   A select tree can be provided a call to action string value (e.g. 'Please
                   select an option...' ) which isn't included in the select list & doesn't
                   provide a value. This is an alternative to the default value.

                   A select option with child nodes can either be:
                   * A clickable link itself which provides a selected value
                   * A click target for expanding/collapsing child nodes which provides no
                     selected value

    TL-19476   +   Added custom field 'created' and 'updated' events

                   These new events are also observed by the new catalogue in order to update
                   the search indexes when new fields are added, or existing fields are
                   updated.

Bug fixes:

    TL-16529       Fixed Global Search to accept the parameter type of either 'string' or 'array'

                   Prior to this patch: when user was trying to perform global search, the
                   system would throw an error. It happened because the query from request was
                   a string instead of an array and the global search handler was expecting
                   array data type only.

                   After this patch: the issue has been resolved, global search handler is now
                   accepting either 'string' or 'array' parameter.

    TL-16788       Fixed audience visible learning report's javascript

                   Prior to this patch, with a report using source 'Audience: visible
                   learning', when changing the visibility of an audience, the system would
                   update nothing. This happened because the javascript for the report was
                   looking into the wrong elements and it would not trigger any update to the
                   server side when event triggered.

                   With this patch, given the same scenario, audience visibility of
                   course/program will be updated.

    TL-17804       Fixed certification expiry date not being updated when a user is granted an extension
    TL-18558       Fixed display activity restrictions for editing teachers.

                   Editing teachers can see activity restrictions whether they match them or
                   not.

    TL-18806       Prevented prog_write_completion from being used with certification data
    TL-18821       Fixed the rendering of course's topic restriction when using the 'Restriction Set'
    TL-18895       Added warning text to the audience's rules if there are any rules that are referencing a deleted item

                   Prior to the patch: when an item (for example: program, course, position
                   and so on) that was referenced in an audience rule got deleted, there were
                   no obvious way to tell the user that this item had been deleted.

                   With this patch: there will be a warning text, when user is viewing the
                   rule that is still referencing a deleted item.

    TL-18932       Added an ability to detect the broken audience rules when scheduled task starts running to update the audience's members

                   Prior to this patch, when the scheduled task
                   (\totara_cohort\task\update_cohort_task) was running, there was no way that
                   it could detect whether the rules were still referencing to the invalid
                   instance records or not (for example: course, program, user's position, and
                   so on). Therefore, if the rule had a reference to an invalid instance
                   record, audience will not be able update its members correctly.

                   With this patch, it will start checking whether the referenced instance
                   records are valid or not before the process of updating members. If there
                   are any invalid instance records, then the system will send an email out to
                   notify the site administrator.

    TL-19000       Fixed task block for Seminar event approver roles
    TL-19026   +   Changed the date format of Seminar report builder Dates and Times related columns report source

                   Previously the report columns 'Event created', 'Last Updated', 'Sign-up
                   Period', 'Sign-up Start Date', 'Sign-up End Date', 'Cancellation date',
                   'Time of sign-up', 'Event Start time', 'Event finish time' and 'Approval
                   time' were formatted differently than the 'Session Start' and 'Session
                   Finish' columns. These columns are now formatted consistently.

    TL-19122       Fixed an issue in the recurring courses where after the course restarts the enrolment date remained the date from the original course
    TL-19129   +   Reduced space between Totara menu & page content
    TL-19149       Made sure completion editor form is submitted correctly when the site is running non-English language
    TL-19157   +   Removed popper.js source map path

                   The popper.js library included a path to a non-existent source map which
                   caused a warning message in the browser console.

    TL-19158       Fixed 'Hide/Show' actions on the course/program custom fields page
    TL-19160       Clarified date filter label that 'today' means 'start of today'
    TL-19190       Fixed duplicate rows in the Program Completion report when "Is user assigned?" column is included
    TL-19215       Improved handling of text in autocomplete forms

                   Previously when adding HTML tags to an autocomplete field, they would be
                   interpreted by the browser. This issue ensures that they are displayed as
                   plain text, with offending content being removed when the form being
                   reloaded.

                   This is not a security fix as the only person who could be affected is the
                   person who is entering the data, when they are first entering the data (and
                   not on subsequent visits).

    TL-19247       Fixed race condition when adding programs to the program completion block
    TL-19248       Report builder filters supply the report id when changing

                   Previously there were some filters that did not supply the report id when
                   changing the filter. This issue ensures the access checks are done
                   correctly for the report

    TL-19249       Fixed cancel button not working in switch role form in course

                   Previously the cancel button had the same functionality as the 'Save
                   changes' button, changing the users role.

                   With this patch, the cancel button now just redirects back to the course
                   view page.

    TL-19250       Fixed Totara forms file manager element with disabled subdirectories bug when uploading one file only
    TL-19256       Ensured enrolment messages are send correctly after user assignment exceptions have been resolved
    TL-19297       Fixed errors when changing course format to different format on course's editing page
    TL-19302   +   Navigation on audiences pages is now consistent across them all

                   Multilang support was fixed on all pages at the same time.

    TL-19325   +   Fixed enabling/disabling antivirus plugins
    TL-19328   +   Custom assets can once again be published making them available to all Seminar events
    TL-19334   +   Removed unused coursetagging admin setting

                   Course tagging has been controlled since the general enable tags setting as
                   of Totara 9.0.
                   The setting was missed in the clean up and remained in the product but did
                   nothing.
                   It has now been removed.

    TL-19350   +   Fixed an issue with hierarchy field mapping in HR Import
    TL-19374       Removed a trailing space on the output of the certif_status Report Builder display
    TL-19439       Fixed select all checkbox not working in comments report in IE11/Edge
    TL-19462   +   The actions column in the GO1 marketplace interface is no longer shown when marketplaces are disabled
    TL-19472       Fixed temporary manager expiry checkbox not being unchecked when temporary manager removed
    TL-19495       Ensured the course shortname and category fields export correctly on the 'Program overview' Report Builder source

API changes:

    TL-16726   +   Refactored Report builder initialisation

                   More information will be provided in the Totara 12 changelogs

Miscellaneous Moodle fixes:

    TL-19387   +   MDL-63050: Made session check compatible with Redis 4.0
    TL-19392   +   MDL-63101: Improved accuracy of cache event invalidation
    TL-19396   +   MDL-62880: Dropped support for legacy question import format
    TL-19399   +   MDL-62497: Protect against QuickForm remote code execution

                   This vulnerability had already been fixed in a previous Totara patch (see
                   TL-18491 from previous releases of Totara).

                   An additional fix was added from this set of Moodle fixes which ensures
                   that the Feedback module uses the QuickForm API correctly and safely,
                   making sure that type checking of values is done as specified.

Contributions:

    * Jo Jones at Kineo UK - TL-18686
    * Joby Harding at 77 Gears Ltd - TL-10852, TL-19045
    * Michael D at Androgogic - TL-18931


Release Evergreen (25th October 2018):
======================================

Key:           + Evergreen only

Security issues:

    TL-18957       Fixed permission checks for learning plans

                   Prior to this patch all plan templates were being checked to see if a user
                   had a permission (e.g. update plan). Now only the template that the plan is
                   based off is checked for the permission.

New features:

    TL-16649   +   Added reusable select and region UI components

                   The new select components are:
                    * Multi select - Similar to a multiple select and can return multiple options
                    * Single select tree - Similar to a single select dropdown that allows nestable options
                    * Text search - A stylised text input field with search icon

                   These are designed for use inside the added region container which has
                   'clear all' functionality. Initially these will be used in the new
                   catalogue.

    TL-17603   +   Added reusable UI grid component

                   Added a reusable UI component for displaying content in a grid format. The
                   component includes events for setting an active tile state based on user
                   clicks.

    TL-19014       Implemented new capabilities for controlling the access to SCORM content

                   Previously all users who could enter a course were able to launch SCORM
                   activities.
                   The only way to limit access was to make the activity hidden and then to
                   use the moodle/course:viewhiddenactivities capability to grant access.

                   Two new capabilities have been added to allow better control of access to
                   SCORM activities.
                    * mod/scorm:view
                    * mod/scorm:launch

Improvements:

    TL-6152    +   Added an RPL note column to the Course completion report source

                   A new column "RPL note" has been added to the Course completion report
                   source.
                   This column contains the note provided when users were manually awarded an
                   RPL completion.
                   If it is not an RPL completion, or if no note was provided then the column
                   will be empty.
                   The new column was added to the course completion report source only.

    TL-8092    +   Added a 'Date Completed' filter to the program overview report source
    TL-12955   +   Added a dynamic audience rule for user's authentication method
    TL-17580   +   Refactored and simplified the Flex icon AMD JavaScript module
    TL-17586       Greatly improved the performance of the update competencies scheduled task

                   The scheduled task to reaggregate the competencies
                   "\totara_hierarchy\task\update_competencies_task" was refactored to fix a
                   memory leak. The scheduled task now loops through the users and loads and
                   reaggregates items per user and not in one huge query as before. This
                   minimises impact on memory but increases number of queries and runtime.

    TL-17719   +   Converted front page content to use the new centre block region

                   The following blocks have been introduced for backward compatibility:
                    * Course progress report
                    * Courses and categories
                    * Course search

                   "Course progress report" and "Courses and categories" blocks are disabled
                   by default in new installations, and only enabled on upgrade if the
                   respective front page content settings were enabled.

    TL-17835   +   Improved calendar popover

                   Previously this was using a YUI module. This has now been updated to use
                   the Bootstrap popover.

    TL-17850   +   Improved colour of text input placeholders in Totara forms
    TL-17974   +   Site-wide settings for HR Import can now be overridden by element

                   The HR Import page for 'General settings' has been renamed to 'Default
                   settings'. This page includes the same settings as previously, but will
                   also list which elements are using a given setting area.

                   Element setting pages now contain settings relating to file access,
                   notifications and scheduling. These settings allow you to select the
                   default settings to apply or to override them with values that will apply
                   to that element.

                   Following the upgrade, values from 'General settings' will remain unchanged
                   in the 'Default settings' page. Any enabled elements will use the default
                   settings until changed.

    TL-18554   +   Introduced common block settings and API to manage those

                   The idea of the common block settings API is to allow core developers to
                   have predictable common settings storage for all the blocks and if
                   necessary,  introduce properties which cover all block types without
                   interfering with settings provided the by third-party block developers.
                   It also includes a few minor changes for block configuration: hiding,
                   docking and show header/border settings now use checkboxes instead of radio
                   buttons. Moreover, to provide better backwards compatibility a setting
                   "Override default block title" has been introduced and unless it is checked
                   the block retains pre-patch behaviour for the title supplied by the block
                   developer.

    TL-18565       Moved 'Override user conflicts' action out of the seminar event setting page and into a 'save' modal dialog

                   The 'Override user scheduling conflicts' setting was initially intended for
                   use with new events where the assigned roles resulted in conflicts with
                   existing events. It was not originally designed to work with existing
                   events.
                   We improved the wording to clarify this feature without
                   further changes in the UI and workflow.

    TL-18575   +   A limitation of 255 characters is now consistently applied when validating course shortname

                   The course shortname field in the database has always been 255 characters.
                   However the course creation form arbitrarily limited course shortname
                   length to 100 characters.
                   As of this change the course shortname form now checks that the
                   user-entered value is no longer than 255 characters, matching the database
                   limitation.

    TL-18666   +   Improved AMD module loading by converting the core/first AMD module to use RequireJS bundling instead
    TL-18713   +   Reduced space between the main navigation and blocks when there are no breadcrumbs
    TL-18757       Send notifications to new appraisees for an already activated appraisal

                   Previously the appraisals module only sent out notifications to learners
                   when the appraisal was activated. If new learners are added to the
                   appraisal after activation, they did not receive any notification.

                   With this patch, notifications are sent out when new learners are added to
                   the appraisal after activation.

    TL-18770       Disabled the site policy translation interface language selector when only a single language is available
    TL-18852       Database table prefix is now required for all new installations

                   Previously MySQL did not require database prefix to be set in config.php,
                   since MySQL 8.0 the prefix is however required. To prevent problems in
                   future upgrades Totara now requires table prefix for all databases.

    TL-18896   +   Date pickers in forms now use the same order of day, month and year fields as current language full date and time display format
    TL-18909       Fixed compatibility issues with PHP 7.3RC1
    TL-18929   +   Added two indexes to speed up queries accessing the block_totara_stats table

                   In quite a few places throughout the code we query the table
                   'block_totara_stats' using two combinations of columns. In adding indexes
                   on these column combinations query speed will be improved, especially with
                   a lot of entries in the table.

    TL-18978   +   Improved the validation display for dynamic audience rules that use a date selector
    TL-18983       Added workaround for missing support for PDF embedding on iOS devices

                   Web browsers on iOS devices have very limited support for embedding PDF
                   files – for example, only the first page is displayed and users cannot
                   scroll to next page. A new workaround was added to PDF embedding in File
                   resource to allow iPhone and iPad users to open a PDF in full-screen mode
                   after clicking on an embedded PDF.

    TL-18998       Improved performance of language pack installation by changing to gzip

                   Language pack installation and updates now utilise gzip instead of zip.
                   Extract of gzip files is much quicker than zip files within Totara.
                   Manual installation and updates using zip files are still supported and
                   will continue to operate.
                   All online installations and updates will now use tgz files exclusively.

    TL-19053   +   Improved the performance of full text searches within PostgreSQL
    TL-19066   +   Database table context_temp is now a  real temporary table

                   The original context_temp table has now been dropped.
                   This table was only ever intended as an internal store, and should not have
                   been used by anything other than the access API.

    TL-19084       Enrolment type column in course completion report source is now using subqueries to improve compatibility of other general columns in the same report

Bug fixes:

    TL-14204       Updated the inline helper text for course completion tracking

                   Prior to this patch, there was a misleading inline helper text on the
                   course view page next to 'Your progress'.
                   With this patch, the inline helper text is updated to reflect with the
                   change of the completion icon.

    TL-16539       Fixed capacity reporting when viewing Seminar event information on the course page

                   Previously a wait-list seminar event with 1 booked user and 1 wait-listed
                   user reported the capacity wrongly as '2 wait-listed'.
                   With this patch, the capacity is now reported correctly.

    TL-17584       Fixed the default heading location for Featured links block gallery tiles

                   Heading location for Gallery tiles now defaults to 'Top' like the default
                   tile. Any tiles created without setting the heading location will be set to
                   'Top'.

    TL-17629       Fixed failures in the Seminar send_notification_task when performed under high load

                   Some sites with large number of Seminar activities (100 000+) experienced
                   'out of memory' failures during execution of the scheduled task
                   (send_notifications_task). This task has now been optimised to use less
                   memory.

    TL-17652   +   Removed 'Update activities' checkbox from Seminar notification template form when new customer notification template is added
    TL-17658       MSSQL 2016 and below now correctly sort aggregated course columns in the program overview report

                   The program overview report was using SQL group_concat to ensure
                   concatenated columns such as course name, and course status were ordered
                   correctly and consistently.
                   However the group_concat functionality in MSSQL 2016 and below does not
                   support sorting, and there is no alternative.
                   The fix for this was to shift sorting from the database to Totara if the
                   site is running on MSSQL 2016 or below.
                   This will have a small impact on performance, but will ensure for those
                   sites that the columns are correctly and consistently sorted.
                   Our recommendation is to upgrade MSSQL 2017 is possible.

    TL-17773       Fixed the rendering of visibility controls within the course management interface for hidden categories

                   The issue happened within 'Manage courses and categories' page alongside
                   the enabled setting 'Audience visibility'.

                   When rendering the page, the course checks for the setting 'Audience
                   visible' (global) before 'Visible' (module) setting, to determine whether
                   the 'Eye Icon' should be marked as hidden or not.

                   Previously, when the 'Course category' was marked as hidden, all the
                   courses within that category were also marked as hidden. However, after
                   reloading the page, these courses were not marked as hidden. This was due
                   to the fact that the same behaviour of rendering the page was not applied
                   to AJAX interface.

                   With this the patch, the behaviour applied when rendering the page is now
                   also applied to the AJAX interface and therefore results in the same
                   behaviour.

    TL-17919   +   Fixed the display of the main region in core themes
    TL-18706   +   Fixed the incompatible version message shown when attempting to restore an old backup

                   The "This backup file has been created with Totara ..." error message was
                   incorrectly referring to Moodle version instated of Totara version

    TL-18727   +   Fixed galleries in the featured links block not being reinstated after update
    TL-18776       Fixed a bug causing the Atto editor to lose track of the user's selection in IE11 and Edge

                   Prior this change if heavily editing content in the Atto editor will
                   occasionally result in the wrong content being formatted.
                   This occurred only when formatting selected text, and occurred because the
                   browser would lose track of the user's selection.
                   This only affected IE11 and Edge.

    TL-18790       Fixed the Organisation content restriction within the 'Record of Learning: Certifications' report source

                   Before: within a report using 'Record of Learning: Certifications' source
                   and content restriction as 'Staff at or below any of the user's assigned
                   organisations', the User's Organisation(s) filter had an issue with its SQL
                   query.

                   After the patch: this issue is now fixed, the Organisations will display,
                   if there are any.

    TL-18802       Changed the date format of Session Date related columns within Seminar Sign-ups report source

                   Previously the report columns 'Session Start' and 'Session Finish' were
                   formatted differently than the 'Session Start (linked to activity)' column.
                   These columns are now formatted consistently.

    TL-18839       The 'Blocks editing on' button has been put back onto the 'Browse list of users' report page

                   Prior to this page being converted to an embedded report it had a button to
                   turn editing on.
                   That button was unintentionally removed during the conversion.
                   It has now been put back.

    TL-18846       User's preference 'email bounce count' is reset when user requests to change their email address

                   Prior to this patch, when the user requested to change their email address
                   and the user's email bounce count preference reached the threshold, the
                   confirmation email could not be sent to the user.

                   With this patch, given the same scenario, the email will be sent to the
                   user for the confirmation of change request.

    TL-18864       Fixed the population of the template field value when editing a Seminar's notification

                   Prior to this patch, when adding a new notification using a custom
                   notification template the user was not able to see the Template field
                   populated when editing it. Now, the field Template will be populated with
                   the right value used for the notification.

    TL-18866       Fixed the way the add-on list is displayed on the Totara registration page

                   Prior to this patch, on the Totara registration page, all the add-on
                   components were rendered without spaces separating them, preventing the
                   text from wrapping and forcing the need for horizontal scrolling in the
                   browser.

                   After the patch, there is a word wrap in place to make the text fit on the
                   screen.

    TL-18867       Fixed exported status of cancelled events in the Seminar attendance report
    TL-18880       Fixed Seminar 'Job assignment on sign up' column to exclude html text when exporting to other format
    TL-18887       Fixed resetting of course type when uploading courses using a CSV file that does not contain the column

                   This is a regression from TL-17920 which added Course type as a supported
                   column when uploading courses via CSV.
                   Totara 10.12, 10.13, 11.6, and 11.7 are affected.

    TL-18897       Added a link on Appraisal stage interfaces for navigation back to the Appraisal
    TL-18904   +   Fixed up the context level of the totara/contentmarketplace:add capability

                   It now shares the same configuration as the moodle/course:create
                   capability.

                   Coding style within the component and single plugin was tidied up at the
                   same time.

    TL-18908       Fixed window resize functionality when viewing the grader report
    TL-18922       Fixed the overlapping text within Select Assets dialog box
    TL-18941       Changed z-index of Totara dialogs to match Bootstrap 3 modal levels

                   Previously the modal had a z-index of 1 (and the backdrop 0) which caused
                   some content to be displayed above them. This sets the level to 1050 (with
                   the backdrop at 1040).

                   This will require CSS to be regenerated for themes that use LESS
                   inheritance.

    TL-18942       Fixed the email subject line format for emails sent from the Certificate module

                   Prior to this change the subject lines used for emails sent by the
                   Certificate module were being formatted for HTML, not for emails.
                   This led to characters being escaped and converted to entities.
                   As of this fix the subject is formatted correctly, and should no longer end
                   up with HTML entities within it.

    TL-18959       Fixed the double escaping of Report names when viewing the list of my reports

                   The report name was being passed once through format_string() and once
                   through s() when displaying the list of reports to the user on the My
                   reports page.
                   The fix for this change involved modifying templates, see
                   totara/core/upgrade.txt for technical details.

    TL-18965       Changed the embedded URL of User's name within Seminar's event Cancellation
    TL-19009       Fixed incorrectly deprecated filter language strings on "Bulk user actions" page

                   Strings datelabelisafter, datelabelisbefore, datelabelisbetween were
                   deprecated in Totara 10.0 while still being used on the "Bulk user
                   actions" page. This has now been corrected and these strings were removed
                   from the deprecated list.

    TL-19010       Removed incorrectly deprecated function message_page_type_list() from the deprecatedlib.php
    TL-19017       Added styles to display Appraisal content correctly on mobile devices

                   Added styles for correctly displaying an individual Appraisal selected from
                   the 'My appraisals' page on mobile devices. Previously content had layout
                   issues and was incorrectly cropped.

    TL-19018       Fixed problems with forced redirect for new required custom profile fields
    TL-19030       Fixed the duplicate submit request for the course page when enroling

                   Double clicking the Enrol button on courses with self enrolment enabled no
                   longer submits duplicate requests.

    TL-19043   +   Fixed php undefined property notice in assignment grading when changing 'Enrolment ends' to a date in the past
    TL-19046       Course completion's cache is now being cleared after user deletes the course completion

                   Prior to this patch, when a manager with the
                   'totara/program:markstaffcoursecomplete' capability, marked a course as a
                   incomplete on a user's 'Record of Learning > Programs' page, the cache
                   would not be fully cleared. Causing the page to still render with the
                   course marked as complete.
                   With this patch, given the same scenario, the course completion cache of a
                   user will be reset when the manager removes the course completion for that
                   course. This will result in the completion being rendered correctly.

    TL-19072       Fixed wait-listed attendees not being automatically added to the Seminar's attendees list after a reservation is deleted

API changes:

    TL-6630    +   Added functionality to perform capability checks directly against the database

                   A new get_has_capability_sql() function has been introduced that returns an
                   SQL snippet to resolve capability checks against the database.
                   Among other uses this allows Totara to resolve visibility state much more
                   efficiently than before without sacrificing accuracy.

                   As part of this change a new table containing flattened context data will
                   be created and maintained.
                   There are a couple of important things to note about this:

                   During upgrade to this release the table will be created and populated.
                   This upgrade step could take several minutes on large sites.
                   The table is kept up-to-date automatically by the access API. If you have
                   third party plugins or customisations that are directly manipulating access
                   data then you will need to review these.
                   We have extensively tested the performance of this change during our QA
                   process and are confident with the results. If you experience any problems
                   please let us know immediately.

    TL-15818   +   Refactored Seminar code to allow multi-language notifications and consistent booking state processing

                   Multi-language:
                   Added support for the "Multi-Language Content" filter plugin in Seminar
                   notifications. Notification content will now be filtered according to each
                   recipient's language settings.

                   Booking system:
                   The main target of refactoring was to bring consistency to the bookings
                   state changes throughout all related code, leading to predictable and
                   controllable rules for each state transition. For this purpose we have
                   implemented a simplified Finite State Machine with a definition for each
                   state, following states and rules that must be matched for state transition
                   to happen. This will greatly reduce complexity during further changes to
                   how booking states are managed.

                   Despite our efforts to maintain existing behaviour, some inconsistencies in
                   old code forced some minor changes in behaviour. We have identified the
                   following changes:

                   1) Enable waitlist and overbooking - Previously when a Seminar's event had
                   the setting 'Enable Waitlist' enabled, then all the attendees that got
                   signed up by an admin or any user that has capability would have a status
                   as booked. Now users will be booked until the event's room capacity has
                   been reached, the rest of the users will be added to the waitlist. Later on
                   an admin or another user with the "mod/facetoface:signupwaitlist"
                   capability will be able to confirm users on the waitlist, overbooking the
                   event.

                   2) Events without session - Administrators could previously book users onto
                   events without sessions by confirming users on the waitlist. Now as the
                   booked state requires a session to be set, this attempt will return error
                   until a session is created.

                   3) Action buttons labels -  Removed some inconsistencies with "Sign-up",
                   "Join waitlist" buttons and added "Request approval" when approval is
                   required. Previously calendar and upcoming events block would display a
                   "sign-up" button, while the sign-up page would offer "Join waitlist". These
                   inconsistencies were largely removed by using the same prediction logic for
                   all three source of actions (course view, calendar, and sign-up page).
                   Also, when approval is required, the user is now properly informed that
                   approval will be required.

                   API changes:
                   The API has been significantly changed. We have moved to a proper class
                   structure for all Seminar entities and their relationships. Along with that
                   we didn't change the database structure, except for some varchar fields
                   that were converted to text to allow the multi-language filter to work
                   properly. We have also minimised front-end changes as much as possible. All
                   functions that were likely to be used by third-party code have been kept in
                   the code base and deprecated. Deprecated functions from main lib.php file
                   were moved to deprecatedlib.php file (which is required by lib.php file).

                   In order to reduce API changes we've deprecated mostly functions that were
                   relevant to state machine (booking states), and functions that were
                   completely covered by OOP (e.g. rooms, assets, reservations, calendar).

    TL-18845       Removed a superfluous unique index on the job_assignment.id column
    TL-18899   +   Added additional PHPdocs and return value declarations in workflow classes

                   The following method signatures have been modified to enforce the expected
                   return types:
                    * workflow_manager\base->add_workflow_manager_form_elements() returns
                   void
                    * workflow_manager\base->split_classname() returns array
                    * workflow_manager\base->get_workflow() can no longer return null
                    * workflow_manager\base->set_params() returns void
                    * workflow\base->__construct() returns void
                    * workflow\base->split_classname() returns array
                    * workflow\base->add_workflow_form_elements() returns void
                    * workflow\base->define_form() returns void
                    * workflow\base->process_form() returns void
                    * workflow\base->enable() returns void
                    * workflow\base->disable() returns void
                    * workflow\base->set_params() returns void
                    * workflow\base->get_workflow_manager_data() returns array

                   3rd party workflow and workflow manager classes which extend the base
                   classes will need to update their message signatures, but behaviour is
                   unchanged.

                   In addition:
                    * workflow\base->get_form_name() is now final



    TL-18921   +   Removed the Memcache cache store from core

                   Not to be confused with the Memcached cache store.
                   The Memcache PHP extension is not compatible with PHP7, and as such the
                   Memcache cache store could not be used.
                   It has now been removed from core.

                   If you are currently using the Memcache cache store and plan to upgrade in
                   future, this may be an issue.

    TL-18927       Totara form load deferred object now resolves after the form initialise JavaScript is called

                   Previously, the Totara form initialise code was run after the load deferred
                   object had been resolved. This meant that calls to getFormInstance(formid)
                   would return null on load.done(), and not the form that was requested.

    TL-18944   +   MDL-53848: Added hideIf functionality to Moodle forms

                   Elements can now be hidden based on the value of another element. Usage
                   matches that of the disabledIf functionality that was already available in
                   the Moodle forms.

    TL-18985       Unit tests may now override lang strings


Release Evergreen (19th September 2018):
========================================

Key:           + Evergreen only

Important:

    TL-14270       Added additional information about plugins usage to registration system
    TL-18788       Added data about installed language packs into registration system
    TL-18789       Added data about number of active users in last 3 months to registration system

New features:

    TL-17426   +   Add Totara content marketplace and GO1 marketplace

                   Totara content marketplace provides support for browsing and importing
                   external content from content providers directly into your site.

                   Content providers can implement a new "marketplace" plugin type to
                   integrate their content into Totara Learn. The release includes a
                   marketplace plugin for GO1 ([https://totara.go1.com/]), which provides
                   direct access to search and include GO1 aggregated content.

                   When first installed the content marketplace plugin will send an internal
                   notification to site administrators and site managers on the next cron run,
                   letting them know that content marketplaces are available. To prevent this
                   notification and completely disable marketplaces add
                   $CFG->enablecontentmarketplaces = false; in your site's config.php *before*
                   you upgrade your site.

    TL-17475   +   Added support for pluggable course creation workflows

                   This patch adds support for general purpose, pluggable workflows which
                   provide an extensible way to provide different workflows for a specific
                   task.

                   The first workflow type to be implemented is the course creation workflow,
                   which provides a way to design custom workflows to collect information and
                   generate specific types of courses.

                   See here for developer documentation:

                   https://help.totaralearning.com/display/DEV/Workflows

Improvements:

    TL-11243       Removed ambiguity from the confirmation messages for Seminar booking requests
    TL-16728   +   Ensured all Report Builder columns have a display class defined

                   To improve Report Builder performance, all columns now need to define a
                   display class best suited to the data type being displayed. This reduces
                   unnecessary formatting.

                   A PHP Unit test is included to assert new columns have the 'displayfunc'
                   option defined.
                   Run 'vendor/bin/phpunit totara_reportbuilder_display_testcase
                   totara/reportbuilder/tests/display_test.php' to find any local
                   customisations that should be updated.

    TL-17130       Added consent statement filter for the Site policies report

                   This patch adds support for a consent statement filter for the Site
                   policies report as well as a few minor improvements to the site policy
                   filters including:
                    * Removing the filter Current Version (Primary Policy)
                    * Replacing plain text version filter to a smart dropdown menu, which
                      includes now the list of available versions as well as the option to select
                      current version of the policy
                    * Adding policy filter which allows you to filter only by policy
                    * Making user consent statement a simple filter
                    * Added custom help for consent statement filter
                    * Added custom help for policy version filter

                   Now to select the current version of the policy it is a matter of using 2
                   filters:
                    * Policy filter to select appropriate policy
                    * Version filter to select current version

                   Please note, that this patch will also remove Current Version (Primary
                   Policy) filter from any saved search using it.

    TL-17901   +   Hierarchy export improvements

                   Hierarchy export has been improved as follows:
                    * Competency items can now be exported in the same manner as any other
                      type of hierarchy
                    * The default export file format has been changed. By default the file
                      will now contain all item data allowing it to be used for re-import via HR
                      Import.
                      To revert back to the old hierarchical format (not suitable for HR Import),
                      add the following line to config.php:
                         $CFG->hierarchylegacyexport = 0;
                    * An option has been added to the Manage _<hierarchy>_ pages allowing the
                      user to export all items in all frameworks to a single file

    TL-17902   +   Added HR Import for competencies

                   Competencies can now be created, updated and deleted via HR Import.

                   Each competency must reference an existing framework via its ID Number.
                   Values for types and custom fields may also be imported for each
                   competency, providing these exist on the site that the import is run on.

    TL-18591   +   Added an index to the moduleinstance column of the course_completion_criteria database table
    TL-18596       Added a filter for the Number of Job Assignments for a user

                   A filter has been added for the Number of Job Assignments column and is
                   available in all report sources that include the Job Assignments filters.
                   This filter adds a way to filter users that have no Job Assignments.

    TL-18600   +   Import of custom field values allows for duplicate shortnames

                   When using HR Import to create and update positions or organisations,
                   custom field short names had to be unique across the site, despite the only
                   restriction in the UI being that they are unique within a given type. HR
                   Import now accounts for this configuration when importing custom fields for
                   hierarchies, such as position and organisation.

    TL-18601   +   Added 'type ID number' column to the 'Manage types' hierarchy tables to allow administrators
                   to have one place to go to to identify the available typeidnumbers
    TL-18639       Added support for custom help tooltips for Report Builder filters

                   When a report source is defined it is now possible to define a custom
                   filter option to override the default help tooltip for the given filter.

    TL-18646   +   HR Import allows HTML tags for fields where this is permitted

                   Fields such as descriptions or text area custom fields allow HTML tags when
                   a value is added via the interface. However, HR Import was stripping these
                   tags. Cleaning of these fields is now the same whether values are added via
                   the interface or HR Import, i.e. they retain their HTML tags.

    TL-18693   +   Fixed memory leaks in PHPUnit test by resetting properties in tearDown() method

                   Additionally this patch introduces a check in the advanced_testcase which
                   checks after each test for properties which weren't reset. It fails any
                   test where it finds unreset instance properties to prevent creating more
                   memory leaks in the future. There is an option to disable this check if
                   needed by setting the constant PHPUNIT_DISABLE_UNRESET_PROPERTIES_CHECK in
                   phpunit.xml.

    TL-18700       Backported MDL-54901 to add an environment check for https

                   If the site is not running on https the environment check now shows a
                   warning that it is not enabled. Installing the site is still possible
                   without https.

    TL-18709   +   Changed font size in header navigation from 16px to 14px
    TL-18712   +   The site logo link now takes the user to their default home page

                   Previously when the user clicked on the site logo they were taken to the
                   sites home page.
                   Now they are taken to their default home page, which may be the site home
                   page, or one of their dashboards.

    TL-18718   +   Added upgrade step to set new redis cache store settings 'test_password' and 'test_serializer'
                   to default values when not already set

                   In a previous patch new settings 'test_password' and 'test_serializer' for
                   the Redis Cache Store were introduced. If the site hasn't already been
                   upgraded to a version which includes these settings we set the password to
                   an empty string and the serializer to PHP's default value to ensure that
                   previous functionality works as before. These settings can still be changed
                   in the appropriate section of the Site Administration.

    TL-18777       Allowed plugins to have custom plugininfo class instead of just type class
    TL-18793       Improved display of course details in the course and categories management page

Bug fixes:

    TL-16532       Fixed caching of OpenSesame reports
    TL-17852   +   onchange Totara form actions now support comparing against arrays
    TL-18494       Fixed 'Bulk add attendees' results in Seminar to show ID Number instead of internal user ID
    TL-18549       Fixed 'Remove users' option showing in attendee actions for users without the removeattendees capability
    TL-18571       Fixed access rights bug when viewing goal questions in completed appraisals

                   If an appraisal has a goal question and the appraisal was completed, then
                   it is the current learner's manager who can see the goal question. However,
                   there was an issue when a learner and their manager completed the appraisal
                   but then a new manager was assigned to the learner. In this case, only the
                   old manager could see the completed appraisal but they could not see the
                   goal question because they didn't have the correct access rights. The new
                   manager could not see the completed appraisal at all.

                   This applies to static appraisals.

    TL-18578       Fixed missing required parameter when viewing 'Course membership' embedded report
    TL-18588       Prevented duplicate results when searching in Seminar dialogs

                   Seminar dialogs that provide search functionality (such as the rooms and
                   assets selectors) now ensure that search results are unique.

    TL-18602       Fixed Seminar's event decline emails to not include iCalendar attachments

                   When a booking approval request with a setting of email confirmation set as
                   'Email with iCalendar appointment' gets declined, then the iCalendar
                   attachment will not be included in the email sent back to the user who made
                   the request.

    TL-18680       Fixed the resetting of event data for each recipient of email notifications for under-capacity seminars
    TL-18682       Fixed the course name not appearing below the event time in calendar

                   This will require CSS to be regenerated for themes that use LESS
                   inheritance.

    TL-18685       Fixed the Seminar summary report visibility records when Audience-based visibility is enabled

                   When a course had audience-based visibility enabled and the course
                   visibility was set to anything other than 'All users', the seminar sessions
                   report was still displaying the course to users even when they didn't match
                   the visibility criteria. This has been corrected.

    TL-18687   +   Fixed seminar signup-custom field action cog column placement to be right-most when more then one custom field is used
    TL-18691       Fixed course's visibility icon within course management search to reflect the course visibility settings
    TL-18707       Fixed HR Import sanity check for an Organisation or Position parent

                   If the organisation or position parent id number was set to zero, the
                   sanity check to determine if the parent exists was being skipped. Zero is a
                   valid idnumber and is now used in the sanity check.

    TL-18737       Fixed issue with help icons not having an alt text associated with them
    TL-18738       Replaced hardcoded strings in environment checks with properly translated strings
    TL-18740       Updated program observer sql for course_in_progress() function to ensure first column is always unique
    TL-18742       Fixed failing unit tests in totara_job_dialog_assign_manager_testcase
    TL-18743       Fixed date conflicts validation error showing repeatedly in the event form
    TL-18746   +   Fixed performance by removing multiple course_in_progress event triggers

                   Performance is improved by removing multiple course_in_progress event
                   triggers when activity or course completion is triggered.

                   Event \core\event\course_in_progress was triggered every time when
                   completion_completion::mark_in_progress() was called. Now this event is
                   triggered only once per user enrolment (when timestarted is not yet set).
                   This is a change in behaviour since events will not be triggered anymore.
                   This behaviour will affect sites that have callbacks assuming that
                   course_in_progress will be fired each time when mark_in_progress is called.

    TL-18758       Fixed JavaScript race condition error when adding attendees
    TL-18765       Fixed usertours not recognising parameters on some program pages
    TL-18766       Fixed changes to Site Policy primary language not being saved

                   It is now possible to change the primary language of a Site Policy after it
                   was created.

    TL-18771       Fixed the management interface for 'assigned position' access restrictions in course sections

                   Prior to this change it was possible to add assigned position as a
                   conditional access restriction on course sections. However it was not
                   possible after adding the restriction to then edit or delete it. This has
                   now been fixed and the assigned position conditional access restriction for
                   sections behaves like all other conditional access restrictions.

    TL-18775       Added character length validation rule for appraisal multiple choice question options
    TL-18781       Fixed an incorrect condition to detect the csv source in HR Import
    TL-18804       Fixed the management interface for 'assigned organisation' access restrictions in course sections

                   Prior to this change it was possible to add assigned organisations as a
                   conditional access restriction on course sections but subsequent editing or
                   deleting assignments was not possible. This has been fixed and the
                   assigned organisation conditional access restriction for sections behaves
                   like all other conditional access restrictions.

    TL-18811       Fixed issue with HR Import where suspended state for a user would toggle

                   When importing users using HR Import and the 'Source contains all users'
                   setting was being used, any users who were set to be suspended would be set
                   to suspended on the first execution of HR Import and then unsuspended on
                   the second execution. Subsequent runs of HR Import would toggle the
                   suspended state for the user between suspended and active. The user is now
                   only unsuspended if specified in the imported data source.

    TL-18813       Fixed Seminar event dates being incorrectly created when editing an event with no dates

                   Prior to this fix if you created a Seminar event with no dates, and then
                   went back and edited the event, a session date would be automatically
                   created and you would have to remove them again.
                   This fix ensures a default session date is only added when a new event is
                   created.

    TL-18819       Fixed missing library inclusion for Report Builder settings file

                   In some circumstances an error was being thrown when the scheduler class
                   was not found. This only occurred very rarely when the file containing the
                   scheduler class was not included by another file.

    TL-18823       Fixed displayed ordering of items in Current Learning block

                   Items were sorted by short name, but the full name was displayed in the
                   list. Where short and full name differ significantly, the displayed order
                   would then appear to be somewhat random. The items are now sorted by full
                   name, matching what is displayed, to avoid this confusion.

    TL-18856       Added character length validation rule for appraisal multiple choice question options

Contributions:

    * Artur Poninski at Webanywhere - TL-18811
    * Jo Jones at Kineo UK - TL-18591
    * Russell England at Kineo USA - TL-18740, TL-18746


Release Evergreen (24th August 2018):
=====================================

Key:           + Evergreen only

Security issues:

    TL-18491       Added upstream security hardening patch for Quickforms library

                   A remote code execution vulnerability was reported in the Quickforms
                   library. This applied to other software but no such vulnerability was found
                   in Totara. The changes made to fix this vulnerability have been taken to
                   reduce risks associated with this code.

Performance improvements:

    TL-17598       Enrolments for courses added to an audience's enrolled learning are now processed by an adhoc task in the background

                   Prior to this change course enrolments that were required when a course was
                   added to an audiences enrolled learning were being processed immediately.
                   This could lead to the user performing the action having to wait
                   exceptionally long times on the page while this was processed. The fix for
                   this issue was to shift this processing to a background task. Enrolments
                   will now be processed exclusively by cron when adding courses to an
                   audience's enrolled learning.

Improvements:

    TL-13987       Improved approval request messages sent to managers for Learning Plans

                   Prior to this fix if a user requested approval for a learning plan then a
                   message was sent to the user's manager with a link to approve the request,
                   regardless of whether the manager actually had permission to view or
                   approve the request. This fix sends more appropriate messages depending on
                   the view and approve settings in the learning plan template.

    TL-17124   +   The main menu block is no longer added to the home page by default for new installations
    TL-17143   +   AMD modules can now be initialised using data attributes in HTML markup

                   It is now possible to initialise AMD modules using data attributes in HTML
                   markup. This is intended primarily for templates.

    TL-17495   +   Redesigned top navigation, for a more compact style with added support for a third level of links

                   Reworked the existing navigation, improving the user journey and added
                   support for third-level links which will allow us to tie all of the Totara
                   products together.
                    * Redesigned navigation
                    * Added third-level navigation
                    * Moved logo into navigation bar
                    * Moved messages and alerts into navigation
                    * Moved language selector into navigation
                    * Moved user menu into navigation

                   The old navigation menu is now deprecated, but still available with some
                   changes in code. See the following page for details
                   https://help.totaralearning.com/display/DES/Totara+v12+navigation+revert

    TL-17780       Added a warning message about certification changes not affecting users until they re-certify
    TL-17910   +   The single button output component now supports a "primary" state
    TL-17920       Added support for the 'coursetype' field in the 'upload courses' tool

                   The 'coursetype' field will now accept either a string or an integer value
                   from the map below:
                    * 0 => elearning
                    * 1 => blended
                    * 2 => facetoface

                   Within the 'upload courses' CSV file, the value for the 'coursetype' field
                   can be either an integer value or a string value. If the value of
                   'coursetype' was not within the expected range of values (as above), then
                   the system will throw an error message when attempting to upload the
                   course(s) or while previewing the course(s).

                   If the field is missing from the CSV file or the value is empty, then the
                   'coursetype' will be set to 'E-learning' by default. This is consistent
                   with previous behaviour.

    TL-18481       Improved the help strings for the 'Minimum time required' field within a program or certification course set

                   Program and certification 'Course set due' and 'Course set overdue' message
                   help strings have also been updated to convey that the 'Minimum time
                   required' field is used to determine when a course set is due.

    TL-18557   +   Added new base class for output elements that are using templates

                   Output widgets can now extend \core\output\template. Once extended they can
                   be given directly to a renderer's render method, and that renderer will
                   render them from the template. With this approach there is no need to
                   define any render methods at all, or to implement renderers for output
                   widgets.

    TL-18597       Improved the help text for the 'Notification recipients' global seminar setting

                   The setting is located under the notifications header on the site
                   administration > seminars > global settings page, the string changed
                   was 'setting:sessionrolesnotify' within the EN language pack.
                   Full updated text is: This setting affects *minimum
                   booking* and *minimum booking cut-off* notifications. Make sure you
                   select roles that can manage seminar events. Automated warnings will be
                   sent to all users with selected role(s) in seminar activity, course,
                   category, or system level.

    TL-18640       Updated certif_completion join to use 'UNION ALL'

                   The 'certif_completion' join in the 'rb_source_dp_certification' report
                   source now uses 'UNION ALL', previously 'UNION', which will aid
                   performance.

    TL-18675       Added 'not applicable' text to visibility column names when audience visibility is enabled

                   When audience based visibility is enabled it takes priority over other
                   types of visibility. Having multiple visibility columns added to a report
                   may cause confusion as to which type of visibility is being used. '(not
                   applicable)' is now suffixed to the visibility column to clarify which type
                   of visibility is inactive, e.g. 'Program Visible (not applicable)'.

Bug fixes:

    TL-17734       Fixed OpenSesame registration
    TL-17755       Fixed user tours not working when administration block is missing on dashboard
    TL-17767       Fixed multiple blocks of the same type not being restored upon course restore
    TL-17824       Improved the reliability of Totara Connect SSO

                   There is also a new login page parameter '?nosso=1' which may be used to
                   temporarily disable Totara Connect SSO to allow logging in via local
                   authentication method.

    TL-17846       Content restrictions are now applied correctly for Report Builder filters utilising dialogs

                   Before Totara Learn 9 the organisation and position content restriction
                   rules were applied when displaying organisation and position filters in
                   reports.

                   With the introduction of multiple job assignments in Totara Learn 9,
                   organisation and position report filters now use the generic totara dialog
                   to display available organisation and position filter values.

                   This patch added the application of the missing report content restriction
                   rules when retrieving the data to display in totara dialogs used in report
                   filters.

    TL-17857       Deleting a 'featured links' block no longer leaves orphaned cohort visibility records
    TL-17882       Recipient notification preferences are now checked before sending learning plan messages

                   Previously when a new comment was added to a user's learning plan overview,
                   the system would send an email to the target user notifying them about the
                   comment. Now the user's preferences determine whether the email is sent to
                   the user or not, specifically the
                   "message_provider_moodle_competencyplancomment_loggedoff" and
                   "message_provider_moodle_competencyplancomment_loggedin" preferences.

    TL-17934       Fixed waitlisted users not being displayed in seminar reports that included session date columns

                   Previously waitlisted users would be displayed in seminar reports that did
                   not contain session dates, but would disappear if a column related to
                   session dates was added (specifically the session start, session finish,
                   event start time, event finish time columns). Now the waitlisted users will
                   always be displayed regardless of these columns, however the columns will
                   be blank or 'not specified' for these users.

    TL-17936       Report builder graphs now use the sort order from the underlying report

                   When scheduled reports were sent, the report data was correctly ordered,
                   but the graph (if included) was not being ordered correctly. The ordering
                   of the graph now matches the order in the graph table.

    TL-17938       Fixed encoding issues using Scandinavian characters in a Location custom field address

                   This issue only affected Internet Explorer 11. All other browsers handled
                   the UTF8 character natively.

    TL-17955       Progress bar and tooltips in the Current Learning block now work properly with pagination
    TL-17970       Backported MDL-62239 to fix broken drag-drop of question types on iOS 11.3
    TL-17973       Searching a report configured to use custom fields no longer fails after referenced fields have been deleted

                   Previously if a custom field was included in searchable fields for a
                   toolbar search within report builder, and that custom field was then
                   deleted, when a user attempted to search the report using the toolbar
                   search they would get an error. The toolbar search now checks that fields
                   still exists before attempting to perform a search on them.

    TL-17977       Users editing Program assignments are now only shown the option to assign audiences if they have the required capability

                   Previously if a user did not have moodle/cohort:view capability and tried
                   to assign an audience to a program an error would be thrown. The option to
                   add audiences is now hidden from users who do not have this capability.

    TL-18482       Fixed the formatting of Custom profile field data when exporting via 'Bulk user actions'

                   Some values (specifically the Dropdown Menu) were being exported as the
                   index (number) instead of the text name of the option. This is now exported
                   correctly.

    TL-18488       Fixed a regression in DB->get_in_or_equal() when searching only integer values within a character field

                   This is a regression from TL-16700, introduced in 2.6.52, 2.7.35, 2.9.27,
                   9.15, 10.4, and 11.0. A fatal error would be encountered in PostgreSQL if
                   you attempted to call get_in_or_equal() with an array of integers, and then
                   used the output to search a character field.
                   The solution is ensure that all values are handled as strings.

    TL-18498       Fixed the ability to search for custom rooms inside the room selection dialog

                   Previously when custom rooms were created within a seminar session via the
                   room selection dialog, the custom room would not be searchable on the
                   'search' tab of the dialog. Now custom rooms that are visible on the
                   'browse' tab will also be searchable on the 'search'  tab.

    TL-18499       Fixed an issue where searching in glossary definitions longer than 255 characters would return no results on MSSQL database

                   The issue manifested itself in the definitions where the search term
                   appeared in the text only after the 255th character due to incorrectly used
                   concatenation in an SQL query.

    TL-18545       Fixed the management interface for 'audience membership' access restrictions in course sections

                   Prior to this change it was possible to add audience membership as a
                   conditional access restriction on course sections. However it was not
                   possible after adding the restriction to then edit or delete it. This has
                   now been fixed and the audience membership conditional access restriction
                   for sections behaves like all other conditional access restrictions.

    TL-18546       Fixed missing string parameter when exporting report with job assignment filters
    TL-18548       Introduced new permissions for adding and removing recipients to a seminar message

                   The two new permissions were added:
                     1) "mod/facetoface:addrecipients" : This permission allows the role to
                   add any recipients to the seminar message
                     2) "mod/facetoface:removerecipients" : This permission allows the role
                   to remove any recipients from the seminar message

                   Adding or removing seminar's message recipients action would not check for
                   the permission "mod/facetoface:addattendees" or
                   "mod/facetoface:removeattendees" but checking for the new permissions added
                   instead

    TL-18566       Backported MDL-61281 to make Solr function get_response_counts compatible() with PHP 7.2
    TL-18569   +   Removed 'export to portfolio' links from assignment grading interfaces

                   The 'export to portfolio' functionality is designed for a user to export
                   their own assignment submissions to their portfolio. The link was being
                   shown to trainers in the grading interface but displayed an error if it was
                   clicked.

    TL-18573       Added a check for the 'Events displayed on course page' setting when viewing seminar events on the course page

                   Now both settings are taken into account: when the 'Users can sign-up to
                   multiple events setting' is enabled, the number of events displayed for
                   which a user can sign up will be restricted to the number in the ‘Events
                   displayed on course page' setting. Events to which a user is already signed
                   up will always be displayed, and do not form part of the event count.

    TL-18574       Fixed a return type issue within the Redis session management code responsible for checking if a session exists
    TL-18583       Fixed missing status string on Site Policies Report
    TL-18590       Made sure that multiple jobs are not created via search dialogs if multiple jobs are disabled sitewide
    TL-18599       Fixed minor issues with site policies

                   The following minor issues with site policies were fixed:
                    * Viewing the 'Site Policy Records' embedded report while site policies
                   are not enabled now shows the report without throwing an exception.
                    * An 'Edit this report' link is now available to administrators and users
                   with the necessary capabilities when the 'Site Policy Records' embedded
                   report is viewed.
                    * After giving consent to the necessary site policies, the user is now
                   redirected back to the original url. E.g. A user receives an email with a
                   forum link. They click the link which requires them to log in and give
                   consent to the policies. Once they have given the necessary consent, they
                   are now redirected directly to the forum page which was originally
                   requested.

    TL-18615   +   Removed duplicated options in the 'Show with backdrop' selector on the add new step form in user tours
    TL-18618       Restoring a course now correctly ignores links to external or deleted forum discussions
    TL-18649       Improved the Auto login guest setting description and ensured admin pages no longer automatically log guests in

                   The auto login guest setting incorrectly sets the expectation that
                   automatic login only happens when a non-logged in user attempts to access a
                   course. In fact it happens as soon as the user is required to login,
                   regardless of what they are trying to access. The description has been
                   improved to reflect the actual behaviour.
                   Additionally in Totara 12 we have set it so that the user is not
                   automatically logged in if they attempt to access administration pages.

    TL-18676       Improved the performance of 'set of courses' in program content editing

API changes:

    TL-13960   +   Moved all report builder customfield-related functions that added columns, filters, and joins from base source into traits

                   All function that added columns, filters, and joins for custom fields have
                   been deprecated and moved into traits within the report sources associated
                   'customfield' component.

    TL-16729   +   Converted all Report Builder display functions into classes

                   All the Report Builder display functions have been deprecated and converted
                   into display classes for better control over how data is displayed and for
                   improved performance.

                   This patch however does not introduce any changes in the current display of
                   data within the reports.

Miscellaneous Moodle fixes:

    TL-18298   +   MDL-61309: Implemented a new deleted flag for forum posts and adapted userdata purging to use it
    TL-18301   +   MDL-61905: Removed unsused Workshop tables from database

                   A number of tables that were used by the Workshop module in versions 1.1
                   and earlier have been kept but unused since upgrading to version 2.0. Those
                   tables were suffixed with '_old'.

                   If your installation was originally a Moodle or Totara version 1.x, we
                   recommend confirming whether these tables may contain data that should be
                   kept before upgrading as these tables will be dropped.

    TL-15325   +   MDL-57572: Added support for the igbinary serializer in the Redis Cache Store

                   Added setting to switch the serializer to either the builtin php or the
                   igbinary serialiser. The igbinary serialiser stores data structures in
                   compact binary form and savings can be significant for storing cached data
                   in Redis.

    TL-15335   +   MDL-57570: Added support for the igbinary serializer in the Static Cache Store

                   If igbinary is installed the static cache store automatically makes use of
                   it.

    TL-15345   +   MDL-57655: Added support for the igbinary serializer in the Redis Session Handler

                   If igbinary is installed and $CFG->session_redis_serializer_use_igbinary is
                   set to true the Redis session handler uses igbinary for serializing the
                   data.

    TL-15355   +   MDL-55476: Removed loginpasswordautocomplete option

                   The a loginpasswordautocomplete option simply appends autocomplete="off" to
                   the password field in the form. As most of the browsers dropped support for
                   this attribute it is removed.

    TL-15567   +   MDL-58311: Added support for password-protected Redis Session and Cache Store connections

                   Support for setting a password for the Redis Cache and Session Store was
                   added. Password for the cache store can be set when adding or editing the
                   cache store instance settings.

                   The password for the Redis session store can be set with the config
                   $CFG->session_redis_auth.

    TL-15326   +   MDL-56519: Added linting for behat .feature files

                   The linting enforces the following rules on .feature files:
                    * Indentation (in spaces):
                    ** Feature: 0
                    *** Background: 2
                    *** Scenario: 2
                    **** Step: 4
                    **** Given: 4
                    **** And: 4
                    **** Examples: 4
                    **** Example: 6
                    * Other rules:
                    ** Feature names must be unique
                    ** Empty feature files are not allowed anymore
                    ** Feature files w/o scenarios are not allowed anymore
                    ** Partially commented tag lines are not allowed
                    ** Trailing spaces are not allowed
                    ** Unnamed features are not allowed
                    ** Unnamed scenarios are not allowed
                    ** Scenario outlines w/o examples are not allowed

    TL-15356   +   MDL-57896: Added command line tool to read and change configuration settings in the database
    TL-15371   +   MDL-57887: Support nginx and other webservers for logging of username in access logs

                   Support for logging usernames to webserver access logs has been extended to
                   allow sending the username as a custom header which can be logged and
                   stripped out if needed.

    TL-15385   +   MDL-58109: Added check for preventexecpath in the Security Report

                   If the config value $CFG->preventexecpath is set to 'false' this will show
                   up in the Security Report as a warning.

    TL-15397   +   MDL-40759: Added additional Font Awesome support

                   A small number of icons have been converted to Font Awesome icons, and a
                   number of remaining locations where image icons were used have been
                   replaced with font icons.

    TL-18469   +   MDL-60793: Fixed compatibility issue with MySQL 8

                   The chat module used a database field where the name is a reserved word in
                   MySQL 8. This could have caused errors during some database operations. The
                   field has been renamed.

    TL-18047   +   MDL-61658: Fixed display of user's country in course participant list and 'Logged in user' block

                   If a country was excluded from the setting 'allcountrycodes', the country
                   code was not translated to the country name in the 'Logged in user' block
                   and on the course participants list.

    TL-18049   +   MDL-60241: Fixed visible value of general section in course

                   On upgrade to Moodle 3.3 it was possible that the general section of a
                   course was set to visible = 0. Even if this has no effect in Totara this
                   patch reverts this and sets all general sections back to visible = 1.

    TL-18080   +   MDL-61305: Added a lock to prevent 'coursemodinfo' cache to be built multiple times in parallel

                   To reduce impact on the performance, the building of the coursemodinfo
                   cache cannot happen in parallel anymore. There's now a database lock in
                   place to prevent that.

    TL-18210   +   MDL-27886: Fixed handling of course backup settings and dependencies

                   The dependency of backup settings was not working properly. If a default
                   setting was disabled (not locked) then the dependent settings in the backup
                   were locked and could not be changed as expected. The check for locked
                   dependencies has been changed to fix this.

    TL-15306   +   MDL-53814: Show question type icons when manually grading a quiz
    TL-15309   +   MDL-57143: Removed check for Windows when using SQL Server (sqlsrv) drivers

                   When using the SQL driver for Linux there was an error message during
                   initialisation stating that the driver is only available for Windows. This
                   is not true anymore as there is a Linux driver, thus the message got
                   removed.

    TL-15311   +   MDL-56320: Allow uninstall of unused web service plugins
    TL-15312   +   MDL-56640: Converted single selects and URL selects to mustache templates

                   This has also deprecated the YUI auto submit JavaScript.

    TL-15314   +   MDL-56581: Highlighted row when permission is overriden in a course

                   This will require LESS to be re-compiled when using LESS inheritance.

    TL-15315   +   MDL-57472: Removed fix_column_widths Internet Explorer 6 hack

                   Removed old Internet Explorer 6 hack and added deprecated warnings.

    TL-15316   +   MDL-57471: Deprecated init_javascript_enhancement() and smartselect code
    TL-15317   +   MDL-57395: Added new Web Service core_course_get_updates_since
    TL-15319   +   MDL-44172: Removed example htaccess file
    TL-15321   +   MDL-55461: Fixed placement of cursor in Atto equation editor on repeated insertions from predefined buttons
    TL-15322   +   MDL-57392: Modified external function core_course_external::get_courses_by_field to return the course filters list and status
    TL-15323   +   MDL-57149: Made the language import administration page compatible with Bootstrap
    TL-15324   +   MDL-57282: Deprecated the behat step "I go to X in the course gradebook"
    TL-15328   +   MDL-57627: Added new field to forum Web Service to get tracking status of the user
    TL-15329   +   MDL-50549: Added new Web Service to retrieve a list of URLs from several courses
    TL-15330   +   MDL-50542: Added new Web Service to retrieve a list of labels from several courses
    TL-15333   +   MDL-57488: Replaced and deprecated M.util.focus_login_form and M.util.focus_login_error
    TL-15336   +   MDL-57490: Converted Select all/none functionality to use JavaScript

                   In the quiz, SCORM and lesson modules, there was some inline JavaScript
                   handlers. These have been converted to pure JavaScript event listeners.

    TL-15338   +   MDL-50547: Added new Web Service to retrieve a list of resources from several courses

                   Added a new Web Service which returns a list of files in a provided list of
                   courses. If no list is provided all files that the user can view will be
                   returned.

    TL-15339   +   MDL-57550: Updated advanced forum search to use AMD modules
    TL-15340   +   MDL-56449: Provided a more detailed description of group submission problems
    TL-15341   +   MDL-50545: Added new Web Service to retrieve a list of pages from several courses
    TL-15342   +   MDL-50539: Added new Web Service to retrieve a list of folders from several courses
    TL-15343   +   MDL-49423: Added support for optiongroups inside admin selects
    TL-15344   +   MDL-57690: Stopped loading mcore YUI rollup on each page

                   This may expose areas in custom JavaScript that use YUI modules without
                   loading them correctly.

    TL-15346   +   MDL-57273: Added generic exporter, persistent and persistent form classes

                   This patch adds new model classes following an active record pattern to
                   represent, fetch and store data in the database. The persistent class also
                   provides basic validation.

                   Exporters convert objects to stdClasses. The exporter contains the
                   definition of all properties and optionally related objects.

    TL-15348   +   MDL-56808: Removed use of eval in SCORM JavaScript files
    TL-15349   +   MDL-57638: Improved the handling of failed RSS feeds in the RSS block

                   Previously if the cron could not read the RSS feed configured in a block
                   this failure was not visible to the administrator in the interface.
                   Additionally every time the block displayed it tried to fetch the feeds
                   regardless of its status.
                   With this patch the RSS blocks do not try to request the feeds if the
                   'skiptime' and 'skipuntil' values are set. If there are failed feeds then
                   an error message will be shown to the administrator but not to a learner.

    TL-15350   +   MDL-57586: Changed $workshop variable from protected to public in class

                   Changed $workshop from protected to public in class
                   workshop_example_submission to make it easier for renderers in themes to
                   access data instead of retrieving it from the database.

    TL-15354   +   MDL-57697: Converted survey validation JavaScript from YUI2 to AMD
    TL-15357   +   MDL-57890: Improved all get_by_courses Web Services to include the coursemodule (cmid) in the results
    TL-15358   +   MDL-57687: Removed unnecessary init_toggle_class_on_click JavaScript functionality
    TL-15362   +   MDL-57619: Removed behat steps deprecated in Moodle 2.9 or earlier
    TL-15363   +   MDL-57602: Added 'Granted extension' filter for grading table
    TL-15365   +   MDL-57633: Added new Web Service mod_lesson_get_lessons_by_courses
    TL-15366   +   MDL-57527: Changed course reports to use CSS instead of SVG rotation
    TL-15368   +   MDL-53978: Added extra plugin callbacks for every major stage of page render + swap user tours to use them
    TL-15374   +   MDL-57972: Added shortentext mustache helper
    TL-15375   +   MDL-45584: Made cache identifiers part of loaded caches
    TL-15376   +   MDL-57280: Added the ability to create modal types via a registry

                   More information can be found
                   at https://help.totaralearning.com/display/DEV/Modal+registry

    TL-15377   +   MDL-57999: Add itemname to gradereport_user_get_grade_items  Web Service
    TL-15379   +   MDL-57975: Added HTML5 session storage.

                   This can be used by developers using the core/sessionstorage AMD module in
                   much the same way developers can use core/localstorage

                   This also adds a core_get_user_dates and userdate mustache helper.

    TL-15380   +   MDL-57914: Refactored get_databases_by_courses
    TL-15382   +   MDL-57915: Added Web Service mod_data_view_database
    TL-15383   +   MDL-58217: Added data generators for feedback items
    TL-15386   +   MDL-57631: Implemented scheduled task for LDAP Enrolments Sync

                   The previous CLI script has been deprecated in favour of the new scheduled
                   task. The new task is disabled by default.

    TL-15388   +   MDL-50538: Added new Web Service mod_feedback_get_feedbacks_by_courses
    TL-15392   +   MDL-57643: Added new Web Service mod_lesson_get_lesson_access_information
    TL-15393   +   MDL-57645: Added new web service mod_lesson_view_lesson
    TL-15394   +   MDL-57648: Added new web service mod_lesson_get_questions_attempts
    TL-15396   +   MDL-57390: Added capabilities/permission information to Web Service forum_can_add_discussion response
    TL-15398   +   MDL-57657: Added new Web Service mod_lesson_get_user_grade
    TL-15401   +   MDL-57664: Added new lesson Web Service get_content_pages_viewed
    TL-15402   +   MDL-57665: Added new Web Service mod_lesson_get_user_timers
    TL-15404   +   MDL-57812: Added new Web Service get_feedback_access_information
    TL-15406   +   MDL-57811: Added new Web Service mod_feedback_view_feedback
    TL-15407   +   MDL-57916: Added new Web Service mod_data_get_access_information
    TL-15408   +   MDL-57814: Added new Web Service mod_feedback_get_current_completed_tmp
    TL-15409   +   MDL-57823: Implemented the check_updates callback in the feedback module
    TL-15410   +   MDL-57815: Added new Web Service mod_feedback_get_items
    TL-15411   +   MDL-55267: Removed deprecated field datasourceaggregate
    TL-15412   +   MDL-57685: Added new Web Service mod_lesson_get_pages
    TL-15413   +   MDL-57816: Added new Web Service mod_feedback_launch_feedback
    TL-15414   +   MDL-57817: Added new Web Service mod_feedback_get_page_items
    TL-15415   +   MDL-57818: Added new Web Service mod_feedback_process_page
    TL-15417   +   MDL-57820: Added new Web Service mod_feedback_get_analysis
    TL-15418   +   MDL-58229: Added new Web Service get_unfinished_responses
    TL-15419   +   MDL-57688: Added new Web Service mod_lesson_launch_attempt
    TL-15420   +   MDL-57693: Added new Web Service mod_lesson_get_page_data
    TL-15421   +   MDL-57696: Added new Web Service mod_lesson_process_page
    TL-15422   +   MDL-57724: Added new Web Service mod_lesson_finish_attempt
    TL-15423   +   MDL-57754: Added new Web Service mod_lesson_get_attempts_overview
    TL-15424   +   MDL-57757: Added new Web Service mod_lesson_get_user_attempt
    TL-15426   +   MDL-57762: Added check updates functionality to the lesson module
    TL-15427   +   MDL-57760: Added new Web Service mod_lesson_get_pages_possible_jumps
    TL-15428   +   MDL-58329: Added new Web Service mod_lesson_get_lesson
    TL-15430   +   MDL-57965: Enabled gzip compression for SVG files
    TL-15431   +   MDL-58070: Reworded "visible" core string used in course visibility

                   Additionally we aligned the name and value strings of the course visibility
                   default settings. Previously the value strings were different to the actual
                   course settings.

    TL-15432   +   MDL-55139: Added code coverage filter in component phpunit.xml files
    TL-15433   +   MDL-58230: Added new Web Service mod_feedback_get_finished_responses
    TL-15434   +   MDL-57822: Added new Web Service mod_feedback_get_non_respondents
    TL-15436   +   MDL-49409: Added new Web Service mod_data_get_entries
    TL-15437   +   MDL-57918: Added new Web Service mod_data_get_entry
    TL-15438   +   MDL-57919: Added new Web Service mod_data_get_fields
    TL-15439   +   MDL-57920: Added new Web Service mod_data_search_entrie
    TL-15440   +   MDL-57921: Added new Web Service mod_data_approve_entry
    TL-15441   +   MDL-57922: Added new Web Service mod_data_delete_entry
    TL-15442   +   MDL-57923: Added new Web Service mod_data_add_entry
    TL-15443   +   MDL-57924: Added new Web Service mod_data_update_entry
    TL-15444   +   MDL-57925: Implemented check_updates_since callback
    TL-15445   +   MDL-50970: Added new Web Service core_block_get_course_blocks
    TL-15461   +   MDL-57411: mod_check_updates now returns information based on user capabilities
    TL-15464   +   MDL-48771: Improved quiz question editing interface

                   The quiz editing interface has been improved to allow selection of multiple
                   questions to be deleted.

    TL-15466   +   MDL-55941: Improved UX of alpha chooser / initialbar in tablelib and made it responsive
    TL-15496   +   MDL-57503: Allow course ids for enrol_get_my_courses

                   This adds a new parameter for enrol_get_my_courses() to filter the list
                   returned to specific courses.

    TL-15514   +   MDL-58265: Refactored behat to use a new step "I am on the course homepage"

                   The new step directly accesses the course page without following the path
                   from the homepage to the course. A shortcut step "I am on course homepage
                   with editing mode on" was also added to allow accessing a course and turn
                   editing mode on.

    TL-15553   +   MDL-53343: Migrated scorm_cron into new tasks API
    TL-15555   +   MDL-57821: Added Web Service mod_feedback_get_responses_analysis
    TL-15556   +   MDL-51998: Improved manage forum subscribers button
    TL-15557   +   MDL-58444: Added number of unread posts to get_forums_by_courses  Web Services
    TL-15558   +   MDL-58399: Return additional file fields in Web Services to be able to handle external repositories files

                   See mod/upgrade.txt and course/upgrade.txt for details.

    TL-15559   +   MDL-58361: Made core_media_manager final to prevent from being subclassed
    TL-15564   +   MDL-57813: Added Web Service mod_feedback_get_last_completed
    TL-15565   +   MDL-58453: Refactored get_non_respondents Web Service
    TL-15569   +   MDL-56632: Moved the "Turn editing on\off" link to the top of the book administration menu
    TL-15575   +   MDL-57553: Fixed user tour steps so that they do not inherit attributes from CSS selector

                   Updated the flexitour component to v0.10.0 and the popper.js library to
                   v1.0.8 in the process.

    TL-15579   +   MDL-58552: Fixed alignment of quiz icon
    TL-15583   +   MDL-57573: Updated PHPmailer library to v5.2.23
    TL-15589   +   MDL-58493: Converted the delete enrolment icon to a font icon

                   When managing enrolments in a course, if a role was added, the delete icon
                   was an image (instead of a font icon) before the page was reloaded. This
                   has been corrected.

    TL-15594   +   MDL-58549: Added version of jabber/XMPP libraries to thirdpartylibraries.xml
    TL-15598   +   MDL-58574: Removed an unnecessary check for delete icon when working with permissions in an activity module
    TL-15604   +   MDL-58502: Fixed error when cancelling feedback
    TL-15619   +   MDL-58530: Updated the video.js library to v5.18.4
    TL-15620   +   MDL-58412: Fixed several bugs in the new feedback web services
    TL-15630   +   MDL-58415: Multiple bug fixes in the new lesson web services

                   * Avoid inappropriate http redirections
                   * Added missing answer fields
                   * Various code fixes, including ensuring correct variable types are used
                   where necessary

    TL-15635   +   MDL-51932: Improved UX when setting up a workshop

                   When setting up a workshop activity, the stage switch has been updated to
                   state which stage they will take you to.

    TL-15636   +   MDL-58681: Split the checkbox and advcheckbox behat tests

                   Advanced checkboxes cannot be tested without a real browser because Goutte
                   does not support the hidden+checkbox duality.

    TL-15639   +   MDL-58659: Added enddate parameter to Web Services returning course information
    TL-15682   +   MDL-58860: Fixed Web Service mod_lesson_get_attempts_overview when no attempts made
    TL-15684   +   MDL-58857: User session is now terminated when a major upgrade is required
    TL-15708   +   MDL-59132: Fixed anonymous response numbering in feedback Web Service
    TL-17981   +   MDL-62588: Added missing instanceid database field to the Paypal enrolment plugin
    TL-17983   +   MDL-62408: Fixed profile_guided_allocate() function to help split behat scenarios better for parallel runs never being executed in behat_config_util
    TL-17985   +   MDL-62500: Fixed an issue where a checkbox label wasn't updated after updating a tag
    TL-17989   +   MDL-61521: Fixed missing text formatting for category name in get_categories Web Service
    TL-17990   +   MDL-61800: Reset the OUTPUT and PAGE for each task on cron execution
    TL-17993   +   MDL-61012: Allow module name to be guessed only if not set by subclass of the moodleform_mod class
    TL-17995   +   MDL-60882:  Prevent deletion of all responses if the external function delete_choice_responses() is called without responses specified

                   The external function mod_choice_external::delete_choice_responses has
                   changed behaviour - if this function is called by a user who has the
                   'mod/choice:deleteresponses' capability with no responses specified then
                   only the users responses will be deleted, rather than all responses for all
                   users within the choice. To delete all responses from all users, all
                   response IDs must be specified.

    TL-17996   +   MDL-61715: Fixed Question type chooser displaying headings for empty sections under certain conditions
    TL-17997   +   MDL-62011: Fixed an issue where approval of a course request fails if a new course with the same name has been created prior to request approval
    TL-17999   +   MDL-62042: Filtered out some unicode non-characters when building index for Solr
    TL-18001   +   MDL-59857: Increased the length of the 'completionscorerequired' field in SCORM database table
    TL-18002   +   MDL-61348: Fixed incorrect group grade averages in quiz reports
    TL-18003   +   MDL-61520: Fixed references to xhtml in Quiz statistics report
    TL-18006   +   MDL-61928: Made frozen form sections collapsible an expandable
    TL-18008   +   MDL-61708: Fixed LTI to respect fullnamedispaly settings for fullname field in the requests
    TL-18009   +   MDL-61741: Fixed the IPN verification endpoint URL of the Paypal Enrolment plugin
    TL-18010   +   MDL-58697: Fixed issue with assignment submission when toggling group submission

                   When assignment submission was set to group submission and then turned off,
                   the status was not showing an assignment as submitted even if there was a
                   file submitted. The group assignment status is now only considered if group
                   assignment submission is enabled.

    TL-18012   +   MDL-60196: Fixed the display of custom LTI icons
    TL-18013   +   MDL-61033: Fixed an error when editing a quiz while a preview is open in another browser window
    TL-18014   +   MDL-61129: Added 'colgroup' attribute to the survey question tables
    TL-18016   +   MDL-61581: Added styling to the 'returning to lesson' navigation buttons
    TL-18017   +   MDL-61860: Fixed require path for config.php on authentication test settings page
    TL-18019   +   MDL-60115: Fixed a silently failing redirect when creating a new book resource
    TL-18020   +   MDL-60726: Fixed alignment of assignment submission confirmation message
    TL-18021   +   MDL-61020: Fixed Video.js media player timeline progress bar being flipped in RTL mode
    TL-18022   +   MDL-61127: Added improved keyboard navigation when using the file picker
    TL-18023   +   MDL-61163: Fixed a bug preventing guest users from viewing Wiki pages belonging to Wiki activities added to the page
    TL-18025   +   MDL-61502: Added a test for multi-lingual "Select missing words" questions
    TL-18026   +   MDL-61522: Made sure glossary paging bar links do not use relative URLs
    TL-18027   +   MDL-61689: Unexpected and unhandled output during unit tests will now result in the tests being marked as Risky
    TL-18033   +   MDL-55532: Fixed a hard-coded reference to the admin directory within the User tours tool
    TL-18034   +   MDL-60762: tool_usertours blocks upgrade if admin directory renamed
    TL-18036   +   MDL-61257: Fixed the 'Course module completion updated' link in the course log report

                   The link was previously pointing to the course completion report instead of
                   the activity completion report, this has been fixed.

    TL-18037   +   MDL-61321: Fixed a bug in mod_feedback_get_responses_analysis Web Services preventing return of more than first 10 feedback responses
    TL-18038   +   MDL-61328: Fixed the sorting of User tours steps when moving steps up or down
    TL-18039   +   MDL-61576: Ensured the lti_build_custom_parameters function contains all necessary parameters
    TL-18040   +   MDL-61656: Fixed missing role name on the security report for incorrectly defined front page role
    TL-18041   +   MDL-61733: Fixed creation of tables in Atto editor for Database activity templates
    TL-18043   +   MDL-52989: Fixed question clusters occasionally displaying a blank page when a student restarts half way through
    TL-18044   +   MDL-58179: Converted uses of "label" CSS class to "mod_lesson_label"

                   Bootstrap causes HTML elements with the CSS class to have white text. As a
                   result text was not being displayed correctly. This change only affects the
                   lesson activity module.

    TL-18048   +   MDL-59070: Fixed enrol database plugin bug where the 'enablecompletion' value was not loaded
    TL-18050   +   MDL-60398: Fixed an issue with downloading resource of type "Folder" with name of 200+ bytes
    TL-18051   +   MDL-61261: Added validation for requests to 'Open badges' backpack to prevent possible self-XSS
    TL-18057   +   MDL-36157: Fixed HTML entities in RSS feeds that were not displayed correctly
    TL-18058   +   MDL-55153: Fixed an issue with customised language strings that have been removed still showing up in language customisation interface
    TL-18060   +   MDL-60658: Fixed validation of the 'grade to pass' activity setting to ensure that localisations are correctly handled
    TL-18061   +   MDL-61196: Ensured activity titles are correctly formatted when included in the subject for notifications
    TL-18064   +   MDL-61322: The time column within the log and live log reports now displays the year as part of the date
    TL-18065   +   MDL-61453: Fixed accepted file type when uploading user pictures

                   When uploading multiple user pictures, the list of accepted file types for
                   the file picker was not limited to ZIP only. This has been fixed. Attempts
                   to upload non-ZIP files led to an error message.

    TL-18069   +   MDL-61480: Added a check to ensure plugins are installed within get_plugins_with_function()
    TL-18070   +   MDL-58845: The Choice activity report for reviewing answers now respects the 'Display unanswered questions' setting
    TL-18071   +   MDL-61005: Fixed an issue in which system level audiences were potentially excluded when searching audiences in some interfaces
    TL-18072   +   MDL-61289: Fixed choice activity didn't include extra user profile fields on export
    TL-18073   +   MDL-61324: Fixed detection of changed grades during LTI sync

                   Improved the detection of changed grades during LTI sync so that unchanged
                   grades are not synced every time the grade sync task is run anymore.

    TL-18074   +   MDL-61408: Added default button class when checking quiz results
    TL-18076   +   MDL-56688: Fixed the order of grade items in single view and export of the Gradebook

                   All views of grade items now show in the order set in the Gradebook setup.

    TL-18077   +   MDL-61150: Corrected wrong "path" attribute in some core install.xml files
    TL-18078   +   MDL-61153: Made lesson detailed statistics report column widths consistent
    TL-18079   +   MDL-61236: Fixed bug where course welcome message email was not sent from the course contact who was first assigned the role of trainer
    TL-18081   +   MDL-61344: Added display of additional files when adding submissions in assignment module
    TL-18086   +   MDL-42764: Added missing error message for user accounts without email address
    TL-18087   +   MDL-51189: Fixed an issue in the quiz module where trainers were unable to edit override if quiz was not available to student
    TL-18088   +   MDL-52832: Fixed an issue where quiz page did not take user/group overrides into account when displaying the quiz close date
    TL-18090   +   MDL-61027: Fix an issue with datetime profile fields when using non-Gregorian calendars
    TL-18091   +   MDL-61168: Prevented the 'Export to portfolio' buttonfrom getting truncated by collapsed online text submissions

                   When a long 'Online Text' submission is made the entry is truncated and is
                   expandable. The 'Export to portfolio' button, if enabled, was also being
                   truncated. Only the submitted text is truncated now.

    TL-18092   +   MDL-61251: Corrected a message to 'Enable RSS feeds' to point to the proper settings section
    TL-18096   +   MDL-60077: Fixed the display of the pop-up triangle next to rounded corners in User Tours
    TL-18097   +   MDL-60646: Fixed undefined string when managing a user's portfolio
    TL-18098   +   MDL-60997: Added replytoname property to the core_message class allowing to specify "Reply to" field on outgoing emails
    TL-18101   +   MDL-61250: Omitted leading space in question preview link
    TL-18102   +   MDL-61253: Fixed referenced files were not added to archive when trying to download a folder
    TL-18105   +   MDL-58006: Fixed blind marking status not being reset by course reset in assignment module
    TL-18107   +   MDL-60181: Glossary ratings are now displayed in their entry

                   Previously the entry appeared to be in the following glossary entry.

    TL-18108   +   MDL-60918: Made sure current user is used in message preference update
    TL-18109   +   MDL-61077: Made quiz statistics calculations more robust
    TL-18111   +   MDL-61224: Added length validation for short name when creating a role
    TL-18112   +   MDL-61234: Fixed race condition in user tours while resolving the fetchTour promise
    TL-18113   +   MDL-37390: Set course start date when a course is approved to the user's midnight
    TL-18114   +   MDL-55382: Changed quicklist order to be alphabetical when annotating File submission assignments
    TL-18115   +   MDL-60549: Ensured LTI return link works when content is outside of an iframe
    TL-18116   +   MDL-60776: Fixed error in enrolled users listing when custom fullnamedisplay format contains a comma
    TL-18117   +   MDL-61010: Added unread posts link for the counter in "Blog-like" forum which takes a user to the first unread post in the discussion
    TL-18121   +   MDL-43042: Improved layout of multichoice question response in a lesson
    TL-18122   +   MDL-53985: Prevented assignment PDF annotations being removed when a submission is revert back to draft
    TL-18123   +   MDL-57786: Fixed word count for online text submission in assignment module
    TL-18124   +   MDL-60079: Fixed 'User tours' leaving unnecessary aria tags in the page
    TL-18125   +   MDL-60415: Fixed error messages in LTI launch.php when custom parameters are used
    TL-18126   +   MDL-60742: Allow customisation of 12/24h time format strings
    TL-18127   +   MDL-60943: Improved error message for preg_replace errors during global search indexing
    TL-18129   +   MDL-61068: Changed rounding for timed forum posts to the nearest 60 seconds to ensure all neighbouring posts are correctly selected
    TL-18130   +   MDL-61098: Fixed trainers ability to edit or delete WebDav repositories that they have created at a course level
    TL-18132   +   MDL-23887: Replaced deprecated System Tables calls to System Views calls in sql generator for MSSQL
    TL-18134   +   MDL-57727: Fixed Activity completion report to have a default sort order
    TL-18135   +   MDL-61107: Made sure invalid maximum grade input is handled correctly in quiz activity
    TL-18136   +   MDL-33886: Added graceful error handling when backup filename is too long
    TL-18137   +   MDL-43827: Improved accessibility when editing uploaded files on the server
    TL-18138   +   MDL-51089: Improved accessibility when accessing the 'add question' action menu
    TL-18139   +   MDL-58983: Fixed display of grade button in assignments when user doesn't have capability

                   The "grade" button is now hidden if a user doesn't have the capability to
                   grade assignments.

    TL-18142       MDL-60439: Enabled multi-language filter on Tags block title
    TL-18143   +   MDL-60942: Fixed format_string doesn't account for filter in static cache key
    TL-18144   +   MDL-31521: Fixed calculated questions were displaying a warning when more than one unit with multiplier equal to 1
    TL-18145   +   MDL-34389: Fixed users with capability 'moodle/course:changecategory' were able to only select current course category and not its subcategories
    TL-18146   +   MDL-42676: Fixed issue that prevented assignment submissions when grade override was used
    TL-18147   +   MDL-49995: Fixed overwriting of files to not leave orphaned files in the system
    TL-18148   +   MDL-52100: Fixed filearea to not delete files uploaded by users without file size restrictions
    TL-18149   +   MDL-54967: Fixed IMS Common Cartridge import incorrectly decoded html entities in URLs
    TL-18150   +   MDL-57431: Shuffle question help icon in Quiz is now outside the HTML label
    TL-18152   +   MDL-58888: Added sort-order for choice_get_my_response() results by optionid
    TL-18153   +   MDL-59200: Fixed an issue where a user is unable to enter assignment feedback after grade override

                   Fixes an issue where a user would be unable to enter assignment feedback
                   after grade override and if there was no original assignment grade set.

    TL-18154   +   MDL-59709: Fixed export to portfolio button in assignment grading interface for Online Text submissions
    TL-18155   +   MDL-59999: Added a status column to the Essay question grading interface within Lesson
    TL-18156   +   MDL-60161: Ensured that OAuth curl headers are only ever sent once
    TL-18159   +   MDL-60653: Fixed the incorrect indentation of navigation nodes when their identifier happened to be an integer
    TL-18160   +   MDL-60767: Fixed a visual bug causing validation errors to not be shown when saving changes to several admin settings in a single action
    TL-18161   +   MDL-60938: Fixed the rendering of users in the choice activity responses table
    TL-18162   +   MDL-61022: Added acceptance test for user groups restore functionality
    TL-18163   +   MDL-61040: Improved spacing around the "Remove my choice" link within a choice activity
    TL-18164   +   MDL-61042: Fixed undefined variable error when viewing detailed statistics report on empty lesson
    TL-18165   +   MDL-61045: Made sure the 'After the quiz is closed' review option is disabled if the quiz does not have a close date
    TL-18166   +   MDL-40790: Fixed Lesson content button to no longer run off the edge of the page
    TL-18168   +   MDL-44667: Fixed minor field existence checks in three plugins

                   The following three plugins each had one call to a database function that
                   was attempting to validate the existince of the field incorrectly. The
                   affected plugins were:
                   * Assignment file submission
                   * Assignment online text submission
                   * Multi-answer question type

    TL-18169   +   MDL-45500: Enabled ability to uninstall grading plugins
    TL-18171   +   MDL-54021: Fixed an issue where "Course completion status" block didn't show activity name in correct language
    TL-18174   +   MDL-56864: Fixed removal of tags if usage of standard tags is set to force
    TL-18178   +   MDL-59866: Added retries for connecting to Redis in the session handler before failing
    TL-18181   +   MDL-60945: Stopped unneeded completion data being retrieved in Web Service function
    TL-18187   +   MDL-34161: Fixed LTI backup and restore to support course and site tools and submissions
    TL-18188   +   MDL-37757: Added missing clean up external files on removal of a repository
    TL-18190   +   MDL-60219: The 'no blocks' setting in an LTI activity now uses the 'incourse' page layout with blocks disabled
    TL-18191   +   MDL-60443: Improved validation error message when a requested data format does not exist
    TL-18192   +   MDL-60801: User defaults are now applied when uploading new users
    TL-18196   +   MDL-24678: Fixed a race condition in the chat activities leading to multiple messages being returned as the latest message
    TL-18197   +   MDL-27230: Ensured that changes to Quiz group overrides are reflected in the calendar
    TL-18198   +   MDL-45068: Improved group import code, prevented PHP displaying notices and warning for certain CSV files
    TL-18199   +   MDL-46768: Loosened the restriction on the badge name filter to allow quotes
    TL-18201   +   MDL-57569: Fixed a large badge image being unaccessible for the future use
    TL-18203   +   MDL-60188: Implemented cache for user's groups and groupings
    TL-18204   +   MDL-60249: Ensured feedback comments text area is resizeable
    TL-18205   +   MDL-60591: Fixed forum inbound processor discarding the inline images if a message contains quoted text
    TL-18206   +   MDL-60669: Fixed duplicate entry issue when restoring forum subscriptions
    TL-18207   +   MDL-60738: Fixed Web Service theme and language parameters not being cleaned properly
    TL-18208   +   MDL-60838: Fixed Solr files upload to honour timeout restrictions
    TL-18211   +   MDL-55808: Fixed glossary entries search not working with ratings enabled
    TL-18212   +   MDL-56253: Added multilang support to course module name in grades interface
    TL-18213   +   MDL-58817: Ensured LTI icons are not overwritten by cartridge params
    TL-18215   +   MDL-60187: Ensured grade items are not created when grades are disabled

                   When editing LTI titles inline, it makes it appear in the Gradebook even if
                   the privacy option 'Accept grades from the tool' is disabled.

    TL-18216   +   MDL-60253: Ensured both LTI ToolURL and SecureToolURL are used for automatic matching
    TL-18219   +   MDL-60637: Removed unnecessary group id number validation on Web Services
    TL-18220   +   MDL-60773: Added pendingJS checks for autocomplete interactions
    TL-18221   +   MDL-60809: Fixed missing filelib include in XML-RPC function
    TL-18222   +   MDL-60810: Removed string referencing PostNuke from auth/db
    TL-18224   +   MDL-59876: Fixed the Web Service user preference name field type
    TL-18226   +   MDL-60675: Fixed an exception in single selects without a default value
    TL-18227   +   MDL-60693: Added multilang filter to activity titles in course backup and restore
    TL-18228   +   MDL-60741: Refactored admin purge caches page to call admin_externalpage_setup first
    TL-18229   +   MDL-60789: Added length validation rule for a workshop title submission
    TL-18231   +   MDL-60433: Fixed users being able to view all groups even if they were not allowed to
    TL-18233   +   MDL-60104: Fixed SCORM description text to no longer extend outside the page
    TL-18240   +   MDL-60485: Fixed being able to change grade types when grades already exist
    TL-18252   +   MDL-59820: Removed unnecessary CSS class on calendar

                   The course selector now uses the standard HTML/CSS as used by other single
                   selects.

    TL-18260   +   MDL-59532: Fixed check_update callback failing when the activity uses separated groups
    TL-18265   +   MDL-59619: Fixed get_fields Web Services not working properly if database has no fields
    TL-18266   +   MDL-59627: Fixed data_search_entries function in the database module wasn't calculating total count correctly
    TL-18267   +   MDL-59649: Fixed type of content exporter field to the correct value
    TL-18270   +   MDL-59453: Fixed filtering of lesson content in external functions

                   A new 'deleted' column for forum posts was introduced. Now deleted posts
                   and discussions display a placeholder instead of the original text. Purging
                   of user data was modified to set the new deleted flag and empty the title,
                   and body, of the forum posts and discussions. Previously the title and body
                   were replaced by a placeholder instead of dynamically showing it.

    TL-18539   +   MDL-62200: Prevented modals from adding another backdrop when being loaded in from another modal
    TL-18655   +   MDL-62820: Made sure questions text is properly encoded before display after question bank import
    TL-18656   +   MDL-62790: Added capability check in core_course_get_categories for Web Service
    TL-18660   +   MDL-62233: Added validation on callback class when exporting to portfolio

                   Validation had been applied to the callback class in a previous Totara
                   patch. This adds the Moodle solution for compatibility.

    TL-18661   +   MDL-62232: Improved validation when exporting forum attachments to portfolio

                   Validation has been added in a previous Totara patch. This aligns it with
                   Moodle's solution for compatibility.

    TL-18662   +   MDL-62210: Improved validation when exporting assignments to portfolio

Contributions:

    * Jo Jones, Kineo UK - TL-18640
    * Michael Geering, Kineo UK - TL-17973
    * Russell England, Kineo USA - TL-17977


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
