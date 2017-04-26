<?php
/*

Totara LMS Changelog

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
