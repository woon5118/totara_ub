<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2015 onwards Totara Learning Solutions LTD
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
 * @author Petr Skoda <petr.skoda@totaralms.com>
 * @package totara_flavour
 */

use \totara_flavour\helper;

defined('MOODLE_INTERNAL') || die();

/**
 * Tests flavour helper class
 */
class totara_flavour_helper_testcase extends advanced_testcase {
    protected function setUp() {
        global $CFG;
        require_once($CFG->libdir . '/adminlib.php');
        parent::setUp();
        $this->resetAfterTest();
    }

    protected function tearDown() {
        global $CFG;
        // Make sure the $ADMIN static is not messed up by our flavours.
        unset($CFG->forceflavour);
        unset($CFG->showflavours);
        unset($CFG->currentflavour);
        admin_get_root(true, false);
        parent::tearDown();
    }

    public function test_expected_defaults() {
        global $CFG;

        // Verify default settings.
        $this->assertObjectNotHasAttribute('forceflavour', $CFG);
        $this->assertObjectNotHasAttribute('showflavours', $CFG);
        $this->assertObjectNotHasAttribute('currentflavour', $CFG);
        $this->assertEquals(TOTARA_SHOWFEATURE, get_config('moodle', 'enableappraisals'));

        // We need some flavours for testing.
        $this->assertFileExists("$CFG->dirroot/totara/flavour/flavours/enterprise/classes/definition.php");
        $this->assertFileExists("$CFG->dirroot/totara/flavour/flavours/professional/classes/definition.php");
    }

    public function test_get_active_flavour_definition_forced() {
        global $CFG;

        // Nothing active.
        $result = helper::get_active_flavour_definition();
        $this->assertNull($result);

        // Forced flavour.
        set_config('currentflavour', 'flavour_professional', 'totara_flavour');
        $CFG->forceflavour = '';
        $result = helper::get_active_flavour_definition();
        $this->assertNull($result);

        $CFG->forceflavour = 'professional';
        $result = helper::get_active_flavour_definition();
        $this->assertInstanceOf('flavour_professional\\definition', $result);

        $CFG->forceflavour = 'enterprise';
        $result = helper::get_active_flavour_definition();
        $this->assertInstanceOf('flavour_enterprise\\definition', $result);

        // Nothing during initial install unless forced.
        set_config('currentflavour', 'flavour_professional', 'totara_flavour');

        $this->assertFalse(during_initial_install());
        unset($CFG->rolesactive);
        $this->assertTrue(during_initial_install());

        unset($CFG->forceflavour);
        $result = helper::get_active_flavour_definition();
        $this->assertNull($result);

        $CFG->forceflavour = 'enterprise';
        $result = helper::get_active_flavour_definition();
        $this->assertInstanceOf('flavour_enterprise\\definition', $result);

        // Invalid forced.
        $CFG->forceflavour = 'enterprisexxx';
        $result = helper::get_active_flavour_definition();
        $this->assertNull($result);
        $this->assertDebuggingCalled('Invalid flavour specified in $CFG->forceflavour');
    }

    public function test_get_active_flavour_definition_manual() {
        // Nothing active.
        $result = helper::get_active_flavour_definition();
        $this->assertNull($result);

        // Manually activated flavour.
        set_config('currentflavour', 'flavour_professional', 'totara_flavour');
        $result = helper::get_active_flavour_definition();
        $this->assertInstanceOf('flavour_professional\\definition', $result);

        set_config('currentflavour', 'flavour_enterprise', 'totara_flavour');
        $result = helper::get_active_flavour_definition();
        $this->assertInstanceOf('flavour_enterprise\\definition', $result);

        unset_config('currentflavour', 'totara_flavour');
        $result = helper::get_active_flavour_definition();
        $this->assertNull($result);

        // Invalid manual flavour.
        set_config('currentflavour', 'flavour_xxxxx', 'totara_flavour');
        $result = helper::get_active_flavour_definition();
        $this->assertNull($result);
    }

    public function test_get_available_flavour_definitions() {
        $result = helper::get_available_flavour_definitions();
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('flavour_professional', $result);
        $this->assertArrayHasKey('flavour_enterprise', $result);
        foreach ($result as $component => $flavour) {
            $this->assertStringStartsWith('flavour_', $component);
            $this->assertInstanceOf('totara_flavour\\definition', $flavour);
        }
    }

