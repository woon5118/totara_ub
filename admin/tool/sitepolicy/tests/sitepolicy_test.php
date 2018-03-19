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

namespace tool_sitepolicy;

defined('MOODLE_INTERNAL') || die();

/**
 * Sitepolicy tests
 */
class tool_sitepolicy_sitepolicy_test extends \advanced_testcase {
    /**
     * Data provider for test_create_multiversion_policy generator.
     */
    public function data_create_multiversion_policy_generator() {
        return [
            [
                'onedraft',
                [
                    'hasdraft' => true,
                    'numpublished' => 0,
                    'allarchived' => false,
                    'authorid' => 2,
                    'languages' => 'en',
                    'title' => 'Test policy onedraft',
                    'statement' => 'Policy statement onedraft',
                    'numoptions' => 1,
                    'consentstatement' => 'Consent statement onedraft',
                    'providetext' => 'yes',
                    'withheldtext' => 'no',
                    'mandatory' => 'first'
                ]
            ],
            [
                'onepublished',
                [
                    'hasdraft' => false,
                    'numpublished' => 1,
                    'allarchived' => false,
                    'authorid' => 2,
                    'languages' => 'en',
                    'title' => 'Test policy onepublished',
                    'statement' => 'Policy statement onepublished',
                    'numoptions' => 1,
                    'consentstatement' => 'Consent statement onepublished',
                    'providetext' => 'yes',
                    'withheldtext' => 'no',
                    'mandatory' => 'first'
                ]
            ],
            [
                'threearchived',
                [
                    'hasdraft' => false,
                    'numpublished' => 3,
                    'allarchived' => true,
                    'authorid' => 2,
                    'languages' => 'en',
                    'title' => 'Test policy threearchived',
                    'statement' => 'Policy statement threearchived',
                    'numoptions' => 1,
                    'consentstatement' => 'Consent statement threearchived',
                    'providetext' => 'yes',
                    'withheldtext' => 'no',
                    'mandatory' => 'first'
                ]
            ],
            [
                'all',
                [
                    'hasdraft' => true,
                    'numpublished' => 3,
                    'allarchived' => false,
                    'authorid' => 2,
                    'languages' => 'en, nl, es',
                    'langprefix' => ',nl,es',
                    'title' => 'Test policy all',
                    'statement' => 'Policy statement all',
                    'numoptions' => 1,
                    'consentstatement' => 'Consent statement all',
                    'providetext' => 'yes',
                    'withheldtext' => 'no',
                    'mandatory' => 'first'
                ]
            ],
            [
                'draftandarchived',
                [
                    'hasdraft' => true,
                    'numpublished' => 3,
                    'allarchived' => true,
                    'authorid' => 2,
                    'languages' => 'en',
                    'title' => 'Test policy draftandarvhiced',
                    'statement' => 'Policy statement draftandarvhiced',
                    'numoptions' => 1,
                    'consentstatement' => 'Consent statement draftandarchived',
                    'providetext' => 'yes',
                    'withheldtext' => 'no',
                    'mandatory' => 'first'
                ]
            ],
        ];
    }

    /**
     * Test get_sitepolicylist
     *
     * @dataProvider data_create_multiversion_policy_generator
     */
    public function test_get_sitepolicylist($debugkey, $options) {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator()->get_plugin_generator('tool_sitepolicy');

        $sitepolicy = $generator->create_multiversion_policy($options);
        $list = sitepolicy::get_sitepolicylist();

        $hasdraft = $options['hasdraft'];
        $numpublished = $options['numpublished'];
        $allarchived = $options['allarchived'];

        $expected = [
            'draft' => (int)$hasdraft,
            'published' => $numpublished,
            'archived' => $numpublished > 0 ? $numpublished - 1 : 0,
            'status' => $hasdraft ? 'draft' : 'published'];
        if ($numpublished > 0 && $allarchived) {
            $expected['archived'] += 1;
            $expected['status'] = $hasdraft ? 'draft' : 'archived';
        }

        $this->assertEquals(1, count($list));
        $row = array_shift($list);
        $this->assertEquals($expected['draft'], $row->numdraft);
        $this->assertEquals($expected['published'], $row->numpublished);
        $this->assertEquals($expected['archived'], $row->numarchived);
        $this->assertEquals($expected['status'], $row->status);
    }

