<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2014 onwards Totara Learning Solutions LTD
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
 * @author Petr Skoda <petr.skoda@totaralms.com>
 * @package totara
 * @subpackage totara_core
 */

/**
 * Fix incorrectly upgraded text columns.
 */
function totara_core_fix_old_upgraded_mssql() {
    global $CFG, $DB, $OUTPUT;

    if ($DB->get_dbfamily() !== 'mssql') {
        return;
    }

    $dbman = $DB->get_manager();

    // Changing the default of field laststatus on table backup_courses to 5.
    $table = new xmldb_table('backup_courses');
    $field = new xmldb_field('laststatus', XMLDB_TYPE_CHAR, '1', null, XMLDB_NOTNULL, null, '5', 'lastendtime');
    $dbman->change_field_default($table, $field);

    // All these text columns should be NOT NULL.
    $candidates = array(
        'appraisal_event_message' => array('content'),
        'assign' => array('intro'),
        'badge' => array('message', 'messagesubject'),
        'badge_issued' => array('message', 'uniquehash'),
        'facetoface_notification' => array('body'),
        'facetoface_notification_tpl' => array('body'),
        'feedback_value_history' => array('value'),
        'goal_scale_values' => array('name'),
        'qtype_randomsamatch_options' => array('correctfeedback', 'partiallycorrectfeedback', 'incorrectfeedback'),
        'config' => array('value'),
        'config_plugins' => array('value'),
        'course_request' => array('summary', 'reason'),
        'event' => array('description', 'name'),
        'cache_filters' => array('rawtext'),
        'cache_text' => array('formattedtext'),
        'log_queries' => array('sqltext'),
        'scale' => array('scale', 'description'),
        'scale_history' => array('scale', 'description'),
        'role' => array('description'),
        'user_info_field' => array('name'),
        'user_info_data' => array('data'),
        'question_categories' => array('info'),
        'question' => array('questiontext', 'generalfeedback'),
        'question_answers' => array('answer', 'feedback'),
        'question_hints' => array('hint'),
        'question_states' => array('answer'),
        'question_sessions' => array('manualcomment'),
        'mnet_host' => array('public_key'),
        'mnet_rpc' => array('help', 'profile'),
        'events_queue' => array('eventdata'),
        'grade_outcomes' => array('fullname'),
        'grade_outcomes_history' => array('fullname'),
        'tag_correlation' => array('correlatedtags'),
        'cache_flags' => array('value'),
        'comments' => array('content'),
        'blog_external' => array('url'),
        'backup_controllers' => array('controller'),
        'profiling' => array('data'),
        'qtype_match_options' => array('correctfeedback', 'partiallycorrectfeedback', 'incorrectfeedback'),
        'qtype_match_subquestions' => array('questiontext'),
        'question_multianswer' => array('sequence'),
        'qtype_multichoice_options' => array('correctfeedback', 'partiallycorrectfeedback', 'incorrectfeedback'),
        'book_chapters' => array('content'),
        'chat' => array('intro'),
        'chat_messages' => array('message'),
        'chat_messages_current' => array('message'),
        'choice' => array('intro'),
        'data' => array('intro'),
        'data_fields' => array('description'),
        'feedback' => array('intro', 'page_after_submit'),
        'feedback_item' => array('presentation'),
        'feedback_value' => array('value'),
        'feedback_valuetmp' => array('value'),
        'forum' => array('intro'),
        'forum_posts' => array('message'),
        'glossary' => array('intro'),
        'glossary_entries' => array('definition'),
        'label' => array('intro'),
        'lesson' => array('conditions'),
        'lesson_pages' => array('contents'),
        'lti' => array('toolurl'),
        'lti_types' => array('baseurl'),
        'quiz' => array('intro', 'questions'),
        'quiz_attempts' => array('layout'),
        'quiz_feedback' => array('feedbacktext'),
        'resource_old' => array('alltext', 'popup'),
        'scorm' => array('intro'),
        'scorm_scoes' => array('launch'),
        'scorm_scoes_data' => array('value'),
        'scorm_scoes_track' => array('value'),
        'survey' => array('intro'),
        'survey_answers' => array('answer1', 'answer2'),
        'survey_analysis' => array('notes'),
        'url' => array('externalurl'),
        'wiki_pages' => array('cachedcontent'),
        'wiki_versions' => array('content'),
        'block_rss_client' => array('title', 'description'),
        'block_quicklinks' => array('title'),
        'block_totara_stats' => array('data'),
        'mnetservice_enrol_courses' => array('summary'),
        'course_info_field' => array('fullname'),
        'errorlog' => array('details'),
        'comp_scale_values' => array('name'),
        'comp_template' => array('fullname'),
        'dp_priority_scale' => array('description'),
        'prog_message' => array('mainmessage'),
        'tool_customlang' => array('original'),
    );

    $totalcount = 0;
    foreach ($candidates as $table => $columns) {
        if (!$dbman->table_exists($table)) {
            unset($candidates[$table]);
            continue;
        }
        foreach ($columns as $column) {
            $totalcount++;
        }
    }

    $pbar = new progress_bar('mssqlfixextnulls', 500, true);

    $i = 0;
    foreach ($candidates as $table => $columns) {
        $existingcolumns = $DB->get_columns($table);
        foreach ($columns as $column) {
            if (isset($existingcolumns[$column])) {
                /** @var database_column_info $existing */
                $existing = $existingcolumns[$column];
                if ($existing->meta_type === 'X' and !$existing->not_null) {
                    $DB->execute("UPDATE {{$table}} SET $column = '' WHERE $column IS NULL");
                    $xmldbtable = new xmldb_table($table);
                    $xmldbcolumn = new xmldb_field($column, XMLDB_TYPE_TEXT, 'big', null, XMLDB_NOTNULL);
                    $dbman->change_field_notnull($xmldbtable, $xmldbcolumn);
                }
            }
            $i++;
            $pbar->update($i, $totalcount, "Fixed text columns in MS SQL database - $i/$totalcount.");
        }
    }
}

/**
 * Change context cleanup task scheduling from every hour to once a day in off peak hour.
 *
 * NOTE: it is safe to call this repeatedly.
 */
function totara_upgrade_context_task_timing() {
    global $DB;

    // Add new and remove delted core tasks,
    // this removes create_contexts_task before we bump up the main version.
    \core\task\manager::reset_scheduled_tasks_for_component('moodle');

    $task = $DB->get_record('task_scheduled', ['classname' => '\core\task\context_cleanup_task', 'component' => 'moodle']);
    if (!$task) {
        return;
    }

    if ($task->minute == 25 and $task->hour == '*' and $task->day == '*' and $task->customised == 0) {
        $task->minute = 23;
        $task->hour = 23;
        $DB->update_record('task_scheduled', $task);
    }
}

/**
 * Re-add changes to course completion for Totara
 *
 * Although these exist in lib/db/upgrade.php, anyone upgrading from Moodle 2.2.2 or above
 * would already have a higher version number so we need to apply them again:
 *
 * 1. when totara first is installed (to fix for anyone upgrading from 2.2.2+)
 * 2. in a totara core upgrade (to fix for anyone who has already upgraded from 2.2.2+)
 *
 * These changes will only be applied if they haven't been run previously so it's okay
 * to call this function multiple times
 */
