<?php
/*
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
 * @author Brendan Cox <brendan.cox@totaralearning.com>
 * @author Riana Rossouw <riana.rossouw@totaralearning.com>
 * @package totara_competency
 */

namespace totara_competency\controllers;

use context;
use tassign_competency\entities\competency_framework;
use totara_competency\achievement_configuration;
use totara_competency\achievement_criteria;
use totara_competency\entities\competency;
use totara_competency\linked_courses;
use totara_competency\pathway;
use totara_competency\pathway_aggregation_factory;
use totara_competency\pathway_factory;
use totara_competency\plugintypes;
use totara_mvc\admin_controller;

require_once($CFG->dirroot.'/totara/hierarchy/prefix/competency/lib.php');
require_once($CFG->dirroot.'/totara/hierarchy/item/edit_form.php');


class competency_controller extends admin_controller {
    /** @var competency $competency */
    private $competency;

    protected $admin_external_page_name = 'competencymanage';

    private $prefix = 'competency';

    private $shortprefix = 'comp';

    private $endpoints = [
        'summary' => '/totara/competency/competency_summary.php',
        'edit' => '/totara/competency/competency_edit.php',
    ];

    protected function setup_context(): context {
        return \context_system::instance();
    }

    public function action_summary() {
        global $CFG;
        require_once ($CFG->dirroot . '/totara/hierarchy/renderer.php');

        \hierarchy::check_enable_hierarchy($this->prefix);

        $permissions = $this->get_user_permissions();
        if (!$this->validate_user_access($permissions)) {
            print_error('accessdenied', 'admin');
        }

        $comp_id = $this->get_param('id', PARAM_INT, null, true);
        $this->competency = new competency($comp_id);

        $heading = get_string('competencytitle',
            'totara_hierarchy',
            (object)['framework' => $this->competency->framework->fullname, 'fullname' => $this->competency->fullname]);

        $data = [
            'comp_id' => $comp_id,
            'title' => $heading,
            'tabs' => \totara_hierarchy_renderer::get_competency_tabs($comp_id, 'summary'),
            'sections' => [
                $this->export_general_summary(),
                $this->export_linkedcourses_summary(),
                $this->export_achievementpaths_summary(),
            ],
        ];

        $this->add_navigation();

        return new \totara_mvc\view(
            'totara_competency/competency_summary',
            $data
        );
    }

    public function action_edit() {
        global $CFG;
        require_once ($CFG->dirroot . '/totara/hierarchy/renderer.php');

        \hierarchy::check_enable_hierarchy($this->prefix);

        $permissions = $this->get_user_permissions();
        if (!$this->validate_user_access($permissions)) {
            print_error('accessdenied', 'admin');
        }

        $comp_id = $this->get_param('id', PARAM_INT, null, true);
        $section = $this->get_param('s', PARAM_ALPHA, null, true);

        $exportmethod = "export_{$section}_edit";
        if (!method_exists($this, $exportmethod)) {
            print_error('invalidsection', 'totara_competency', '', $section);
        }

        $this->competency = new competency($comp_id);
        $config = new achievement_configuration($this->competency);

        $heading = get_string('editcompetency', 'totara_competency', $this->competency->fullname);

        $data = [
            'comp_id' => $comp_id,
            'scale_id' => $this->competency->scale->id,
            'heading' => $heading,
            'tabs' => \totara_hierarchy_renderer::get_competency_tabs($comp_id, $section),
            'detail' => $this->$exportmethod(),
            'singleuse' => (int)$config->has_singleuse_criteria(),
            'backurl' => new \moodle_url('/totara/competency/competency_summary.php', ['id' => $comp_id]),
        ];

        $this->add_navigation();

        return new \totara_mvc\view(
            'totara_competency/competency_edit',
            $data
        );
    }

    /**
     * Validate the user's access to the competency and management of criteria
     *
     * @return bool
     */
    private function validate_user_access($permissions): bool {
        // TODO: Add criteria management capabitilies
        return !empty($permissions) && !empty($permissions['canview']) && !empty($permissions['canmanage']);
    }

    /**
     * Get the user's access permissions for this competency
     *
     * @return array
     */
    private function get_user_permissions(): array {
        global $CFG;

        require_once($CFG->dirroot.'/totara/hierarchy/lib.php');
        require_once($CFG->dirroot.'/totara/hierarchy/prefix/competency/lib.php');

        if (empty($this->permissions)) {
            $hierarchy = \hierarchy::load_hierarchy($this->prefix);
            $this->permissions = $hierarchy->get_permissions();
        }

        return $this->permissions;
    }

