<?php
/**
 * This file is part of Totara Learn
 *
 * Copyright (C) 2021 onwards Totara Learning Solutions LTD
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
 * @author Matthias Bonk <matthias.bonk@totaralearning.com>
 * @package totara_core
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Trait providing functionality for faking a language pack.
 *
 * Can be used in test cases extending advanced_testcase.
 *
 * @package totara_core
 */
trait language_pack_faker_trait {

    abstract public function overrideLangString($string, $component, $value, $acceptnonexistentstring = false);

    /**
     * Create a fake language pack for testing.
     *
     * @param string $language_key
     * @param array $customised_translations Array structured like this: [component => [translation_key => translation]]
     */
    protected function add_fake_language_pack(string $language_key, array $customised_translations): void {
        // Make the cache include our fake language, so the string manager methods will find it.
        $sm = get_string_manager();
        $rc = new ReflectionClass('core_string_manager_standard');
        $get_key_suffix = $rc->getMethod('get_key_suffix');
        $get_key_suffix->setAccessible(true);
        $rccache = $rc->getProperty('menucache');
        $rccache->setAccessible(true);
        $cachekey = 'list_'.$get_key_suffix->invokeArgs($sm, array());
        $cache = $rccache->getValue($sm);
        $cache->set($cachekey, [
            'en' => get_string('thislanguage', 'langconfig').' (en)',
            $language_key => 'Fake language (' . $language_key . ')'
        ]);
        self::assertCount(2, get_string_manager()->get_list_of_translations(true));

        // Set the customised translations for our fake language.
        force_current_language($language_key);
        foreach ($customised_translations as $component => $translations) {
            foreach ($translations as $key => $value) {
                $this->overrideLangString($key, $component, $value);
            }
        }
        // Empty string disables the current language forcing from above.
        force_current_language('');
    }
}
