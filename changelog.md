Release 13.11 (6th August 2021):
================================


Security issues:

    TL-31873       Improved the security of the shibboleth logout functionality
    TL-31886       Fixed an uncontrolled recursion vulnerability in URL downloader plugin

                   An uncontrolled recursion weakness was fixed in the 'URL downloader'
                   plugin. This posed a risk of recursion denial of service.

    TL-31893       Bulk user download now correctly sanitises data when exporting to the HTML format
    TL-31894       Removed firstname argument from emailconfirmation string to prevent a self-registration phishing risk
    TL-31895       Improved the sanitisation of emails triggered for user to user messages

Bug fixes:

    TL-30988       The @mention selector now disappears completely when there are no results
    TL-31369       Temporary managers are now correctly notified when a user signs up to a seminar event

                   Prior to this patch, when a user signed up to a seminar event requiring
                   both the selection of a job assignment, and manager approval and selected a
                   job assignment referencing an active temporary manager, that temporary
                   manager would not be correctly notified of the signup.

                   This has been fixed, and the temporary manager will now be correctly
                   notified at the time of signup.

    TL-31427       Fixed capability check for selecting audience restrictions when editing settings of an existing course activity
    TL-31467       Seminar messages created using Weka editor are now correctly formatted when being sent
    TL-31469       Fixed HR import field mapping when the original field name also exists in import CSV
    TL-31475       Guest users no longer experience an exception when viewing the comments tab for resources

                   This patch refactors capability checks for all Engage interactions
                   (comments, bookmark, like, share) for resources and playlists to ensure
                   consistent read-only behaviour for guests.

    TL-31654       Seminar cancellation notifications now correctly inform if a manager was also notified
    TL-31728       Fixed the 'Recently viewed' block to use a courses custom image when one has been set
    TL-31757       Ensured user name report builder display classes process html entities correctly for export
    TL-31902       Fixed error when editing user course completion for course containing URL resource

                   Note this only affects courses that are restored from an older version of
                   Totara into version 13 or above.

API changes:

    TL-31067       Changed the architecture of the mobile plugin to allow subplugins

                   This will allow for greater extensibility of the back-end of the mobile
                   app, along with increased ease of customisation for clients interested in
                   doing so.  The current learning queries have been moved to the first
                   sub-plugin, class stubs have been left in place in case any thing has been
                   extended and persisted queries remain the same place pointing to the moved
                   revolvers to avoid any conflicts with existing customisations.

                   The totara_mobile_me query has also been updated to return version
                   information on any enabled mobile sub-plugins, for now this is limited to
                   the current learning plugin but it allows further flexibility going
                   forwards.

Tui front end framework:

    TL-31560       Improved handling for invalid date formats within the Tui date selector component


Release 13.10 (29th July 2021):
===============================


Important:

    TL-31725       Added capabilities to roles based on the staff manager archetype

                   As part of the multitenancy solution introduced In Totara 13.0 we've made
                   changes to the staff manager role adding missing capabilities. These
                   capabilities are automatically given during upgrade to Totara 13.0. This
                   may have GDPR, or other privacy-related implications for the site. After
                   upgrading to Totara 13 or higher it makes sense to review your roles and
                   permissions and potentially your site policies to ensure they align with
                   your current system behaviour.

                   What are these changes
                    --------------------------
                    During upgrade the following capabilities are automatically set to
                   "Allowed" for Staff Managers and any other custom role based on the
                   staffmanager archetype.
                    * moodle/user:viewalldetails
                    * moodle/user:viewhiddendetails
                    * moodle/site:viewfullnames
                    * moodle/site:viewuseridentity

                   What this means in practice
                    -----------------------------
                    This means that users with the staff manager role (typically users with
                   direct reports assigned) will be able to see additional information about
                   the users in the context this role is applied (typically their direct
                   reports). This information might include
                    * email (regardless of email visibility set by the user)
                    * username
                    * full name

                   Why these changes were made
                    --------------------------------
                    These changes were made to ensure consistent visibility of user
                   information as part of the multitenancy implementation.

                   What should I do about it
                    --------------------------
                    When upgrading Totara to version 13 or later, we recommend you to do a
                   review of your roles and permissions, especially the ones related to the
                   capabilities listed above. Also, consider updating the site policies to
                   ensure they align with the system behaviour to avoid any potential GDPR
                   breach.

Performance improvements:

    TL-30652       Improved the performance of course completion aggregations for the completion_regular_task

                   On large sites, especially those containing courses with multiple
                   activities, enrolling large numbers of users to these courses can result in
                   the catch-all task 'core\task\completion_regular_task' taking a very long
                   time to complete.

                   The purpose of this task is to ensure that all completion information for
                   all users enrolled in courses is correct and up to date. When users are
                   enrolled in bulk, or changes are made to courses with a large number of
                   enrolled users, the task may need to check and process thousands of
                   completion records.

                   To improve performance and ensure that the task completes in a reasonable
                   time, this patch not only streamlines the underlying check and processing
                   steps, but also introduces the processing of completion records in batches.
                   Only a single batch of completion records that needs to be re-checked and
                   re-aggregated is processed in a single cron run. The following batch will
                   be processed during the next cron run, etc.

                   The patch also includes more detailed information on progress.

    TL-31156       Improved performance of displaying seminars with many events

                   Prior to this patch, the performance of the page showing all upcoming and
                   past events for one seminar did not scale well with increasing number of
                   events when the enrolment plugin 'Seminar direct enrolment' was activated.
                   With this patch the performance of this page is significantly improved.

    TL-31210       Improved performance of the \totara_program\task\recurrence_history_task scheduled task

Improvements:

    TL-30285       Allow the uploading of custom evidence data while uploading course or certification completion records

                   It is now possible to include custom field data when importing course and
                   certification completion evidence records.

                   The format for specifying custom field data is similar to what was used in
                   earlier versions of Totara. The only difference being that fields available
                   for import are no longer the same for all evidence types; these are now
                   determined by the fields defined for the evidence type selected when
                   starting the upload process.

                   Only evidence types marked as 'Available for completion import' can be used
                   during the import process.

    TL-31276       Updated URL to Product documentation and improved wording of Help tab in the Microsoft Teams application
    TL-31368       Added a script to bulk set the 'Assignment creation availability' of competencies

                   Before Totara 13, users were assigned to competencies through learning
                   plans. Totara 13 introduced competency assignment without the need to
                   create learning plans. To make this even more configurable, administrators
                   can now indicate which competencies are assignable and who can assign users
                   to them. This is done through the 'Assignment creation availability'
                   attribute of a competency. It can be set to allow users to assign
                   themselves, assign other users, both or none.

                   Without this script the only way to change the 'Assignment creation
                   availability' of multiple competencies is to open each competency and
                   manually change the attribute as needed.

                   The provided script, located in
                   dev/perform/set_competency_assign_availability, allows administrators to
                   perform bulk updates of the 'Assignment creation availability' of multiple
                   competencies. Help on how to run this script can be obtained by calling it
                   with '--help'.

    TL-31402       Increased the maximum length of course category names to 1333 characters
    TL-31713       Updated the welcome message that is sent when adding the Totara app to Microsoft Teams for the first time

Bug fixes:

    TL-30068       Fixed popover being cut off from its nearest container's boundaries

                   The display of popovers has, in some situations, been delegated to the root
                   DOM node to facilitate reliable z-index display, which was suffering from
                   stacking context conflicts. There are now two modes; 'contained' which
                   respects a parent container, and 'uncontained' which respects the root DOM
                   node.

    TL-30290       Changed visibility checks for competencies linked courses within a plan

                   Now the visibility checks for competencies linked courses are being made
                   based on the person who linked the competency to the plan instead of the
                   plan's owner. This way we have a consistent behaviour when adding courses
                   to a user's learning plan.

    TL-30394       Fixed auto-subscribe behaviour for forums that are added to the front page

                   There was an issue where the forum 'Auto subscribe' option would not work
                   correctly for forums added to the site front page. All newly created users
                   are now automatically subscribed to front page forums (if the forum setting
                   is enabled).

    TL-30413       Tui Grid component now handles zero unit GridItems correctly

                   Before this change, GridItems with zero units risked still having gutters
                   and affecting overall Grid size. Originally we thought zero units shouldn't
                   be supported, but we've found a couple of use cases now, and so this is now
                   correctly supported - no console errors are generated by Vue prop
                   validation failure.

    TL-30421       Role descriptions are now consistently formatted across the site
    TL-30662       Fixed incorrect site policy check in notifications and messages popover
    TL-30734       Fixed JSON validation for json editor throwing unnecessary debugging message
    TL-30908       Fixed an error message appearing when a user accessed a non-joined workspace with a tour enabled
    TL-31098       Fixed managers being able to do administrative approval in seminars

                   Fixed bug where managers could solely approve seminar signups that required
                   administrative approval by approving the request twice (for example, first
                   time via seminar approval form and second time via accepting the task in a
                   task block).

    TL-31106       Fixed Evidence columns being shown in Learning Plans when Evidence is disabled

                   The learning plan reports no longer include an evidence column by default
                   if evidence has been disabled for the site.

    TL-31208       Fixed course search functionality to be tenant aware
    TL-31239       Audio controls are now shown on links to external audio files
    TL-31241       Fixed reporting of Throwable errors in scheduled tasks
    TL-31281       Ensured all custom fields are visible in the custom settings of the user profile block

                   Prior to this, if a custom field was empty, it would not be displayed in
                   the custom settings for the block. This prevented administrators from
                   including or excluding the field for display.

    TL-31311       Fixed 'Assign competencies' button in competency profile to display only when the user has the necessary capability
    TL-31312       Fixed hidden categories visible in grid catalogue filter
    TL-31325       Fixed the rendering of the course self enrolment form when an associated audience is deleted

                   Prior to this fix, an error would occur when an administrator attempts to
                   view a course's self enrolment configuration form, when the dropdown
                   setting "Only audience members" had been set to an audience that had been
                   deleted.

                   With this fix, an administrator can now view the course's self enrolment
                   configuration form, despite the setting "Only audience members" still using
                   the deleted audience.

    TL-31365       Fixed bug causing unsharing a resource from a workspace to fail
    TL-31399       Improved reliability of SCORM packages saving progress with large amounts of data
    TL-31414       Ensured only tenant users are displayed in user select search
    TL-31426       Fixed issue with sending scheduled reports if dataroot folder is a symlink

                   If the dataroot folder specified in the config.php is a symlink to another
                   folder located elsewhere the attachment path wouldn't be resolved
                   correctly.

    TL-31429       Fixed error when connecting Microsoft account to Microsoft Teams virtual meetings plugin when using nginx
    TL-31431       Switched the incorrect display of the 'locked' and 'unlocked' icons when protecting a block
    TL-31433       Changed the json_editor audio node to allow an empty transcript attribute
    TL-31462       Fixed Weka editor converting external link to internal one when displayed as a card
    TL-31473       Patched YUI3.17.2 to prevent IE11 JS error in un-polyfilled iframe context

                   A previous patch introduced an ES6 feature which would normally be
                   available for IE11, however the YUI3 library dynamically creates an iframe
                   to facilitate file uploading (within Repositories for example) and that
                   iframe never receives polyfill dependencies. This fix uses the iframe's
                   parent scope which does have polyfill dependencies.

    TL-31550       Reduced the length of table prefix used in MySQL/MariaDB testing to avoid hitting table name limits
    TL-31561       Fixed temporary managers not being unassigned when a job assignment is deleted if a manager is also assigned
    TL-31624       The course administration dock has been hidden when viewing a course in Microsoft Teams

API changes:

    TL-28526       Added GraphQL mutations to mark courses, programs, certifications as viewed
    TL-31645       Added behat steps to check toolbar options in Weka Editor
    TL-31661       \page_requirements_manager::js_call_amd() can now be called without specifying the 'function' parameter

Contributions:

    *  John Phoon - Kineo Pacific - TL-31414


Release 13.9 (28th June 2021):
==============================


Important:

    TL-31000       Changed the default of the "Disable consistent cleaning" setting to "Yes" when upgrading from version 12 or lower to 13

                   With Totara 13 the new “Disable consistent cleaning” security setting
                   was introduced. Prior to this patch, upgrading from Totara 12 or lower to
                   Totara 13 left this new setting disabled, thereby introducing added
                   security but potentially leading to unintentional data loss when editing
                   existing HTML content. With this patch, upgrading from Totara 12 or lower
                   to Totara 13 will enable this setting to preserve previous behaviour in
                   order to prevent data loss.

Performance improvements:

    TL-30630       Fixed counting likes/comments in a workspace discussion multiple times in one request

                   Previously when loading a list of discussions in a workspace, the number of
                   likes and number of comments for each discussion were independently
                   queried, resulting in a large number of queries run for a single request.

                   With this patch in place when loading a list of workspace discussions, the
                   counts are performed once with the initial query, decreasing the number of
                   queries needed to return the results.

    TL-30973       Improved the performance of the Record of Learning: Course report source when used with a very large number of course completion history records

                   Prior to this patch on older databases a large number of course completion
                   history records (in the millions) would slow down the Record of Learning:
                   Course report when the Past Completions columns were added. With this patch
                   the report should now behave faster when used with a large dataset.

                   Important: This change introduces a new index on the
                   course_completion_history table, which can take several minutes to run on a
                   large Totara instance.

    TL-31089       Improved user content restriction query performance

                   Mainly on MySQL databases the user content restrictions were scaling poorly
                   with larger amounts of job assignment records. This has been fixed and
                   performance on MySQL 5.7 and MySQL 8 is now significantly improved if user
                   content restrictions are used in reports.

    TL-31095       Improved performance of reports based on the user source when certain count columns are used

                   The query part for the following columns changed to improve performance:
                    * User's Achieved Competency Count
                    * User's Courses Started Count
                    * User's Courses Completed Count
                    * Course Completions as Evidence
                    * Extensions

Improvements:

    TL-29734       Updated 'help command' into 'hero card' that contains 'sign-in', 'sign-out' and 'help' button in Microsoft Teams
    TL-30025       Created a new 'help tab' to display 'help' information in Microsoft Teams
    TL-30027       Added sign-out link for manual login settings on each static tab in Microsoft Teams
    TL-30221       Added 'Achievement Status' column and filter to the Competency Status report

                   It is now possible to filter records shown in the Competency Status report
                   on the Achievement status column thus allowing users to view only active
                   (current) achieved values.

    TL-30395       Set loglevel to "warn" in .npmrc, which suppresses "notice" level output such as the core-js advertising on npm ci / npm install.
    TL-30791       Added a logo button to use by default for Microsoft OAuth2 issuers on the site login page

                   A new option has been added to Microsoft issuers for the OAuth2 plugin,
                   named 'Show default Microsoft branding'. This ensures that any new issuers
                   of the Microsoft type meet Microsoft's corporate branding requirements.
                   Existing issuers of the Microsoft type are not affected by this change.

    TL-30794       Added config for report builder graph to specify max value for x and y axes
    TL-30797       A users competency page now uses up to 2 lines when viewing the competency graph
    TL-30852       Renamed the 'Open in new window' button in Microsoft Teams to 'Open in browser'
    TL-31009       Adjusted the default scheduled 'send_registration_data_task' task to randomise the time it is run to distribute the load

                   The change also uses an upgrade script to reset the task to ensure the
                   new 'randomised' time is set.

    TL-31021       Ensure MS teams theme CSS is compatible with older browsers
    TL-31038       Added settings option to Microsoft Teams tabs to allow the modification of existing tabs

Bug fixes:

    TL-28866       Fixed the report builder course category filter showing the hidden workspace and performance activity categories.
    TL-29257       Fixed styling of "programs" header in record of learning
    TL-29923       Clicking on a link in a navigation block no longer expands the node
    TL-29958       Report tiles no longer have the brand colour in their shadow
    TL-30032       Added the alert when users visit external url from totara msteams tabs
    TL-30057       Fixed pulling the image from Vimeo to render on resource cards when Vimeo config is enabled
    TL-30072       Fixed page advancement on multi-value multichoice questions in lessons

                   When using a multi-choice question with multiple answers in a lesson where
                   the user is allowed to have multiple attempts, the system now uses the
                   defined 'Jump to' page as defined in the answers as follows:
                    * If the answer is correct, i.e. all the correct answer(s) are selected
                   without selecting an incorrect answer, the 'Jump to' value of the first
                   correct answer is used.
                    * If the answer is not totally correct, i.e. not all correct answer(s) are
                   selected or any incorrect answer is selected, the 'Jump to' value of the
                   first incorrect answer is used.
                    * It only some of the correct answers are selected and the question has no
                   incorrect answers defined, the user will stay on the same page.

    TL-30119       The bottom of 'g' is no longer hidden in a mini profile card
    TL-30254       Fixed error being caused when mathjax filter was enabled
    TL-30359       Fixed a report builder bug where tag filter could exclude rows without any tags

                   Prior to this patch, reports that had a tag filter added did not show rows
                   without any tags assigned when resetting the tag filter to the 'Any value'
                   option after having it set to another option. This bug is fixed with this
                   patch.

    TL-30481       Fixed PHP warning if language pack cannot be downloaded and provided option to retry or choose different language
    TL-30563       Fixed bug causing enrolment error exception when default role has been deleted

                   It has been possible for administrators to delete a role whilst that role
                   has active future references (e.g. default role for self enrolment).  When
                   a user self-enrols on such a course after the role has been removed, the
                   system would throw an error.  This patch prevents removal of roles
                   referenced as default roles and also prevents system from throwing an error
                   if the role was somehow removed.

    TL-30597       Fixed the report builder course category filter showing an error message when using hidden categories.

                   Previously when there are hidden categories in a site and a user tried to
                   use the "Course Category (multichoice)" filter in a report, the category
                   picker would display an error message. With this fix categories the user
                   can see should be listed without the error message.

    TL-30599       Added relationship validation check for update_section_settings_validation graphql call
    TL-30614       Added CSS.AllowTricky to the HTMLPurifier configuration to allow page layout modifying CSS which does not directly constitute a security risk
    TL-30636       Enabled localisation for custom field checkbox options in totara catalog
    TL-30653       Updated the reCAPTCHA endpoints to use internationally accessible URLs
    TL-30654       Implemented return methods for organisation and position webservices

                   Previously,  these webservices always returned a null value (instead of a
                   'proper' value eg list of org frameworks) when the webservice was accessed
                   by REST clients.

                   This patch fixes the return methods for these webservices.

    TL-30688       Book Activity print stylesheet is now correctly applied
    TL-30689       Fixed current learning block always using default alert period
    TL-30702       Fixed an error creating the "Resources Engagement" report that happened when "Recommendations" was turned off in Engage settings
    TL-30752       Added missing multilang filter support to the Recently Viewed block
    TL-30754       The drag and drop notification on course pages no longer makes items unclickable after it disappears
    TL-30763       Improved the displayed error message for non-existent workspaces or discussions
    TL-30768       Removed extra space above logo in mobile widths
    TL-30822       Changed the Report builder table to stay below the graph in the report graph block when changing the page or sorting the table
    TL-30823       Fixed notification not being sent when seminar booking is cancelled due to user unenrolment
    TL-30833       Fixed error when checking whether competency profile can be viewed of a deleted user
    TL-30834       Added HTML sanitization when displaying draft content in atto editor
    TL-30835       Fixed the display of error notifications when attempting to approve seminar requests
    TL-30847       Improved height calculation incorporating available window height when using SCORM packages
    TL-30861       Fixed a bug when adding more than 10 programs to badge criteria

                   Prior to this patch, badge access could not be activated when there were
                   more than 10 programs assigned to the badge criteria in combination with
                   selecting the option "Complete all of the selected programs". This patch
                   fixes this bug.

    TL-30903       Fixed incorrect conversion of emojis in the JSON editor emoji node to the correct HTML entities and unicode characters
    TL-30913       Fixed member add/remove capability checks for Workspace Owner role
    TL-30930       Fixed missing password message displays when the Unmask password is selected
    TL-30936       Behat multiselect matches checks now works as expected

                   The behat steps `And the field "<name>" does not match value "<comma
                   separated value>"` and `And the field "<name>" matches value "<comma
                   separated value>"` now work correctly when pointing to a multiselect HTML
                   input

    TL-30959       Fixed notifications for performance activities not being sent in the preferred language of the recipients

                   Notifications for performance activities are now sent in the preferred
                   language of the recipient. If the recipient is an external participant it
                   will use the default language of the site.

    TL-30962       Blanked out content text field during a soft delete of a comment

                   Previously, only the content field in comment database record was cleared
                   in a soft delete; the contexttext column still showed the original comment.
                   This patch makes sure the contenttext column gets cleared as well.

    TL-30966       Fixed a bug where notify_added_to_workspace_task triggers an error if a member user is deleted before a notification is sent
    TL-30968       Fixed performance activity name not being formatted by format_string in several places
    TL-30969       Fixed bug where .ods and .xlsx spreadsheet downloads are corrupted by double-quotes in sheet title
    TL-30991       Fixed Responsive component losing resizes in rapid succession
    TL-30995       Fixed modal transitions ending early
    TL-31017       Fixed issue when creating a workspace with a description in Safari causing a navigate away browser notification
    TL-31039       Fixed notification not being sent on room change for 'One message per date' setting

                   Prior to this patch, when the 'One message per date' setting was turned on
                   in the seminar global settings and room or facilitator changes were made to
                   a session, the 'Date/time changed' notifications were not sent to the
                   participant. This patch fixes the issue, so that notifications will be sent
                   in this case.

    TL-31078       Fixed placeholder dropdown appearing when clicked randomly inside the editor
    TL-31111       Updated user details during user creation

                   Previously, when an oauth2 authenticated user first logged onto Totara,
                   only a few key fields were populated in that user's mdl_user record (eg
                   alternatename, firstname, lastname). The user had to log on a second time
                   before other fields (eg description, city, country) were updated in the
                   record.

                   This patch fixes the user creation process so that all details are put into
                   the user record on the first login.

    TL-31144       Fixed theme formatter to not force theme files to have default versions
    TL-31185       Fixed the Inappropriate Content report displaying an error when a comment with a embedded image is reported.
    TL-31191       Fixed loading of current and archived assignments on the competency profile

                   When a user got unassigned due to not being part of the assigned group
                   anymore, the competency profile and competency details page still showed
                   the unassigned competency under the current assignments. This has been
                   fixed. Unassigned competencies are now treated the same as archived
                   assignments and show up under the archived assignments list.

    TL-31212       Fixed broken engage workspace library search
    TL-31217       Removed horizontal scrollbar when viewing the activity completion report (in a course)
    TL-31251       Modified Report Builder graphs to respect the 'Maximum number of used records' setting  instead of current page limit

API changes:

    TL-30635       Made grade feedback format an optional value for get_grade_items webservice return values

Contributions:

    * David Scotson - Synergy Learning - TL-30689
    * Davo Smith - Synergy Learning - TL-30936
    * Joseph Kelly - Digital Learning (Chile) - TL-30654


Release 13.8 (19th May 2021):
=============================

Important:

    TL-30681       Fixed several issues in the migration of competencies

                   During the upgrade to Totara 13 existing competencies and the values users
                   achieved in those are migrated to the new competency achievement system. If a
                   competency was assigned to a Learning Plan prior to this patch, the migration
                   would not have created the necessary records in the new tables and as such it
                   would appear to users that they do not have any values for their competencies in
                   their Learning Plans set anymore. Furthermore, the Record of Learning did not
                   show the previously achieved values due to the new achievements being set to an
                   archived state.

                   This patch fixes this migration issue and all future migrations will create the
                   data in the new tables correctly and thus the Learning Plans and Record of
                   Learning will show the right values for users.

                   If Perform is not enabled, this patch also changes the aggregation method used
                   for competencies in Totara 13 to "Highest". Previously the default method was
                   "Latest achieved". It turned out that "Latest achieved" does not match the
                   behaviour of Totara 12 and earlier versions exactly. With "Highest" as
                   aggregation method the behaviour to achieve values in competencies now matches
                   the previous behaviour. The main difference to "Latest achieved" is that once
                   users completed a course linked to the competency or achieved proficiency via
                   proficiency in child competencies they cannot be given a value lower than the
                   minimum proficiency value. The aggregation will always set it back to the higher
                   value.

                   Another issue fixed in this patch is that the aggregation now considers the
                   actual achievement date of pathways and criteria rather than using the time the
                   task was run. This only affects the "Latest achieved" aggregation method. For
                   example, if a user completed a linked course first and then the value gets
                   changed in a Learning Plan, they will now correctly been given the Learning Plan
                   value whereas before, it depended completely on the order in which the
                   competency pathways were processed.

                   If a site has already been upgraded to Totara 13 without this patch, this patch
                   will leave the aggregation method on "Latest achieved". This patch introduces a
                   setting "legacy_aggregation_method" to change the method for all existing and
                   new competencies. Admins can change this setting to "Highest" but should
                   consider that depending on the amount of competencies and achievements in the
                   system the aggregation task on the next cron run might take some time to
                   reaggregate all existing competencies. Modifying the aggregation method can lead
                   to changes to already achieved values for users.

Security issues:

    TL-30569       Hardened security around block config data retrieval to prevent object injection

                   This change hardens the unserializing of block config data in the backup and
                   restore code and when instantiating block instances in order to protect against
                   unknown and potentially dangerous classes being injected.

    TL-30682       Backported two minor jQuery security fixes

                   The following two security fixes have been backported from jQuery 3.5.0:
                   * https://github.com/jquery/jquery/security/advisories/GHSA-jpcq-cgw6-v4j6
                   * https://github.com/jquery/jquery/security/advisories/GHSA-gxr4-xjj5-5px2

Improvements:

    TL-27036       Added setting to use X-Accel-Redirect for NGINX to server content files directly from S3 cloud
    TL-30509       Hyphenation applied to Engage user-generated text

                   Before this change, no hyphenation was applied when words were broken into
                   pieces to wrap onto new lines. This can be difficult to read for some people,
                   and so hyphenation has been added when the browser cannot safely force a whole
                   word onto a new line.

    TL-30729       Adjusted some settings for the Learn Professional flavour

                   These changes were made for the Learn Professional flavour:
                    - Added Programs to the enabled features.
                    - Removed Certifications from the enabled features.
                    - Removed Position hierarchies from the enabled features.

