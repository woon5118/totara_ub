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
 * @author Carl Anderson <carl.anderson@totaralearning.com>
 * @package totara_reportbuilder
 */

namespace totara_reportbuilder\local\graph;

/**
 * Class to draw Graphs based on reporting data
 *
 * Class graph_base
 * @package totara_reportbuilder\local\graph
 */
abstract class base {
    const GRAPH_CATEGORY_SIMPLE = -1;
    const GRAPH_CATEGORY_COLUMN = -2;

    /** @var \stdClass record from report_builder_graph table */
    protected $record;
    /** @var \reportbuilder the relevant reportbuilder instance */
    protected $report;
    /** @var array category and data series */
    protected $values;
    /** @var int count of records processed - count() in PHP may be very slow */
    protected $processedcount;
    /** @var int index of category, -1 means simple counter, -2 means category in column */
    protected $category;
    /** @var array indexes of series columns */
    protected $series;
    /** @var int legend column index when headings used as category */
    protected $legendcolumn;
    /** @var array Settings supplied by user */
    protected $usersettings;
    /** @var  array list of supported chart types */
    protected static $allowed_types;

    /**
     * Class constructor
     *
     * @param $report \reportbuilder report object used to build this graph
     * @param $autoload bool whether to load the data from the report into the graph immediately (use load() otherwise)
     */
    public function __construct(\reportbuilder $report, bool $autoload = true) {
        $this->report = $report;
        $this->init();

        if ($autoload && $this->is_valid()) {
            $this->load();
        }
    }

    /**
     * Load graph record, and set up graph object
     */
    protected function init(): void {
        global $DB;

        $this->processedcount = 0;
        $this->values = [];
        $this->series = [];

        $this->record = $DB->get_record('report_builder_graph', ['reportid' => $this->report->_id]);
        if (!$this->record) {
            $this->record = new \stdClass();
            $this->record->type = '';
            return;
        }

        // Load user settings.
        if (isset($this->record->settings)) {
            $this->usersettings = fix_utf8(json_decode($this->record->settings, true));
        } else {
            $this->usersettings = [];
        }

        $columns = [];
        $columnsmap = [];
        $i = 0;
        foreach ($this->report->columns as $colkey => $column) {
            if (!$column->display_column(true)) {
                continue;
            }
            $columns[$colkey] = $column;
            $columnsmap[$colkey] = $i++;
        }
        $rawseries = fix_utf8(json_decode($this->record->series, true));
        $series = [];
        foreach ($rawseries as $colkey) {
            $series[$colkey] = $colkey;
        }

        if ($this->record->category === 'columnheadings') {
            $this->category = base::GRAPH_CATEGORY_COLUMN;

            $legendcolumn = $this->record->legend;
            if ($legendcolumn and isset($columns[$legendcolumn])) {
                $this->legendcolumn = $columnsmap[$legendcolumn];
            }

            foreach ($columns as $colkey => $column) {
                if (!isset($series[$colkey])) {
                    continue;
                }
                $i = $columnsmap[$colkey];
                $this->values[$i][base::GRAPH_CATEGORY_COLUMN] = $this->report->format_column_heading($this->report->columns[$colkey], true);
            }
        } else {
            if (isset($columns[$this->record->category])) {
                $this->category = $columnsmap[$this->record->category];
                unset($series[$this->record->category]);
            } else { // Category value 'none' or problem detected.
                $this->category = base::GRAPH_CATEGORY_SIMPLE;
            }

            foreach ($series as $colkey) {
                if (!isset($columns[$colkey])) {
                    continue;
                }
                $i = $columnsmap[$colkey];
                $this->series[$i] = $colkey;
            }
        }
    }

    /**
     * Initialise object with report data
     */
    public function load(): void {
        if (!$this->is_valid()) {
            throw new \moodle_exception('Tried to load invalid graph');
        }

        $order = $this->report->get_report_sort();
        list($sql, $params, $cache) = $this->report->build_query(false, true);

        $reportdb = $this->report->get_report_db();
        $records = $reportdb->get_recordset_sql($sql.$order, $params, 0, $this->get_max_records());
        foreach ($records as $record) {
            $this->add_record($record);
        }
    }

    public function add_record($record): void {
        $recorddata = $this->report->src->process_data_row($record, 'graph', $this->report);
        $this->process_data($recorddata);
    }

    /**
     * Process individual data rows from the report
     *
     * This function creates legend entries, axis labels, and sorts data into datasets.
     *
     * @param $data array report data row
     */
    abstract protected function process_data(array $data): void;

    /**
     * Returns the rendered graph markup
     * @param int $width width of the graph
     * @param int $height height of the graph
     * @return string
     */
    abstract public function render(?int $width = null, ?int $height = null): string;

    public function count_records(): int {
        return $this->processedcount;
    }

    public function get_max_records(): int {
        return $this->record->maxrecords;
    }

    /**
     * Returns whether this graph is valid
     * @return bool
     */
    public function is_valid(): bool {
        if (empty($this->record->type)) {
            return false;
        }

        return (bool)$this->series;
    }

    /**
     * Save the data in this report to store in a cache
     *
     * @return string
     */
    public function save_for_cache(): string {
        // Method stub -- override in subclass
        return '';
    }

    /**
     * Load data into this report from cached data.
     *
     * This function must be implemented if allow_caching() returns true
     *
     * @param string $cached
     * @return bool
     */
    public function load_from_cache(string $cached): bool {
        throw new \coding_exception("The class ".get_called_class()." has not overridden the method load_from_cache. This issue must be fixed by a developer");
    }

    /**
     * Gets the name to display for the charting library
     * @return string display name
     */
    public static function get_name(): string {
        // Simply provide the class name if they haven't set it
        $path = explode('\\', get_called_class());
        return array_pop($path);
    }

    /**
     * Gets the allowed chart types for a the selected graph
     * @return array
     */
    public static function get_allowed_types(): array {
        if (get_called_class() === 'totara_reportbuilder\local\graph\base') {
            $class = get_config('totara_reportbuilder', 'graphlibclass');
            if ($class && class_exists($class)) {
                return $class::get_allowed_types();
            }

            // fallback to chartjs
            return chartjs::get_allowed_types();
        }

        return static::$allowed_types;
    }

    /**
     * Create an instance of a graph using the system's default library
     *
     * @param $report \reportbuilder report object used to build this graph
     * @param $autoload bool whether to load the data from the report into the graph immediately (use load() otherwise)
     * @return base
     */
    final public static function create_graph(\reportbuilder $report, bool $autoload = true): base {
        $class = get_config('totara_reportbuilder', 'graphlibclass');
        if ($class && class_exists($class)) {
            return new $class($report, $autoload);
        }

        // fallback to chartjs
        return new chartjs($report, $autoload);
    }

    /**
     * Create an instance of a graph using data previously saved in a cache
     *
     * @param \reportbuilder $report
     * @param string $cached
     * @return base|null
     */
    final public static function create_from_cache(\reportbuilder $report, string $cached): ?base {
        $class = get_config('totara_reportbuilder', 'graphlibclass');
        if (!class_exists($class) || !$class::allow_caching()) {
            return null;
        }

        $chart = new $class($report, false);
        $chart->load_from_cache($cached);

        return $chart;
    }

    /**
     * Whether this report allows caching of results
     * @return bool
     */
    public static function allow_caching(): bool {
        // To allow caching, set set this to true in subclass
        return false;
    }
}
