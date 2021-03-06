<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2010 onwards Totara Learning Solutions LTD
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
 * @author Simon Coggins <simon.coggins@totaralms.com>
 * @author Jonathan Newman
 * @package totara
 * @subpackage totara_hierarchy
 */

/**
 * competency/lib.php
 *
 * Library to construct competency hierarchies
 */

use core\orm\query\builder;
use hierarchy_competency\event\competency_updated;
use totara_competency\entity\competency_achievement as competency_achievement_entity;
use totara_competency\pathway;
use totara_core\advanced_feature;

require_once("{$CFG->dirroot}/totara/hierarchy/lib.php");
require_once("{$CFG->dirroot}/totara/hierarchy/prefix/competency/evidenceitem/type/abstract.php");
require_once("{$CFG->dirroot}/totara/core/utils.php");
require_once("{$CFG->dirroot}/totara/core/js/lib/setup.php");

/**
 * Oject that holds methods and attributes for competency operations.
 * @abstract
 */
class competency extends hierarchy {

    /**
     * The base table prefix for the class
     */
    const PREFIX = 'competency';
    const SHORT_PREFIX = 'comp';
    var $prefix = self::PREFIX;
    var $shortprefix = self::SHORT_PREFIX;
    protected $extrafields = array('evidencecount');


    // The following is Perform specific constants. This may / should be moved in later versions of perform
    /** Assign Availability constants */
    public const ASSIGNMENT_CREATE_SELF = 1;
    public const ASSIGNMENT_CREATE_OTHER = 2;

    public const AGGREGATION_METHOD_ALL = 1;
    public const AGGREGATION_METHOD_ANY = 2;
    public const AGGREGATION_METHOD_OFF = 3;

    public const COMP_AGGREGATION = [
        'ALL' => competency::AGGREGATION_METHOD_ALL,
        'ANY' => competency::AGGREGATION_METHOD_ANY,
        'OFF' => competency::AGGREGATION_METHOD_OFF,
    ];

    /**
     * Get template
     * @param int Template id
     * @return object|false
     */
    function get_template($id) {
        global $DB;
        return $DB->get_record($this->shortprefix.'_template', array('id' => $id));
    }

    /**
     * Gets templates.
     *
     * @global object $CFG
     * @return array
     */
    function get_templates() {
        global $DB;
        return $DB->get_records($this->shortprefix.'_template', array('frameworkid' => $this->frameworkid), 'fullname');
    }

    /**
     * Hide the competency template
     * @var int - the template id to hide
     * @return void
     */
    function hide_template($id) {
        global $DB;
        $template = $this->get_template($id);
        if ($template) {
            $visible = 0;
            $DB->set_field($this->shortprefix.'_template', 'visible', $visible, array('id' => $template->id));
        }
    }

    /**
     * Show the competency template
     * @var int - the template id to show
     * @return void
     */
    function show_template($id) {
        global $DB;
        $template = $this->get_template($id);
        if ($template) {
            $visible = 1;
            $DB->set_field($this->shortprefix.'_template', 'visible', $visible, array('id' => $template->id));
        }
    }

    /**
     * Delete competency framework and updated associated scales
     * @access  public
     * @param boolean $triggerevent Whether the delete item event should be triggered or not
     * @return  void
     */
    function delete_framework($triggerevent = true) {
        global $DB;

        // Start transaction
        $transaction = $DB->start_delegated_transaction();

        // Run parent method
        parent::delete_framework();
        // Delete references to scales
        if ($DB->count_records($this->shortprefix.'_scale_assignments', array('frameworkid' => $this->frameworkid))) {
            $DB->delete_records($this->shortprefix.'_scale_assignments', array('frameworkid' => $this->frameworkid));
        }
        // End transaction
        $transaction->allow_commit();
        return true;
    }