    public function test_get_active_flavour_notice() {
        global $CFG, $PAGE;

        /** @var core_admin_renderer $output */
        $output = $PAGE->get_renderer('core', 'admin');

        // No flavour.
        $result = helper::get_active_flavour_notice($output);
        $this->assertNull($result);

        // Enterprise means everything enabled === no notice.
        $CFG->forceflavour = 'enterprise';
        $result = helper::get_active_flavour_notice($output);
        $this->assertNull($result);

        // Upgrade notice to full.
        $CFG->forceflavour = 'professional';
        $result = helper::get_active_flavour_notice($output);
        $this->assertNotNull($result);
    }

    public function test_get_active_flavour_component() {
        global $CFG;

        // No flavour.
        $result = helper::get_active_flavour_component();
        $this->assertNull($result);

        $CFG->forceflavour = 'enterprise';
        $result = helper::get_active_flavour_component();
        $this->assertSame('flavour_enterprise', $result);
    }

    public function test_get_default_settings() {
        global $CFG;

        // No flavour.
        $result = helper::get_defaults_setting();
        $this->assertSame(array(), $result);

        $CFG->forceflavour = 'enterprise';
        $result = helper::get_defaults_setting();
        $this->assertSame(array(), $result);

        $CFG->forceflavour = 'professional';
        $result = helper::get_defaults_setting();
        $this->assertSame(array(), $result);
    }

    public function test_get_enforced_settings() {
        global $CFG;

        // No flavour.
        $result = helper::get_enforced_settings();
        $this->assertSame(array(), $result);

        $CFG->forceflavour = 'enterprise';
        $result = helper::get_enforced_settings();
        $this->assertSame(array(), $result);

        $CFG->forceflavour = 'professional';
        $result = helper::get_enforced_settings();
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('moodle', $result);
        $this->assertArrayNotHasKey('', $result);
        // Test a few settings to verify the format.
        $this->assertSame(TOTARA_DISABLEFEATURE, $result['moodle']['enableappraisals']);
        $this->assertSame('', $result['theme_customtotararesponsive']['customcss']);
    }

    public function test_get_prohibited_settings() {
        global $CFG;

        // No flavour.
        $result = helper::get_prohibited_settings();
        $this->assertSame(array(), $result);

        $CFG->forceflavour = 'enterprise';
        $result = helper::get_prohibited_settings();
        $this->assertSame(array(), $result);

        $CFG->forceflavour = 'professional';
        $result = helper::get_prohibited_settings();
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('moodle', $result);
        $this->assertArrayNotHasKey('', $result);
        // Test a few settings to verify the format.
        $this->assertSame(true, $result['moodle']['enableappraisals']);
        $this->assertSame(true, $result['theme_customtotararesponsive']['customcss']);
    }

    public function test_execute_post_install_steps() {
        global $CFG;
        $this->setAdminUser();

        $CFG->forceflavour = 'professional';
        helper::execute_post_install_steps();

        $this->assertSame('flavour_professional', get_config('totara_flavour', 'currentflavour'));
        $this->assertEquals(TOTARA_DISABLEFEATURE, get_config('moodle', 'enableappraisals'));
    }

    public function test_execute_post_upgrade_steps() {
        global $CFG;
        $this->setAdminUser();

        $CFG->forceflavour = 'professional';
        helper::execute_post_upgrade_steps();

        $this->assertSame('flavour_professional', get_config('totara_flavour', 'currentflavour'));
        $this->assertEquals(TOTARA_DISABLEFEATURE, get_config('moodle', 'enableappraisals'));
    }

    public function test_execute_post_upgradesettings_steps() {
        global $CFG;
        $this->setAdminUser();

        $CFG->forceflavour = 'professional';
        helper::execute_post_upgradesettings_steps();

        $this->assertSame('flavour_professional', get_config('totara_flavour', 'currentflavour'));
        $this->assertEquals(TOTARA_DISABLEFEATURE, get_config('moodle', 'enableappraisals'));
    }

    public function test_set_active_flavour() {
        $this->resetAfterTest();
        $this->setAdminUser();

        helper::set_active_flavour('flavour_professional');

        $this->assertSame('flavour_professional', get_config('totara_flavour', 'currentflavour'));
        $this->assertEquals(TOTARA_DISABLEFEATURE, get_config('moodle', 'enableappraisals'));

        helper::set_active_flavour('flavour_enterprise');

        $this->assertSame('flavour_enterprise', get_config('totara_flavour', 'currentflavour'));
        $this->assertEquals(TOTARA_DISABLEFEATURE, get_config('moodle', 'enableappraisals'));

        // Invalid value.
        try {
            helper::set_active_flavour('professional');
            $this->fail('coding_exception expected for invalid flavour name');
        } catch (moodle_exception $ex) {
            $this->assertInstanceOf('coding_exception', $ex);
        }
    }
}
