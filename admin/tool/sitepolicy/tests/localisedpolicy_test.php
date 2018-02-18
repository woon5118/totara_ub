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
* Sitepolicy localised policy tests.
*/
class tool_sitepolicy_localisedpolicy_test extends \advanced_testcase {

    /**
    * Test from_data
    */
    public function test_from_data() {
        global $DB;

        $this->resetAfterTest();

        $sitepolicy = new sitepolicy();
        $sitepolicy->save();
        $version = policyversion::new_policy_draft($sitepolicy);
        $version->save();

        $localisedpolicy = localisedpolicy::from_data($version, 'en');
        $this->assertEquals(1, $localisedpolicy->is_primary());

        // Constructing a new instance doesn't persist it to the db
        $rows = $DB->get_records('tool_sitepolicy_localised_policy');
        $this->assertEquals(0, count($rows));

        // Save this version and create a new one
        $localisedpolicy->save();

        // Duplicate languages are only tested on save
        $localisedpolicy = localisedpolicy::from_data($version, 'en');
        $this->assertEquals(0, $localisedpolicy->is_primary());
    }

    /**
     * Test save with exceptions
     */
    public function test_save_execeptions() {
        global $DB;

        $this->resetAfterTest();
        $this->expectException('coding_exception');

        $sitepolicy = new sitepolicy();
        $sitepolicy->save();
        $version = policyversion::new_policy_draft($sitepolicy);
        $version->save();

        $localisedpolicy = localisedpolicy::from_data($version, 'en');
        $localisedpolicy->save();

        $localisedpolicy = localisedpolicy::from_data($version, 'en');
        $localisedpolicy->save();

        $localisedpolicy = localisedpolicy::from_data($version, 'nl', localisedpolicy::STATUS_PRIMARY);
        $localisedpolicy->save();
    }

    /**
     * Test save
     */
    public function test_save() {
        global $DB;

        $this->resetAfterTest();

        $sitepolicy = new sitepolicy();
        $sitepolicy->save();
        $version = policyversion::new_policy_draft($sitepolicy);
        $version->save();

        $localisedpolicy = localisedpolicy::from_data($version, 'en');
        $localisedpolicy->save();

        $rows = $DB->get_records('tool_sitepolicy_localised_policy');
        $this->assertEquals(1, count($rows));
        $row = reset($rows);
        $this->assertEquals('en', $row->language);
        $this->assertEquals('', $row->title);
        $this->assertEquals('', $row->policytext);
        $this->assertEquals('', $row->whatsnew);
        $this->assertFalse(empty($row->timecreated));
        $this->assertEquals(localisedpolicy::STATUS_PRIMARY, $row->isprimary);
        $this->assertEquals($version->get_id(), $row->policyversionid);

        $time = time();
        $localisedpolicy->set_title('The title');
        $localisedpolicy->set_policytext('The policy text');
        $localisedpolicy->set_whatsnew('The whatsnew text');
        $localisedpolicy->set_timecreated($time);
        $localisedpolicy->set_isprimary(0);
        $localisedpolicy->set_authorid(2);
        $localisedpolicy->save();

        $rows = $DB->get_records('tool_sitepolicy_localised_policy');
        $this->assertEquals(1, count($rows));
        $row = reset($rows);
        $this->assertEquals('en', $row->language);
        $this->assertEquals('The title', $row->title);
        $this->assertEquals('The policy text', $row->policytext);
        $this->assertEquals('The whatsnew text', $row->whatsnew);
        $this->assertEquals($time, $row->timecreated);
        $this->assertEquals(localisedpolicy::STATUS_NOTPRIMARY, $row->isprimary);
        $this->assertEquals($version->get_id(), $row->policyversionid);
    }

