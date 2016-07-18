<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2016 onwards Totara Learning Solutions LTD
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author  Joby Harding <joby.harding@totaralms.com>
 * @author  Petr Skoda <petr.skoda@totaralms.com>
 * @package core
 */

/**
 * Information located pix/flex_icons.php files is merged to obtain
 * a full map of all flex icon definitions. Themes have the highest
 * priority and can override any map or translation item.
 */

/*
 * Translation of old pix icon names to flex icon identifiers.
 *
 * The old pix icon name format is "component_name|originalpixpath"
 * similar to the old pix placeholders in CSS.
 *
 * All flex icon identifiers must be present in the $map.
 */
$translations = array(
    'core|a/add_file' => 'new-document',
    'core|a/create_folder' => 'folder-create',
    'core|a/download_all' => 'download',
    'core|a/help' => 'question-circle',
    'core|a/logout' => 'sign-out',
    'core|a/refresh' => 'refresh',
    'core|a/search' => 'search',
    'core|a/setting' => 'cog',
    'core|a/view_icon_active' => 'th-large',
    'core|a/view_list_active' => 'align-justify',
    'core|a/view_tree_active' => 'viewtreeactive',
    'core|b/bookmark-new' => 'bookmark-o',
    'core|b/document-edit' => 'edit-document',
    'core|b/document-new' => 'new-document',
    'core|b/document-properties' => 'document-properties',
    'core|b/edit-copy' => 'copy',
    'core|b/edit-delete' => 'times-danger',
    'core|c/event' => 'calendar',
    'core|docs' => 'info-circle',
    'core|e/accessibility_checker' => 'wheelchair',
    'core|e/align_center' => 'align-center',
    'core|e/align_left' => 'align-left',
    'core|e/align_right' => 'align-right',
    'core|e/anchor' => 'bookmark-o',
    'core|e/bold' => 'bold',
    'core|e/bullet_list' => 'list-ul',
    'core|e/cite' => 'quote-left',
    'core|e/copy' => 'copy',
    'core|e/cut' => 'scissors',
    'core|e/decrease_indent' => 'outdent',
    'core|e/delete' => 'strikethrough',
    'core|e/document-properties' => 'document-properties',
    'core|e/document_properties' => 'file-wrench',
    'core|e/emoticons' => 'smile-o',
    'core|e/fullscreen' => 'expand',
    'core|e/help' => 'question-circle',
    'core|e/increase_indent' => 'indent',
    'core|e/insert' => 'underline',
    'core|e/insert_date' => 'calendar',
    'core|e/insert_edit_image' => 'image',
    'core|e/insert_edit_link' => 'link',
    'core|e/insert_edit_video' => 'film',
    'core|e/insert_horizontal_ruler' => 'dash',
    'core|e/insert_time' => 'clock-o',
    'core|e/italic' => 'italic',
    'core|e/justify' => 'align-justify',
    'core|e/math' => 'calculator',
    'core|e/new_document' => 'new-document',
    'core|e/numbered_list' => 'list-ol',
    'core|e/paste' => 'paste',
    'core|e/prevent_autolink' => 'prevent-autolink',
    'core|e/preview' => 'eye',
    'core|e/print' => 'print',
    'core|e/question' => 'question',
    'core|e/redo' => 'repeat',
    'core|e/remove_link' => 'unlink',
    'core|e/resize' => 'arrows-alt',
    'core|e/restore_draft' => 'restore-draft',
    'core|e/save' => 'save',
    'core|e/screenreader_helper' => 'wheelchair',
    'core|e/search' => 'search',
    'core|e/show_invisible_characters' => 'paragraph',
    'core|e/source_code' => 'code',
    'core|e/strikethrough' => 'strikethrough',
    'core|e/subscript' => 'subscript',
    'core|e/superscript' => 'superscript',
    'core|e/table' => 'table',
    'core|e/text_color' => 'font-info',
    'core|e/text_color_picker' => 'text-color-picker',
    'core|e/tick' => 'check-success',
    'core|e/toggle_blockquote' => 'quote-left',
    'core|e/underline' => 'underline',
    'core|e/undo' => 'undo',
    'core|f/archive' => 'box-alt',
    'core|f/audio' => 'volume-up',
    'core|f/avi' => 'film',
    'core|f/base' => 'database',
    'core|f/bmp' => 'file-image-o',
    'core|f/chart' => 'bar-chart',
    'core|f/database' => 'database',
    'core|f/dmg' => 'file-archive-o',
    'core|f/document' => 'file-text-o',
    'core|f/edit' => 'pencil-square-o',
    'core|f/eps' => 'file-image-o',
    'core|f/epub' => 'ebook',
    'core|f/explore' => 'explore',
    'core|f/flash' => 'file-video-o',
    'core|f/folder' => 'folder-o',
    'core|f/folder-open' => 'folder-open-o',
    'core|f/gif' => 'file-image-o',
    'core|f/help-32' => 'question-circle',
    'core|f/html' => 'file-code-o',
    'core|f/image' => 'file-image-o',
    'core|f/jpeg' => 'file-image-o',
    'core|f/markup' => 'file-code-o',
    'core|f/mov' => 'file-video-o',
    'core|f/move' => 'arrows',
    'core|f/mp3' => 'file-sound-o',
    'core|f/mpeg' => 'file-video-o',
    'core|f/parent-32' => 'level-up',
    'core|f/pdf' => 'file-pdf-o',
    'core|f/png' => 'file-image-o',
    'core|f/powerpoint' => 'file-powerpoint-o',
    'core|f/psd' => 'file-image-o',
    'core|f/quicktime' => 'file-video-o',
    'core|f/sourcecode' => 'file-code-o',
    'core|f/spreadsheet' => 'file-excel-o',
    'core|f/text' => 'file-text-o',
    'core|f/tiff' => 'file-image-o',
    'core|f/unknown' => 'file-o',
    'core|f/video' => 'file-video-o',
    'core|f/wav' => 'file-sound-o',
    'core|f/wmv' => 'file-video-o',
    'core|g/f1' => 'smile-o',
    'core|g/f2' => 'smile-o',
    'core|help' => 'question-circle',
    'core|i/admin' => 'cog',
    'core|i/agg_mean' => 'mean',
    'core|i/agg_sum' => 'sigma',
    'core|i/ajaxloader' => 'spinner-pulse',
    'core|i/assignroles' => 'user-plus',
    'core|i/backup' => 'upload',
    'core|i/badge' => 'trophy',
    'core|i/calc' => 'calculator',
    'core|i/calendar' => 'calendar',
    'core|i/caution' => 'exclamation-circle',
    'core|i/checkpermissions' => 'check-permissions',
    'core|i/closed' => 'folder-o',
    'core|i/cohort' => 'users',
    'core|i/completion-auto-enabled' => 'auto-completion-on',
    'core|i/completion-auto-fail' => 'times-circle-o-danger',
    'core|i/completion-auto-n' => 'circle-o',
    'core|i/completion-auto-pass' => 'check-circle-o-success',
    'core|i/completion-auto-y' => 'check-circle-o',
    'core|i/completion-manual-enabled' => 'manual-completion-on',
    'core|i/completion-manual-n' => 'square-o',
    'core|i/completion-manual-y' => 'check-square-o',
    'core|i/configlock' => 'cog-lock',
    'core|i/course' => 'cube',
    'core|i/courseevent' => 'course-event',
    'core|i/db' => 'database',
    'core|i/delete' => 'times-danger',
    'core|i/down' => 'arrow-down',
    'core|i/dragdrop' => 'arrows',
    'core|i/dropdown' => 'caret-down',
    'core|i/edit' => 'edit',
    'core|i/email' => 'envelope-o',
    'core|i/enrolmentsuspended' => 'enrolment-suspended',
    'core|i/enrolusers' => 'user-plus',
    'core|i/export' => 'upload',
    'core|i/feedback' => 'comment-o',
    'core|i/feedback_add' => 'add-feedback',
    'core|i/files' => 'file-o',
    'core|i/filter' => 'filter',
    'core|i/flagged' => 'flag',
    'core|i/folder' => 'folder-o',
    'core|i/grade_correct' => 'check-success',
    'core|i/grade_incorrect' => 'times-danger',
    'core|i/grade_partiallycorrect' => 'check-warning',
    'core|i/grades' => 'grades',
    'core|i/group' => 'users',
    'core|i/groupevent' => 'group-event',
    'core|i/groupn' => 'no-groups',
    'core|i/groups' => 'separate-groups',
    'core|i/groupv' => 'users',
    'core|i/guest' => 'user-secret',
    'core|i/hide' => 'eye-slash',
    'core|i/hierarchylock' => 'site-lock',
    'core|i/import' => 'download',
    'core|i/info' => 'info-circle',
    'core|i/invalid' => 'times-danger',
    'core|i/key' => 'key',
    'core|i/loading' => 'spinner-pulse',
    'core|i/loading_small' => 'spinner-pulse',
    'core|i/lock' => 'lock',
    'core|i/log' => 'log',
    'core|i/mahara_host' => 'mahara',
    'core|i/manual_item' => 'edit',
    'core|i/marked' => 'lightbulb-o',
    'core|i/marker' => 'lightbulb-o-disabled',
    'core|i/mean' => 'mean',
    'core|i/menu' => 'bars',
    'core|i/mnethost' => 'mnet-host',
    'core|i/moodle_host' => 'moodle',
    'core|i/move_2d' => 'arrows',
    'core|i/new' => 'new',
    'core|i/news' => 'newspaper-o',
    'core|i/nosubcat' => 'no-subcategory',
    'core|i/open' => 'folder-open-o',
    'core|i/outcomes' => 'pie-chart',
    'core|i/payment' => 'dollar',
    'core|i/permissionlock' => 'permission-lock',
    'core|i/permissions' => 'permissions',
    'core|i/portfolio' => 'portfolio',
    'core|i/preview' => 'eye',
    'core|i/publish' => 'publish',
    'core|i/questions' => 'question',
    'core|i/reload' => 'refresh',
    'core|i/report' => 'file-text-o',
    'core|i/repository' => 'database',
    'core|i/restore' => 'download',
    'core|i/return' => 'undo',
    'core|i/risk_config' => 'cogs-risk',
    'core|i/risk_dataloss' => 'database-risk',
    'core|i/risk_managetrust' => 'shield-risk',
    'core|i/risk_personal' => 'user-risk',
    'core|i/risk_spam' => 'envelope-risk',
    'core|i/risk_xss' => 'code-risk',
    'core|i/rss' => 'rss',
    'core|i/scales' => 'scales',
    'core|i/scheduled' => 'clock-o',
    'core|i/search' => 'search',
    'core|i/self' => 'user',
    'core|i/settings' => 'cog',
    'core|i/show' => 'eye',
    'core|i/siteevent' => 'calendar',
    'core|i/star-rating' => 'star-half-o',
    'core|i/stats' => 'line-chart',
    'core|i/switch' => 'toggle-on',
    'core|i/switchrole' => 'user-refresh',
    'core|i/twoway' => 'arrows-h',
    'core|i/unflagged' => 'flag-o',
    'core|i/unlock' => 'unlock',
    'core|i/up' => 'arrow-up',
    'core|i/user' => 'user',
    'core|i/useradd' => 'user-plus',
    'core|i/userdel' => 'user-times',
    'core|i/userevent' => 'user-event',
    'core|i/users' => 'users',
    'core|i/valid' => 'check-success',
    'core|i/warning' => 'warning-warning',
    'core|i/withsubcat' => 'viewtreeactive',
    'core|m/USD' => 'dollar',
    'core|req' => 'asterisk',
    'core|spacer' => 'spacer',
    'core|t/add' => 'plus',
    'core|t/addcontact' => 'contact-add',
    'core|t/adddir' => 'folder-create',
    'core|t/addfile' => 'new-document',
    'core|t/approve' => 'check',
    'core|t/assignroles' => 'user-plus',
    'core|t/award' => 'trophy',
    'core|t/backpack' => 'backpack',
    'core|t/backup' => 'upload',
    'core|t/block' => 'block',
    'core|t/block_to_dock' => 'block_to_dock',
    'core|t/block_to_dock_rtl' => 'dock_to_block',
    'core|t/cache' => 'cache',
    'core|t/calc' => 'calculator',
    'core|t/calc_off' => 'calculator-off',
    'core|t/calendar' => 'calendar',
    'core|t/check' => 'check',
    'core|t/cohort' => 'users',
    'core|t/collapsed' => 'caret-right',
    'core|t/collapsed_empty' => 'caret-right-disabled',
    'core|t/collapsed_empty_rtl' => 'caret-left-disabled',
    'core|t/collapsed_rtl' => 'caret-left',
    'core|t/contextmenu' => 'bars',
    'core|t/copy' => 'copy',
    'core|t/delete' => 'times-danger',
    'core|t/disable_down' => 'arrow-down',
    'core|t/disable_up' => 'arrow-up',
    'core|t/dock_to_block' => 'dock_to_block',
    'core|t/dock_to_block_rtl' => 'block_to_dock',
    'core|t/dockclose' => 'times-circle-o',
    'core|t/down' => 'arrow-down',
    'core|t/download' => 'download',
    'core|t/dropdown' => 'caret-down',
    'core|t/edit' => 'cog',
    'core|t/edit_gray' => 'edit',
    'core|t/edit_menu' => 'editmenu',
    'core|t/editstring' => 'edit',
    'core|t/email' => 'envelope-o',
    'core|t/emailno' => 'no-email',
    'core|t/enroladd' => 'plus',
    'core|t/enrolusers' => 'user-plus',
    'core|t/expanded' => 'caret-down',
    'core|t/feedback' => 'comment-o',
    'core|t/feedback_add' => 'add-feedback',
    'core|t/go' => 'circle-success',
    'core|t/grades' => 'grades',
    'core|t/groupn' => 'no-groups',
    'core|t/groups' => 'separate-groups',
    'core|t/groupv' => 'users',
    'core|t/hide' => 'eye-slash',
    'core|t/left' => 'arrow-left',
    'core|t/less' => 'minus',
    'core|t/lock' => 'lock',
    'core|t/locked' => 'lock',
    'core|t/locktime' => 'clock-locked',
    'core|t/log' => 'log',
    'core|t/markasread' => 'check',
    'core|t/mean' => 'mean',
    'core|t/message' => 'comment',
    'core|t/messages' => 'comments',
    'core|t/more' => 'plus',
    'core|t/move' => 'arrows-v',
    'core|t/portfolioadd' => 'portfolio-add',
    'core|t/preferences' => 'sliders',
    'core|t/preview' => 'eye',
    'core|t/print' => 'print',
    'core|t/ranges' => 'ranges',
    'core|t/recycle' => 'recycle',
    'core|t/reload' => 'refresh',
    'core|t/removecontact' => 'contact-remove',
    'core|t/reset' => 'undo',
    'core|t/restore' => 'download',
    'core|t/right' => 'arrow-right',
    'core|t/scales' => 'scales',
    'core|t/show' => 'eye',
    'core|t/sigma' => 'sigma',
    'core|t/sigmaplus' => 'sigma-plus',
    'core|t/sort' => 'sort',
    'core|t/sort_asc' => 'sort-asc',
    'core|t/sort_desc' => 'sort-desc',
    'core|t/stop' => 'circle-danger',
    'core|t/stop_gray' => 'circle-disabled',
    'core|t/switch' => 'plus-square',
    'core|t/switch_minus' => 'minus-square',
    'core|t/switch_plus' => 'plus-square',
    'core|t/switch_plus_rtl' => 'plus-square',
    'core|t/switch_whole' => 'external-link-square',
    'core|t/unblock' => 'check',
    'core|t/unlock' => 'unlock',
    'core|t/unlocked' => 'unlock-alt',
    'core|t/up' => 'arrow-up',
    'core|t/user' => 'user',
    'core|t/usernot' => 'user-times',
    'core|t/viewdetails' => 'eye',
    'core|u/f1' => 'user-disabled',
    'core|u/f2' => 'user-disabled',
    'core|u/f3' => 'user-disabled',
    'core|u/user100' => 'user-disabled',
    'core|u/user35' => 'user-disabled',
    'core|y/lm' => 'caret-down',
    'core|y/loading' => 'spinner-pulse',
    'core|y/lp' => 'caret-right',
    'core|y/lp_rtl' => 'caret-left',
    'core|y/tm' => 'caret-down',
    'core|y/tp' => 'caret-right',
    'core|y/tp_rtl' => 'caret-left',
);

