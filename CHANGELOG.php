<?php
/*

Totara Learn 12.0 release
=========================

System requirement changes
--------------------------

    * Added support for PHP 7.3
    * Added an upgrade path from Moodle 3.3 to Totara 12
    * PostgreSQL minimum require version increased to 9.4
    * MSSQL now requires the Full-Text search component to be installed
    * Safari minimum supported version increased to recent versions of 11


Key:           +   Included in one or more stable releases as well

New features
------------

    TL-17902       Added HR Import for competencies

                   Competencies can now be created, updated and deleted via HR Import.

                   Each competency must reference an existing framework via its ID Number. Values for types and custom
                   fields may also be imported for each competency, providing these exist on the site that the import
                   is run on.

    TL-17752       New course, program and certification catalogue

                   Implemented a new, modern, media-rich catalogue focused on improving the user experience while
                   browsing for content.

                   The new catalogue is intended as a replacement for the 'Enhanced catalogue' which has been renamed
                   'Report-based catalogue'.

                   Improvements include:
                    * One area to search for courses, programs and certifications
                    * Ability to browse learning items by tile or list views
                    * Flexibility for administrators to configure display of different metadata
                    * Ability to show icons related to the learning item
                    * Ability to show learning item images
                    * Ability to search by tags
                    * Ability to visually highlight recommended training
                    * Search beyond title and description using tags, metadata, summary, etc
                    * Ability to share the URL of search criteria

                   Please also note the following:
                    * After upgrading cron must be run in order to populate the catalogue
                    * Search within the catalogue uses your databases full-text search capability. As such the search in each database works slightly differently.

    TL-17475       Added support for pluggable course creation workflows

                   This patch adds support for general purpose, pluggable workflows which provide an extensible way to
                   provide different workflows for a specific task.

                   The first workflow type to be implemented is the course creation workflow, which provides a way to
                   design custom workflows to collect information and generate specific types of courses.

                   See our developer documentation for more information:
                   https://help.totaralearning.com/display/DEV/Workflows

    TL-17426   +   Add Totara content marketplace and GO1 marketplace

                   Totara content marketplace provides support for browsing and importing external content from content
                   providers directly into your site.

                   Content providers can implement a new "marketplace" plugin type to integrate their content into
                   Totara Learn. The release includes a marketplace plugin for GO1 ([https://totara.go1.com/]), which
                   provides direct access to search and include GO1 aggregated content.

                   When first installed the content marketplace plugin will send an internal notification to site
                   administrators and site managers on the next cron run, letting them know that content marketplaces
                   are available. To prevent this notification and completely disable marketplaces add
                   $CFG->enablecontentmarketplaces = false; in your site's config.php *before* you upgrade your site.


Navigation improvements
-----------------------

    TL-19620       General improvements to the main menu administration

                   The issue saw the main menu code refactored in order to provide better support containers, and to
                   ensure that a smooth upgrade path exists.

                   Please note that when upgrading as the main menu no longer supports branches that are also items,
                   any menu items that were added may have been moved to an "Unused" container that is not visible on
                   the menu, in order to ensure that they are not lost.
                   We strongly recommend visiting the main menu administration page after upgrading.

    TL-19595       Replaced navigation block with course navigation blocks in each course

                   All instances of the navigation block have been removed. 'Navigation' block
                   is no longer required by theme and can be added and removed as needed.

                   To facilitate navigation between the course section new 'Course navigation'
                   block has been added to all existing courses and any new course created
                   from now on. This block behaves similarly to the 'Navigation' block, but is
                   limited in scope to the course and its activities only.

    TL-18995       Added a new block to link administrators of new sites to the Totara Community
    TL-18713       Reduced space between the main navigation and blocks when there are no breadcrumbs
    TL-18712       The site logo link now takes the user to their default home page

                   Previously when the user clicked on the site logo they were taken to the site's home page.
                   Now they are taken to their default home page, which may be the site home page, or one of their
                   dashboards.

    TL-17941       New administration menu

                   The new quick access menu is a replacement for the old Site Administration
                   menu and is customisable for each user. The menu will only be available if
                   a user has capabilities to perform one or more administration tasks.

    TL-17719       Converted front page content to use the new centre block region

                   The following blocks have been introduced for backward compatibility:
                    * Course progress report
                    * Courses and categories
                    * Course search

                   "Course progress report" and "Courses and categories" blocks are disabled by default in new
                   installations, and only enabled on upgrade if the respective front page content settings were
                   enabled.

    TL-17495       Redesigned main menu, for a more compact style with added support for a third level of links

                   Reworked the existing navigation, improving the user journey and added support for third-level links
                   which will allow us to tie all of the Totara products together.
                    * Redesigned navigation
                    * Added third-level navigation
                    * Moved logo into navigation bar
                    * Moved messages and alerts into navigation
                    * Moved language selector into navigation
                    * Moved user menu into navigation

                   The old navigation menu is now deprecated, but still available with some changes in code. See the
                   following page for details: https://help.totaralearning.com/display/DES/Totara+v12+navigation+revert

    TL-17494       Improved the work flow of adding blocks to editable regions

                   * Removed the existing "Add a block" block.
                   * Each editable region now has a dotted border, when editing is enabled.
                   * Added a "+" icon button to the centre of every block region.
                   * Clicking the "+" button opens a modal dialogue with a list of all available block types and a
                     search input.
                   * The search input provides real-time filtering of the block type list.
                   * Clicking a block name reloads the page and that block will be added to the same region.

    TL-17450       Added full width top and bottom block regions to the homepage and dashboard

                   In addition to existing block regions (side-pre, main, side-post), there are now 2 new regions (top,
                   bottom) that can show blocks as well. These new regions have already been added to the roots and
                   basis themes; if you want them in custom themes, you need to explicitly add them in.

                   Note: Just because existing blocks can be shown in these regions does not mean those blocks are
                   suited to these areas. There could be excess space or undesirable aesthetics involved. The best
                   blocks for these new regions are those that can display their information in wide columns, for
                   example tabular data, listings or banners.

    TL-17124       The main menu block is no longer added to the home page by default for new installations
    TL-16848   +   Renamed the "Site policies" side menu item in the "Security" section

                   The Security > "Site policies" side menu item has been renamed to "Security settings" to avoid
                   confusion with the new "Site policies" item when GDPR site policies are enabled.


Seminar improvements
--------------------

    TL-19184       Improved the appearance of seminar's notification form to resolve the confusion of notification's recipients

                   Prior to this patch, on a creating new seminar's notification page, the label 'All booked' within
                   the recipients section was misaligned, causing confusion.

                   After the patch, the label 'All booked' has been changed into 'All (past and present booked)'.
                   Furthermore, there is an improvement on form's UI, in which the 'Booked type' option is no longer a
                   checkbox, but a selection element instead.

    TL-18597   +   Improved the help text for the 'Notification recipients' global seminar setting

                   The setting is located under the notifications header on the site administration > seminars > global
                   settings page, the string changed was 'setting:sessionrolesnotify' within the EN language pack.
                   Full updated text is: This setting affects *minimum booking* and *minimum booking
                   cut-off* notifications. Make sure you select roles that can manage seminar events. Automated
                   warnings will be sent to all users with selected role(s) in seminar activity, course, category, or
                   system level.

    TL-18565   +   Moved 'Override user conflicts' action out of the seminar event setting page and into a 'save' modal dialog

                   The 'Override user scheduling conflicts' setting was initially intended for use with new events
                   where the assigned roles resulted in conflicts with existing events. It was not originally designed
                   to work with existing events.
                   We improved the wording to clarify this feature without further changes in the UI and workflow.

    TL-17288   +   Missing seminar notifications can now be restored by a single bulk action

                   During Totara upgrades from earlier versions to Totara Learn 9 and above, existing seminars are missing
                   the new default notification templates. There is existing functionality to restore them by visiting each
                   seminar notification one by one, which will take some time if there are a lot of seminars. This
                   patch introduces new functionality to restore any missing templates for ALL existing seminars at
                   once.

    TL-16864   +   Improved the template of seminar date/time change notifications to accommodate booked and wait-listed users

                   Clarified Seminar notification messages to specifically say that it is related to the session that
                   you are booked on, or are on the waitlist for. Also removed the iCal invitations/cancellations from
                   the templates of users on the waitlist so that there is no confusion, as previously users who were
                   on the waitlist when the date of a seminar was changed received an email saying that the session you
                   are booked on has changed along with an iCal invitation which was misleading.

    TL-16255   +   Added a "readonly" state to the Totara reserved custom fields to prevent users from changing the pre-existing seminar custom fields
    TL-15818       Refactored seminar code to allow multi-language notifications and consistent booking state processing

                   Multi-language:
                   Added support for the "Multi-Language Content" filter plugin in Seminar notifications. Notification
                   content will now be filtered according to each recipient's language settings.

                   Booking system:
                   The main target of refactoring was to bring consistency to the bookings state changes throughout all
                   related code, leading to predictable and controllable rules for each state transition. For this
                   purpose we have implemented a simplified Finite State Machine with a definition for each state,
                   following states and rules that must be matched for state transition to happen. This will greatly
                   reduce complexity during further changes to how booking states are managed.

                   Despite our efforts to maintain existing behaviour, some inconsistencies in old code forced some
                   minor changes in behaviour. We have identified the following changes:

                   1) Enable waitlist and overbooking - Previously when a Seminar's event had the setting 'Enable
                   Waitlist' enabled, then all the attendees that got signed up by an admin or any user that has
                   capability would have a status as booked. Now users will be booked until the event's room capacity
                   has been reached, the rest of the users will be added to the waitlist. Later on an admin or another
                   user with the "mod/facetoface:signupwaitlist" capability will be able to confirm users on the
                   waitlist, overbooking the event.

                   2) Events without session - Administrators could previously book users onto events without sessions
                   by confirming users on the waitlist. Now as the booked state requires a session to be set, this
                   attempt will return error until a session is created.

                   3) Action buttons labels -  Removed some inconsistencies with "Sign-up", "Join waitlist" buttons and
                   added "Request approval" when approval is required. Previously calendar and upcoming events block
                   would display a "sign-up" button, while the sign-up page would offer "Join waitlist". These
                   inconsistencies were largely removed by using the same prediction logic for all three source of
                   actions (course view, calendar, and sign-up page). Also, when approval is required, the user is now
                   properly informed that approval will be required.

                   API changes:
                   The API has been significantly changed. We have moved to a proper class structure for all Seminar
                   entities and their relationships. Along with that we didn't change the database structure, except
                   for some varchar fields that were converted to text to allow the multi-language filter to work
                   properly. We have also minimised front-end changes as much as possible. All functions that were
                   likely to be used by third-party code have been kept in the code base and deprecated. Deprecated
                   functions from main lib.php file were moved to deprecatedlib.php file (which is required by lib.php
                   file).

                   In order to reduce API changes we've deprecated mostly functions that were relevant to state machine
                   (booking states), and functions that were completely covered by OOP (e.g. rooms, assets,
                   reservations, calendar).

    TL-11243   +   Removed ambiguity from the confirmation messages for seminar booking requests
    TL-5964    +   Added settings to seminars that improve the control over multiple signups

                   This change introduces three new settings to both the settings form and the activity defaults admin
                   page for seminars. These new settings are:

                   1) How many times the user can sign-up? - This setting replaces the old 'multiple signups enabled',
                      it allows you to choose values between 1-10 or unlimited. To maintain current behaviour for existing
                      sites, they will have this set to 1 if 'multiple signups enabled' was not ticked, or unlimited if it
                      was ticked. Note: cancelled or declined sign-ups are not considered as part of this setting, neither
                      are sign-ups that have been archived by certifications.

                   2) Restrict subsequent sign-ups to - This setting restricts subsequent sign-ups to the seminar based
                      on the state of the current sign-up, the options are the attendance states 'fully attended',
                      'partially attended', and 'no show'. Selecting any of these options will restrict users to a single
                      concurrent sign-up, until the attendance has been taken for that event. Not selecting any of these
                      options will allow users to have as many concurrent sign-up as they want, up to the limit specified
                      by the setting above.

                   3) Clear expired waitlists - If enabled waitlisted sign-ups to seminar events will be cancelled by a
                      cron task after the event has begun, allowing those users to sign up for another seminar event.
                      Along with this setting there is also a new notification added to seminars, the 'Waitlisted sign-up
                      expired' notification. This can be used to inform users that their sign-up has been automatically
                      cancelled, and prompt them to go and sign-up to another event.


Report builder improvements
---------------------------

    TL-19111       Removed obsolete non-functional support for report builder report and source groups
    TL-19098       Automatic report builder data grouping was deprecated and affected report sources were rewritten to use subqueries
    TL-18639   +   Added support for custom help tooltips for Report Builder filters

                   When a report source is defined it is now possible to define a custom filter option to override the
                   default help tooltip for the given filter.

    TL-17872       Added an audience-based content restriction to all user-oriented report builder sources

                   Report builder sources that focus report on user's have a new content restriction that can be used
                   to restrict the user's appearing in the report to just those who are a member of an audience.

    TL-17353   +   Updated the description for "Minimum scheduled report frequency" in the report builder general settings
    TL-16729       Converted all report builder display functions into classes

                   All the Report Builder display functions have been deprecated and converted into display classes for
                   better control over how data is displayed and for improved performance.

                   This patch however does not introduce any changes in the current display of data within the reports.

    TL-16728       Ensured all Report Builder columns have a display class defined

                   To improve Report Builder performance, all columns now need to define a display class best suited to
                   the data type being displayed. This reduces unnecessary formatting.

                   A PHP Unit test is included to assert new columns have the 'displayfunc' option defined.
                   Run 'vendor/bin/phpunit totara_reportbuilder_display_testcase
                   totara/reportbuilder/tests/display_test.php' to find any local customisations that should be
                   updated.

    TL-16727       Moved all report builder functions that added columns, filters and joins from base source in to traits

                   All function that added columns, filters and joins have been deprecated and moved into traits within
                   the report sources associated component.

    TL-16726       Refactored Report builder initialisation

                    * Report builder constructor should no longer be used for initialising the report instances. New
                      factory methods were added to facilitate report initialisation: create() and create_embedded(). In
                      the future, Report builder constructor will be made private.
                    * New class rb_config was added to be used with the factory methods for passing report configuration
                      settings to the report initialisation. Instances of the rb_config can be shared between the reports,
                      but cannot be changed once they are finalised during the report initialisation.
                    * All described API changes are fully backwards compatible, however debugging messages will be
                      displayed when a site is running in developer mode to warn about any required changes in
                      customisations.

    TL-14966       Added a new conditional access restriction based on time since activity completion

                   Access to an activity can now be restricted based on time since completing another activity.

    TL-14939       Made it possible for report builder columns to be flagged as deprecated
    TL-13960       Moved all report builder customfield-related functions that added columns, filters, and joins from base source into traits

                   All function that added columns, filters, and joins for custom fields have been deprecated and moved
                   into traits within the report sources associated 'customfield' component.

    TL-10295   +   Added link validation for report builder rb_display functions

                   In some cases if a param value in rb_display function is empty the function
                   returns the HTML link with empty text which breaks a page's accessibility.


User data and site policy improvements
--------------------------------------

    TL-17383   +   Improved the wording and grouping of user data items
    TL-17378   +   Implemented user data item for the transaction information of the PayPal enrolment plugin

                   When the user enrols via PayPal the transaction details are sent to the IPN
                   endpoint in Totara which records the information in the enrol_paypal
                   table. The user data item takes care of purging, exporting and counting
                   this transaction information.

    TL-17374   +   Implemented user data item for course requests
    TL-17373   +   Implemented user data item for external blogs

                   This user data items takes care of the exporting and purging of external blogs. It includes all
                   external blogs created by the user, including tags assigned to it, all synced posts, and all
                   comments made on the blogs.

    TL-17362   +   Implemented user data item for portfolios

                   Implemented user data elements for portfolios. This allows the exporting and purging of user data
                   kept in relation to exporting of data to portfolios.

    TL-17354   +   Ordered all user data item groups alphabetically
    TL-17227   +   Implemented user data item for role assignments
    TL-17142   +   Enabled use of the HTML editor when creating site policy statements and added the ability to preview

                   An HTML editor is now used when adding and editing Site Policy statements and translations. A
                   preview function was also added. This enables the policy creator to view how the policy will be
                   rendered to users.

                   Anyone upgrading from an earlier version of Totara 11 who has previously added site policies and
                   wants to use html formatting will need to:
                    * Edit the policy text
                    * The text will still be displayed in a text editor, but you will have an option to change the
                      entered format
                    * Make sure you have a copy of the current text somewhere (copy/paste)
                    * Change the format to "HTML format"
                    * Save and re-open the policy OR Preview and click "Continue editing". The policy text will be
                      shown in the HTML editor but will most likely contain no formatting
                    * Replace the current (unformatted) text by pasting back in the copy of the original text
                    * Save

                   Please note that this will be considered a new version of the policy, and users will be required to
                   accept it again.

    TL-17137   +   The site policy user consent report now appears in the settings block

                   A user consent report exists for the new site policy tool, however it was never linked to from the
                   current navigation. This user consent report is now linked to from the Settings block, you can find
                   it by navigating to Security > Site policies > User consent report.

    TL-17130   +   Added consent statement filter for the Site policies report

                   This patch adds support for a consent statement filter for the Site policies report as well as a few
                   minor improvements to the site policy filters including:
                    * Removing the filter Current Version (Primary Policy)
                    * Replacing plain text version filter to a smart dropdown menu, which includes now the list of
                      available versions as well as the option to select current version of the policy
                    * Adding policy filter which allows you to filter only by policy
                    * Making user consent statement a simple filter
                    * Added custom help for consent statement filter
                    * Added custom help for policy version filter

                   Now to select the current version of the policy it is a matter of using 2 filters:
                    * Policy filter to select appropriate policy
                    * Version filter to select current version

                   Please note, that this patch will also remove Current Version (Primary Policy) filter from any saved
                   search using it.

    TL-16936   +   Implemented user data item for Competency progress

                   The competency progress item is specifically for the comp_criteria_record table; other competency
                   tables are handled by the competency status item.

    TL-16877   +   Implemented user data items for comments and HTML blocks

                   Now it is possible to purge, export and audit the data stored in the comments and HTML blocks.

                   In case of the comments block item, all comments made by users in all created comment blocks are
                   purged or exported. This affects the front page, personal dashboards and courses.

                   In case of the HTML block item, all blocks created by the users in their personal dashboards are
                   purged and exported. HTML blocks in other contexts (front page, courses) are not affected as they
                   are related to the course or the site and not personal to the user.

    TL-16840   +   Implemented user data item for user data export requests
    TL-16777   +   Implemented user data item for the Featured links block
    TL-16775   +   Implemented user data item for RSS client
    TL-16739   +   Implemented user data items for program and certification completion

                   This includes exporting and purging of program and certification assignments, completion records
                   (including completion history and logs). It also includes exceptions, program extensions and the log
                   of program messages sent to the user.

                   Users are unassigned from any program or certification regardless of the assignment type. If users
                   were assigned via audience, position or organisation it's possible that they will be reassigned
                   automatically as soon as the next scheduled task for dynamic user assignment is triggered.

    TL-16738   +   Implemented user data items for grades

                   The following user data items have been introduced:
                    * Grades - This item takes care of the Gradebook records, supporting both export and purge.
                    * Temp import - This item is a fail-safe cleanup for the tables which are used by grade import
                      script for temporary storage, supporting only purge.
                    * Improved Individual assignments item - This item includes feedback and grades awarded via
                      advanced grading (Guide and Rubric), supporting both purge and export.

    TL-16736   +   Implemented user data items for course enrolments

                   Added two user data items that allow exporting and purging:
                    * An item for course enrolment data.
                    * An item for pending enrolments that belong to the Flat file enrolment plugin.

    TL-16367   +   Implemented user data items for standard and legacy logs
    TL-16365   +   Implemented user data items for the Wiki module

                   The following user data items have been introduced:
                    * Individual wiki as a whole.
                    * Collaborative wiki files export files uploaded by the user to the collaborative wiki.
                    * Collaborative wiki comments exports\purges user's comments for collaborative wiki pages.
                    * Collaborative wiki versions exports collaborative wiki page versions submitted by the user.

    TL-16360   +   Implemented user data item for glossary entries, comments and ratings
    TL-16357   +   Implemented user data item for LTI submissions
    TL-16356   +   Implemented user data item for the database module
    TL-16350   +   Implemented user data items for appraisals

                   Added five user data items:
                    * "Appraisals" - purge all appraisal data where the user is a learner
                    * "As the learner, excluding hidden answers from other roles" - export all appraisal content that
                      the user can see as a learner
                    * "As the learner, including hidden answers from other roles" - export all appraisal content,
                      including all answers from other roles, regardless of visibility settings, where the user is the
                      learner
                    * "Participation in other users' appraisals" - export all other users' appraisals that the user is
                      currently participating in
                    * "Participation history" - export the history of participation in other users' appraisals

    TL-16349   +   Implemented user data items for Learning Plans and Evidence

                   This allows user data for Learning Plans and Evidence items to be purged and exported.

    TL-16346   +   Implemented user data items for feedback 360

                   Feedback360 has two user data items, both implementing export and purge:
                    * The user assignments item, this covers all of a user's assignments to a Feedback360 and all
                      responses to their requests.
                    * The response assignments item, this covers all of a user's responses to other user's Feedback360
                      requests.

                   It is worth noting that self evaluation responses will be included in both user data items.

    TL-16345   +   Implemented user data item for event monitor subscriptions

                   Implemented user data item for event monitor subscriptions to allow the exporting and purging of
                   user data kept in relation to event monitoring.

    TL-16344   +   Implemented user data item for the "Self-registration with approval" authentication plugin
    TL-16334   +   Implemented user data items for component and plugin user preference data

                   It is now possible to export and purge user preference data being used by all parts of the system.
                   These preferences store a range of information, all pertaining to the user, and the state of things
                   that they have interacted with on the site, or the decisions that they have made.
                   Some examples are:
                    * What user tours the user has completed, and when.
                    * The admin bookmarks that they have saved.
                    * Their preferences for the course overview block.
                    * Whether they have docked the admin and navigation blocks.
                    * Their preferred display mode for forums.
                    * What regions within a workshop activity they have collapsed.

    TL-16332   +   Implemented user data items for Audience memberships

                   Items for exporting and purging a user's audience membership has been added. This is split into two
                   items: Set audience membership and dynamic audience membership.

    TL-16327   +   Implemented user data items for report builder

                   Added items that allow exporting and purging of user-made saved searches (private and public),
                   scheduled reports, and their participation in global report restriction.


Frontend improvements
---------------------

    TL-18927   +   Totara form load deferred object now resolves after the form initialise JavaScript is called

                   Previously, the Totara form initialise code was run after the load deferred object had been
                   resolved. This meant that calls to getFormInstance(formid) would return null on load.done(), and not
                   the form that was requested.

    TL-17603       Added reusable UI grid component

                   Added a reusable UI component for displaying content in a grid format. The component includes events
                   for setting an active tile state based on user clicks.

    TL-16649       Added reusable select and region UI components

                   The new select components are:
                    * Multi select - Similar to a multiple select and can return multiple options
                    * Single select tree - Similar to a single select dropdown that allows nestable options
                    * Text search - A stylised text input field with search icon

                   These are designed for use inside the added region container which has 'clear all' functionality.
                   Initially these will be used in the new catalogue.

    TL-19264       Switched to using standardised URL querystring parameters for the multi select component
    TL-19322       Added additional UX options to the select tree component

                   Extended the select tree component to also support the following features:

                   A select tree can be provided a call to action string value (e.g. 'Please select an option...' )
                   which isn't included in the select list & doesn't provide a value. This is an alternative to the
                   default value.

                   A select option with child nodes can either be:
                    * A clickable link itself which provides a selected value
                    * A click target for expanding/collapsing child nodes which provides no selected value

    TL-19288       Increased z-index of YUI dialogs to match other dialogs
    TL-19045       Centered login panel vertically
    TL-18709       Changed font size in header navigation from 16px to 14px
    TL-18557       Added new base class for output elements that are using templates

                   Output widgets can now extend \core\output\template. Once extended they can be given directly to a
                   renderer's render method, and that renderer will render them from the template. With this approach
                   there is no need to define any render methods at all, or to implement renderers for output widgets.

    TL-17910       The single button output component now supports a "primary" state
    TL-17891   +   Changed the Change password page to use the standard page layout

                   This gives the Change password page the standard navigation and blocks

    TL-17850       Improved colour of text input placeholders in Totara forms
    TL-17835       Improved calendar popover

                   Previously this was using a YUI module. This has now been updated to use the Bootstrap popover.

    TL-17795   +   Tooltips in the "Current learning" block are now displayed when focused via the tab key
    TL-17790   +   Improved the HTML of the change password page

                   Previously the "Change password" heading was in a legend, this patch moves it to a proper HTML heading.

    TL-17580       Refactored and simplified the Flex icon AMD JavaScript module
    TL-17517   +   Improved the user interface for course import when no courses match a search term
    TL-17439       Split block configuration settings into two sections

                   The general section contains all the settings common to every block, and the new custom section
                   contains settings specific to the block type.

                   If you have any custom blocks please refer to the blocks/upgrade.txt file for more information.

    TL-17403   +   Removed calls to deprecated table() and cellpadding() functions within forum ratings and external blogs
    TL-17372       Deprecated footer navigation in the Basis theme

                   The footer menu no longer shows when using Basis as your theme (and themes that include
                   "theme/basis/layout/partials/footer.php"). The functionality that provides this has been deprecated
                   and will be removed in a future version of Totara.

                   If you would like to keep this functionality beyond Totara 12, we recommend you copy the following
                   files into a custom theme that inherits Basis:
                    * theme/basis/templates/page_footer_nav.mustache
                    * theme/basis/classes/renderer.php (2 functions that have been deprecated)
                    * theme/basis/classes/output/page_footer_nav.php
                    * theme/basis/less/totara/page-footer.less

    TL-17143       AMD modules can now be initialised using data attributes in HTML markup

                   It is now possible to initialise AMD modules using data attributes in HTML markup. This is intended
                   primarily for templates.

    TL-16918       Removed Polyfills required for IE9

                   As of Totara 10, IE9 was no longer supported. This issue removes the polyfills that enabled IE9 to
                   have the same functionality as more modern browsers.

    TL-16881       Update jQuery to 3.3.1
    TL-16797   +   Standardised the use of styling in the details of activity access restrictions

                   When some new activity access restrictions were introduced in Totara 11.0, the display of
                   restriction details in the course was not in bold like existing restrictions. This patch corrects
                   the styling.

    TL-16731       Added LESS structure to help maintain consistency with common styles
    TL-16178       Atto autosave notifications now use standardised components
    TL-16171       Improved the warning notification in the Assignments module

                   When grading and viewing an assignment, the CSS classes alert and alert-error were being used. These
                   have been removed in favour of adding a warning icon before the message.

    TL-16157   +   Improved the layout of progress bars inside the current learning block
    TL-14714       Added onchange support to radio form elements

                   Allow radio groups to use the onchange client action in the Totara forms library.

    TL-10852       Improved footer appearance to fill bottom of the page
    TL-9414   +    Required totara form Checkbox lists are validated in the browser (as opposed to a page reload)

Please note that several of the changes above will require CSS to be regenerated for themes that use LESS inheritance.


Performance improvements
------------------------

    TL-19084   +   Enrolment type column in course completion report source is now using subqueries to improve compatibility of other general columns in the same report
    TL-19053       Improved the performance of full text searches within PostgreSQL
    TL-18998   +   Improved performance of language pack installation by changing to gzip

                   Language pack installation and updates now utilise gzip instead of zip.
                   Extract of gzip files is much quicker than zip files within Totara.
                   Manual installation and updates using zip files are still supported and will continue to operate.
                   All online installations and updates will now use tgz files exclusively.

    TL-18929       Added two indexes to speed up queries accessing the block_totara_stats table

                   In quite a few places throughout the code we query the table 'block_totara_stats' using two
                   combinations of columns. In adding indexes on these column combinations query speed will be
                   improved, especially with a lot of entries in the table.

    TL-18845   +   Removed a superfluous unique index on the job_assignment.id column
    TL-18693       Fixed memory leaks in PHPUnit test by resetting properties in tearDown() method

                   Additionally this patch introduces a check in the advanced_testcase which checks after each test for
                   properties which weren't reset. It fails any test where it finds unreset instance properties to
                   prevent creating more memory leaks in the future. There is an option to disable this check if needed
                   by setting the constant PHPUNIT_DISABLE_UNRESET_PROPERTIES_CHECK in phpunit.xml.

    TL-18686       Optimised the performance of dynamic audiences

                   With this patch, the scheduled task (Dynamic Audiences update) is now sorting audiences in order of
                   their dependencies on other audiences. Audiences that depend on other audiences will be updated
                   after their dependencies updates.

                   This allows faster and more consistent propagation of audience changes (ideally in one task run).

    TL-18666       Improved AMD module loading by converting the core/first AMD module to use RequireJS bundling instead
    TL-18640   +   Updated certif_completion join to use 'UNION ALL'

                   The 'certif_completion' join in the 'rb_source_dp_certification' report source now uses 'UNION ALL',
                   previously 'UNION', which will aid performance.

    TL-18591       Added an index to the moduleinstance column of the course_completion_criteria database table
    TL-17661       Enabled missing gzip compression for uncached js files
    TL-17586   +   Greatly improved the performance of the update competencies scheduled task

                   The scheduled task to reaggregate the competencies "\totara_hierarchy\task\update_competencies_task"
                   was refactored to fix a memory leak. The scheduled task now loops through the users and loads and
                   reaggregates items per user and not in one huge query as before. This minimises impact on memory but
                   increases number of queries and runtime.

    TL-17414   +   Improved information around the 'completions archive' functionality

                   It now explicitly expresses that completion data will be permanently deleted and mentions that the
                   data that will be archived is limited to: id, courseid, userid, timecompleted, and grade. It also
                   mentions that this information will be available in the learner's Record of Learning.


Developer improvements
----------------------

    TL-18985   +   Unit tests may now override lang strings
    TL-18909   +   Fixed compatibility issues with PHP 7.3
    TL-18777   +   Allowed plugins to have custom plugininfo class instead of just type class
    TL-17877       Regenerate lintignore files: Regenerated ignore files for linters
    TL-17746       Removed minified AMD modules with no Source files

                   The following minified AMD JavaScript modules were removed as they are not used and have no source files:
                    * 'block_totara_featured_links/course_dialog'
                    * 'block_totara_featured_links/icon_picker'
                    * 'totara_form/form_clientaction_autosubmit'

    TL-17668       Added support for full text searching

                   This improvement saw the introduction of the following full text search features:
                   * Full text search indexes can now be added to fields within the Totara database.
                   * Full text searches can now be run on these indexes.

                   This functionality is used by the new catalog to provide better searching.

                   To get the best possible result from full text searches, sites should set the full text search
                   language that will be used in the creation of indexes within their sites config.php file. For more
                   information on how to do this, please refer to the config-dist.php file provided with Totara. All
                   information is under the "FULL TEXT SEARCH" heading.

                   Technical documentation for developers can be found at
                   https://help.totaralearning.com/display/DEV/Full+text+search
                   For those intending to add full text search to their plugins and customisations, we recommend that
                   you read and follow the instructions in the technical documentation. Most importantly always define
                   a new table to use for full text searching, have a cron routine that ensures it is kept up to date,
                   and use event observers to keep it up to date with live changes.

    TL-17384   +   composer.json now includes PHP version and extension requirements
    TL-17347       Code related to previously disabled $CFG->loginhttps setting was removed and public API was deprecated
    TL-17357   +   Unsupported symlinks are now ignored in phpunit tests
    TL-17352   +   PHPUnit and Behat do not show composer suggestions any more to minimise developer confusion
    TL-17268   +   Upgraded Node.js requirements to v8 LTS
    TL-16912       Added JavaScript polyfill in IE11 to support basic ECMAScript 6 functionality

                   For more information please refer to our developer documentation
                   https://help.totaralearning.com/display/DEV/ES+6+functionality

    TL-6630    +   Added functionality to perform capability checks directly against the database

                   A new get_has_capability_sql() function has been introduced that returns an SQL snippet to resolve
                   capability checks against the database.
                   Among other uses this allows Totara to resolve visibility state much more efficiently than before
                   without sacrificing accuracy.

                   As part of this change a new table containing flattened context data will be created and
                   maintained.
                   There are a couple of important things to note about this:

                   During upgrade to this release the table will be created and populated. This upgrade step could take
                   several minutes on large sites.
                   The table is kept up-to-date automatically by the access API. If you have third party plugins or
                   customisations that are directly manipulating access data then you will need to review these.
                   We have extensively tested the performance of this change during our QA process and are confident
                   with the results. If you experience any problems please let us know immediately.


Platform improvements
---------------------

    TL-19476       Added custom field 'created' and 'updated' events

                   These new events are also observed by the new catalogue in order to update the search indexes when
                   new fields are added, or existing fields are updated.

    TL-19066       Database table context_temp is now a real temporary table

                   The original context_temp table has now been dropped.
                   This table was only ever intended as an internal store, and should not have been used by anything
                   other than the access API.

    TL-18983   +   Added workaround for missing support for PDF embedding on iOS devices

                   Web browsers on iOS devices have very limited support for embedding PDF files – for example, only
                   the first page is displayed and users cannot scroll to next page. A new workaround was added to PDF
                   embedding in File resource to allow iPhone and iPad users to open a PDF in full-screen mode after
                   clicking on an embedded PDF.

    TL-18921       Removed the Memcache cache store from core

                   Not to be confused with the Memcached cache store.
                   The Memcache PHP extension is not compatible with PHP7, and as such the Memcache cache store could
                   not be used.
                   It has now been removed from core.

                   If you are currently using the Memcache cache store and plan to upgrade in future, this may be an issue.

    TL-18852   +   Database table prefix is now required for all new installations

                   Previously MySQL did not require database prefix to be set in config.php, since MySQL 8.0 the prefix
                   is however required. To prevent problems in future upgrades Totara now requires table prefix for all
                   databases.

    TL-18722       Added critical notifications type, which go into their own section above the navbar
    TL-18626       Moodle: De-moodle strings: Replaced some Moodle strings with Totara equivalents
    TL-18554       Introduced common block settings and API to manage those

                   The idea of the common block settings API is to allow core developers to have predictable common
                   settings storage for all the blocks and if necessary,  introduce properties which cover all block
                   types without interfering with settings provided the by third-party block developers.
                   It also includes a few minor changes for block configuration: hiding, docking and show header/border
                   settings now use checkboxes instead of radio buttons. Moreover, to provide better backwards
                   compatibility a setting "Override default block title" has been introduced and unless it is checked
                   the block retains pre-patch behaviour for the title supplied by the block developer.

    TL-17905       Updated the default value for the 'docroot' setting

                   Previously, error pages included a link to Moodle documentation, which often didn't exist for
                   Totara-specific errors. This change removes the default documentation root so the 'More information
                   about this error' link is no longer shown.

                   If you wish to restore the links, set the docroot back to
                   http://docs.moodle.org after upgrading.

    TL-17738   +   Changed data-vocabulary.org URL in metadata to be https

                   This URL is used to provide extra information for navigation breadcrumbs to search engines when your
                   site is indexed.

    TL-17280   +   Improved compatibility for browsers with disabled HTTP referrers
    TL-17214       InnoDB upgrade tool and deprecated authentication plugins were removed from distribution

                   The following authentication plugins were removed:
                    # auth_fc
                    # auth_imap
                    # auth_nntp
                    # auth_none
                    # auth_pam
                    # auth_pop3

                   The following upgrade tool was removed: tool_innodb

    TL-17024   +   Added detection of pending upgrades to admin settings related pages
    TL-16958   +   Updated language strings to replace outdated references to system roles

                   This issue is a follow up to TL-16582 with further updates to language strings to ensure any
                   outdated references to systems roles are corrected and consistent, in particular changing student to
                   learner and teacher to trainer.

    TL-16582   +   Updated language contextual help strings to use terminology consistent with the rest of Totara

                   This change updates the contextual help information displayed against form labels. For example this
                   includes references to System roles, such as student and teacher, have been replaced with learner
                   and trainer.

                   In addition, HTML mark-up has been removed in the affected strings and replaced with Markdown.

    TL-15739   +   Imported HTMLPurifier library v4.10.0
    TL-14282   +   Imported ADOdb library v5.20.12


Miscellaneous improvements
--------------------------

    TL-19145   +   Improved terminology for non-graded assignment strings
    TL-19014   +   Implemented new capabilities for controlling the access to SCORM content

                   Previously all users who could enter a course were able to launch SCORM
                   activities.
                   The only way to limit access was to make the activity hidden and then to
                   use the moodle/course:viewhiddenactivities capability to grant access.

                   Two new capabilities have been added to allow better control of access to
                   SCORM activities.
                    * mod/scorm:view
                    * mod/scorm:launch

    TL-19002       Changed the legacy programs/certifications catalogue UI to be consistent with course catalogue as a model

                   Changes are made for the legacy programs/certifications catalogue UI (it uses one base code) to be
                   consistent with course catalogue as a model when enhanced catalogue is disabled
                    # Search box is moved to the top-left of the catalogue page
                    # Added 16px margin-bottom space for the top-left search box
                    # Search box label is removed
                    # The "Add new program/certification" button is moved to center of the page
                    # Course/program/certification titles font is changed from H3 to standard font
                    # Programs/certifications dropdown box with the categories/sub-categories options is moved to the right of the page
                    # Fixed program/certifications breadcrumbs
                    # Fixed if program has any associated overview files
                    # Fixed behat test after new UI applied

    TL-18978       Improved the validation display for dynamic audience rules that use a date selector
    TL-18963   +   Improved the help text for the 'Enable messaging system' setting on the advanced settings page
    TL-18896       Date pickers in forms now use the same order of day, month and year fields as current language full date and time display format
    TL-18840       Added a new dynamic audience rule for user's certification completion date
    TL-18793   +   Improved display of course details in the course and categories management page
    TL-18770   +   Disabled the site policy translation interface language selector when only a single language is available
    TL-18757   +   Send notifications to new appraisees for an already activated appraisal

                   Previously the appraisals module only sent out notifications to learners when the appraisal was activated.
                   If new learners were added to the appraisal after activation, they did not receive any notification.

                   With this patch, notifications are sent out when new learners are added to the appraisal after activation.

    TL-18718       Added upgrade step to set new redis cache store settings 'test_password' and 'test_serializer' to default values when not already set

                   In a previous patch new settings 'test_password' and 'test_serializer' for the Redis Cache Store
                   were introduced. If the site hasn't already been upgraded to a version which includes these settings
                   we set the password to an empty string and the serializer to PHP's default value to ensure that
                   previous functionality works as before. These settings can still be changed in the appropriate
                   section of the Site Administration.

    TL-18697       Totara Connect login error handling was improved and diagnostic logging was added
    TL-18675   +   Added 'not applicable' text to visibility column names when audience-based visibility is enabled

                   When audience based visibility is enabled it takes priority over other types of visibility. Having
                   multiple visibility columns added to a report may cause confusion as to which type of visibility is
                   being used. '(not applicable)' is now suffixed to the visibility column to clarify which type of
                   visibility is inactive, e.g. 'Program Visible (not applicable)'.

    TL-18646       HR Import allows HTML tags for fields where this is permitted

                   Fields such as descriptions or text area custom fields allow HTML tags when a value is added via the
                   interface. However, HR Import was stripping these tags. Cleaning of these fields is now the same
                   whether values are added via the interface or HR Import, i.e. they retain their HTML tags.

    TL-18601       Added 'type ID number' column to the 'Manage types' hierarchy tables to allow administrators to have one place to go to to identify the available typeidnumbers
    TL-18600       Import of custom field values allows for duplicate shortnames

                   When using HR Import to create and update positions or organisations, custom field short names had
                   to be unique across the site, despite the only restriction in the UI being that they are unique
                   within a given type. HR Import now accounts for this configuration when importing custom fields for
                   hierarchies, such as position and organisation.

    TL-18596   +   Added a filter for the Number of Job Assignments for a user

                   A filter has been added for the Number of Job Assignments column and is available in all report
                   sources that include the Job Assignments filters. This filter adds a way to filter users that have
                   no Job Assignments.

    TL-18575       A limitation of 255 characters is now consistently applied when validating course shortname

                   The course shortname field in the database has always been 255 characters.
                   However the course creation form arbitrarily limited course shortname length to 100 characters.
                   As of this change the course shortname form now checks that the user-entered value is no longer than
                   255 characters, matching the database limitation.

    TL-18481   +   Improved the help strings for the 'Minimum time required' field within a program or certification course set

                   Program and certification 'Course set due' and 'Course set overdue' message help strings have also
                   been updated to convey that the 'Minimum time required' field is used to determine when a course set
                   is due.

    TL-17974       Site-wide settings for HR Import can now be overridden by element

                   The HR Import page for 'General settings' has been renamed to 'Default settings'. This page includes
                   the same settings as previously, but will also list which elements are using a given setting area.

                   Element setting pages now contain settings relating to file access, notifications and scheduling.
                   These settings allow you to select the default settings to apply or to override them with values
                   that will apply to that element.

                   Following the upgrade, values from 'General settings' will remain unchanged in the 'Default
                   settings' page. Any enabled elements will use the default settings until changed.

    TL-17920   +   Added support for the 'coursetype' field in the 'upload courses' tool

                   The 'coursetype' field will now accept either a string or an integer value from the map below:
                    * 0 => elearning
                    * 1 => blended
                    * 2 => facetoface

                   Within the 'upload courses' CSV file, the value for the 'coursetype' field can be either an integer
                   value or a string value. If the value of 'coursetype' was not within the expected range of values
                   (as above), then the system will throw an error message when attempting to upload the course(s) or
                   while previewing the course(s).

                   If the field is missing from the CSV file or the value is empty, then the 'coursetype' will be set
                   to 'E-learning' by default. This is consistent with previous behaviour.

    TL-17901       Hierarchy export improvements

                   Hierarchy export has been improved as follows:
                    * Competency items can now be exported in the same manner as any other type of hierarchy
                    * The default export file format has been changed. By default the file will now contain all item
                      data allowing it to be used for re-import via HR Import.
                      To revert back to the old hierarchical format (not suitable for HR Import), add the following line
                      to config.php:
                          _$CFG->hierarchylegacyexport = 0;_
                    * An option has been added to the Manage _<hierarchy>_ pages allowing the user to export all items
                      in all frameworks to a single file

    TL-17780   +   Added a warning message about certification changes not affecting users until they re-certify
    TL-17720   +   Added 'audience visible' default course option to the upload course tool
    TL-17626   +   Prevented report managers from seeing performance management data without specific capabilities

                   Site managers will no longer have access to the following report columns as a default:

                   Appraisal Answers: Learner's Answers, Learner's Rating Answers, Learner's Score, Manager's
                   Answers, Manager's Rating Answers, Manager's Score, Manager's Manager Answers, Manager's Manager
                   Rating Answers, Manager's Manager Score, Appraiser's Answers, Appraiser's Rating Answers,
                   Appraiser's Score, All Roles' Answers, All Roles' Rating Answers, All Roles' Score.

                   Goals: Goal Name, Goal Description

                   This has been implemented to ensure site managers cannot access users' performance-related personal
                   data. To give site managers access to this data the role must be updated with the following
                   permissions:
                   * totara/appraisal:viewallappraisals
                   * totara/hierarchy:viewallgoals

    TL-17613   +   Added a hook to the last course accessed block to allow extra data to be passed to template

                   This enables extra data to be passed through to the Last Course Accessed block template so that the
                   display can be more easily modified without changing core code.

    TL-17611   +   Added a hook to the last course accessed block to allow courses to be excluded from being displayed

                   This hook allows specified courses to be excluded from being displayed in the Last Course Accessed
                   block. If the most recently accessed course is excluded then the next most recently accessed course
                   is displayed.

    TL-17390   +   Enabled the "Force users to log in to view user pictures" setting by default for new installations to improve privacy
    TL-17261       Multiple improvements in the authentication plugins

                   * Authentication plugins are now required to use new settings.php for plugin configuration.
                   * CLI sync scripts were converted to scheduled tasks.
                   * External Database authentication supports PDO.
                   * Shibboleth user may change their passwords.

    TL-17232   +   Made the "Self-registration with approval" authentication type use the standard notification system

                   The "Self-registration with approval" authentication plugin is now using standard notifications
                   instead of alerts, for "unconfirmed request" and "confirmed request awaiting approval" messages. A
                   new notification was also added for "automatically approved request" messages when the "require
                   approval" setting is disabled.

    TL-17170   +   Included hidden items while updating the sort order of programs and certifications
    TL-17149   +   Fixed undefined index for the 'Audience visibility' column in report builder when there is no course present
    TL-16921   +   Converted utc10 Totara form field to use the same date picker that the date time field uses

                   This only affects desktop browsers

    TL-16914   +   Added contextual details to the notification about broken audience rules

                   Additional information about broken rules and rule sets are added to email notifications. This
                   information is similar to what is displayed on audiences "Overview" and "Rule Sets" tabs and
                   contains the broken audience name, the rule set with broken rule, and the internal name of the
                   broken rule.

                   This will be helpful to investigate the cause of the notifications if a rule was fixed before
                   administrator visited the audience pages.

    TL-16909   +   Increased the limit for the defaultid column in hierarchy scale database tables

                   Previously the defaultid column in the comp_scale and goal_scale tables was a smallint, however the
                   column contained the id of a corresponding <type>_scale_values record which was a bigint. It is
                   highly unlikely anyone has encountered this limit, unless there are more than 32,000 scale values on
                   your site, however the defaultid column has been updated to remove any possibility of a conflict.

    TL-16893       Removed unused content options from the program report source

                   The program report source's "Hide currently unavailable content" setting had no effect and has been
                   removed. The code governing the setting has also been deprecated. The functionality it previously
                   offered is already provided by the Report Builder's visibility controls and capabilities relating to
                   this.

    TL-16150       Added image for course and program tiles in featured links
    TL-16149       Added the ability to have images associated with courses, programs and certifications

                   This improvement saw three notable changes made:

                   1) An image can now be set for courses, programs, and certifications via their respective settings pages.
                   2) An out of the box default image has been added for courses, programs, and certifications.
                   3) The default image for courses, programs, and certifications can be overridden by an admin.

    TL-16143       Added more configuration options to the Gallery Tile in the Featured Links block

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

                   The switcher.js JavaScript that changes the gallery tile has been rewritten to use the 3rd party
                   library Slick. This caused large changes to the structure of the html as Slick added a number of
                   elements.

    TL-16140       Added the ability for gallery tiles in the featured links block to contain other tiles

                   Gallery tile content is now based on other tiles rather than a set of images. Each tile in a gallery
                   tile still has all the normal configuration and visibility associated with it, along with an
                   additional meta tile interface for any tile that can contain other tiles. This is so that meta tiles
                   can define that they cannot contain other meta tiles. There is a new database column for parentid
                   added to the block_totara_featured_links_tiles table, this remembers the relationship between the
                   gallery tile and sub tiles.

                   Note: If there are any custom tiles based on the gallery tile then there is a high probability that
                   they will no longer work as they used to, as the templates and structure has changed.

    TL-16139       Added the ability to add icons into static tiles in the featured links block

                   In the edit content form of a featured links block, there is now an option to select an icon that
                   will show in the background at various sizes. The available icons are all from the themes that have
                   been installed.

    TL-14114   +   Added support for Google ReCaptcha v2

                   Google deprecated reCAPTCHA V1 in May 2016 and it will not work for newer
                   sites. reCAPTCHA v1 is no longer supported by Google and continued
                   functionality can not be guaranteed.

    TL-13987   +   Improved approval request messages sent to managers for Learning Plans

                   Prior to this fix if a user requested approval for a learning plan then a message was sent to the
                   user's manager with a link to approve the request, regardless of whether the manager actually had
                   permission to view or approve the request. This fix sends more appropriate messages depending on the
                   view and approve settings in the learning plan template.

    TL-12955       Added a dynamic audience rule for user's authentication method
    TL-12620   +   Automated the selection of job assignments upon a users assignment to an appraisal when possible

                   When an appraisal is activated or when learners are dynamically or manually added to an active
                   appraisal, a learner's job assignment is now automatically linked to their appraisal assignment.
                   Before this change, the learner had to open the appraisal for this to happen.

                   This will only come into effect if the setting "Allow multiple job assignments" is turned OFF.

                   If a user has multiple job assignments, this automatic assignment will not apply. If a user has no
                   job assignment, an empty job assignment will still be automatically created.

    TL-12393   +   Added new system role filter for reports using standard user filters
    TL-12253       Removed completionstartonenrol setting from course settings screen
    TL-10651       HR Import now handles empty fields consistently

                   Empty fields being imported into HR Import were inconsistently handled across field types, sources
                   and elements. This makes changes to introduce consistency so if a field is left empty in the CSV or
                   database then it will delete the existing data (except if the "Empty string behaviour in CSV"
                   setting is set to "Empty strings are ignored").

                   The main change in behaviour is with empty fields when custom fields are included in the import.
                   Prior to this patch custom fields would sometimes not be erased when an empty field was imported.
                   These should now be erased correctly (for CSV this is only when "Empty strings erase existing data"
                   is set).

    TL-8092    +   Added a 'Date Completed' filter to the program overview report source
    TL-7918    +   Added a new dynamic audience rule for user's certification status
    TL-6152    +   Added an RPL note column to the course completion report source

                   A new column "RPL note" has been added to the Course completion report source.
                   This column contains the note provided when users were manually awarded an RPL completion.
                   If it is not an RPL completion, or if no note was provided then the column will be empty.
                   The new column was added to the course completion report source only.

    TL-4186    +   Improved the calculation and display of program and certification progress

                   The calculation of a user's progress towards completion of a program or certification has been
                   improved to take progress of all involved courses into consideration. This progress is now
                   displayed as a true percentage in a progress bar.


Bug fixes
---------

    TL-19682       Fixed populating the default values when editing an existing default tile in featured link gallery
    TL-19673       Fixed an error preventing the creation of course tiles within a featured links block

                   Prior to this patch: when user was adding a new course tile to a gallery featured link, there would
                   be an exception thrown, due to function not found.

                   With this patch: given the same scenario, user will be able to add a new course tile into a gallery
                   featured link.

    TL-19625       Fixed an error when previewing an appraisal

                   Prior to this patch: when user previewed an appraisal, the system will throw a warning message
                   stating that the data was not populated properly (it only happened if $CFG->debug is being set to E_ALL)

                   With this patch, given the same scenario, the data is being populated with the default value and
                   system will not throw any warnings.

    TL-19617       Fixed display failure message on sign up page when user is trying to book for a session that is in a past
    TL-19606       Fixed scalability of add block popover with browser minimum fonts

                   Fixed the add block pop-over to display it's content correctly when a reasonable browser minimum
                   font size has been set.

    TL-19600       Improved the display of the certification due soon message
    TL-19562       Fixed theme style overrides on admin navigation menu

                   The theme style overrides are now consistent on both the top level navigation & the admin expanded menu.

    TL-19350       Fixed an issue with hierarchy field mapping in HR Import
    TL-19334       Removed unused coursetagging admin setting

                   Course tagging has been controlled since the general enable tags setting as of Totara 9.0.
                   The setting was missed in the clean up and remained in the product but did nothing.
                   It has now been removed.

    TL-19325       Fixed enabling/disabling antivirus plugins
    TL-19311       Added event's observers for course restoring to update the course format

                   Prior to this patch, when restoring the course, there is no action on
                   updating the course's activities base on its format.

                   After this patch, the course's activities will be updated, via the event's
                   observer

    TL-19302       Navigation on audiences pages is now consistent across them all

                   Multilang support was fixed on all pages at the same time.

    TL-19157       Removed popper.js source map path

                   The popper.js library included a path to a non-existent source map which caused a warning message in
                   the browser console.

    TL-19129       Reduced space between Totara menu & page content
    TL-19043       Fixed php undefined property notice in assignment grading when changing 'Enrolment ends' to a date in the past
    TL-19026       Changed the date format of seminar report builder Dates and Times related columns report source

                   Previously the report columns 'Event created', 'Last Updated', 'Sign-up Period', 'Sign-up Start
                   Date', 'Sign-up End Date', 'Cancellation date', 'Time of sign-up', 'Event Start time', 'Event finish
                   time' and 'Approval time' were formatted differently than the 'Session Start' and 'Session Finish'
                   columns. These columns are now formatted consistently.

    TL-18904       Fixed up the context level of the totara/contentmarketplace:add capability

                   It now shares the same configuration as the moodle/course:create capability.

                   Coding style within the component and single plugin was tidied up at the same time.

    TL-18746       Fixed performance by removing multiple course_in_progress event triggers

                   Performance is improved by removing multiple course_in_progress event triggers when activity or
                   course completion is triggered.

                   Event \core\event\course_in_progress was triggered every time when
                   completion_completion::mark_in_progress() was called. Now this event is triggered only once per user
                   enrolment (when timestarted is not yet set). This is a change in behaviour since events will not be
                   triggered anymore. This behaviour will affect sites that have callbacks assuming that
                   course_in_progress will be fired each time when mark_in_progress is called.

    TL-18727       Fixed galleries in the featured links block not being reinstated after update
    TL-18706       Fixed the incompatible version message shown when attempting to restore an old backup

                   The "This backup file has been created with Totara ..." error message was incorrectly referring to
                   Moodle version instated of Totara version

    TL-18615       Removed duplicated options in the 'Show with backdrop' selector on the add new step form in user tours
    TL-18569       Removed 'export to portfolio' links from assignment grading interfaces

                   The 'export to portfolio' functionality is designed for a user to export their own assignment
                   submissions to their portfolio. The link was being shown to trainers in the grading interface but
                   displayed an error if it was clicked.

    TL-17919       Fixed the display of the main region in core themes
    TL-17852       onchange Totara form actions now support comparing against arrays
    TL-17725       Fixed display issue when selecting a course icon

                   When selecting a course icon, if the last icon in a row was selected, the first icon in the
                   following row previously appeared directly below the selected icon.

    TL-17652       Removed 'Update activities' checkbox from seminar notification template form when new customer notification template is added
    TL-17645       Mustache esc helper now supports full mustache syntax
    TL-17632       Ensured that recursion in mustache helpers is prevented when debugging is off
    TL-17417       Fixed an issue with links not being generated correctly within the totara_message component

                   This was primarily an issue with the "more details" link in messages sent when commenting on a
                   user's learning plan.

    TL-14015       Deprecated unused totara/core/js/goal.item.js file

Upstream improvements from Moodle
---------------------------------

    TL-19399       MDL-62497: Protect against QuickForm remote code execution

                   This vulnerability had already been fixed in a previous Totara patch (see TL-18491 from previous
                   releases of Totara).

                   An additional fix was added from this set of Moodle fixes which ensures that the Feedback module
                   uses the QuickForm API correctly and safely, making sure that type checking of values is done as
                   specified.

    TL-19396       MDL-62880: Dropped support for legacy question import format
    TL-19392       MDL-63101: Improved accuracy of cache event invalidation
    TL-19387       MDL-63050: Made session check compatible with Redis 4.0

    TL-18944       MDL-53848: Added hideIf functionality to Moodle forms

                   Elements can now be hidden based on the value of another element. Usage matches that of the
                   disabledIf functionality that was already available in the Moodle forms.

    TL-18662       MDL-62210: Improved validation when exporting assignments to portfolio
    TL-18661       MDL-62232: Improved validation when exporting forum attachments to portfolio

                   Validation has been added in a previous Totara patch. This aligns it with Moodle's solution for compatibility.

    TL-18660       MDL-62233: Added validation on callback class when exporting to portfolio

                   Validation had been applied to the callback class in a previous Totara patch. This adds the Moodle
                   solution for compatibility.

    TL-18656       MDL-62790: Added capability check in core_course_get_categories for Web Service
    TL-18655       MDL-62820: Made sure questions text is properly encoded before display after question bank import
    TL-18539       MDL-62200: Prevented modals from adding another backdrop when being loaded in from another modal
    TL-18469       MDL-60793: Fixed compatibility issue with MySQL 8

                   The chat module used a database field where the name is a reserved word in
                   MySQL 8. This could have caused errors during some database operations. The
                   field has been renamed.

    TL-18301       MDL-61905: Removed unused Workshop tables from database

                   A number of tables that were used by the Workshop module in versions 1.1 and earlier have been kept
                   but unused since upgrading to version 2.0. Those tables were suffixed with '_old'.

                   If your installation was originally a Moodle or Totara version 1.x, we recommend confirming whether
                   these tables may contain data that should be kept before upgrading as these tables will be dropped.

    TL-18298       MDL-61309: Implemented a new deleted flag for forum posts and adapted userdata purging to use it

                   A new 'deleted' column for forum posts was introduced. Now deleted posts and discussions display a
                   placeholder instead of the original text. Purging of user data was modified to set the new deleted
                   flag and empty the title, and body, of the forum posts and discussions. Previously the title and
                   body were replaced by a placeholder instead of dynamically showing it.

    TL-18270       MDL-59453: Fixed filtering of lesson content in external functions
    TL-18267       MDL-59649: Fixed type of content exporter field to the correct value
    TL-18266       MDL-59627: Fixed data_search_entries function in the database module wasn't calculating total count correctly
    TL-18265       MDL-59619: Fixed get_fields Web Services not working properly if database has no fields
    TL-18260       MDL-59532: Fixed check_update callback failing when the activity uses separated groups
    TL-18252       MDL-59820: Removed unnecessary CSS class on calendar

                   The course selector now uses the standard HTML/CSS as used by other single
                   selects.

    TL-18240       MDL-60485: Fixed being able to change grade types when grades already exist
    TL-18233       MDL-60104: Fixed SCORM description text to no longer extend outside the page
    TL-18231       MDL-60433: Fixed users being able to view all groups even if they were not allowed to
    TL-18229       MDL-60789: Added length validation rule for a workshop title submission
    TL-18228       MDL-60741: Refactored admin purge caches page to call admin_externalpage_setup first
    TL-18227       MDL-60693: Added multilang filter to activity titles in course backup and restore
    TL-18226       MDL-60675: Fixed an exception in single selects without a default value
    TL-18224       MDL-59876: Fixed the Web Service user preference name field type
    TL-18222       MDL-60810: Removed string referencing PostNuke from auth/db
    TL-18221       MDL-60809: Fixed missing filelib include in XML-RPC function
    TL-18220       MDL-60773: Added pendingJS checks for autocomplete interactions
    TL-18219       MDL-60637: Removed unnecessary group id number validation on Web Services
    TL-18216       MDL-60253: Ensured both LTI ToolURL and SecureToolURL are used for automatic matching
    TL-18215       MDL-60187: Ensured grade items are not created when grades are disabled

                   When editing LTI titles inline, it makes it appear in the Gradebook even if the privacy option
                   'Accept grades from the tool' is disabled.

    TL-18213       MDL-58817: Ensured LTI icons are not overwritten by cartridge params
    TL-18212       MDL-56253: Added multilang support to course module name in grades interface
    TL-18211       MDL-55808: Fixed glossary entries search not working with ratings enabled
    TL-18210       MDL-27886: Fixed handling of course backup settings and dependencies

                   The dependency of backup settings was not working properly. If a default setting was disabled (not
                   locked) then the dependent settings in the backup were locked and could not be changed as expected.
                   The check for locked dependencies has been changed to fix this.

    TL-18208       MDL-60838: Fixed Solr files upload to honour timeout restrictions
    TL-18207       MDL-60738: Fixed Web Service theme and language parameters not being cleaned properly
    TL-18206       MDL-60669: Fixed duplicate entry issue when restoring forum subscriptions
    TL-18205       MDL-60591: Fixed forum inbound processor discarding the inline images if a message contains quoted text
    TL-18204       MDL-60249: Ensured feedback comments text area is resizeable
    TL-18203       MDL-60188: Implemented cache for user's groups and groupings
    TL-18201       MDL-57569: Fixed a large badge image being unaccessible for the future use
    TL-18199       MDL-46768: Loosened the restriction on the badge name filter to allow quotes
    TL-18198       MDL-45068: Improved group import code, prevented PHP displaying notices and warning for certain CSV files
    TL-18197       MDL-27230: Ensured that changes to Quiz group overrides are reflected in the calendar
    TL-18196       MDL-24678: Fixed a race condition in the chat activities leading to multiple messages being returned as the latest message
    TL-18192       MDL-60801: User defaults are now applied when uploading new users
    TL-18191       MDL-60443: Improved validation error message when a requested data format does not exist
    TL-18190       MDL-60219: The 'no blocks' setting in an LTI activity now uses the 'incourse' page layout with blocks disabled
    TL-18188       MDL-37757: Added missing clean up external files on removal of a repository
    TL-18187       MDL-34161: Fixed LTI backup and restore to support course and site tools and submissions
    TL-18181       MDL-60945: Stopped unneeded completion data being retrieved in Web Service function
    TL-18178       MDL-59866: Added retries for connecting to Redis in the session handler before failing
    TL-18174       MDL-56864: Fixed removal of tags if usage of standard tags is set to force
    TL-18171       MDL-54021: Fixed an issue where "Course completion status" block didn't show activity name in correct language
    TL-18169       MDL-45500: Enabled ability to uninstall grading plugins
    TL-18168       MDL-44667: Fixed minor field existence checks in three plugins

                   The following three plugins each had one call to a database function that was attempting to validate
                   the existence of the field incorrectly. The affected plugins were:
                   * Assignment file submission
                   * Assignment online text submission
                   * Multi-answer question type

    TL-18166       MDL-40790: Fixed Lesson content button to no longer run off the edge of the page
    TL-18165       MDL-61045: Made sure the 'After the quiz is closed' review option is disabled if the quiz does not have a close date
    TL-18164       MDL-61042: Fixed undefined variable error when viewing detailed statistics report on empty lesson
    TL-18163       MDL-61040: Improved spacing around the "Remove my choice" link within a choice activity
    TL-18162       MDL-61022: Added acceptance test for user groups restore functionality
    TL-18161       MDL-60938: Fixed the rendering of users in the choice activity responses table
    TL-18160       MDL-60767: Fixed a visual bug causing validation errors to not be shown when saving changes to several admin settings in a single action
    TL-18159       MDL-60653: Fixed the incorrect indentation of navigation nodes when their identifier happened to be an integer
    TL-18156       MDL-60161: Ensured that OAuth curl headers are only ever sent once
    TL-18155       MDL-59999: Added a status column to the Essay question grading interface within Lesson
    TL-18154       MDL-59709: Fixed export to portfolio button in assignment grading interface for Online Text submissions
    TL-18153       MDL-59200: Fixed an issue where a user is unable to enter assignment feedback after grade override

                   Fixes an issue where a user would be unable to enter assignment feedback after grade override and if
                   there was no original assignment grade set.

    TL-18152       MDL-58888: Added sort-order for choice_get_my_response() results by optionid
    TL-18150       MDL-57431: Shuffle question help icon in Quiz is now outside the HTML label
    TL-18149       MDL-54967: Fixed IMS Common Cartridge import incorrectly decoded html entities in URLs
    TL-18148       MDL-52100: Fixed filearea to not delete files uploaded by users without file size restrictions
    TL-18147       MDL-49995: Fixed overwriting of files to not leave orphaned files in the system
    TL-18146       MDL-42676: Fixed issue that prevented assignment submissions when grade override was used
    TL-18145       MDL-34389: Fixed users with capability 'moodle/course:changecategory' were able to only select current course category and not its subcategories
    TL-18144       MDL-31521: Fixed calculated questions were displaying a warning when more than one unit with multiplier equal to 1
    TL-18143       MDL-60942: Fixed format_string doesn't account for filter in static cache key
    TL-18139       MDL-58983: Fixed display of grade button in assignments when user doesn't have capability

                   The "grade" button is now hidden if a user doesn't have the capability to grade assignments.

    TL-18138       MDL-51089: Improved accessibility when accessing the 'add question' action menu
    TL-18137       MDL-43827: Improved accessibility when editing uploaded files on the server
    TL-18136       MDL-33886: Added graceful error handling when backup filename is too long
    TL-18135       MDL-61107: Made sure invalid maximum grade input is handled correctly in quiz activity
    TL-18134       MDL-57727: Fixed Activity completion report to have a default sort order
    TL-18132       MDL-23887: Replaced deprecated System Tables calls to System Views calls in sql generator for MSSQL
    TL-18130       MDL-61098: Fixed trainers ability to edit or delete WebDav repositories that they have created at a course level
    TL-18129       MDL-61068: Changed rounding for timed forum posts to the nearest 60 seconds to ensure all neighbouring posts are correctly selected
    TL-18127       MDL-60943: Improved error message for preg_replace errors during global search indexing
    TL-18126       MDL-60742: Allow customisation of 12/24h time format strings
    TL-18125       MDL-60415: Fixed error messages in LTI launch.php when custom parameters are used
    TL-18124       MDL-60079: Fixed 'User tours' leaving unnecessary aria tags in the page
    TL-18123       MDL-57786: Fixed word count for online text submission in assignment module
    TL-18122       MDL-53985: Prevented assignment PDF annotations being removed when a submission is revert back to draft
    TL-18121       MDL-43042: Improved layout of multichoice question response in a lesson
    TL-18117       MDL-61010: Added unread posts link for the counter in "Blog-like" forum which takes a user to the first unread post in the discussion
    TL-18116       MDL-60776: Fixed error in enrolled users listing when custom fullnamedisplay format contains a comma
    TL-18115       MDL-60549: Ensured LTI return link works when content is outside of an iframe
    TL-18114       MDL-55382: Changed quicklist order to be alphabetical when annotating File submission assignments
    TL-18113       MDL-37390: Set course start date when a course is approved to the user's midnight
    TL-18112       MDL-61234: Fixed race condition in user tours while resolving the fetchTour promise
    TL-18111       MDL-61224: Added length validation for short name when creating a role
    TL-18109       MDL-61077: Made quiz statistics calculations more robust
    TL-18108       MDL-60918: Made sure current user is used in message preference update
    TL-18107       MDL-60181: Glossary ratings are now displayed in their entry

                   Previously the entry appeared to be in the following glossary entry.

    TL-18105       MDL-58006: Fixed blind marking status not being reset by course reset in assignment module
    TL-18102       MDL-61253: Fixed referenced files were not added to archive when trying to download a folder
    TL-18101       MDL-61250: Omitted leading space in question preview link
    TL-18098       MDL-60997: Added replytoname property to the core_message class allowing to specify "Reply to" field on outgoing emails
    TL-18097       MDL-60646: Fixed undefined string when managing a user's portfolio
    TL-18096       MDL-60077: Fixed the display of the pop-up triangle next to rounded corners in User Tours
    TL-18092       MDL-61251: Corrected a message to 'Enable RSS feeds' to point to the proper settings section
    TL-18091       MDL-61168: Prevented the 'Export to portfolio' button from getting truncated by collapsed online text submissions

                   When a long 'Online Text' submission is made the entry is truncated and is expandable. The 'Export
                   to portfolio' button, if enabled, was also being truncated. Only the submitted text is truncated now.

    TL-18090       MDL-61027: Fix an issue with datetime profile fields when using non-Gregorian calendars
    TL-18088       MDL-52832: Fixed an issue where quiz page did not take user/group overrides into account when displaying the quiz close date
    TL-18087       MDL-51189: Fixed an issue in the quiz module where trainers were unable to edit override if quiz was not available to student
    TL-18086       MDL-42764: Added missing error message for user accounts without email address
    TL-18081       MDL-61344: Added display of additional files when adding submissions in assignment module
    TL-18080       MDL-61305: Added a lock to prevent 'coursemodinfo' cache to be built multiple times in parallel

                   To reduce impact on the performance, the building of the coursemodinfo cache cannot happen in
                   parallel anymore. There's now a database lock in place to prevent that.

    TL-18079       MDL-61236: Fixed bug where course welcome message email was not sent from the course contact who was first assigned the role of trainer
    TL-18078       MDL-61153: Made lesson detailed statistics report column widths consistent
    TL-18077       MDL-61150: Corrected wrong "path" attribute in some core install.xml files
    TL-18076       MDL-56688: Fixed the order of grade items in single view and export of the Gradebook

                   All views of grade items now show in the order set in the Gradebook setup.

    TL-18074       MDL-61408: Added default button class when checking quiz results
    TL-18073       MDL-61324: Fixed detection of changed grades during LTI sync

                   Improved the detection of changed grades during LTI sync so that unchanged grades are not synced
                   every time the grade sync task is run anymore.

    TL-18072       MDL-61289: Fixed choice activity didn't include extra user profile fields on export
    TL-18071       MDL-61005: Fixed an issue in which system level audiences were potentially excluded when searching audiences in some interfaces
    TL-18070       MDL-58845: The Choice activity report for reviewing answers now respects the 'Display unanswered questions' setting
    TL-18069       MDL-61480: Added a check to ensure plugins are installed within get_plugins_with_function()
    TL-18065       MDL-61453: Fixed accepted file type when uploading user pictures

                   When uploading multiple user pictures, the list of accepted file types for the file picker was not
                   limited to ZIP only. This has been fixed. Attempts to upload non-ZIP files led to an error message.

    TL-18064       MDL-61322: The time column within the log and live log reports now displays the year as part of the date
    TL-18061       MDL-61196: Ensured activity titles are correctly formatted when included in the subject for notifications
    TL-18060       MDL-60658: Fixed validation of the 'grade to pass' activity setting to ensure that localisations are correctly handled
    TL-18058       MDL-55153: Fixed an issue with customised language strings that have been removed still showing up in language customisation interface
    TL-18057       MDL-36157: Fixed HTML entities in RSS feeds that were not displayed correctly
    TL-18051       MDL-61261: Added validation for requests to 'Open badges' backpack to prevent possible self-XSS
    TL-18050       MDL-60398: Fixed an issue with downloading resource of type "Folder" with name of 200+ bytes
    TL-18049       MDL-60241: Fixed visible value of general section in course

                   On upgrade to Moodle 3.3 it was possible that the general section of a course was set to visible =
                   0. Even if this has no effect in Totara this patch reverts this and sets all general sections back
                   to visible = 1.

    TL-18048       MDL-59070: Fixed enrol database plugin bug where the 'enablecompletion' value was not loaded
    TL-18047       MDL-61658: Fixed display of user's country in course participant list and 'Logged in user' block

                   If a country was excluded from the setting 'allcountrycodes', the country code was not translated to
                   the country name in the 'Logged in user' block and on the course participants list.

    TL-18044       MDL-58179: Converted uses of "label" CSS class to "mod_lesson_label"

                   Bootstrap causes HTML elements with the CSS class to have white text. As a result text was not being
                   displayed correctly. This change only affects the lesson activity module.

    TL-18043       MDL-52989: Fixed question clusters occasionally displaying a blank page when a student restarts half way through
    TL-18041       MDL-61733: Fixed creation of tables in Atto editor for Database activity templates
    TL-18040       MDL-61656: Fixed missing role name on the security report for incorrectly defined front page role
    TL-18039       MDL-61576: Ensured the lti_build_custom_parameters function contains all necessary parameters
    TL-18038       MDL-61328: Fixed the sorting of User tours steps when moving steps up or down
    TL-18037       MDL-61321: Fixed a bug in mod_feedback_get_responses_analysis Web Services preventing return of more than first 10 feedback responses
    TL-18036       MDL-61257: Fixed the 'Course module completion updated' link in the course log report

                   The link was previously pointing to the course completion report instead of the activity completion
                   report, this has been fixed.

    TL-18034       MDL-60762: tool_usertours blocks upgrade if admin directory renamed
    TL-18033       MDL-55532: Fixed a hard-coded reference to the admin directory within the User tours tool
    TL-18027       MDL-61689: Unexpected and unhandled output during unit tests will now result in the tests being marked as Risky
    TL-18026       MDL-61522: Made sure glossary paging bar links do not use relative URLs
    TL-18025       MDL-61502: Added a test for multi-lingual "Select missing words" questions
    TL-18023       MDL-61163: Fixed a bug preventing guest users from viewing Wiki pages belonging to Wiki activities added to the page
    TL-18022       MDL-61127: Added improved keyboard navigation when using the file picker
    TL-18021       MDL-61020: Fixed Video.js media player timeline progress bar being flipped in RTL mode
    TL-18020       MDL-60726: Fixed alignment of assignment submission confirmation message
    TL-18019       MDL-60115: Fixed a silently failing redirect when creating a new book resource
    TL-18017       MDL-61860: Fixed require path for config.php on authentication test settings page
    TL-18016       MDL-61581: Added styling to the 'returning to lesson' navigation buttons
    TL-18014       MDL-61129: Added 'colgroup' attribute to the survey question tables
    TL-18013       MDL-61033: Fixed an error when editing a quiz while a preview is open in another browser window
    TL-18012       MDL-60196: Fixed the display of custom LTI icons
    TL-18010       MDL-58697: Fixed issue with assignment submission when toggling group submission

                   When assignment submission was set to group submission and then turned off, the status was not
                   showing an assignment as submitted even if there was a file submitted. The group assignment status
                   is now only considered if group assignment submission is enabled.

    TL-18009       MDL-61741: Fixed the IPN verification endpoint URL of the Paypal Enrolment plugin
    TL-18008       MDL-61708: Fixed LTI to respect fullnamedispaly settings for fullname field in the requests
    TL-18006       MDL-61928: Made frozen form sections collapsible an expandable
    TL-18003       MDL-61520: Fixed references to xhtml in Quiz statistics report
    TL-18002       MDL-61348: Fixed incorrect group grade averages in quiz reports
    TL-18001       MDL-59857: Increased the length of the 'completionscorerequired' field in SCORM database table
    TL-17999       MDL-62042: Filtered out some unicode non-characters when building index for Solr
    TL-17997       MDL-62011: Fixed an issue where approval of a course request fails if a new course with the same name has been created prior to request approval
    TL-17996       MDL-61715: Fixed Question type chooser displaying headings for empty sections under certain conditions
    TL-17995       MDL-60882: Prevent deletion of all responses if the external function delete_choice_responses() is called without responses specified

                   The external function mod_choice_external::delete_choice_responses has changed behaviour - if this
                   function is called by a user who has the 'mod/choice:deleteresponses' capability with no responses
                   specified then only the user's responses will be deleted, rather than all responses for all users
                   within the choice. To delete all responses from all users, all response IDs must be specified.

    TL-17993       MDL-61012: Allow module name to be guessed only if not set by subclass of the moodleform_mod class
    TL-17990       MDL-61800: Reset the OUTPUT and PAGE for each task on cron execution
    TL-17989       MDL-61521: Fixed missing text formatting for category name in get_categories Web Service
    TL-17985       MDL-62500: Fixed an issue where a checkbox label wasn't updated after updating a tag
    TL-17983       MDL-62408: Fixed profile_guided_allocate() function to help split behat scenarios better for parallel runs never being executed in behat_config_util
    TL-17981       MDL-62588: Added missing instanceid database field to the Paypal enrolment plugin
    TL-17337   +   MDL-61392: Improved the IPN notifications handling in Paypal enrollment plugin
    TL-17335   +   MDL-61269: Set composer license to GPL-3.0-or-later
    TL-17326   +   MDL-60436: Improved the performance of block loading
    TL-17089   +   MDL-58699: Improved the security of the quiz module while using browser security settings

                   When the "Browser Security" setting is set to "Full screen pop-up with some JavaScript security",
                   the "Attempt quiz" button is no longer visible if a user has JavaScript disabled.

    TL-17083   +   MDL-59858: After closing a modal factory modal, focus goes back to the element that triggered it.
    TL-17058   +   MDL-60535: Improved style of button when adding questions from a question bank to a quiz
    TL-17057   +   MDL-51892: Added a proper description of the login errors
    TL-17055   +   MDL-60571: Styled "Save and go to next page" as a primary button when manually grading quiz questions
    TL-17053   +   MDL-36580: Added encryption of secrets in backup and restore functionality

                   LTI (external tool) activity secret and key are encrypted during backup and decrypted during restore
                   using aes-256-cbc encryption algorithm. Encryption key is stored in the site configuration so
                   backup made with encryption will be restored with lti key and secret on the same site, and without
                   these values on different site.

    TL-17050   +   MDL-60489: Content height changes when using the modal library are now smooth transitions
    TL-17043   +   MDL-60449: Various language strings improvements in courses and administration
    TL-17013   +   MDL-54540: Added allowfullscreen attribute to LTI iFrames to ensure the full screen can be used

                   This change adds attributes to the LTI iframe allowing the content to be viewed in full screen.

    TL-16995   +   MDL-35849: Added "alert" role HTML attribute to the log in errors

                   This allows screen readers to identify when a user has not logged in correctly

    TL-15708       MDL-59132: Fixed anonymous response numbering in feedback Web Service
    TL-15684       MDL-58857: User session is now terminated when a major upgrade is required
    TL-15682       MDL-58860: Fixed Web Service mod_lesson_get_attempts_overview when no attempts made
    TL-15639       MDL-58659: Added enddate parameter to Web Services returning course information
    TL-15636       MDL-58681: Split the checkbox and advcheckbox behat tests

                   Advanced checkboxes cannot be tested without a real browser because Goutte does not support the
                   hidden+checkbox duality.

    TL-15635       MDL-51932: Improved UX when setting up a workshop

                   When setting up a workshop activity, the stage switch has been updated to state which stage they
                   will take you to.

    TL-15630       MDL-58415: Multiple bug fixes in the new lesson web services

                   * Avoid inappropriate http redirections
                   * Added missing answer fields
                   * Various code fixes, including ensuring correct variable types are used where necessary

    TL-15620       MDL-58412: Fixed several bugs in the new feedback web services
    TL-15619       MDL-58530: Updated the video.js library to v5.18.4
    TL-15604       MDL-58502: Fixed error when cancelling feedback
    TL-15598       MDL-58574: Removed an unnecessary check for delete icon when working with permissions in an activity module
    TL-15594       MDL-58549: Added version of jabber/XMPP libraries to thirdpartylibraries.xml
    TL-15589       MDL-58493: Converted the delete enrolment icon to a font icon

                   When managing enrolments in a course, if a role was added, the delete icon
                   was an image (instead of a font icon) before the page was reloaded. This
                   has been corrected.

    TL-15583       MDL-57573: Updated PHPmailer library to v5.2.23
    TL-15579       MDL-58552: Fixed alignment of quiz icon
    TL-15575       MDL-57553: Fixed user tour steps so that they do not inherit attributes from CSS selector

                   Updated the flexitour component to v0.10.0 and the popper.js library to v1.0.8 in the process.

    TL-15569       MDL-56632: Moved the "Turn editing on\off" link to the top of the book administration menu
    TL-15567       MDL-58311: Added support for password-protected Redis Session and Cache Store connections

                   Support for setting a password for the Redis Cache and Session Store was added. Password for the
                   cache store can be set when adding or editing the cache store instance settings.

                   The password for the Redis session store can be set with the config $CFG->session_redis_auth.

    TL-15565       MDL-58453: Refactored get_non_respondents Web Service
    TL-15564       MDL-57813: Added Web Service mod_feedback_get_last_completed
    TL-15559       MDL-58361: Made core_media_manager final to prevent from being subclassed
    TL-15558       MDL-58399: Return additional file fields in Web Services to be able to handle external repositories files

                   See mod/upgrade.txt and course/upgrade.txt for details.

    TL-15557       MDL-58444: Added number of unread posts to get_forums_by_courses  Web Services
    TL-15556       MDL-51998: Improved manage forum subscribers button
    TL-15555       MDL-57821: Added Web Service mod_feedback_get_responses_analysis
    TL-15553       MDL-53343: Migrated scorm_cron into new tasks API
    TL-15514       MDL-58265: Refactored behat to use a new step "I am on the course homepage"

                   The new step directly accesses the course page without following the path from the homepage to the
                   course. A shortcut step "I am on course homepage with editing mode on" was also added to allow
                   accessing a course and turn editing mode on.

    TL-15496       MDL-57503: Allow course ids for enrol_get_my_courses

                   This adds a new parameter for enrol_get_my_courses() to filter the list returned to specific courses.

    TL-15466       MDL-55941: Improved UX of alpha chooser / initialbar in tablelib and made it responsive
    TL-15464       MDL-48771: Improved quiz question editing interface

                   The quiz editing interface has been improved to allow selection of multiple questions to be deleted.

    TL-15461       MDL-57411: mod_check_updates now returns information based on user capabilities
    TL-15445       MDL-50970: Added new Web Service core_block_get_course_blocks
    TL-15444       MDL-57925: Implemented check_updates_since callback
    TL-15443       MDL-57924: Added new Web Service mod_data_update_entry
    TL-15442       MDL-57923: Added new Web Service mod_data_add_entry
    TL-15441       MDL-57922: Added new Web Service mod_data_delete_entry
    TL-15440       MDL-57921: Added new Web Service mod_data_approve_entry
    TL-15439       MDL-57920: Added new Web Service mod_data_search_entrie
    TL-15438       MDL-57919: Added new Web Service mod_data_get_fields
    TL-15437       MDL-57918: Added new Web Service mod_data_get_entry
    TL-15436       MDL-49409: Added new Web Service mod_data_get_entries
    TL-15434       MDL-57822: Added new Web Service mod_feedback_get_non_respondents
    TL-15433       MDL-58230: Added new Web Service mod_feedback_get_finished_responses
    TL-15432       MDL-55139: Added code coverage filter in component phpunit.xml files
    TL-15431       MDL-58070: Reworded "visible" core string used in course visibility

                   Additionally we aligned the name and value strings of the course visibility default settings.
                   Previously the value strings were different to the actual course settings.

    TL-15430       MDL-57965: Enabled gzip compression for SVG files
    TL-15428       MDL-58329: Added new Web Service mod_lesson_get_lesson
    TL-15427       MDL-57760: Added new Web Service mod_lesson_get_pages_possible_jumps
    TL-15426       MDL-57762: Added check updates functionality to the lesson module
    TL-15424       MDL-57757: Added new Web Service mod_lesson_get_user_attempt
    TL-15423       MDL-57754: Added new Web Service mod_lesson_get_attempts_overview
    TL-15422       MDL-57724: Added new Web Service mod_lesson_finish_attempt
    TL-15421       MDL-57696: Added new Web Service mod_lesson_process_page
    TL-15420       MDL-57693: Added new Web Service mod_lesson_get_page_data
    TL-15419       MDL-57688: Added new Web Service mod_lesson_launch_attempt
    TL-15418       MDL-58229: Added new Web Service get_unfinished_responses
    TL-15417       MDL-57820: Added new Web Service mod_feedback_get_analysis
    TL-15415       MDL-57818: Added new Web Service mod_feedback_process_page
    TL-15414       MDL-57817: Added new Web Service mod_feedback_get_page_items
    TL-15413       MDL-57816: Added new Web Service mod_feedback_launch_feedback
    TL-15412       MDL-57685: Added new Web Service mod_lesson_get_pages
    TL-15411       MDL-55267: Removed deprecated field datasourceaggregate
    TL-15410       MDL-57815: Added new Web Service mod_feedback_get_items
    TL-15409       MDL-57823: Implemented the check_updates callback in the feedback module
    TL-15408       MDL-57814: Added new Web Service mod_feedback_get_current_completed_tmp
    TL-15407       MDL-57916: Added new Web Service mod_data_get_access_information
    TL-15406       MDL-57811: Added new Web Service mod_feedback_view_feedback
    TL-15404       MDL-57812: Added new Web Service get_feedback_access_information
    TL-15402       MDL-57665: Added new Web Service mod_lesson_get_user_timers
    TL-15401       MDL-57664: Added new lesson Web Service get_content_pages_viewed
    TL-15398       MDL-57657: Added new Web Service mod_lesson_get_user_grade
    TL-15397       MDL-40759: Added additional Font Awesome support

                   A small number of icons have been converted to Font Awesome icons, and a number of remaining
                   locations where image icons were used have been replaced with font icons.

    TL-15396       MDL-57390: Added capabilities/permission information to Web Service forum_can_add_discussion response
    TL-15394       MDL-57648: Added new web service mod_lesson_get_questions_attempts
    TL-15393       MDL-57645: Added new web service mod_lesson_view_lesson
    TL-15392       MDL-57643: Added new Web Service mod_lesson_get_lesson_access_information
    TL-15388       MDL-50538: Added new Web Service mod_feedback_get_feedbacks_by_courses
    TL-15386       MDL-57631: Implemented scheduled task for LDAP Enrolments Sync

                   The previous CLI script has been deprecated in favour of the new scheduled task. The new task is
                   disabled by default.

    TL-15385       MDL-58109: Added check for preventexecpath in the Security Report

                   If the config value $CFG->preventexecpath is set to 'false' this will show up in the Security Report
                   as a warning.

    TL-15383       MDL-58217: Added data generators for feedback items
    TL-15382       MDL-57915: Added Web Service mod_data_view_database
    TL-15380       MDL-57914: Refactored get_databases_by_courses
    TL-15379       MDL-57975: Added HTML5 session storage.

                   This can be used by developers using the core/sessionstorage AMD module in much the same way
                   developers can use core/localstorage

                   This also adds a core_get_user_dates and userdate mustache helper.

    TL-15377       MDL-57999: Add itemname to gradereport_user_get_grade_items  Web Service
    TL-15376       MDL-57280: Added the ability to create modal types via a registry

                   More information can be found at https://help.totaralearning.com/display/DEV/Modal+registry

    TL-15375       MDL-45584: Made cache identifiers part of loaded caches
    TL-15374       MDL-57972: Added shortentext mustache helper
    TL-15371       MDL-57887: Support nginx and other webservers for logging of username in access logs

                   Support for logging usernames to webserver access logs has been extended to allow sending the
                   username as a custom header which can be logged and stripped out if needed.

    TL-15368       MDL-53978: Added extra plugin callbacks for every major stage of page render + swap user tours to use them
    TL-15366       MDL-57527: Changed course reports to use CSS instead of SVG rotation
    TL-15365       MDL-57633: Added new Web Service mod_lesson_get_lessons_by_courses
    TL-15363       MDL-57602: Added 'Granted extension' filter for grading table
    TL-15362       MDL-57619: Removed behat steps deprecated in Moodle 2.9 or earlier
    TL-15358       MDL-57687: Removed unnecessary init_toggle_class_on_click JavaScript functionality
    TL-15357       MDL-57890: Improved all get_by_courses Web Services to include the coursemodule (cmid) in the results
    TL-15356       MDL-57896: Added command line tool to read and change configuration settings in the database
    TL-15355       MDL-55476: Removed loginpasswordautocomplete option

                   The a loginpasswordautocomplete option simply appends autocomplete="off" to the password field in
                   the form. As most of the browsers dropped support for this attribute it is removed.

    TL-15354       MDL-57697: Converted survey validation JavaScript from YUI2 to AMD
    TL-15350       MDL-57586: Changed $workshop variable from protected to public in class

                   Changed $workshop from protected to public in class workshop_example_submission to make it easier
                   for renderers in themes to access data instead of retrieving it from the database.

    TL-15349       MDL-57638: Improved the handling of failed RSS feeds in the RSS block

                   Previously if the cron could not read the RSS feed configured in a block this failure was not
                   visible to the administrator in the interface. Additionally every time the block displayed it tried
                   to fetch the feeds regardless of its status.
                   With this patch the RSS blocks do not try to request the feeds if the 'skiptime' and 'skipuntil'
                   values are set. If there are failed feeds then an error message will be shown to the administrator
                   but not to a learner.

    TL-15348       MDL-56808: Removed use of eval in SCORM JavaScript files
    TL-15346       MDL-57273: Added generic exporter, persistent and persistent form classes

                   This patch adds new model classes following an active record pattern to represent, fetch and store
                   data in the database. The persistent class also provides basic validation.

                   Exporters convert objects to stdClasses. The exporter contains the definition of all properties and
                   optionally related objects.

    TL-15345       MDL-57655: Added support for the igbinary serializer in the Redis Session Handler

                   If igbinary is installed and $CFG->session_redis_serializer_use_igbinary is set to true the Redis
                   session handler uses igbinary for serializing the data.

    TL-15344       MDL-57690: Stopped loading mcore YUI rollup on each page

                   This may expose areas in custom JavaScript that use YUI modules without loading them correctly.

    TL-15343       MDL-49423: Added support for optiongroups inside admin selects
    TL-15342       MDL-50539: Added new Web Service to retrieve a list of folders from several courses
    TL-15341       MDL-50545: Added new Web Service to retrieve a list of pages from several courses
    TL-15340       MDL-56449: Provided a more detailed description of group submission problems
    TL-15339       MDL-57550: Updated advanced forum search to use AMD modules
    TL-15338       MDL-50547: Added new Web Service to retrieve a list of resources from several courses

                   Added a new Web Service which returns a list of files in a provided list of courses. If no list is
                   provided all files that the user can view will be returned.

    TL-15336       MDL-57490: Converted Select all/none functionality to use JavaScript

                   In the quiz, SCORM and lesson modules, there was some inline JavaScript handlers. These have been
                   converted to pure JavaScript event listeners.

    TL-15335       MDL-57570: Added support for the igbinary serializer in the Static Cache Store

                   If igbinary is installed the static cache store automatically makes use of it.

    TL-15333       MDL-57488: Replaced and deprecated M.util.focus_login_form and M.util.focus_login_error
    TL-15330       MDL-50542: Added new Web Service to retrieve a list of labels from several courses
    TL-15329       MDL-50549: Added new Web Service to retrieve a list of URLs from several courses
    TL-15328       MDL-57627: Added new field to forum Web Service to get tracking status of the user
    TL-15326       MDL-56519: Added linting for behat .feature files

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

    TL-15325       MDL-57572: Added support for the igbinary serializer in the Redis Cache Store

                   Added setting to switch the serializer to either the builtin php or the igbinary serialiser. The
                   igbinary serialiser stores data structures in compact binary form and savings can be significant for
                   storing cached data in Redis.

    TL-15324       MDL-57282: Deprecated the behat step "I go to X in the course gradebook"
    TL-15323       MDL-57149: Made the language import administration page compatible with Bootstrap
    TL-15322       MDL-57392: Modified external function core_course_external::get_courses_by_field to return the course filters list and status
    TL-15321       MDL-55461: Fixed placement of cursor in Atto equation editor on repeated insertions from predefined buttons
    TL-15319       MDL-44172: Removed example htaccess file
    TL-15317       MDL-57395: Added new Web Service core_course_get_updates_since
    TL-15316       MDL-57471: Deprecated init_javascript_enhancement() and smartselect code
    TL-15315       MDL-57472: Removed fix_column_widths Internet Explorer 6 hack

                   Removed old Internet Explorer 6 hack and added deprecated warnings.

    TL-15314       MDL-56581: Highlighted row when permission is overriden in a course
    TL-15312       MDL-56640: Converted single selects and URL selects to mustache templates

                   This has also deprecated the YUI auto submit JavaScript.

    TL-15311       MDL-56320: Allow uninstall of unused web service plugins
    TL-15309       MDL-57143: Removed check for Windows when using SQL Server (sqlsrv) drivers

                   When using the SQL driver for Linux there was an error message during initialisation stating that
                   the driver is only available for Windows. This is not true anymore as there is a Linux driver, thus
                   the message got removed.

     TL-15306      MDL-53814: Show question type icons when manually grading a quiz


Contributions:

    * James Voong from Catalyst - TL-17357
    * Jo Jones at Kineo UK - TL-18686, TL-18640, TL-18591
    * Joby Harding at 77 Gears Ltd - TL-19045, TL-10852
    * Michael Dunstan at Androgogic - TL-18931
    * Russell England at Kineo USA - TL-18746, TL-17149

*/