    /**
     * Test set_statements
     */
    public function test_set_statements () {
        global $DB;

        $this->resetAfterTest();

        $sitepolicy = new sitepolicy();
        $sitepolicy->save();
        $version = policyversion::new_policy_draft($sitepolicy);
        $version->save();

        $localisedpolicy = localisedpolicy::from_data($version, 'en');
        $time = time();

        // 3 new statements
        $statements = [];
        for ($i = 0; $i < 3; $i++) {
            $stmt = new statement();
            $stmt->__set('dataid', 0);
            $stmt->__set('instance', $i + 1);
            $stmt->__set('statement', "Consent statement $i");
            $stmt->__set('provided', "Yes");
            $stmt->__set('withheld', "No");
            $stmt->__set('mandatory', $i == 0 ? 1 : 0);
            $stmt->__set('removedstatement', false);
            $stmt->__set('index', $i + 1);
            $statements[$i] = $stmt;
        }

        $localisedpolicy->set_statements($statements);

        $options = $localisedpolicy->get_consentoptions();
        $this->assertSame(count($statements), count($options));
        foreach($options as $i => $localisedconsent) {
            $consentoption = $localisedconsent->get_option();
            $this->assertEquals(0, $consentoption->get_id());
            $this->assertEquals(($i == 0), $consentoption->get_mandatory());
            $this->assertEquals($statements[$i]->statement, $localisedconsent->get_statement());
            $this->assertEquals($statements[$i]->provided, $localisedconsent->get_consentoption());
            $this->assertEquals($statements[$i]->withheld, $localisedconsent->get_nonconsentoption());
            $this->assertFalse($localisedconsent->is_removed());
        }

        // 1 new removed statement
        $statements = [];
        $stmt = new statement();
        $stmt->__set('dataid', 0);
        $stmt->__set('instance', 1);
        $stmt->__set('statement', "Consent statement");
        $stmt->__set('provided', "Yes");
        $stmt->__set('withheld', "No");
        $stmt->__set('mandatory', 0);
        $stmt->__set('removedstatement', true);
        $stmt->__set('index', 1);
        $statements[$i] = $stmt;

        $localisedpolicy->set_statements($statements);
        $options = $localisedpolicy->get_consentoptions();
        $this->assertSame(0, count($options));

        // 1 existing removed statement
        $entry = new \stdClass();
        $entry->mandatory = 1;
        $entry->idnumber = 1;
        $entry->policyversionid = $version->get_id();
        $consentoptionid = $DB->insert_record('tool_sitepolicy_consent_options', $entry);

        $statements = [];
        $stmt = new statement();
        $stmt->__set('dataid', $consentoptionid);
        $stmt->__set('instance', 1);
        $stmt->__set('statement', "Consent statement");
        $stmt->__set('provided', "Yes");
        $stmt->__set('withheld', "No");
        $stmt->__set('mandatory', 0);
        $stmt->__set('removedstatement', true);
        $stmt->__set('index', 1);
        $statements[$i] = $stmt;

        $localisedpolicy->set_statements($statements);
        $options = $localisedpolicy->get_consentoptions();
        $this->assertSame(1, count($options));

        $localisedconsent = $options[0];
        $consentoption = $localisedconsent->get_option();
        $this->assertEquals($consentoptionid, $consentoption->get_id());
        $this->assertEquals(0, $consentoption->get_mandatory());
        $this->assertEquals($stmt->statement, $localisedconsent->get_statement());
        $this->assertEquals($stmt->provided, $localisedconsent->get_consentoption());
        $this->assertEquals($stmt->withheld, $localisedconsent->get_nonconsentoption());
        $this->assertTrue($localisedconsent->is_removed());
    }


