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
class tool_sitepolicy_userconsent_test extends \advanced_testcase {

    /**
     * Test save with and without consentoption and/or language
     * @expectedException coding_exception
     */
    public function test_save() {
        global $DB;

        $this->resetAfterTest();

        $userconsent = new userconsent();
        $userconsent->save();
        $this->assertEquals(0, $userconsent->get_id());
        $rows = $DB->get_records('tool_sitepolicy_localised_consent');
        $this->assertFalse($rows);

        $userconsent->set_consentoptionid(1);
        $userconsent->save();
        $this->assertEquals(0, $userconsent->get_id());
        $this->assertFalse($rows);

        $userconsent->set_language('en');
        $userconsent->save();
        $rows = $DB->get_records('tool_sitepolicy_localised_consent');
        $this->assertEquals(1, count($rows));
        $row = array_shift($rows);
        $this->assertEquals($row->id, $userconsent->get_id());
    }

    /**
     * Test get_unansweredpolicies when there is only a draft version
     */
    public function test_get_unansweredpolicies_draft() {
        global $DB;

        $this->resetAfterTest();
        $generator = $this->getDataGenerator()->get_plugin_generator('tool_sitepolicy');

        $options = [
            'hasdraft' => true,
            'numpublished' => 0,
            'allarchived' => false,
            'authorid' => 2,
            'languages' => 'sp,en,nl',
            'langprefix' => ',en,nl',
            'title' => 'Test policy',
            'policystatement' => 'Policy statement',
            'numoptions' => 2,
            'consentstatement' => 'Consent statement',
            'providetext' => 'yes',
            'withheldtext' => 'no',
            'mandatory' => 'first'
            ];

        $sitepolicy = $generator->create_multiversion_policy($options);

        $consentpolicies = userconsent::get_unansweredpolicies(2);
        $this->assertEquals(0, count($consentpolicies));
    }

    /**
     * Test get_unansweredpolicies when there are only archived versions
     */
    public function test_get_unansweredpolicies_archived() {
        global $DB;

        $this->resetAfterTest();
        $generator = $this->getDataGenerator()->get_plugin_generator('tool_sitepolicy');

        $options = [
            'hasdraft' => false,
            'numpublished' => 1,
            'allarchived' => true,
            'authorid' => 2,
            'languages' => 'sp,en,nl',
            'langprefix' => ',en,nl',
            'title' => 'Test policy',
            'policystatement' => 'Policy statement',
            'numoptions' => 2,
            'consentstatement' => 'Consent statement',
            'providetext' => 'yes',
            'withheldtext' => 'no',
            'mandatory' => 'first'
            ];

        $sitepolicy = $generator->create_multiversion_policy($options);

        $consentpolicies = userconsent::get_unansweredpolicies(2);
        $this->assertEquals(0, count($consentpolicies));
    }

    /**
     * Test get_unansweredpolicies when there is only a published version
     */
    public function test_get_unansweredpolicies_published() {
        global $DB;

        $this->resetAfterTest();
        $generator = $this->getDataGenerator()->get_plugin_generator('tool_sitepolicy');

        $options = [
            'hasdraft' => false,
            'numpublished' => 1,
            'allarchived' => false,
            'authorid' => 2,
            'languages' => 'sp,en,nl',
            'langprefix' => ',en,nl',
            'title' => 'Test policy',
            'policystatement' => 'Policy statement',
            'numoptions' => 2,
            'consentstatement' => 'Consent statement',
            'providetext' => 'yes',
            'withheldtext' => 'no',
            'mandatory' => 'first'
            ];

        $sitepolicy = $generator->create_multiversion_policy($options);
        $activeversion = policyversion::from_policy_latest($sitepolicy, policyversion::STATUS_PUBLISHED);

        $existingoptions = $DB->get_records('tool_sitepolicy_consent_options', ['policyversionid' => $activeversion->get_id()]);
        $mandatoryoptions = array_filter($existingoptions, function($val) {
            return $val->mandatory;
        });
        $mandatoryoption = array_shift($mandatoryoptions);
        $optionaloptions = array_filter($existingoptions, function($val) {
            return !$val->mandatory;
        });
        $optionaloption = array_shift($optionaloptions);

        $this->assertEquals(2, count($existingoptions));

        // No consent given yet
        $consentpolicies = userconsent::get_unansweredpolicies(2);
        $this->assertEquals(1, count($consentpolicies));

        // User doesn't agree with mandatory option
        $userconsent = new userconsent();
        $userconsent->set_userid(2);
        $userconsent->set_consentoptionid($mandatoryoption->id);
        $userconsent->set_language('nl');
        $userconsent->set_hasconsented(0);
        $userconsent->set_timeconsented(time()-10);
        $userconsent->save();

        $consentpolicies = userconsent::get_unansweredpolicies(2);
        $this->assertEquals(1, count($consentpolicies));

        // User again doesn't agree with mandatory option
        $userconsent = new userconsent();
        $userconsent->set_userid(2);
        $userconsent->set_consentoptionid($mandatoryoption->id);
        $userconsent->set_language('nl');
        $userconsent->set_hasconsented(0);
        $userconsent->set_timeconsented(time()-9);
        $userconsent->save();

        $consentpolicies = userconsent::get_unansweredpolicies(2);
        $this->assertEquals(1, count($consentpolicies));

        // User doesn't agree with optional option
        $userconsent->set_consentoptionid($optionaloption->id);
        $userconsent->set_language('nl');
        $userconsent->set_hasconsented(0);
        $userconsent->set_timeconsented(time()-8);
        $userconsent->save();

        $consentpolicies = userconsent::get_unansweredpolicies(2);
        $this->assertEquals(1, count($consentpolicies));

        // User consents to mandatory option
        $userconsent->set_consentoptionid($mandatoryoption->id);
        $userconsent->set_hasconsented(1);
        $userconsent->set_timeconsented(time()-7);
        $userconsent->save();

        $consentpolicies = userconsent::get_unansweredpolicies(2);
        $this->assertEquals(0, count($consentpolicies));

        // And then revokes his consent
        $userconsent->set_consentoptionid($mandatoryoption->id);
        $userconsent->set_hasconsented(0);
        $userconsent->set_timeconsented(time()-6);
        $userconsent->save();

        $consentpolicies = userconsent::get_unansweredpolicies(2);
        $this->assertEquals(1, count($consentpolicies));

        // Also check that full history is stored
        $rows = $DB->get_records('tool_sitepolicy_user_consent');
        $this->assertEquals(5, count($rows));
    }

