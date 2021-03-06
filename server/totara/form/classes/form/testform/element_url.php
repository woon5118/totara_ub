<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2016 onwards Totara Learning Solutions LTD
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
 * @author Sam Hemelryk <sam.hemelryk@totaralearning.com>
 * @package totara_form
 */

namespace totara_form\form\testform;

use totara_form\form\element\url;
use totara_form\form\group\section;
use totara_form\form\clientaction\hidden_if;

/**
 * URL test form
 *
 * @author Sam Hemelryk <sam.hemelryk@totaralearning.com>
 * @copyright 2016 Totara Learning Solutions Ltd {@link http://www.totaralearning.com/}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package totara_form
 */
class element_url extends form {

    /**
     * Returns the name for this test form.
     * @return string
     */
    public static function get_form_test_name() {
        return 'Basic url element';
    }

    /**
     * Returns the current data for this form.
     * @return array
     */
    public static function get_current_data_for_test() {
        return [
            'url_with_current_data' => 'http://www.totaralearning.com',
            'url_frozen_with_current_data' => 'https://www.totaralearning.com',
        ];
    }

    /**
     * Defines the test form
     */
    public function definition() {

        $this->model->add(new url('url_basic', 'Basic url'));
        $url_required = $this->model->add(new url('url_required', 'Required basic url'));
        $url_required->set_attribute('required', true);
        $url_required->add_help_button('cachejs', 'core_admin'); // Just a random help string.
        $this->model->add(new url('url_with_current_data', 'url with current data'))->add_help_button('cachejs', 'core_admin'); // Just a random help string.;
        $this->model->add(new url('url_frozen_empty', 'Empty frozen url'))->set_frozen(true);
        $this->model->add(new url('url_frozen_with_current_data', 'Frozen url with current data'))->set_frozen(true);

        $section = $this->model->add(new section('test_hiddenif', 'Testing Hiddenif'));
        $hiddenif_primary = $section->add(new url('hiddenif_primary', 'Hidden if reference'));
        $hiddenif_secondary_a = $section->add(new url('hiddenif_secondary_a', 'Visible when test is not empty'));
        $hiddenif_secondary_b = $section->add(new url('hiddenif_secondary_b', 'Visible when test is empty'));
        $hiddenif_secondary_c = $section->add(new url('hiddenif_secondary_c', 'Visible when test is not equal to \'https://totaralearning.com\''));
        $hiddenif_secondary_d = $section->add(new url('hiddenif_secondary_d', 'Visible when test equals \'https://totaralearning.com\''));
        $hiddenif_secondary_e = $section->add(new url('hiddenif_secondary_e', 'Visible when test is not filled'));
        $hiddenif_secondary_f = $section->add(new url('hiddenif_secondary_f', 'Visible when test is filled'));

        $this->model->add_clientaction(new hidden_if($hiddenif_secondary_a))->is_empty($hiddenif_primary);
        $this->model->add_clientaction(new hidden_if($hiddenif_secondary_b))->not_empty($hiddenif_primary);
        $this->model->add_clientaction(new hidden_if($hiddenif_secondary_c))->is_equal($hiddenif_primary, 'https://totaralearning.com');
        $this->model->add_clientaction(new hidden_if($hiddenif_secondary_d))->not_equals($hiddenif_primary, 'https://totaralearning.com');
        $this->model->add_clientaction(new hidden_if($hiddenif_secondary_e))->is_filled($hiddenif_primary);
        $this->model->add_clientaction(new hidden_if($hiddenif_secondary_f))->not_filled($hiddenif_primary);

        $section = $this->model->add(new section('test_hiddenif_required', 'Testing Hiddenif with required'));
        $hiddenif_required_a = $section->add(new url('hiddenif_required_a', 'Visible when required url is not empty'));
        $hiddenif_required_b = $section->add(new url('hiddenif_required_b', 'Visible when required url is empty'));
        $this->model->add_clientaction(new hidden_if($hiddenif_required_a))->is_empty($url_required);
        $this->model->add_clientaction(new hidden_if($hiddenif_required_b))->not_empty($url_required);

        $this->add_required_elements();
    }


}
