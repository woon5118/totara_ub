<?php // $Id$
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
 * organisation/lib.php
 *
 * Library to construct organisation hierarchies
 */

use totara_core\advanced_feature;

require_once("{$CFG->dirroot}/totara/hierarchy/lib.php");
require_once("{$CFG->dirroot}/totara/core/utils.php");

/**
 * Oject that holds methods and attributes for organisation operations.
 * @abstract
 */
class organisation extends hierarchy {

    /**
     * The base table prefix for the class
     */
    var $prefix = 'organisation';
    var $shortprefix = 'org';
    protected $extrafields = null;

    /**
     * Run any code before printing header
     * @param $page string Unique identifier for page
     * @return void
     */
    function hierarchy_page_setup($page = '', $item) {
        global $CFG, $PAGE;

        if ($page !== 'item/view') {
            return;
        }

        $id = optional_param('id', 0, PARAM_INT);
        $frameworkid = optional_param('frameworkid', 0, PARAM_INT);

        // Setup custom javascript
        require_once($CFG->dirroot.'/totara/core/js/lib/setup.php');

        // Setup lightbox
        local_js(array(
            TOTARA_JS_DIALOG,
            TOTARA_JS_TREEVIEW
        ));

        $PAGE->requires->strings_for_js(array('linkcompetencies', 'assigngoals'), 'totara_hierarchy');

        $args = array('args'=>'{"id":' . $id . ','
                             . '"frameworkid":' . $frameworkid . ','
                             . '"sesskey":"' . sesskey() .'"}');

        $jsmodule = array(
            'name' => 'totara_organisationitem',
            'fullpath' => '/totara/core/js/organisation.item.js',
            'requires' => array('json'));
        $PAGE->requires->js_init_call('M.totara_organisationitem.init',
            $args, false, $jsmodule);
    }


    /**
     * Delete all data associated with the organisations
     *
     * This method is protected because it deletes the organisations, but doesn't use transactions
     *
     * Use {@link hierarchy::delete_hierarchy_item()} to recursively delete an item and
     * all its children
     *
     * @param array $items Array of IDs to be deleted
     *
     * @return boolean True if items and associated data were successfully deleted
     */
    protected function _delete_hierarchy_items($items) {
        global $DB;

        // First call the deleter for the parent class
        if (!parent::_delete_hierarchy_items($items)) {
            return false;
        }

        // TODO: Not using the comp_record table anymore. Leaving it in for v1 as it will not have any adverse effect
        // nullify all references to these organisations in comp_record table
        $prefix = hierarchy::get_short_prefix('competency');

        list($in_sql, $params) = $DB->get_in_or_equal($items);

        $sql = "UPDATE {{$prefix}_record}
            SET organisationid = NULL
            WHERE organisationid $in_sql";
        $DB->execute($sql, $params);

        // nullify all references to these organisations in course_completions table
        $sql = "UPDATE {course_completions}
            SET organisationid = NULL
            WHERE organisationid $in_sql";
        $DB->execute($sql, $params);

        // Remove all references to these organisations in job_assignment table.
        foreach ($items as $organisationid) {
            \totara_job\job_assignment::update_to_empty_by_criteria('organisationid', $organisationid);
        }

        return true;
    }


    /**
     * Print any extra markup to display on the hierarchy view item page
     * @param $item object Organisation being viewed
     * @return void
     */
    function display_extra_view_info($item, $frameworkid=0) {
        global $CFG, $OUTPUT, $PAGE;

        require_once($CFG->dirroot . '/totara/hierarchy/prefix/goal/lib.php');

        $sitecontext = context_system::instance();
        $can_edit = has_capability('totara/hierarchy:updateorganisation', $sitecontext);
        $comptype = optional_param('comptype', 'competencies', PARAM_TEXT);
        $renderer = $PAGE->get_renderer('totara_hierarchy');

        if (advanced_feature::is_enabled('competencies') && advanced_feature::is_disabled('competency_assignment')) {
            // Spacing.
            echo html_writer::empty_tag('br');

            echo html_writer::start_tag('div', array('class' => "list-linkedcompetencies"));
            echo $OUTPUT->heading(get_string('linkedcompetencies', 'totara_hierarchy'));

            echo $this->print_comp_framework_picker($item->id, $frameworkid);

            if ($comptype == 'competencies') {
                // Display assigned competencies.
                $items = $this->get_assigned_competencies($item, $frameworkid);
                $addurl = new moodle_url('/totara/hierarchy/prefix/organisation/assigncompetency/find.php', array('assignto' => $item->id));
                $displaytitle = 'linkedcompetencies';
            } else {
                if ($comptype == 'comptemplates') {
                    // Display assigned competencies.
                    $items = $this->get_assigned_competency_templates($item, $frameworkid);
                    $addurl = new moodle_url('/totara/hierarchy/prefix/organisation/assigncompetencytemplate/find.php', array('assignto' => $item->id));
                    $displaytitle = 'assignedcompetencytemplates';
                }
            }
            echo $renderer->print_hierarchy_items($frameworkid, $this->prefix, $this->shortprefix, $displaytitle, $addurl, $item->id, $items, $can_edit);
            echo html_writer::end_tag('div');
        }

        // Spacing.
        echo html_writer::empty_tag('br');

        // Display all goals assigned to this item.
        if (advanced_feature::is_enabled('goals') && !is_ajax_request($_SERVER)) {
            $addgoalparam = array('assignto' => $item->id, 'assigntype' => GOAL_ASSIGNMENT_ORGANISATION, 'sesskey' => sesskey());
            $addgoalurl = new moodle_url('/totara/hierarchy/prefix/goal/assign/find.php', $addgoalparam);
            echo html_writer::start_tag('div', array('class' => 'list-assigned-goals'));
            echo $OUTPUT->heading(get_string('goalsassigned', 'totara_hierarchy'));
            echo $renderer->assigned_goals($this->prefix, $this->shortprefix, $addgoalurl, $item->id);
            echo html_writer::end_tag('div');
        }
    }

