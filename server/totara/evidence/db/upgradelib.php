<?php
/**
 * This file is part of Totara Learn
 *
 * Copyright (C) 2019 onwards Totara Learning Solutions LTD
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
 * @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
 * @package totara_evidence
 */

/**
 * Create the system evidence types needed for the completion history import tool
 */
function totara_evidence_create_completion_types() {
    global $DB;

    $now = time();
    $admin = get_admin()->id;

    // Templates
    $type = [
        'descriptionformat' => FORMAT_HTML,
        'created_at' => $now,
        'modified_at' => $now,
        'created_by' => $admin,
        'modified_by' => $admin,
        'location' => 1, // Equal to \totara_evidence\models\evidence_type::LOCATION_RECORD_OF_LEARNING
        'status' => 0, // Equal to \totara_evidence\models\evidence_type::STATUS_HIDDEN
    ];
    $text_field = [
        'datatype' => 'text',
        'hidden' => 0,
        'locked' => 0,
        'required' => 0,
        'forceunique' => 0,
        'defaultdata' => '',
        'param1' => 30,
        'param2' => 2048,
    ];
    $date_field = [
        'datatype' => 'datetime',
        'hidden' => 0,
        'locked' => 0,
        'required' => 0,
        'forceunique' => 0,
        'defaultdata' => 0,
        'param1' => 1919,
        'param2' => 2038,
    ];

    $transaction = $DB->start_delegated_transaction();

    // Course type and its fields
    // Note: The shortnames must match get_columnnames() in totara/completionimport/lib.php
    if (!$DB->record_exists('totara_evidence_type', ['idnumber' => 'coursecompletionimport'])) {
        $course_type = $DB->insert_record('totara_evidence_type', array_merge($type, [
            'name' => 'multilang:completion_course',
            'idnumber' => 'coursecompletionimport',
            'description' => 'multilang:completion_course',
        ]));
        $DB->insert_record('totara_evidence_type_info_field', array_merge($text_field, [
            'typeid' => $course_type,
            'fullname' => 'multilang:course_shortname',
            'shortname' => 'courseshortname',
            'sortorder' => 1,
            'param1' => 20,
        ]));
        $DB->insert_record('totara_evidence_type_info_field', array_merge($text_field, [
            'typeid' => $course_type,
            'fullname' => 'multilang:course_idnumber',
            'shortname' => 'courseidnumber',
            'sortorder' => 2,
            'param1' => 10,
            'param2' => 100,
        ]));
        $DB->insert_record('totara_evidence_type_info_field', array_merge($date_field, [
            'typeid' => $course_type,
            'fullname' => 'multilang:completion_date',
            'shortname' => 'completiondate',
            'sortorder' => 3,
        ]));
        $DB->insert_record('totara_evidence_type_info_field', array_merge($text_field, [
            'typeid' => $course_type,
            'fullname' => 'multilang:grade',
            'shortname' => 'grade',
            'sortorder' => 4,
            'param1' => 5,
        ]));
        $DB->insert_record('totara_evidence_type_info_field', array_merge($text_field, [
            'typeid' => $course_type,
            'fullname' => 'multilang:import_id',
            'shortname' => 'importid',
            'sortorder' => 5,
            'param1' => 10,
        ]));
    }

    // Certification type and its fields
    // Note: The shortnames must match get_columnnames() in totara/completionimport/lib.php
    if (!$DB->record_exists('totara_evidence_type', ['idnumber' => 'certificationcompletionimport'])) {
        $certification_type = $DB->insert_record('totara_evidence_type', array_merge($type, [
            'name' => 'multilang:completion_certification',
            'idnumber' => 'certificationcompletionimport',
            'description' => 'multilang:completion_certification',
        ]));
        $DB->insert_record('totara_evidence_type_info_field', array_merge($text_field, [
            'typeid' => $certification_type,
            'fullname' => 'multilang:certification_shortname',
            'shortname' => 'certificationshortname',
            'sortorder' => 1,
            'param1' => 20,
        ]));
        $DB->insert_record('totara_evidence_type_info_field', array_merge($text_field, [
            'typeid' => $certification_type,
            'fullname' => 'multilang:certification_idnumber',
            'shortname' => 'certificationidnumber',
            'sortorder' => 2,
            'param1' => 10,
            'param2' => 100,
        ]));
        $DB->insert_record('totara_evidence_type_info_field', array_merge($date_field, [
            'typeid' => $certification_type,
            'fullname' => 'multilang:completion_date',
            'shortname' => 'completiondate',
            'sortorder' => 3,
        ]));
        $DB->insert_record('totara_evidence_type_info_field', array_merge($date_field, [
            'typeid' => $certification_type,
            'fullname' => 'multilang:due_date',
            'shortname' => 'duedate',
            'sortorder' => 4,
        ]));
        $DB->insert_record('totara_evidence_type_info_field', array_merge($text_field, [
            'typeid' => $certification_type,
            'fullname' => 'multilang:import_id',
            'shortname' => 'importid',
            'sortorder' => 5,
            'param1' => 10,
        ]));
    }

    $transaction->allow_commit();
    return true;
}

