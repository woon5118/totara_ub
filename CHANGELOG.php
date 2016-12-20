<?php
/*

Totara LMS Changelog

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