Performance improvements:

    TL-30540       Improved the performance of the workspace page when loading discussions

                   This patch adds missing indexes on the totara_comment table and drastically
                   reduces the amount of unnecessary queries being triggered. In addition, where
                   possible, GraphQL queries on the workspace page are now requested in batch to
                   reduce the amount of Ajax requests on that page. Overall, this will improve the
                   performance on this page significantly, especially if there are a lot of
                   discussions and comments in the database tables.

    TL-30547       Improved the initial load times for the grid catalogue on sites with large numbers of categories

                   We identified that one of the main culprits slowing down the initial page load
                   on larger sites was the default category filter. This patch updates the
                   catalogue caches so that the first time you visit the page they will prime via
                   bulk queries rather than running several queries per category in the system. For
                   any sites with a large number of categories that still experience performance
                   issues after upgrading, we recommend turning off the category filter. Simply by
                   viewing the catalogue, clicking the "Configure catalogue" button, navigating to
                   the "General" tab, and setting "Browse menu" to none.

Bug fixes:

    TL-28867       Fixed modal backdrop issue caused by overlapping modals
    TL-29284       Weka editor ImageBlock node context menu is no longer cut off

                   Incorrect CSS positioning was applied to the ImageBlock, by wrapping the desired
                   elements and setting position on that wrapper, we can avoid working against how
                   Weka and overflow/positioning techniques work.

    TL-30013       Fixed 'Lock after final attempt' setting not working properly
    TL-30023       Updated two MSTeams bot command strings
    TL-30037       Updated help text for MSTeams messaging extension
    TL-30047       Fixed theme settings being rendered with the UI for the currently active theme instead of the theme being edited
    TL-30236       Fixed incorrect URL saved when images are used in workspace replies

                   Previously, when images are used in replies in Engage workspaces, an incorrect
                   URL was saved, resulting in errors being shown when trying to edit these
                   replies.

                   This has now been fixed. New discussion replies that include images will now
                   result in a valid image URL being saved. However, replies created previously
                   might still have an invalid URL. 

    TL-30403       Fixed JavaScript error when uploading files into a course using drag-and-drop
    TL-30411       Fixed a bug preventing the reordering of playlist cards via drag-and-drop
    TL-30412       Fixed username encoding in Engage and Perform

                   Previously, in several places throughout Engage and Perform special characters
                   in the fullname for users were displayed in an encoded form. This has been fixed
                   in the core user resolver and will affect all places where the core_user GraphQL
                   type is used and the requested field is 'fullname'.

    TL-30424       Fixed an issue where the Weka editor was not clickable in Safari when editing static content in a performance activity 
    TL-30435       The Perform module is no longer displayed in the "activity type" filter in the Grid catalogue
    TL-30437       Fixed accessibility issues on Engage survey cards
    TL-30438       Changed theme settings controller to admin controller
    TL-30458       Fixed wrong encoding in filter options on 'Your resources' page
    TL-30469       Fixed archive assignment button not showing on the competency details page for active assignment when the user also has archived assignments
    TL-30472       Fixed multilang filter in report titles not being applied
    TL-30473       Fixed inconsistencies for type description labels in all hierarchy items
    TL-30475       Updated the user search SQL used when adding seminar attendees to use named parameters

                   Previously there was an issue when multitenancy was enabled where the wrong
                   parameters would be used for the wrong arguments in the SQL. Changing these to
                   explicitly named parameters makes sure this no longer happens.

    TL-30495       After opening a tui dropdown menu, right clicking outside of it now closes it
    TL-30503       Removed excessive filtering of Weka editor content in playlists, workspaces, and comments

                   Fixed bug with removing content between < and > brackets when using the Weka
                   editor in playlist summaries, workspace descriptions, workspace discussions, and
                   comments across Engage.

    TL-30518       Fixed Engage survey options sometimes appearing in a random order
    TL-30522       Prevented the sending of notifications in a muted workspace
    TL-30523       Deleting a custom tenant logo now correctly reverts the logo the custom site logo rather than the default Totara logo
    TL-30526       Fixed image URL showing incorrectly in new discussion notifications
    TL-30538       Fixed mislabelling of time created resource field when configuring the Grid catalogue
    TL-30543       The comment entry box now scrolls to the correct location after clicking the Comments link
    TL-30545       Replying to a comment now scrolls to the Weka editor window
    TL-30548       Fixed the Tui style resolver to format content based on dev/prod mode
    TL-30550       Fixed the accessibility of the dialogue used when adding a private resource to a public playlist

                   When adding a private resource to a public playlist, a modal appears warning the
                   user that the resource is to be made public. This modal now has an appropriate
                   ARIA label.

    TL-30552       Fixed display of preview images for resources, workspaces, course, programs, and certifications uploaded as SVG images
    TL-30556       Fixed invalid upload issue resetting theme image back to its default
    TL-30557       Fixed an overflow issue with the at-mention popover within the comment area in Weka editor
    TL-30573       Fixed theme inheritance for custom theme images

                   A theme should not inherit any custom theme settings applied to any of its
                   parents. This functionality has been removed.

    TL-30579       Added XSS risk to theme settings capability
    TL-30581       Fixed cleaning content when updating an article
    TL-30583       Share and like buttons on a resource are now circular in IE11
    TL-30585       Fixed updating resource name when updating question
    TL-30600       Added a maximum length validation for perform section title in the GraphQL mutation
    TL-30601       Added a maximum length validation for performance activity respondable element title in the GraphQL mutation
    TL-30611       Fixed JSON parsing with an empty string in the performance activity section content Vue page
    TL-30613       Added a maximum length validation for perform element identifier in the UI and in the GraphQL mutation
    TL-30621       Added a maximum length validation for workspace name on the update GraphQL mutation
    TL-30626       Fixed Vue warning when adding a private resource to a public playlist
    TL-30639       Fixed an error when reviewing a lesson activity with an essay page
    TL-30642       Fixed HTML cleaning issue when returning the empty message for the quick access menu 

                   The quick access menu webservice triggered an error during the validation of the
                   return values for some languages due to clean_text modifying the HTML in the
                   message.

    TL-30655       Engage survey title now takes up the full width when answers are short
    TL-30663       Fixed Weka editor console error when saved embedded video were still loading
    TL-30668       Fixed undefined functions within the exception handler on early exceptions
    TL-30695       Fixed display of survey answers at narrow widths in IE11
    TL-30761       Embedded audio files in the Weka editor can now be deleted
    TL-30764       User tours URL matching changed to anchor to the end of string

                   Previously, URL matching in user tours was done as a substring search. This
                   resulted in URL pattern "index.php?id=1" to be matched to "index.php?id=11".

                   The fix anchors patterns to the end of the string, so pattern "index.php?id=1"
                   will match only URLs ending on "index.php?id=1" but not "index.php?id=11". To
                   allow that specifically, the pattern should have wildcard "%" in the end
                   (index.php?id=1%).

                   To maintain the old behaviour for existing user tours, "%" will be added to the
                   end of the existing patterns during upgrade.

    TL-30777       Fixed an issue where DDL queries were missing table name and database name conditions

                   On MySQL some DDL queries to determine the existing constraints on a table did
                   not include the table name and the database name. This could have led to issues
                   on upgrades when there are multiple sites on the same database server with
                   different versions.

    TL-30828       Fixed an error showing when opening the long text question preview in performance response reporting
    TL-30848       Removed additional spacing under the footer when there are a lot of related items associated with a resource
    TL-30856       Fixed text containing HTML elements being stripped from Weka content
    TL-30857       Fixed quotation marks in Weka editor in Learn being converted to HTML entities
    TL-30871       Fixed reaggregation of assigned users not being triggered if aggregation method of competency changes
    TL-30882       Fixed visibility checks for allocated users when viewing submissions in assignments


Release 13.7 (28th April 2021):
===============================


Security issues:

    TL-30567       Fixed XSS vulnerability exposed through the redirect_uri parameter LTI authentication
    TL-30568       The mnet authentication keep alive method now follows standards and uses $DB->get_in_or_equal
    TL-30570       Fixed "Protect user names" configuration not working as expected for unconfirmed users on forgot password page

                   Prior to this patch, the form for forgotten passwords could display error
                   messages revealing the unconfirmed state of self-registering users even
                   though the 'Protect user names' security setting was activated. Also, when
                   the 'Allow accounts with same email' setting was activated, the same form
                   could reveal in an error message that an email was used by multiple users.

                   This patch prevents these error messages from showing when the 'Protect
                   user names' setting is activated.

Performance improvements:

    TL-29551       Improved the load times for the 'participants list' within the assignment activity

                   We became aware of an issue within the assignment activity when appearing
                   within a course with hundreds of thousands of enrolled users.
                   It now better manages the situation by only loading required data.

    TL-30329       Improved the load times for the user profile page by optimising the user profile block

                   While load testing during the Totara 14 QA we became aware of a performance
                   issue on the user profile page that affected all users. Blocks on the page
                   were repeatedly hitting the database for excess information that was not
                   always needed. In situations where the user had a large number of course
                   enrolments the page may take several seconds to load.

                   The poor performing block now makes full use of caching to optimise how it
                   fetches data, and to ensure data is only fetched once regardless how many
                   instances of the block are on the page.
                   We also added page limiting to the enrolment information fetched for the
                   user, limiting it to just the amount of data that is required to display
                   the page.

Improvements:

    TL-10392       Improved how scheduled reports are handled when they do not contain any data

                   Previously empty reports were still generated, saved to disk, and sent to
                   users. They are now generated, and if the result is empty, no longer saved
                   to disk or sent to users.

    TL-29554       Rephrased the options for the 'email display' profile setting to better reflect actual behaviour
    TL-29615       Allowed all theme images to be updated on a per tenant basis

                   Added the following images to the tenant customisable theme settings:
                    # Course image
                    # Program image
                    # Certification image
                    # Resource image
                    # Workspace image

                   A breaking change was introduced with this work.
                   Any customisation developer who has extended core\theme\file\theme_file and
                   has defined or overridden the get_default_context() method should review
                   their implementation. That function no longer needs to be defined unless
                   something very custom is being done.
                   If it had been inadvertently overridden or defined tenant files may not
                   show correctly nor will they be configurable within tenants.
                   If you are unsure please reach out to us via our help desk.

    TL-29959       Added a new 'Currently enrolled?' column and filter to the 'Course membership' reportbuilder report
    TL-30087       Sorted catalogue filter options by case insensitive natural order instead of binary comparison order
    TL-30140       Fixed the display logic for ExpandCell of Table component to allow not-showing chevrons in non-header rows

Bug fixes:

    TL-29751       Removed the option to add blocks on the my appraisal page

                   It is not possible to add blocks to this page but inadvertently, when
                   editing was turned on via another page, the option to add blocks was being
                   displayed. This has now been removed.

    TL-29836       Reset seminar signup fields when signup is reused to prevent notifications from being sent to wrong user

                   When a user signs up for a seminar that requires manager approval, and
                   their request is denied (or they cancel), and then they sign up again
                   later, the same database record is used. Previously, the manager and job
                   assignment fields were not cleared when the signup was reused, which could
                   result in manager request notifications being sent to the wrong
                   manager. This condition only occurs when 'Users Select Manager' was
                   enabled at the time the signup record was created or reused.

                   These fields are now cleared whenever a signup is reused. However, there is
                   no way to detect and remove the condition in existing seminar signups, so
                   the problem could persist for signups that are currently in use.

    TL-29844       Added an upgrade step to add missing link to contexts for some engage resources
    TL-29993       Updated the create workspace button so it is hidden when a workspace cannot be created
    TL-30009       Fixed some reportbuilder graph labels not being displayed as expected

                   Previously when there were a small number of labels on the x-axis of a
                   graph and at certain graph widths, some labels would disappear. Now the
                   x-axis labels are always displayed.

    TL-30011       Ensured trainer notifications in the feedback module correctly respect the 'Separate Groups' groups setting.

                   This change ensures, when 'Separate Groups' is set, that trainers within a
                   group are not sent notifications when learners who belong to no groups
                   complete the feedback.

    TL-30050       Allowed more than 20 items to be displayed when adding resources to a workspace by adding a "Load more" button
    TL-30075       Fixed the "Email address" label on the forgotten password page so that it no longer splits across 2 lines
    TL-30079       State is now preserved when resizing core re-usable Layout components

                   The main regions of two layout components did not have unique keys
                   assigned, causing non-managed state to be thrown away when the browser is
                   resized and the layout is re-rendered. This no longer happens.

    TL-30086       Fixed the display of seminar activity filters on the calendar view screen

                   Previously seminar filters were horizontally aligned and overflowing making
                   them appear unstyled. They are now vertically aligned and properly spaced.

    TL-30089       Fixed loading of more than 20 items in other user's library view
    TL-30090       Ensured date fields in Totara forms retain data correctly after a validation failure
    TL-30104       Ensured duplicate Program or Certification assignment messages are not sent for a user
    TL-30135       Fixed incorrect accessibility warning when seeing who liked a resource
    TL-30144       Fixed competency achievement record migration running out of memory on larger sites
    TL-30150       Declared a missing value in core_course_renderer::course_section_cm_availability()
    TL-30151       Fixed a bug that prevented old SCORM activities from displaying in the Totara Mobile app
    TL-30180       Fixed the formatting of program and certification summaries on the required leading page
    TL-30186       Fixed some Atto button icons taking global button foreground colour
    TL-30196       Fixed deleting users with pending subject instances or view-only participant instances

                   Previously an exception got thrown if a user gets deleted who has pending
                   subject instances or view-only participant instances in an activity. This
                   patch fixes this and will delete instead of closing pending subject
                   instances. View-only participant instances won't be touched as they do not
                   have a closed state.

    TL-30198       Fixed typo in URL causing error when using the 'Site policy records' embedded report
    TL-30217       The correct context is now referred to in explanation text on the audience assign roles page

                   Prior to this change when on the assign roles tab for an audience text on
                   the page would refer to the system context when explaining what was
                   happening regardless of whether it was a system audience or a category
                   audience.
                   This has now been fixed and the correct context is referred to in the
                   explanation text.

    TL-30222       Fixed the responsive display of SVG course icons on narrow screens
    TL-30225       Fixed adding default coursesperpage configuration setting when catalogtype is set

                   In the past when setting the catalogtype in config.php to anything but
                   'moodle', the default configuration setting for 'coursesperpage' was not
                   created. This resulted in errors on pages that rely on $CFG->coursesperpage
                   to exist.

                   This patch ensures that the 'coursesperpage' configuration setting will
                   always exist regardless of the catalogtype setting.

    TL-30249       Fixed the users' language not being explicitly set for request made by the Totara Mobile app

                   Prior to this patch the session language was being set to default, leading
                   to some unexpected behaviour in areas like access restrictions on course
                   modules. This has been rectified so that mobile sessions are always created
                   with the user's preferred language.

    TL-30253       Ensured the correct context used in the Feedback activity edit_form for templates

                   This has changed the context for the 'createpublictemplate' capability
                   check when specifying if a template should be public from system to module.

    TL-30262       Fixed alignment of text when viewing a single answer lesson page
    TL-30273       Improved handling of modal outer clicks to prevent modal closure when a popup is closed
    TL-30277       Fixed the meatball menu within Weka nodes that have children
    TL-30280       Fixed an unknown field error in Perform when reporting on anonymous activity sections
    TL-30281       Fixed the favicon resolver to resolve a protocol relative URL
    TL-30292       Added missing language strings in the admin notification sent when OAuth2 tokens need to be refreshed
    TL-30347       Fixed SQL error reporting for PostgreSQL

                   Prior to this fix the last_error value within PostgreSQL would be
                   inadvertently lost if a savepoint rollback was triggered.
                   We now ensure that the last_error value persists through the triggered
                   rollback.

    TL-30483       Moved bot entry login page out of classes folder to not expose any entry file in classes folder because users do not have access to any files in class folder
    TL-30489       Fixed error when upgrading completion evidence that do not have an evidence type
    TL-30572       Broken PHP polyfills were replaced by Symfony PHP polyfills
    TL-30575       Fix ventura appearance links and settings

                   Ventura specific admin links are no longer available when current selected
                   theme is not Ventura.

    TL-30644       Fixed weka editor user mention query returning users from other tenants

Tui front end framework:

    TL-30336       Updated Tui NPM dependencies

                   The NPM libraries Tui depends upon have all been updated to ensure we have
                   the latest security and bug fixes.
                   They were at the same time switched over the exact versions to ensure
                   consistent builds across the many development, testing, and automation
                   environments that we work with.

Contributions:

    * Alex Morris at Catalyst - TL-30281
    * Brad Simpson at Kineo USA - TL-30186, TL-30262
    * Julie Prescott at Innovate-Solutions - TL-30150


Release 13.6 (24th March 2021):
===============================


Security issues:

    TL-29937       Added missing role validation in course enrolment interface
    TL-29939       Backported MDL-70822: Fixed profile access check when fetching a user's enrolled courses via web service

                   Previously, the external method core_enrol_get_users_courses didn't check
                   for each course that the acting user can view the other user's profile in
                   that course. For courses with "Separate groups" mode and enabled setting
                   "Force group mode", this could lead to visibility of user enrolments via
                   webservice when it should have been hidden. This patch fixes this bug.

    TL-29940       Backported MDL-70767: Fixed cleanup of feedback answer text to prevent possibility to store XSS and blind SSRF
    TL-29941       Backported MDL-70668: Prevented user account confirmation without valid secret key

                   An internal function was vulnerable to confirming user accounts with an
                   invalid secret key. The function has been improved to prevent this. All
                   existing places where the function are being used already provided valid
                   secret keys to the function, so did not expose a security vulnerability -
                   this proactive change was made to ensure that it cannot happen in future.

    TL-29943       Backported MDL-69844: Fixed the bulk messaging page for courses not obeying the site-wide user email visibility policy
    TL-29945       Backported MDL-69378: Fixed upload methods for enrolments
    TL-29946       Backported MDL-68486: Fixed arbitrary PHP code execution by site admin via shibboleth configuration
    TL-29947       Backported MDL-68426: Set a limit on paths length in yui_combo
    TL-29948       Backported MDL-67837: Teacher is able to unenrol users without permission during course restore
    TL-29949       Backported MDL-67782: Added a max length attribute to the personal message input box
    TL-29950       Backported MDL-67015: Improved testing around database module group access
    TL-29951       Backported MDL-65552: Fixed XSS vulnerabilities within activity results block
    TL-29953       Backported MDL-59293: Fixed checks whether current user can view online users
    TL-29954       Backported MDL-56310 and MDL-65326: Fixed privilege escalation within course when restoring role overrides 

Performance improvements:

    TL-29733       Added function to get names of enabled editors without having to load them all

                   This function gets the names of all the enabled editors without having to
                   load the editors. Using the new function editors_get_enabled_names()
                   instead of instead of editors_get_enabled() improves performance due to it
                   being used in page navigation.

    TL-29782       Optimised capability and access control checks when generating the settings navigation structure

Improvements:

    TL-29537       Fixed Tui Date selector input order

                   Tui Date selector input order assumed NZ/UK d-m-Y, it now respects
                   internationalisation

    TL-29553       Fixed autocomplete default settings used by Tui Forms

                   Tui Forms autocomplete now uses same default as Legacy Forms

    TL-29616       Converted warning text to info banner in course badges page when start date is in the future
    TL-29617       Usages of the SCSS @extend directive resulting in excessively large selectors have been removed

                   This improved IE11 developer tools time to open, and reduced overall CSS
                   bundle size dramatically.

                   It is recommended to avoid this directive unless you are extending an SCSS
                   placeholder.

    TL-29778       Added a new Report Builder column in the 'Assignment submissions' report source which displays the Assignment name with a link to the assignment activity
    TL-29852       Fixed Ventura theme showing under tenant menu when it is not the current theme
    TL-30039       Added new hook for altering of cache key in totara_report_graph block

Bug fixes:

    TL-28969       Restored seminar change notifications when only rooms or facilitators have changed
    TL-29060       Made core_renderer::favicon() always return moodle_url

                   The function was supposed to return string but some code secretly relied on
                   the internal implimentation that it had actually been moodle_url. With this
                   patch, the function now always returns moodle_url.

    TL-29064       Prevented a second request to accept site policies when using email-based self registration

                   New users are no longer required to accept site policies twice when using
                   email-based self registration.

    TL-29068       Fixed the tui theme mediator not allowing themes with numbers in their name to load
    TL-29176       Added an automatic reload of the page when an evidence type selection gets cancelled in the evidence bank

                   Previously when the user selected, then cancelled an evidence type to add
                   to his evidence bank, the selected option was not automatically redisplayed
                   in the list of available evidence types. It was only when the user selected
                   another evidence type or refreshed the page that the correct list of
                   evidence types was displayed. This fix forces a page refresh so that the
                   correct options are displayed.

    TL-29206       Fixed user data purge for users no longer assigned to programs and certifications

                   Before the fix, if a user was not assigned to a program or certification at
                   the time of a data purge, their completion data was not being deleted. If
                   this completion data is unwanted then a data purge should be reapplied.

    TL-29274       Fixed typo in totara message task_description string
    TL-29277       Hid the 'reset profile for all users' button when there are no custom user profiles
    TL-29307       Fixed tenant parameter validation for fetching styles

                   Fixed styles_debug to correctly interpret the tenant parameter. A bug
                   caused the tenant parameter to be incorrectly interpreted, thus ignoring
                   the tenant parameter and loading site CSS settings instead.

    TL-29394       Converted 'Required grade' label in 'Course completion' report source to a language string
    TL-29404       Added notice informing users that updating MS Teams virtual meeting rooms will cause their settings to be reset

                   Due to limitations of the Microsoft Graph API for meeting rooms, it is not
                   currently possible to update a room. In order to work around this
                   limitation, when meeting times are changed the MS Teams virtualmeeting
                   plugin deletes the existing room and creates a new room with the correct
                   times. This patch adds a warning to the seminar event edit screen, so that
                   room creators know to check their meeting settings in Teams after update.

    TL-29418       Fixed responsiveness of embedded YouTube and Vimeo media

                   Embedded YouTube and Vimeo media are now responsive when there is not
                   enough width to display with their configured size

    TL-29473       Fixed MS Teams theme so that it inherits some settings from Ventura theme

                   Before patch MS Teams theme inherited only default settings from Ventura
                   theme, so administrators cannot change colors, fonts, etc.

                   Now, any custom CSS setting from Ventura theme will be applied in MS Teams
                   application as well.

    TL-29480       Fixed recommenders crashes when there is no users or items data

                   Fixed recommenders not being able to provide any recommendations when there
                   is no users, content items (e.g. resources), or interactions exists within
                   any single tenant.

    TL-29535       Fixed language pack issue when using program notification

                   The German language pack didn't take effect when doing local customisation
                   in a part of Program messages. With this patch, Local Customisation shows
                   the preferred language edited string correctly.

    TL-29565       Prevented creating job assignment specific subject instances where the job assignment no longer exists
    TL-29578       Fixed a bug where some videos would no longer be centered, and fixed handling of percentage widths in media plugins
    TL-29608       Fixed the issue of mixing hidden workspace with setting enable audience-based visibility which show the hidden workspaces to the non member users

                   Prior to the patch, when the global setting Audience-based visibility is
                   enabled, and a hidden workspace was created, then a non member users of
                   that workspace were able to see the workspace. Which it was an actual bug.

                   With this patch applied, the hidden workspace will no respect the setting
                   Audience-based visibility and non member users of the hidden workspace will
                   not be able to see the hidden workspace.

    TL-29624       Added manager id to the seminar 'Event booking request created' event log
    TL-29629       Fixed notifications messages formatting

                   Fixed notifications formatting issue when some notifications (mostly Engage
                   related) where sent as single line text instead of being formatted as plain
                   text.

    TL-29634       Fixed theme panel button colour not applying to non-Tui buttons
    TL-29664       Fixed seminar 'Sign-up' report 'Booked by' filter
    TL-29665       Top level tenant course category visibility is synchronised with tenant suspension
    TL-29667       Fixed dock overlaying main navigation logo on small screens
    TL-29724       Fixed theme colours not applying properly to navigation on mobile-sized screens
    TL-29749       Fixed an error when viewing evidence reports that only have the 'Name' column shown
    TL-29785       Allowed users who have the ability to create programmes or certifications in sub-categories to create them directly from the catalogue

                   Previously, the 'Create program' and 'Create certification' options would
                   only appear for users with the ability to create those learning items at
                   the site level. Creation options are now available for users who can create
                   items in any category or sub-category, even if they cannot create at the
                   site level.

    TL-29791       Fixed external participants being unable to view files uploaded to static content elements in a performance activity
    TL-29795       Fixed display of the 'Featured Links' block when using random display of gallery type tiles

                   This resolves an issue where the first tile would always be the same tile
                   and would not be randomised correctly.

    TL-29800       Fixed the Seminar update instance to skip the calendar update if there is a minor seminar changes

                   Previously if existing seminar is updated without changes it will re-create
                   all calendar entries which takes a lot of time to process it if the seminar
                   has 100+ events.

                   Now it fixed, the calendar entries will be updated if one value of these
                   fields is changed:
                    # Seminar name
                    # Seminar description
                    # Seminar short name
                    # Seminar calendar display settings
                    # Seminar show entry on user's calendar

    TL-29854       Fixed an overflow issue with the Tui checkbox component
    TL-29862       Fixed @mention and #hashtag suggestions appearing below the editor in some situations
    TL-29869       Weka fallback text area is now the same width as the editor was
    TL-29907       Removed unnecessary recursive method call in perform activity schedule tasks
    TL-29911       Added missing format_string() for Program & Certification names that are displayed on the Required Learning page
    TL-29920       Fixed animated gifs on the grid catalogue

                   The catalogue creates previews for learning item images in order to reduce
                   file size and load times. In the case of animated gifs, this was creating a
                   static image of the first frame. Previews will no longer be used for .gif
                   files, allowing them to be animated on the catalogue.

    TL-29924       Fixed display of the course participants page when the user is not in a group
    TL-29929       Fixed an edge case where old Uniform/Reform form field errors can stick around when related fields are edited
    TL-29960       Fixed theme loading in theme settings with minimisation of API changes

                   Previously theme settings relied on the global moodle page object to
                   determine the correct theme to use. This proved to be problematic as the
                   moodle page object is not set up in all scenarios, especially for GraphQL
                   requests. This fix causes the front-end components to pass the theme, set
                   up during the page request, to the API's that needs it in order to load the
                   theme settings.

                   A debugging message will be logged if the theme config parameter is not
                   passed for a specific API and the default config specified theme will be
                   used. To avoid any debugging message, or default theme config being used,
                   always pass theme config where possible to any theme settings API.

    TL-30045       Fixed exception that could occur with FormScope validators
    TL-30052       Ensured the selection of available Organisations and Positions are in an alphabetical order when using Self-registration with approval

Contributions:

    * Wajdi Bshara from Xtractor - TL-29060


Release 13.5 (24th February 2021):
==================================