    /**
     * Delete all data associated with the competencies
     *
     * This method is protected because it deletes the competencies, but doesn't use
     * transactions
     * Use {@link hierarchy::delete_hierarchy_item()} to recursively delete an item and
     * all its children
     *
     * @param array $items Array of IDs to be deleted
     *
     * @return boolean True if items and associated data were successfully deleted
     */
    protected function _delete_hierarchy_items($items) {
        global $DB;

        // First delete all competency achievement pathways for the competency
        // TODO TL-23039 Find a way to move this into the competency observer
        foreach ($items as $competency_id) {
            pathway::delete_all_for_competency($competency_id);
        }

        // Then call the deleter for the parent class
        if (!parent::_delete_hierarchy_items($items)) {
            return false;
        }

        // delete rows from all these other tables:
        $db_data = array(
            $this->shortprefix.'_record_history' => 'competencyid',
            $this->shortprefix.'_record' => 'competencyid',
            $this->shortprefix.'_criteria' => 'competencyid',
            $this->shortprefix.'_criteria_record' => 'competencyid',
            $this->shortprefix.'_relations' => 'id1',
            $this->shortprefix.'_relations' => 'id2',
            hierarchy::get_short_prefix('position').'_competencies' => 'competencyid',
            hierarchy::get_short_prefix('organisation').'_competencies' => 'competencyid',
            'dp_plan_competency_assign' => 'competencyid',
            'comp_assign_availability' => 'comp_id',
        );
        list($items_sql, $items_params) = $DB->get_in_or_equal($items);
        foreach ($db_data as $table => $field) {
            $select = "$field {$items_sql}";
            $DB->delete_records_select($table, $select, $items_params);
        }


        // update the template count

        // start by getting a list of templates affected by the deletions
        $modified_templates = array();
        $sql = "
            SELECT DISTINCT templateid
            FROM {{$this->shortprefix}_template_assignment}
            WHERE type = ? AND instanceid {$items_sql}";
        $records = $DB->get_records_sql($sql, array_merge(array('1'), $items_params));
        if ($records) {
            foreach ($records as $template) {
                $modified_templates[] = $template->templateid;
            }
        }

        // now delete the template assignments
        $DB->delete_records_select($this->shortprefix.'_template_assignment',
            'type = ? AND instanceid ' . $items_sql, array_merge(array('1'), $items_params));


        // only continue if at least one template has changed
        if (count($modified_templates) > 0) {
            list($templates_sql, $templates_params) = $DB->get_in_or_equal($modified_templates);
            $templatecounts = $DB->get_records_sql(
                "SELECT templateid, COUNT(instanceid) AS count
                FROM {{$this->shortprefix}_template_assignment}
                WHERE type = ?
                GROUP BY templateid
                HAVING templateid {$templates_sql}", array_merge(array('1'), $templates_params));

            if ($templatecounts) {
                foreach ($templatecounts as $templatecount) {
                    // now update count for templates that still have at least one assignment
                    // this won't catch templates that now have zero competencies as there
                    // won't be any entries in comp_template_assignment
                    $sql = "UPDATE {{$this->shortprefix}_template}
                        SET competencycount = ?
                        WHERE id = ?";
                    $DB->execute($sql, array($templatecount->count, $templatecount->templateid));
                }
            }

            // figure out if any of the modified templates are now empty
            $empty_templates = $modified_templates;
            $sql = "SELECT DISTINCT templateid
                FROM {{$this->shortprefix}_template_assignment}";
            $records = $DB->get_recordset_sql($sql);
            foreach ($records as $record) {
                $key = array_search($record->templateid, $empty_templates);
                if ($key !== false) {
                    // it's not empty if there's an assignment
                    unset($empty_templates[$key]);
                }
            }
            $records->close();

            // finally, set the count to zero for any of the templates that no longer
            // have any assignments
            if (count($empty_templates) > 0) {
                list($in_sql, $in_params) = $DB->get_in_or_equal($empty_templates);
                $sql = "UPDATE {{$this->shortprefix}_template}
                    SET competencycount = 0
                    WHERE id {$in_sql}";
                $DB->execute($sql, $in_params);
            }
        }

        return true;

    }


    /**
     * Delete template and associated data
     * @var int - the template id to delete
     * @return  void
     */
    function delete_template($id) {
        global $DB;
        $DB->delete_records($this->shortprefix.'_template_assignment', array('templateid' => $id));
        $DB->delete_records(hierarchy::get_short_prefix('position').'_competencies', array('templateid' => $id));

        // Delete this item
        $DB->delete_records($this->shortprefix.'_template', array('id' => $id));
    }

    /**
     * Get competencies assigned to a template
     * @param int $id Template id
     * @return array
     */
    function get_assigned_to_template($id) {
        global $DB;

        return $DB->get_records_sql(
            "
            SELECT
                c.id AS id,
                c.fullname AS competency,
                c.fullname AS fullname    /* used in some places (for genericness) */
            FROM
                {{$this->shortprefix}_template_assignment} a
            LEFT JOIN
                {{$this->shortprefix}_template} t
             ON t.id = a.templateid
            LEFT JOIN
                {{$this->shortprefix}} c
             ON a.instanceid = c.id
            WHERE
                t.id = ?
            "
        , array($id));
    }

    /**
     * Get evidence items for a competency
     * @param $item object Competency
     * @return array
     */
    function get_evidence($item) {
        global $DB;
        return $DB->get_records($this->shortprefix.'_criteria', array('competencyid' => $item->id), 'id');
    }

    /**
     * Get related competencies
     * @param $item object Competency
     * @return array
     */
    function get_related($item) {
        global $DB;

        return $DB->get_records_sql(
            "
            SELECT DISTINCT
                c.id AS id,
                c.fullname,
                f.id AS fid,
                f.fullname AS framework,
                it.fullname AS itemtype
            FROM
                {{$this->shortprefix}_relations} r
            INNER JOIN
                {{$this->shortprefix}} c
             ON r.id1 = c.id
             OR r.id2 = c.id
            INNER JOIN
                {{$this->shortprefix}_framework} f
             ON f.id = c.frameworkid
            LEFT JOIN
                {{$this->shortprefix}_type} it
             ON it.id = c.typeid
            WHERE
                (r.id1 = ? OR r.id2 = ?)
            AND c.id != ?
            ORDER BY c.fullname
            ",
        array($item->id, $item->id, $item->id));
    }

    /**
     * Get competency evidence using in a course
     *
     * @param   $courseid   int
     * @return  array
     */
    function get_course_evidence($courseid) {
        global $DB;

        return $DB->get_records_sql(
                "
                SELECT DISTINCT
                    cc.id AS evidenceid,
                    c.id AS id,
                    c.fullname,
                    f.id AS fid,
                    f.fullname AS framework,
                    cc.itemtype AS evidencetype,
                    cc.iteminstance AS evidenceinstance,
                    cc.itemmodule AS evidencemodule,
                    cc.linktype as linktype
                FROM
                    {{$this->shortprefix}_criteria} cc
                INNER JOIN
                    {{$this->shortprefix}} c
                 ON cc.competencyid = c.id
                INNER JOIN
                    {{$this->shortprefix}_framework} f
                 ON f.id = c.frameworkid
                LEFT JOIN
                    {modules} m
                 ON cc.itemtype = 'activitycompletion'
                AND m.name = cc.itemmodule
                LEFT JOIN
                    {course_modules} cm
                 ON cc.itemtype = 'activitycompletion'
                AND cm.instance = cc.iteminstance
                AND cm.module = m.id
                WHERE
                (
                        cc.itemtype <> 'activitycompletion'
                    AND cc.iteminstance = ?
                )
                OR
                (
                        cc.itemtype = 'activitycompletion'
                    AND cm.course = ?
                )
                ORDER BY
                    c.fullname
                ",
        array($courseid, $courseid));
    }

    /**
     * Run any code before printing header
     * @param $page string Unique identifier for page
     * @return void
     */
    function hierarchy_page_setup($page = '', $item=null) {
        global $CFG, $USER, $PAGE;

        if (!in_array($page, array('template/view', 'item/view', 'item/add'))) {
            return;
        }

        // Setup custom javascript
        require_once($CFG->dirroot.'/totara/core/js/lib/setup.php');

        // Setup lightbox
        local_js(array(
            TOTARA_JS_DIALOG,
            TOTARA_JS_TREEVIEW
        ));

        switch ($page) {
            case 'item/view':

                $args = array();
                if (!empty($item->id)) {
                    $args['id'] = $item->id;
                }
                // Include competency item js module
                $PAGE->requires->strings_for_js(array('assignrelatedcompetencies',
                        'assignnewevidenceitem','assigncoursecompletions'), 'totara_hierarchy');
                $PAGE->requires->js_call_amd('totara_hierarchy/competency_item', 'item', $args);

                break;
            case 'template/view':

                $args = array();
                if (!(empty($item->id))) {
                    $args['id'] = $item->id;
                }

                // Include competency template js module
                $PAGE->requires->string_for_js('assignnewcompetency', 'totara_competency');
                $PAGE->requires->js_call_amd('totara_hierarchy/competency_item', 'template', $args);

                break;
            case 'item/add':
                $selected_position = json_encode(dialog_display_currently_selected(get_string('selected', 'totara_hierarchy'), 'position'));
                $selected_organisation = json_encode(dialog_display_currently_selected(get_string("currentlyselected", "totara_hierarchy"), "organisation"));
                $args = array('args'=>'{"userid":'.$USER->id.','.
                              '"can_edit": true,'.
                              '"dialog_display_position":'.$selected_position.','.
                              '"dialog_display_organisation":'.$selected_organisation.'}');
                $PAGE->requires->strings_for_js(array('chooseposition', 'choosemanager','chooseorganisation'), 'totara_hierarchy');
                $jsmodule = array(
                        'name' => 'totara_competencyaddevidence',
                        'fullpath' => '/totara/plan/components/competency/competency.add_evidence.js',
                        'requires' => array('json'));
                $PAGE->requires->js_init_call('M.totara_competencyaddevidence.init', $args, false, $jsmodule);
                break;
        }
    }

    /**
     * Print any extra markup to display on the hierarchy view item page
     * @param $item object Competency being viewed
     * @return void
     */
    function display_extra_view_info($item, $section='') {
        global $CFG, $PAGE;
        $renderer = $PAGE->get_renderer('totara_hierarchy');

        $sitecontext = context_system::instance();
        $can_edit = has_capability('totara/hierarchy:updatecompetency', $sitecontext);
        if ($can_edit) {
            $str_edit = get_string('edit');
            $str_remove = get_string('remove');
        }

        if (!$section || $section == 'related') {
            // Display related competencies
            echo html_writer::start_tag('div', array('class' => 'list-related'));
            $related = $this->get_related($item);
            echo $renderer->competency_view_related($item, $can_edit, $related);
            echo html_writer::end_tag('div');
        }

        if (!$section || $section == 'evidence') {
            // Display evidence
            $evidence = $this->get_evidence($item);
            echo $renderer->competency_view_evidence($item, $evidence, $can_edit);
        }
    }

    /**
     * Return hierarchy prefix specific data about an item
     *
     * The returned array should have the structure:
     * array(
     *  0 => array('title' => $title, 'value' => $value),
     *  1 => ...
     * )
     *
     * @param $item object Item being viewed
     * @param $cols array optional Array of columns and their raw data to be returned
     * @return array
     */
    function get_item_data($item, $cols = NULL) {

        $data = parent::get_item_data($item, $cols);

        $prefix = get_string($this->prefix, 'totara_hierarchy');
        $aggregationmethod = $item->aggregationmethod ?? self::AGGREGATION_METHOD_ALL;
        // Add aggregation method
        $data[] = array(
            'title' => get_string('aggregationmethodview', 'totara_hierarchy', $prefix),
            'value' => get_string('aggregationmethod'.$aggregationmethod, 'totara_hierarchy')
        );

        return $data;
    }

    /**
     * Get the competency scale for this competency (including all the scale's
     * values in an attribute called valuelist)
     *
     * @global object $CFG
     * @return object
     */
    function get_competency_scale() {
        global $DB;
        $sql = "
            SELECT scale.*
            FROM
                {{$this->shortprefix}_scale_assignments} sa,
                {{$this->shortprefix}_scale} scale
            WHERE
                sa.scaleid = scale.id
                AND sa.frameworkid = ?
        ";
        $scale = $DB->get_record_sql($sql, array($this->frameworkid));

        $valuelist = $DB->get_records($this->shortprefix.'_scale_values', array('scaleid' => $scale->id), 'sortorder');
        if ($valuelist) {
            $scale->valuelist = $valuelist;
        }
        return $scale;
    }


    /**
     * Get scales for a competency
     * @return array
     */
    function get_scales() {
        global $DB;
        return $DB->get_records($this->shortprefix.'_scale', null, 'name');
    }

    /**
     * Delete  a competency assigned to a template
     * @param $templateid
     * @param $competencyid
     * @return void;
     */
    function delete_assigned_template_competency($templateid, $competencyid) {
        global $DB;
        if (!$template = $this->get_template($templateid)) {
            return;
        }

        // Delete assignment
        $DB->delete_records('comp_template_assignment', array('templateid' => $template->id, 'instanceid' => $competencyid));

        // Reduce competency count for template
        $template->competencycount--;

        if ($template->competencycount < 0) {
            $template->competencycount = 0;
        }

        $DB->update_record('comp_template', $template);
    }


    /**
     * Returns an array of all competencies that a user has a comp_record
     * record for, keyed on the competencyid. Also returns the required
     * proficiency value and isproficient, which is 1 if the user meets the
     * proficiency and 0 otherwise
     *
     * @deprecated since Totara 13
     */
    static function get_proficiencies($userid) {
        global $DB;

        debugging('competency::get_proficiencies has been deprecated. Use competency::get_user_completed_competencies to check what competencies a user has completed.');

        $sql = "SELECT cr.competencyid, prof.proficiency, csv.proficient AS isproficient
            FROM {comp_record} cr
            LEFT JOIN {comp} c ON c.id=cr.competencyid
            LEFT JOIN {comp_scale_assignments} csa
                ON c.frameworkid = csa.frameworkid
            LEFT JOIN {comp_scale_values} csv
                ON csv.scaleid=csa.scaleid
                AND csv.id=cr.proficiency
            LEFT JOIN (
                SELECT scaleid, MAX(id) AS proficiency
                FROM {comp_scale_values}
                WHERE proficient=1
                GROUP BY scaleid
            ) prof on prof.scaleid=csa.scaleid
            WHERE cr.userid = ?";
        return $DB->get_records_sql($sql, array($userid));
    }


    /**
     * Prints the list of linked evidence
     *
     * @param int $courseid
     * @return string
     */
    function print_linked_evidence_list($courseid) {
        global $CFG, $DB, $OUTPUT, $PAGE;

        $system_context = context_system::instance();

        $can_edit = has_capability('totara/hierarchy:updatecompetency', $system_context);
        $can_manage_fw = has_capability('totara/hierarchy:updatecompetencyframeworks', $system_context);

        $course = $DB->get_record('course', array('id' => $courseid));

        // define the table
        $out = new html_table();
        $out->id = 'list-coursecompetency';
        $out->head = array();
        $out->rowclasses[0] = 'header';

        // header row
        $header = new html_table_row();
        $header->attributes = array('scope' => 'col');
        $header->cells = array();
        $head = array();

        // header cells
        $heading0 = new html_table_cell();
        $heading0->text = get_string('competencyframework', 'totara_hierarchy');
        $heading0->header = true;
        $head[] = $heading0;

        $heading1 = new html_table_cell();
        $heading1->text = get_string('name');
        $heading1->header = true;
        $head[] = $heading1;

        if ($can_edit) {
            require_once($CFG->dirroot.'/totara/plan/lib.php');
            $heading3 = new html_table_cell();
            $heading3->text = get_string('linktype', 'totara_plan');
            $heading3->header = true;
            $head[] = $heading3;

            $heading4 = new html_table_cell();
            $heading4->text = get_string('options', 'totara_hierarchy');
            $heading4->header = true;
            $head[] = $heading4;

            $js_params = array('prefix' => 'course');
            $PAGE->requires->js_call_amd('totara_hierarchy/hierarchyitems', 'init', $js_params);
        } // if ($can_edit)
        // add the completed row to the table
        $out->head = $head;

        // Get any competencies used in this course
        $competencies = $this->get_course_evidence($course->id);
        if ($competencies) {

            $str_remove = get_string('remove');

            $activities = array();

            $data = array();
            foreach ($competencies as $competency) {
                $framework_text = ($can_manage_fw) ?
                     $OUTPUT->action_link(new moodle_url('/totara/hierarchy/index.php', array('prefix' => 'competency', 'frameworkid' => $competency->fid)), format_string($competency->framework))
                     : format_string($competency->framework);

                // define a data row
                $row = new html_table_row();

                //define data cells
                $cell = new html_table_cell($framework_text);
                $row->cells[] = $cell;

                $cell = new html_table_cell($OUTPUT->action_link(new moodle_url('/totara/hierarchy/item/view.php', array('prefix' => 'competency', 'id' => $competency->id)), format_string($competency->fullname)));
                $row->cells[] = $cell;

                // Create evidence object
                $evidence = new stdClass();
                $evidence->id = $competency->evidenceid;
                $evidence->itemtype = $competency->evidencetype;
                $evidence->iteminstance = $competency->evidenceinstance;
                $evidence->itemmodule = $competency->evidencemodule;

                // Options column
                if ($can_edit) {
                    $cell = new html_table_cell();

                    // TODO: Rewrite to use a component_action object
                    $select = html_writer::select(
                        $options = array(
                            PLAN_LINKTYPE_OPTIONAL => get_string('optional', 'totara_hierarchy'),
                            PLAN_LINKTYPE_MANDATORY => get_string('mandatory', 'totara_hierarchy'),
                        ),
                        'linktype', //$name,
                        (isset($competency->linktype) ? $competency->linktype : PLAN_LINKTYPE_MANDATORY), //$selected,
                        false, //$nothing,
                        array('data-id' => $competency->evidenceid, 'aria-label' => get_string('linktype', 'hierarchy_competency', format_string($competency->fullname)))
                    );

                    $cell->text = $select;
                    $row->cells[] = $cell;

                    $cell = new html_table_cell();
                    $cell->text = $OUTPUT->action_icon(new moodle_url('/totara/hierarchy/prefix/competency/course/remove.php',
                            array('id' => $evidence->id, 'course' => $courseid, 'returnurl' => $PAGE->url->out())),
                        new pix_icon('t/delete', $str_remove), null, array('class' => 'iconsmall', 'alt' => $str_remove, 'title' => $str_remove));
                    $row->cells[] = $cell;
                }

                $data[] = $row;
            }
            $out->data = $data;

        } else {
            $row = new html_table_row();
            $row->attributes['class'] = 'noitems-coursecompetency';

            $cell = new html_table_cell();
            $cell->colspan = 5;
            $cell->text = html_writer::tag('i', get_string('nocoursecompetencies', 'totara_hierarchy'));
            $row->cell[0] = $cell;

            $out->data = array($row);
        }

        return html_writer::table($out);
    }

    /**
     * Returns an array of competency ids that have been completed by the specified user
     * @param int $userid user to get competencies for
     * @return array list of ids of completed competencies
     */
    static function get_user_completed_competencies($userid) {
        global $DB;

        // We store achievements per assignment - need DISTINCT
        return $DB->get_fieldset_sql(
            "SELECT DISTINCT competency_id
                 FROM {totara_competency_achievement}
                 WHERE user_id = :userid
                   AND status = :status
                   AND proficient = :proficient",
            [
                'userid' => $userid,
                'status' => competency_achievement_entity::ACTIVE_ASSIGNMENT,
                'proficient' => 1
            ]
        );
    }

    /**
     * Provides all competency scale values for all competencies that are proficient.
     *
     * @return array of all competency scale value records
     */
    public static function get_all_proficient_scale_values() {
        global $DB;

        return $DB->get_records_sql(
            "SELECT csv.*
                   FROM {comp_scale_values} csv
                   JOIN {comp_scale} cs ON cs.id = csv.scaleid
                   JOIN {comp_scale_values} csvmin ON cs.minproficiencyid = csvmin.id 
                  WHERE csv.sortorder <= csvmin.sortorder"
        );
    }

    /**
     * Given an Id for a competency scale value, returns whether or not it is proficient.
     *
     * @param int $valueid Id from the comp_scale_values table
     * @return bool True if the value is proficient
     */
    public static function value_is_proficient($valueid) {
        global $DB;

        if (empty($valueid)) {
            return false;
        }

        return $DB->record_exists_sql(
            "SELECT csv.id
                   FROM {comp_scale_values} csv
                   JOIN {comp_scale} cs ON cs.id = csv.scaleid
                   JOIN {comp_scale_values} csvmin ON cs.minproficiencyid = csvmin.id 
                  WHERE csv.sortorder <= csvmin.sortorder
                    AND csv.id = ?
            ",
            array($valueid)
        );
    }


    /**
     * Extra form elements to include in the add/edit form for items of this prefix
     *
     * @param object &$mform Moodle form object (passed by reference)
     */
    function add_additional_item_form_fields(&$mform) {
        global $DB;

        $frameworkid = $this->frameworkid;

        // Get all aggregation methods
        $aggregations = array();
        foreach (self::COMP_AGGREGATION as $title => $key) {
            $aggregations[$key] = get_string('aggregationmethod'.$key, 'totara_hierarchy');
        }

        // Get the name of the framework's scale. (Note this code expects there
        // to be only one scale per framework, even though the DB structure
        // allows there to be multiple since we're using a go-between table)
        $scaledesc = $DB->get_field_sql("
                SELECT s.name
                FROM
                {{$this->shortprefix}_scale} s,
                {{$this->shortprefix}_scale_assignments} a
                WHERE
                a.frameworkid = ?
                AND a.scaleid = s.id
        ", array($frameworkid));

        if (!advanced_feature::is_enabled('competency_assignment')) {
            $mform->addElement('select', 'aggregationmethod', get_string('aggregationmethod', 'totara_hierarchy'), $aggregations);
            $mform->addHelpButton('aggregationmethod', 'competencyaggregationmethod', 'totara_hierarchy');
            $mform->addRule('aggregationmethod', get_string('aggregationmethod', 'totara_hierarchy'), 'required', null);
        }

        $mform->addElement('static', 'scalename', get_string('scale'), ($scaledesc) ? format_string($scaledesc) : get_string('none'));
        $mform->addHelpButton('scalename', 'competencyscale', 'totara_hierarchy');

        $mform->addElement('hidden', 'proficiencyexpected', 1);
        $mform->setType('proficiencyexpected', PARAM_INT);
        $mform->addElement('hidden', 'evidencecount', 0);
        $mform->setType('evidencecount', PARAM_INT);

        if (advanced_feature::is_enabled('competency_assignment')) {
            // Assignment Availability required in
            $checkboxGroup = array();
            $checkboxGroup[] =& $mform->createElement('advcheckbox',
                'assignavailself',
                'assignavail',
                get_string('competencyassignavailabilityself', 'totara_hierarchy'),
                array('group' => 'tw_comp_assign_avail')
            );
            $checkboxGroup[] =& $mform->createElement('advcheckbox',
                'assignavailother',
                'assignavail',
                get_string('competencyassignavailabilityother', 'totara_hierarchy'),
                array('group' => 'tw_comp_assign_avail')
            );

            $mform->addGroup($checkboxGroup, 'assignavail', get_string('competencyassignavailability', 'totara_hierarchy'), array('<br />'), false);
            $mform->addHelpButton('assignavail', 'competencyassignavailability', 'totara_hierarchy');
        }
    }

    /**
     * Format additional fields shown in the competency add/edit forms, proficiencyexpected and evidencecount.
     *
     * @param $item object      The form data object to be formatted
     * @return object           The same object after formatting
     */
    public function process_additional_item_form_fields($item) {

        // Set the default proficiency expected.
        if (!isset($item->proficiencyexpected)) {
            $item->proficiencyexpected = 1;
        }

        // Set the default evidence count.
        if (!isset($item->evidencecount)) {
            $item->evidencecount = 0;
        }

        // Properly format assignment availability from individual form values into a single array
        $item->assignavailability = $item->assignavailability ?? [];
        if (advanced_feature::is_enabled('competency_assignment')) {
            $checkbox_mappings = [
                'assignavailself' => self::ASSIGNMENT_CREATE_SELF,
                'assignavailother' => self::ASSIGNMENT_CREATE_OTHER,
            ];
            foreach ($checkbox_mappings as $checkbox => $availability) {
                if (isset($item->$checkbox) && $item->$checkbox && !in_array($availability, $item->assignavailability)) {
                    $item->assignavailability[] = $availability;
                }
            }
        }

        return $item;
    }

    /**
     * Returns various stats about an item, used for listed what will be deleted
     *
     * @deprecated since Totara 13
     * @param integer $id ID of the item to get stats for
     * @return array Associative array containing stats
     */
    public function get_item_stats($id) {
        debugging('This method has been deprecated since Totara 13, please use get_related_data() instead.');

        global $DB;
        if (!$data = parent::get_item_stats($id)) {
            return false;
        }

        // should always include at least one item (itself)
        if (!$children = $this->get_item_descendants($id)) {
            return false;
        }

        $ids = array_keys($children);

        list($idssql, $idsparams) = sql_sequence('competencyid', $ids);
        // number of comp_record records
        $data['user_achievement'] = $DB->count_records_select('comp_record', $idssql, $idsparams);

        // number of comp_criteria records
        $data['evidence'] = $DB->count_records_select('comp_criteria', $idssql, $idsparams);

        // number of comp_relations records
        list($ids1sql, $ids1params) = sql_sequence('id1', $ids);
        list($ids2sql, $ids2params) = sql_sequence('id2', $ids);
        $data['related'] = $DB->count_records_select('comp_relations',
            $ids1sql . ' OR ' . $ids2sql, array_merge($ids1params, $ids2params));

        return $data;
    }

    /**
     * Returns various stats about an item, used for listed what will be deleted
     *
     * @param int $id ID of the item to get stats for
     * @return array|bool
     */
    public function get_framework_related_data($id) {
        global $DB;

        $data = parent::get_framework_related_data($id);

        // should always include at least one item (itself)
        $children = $DB->get_records('comp', ['frameworkid' => $id]);

        if (!empty($children)) {
            $ids = array_keys($children);

            // number of achievement records
            $data['user_achievement'] = competency_achievement_entity::repository()
                ->where('competency_id', $ids)
                ->where('status', competency_achievement_entity::ACTIVE_ASSIGNMENT)
                ->count();

            // number of comp_criteria records
            [$idssql, $idsparams] = sql_sequence('competencyid', $ids);
            $data['evidence'] = $DB->count_records_select('comp_criteria', $idssql, $idsparams);

            // Count competency assignments
            [$in_sql, $in_params] = $DB->get_in_or_equal($ids, SQL_PARAMS_NAMED);
            // TODO TL-23039 create a proper API method to not have a hard dependency on the assignment table here
            $data['assignments'] = $DB->count_records_select('totara_competency_assignments', "competency_id {$in_sql}", $in_params);

            // number of comp_relations records
            [$ids1sql, $ids1params] = sql_sequence('id1', $ids);
            [$ids2sql, $ids2params] = sql_sequence('id2', $ids);
            $sql = "{$ids1sql} OR {$ids2sql}";
            $params = array_merge($ids1params, $ids2params);
            $data['related'] = $DB->count_records_select('comp_relations', $sql, $params);
        } else {
            $data['user_achievement'] = 0;
            $data['evidence'] = 0;
            $data['assignments'] = 0;
            $data['related'] = 0;
        }

        return $data;
    }

    /**
     * Get data related to this hierarchy item.
     *
     * @param int $id Hierarchy item id
     * @return array|bool
     */
    public function get_related_data($id) {
        global $DB;

        if (!$data = parent::get_related_data($id)) {
            return false;
        }

        // Should always include at least one item (itself).
        if (!$children = $this->get_item_descendants($id)) {
            return false;
        }

        $ids = array_keys($children);

        // It's not really called user achievement anywhere.
        $data['user_achievement'] = competency_achievement_entity::repository()
            ->where('competency_id', $ids)
            ->where('status', competency_achievement_entity::ACTIVE_ASSIGNMENT)
            ->count();

        // Number of comp_criteria records.
        [$ids_sql, $ids_params] = sql_sequence('competencyid', $ids);
        $data['evidence'] = $DB->count_records_select('comp_criteria', $ids_sql, $ids_params);

        // Number of comp_relations records.
        [$ids1sql, $ids1params] = sql_sequence('id1', $ids);
        [$ids2sql, $ids2params] = sql_sequence('id2', $ids);
        $data['related'] = $DB->count_records_select('comp_relations', "{$ids1sql} OR {$ids2sql}", array_merge($ids1params, $ids2params));

        // Count the number of assignments of a competency and its descendants
        [$in_sql, $in_params] = $DB->get_in_or_equal($ids, SQL_PARAMS_NAMED);
        // TODO TL-23039 create a proper API method to not have a hard dependency on the assignment table here
        $data['assignments'] = $DB->count_records_select('totara_competency_assignments', "competency_id {$in_sql}", $in_params);

        return $data;
    }

    /**
     * Prepare a list of related data information to be deleted for hierarchy items
     * It's expected to be called from overridden methods as ALL hierarchy items may have children or custom fields.
     *
     * @param int|int[] $id Id(s) of hierarchy items to delete
     * @return array
     */
    public function prepare_delete_message($id) {
        $messages = parent::prepare_delete_message($id);
        $data = $this->get_all_related_data($id);

        return array_merge($messages, [
            'user_achievement' => get_string('delete_competency_user_achievements', 'totara_hierarchy', $data['user_achievement']),
            'assignments' => get_string('delete_competency_assignments', 'totara_hierarchy', $data['assignments']),
            'evidence' => get_string('delete_competency_evidence_items', 'totara_hierarchy', $data['evidence']),
            'related' => get_string('delete_competency_related_links', 'totara_hierarchy', $data['related']),
        ]);
    }

    /**
     * Given some stats about an item, return a formatted delete message
     *
     * @deprecated since Totara 13
     * @param array $stats Associative array of item stats
     * @return string Formatted delete message
     */
    public function output_delete_message($stats) {
        debugging('This function has been deprecated since Totara 13, please use get_related_data() instead.');
        $message = parent::output_delete_message($stats);

        if ($stats['user_achievement'] > 0) {
            $message .= get_string('deleteincludexuserstatusrecords', 'totara_hierarchy', $stats['user_achievement']) . html_writer::empty_tag('br');
        }

        if ($stats['evidence'] > 0) {
            $message .= get_string('deleteincludexevidence', 'totara_hierarchy', $stats['evidence']) . html_writer::empty_tag('br');
        }

        if ($stats['related'] > 0) {
            $message .= get_string('deleteincludexrelatedcompetencies', 'totara_hierarchy', $stats['related']). html_writer::empty_tag('br');
        }

        return $message;
    }

    /**
     * Prepare framework delete message points
     *
     * @param \stdClass $framework Framework object
     * @return array
     */
    public function prepare_framework_delete_message($framework) {
        $data = $this->get_framework_related_data($framework->id);

        return array_merge(parent::prepare_framework_delete_message($framework), [
            'user_achievement' => get_string('delete_competency_user_achievements', 'totara_hierarchy', $data['user_achievement']),
            'assignments' => get_string('delete_competency_assignments', 'totara_hierarchy', $data['assignments']),
            'evidence' => get_string('delete_competency_evidence_items', 'totara_hierarchy', $data['evidence']),
            'related' => get_string('delete_competency_related_links', 'totara_hierarchy', $data['related']),
        ]);
    }

    /**
     * Update an existing hierarchy item
     *
     * This can include moving to a new location in the hierarchy or changing some of its data values.
     * This method will not update an item's custom field data
     *
     * @param integer $itemid ID of the item to update
     * @param object $newitem An object containing details to be updated
     *                        Only a parentid is required to update the items location, other data such as
     *                        depthlevel, sortthread, path, etc will be handled internally
     * @param boolean $usetransaction If true this function will use transactions (optional, default: true)
     * @param boolean $triggerevent If true, this command will trigger a "{$prefix}_added" event handler.
     * @param boolean $removedesc If true this sets the description field to null,
     *                             descriptions should be set by post-update editor operations
     *
     * @return object|false The updated item, or false if it could not be updated
     */
    function update_hierarchy_item($itemid, $newitem, $usetransaction = true, $triggerevent = true, $removedesc = true) {
        global $DB;

        $olditem = $DB->get_record('comp', array('id' => $itemid));

        if ($usetransaction) {
            $transaction = $DB->start_delegated_transaction();
        }

        $updateditem = parent::update_hierarchy_item($itemid, $newitem, false, false, $removedesc);

        if ($updateditem && isset($newitem->assignavailability)) {
            $this->save_assignment_availabilities($itemid, $newitem->assignavailability);
        }

        if ($usetransaction) {
            $transaction->allow_commit();
        }

        // Raise an event to let other parts of the system know.
        if ($triggerevent) {
            competency_updated::create_from_old_and_new($updateditem, $olditem)->trigger();
        }

        return $updateditem;
    }

    /**
     * Check if competency feature is disabled
     *
     * @return Nothing but print an error if competencies are not enabled
     */
    public static function check_feature_enabled() {
        if (advanced_feature::is_disabled('competencies')) {
            print_error('competenciesdisabled', 'totara_hierarchy');
        }
    }

    /**
     * Return the fields to include in export
     *
     * @return array Array of heading and query field maps
     */
    protected function get_export_fields() {
        $fields = parent::get_export_fields();
        // Show aggregation for non-perform,
        // Show assign availability for perform
        if (!advanced_feature::is_enabled('competency_assignment')) {
            $fields = array_merge($fields, [
                'aggregationmethod' => 'hierarchy.aggregationmethod',
            ]);
        } else {
            $fields = array_merge($fields, [
                'assignavailability' =>
                    "CASE 
                        WHEN assign_availability_self.availability IS NULL AND assign_availability_other.availability IS NULL
                            THEN 'none'
                        WHEN assign_availability_self.availability IS NOT NULL AND assign_availability_other.availability IS NOT NULL
                            THEN 'any'
                        WHEN assign_availability_self.availability IS NOT NULL
                            THEN 'self'
                        WHEN assign_availability_other.availability IS NOT NULL
                            THEN 'other'
                    END",
            ]);
        }

        return $fields;
    }

    /**
     * Add joins for getting assignment availability values
     *
     * @return array
     */
    protected function get_export_join_def() {
        $def = parent::get_export_join_def();

        if (advanced_feature::is_enabled('competency_assignment')) {
            $availabilities = [
                self::ASSIGNMENT_CREATE_SELF => 'self',
                self::ASSIGNMENT_CREATE_OTHER => 'other',
            ];
            foreach ($availabilities as $value => $text) {
                $def['from'] .= " LEFT JOIN (
                    SELECT comp_id, availability FROM {comp_assign_availability} WHERE availability = :{$text}_value
                ) assign_availability_{$text} ON assign_availability_{$text}.comp_id = hierarchy.id";
                $def['params'][$text . '_value'] = $value;
            }
        }
        return $def;
    }

    /**
     * Retrieve the specific hierarchy item from the database
     *
     * @param int $id Id of the item to retrieve
     * @return stdClass
     */
    function retrieve_hierarchy_item($id) {
        if ($item = parent::retrieve_hierarchy_item($id)) {
            $item->assignavailability = builder::table('comp_assign_availability')
                ->where('comp_id', $id)
                ->get()
                ->pluck('availability');

            $checkbox_mappings = [
                self::ASSIGNMENT_CREATE_SELF => 'assignavailself',
                self::ASSIGNMENT_CREATE_OTHER => 'assignavailother',
            ];
            foreach ($item->assignavailability as $availability) {
                $item->{$checkbox_mappings[$availability]} = 1;
            }
        }

        return $item;
    }

    /**
     * Add a new hierarchy item to an existing framework
     *
     * Given an object to insert and a parent id, create a new hierarchy item
     * and attach it to the appropriate location in the hierarchy
     *
     * Also add comp_assign_availability rows if required
     *
     * @param object $item The item to insert. This should contain all data describing the object *except*
     *                     the information related to its location in the hierarchy:
     *                      - depthlevel
     *                      - path
     *                      - frameworkid
     *                      - sortthread
     *                      - parentid
     *                      - timecreated
     * @param integer $parentid The ID of the parent to attach to, or 0 for top level
     * @param integer $frameworkid ID of the parent's framework (optional, unless parentid == 0)
     * @param boolean $usetransaction If true this function will use transactions (optional, default: true)
     * @param boolean $triggerevent If true, this command will trigger a "{$prefix}_added" event handler
     * @param boolean $removedesc
     *
     * @return object|false A copy of the new item, or false if it could not be added
     */
    function add_hierarchy_item($item, $parentid, $frameworkid = null, $usetransaction = true, $triggerevent = true, $removedesc = true) {
        global $DB;

        if ($usetransaction) {
            $transaction = $DB->start_delegated_transaction();
        }

        if ($newitem = parent::add_hierarchy_item($item, $parentid, $frameworkid, false, false, $removedesc)) {
            $item->id = $newitem->id;

            if (isset($item->assignavailability)) {
                $this->save_assignment_availabilities($item->id, $item->assignavailability);
            }
        }

        if ($usetransaction) {
            $transaction->allow_commit();
        }

        // Trigger an event if required.
        if ($triggerevent) {
            $eventclass = "\\hierarchy_{$this->prefix}\\event\\{$this->prefix}_created";
            $eventclass::create_from_instance($newitem)->trigger();
        }

        return $newitem;
    }

    /**
     * Save (create/update) assignment availability data for a competency
     *
     * @param int $comp_id
     * @param array $availabilities
     */
    protected function save_assignment_availabilities(int $comp_id, array $availabilities) {
        if (!advanced_feature::is_enabled('competency_assignment')) {
            return;
        }

        builder::get_db()->transaction(function () use ($comp_id, $availabilities) {
            builder::table('comp_assign_availability')
                ->where('comp_id', $comp_id)
                ->delete();

            foreach ($availabilities as $availability) {
                builder::table('comp_assign_availability')->insert([
                    'comp_id' => $comp_id,
                    'availability' => $availability,
                ]);
            }
        });
    }

    /**
     * Update assignment availability data for competencies
     *
     * @param array $availabilities
     * @param int|null $fw_id
     */
    public static function update_assignment_availabilities(array $availabilities, ?int $fw_id = null) {
        global $DB;

        if (!advanced_feature::is_enabled('competency_assignment')) {
            return;
        }

        $delete_sql = "DELETE from {comp_assign_availability}";
        $delete_params = [];

        $insert_sql =
            "INSERT into {comp_assign_availability}
             (comp_id, availability)   
             SELECT c.id, :availability
               FROM {comp} c";
        $insert_params = [];

        if ($fw_id !== null) {
            $delete_sql .= " WHERE comp_id IN (
                SELECT id FROM {comp}
                 WHERE frameworkid = :fw_id)";
            $delete_params['fw_id'] = $fw_id;

            $insert_sql .= " WHERE c.frameworkid = :fw_id";
            $insert_params['fw_id'] = $fw_id;
        }

        // To avoid differences in databases on using the same table for insert and sub-select table,
        // we are going for the more brute approach - delete competency_assign_availability records and re-insert the onces we need

        /** @var moodle_transaction $transaction */
        $transaction = $DB->start_delegated_transaction();
        $DB->execute($delete_sql, $delete_params);

        foreach ($availabilities as $availability) {
            $insert_params['availability'] = $availability;
            $DB->execute($insert_sql, $insert_params);
        }

        $transaction->allow_commit();
    }

}  // class