    /**
     * Returns an array of assigned competencies that are assigned to the organisation
     * @param $item object|int Organisation being viewed
     * @param $frameworkid int If set only return competencies for this framework
     * @param $excluded_ids array an optional set of ids of competencies to exclude
     * @return array List of assigned competencies
     */
    function get_assigned_competencies($item, $frameworkid=0, $excluded_ids=false) {
        global $DB;
        if (is_object($item)) {
            $itemid = $item->id;
        } else if (is_numeric($item)) {
            $itemid = $item;
        } else {
            return false;
        }

        $sql = "SELECT
                    c.*,
                    cf.id AS fid,
                    cf.fullname AS framework,
                    ct.fullname AS type,
                    oc.id AS aid,
                    oc.linktype as linktype
                FROM
                    {org_competencies} oc
                INNER JOIN
                    {comp} c
                 ON oc.competencyid = c.id
                INNER JOIN
                    {comp_framework} cf
                 ON c.frameworkid = cf.id
                LEFT JOIN
                    {comp_type} ct
                 ON c.typeid = ct.id
                WHERE
                    oc.templateid IS NULL
                AND oc.organisationid = ?
            ";
        $params = array($itemid);

        if (!empty($frameworkid)) {
            $sql .= " AND c.frameworkid = ?";
            $params[] = $frameworkid;
        }
        if (is_array($excluded_ids) && !empty($excluded_ids)) {
            $ids = implode(',', $excluded_ids);
            list($in_sql, $in_params) = $DB->get_in_or_equal($excluded_ids, SQL_PARAMS_QM, 'param', false);
            $sql .= " AND c.id $in_sql";
            $params = array_merge($params, $in_params);
        }

        $sql .= " ORDER BY c.fullname";
        return $DB->get_records_sql($sql, $params);
    }

   /**
    * Gets assigned competency templates
    *
    * @param int|object $item the item id
    * @param int $frameworkid default 0 the framework id
    * @return array
    */
    function get_assigned_competency_templates($item, $frameworkid=0) {
        global $DB;

        if (is_object($item)) {
            $itemid = $item->id;
        } else if (is_numeric($item)) {
            $itemid = $item;
        }

        $sql = "SELECT
                    c.*,
                    cf.id AS fid,
                    cf.fullname AS framework,
                    oc.id AS aid
                FROM
                    {org_competencies} oc
                INNER JOIN
                    {comp_template} c
                 ON oc.templateid = c.id
                INNER JOIN
                    {comp_framework} cf
                 ON c.frameworkid = cf.id
                WHERE
                    oc.competencyid IS NULL
                AND oc.organisationid = ?
            ";

        if (!empty($frameworkid)) {
            $sql .= " AND c.frameworkid = ?";
        }

        return $DB->get_records_sql($sql, array($itemid, $frameworkid));
    }