Security issues:

    TL-29223       Added sanitisation and filtering to customfield textarea output

                   As part of an investigation into filtering of other custom field types, we
                   discovered that textarea custom field values were not being correctly
                   sanitised for output, and filtering (for example Multi-Language filtering)
                   was not being applied.

                   User-submitted textarea values were sanitised on input, so it would be
                   difficult for users to exploit this bug for cross-site scripting without
                   access to the database.

                   Textarea custom field values are now being sanitised and filtered on
                   output.

New features:

    TL-29235       Modified the recommender engine to use user profile data

Performance improvements:

    TL-29347       Improved performance of get_records_menu function

                   The get_records_menu function was calling array_shift a huge number of
                   times. All the _menu dml functions have be re-written to be more efficient.

    TL-29351       Added static cache to improve performance of the normalize_component function
    TL-29353       Improved performance of fix_table_names in Database Layer functions

                   This patch makes an improvement to a core function in the database layer to
                   reduce the number of expensive function calls.

Improvements:

    TL-11308       Added an aria-label attribute when setting link type to a course for a legacy competency
    TL-26729       Fixed Tui Modals so they now check for accessible models on every title change
    TL-28814       Moved Torara course completion import to an adhoc task

                   As part of this change a new 'Processed' column has been added to the
                   'Completion import: Certification status' and 'Completion import: Course
                   status' embedded reports. On upgrade, this will need to be manually added
                   or the report restored to default settings for it to show.

    TL-28971       Added Course completion status column to the Record of Learning: Courses report source
    TL-28978       Improved accessibility of admin menu settings page
    TL-29019       Added client-side validation when adding virtual rooms to a seminar session
    TL-29202       Added a discovery call to Zoom virtual meeting plugin so that it only attempts a meeting update if the date and/or duration have actually changed
    TL-29205       Improved location of 'expand all' and 'collapse all' links when using expanding course topics
    TL-29354       Changed is_numeric() to is_number() in normalise_limit_from_num() database layer function

                   A debugdeveloper notice will be generated if whole numbers are not used.

    TL-29377       Included an upload transcript button on audio file block

                   "Upload transcript" button appears on the weka audio file block when
                   uploaded only for the first time.

    TL-29422       Created a new notification when a new discussion is posted in the workspace

                   Workspace members will now receive a notifications when a new discussion is
                   posted in the workspace

    TL-29430       Converted reset tour link to a button to improve accessibility
    TL-29561       Improved alignment of topics with long names and collapsible topics
    TL-29563       Removed incorrect direct use of phpunit_util from tests

Bug fixes:

    TL-27159       Added the ability for the mobile plugin to remove rejected push notification tokens

                   Previously if AirNotifier rejected a push notification's token because
                   Google Firebase Cloud Messaging reported it as being invalid, the error was
                   ignored.

                   Now it is logged, and the invalid token is removed from any devices using
                   it.

    TL-28418       Fixed unread message count badge on Totara Mobile iOS app when using push notifications
    TL-28472       Fixed theme settings not applying on Edge Legacy
    TL-28765       Fixed memory limit exceeded when loading performance activities with a large number of section elements
    TL-28942       Improved accessibility of course topics format
    TL-28962       Fixed competency criteria aggregation allowing 0 required items
    TL-28997       Fixed filtering of location custom field values

                   Previously, location custom field values were filtered on input. When the
                   Multi-Language filter was enabled, this resulted in a Multi-Language value
                   being saved in the user's current language only, while values in other
                   languages were lost.

                   This has been fixed, and new Multi-Language values in location custom
                   fields will work as expected for users viewing the value in other
                   languages.

    TL-29052       Fixed email mustache template to use colours from theme settings
    TL-29153       Fixed theme settings capability issue during site upgrade

                   During site upgrade, using the web interface and upgrading from versions
                   earlier than 13.0, debug messages are thrown in the error logs and the HTTP
                   request for styles might fail because of a capability check for a
                   capability that might not be installed yet.

    TL-29221       Indicated user's preferred language when making Microsoft Graph API calls

                   This patch forwards the user's language when creating MS Teams virtual
                   meeting rooms, so that the resulting room info, which is generated by the
                   Graph API, is in the room creator's language.

    TL-29323       Fixed theme settings to use theme assigned to user instead of theme defined in config
    TL-29368       Stopped an 'Unsaved changes' message when saving a form after uploading files via an atto editor
    TL-29384       Hook added to extend list of categories with CSS variables in theme settings

                   Clients can now use the hook \core\hook\theme_settings_css_categories to
                   extend the list of categories in theme settings that contains CSS variable
                   settings

    TL-29391       Fixed the ability to use a default category

                   Since we added new hidden system categories in Totara 13.0 it has been
                   possible to enter a broken state by deleting the default "Miscellaneous"
                   category, in some cases this would lead to the system categories being used
                   as defaults. This caused several issues, the most notable of which is the
                   create course/program/certification forms would be broken. We've rectified
                   the issue by setting the default category to a non-system category,
                   recreating "Miscellaneous" if necessary. And making sure that system
                   categories are not used by default.

    TL-29392       Fixed an issue with Microsoft Teams where the 'tap area' of a card was preventing contents being inserted via the messaging extension

                   The tap area has been replaced by a button matching the catalogue details,
                   'View' or 'Go to'

    TL-29393       Added missing admin_externalpage_setup() to scheduledtasks.php
    TL-29406       Fixed badge notifications created with Weka editor displaying as JSON code
    TL-29409       Added missing language strings for recent versions of Totara Mobile app

                   Several new language strings were added to the Totara Mobile app since the
                   release of Totara 13, but not added to Totara and AMOS to be translated.
                   These have now been added and will be available in the translation and
                   language string customisation systems.

    TL-29415       Fixed virtual meeting information display on seminar room details page

                   Several fixes have been made to the virtual meeting information card:
                    * Made card visible to managers approving booking requests
                    * Prevented showing the card to learners when they should not see virtual
                      meeting information
                    * Hid the 'Host meeting' button from non-owners as only the meeting owner
                      can access the host URL
                    * Fixed some accessibility issues

    TL-29417       Fixed inconsistent filtering of custom field text values

                   As part of an investigation into filtering of other custom field types, we
                   discovered that filtering (for example multilang filtering) was being
                   applied to text custom field values when displayed in report builder, but
                   not in other areas.

                   Text custom field values are now consistently formatted for display.

    TL-29429       Fixed memory issues and improved performance of evidence migration
    TL-29431       Fixed 'Number of Attendees' report builder column for seminar event report
    TL-29433       Fixed 'Can not find data record in database' error when seminar virtualmeeting room was used
    TL-29434       Fixed 'Booking status' report builder column for seminar event report
    TL-29436       Fixed theme_config loading issues in theme settings
    TL-29443       Fixed a redirection problem of the Find learning tab on Microsoft Teams
    TL-29444       Fixed rendering of graphs when exporting reports to PDF
    TL-29445       Fixed redirection to home page after adding missing required profile data when user logs in via OAuth 2
    TL-29446       Added login image and footer to tenant-customisable theme config
    TL-29464       Fixed upgrade step issue when creating Learning Plan assignment types for Programs introduced via TL-24703
    TL-29465       Fixed a typo for seminar manager approval help string
    TL-29560       Fixed caseless searching of seminar room, asset, and facilitator dialogs when non-ascii characters are used
    TL-29562       Ensured the learner is returned to the course when using guest enrolment
    TL-29576       Fixed the display of questions in a quiz activity for the Basis theme
    TL-29583       Fixed missing aria-label when adding new groups on admin menu settings page
    TL-29609       Fixed breadcrumbs on the certification details page
    TL-29610       Fixed missing escaping of table names in ORM has_many_through and has_one_through relations
    TL-29618       Fixed incorrect event observers and hook watchers reset in PHPUnit tests
    TL-29619       Updated link to event page in seminar notification for virtual meeting creation failure

                   This patch contains an upgrade step which replaces the
                   '[session:room:link]' placeholder in the global 'Virtual meeting creation
                   failure' notification template with '[seminareventdetailslink]', and also
                   updates the placeholder in any seminar activity notifications linked to
                   that template. If you have customised the 'Virtual meeting creation
                   failure' notification in any seminar activities, we recommend replacing the
                   placeholder by hand.

    TL-29625       Added inline documentation to explain the purpose of, and ensured that $PAGE->context is set for, the server error page.
    TL-29635       Ensured that the correct method to detect whether tags are enabled is used in modedit.php

API changes:

    TL-29345       Updated PHPUnit to prime and store the GraphQL schema cache between tests

Contributions:

    * Russell England - Kineo USA - TL-29635


Release 13.4 (26th January 2021):
=================================


Important:

    TL-29285       Fixed incorrect seminar notification for users with event role in other courses when event is cancelled

                   In Totara 13 prior to this patch, when a seminar event was cancelled or
                   deleted, the code that generated the cancellation notification mistakenly
                   loaded a list of users who held an event role on any seminar event, rather
                   than the current event.

                   This caused an event cancellation notification to be sent to users (and
                   their managers) who had nothing to do with the affected seminar. This has
                   now been fixed.

                   Sites that use seminar event roles are strongly encouraged to upgrade.

Security issues:

    TL-21540       Fixed potential XSS bug in developer debugging messages

                   Prior to this patch, the debuginfo part of developer debugging messages was
                   not properly escaped, which could lead to a situation where a cross-site
                   scripting attack was possible. The debuginfo message is only ever sent to
                   output when 'Debug messages' is set to developer, and
                   'Display debug messages' is on. This should never be the case on a
                   production site. Nevertheless, it is a potential attack vector on staging
                   or development sites and has been fixed.

New features:

    TL-28886       Created Zoom Meeting virtualmeeting plugin for use with seminar sessions

                   See
                   https://help.totaralearning.com/display/TH13/Working+with+virtual+rooms for
                   more information on using the new virtualmeeting plugins with seminars.

Improvements:

    TL-17516       Added a 'course end date' column and filter to course report sources
    TL-24483       Improved accessibility of selected items area in the competency assignments list
    TL-28474       Added a placeholder text and changed icon colour in taglist component for consistency
    TL-28523       Added 'Activity viewed' GraphQL mutation for Totara mobile app
    TL-28606       Added aria attributes for Totara form elements when there is a validation error
    TL-28658       Added GraphQL/DB performance metrics in the footer 
    TL-28738       Added a warning on competency profile and detail pages when a relevant competency aggregation task is pending
    TL-28806       Added a 'Tenant login link' column to the manage tenants report source
    TL-28822       Added support for migration from Moodle 3.5.15, 3.7.9, 3.8.6 and 3.9.3
    TL-28914       Added support for PUT and PATCH requests to Totara cURL client
    TL-29011       Updated tag form fields to use background and accent colours from the theme
    TL-29020       Added GDPR support for virtualmeeting plugins and seminar virtual meeting rooms
    TL-29035       Improved Engage 'Your resources' page performance by loading filters via page loads 
    TL-29102       Added an error message to be displayed when single sign-on is not working on MS Teams
    TL-29109       Added visual indicators for seminar virtual meeting rooms that are not editable by the current user, because they were created by someone else
    TL-29228       Added Byte-Order-Mark to CSV optimised for Excel to improve Unicode detection in MS Excel
    TL-29256       Improved performance of the badge award cron job when using audience criteria when just one of multiple audiences is needed to be completed
    TL-29270       Improved reliability of Behat test step "I run all adhoc tasks"

Bug fixes:

    TL-25650       Updated width rules on "Recently viewed" dashboard block to not be affected by title length
    TL-26557       Fixed random PHPUnit failures caused by missing content file
    TL-27368       Fixed highlighting of the toggle switch to indicate when it has focus
    TL-28007       Fixed race condition when creating universal cache file

                   This patch fixes an issue where parallel requests try to write to the same
                   universal cache file. Previously, during installation of a fresh instance
                   all the CSS files were requested which caused the system to write to cache.
                   During cache creation the system will try to create a universal cache file
                   which stores all the cache's metadata. Due to the CSS file being requested
                   this process was triggered in parallel. This caused debugging messages
                   being triggered as the locks could not be acquired for the universal cache
                   file could to be written.

    TL-28025       Updated mobile current learning GraphQL query to use theme default images for courses, programs and certifications
    TL-28070       Fixed cache not being updated after using the course completion editor
    TL-28508       Ensured keyboard controls are trapped in Totara dialogues when opened
    TL-28510       Added correct aria attributes when viewing report builder tables to improve accessibility
    TL-28555       Increased margin between radio button and date selector form input
    TL-28657       Modified the size of the close 'x' button to 300 on the notification banner
    TL-28659       Fixed wrong size and colour for close 'x' button on toast 
    TL-28687       Fixed invalid page URL in LTI enrolment proxy page
    TL-28703       Updated form autocomplete hover background to use a standard colour
    TL-28718       Increased the width of the decorator separator line in posting new discussion form by involving the new normal prop
    TL-28769       Replaced label and form tag with div for performance activity print page and fixed style
    TL-28849       Added aria-disabled on side panel toggle button for better accessibility support 
    TL-28900       Ensured the PDF annotation review panel is hidden for 'Online text' only assignment submissions
    TL-28954       Fixed misalignment of labels when creating seminar rooms, assets, and facilitators
    TL-28989       Fixed Weka editor error on course edit page in IE11
    TL-29000       Fixed a JavaScript error when rearranging a playlist by dragging the resource image
    TL-29004       Added user-friendly error when attempting to view a hidden category in the grid catalogue
    TL-29007       Fixed conditions for displaying a warning about pending updates for appraisal assignments

                   Previously, a warning about pending updates was wrongly displayed in the
                   assignments tab of the appraisal administration when there were users that
                   had completed that appraisal, even when no updates were pending. This has
                   been fixed with this patch.

    TL-29016       Fixed formatting of multi-lang names used in competency types, scales and frameworks
    TL-29027       Fixed error creating Engage reports when Engage feature is disabled
    TL-29028       Updated workspace delete endpoint to not queue duplicate delete tasks
    TL-29032       Fixed Engage notifications to observe the recipient's language preference
    TL-29042       Fixed Vimeo video not being responsive when placed in dashboard block
    TL-29072       Fixed PHPUnit failures caused by incorrect PostgreSQL database snapshot reset
    TL-29086       Added a pending js to the Weka editor initialisation code and made the long text question response saving more robust
    TL-29095       Fixed theme settings validation for tenants
    TL-29098       Fixed popover content not being clickable
    TL-29112       Added missing 'Join now' buttons to seminar events dashboard for seminar virtual meeting rooms
    TL-29114       Prevented Totara sending any notifications to a bot when bot feature is disabled

                   Previously, when a bot was disabled, it still received a message about
                   sign-in into the system. Now to send notifications, you need to enable the
                   bot feature first.

    TL-29122       Fixed an issue that caused out-of-date course images to appear in the catalogue
    TL-29150       Fixed an error message which displayed above seminar events when event roles were enabled, but no users were enrolled with those roles
    TL-29159       Ensured notifications count is not displayed if notifications are disabled for the user
    TL-29160       Fixed the ordering of Engage content on the grid catalogue

                   When a site has multiple languages installed and potentially uses the
                   multi-lang filter, we can not alphabetically sort catalogue items by their
                   name and default to sorting by the timecreated field instead. Previously
                   the Engage resource and playlist items were not getting this value set in
                   the catalogue data, this has been rectified.

                   Note: The catalogue data will not be updated until the next time the
                   "refresh_catalog_data" scheduled task runs.

    TL-29161       Fixed an exception when attempting to edit a seminar facilitator without permission
    TL-29187       Added presentation role to tables when approving changes to a learning plan
    TL-29212       Fixed bug causing the recommendation engine to skip non-tenants when multitenancy is enabled
    TL-29217       Fixed updating of usernames when using user upload functionality

                   When updating usernames using 'oldusername' and the idnumber was present
                   the duplicate idnumber validation check would incorrectly report that the
                   username was a duplicate for users who were having their username changed.
                   The idnumber validation now works correctly with updating usernames.

    TL-29218       Fixed incorrect string component for 'noposition' and 'noorganisation' in the signup form

                   When using email based self registration and there are no positions set up
                   on the site, the signup form no longer uses an invalid string when users
                   try to sign up.

    TL-29219       Fixed keyboard accessibility of grid catalogue category drop down

                   Shift-tabbing in the category selector now moves to the previous option as
                   expected.

    TL-29244       Fixed PHPUnit failures occurring when zlib compression is not enabled

                   When zlib compression is not enabled on a test site tests will no longer
                   expect Content-Length headers.

    TL-29255       Removed aggressive user session cleanup code to eliminate some session timeouts on login page
    TL-29261       Fixed inability to remove custom room link from a seminar room
    TL-29264       Prevented changing the virtualmeeting provider for a seminar room
    TL-29269       Fixed TUI CSS being cached when caching was disabled in development mode
    TL-29342       Fixed "expand/collapse all" link showing when Collapsible topics is not enabled
    TL-29357       Restored the ability to create and edit site-wide seminar rooms with custom virtual room links

Contributions:

    * Russell England, Kineo USA - TL-29159


Release 13.3 (24th December 2020):
==================================


New features:

    TL-11172       Created virtual meeting service plugin architecture for seminar activities 

                   Created a new pluggable component to support integration with online
                   meeting service providers, an initial 'virtualmeeting' plugin for Microsoft
                   Teams, and support for virtualmeeting plugins in seminar rooms. 

                   When a virtualmeeting plugin is selected as a seminar room's 'Virtual room
                   link', the plugin will attempt to create online meetings to match the
                   sessions to which the room is assigned. This is carried out via adhoc task
                   so that the user is not blocked during API calls to meeting service
                   providers. If the plugin is unable to create or update meetings, a
                   notification will be sent to the room creator.

                   Because virtualmeeting plugins may rely on OAuth2 delegated permissions,
                   only the creator of a room may change the date and time of seminar sessions
                   to which it is assigned. There is also a limit of one virtualmeeting room
                   per seminar session.

    TL-25355       Rich text responses and file attachments are now supported via the Weka editor for the "Text: Long response" element in a performance activity
    TL-28632       Implemented bulk adding members to a workspace via an audience

                   This introduces a method to add members in bulk to a workspace by selecting
                   one or more audiences. All current members in the audience(s) will be added
                   as members to the workspace. This will happen in the background as an ad
                   hoc task to not block the interface for the users. The user who is using
                   this new feature will receive a notification once all members are added.

    TL-28877       Added the new flavour 'Learn Professional'

                   Developed a new flavour of Learn to deliver Learn Professional. See
                   https://totara.community/mod/forum/discuss.php?d=24797#p98628 and
                   https://help.totaralearning.com/display/TH13/Totara+Learn+Professional for
                   details.

Improvements:

    TL-11278       Fixed an accessibility issue in the course and category management page
    TL-11287       Improved the accessibility of the Filepicker
    TL-11290       Added the user name in role assign/unassign links

                   For course enrolment, the corresponding user name has been added to the
                   assign/unassign links so that screen readers can also read it out to the
                   user. This will assist disabled users in identifying the correct
                   assign/unassign links in relation to the user.

    TL-11313       Added accessible names to forum advanced searches
    TL-17223       Added option to make topic course format collapsible
    TL-21560       Added honoring of 'totara/cohort:managerules' capability in tenant context

                   Tenant members with the 'totara/cohort:managerules' capability are now able
                   to create or manage dynamic audiences.

    TL-21719       Added a UI for Report Builder default graph colours
    TL-24018       Improved the TUI popover component
    TL-24831       Added \core\hook\phpunit to core 
    TL-25031       Updated SearchBox and SearchFilter component usage and implementation

                   All components using the SearchBox and SearchFIlter components now have the
                   same look and feel.

    TL-25715       Added an alt text button to enhance the accessibility of images in the Weka editor
    TL-25795       Improved observers to decrease priority of recommended items that have been seen by user
    TL-25841       Added aria-haspopup as a prop for button-like components
    TL-26937       Cleaned up code and improved test coverage for component ml_recommender
    TL-27221       Improved location of error message when uploading a logo in Ventura theme settings
    TL-27230       Added a warning when switching tabs or navigating away with unsaved changes in Engage contribution modal
    TL-27330       Exposed aria label prop for draggable component
    TL-27336       Updated 'Your Library' side-panel to ensure it will always reach the bottom of the main page container
    TL-27337       Improved spacing on the log in page
    TL-27508       Improved display of progress doughnuts when viewing inside a report graph block
    TL-27624       Improved self-registration language strings
    TL-27885       Changed user profile social block name to 'contributions'
    TL-28053       Added support for audio transcript file and video caption file
    TL-28106       Added front-end tui editor abstraction so the system and user's editor preference settings are respected
    TL-28110       Added specific components and improved layout for printing performance activities
    TL-28319       Changed trash icon to remove playlists / resources to a remove icon
    TL-28384       Added support for custom footer and footer theme colours

                   Theme Appearance settings, for Themes based on Ventura, now have additional
                   settings:
                    * "Colours" tab which allows simple application of colour to the footer
                   background and text/links added to the footer.
                    * "Custom" tab which allows addition of text that will output into the
                   footer for the current Theme. This field also supports markup, so HTML
                   footers can be included and styled by a custom Theme or using the "Custom
                   CSS" setting.

                   This footer setting equates to the HTML-mode "footnote" setting found in
                   Roots/Basis Themes.

                   Note that the value of the custom footer field inherits to child Themes.
                   Within a child Theme the field can be emptied, the form saved, then the
                   inherited footer is not used.

                   The Footer region affected has also been simplified, with logout button and
                   username removed.

    TL-28487       Added ability to use pre-login tenant themes, and to customise the login banner image in tenant theme settings

                   Previously, all tenant theme settings have necessarily taken effect after
                   login, because the tenant in use is determined by the user's tenant
                   membership.

                   With this patch, administrators can toggle the tenant setting 'Enable
                   pre-login tenant themes,' which allows them to add
                   '?tenanttheme=tenantidentifier' to the site URL to create a tenant-specific
                   login page. The tenant theming will persist from session to session in the
                   same browser, until a user logs in who is not a tenant member.

                   Tenant theme settings have also been extended to include the login banner
                   image and associated alt text.

    TL-28497       Prevented interactions with private workspaces and resources from being processed by the recommender system
    TL-28514       Added 'Course viewed' log record for Totara mobile app
    TL-28591       'Login as' functionality is now using full site-level switching from course profile if possible
    TL-28597       Added virtual scroll option for taglist core component
    TL-28620       Append subject instance creation time to performance activity title in user activity listing to distinguish repeating instances from each other
    TL-28645       Added workaround for duplicate tag name detection when invalid accent insensitive collation used in MySQL

                   Note that accent and case insensitive collations are not compatible with
                   tags implementation, please consider upgrading MySQL 5.7 and MariaDB to
                   MySQL 8.0 and switching to utf8mb4_0900_as_cs collation.

    TL-28702       Improved performance of badge award cron job when using programs criteria when just one of multiple programs is needed to be completed
    TL-28708       Added Python unit tests for the Recommender engine
    TL-28724       Modified the user-to-items recommendation process to reduce the weight of items already interacted with
    TL-28747       Improved weka editor's features

                   Several features have been changed:
                   1. Weka editor now no longer supports custom extension(s) that can be
                   introduced as a part of any other plugins
                   2. Core editor's API is now in charge of defining editor variant. Weka
                   editor no longer supports custom variants that are introduced by the
                   plugins. However, the variants for weka that had been introduced by
                   different plugins prior to this change are now a part of weka and these
                   should not be used anymore.
                   3. The file metadata is no longer a part of the weka metadata returned from
                   GraphQL. However, the file metadata will now be fetched at runtime.
                   4. APIs deprecation and behaviour of APIs are changed – for more
                   information please have a look into
                    * client/
                     ** component/container_workspace/src/upgrade.txt
                     ** comoponent/editor_weka/src/upgrade.txt
                     ** component/engage_article/src/upgrade.txt
                     ** component/performelement_static_content/src/upgrade.txt
                     ** component/totara_comment/src/upgrade.txt
                     ** component/totara_playlist/src/upgrade.txt
                    * server/
                     ** lib/editor/weka/upgrade.txt
                     ** totara/comment/upgrade.txt

    TL-28917       Refactored multi-section toggle to reuse the first section instead of recreating the section in performance activities

                   Previously, when switching from a multi-section to a single-section
                   performance activity all sections were removed and the single section
                   recreated. With this patch all elements will be moved to the first section
                   and only the now-empty sections will be deleted to keep the original
                   element records.

    TL-28957       Added loading indicator to audience adder add button
    TL-28984       Added contexturl and HTML-format message to totara_mobile_messages GraphQL query
    TL-29013       Removed default package name setting for the MS Teams app

                   The MS Teams app package name must be a globally unique value; the old
                   default value has been removed and cannot be used as a package name.

    TL-29034       Added hook to override which tenant theme settings are customizable

                   Clients can now use the hook \core\hook\tenant_customizable_theme_settings
                   to define the theme settings that can be customized for tenants.

    TL-29043       Added ARIA live region to Adder component

