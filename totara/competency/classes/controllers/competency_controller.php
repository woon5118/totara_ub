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
use moodle_url;
use totara_competency\entities\competency;
use totara_competency\entities\competency_framework;
use totara_competency\achievement_configuration;
use totara_competency\achievement_criteria;
use totara_competency\pathway;
use totara_competency\pathway_factory;
use totara_competency\plugin_types;
use totara_core\advanced_feature;
use totara_mvc\admin_controller;
use totara_mvc\tui_view;

global $CFG;
require_once($CFG->dirroot.'/totara/hierarchy/prefix/competency/lib.php');
require_once($CFG->dirroot.'/totara/hierarchy/item/edit_form.php');

class competency_controller extends admin_controller {

    /**
     * @var competency
     */
    protected $competency;

    /**
     * @var competency_framework
     */
    protected $framework;

    protected $admin_external_page_name = 'competencymanage';

    private $prefix = 'competency';

    protected function setup_context(): context {
        return \context_system::instance();
    }

    protected function setup() {
        \hierarchy::check_enable_hierarchy($this->prefix);

        $permissions = $this->get_user_permissions();
        if (!$this->validate_user_access($permissions)) {
            print_error('accessdenied', 'admin');
        }

        $this->competency = competency::repository()
            ->where('id', required_param('id', PARAM_INT))
            ->with('framework')
            ->one();

        $this->framework = $this->competency->framework;

        $this->page->navbar
            ->add(
                format_string($this->framework->fullname),
                new moodle_url('/totara/hierarchy/index.php', [
                    'prefix' => $this->prefix, 'frameworkid' => $this->framework->id
                ])
            );
    }

    public function action_summary() {
        $this->setup();

        $url = new moodle_url('/totara/competency/competency_summary.php', ['id' => $this->competency->id]);
        $title = get_string('competencytitle', 'totara_hierarchy', (object) [
            'framework' => format_string($this->framework->fullname),
            'fullname' => format_string($this->competency->display_name)
        ]);
        $this->page->set_url($url);
        $this->page->set_title($title);
        $this->page->navbar->add(format_string($this->competency->display_name));

        return new tui_view('totara_competency/pages/CompetencySummary', [
            'competency-id' => $this->competency->id,
            'competency-name' => format_string($this->competency->display_name),
            'framework-id' => $this->framework->id,
            'framework-name' => format_string($this->framework->fullname),
            'perform-enabled' => advanced_feature::is_enabled('competency_assignment'),
        ]);
    }

    public function action_edit() {
        global $CFG;
        require_once($CFG->dirroot . '/totara/hierarchy/renderer.php');

        $this->setup();

        $section = $this->get_param('s', PARAM_ALPHA, null, true);

        $exportmethod = "export_{$section}_edit";
        if (!method_exists($this, $exportmethod)) {
            print_error('invalidsection', 'totara_competency', '', $section);
        }

        $heading = get_string('editcompetency', 'totara_competency', format_string($this->competency->display_name));
        $this->page->navbar->add($heading);

        // TODO: Use one single competency entity instead of using both kinds!
        $this->competency = new \totara_competency\entities\competency($this->competency->id);
        $config = new achievement_configuration($this->competency);

        $data = [
            'competency_id' => $this->competency->id,
            'scale_id' => $this->competency->scale->id,
            'heading' => $heading,
            'tabs' => \totara_hierarchy_renderer::get_competency_tabs($this->competency->id, "edit$section"),
            'detail' => $this->$exportmethod(),
            'singleuse' => (int)$config->has_singleuse_criteria(),
            'backurl' => new \moodle_url('/totara/competency/competency_summary.php', ['id' => $this->competency->id]),
        ];

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
        advanced_feature::require('competency_assignment');

        $comp_agg_type = $this->competency->scale_aggregation->type ?? 'highest';

        $results = [
            'templatename' => 'totara_competency/achievement_paths',
            'overall_aggregation' => $comp_agg_type,
            'multivalue_types' => [],
            'aggregation_types' => [],
        ];

        // Pathway types
        $types = plugin_types::get_enabled_plugins('pathway', 'totara_competency');
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
        $aggmethods = achievement_criteria::get_available_overall_aggregation_methods();
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

        // TODO this extract is really dodgy...
        $hierarchy = new \competency();
        extract($hierarchy->get_permissions());

        // Framework
        $url = null;
        if ($canviewframeworks) {
            $url = new \moodle_url('/totara/hierarchy/index.php',
                ['prefix' => $this->prefix, 'frameworkid' => $this->competency->frameworkid]
            );
        }
        $this->page->navbar->add(format_string($this->competency->framework->fullname), $url);

        $name = $this->competency->fullname ?? $this->competency->shortname ?? '';
        if (!empty($name)) {
            $this->page->navbar->add(format_string($name));
        };

        // TODO: Capabilties, Add
    }

}
