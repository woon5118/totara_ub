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
 * @author Simon Player <simon.player@totaralearning.com>
 * @package totara_reportbuilder
 */

namespace totara_reportbuilder;

final class template_helper {

    /**
     * Get all templates.
     *
     * @return string[]
     */
    public static function get_templates() : array {
        static $templates;

        if ($templates === null) {
            $templates = \core_component::get_namespace_classes('rb\template', 'totara_reportbuilder\rb\template\base');
        }

        return $templates;
    }

    /**
     * Get template class object
     *
     * @param string $templateclassname
     * @return object|false
     */
    public static function get_template_object(string $templateclassname) : ?object {
        if (class_exists($templateclassname) && in_array($templateclassname, self::get_templates())) {
            return new $templateclassname();
        }

        return false;
    }

    /**
     * Create report from template
     *
     * @param string $templateclassname The class name of the template
     * @param string &$error Error string to return on failure
     * @return int|bool The created report id or false if failed to create
     */
    public static function create_from_name(string $templateclassname, string &$error = null) : ?int {
        global $DB;

        $template = self::get_template_object($templateclassname);

        if (!$template) {
            return false;
        }

        // Ensure the source exists.
        if (!\reportbuilder::get_source_class($template->source)) {
            $error = "Source {$template->source} not found";
            return false;
        }

        $todb = new \stdClass();
        $todb->shortname            = \reportbuilder::create_shortname($template->shortname);
        $todb->fullname             = $template->fullname;
        $todb->summary              = $template->summary;
        $todb->source               = $template->source;
        $todb->hidden               = $template->hidden;
        $todb->accessmode           = $template->accessmode;
        $todb->contentmode          = $template->contentmode;
        $todb->embedded             = 0;
        $todb->defaultsortcolumn    = $template->defaultsortcolumn;
        $todb->defaultsortorder     = $template->defaultsortorder;

        $transaction = $DB->start_delegated_transaction();
        try {
            $reportid = $DB->insert_record('report_builder', $todb);

            // Add columns.
            $sortorder = 1;
            foreach ($template->columns as $column) {
                // Check data.
                if (!empty($column['aggregate'])) {
                    $classname = "\\totara_reportbuilder\\rb\\aggregate\\" . $column['aggregate'];
                    if (!class_exists($classname)) {
                        $column['aggregate'] = null;
                    }
                }

                $todb = new \stdClass();
                $todb->reportid         = $reportid;
                $todb->type             = $column['type'];
                $todb->value            = $column['value'];
                $todb->heading          = empty($column['customheading']) ? null : $column['heading'];
                $todb->customheading    = empty($column['customheading']) ? 0 : 1;
                $todb->transform        = empty($column['transform']) ? null : $column['transform'];
                $todb->aggregate        = empty($column['aggregate']) ? null : $column['aggregate'];
                $todb->sortorder        = $sortorder++;
                $todb->hidden           = empty($column['hidden']) ? 0 : 1;
                $DB->insert_record('report_builder_columns', $todb);
            }

            // Add filters.
            $sortorder = 1;
            foreach ($template->filters as $filter) {
                $todb = new \stdClass();
                $todb->reportid         = $reportid;
                $todb->type             = $filter['type'];
                $todb->value            = $filter['value'];
                $todb->advanced         = empty($filter['advanced']) ? 0 : 1;
                $todb->defaultvalue     = empty($filter['defaultvalue']) ? null : serialize($filter['defaultvalue']);
                $todb->filtername       = empty($filter['filtername']) ? '' : $filter['filtername'];
                $todb->customname       = empty($filter['filtername']) ? 0 : 1;
                $todb->sortorder        = $sortorder++;
                $todb->region           = isset($filter['region']) ? $filter['region'] : \rb_filter_type::RB_FILTER_REGION_STANDARD;
                $DB->insert_record('report_builder_filters', $todb);
            }

            // Add toolbar search columns.
            foreach ($template->toolbarsearchcolumns as $toolbarsearchcolumn) {
                $todb = new \stdClass();
                $todb->reportid = $reportid;
                $todb->type     = $toolbarsearchcolumn['type'];
                $todb->value    = $toolbarsearchcolumn['value'];
                $DB->insert_record('report_builder_search_cols', $todb);
            }

            // Add graph.
            if ($template->graph) {
                $todb = new \stdClass();
                $todb->reportid     = $reportid;
                $todb->type         = $template->graph['type'];
                $todb->maxrecords   = empty($template->graph['maxrecords']) ? 500 : $template->graph['maxrecords'];
                $todb->category     = $template->graph['category'];
                $todb->series       = $template->graph['series'];
                $todb->settings     = empty($template->graph['settings']) ? '' : $template->graph['settings'];
                $todb->timemodified = time();
                $DB->insert_record('report_builder_graph', $todb);
            }

            // Add content restrictions.
            foreach ($template->contentsettings as $option => $settings) {
                $classname = '\totara_reportbuilder\rb\content\\' . $option;
                if (class_exists($classname)) {
                    foreach ($settings as $name => $value) {
                        if (!\reportbuilder::update_setting($reportid, $classname::TYPE, $name, $value)) {
                            throw new \moodle_exception('Error inserting content restrictions');
                        }
                    }
                }
            }

            // Add access restrictions.
            foreach ($template->accesssettings as $classname => $settings) {
                if (class_exists('totara_reportbuilder\rb\access\\' . $classname)) {
                    foreach ($settings as $name => $value) {
                        if (!\reportbuilder::update_setting($reportid, $classname . "_access", $name, $value)) {
                            throw new \moodle_exception('Error inserting access restrictions');
                        }
                    }
                }
            }

            $transaction->allow_commit();
        } catch (\Exception $e) {
            $transaction->rollback($e);
            $error = $e->getMessage();
            return false;
        }

        return $reportid;
    }
}