Bug fixes:

    TL-6314        Added accessible names to smileys in HTML Settings
    TL-6392        Fixed page titles to meet accessibility standards

                   Fixed titles include
                   * competencies
                   * workspace and survey
                   * Record of Learning
                   * feedback activities
                   * forum activities
                   * quizzes
                   * catalogue, seminars and assignments

    TL-7251        Mark glossary tables as presentational to fix accessibility
    TL-7252        Mark wiki comment and map tables as presentational to fix accessibility
    TL-11279       Added text for the fieldset on the enrolment edit form
    TL-11322       Improved accessibility of checkboxes for 'View all alerts' page
    TL-11328       Fixed report builder filter page to add accessible name to inputs
    TL-12651       Added role attribute to table element for database module
    TL-23355       Prevented additional system roles from being deleted

                   Added more role ids (managerroleid, learnerroleid, assessorroleid,
                   performanceactivitycreatornewroleid, creatornewroleid, restorernewroleid)
                   to the list of roles which cannot be removed if they are assigned under
                   User Policies, and also introduced a hook to allow plugins to add to the
                   list.

    TL-25757       Added the ability to restore archived seminar sign-ups

                   When course completions are archived, any completed seminar signups are
                   also marked as archived, and kept for reporting purposes. Archived seminar
                   signups cannot be changed or removed from the system.

                   This patch introduces a new capability,
                   mod/facetoface:managearchivedattendees, which allows a user to 'un-archive'
                   seminar signups. When a user has this capability they will see a 'Manage
                   archived users' action in the drop down menu on the 'Attendees' tab, which
                   will allow them to select archived signups to be restored to a regular
                   state.

                   When a signup is restored, the attendance is set to 'Not set' and the grade
                   is set to empty. At that point the signup can be removed, or the
                   attendance/grade changed as necessary.

    TL-26449       Fixed duplicate IDs in nav menu items
    TL-26451       Added presentation role to the list item element in sub navigation
    TL-26542       Made new debounce tui function behat compatible by adding a pending
    TL-26859       Fixed embed setting for PDF course file resources
    TL-26999       Fixed content tab single section element count padding
    TL-27215       Fixed focus order bug on InlineEditing of Playlists on IE11
    TL-27502       Removed dependency on site theme settings when using custom tenant branding
    TL-27548       Tui development mode moderated content is now removed from cache when changes occur
    TL-27558       Fixed incorrect event context name in site logs

                   The event context for containers such as courses, workspaces and
                   performance activities are now prefixed by the corresponding container
                   name, rather than 'Course' for all container types.

    TL-27651       Removed 'Enrolled learning' and 'Goals' tabs from tenant audience page
    TL-27665       Fixed focus issue on buttons when enter key is pressed  
    TL-28032       Fixed themes inheriting custom settings from parent themes
    TL-28101       Fixed legacy seminar signup records stuck between 'manager approval required' and 'booked'

                   This patch adds an upgrade step that originally shipped with Totara 9, to
                   change any remaining seminar signup records with the old 'approved' status
                   code to 'declined'. All changes are logged.

                   Signups with status code 'approved' were meant to transition immediately to
                   'booked' but because of bugs or race conditions there may be records which
                   got stuck at 'approved'. The associated users would not have been notified,
                   and they would not have been able to complete the activity on the affected
                   signup. Such a bug was discovered in 2014, and an upgrade step in Totara 9
                   should have removed any affected records, but the potential for this to
                   happen exists in Totara 9, 10, and 11.

                   The 'approved' status code (50) was removed in Totara 12; approved seminar
                   signups transition directly to 'booked' with no intermediate state.

    TL-28127       Fixed incorrect image aspect ratios on 'Recently viewed' dashboard cards
    TL-28138       Fixed an issue with the enter key incorrectly refreshing the Perform manage activities page while adding a new activity.
    TL-28297       Fixed notice being thrown during web install due to config cache being created by another request
    TL-28464       Fixed public access to custom default images for playlists and resources

                   The 'Allow public access to catalog item images' setting allows
                   administrators to bypass login requirements for catalog images, in case
                   they need to make preview images available to third-parties such as
                   Microsoft Teams.

                   Previously there was a bug in this feature that prevented custom default
                   images for Engage playlists and resources, added via theme settings, from
                   being available without login. The bug has been fixed, and unit tests were
                   added to prevent future problems. 

    TL-28582       Added customised alt text for article image
    TL-28628       Ensured competency achievement paths honour disabled features for Learning Plans
    TL-28642       Fixed embedded images being lost when editing workspace discussions
    TL-28648       Fixed bulk user actions not respecting language-specific name order
    TL-28655       Added badges to user profile on install and upgrade and removed badge preferences link
    TL-28671       Fixed accessibility for multi-choice component
    TL-28689       Fixed TUI JSON mediator to return valid JSON data when file is not found instead of "/** File not found */"

                   The TUI mediators, forJSON files that don't exist, used to return "/** File
                   not found */" but this is not valid JSON syntax. This has been replaced by
                   a 'null' response instead.

    TL-28762       Fixed seminar facilitator user assignment search limit

                   The selection dialogue for linking a user to a seminar facilitator record
                   has a display limit of 1000 users; beyond that an admin needs to use the
                   search feature to select the user they want. However, the search feature
                   did not remove the 1000-record limit, preventing the selection of most
                   users in a large site. This has been fixed.

    TL-28766       Fixed missing support for report builder content restriction classes in plugins
    TL-28777       Fixed logic of delete unconfirmed users task

                   Unconfirmed users task was incorrectly using the firstaccess field to
                   determine when to delete an unconfirmed user however firstaccess is never
                   populated until the user first logs in (which requires them to be
                   confirmed). The task is now correctly using timecreated.

    TL-28779       Fixed image reset icon not displaying in theme settings

                   Fixed bug that caused the trash icon to not display for the program and
                   certification images in theme settings.

    TL-28785       Added numeric equivalent to 'any' option in assignavailability field for competencies in HR Import

                   All other valid options for for setting 'assignavailability' for a
                   competency in HR Import have a numeric equivalent to maintain consistency
                   – '3' has been added as the equivalent to 'any'.

    TL-28807       Replaced Engage Warning Modal component with core Confirmation Modal
    TL-28808       Fixed SQL in the 'In Progress' column of the Course Completions by Organisation Report Builder report
    TL-28829       Fixed invalid configuration defaults in Current learning block
    TL-28838       Removed unused recommender export classes
    TL-28844       Removed incompatible name 'is empty' filter from seminar room, asset, and facilitator reports
    TL-28853       Stopped event being fired twice when creating records in database activity
    TL-28854       Use supplied ariaLabelExtension in ImageUploadSetting component
    TL-28856       Fixed embedding external images in articles and playlists 
    TL-28862       Fixed various validation issues in the multichoice-multiselect element in performance activities
    TL-28864       Fixed microlearning resources not appearing in the 'Recommended For You' block
    TL-28865       Fixed bug where recommenders engine crashed after moving users between tenants
    TL-28879       Fixed display of playlist shares on the 'Settings' page when adding a resource to a playlist
    TL-28918       Fixed error when moving a topic item to the last item in a course topic
    TL-28929       Added upgrade step removing obsolete static content element files for deleted performance activities
    TL-28934       Fixed PHP undefined variable notice in the webservices documentation
    TL-28935       Fixed target="_blank" being removed by XSS cleaning in the Atto editor
    TL-28936       Removed the hardcoded 60 megabytes upload file size limit in the Weka editor 

                   Weka editor now falls back to the maximum bytes setting from the system
                   rather than using the hardcoded 60MB.

    TL-28941       Fixed warning about including additional name in user repository
    TL-28951       Added support to remove the interactions of the users who are not found in tenant user data
    TL-28961       Learner role now marked as compatible with role assignments in programs
    TL-28964       Fixed error when accessing courses in program or certification through current learning block
    TL-28981       Fixed Record of Learning for course to only display course records

                   Without the patch, the record of learning report was displaying the
                   non-learning course records on the report. This patch fixes the issue and
                   now only displays course records.

    TL-28982       Converted the hashtag match to a hashtag node in weka editor when export to json document
    TL-28985       Fixed string debugging parameters not working with Vue

                   When the debugstringids functionality is enabled, lang strings in Vue
                   components will now show their key and component.

    TL-28996       Fixed "out of memory" issue caused by visibility issue in the Main Menu

                   Some Main menu custom visibility configurations are incompatible with
                   changes introduced in Totara 13. If a site with these incompatible
                   configurations is upgraded to Totara 13 it will get into a state where the
                   site cannot be accessed. This upgrade fix corrects the incompatible
                   configuration preventing the site from becoming unusable.

    TL-29005       Fixed incorrect unique parameters in DML drivers
    TL-29017       Fixed custom CSS rendering for the Venture theme in IE11
    TL-29026       Fixed GraphQL error in the competency profile when an assigned competency's scale had a non-integer numeric scale value
    TL-29041       Fixed incorrect strings for performance activity visibility conditions
    TL-29061       Email obfuscation was fixed to be compatible with special characters in emails
    TL-29066       Fixed display of escaped characters in section title on performance activity content elements edit page

Tui front end framework:

    TL-26518       The TUI framework is now loaded on every page

                   Prior to this change Tui was only ever loaded when needed. This however
                   meant that components depending on attribute auto-initialisation held a
                   dependency on at least one other Tui component existing on the page.
                   The Tui framework is now loaded on all pages, ensuring that attribute
                   auto-initialisation is always available.

Contributions:

    * Alex Morris at Catalyst - TL-28101


Release 13.2 (27th November 2020):
==================================


Security issues:

    TL-28307       The correct capability is now checked when adding members to a workspace

                   Previously the access control checks made when adding members to a
                   workspace were checking the wrong capability. Any user who could invite
                   users to a workspace could through direct querying immediately enrol
                   users.
                   Access control has now been fixed and the correct capability is now being
                   checked.

    TL-28310       Improved validation of urls requested by the core_get_linkmetadata GraphQL query

                   Previously, the query to get meta data for links added in the Weka editor
                   did allow links to internal websites being added even though the current
                   user would not have access to them. This patch improves the validation:
                   Only https links and links to hosts with IP addresses from non-reserved and
                   non-private IP address ranges will be requested and parsed. Additionally it
                   introduced new config variables $CFG->link_parser_allowed_hosts and
                   $CFG->link_parser_blocked_hosts to be able to explicitly allow or block
                   hosts.

                   This also introduces a rate_limiter middleware which restricts the amount
                   of request per minute on this GraphQL query for the currently logged in
                   user.

    TL-28314       Modified help text for the HTTP Strict Transport Security setting

                   Previously, the HTTP Strict Transport Security setting just enabled HSTS
                   with a default max-age of 16070400 which the help text does not make clear.
                   The help text got extended to include more details on what response header
                   is set and what to do if a custom header needs to be set.

    TL-28315       Prevented self-scripting vulnerability when switching from raw HTML to visual mode in the Atto editor

                   By default, Totara sanitises all HTML content to prevent cross-site
                   scripting attacks. However, this sanitisation was only happening on the
                   server, when the content was saved. It was possible to type or paste code
                   into the Atto editor in raw HTML mode, and when the editor was switched
                   back to visual mode the code would be executed.

                   To fix this, Atto now performs client-side sanitisation of all HTML that is
                   entered, unless the site or activity security settings explicitly allow
                   unsafe HTML content.

    TL-28437       Removed 'sesskey' value from delete topic handling URL

                   Moved the 'sesskey' value that was exposed in delete topic URL, to POST
                   data.

    TL-28438       Fixed the logged in user's sesskey being shown in the audience dialogue request URLs
    TL-28439       Removed sesskey from the URL after restoring a course
    TL-28440       Updated legacy webservice calls to send sesskey as HTTP header rather than as a get parameter

                   The JavaScript AJAX wrapper automatically added the current sesskey to
                   every AJAX call as a get parameter, making it part of the URL. This could
                   lead to sesskey exposure via server or proxy logs.

                   The wrapper has been updated to send the sesskey in the X-Totara-Sesskey
                   HTTP header, and the confirm_sesskey() function has been updated to check
                   for a sesskey there.

    TL-28441       Removed sesskey from URL's when editing blocks
    TL-28460       Properly validated getnavbranch.php id values

                   The getnavbranch.php AJAX endpoint is used by the Navigation and Course
                   Navigation blocks to dynamically load collapsed sections of the navigation
                   tree. For most branch types, it was designed to use the parent item id to
                   load child items, but two values were allowed for the root node branch
                   type: 'courses' and 'mycourses'. As a result, the endpoint allowed any
                   alphanumeric value to be passed as an id. Also in Totara 13, a hook was
                   implemented to allow plugins to override the navigation tree, which might
                   accept any string value as a root node id.

                   String values are now only allowed for the root node branch type; all
                   other id values must be integers, to prevent any potential SQL injection
                   vulnerabilities.

    TL-28496       Removed sesskey from the URL when uploading a backup file during restore

Performance improvements:

    TL-24474       Reduced number of database queries triggered during subject and participant instance creation
    TL-28192       Reduced number of queries for end user competency profile page

Improvements:

    TL-11276       Removed fieldset around enrolled users when editing course settings
    TL-11307       Added label tag into checkbox on comment report table
    TL-21748       Updated Learning Plan template 'User driven workflow' description text
    TL-24195       Removed back to course button from quizzes when viewing in the mobile app
    TL-24531       Implemented pagination for the list of activities on the manage activities page
    TL-24540       Added pagination support for user performance activities
    TL-25244       Improved display of expandable data tables
    TL-25546       Added new report source for saved searches
    TL-25566       Ensured that like buttons display appropriately for private and restricted Engage resources and surveys
    TL-25724       Added the ability to set the values of Uniform forms externally
    TL-25790       Added support for generating top recommendations per item category in the recommender engine for users

                   Improved approach to group recommendations ensures that users will have
                   enough recommendations in each category (such as micro-learning,
                   workspaces, resources, etc)

    TL-25855       Converted some section dividers in resources and workspaces to a neutral colour
    TL-26027       Added behat tests for ventura theme settings
    TL-26554       Allowed the perform activity numeric rating scale to be optional
    TL-26908       Activity participant form layout improvements

                   Standardises the activity participant form to align the spacing more
                   closely with other pages

    TL-26947       Improve the positioning and size of the activity print button
    TL-27009       Improved user experience of Performance activity printing
    TL-27014       Moved activity content form builder to its own page. Formerly it was on a sheet modal.
    TL-27097       Improved display of icons

                   With the addition of Ventura, a whole new icon set has been created. This
                   resulted in the following changes:
                    * The 'Base' theme CSS is now is written using SCSS (and not LESS)
                    * The 'Roots' theme no longer explicitly excludes base CSS
                      (as it previously included it as part of LESS compilation)
                    * The new 'Legacy' theme inherits 'Base' themes SCSS (as 'Roots' did previously)

    TL-27322       Added spacing between log in form error message and field labels in Ventura theme
    TL-27324       Fixed workspace tab order to be handled by html

                   Fixed Handling tab order by html instead of flex order for accessibility
                   purposes

    TL-27434       Added focusing on title field when opening an activity section form.
    TL-27467       New performance activities will now have important notifications activated by default

                   The "Participant selection" and the "Participant instance creation"
                   notifications are now activated by default upon the creation of new
                   performance activities. For the "Participant instance creation"
                   notification, only the "External respondent" recipient will be active by
                   default.

                   This change is to help guarantee that users are aware they must take action
                   on an activity in order for it to progress. If these notifications are not
                   received, then the activity may not be able to be completed without manual
                   intervention.

    TL-27572       Fixed Seminar event attendance tracking grade validation

                   Seminar manual grading fields are now limited to numeric values, and any
                   validation errors are shown inline rather than at the top of the form.

    TL-27671       Improved alignment of the subcategories section when managing programs or certifications
    TL-27946       Added functionality to remove a custom image from theme settings
    TL-28015       Created container categories immediately instead of on-demand

                   The course categories associated with containers such as perform and
                   workspaces were not created upon their installation and were not created
                   when their parent tenants were created. Instead, they were created
                   on-demand when required. This has been changed, in order to fix PHPUnit
                   failures and to increase performance.

    TL-28039       Tui unit tests will now fail if any console output is printed
    TL-28103       Optimized DB calls in perform notification task
    TL-28151       Improved responsiveness of current learning tiles

                   Previously when viewing the current learning block in the tiles view, the
                   number and layout of tiles was dictated by the block region to which the
                   block was assigned, and, to a lesser extent, the current screen width. This
                   improvement makes tile layout entirely dependant on the width of the block,
                   regardless of the block region where it is displayed.

    TL-28159       Added draft validation rules for Performance activity section element save responses
    TL-28160       Extracted common response logic and lang strings out of individual components

                   The Vue components for the question elements on the participant form got
                   refactored and common logic and lang strings were extracted to reduce
                   duplicated code and increase maintainability. Individual language strings
                   previously defined on element level got deprecated. For details please
                   refer to the upgrade.txt files in the affected components.

                   The structure of the element responses was simplified, they now are not
                   unnecessarily wrapped into parent structure anymore. All existing element
                   responses will be migrated to the new structure on upgrade.

    TL-28162       Added HTTPS check to the MS Teams app installation page to prevent site admin from downloading manifest on http site
    TL-28205       Added microlearning as a distinct item type in recommender export
    TL-28281       Added navigation back to performance activity list for participants in an activity
    TL-28285       Added a link to the competency profile of team members in the team members report
    TL-28301       Improved the text for prompting users to select participants for performance activities
    TL-28305       Improved the help text for the default mobile compatibility setting
    TL-28359       Restricted use of relate() on ORM entity to only defined relationships in entity class
    TL-28363       Added aria-describedby attribute to form elements when validation errors are displayed
    TL-28369       Added automatic handling of deprecated fields in GraphQL

                   With this patch, all fields marked as @deprecated in the GraphQL schema
                   will trigger a debugging message automatically if they are still in use.
                   Also if developer mode is enabled the deprecation messages will be returned
                   in the extensions field in the response.

    TL-28372       Improved consistency and reduced code duplication in performance activity admin elements

                   We have aligned the performance activity admin elements providing a more
                   consistent approach, reducing a large amount of duplication. As the
                   components have been restructured this is a breaking change from the
                   previous implementation.

                   Added a new wrapper component for all admin elements which provides the
                   outer card style, action icons, reporting ID and the element title.

                   A new edit form wrapper component was added for the edit mode of each
                   element type. This provides the form functionality and optional form inputs
                   for the common patterns.

    TL-28375       Removed self completion form when viewing modules in the mobile app

                   The mobile app supports this functionality in its native course view, so it
                   has been removed from module webviews to reduce screen clutter and improve
                   user experience.

    TL-28402       Added PHP Unit test to detect untranslatable strings

                   Strings with keys ending in '_link' are used to generate URLs to the Totara
                   documentation site. They cannot be translated in AMOS or using the language
                   customisation tool within Totara. This test fails if there are strings
                   ending in '_link' and are not in the built-in whitelist. If you have a
                   customisation which contains strings ending in '_link' then either rename
                   them to allow them to be translated, or add then to the whitelist to have
                   them ignored by the test.

    TL-28419       Fixed phpunit DML test compatibility with MySQL 8.0.22
    TL-28447       Improved webservice entrypoint to show generic error message in production environment
    TL-28455       Allowed negative numbers for numeric rating scale form elements in Perform
    TL-28456       Added a seminar virtual room link to the notification emails
    TL-28458       Seminar room details page updated to display the virtual room link and 'join now' button only if conditions are met

                   When a virtual room URL is added to a seminar room, a 'Join Now' button is
                   displayed in the event listing and event details from 15 minutes before the
                   session starts until the session ends. The room name is also displayed, and
                   is clickable to discover room details, such as physical address,
                   description, and custom fields.

                   Prior to this patch, the virtual room URL was also displayed on the room
                   details page. This was undesirable, as the room might be in use for other
                   sessions. There is now a virtual room card displayed on the room details
                   page when a virtual room url is present. Admins, trainers, facilitators,
                   and other users with event roles can always click the virtual room url, but
                   it will only be available to learners at the same time the 'Join now'
                   button is available.

    TL-28463       Allowed course custom fields to be both locked and required
    TL-28509       Improved accessibility of date and date time moodle form inputs
    TL-28528       Some proprietary CSS is now allowed in sanitised HTML

                   The list of allowed CSS styles:
                    * scrollbar-arrow-color
                    * scrollbar-base-color
                    * scrollbar-darkshadow-color
                    * scrollbar-face-color
                    * scrollbar-highlight-color
                    * scrollbar-shadow-color
                    * -moz-opacity
                    * -khtml-opacity
                    * filter (only opacity)
                    * page-break-after
                    * page-break-before
                    * page-break-inside
                    * border-radius

    TL-28553       Display a 'misconfigured web server' error message at root index.php

                   In Totara 13, the web root has been moved to the server/ directory.
                   Previously, the root index.php redirected to server/index.php as a
                   convenience. But allowing the root directory to be web accessible is
                   considered a misconfiguration as it may expose files and directories that
                   are not meant to be served directly, and not tested as such.

                   The root index.php file will no longer redirect. Please update your server
                   configuration to make server/ the web root.

    TL-28563       Added new Report builder graph option colorRanges

                   This new setting can be used to select item colour based on its value, it
                   expects array of cut values that specify intervals for each colour.

                   For example:
                   {"colors" : ["red", "yellow", "green"],"colorRanges": [20, 100]}
                   results in values having following colours:
                    * -1 red
                    * 0 red
                    * 10 red
                    * 19 red
                    * 20 yellow
                    * 21 yellow
                    * 95 yellow
                    * 99 yellow
                    * 100 green
                    * 500 green

                   If there are fewer colours than ranges then the colours are repeated.

    TL-28572       Added support for custom chart colours

                   There is a new "colors" setting available in Custom settings in Report
                   builder graph configuration, it accepts an array of CSS colours.

    TL-28611       Renamed and re-grouped Engage reports into a single 'Engagement' category.
    TL-28704       Implemented small UX improvements for performance activities

                   * Improved text for Multiple Choice Multi element settings
                   * Reordered question element items on selector
                   * Renamed question elements for easier comparison
                   * Updated help strings for question elements
                   * Updated string on performance activity status banner

    TL-28761       Added new column and filter for user's time zone in Report builder