/**
 * Migrate report column or filter types and values.
 * We only migrate one old value to a new value and not any extra to avoid duplicate column errors.
 *
 * @param string $table
 * @param string $source
 * @param string $old_type
 * @param string[] $old_values
 * @param string $new_type
 * @param string $new_value
 */
function totara_evidence_migrate_remap_report_values($table, $source, $old_type, $new_type, $old_values, $new_value) {
    global $DB;

    $old_values_param = "'" . implode("', '", $old_values) . "'";
    $reports = $DB->get_recordset('report_builder', ['source' => $source]);

    foreach ($reports as $report) {
        $report_record = $DB->get_record_select(
            $table,
            'reportid = :report_id AND type = :old_type AND value IN (' . $old_values_param . ') ORDER BY sortorder',
            ['report_id' => $report->id, 'old_type' => $old_type],
            'id',
            IGNORE_MULTIPLE
        );
        if ($report_record) {
            $DB->update_record($table, [
                'id' => $report_record->id,
                'type' => $new_type,
                'value' => $new_value,
            ]);
        }
    }

    $reports->close();
}

/**
 * Migrate saved search data that was used in evidence reports
 *
 * @param string $source
 * @param string $old_type
 * @param string $new_type
 * @param string[] $old_values
 * @param string $new_value
 */