function totara_readd_course_completion_changes() {
    global $CFG, $DB;
    $dbman = $DB->get_manager();

    // Define index useridcourse (unique) to be added to course_completions
    $table = new xmldb_table('course_completions');
    $index = new xmldb_index('useridcourse', XMLDB_INDEX_UNIQUE, array('userid', 'course'));

    // Conditionally launch add index useridcourse
    if (!$dbman->index_exists($table, $index)) {
        // Clean up all instances of duplicate records
        // Add indexes to prevent new duplicates
        totara_upgrade_course_completion_remove_duplicates(
            'course_completions',
            array('userid', 'course'),
            array('timecompleted', 'timestarted', 'timeenrolled')
        );

        $dbman->add_index($table, $index);
    }

    // Define index useridcoursecriteraid (unique) to be added to course_completion_crit_compl
    $table = new xmldb_table('course_completion_crit_compl');
    $index = new xmldb_index('useridcoursecriteraid', XMLDB_INDEX_UNIQUE, array('userid', 'course', 'criteriaid'));

    // Conditionally launch add index useridcoursecriteraid
    if (!$dbman->index_exists($table, $index)) {
        totara_upgrade_course_completion_remove_duplicates(
            'course_completion_crit_compl',
            array('userid', 'course', 'criteriaid'),
            array('timecompleted')
        );

        $dbman->add_index($table, $index);
    }

    // Define index coursecriteratype (unique) to be added to course_completion_aggr_methd
    $table = new xmldb_table('course_completion_aggr_methd');
    $index = new xmldb_index('coursecriteriatype', XMLDB_INDEX_UNIQUE, array('course', 'criteriatype'));

    // Conditionally launch add index coursecriteratype
    if (!$dbman->index_exists($table, $index)) {
        totara_upgrade_course_completion_remove_duplicates(
            'course_completion_aggr_methd',
            array('course', 'criteriatype')
        );

        $dbman->add_index($table, $index);
    }

    require_once("{$CFG->dirroot}/completion/completion_completion.php");

    /// Define field status to be added to course_completions
    $table = new xmldb_table('course_completions');
    $field = new xmldb_field('status', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, 0, 'reaggregate');
    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field);

        // Get all records
        $rs = $DB->get_recordset_sql('SELECT * FROM {course_completions}');
        foreach ($rs as $record) {
            // Update status column
            $status = completion_completion::get_status($record);
            if ($status) {
                $status = constant('COMPLETION_STATUS_'.strtoupper($status));
            }

            $record->status = $status;

            if (!$DB->update_record('course_completions', $record)) {
                break;
            }
        }
        $rs->close();
    }

}

/**
 * This function finds duplicate records (based on combinations of fields that should be unique)
 * and then programmatically generated a "most correct" version of the data, update and removing
 * records as appropriate
 *
 * It was originally a part of Moodle, but removed in Moodle 3.1 as part of their upgrade cleanup.
 * We copied it here as this is still potentially needed.
 * It will be removed from here when we clean up our installation process.
 *
 * Thanks to Dan Marsden for help
 *
 * @param   string  $table      Table name
 * @param   array   $uniques    Array of field names that should be unique
 * @param   array   $fieldstocheck  Array of fields to generate "correct" data from (optional)
 * @return  void
 */
function totara_upgrade_course_completion_remove_duplicates($table, $uniques, $fieldstocheck = array()) {
    global $DB;
    // Find duplicates
    $sql_cols = implode(', ', $uniques);
    $sql = "SELECT {$sql_cols} FROM {{$table}} GROUP BY {$sql_cols} HAVING (count(id) > 1)";
    $duplicates = $DB->get_recordset_sql($sql, array());
    // Loop through duplicates
    foreach ($duplicates as $duplicate) {
        $pointer = 0;
        // Generate SQL for finding records with these duplicate uniques
        $sql_select = implode(' = ? AND ', $uniques).' = ?'; // builds "fieldname = ? AND fieldname = ?"
        $uniq_values = array();
        foreach ($uniques as $u) {
            $uniq_values[] = $duplicate->$u;
        }
        $sql_order = implode(' DESC, ', $uniques).' DESC'; // builds "fieldname DESC, fieldname DESC"
        // Get records with these duplicate uniques
        $records = $DB->get_records_select(
            $table,
            $sql_select,
            $uniq_values,
            $sql_order
        );
        // Loop through and build a "correct" record, deleting the others
        $needsupdate = false;
        $origrecord = null;
        foreach ($records as $record) {
            $pointer++;
            if ($pointer === 1) { // keep 1st record but delete all others.
                $origrecord = $record;
            } else {
                // If we have fields to check, update original record
                if ($fieldstocheck) {
                    // we need to keep the "oldest" of all these fields as the valid completion record.
                    // but we want to ignore null values
                    foreach ($fieldstocheck as $f) {
                        if ($record->$f && (($origrecord->$f > $record->$f) || !$origrecord->$f)) {
                            $origrecord->$f = $record->$f;
                            $needsupdate = true;
                        }
                    }
                }
                $DB->delete_records($table, array('id' => $record->id));
            }
        }
        if ($needsupdate || isset($origrecord->reaggregate)) {
            // If this table has a reaggregate field, update to force recheck on next cron run
            if (isset($origrecord->reaggregate)) {
                $origrecord->reaggregate = time();
            }
            $DB->update_record($table, $origrecord);
        }
    }
}

/**
 * Uninstall Moodle plugins removed in 3.1 - 3.4 and Totara 13 plugins.
 */