Bug fixes:

    TL-11274       Replaced fieldset with div on the language import page
    TL-11326       Fixed the select check boxes within manage program/certification pages to be accessible
    TL-11327       Fixed inputs on the report builder columns page without accessible names
    TL-23457       Enabled Totara Mobile app to use basic LDAP authentication in-app

                   Previously, admins who wished to allow users to use LDAP authentication
                   could not enable 'native' (in-app) authentication in the Totara Mobile
                   app.

                   LDAP is now allowed for mobile native authentication, as long as the NTLM
                   SSO feature is disabled (because NTLM SSO requires a web browser for
                   authentication.)

    TL-23555       Fixed User calendar entries to respect course visibility

                   Previously if the Seminar Calendar option is set to "Course", the learner
                   who was not enrolled into a course was able to see the Seminar events, now
                   this issue is fixed.

    TL-24735       Improved the way to handle the seminar notification templates when unsafe the characters used
    TL-25078       Fixed accessibility of FormRowDetails by linking details using aria-describedby
    TL-26097       Fixed the 'signout' command in MS Teams so that it logs the user out of Totara
    TL-26233       Fixed export on "Usage of topics" page does not work

                   Usage of topics did not work with export,  it has been fixed.

    TL-26496       Removed comment's author profile link when user actor is not able to see the author
    TL-26637       Removed back-to-course links from feedback activity when viewed in the mobile app
    TL-26917       Changed validation text in profile summary card

                   Changed validation text in profile summary card to make error wording
                   should be succinct

    TL-27031       Fixed allowing non tenant user to access shared resources from the tenant member

                   Fixed the inconsistent logic rules around resources with multi-tenancy,
                   where the system user is now able to interact with the tenant user's
                   resources. However, when isolation mode is on, this ability will be
                   revoked.

    TL-27081       Updated  number of resources text  on workspace library to number of item text
    TL-27102       Fixed the bug that playlist' back  button respect design requirement
    TL-27213       Closing a tui modal using the "escape" key now works in IE11
    TL-27283       Fixed workspace image upload for tenant members
    TL-27289       Fixed the ability to see shared resources that have been bookmarked

                   Previously attempting to view resources that had been shared after removing
                   them from the "shared with you" page would result in an error. Now users
                   should still be able to see the bookmarked resources via their "saved
                   resources" page.

    TL-27291       Fixed action buttons overlapping with form elements in edit survey
    TL-27385       Fixed fullmessageformat in seminar notification

                   In seminar notification, fullmessageformat was hardcoded as PLAIN.

                   Now it will return HTML or JSON_EDITOR when it will fully be implemented
                   in seminar notification.

    TL-27485       Improved styling of the password reset confirmation page
    TL-27522       Fixed report sources still available when feature disabled

                   Report sources and templates belonging to features that are disabled
                   (including when part of a flavour that is not installed) were available
                   when creating custom reports. Also, some report sources were not correctly
                   being flagged as belonging to a feature, resulting in embedded reports
                   being listed in the embedded reports list when the feature was disabled.
                   These problems have now been corrected.

    TL-27593       Fixed bug permitting successful oAuth2 login to redirect away from site
    TL-27601       Improved text labels for workspace "Add members" dialog
    TL-27602       Removed a seminar send-to-recipients template which exposed PHP code

                   The html file /server/mod/facetoface/editrecipients.html was being used as
                   a template by the message users edit recipients endpoint, and included PHP
                   code which could be exposed on the server. The HTML has been moved into the
                   PHP endpoint.

    TL-27652       Fixed search in workspace and indication to empty search results

                   Included a remove icon to clear search input field all at once and display
                   default state. Indication to total available workspaces and message to
                   indicate if no results are returned when filtered.

    TL-27677       Fixed bug causing recommender to skip non-tenants when multitenancy enabled
    TL-27688       Removed remaining references of removed earlier totara_competency block
    TL-27724       Fixed logic rules about sharing resources between tenant user and tenant participant

                   Fixed the issue when user tenant received the shared resources from a
                   tenant member but could not view the resource due to isolation mode was on.
                   The fix is about allowing tenant participant to access to the tenant
                   member's resources despite of isolation mode status.

    TL-27725       Added clear icon to search box for member/discussion/your library search
    TL-27740       Improve handling deleted user in engage

                   Several things had been added with this patch
                    * Survey page will no longer be available to view when user owner has been deleted
                    * Article/Resource page will no longer be available to view when user owner has been deleted.
                    * Playlist page will no longer be available to view when user owner has been deleted.
                    * Catalog page is now excluding those resources/playlists that belong to a deleted user.
                    * Workspace's members list are no longer including deleted users.

    TL-27747       Fixed popover elements being overlapped by the side panel button

                   Fixed the like button's hovering over popover being overlapped by the side
                   panel button

    TL-27774       Fixed thumbnail generator to keep transparent background in GIF and PNG images
    TL-27780       Modified the main script of the recommender engine to skip the model building process when data is not enough

                   When the data was too small with the existing script, the engine was
                   producing the model with warning messages. The script is modified now to
                   skip such cases.

    TL-27782       Removed unused container_workspace_count_members query
    TL-27867       Added multi-tenancy checks when adding contacts via messaging api
    TL-27880       Fixed access logic for Engage resources, Workspaces, and playlists within cross-tenant settings
    TL-27957       Fixed the message when viewing a user library with 0 contributions
    TL-27989       Fixed tenancy logic rules applied for users when searching for workspaces
    TL-28008       Improved handling of system-managed categories in coursecat methods

                   System-managed categories were introduced in Totara 13, and behave as
                   invisible categories outside any user's ability to manage. There were a few
                   methods in the coursecat class which didn't properly take them into
                   account, particularly when loading child categories or counting the number
                   of subcategories. These have been fixed.

    TL-28031       Fixed TUI theme settings parameter naming

                   Fixed the parameter naming from 'theme' to 'theme_name' as 'theme' caused
                   the site theme to be changed when 'allowthemechangeonurl' config property
                   is true.

    TL-28034       Added PHP 7.2 compatibility function getallheaders to ensure code that uses it doesn't break

                   Some functionality in Totara uses the function getallheaders which was
                   added in PHP 7.3. This adds a compatibility function to ensure running on
                   PHP 7.2 works as expected.

    TL-28044       Remove Avatar when workspace is on the moblie view
    TL-28046       Fixed accessibility issues in the assignment online submission settings
    TL-28075       Header text colour and page text colour settings now affect all entries in the Navigation
    TL-28099       Fixed capability checking in mod_perform\settings::add_manage_activities_link method
    TL-28134       Fixed keyboard accessiblity of Article and Playlist cards
    TL-28175       Fixed an issue with catalogue filter merging when loading filters from multiple sources
    TL-28184       Fixed a bug causing random failures of the totara_engage_webapi_resolver_query_share_totals_testcase
    TL-28187       Fixed a bug that allowed clicking the cog icon on an archived seminar signup
    TL-28204       Fixed catalogue image display in MS Teams

                   The playlist thumbnails now use the accent colour as background in the
                   settings flyout of a configurable tab.

    TL-28279       Updated tablelib to not print empty rows after results
    TL-28303       Fixed bug causing realpath to break system call to symlinked python executable
    TL-28316       Fixed automatic creation of missing default perform activity setting records as non-admin
    TL-28317       Fixed workspaces not being created due to too long names with the dev generator
    TL-28322       Fixed a bug where the workspace owner can remove shared content from workspace library
    TL-28323       Fixed a bug where access permissions are not honoured when a member leaves a private workspace

                   Previously when member bookmarked the resource in the workspace and then
                   left workspace, they still could see the resource in the saved resource
                   page.

    TL-28328       Fixed issues with the access setting modal style for iOS
    TL-28329       Fixed hiding the recommendations related tab when it is empty
    TL-28358       Fixed the incorrect type being used for area graphql property

                   The 'area' GraphQL property used by engage content sharing was incorrectly
                   specified as param_text this has now been updated to be param_area.

    TL-28361       Displayed an alert banner when a scorm page is not compatible with MS Teams

                   The 'new window (simple)' option is not compatible with MS Teams
                   integration. The workaround solution aka shim was added to open in a new
                   window when such a scorm activity is launched in MS Teams.
                   Note that the shim is not compatible with IE11. Please do not use MS Teams
                   on IE11.

    TL-28362       Fixed an undefined function error appearing when running the recommenders export scheduled task
    TL-28364       Removed an invalid href HTML attribute on the share button in the grid catalog
    TL-28371       Fixed admin settings form elements to correctly be reverted when reloading the page without saving changes
    TL-28379       Stopped a warning from being shown when an alert or task is being sent without setting msgtype
    TL-28381       Updated the get_docs_url() function to use the new 'TH' prefix
    TL-28382       Fixed memory issues when upgrading large evidence files
    TL-28385       Fixed the topic filter on the catalogue not working with custom labels

                   When clicking on a topic in a playlist or resource the sidepanel filter
                   would only work if the defined label was "Topics". Now the label used on
                   the topic filter does not matter, it will always apply.

    TL-28387       Added an automated fix for certifications which were incorrectly reassigned

                   Prior to Totara 10.0, it was possible that a user who was unassigned from a
                   certification and then reassigned would not be put back into the correct
                   state. This patch provides an automated fix which can be applied to users
                   who were affected in this way.

    TL-28413       Fixed the current learning tab in MS Teams to allow tile view and other custom settings
    TL-28415       Targeting an item in the navigation bar of a user tour now highlights the item correctly
    TL-28430       Ensured the 'currentstagename' and 'expectedstagecompletiondate' Totara legacy appraisal message placeholders work correctly
    TL-28431       Fixed the opening of PDF certificates in the browser window
    TL-28433       Fixed the reordering of a course's activities within the same course section

                   Fixed an issue with storing the order of activities within a course
                   section, when they got moved the result was not stored correctly.

    TL-28444       Allowed guest users to see catalogue images for visible programs and certifications
    TL-28452       Fixed an error stopping an admin from deleting other user's workspaces during cron run
    TL-28459       Fixed an issue with the handling of files and attachments in the Weka editor
    TL-28461       Removed remote unserialize() call from Flickr reverse geocoding method, and deprecated the method

                   The phpFlickr::getFriendlyGeodata() method, which was used to discover the
                   place name at a given latitude and longitude (reverse geocoding), relied on
                   a script on the developer's website which is no longer available.
                   Additionally, the response from the website was passed directly to PHP's
                   unserialize() function, which could lead to PHP object injection.

                   The method has been deprecated, and now always returns false.

    TL-28462       Added a workaround for elimination of duplicate records in the course completion logs
    TL-28469       Changed the notification only display in shared-with-you and saved-resources page

                   Before notification showed when users created resource/survey, the current
                   fix is the notification only display when users create resource/ survey in
                   share-with-you and saved-resources page

    TL-28527       Fixed "Team" menu item being disabled if perform is disabled

                   Previously, the team menu item was disabled if both the competency
                   assignments and the performance activities feature were disabled, and it
                   was not possible to enable it. This has been fixed and the team menu item
                   is now hidden by default unless the perform features are enabled. The team
                   menu item can be enabled manually in the main menu settings at any time.

    TL-28536       Fixed notification settings disappearing from a performance activity while managing activation
    TL-28564       Fixed Learning plan items so they maintain state changes correctly
    TL-28574       Updated the paging background in the current learning block to be set by the primary button colour in Ventura settings
    TL-28580       Profile image in mini profile card is hidden from screen readers when no alt text is provided
    TL-28481       Fixed a non-translatable string when viewing your reports
    TL-28507       Renamed strings in weka editor which used reserved keys
    TL-28525       Renamed strings in perform which used reserved keys
    TL-28541       Renamed strings in engage workspaces which used reserved keys
    TL-28542       Renamed strings in totara catalog which used reserved keys
    TL-28543       Renamed strings in totara competencies which used reserved keys
    TL-28544       Renamed strings in completion reports which used reserved keys
    TL-28586       Fixed lang string deprecation files belonging to report sources not being loaded

                   Previously, if a report builder's lang folder contained a deprecation.txt
                   file, it was not being loaded. This resulted in allowing the deprecated
                   strings within to continue to be used undetected. This has now been fixed.

                   While all uses of deprecated strings within the core Totara code have been
                   removed, it is possible that a customisation using a previously undetected
                   deprecated string might now cause deprecation warnings.

                   Also, the Totara Plan Evidence lang file has been removed without
                   deprecation. These strings were inaccessible due to the corresponding
                   report source being removed in Totara 13.0.

    TL-28590       Ensured an alert is sent for failed certification completion imports
    TL-28592       Fixed incorrect system context id in quicklinks block installation
    TL-28646       Fixed HR import to not skip records when column length limit is exceeded
    TL-28695       Updated how button padding is calculated based off the height variable
    TL-28699       Fixed participants not being able to view the profile of a subject in anonymous activities
    TL-28700       Fixed multi-language filter support for seminar names
    TL-28706       Fixed theme settings not taking effect in IE until caches were manually purged
    TL-28710       Fixed the permissions cache for appraisals not being correctly cleared during PHPUnit execution
    TL-28726       Added require competency/lib.php in the competency_frameworks filter for the 'Competency Assignments' Report Builder report

API changes:

    TL-28368       Renamed Perform entities namespace to entity

                   All renamed classes got added to the db/renamedclasses.php file and usage
                   of the old classnames will trigger debugging messages.

Tui front end framework:

    TL-25446       Added the ability to hide the close button in popover elements
    TL-27702       Reduced the number of language string requests made for asynchronous components


Release 13.1 (22nd October 2020):
=================================


Performance improvements:

    TL-28016       Reduced the number of database queries issued to load the list of activities for end users
    TL-28017       Reduced the number of database queries issued to load the list of performance activities to manage
    TL-28018       Reduced the number of database queries issued to load the data for activity content management
    TL-28020       Reduced the number of AJAX requests issued when managing notifications for performance activities
    TL-28029       Improved memory consumption of the subject_instance creation task
    TL-28093       Improved performance loading Perform activities list
    TL-28094       Improved performance of WebApi requests by optimising the internal function to split the name of the type

Improvements:

    TL-6557        Added User's mobile phone number column and filter to report sources using the user trait
    TL-26412       Added new tile view to the current learning block
    TL-27021       Added a mobile compatibility column and filter to course reports

                   Added a new column to course-related reports to display the courses "Course
                   compatible in-app" value, along with a filter to allow users to specify
                   which records are shown based on that value.

    TL-27306       Updated the Perform participant responses to use smaller user avatars
    TL-27345       Made the Custom CSS text box variable height
    TL-27641       Improved display of icons with a slash in them
    TL-27878       Specific embedded reports can now be prevented from being cloned

                   Embedded reports such as the 'Performance activity response export' report
                   that are not intended to be viewed by end users must be restricted so they
                   can not be cloned.

                   A warning message has been added for when a user attempts to clone such
                   embedded reports, which prevents them from cloning with a reason as to why.

    TL-28058       Added new option for allowing embedding of PDF files in course resources
    TL-28061       Added bottom padding for editing a long resource
    TL-28065       Added 'image' and 'component_name' properties to user_learning items
    TL-28077       Adjusted the dropdown menu item colour in the seminar dashboard to match other menus
    TL-28095       Added option for Totara Connect users to change their password from client sites

Tui front end framework:

    TL-23882       Fixed source map warnings in Chrome 80+

                   This will require you to run `npm ci` as package.json has been updated.

    TL-27169       Allow specifying new components to use in the template when you override a component

                   When overriding a template from a theme, previously it was not possible to
                   add new components without replacing the entire script block.

                   You can now specify <script extends> to have the script block inherit from
                   the previous one, and add your additional components to the "components"
                   option.

    TL-27726       Updated the colour and font size of loading icons to be less prominent

Bug fixes:

    TL-23909       Fixed the use of theme_roots and theme_basis component names
    TL-26005       Added a confirmation message when a resource or survey is added to a user's library

                   Previously when a user opened their 'Shared with you' or 'Saved resources'
                   page and created a resource or survey, there would be no visual indication
                   the resource/survey was created successfully. Now when creating a resource
                   or survey from any of the 'Your library' pages, a notification will appear
                   at the top of the screen confirming the action.

    TL-26034       Fixed length validation for playlist and workspace titles
    TL-26909       Improved content layout on the 'General' tab on performance activity management screens

                   Improved the spacing between form elements and wrapped the page in a common
                   layout.

    TL-26935       Fixed participant section to ensure showing the updated responses after switching sections
    TL-26943       Prevented duplicate navigation bars from being displayed inside a MS Teams tab
    TL-27071       Fixed the print performance activity page to display general information about the participant
    TL-27111       Fixed favicon theme file by using new icon file type and validate uploaded files accordingly
    TL-27149       Fixed the hash tag popup to not display '[[loadinghelp, core]]'
    TL-27214       Fixed the survey icon not using the themes custom colours

                   Previously the survey icon was served as a static jpg image which did not
                   react to theme changes. The survey icon is now a svg that reacts to changes
                   to theme colours.

    TL-27234       Fixed the use of alternative text for Totara logo set in the theme settings
    TL-27250       Fixed the file uploader on the theme settings page so it accepts different images with the same file name
    TL-27273       Fixed bug preventing article owner from clicking on hashtag
    TL-27316       Changed the aria label for the SidePanel component toggle button for accessibility

                   The expand/collapse button in the SidePanel component had an aria label of
                   'Expand' or 'Collapse' depending on the panel's state which was not
                   correct. The state of the panel was already indicated with the
                   aria-expanded attribute. This is now changed so the aria label is simply
                   'Side panel' and the state is indicated with the aria-expanded attribute.

    TL-27395       Fixed a bug where you could change a resource/survey from private to public without setting topics using GraphQL

                   Previously the check to see whether a resource or survey had topics when
                   changed from private/restricted to public happened on the client side only.
                   With this fix the GraphQL will also perform the check so resources/surveys
                   cannot be made public without associating one or more topics.

    TL-27402       Stopped user being redirected to course pages when logging back in after using login as functionality

                   After using the 'login as' functionality and logging in again, if you were
                   on a course page (or performance activity page), the system would redirect
                   back to that page which sometimes causes irrelevant error messages. Logging
                   in after using this functionality now redirects to the default homepage.

    TL-27403       Updated the link to share engage content with multiple people to display as a clickable link

                   Sharing with multiple people link was not clickable and did not have the
                   link colour, so updating it in the tag list selector to make it clickable
                   and added colour to it.

    TL-27452       Prevented the use of formats other than JSON_EDITOR_TEXT from being used in playlist summary GraphQL mutations

                   Prior to this patch, create and update playlist's summary via GraphQL
                   mutation allowed the use of any valid text format. This was causing issues
                   as the mutations were expecting to be run exclusively in
                   FORMAT_JSON_EDITOR.

                   With this patch, the GraphQL mutations are now locked down to
                   FORMAT_JSON_EDITOR and will throw an error if the text format is different
                   from FORMAT_JSON_EDITOR.

    TL-27455       The Engage resources access setting modal now has 'Only you' selected by default
    TL-27483       Fixed missing advanced feature checks in external competency APIs
    TL-27489       Prevented the use of formats other than JSON_EDITOR_TEXT from being used in article summary GraphQL mutations

                   Prior to this patch, create and update article's summary via GraphQL
                   mutation allowed the use of any valid text format. This was causing issues
                   as the mutations were expecting to be run exclusively in
                   FORMAT_JSON_EDITOR.

                   With this patch, the GraphQL mutations are now locked down to
                   FORMAT_JSON_EDITOR and will throw error if the text format is different
                   from FORMAT_JSON_EDITOR.

    TL-27490       Fixed GraphQL mutations (create/update) to not accept different content format other than FORMAT_JSON_EDITOR nor json document that appears to be an empty document

                   Forced the GraphQL mutations that create or update the workspace,
                   discussion, comments or resource to only accept FORMAT_JSON_EDITOR for
                   content format. Different content formats such as FORMAT_PLAIN,
                   FORMAT_MOODLE or FORMAT_MARKDOWN will result in an error in the mutations.
                   However, the lower level API still accepts different formats, this is to
                   help writing test easier.

                   Furthermore, the GraphQL mutations are now preventing to save the json
                   document that appears to be an empty document for mutations to create or
                   update the discussion, comments or resource.

                   Example of empty document: {"type":"doc","content":[]}

    TL-27524       Fixed Perform navigation items not showing as selected across Perform pages
    TL-27600       Fixed a bug where a topic's case could not be changed by an admin

                   Previously the case of a topic could not be changed as the admin form would
                   display an error message stating that the topic already exists. With this
                   fix topics can now have their case changed in the manage topics interface.

    TL-27617       Fixed a bug when creating a resource via a workspace if the workspace was removed from the 'Share to' taglist, it could not be added back in again

                   Previously if the active workspace was removed from the 'Share to' taglist
                   while inside the workspace's library tab, the workspace could not be chosen
                   again. Now if the workspace is removed from the 'Share to' taglist, it can
                   be chosen in the taglist like any other user or workspace.

    TL-27655       Colour of name(s) under 'Shared with x people and x workspace(s)' changed so it does not appear as if it is a link

                   This applies to workspaces, playlists, resources and surveys.

    TL-27663       Fixed keyboard navigation in the Quick Access menu and Message Menu
    TL-27701       Fixed GraphQL query mod_perform_participant_section to only be called once
    TL-27728       Fixed room availabilities when they are used in cancelled events
    TL-27738       Fixed topic tag selector to allow case-insensitive searching
    TL-27746       Removed unnecessary id param from manage room and assets page links
    TL-27838       Fixed keyboard accessibility of playlist rating stars
    TL-27850       Fixed various problems related to weka editor in assignments and forums
    TL-27860       Fixed 'My status' column for the Seminar sessions report source when a user has 'Not set' status
    TL-27883       Added 'legacy' label to 360 feedback link in user_with_components_links Report Builder display class
    TL-27891       Added workaround to the 'Open in new window' link for Google Chrome when MS Teams is opened in the web browser
    TL-27892       Fixed removing a resource from a playlist so the playlist image is updated correctly

                   Previously removing a resource with an image from a playlist would not
                   update the playlist image. Now the playlist image will update every time a
                   resource is removed from it.

    TL-27895       Changed the playlist order so that newly added items appear on the top
    TL-27905       Fixed removal of performance activity containers when mod_perform plugin is uninstalled

                   Prior to this patch performance activity container categories and courses
                   were not deleted when the mod_perform plugin was uninstalled.

    TL-27932       Current learning block now has the correct theme link colour
    TL-27942       Fixed a bug where administrators were unable to update the order of resources/surveys on a playlist

                   Previously an administrator would encounter an error message when trying to
                   reorder resources on a playlist. With this fix administrators can now order
                   resources on any playlist.

    TL-27947       Ensured user identities are displayed when allocating spaces to a Seminar event

                   This creates consistency between 'Add attendees' and 'Allocate spaces' user
                   selectors.

    TL-27963       Fixed the text colour of the dropdown menu items on the main header
    TL-27965       Fixed the capability and hierarchy checks for the update_theme_settings GraphQL mutation

                   Tenants are only allowed to update their own brand and colours.

                   Prior to this change, if settings not belonging to a brand or colour were
                   passed to the update_theme_settings GraphQL mutation, those settings were
                   still being applied, which is incorrect. A tenant could update custom CSS
                   or change the login image for example, which is not allowed.

    TL-27967       The Date Reviewed column on Inappropriate Content report will immediately update when an action is taken

                   Previously only the "Status" column would update immediately on the
                   Inappropriate Content report, but the "Date Reviewed" column would not
                   update until the page is refreshed. Now when something is approved or
                   removed via the Inappropriate Content report, the "Date Reviewed" column
                   will immediately update without needing to refresh the page.

    TL-27974       Deletion of workspaces is now deferred to run via cron

                   When a workspace is deleted, the record will be flagged as pending deletion
                   in the database, and an ad hoc task will be created to perform the actual
                   deletion.

                   The patch represents a significant change in the behaviour first introduced
                   in 13.0 where the deletion of a workspace occurred immediately. This will
                   help to improve performance and lower the risk of data loss.

                   The flagged (to be deleted) workspace will not be shown to the user except
                   on the Resource's sharing recipient(s) page.

    TL-28014       Removed hyphenation for resource card and resource content
    TL-28022       Fixed editable region border for playlist summary being cut off
    TL-28027       Fixed Engage Resource cards in Playlist so they don't take all horizontal space on the screen
    TL-28036       Fixed form rows wrapping incorrectly in Safari at narrow viewport sizes
    TL-28037       Added relationship selector to print button in user activity list

                   Prior to this patch, activity participants with more than one relationship
                   in an activity could not select the relationship which they would like to
                   use to view the print version of the activity. Instead one of the
                   relationships was automatically used. This patch adds the missing
                   relationship selector.

    TL-28040       Fixed JS bundles from parent themes not being loaded
    TL-28043       Fixed errors printed to console when popover target is hidden
    TL-28055       Fixed overlapping text in profile mini card on mobile view
    TL-28056       Fixed upgrade of Perform user reports created in Totara 13.0 RC1

                   In RC1 some performance activity report sources and embedded reports got
                   renamed. The upgrade step originally just renamed the embedded reports but
                   not any user reports already been created based upon those. This patch adds
                   an additional upgrade step to also rename any existing user reports.

    TL-28060       Fixed the formatting of course summary URLs returned by GraphQL
    TL-28067       Moved some incorrectly placed quotation marks from the subject to the body text of Engage "like" notifications

                   The Engage "like" resource notification had incorrectly placed quotation
                   marks around the resource type in the subject and missing quotation marks
                   around the resource name in the body. These have been switched so the
                   notification surrounds the correct thing with quotations.

    TL-28080       Fixed incorrect theme settings URL after purging caches
    TL-28084       Updated the timestamp of the discussion when there is a reply or comment added to the discussion

                   Prior to this patch, a discussion with a comment/reply recently added would
                   not be moved to the top in a sort order of recently updated when loading
                   the discussions.

                   With the patch, when a discussion with comment/reply recently added will
                   not be moved to the top in a sort order of recently updated when loading
                   the discussions.

    TL-28089       Fixed link to Notification preferences in plain text body of notification emails
    TL-28090       Fixed incorrect validation message when checking for Course Question Category loops
    TL-28091       Fixed the workspace name appearing as dark text on a dark background on the dashboard 'Recently Viewed' block
    TL-28104       Fixed Seminar so attendance can still be taken after some signups have been archived

                   Previously, for a seminar where attendance had been marked for some
                   learners, and where course completions had been archived, it was impossible
                   to mark attendance for the remaining learners.

                   Archived seminar signups are now ignored when taking attendance.

    TL-28113       Fixed duplicate triggering of module created event in test generators
    TL-28117       Fixed the link included in badge notifications
    TL-28166       Fixed invalid default substring length in MS SQL Server database driver
    TL-28172       Fixed the location of the workspace members search box and placeholder text

                   The workspace members search box was in the wrong location and had the
                   wrong placeholder.

    TL-28195       Fixed warning when exporting report using wkhtmltopdf

                   Prevented PDF export warning being displayed. A plain text description for
                   a report is now displayed with links and embedded images stripped out.

    TL-28198       Fixed the JSON editor escaping URLs in mobile GraphQL queries

Contributions:

    * Davo Smith from Synergy Learning - TL-28113
    * Michael Geering from Kineo UK - TL-28166


Release 13.0 (1st October 2020):
================================

Perform
-------

    Totara 13 adds an entirely new product, Totara Perform, to the Totara codebase.
    This has been implemented as a number of separate components, some of which are
    unique to Perform, some of which are part of the core platform and some of which
    are a mixture. For specific information about what has changed in 13, see the
    What's new page:
    
    https://help.totaralearning.com/display/TH13/What%27s+new

  Performance activities (Perform only)

    Performance activities are a new component that support the creation of and
    participation in appraisals, check-ins, 360 feedbacks and other workflow-based
    form activities. It includes support for creating custom forms with workflows,
    assignment and tracking progress, sending notifications and reporting on
    responses. More information about this feature can be found in the end-user
    documentation:

    https://help.totaralearning.com/display/TH13/Managing+performance+activities

    Technical documentation can be found here:

    https://help.totaralearning.com/display/DEV/Performance+activities+architecture

  Competencies (Partly core platform, partly Perform only)

    The existing competency functionality remains in the core platform and is
    largely unchanged. For Perform customers the functionality has been extended to
    support a range of additional behaviours. Competencies can now be assigned to
    groups of users, so it's possible to track who is required to achieve specific
    competencies. Competency criteria has been extended to support pluggable
    criteria and aggregation, so you can set up complex rules to achieve completion.
    End users now see a competency profile which displays their progress visually,
    and they can drill down to see achievement criteria and an activity log history.
    Competencies support rating by a range of roles.

    More information about this feature can be found in the documentation:

    https://help.totaralearning.com/display/TH13/Competency+assignment

  Evidence (Core platform)

    The core evidence functionality has been extended and improved in a number of
    ways. The management of evidence has been moved to a user's own dedicated
    evidence bank, and there are permissions to allow self-management of evidence or
    management by a user's manager. Evidence types have been improved to support
    different custom fields per type. Evidence created via course or certification
    completion import will continue to show up in the record of learning but other
    evidence is now displayed in the evidence bank only.

    More information about this feature can be found in the documentation:

    https://help.totaralearning.com/display/TH13/Evidence

Engage
------

    Totara Engage is a brand new product in Totara codebase, a Learning Experience
    platform introduced in Totara 13 release. It unlocks the feature set to enable
    user-generated content creation and curation workflows, workspaces creation and
    collaboration, brings recommendations of formal and informal learning and
    integration with Microsoft Teams.

    More information about this  can be found in the documentation:

    https://help.totaralearning.com/display/TH13/What%27s+new

  Library, content creation and curation (Engage only)

    With Totara Engage there's now the ability for all the users to create content
    and curate it for other people. Users get access to their libraries, where they
    get their content and content others shared with them organised. From there they
    can create resources, surveys or playlists and share them with other users. The
    permissions to create content are defined on the level of capabilities. A new
    taxonomy level - topics - was introduced for categorisation of Totara Engage
    content. Learning catalogue was extended to include Resources and Playlists.

    https://help.totaralearning.com/display/TH13/Library

    Technical documentation can be found here:

    https://help.totaralearning.com/display/DEV/Engage+resources

  Workspaces (Engage only)

    After activating Totara Engage, users will be able to create workspaces (groups)
    for collaboration, link formal and informal learning to the workspaces. It
    allows flexible setting of access permissions to the workspaces and reveals
    collaborative tools in the workspaces - discussion thread, workspace files and
    workspace library.

    More information about this feature can be found in the documentation:

    https://help.totaralearning.com/display/TH13/Workspaces

  Recommendations (Engage only)

    With activating Totara Engage users to get access to the Totara-powered
    proprietary recommendations engine, which analyses user's interaction with the
    system, other users' interactions and recognises what the content is about and
    based on these results recommends users formal and informal content in dashboard
    blocks and side panels.

    More information about this feature can be found in the documentation:

    https://help.totaralearning.com/display/TH13/Recommendations

    Technical documentation can be found here

    https://help.totaralearning.com/display/DEV/Recommender+installation+and+configuration

  Microsoft Teams Integration (Engage only)
    
    With Totara Engage users get the plugin that enables integration with Microsoft
    Teams. With this integrations it will be possible to surface Totara content in
    Microsoft teams, push all the notifications there and create content (Resources
    and playlists) and then share it within Microsoft Teams.

    More information about this feature can be found in the documentation:

    https://help.totaralearning.com/display/TH13/Microsoft+Teams+integration+setup