function totara_evidence_migrate_remap_report_saved_searches($source, $old_type, $new_type, $old_values, $new_value) {
    global $DB;

    $saved_searches = $DB->get_recordset_sql("
        SELECT rbs.* FROM {report_builder_saved} rbs
        JOIN {report_builder} rb
        ON rb.id = rbs.reportid
        WHERE rb.source = :source
    ", ['source' => $source]);

    foreach ($saved_searches as $saved) {
        if (empty($saved->search)) {
            $DB->delete_records('report_builder_saved', ['id' => $saved->id]);
            continue;
        }

        $search = unserialize($saved->search, ['allowed_classes' => false]);

        if (!is_array($search)) {
            $DB->delete_records('report_builder_saved', ['id' => $saved->id]);
            continue;
        }

        // Check for any filters that will need to be updated.
        $update = false;
        foreach ($search as $old_key => $info) {
            [$type, $value] = explode('-', $old_key);

            // There is no longer the ability to filter custom fields so remove the search
            if ($type === 'dp_plan_evidence') {
                unset($search[$old_key]);
                continue;
            }

            foreach ($old_values as $old_value) {
                if ($type === $old_type && $value === $old_value) {
                    $update = true;

                    $new_key = "{$new_type}-{$new_value}";
                    $search[$new_key] = $info;
                    unset($search[$old_key]);
                }
            }
        }

        if ($update) {
            $DB->update_record('report_builder_saved', [
                'id' => $saved->id,
                'search' => serialize($search),
            ]);
        }
    }

    $saved_searches->close();
}

/**
 * Migrate old reports to use new evidence report source columns and filters
 */
function totara_evidence_migrate_reports() {
    global $DB;

    // What columns and filters to migrate from and to
    $old_source = 'dp_evidence';
    $new_source = 'evidence_item';
    $value_mappings = [
        [
            'old_type' => 'evidence',
            'new_type' => 'base',
            'values' => [
                'name' => [
                    'name',
                    'namelink',
                    'viewevidencelink',
                ],
                'created_at' => [
                    'timecreated',
                ],
                'modified_at' => [
                    'timemodified',
                ],
                'in_use' => [
                    'evidenceinuse',
                ],
                'actions' => [
                    'actionlinks',
                ],
            ]
        ],
        [
            'old_type' => 'evidence',
            'new_type' => 'type',
            'values' => [
                'name' => [
                    'evidencetypename',
                    'evidencetypeid',
                ],
            ]
        ],
    ];

    $transaction = $DB->start_delegated_transaction();

    // Remap the values for columns, filters and saved searches
    foreach ($value_mappings as $mapping) {
        foreach ($mapping['values'] as $new_value => $old_values) {
            totara_evidence_migrate_remap_report_values(
                'report_builder_columns', $old_source, $mapping['old_type'], $mapping['new_type'], $old_values, $new_value
            );
            totara_evidence_migrate_remap_report_values(
                'report_builder_filters', $old_source, $mapping['old_type'], $mapping['new_type'], $old_values, $new_value
            );
            totara_evidence_migrate_remap_report_saved_searches(
                $old_source, $mapping['old_type'], $mapping['new_type'], $old_values, $new_value
            );
        }
    }

    // Change the record of learning embedded report to use new source and shortname
    $DB->set_fields(
        'report_builder',
        ['source' => $new_source, 'shortname' => 'evidence_record_of_learning'],
        ['source' => $old_source, 'shortname' => 'plan_evidence', 'embedded' => 1]
    );

    // Change the reports to use the new sources
    $DB->set_field('report_builder', 'source', $new_source, ['source' => $old_source]);

    $transaction->allow_commit();
}

/**
 * Migrate a set of files to their new item ids
 *
 * @param string $component component
 * @param string $filearea file area
 * @param int|false $itemid item ID or all files if not specified
 * @param array $new_record_data
 * @param bool $delete_old Delete the original file too? Defaults to true
 */
function totara_evidence_migrate_files($component, $filearea, $itemid, $new_record_data, $delete_old = true) {
    global $DB;
    $fs = get_file_storage();

    $params = [
        'contextid' => context_system::instance()->id,
        'component' => $component,
        'filearea' => $filearea,
        'filename_exclude' => '.',
    ];
    $itemid_sql = '';
    if ($itemid) {
        $params['itemid'] = $itemid;
        $itemid_sql = 'AND itemid = :itemid';
    }
    $file_records = $DB->get_recordset_select(
        'files',
        "contextid = :contextid AND component = :component AND filearea = :filearea $itemid_sql AND filename <> :filename_exclude",
        $params
    );

    foreach ($file_records as $file_record) {
        $file = $fs->get_file_instance($file_record);
        $file_content = $file->get_content();
        if ($delete_old) {
            $file->delete();
        }

        foreach ($new_record_data as $key => $value) {
            $file_record->$key = $value;
        }
        $fs->create_file_from_string($file_record, $file_content);
    }

    $file_records->close();
}

/**
 * Return legacy evidence types and virtual (no-type) if any items of this type exist in the system
 *
 * @param int $current_time
 * @param int $admin_userid
 * @return array
 */
function totara_evidence_get_legacy_evidence_types(int $current_time, $admin_userid) {
    global $DB;

    // List evidence types
    $types = $DB->get_records('dp_evidence_type', null, 'sortorder');

    // Let's check whether there are any records with no type specified.
    if (!$types || $DB->count_records('dp_plan_evidence', ['evidencetypeid' => 0, 'readonly' => 0])) {
        array_unshift($types, (object) [
            'id' => 0,
            'name' => 'multilang:unspecified',
            'description' => 'multilang:unspecified',
            'descriptionformat' => FORMAT_HTML,
            'usermodified' => $admin_userid,
            'timemodified' => $current_time,
        ]);
    }

    return $types;
}

/**
 * Create a dropdown select field that stores the old types that used to exist
 *
 * @param int $type_id
 * @return int The new field ID
 */
function totara_evidence_migrate_type_name_field(int $type_id) {
    global $DB;

    // Create an extra field that stores the name of the old type that the evidence used to be
    $old_type_names_param = '';
    $i = 0;
    $types = $DB->get_recordset('dp_evidence_type', null, 'sortorder');
    foreach ($types as $type) {
        if ($i > 0) {
            $old_type_names_param .= "\n"; // Deliberately don't use PHP_EOL
        }
        $i++;
        $old_type_names_param .= $type->name;
    }
    $types->close();

    // There could potentially already be a custom field using the shortname we want to use, if so then append a number to the end
    $shortname = 'oldtypename';
    $new_shortname = $shortname;
    $shortname_count = 1;
    while ($DB->record_exists('totara_evidence_type_info_field', ['shortname' => &$new_shortname])) {
        $new_shortname = $shortname . $shortname_count;
        $shortname_count++;
    }

    // Create the field
    return $DB->insert_record('totara_evidence_type_info_field', [
        'typeid' => $type_id,
        'fullname' => 'multilang:old_type',
        'shortname' => $new_shortname,
        'datatype' => 'menu',
        'sortorder' => 0,
        'hidden' => 0,
        'locked' => 0,
        'required' => 0,
        'forceunique' => 0,
        'defaultdata' => '',
        'param1' => $old_type_names_param,
    ]);
}

/**
 * Migrate custom fields to a new evidence type
 *
 * @param int $new_type_id New type ID
 * @param moodle_recordset|null $fields Custom field definitions associated to the legacy evidence plugins, to save on DB calls
 * @return int[] A map of old IDs to new IDs
 */
function totara_evidence_migrate_type_fields(int $new_type_id, moodle_recordset $fields = null) {
    global $DB;

    // Keeping a map between old and new IDs for custom fields
    $field_ids = [];

    // Populate custom fields for one type. Essentially, we are just cloning an existing set of custom fields
    // over to each new type.
    $fields = $fields ?? $DB->get_recordset('dp_plan_evidence_info_field');
    foreach ($fields as $old_field) {
        // Insert an old custom field into a new table
        $new_field = clone $old_field;
        $new_field->typeid = $new_type_id;
        unset($new_field->id);
        $field_ids[$old_field->id] = $new_field;
        $new_field->id = $DB->insert_record('totara_evidence_type_info_field', $new_field);

        if ($new_field->datatype === 'textarea') {
            // Copy the textarea files to a temp file area first.
            // We don't delete the old files in this step as they need to be copied to every evidence type.
            totara_evidence_migrate_files(
                'totara_customfield',
                'old_evidence_textarea',
                $old_field->id,
                ['itemid' => $new_field->id, 'filearea' => 'textarea'],
                false
            );
        }
    }

    $fields->close();

    return $field_ids;
}

/**
 * Generate a unique name by appending and increasing (n) to the filename until it is unique
 *
 * @param file_storage $fs
 * @param object $file_record
 * @return string
 */
function totara_evidence_migrate_get_unique_name_for_record(file_storage $fs, object $file_record) {
    $filename = pathinfo($file_record->filename);
    $i = 1;

    while ($fs->file_exists(
        $file_record->contextid,
        $file_record->component,
        $file_record->filearea,
        $file_record->itemid,
        $file_record->filepath,
        $file_record->filename
    )) {
        $file_record->filename = "{$filename['filename']} ($i).{$filename['extension']}";
        $i++;
    }

    return $file_record->filename;
}

/**
 * Migrate evidence item text area images whilst preventing duplicates.
 *
 * Previously with old evidence, images that were in the default value for a text area were not prefixed
 * with "@@PLUGINFILE@@" when they were saved, and instead just saved the full URL to the text area file.
 *
 * @param int $old_field_id The ID of the old text area field
 * @param int $new_data_id The ID of the new text area field data instance
 * @param string $textarea_content The actual content of the textarea
 */
function totara_evidence_migrate_item_textarea_files($old_field_id, $new_data_id, $textarea_content) {
    global $DB;
    $context = context_system::instance()->id;
    $fs = get_file_storage();
    $pluginfile = 'pluginfile.php';
    $component = 'totara_customfield';
    $old_filearea = 'textarea';
    $new_filearea = 'evidence';

    $file_records = $DB->get_recordset_select(
        'files',
        "contextid = :contextid AND component = :component AND filearea = :filearea AND itemid = :itemid AND filename <> '.'",
        [
            'contextid' => $context,
            'component' => $component,
            'filearea' => 'old_evidence_textarea',
            'itemid' => $old_field_id,
        ]
    );
    foreach ($file_records as $file_record) {
        $file = $fs->get_file_instance($file_record);

        $file_record->filearea = $new_filearea;
        $file_record->itemid = $new_data_id;
        $old_filename = $file_record->filename;
        $file_record->filename = totara_evidence_migrate_get_unique_name_for_record($fs, $file_record);
        $fs->create_file_from_string($file_record, $file->get_content());

        // Change the file URL to use the new filename.
        // We need to encode spaces and other entities in the filename as that is how they are saved in the database.
        $old_filename_encoded = rawurlencode($old_filename);
        $new_filename_encoded = rawurlencode($file_record->filename);
        $textarea_content = str_replace(
            "$pluginfile/$context/$component/$old_filearea/$old_field_id/$old_filename_encoded",
            "$pluginfile/$context/$component/$old_filearea/$old_field_id/$new_filename_encoded",
            $textarea_content
        );
    }
    $file_records->close();

    // Remove the full URL from the images, making it use @@PLUGINFILE@@ instead
    $textarea_content = file_rewrite_pluginfile_urls(
        $textarea_content, $pluginfile, $context, $component, $old_filearea, $old_field_id, ['reverse' => true]
    );
    $DB->set_field('totara_evidence_type_info_data', 'data', $textarea_content, ['id' => $new_data_id]);
}

/**
 * Migrate a single evidence item record with custom fields
 *
 * @param object $item Evidence item database record
 * @param int[] $fields Custom fields id mapping [ $oldId => $newField ]
 * @param int A new type id to use
 * @param int $current_time
 * @param int $admin_userid
 * @return int The new item ID
 */
function totara_evidence_migrate_item(object $item, array $fields, int $type_id, int $current_time, int $admin_userid) {
    global $DB;

    // Let's build a new record and insert it in the database.
    // The record may have creator or dates created\modified not set, new bank doesn't allow nullable for these fields
    $new_record = [
        'typeid' => $type_id,
        'user_id' => $item->userid,
        'name' => $item->name,
        'status' => 1, // Equal to \totara_evidence\models\evidence_item::STATUS_ACTIVE
        'created_by' => $item->usermodified ?: $admin_userid,
        'modified_by' => $item->usermodified ?: $admin_userid,
        'created_at' => $item->timecreated ?: $current_time,
        'modified_at' => $item->timemodified ?: $current_time,
    ];

    $new_item_id = $DB->insert_record('totara_evidence_item', $new_record);

    // Get all custom fields for a given evidence.
    $data_records = $DB->get_recordset('dp_plan_evidence_info_data', ['evidenceid' => $item->id]);
    foreach ($data_records as $data_record) {
        // Now let's insert it to a new table
        $new_record = [
            'fieldid' => $fields[$data_record->fieldid]->id,
            'evidenceid' => $new_item_id,
            'data' => $data_record->data,
        ];

        $data_id = $DB->insert_record('totara_evidence_type_info_data', $new_record);

        // Let's check for param records
        $params = $DB->get_recordset('dp_plan_evidence_info_data_param', ['dataid' => $data_record->id]);
        foreach ($params as $param) {
            // Insert these param records...
            $DB->insert_record('totara_evidence_type_info_data_param', [
                'dataid' => $data_id,
                'value' => $param->value,
            ]);
        }
        $params->close();

        // Migrate files in text area and file manager fields
        if ($fields[$data_record->fieldid]->datatype === 'textarea') {
            totara_evidence_migrate_files('totara_customfield', 'old_evidence', $data_record->id, [
                'itemid' => $data_id,
                'filearea' => 'evidence',
            ], true);
            totara_evidence_migrate_item_textarea_files($data_record->fieldid, $data_id, $data_record->data);
        } else if ($fields[$data_record->fieldid]->datatype === 'file') {
            totara_evidence_migrate_files('totara_customfield', 'old_evidence_filemgr', $data_record->id, [
                'itemid' => $data_id,
                'filearea' => 'evidence_filemgr',
            ], true);

            // We also need to remap the data field to the new file ID
            $DB->set_field('totara_evidence_type_info_data', 'data', $data_id, ['id' => $data_id]);
        }
    }
    $data_records->close();

    // Change the evidence relations to use the new evidence
    $relation_ids = $DB->get_fieldset_select('dp_plan_evidence_relation', 'id', 'evidenceid = ' . $item->id);
    foreach ($relation_ids as $id) {
        $DB->set_field('dp_plan_evidence_relation', 'evidenceid', $new_item_id, ['id' => $id]);
    }

    // Remove the old evidence records
    $old_data_records = $DB->get_recordset('dp_plan_evidence_info_data', ['evidenceid' => $item->id]);
    foreach ($old_data_records as $data) {
        $DB->delete_records('dp_plan_evidence_info_data_param', ['dataid' => $data->id]);
    }
    $old_data_records->close();
    $DB->delete_records('dp_plan_evidence_info_data', ['evidenceid' => $item->id]);
    $DB->delete_records('dp_plan_evidence', ['id' => $item->id]);

    return $new_item_id;
}

/**
 * Get just the custom fields used for the custom fields only
 *
 * @param int $uploaded_type_id
 * @return array
 */
function totara_evidence_migrate_upload_fields(int $uploaded_type_id) {
    global $DB;
    return totara_evidence_migrate_type_fields(
        $uploaded_type_id,
        $DB->get_recordset_sql("
            SELECT DISTINCT field.* FROM {dp_plan_evidence} item 
            INNER JOIN {dp_plan_evidence_info_data} data ON item.id = data.evidenceid 
            INNER JOIN {dp_plan_evidence_info_field} field ON data.fieldid = field.id
            WHERE item.readonly = 1
        ")
    );
}

/**
 * Get already migrated type fields
 *
 * @param int $type_id
 * @return array|false
 */
function totara_evidence_migrate_get_migrated_type_fields(int $type_id) {
    global $DB;

    $type_field_ids = [];
    $type_fields = $DB->get_recordset_sql("
        SELECT old_field.id old_field_id, new_field.* FROM {totara_evidence_type} new_type
        INNER JOIN {totara_evidence_type_info_field} new_field ON new_type.id = new_field.typeid
        INNER JOIN {dp_plan_evidence_info_field} old_field ON old_field.shortname = new_field.shortname
        WHERE new_type.id = :typeid ORDER BY new_field.id
    ", ['typeid' => $type_id]);
    foreach ($type_fields as $field) {
        $old_field_id = $field->old_field_id;
        unset($field->old_field_id);
        $type_field_ids[$old_field_id] = $field;
    }
    $type_fields->close();
    return $type_field_ids;
}

/**
 * Migrate evidence records associated with the course/certification completion upload tool
 *
 * @param int $current_time
 * @param int $admin_userid
 * @param $progress_bar
 */
function totara_evidence_migrate_completion_history_evidence($current_time, $admin_userid, $progress_bar) {
    global $DB;

    if (!$DB->count_records('dp_plan_evidence', ['readonly' => 1])) {
        return;
    }

    $uploaded_type = $DB->get_record('totara_evidence_type', ['idnumber' => 'legacycompletionimport']);
    if ($uploaded_type) {
        $uploaded_type_id = $uploaded_type->id;
        $uploaded_type_field_ids = totara_evidence_migrate_get_migrated_type_fields($uploaded_type_id);
        $fields = $DB->get_records('totara_evidence_type_info_field', ['typeid' => $uploaded_type_id]);
        $old_type_field_id = end($fields)->id;
    } else {
        // Create the legacy completion type if it doesn't exist
        $transaction = $DB->start_delegated_transaction();

        $uploaded_type_data = [
            'name' => 'multilang:completion_legacy',
            'idnumber' => 'legacycompletionimport',
            'description' => 'multilang:completion_legacy',
            'descriptionformat' => FORMAT_HTML,
            'created_at' => $current_time,
            'modified_at' => $current_time,
            'created_by' => $admin_userid,
            'modified_by' => $admin_userid,
            'location' => 1, // Equal to \totara_evidence\models\evidence_type::LOCATION_RECORD_OF_LEARNING
            'status' => 0, // Equal to \totara_evidence\models\evidence_type::STATUS_HIDDEN
        ];
        $uploaded_type_id = $DB->insert_record('totara_evidence_type', $uploaded_type_data);

        $uploaded_type_field_ids = totara_evidence_migrate_upload_fields($uploaded_type_id);

        // We need a field to store what type the evidence was in the old system
        if ($DB->count_records('dp_evidence_type')) {
            $old_type_field_id = totara_evidence_migrate_type_name_field($uploaded_type_id);
        }

        $transaction->allow_commit();
    }

    // Migrate the actual completion evidence, i.e. evidence that is marked as 'readonly'
    $items = $DB->get_recordset('dp_plan_evidence', ['readonly' => 1]);
    foreach ($items as $item) {
        $transaction = $DB->start_delegated_transaction();
        $new_item_id = totara_evidence_migrate_item(
            $item, $uploaded_type_field_ids, $uploaded_type_id, $admin_userid, $current_time
        );

        // If the evidence had a type associated with it, then add it to the type name field
        $legacy_type_name = $DB->get_field('dp_evidence_type', 'name', ['id' => $item->evidencetypeid]);
        if ($legacy_type_name) {
            $DB->insert_record('totara_evidence_type_info_data', [
                'evidenceid' => $new_item_id,
                'fieldid' => $old_type_field_id,
                'data' => $legacy_type_name,
            ]);
        }
        $transaction->allow_commit();

        $progress_bar->increment();
    }
    $items->close();
}

/**
 * Migrate evidence records associated with evidence that was manually created by users
 *
 * @param int $current_time
 * @param int $admin_userid
 * @param $progress_bar
 */
function totara_evidence_migrate_manually_created_evidence($current_time, $admin_userid, $progress_bar) {
    global $DB;

    foreach (totara_evidence_get_legacy_evidence_types($current_time, $admin_userid) as $old_type) {
        $DB->transaction(static function () use ($DB, $current_time, $admin_userid, $progress_bar, $old_type) {
            // We need to create a type from a given record
            $manual_type_record = [
                'name' => $old_type->name,
                'description' => $old_type->description,
                'descriptionformat' => FORMAT_HTML,
                'location' => 0, // Equal to \totara_evidence\models\evidence_type::LOCATION_EVIDENCE_BANK
                'status' => 0, // Equal to \totara_evidence\models\evidence_type::STATUS_HIDDEN
                'created_by' => $old_type->usermodified ?: $admin_userid,
                'modified_by' => $old_type->usermodified ?: $admin_userid,
                'created_at' => $old_type->timemodified ?: $current_time,
                'modified_at' => $old_type->timemodified ?: $current_time,
            ];

            // New type
            $new_type_id = $DB->insert_record('totara_evidence_type', $manual_type_record);
            $type_field_ids = totara_evidence_migrate_type_fields($new_type_id);

            // Migrate any images in the type's description
            if ($old_type->id) {
                totara_evidence_migrate_files('totara_plan', 'dp_evidence_type', $old_type->id, [
                    'itemid' => $new_type_id,
                    'component' => 'totara_evidence',
                    'filearea' => 'type_description', // Equal to \totara_evidence\models\evidence_type::DESCRIPTION_FILEAREA
                ]);
            }

            // Let's migrate all the records of a given type, except 'read-only'
            $items = $DB->get_recordset('dp_plan_evidence', ['readonly' => 0, 'evidencetypeid' => $old_type->id]);
            foreach ($items as $item) {
                totara_evidence_migrate_item($item, $type_field_ids, $new_type_id, $current_time, $admin_userid);

                $progress_bar->increment();
            }
            $items->close();

            if ($old_type->id > 0) {
                $DB->delete_records('dp_evidence_type', ['id' => $old_type->id]);
            }
        });
    }
}

/**
 * We need to shift all the files we want to migrate to a temporary file area first in order
 * to avoid duplicate file item IDs which will prevent us from copying the files.
 */
function totara_evidence_migrate_move_files_to_temp_area() {
    global $DB;

    $evidence_data = $DB->get_recordset('dp_plan_evidence_info_data');
    foreach ($evidence_data as $data) {
        totara_evidence_migrate_files(
            'totara_customfield',
            'evidence',
            $data->id,
            ['filearea' => 'old_evidence']
        );
        totara_evidence_migrate_files(
            'totara_customfield',
            'evidence_filemgr',
            $data->id,
            ['filearea' => 'old_evidence_filemgr']
        );
    }
    $evidence_data->close();

    $textareas = $DB->get_recordset('dp_plan_evidence_info_field', ['datatype' => 'textarea']);
    foreach ($textareas as $textarea) {
        totara_evidence_migrate_files(
            'totara_customfield',
            'textarea',
            $textarea->id,
            ['filearea' => 'old_evidence_textarea']
        );
    }
    $textareas->close();
}

/**
 * Once we are done copying everything over, we need to clean up the temporary file area we created
 */
function totara_evidence_migrate_remove_temporary_files() {
    global $DB;
    $context = context_system::instance()->id;
    $fs = get_file_storage();

    $fs->delete_area_files($context, 'totara_customfield', 'old_evidence');
    $fs->delete_area_files($context, 'totara_customfield', 'old_evidence_filemgr');
    $fs->delete_area_files($context, 'totara_customfield', 'old_evidence_textarea');
    $fs->delete_area_files($context, 'totara_plan', 'dp_evidence_type');

    $DB->delete_records('dp_plan_evidence_info_field');
}

/**
 * Migrate old evidence to the new evidence tables
 */
function totara_evidence_migrate() {
    $time = time();
    $admin_userid = get_admin()->id;

    // Progress bar required to prevent browser timeout if there is a large amount of records to migrate
    $progress_bar = new class () {
        protected $current_count;
        protected $total_count;
        protected $progress_bar;

        public function __construct() {
            global $DB;
            $this->current_count = 0;
            $this->total_count = $DB->count_records('dp_plan_evidence');
        }

        public function increment(): void {
            if (!isset($this->progress_bar)) {
                $this->progress_bar = new progress_bar('totara_evidence_upgrade', 500, true);
            }

            $this->current_count++;
            $this->progress_bar->update($this->current_count, $this->total_count, get_string(
                'upgrading_evidence_progress',
                'totara_evidence',
                ['current' => $this->current_count, 'total' => $this->total_count]
            ));
        }
    };

    totara_evidence_migrate_move_files_to_temp_area();

    totara_evidence_migrate_completion_history_evidence($time, $admin_userid, $progress_bar);

    totara_evidence_migrate_manually_created_evidence($time, $admin_userid, $progress_bar);

    totara_evidence_migrate_remove_temporary_files();

    totara_evidence_migrate_reports();

    return true;
}
