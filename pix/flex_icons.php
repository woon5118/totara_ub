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
 * Default values for the all $map arrays from all plugins, core and themes.
 * This can be overridden in themes only.
 */
$defaults = array(
    'template' => 'core/flex_icon',
);

/*
 * Translations array is expected to be used in plugins pix/flex_icons.php only.
 *
 * The data format is: array('mod_xxxx|someicon' => 'mapidentifier', 'mod_xxxx|otehricon' => 'mapidentifierx')
 */
$translations = array(
    // NOTE: do not add anything here in core, use the $map instead!
);

/*
 * Font icon map - this definition tells us how is each icon constructed.
 *
 * The identifiers in this core map are expected to be general
 * shape descriptions not directly related to Totara.
 *
 * In plugins the map is used when plugin needs a completely new icon
 * that is not defined here in core.
 */
$map = array(
    /* Do not use 'flex-icon-missing' directly, it indicates requested icon was not found */
    'flex-icon-missing' =>
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
    'ban' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-ban',
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
    'block_to_dock' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-caret-square-o-left',
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
    'cog' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-cog',
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
    'comment-add-o' =>
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
    'comments-search' =>
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
    /* Totara course */
    'course' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-cube',
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
    /* General delete icon to be used for all delete actions */
    'delete' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-times ft-state-danger',
                ),
        ),
    'dock_to_block' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-caret-square-o-right',
                ),
        ),
    'document-edit' =>
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
    'document-new' =>
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
    'event-course' =>
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
    'event-group' =>
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
    'event-user' =>
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
    /* For links to Totara help */
    'help' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-question-circle',
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
    'new' => // Something recently added.
        array(
            'data' =>
                array(
                    'classes' => 'ft ft-new',
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
    'print' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-print',
                ),
        ),
    /* Totara program */
    'program' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-cubes',
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
    'risk-config' =>
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
    'risk-dataloss' =>
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
    'risk-managetrust' =>
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
    'risk-personal' =>
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
    'risk-spam' =>
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
    'risk-xss' =>
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
    'rows' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-bars',
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
    /* Settings or editing of stuff that changes how Totara works */
    'settings' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-cog',
                ),
        ),
    'settings-lock' =>
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
    'share-square-o' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-share-square-o',
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
    'th-large' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-th-large',
                ),
        ),
    'th-list' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-th-list',
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
    'toggle-off' =>
        array(
            'data' =>
                array(
                    'classes' => 'fa fa-toggle-off',
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
 * Translation of old pix icon names to flex icon identifiers.
 *
 * The old core pix icon name format is "core|originalpixpath"
 * similar to the old pix placeholders in CSS.
 *
 * This information allows the pix_icon renderer
 * to automatically return the new flex icon markup.
 *
 * All referenced identifiers must be present in the $map.
 *
 * Note that plugins are using the same identifier format
 * for $translations, $deprecated and $map "plugintype_pluginname|icon".
 */
$deprecated = array(
    'core|a/add_file' => 'document-new',
    'core|a/create_folder' => 'folder-create',
    'core|a/download_all' => 'download',
    'core|a/help' => 'help',
    'core|a/logout' => 'sign-out',
    'core|a/refresh' => 'refresh',
    'core|a/search' => 'search',
    'core|a/setting' => 'settings',
    'core|a/view_icon_active' => 'th-large',
    'core|a/view_list_active' => 'th-list',
    'core|a/view_tree_active' => 'viewtreeactive',
    'core|b/bookmark-new' => 'bookmark-o',
    'core|b/document-edit' => 'document-edit',
    'core|b/document-new' => 'document-new',
    'core|b/document-properties' => 'document-properties',
    'core|b/edit-copy' => 'copy',
    'core|b/edit-delete' => 'delete',
    'core|c/event' => 'calendar',
    'core|docs' => 'info-circle',
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
    'core|f/help-32' => 'help',
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
    'core|help' => 'help',
    'core|i/admin' => 'settings',
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
    'core|i/configlock' => 'settings-lock',
    'core|i/course' => 'course',
    'core|i/courseevent' => 'event-course',
    'core|i/db' => 'database',
    'core|i/delete' => 'delete',
    'core|i/down' => 'arrow-down',
    'core|i/dragdrop' => 'arrows',
    'core|i/dropdown' => 'caret-down',
    'core|i/edit' => 'edit',
    'core|i/email' => 'envelope-o',
    'core|i/enrolmentsuspended' => 'enrolment-suspended',
    'core|i/enrolusers' => 'user-plus',
    'core|i/export' => 'upload',
    'core|i/feedback' => 'comment-o',
    'core|i/feedback_add' => 'comment-add-o',
    'core|i/files' => 'file-o',
    'core|i/filter' => 'filter',
    'core|i/flagged' => 'flag',
    'core|i/folder' => 'folder-o',
    'core|i/grade_correct' => 'check-success',
    'core|i/grade_incorrect' => 'times-danger',
    'core|i/grade_partiallycorrect' => 'check-warning',
    'core|i/grades' => 'grades',
    'core|i/group' => 'users',
    'core|i/groupevent' => 'event-group',
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
    'core|i/risk_config' => 'risk-config',
    'core|i/risk_dataloss' => 'risk-dataloss',
    'core|i/risk_managetrust' => 'risk-managetrust',
    'core|i/risk_personal' => 'risk-personal',
    'core|i/risk_spam' => 'risk-spam',
    'core|i/risk_xss' => 'risk-xss',
    'core|i/rss' => 'rss',
    'core|i/scales' => 'scales',
    'core|i/scheduled' => 'clock-o',
    'core|i/search' => 'search',
    'core|i/self' => 'user',
    'core|i/settings' => 'settings',
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
    'core|i/userevent' => 'event-user',
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
    'core|t/addfile' => 'document-new',
    'core|t/approve' => 'check',
    'core|t/assignroles' => 'user-plus',
    'core|t/award' => 'trophy',
    'core|t/backpack' => 'backpack',
    'core|t/backup' => 'upload',
    'core|t/block' => 'ban',
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
    'core|t/delete' => 'delete',
    'core|t/disable_down' => 'arrow-down',
    'core|t/disable_up' => 'arrow-up',
    'core|t/dock_to_block' => 'dock_to_block',
    'core|t/dock_to_block_rtl' => 'block_to_dock',
    'core|t/dockclose' => 'times-circle-o',
    'core|t/down' => 'arrow-down',
    'core|t/download' => 'download',
    'core|t/dropdown' => 'caret-down',
    'core|t/edit' => 'settings',
    'core|t/edit_gray' => 'edit',
    'core|t/edit_menu' => 'editmenu',
    'core|t/editstring' => 'edit',
    'core|t/email' => 'envelope-o',
    'core|t/emailno' => 'no-email',
    'core|t/enroladd' => 'plus',
    'core|t/enrolusers' => 'user-plus',
    'core|t/expanded' => 'caret-down',
    'core|t/feedback' => 'comment-o',
    'core|t/feedback_add' => 'comment-add-o',
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
 * Pix only images are not supposed to be converted to flex icons.
 *
 * - e/xxx pix icons should be used by Atto and TinyMCE editors that do not support flex icons
 *
 */
$pixonlyimages = array(
    'e/abbr',
    'e/absolute',
    'e/accessibility_checker',
    'e/acronym',
    'e/advance_hr',
    'e/align_center',
    'e/align_left',
    'e/align_right',
    'e/anchor',
    'e/backward',
    'e/bold',
    'e/bullet_list',
    'e/cell_props',
    'e/cite',
    'e/cleanup_messy_code',
    'e/clear_formatting',
    'e/copy',
    'e/cut',
    'e/decrease_indent',
    'e/delete',
    'e/delete_col',
    'e/delete_row',
    'e/delete_table',
    'e/document_properties',
    'e/emoticons',
    'e/find_replace',
    'e/forward',
    'e/fullpage',
    'e/fullscreen',
    'e/help',
    'e/increase_indent',
    'e/insert',
    'e/insert_col_after',
    'e/insert_col_before',
    'e/insert_date',
    'e/insert_edit_image',
    'e/insert_edit_link',
    'e/insert_edit_video',
    'e/insert_file',
    'e/insert_horizontal_ruler',
    'e/insert_nonbreaking_space',
    'e/insert_page_break',
    'e/insert_row_after',
    'e/insert_row_before',
    'e/insert_time',
    'e/italic',
    'e/justify',
    'e/layers',
    'e/layers_over',
    'e/layers_under',
    'e/left_to_right',
    'e/manage_files',
    'e/math',
    'e/merge_cells',
    'e/new_document',
    'e/numbered_list',
    'e/page_break',
    'e/paste',
    'e/paste_text',
    'e/paste_word',
    'e/prevent_autolink',
    'e/preview',
    'e/print',
    'e/question',
    'e/redo',
    'e/remove_link',
    'e/remove_page_break',
    'e/resize',
    'e/restore_draft',
    'e/restore_last_draft',
    'e/right_to_left',
    'e/row_props',
    'e/save',
    'e/screenreader_helper',
    'e/search',
    'e/select_all',
    'e/show_invisible_characters',
    'e/source_code',
    'e/special_character',
    'e/spellcheck',
    'e/split_cells',
    'e/strikethrough',
    'e/styleprops',
    'e/subscript',
    'e/superscript',
    'e/table',
    'e/table_props',
    'e/template',
    'e/text_color',
    'e/text_color_picker',
    'e/text_highlight',
    'e/text_highlight_picker',
    'e/tick',
    'e/toggle_blockquote',
    'e/underline',
    'e/undo',
    'e/visual_aid',
    'e/visual_blocks',
);