function totara_core_upgrade_delete_moodle_plugins() {
    global $DB;

    // NOTE: this should match \core_plugin_manager::is_deleted_standard_plugin() data.

    $deleteplugins = array(
        // Moodle 3.9.x premigration removals.
        'mod_h5pactivity',
        'quizaccess_seb',
        'tool_licensemanager',
        'tool_moodlenet',
        'report_status',
        'repository_contentbank',
        'contenttype_h5p',
        'h5plib_v124',

        // Moodle 3.8.x premigration removals.
        'filter_displayh5p',
        'atto_emojipicker',
        'atto_h5p',
        'forumreport_summary',

        // Moodle 3.7.x premigration removals.
        'dataformat_pdf',
        'theme_classic',
        'customfield_checkbox',
        'customfield_date',
        'customfield_select',
        'customfield_text',
        'customfield_textarea',

        // Moodle 3.6.x premigration removals.
        'repository_nextcloud',
        'block_recentlyaccessedcourses',
        'block_recentlyaccesseditems',
        'block_starredcourses',
        'block_timeline',
        'tool_assignmentupgrade',

        // Moodle 3.5.x premigration removals.
        'atto_recordrtc',
        'search_simpledb',

        // Totara 13 removals.
        'flavour_enterprise',
        'assignment_offline',
        'assignment_online',
        'assignment_upload',
        'assignment_uploadsingle',
        'mod_assignment',
        'block_community',
        'block_quiz_results',
        'block_course_progress_report',
        'block_frontpage_combolist',
        'gradeexport_fusion',
        'repository_picasa',
        'portfolio_picasa',
        'message_airnotifier',
        // Moodle 3.4 merge skipped.
        'tool_analytics', 'tool_httpsreplace', 'report_insights', 'mlbackend_php', 'mlbackend_python',

        // Totara 12 removals.
        'auth_none', 'tool_innodb', 'cachestore_memcache',

        // Moodle 3.3 removals.
        'repository_onedrive',
        'tool_dataprivacy',
        'tool_policy',
        'block_myoverview',
        'fileconverter_googledrive',
        'fileconverter_unoconv',

        // Totara 10.0 removals.
        'theme_kiwifruitresponsive',
        'theme_customtotararesponsive',
        'theme_standardtotararesponsive',

        // Moodle merge removals - we do not want these!
        'block_lp',
        'editor_tinymce',
        'report_competency',
        'theme_boost',
        'theme_bootstrapbase',
        'theme_canvas',
        'theme_clean',
        'theme_more',
        'tinymce_ctrlhelp', 'tinymce_managefiles', 'tinymce_moodleemoticon', 'tinymce_moodleimage',
        'tinymce_moodlemedia', 'tinymce_moodlenolink', 'tinymce_pdw', 'tinymce_spellchecker', 'tinymce_wrap',
        'tool_cohortroles',
        'tool_installaddon',
        'tool_lp',
        'tool_lpimportcsv',
        'tool_lpmigrate',
        'tool_mobile',

        // Upstream Moodle 3.1 removals.
        'webservice_amf',

        // Upstream Moodle 3.2 removals.
        'auth_radius',
        'report_search',
        'repository_alfresco',
    );

    foreach ($deleteplugins as $deleteplugin) {
        list($plugintype, $pluginname) = explode('_', $deleteplugin, 2);
        $dir = core_component::get_plugin_directory($plugintype, $pluginname);
        if ($dir and file_exists("$dir/version.php")) {
            // This should not happen, this is not a standard distribution!
            continue;
        }
        if (!get_config($deleteplugin, 'version')) {
            // Not installed.
            continue;
        }
        if ($deleteplugin === 'tool_dataprivacy') {
            if ($DB->record_exists('tool_dataprivacy_request', array())) {
                continue;
            }
        }
        if ($deleteplugin === 'tool_policy') {
            if ($DB->record_exists('tool_policy', array())) {
                continue;
            }
        }
        if ($deleteplugin === 'auth_radius') {
            if ($DB->record_exists('user', array('auth' => 'radius', 'deleted' => 0))) {
                // Do not uninstall if users with this auth exist!
                continue;
            }
        }
        if ($deleteplugin === 'repository_onedrive') {
            if ($DB->record_exists('repository_onedrive_access', array())) {
                continue;
            }
        }
        if ($deleteplugin === 'mod_assignment') {
            if ($DB->record_exists('assignment', [])) {
                continue;
            }
        }
        if ($deleteplugin === 'tool_mobile') {
            $service = $DB->get_record('external_services', array('shortname' => 'moodle_mobile_app'));
            if ($service) {
                $DB->delete_records('external_services_functions', array('externalserviceid' => $service->id));
                $DB->delete_records('external_services_users', array('externalserviceid' => $service->id));
                $DB->delete_records('external_tokens', array('externalserviceid' => $service->id));
                $DB->delete_records('external_services_functions', array('externalserviceid' => $service->id));
                $DB->delete_records('external_services', array('id' => $service->id));
            }
        }
        if ($deleteplugin === 'editor_tinymce') {
            $editors = get_config('core', 'texteditors');
            if ($editors) {
                $editors = explode(',', $editors);
                $editors = array_flip($editors);
                unset($editors['tinymce']);
                set_config('texteditors', implode(',', array_keys($editors)));
            }
            // NOTE: there is no need to update user preference, if editor is not found the default is used instead.
        }
        if ($deleteplugin === 'message_airnotifier') {
            if ($DB->record_exists('message_airnotifier_devices', [])) {
                continue;
            }
        }
        uninstall_plugin($plugintype, $pluginname);
    }

    // Delete all removed settings that are not linked to the plugins above.
    unset_config('disableupdatenotifications');
    unset_config('disableupdateautodeploy');
    unset_config('updateautodeploy');
    unset_config('updateautocheck');
    unset_config('updatenotifybuilds');
    unset_config('updateminmaturity');
    unset_config('updatenotifybuilds');
    unset_config('pathtounoconv');
}

/**
 * Moodle developers incorrectly introduced multiple broken course backups areas,
 * they were always supposed to live in course context only!!!
 *
 * @internal
 * @param int $contextid
 */
function totara_core_migrate_bogus_course_backup_area($contextid) {
    global $SITE, $DB;
    $frontpagecontext = context_course::instance($SITE->id);
    $fs = get_file_storage();

    // Make sure we do all or nothing to prevent duplicate problems on rerun.
    $trans = $DB->start_delegated_transaction();
    $files = $fs->get_area_files($contextid, 'backup', 'course');
    foreach ($files as $file) {
        $newfile = array('contextid' => $frontpagecontext->id);
        if ($fs->file_exists($frontpagecontext->id, 'course', 'backup', 0, $file->get_filepath(), $file->get_filename())) {
            // The backup files must be unique, use some weird prefix to make sure we do not override anything.
            $newfile['filename'] = 'ctx' . $contextid . '_' . $file->get_filename();
        }
        $fs->create_file_from_storedfile($newfile, $file);
    }
    $fs->delete_area_files($contextid, 'backup', 'course');
    $trans->allow_commit();
}

/**
 * Move contents of all non-functional backup areas to frontpage and drop them.
 */
function totara_core_migrate_bogus_course_backup_areas() {
    global $DB;

    $syscontext = context_system::instance();
    totara_core_migrate_bogus_course_backup_area($syscontext->id);

    $sql = "SELECT DISTINCT c.id
              FROM {files} f
              JOIN {context} c ON c.id = f.contextid
             WHERE c.contextlevel <> :courselevel";
    $contexids = $DB->get_records_sql($sql, array('courselevel' => CONTEXT_COURSE));
    foreach ($contexids as $contextid => $unused) {
        totara_core_migrate_bogus_course_backup_area($contextid);
    }
}

/**
 * Makes sure that context and tenant related tables are up to date.
 *
 * NOTE: this must be called before upgrade starts executing.
 */