Tui front end framework
-----------------------

    Totara has a new Vue.js based frontend framework that we call Tui. It enables us
    to create rich, modern, and reactive experiences within our product.

    It is separated from the main body of the codebase as discussed above in code
    reorganisation and is distributed under a proprietary licence.

    Tui is a frontend framework, consisting of Vue.js components that embrace modern
    development practices when manipulating the DOM and working with data. Tui
    requires a build process during development that takes the Vue component files,
    which include JavaScript, SCSS, HTML and Language strings, and compiles them
    into bundles for functionality and style. These compiled files are referred to
    as build files.

    A new server plugin called totara_tui facilitates the use of Tui on pages by
    outputting a minimal block of HTML markup that will be replaced by Tui pages and
    experiences when the page loads on the client. The totara_tui plugin also
    mediates the compiled build files when requested by the client.

    Entire pages and experiences are created using Tui.

    Tui then communicates with the server via WebAPI and more specifically using
    GraphQL queries and mutations.

    Importantly, and as noted in the backwards compatibility section, Tui is opt-in
    for developers. While the new products (Totara Engage and Totara Perform) use it
    extensively, if you are working with existing plugins you can continue to do
    exactly what you did before. As a bonus in that situation you can additionally
    tap into the WebAPI GraphQL queries and mutations if you choose, from your
    plugin, without needing to use Tui.

    For more information on Vue.js we recommend you read the [official Vue.js
    documentation](https://vuejs.org/). Our implementation of Vue.js within Tui
    follows their guidelines.

    In addition to this we use the [Apollo client](https://apollographql.com/client)
    and [Vue Apollo|https://apollo.vuejs.org/] libraries in order to extend Vue.js,
    and to enable easy integration and communication between Tui and Totara core via
    the WebAPI GraphQL services.

    For detailed information on the architecture, principles and how to's of Tui
    please see our [Tui front end
    framework](https://help.totaralearning.com/display/DEV/Tui+front+end+framework)
    developer documentation.

New features
------------

  TL-18501 Cloud-based file storage support

    Totara can now make native use of S3 and Azure cloud storage for data directory
    storage.

    This is an advanced configuration option that will enable those using these
    engines to optimise data directory storage within Totara.
    These changes are also compatible with xsendfile (X-Accel-Redirect) and through
    dual configuration with web servers such as nginx enable serving files directly
    from disk bypassing php passthrough.

  TL-18605 GraphQL-based Web API

    Totara 13 has a brand-new service layer we call Web API. At it's heart Web API
    uses the GraphQL query language. This enables rich descriptions of the APIs and
    makes a range of developer tools immediately available, making it easier to work
    with the new service layer.
    Perform, Engage, and Mobile all use this new service layer extensively.

    The rich description includes not just the queries and mutations used to get
    information from the system, and manipulate it, but also provides a description
    of the data that enables the explanation of object types, fields, interfaces,
    and others.

    The Web API implementation of GraphQL follows the official specification that
    can be found at: https://graphql.org|https://graphql.org/. For those interested,
    we recommend first learning about GraphQL from the official site.

    The following are the notable decisions made when in our Web API GraphQL
    implementation:
    * Each component and plugin can introduce its own schema; at runtime these
      schema files from all components and plugins are built into a complete schema
      file for the site that is cached and mediated by Totara.
    * We only support named queries (sometimes referred to as persistent queries).
      This means you can only call pre-defined queries and mutations. You cannot write
      you own query on the client and execute it.
    * Queries can be batched, in order to support the bulk loading of information.
    * Each consumer should have its own endpoint, and can have its own named queries
      and mutations. Totara Core ships with two such endpoints, the AJAX endpoint that
      can be used from a browser, and a Mobile endpoint used by our new mobile app.
    * The server-side implementation of the resolvers marries up with the intended
      flexibility of the query language – each individual type and field is
      independently resolvable.
    * We use an open-source PHP GraphQL library as the base of our implementation.
      Read more at https://github.com/webonyx/graphql-php.
    * Query and mutation resolvers can use middleware as a way to include reusable
      code to be called before or after a resolver is called. See
      https://help.totaralearning.com/display/DEV/Middleware for technical
      documentation of this feature.
    * Entity buffer - this allows you to defer loading of entities by buffering them
      and combining them into a single query across all type resolvers involved in the
      query. It also works across batched queries thus reducing the number of queries
      for the same type drastically. See
      https://help.totaralearning.com/display/Dev/GraphQL for more documentation.

    You can find more on [developing with Web API and GraphQL in Totara
    Core](https://help.totaralearning.com/display/DEV/Integration+with+Totara+Core)
    in our developer documentation.

  TL-18786 Chart.js charts for report builder

    Chart.js has been integrated with Totara. The first use of this library is
    within report builder where it is now used as the default chart library. The
    original charting library SVGGraphs can be reverted to if desired. Information
    on configuring the display of report builder charts can be found in our
    [developer
    documentation](https://help.totaralearning.com/display/DEV/Advanced+settings).

  TL-20345 ORM framework

    Totara now includes an ORM framework based on top of the earlier-introduced
    query builder that further abstracts interaction with the database. Each table
    can be represented by its own entity class which allows for auto-completion of
    the fields, as well as some other convenience methods. It also makes it possible
    to define relations between entities and fetch related data together.

    Please refer to the full documentation available here:
    https://help.totaralearning.com/x/TKbgB

  TL-21039 Added totara_mobile plugin to provide a GraphQL endpoint and other services to the Totara Mobile app

    This new plugin implements the settings and services required by the Totara
    Mobile app, including:
    * App settings such as branding and authentication preference
    * Handshake negotiation between the app and the server
    * Login and device registration
    * A set of dedicated GraphQL queries tailored to the needs of the app
    * Support for app-specific webviews of Totara pages

    This plugin must be enabled in order for users to use the app with your site.
    For more information,
    see https://help.totaralearning.com/display/TM/Introducing+Totara+Mobile

  TL-21164 Multitenancy functionality

    Multitenancy within Totara 13 brings the ability to separate, and if desired,
    isolate users and content, based upon the tenant that a user is a member of.
    It is an advanced feature and must be enabled for the site if wanted. Once
    enabled the tenant management interfaces become available, as does an
    experimental setting to isolate tenants.

    An explanation of what this means in product can be found in our help
    documentation on [using
    multitenancy](https://help.totaralearning.com/display/TH13/Using+multi-tenancy).

    The following are the most notable technical changes to the platform made to
    support this functionality:
    * Tenant context and it's effect on the user context
      With this change we have introduced a new context level CONTEXT_TENANT that must
      have the system context as it's parent. One is created for each tenant.
      A user is either a system user, or a tenant member. If they are a tenant member
      their context level will use the tenant context as its parent. Contexts for
      system users continue to use the system context as a parent.
    * Tenant category
      Each tenant has a tenant category that is created when the tenant is created.
      The tenant category is always a top-level category. It cannot be edited like
      other categories, and cannot be manually deleted.
      Content within the category is considered to belong to the tenant. This includes
      subcategories, courses, activities, and blocks.
    * Permission resolution
      The behaviour of has_capability and associated functions has changed in order to
      support tenants and each context now tracks the tenant that it belongs to. When
      multitenancy is enabled, a tenant member cannot pass capability checks if they
      are made against a context belonging to a different tenant. In other words, a
      member of one tenant cannot access content or users belonging to a different
      tenant.

    A tenant member can still access system content and users (contexts that do not
    belong to a tenant) providing isolation mode has not been turned on. When
    isolation mode has been turned on, tenant members can only access content and
    users within their tenant.

  TL-21193 Added Laravel-like Query Builder

    This patch introduces a query builder which abstracts querying the database on
    top of the DML layer. The query builder is inspired by [Laravel’s Query
    Builder](https://laravel.com/docs/master/queries) and provides a similar
    feature set. It provides a consistent fluent interface. Internally it uses the
    DML layer to execute queries so database compatibility and low-level query
    integrity is ensured. The query builder provides at least the same functionality
    as the DML layer. It should be possible to substitute existing DML actions with
    it, as well as cover more complex cases which are only possible via raw SQL
    queries at the moment.

    Full documentation is available here:
    https://help.totaralearning.com/display/DEV/Query+builder

  TL-22816 Added support for migration from Moodle 3.5.10, 3.6.8, 3.7.4 and 3.8.1

  TL-23805 AirNotifier push notification message output plugin

    An AirNotifier push notification message output plugin has been added to Totara
    to provide support for push notifications being sent from Totara through to the
    Totara Mobile App.

  TL-24352 Implemented an MVC framework

    The totara_mvc plugin enables developers to implement new pages by using
    controllers and view, which can reduce boiler plate code. It comes with a
    generic controller class and an admin controller class which can be extended and
    supports views (generic, report and tui view classes are included in
    totara_mvc).

    Technical documentation can be found here:

    https://help.totaralearning.com/display/DEV/Model-view-controller+%28MVC%29+pattern

  TL-27902 Totara containers

    Totara 13 introduces a new conceptual approach to how course core functionality
    works. To allow for more flexibility around different use cases and
    requirements, we abstracted out core functionality that separates the courses
    core API from course implementation itself. Now course in its conventional
    meaning is a specific implementation of container.

    This functionality provides an easier way to introduce and maintain various
    types of containers that require the advantages of course API without having to
    create a generic course. This is done by setting up specific hooks across course
    API codebase that allow redefining how any feature, plugin or sub-plugin used in
    courses works, as well as regulate its availability and appearance.

    This API is still backwards compatible. However, many legacy functions were
    deprecated. Legacy courses and related plugins behaviour have not been changed.

    For more information about container feature and the way it works, please visit
    https://help.totaralearning.com/display/DEV/Totara+containers

  TL-27903 Weka editor

    Totara 13 has a brand-new Weka editor that edits a structured document
    representation stored as JSON, instead of directly editing HTML. Weka is built
    on top of the ProseMirror rich text editor framework (https://prosemirror.net/).

    This enables users to produce content that can be rendered in multiple ways,
    such as HTML, plain text, and markdown. Furthermore, the content can be
    supported by different platforms rather than just web-based, e.g: Mobile apps.

    The JSON document produced by Weka editor is built out of smaller structured
    nodes, nested according to a predefined schema. Here are notable things about
    nodes:
    * Each component and plugin can introduce its own node(s), and it will be
      automatically picked up by the editor.
    * Each component and plugin can decide whether the node is available for the
      certain area or not.
    * Each node has a limited set of valid attributes with defined meanings. This
      allows renderers to be built for the format across multiple output formats.

    Apart from the ability to extend the nodes of the JSON document, we also enabled
    the ability to let plugins configure the Weka editor in different areas.

    For more information, please
    visit https://help.totaralearning.com/display/DEV/Weka+editor

Security issues
---------------

  TL-20656 Improved server-side validation of audience rules

    Server-side code handling audience forms has been reviewed to ensure that all
    incoming data is correctly validated against the expected format for the rule
    being created/edited.

  TL-20704 Improved the format_string() function to prevent XSS when results are not properly encoded in HTML attributes

    Previously it was possible to enable the use of arbitrary HTML tags in course
    and activity names. This is a security risk and is no longer allowed.

  TL-20729 All text is now consistently sanitised before being displayed or edited

    Prior to this change, privileged users could introduce security vulnerabilities
    through areas such as course summaries, section descriptions and activity
    introductions.

    The original purpose of the functionality was to allow content creators to use
    advanced HTML functionality such as iframes, JavaScript and objects. In some
    areas it was explicitly allowed to happen. In others, the trusttext system was
    used to manage who could embed potentially harmful content.

    This patch includes the following changes:
    * A new setting 'Disable consistent cleaning' has been introduced. It is set to
      'off' by default.
    * Text in the affected areas will be now be sanitised, both when it is
      displayed, and when it is loaded into an editor.
    * The trusttext system will be forced off by default and be disabled unless the
      new setting is turned on.
    * SVG images will be served with more appropriate content-disposition headers.

    The consequence of this change is that by default no user will be able to use
    the likes of iframes, JavaScript or object tags in the majority of places where
    they previously could.

    For those who rely on the old behaviour, the new 'Disable consistent cleaning'
    setting can be enabled in order to return to the old behaviour. However we
    strongly recommend that you leave this setting off, as when it is turned on the
    security vulnerabilities will be present. When enabled, this setting will be
    shown in the security report.

    Please be aware that there is a data-loss risk for any sites which are upgrading
    to this release and have relied upon the previous behaviour if they have not
    enabled the new 'Disable consistent cleaning' setting. After upgrading, unless
    you enable the legacy behaviour, when a user edits content relying upon this
    functionality and saves it, they will cause the cleaned version to be saved to
    the database. Any unallowed HTML tags, or attributes, will have been removed.

    For more information on this change, and a list of affected areas, please refer
    to our help documentation.
    https://help.totaralearning.com/display/DEV/Totara+13+changes+to+content+sanitisation

  TL-20891 Ensured user identity fields are consistently sanitised

  TL-20996 Ensured user email addresses are consistently sanitised

Performance improvements
------------------------

  TL-19815 Improved performance of replace_all_text() method in the DML layer

    This improved performance of unsupported 'DB Search and replace' tool. Instead
    of blind attempts to search and replace content in all rows, it selects only
    rows that have searched content first.
    Contributed by Jo Jones at Kineo UK

  TL-21853 Improved the performance of the course and category management interface

    The contents of each category's 'Actions' cog menu on the 'Course and category
    management' page is now rendered upon request. This provides a noticeable
    performance improvement over rendering them all in advance.

  TL-27549 Improved instance-creation performance

Improvements
------------

  TL-5081  Added a new 'Program exceptions' report source

    Contributed by Mark Ward at Learning Pool

  TL-5287  Added additional options to seminar notification recipients field

    Previously, the selection of seminar notification recipients was limited to a
    few classes of attendee: booked, wait-listed, cancelled, and request pending. It
    was possible to refine the booked category to be either 'all events', attended,
    or no-show. There was no way to target attendees on just current or future
    events.

    The recipient selection interface has been upgraded to allow independent
    selection of attendees who are booked on future, current, or past events; and
    also attendees who are fully attended, partially attended, unable to attend, or
    no-show. The options for wait-listed, cancelled, and pending requests remain
    unchanged.

  TL-5629  Added temporary manager and expiry date to HR Import Job Assignment element

    As part of this the pre-existing Job Assignment import field
    'managerjobassignmentidnumber' has been renamed to 'managerjaidnumber'.

    Important: CSV or database sources that do not have field mappings will require
    the source field name to be updated.

  TL-5660  Uploading completion records no longer creates evidence for unrecognised records by default

    Previously, when uploading course or certification completion data using a CSV
    file, an evidence record would be created for any row in the file that did not
    match up exactly with an existing course or certification. The default was to
    create generic evidence, but other 'Default evidence types' were selectable.

    The new default 'Default evidence type' setting is 'Do not create evidence'.
    This will cause unmatched rows to be marked as errors instead of being used to
    create evidence records.

    To recreate the old behaviour, set 'Create generic evidence' as the 'Default
    evidence type' for the import.

  TL-6204  Customisable font when exporting a report to PDF

    A new setting has been introduced that allows the font used within report
    builder PDF exports to be customised. This allows those on non-standard
    installations to work around required fonts that they do not have.

  TL-6693  Added audience rules for position and organisation multi-select custom fields

    Previously you could create audience rules based on other position and
    organisation custom fields (menu of choices, checkboxes etc), but not based on
    multi-select custom fields. This patch adds a new rule type for multi-select
    custom fields that has 4 operators
    * in all of the selected options
    * in any of the selected options
    * not in all of the selected options
    * not in any of the selected options

    It is worth noting that the in any/all operators will include users that have at
    least one job assignment that have all/any of the selections. Similarly the not
    in any/all operators will include users that have at least one job assignment
    that does not have all/any of the selections. None of the operators will include
    users with no job assignments.

  TL-6695  Added new course or program assignment dynamic audience rule

    This new rule allows you to include or exclude users from an audience based on
    their enrolment in specified courses or programs.

  TL-6725  Expanded the 'Has direct reports' audience rule options

    Previously there were two options:
    * Has direct reports
    * Does not have direct reports

    There are now four options available.
    * 'None': the user has no direct reports
    * 'At least': the user has exactly X reports, or more
    * 'No more than': the user has less than or exactly X reports
    * 'Exactly': the user has exactly X reports

    During upgrade:
    * Any rules previously using 'Has direct reports' will be converted to use 'At
      least' and '1' user.
    * Any rules previously using 'Does not have direct reports' will be converted to
      use 'None'.

    This will ensure that behaviour does not change when upgrading.

  TL-7394  Added a new dynamic audience rule based on historic course completion dates

    This new rule closely resembles the existing course completion rules, but
    instead of comparing the user's current completion it checks the rule against
    any archived completions in the course_completion_history table.
    Contributed by Jamie Kramer at Elearning Experts

  TL-7808  Added seminar reset functionality to course reset

    Previously, seminars did not have any code supporting course reset
    functionality.

    Now if you attempt to reset a course containing a seminar activity there are
    options to 'Delete attendees' and 'Delete all events'. Both are ticked by the
    'Select default' button, but can be unticked to keep events, or keep events and
    their attendees, after the course is reset.

  TL-7967  Changed the certification workflow to only reset primary certification path on expiry

    Previously, when a recertification window opened, courses on both primary
    certification and recertification paths were reset. Now only the recertification
    path courses will be reset when the recertification window opens. Primary
    certification path courses will now be reset only on expiry, and only if they
    are not also on the recertification path. This ensures that courses are only
    reset when they need to be recompleted, and that progress towards
    recertification, if applicable, will contribute to primary certification in the
    event of a user's certification expiring.

  TL-8300  Added the ability to order courses within a Program or Certification course set

    Courses within Program and Certification course sets can now be ordered as
    desired. This order is then reflected when displaying the list of courses back
    to the end user.
    Contributed by Chris Wharton at Catalyst EU

  TL-8308  Improved aggregation support for the certification report source

    Previously the certification report source contained several required columns in
    order to ensure user visibility was correctly applied. These required columns
    led to aggregation within the report not working. Thanks to improvements made in
    Totara 12 this could be refactored so that the required columns were no longer
    required. Visibility is still calculated accurately and aggregation is now
    working for this report source.

  TL-8314  Improved aggregation support for the program report source

    Previously the program report source contained several required columns in order
    to ensure user visibility was correctly applied. These required columns led to
    aggregation within the report not working. Thanks to improvements made in Totara
    12 this could be refactored so that the required columns are no longer
    necessary. Visibility is still calculated accurately and aggregation is now
    working for this report source.

  TL-8315  Improved aggregation support for the course report source

    Previously the course report source contained several required columns in order
    to ensure user visibility was correctly applied. These required columns led to
    aggregation within the report not working. Thanks to improvements made in Totara
    12 this could be refactored so that the required columns are no longer
    necessary. Visibility is still calculated accurately and aggregation is now
    working for this report source.

    Please note that the course report source no longer supports caching.

  TL-8754  Added a 'Has temporary reports' dynamic audience rule

    This rule allows you to add users who are currently serving as a temporary
    manager for one or more users to a dynamic audience.

  TL-9209  Added a new dynamic audience rule based on user creation dates

    This rule allows you to define an audience based on the 'timecreated' column of
    a user's database record. Like existing date time rules, this can either be
    compared to an entered date/time, or to the current time when the rule is
    reaggregated.

  TL-11158 Changed the duration format to use a calendar day if a session event crosses midnight

    The duration is now displayed as a calendar day if it is a multi-day session.
    For example, if a session starts at 1:00pm 30/01/2019 and finishes at 11:00am
    31/01/2019, then the duration displays "2 days" instead of "22 hours".

  TL-12692 Added the ability to track attendance at the session level of seminars

    Previously it was only possible to track attendance at the event level of a
    seminar. With this improvement, attendance can be tracked for each individual
    session within an event. This includes:

    * A new seminar setting, 'Session attendance tracking', which allows trainers to
      record attendance for each session of a seminar event. The recorded session
      attendance is summarised on the event attendance form, allowing trainers to use
      it as the basis for setting an overall attendance status for each attendee.
    * A new seminar setting, 'Mark attendance at', which determines when trainers
      are allowed to begin taking attendance for an event or session.
    * A new attendance status, 'Unable to attend', which provides an option for
      trainers to mark an attendee as not having attended a session or event, but
      without marking them as a 'no show'. The 'Restrict subsequent sign-ups to'
      setting now includes 'Unable to attend' as one of its options.
    * The seminar events dashboard has been consolidated into a single list of
      sessions and events, with a filter allowing participants to see all events, or
      only those that are upcoming, in progress, or in the past. 
    * If 'Session attendance tracking' is enabled, a per-session 'Attendance
      tracking' column appears on the events dashboard, allowing trainers to see at a
      glance which sessions are marked or are ready to be marked.

  TL-14764 Added support for Open Badges Specification 2.0 and Open Badges 2.0 platforms

  TL-15758 Added a 'require passing grade' conditional access criteria to the assignment module

  TL-17209 Converted the seminar wait-list page into an embedded report

  TL-17469 Added dynamic audience rule for 'Has indirect reports'

    Created a dynamic audience rule based on whether the person has indirect
    reports.

  TL-17778 Added image optimisation to Totara catalogue and Featured Links block

    Images used within the catalogue and within the Featured Links blocks are now
    optimised for their use in these locations prior to delivery.

  TL-17930 Enable a report builder saved search to be used as the default view for the report

    As a report builder report curator, a saved search can be set as the report
    default view. This search will be applied as a default view for everyone who has
    visibility of the report. Viewers of the report can remove the default or change
    to another saved search so that they have their own saved view.

  TL-18678 Replaced the course selector form element used for 'Recurring course' when editing program content with the standard course selector form element

    Prior to this change, all courses were loaded into a single dropdown, which
    could lead to performance issues on sites with a large number of courses. This
    dropdown has now been replaced with the standard course selector dialogue
    already used in selecting courses for program course sets.

  TL-19259 Added a 'Has appraisees' dynamic audience rule

  TL-19447 Added 'Totara grid catalogue' option to 'Default home page for users' setting

    Totara grid catalogue can now be selected as the default home page for users. At
    the same time the default value for the 'Allow default page selection' setting
    has been changed from 'Yes' to 'No'.

  TL-19493 A link to the component overview screen is now shown when viewing Learning Plan component items

    A link has been added to the screen for individual Learning Plan component items
    (e.g., a specific course, program, competency, or objective) that returns the
    user to the component overview screen (e.g., all courses, programs,
    competencies, objectives).

  TL-19799 Removed the non-functional Google Fusion export option

  TL-19808 Allowed CSV import of seminar attendees from files without columns for custom fields

    Seminar attendees can now be imported from CSV files that only have columns for
    required custom fields or, if there are no required custom fields, from a list
    of users with no other columns.

  TL-20041 Added a new setting in the course defaults page to enable/disable the course end date by default when creating a new course

  TL-20051 Added a new Job Assignment ID number dynamic audience rule

    This new rule allows you to include or exclude users from an audience based on
    the idnumber field in their job assignments.

  TL-20248 Filters now only show on the seminar events page if there is content to filter

  TL-20274 Introduced minimum required proficiency setting for competency scales

    Competency scales now have a value that is considered the minimum a user must
    achieve to be considered proficient. Values are no longer individually set as
    proficient or not proficient, but instead will respect this setting on the
    scale.

    This will be set for existing scales automatically on upgrade.

    IMPORTANT: Upgrade will be blocked if the proficient values in the scale are not
    ordered correctly (where there are non-proficient values that are higher on the
    scale than proficient ones). If that is the case, sites can be taken back to a
    release that contains TL-21175 where the proficient setting on individual scale
    values can be modified in order to fix this.

  TL-20397 Redesigned the user reports page

    The user report page was redesigned replacing the list of reports with a
    grid-like user interface showing report thumbnails that reflect the nature of
    the report:
    * table reports
    * graphical reports (with different thumbnails for each chart type)

    Added a button to the page to create new reports that will be shown to users
    with appropriate permissions.

  TL-20400 Changed the default seminar grading method, and added manual grading option to seminar events

    There is a new 'Grading method' setting for seminars, which determines which
    grade to use for the overall activity grade when a learner attends multiple
    seminar events. Choices are 'Highest event grade,' 'Lowest event grade', 'First
    event grade', and 'Last event grade'.

    The default seminar grading method has been changed to 'Highest event grade'.
    Prior to this change, a seminar attendee's grade was based on the last
    attendance taken. The old behaviour can be replicated in practice by setting the
    grading method to 'Last event grade'.

    Trainers now also have the ability to assign arbitrary grades to seminar
    attendees. When 'Event manual grading' is enabled, a 'Grade' column is added to
    the event 'Take attendance' form. For each learner, trainers can set attendance,
    a grade, or both.

  TL-20418 Allow a seminar attendance export in CSV format that can then be imported to update attendance

    Following on the ability to upload seminar attendance in the last release, it is
    now possible to download a seminar attendance report that is already correctly
    formatted for upload.

    Trainers can use the new 'CSV export for upload' to mark event attendance, and
    optionally grade if manual event grading is enabled, in bulk. The file can then
    be uploaded with no further changes to column layout or header names.

  TL-20421 Seminar event attendance and grades can now be imported via CSV

    With this feature, accessible from the seminar event 'Take attendance' page,
    trainers are able to upload a CSV file with attendance information for each
    event attendee. If event manual grading is enabled, the CSV file may also
    include grades.

  TL-20422 Moved seminar event and session details to its own page when managing a seminar event

    Previously details of a seminar event and its associated sessions, including
    room and asset information, were shown to trainers at the top of each seminar
    management tab ('Attendees', 'Cancellations', 'Take attendance', et cetera).
    This information was the same from tab to tab, and pushed unique information and
    functionality down the page.

    Seminar event and session information has now been moved to its own tab, 'Event
    details', and removed from all other seminar management tabs. 

  TL-20423 Replaced all seminar 'Go back' links with 'View all events' buttons

    In order to simplify seminar management and improve usability for trainers, the
    'Go back' links at the bottom of all seminar management screens have been
    replaced with buttons that read 'View all events'.

  TL-20425 Updated seminar event dashboard and course view

    This patch contains several improvements to the seminar event dashboard and the
    course activity view, including:
    * Added 'Previous events time period' options to be able to display only past
      events in the specific time period
    * Redesigned the filter bar with tool-tips and icon
    * Added new filters: booking status, attendance tracking status
    * Reverted the change in TL-19928 (February 2019 release); the seminar event
      dashboard is now back to two tables: one is for upcoming or ongoing events, the
      other is for past or cancelled events
    * Redesigned session list table
    * Rearranged table columns
    * Broke down event status into three types: event time, event booking status,
      and user booking status

  TL-20427 Improved the display and usability of download controls when viewing seminar attendees sign-in sheets

  TL-20441 Converted the seminar cancellation page into an embedded report

  TL-20476 Created new seminar setting 'Passing grade' and added 'Require passing grade' seminar activity completion option

    Seminar activity completion options have been enhanced to bring seminar in line
    with other Totara activities like assignment and quiz. Previously, seminar only
    had a 'Learner must receive a grade to complete this activity' option. This has
    been replaced by a 'Require grade' option with two choices: 'Yes, any grade' and
    'Yes, passing grade'.

    If 'Yes, passing grade' is chosen, a passing grade must be set for the seminar.
    The default passing grade can be set globally. Setting the passing grade higher
    than 0 enables the use of pass/fail marks on the activity completion report.

    In order to provide backward compatibility with previous seminar activity
    completion options, the upgrade will set 'Require grade' to 'Yes, any grade' and
    'Passing grade' to '0' on any seminar where 'Learner must receive a grade to
    complete this activity' is enabled. This has the effect of exactly reproducing
    the previous behaviour.

    In addition, this patch has fixed two other minor issues:
    * the facetoface_signups_status.createdby database field was not being updated
      when taking attendance
    * archived sign-up data entries were not being excluded from the computation of
      a seminar grade

  TL-20546 Added a new 'Event grade' column to seminar signup report source

  TL-20579 Improved deletion confirmation for hierarchy frameworks and items

    This patch unifies deletion confirmation for hierarchy frameworks and items, as
    well as adding details about related data to be deleted in the framework
    confirmation and bulk delete confirmation dialogues.

  TL-20756 Added new custom setting in section links block for the display style of topic link

    The new custom setting in section links block will allow the course editor to
    change the display style of topics within this block. By default, it will
    display the section link as a number. However, the course editor is able to
    switch to either section 'title only' or 'number and title'.
    Contributed by Russell England at Kineo USA

  TL-20799 Added support for whitelisting of known trusted SCORM packages

  TL-21036 Implemented a 'CSV for spreadsheets' export format for report builder

    This new CSV export format is designed for use with spreadsheets.

    It produces a CSV file that is close to RFC4180 but has an escape character in
    front of any data that may be interpreted by the spreadsheet application.

    We recommend that users use this export format if they have to export to CSV but
    intend to open the .csv file in a spreadsheet application as it protects them
    against CSV injection attacks.

    This export format is not enabled by default. Those wanting to use it must
    enable it within the 'Export options' setting for report builder.

  TL-21084 Improved seminar session Date/Time format and export for report builder

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
    * There is a new format for date/time with timezone for report builder:
      '2 July 2019, 5 PM
      Timezone: Pacific/Auckland'
    * All Date/Time columns have a proper ODS/Excel export

  TL-21098 Implemented job assignment GraphQL services and converted the profile page

    This is a technical improvement, introducing new GraphQL services for job
    assignments and converting the profile interface list of jobs to use the new
    services.

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

  TL-21109 Updated the course and certification completion import tools to use the new evidence functionality

    The course and certification completion import tools have been changed to use a
    system-defined evidence type when importing unrecognised courses and
    certifications. It is now no longer possible to upload custom field data for
    evidence using these tools.

  TL-21115 Added new database settings for encryption of database communication

    Full details on how to configure SSL communication with your database can be
    found in config-dist.php after upgrade.

  TL-21197 SQLSRV SSL connections now support the 'TrustServerCertificate' option

    TL-21115 introduced the ability to force database connections over SSL. However,
    SQLSRV required a signed certificate and there was no way to force the
    TrustServerCertificate connection option through Totara.

    A new dboption 'trustservercertificate' has been added that is passed through to
    the 'TrustServerCertificate' option during connection.

  TL-21198 Added Asset/Room/Facilitator filters to Seminar Sessions report

  TL-21422 Added a setting to display a seminar description on a course's homepage

  TL-21486 Added an 'Edit event' button to the seminar event details tab

  TL-21487 Added ability to mark seminar event and session attendance at different times

    The previous 'Mark attendance at' option is now separated into two options - an
    option as to when you can mark Session Attendance AND a separate option for when
    you can mark Event Attendance.

  TL-21491 Added [seminarname] and [seminardescription] placeholders for Seminar notifications

    The [seminarname] placeholder has been added to replace the [facetofacename]
    placeholder, although the system will still support both [seminarname] and
    [facetofacename] placeholders. An optional placeholder, [seminardescription],
    has also been added.

  TL-21507 New room/asset capabilities given to the 'editingteacher' and 'teacher' roles by default

    The following capabilities are now granted to the 'editingteacher' and 'teacher'
    roles by default:
    * mod/facetoface:manageadhocroom
    * mod/facetoface:managesitewideroom
    * mod/facetoface:manageadhocasset
    * mod/facetoface:managesitewideasset
    * mod/facetoface:manageadhocfacilitator
    * mod/facetoface:managesitewidefacilitator

  TL-21513 Added 'Require event over for' seminar activity completion criteria

    By default, seminar activities are considered complete as soon as the required
    completion criteria are achieved. With the ability to take attendance and mark
    grades at the beginning of sessions (or anytime) it is possible for trainers to
    trigger seminar activity completion before a seminar event is complete. 

    As this may not be a desirable outcome, there is now a 'Require event over for'
    criteria that delays activity completion for 0 or more days after the end of an
    event. To enable this functionality, there is a new scheduled task which is set
    to run every 5 minutes by default. Trigger delayed seminar activity completions
    \mod_facetoface\task\activity_completion_task

  TL-21516 Seminar event sessions can now have facilitators associated with them

  TL-21518 Seminar event dashboard improvements

    The following updates have been made to the seminar event dashboard:
    * Added support to multiple rooms and facilitators
    * Reorganised table column orders
    * Added ability to hide empty table columns
    * Action links and icons have been moved to the new dropdown menu
    * The event list tables are now updated in-place, rather than on page reloading

  TL-21554 Refined access control for privileged users suspending, activating and unlocking user accounts

    There is a new capability 'moodle/user:managelogin' controlling who can suspend,
    activate and unlock user accounts and change their passwords. Previously this
    was controlled via the 'moodle/user:update' capability.

  TL-21559 Added tenancy support to program and certification assignments

    Individual assignments and audience assignments are now restricted by tenancy in
    the selector dialogues.

  TL-21590 Tenant information can now be included when importing users via HR Import

    Added tenant 'tenantmember' and 'tenantparticipant'  to the HR Import user
    source

  TL-21595 Added backend support for report templates in report builder

  TL-21600 Improved the grid items functionality when reducing the browser size

    Previously theme root grid items used by the grid catalogue had white space on
    the right which prevented the full width being used. The issue has now been
    fixed with a new implementation of grid items.

    This will require CSS to be regenerated for themes that use LESS inheritance.

  TL-21686 Added inline editing of report titles when viewing a report

  TL-21739 Added option to display seminar room building and address values in addition to room name

  TL-21744 Added tenancy support to seminar user selector

  TL-21745 Ensured it is not possible to add non-participants as attendees in tenant contexts via CSV

  TL-21909 Added a 'Room name' column to the 'My future bookings' seminar report

    This change only affects new installations; admins of existing sites are
    encouraged to add a 'Room Name (linked to room details page)' column to the 'My
    Future Bookings' report to provide more complete information for learners.

  TL-22047 Renamed 'Hide in My Reports' setting name to 'Hide on user reports list' and updated its help string in report builder

  TL-22049 Moved and renamed 'View This Report' link when editing a report builder report

  TL-22132 Added 'Labels' and 'Summary' fields to report builder sources

    Report builder source classes now have 'sourcelabel' and 'sourcesummary'
    properties which will be used in the new report creation workflow.

  TL-22288 Added additional information to the audience deletion confirmation page

    The confirmation page now better informs the user on how assignments,
    visibility, and access provided to audience members will be affected by the
    deletion.

  TL-22292 Added event attendance status to seminar event reports and the seminar event dashboard

  TL-22362 All role-related changes now trigger events and are logged

    New role-related events:
    * core\event\role_created
    * core\event\role_updated
    * core\event\role_capability_updated (replaces
      core\event\role_capabilities_updated)
    * core\event\role_contextlevel_updated

  TL-22406 Redesigned report builder report creation

    This change improved the workflow for creating reports in report builder. Now
    report creation starts at the new report library that includes all available
    report sources plus the new report templates. The report sources and templates
    are categorised and can be filtered by the target audience in the report
    library.

    The report library is compatible with custom report sources and custom report
    templates so the list of items can be extended by partners.

  TL-22436 Added a new option for requiring users to apply filters before reports can be displayed

  TL-22446 Added already-selected items to seminar resource selection dialogues

    When selecting rooms, facilitators, or assets for a seminar session, the
    selection dialogue now includes any already-selected items, for reference and to
    allow them to be deselected when necessary.

  TL-22470 Added seminar events to facilitator user calendars where appropriate

    In addition, when seminar events are rendered for the calendar, only the session
    that pertains to the calendar date is included.

  TL-22527 Added additional JavaScript polyfills for IE 11

    Added the following polyfills to support modern JavaScript APIs in IE 11:
    * {String, Array}.prototype.includes
    * Array.prototype.\{find, findIndex}
    * NodeList.prototype.forEach
    * Object.\{entries, values}
    * Number.\{isFinite, isInteger, isNaN, parseFloat, parseInt}

  TL-22591 Improved the accessibility of 'Create' menu in the grid catalogue

  TL-22798 Improved the workflow for adding attendees to a seminar

    The process for adding one or more attendees to a seminar involves three steps.
    Step 1 is selecting or providing the list of potential attendees. Step 2 is
    confirming the list and setting the notification preferences. Step 3 creates the
    signups and lists any errors that occurred. 

    This improvement addresses some quirks in the behaviour of this process, and
    makes error handling more consistent. Specifically:
    1.  Learners who are not enrolled in the course are enrolled by the system
    before signup is attempted. Previously, if such a learner could not be signed up
    for any reason, they would remain enrolled in the course. This issue has been
    fixed, and these enrolments are removed at step 3.
    2.  Some checks for whether a learner could sign up to a seminar happened at
    step 1, and others at step 2. All checks now happen at step 2. Learners who can
    sign up are added as attendees (or potential attendees), and learners who cannot
    are listed in the 'Bulk add attendees' error report at step 3.
    3.  Any learners specified at step 1 who could not be matched with users in the
    system would cause the process to fail at step 1. The process now continues for
    learners who are matched with user accounts, and the missing learners are listed
    in the 'Bulk add attendees' error report at step 3.
    4.  Specifying any learners who were already signed up would cause the process
    to fail at step 1. Learners who are already signed up are now silently ignored,
    and the process continues for everyone else.

    Please note this is a change in behaviour: there are no longer any errors that
    stop the bulk add attendees process. Learners who can be signed up are signed
    up, and those who cannot are detailed in the 'Bulk add attendees' error report.

  TL-22972 Added the ability to hide the 'Export attendance' button on the seminar events dashboard

    In order to allow administrators to prevent trainers from downloading the full
    list of seminar attendees, a new 'exportattendance' capability was created.
    Users without this capability do not see the 'Export attendance' button on the
    seminar events dashboard. 

    The new capability has been assigned to all roles that currently have
    'viewattendees' capability by default.

  TL-22993 Simplified default seminar terms and conditions

    The new default text is: 'Check the box to confirm your eligibility to sign up
    to this seminar.'

  TL-22997 Grid catalogue can now be customised with top and bottom page blocks

  TL-23027 Updated who can see 'Join now' links for seminars

    When a room with a room link is assigned to a seminar session, a 'Join now' link
    appears for attendees while the session is in progress. With this patch, the
    'Join now' link also appears for any facilitators assigned to the same session,
    and to any users who are assigned the 'mod/facetoface:joinanyvirtualroom'
    capability in the course or activity context. Trainers and editing trainers are
    given this capability by default.

  TL-23053 Improved user assignment search for appraisals

    Previously, the user search for appraisal assignments, 360 feedback and report
    builder global restrictions did not find expected results for some search terms,
    e.g. when typing first name and last name of a user. This has been improved by
    switching to a better keyword parsing method.

  TL-23411 Prevented potential problems with memory use of record_exists database queries

  TL-23488 Password with one extra trailing space is now considered valid when logging in or changing passwords

  TL-23502 Removed obsolete drivers from auth_db and enrol_database settings

  TL-23820 Added site administration overview link to the site administration menu at the top of the page

  TL-23904 Minor visual improvements to the report selection page within report builder

  TL-23954 Deprecated the Roots and Basis themes

  TL-24308 Added the ability to have guest accessible dashboards

    There is a new setting for dashboards that enables guest access to a dashboard.
    Prior to this change guests were not able to view any dashboards.

    When enabling this setting be aware that it may expose sensitive content on the
    dashboard to guests.

  TL-24326 Deprecated the 'Flash animation' player plugin

  TL-24327 Deprecated the 'Email protection' content filter

  TL-24328 Deprecated the 'Demo' course format plugin

  TL-24329 Deprecated the 'Social' course format plugin

  TL-24334 Removed deprecated 'mod_assignment' and 'tool_assignmentupgrade' plugins

  TL-24335 Deprecated the 'Survey' activity module

  TL-24337 Deprecated the 'Mentees' block

  TL-24338 Removed the already-deprecated 'Quiz results' block

  TL-24339 Migrated profile fields with obsolete messaging IDs to hidden custom profile fields

    The affected fields are 'ICQ number', 'Yahoo ID', 'MSN ID' and 'AIM ID'.

  TL-24340 Deprecated the 'HTML tidy' content filter

  TL-24387 Improved seminar notification view by adding link to resource title

  TL-24399 Converted monolithic user profile to multiple instances of new 'User Profile block'

    The automatically-generated comprehensive user profile has been removed and
    replaced with a block area to allow full customisation of the user profile page.

    Additionally, the ability to create custom user profiles has been removed.
    Custom user profiles will persist until the admin clicks the 'Reset profile for
    all users' button on the default user profile. At that point, only the default
    user profile will remain, and all users will always see the default. Users will
    not have the option of customising their profile page by adding blocks.

    A new block, 'User Profile', has been created which will display selected items
    from a single user profile category. By using multiple User Profile blocks, one
    for each category, an admin can define exactly which categories and fields
    should be displayed on user profiles.

    A limited number of user profile blocks have been added to the default profile
    to recreate the essence of the old user profile and ensure that key
    functionality is still accessible to users. These can, of course, be
    reconfigured or removed as necessary.

  TL-24432 Removed the already-deprecated 'Course progress report' block and the 'Frontpage combolist' block

  TL-24459 Improve keyboard accessibility of the InlineEditing component

  TL-24475 All MNET functionality has been deprecated and will be removed in Totara 14

  TL-24476 Deprecated the 'Legacy log' log store plugin

  TL-24509 Removed support for gopher and ftp links from HTML texts due to lack of support in modern browsers

  TL-24554 Added a tooltip to 'Claim URL' field when creating badges

  TL-24660 Added a new 'Certification Exceptions' report source

  TL-24664 Implemented a flexible means for plugins to introduce preview options for image handling

    Prior to this patch, the options for query string 'preview' in image url are
    limited to three options.

    With this patch, the new options are able to be added via other plugins and can
    be overridden by theme.

  TL-24684 Removed references to the deprecated Mozilla badge backpack

  TL-24703 Added a new learning plan assignment type for programs

    Previously, when a program was added to a user's learning plan, it was not
    considered as 'Required learning'. This has now changed. All programs added to a
    user's learning plan are now considered as 'Required learning'.

    The learning plan assignment behaviour is the same as the other assignment
    types except that there is no user interface for the learning plan
    assignments within the program assignments section. Learning plan assignments
    can only be made from the learning plan interfaces and not from within the
    program itself. The creation of learning plan assignments only occurs when the
    learning plan is approved. When a program is removed from a learning plan, and
    the changes are approved, learning plan assignments to the program will be
    removed.

    When upgrading to Totara 13 from earlier versions, learning plan assignments
    will be created as required.

  TL-24759 Added a setting that allows MS Teams to access catalogue images

    A new setting 'Force users to log in to view catalogue pictures' has been added
    to Totara. When enabled it ensures that users are required to log in in order to
    access catalogue entry images. The setting is on by default.

    When turned off, users attempting to access these images will not be required to
    log in. This enables MS Teams and other integration to also access these images.

    We recommend keeping this setting turned on unless public access to these images
    is explicitly required for your site.

  TL-24910 Improved file serving performance through the use of read-only lock-less session mode

  TL-24975 Deprecated contextual user profile page

  TL-25020 Added new 'details content' setting to the grid catalogue general settings

    If this setting is enabled, as it will be automatically for any upgrading sites,
    there should be no change in current behaviour. When a catalogue item is
    clicked, a details pop-up will be displayed with a view or enrol button.

    If the setting is disabled, as it will be automatically for any new
    installations, when you click an item on the catalogue it will redirect you
    immediately to the URL used by the view button in the details pop-up instead of
    displaying the details.

  TL-25164 Implemented generic relationships for resolving relationships between users

  TL-25276 Added various improvements in Redis session locking and debugging

  TL-25321 Added a new native MySQL locking factory

  TL-25334 Added a new native MSSQL Server locking factory

  TL-25382 Added a new native Redis session handler

    See sample '\core\session\redis5' session handler configuration in
    config-dist.php for more information.

  TL-25463 Added support for Open Badges 2.1

  TL-25719 Added the usability of the job assignments form by grouping based upon purpose

  TL-25727 Added support for Redis Sentinel in Redis MCU store and session handler

  TL-25758 The sessions looper only returns the facilitating sessions on facilitator notifications

  TL-25760 Added the following seminar placeholders

     
    * [coursenamelink] - Name of course with link
    * [seminarnamelink] - Name of seminar activity with link
    * [eventpagelink] - Link to the event page

  TL-26100 Added support for 'customhelptext' option to filters

    Provides 'customtext' option for filters so the help text can be customised.

  TL-26107 Added EXIF orientation correction to preview image generation

    Provided the PHP EXIF extension is available, Totara will use the EXIF
    orientation tag to correct rotated and/or mirrored JPEG images taken by a
    smartphone.

  TL-26130 Removed the ability to create/manage a custom user profile

    Existing custom user profiles are not deleted, but new ones cannot be created.
    The default user profile is used for all users.

  TL-26198 Disabled SCORM network connectivity checks when using mobile app webview

  TL-26408 Updated rules for showing course statuses when viewing certifications

    This change removes the display of irrelevant progress bars when viewing a
    certification. When a user is on the original certification path, progress for
    courses on the re-certification path are not shown, and when the user is on the
    re-certification path the original certification path course progresses are not
    shown. This is because course progress gets reset for the user when they are
    transitioned into the next path.

  TL-26606 Added ability to set Totara form action_button to be a primary button

    This adds the 'primary-btn' class to the button and it is styled accordingly.

  TL-26704 Added support for read replicas within the Redis cachestore

  TL-26928 Replaced bespoke user details header in competencies with system-wide component

  TL-26963 Reorganised the advanced feature's admin settings pages

  TL-27001 Changed course section summary format of new sections to the preferred editor format

    Previously, when sections were added to a course, the format of the section
    summary was hard-coded to HTML. The course creator's editor preference setting
    is now used to determine what format should be used for the summary of new
    sections.

  TL-27243 Fixed dropdown group keyboard focus

    Prevented group names in dropdowns from receiving focus when navigating with a
    keyboard.

  TL-27274 Changing a user's password now redirects back to the user's preference page

  TL-27545 Added support for bulk inserts in the Mssql database driver

  TL-27580 Ensured third party libraries are all correctly recorded in product

  TL-27656 Added Perform feature usage to registration data

    Added high-level usage data on Perform features into the registration data
    system.

    This patch adds high-level counts of number of records from certain database
    tables, for the purpose of assessing general system usage:
    *  Performance activities
    ** Performance activities enabled
    ** Number of activities
    ** Number of user assignments
    ** Number of subject instances
    ** Number of participant instances
    ** Number of element responses
    * Competencies
    ** Competency assignments enabled
    ** Number of user assignments
    ** Number of assignments
    ** Number of achievements
    * Evidence
    ** Evidence enabled
    ** Number of evidence items
    ** Number of evidence types

  TL-27676 Added Engage feature usage to registration data

    Added high-level usage data on Engage features into the registration data
    system.

    This patch adds high-level counts of number of records from certain database
    tables, for the purpose of assessing general system usage:
    * Workspaces
    ** Workspaces enabled
    ** Number of workspaces
    ** Number of workspace discussions
    * Recommender
    ** Recommender enabled
    ** Number of interactions
    ** Number of items
    ** Number of trending items
    ** Number of users
    *  Comments
    ** Number of comments in system
    * Engage
    ** Resources enabled
    ** Number of surveys
    ** Number of resources
    ** Number of bookmarks
    ** Number of ratings
    * MS Teams
    ** MS Teams enabled
    ** Number of bots
    ** Number of MS team users
    ** Number of channels
    ** Number of subscriptions
    ** Number of MS team tenants
    * Playlists
    ** Number of playlists
    * Reactions
    ** Number of reactions
    * Topics
    ** Number of topics
    ** Number of topic instances

  TL-27795 Moved shared CSS variables from Vue components to variable files

    SCSS variables declared within a Vue component shouldn't be used within other
    components. Common variables should instead be declared within SCSS. This patch
    moves all currently shared variables into SCSS.

  TL-27806 Added core feature usage to registration data

    Added high-level usage data on core features into the registration data system.

    This patch adds high-level counts of number of records from certain database
    tables, for the purpose of assessing general system usage:
    * Catalogue
    ** Catalogue mode (grid, report, etc)
    ** Active learning types (course, program, certification, etc)
    * Mobile
    ** Mobile enabled
    ** AirNotifier enabled
    ** AirNotifier customised (true if no longer using the default URL)
    ** Number of devices that have been linked
    ** Number of offline SCORMs
    ** Number of mobile compatible courses

  TL-27834 Added single column layout component for Vue pages

    Added a single column layout component for Vue pages. This allows for the
    structure of Vue pages to use a more consistent structure.

  TL-27872 Enabled site admin to request an auto-generated AirNotifier app code from push.totaralearning.com

  TL-27888 Improved database version detection for MySQL and MariaDB

  TL-27912 Changed default database collation for MySQL 8 to utf8mb4_0900_as_cs

    Please note that MariaDB does not have any suitable case and accent sensitive
    collation – production servers should be upgraded to MySQL 8.

  TL-27934 Added caching to get_site() function

    Adds a new cache definition for storing the site course in an application cache
    for faster retrieval of the site course on each request.

Bug fixes
---------

  TL-14099 Fixed a bug in course completion determination when multiple enrolments are present

    Previously when a user has multiple enrolments in the same course, and course
    completion is determined by how many days the user was enrolled in the course,
    the cron job that updated course completions would fail.

  TL-16324 Fixed global search navigation when Solr is enabled and configured

    Prior to this patch, the 'Manage global search' page would only be shown in the
    site administration structure on certain pages. It is now shown consistently
    when intended.

  TL-18946 Added missing recipient types and descriptions to seminar notifications

    Prior to this patch, there were a few notifications in seminar that did not
    specify the recipient types nor the description of the notification.

    With this patch, the recipient types and description of notifications are now
    specified.

  TL-19054 Set notification type when cloning a report builder embedded report to a warning instead of an error

  TL-20305 Prevented filters from being changed on the seminar events dashboard while events are loading

  TL-20327 Fixed race condition when dialogs are not initialised when adding components to a learning plan

  TL-20338 Removed deleted users from seminar views

    Prior to this patch, when a user record was deleted from the system, all of the
    user's signup records remained visible in seminar views.

    With this patch, only users with permission to see deleted users
    (totara/core:seedeletedusers capability) will be able to see or modify the
    signups of deleted users.

  TL-20453 Fixed broken 'Turn editing off' link on the seminar attendees page

  TL-20513 Ensured that seminar activity 'View all events' link on course homepage isn't hidden by horizontal scrollbar on Mac OS

    On Mac OS, the default System Preference is to hide scrollbars until needed.
    When the scrollbars are shown, they may obscure content or make it difficult to
    click links that are underneath them. This was sometimes the case with the 'View
    all events' link under seminar activities on course homepages.

    The link has been made larger, and padding added, to ensure that it is still
    clickable if a horizontal scrollbar appears under it.

  TL-20520 Fixed saved-search functionality on seminar room and asset embedded reports

    Added 'rb_config' and '$sid' to asset and room embedded reports to ensure saved
    searched can be viewed.

  TL-20547 Fixed JavaScript validation on Moodle forms

    Previously, when calls were made to $PAGE->get_end_code(false), AMD JavaScript
    was not being added to the HTML. This has now been corrected. This enables
    Moodle form validation when editing appraisals, audience rules and seminar
    times, rooms and assets. 

  TL-20629 Fixed sign-up links on course page that pointed to the wrong URL when seminar direct enrolment was enabled

  TL-20685 Fixed a bug preventing the export of seminar events

  TL-20793 Fixed handling of the 'required' attribute when applied to the Atto editor

  TL-20804 Seminar 'Add users' step 2 now respects the 'showuseridentity' config setting

    Previously, user full name, email address,  username and ID number were
    displayed in step 2 of the 'Add user' workflow without respecting the
    'showuseridentity' config setting. Now ID number and username are no longer
    shown, and display of email address respects the 'showuseridentity' config
    setting.

  TL-20847 Fixed a bug that prevented taking seminar session attendance in some cases

    In the previous release of Totara Evergreen, when the in-memory list of seminar
    sessions was sorted, it did not maintain an ID-to-session relationship. This
    caused seminar session attendance to fail with an error because the requested
    session could not be looked up by ID.

    With this patch, session IDs in the list are preserved during sorting, allowing
    the requested session to be found.

  TL-20854 Fixed the creation and editing of multi-select cohort rules

    TL-20547 introduced a regression when editing a multi-select cohort rule where
    it couldn't be saved. This is now fixed.

  TL-20987 Fixed double encoding of user identity fields in the history grader report

    Any customisations made using the '/grade/report/history/users_ajax.php' file
    should check the output of user identity fields after upgrade to ensure proper
    sanitisation is happening on output.

  TL-20998 Fixed possible double entity encoding when rendering templates in javascript

    This was evident in default column names when creating new reports in report
    builder, but has been fixed in core template to resolve any unfound instances.

  TL-20999 Fixed seminar grade input field to respect the course 'grade_decimalpoints' configuration

  TL-21001 Fixed regression in the report builder management UI where special characters were incorrectly encoded as entities

  TL-21049 Fixed improperly removed seminar event roles

    Seminar refactoring in the previous release created a bug that led to improper
    deletion of seminar event roles. This, in turn, caused an error when attempting
    to update seminar events that had unassigned event roles.

    The bug has been fixed, and improperly deleted roles will be removed correctly
    on upgrade.

  TL-21117 Fixed a bug that generated the wrong page URL for seminar session 'Take attendance' page

  TL-21149 Images displayed in a static form field no longer cause horizontal scroll

    This will require CSS to be regenerated for themes that use LESS inheritance.

  TL-21252 Added database table keys skipped during upgrade and migration

  TL-21275 Fixed recent regression with double encoded entities in report exports

    Replaced relevant report builder calls to 'format_string()' with calls to the
    report builder display class 'format_string' which correctly encodes the string
    according to the output format.

  TL-21290 Fixed report builder saved searches to be sorted alphabetically in the 'Manage your saved searches' dialogue 

  TL-21365 Removed duplicate records from the cancelled attendees list for seminar events with multiple sessions

  TL-21378 Updated seminar 'Message users' tab to respect 'User identity' settings when displaying lists of users

  TL-21412 Fixed database query logging when ignoring errors in the database transactions

  TL-21436 Updated seminar date/time columns in report builder to use the correct timezone

    Seminar sessions can be set to display their start and end time in a particular
    timezone, known as the event timezone. Aside from the start and end time, all
    other seminar date/time values (such as the signup period start and end time, or
    the date and time when a user declares interest) use the system timezone.

    This update causes all seminar-related date/time values, except for the session
    start and end times, to be displayed using the system timezone.

  TL-21536 Updated the default capabilities of the Trainer and Editing Trainer roles to allow 'mod/facetoface:viewallsessions'

    Previously the Trainer and Editing Trainer roles were unable to view the seminar
    'Event details' page without the 'mod/facetoface:viewallsessions' capability.
    These roles will now have the capability enabled by default for new
    installations. Sites upgrading to this release are recommended to manually
    enable the capability for the roles.

  TL-21631 Fixed inconsistent booking status in events and sessions report

    Previously events with booking status 'closed' were showing as open in the
    events and session reports. Now the 'booking status' column is updated in both
    reports to reflect the actual booking state.

  TL-21992 Fixed incorrect graph layouts when using the progress chart type in report builder

  TL-22001 Fixed minor visual bugs in ChartJS pie and doughnut charts

    Several small visual improvements have been made to the pie and doughnut chart
    types within ChartJS:

    * Chart colours have been adjusted to ensure similar hues no longer appear next
      to each other in a chart.
    * Thin white borders have been added between slices.
    * Increased the inner edge diameter for doughnut charts, reducing their
      thickness.

  TL-22041 Ensured activity descriptions are consistently cleaned

    Prior to this change, the activity descriptions on the course page were cleaned
    regardless of the 'Disable consistent cleaning' setting. This was inconsistent
    with the display of activity descriptions throughout the rest of the site.

    The 'Disable consistent cleaning' setting is now consistently respected.

  TL-22062 Allow more relevant access to goal names in the 'Goal status' report

    Previously, the 'Goal name' column had been changed to only appear when the user
    had the 'totara/hierarchy:viewallgoals' capability due to data privacy and
    protection concerns for user reports created by the report source. This resulted
    in user's being unable to see their own company goals in the embedded or user
    reports. Similarly, managers could not see their team's goals in the report
    unless they had this broad capability.

    Now, the 'Goal name' column is shown (using a new report builder display class)
    if the viewer has any of the following:
    * the capability 'totara/hierarchy:viewallgoals'
    * the goal is their own and they have the 'totara/hierarchy:viewownpersonalgoal'
      capability
    * the goal belongs to someone the viewer manages and they have
      'totara/hierarchy:viewstaffpersonalgoal' capability.

  TL-22124 Fixed line chart line colour not matching the dots in ChartJS

  TL-22239 Added a missing 'Number of unable to attend' column in seminar report

  TL-22241 Fixed the description of date-based dynamic audience rules to match the back-end logic

  TL-22272 Fixed the 'Record of Learning: Courses' report to ensure correct records for active and completed learning are displayed

    TL-20772 incorrectly applied report parameters which led to active courses
    appearing in the 'Completed learning' report for a user. This has now been fixed
    and the users will see only completed courses when viewing this report under
    their Record of Learning.

  TL-22577 Converted self-completion functionality on the course page to use a standard checkbox for improved accessibility

  TL-22825 Fixed incorrect licence information on the ChartJS doughnut label plugin

  TL-22832 Fixed the display of the manager's name on seminar event info page when the learner has more than one manager

  TL-22911 Fixed seminar event grade not being updated when a seminar event was cancelled or deleted

    The grading subsystem has been decoupled from the core seminar component. Grades
    and activity completion status will be updated through an event observer.
    Note that existing grades are not automatically recalculated on upgrade.

  TL-22947 Totara forms now wait for the previous submission to complete

    Previously some Totara forms could be submitted multiple times by clicking the
    save button in quick succession causing multiple records to be created.

  TL-23120 Fixed a bug that sent a booking confirmation when the attendance state was set to 'not set' on the taking attendance page

  TL-23157 Fixed inconsistent sorting of enrolment methods on the course enrolment page

  TL-23225 Adjusted popover's z-index to display in the correct stack order

    This will require CSS to be regenerated for themes that use LESS inheritance.

  TL-23234 Fixed profiling runs table being hidden from view

  TL-23520 Fixed shadow being hidden by graph image on the reports page

    This will require CSS to be regenerated for themes that use LESS inheritance.

  TL-23521 Removed styling  of report name when viewing the reports page

    This will require CSS to be regenerated for themes that use LESS inheritance.

  TL-23695 Fixed error in task block when a manager approval seminar event is deleted while approval is pending

  TL-23732 Ensured progress doughnuts are the same size

    This will require CSS to be regenerated for themes that use LESS inheritance.

  TL-23744 Fixed the issue where 'Report Manager' block was not displaying report correctly

  TL-23936 Ensured that when seminar events in the past are deleted, their associated calendar events are also removed

  TL-23969 Uploading seminar attendance records now correctly prevents changes to attendance status for archived attendees

  TL-24882 Fixed a PHP warning about non-numeric values when displaying graphs in report builder

  TL-25075 Saved search has been removed from the audience admin UI due to this functionality not being compatible with the embedded reports

API changes
-----------

  TL-9072  Refactored certification core code

    Introduced some separation around the transitions and creating completion
    records for certifications. This added specific functions for certification-only
    operations such as becoming certified, window opening and expiring. Conditions
    that were providing similar functionality in programs no longer work if the
    program being supplied to them is a certification, and throw an exception.

  TL-14412 Deprecated custom notification handling

    The following functions have been deprecated as part of this:
    * Function: totara_get_notifications() (alternative method:
      \core\notification::fetch())
    * Function: Function: totara_set_notification() (alternative: redirect or
      \core\notification::*())
    * Function: totara_convert_notification_to_legacy_array() (no alternative)
    * Function: totara_queue_append() (no alternative)
    * Function: totara_queue_shift() (no alternative)
    * Method: \core\notification::add_totara_legacy() (no alternative)

  TL-16531 Refactored internal 'totara_sync' code to use traits

  TL-16600 Deprecated the rest of facetoface_send_* functions

  TL-17311 Converted seminar CSS to use LESS

    IMPORTANT: This will require CSS to be regenerated for themes that use LESS
    inheritance.

  TL-18699 Separated the requested approval state into requested manager approval and requested role approval

    The requested approval state has been split into two separate states, requested
    manager approval state, and the requested role approval state.
    This allows for better control and transitioning when in a requested approval
    state.

  TL-19892 Abandoned DbUnit extension for PHPUnit has been removed

    phpunit_ArrayDataSet class no longer extends AbstractDataSet from DbUnit. Any
    PHPUnit tests in customisations that may be failing due to this change will need
    to be fixed by the developers.

  TL-20021 Deprecated event time status functions in facetoface

    Deprecated functions:
    * facetoface_allow_user_cancellation()
    * facetoface_is_adminapprover()
    * facetoface_get_manager_list()
    * facetoface_save_customfield_value()
    * facetoface_get_customfield_value()

    For more information, see mod/facetoface/upgrade.txt

  TL-20063 Converted seminar take attendance JavaScript from YUI module to AMD module

  TL-20331 Updated Basis notification icon definitions

    Previously the notification icon definitions provided by Basis did not include
    the component. This has now been corrected.

  TL-20362 Converted M.totara_plan_course_find from a YUI module to an AMD module

  TL-20363 Converted M.totara_plan_program_find from a YUI module to an AMD module

  TL-20364 Converted M.totara_plan_competency_find from a YUI module to an AMD module

  TL-20376 Deprecated date management functions related to facetoface

    Deprecated functions:
    1.  facetoface_save_dates()
    2.  facetoface_session_dates_check()

    For more information, see mod/facetoface/upgrade.txt

  TL-20377 Deprecated notification-related functions in mod/facetoface/lib.php

    Deprecated functions
    * facetoface_notify_under_capacity()
    * facetoface_notify_registration_ended()
    * facetoface_cancel_pending_requests()

    For more information, see mod/facetoface/upgrade.txt

  TL-20378 Deprecated environment functions related to facetoface 

    Deprecated functions:
    1.  facetoface_get_session()
    2.  facetoface_get_env_session()

    For more information, see mod/facetoface/upgrade.txt

  TL-20380 Deprecated export functionality within facetoface

    Deprecated functions:
    1.  facetoface_write_activity_attendance()
    2.  facetoface_get_user_customfields()

    For more information, see mod/facetoface/upgrade.txt

  TL-20381 Deprecated trivial facetoface functions

    Deprecated functions:
    * facetoface_allow_user_cancellation()
    * facetoface_is_adminapprover()
    * facetoface_get_manager_list()
    * facetoface_save_customfield_value()
    * facetoface_get_customfield_value()

    For more information, see mod/facetoface/upgrade.txt

  TL-20383 Deprecated seminar's attendees retriever functions

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

  TL-20536 Added Behat steps for checking emails

    Developers can now write Behat steps that trigger the creation of emails which
    will be captured and can be examined for accuracy. These are the Behat steps
    available:
    1.  I reset the email sink
    2.  the following emails should have been sent
    3.  the following emails should not have been sent
    4.  I close the email sink

  TL-20542 The phar stream wrapper is now disabled by default during setup

    Phar is an advanced means of packaging and reading PHP code. It is not used by
    Totara, and in order to reduce the security surface area of the product we have
    disabled it by default.

    If you have a plugin or customisation that requires the phar stream wrapper to
    be available, we recommend you enable it in code immediately before it is
    required, and disable it again immediately afterwards.

  TL-20548 'runTemplateJS()' now returns an ES6 promise

    The 'runTemplateJS' function in the core/templates AMD library now returns an
    ES6 Promise once all UI components have been initialised

  TL-20749 New "ttr_tablename" syntax is allowed in SQL queries in addition to current {tablename}

    As well as using

    {tablename}

    in an SQL query it is now also possible to use "ttr_tablename".
    This enables SQL queries to be written that can be processed by code parsers and
    IDEs.
    Developers may want to consider using ttr_ as your default database prefix from
    now on.

  TL-20765 Added a new SQL class to improve handling of raw SQL in DML API

  TL-20819 Added a new interface for placeholder objects used within 'get_string()' calls

    Developers can now pass objects which implement 'core_string_placeholders' to
    the third parameter of 'get_string'. The replace function which these objects
    provide will be used to perform string placeholder substitution. This allows
    more powerful and complex placeholder systems to be implemented, in a consistent
    and reusable way. All values which could previously be passed as the third
    parameter of 'get_string' are still supported.

  TL-20857 Added method to clear visible notifications banners via JavaScript

  TL-20864 Upgraded jQuery to 3.4.1

    jQuery changelog can be found at
    https://blog.jquery.com/2019/04/10/jquery-3-4-0-released/

  TL-20918 Implemented new DML function 'set_fields' and 'set_fields_select' to update multiple fields in a table

  TL-20924 Updated PHPMailer to version 6.0.7

  TL-21024 Added support for enforced foreign key consistency

    Onupdate and ondelete referential integrity actions can now be added to foreign
    key relations. By default foreign keys are not enforced in any way.

    During definition of a foreign key using the XMLDB editor you can now choose to
    enforce referential integrity through set actions. The following actions are
    available:
    * 'restrict' blocks violation of foreign keys
    * 'cascade' propagates deletes
    * 'setnull' changes value to NULL

  TL-21040 Converted report_loglive YUI module to AMD module

    This removes the original YUI module.

  TL-21176 Upgraded chart.js library to version 2.8.0

  TL-21177 Added 'core/popover:destroy' event to the popover component

  TL-21222 Added support for deferring the creation of foreign keys

    This improvement extends TL-21024 which added support for enforcing foreign key
    relationships within install.xml.

    It is now possible to mark a foreign key relationship as deferred within
    install.xml, causing the system to skip the creation of the foreign key during
    installation. The developer is then responsible for creating the foreign key at
    the right time within an install.php file.

  TL-21230 Added a new transaction function in DML which accepts a Closure

    The new 'transaction()' method accepts a Closure which is automatically wrapped
    in a transaction. This is an alternative syntax to the traditional transaction
    handling.

  TL-21240 Extracted class 'program_utilities' into its own autoloaded class '\totara_program\utils'

  TL-21256 Nested transactions can be safely rolled back

    Previously transaction rollbacks were not supposed to be used from non-system
    code and they were not allowed at all in nested transactions.

    Rollback of individual nested transactions is now fully supported, and it is
    also not required to supply an exception when rolling back nested or main
    transactions.

  TL-21288 Relative file serving now facilitates file serving options including 'allowxss'

  TL-21327 Extracted program exceptions code into autoloaded classes \totara_program\exception\*

  TL-21368 Implemented formatters to be used in GraphQL type resolvers

    To simplify formatting of objects returned by GraphQL type resolvers a formatter
    can be used. It defines a map using field names for the keys and field format
    functions for the values. The formatter will get the value from the object, run
    it through the format function defined in the map and return the formatted
    value. Currently we support text (using format_text()), string (using
    format_string()) and date formatters. Custom field formatters can easily be
    implemented extending the base field formatter. 

    The existing helper functions format_text() and format_date()
    in \core\webapi\execution_context were deprecated in favour of the new field
    formatters \totara_core\formatter\field\text_field_formatter
    and \totara_core\formatter\field\date_field_formatter.

    Documentation: https://help.totaralearning.com/display/DEV/Formatters

  TL-21435 Removed typo3 library dependency from the 'core_text' class

  TL-21501 Replaced deprecated PHPExcel with PHPSpreadsheet library

  TL-21563 Removed 'portfolio_picasa' and 'repository_picasa' plugins that have been deprecated by Google

    In January 2019, Google deprecated its Picasa Web Albums Data API and disabled
    all associated OAuth scopes. In March 2019, the Picasa Web Albums API was
    completely turned off. We've removed the associated plugin and repository as
    they will no longer be functional.

  TL-21711 Extracted report builder content code into autoloaded classes \totara_reportbuilder\rb\content\*

  TL-21723 Added support for iteration over very large record sets to the DML layer

  TL-21810 Implemented performance metrics being returned in GraphQL query / mutation results if performance debugging is turned on

  TL-21822 Added a new 'Abstract' text field for report builder reports to allow content to be truncated and displayed correctly

  TL-21825 Implemented a cursor-based paginator for the ORM and DML

    The cursors paginator enables pagination using an opaque cursor. It can be used
    for paginating queries using a 'load more' approach to load the next set of
    results. 

    Using an opaque cursor also provides the benefit of encoding information used
    for classic offset-based pagination. 

    This patch includes a cursor paginator for the ORM and a paginator supporting
    classic offset-based pagination for both, the ORM and DML queries.

    For further information and documentation please refer to the paginator
    documentation in the public developer documentation.

  TL-21922 Introduced and applied prettier to .graphql and .grapqhls files

    This patch adds prettier support for .graphql and .graphqls files. It also adds
    a grunt task for it which is automatically run with grunt.

    Make sure you update your node modules via 'npm install'. To trigger prettier to
    format all graphql/graphqls files use './node_modules/.bin/grunt prettier'.

    Instructions on how to integrate prettier with your IDE can be found here:
    https://prettier.io/docs/en/editors.html.

  TL-21974 Added support for allowed values constraint on integer and character fields in database schema files

  TL-22069 Fixed a bug where dropping a test database had not been possible on MySQL due to foreign keys

  TL-22203 Upgraded PHP-CSS-Parser to 8.3.0, allowing for usage of calc()

  TL-22249 The 'Cancel' modal can now have a custom string for the 'Cancel' button

  TL-22250 Plugins can define multiple *.graphqls schema files within the webapi directory and all will be included

    Previously only one schema.graphqls file was supported. As the schema grows, the
    files can become quite large. You can now split up the file into multiple
    .graphqls files.

  TL-22255 Extend registration data to cover multi-tenancy usage

  TL-22399 Fixed majority of compatibility issues with PHP 7.4

  TL-22617 Move advanced feature checks from lib file into namespaced class

    The old functions were marked as deprecated: totara_feature_visible(),
    totara_feature_disabled()

    Please use \totara_core\advanced_feature::is_enabled() and
    \totara_core\advanced_feature::is_disabled() instead.

    Function totara_feature_hidden() is now deprecated without an alternative in the
    advanced_features class as the hidden status is no longer supported. Please use
    \totara_core\advanced_feature::is_disabled() instead.

    The advanced features settings page was modified to show the term 'Enable'
    instead of 'Show' to match the actual meaning of the setting. 

  TL-22665 Imported latest SimplePie 1.5.3 library

  TL-22803 Invalid composed unique indexes with nullable columns are now reported in PHPUnit test

  TL-22888 Improved the debugging message with human-readable callback name

    Prior to this patch, when an event or hook callback threw an exception, the
    debugging message to capture the error was not clear about the callback name,
    especially when the callback was specified using an array, or written as a
    closure.

    This patch makes it easier to see which callback threw an exception by providing
    a readable name in all cases.

  TL-23038 Improved sanitisation of content included when handling fatal errors

  TL-23121 Upgraded Bootstrap to 3.4.1

    Bootstrap JavaScript has been upgraded from version 3.3.4 to 3.4.1 and Bootstrap
    CSS has been upgraded from 3.3.7 to 3.4.1. This includes a number of minor bug
    fixes and improvements. All security issues had previously been backported.

  TL-23255 DML counted recordsets were completely deprecated, use two separate queries for record counting instead

  TL-23279 Added 'to_array()' method to database recordset

  TL-23280 Improved the handling of recordsets in HR Import

    Made sure that database recordsets are closed properly in HR Import
    hierarchy.element.class.php

  TL-23322 Imported HTML Purifier 4.12.0

  TL-23323 Imported TCPDF 6.3.2

  TL-23324 Imported AdoDB 5.20.15

  TL-23336 Upgraded PHP-CSS-Parser, fixing an issue with RTLCSS control comments introduced by the previous upgrade to 8.3.0

  TL-23339 Added redirecting hooks functionality in phpunit tests

    Analogue to the events hooks can now be intercepted in unit tests to be able to
    check if a hook was executed.

    Use '$sink = $this->redirectHooks();' to turn redirection on and all hooks
    executed after that will land in the hook sink. No watchers will be called.

  TL-23405 Added '_unkeyed' functions to the DML select functions

    This allows the return of database results without keying by the first column
    without using recordsets. This has no significant performance impact compared to
    using a recordset.

  TL-23453 Added support for reversed order in core_collator, core_collator::SORT_REGULAR constant was changed to 128

  TL-23460 Data fetching from MSSQL database has been rewritten to improve reliability

    Please note it may be required to increase available PHP memory compared to
    previous releases.

  TL-23494 Imported mustache library version 2.13.0

  TL-23507 Updated markdown library to version 1.9.0

  TL-23551 Moved seminar assets and deprecated methods into traits

  TL-23554 Updated PHPMailer to version 6.1.4

  TL-23645 Backport mod_lti changes from Moodle 3.8.1

  TL-23658 Changed content marketplace to allow non-integer learning object keys

    The function 'get_learning_object' in the search class has been changed. Instead
    of an integer, it now expects a string. All custom content marketplaces will
    need to update their function signatures to match this change, otherwise an
    error will be reported when trying to fetch learning object details. After
    making a change to your custom content marketplace, you can check that the
    change was successful by going to Explore Content, clicking a learning object,
    and seeing that the details load successfully.

    IMPORTANT: This is a breaking change. All custom content marketplaces will need
    to be reviewed.

  TL-24120 Added a String.fromCodePoint polyfill in JavaScript for IE 11

  TL-24172 Increased the maximum length of database column and table names

    Maximum database column name length is now 63 characters. Maximum database table
    name length is now 48 characters.

  TL-24318 Updated TCPDF library to version 3.6.5

  TL-24602 Deprecated the 'Switch to other roles' capability

    All related functionality will be removed in Totara 14

  TL-25277 Deprecated Memcache session handler, use Memcached handler instead

  TL-25394 Upgraded PHPUnit testing framework to version 8.5

  TL-25771 Disabled report builder caching when content restrictions are enabled

    This change was required as report builder must rely upon live data when
    resolving content restrictions. Cached report data is often not in sync with
    live data and attempting to resolve relationships across the combined dataset
    can lead to errors.

    If you have an existing report configured to use both content restrictions and
    caching, the caching will no longer be used.

    Should you be using report caching in Totara 12 due to the performance of any
    report source, please inform us via the help desk so that we can review the
    report source with the aim of improving its overall performance.

  TL-26918 Added to_array function for context class

    The to_array function allows easy access to full detail of context object when
    needed. This is useful in functions such as json_encode that ignore objects with
    protected properties.

  TL-27162 Created data generation scripts to generate data for performance testing

Miscellaneous Moodle fixes
--------------------------

  TL-20490 MDL-64971: Ensure that the capability exists when fetching

  TL-22359 Backport useful accesslib improvements from Moodle 3.4-3.8: Multiple accesslib improvements merged from Moodle

    * MDL-61875 core_component: new method to get full components list
    * MDL-61441 accesslib: get_users_by_capability groups perf improvement
    * MDL-46783 permissions: let some moodle/ caps be overriden in all mods
    * MDL-63818 core: Add all relevant module context caps
    * MDL-54035 accesslib: Rewritten cache invalidation
    * MDL-62065 core_access: deprecation of get roles on exact context
    * MDL-62747 accesslib: rdef caching should handle roles with no caps
    * MDL-60043 accesslib: improve query performance by removing ORDER BY
    * MDL-59897 Accesslib: get_user_capability_course is slow

  TL-22499 Selective Moodle 3.4.9 merge: Upgrade path from Moodle 3.4.9 to Totara 13

    Moodle 3.4.9 sites can be upgraded to Totara 13.
    Bug fixes and improvements have been selectively cherry-picked. Each change
    cherry-picked is separately noted in the changelogs.

  TL-22512 Cherry pick Global search changes from 3.4.9: Multiple fixes and improvements for experimental Global search

  TL-22515 MDL-40838: New options to restore enrolment methods without users

  TL-22534 Cherry pick new file type restrictions from 3.4.9: New options to restrict uploaded file types

  TL-22536 MDL-58567: Upgrades now show time it took to run each upgrade step

  TL-22538 MDL-50011: New system wide default setting page for the multichoice question

  TL-22539 MDL-36501: New checkbox for extra credit when adding a grade item

  TL-22541 MDL-58820: Response numbering styling is now allowed in MCQs

  TL-22542 MDL-59125: nolink class is now respected by urltolink filter

  TL-22543 MDL-59427: Purge ad-hoc caches when purging all caches

  TL-22544 MDL-58851: LTI administration capability was added

  TL-22566 MDL-59323: Fixed database check for defaults of character fields

  TL-22634 MDL-57991: Improved rendering of media players

  TL-22635 MDL-59460: Added new setting for default subscription mode in forum activity

  TL-22636 MDL-59572: error_log is now used for AJAX/WS calls in DML layer

  TL-22652 MDL-59702: User identity display was fixed in lesson activity overview report

  TL-22657 MDL-59084: All ad-hoc tasks now run using the original user id by default

  TL-22662 MDL-57115: The messages block has been removed from the Totara distribution

  TL-22663 MDL-60197: All/none option is now shown only if necessary in database activity export

  TL-22668 Import latest HTMLPurifier: HTMLPurifier upgraded to latest version 4.11.0

  TL-22671 Import latest ADOdb 5.20.14: Imported ADOdb 5.20.14 library

  TL-22676 MDL-59274: Imported more recent video.js library and plugins

  TL-22677 MDL-60209: Multiple fixes and improvements in MathJax integration

  TL-22682 MDL-59844: Bearer auth method was added to WebDAV requests

  TL-22685 MDL-60268: Made location for resetting user tours explicit

  TL-22687 MDL-46269: New $casesensitive argument added to the sql_regex() method

  TL-22688 MDL-57455: Implemented tagging in database activity

  TL-22689 MDL-60211: Added new filters for category, course, and course type to user tours

  TL-22690 MDL-60116: Password reset email now contains user name

  TL-22698 MDL-52538: Fixed grade info displayed when ongoing score disabled

  TL-22701 MDL-61081: Added start and end date for courses created by external db

  TL-22702 MDL-60547: Prevented scroll jump when 'Mark all as read' is clicked in notifications drop down

  TL-22708 MDL-57456: Implemented tagging in glossary activity

  TL-22712 MDL-57742: Improved lti compatibility by making tool_consumer_instance_guid optional to match the specification

  TL-22717 MDL-31443: Improved labels on backup and restore pages

  TL-22719 MDL-57968: Improved performance by optimising new message notification

  TL-22722 MDL-62320: JSON format was added to the default mime types list

  TL-22730 MDL-61786: Test interface for external authentication settings can now be localised

  TL-22735 MDL-62325: Added some keys and indexes to enrol_paypal

  TL-22737 MDL-61296: Fixed notices when LDAP authentication is misconfigured

  TL-22740 MDL-62772: Plagiarism disclosure info is shown to all users in assignment

  TL-22743 MDL-53537: New event is triggered when course is backed up

  TL-22752 MDL-62771: Plagiarism disclosure information is always displayed on forum if enabled, not just for first post

  TL-22754 MDL-59611: Moved help icons for subplugin types out of labels in admin UI

  TL-22755 MDL-57666: Fixed display of error in Dropbox repository

  TL-22756 MDL-62750: Invalid whitespace is now stripped from the langlist setting value

  TL-22758 MDL-43334: Fixed issues with Cancel backup button

  TL-22759 MDL-62643: Empty online text files are now skipped when providing feedback

  TL-22760 MDL-61650: Made the assignment module check for empty text more consistent

  TL-22761 MDL-57741: Added workaround for LTI launch without cartridge support

  TL-22763 MDL-62867: Improved tags performance

  TL-22770 MDL-50314: Quiz reports now honour 'showuseridentity' setting when exporting

  TL-22774 MDL-58781: Improved consistency of feedback analysis export

  TL-22776 MDL-62717: Descriptions of automatically created announcement forums are now using HTML format

  TL-22782 MDL-51969: Improved compatibility of LTI module with some webservers

  TL-22784 MDL-63456: Improved Aiken question type error handling

  TL-22787 MDL-60897: Fixed handling of invalid Cloze questions

  TL-22795 MDL-62702: Improved LTI provider consistency checks

  TL-22805 MDL-57457: Implemented book tagging

  TL-22807 MDL-46929: Implemented tagging in forum activities

  TL-22818 MDL-33483: Improved file import from Google docs repository

  TL-22819 MDL-47354: Option to select number of items per page was added to Single view gradebook report

  TL-22820 MDL-42266: More options were added to the list of maximum file size settings

Contributions
-------------

  * Chris Wharton at Catalyst EU - TL-8300
  * Jamie Kramer at Elearning Experts - TL-7394
  * Jo Jones at Kineo UK - TL-19815
  * Mark Ward at Learning Pool - TL-5081
  * Russell England at Kineo USA - TL-20756
