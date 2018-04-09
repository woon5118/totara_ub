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
 * @author Courteney Brownie <courteney.brownie@totaralearning.com>
 * @author Riana Rossouw <riana.rossouw@totaralearning.com>
 * @package tool_sitepolicy
 */

namespace tool_sitepolicy\form;

defined('MOODLE_INTERNAL') || die();

use tool_sitepolicy\localisedpolicy;
use totara_form\form;
use totara_form\form\element\text;
use totara_form\form\element\static_html;
use totara_form\form\element\textarea;
use totara_form\form\element\hidden;
use totara_form\form\group\section;

/**
 * Class translationform
 * This form is responsible for localisation of the site policy version.
 */
class translationform extends form {
    protected function definition() {

        $model = $this->model;
        $model->add(new hidden('localisedpolicy', PARAM_INT));
        $model->add(new hidden('language', PARAM_SAFEDIR)); // We can't use PARAM_LANG here as the language pack may have been uninstalled.
        $model->add(new hidden('policyversionid', PARAM_INT));

        $model->add(new static_html('primarytitle', '&nbsp;', $this->parameters['primarytitle']));

        $policytitle = $model->add(new text('title', get_string('policytitle', 'tool_sitepolicy'), PARAM_TEXT));
        $policytitle->set_attribute('size', 1335);
        $policytitle->set_attribute('required', true);

        $primarypolicy = $model->add(new textarea('primarypolicytext', '', PARAM_TEXT));
        $primarypolicy->set_attributes(['rows' => 10]);
        $primarypolicy->set_frozen(true);
        $policyeditor = $model->add(new textarea('policytext', get_string('policystatement', 'tool_sitepolicy'), PARAM_TEXT));
        $policyeditor->set_attributes(['rows' => 20, 'required' => true]);

        $statement = new element\statement('statements', true);
        $model->add($statement);
        $statement->set_attribute('required', true);

        if ($this->parameters['versionnumber'] > 1) {
            $consent = $model->add(new section('whatschanged', get_string('policyversionwhatschanged', 'tool_sitepolicy')));
            $primarywhatsnew = $consent->add(new textarea('primarywhatsnew', '', PARAM_TEXT));
            $primarywhatsnew->set_frozen(true);
            $policywhatsnew = $consent->add(new textarea('whatsnew', get_string('policyversionchanges', 'tool_sitepolicy'), PARAM_TEXT));
            $policywhatsnew->set_attributes(['rows' => 5]);
        }
        $model->add_action_buttons(true, get_string('translationsave', 'tool_sitepolicy'));
    }

    /**
     * Returns class responsible for form handling.
     * This is intended especially for ajax processing.
     *
     * @return null|\totara_form\form_controller
     */
    public static function get_form_controller() {
        return new translationform_controller();
    }

    /**
     * Prepares current data for this form, given the localised policy, and the primary localised policy.
     *
     * @param localisedpolicy $primarypolicy
     * @param localisedpolicy $localisedpolicy
     * @return array
     */
    public static function prepare_current_data(localisedpolicy $primarypolicy, localisedpolicy $localisedpolicy) {
        $primaryoptions = $primarypolicy->get_statements(false);
        $options = $localisedpolicy->get_statements(false);
        if (empty($options)) {
            $options = [];
            foreach ($primaryoptions as $option) {
                $option->primarystatement = $option->statement;
                $option->statement = '';
                $option->primaryprovided = $option->provided;
                $option->provided = '';
                $option->primarywithheld = $option->withheld;
                $option->withheld = '';

                $options[] = $option;
            }

        } else {
            // We need to merge the primary and localised options
            // Find all options that are only in one list
            $newprimary = array_diff_key($primaryoptions, $options);

            foreach ($options as $idx => $option) {
                if (isset($primaryoptions[$idx])) {
                    $primaryoption = $primaryoptions[$idx];
                    $option->primarystatement = $primaryoption->statement;
                    $option->primaryprovided = $primaryoption->provided;
                    $option->primarywithheld = $primaryoption->withheld;
                } else {
                    if ($idx < 0) {
                        $option[$idx]->removedstatement = true;
                    } else {
                        unset($option[$idx]);
                    }
                }
            }

            foreach ($newprimary as $idx) {
                $primaryoption = $primaryoptions[$idx];
                $option->primarystatement = $primaryoption->statement;
                $option->statement = '';
                $option->primaryprovided = $primaryoption->provided;
                $option->provided = '';
                $option->primarywithheld = $primaryoption->withheld;
                $option->withheld = '';

                $options[] = $option;
            }
        }

        $currentdata = [
            'localisedpolicy' => $localisedpolicy->get_id(),
            'language' => $localisedpolicy->get_language(false),
            'policyversionid' => $localisedpolicy->get_policyversion()->get_id(),
            'title' => $localisedpolicy->get_title(false),
            'policytext' => $localisedpolicy->get_policytext(false),
            'statements' => $options,
            'whatsnew' => $localisedpolicy->get_whatsnew(),

            // Set primary fields required to display in textareas.
            'primarypolicytext' => $primarypolicy->get_policytext(false),
            'primarywhatsnew' => $primarypolicy->get_whatsnew(),
        ];
        return $currentdata;
    }
}