    /**
     * Test saving of consent options
     */
    public function test_save_consentoptions() {
        global $DB;

        $this->resetAfterTest();

        $sitepolicy = new sitepolicy();
        $sitepolicy->save();
        $version = policyversion::new_policy_draft($sitepolicy);
        $version->save();

        $localisedpolicy = localisedpolicy::from_data($version, 'en');
        $time = time();
        $localisedpolicy->set_title('The title');
        $localisedpolicy->set_policytext('The policy text');
        $localisedpolicy->set_whatsnew('The whatsnew text');
        $localisedpolicy->set_timecreated($time);
        $localisedpolicy->set_isprimary(1);
        $localisedpolicy->set_authorid(2);

        // Add 3 new options to be saved
        $statements = [];
        for ($i = 0; $i < 3; $i++) {
            $stmt = new statement();
            $stmt->__set('dataid', 0);
            $stmt->__set('instance', $i + 1);
            $stmt->__set('statement', "Consent statement $i");
            $stmt->__set('provided', "Yes");
            $stmt->__set('withheld', "No");
            $stmt->__set('mandatory', $i == 0 ? 1 : 0);
            $stmt->__set('removedstatement', false);
            $stmt->__set('index', $i + 1);
            $statements[$i] = $stmt;
        }
        $localisedpolicy->set_statements($statements);
        $localisedpolicy->save();

        $sql = "
            SELECT tsco.id,
                   tsco.mandatory,
                   tslc.statement,
                   tslc.consentoption,
                   tslc.nonconsentoption
              FROM {tool_sitepolicy_consent_options} tsco
              JOIN {tool_sitepolicy_localised_consent} tslc
                ON tsco.id = tslc.consentoptionid
             WHERE tsco.policyversionid = :policyversionid
               AND tslc.localisedpolicyid = :localisedpolicyid
            ";
        $params = ['policyversionid' => $version->get_id(),
                   'localisedpolicyid' => $localisedpolicy->get_id()];
        $optionrows = $DB->get_records_sql($sql, $params);
        $this->assertSame(3, count($optionrows));

        foreach($optionrows as $row) {
            $idx = substr($row->statement, strrpos($row->statement, ' ') + 1);
            $this->assertEquals((int)($idx == 0), $row->mandatory);
            // Set the dataids for later tests
            $statements[$idx]->dataid = $row->id;
        }

        // Remove an existing statement
        $statements[1]->removedstatement = true;
        $localisedpolicy->set_statements($statements);
        $localisedpolicy->save();

        $sql = "
            SELECT tsco.id,
                   tsco.mandatory,
                   tslc.statement,
                   tslc.consentoption,
                   tslc.nonconsentoption
              FROM {tool_sitepolicy_consent_options} tsco
              JOIN {tool_sitepolicy_localised_consent} tslc
                ON tsco.id = tslc.consentoptionid
             WHERE tsco.policyversionid = :policyversionid
               AND tslc.localisedpolicyid = :localisedpolicyid
            ";
        $params = ['policyversionid' => $version->get_id(),
                   'localisedpolicyid' => $localisedpolicy->get_id()];
        $optionrows = $DB->get_records_sql($sql, $params);
        $this->assertSame(2, count($optionrows));
        foreach($optionrows as $row) {
            $idx = substr($row->statement, strrpos($row->statement, ' ') + 1);
            $this->assertFalse($statements[$idx]->removedstatement);
        }
    }

    /**
     * Test get_statements
     */
    public function test_get_statements() {
        global $DB;

        $this->resetAfterTest();
        $generator = $this->getDataGenerator()->get_plugin_generator('tool_sitepolicy');

        $options = [
            'authorid' => 2,
            'languages' => 'en,nl',
            'langprefix' => ',nl',
            'title' => 'Test policy get statements',
            'policystatement' => 'Policy statement get statements',
            'numoptions' => 3,
            'consentstatement' => 'Consent statement get statements',
            'providetext' => 'Yes',
            'withheldtext' => 'No',
            'mandatory' => 'first'
        ];

        $sitepolicy = $generator->create_draft_policy($options);
        $version = policyversion::from_policy_latest($sitepolicy);
        $localisedpolicy = localisedpolicy::from_version($version, ['language' => 'nl']);

        $statements = $localisedpolicy->get_statements(false);
        $this->assertEquals(3, count($statements));
        $idx = 0;
        foreach ($statements as $stmt) {
            $idx += 1;
            $this->assertEquals('', $stmt->primarystatement);
            $this->assertEquals('', $stmt->primaryprovided);
            $this->assertEquals('', $stmt->primarywithheld);
            $this->assertEquals("nl Consent statement get statements $idx", $stmt->statement);
            $this->assertEquals('nl Yes', $stmt->provided);
            $this->assertEquals('nl No', $stmt->withheld);
            $this->assertEquals($idx == 1, $stmt->mandatory);
        }
    }

