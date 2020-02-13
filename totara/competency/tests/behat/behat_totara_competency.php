<?php
/*
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
 * @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
 * @package totara_competency
 */

use Behat\Gherkin\Node\TableNode as TableNode;
use totara_competency\achievement_configuration;
use totara_competency\entities\competency;

class behat_totara_competency extends behat_base {

    /**
     * @var totara_competency_generator
     */
    protected $generator;

    /**
     * Create a scale with a name and scale values.
     *
     * @Given /^a competency scale called "(?P<scalename_string>(?:[^"]|\\")*)" exists with the following values:$/
     * @param string $scalename
     * @param TableNode $table
     * @throws Exception
     */
    public function competency_scale_called_exists($scalename, TableNode $table) {
        \behat_hooks::set_step_readonly(true); // Backend action.

        $scale_values = $this->parse_table(
            $table,
            ['name', 'proficient', 'default', 'sortorder'],
            ['idnumber', 'description']
        );

        $this->get_data_generator()->create_scale($scalename, null, $scale_values);
    }

    /**
     * Create the default achievement paths for a competency.
     *
     * @Given /^the default achievement paths exist for the "(?P<competency_string>(?:[^"]|\\")*)" competency$/
     * @param string $competency
     */
    public function the_default_achievement_paths_exist_for_the_competency($competency) {
        global $DB;
        \behat_hooks::set_step_readonly(true); // Backend action.

        $competency_id = $DB->get_field(competency::TABLE, 'id', ['idnumber' => $competency]);
        $config = new achievement_configuration(new competency($competency_id));
        $config->link_default_preset();
    }

    /**
     * Turn the table into an array of key=>value records.
     *
     * @param TableNode $table
     * @param array $required_columns
     * @param array $optional_columns
     * @return array
     * @throws Exception
     */
    private function parse_table(TableNode $table, array $required_columns, array $optional_columns = []): array {
        $table = $table->getHash();
        $first_row = reset($table);

        // Check required fields are present.
        foreach ($required_columns as $column) {
            if (!isset($first_row[$column])) {
                throw new Exception("The {$column} field must be defined!");
            }
        }

        // Copy values, ready to pass on to the generator.
        $records = [];

        foreach ($table as $row) {
            $record = [];
            foreach ($row as $field_name => $value) {
                if (in_array($field_name, $required_columns)) {
                    $record[$field_name] = $value;
                } else if (in_array($field_name, $optional_columns)) {
                    $record[$field_name] = $value;
                } else {
                    throw new Exception("Unknown field {$field_name} in the table definition!");
                }
            }
            $records[] = $record;
        }

        return $records;
    }

    /**
     * @return totara_competency_generator
     */
    private function get_data_generator() {
        global $CFG;
        require_once($CFG->dirroot . '/totara/competency/tests/generator/totara_competency_generator.class.php');

        if (is_null($this->generator)) {
            $this->generator = new totara_competency_generator(new testing_data_generator());
        }

        return $this->generator;
    }

}
