<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2018 onwards Totara Learning Solutions LTD
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
 * @author Simon Coggins <simon.coggins@totaralearning.com>
 * @package totara_workflow
 */

namespace totara_workflow\workflow;

defined('MOODLE_INTERNAL') || die();

/**
 * Interface for representation of workflows.
 */
abstract class base implements \templatable {

    /**
     * @var \totara_workflow\workflow_manager\base $manager
     */
    protected $manager;

    public function __construct(\totara_workflow\workflow_manager\base $workflowmanager) {
        $this->manager = $workflowmanager;
    }

    /**
     * Name of workflow
     *
     * @return string
     */
    abstract public function get_name(): string;

    /**
     * Description of workflow (optional)
     *
     * Plain text description of the workflow (for the purpose of helping
     * the user decide whether to choose it).
     *
     * @return string
     */
    public function get_description(): string {
        return '';
    }

    /**
     * Get image for workflow (optional)
     *
     * Optional image to be displayed when selecting workflow.
     * Will be cropped and resized to fill 100x100px area.
     *
     * @return moodle_url URL of image resource.
     */
    public function get_image(): ?\moodle_url {
        return null;
    }


    /**
     * Check if this workflow is available for use.
     * Will check access control for manager and
     * workflow and that workflow is enabled.
     *
     * @return bool True if the workflow is available.
     */
    final public function is_available(): bool {

        $workflows = $this->manager->get_workflows();
        return in_array(static::class, array_keys($workflows));
    }

    /**
     * Access control checks required to use this workflow (optional)
     *
     * @return bool
     */
    public function can_access(): bool {
        return true;
    }

    /**
     * Extract component, manager and workflow data from the classname.
     *
     * @param string $workflowclass Name of the workflow class to split.
     * @return array Array of [$component, $manager, $workflow] as strings.
     */
    protected static function split_classname(string $workflowclass) {
        if (!preg_match('/^([a-z][a-z0-9_]*)\\\\workflow\\\\([a-zA-Z0-9_-]+)\\\\([a-zA-Z0-9_-]+)$/', $workflowclass, $matches)) {
            throw new \coding_exception("Workflow class '{$workflowclass}' does not match expected format.");
        }
        $component = $matches[1] ?? null;
        $manager = $matches[2] ?? null;
        $workflow = $matches[3] ?? null;

        return [$component, $manager, $workflow];
    }

    /**
     * Destination when selecting this workflow.
     *
     * Can be implemented in workflow definition to override default
     * workflow form page.
     *
     * @return \moodle_url
     */
    protected function get_workflow_url(): \moodle_url {
        list($component, $manager, $workflow) = self::split_classname(get_class($this));

        return new \moodle_url('/totara/workflow/workflow.php', [
            'component' => $component,
            'manager' => $manager,
            'workflow' => $workflow,
        ]);
    }

    /**
     * Return final url for workflow.
     *
     * Retrieve workflow URL and adds in additional parameters from manager.
     *
     * @return \moodle_url
     */
    public final function get_url(): \moodle_url {
        $url = $this->get_workflow_url();
        $url->params(
            $this->manager->get_params()
        );
        return $url;
    }

    /**
     * Returns the workflow's manager instance.
     *
     * Must be implemented in workflow definition.
     *
     * @return string name of workflow's manager class.
     */
    abstract public static function get_manager_class(): string;

    /**
     * Return the data required to render the workflow template.
     *
     * @param \renderer_base Output renderer.
     * @return array Template context data.
     */
    public function export_for_template(\renderer_base $output): array {

        list($component, $manager, $workflow) = self::split_classname(get_class($this));

        return [
            'url' => $this->get_url(),
            'name' => $this->get_name(),
            'description' => $this->get_description(),
            'imageurl' => $this->get_image(),
            'component' => $component,
            'manager' => $manager,
            'workflow' => $workflow,
            'enabled' => (int)$this->is_enabled(),
        ];
    }

    /**
     * Obtain an instance of the workflow.
     *
     * Helper as workflow instances are created via their manager.
     *
     * @return \totara_workflow\workflow\base Workflow instance.
     */
    public static function instance(): \totara_workflow\workflow\base {
        $managername = static::get_manager_class();
        $wm = new $managername();
        $workflow = $wm->get_workflow(static::class);
        return $workflow;
    }

    /**
     * Add additional form elements into this workflow's form.
     *
     * Used when the workflow or manager needs to pass data through to processing.
     *
     * @param \totara_form\model $model Form model to add elements to.
     */
    public function add_workflow_form_elements(\totara_form\model $model) {

        $this->manager->add_workflow_manager_form_elements($model);
    }

    /**
     * Optional method to allow workflow to add
     * additional required data for form.
     *
     * @return array Key/value data array.
     */
    public function get_workflow_data(): array {
        return [];
    }

    /**
     * Actual method for getting current data for
     * form. Allows manager to pass additional
     * required data.
     *
     * @return array Key/value data array.
     */
    public final function get_current_data(): array {

        $component = required_param('component', PARAM_COMPONENT);
        $manager = required_param('manager', PARAM_ALPHANUMEXT);
        $workflow = required_param('workflow', PARAM_ALPHANUMEXT);
        $data = [
            'component' => $component,
            'manager' => $manager,
            'workflow' => $workflow,
        ];

        return array_merge(
            $this->get_workflow_data(),
            $this->manager->get_params(),
            $data
        );
    }

    /**
     * Returns true if the workflow has been enabled via the workflow management page.
     *
     * @return bool True if enabled.
     */
    public final function is_enabled(): bool {
        return (bool) get_config('totara_workflow', get_class($this));
    }

    /**
     * Enable this workflow.
     */
    public final function enable() {
        set_config(get_class($this), 1, 'totara_workflow');
    }

    /**
     * Disable this workflow.
     */
    public final function disable() {
        set_config(get_class($this), 0, 'totara_workflow');
    }

    /**
     * Set workflow parameters.
     *
     * @param array $params Key/value array of parameters to store.
     */
    public function set_params(array $params) {
        $this->manager->set_params($params);
    }

    /**
     * Get workflow parameters.
     *
     * @return array Array of parameters.
     */
    public function get_params(): array {
        return $this->manager->get_params();
    }

    /**
     * Get data required by this workflow's manager.
     *
     * @return array Workflow manager data.
     */
    public function get_workflow_manager_data() {
        return $this->manager->get_workflow_manager_data();
    }
}