/*
 * Map describes available flex icons.
 */
$map = array(
    /* Do not use 'missing-flex-icon' directly, it indicates icon was not found */
    'missing-flex-icon' =>
        array(
            'template' => 'core/flex_icon_stack',
            'data' =>
                array(
                    'classes' =>
                        array(
                            'stack_first' => 'fa fa-question ft-stack-main',
                            'stack_second' => 'fa fa-exclamation ft-stack-suffix'
                        ),
                ),
        ),
    'add-feedback' =>
        array(
            'template' => 'core/flex_icon_stack',
            'data' =>
                array(
                    'classes' =>
                        array(
                            'stack_first' => 'fa fa-comment-o ft-stack-main',
                            'stack_second' => 'fa fa-plus ft-stack-suffix',
                        ),
                ),
        ),
    'addressbook' =>
        array(
            'data' =>
                array(
                    'classes' => 'ft ft-addressbook',
                ),
        ),
    'alarm' =>
        array(
            'data' =>
                array(
                    'classes' => 'ft ft-alarm',
                ),
        ),
    'alarm-danger' =>
        array(
            'template' => 'core/flex_icon_stack',
            'data' =>
                array(
                    'classes' =>
                        array(
                            'stack_first' => 'ft ft-alarm ft-stack-main',
                            'stack_second' => 'fa fa-bolt ft-stack-suffix ft-state-danger',
                        ),
                ),
        ),
    'alarm-warning' =>
        array(
            'template' => 'core/flex_icon_stack',
            'data' =>
                array(
                    'classes' =>
                        array(
                            'stack_first' => 'ft ft-alarm ft-stack-main',
                            'stack_second' => 'fa fa-warning ft-stack-suffix ft-state-warning',
                        ),
                ),
        ),
    'alfresco' =>
        array(
            'data' =>
                array(
                    'classes' => 'ft ft-alfresco',
                ),
        ),
    'align-center' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-align-center',
                ),
        ),
    'align-justify' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-align-justify',
                ),
        ),
    'align-left' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-align-left',
                ),
        ),
    'align-right' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-align-right',
                ),
        ),
    'archive' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-archive',
                ),
        ),
    'archive-alt' =>
        array(
            'data' =>
                array(
                    'classes' => 'ft ft-archive-alt',
                ),
        ),
    'archives-alt' =>
        array(
            'data' =>
                array(
                    'classes' => 'ft ft-archives-alt',
                ),
        ),
    'areafiles' =>
        array(
            'template' => 'core/flex_icon_stack',
            'data' =>
                array(
                    'classes' =>
                        array(
                            'stack_first' => 'fa fa-file-text-o ft-stack-main',
                            'stack_second' => 'fa fa-paperclip ft-stack-suffix',
                        ),
                ),
        ),
    'arrow-down' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-arrow-down',
                ),
        ),
    'arrow-left' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-arrow-left',
                ),
        ),
    'arrow-right' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-arrow-right',
                ),
        ),
    'arrow-up' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-arrow-up',
                ),
        ),
    'arrows' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-arrows',
                ),
        ),
    'arrows-alt' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-arrows-alt',
                ),
        ),
    'arrows-h' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-arrows-h',
                ),
        ),
    'arrows-v' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-arrows-v',
                ),
        ),
    'assign' =>
        array(
            'template' => 'core/flex_icon_stack',
            'data' =>
                array(
                    'classes' =>
                        array(
                            'stack_first' => 'fa fa-file-text-o ft-stack-main',
                            'stack_second' => 'fa fa-thumb-tack ft-stack-suffix ft-state-info',
                        ),
                ),
        ),
    'asterisk' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-asterisk',
                ),
        ),
    'auto-completion-on' =>
        array(
            'template' => 'core/flex_icon_stack',
            'data' =>
                array(
                    'classes' =>
                        array(
                            'stack_first' => 'fa fa-check-circle-o ft-stack-main',
                            'stack_second' => 'fa fa-play ft-stack-suffix',
                        ),
                ),
        ),
    'backpack' =>
        array(
            'data' =>
                array(
                    'classes' => 'ft ft-backpack',
                ),
        ),
    'bar-chart' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-bar-chart',
                ),
        ),
    'bars' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-bars',
                ),
        ),
    'blended' =>
        array(
            'data' =>
                array(
                    'classes' => 'ft ft-blended',
                ),
        ),
    'block' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-ban',
                ),
        ),
    'block_to_dock' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-caret-square-o-left',
                ),
        ),
    'bold' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-bold',
                ),
        ),
    'bookmark-o' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-bookmark-o',
                ),
        ),
    'box-alt' =>
        array(
            'data' =>
                array(
                    'classes' => 'ft ft-box-alt',
                ),
        ),
    'box-net' =>
        array(
            'data' =>
                array(
                    'classes' => 'ft ft-box-net',
                ),
        ),
    'briefcase' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-briefcase',
                ),
        ),
    'bullhorn' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-bullhorn',
                ),
        ),
    'cache' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-bolt',
                ),
        ),
    'calculator' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-calculator',
                ),
        ),
    'calculator-off' =>
        array(
            'template' => 'core/flex_icon_stack',
            'data' =>
                array(
                    'classes' =>
                        array(
                            'stack_first' => 'fa fa-calculator ft-stack-main',
                            'stack_second' => 'ft ft-slash ft-stack-over ft-state-danger',
                        ),
                ),
        ),
    'calendar' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-calendar',
                ),
        ),
    'caret-down' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-caret-down',
                ),
        ),
    'caret-left' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-caret-left',
                ),
        ),
    'caret-left-disabled' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-caret-left ft-state-disabled',
                ),
        ),
    'caret-left-info' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-caret-left ft-state-info',
                ),
        ),
    'caret-right' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-caret-right',
                ),
        ),
    'caret-right-disabled' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-caret-right ft-state-disabled',
                ),
        ),
    'caret-right-info' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-caret-right ft-state-info',
                ),
        ),
    'caret-up' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-caret-up',
                ),
        ),
    'certificate' =>
        array(
            'data' =>
                array(
                    'classes' => 'ft ft-certificate',
                ),
        ),
    'chart-bar' =>
        array(
            'data' =>
                array(
                    'classes' => 'ft ft-chart-bar',
                ),
        ),
    'check' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-check',
                ),
        ),
    'check-circle-o' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-check-circle-o',
                ),
        ),
    'check-circle-o-success' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-check-circle-o ft-state-success',
                ),
        ),
    'check-circle-success' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-check-circle ft-state-success',
                ),
        ),
    'check-disabled' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-check ft-state-disabled',
                ),
        ),
    'check-permissions' =>
        array(
            'template' => 'core/flex_icon_stack',
            'data' =>
                array(
                    'classes' =>
                        array(
                            'stack_first' => 'fa fa-user ft-stack-main',
                            'stack_second' => 'fa fa-warning ft-stack-suffix ft-state-warning',
                        ),
                ),
        ),
    'check-square-o' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-check-square-o',
                ),
        ),
    'check-success' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-check ft-state-success',
                ),
        ),
    'check-warning' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-check ft-state-warning',
                ),
        ),
    'checklist' =>
        array(
            'data' =>
                array(
                    'classes' => 'ft ft-checklist',
                ),
        ),
    'circle-danger' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-circle ft-state-danger',
                ),
        ),
    'circle-disabled' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-circle ft-state-disabled',
                ),
        ),
    'circle-o' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-circle-o',
                ),
        ),
    'circle-success' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-circle ft-state-success',
                ),
        ),
    'clock-locked' =>
        array(
            'template' => 'core/flex_icon_stack',
            'data' =>
                array(
                    'classes' =>
                        array(
                            'stack_first' => 'fa fa-clock-o ft-stack-main',
                            'stack_second' => 'fa fa-lock ft-stack-suffix ft-state-danger',
                        ),
                ),
        ),
    'clock-o' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-clock-o',
                ),
        ),
    'code' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-code',
                ),
        ),
    'code-risk' =>
        array(
            'template' => 'core/flex_icon_stack',
            'data' =>
                array(
                    'classes' =>
                        array(
                            'stack_first' => 'fa fa-code ft-stack-main',
                            'stack_second' => 'fa fa-warning ft-stack-suffix ft-state-danger',
                        ),
                ),
        ),
    'cog' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-cog',
                ),
        ),
    'cog-lock' =>
        array(
            'template' => 'core/flex_icon_stack',
            'data' =>
                array(
                    'classes' =>
                        array(
                            'stack_first' => 'fa fa-cog ft-stack-main',
                            'stack_second' => 'fa fa-lock ft-stack-suffix ft-state-danger',
                        ),
                ),
        ),
    'cogs-risk' =>
        array(
            'template' => 'core/flex_icon_stack',
            'data' =>
                array(
                    'classes' =>
                        array(
                            'stack_first' => 'fa fa-cogs ft-stack-main',
                            'stack_second' => 'fa fa-warning ft-stack-suffix ft-state-danger',
                        ),
                ),
        ),
    'columns' =>
        array(
            'data' =>
                array(
                    'classes' => 'ft ft-columns',
                ),
        ),
    'comment' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-comment',
                ),
        ),
    'comment-o' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-comment-o',
                ),
        ),
    'commenting-info' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-commenting ft-state-info',
                ),
        ),
    'comments' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-comments',
                ),
        ),
    'comments-o' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-comments-o',
                ),
        ),
    'competency' =>
        array(
            'data' =>
                array(
                    'classes' => 'ft ft-competency',
                ),
        ),
    'competency-achieved' =>
        array(
            'template' => 'core/flex_icon_stack',
            'data' =>
                array(
                    'classes' =>
                        array(
                            'stack_first' => 'ft ft-competency ft-stack-main',
                            'stack_second' => 'fa fa-check ft-stack-suffix ft-state-success',
                        ),
                ),
        ),
    'contact-add' =>
        array(
            'template' => 'core/flex_icon_stack',
            'data' =>
                array(
                    'classes' =>
                        array(
                            'stack_first' => 'ft ft-address-book ft-stack-main',
                            'stack_second' => 'fa fa-plus ft-stack-suffix ft-state-info',
                        ),
                ),
        ),
    'contact-remove' =>
        array(
            'template' => 'core/flex_icon_stack',
            'data' =>
                array(
                    'classes' =>
                        array(
                            'stack_first' => 'ft ft-address-book ft-stack-main',
                            'stack_second' => 'fa fa-minus ft-stack-suffix ft-state-danger',
                        ),
                ),
        ),
    'copy' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-copy',
                ),
        ),
    'course-completed' =>
        array(
            'template' => 'core/flex_icon_stack',
            'data' =>
                array(
                    'classes' =>
                        array(
                            'stack_first' => 'fa fa-cube ft-stack-main',
                            'stack_second' => 'fa fa-check ft-stack-suffix ft-state-success',
                        ),
                ),
        ),
    'course-event' =>
        array(
            'template' => 'core/flex_icon_stack',
            'data' =>
                array(
                    'classes' =>
                        array(
                            'stack_first' => 'fa fa-cube ft-stack-main',
                            'stack_second' => 'fa fa-clock-o ft-stack-suffix',
                        ),
                ),
        ),
    'course-started' =>
        array(
            'template' => 'core/flex_icon_stack',
            'data' =>
                array(
                    'classes' =>
                        array(
                            'stack_first' => 'fa fa-cube ft-stack-main',
                            'stack_second' => 'fa fa-play ft-stack-suffix',
                        ),
                ),
        ),
    'cube' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-cube',
                ),
        ),
    'cubes' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-cubes',
                ),
        ),
    'dash' =>
        array(
            'data' =>
                array(
                    'classes' => 'ft ft-dash',
                ),
        ),
    'database' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-database',
                ),
        ),
    'database-risk' =>
        array(
            'template' => 'core/flex_icon_stack',
            'data' =>
                array(
                    'classes' =>
                        array(
                            'stack_first' => 'fa fa-database ft-stack-main',
                            'stack_second' => 'fa fa-warning ft-stack-suffix ft-state-danger',
                        ),
                ),
        ),
    'dock_to_block' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-caret-square-o-right',
                ),
        ),
    'document-properties' =>
        array(
            'template' => 'core/flex_icon_stack',
            'data' =>
                array(
                    'classes' =>
                        array(
                            'stack_first' => 'fa fa-file-o ft-stack-main',
                            'stack_second' => 'fa fa-wrench ft-stack-suffix',
                        ),
                ),
        ),
    'dollar' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-dollar',
                ),
        ),
    'dot-circle-o' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-dot-circle-o',
                ),
        ),
    'download' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-download',
                ),
        ),
    'dropbox' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-dropbox',
                ),
        ),
    'ebook' =>
        array(
            'data' =>
                array(
                    'classes' => 'ft ft-book-alt',
                ),
        ),
    'edit' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-edit',
                ),
        ),
    'edit-document' =>
        array(
            'template' => 'core/flex_icon_stack',
            'data' =>
                array(
                    'classes' =>
                        array(
                            'stack_first' => 'fa fa-file-o ft-stack-main',
                            'stack_second' => 'fa fa-pencil ft-stack-suffix',
                        ),
                ),
        ),
    'editmenu' =>
        array(
            'template' => 'core/flex_icon_stack',
            'data' =>
                array(
                    'classes' =>
                        array(
                            'stack_first' => 'fa fa-cog ft-stack-main',
                            'stack_second' => 'fa fa-caret-down ft-stack-suffix',
                        ),
                ),
        ),
    'ellipsis-h' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-ellipsis-h',
                ),
        ),
    'enrolment-suspended' =>
        array(
            'template' => 'core/flex_icon_stack',
            'data' =>
                array(
                    'classes' =>
                        array(
                            'stack_first' => 'fa fa-user ft-stack-main',
                            'stack_second' => 'fa fa-pause ft-stack-suffix',
                        ),
                ),
        ),
    'envelope-o' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-envelope-o',
                ),
        ),
    'envelope-risk' =>
        array(
            'template' => 'core/flex_icon_stack',
            'data' =>
                array(
                    'classes' =>
                        array(
                            'stack_first' => 'fa fa-envelope ft-stack-main',
                            'stack_second' => 'fa fa-warning ft-stack-suffix ft-state-danger',
                        ),
                ),
        ),
    'exclamation-circle' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-exclamation-circle',
                ),
        ),
    'expand' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-expand',
                ),
        ),
    'explore' =>
        array(
            'template' => 'core/flex_icon_stack',
            'data' =>
                array(
                    'classes' =>
                        array(
                            'stack_first' => 'fa fa-folder-o ft-stack-main',
                            'stack_second' => 'fa fa-search ft-stack-suffix',
                        ),
                ),
        ),
    'external-link-square' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-external-link-square',
                ),
        ),
    'eye' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-eye',
                ),
        ),
    'eye-slash' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-eye-slash',
                ),
        ),
    'file-archive-o' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-file-archive-o',
                ),
        ),
    'file-code-o' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-file-code-o',
                ),
        ),
    'file-excel-o' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-file-excel-o',
                ),
        ),
    'file-image-o' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-file-image-o',
                ),
        ),
    'file-o' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-file-o',
                ),
        ),
    'file-pdf-o' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-file-pdf-o',
                ),
        ),
    'file-powerpoint-o' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-file-powerpoint-o',
                ),
        ),
    'file-sound-o' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-file-sound-o',
                ),
        ),
    'file-text' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-file-text',
                ),
        ),
    'file-text-o' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-file-text-o',
                ),
        ),
    'file-video-o' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-file-video-o',
                ),
        ),
    'file-wrench' =>
        array(
            'template' => 'core/flex_icon_stack',
            'data' =>
                array(
                    'classes' =>
                        array(
                            'stack_first' => 'fa fa-file-o ft-stack-main',
                            'stack_second' => 'fa fa-wrench ft-stack-suffix',
                        ),
                ),
        ),
    'film' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-film',
                ),
        ),
    'filter' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-filter',
                ),
        ),
    'flag' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-flag',
                ),
        ),
    'flag-o' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-flag-o',
                ),
        ),
    'flickr' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-flickr',
                ),
        ),
    'folder-create' =>
        array(
            'template' => 'core/flex_icon_stack',
            'data' =>
                array(
                    'classes' =>
                        array(
                            'stack_first' => 'fa fa-folder-o ft-stack-main',
                            'stack_second' => 'fa fa-plus ft-stack-suffix',
                        ),
                ),
        ),
    'folder-o' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-folder-o',
                ),
        ),
    'folder-open-o' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-folder-open-o',
                ),
        ),
    'font-info' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-font ft-state-info',
                ),
        ),
    'frown-o' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-frown-o',
                ),
        ),
    'gdrive' =>
        array(
            'data' =>
                array(
                    'classes' => 'ft ft-gdrive',
                ),
        ),
    'globe' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-globe',
                ),
        ),
    'grades' =>
        array(
            'data' =>
                array(
                    'classes' => 'ft ft-grades',
                ),
        ),
    'group-event' =>
        array(
            'template' => 'core/flex_icon_stack',
            'data' =>
                array(
                    'classes' =>
                        array(
                            'stack_first' => 'fa fa-users ft-stack-main',
                            'stack_second' => 'fa fa-clock-o ft-stack-suffix ft-state-info',
                        ),
                ),
        ),
    'highlight' =>
        array(
            'data' =>
                array(
                    'classes' => 'ft ft-highlight',
                ),
        ),
    'image' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-image',
                ),
        ),
    'indent' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-indent',
                ),
        ),
    'info-circle' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-info-circle',
                ),
        ),
    'italic' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-italic',
                ),
        ),
    'key' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-key',
                ),
        ),
    'laptop' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-laptop',
                ),
        ),
    'level-up' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-level-up',
                ),
        ),
    'lightbulb-o' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-lightbulb-o',
                ),
        ),
    'lightbulb-o-disabled' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-lightbulb-o ft-state-disabled',
                ),
        ),
    'line-chart' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-line-chart',
                ),
        ),
    'link' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-link',
                ),
        ),
    'list-alt' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-list-alt',
                ),
        ),
    'list-ol' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-list-ol',
                ),
        ),
    'list-ul' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-list-ul',
                ),
        ),
    'lock' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-lock',
                ),
        ),
    'log' =>
        array(
            'data' =>
                array(
                    'classes' => 'ft ft-hbars',
                ),
        ),
    'mahara' =>
        array(
            'data' =>
                array(
                    'classes' => 'ft ft-mahara',
                ),
        ),
    'manual-completion-on' =>
        array(
            'template' => 'core/flex_icon_stack',
            'data' =>
                array(
                    'classes' =>
                        array(
                            'stack_first' => 'fa fa-check-square-o ft-stack-main',
                            'stack_second' => 'fa fa-play ft-stack-suffix',
                        ),
                ),
        ),
    'mean' =>
        array(
            'data' =>
                array(
                    'classes' => 'ft ft-mean',
                ),
        ),
    'minus' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-minus',
                ),
        ),
    'minus-square' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-minus-square',
                ),
        ),
    'minus-square-o' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-minus-square-o',
                ),
        ),
    'mnet-host' =>
        array(
            'data' =>
                array(
                    'classes' => 'ft ft-mnethost',
                ),
        ),
    'moodle' =>
        array(
            'data' =>
                array(
                    'classes' => 'ft ft-moodle',
                ),
        ),
    'moon-o' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-moon-o',
                ),
        ),
    'mouse-pointer' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-mouse-pointer',
                ),
        ),
    'new' =>
        array(
            'data' =>
                array(
                    'classes' => 'ft ft-new',
                ),
        ),
    'new-document' =>
        array(
            'template' => 'core/flex_icon_stack',
            'data' =>
                array(
                    'classes' =>
                        array(
                            'stack_first' => 'fa fa-file-o ft-stack-main',
                            'stack_second' => 'fa fa-plus ft-stack-suffix',
                        ),
                ),
        ),
    'newspaper-o' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-newspaper-o',
                ),
        ),
    'no-email' =>
        array(
            'template' => 'core/flex_icon_stack',
            'data' =>
                array(
                    'classes' =>
                        array(
                            'stack_first' => 'fa fa-envelope-o ft-stack-main',
                            'stack_second' => 'ft ft-slash ft-stack-over ft-state-danger',
                        ),
                ),
        ),
    'no-groups' =>
        array(
            'template' => 'core/flex_icon_stack',
            'data' =>
                array(
                    'classes' =>
                        array(
                            'stack_first' => 'fa fa-users ft-stack-main',
                            'stack_second' => 'ft ft-slash ft-stack-main ft-state-danger',
                        ),
                ),
        ),
    'no-key' =>
        array(
            'template' => 'core/flex_icon_stack',
            'data' =>
                array(
                    'classes' =>
                        array(
                            'stack_first' => 'fa fa-key ft-stack-main',
                            'stack_second' => 'ft ft-slash ft-stack-over ft-state-danger',
                        ),
                ),
        ),
    'no-subcategory' =>
        array(
            'template' => 'core/flex_icon_stack',
            'data' =>
                array(
                    'classes' =>
                        array(
                            'stack_first' => 'ft ft-viewtreeactive ft-stack-main',
                            'stack_second' => 'ft ft-slash ft-stack-over ft-state-danger',
                        ),
                ),
        ),
    'objective' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-bullseye',
                ),
        ),
    'objective-achieved' =>
        array(
            'template' => 'core/flex_icon_stack',
            'data' =>
                array(
                    'classes' =>
                        array(
                            'stack_first' => 'fa fa-bullseye ft-stack-main',
                            'stack_second' => 'fa fa-check ft-stack-suffix ft-state-success',
                        ),
                ),
        ),
    'outdent' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-outdent',
                ),
        ),
    'package' =>
        array(
            'data' =>
                array(
                    'classes' => 'ft ft-package',
                ),
        ),
    'paperclip' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-paperclip',
                ),
        ),
    'paragraph' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-paragraph',
                ),
        ),
    'paste' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-paste',
                ),
        ),
    'paypal' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-paypal',
                ),
        ),
    'pencil' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-pencil',
                ),
        ),
    'pencil-square-info' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-pencil-square ft-state-info',
                ),
        ),
    'pencil-square-o' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-pencil-square-o',
                ),
        ),
    'permission-lock' =>
        array(
            'template' => 'core/flex_icon_stack',
            'data' =>
                array(
                    'classes' =>
                        array(
                            'stack_first' => 'fa fa-user ft-stack-main',
                            'stack_second' => 'fa fa-lock ft-stack-suffix ft-state-danger',
                        ),
                ),
        ),
    'permissions' =>
        array(
            'template' => 'core/flex_icon_stack',
            'data' =>
                array(
                    'classes' =>
                        array(
                            'stack_first' => 'fa fa-user ft-stack-main',
                            'stack_second' => 'fa fa-key ft-stack-suffix ft-state-info',
                        ),
                ),
        ),
    'picasa' =>
        array(
            'data' =>
                array(
                    'classes' => 'ft ft-picasa',
                ),
        ),
    'pie-chart' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-pie-chart',
                ),
        ),
    'pipe' =>
        array(
            'data' =>
                array(
                    'classes' => 'ft ft-pipe',
                ),
        ),
    'plug' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-plug',
                ),
        ),
    'plus' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-plus',
                ),
        ),
    'plus-circle-info' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-plus-circle ft-state-info',
                ),
        ),
    'plus-square' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-plus-square',
                ),
        ),
    'plus-square-o' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-plus-square-o',
                ),
        ),
    'portfolio' =>
        array(
            'data' =>
                array(
                    'classes' => 'ft ft-profile',
                ),
        ),
    'portfolio-add' =>
        array(
            'template' => 'core/flex_icon_stack',
            'data' =>
                array(
                    'classes' =>
                        array(
                            'stack_first' => 'ft ft-profile ft-stack-main',
                            'stack_second' => 'fa fa-plus ft-stack-suffix',
                        ),
                ),
        ),
    'prevent-autolink' =>
        array(
            'template' => 'core/flex_icon_stack',
            'data' =>
                array(
                    'classes' =>
                        array(
                            'stack_first' => 'fa fa-link ft-stack-main',
                            'stack_second' => 'ft ft-slash ft-stack-over ft-state-danger',
                        ),
                ),
        ),
    'print' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-print',
                ),
        ),
    'publish' =>
        array(
            'template' => 'core/flex_icon_stack',
            'data' =>
                array(
                    'classes' =>
                        array(
                            'stack_first' => 'fa fa-globe ft-stack-main',
                            'stack_second' => 'fa fa-play ft-stack-suffix ft-state-info',
                        ),
                ),
        ),
    'puzzle-piece' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-puzzle-piece',
                ),
        ),
    'question' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-question',
                ),
        ),
    'question-circle' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-question-circle',
                ),
        ),
    'question-circle-warning' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-question-circle ft-state-warning',
                ),
        ),
    'quote-left' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-quote-left',
                ),
        ),
    'ranges' =>
        array(
            'data' =>
                array(
                    'classes' => 'ft ft-vbars',
                ),
        ),
    'recycle' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-recycle',
                ),
        ),
    'refresh' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-refresh',
                ),
        ),
    'repeat' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-repeat',
                ),
        ),
    'restore-draft' =>
        array(
            'template' => 'core/flex_icon_stack',
            'data' =>
                array(
                    'classes' =>
                        array(
                            'stack_first' => 'fa fa-file-o ft-stack-main',
                            'stack_second' => 'fa fa-undo ft-stack-suffix',
                        ),
                ),
        ),
    'rss' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-rss',
                ),
        ),
    'save' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-save',
                ),
        ),
    'scales' =>
        array(
            'data' =>
                array(
                    'classes' => 'ft ft-stats-bars',
                ),
        ),
    'scissors' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-scissors',
                ),
        ),
    'search' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-search',
                ),
        ),
    'search-comments' =>
        array(
            'template' => 'core/flex_icon_stack',
            'data' =>
                array(
                    'classes' =>
                        array(
                            'stack_first' => 'fa fa-comment ft-stack-main',
                            'stack_second' => 'fa fa-search ft-stack-suffix',
                        ),
                ),
        ),
    'seminar' =>
        array(
            'data' =>
                array(
                    'classes' => 'ft ft-seminar',
                ),
        ),
    'separate-groups' =>
        array(
            'template' => 'core/flex_icon_stack',
            'data' =>
                array(
                    'classes' =>
                        array(
                            'stack_first' => 'fa fa-users ft-stack-prefix',
                            'stack_second' => 'fa fa-users ft-stack-suffix',
                        ),
                ),
        ),
    'share-square-o' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-share-square-o',
                ),
        ),
    'shield-risk' =>
        array(
            'template' => 'core/flex_icon_stack',
            'data' =>
                array(
                    'classes' =>
                        array(
                            'stack_first' => 'fa fa-shield ft-stack-main',
                            'stack_second' => 'fa fa-warning ft-stack-suffix ft-state-danger',
                        ),
                ),
        ),
    'sigma' =>
        array(
            'data' =>
                array(
                    'classes' => 'ft ft-sigma',
                ),
        ),
    'sigma-plus' =>
        array(
            'template' => 'core/flex_icon_stack',
            'data' =>
                array(
                    'classes' =>
                        array(
                            'stack_first' => 'ft ft-sigma ft-stack-main',
                            'stack_second' => 'fa fa-plus ft-stack-suffix',
                        ),
                ),
        ),
    'sign-out' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-sign-out',
                ),
        ),
    'site-lock' =>
        array(
            'template' => 'core/flex_icon_stack',
            'data' =>
                array(
                    'classes' =>
                        array(
                            'stack_first' => 'fa fa-sitemap ft-stack-main',
                            'stack_second' => 'fa fa-lock ft-stack-suffix ft-state-danger',
                        ),
                ),
        ),
    'skyatlas' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-skyatlas',
                ),
        ),
    'slash' =>
        array(
            'data' =>
                array(
                    'classes' => 'ft ft-slash',
                ),
        ),
    'sliders' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-sliders',
                ),
        ),
    'smile-o' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-smile-o',
                ),
        ),
    'sort' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-sort',
                ),
        ),
    'sort-asc' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-sort-asc',
                ),
        ),
    'sort-desc' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-sort-desc',
                ),
        ),
    'spacer' =>
        array(
            'template' => 'core/flex_icon_spacer',
        ),
    'spinner-pulse' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-spinner fa-pulse',
                ),
        ),
    'square-o' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-square-o',
                ),
        ),
    'stamp' =>
        array(
            'data' =>
                array(
                    'classes' => 'ft ft-stamp',
                ),
        ),
    'star' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-star',
                ),
        ),
    'star-half-o' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-star-half-o',
                ),
        ),
    'star-o' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-star-o',
                ),
        ),
    'strikethrough' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-strikethrough',
                ),
        ),
    'subscribed' =>
        array(
            'template' => 'core/flex_icon_stack',
            'data' =>
                array(
                    'classes' =>
                        array(
                            'stack_first' => 'fa fa-envelope-o ft-stack-main',
                            'stack_second' => 'fa fa-check ft-stack-suffix ft-state-success',
                        ),
                ),
        ),
    'subscript' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-subscript',
                ),
        ),
    'superscript' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-superscript',
                ),
        ),
    'table' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-table',
                ),
        ),
    'tag' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-tag',
                ),
        ),
    'text-color-picker' =>
        array(
            'template' => 'core/flex_icon_stack',
            'data' =>
                array(
                    'classes' =>
                        array(
                            'stack_first' => 'fa fa-font ft-stack-main',
                            'stack_second' => 'fa fa-eyedropper ft-stack-suffix',
                        ),
                ),
        ),
    'th-large' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-th-large',
                ),
        ),
    'thumbs-down-danger' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-thumbs-down ft-state-danger',
                ),
        ),
    'thumbs-up-success' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-thumbs-up ft-state-success',
                ),
        ),
    'times-circle-danger' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-times-circle ft-state-danger',
                ),
        ),
    'times-circle-o' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-times-circle-o',
                ),
        ),
    'times-circle-o-danger' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-times-circle-o ft-state-danger',
                ),
        ),
    'times-danger' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-times ft-state-danger',
                ),
        ),
    'times-disabled' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-times ft-state-disabled',
                ),
        ),
    'toggle-on' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-toggle-on',
                ),
        ),
    'trash' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-trash',
                ),
        ),
    'trophy' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-trophy',
                ),
        ),
    'underline' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-underline',
                ),
        ),
    'undo' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-undo',
                ),
        ),
    'unlink' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-unlink',
                ),
        ),
    'unlock' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-unlock',
                ),
        ),
    'unlock-alt' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-unlock-alt',
                ),
        ),
    'unsubscribed' =>
        array(
            'template' => 'core/flex_icon_stack',
            'data' =>
                array(
                    'classes' =>
                        array(
                            'stack_first' => 'fa fa-envelope-o ft-stack-main',
                            'stack_second' => 'fa fa-times ft-stack-suffix ft-state-danger',
                        ),
                ),
        ),
    'upload' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-upload',
                ),
        ),
    'user' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-user',
                ),
        ),
    'user-disabled' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-user ft-state-disabled',
                ),
        ),
    'user-event' =>
        array(
            'template' => 'core/flex_icon_stack',
            'data' =>
                array(
                    'classes' =>
                        array(
                            'stack_first' => 'fa fa-user ft-stack-main',
                            'stack_second' => 'fa fa-clock-o ft-stack-suffix ft-state-info',
                        ),
                ),
        ),
    'user-plus' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-user-plus',
                ),
        ),
    'user-refresh' =>
        array(
            'template' => 'core/flex_icon_stack',
            'data' =>
                array(
                    'classes' =>
                        array(
                            'stack_first' => 'fa fa-user ft-stack-main',
                            'stack_second' => 'fa fa-refresh ft-stack-suffix ft-state-info',
                        ),
                ),
        ),
    'user-risk' =>
        array(
            'template' => 'core/flex_icon_stack',
            'data' =>
                array(
                    'classes' =>
                        array(
                            'stack_first' => 'fa fa-user ft-stack-main',
                            'stack_second' => 'fa fa-warning ft-stack-suffix ft-state-danger',
                        ),
                ),
        ),
    'user-secret' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-user-secret',
                ),
        ),
    'user-times' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-user-times',
                ),
        ),
    'users' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-users',
                ),
        ),
    'viewtreeactive' =>
        array(
            'data' =>
                array(
                    'classes' => 'ft ft-viewtreeactive',
                ),
        ),
    'volume-up' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-volume-up',
                ),
        ),
    'warning-warning' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-warning ft-state-warning',
                ),
        ),
    'wheelchair' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-wheelchair',
                ),
        ),
    'wikipedia-w' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-wikipedia-w',
                ),
        ),
    'youtube-play' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-youtube-play',
                ),
        ),
);

/*
 * List of deprecated translations that will be removed in the next release.
 */
$deprecated = array(
);

/*
 * Default values for the all $map arrays from all plugins, core and themes.
 * This can be overridden in themes only.
 */
$defaults = array(
    'template' => 'core/flex_icon',
);
