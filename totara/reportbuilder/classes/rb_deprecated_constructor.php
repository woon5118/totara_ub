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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Yuliya Bozhko <yuliya.bozhko@totaralearning.com>
 * @package totara_reportbuilder
 */

namespace totara_reportbuilder;

defined('MOODLE_INTERNAL') || die();

/**
 * Old constructor that was doing a lot of extra unnecessary stuff.
 * Immediately deprecated in T12, to be deleted in T13.
 *
 * @deprecated
 */
trait rb_deprecated_constructor {
    private function __old_construct($id=null, $shortname=null, $embed_deprecated=false, $sid=null, $reportfor=null,
                                $nocache = false, $embeddata = array(), \rb_global_restriction_set $globalrestrictionset = null) {
        global $USER, $DB, $CFG;

        $report = false;
        if ($id != null) {
            // look for existing report by id
            $report = $DB->get_record('report_builder', array('id' => $id), '*', IGNORE_MISSING);
        } else if ($shortname != null) {
            // look for existing report by shortname
            $report = $DB->get_record('report_builder', array('shortname' => $shortname), '*', IGNORE_MISSING);
        } else {
            // either id or shortname is required
            print_error('noshortnameorid', 'totara_reportbuilder');
        }

        // Handle if report not found in db.
        $embed = null;
        if (!$report) {
            // Determine if this is an embedded report with a missing embedded record.
            if ($embed_deprecated) {
                $embed = $embed_deprecated;
            } else if ($shortname !== null) {
                $embed = reportbuilder_get_embedded_report_object($shortname, $embeddata);
            }
            if ($embed) {
                // This is an embedded report - maybe this is the first time we have run it, so try to create it.
                if (! $id = reportbuilder_create_embedded_record($shortname, $embed, $error)) {
                    print_error('error:creatingembeddedrecord', 'totara_reportbuilder', '', $error);
                }
                $report = $DB->get_record('report_builder', array('id' => $id));
            }
        }

        if (!$report) {
            print_error('reportwithidnotfound', 'totara_reportbuilder', '', $id);
        }

        // If this is an embedded report then load the embedded report object.
        $embedgrrsupport = true;
        if ($report->embedded && !$embed) {
            $embed = reportbuilder_get_embedded_report_object($report->shortname, $embeddata);
            if ($embed instanceof \rb_base_embedded) {
                $embedgrrsupport = $embed->embedded_global_restrictions_supported();
            }
        }

        // Load restriction set.
        if (!empty($CFG->enableglobalrestrictions) and $globalrestrictionset !== null && $embedgrrsupport) {
            $this->globalrestrictionset = $globalrestrictionset;
            $nocache = true; // Caching cannot work together with restrictions, sorry.
            $usesourcecache = false; // Cannot use the source cache if we have a restrictionset.
        } else {
            $this->globalrestrictionset = null;
            $usesourcecache = true; // There is no restrictionset so we can use the sourcecache.
        }

        $this->_id = $report->id;
        $this->source = $report->source;
        $this->src = self::get_source_object($this->source, $usesourcecache, true, $this->globalrestrictionset);
        $this->shortname = $report->shortname;
        $this->fullname = $report->fullname;
        $this->hidden = $report->hidden;
        $this->initialdisplay = $report->initialdisplay;
        $this->toolbarsearch = $report->toolbarsearch;
        $this->description = $report->description;
        $this->embedded = $report->embedded;
        $this->globalrestriction = $report->globalrestriction;
        $this->contentmode = $report->contentmode;
        // Store the embedded URL for embedded reports only.
        if ($report->embedded && $embed) {
            $this->embeddedurl = $embed->url;
        }
        $this->embedobj = $embed;
        $this->recordsperpage = $report->recordsperpage;
        $this->defaultsortcolumn = $report->defaultsortcolumn;
        $this->defaultsortorder = $report->defaultsortorder;
        $this->showtotalcount = (!empty($report->showtotalcount) && !empty(get_config('totara_reportbuilder', 'allowtotalcount')));
        $this->_sid = $sid;

        // Assign a unique identifier for this report.
        $this->uniqueid = $report->id;

        // If uniqueid was overridden, apply it here and reset.
        if (isset(self::$overrideuniquid)) {
            $this->uniqueid = self::$overrideuniquid;
            self::$overrideuniquid = null;
        }

        // If ignoreparams was overridden, apply it here and reset.
        if (isset(self::$overrideignoreparams)) {
            $this->ignoreparams = self::$overrideignoreparams;
            self::$overrideignoreparams = null;
        }

        // Assume no grouping initially.
        $this->grouped = false;
        $this->pregrouped = false;

        $this->cacheignore = $nocache;
        if ($this->src->cacheable) {
            $this->cache = $report->cache;
            $this->cacheschedule = $DB->get_record('report_builder_cache', array('reportid' => $this->_id), '*', IGNORE_MISSING);
        } else {
            $this->cache = 0;
            $this->cacheschedule = false;
        }

        $this->useclonedb = $report->useclonedb;

        // Determine who is viewing or receiving the report.
        // Used for access and content restriction checks.
        if (isset($reportfor)) {
            $this->reportfor = $reportfor;
        } else {
            $this->reportfor = $USER->id;
        }

        if ($sid) {
            $this->restore_saved_search();
        }

        $this->_paramoptions = $this->src->paramoptions;

        if ($embed) {
            $this->_embeddedparams = $embed->embeddedparams;
        }
        $this->_params = $this->get_current_params();

        // Run the embedded report's capability checks.
        if ($embed) {
            if (defined('REPORTBUIDLER_MANAGE_REPORTS_PAGE')) {
                // The is_capable is intended for report viewing only!
                require_capability('totara/reportbuilder:manageembeddedreports', \context_system::instance());
                if (!method_exists($embed, 'is_capable')) {
                    debugging("Missing is_capable() method in embedded report {$embed->fullname}", DEBUG_DEVELOPER);
                }
            } else if (method_exists($embed, 'is_capable')) {
                if (!$embed->is_capable($this->reportfor, $this)) {
                    print_error('nopermission', 'totara_reportbuilder');
                }
            } else {
                debugging('This report doesn\'t implement is_capable().
                    Sidebar filters will only use form submission rather than instant filtering.', DEBUG_DEVELOPER);
            }
        }

        // Allow sources to modify itself based on params.
        $this->src->post_params($this);

        $this->_base = $this->src->base . ' base';

        $this->requiredcolumns = array();
        if (!empty($this->src->requiredcolumns)) {
            foreach ($this->src->requiredcolumns as $column) {
                $key = $column->type . '-' . $column->value;
                $this->requiredcolumns[$key] = $column;
            }
        }
        if (!empty($embed->requiredcolumns)) {
            foreach ($embed->requiredcolumns as $column) {
                $key = $column->type . '-' . $column->value;
                $this->requiredcolumns[$key] = $column;
            }
        }

        $this->columnoptions = array();
        foreach ($this->src->columnoptions as $columnoption) {
            $key = $columnoption->type . '-' . $columnoption->value;
            if (isset($this->columnoptions[$key])) {
                debugging("Duplicate column option $key detected in source " . get_class($this->src), DEBUG_DEVELOPER);
            }
            $this->columnoptions[$key] = $columnoption;
        }

        $this->columns = $this->get_columns();

        // Some sources add joins when generating new columns.
        $this->_joinlist = $this->src->joinlist;

        $this->contentoptions = $this->src->contentoptions;

        $this->filteroptions = array();
        foreach ($this->src->filteroptions as $filteroption) {
            $key = $filteroption->type . '-' . $filteroption->value;
            if (isset($this->filteroptions[$key])) {
                debugging("Duplicate filter option $key detected in source " . get_class($this->src), DEBUG_DEVELOPER);
            }
            $this->filteroptions[$key] = $filteroption;
        }

        $this->filters = $this->get_filters();

        $this->searchcolumns = $this->get_search_columns();

        // Make sure everything is compatible with caching, if not disable the cache.
        if ($this->cache) {
            if ($this->get_caching_problems()) {
                $this->cache = 0;
            }
        }

        $this->process_filters();

        // Allow the source to configure additional restrictions,
        // note that columns must not be changed any more here
        // because we may have already decided if cache is used.
        $colkeys = array_keys($this->columns);
        $reqkeys = array_keys($this->requiredcolumns);
        $this->src->post_config($this);
        if ($colkeys != array_keys($this->columns) or $reqkeys != array_keys($this->requiredcolumns)) {
            throw new \coding_exception('Report source ' . get_class($this->src) .
                                       '::post_config() must not change report columns!');
        }

        $this->ready();
        $this->initialised();
    }
}
