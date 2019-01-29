<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2019 onwards Totara Learning Solutions LTD
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
 * @author Oleg Demeshev <oleg.demeshev@totaralearning.com>
 * @package totara_cohort
 */

namespace totara_cohort\rules\ui;

defined('MOODLE_INTERNAL') || die();

use totara_catalog\optional_param;
use totara_cohort\rules\ui\base_form as base_form;

class none_min_max_exactly extends base_form {

    // User is a manager with/has direct/indirect/temporary/appraisees.
    const COHORT_RULES_OP_NONE = 0;
    const COHORT_RULES_OP_MIN = 1; // lets keep the old value for consistency with t12, "has direct report" rule
    const COHORT_RULES_OP_MAX = 20;
    const COHORT_RULES_OP_EXACT = 30;

    /** @var array $params */
    public $params = [
        'equal' => 0,
        'listofvalues' => 1
    ];

    /** @var array $operators */
    private static $operators = [];

    /** @var string $formsuffix for listofvalues text input */
    private static $formsuffix;

    /** @var string $error message for listofvalues text input */
    private static $error;

    public function __construct() {
        self::$operators = [
            self::COHORT_RULES_OP_NONE  => get_string('none',  'totara_cohort'),
            self::COHORT_RULES_OP_MIN   => get_string('min',   'totara_cohort'),
            self::COHORT_RULES_OP_MAX   => get_string('max',   'totara_cohort'),
            self::COHORT_RULES_OP_EXACT => get_string('exact', 'totara_cohort')
        ];
        self::$formsuffix = get_string('rulelegend-alljobassign-persons', 'totara_cohort');
        self::$error = get_string('error:mustspecifyvalue', 'totara_cohort');
    }

    /**
     * Fill in default form data.
     * @return array
     */
    protected function addFormData(): array {
        // Figure out starting data
        $formdata = [];
        if (isset($this->equal)) {
            $formdata['equal'] = $this->equal;
        }
        if (isset($this->listofvalues)) {
            $formdata['listofvalues'] = array_shift($this->listofvalues);
        }
        return $formdata;
    }

    /**
     * Form elements for this dialog.
     * @param MoodleQuickForm $mform
     */
    protected function addFormFields(&$mform) {

        $mform->addElement('html', \html_writer::tag('h4', $this->label));
        $row = array();
        $row[0] = $mform->createElement(
            'select',
            'equal',
            '',
            self::$operators
        );
        $row[1] = $mform->createElement('text', 'listofvalues', '');
        $row[2] = $mform->createElement('static', 'persons', '', self::$formsuffix);
        $mform->setType('listofvalues', PARAM_INT);
        $mform->disabledIf('listofvalues', 'equal', 'eq', self::COHORT_RULES_OP_NONE);
        $mform->addGroup($row, 'row1', ' ', ' ', false);
        // Make sure they filled in the text field
        $mform->addGroupRule(
            'row1',
            [1 =>
                [
                    [
                        0 => self::$error,
                        1 => 'callback',
                        2 => 'validate_emptyruleuiform',
                        3 => 'client'
                    ]
                ]
            ]
        );
        $mform->addElement('html', self::add_js());
    }

    /**
     * Get the description of this rule
     * @param int $ruleid
     * @param boolean $static only display static description, without action controls
     * @return string
     */
    public function getRuleDescription($ruleid, $static = true): string {

        if (!isset($this->equal) || !isset($this->listofvalues)) {
            return get_string('error:rulemissingparams', 'totara_cohort');
        }

        $strvar = new \stdClass();
        $strvar->desc = $this->label;
        $strvar->join = self::$operators[$this->equal];

        // Show value only if the rule is different from "none"
        $strvar->vars = '';
        if ($this->equal != self::COHORT_RULES_OP_NONE) {
            $strvar->vars = '"' . array_shift($this->listofvalues) . '" ' . self::$formsuffix;
        }
        return get_string('ruleformat-descjoinvars', 'totara_cohort', $strvar);
    }

    /**
     * Process the data returned by this UI element's form elements
     * @param \cohort_rule_sqlhandler $sqlhandler
     */
    public function handleDialogUpdate($sqlhandler) {
        $equal = required_param('equal', PARAM_INT);
        $listofvalues = optional_param('listofvalues', 0, PARAM_INT);
        $this->equal = $sqlhandler->equal = $equal;
        $this->listofvalues = $sqlhandler->listofvalues = $listofvalues;
        $sqlhandler->write();
    }

    /**
     * Generate java sctipt to validate listofvalues input.
     * @return string
     */
    protected static function add_js(): string {
        $msg = self::$error;
        $none = self::COHORT_RULES_OP_NONE;
        // Allow empty value as long as the rule is "none".
        $js = <<<JS
<script type="text/javascript">
function validate_emptyruleuiform() {
    var sucess = true;
    if ($('#id_listofvalues').val() === '' && $('#id_equal').val() !== '$none') {
        if ($('#id_error_listofvalues').length == 0 ) {
            $('div#fgroup_id_row1 > fieldset').prepend('<span id="id_error_listofvalues" class="error">{$msg}</span><br>');
        }
        sucess = false;
    }
    return sucess;
}
</script>
JS;
        return $js;
    }
}