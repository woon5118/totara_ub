<?php
/*

Totara Learn Changelog

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
