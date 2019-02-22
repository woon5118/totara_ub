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

namespace totara_reportbuilder\graph;


final class chartjs extends base {
    /** @var array ChartJS settings */
    protected $chartsettings;
    /** @var array list of chart labels */
    protected $labels;

    protected $colors;

    protected function init() : void {
        parent::init();

        $this->chartsettings = array(
            'responsive' => true,
            'maintainAspectRatio' => false,
        );

        if ($this->record->type !== 'pie') {
            $this->chartsettings['scales'] = array(
                'xAxes' => array(
                    array(
                        'stacked' => empty($this->record->stacked) ? false : true,
                        'ticks' => array(
                            'beginAtZero' => true,
                        ),
                        'gridLines' => array(
                            'display' => $this->record->type === 'bar',
                        ),
                    )
                ),
                'yAxes' => array(
                    array(
                        'stacked' => empty($this->record->stacked) ? false : true,
                        'ticks' => array(
                            'beginAtZero' => true,
                        ),
                        'gridLines' => array(
                            'display' => $this->record->type !== 'bar',
                        ),
                    )
                )
            );
        }

        $this->labels = array();
        $this->colors = array(
            '#3869B1',
            '#DA7E31',
            '#3F9852',
            '#CC2428',
            '#958C3D',
            '#6B4C9A',
            '#8C8C8C',
        );

        //Create an array entry for each series
        foreach ($this->series as $k => $item) {
            if (empty($item)) {
                continue;
            }

            $this->values[] = array(
                'label' => $this->report->format_column_heading($this->report->columns[$item], true), //TODO: make this work
                'fill' => $this->record->type === 'area',
                'showLine' => $this->record->type !== 'scatter',
                'data' => array(),
                'backgroundColor' => array(), //for pie charts -- will be overridden further down
            );
        }

        if ($this->record->type !== 'pie') {
            foreach ($this->values as $k => $val) {
                $this->values[$k]['backgroundColor'] = $this->colors[$k % count($this->colors)];
            }
        }
    }

    protected function process_data($data): void {

        $values = array();
        if ($this->category == base::GRAPH_CATEGORY_SIMPLE) {
            $label = $this->processedcount + 1;
        } else {
            $label = $data[$this->category];
        }
        $this->labels[] = $label;

        foreach ($this->series as $i => $key) {
            $val = $data[$i];
            $values[] = $val;
        }

        foreach ($values as $k => $val) {
            if ($this->record->type === 'scatter') {
                $this->values[$k]['data'][] = array('x' => $label, 'y' => $val);
            } else {
                $this->values[$k]['data'][] = $val;
            }

            if ($this->record->type === 'pie') {
                $this->values[$k]['backgroundColor'][] = $this->colors[$this->processedcount % count($this->colors)];
            }

            $this->values[$k]['stack'] = $k;
        }

        $this->processedcount++;
    }

    public function render(int $width = null, int $height = null): string {
        global $OUTPUT;

        // Since we're using the ChartJS responsive setting, we ignore $width so it will grow correctly
        $context = array(
            'height' => $height,
            'options' => json_encode(array(
                'type' => $this->convert_type($this->record->type),
                'data' => array(
                    'labels' => $this->labels,
                    'datasets' => $this->values,
                ),
                'options' => $this->chartsettings,
            ))
        );

        return $OUTPUT->render_from_template('totara_reportbuilder/chartjs', $context);
    }

    protected function convert_type($type) {
        switch($type) {
            case 'column':
                return 'bar';
            case 'bar':
                return 'horizontalBar';
            case 'area':
                return 'line';
            default:
                return $type;
        }
    }

    public static function get_name() : string {
        return get_string('graphchartjs', 'totara_reportbuilder');;
    }

}