function totara_core_upgrade_context_tables() {
    global $DB, $CFG;

    $dbman = $DB->get_manager();

    $updated = false;

    // Add parentid to context table.
    $table = new xmldb_table('context');
    $field = new xmldb_field('parentid', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'depth');
    $index = new xmldb_index('parentid', XMLDB_INDEX_NOTUNIQUE, array('parentid'));
    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field);
        $updated = true;
    }
    if (!$dbman->index_exists($table, $index)) {
        $dbman->add_index($table, $index);
    }

    // Remove fake context_temp table, real temp table is used in Totara.
    $table = new xmldb_table('context_temp');
    if ($dbman->table_exists($table)) {
        $dbman->drop_table($table);
    }

    // Add context_map table to be used for flattening context tree.
    $table = new xmldb_table('context_map');
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $table->add_field('parentid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
    $table->add_field('childid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
    $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    $table->add_index('parentid_childid_ix', XMLDB_INDEX_UNIQUE, array('parentid', 'childid'));
    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);
        $updated = true;
    }

    // Add tenant stuff.
    if (empty($CFG->tenantready)) {
        $table = new xmldb_table('tenant');
        if (!$dbman->table_exists($table)) {
            $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
            $table->add_field('name', XMLDB_TYPE_CHAR, '1333', null, XMLDB_NOTNULL, null, null);
            $table->add_field('idnumber', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
            $table->add_field('description', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
            $table->add_field('descriptionformat', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '1');
            $table->add_field('suspended', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');
            $table->add_field('categoryid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
            $table->add_field('cohortid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
            $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
            $table->add_field('usercreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
            $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
            $table->add_key('categoryid', XMLDB_KEY_FOREIGN_UNIQUE, array('categoryid'), 'course_categories', array('id'), 'restrict');
            $table->add_key('cohortid', XMLDB_KEY_FOREIGN_UNIQUE, array('cohortid'), 'cohort', array('id'), 'restrict'); // Deferred installation in install.xml, the cohort table exists now.
            $table->add_key('usercreated', XMLDB_KEY_FOREIGN, array('usercreated'), 'user', array('id'));
            $table->add_index('idnumber', XMLDB_INDEX_UNIQUE, array('idnumber'));
            $dbman->create_table($table);
        }

        $table = new xmldb_table('context');
        $field = new xmldb_field('tenantid', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'parentid');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
            $key = new xmldb_key('tenantid', XMLDB_KEY_FOREIGN, array('tenantid'), 'tenant', array('id'), 'restrict');
            $dbman->add_key($table, $key);
        }

        $table = new xmldb_table('user');
        $field = new xmldb_field('tenantid', XMLDB_TYPE_INTEGER, '10', null, null, null, null); // Do not use totarasync here, it may not exist yet.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
            $key = new xmldb_key('tenantid', XMLDB_KEY_FOREIGN, array('tenantid'), 'tenant', array('id'), 'restrict');
            $dbman->add_key($table, $key);
        }

        set_config('tenantready', '1');
        // NOTE: no need to set $updated to true here, there are no tenants yet, so the tenantid will be null in all contexts.
    }

    if ($updated) {
        // Add parentid to context and build context_map.
        $systemcontext = context_system::instance();
        $systemcontext->mark_dirty();
        upgrade_set_timeout(7200);
        \context_helper::build_all_paths(true, false);
    }
}

/**
 * The logic here is copied from blocks_add_default_course_blocks which is used during install.
 * The block API must be functioning, but to be safe only use the structures used there.
 *
 * @see blocks_add_default_course_blocks()
 */
function totara_core_migrate_frontpage_display() {
    global $CFG, $DB;

    $tryupgrade = true;

    if ($tryupgrade && !class_exists('moodle_page')) {
        // We need to be able to use moodle_page.
        $tryupgrade = false;
    }

    if ($tryupgrade && !defined('SITEID')) {
        // We don't know the siteid.
        $tryupgrade = false;
    }

    $course = $DB->get_record('course', ['id' => SITEID]);
    if ($tryupgrade && !$course) {
        // We don't have the site course.
        $tryupgrade = false;
    }

    if ($tryupgrade) {
        $blocks = [];

        $frontpage = get_config('core', 'frontpageloggedin');
        $frontpagelayout = explode(',', $frontpage);

        foreach ($frontpagelayout as $widget) {
            switch ($widget) {
                // Display the main part of the front page.
                case '0': // FRONTPAGENEWS
                    // Add an instance of the news items block.
                    $blocks[] = 'news_items';
                    break;

                case '5': // FRONTPAGEENROLLEDCOURSELIST
                    // Add course_list
                    $blocks[] = 'course_list';
                    break;

                case '7': // FRONTPAGECOURSESEARCH
                    // Add course_search block
                    $blocks[] = 'course_search';
                    break;
            }
        }

        if (!empty($blocks)) {
            $blocks = array_unique($blocks);

            foreach ($blocks as $key => $name) {
                // Ensure the block is visible, it needs to be so that we can add it.
                if (!$DB->record_exists('block', ['name' => $name])) {
                    // Likely the block is not installed yet.
                    $file = $CFG->dirroot . '/blocks/' . $name . '/version.php';
                    if (file_exists($file)) {
                        // OK, it's going to be installed later on.
                        set_config('frontpage_migration', 1, 'block_' . $name);
                    }
                    unset($blocks[$key]); // Remove it, we can't add it yet.
                }
            }

            $page = new moodle_page();
            $page->set_course($course);
            $page->blocks->add_blocks(['main' => $blocks], 'site-index');
        }
    }
}

/**
 * Migrate block title to the new way of storing it
 */
function totara_core_migrate_old_block_titles() {
    global $DB;

    $dbman = $DB->get_manager();

    $table = new xmldb_table('block_instances');
    $field = new xmldb_field('common_config', XMLDB_TYPE_TEXT);

    // Only proceed if the field doesn't exist.
    if ($dbman->field_exists($table, $field)) {
        return;
    }

    $dbman->add_field($table, $field);

    $instances = $DB->get_records_sql("SELECT id, configdata, blockname FROM {block_instances} WHERE configdata <> ''");

    foreach ($instances as $id => $instance) {

        // We upgrade border for all blocks and title only for those which had user-configurable title

        $title_upgrade_elegible = [
            'html',
            'totara_featured_links',
            'totara_report_graph',
            'totara_report_table',
            'totara_quick_links',
            'totara_program_completion',
            'tags',
            'tag_youtube',
            'tag_flickr',
            'rss_client',
            'mentees',
            'glossary_random',
            'blog_tags',
        ];

        $config = (array) unserialize(base64_decode($instance->configdata));

        // Explicitly converting config values to proper types below to avoid any confusions down the road as we
        // expect title to be a string.

        $common_config = [];

        if (isset($config['title']) && in_array($instance->blockname, $title_upgrade_elegible)) {

            // HTML block is a very special boy, it allows you to have an empty title, the rest just replace
            // it with default if title is not specified.
            if ($instance->blockname == 'html' || !empty($config['title'])) {
                $common_config['title'] = (string) $config['title'];
                $common_config['override_title'] = true;
            }
        }

        if (isset($config['display_with_border'])) {
            $common_config['show_border'] = (bool) $config['display_with_border'];
        }

        if (!empty($common_config)) {
            $DB->update_record('block_instances', (object) [
                'id' => $id,
                'common_config' => json_encode($common_config),
            ]);
        }
    }
}

/**
 * Add new 'course_navigation' block to all existing courses.
 * The block API must be functioning, but to be safe only use the structures used there.
 */
function totara_core_add_course_navigation() {
    global $CFG, $DB;

    if (!class_exists('moodle_page')) {
        // We need to be able to use moodle_page.
        return;
    }

    if (!$courses = $DB->get_records('course')) {
        // We don't have any courses yet.
        return;
    }

    // Ensure the block is visible, so that we can add it.
    if (!$DB->record_exists('block', ['name' => 'course_navigation'])) {
        // Likely the block is not installed yet.
        $file = $CFG->dirroot . '/blocks/course_navigation/version.php';
        if (file_exists($file)) {
            // OK, it's going to be installed later on.
            set_config('navigation_migration', 1, 'block_course_navigation');
        }
        return;
    }

    foreach ($courses as $course) {
        $page = new moodle_page();
        $page->set_course($course);
        $page->blocks->add_blocks(['side-pre' => ['course_navigation']], '*', null, true, -10);
    }
}

function totara_core_upgrade_course_defaultimage_config() {
    global $DB;

    $fs = get_file_storage();
    $context = context_system::instance();

    // If the file system has more than one files for setting 'defaultimage', then we will kinda assure that the
    // latest file is the used file for that specific setting.
    $files = $fs->get_area_files(
        $context->id,
        'course',
        'defaultimage',
        false,
        'timemodified DESC',
        false
    );

    if (!empty($files)) {
        $oldfile = reset($files);

        if (!$fs->file_exists($context->id, 'course', 'defaultimage', 0, '/', $oldfile->get_filename())) {
            // Start writing the old file to the file storage system. So that the admin settting is able to find it.
            // There is only one default image, and it must be a ZERO.
            $rc = [
                'contextid' => $context->id,
                'component' => 'course',
                'filearea' => 'defaultimage',
                'timemodified' => time(),
                'itemid' => 0,
                'source' => $oldfile->get_source(),
                'filepath' => '/',
                'filename' => $oldfile->get_filename()
            ];

            $fs->create_file_from_storedfile($rc, $oldfile);
            set_config('defaultimage', $oldfile->get_filepath() . $oldfile->get_filename(), 'course');

            // Just remove this old file, it is no longer being used.
            $oldfile->delete();
        }
    } else if (false !== get_config('course', 'defaultimage')) {
        // This seemed wrong that system admin was trying to use some random URL as their default image. But it is
        // really an edge case.
        unset_config('defaultimage', 'course');
    }

    // We need to remove pretty much all the course defaultimage that has itemid > zero. After the default image is
    // being set to zero at this point.
    $sql = "SELECT DISTINCT itemid FROM {files} WHERE itemid > 0 AND component = 'course' AND filearea = 'defaultimage'";
    $records = $DB->get_records_sql($sql);
    foreach ($records as $itemid => $unused) {
        $fs->delete_area_files($context->id, 'course', 'defaultimage', $itemid);
    }
}

/**
 * Upgrading course 'images' itemid to zero. Because course image should be found via context course id. Not item id.
 * @return void
 */
function totara_core_upgrade_course_images() {
    global $DB;

    $fs = get_file_storage();

    // For older version, itemid of course-images is being set as courseid.
    $sql = "SELECT DISTINCT itemid FROM {files} WHERE itemid > 0 AND component = 'course' AND filearea = 'images'";
    $records = $DB->get_records_sql($sql);

    foreach ($records as $itemid => $unused) {
        $ctx = context_course::instance($itemid, IGNORE_MISSING);
        if (!$ctx) {
            continue;
        }

        // The latest file should be the file that is being used for the course.
        $files = $fs->get_area_files($ctx->id, 'course', 'images', $itemid, 'timemodified DESC', false);

        if (!empty($files)) {
            $oldfile = reset($files);

            if (!$fs->file_exists($ctx->id, 'course', 'images', 0, '/', $oldfile->get_filename())) {
                $rc = [
                    'contextid' => $ctx->id,
                    'component' => 'course',
                    'filearea' => 'images',
                    'itemid' => 0,
                    'source' => $oldfile->get_source(),
                    'filepath' => '/',
                    'filename' => $oldfile->get_filename()
                ];

                $fs->create_file_from_storedfile($rc, $oldfile);
            }
        }

        // Does not really matter if the files are there or not. This will ensure we are removing unused files.
        $fs->delete_area_files($ctx->id, 'course', 'images', $itemid);
    }
}

/**
 * Upgrade to remove the invalid tags from system. Steps explaination of upgrading:
 * + Load the whole list of tag_instances
 * + Then start looking into each tag instance and checking if it is invalid with name or not (record with special
 * + characters encoded).
 * + Checking that if there are any original record for this invalid record
 * + If there is, then start the cleaning process. Luckily that the tag component itself does auto-clean up.
 *
 * @return void
 */
function totara_core_core_tag_upgrade_tags() {
    global $DB;

    // Load tag_instance + tag name and tagcollid.
    $taginstances = $DB->get_records_sql(
        "SELECT ti.*, t.name, t.tagcollid
                FROM {tag_instance} ti
          INNER JOIN {tag} t ON t.id = ti.tagid
               WHERE t.isstandard = 0"
    );

    $tagstobedeleted = [];
    $taginstancestobedeleted = [];

    foreach ($taginstances as $taginstance) {
        $name = $taginstance->name;
        $originalid = $taginstance->tagid;

        // Detect whether tag name changes.
        $name_changed = false;

        while ($name !== htmlspecialchars_decode($name)) {
            // We only want it to go back to the very first decoded value (skipping the middle encoded value).
            $name = htmlspecialchars_decode($name);
            $name = clean_param($name, PARAM_TAG);
            $name_changed = true;
        }

        // If name didn't get encoded, then we don't need to do anything.
        if (!$name_changed) {
            continue;
        }

        // This query looks for a tag with the decoded name and the same tagcollid: the "previous" tag.
        $sql = "SELECT t.id FROM {tag} t
                 WHERE t.name = :name
                   AND t.tagcollid = :tagcollid";
        $previoustag = $DB->get_record_sql($sql, ['name' => $name, 'tagcollid' => $taginstance->tagcollid]);

        // IF there is a previous tag, then modify this tag instance to point to it.
        if ($previoustag) {
            if ($taginstance->tagid !== $previoustag->id) {
                // TL-22408 - If there is already an instance of the previous tag, schedule this taginstance to be deleted.
                // Because there is a unique key on component, itemtype, itemid, tiuserid, tagid.
                $checkinstance = clone $taginstance;
                $checkinstance->tagid = $previoustag->id;
                $checkparams = [
                    'component' => $checkinstance->component,
                    'itemtype' => $checkinstance->itemtype,
                    'itemid' => $checkinstance->itemid,
                    'tiuserid' => $checkinstance->tiuserid,
                    'tagid' => $checkinstance->tagid
                ];
                $check = $DB->get_record('tag_instance', $checkparams, 'id');

                // If taginstance exists for the previous tag, then just delete this old, encoded instance.
                if ($check) {
                    $taginstancestobedeleted[$taginstance->id] = $taginstance->id;
                } else {
                    // Update this taginstance to reference the previous tag.
                    $taginstance->tagid = $previoustag->id;
                    $DB->update_record('tag_instance', $taginstance);
                }

                if (!isset($tagstobedeleted[$originalid])) {
                    $tagstobedeleted[$originalid] = $originalid;
                }
            }
        } else {
            // No other tag matches this one, update this tag and leave the tag instance alone.
            $tag = $DB->get_record('tag', ['id' => $originalid]);
            $rawname = $tag->rawname;
            while ($rawname !== htmlspecialchars_decode($rawname)) {
                // We only want it to go back to the very first decoded value (skipping the middle encoded value).
                $rawname = htmlspecialchars_decode($rawname);
                $rawname = clean_param($rawname, PARAM_TAG);
            }
            // Use the decoded name from above.
            $tag->name = $name;
            $tag->rawname = $rawname;
            $DB->update_record('tag', $tag);
        }
    }

    // Now delete the orphaned or redundant tag instances.
    foreach ($taginstancestobedeleted as $taginstanceid) {
        $DB->delete_records('tag_instance', ['id' => $taginstanceid]);
    }

    // Now delete the orphaned tags.
    foreach ($tagstobedeleted as $tagid) {
        if ($DB->record_exists('tag_instance', ['tagid' => $tagid])) {
            // There are other instances that are using this tag?
            throw new coding_exception('Tried to delete what we thought was an orphaned tag, but an instance of it exists');
        }

        $DB->delete_records('tag', ['id' => $tagid]);
    }
}

/**
 * After adding RISK_ALLOWXSS we would need to manually bump all
 * affected version.phps to update the capability risks in database,
 * this is a workaround that can be triggered from core upgrade instead
 * which requires only totara_core version bump.
 *
 * @since Totara 13.0
 */
function totara_core_upgrade_fix_role_risks() {
    global $DB, $CFG;

    $currentcaps = $DB->get_records_menu('capabilities', [], 'name ASC', 'name, riskbitmask');

    $types = core_component::get_plugin_types();
    foreach ($types as $type => $typedir) {
        $plugins = core_component::get_plugin_list($type);
        $plugins['core'] = $CFG->dirroot . '/lib';
        foreach ($plugins as $name => $plugindir) {
            $file = $plugindir . '/db/access.php';
            if (!file_exists($file)) {
                continue;
            }
            $capabilities = [];
            require($file);
            foreach ($capabilities as $capname => $capdef) {
                if (!isset($currentcaps[$capname])) {
                    continue;
                }
                if (empty($capdef['riskbitmask'])) {
                    $risks = 0;
                } else {
                    $risks = $capdef['riskbitmask'];
                }
                if ($risks != $currentcaps[$capname]) {
                    $DB->set_field('capabilities', 'riskbitmask', $risks, ['name' => $capname]);
                }
            }
        }
    }
}

/**
 * Migrate obsolete user field to custom profile field and delete the data.
 * @since Totara 13.0
 */
function totara_core_upgrade_migrate_removed_user_fields() {
    global $DB, $CFG;
    require_once($CFG->dirroot . '/user/lib.php');

    $dbman = $DB->get_manager();

    $fields = core_user::REMOVED_FIELDS;
    $categoryid = $DB->get_field('user_info_category', 'MIN(id)', []);

    foreach ($fields as $fieldshortname => $fieldlongname) {
        $table = new xmldb_table('user');
        $field = new xmldb_field($fieldshortname);
        if (!$dbman->field_exists($table, $field)) {
            continue;
        }

        // Remove column from hidden profile fields.
        if (isset($CFG->hiddenuserfields) && $CFG->hiddenuserfields !== '') {
            $hiddenuserfields = explode(',', $CFG->hiddenuserfields);
            $hiddenuserfields = array_map('trim', $hiddenuserfields);
            $hiddenuserfields = array_flip($hiddenuserfields);
            unset($hiddenuserfields[$fieldshortname]);
            set_config('hiddenuserfields', implode(',', array_keys($hiddenuserfields)));
        }

        // Add new custom field only if the column was used.
        if ($DB->record_exists_select('user', "deleted = 0 AND {$fieldshortname} <> ''")) {

            $shortname = $fieldshortname;
            if ($DB->record_exists('user_info_field', ['shortname' => $shortname])) {
                $i = 2;
                $shortname = $shortname . '_' . $i;
                while ($DB->record_exists('user_info_field', ['shortname' => $shortname])) {
                    $i++;
                }
            }
            // The custom fields API is weird, better use direct DB inserts here.

            if (!$categoryid) {
                $defaultcategory = new stdClass();
                $defaultcategory->name = get_string('profiledefaultcategory', 'admin');
                $defaultcategory->sortorder = 1;
                $categoryid = $DB->insert_record('user_info_category', $defaultcategory);
            }

            $record = new stdClass();
            $record->shortname = $shortname;
            $record->name = $fieldlongname;
            $record->datatype = 'text';
            $record->description = '';
            $record->descriptionformat = 1;
            $record->categoryid = $categoryid;
            $record->sortorder = 1 + (int)$DB->get_field('user_info_field', 'MAX(sortorder)', ['categoryid' => $record->categoryid]);
            $record->required = 0;
            $record->locked = 0;
            $record->visible = 0;
            $record->forceunique = 0;
            $record->signup = 0;
            $record->defaultdata = '';
            $record->defaultdataformat = 0;
            $record->param1 = 30;
            $record->param2 = 2048;
            $record->param3 = 0;
            $record->param4 = '';
            $record->param5 = '';
            $record->id = $DB->insert_record('user_info_field', $record);

            $sql = 'INSERT INTO "ttr_user_info_data" (userid, fieldid, data)

                    SELECT id, ' . $record->id . ', ' . $fieldshortname . '
                      FROM "ttr_user"
                     WHERE deleted = 0 AND ' . $fieldshortname . ' <> \'\'
                  ORDER BY id';
            $DB->execute($sql);
        }

        $dbman->drop_field($table, $field);
    }
}

/**
 * This is for purging all the cached preview image, it should be called when you are introduce a new image sizes
 * or update the current image_sizes. The function will query all the preview files for every available themes +
 * available image_sizes within the system and start purging it one by one.
 *
 * Unless $preview_mode is defined, then the function will actually purge cache for specific preview mode files
 * within all available theme plugins.
 *
 * @param string|null $preview_mode
 * @since Totara 13.0
 */
function totara_core_clear_preview_image_cache(?string $preview_mode = null): void {
    global $DB;

    $fs = get_file_storage();
    $image_sizes = \core\image\preview_helper::get_all_sizes();

    if ($preview_mode !== null && $preview_mode !== '') {
        $image_sizes = array_filter(
            $image_sizes,
            function (string $key) use ($preview_mode): bool {
                return $key === $preview_mode;
            },
            ARRAY_FILTER_USE_KEY
        );
    }

    $themes = \core_component::get_plugin_list('theme');
    foreach ($image_sizes as $mode => $unsed_image_size) {
        // Cleaning up all the preview files one by one and separated by the theme.
        foreach ($themes as $theme_name => $unused_theme) {
            $path = '/' . trim($mode, '/') . '/' . $theme_name . '/';
            $records = $DB->get_records('files', [
                'component' => 'core',
                'filearea' => 'preview',
                'filepath' => $path
            ]);

            if (empty($records)) {
                continue;
            }

            foreach ($records as $record) {
                $file = $fs->get_file_instance($record);
                $file->delete();
            }
        }
    }
}

/**
 * Create a relationship and corresponding relationship resolver record for a relationship class.
 *
 * Please note that if you refactor/move a relationship resolver class, you will need to
 * update all corresponding relationship resolver table rows that use that class_name!
 *
 * @param string|array $resolver_classes
 * @param string $idnumber Unique identifier.
 * @param int $sort_order
 * @param int $type Optional type identifier - defaults to 0.
 * @param string $component Plugin that the relationship is exclusive to. Defaults to being available for all.
 *
 * @since Totara 13.0
 */
function totara_core_upgrade_create_relationship($resolver_classes, $idnumber = null, $sort_order = 1, $type = 0, $component = null) {
    global $DB;

    $resolver_classes = is_array($resolver_classes)
        ? $resolver_classes
        : [$resolver_classes];

    // Checks if idnumber already exists, then updates the relationship.
    if ($idnumber) {
        $sql = "idnumber = :idnumber OR idnumber = :resolver_class";
        $params = ['idnumber' => $idnumber, 'resolver_class' => $resolver_classes[0]];
        $relationship = $DB->get_record_select(
            'totara_core_relationship',
            $sql,
            $params
        );

        // Update the sort order, type & component if the relationship already exists.
        if ($relationship) {
            $relationship->idnumber = $idnumber;
            // Conditionally add properties if they exist as a db column.
            if (isset($relationship->sort_order)) {
                $relationship->sort_order = $sort_order;
            }
            if (isset($relationship->type)) {
                $relationship->type = $type;
            }
            if (isset($relationship->component)) {
                $relationship->component = $component;
            }
            totara_core_update_relationship($relationship, $resolver_classes);
            return;
        }
    }

    if (!$idnumber) {
        $idnumber = $resolver_classes[0];
    }
    // Creates the new relationship with the resolver classes.
    totara_core_create_relationship($resolver_classes, $idnumber, $sort_order, $type, $component);
}

/**
 * Creates a totara relationship with the resolvers.
 *
 * @param array $resolver_classes
 * @param string $idnumber
 * @param int $sort_order
 * @param int $type
 * @param string|null $component
 */
function totara_core_create_relationship(array $resolver_classes, string $idnumber, int $sort_order = 1, int $type = 0, string $component = null): void {
    global $DB;
    $DB->transaction(static function() use ($DB, $resolver_classes, $idnumber, $type, $component, $sort_order) {
        $relationship_id = $DB->insert_record(
            'totara_core_relationship',
            [
                'idnumber' => $idnumber ? $idnumber : $resolver_classes[0],
                'type' => $type,
                'component' => $component,
                'sort_order' => $sort_order,
                'created_at' => time(),
            ]
        );

        foreach ($resolver_classes as $resolver_class) {
            $DB->insert_record('totara_core_relationship_resolver', [
                'relationship_id' => $relationship_id,
                'class_name' => $resolver_class,
            ]);
        }
    });
}

/**
 * Updates a relationship's properties and resolvers.
 *
 * @param $relationship
 * @param array $resolvers
 */
function totara_core_update_relationship ($relationship, array $resolvers) {
    global $DB;

    $DB->update_record( 'totara_core_relationship', $relationship);
    $existing_resolvers = $DB->get_records(
        'totara_core_relationship_resolver',
        [
            'relationship_id' => $relationship->id
        ]
    );
    $resolver_classes = array_column($existing_resolvers, 'class_name');

    foreach ($resolvers as $resolver) {
        if (!in_array($resolver, $resolver_classes, true)) {
            $DB->insert_record(
                'totara_core_relationship_resolver',
                [
                    'relationship_id' => $relationship->id,
                    'class_name' => $resolver
                ]
            );
        }
    }
}

/**
 * Set the site course to containertype=container_site.
 * Copy the logic from {@see get_site()} use execute, we won't check the outcome as if you don't meet this check
 * then you have bigger problems!
 *
 * This is moved into a function so that we can make sure that we have a phpunit test to cover it.
 *
 * @return void
 */
function totara_core_update_site_container_type(): void {
    global $DB;

    $site_id = $DB->get_field('course', 'id', ['category' => 0], MUST_EXIST);

    $update_site_record = new stdClass();
    $update_site_record->id = $site_id;
    $update_site_record->containertype = 'container_site';

    $DB->update_record('course', $update_site_record);
}

/**
 * Creating a tag collection record for hashtag. Then returning the id of the collection.
 * This function will also try to set the config for `hashtag_collection_id` which is `$CFG->hashtag_collection_id`
 *
 * @return int
 */
function totara_core_add_hashtag_tag_collection(): int {
    global $DB, $CFG;

    if (!empty($CFG->hashtag_collection_id)) {
        return $CFG->hashtag_collection_id;
    }

    $sql = 'SELECT MAX(sortorder) AS sortorder FROM "ttr_tag_coll"';
    $current_sort_order = $DB->get_field_sql($sql);

    $record = new stdClass();
    $record->name = get_string('hashtag', 'totara_core');
    $record->isdefault = 1;
    $record->component = 'totara_core';
    $record->sortorder = $current_sort_order + 1;
    $record->searchable = 1;

    $id = (int) $DB->insert_record('tag_coll', $record);

    set_config('hashtag_collection_id', $id);
    return $id;
}

/**
 * This function should be called if the $CFG->defaultrequestcategory value is not currently valid
 * it will first try to reset the value to misc, then to another valid category, before recreating misc.
 *
 * @return bool
 */
function totara_core_refresh_default_category() {
    global $DB, $CFG;

    if (!empty($CFG->defaultrequestcategory)) {
        $default = $DB->get_record('course_categories', ['id' => $CFG->defaultrequestcategory]);
        if (!empty($default) && !$default->issystem) {
            return false; // Default category looks good, nothing to do here.
        }
    }

    // First check if we still have a valid misc category to fall back on.
    $name = get_string('miscellaneous');
    $misc = $DB->get_record('course_categories', ['name' => $name]);
    if (!empty($misc) && !$misc->issystem) {
        // We have a valid misc category, use that and nevermind the rest.
        set_config('defaultrequestcategory', $misc->id);
        return false; // Don't want to resort this category.
    }

    // Next check if we have another valid category we can use.
    $sql = "SELECT cc.*
              FROM {course_categories} cc
             WHERE cc.issystem = 0
          ORDER BY cc.depth, cc.sortorder";
    $cats = $DB->get_records_sql($sql);
    if (!empty($cats) && $cat = array_shift($cats)) {
        // We have an existing valid category to use, lets use it.
        set_config('defaultrequestcategory', $cat->id);
        return false; // Don't want to resort this category.
    }

    // Okay neither of those worked, lets re-make misc and use that.
    $default = new stdClass();
    $default->name = empty($misc) ? $name : $name . time(); // Highly unlikely there is a system level misc category, but just in case.
    $default->descriptionformat = FORMAT_MOODLE;
    $default->description = '';
    $default->idnumber = '';
    $default->theme = '';
    $default->parent = 0; // Top level category.
    $default->depth = 1; // Top level category.
    $default->visible = 1;
    $default->visibleold = 1;
    $default->sortorder = 0; // We'll fix this later on.
    $default->timemodified = time();
    $default->issystem = 0;

    $default->id = $DB->insert_record('course_categories', $default);

    // Update path (only possible after we know the category id).
    $path = '/' . $default->id;
    $DB->set_field('course_categories', 'path', $path, array('id' => $default->id));

    // We should mark the context as dirty.
    context_coursecat::instance($default->id)->mark_dirty();

    set_config('defaultrequestcategory', $default->id);
    return true;
}

/**
 * Note: This was designed to be used in conjunction with totara_core_fix_category_sortorder
 * Fix any course sortorders that are out of bounds of the new category sortorders
 *
 * @return bool
 */
function totara_core_fix_course_sortorder($verbose = false) {
    global $DB;

    // First move any categories that are not sorted yet to the end.
    if ($unsorted = $DB->get_records('course_categories', ['sortorder' => 0])) {
        $DB->set_field('course_categories', 'sortorder', MAX_COURSES_IN_CATEGORY * MAX_COURSE_CATEGORIES, ['sortorder' => 0]);
    }

    // Then get all the top level categories.
    $topcats = $DB->get_records(
        'course_categories',
        ['depth' => 1],
        'sortorder,
        id',
        'id, sortorder, parent, issystem, depth, path'
    );

    $sortorder = 0;
    totara_core_fix_category_sortorder($topcats, $sortorder, 0, false, $verbose);


    // Make sure course sortorder is within the new category bounds.
    $sql = "SELECT DISTINCT cc.id, cc.sortorder
              FROM {course_categories} cc
              JOIN {course} c ON c.category = cc.id
             WHERE c.sortorder < cc.sortorder OR c.sortorder > cc.sortorder + " . MAX_COURSES_IN_CATEGORY;

    if ($fixcategories = $DB->get_records_sql($sql)) {
        //fix the course sortorder ranges
        foreach ($fixcategories as $cat) {
            $sql = "UPDATE {course}
                       SET sortorder = " . $DB->sql_modulo('sortorder', MAX_COURSES_IN_CATEGORY) . " + :catsort
                     WHERE category = :catid";
            $DB->execute($sql, ['catsort' => $cat->sortorder, 'catid' => $cat->id]);
        }

        // Reset the caches by event.
        cache_helper::purge_by_event('changesincourse');
    }
    unset($fixcategories);

    return true;
}

/**
 * Note: This was designed to be used in conjunction with totara_core_fix_course_sortorder
 * Move any/all top level system categories to the back of the sortorder so they don't get used as defaults.
 * And if we do make top level changes, make sure the changes flow down to the sub-categories and courses.
 *
 * @param array $categories
 * @param int   $sortorder
 * @param int   $parent
 * @param bool  $changesmade
 * @param bool  $verbose
 * @return bool
 */
function totara_core_fix_category_sortorder($categories, &$sortorder, $parent, $changesmade = false, $verbose = false) {
    global $DB;

    // First move any system categories to the back.
    $cats = [];
    $syscats = [];
    $syschanged = false;
    foreach ($categories as $cat) {
        if ($cat->issystem) {
            $syschanged = true;
            $syscats[] = $cat;
        } else {
            // If we hit a regular cat after a sys one, we're making a change.
            if ($syschanged) {
                $changesmade = true;
            }
            $cats[] = $cat;
        }
    }
    $categories = array_merge($cats, $syscats); // System categories at the back.
    unset($cats);
    unset($syscats);

    if ($changesmade) {
        // The top level ordering has changed, so lets follow through.
        foreach ($categories as $cat) {
            $sortorder = $sortorder + MAX_COURSES_IN_CATEGORY;

            // Override and update the record if necessary.
            if ($sortorder != $cat->sortorder) {
                $cat->sortorder = $sortorder;
                $DB->update_record('course_categories', $cat, true);

                $context = context_coursecat::instance($cat->id)->reset_paths(false);
            }

            // Update any sub-categories as well.
            $subcats = $DB->get_records('course_categories', ['parent' => $cat->id], 'sortorder, id');
            totara_core_fix_category_sortorder($subcats, $sortorder, $cat->id, $changesmade);
        }

        // Reset the caches by event.
        cache_helper::purge_by_event('changesincoursecat');

        // When we're all done, rebuild the context paths.
        if ($parent === 0) {
            $start = time();
            if ($verbose) {
                // Implement our own smaller version of verbose rather than using the build all paths verbose,
                // As we aren't doing a forced run it shouldn't take any where near as long, so we just give a small warning.
                mtrace(str_pad(userdate($start, '%H:%M:%S'), 10) . "Updating context paths, this may take a minute...\n");
            }
            context_helper::build_all_paths(false, false); // Not forced, not verbose.
            $duration = time()  - $start;
            $seconds = $duration % 60;
            $minutes = (int)floor($duration / 60);
            if ($verbose) {
                mtrace(str_pad(userdate(time(), '%H:%M:%S'), 10) . "... done, duration {$minutes}:{$seconds}\n");
            }
        }
    }
    unset($categories);

    return true;
}

/**
 * Activate setting "Disable consistent cleaning" if it is not set to something else yet.
 */
function totara_core_init_setting_disableconsistentcleaning() {
    global $CFG;

    if (!property_exists($CFG, 'disableconsistentcleaning')) {
        set_config('disableconsistentcleaning', '1');
    }
}

/**
 * Add additional columns for type and showing default branding to the existing oauth2_issuers table.
 * Can just determine this from the image field.
 *
 * @since Totara 13.9
 */
function totara_core_upgrade_oauth2_issuers_add_type_and_branding_columns() {
    global $DB;
    $dbman = $DB->get_manager();

    // Define field type to be added to oauth2_issuer.
    $table = new xmldb_table('oauth2_issuer');
    $field = new xmldb_field('type', XMLDB_TYPE_CHAR, '100', null, null, null, null, 'requireconfirmation');

    // Conditionally launch add field type.
    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field);
    }

    // Define field show_default_branding to be added to oauth2_issuer.
    $table = new xmldb_table('oauth2_issuer');
    $field = new xmldb_field('show_default_branding', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'type', ['0', '1']);

    // Conditionally launch add field show_default_branding.
    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field);
    }

    $issuer_types = [
        'google',
        'microsoft',
        'facebook',
        'nextcloud',
    ];

    foreach ($issuer_types as $issuer_type) {
        $issuer_like = $DB->sql_like('image', ':issuer_type');
        $DB->execute("
            UPDATE {oauth2_issuer}
            SET type = '{$issuer_type}'
            WHERE {$issuer_like}
        ", ['issuer_type' => "%{$issuer_type}%"]);
    }
}
