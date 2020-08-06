<?php
/**
 * This file is part of Totara Learn
 *
 * Copyright (C) 2020 onwards Totara Learning Solutions LTD
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
 * @author Johannes Cilliers <johannes.cilliers@totaralearning.com>
 * @package performelement_numeric_rating_scale
 */

defined('MOODLE_INTERNAL') || die();

$string['default_number_label'] = 'Default value';
$string['high_value_label'] = 'Maximum numeric value';
$string['low_value_label'] = 'Minimum numeric value';
$string['name'] = 'Numeric Rating scale';
$string['no_response_submitted'] = 'No response submitted';
$string['pluginname'] = 'Rating scale element';
$string['preview'] = 'Preview';
$string['question_label'] = 'Question';
$string['question_placeholder'] = 'Type a question that prompts a numerical rating';
$string['scale_numeric_values'] = 'Numeric values';

// Help messages
$string['numeric_values_help'] = "Enter minimum and maximum numeric values.

Use whole numbers.

The scale steps in intervals of 1, with a minimum of 3 steps. For example, from 1 to 3.";
$string['preview_help'] = "The slider position represents the default value, a whole number in proximity to the scale numeric values median. For example, on a scale of 0 - 21 the default value is 10.";
$string['default_value_help'] = "Default value is a whole number between the numeric values. It is automatically set in proximity to the scale numeric median.
For example, on a scale of 0 - 21 the default value is 10.

Use this setting to customise default value.";
$string['response_required_help'] = "Rating scale questions always require an answer.";

// Errors
$string['error:answer_invalid'] = 'Answer is not valid';
$string['error:answer_required'] = 'Answer is required';