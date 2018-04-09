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
use totara_form\form\element\textarea;
use totara_form\form\group\section;
use totara_form\form\element\select;
use totara_form\form\element\hidden;

/**
 * Class versionform
 * This form manages primary localised policy, which defines original content and consent options for policy version
 */
class versionform extends form {
    protected function definition() {
        $model = $this->model;

        $model->add(new hidden('localisedpolicy', PARAM_INT));
        $model->add(new hidden('versionnumber', PARAM_INT));
        $model->add(new hidden('sitepolicyid', PARAM_INT));
        $model->add(new hidden('newpolicy', PARAM_BOOL));
        $model->add(new hidden('buttonlabel', PARAM_ALPHANUMEXT));
        $model->add(new hidden('ret', PARAM_TEXT));

        $options = get_string_manager()->get_list_of_translations();
        $model->add(new select('language', get_string('policyprimarylanguage', 'tool_sitepolicy'), $options));

        $policytitle = $model->add(new text('title', get_string('policytitle', 'tool_sitepolicy'), PARAM_TEXT));
        $policytitle->set_attributes(['size' => 1335, 'required' => true]);

        $policyeditor = $model->add(new textarea('policytext', get_string('policystatement', 'tool_sitepolicy'), PARAM_TEXT));
        $policyeditor->set_attributes(['rows' => 20, 'required' => true]);

        $statements = new element\statement('statements');
        $model->add($statements);
        $statements->set_attribute('required', true);

        ['versionnumber' => $versionnumber] = $this->model->get_current_data('versionnumber');
        if ($versionnumber > 1) {
            $consent = $model->add(new section('whatschanged', get_string('policyversionwhatschanged', 'tool_sitepolicy')));
            $whatsnew = $consent->add(new textarea('whatsnew', get_string('policyversionchanges', 'tool_sitepolicy'), PARAM_TEXT));
            $whatsnew->set_attributes(['rows' => 5]);
        }

        $model->add_action_buttons(true, get_string('policysave', 'tool_sitepolicy'));
    }

    /**
     * Returns class responsible for form handling.
     * This is intended especially for ajax processing.
     *
     * @return null|\totara_form\form_controller
     */
    public static function get_form_controller() {
        return new versionform_controller();
    }

    /**
     * Prepares current data for this form given the localised policy.
     *
     * @param localisedpolicy $localisedpolicy
     * @param bool $newpolicy
     * @param string $returnpage
     * @return array
     */
    public static function prepare_current_data(localisedpolicy $localisedpolicy, bool $newpolicy, string $returnpage) {
        $version = $localisedpolicy->get_policyversion();
        $currentdata = [
            'localisedpolicy' => $localisedpolicy->get_id(),
            'versionnumber' => $version->get_versionnumber(),
            'language' => $localisedpolicy->get_language(false),
            'policyversionid' => $version->get_id(),
            'title' => $localisedpolicy->get_title(false),
            'policytext' => $localisedpolicy->get_policytext(false),
            'whatsnew' => $localisedpolicy->get_whatsnew(),
            'statements' => $localisedpolicy->get_statements(false),
            'sitepolicyid' => $version->get_sitepolicy()->get_id(),
            'newpolicy' => $newpolicy,
            'ret' => $returnpage,
        ];
        return $currentdata;
    }
}