    /**
     * Test has_user_consented
     */
    public function test_has_user_consented() {
        global $DB;

        $this->resetAfterTest();
        $generator = $this->getDataGenerator()->get_plugin_generator('tool_sitepolicy');

        $options = [
            'hasdraft' => false,
            'numpublished' => 1,
            'allarchived' => false,
            'authorid' => 2,
            'languages' => 'sp,en,nl',
            'langprefix' => ',en,nl',
            'title' => 'Test policy',
            'policystatement' => 'Policy statement',
            'numoptions' => 2,
            'consentstatement' => 'Consent statement',
            'providetext' => 'yes',
            'withheldtext' => 'no',
            'mandatory' => 'first'
            ];

        $sitepolicy = $generator->create_multiversion_policy($options);
        $version = policyversion::from_policy_latest($sitepolicy);

        $existingoptions = $DB->get_records('tool_sitepolicy_consent_options', ['policyversionid' => $version->get_id()]);
        $oneoption = reset($existingoptions);
        $this->assertEquals(2, count($existingoptions));

        // No consent given yet
        foreach ($existingoptions as $option) {
            $this->assertFalse(userconsent::has_user_consented($oneoption->id, 2));
        }

        // User doesn't agree with this option
        $userconsent = new userconsent();
        $userconsent->set_userid(2);
        $userconsent->set_consentoptionid($oneoption->id);
        $userconsent->set_language('nl');
        $userconsent->set_hasconsented(0);
        $userconsent->set_timeconsented(time()-2);
        $userconsent->save();
        $this->assertFalse(userconsent::has_user_consented($oneoption->id, 2));

        // Then user agrees with this option in a different language
        $userconsent->set_language('sp');
        $userconsent->set_hasconsented(1);
        $userconsent->set_timeconsented(time()-1);
        $userconsent->save();
        $this->assertTrue(userconsent::has_user_consented($oneoption->id, 2));

        // And now he revokes his consent again
        $userconsent->set_language('en');
        $userconsent->set_hasconsented(0);
        $userconsent->set_timeconsented(time());
        $userconsent->save();
        $this->assertFalse(userconsent::has_user_consented($oneoption->id, 2));
    }

    /**
     * Test get_userconsenttable
     */
    public function test_get_userconsenttable() {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator()->get_plugin_generator('tool_sitepolicy');

        $options = [
            'hasdraft' => true,
            'numpublished' => 3,
            'allarchived' => false,
            'authorid' => 2,
            'languages' => 'sp,en,nl',
            'langprefix' => ',en,nl',
            'title' => 'Test policy',
            'policystatement' => 'Policy statement',
            'numoptions' => 2,
            'consentstatement' => 'Consent statement',
            'providetext' => 'yes',
            'withheldtext' => 'no',
            'mandatory' => 'first',
            'hasconsented' => true,
            'consentuser' => 3
            ];

        $sitepolicy = $generator->create_multiversion_policy($options);

        $consents = userconsent::get_userconsenttable(5);
        $this->assertEquals(0, count($consents));

        $consents = userconsent::get_userconsenttable(3);
        $this->assertEquals(2, count($consents));
    }
}