    /**
     * Export the general summary data
     *
     * @return array
     */
    private function export_general_summary(): array {
        $results = [
            'editurl' => new \moodle_url($this->endpoints['edit'], ['id' => $this->competency->id, 's' => 'general']),
            'expanded' => true,
            'heading' => get_string('general', 'totara_competency'),
            'key' => 'general',
            'section_data' => [
                'templatename' => 'totara_competency/competency_summary_general',
                'fullname' => $this->competency->fullname ?? $this->competency->shortname ?? '',
                'idnumber' => $this->competency->idnumber ?? '',
                'description' => $this->competency->description ?? '',
                'type' => '',
                'assign_availability' => [],
                'customfields' => [],
            ],
        ];

        $comp_type = $this->competency->comp_type;
        if (is_null($comp_type)) {
            $results['section_data']['type'] = get_string('unclassified', 'totara_hierarchy');
        } else {
            $results['section_data']['type'] = $comp_type->fullname ?? $comp_type->shortname ?? $val['values'][0];
        }

        $assign_availability = $this->competency->assign_availability;
        foreach ($assign_availability as $avail) {
            switch ($avail) {
                case \competency::ASSIGNMENT_CREATE_SELF:
                    $results['section_data']['assign_availability'][] = get_string('competencyassignavailabilityself', 'totara_hierarchy');
                    break;
                case \competency::ASSIGNMENT_CREATE_OTHER:
                    $results['section_data']['assign_availability'][] = get_string('competencyassignavailabilityother', 'totara_hierarchy');
                    break;
            }
        }

        if (count($results['section_data']['assign_availability']) == 0) {
            $results['section_data']['assign_availability'][] = get_string('none', 'totara_competency');
        }

        $customfields = $this->competency->custom_fields;
        if (!empty($customfields)) {
            foreach ($customfields as $cf) {
                $results['section_data']['customfields'][] = [
                    'label' => $cf->title,
                    'value' => $cf->value,
                ];
            }
        }

        return $results;
    }

    /**
     * Export the linked courses data
     *
     * @return array
     */
    private function export_linkedcourses_summary(): array {
        $linkedcourses = linked_courses::get_linked_courses($this->competency->id);

        $results = [
            'editurl' => new \moodle_url($this->endpoints['edit'], ['id' => $this->competency->id, 's' => 'linkedcourses']),
            'expanded' => count($linkedcourses) == 0,
            'heading' => get_string('linkedcourses', 'totara_competency'),
            'key' => 'linkedcourses',
            'section_data' => [
                'templatename' => 'totara_competency/competency_summary_linkedcourses',
            ]
        ];

        if (count($linkedcourses) > 0) {
            $str_mandatory = get_string('mandatory', 'totara_competency');
            $str_optional = get_string('optional', 'totara_competency');

            $results['section_data']['courses'] = [];

            foreach ($linkedcourses as $lcourse) {
                $results['section_data']['courses'][] = [
                    'url' => new \moodle_url('/course/view.php', ['id' => $lcourse->id]),
                    'fullname' => $lcourse->fullname,
                    'linktype' => $lcourse->linktype == linked_courses::LINKTYPE_MANDATORY ? $str_mandatory : $str_optional,
                ];
            }
        }

        return $results;
    }

    /**
     * Export the achievement paths data
     *
     * @return array
     */
    private function export_achievementpaths_summary(): array {
        $scale = $this->competency->scale;
        $agg_type = $this->competency->scale_aggregation_type ?: 'highest';
        $agg = pathway_aggregation_factory::create($agg_type);

        $results = [
            'editurl' => new \moodle_url($this->endpoints['edit'], ['id' => $this->competency->id, 's' => 'achievementpaths']),
            'haspaths' => true,
            'expanded' => false,
            'heading' => get_string('achievementpaths', 'totara_competency'),
            'key' => 'achievementpaths',
            'section_data' => [
                'templatename' => 'totara_competency/competency_summary_paths',
                'overall_aggregation' => $agg->get_title(),
            ],
        ];

        // Pathways
        // Assuming returned in sortorder order
        $config = new achievement_configuration($this->competency);
        $paths = $config->get_active_pathways();

        if (count($paths) == 0) {
            $results['haspaths'] = false;
            $results['expanded'] = true;
            return $results;
        }

        // Order in template format
        $results['paths'] = [];
        $critidx = null;
        $idx = 0;

        foreach ($paths as $pw) {
            // Grouping all single-value pws together
            // Note:  - at the moment there is only 1 single-value pathway plugin (criteria_group)
            //          may need to have a method/array const defining this

            if ($pw->get_classification() == pathway::PATHWAY_MULTI_VALUE) {
                $results['paths'][$idx++] = $pw->export_pathway_view_template();
            } else {
                // Pathways are added under the correct scalevalue
                if (is_null($critidx)) {
                    $critidx = $idx++;
                    $results['paths'][$critidx] = [
                        'pathway_templatename' => 'totara_competency/scalevalue_pathways',
                        'scalevalues' => []];

                    // mustache doesn't like non-following numeric array indexes!!
                    $scalevalue_idx = [];

                    $scalevalues = $scale->scale_values;
                    foreach ($scalevalues as $scalevalue) {
                        if (!isset($scalevalue_idx[$scalevalue->id])) {
                            $scalevalue_idx[$scalevalue->id] = count($scalevalue_idx);
                        }

                        $valueidx = $scalevalue_idx[$scalevalue->id];
                        $results['paths'][$critidx]['scalevalues'][$valueidx] = [
                            'id' => $scalevalue->id,
                            'name' => $scalevalue->name,
                            'proficient' => $scalevalue->proficient,
                        ];
                    }
                }

                $valueidx = $scalevalue_idx[$pw->get_scale_value()->id];

                if (!isset($results['paths'][$critidx]['scalevalues'][$valueidx]['pathways'])) {
                    $results['paths'][$critidx]['scalevalues'][$valueidx]['haspathways'] = true;
                    $results['paths'][$critidx]['scalevalues'][$valueidx]['pathways'] = [];
                }

                $pwbase = &$results['paths'][$critidx]['scalevalues'][$valueidx]['pathways'];

                // Remove the pathway's templatename as it is displayed through the scalevalue template
                $pw_data = $pw->export_pathway_view_template();
                if (isset($pw_data['pathway_templatename'])) {
                    unset($pw_data['pathway_templatename']);
                }

                // We want to show a divider between pathways that result in the same value
                $pw_data['showor'] = count($pwbase) > 0;
                $pwbase[] = $pw_data;
            }
        }

        return $results;
    }

