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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Simon Coggins <simon.coggins@totaralearning.com>
 * @package flavour_perform
 */

namespace flavour_perform;

use totara_core\advanced_feature;

defined('MOODLE_INTERNAL') || die();

/**
 * Perform flavour definition
 */
class definition extends \totara_flavour\definition {

    /**
     * @inheritDoc
     */
    public function get_component() {
        return 'flavour_perform';
    }

    /**
     * @inheritDoc
     */
    protected function load_default_settings() {
        return [
            '' => [
                // Enable non-legacy Perform features
                'enableperformance_activities' => advanced_feature::ENABLED,
                'enablecompetency_assignment' => advanced_feature::ENABLED,
                'enablegoals' => advanced_feature::ENABLED,
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    protected function load_enforced_settings() {
        return [
            '' => [
                // Disable Engage only features
                'enableengage_resources' => advanced_feature::DISABLED,
                'enablecontainer_workspace' => advanced_feature::DISABLED,
                'enabletotara_msteams' => advanced_feature::DISABLED,
                'enableml_recommender' => advanced_feature::DISABLED,
                // Disable Learn only features
                // Non-legacy features
                'enableoutcomes' => advanced_feature::DISABLED,
                'enableportfolios' => advanced_feature::DISABLED,
                'enablecompletion' => advanced_feature::DISABLED,
                'completiondefault' => advanced_feature::DISABLED,
                'enableavailability' => advanced_feature::DISABLED,
                'enablecourserpl' => advanced_feature::DISABLED,
                'enablemodulerpl' => advanced_feature::DISABLED,
                'enableplagiarism' => advanced_feature::DISABLED,
                'enablecontentmarketplaces' => advanced_feature::DISABLED,
                'enableprogramextensionrequests' => advanced_feature::DISABLED,
                'enablelearningplans' => advanced_feature::DISABLED,
                'enableprograms' => advanced_feature::DISABLED,
                'enablecertifications' => advanced_feature::DISABLED,
                'enablerecordoflearning' => advanced_feature::DISABLED,
                'enableprogramcompletioneditor' => advanced_feature::DISABLED,
                // Legacy features
                'enablelegacyprogramassignments' => advanced_feature::DISABLED,
            ]
        ];
    }
}
