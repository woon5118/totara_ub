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
 * @author  Petr Skoda <petr.skoda@totaralearning.com>
 * @package totara_core
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Test functions in totara/core/db/upgradelib.php
 */
class totara_core_upgradelib_testcase extends advanced_testcase {
    public function test_totara_core_upgrade_fix_role_risks(): void {
        global $CFG, $DB;
        require_once("{$CFG->dirroot}/totara/core/db/upgradelib.php");

        $initialcaps = $DB->get_records_menu('capabilities', [], 'name ASC', 'name, riskbitmask');
        totara_core_upgrade_fix_role_risks();
        $this->assertSame($initialcaps, $DB->get_records_menu('capabilities', [], 'name ASC', 'name, riskbitmask'));

        $DB->set_field('capabilities', 'riskbitmask', RISK_SPAM | RISK_PERSONAL | RISK_XSS | RISK_CONFIG | RISK_DATALOSS, ['name' => 'moodle/site:config']);
        $DB->set_field('capabilities', 'riskbitmask', RISK_CONFIG, ['name' => 'totara/core:appearance']);
        $DB->set_field('capabilities', 'riskbitmask', RISK_PERSONAL | RISK_ALLOWXSS, ['name' => 'moodle/backup:backupcourse']);
        $DB->set_field('capabilities', 'riskbitmask', RISK_CONFIG | RISK_ALLOWXSS | RISK_ALLOWXSS, ['name' => 'totara/core:appearance']);
        totara_core_upgrade_fix_role_risks();
        $this->assertSame($initialcaps, $DB->get_records_menu('capabilities', [], 'name ASC', 'name, riskbitmask'));

        // Make sure missing caps are skipped and extra ignored.
        $oldcap = $DB->get_record('capabilities', ['name' => 'totara/core:appearance']);
        unset($oldcap->id);
        $oldcap->name = 'totara/core:xappearance';
        $DB->insert_record('capabilities', $oldcap);
        $DB->delete_records('capabilities', ['name' => 'totara/core:appearance']);
        $initialcaps = $DB->get_records_menu('capabilities', [], 'name ASC', 'name, riskbitmask');
        totara_core_upgrade_fix_role_risks();
        $this->assertSame($initialcaps, $DB->get_records_menu('capabilities', [], 'name ASC', 'name, riskbitmask'));
    }
}