    /**
     * Export template and data for editing general attributes of a competency
     *
     * @return array
     */
    private function export_general_edit() {
        $url = new \moodle_url('/totara/hierarchy/item/edit.php',
            [
                'prefix' => $this->prefix,
                'frameworkid' => $this->competency->frameworkid,
                'id' => $this->competency->id,
            ]
         );
        redirect($url);

        // global $TEXTAREA_OPTIONS;

        // // We are re-using the totara/hierarchy/item/edit_form form as there are only a few changes required for perform
        // $item = (object)$this->competency->to_array();
        // $item->framework = $this->competency->framework->fullname;
        // $item->descriptionformat = FORMAT_HTML;
        // $item = file_prepare_standard_editor($item, 'description', $TEXTAREA_OPTIONS, $TEXTAREA_OPTIONS['context'],
        //     'totara_hierarchy', $this->shortprefix, $item->id);

        // $datatosend = ['prefix' => $this->prefix, 'item' => $item, 'hierarchy' => new \competency()];
        // $form = new \item_edit_form(null, $datatosend);
        // $form->set_data($item);
        // $form->display();
        // return array_merge(['templatename' => $form->get_template()], $form->export_for_template($this->output));
    }

    /**
     * Export template and data for editing linked courses
     *
     * @return array
     */
    private function export_linkedcourses_edit() {
        return ['templatename' => 'totara_competency/competency_edit_linkedcourses'];
    }

    /**
     * Export template and skeleton data for editing achievement paths
     * On load, the javascript will retrieve the pathways and show them
     *
     * @return array
     */
    private function export_achievementpaths_edit() {
        $comp_agg_type = $this->competency->scale_aggregation_type ?: 'highest';

        $results = [
            'templatename' => 'totara_competency/achievement_paths',
            'overall_aggregation' => $comp_agg_type,
            'multivalue_types' => [],
            'aggregation_types' => [],
        ];

        // Pathway types
        $types = plugintypes::get_enabled_plugins('pathway', 'totara_competency');
        foreach ($types as $type) {
            $pw = pathway_factory::create($type);
            $toadd = [
                'type' => $type,
                'name' => $pw->get_title(),
            ];

            if ($pw->get_classification() == pathway::PATHWAY_MULTI_VALUE) {
                $results['multivalue_types'][] = $toadd;
            } else {
                $results['singlevalue_types'][] = $toadd;
            }
        }

        // Get the available pathway aggregation methods
        $aggmethods = achievement_criteria::get_available_pathway_aggregation_methods();
        foreach ($aggmethods as $method) {
            $results['aggregation_types'][] = [
                'type' => $method->get_agg_type(),
                'title' => $method->get_title(),
                'description' => $method->get_description(),
                'editfunction' => $method->get_aggregation_js_function(),
                'selected' => $method->get_agg_type() == $comp_agg_type,
            ];
        }

        return $results;
    }

    /**
     * Add navigation to the top of the page
     */
    private function add_navigation() {

        $hierarchy = new \competency();
        extract($hierarchy->get_permissions());

        // Framework
        $url = null;
        if ($canviewframeworks) {
            $url = new \moodle_url('/totara/hierarchy/index.php', ['prefix' => $this->prefix, 'frameworkid' => $this->competency->frameworkid]);
        }
        $framework = competency_framework::repository()->find($this->competency->frameworkid);
        $this->page->navbar->add(format_string($framework->fullname), $url);

        $name = $this->competency->fullname ?? $this->competency->shortname ?? '';
        if (!empty($name)) {
            $this->page->navbar->add(format_string($name));
        };

        // TODO: Capabilties, Add
    }

}