    /**
     * Test switchversion method
     */
    public function test_get_switchversion() {
        global $DB;

        $this->resetAfterTest();
        $generator = $this->getDataGenerator()->get_plugin_generator('tool_sitepolicy');

        $options = [
            'hasdraft' => true,
            'numpublished' => 3,
            'allarchived' => false,
            'authorid' => 2,
            'languages' => 'en, nl, es',
            'langprefix' => ',nl,es',
            'title' => 'Test policy all',
            'statement' => 'Policy statement all',
            'numoptions' => 1,
            'consentstatement' => 'Consent statement all',
            'providetext' => 'yes',
            'withheldtext' => 'no',
            'mandatory' => 'first'
        ];

        $sitepolicy = $generator->create_multiversion_policy($options);

        $rows = $DB->get_records('tool_sitepolicy_policy_version');
        $this->assertEquals(4, count($rows));

        $drafts = array_filter($rows, function($policy) {
            return (is_null($policy->timepublished) && is_null($policy->timearchived));
        });
        $this->assertEquals(1, count($drafts));

        $published = array_filter($rows, function($policy) {
            return (!is_null($policy->timepublished) && is_null($policy->timearchived));
        });
        $this->assertEquals(1, count($published));

        $archived = array_filter($rows, function($policy) {
            return (!is_null($policy->timepublished) && !is_null($policy->timearchived));
        });
        $this->assertEquals(2, count($archived));

        $olddraftid = reset($drafts)->id;
        $oldpublishedid = reset($published)->id;
        $draftversion = new policyversion($olddraftid);

        // Now publish the old draft version
        $sitepolicy->switchversion($draftversion);

        // Verify the the old draft version is now the published version
        // and the old published version is now archived
        $rows = $DB->get_records('tool_sitepolicy_policy_version');
        $this->assertEquals(4, count($rows));

        $drafts = array_filter($rows, function($policy) {
            return (is_null($policy->timepublished) && is_null($policy->timearchived));
        });
        $this->assertEquals(0, count($drafts));

        $published = array_filter($rows, function($policy) {
            return (!is_null($policy->timepublished) && is_null($policy->timearchived));
        });
        $this->assertEquals(1, count($published));
        $this->assertEquals($olddraftid, reset($published)->id);

        $archived = array_filter($rows, function($policy) {
            return (!is_null($policy->timepublished) && !is_null($policy->timearchived));
        });
        $this->assertTrue(array_key_exists($oldpublishedid, $archived));
    }

    /**
     * Test save and delete methods
     */
    public function test_save_and_delete() {
         global $DB;

        $this->resetAfterTest();
        $generator = $this->getDataGenerator()->get_plugin_generator('tool_sitepolicy');

        // Verify no existing site_policies
        $rows = $DB->get_records('tool_sitepolicy_site_policy');
        $this->assertEquals(0, count($rows));

        $sitepolicy = new sitepolicy();
        $sitepolicy->save();

        // Verify new site_policy saved
        $rows = $DB->get_records('tool_sitepolicy_site_policy');
        $this->assertEquals(1, count($rows));
        $id = reset($rows)->id;

        // Now update timecreated and save again
        $sitepolicy->set_timecreated(12345);
        $sitepolicy->save();
        $rows = $DB->get_records('tool_sitepolicy_site_policy');
        $this->assertEquals(1, count($rows));
        $this->assertEquals($id, reset($rows)->id);

        // Now delete the policy
        $sitepolicy->delete();
        $rows = $DB->get_records('tool_sitepolicy_site_policy');
        $this->assertEquals(0, count($rows));
    }

}