   /**
    * prints competency framework pickler
    *
    * @param int $organisationid
    * @param int $currentfw
    */
    function print_comp_framework_picker($organisationid, $currentfw) {
        global $CFG, $DB, $OUTPUT;

        require_once($CFG->dirroot.'/totara/hierarchy/prefix/competency/lib.php');

        $edit = optional_param('edit', 'off', PARAM_TEXT);

        $competency = new competency();
        $frameworks = $competency->get_frameworks();

        $assignedcounts = $DB->get_records_sql_menu("SELECT comp.frameworkid, COUNT(*)
            FROM {org_competencies} orgcomp
            INNER JOIN {comp} comp ON orgcomp.competencyid=comp.id
            WHERE orgcomp.organisationid= ? GROUP BY comp.frameworkid", array($organisationid));

        $out = '';

        $out .= html_writer::start_tag('div', array('class' => "frameworkpicker"));
        if (!empty($frameworks)) {
            $fwoptions = array();
            foreach ($frameworks as $fw) {
                $count = isset($assignedcounts[$fw->id]) ? $assignedcounts[$fw->id] : 0;
                $fwoptions[$fw->id] = $fw->fullname . " ({$count})";
            }
            $fwoptions = count($fwoptions) > 1 ? array(0 => get_string('all')) + $fwoptions : $fwoptions;
            $out .= html_writer::start_tag('div', array('class' => "hierarchyframeworkpicker"));

            $out .= get_string('filterframework', 'totara_hierarchy') . $OUTPUT->single_select(
                new moodle_url('/totara/hierarchy/item/view.php', array('id' => $organisationid, 'edit' => $edit, 'prefix' => 'organisation')),
                'framework',
                $fwoptions,
                $currentfw,
                null,
                'switchframework');

            $out .= html_writer::end_tag('div');
        } else {
            $out .= get_string('competencynoframeworks', 'totara_hierarchy');
        }
        $out .= html_writer::end_tag('div');

        return $out;
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

        list($idssql, $idsparams) = sql_sequence('organisationid', $ids);
        // Number of job assignment records with matching organisation.
        $data['job_assignment'] = $DB->count_records_select('job_assignment', $idssql, $idsparams);

        // number of assigned competencies
        $data['assigned_comps'] = $DB->count_records_select('org_competencies', $idssql, $idsparams);

        return $data;
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

        if ($stats['job_assignment'] > 0) {
            $message .= get_string('organisationdeleteincludexjobassignments', 'totara_hierarchy', $stats['job_assignment']) .
                html_writer::empty_tag('br');
        }

        if ($stats['assigned_comps'] > 0) {
            $message .= get_string('organisationdeleteincludexlinkedcompetencies', 'totara_hierarchy', $stats['assigned_comps']).
                html_writer::empty_tag('br');
        }

        return $message;
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

        [$ids_sql, $ids_params] = $DB->get_in_or_equal($ids, SQL_PARAMS_NAMED);

        // Count the number of competency assignments for the framework and its descendants
        // TODO TL-23039 create a proper API method to not have a hard dependency on the assignment table here
        $data['comp_assignments'] = $DB->count_records_select('totara_competency_assignments', "user_group_type = 'organisation' AND user_group_id {$ids_sql}", $ids_params);

        // Number of job assignment records with matching organisation.
        $data['job_assignment'] = $DB->count_records_select('job_assignment', "organisationid {$ids_sql}", $ids_params);

        // Number of assigned competencies.
        $data['assigned_comps'] = $DB->count_records_select('org_competencies', "organisationid {$ids_sql}", $ids_params);

        // Number of related goals.
        $data['related_goals'] = $DB->count_records_select('goal_grp_org', "orgid {$ids_sql}", $ids_params);

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
        $children = $DB->get_records('org', ['frameworkid' => $id]);

        if (!empty($children)) {
            $ids = array_keys($children);

            [$ids_sql, $ids_params] = $DB->get_in_or_equal($ids, SQL_PARAMS_NAMED);

            // Number of job assignment records with matching organisation.
            $data['job_assignment'] = $DB->count_records_select('job_assignment', "organisationid {$ids_sql}", $ids_params);

            // Number of assigned competencies.
            $data['assigned_comps'] = $DB->count_records_select('org_competencies', "organisationid {$ids_sql}", $ids_params);

            // Number of related goals.
            $data['related_goals'] = $DB->count_records_select('goal_grp_org', "orgid {$ids_sql}", $ids_params);

            // Count the number of competency assignments for the framework and its descendants
            // TODO TL-23039 create a proper API method to not have a hard dependency on the assignment table here
            $data['comp_assignments'] = $DB->count_records_select('totara_competency_assignments', "user_group_type = 'organisation' AND user_group_id {$ids_sql}", $ids_params);
        } else {
            $data['job_assignment'] = 0;
            $data['assigned_comps'] = 0;
            $data['related_goals'] = 0;
            $data['comp_assignments'] = 0;
        }

        return $data;
    }

    /**
     * Prepare a list of related data information to be deleted for hierarchy items
     * It's expected to be called from overridden methods as ALL hierarchy items may have children or custom fields.
     *
     * @param \int|int[] $id Id(s) of hierarchy items to delete
     * @return array
     */
    public function prepare_delete_message($id) {
        $messages = parent::prepare_delete_message($id);
        $data = $this->get_all_related_data($id);

        return array_merge($messages, [
            'job_assignment' => get_string('delete_organisation_related_job_assignments', 'totara_hierarchy', $data['job_assignment']),
            'assigned_comps' => get_string('delete_organisation_linked_competencies', 'totara_hierarchy', $data['assigned_comps']),
            'assigned_goals' => get_string('delete_organisation_linked_goals', 'totara_hierarchy', $data['related_goals']),
            'comp_assignments' => get_string('delete_organisation_archive_assignments', 'totara_hierarchy', $data['comp_assignments']),
        ]);
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
            'job_assignment' => get_string('delete_organisation_related_job_assignments', 'totara_hierarchy', $data['job_assignment']),
            'assigned_comps' => get_string('delete_organisation_linked_competencies', 'totara_hierarchy', $data['assigned_comps']),
            'related_goals' => get_string('delete_organisation_linked_goals', 'totara_hierarchy', $data['related_goals']),
            'comp_assignments' => get_string('delete_organisation_archive_assignments', 'totara_hierarchy', $data['comp_assignments']),
        ]);
    }
}
