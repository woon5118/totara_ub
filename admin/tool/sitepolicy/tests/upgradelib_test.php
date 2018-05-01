<?php
/**
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Riana Rossouw <riana.rossouw@totaralearning.com>
 * @package tool_sitepolicy
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/admin/tool/sitepolicy/db/upgradelib.php');

/**
 * Test upgading of tool_sitepolicy_localised_policy text fields to html
 */
class tool_sitepolicy_upgradelib_testcase extends advanced_testcase {

    /**
      * Insert some test data
      *
      * @return array Associative array linking db ids to row titles
      */
    private function setup_data() : array {
        global $DB;

        $data = [];

        $data['Policytext Only'] = (object)[
            'language' => 'en',
            'title' => 'Policytext Only',
            'policytext' => "The policy text\nwith a newline",
            'timecreated' => time(),
            'isprimary' => 1,
            'authorid' => 2,
            'policyversionid' => 1,
        ];
        $data['Policytext Only']->id = $DB->insert_record('tool_sitepolicy_localised_policy', $data['Policytext Only']);

        $data['Policytext and whatsnew'] = (object)[
            'language' => 'en',
            'title' => 'Policytext and whatsnew',
            'policytext' => 'The policy text without a newline',
            'whatsnew' => "Whatsnew text \nwith a newline",
            'timecreated' => time(),
            'isprimary' => 1,
            'authorid' => 2,
            'policyversionid' => 1,
        ];
        $data['Policytext and whatsnew']->id = $DB->insert_record('tool_sitepolicy_localised_policy', $data['Policytext and whatsnew']);

        $data['Policytext with p tag'] = (object)[
            'language' => 'en',
            'title' => 'Policytext with p tag',
            'policytext' => '<p>The policy text starting with a p tag</p>',
            'whatsnew' => 'Whatsnew without p tag',
            'timecreated' => time(),
            'isprimary' => 1,
            'authorid' => 2,
            'policyversionid' => 1,
        ];
        $data['Policytext with p tag']->id = $DB->insert_record('tool_sitepolicy_localised_policy', $data['Policytext with p tag']);

        $data['Both with p tag'] = (object)[
            'language' => 'en',
            'title' => 'Both with p tag',
            'policytext' => '<p>The policy text starting with a p tag</p>',
            'whatsnew' => '<p>Whatsnew with p tag</p>',
            'timecreated' => time(),
            'isprimary' => 1,
            'authorid' => 2,
            'policyversionid' => 1,
        ];
        $data['Both with p tag']->id = $DB->insert_record('tool_sitepolicy_localised_policy', $data['Both with p tag']);

        return $data;
    }

    public function test_policy_and_whatsnew_conversion_to_html() {
        global $DB;

        $this->resetAfterTest(true);

        $data = $this->setup_data();
        tool_sitepolicy_upgrade_convert_policytext_to_html();

        // We can put this in a fpr loop, but keeping it separate here to not simply duplicate the function
        // code and also show explicitly when we are expecting conversion to happen and when not
        $row = $DB->get_record('tool_sitepolicy_localised_policy', ['id' => $data['Policytext Only']->id]);
        $expected = '<p>'.text_to_html($data['Policytext Only']->policytext, null, false, true).'</p>';
        $this->assertEquals($expected, $row->policytext);
        $this->assertEmpty($row->whatsnew);

        $row = $DB->get_record('tool_sitepolicy_localised_policy', ['id' => $data['Policytext and whatsnew']->id]);
        $expected = '<p>'.text_to_html($data['Policytext and whatsnew']->policytext, null, false, true).'</p>';
        $this->assertEquals($expected, $row->policytext);
        $expected = '<p>'.text_to_html($data['Policytext and whatsnew']->whatsnew, null, false, true).'</p>';
        $this->assertEquals($expected, $row->whatsnew);

        $row = $DB->get_record('tool_sitepolicy_localised_policy', ['id' => $data['Policytext with p tag']->id]);
        $this->assertEquals($data['Policytext with p tag']->policytext, $row->policytext);
        $expected = '<p>'.text_to_html($data['Policytext with p tag']->whatsnew, null, false, true).'</p>';
        $this->assertEquals($expected, $row->whatsnew);

        $row = $DB->get_record('tool_sitepolicy_localised_policy', ['id' => $data['Both with p tag']->id]);
        $this->assertEquals($data['Both with p tag']->policytext, $row->policytext);
        $this->assertEquals($data['Both with p tag']->whatsnew, $row->whatsnew);
    }
}