    /**
     * Test clone content
     */
    public function test_clone_content() {
        global $DB;

        $this->resetAfterTest();
        $generator = $this->getDataGenerator()->get_plugin_generator('tool_sitepolicy');

        $options = [
            'authorid' => 2,
            'languages' => 'en,nl',
            'langprefix' => ',nl',
            'title' => 'Test policy clone',
            'policystatement' => 'Policy statement clone',
            'numoptions' => 3,
            'consentstatement' => 'Consent statement clone',
            'providetext' => 'Yip',
            'withheldtext' => 'Nope',
            'mandatory' => 'first'
        ];

        $sitepolicy = $generator->create_published_policy($options);
        $version = policyversion::from_policy_latest($sitepolicy);
        $draft = policyversion::new_policy_draft($sitepolicy);
        $draft->save();
        $draft->clone_content($version);

        $sql = "
            SELECT tslc.id,
                   tsco.mandatory,
                   tslc.statement,
                   tslc.consentoption,
                   tslc.nonconsentoption,
                   tslp.language,
                   tslp.isprimary
              FROM {tool_sitepolicy_consent_options} tsco
              JOIN {tool_sitepolicy_localised_policy} tslp
                ON tslp.policyversionid = :policyversionid
              JOIN {tool_sitepolicy_localised_consent} tslc
                ON tsco.id = tslc.consentoptionid
             WHERE tsco.policyversionid = :policyversionid2
               AND tslc.localisedpolicyid = tslp.id
            ";
        $params = ['policyversionid' => $version->get_id(), 'policyversionid2' => $version->get_id()];
        $publishedrows = $DB->get_records_sql($sql, $params);

        $params = ['policyversionid' => $draft->get_id(), 'policyversionid2' => $draft->get_id()];
        $draftrows = $DB->get_records_sql($sql, $params);

        $this->assertEquals(6, count($publishedrows));
        $this->assertEquals(3, count($draftrows));

        $primarypublished = array_filter($publishedrows, function($row) {
            return $row->isprimary;
        });
        $otherpublished = array_filter($publishedrows, function($row) {
            return !$row->isprimary;
        });

        foreach ($primarypublished as $option) {
            $fnd = array_filter($draftrows, function($draftrow) use ($option) {
                return ($draftrow->mandatory == $option->mandatory &&
                        $draftrow->statement == $option->statement &&
                        $draftrow->consentoption == $option->consentoption &&
                        $draftrow->nonconsentoption == $option->nonconsentoption &&
                        $draftrow->language == $option->language);
            });
            $this->assertEquals(1, count($fnd));
        }

        foreach ($otherpublished as $option) {
            $fnd = array_filter($draftrows, function($draftrow) use ($option) {
                return $draftrow->language == $option->language;
            });
            $this->assertEquals(0, count($fnd));
        }
    }

    /**
     * Test get primary and localised titles
     */
    public function test_get_titles() {
        global $DB;

        $this->resetAfterTest();
        $generator = $this->getDataGenerator()->get_plugin_generator('tool_sitepolicy');

        $options = [
            'authorid' => 2,
            'languages' => 'en,nl',
            'langprefix' => ',nl',
            'title' => 'Test policy clone',
            'policystatement' => 'Policy statement clone',
            'numoptions' => 3,
            'consentstatement' => 'Consent statement clone',
            'providetext' => 'Yip',
            'withheldtext' => 'Nope',
            'mandatory' => 'first'
        ];

        $sitepolicy = $generator->create_published_policy($options);
        $version = policyversion::from_policy_latest($sitepolicy);
        $localisedpolicy_en = localisedpolicy::from_version($version, ['language' => 'en']);
        $localisedpolicy_nl = localisedpolicy::from_version($version, ['language' => 'nl']);

        $this->assertEquals('Test policy clone', $localisedpolicy_en->get_primary_title());
        $this->assertEquals('Test policy clone', $localisedpolicy_nl->get_primary_title());
        $this->assertEquals('nl Test policy clone', $localisedpolicy_en->get_translated_title('nl'));
        $this->assertEquals('nl Test policy clone', $localisedpolicy_nl->get_translated_title('nl'));
    }
}