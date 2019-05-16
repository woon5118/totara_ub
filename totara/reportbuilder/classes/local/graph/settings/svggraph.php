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

namespace totara_reportbuilder\local\graph\settings;

final class svggraph extends base {

    public static $translation = [
        'padding' => [
            '_default' => ['pad_top', 'pad_left', 'pad_bottom', 'pad_right'],
            'top' => 'pad_top',
            'left' => 'pad_left',
            'bottom' => 'pad_bottom',
            'right' => 'pad_right'
        ],
        'title' => [
            '_default' => 'graph_title',
            'text' => 'graph_title',
            'position' => 'graph_title_position',
            'font' => 'graph_title_font',
            'fontSize' => 'graph_title_font_size',
            'fontStyle' => 'graph_title_font_weight',
            'color' => 'graph_title_colour',
            'padding' => 'graph_title_space',
        ],
        'legend' => [
            'display' => 'show_legend',
            'position' => 'legend_position',
            'font' => 'legend_font',
            'fontSize' => 'legend_font_size',
            'fontStyle' => 'legend_font_weight',
            'color' => 'legend_colour',
            'padding' => 'legend_padding',
        ],
        'tooltips' => [
            'display' => 'show_tooltips',
            'backgroundColor' => 'tooltip_back_colour',
            'font' => 'tooltip_font',
            'fontSize' => 'tooltip_font_size',
            'fontStyle' => 'tooltip_font_weight',
            'color' => 'tooltip_colour',
            'borderRadius' => 'tooltip_round',
            'borderColor' => 'tooltip_colour',
            'borderWidth' => 'tooltip_stroke_width',
        ],
        'axis' => [
            'x' => [
                'display' => 'show_axis_v',
                'title' => [
                    '_default' => 'label_v',
                    'text' => 'label_v',
                    'font' => 'label_font_v',
                    'fontSize' => 'label_font_size_v',
                    'fontStyle' => 'label_font_weight_v',
                    'color' => 'label_colour_v',
                    'padding' => 'label_space',
                ],
                'grid' => [
                    'display' => 'show_grid_v',
                    'color' => 'grid_colour_v'
                ]
            ],
            'y' => [
                'display' => 'show_axis_h',
                'title' => [
                    '_default' => 'label_h',
                    'text' => 'label_h',
                    'font' => 'label_font_h',
                    'fontSize' => 'label_font_size_h',
                    'fontStyle' => 'label_font_weight_h',
                    'color' => 'label_colour_h',
                    'padding' => 'label_space',
                ],
                'grid' => [
                    'display' => 'show_grid_h',
                    'color' => 'grid_colour_h'
                ]
            ]
        ]
    ];

    /**
     * The list of derived settings for this chart -- if some other settings exist, add another setting
     * @var array
     */
    protected static $derived = [];

    public static function create(array $settings): array {
        $setting_list = [];

        foreach (self::$translation as $key => $value) {
            $setting_list = array_merge($setting_list, self::match($key, $settings, self::$translation));
        }

        // Set the derived settings
        foreach (self::$derived as $key => $value) {
            if (isset($setting_list[$key])) {
                $setting_list = array_merge($setting_list, $value);
            }
        }

        // If there are custom settings, override the options with anything inside it
        if (isset($settings['custom'])) {
            $setting_list = array_replace_recursive($setting_list, $settings['custom']);
        }

        return $setting_list;
    }
}
