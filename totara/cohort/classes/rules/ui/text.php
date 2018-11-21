<?php
/*
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author David Curry <david.curry@totaralearning.com>
 * @author Kian Nguyen <kian.nguyen@totaralearning.com>
 * @author Aaron Wells <aaronw@catalyst.net.nz>
 * @package totara_cohort
 */

namespace totara_cohort\rules\ui;

defined('MOODLE_INTERNAL') || die();

use totara_cohort\rules\ui\base_form as base_form;

/**
 * UI for a rule that is defined by a text field (which takes a comma-separated list of values) and an equal/not-equal operator.
 */
class text extends base_form {
    /**
     * @var array
     */
    public $params = array(
        'equal' => 0,
        'listofvalues' => 1
    );

    /**
     *
     * @param string $description Brief description of this rule
     * @param string $example Example text to put below the text field
     */
    public function __construct($description, $example) {
        $this->description = $description;
        $this->example = $example;
    }

    /**
     * Fill in default form data. For this dialog, we need to take the listofvalues and concatenate it
     * into a comma-separated list
     * @return array
     */
    protected function addFormData() {
        // Figure out starting data
        $formdata = array();
        if (isset($this->equal)) {
            $formdata['equal'] = $this->equal;
        }
        if (isset($this->listofvalues)) {
            $formdata['listofvalues'] = implode(',',$this->listofvalues);
        }
        return $formdata;
    }

    /**
     * Form elements for this dialog. That'll be the equal/notequal menu, and the text field
     * @param MoodleQuickForm $mform
     */
    protected function addFormFields(&$mform) {

        // Put everything in one row to make it look cooler
        global $COHORT_RULES_OP_IN_LIST;
        $row = array();
        $row[0] = $mform->createElement(
            'select',
            'equal',
            '',
            $COHORT_RULES_OP_IN_LIST
        );
        $row[1] = $mform->createElement('text', 'listofvalues', '');
        $mform->addGroup($row, 'row1', ' ', ' ', false);
        if (isset($this->example)) {
            $mform->addElement('static', 'exampletext', '', $this->example);
        }

        // Make sure they filled in the text field
        $mform->addGroupRule(
            'row1',
            array(
                1 => array(
                    array(0 => get_string('error:mustpickonevalue', 'totara_cohort'), 1 => 'callback', 2 => 'validate_emptyruleuiform', 3 => 'client')
                )
            )
        );

        $error = get_string('error:mustpickonevalue', 'totara_cohort');
        $isemptyopt = COHORT_RULES_OP_IN_ISEMPTY;

        // Allow empty value for ​​listofvalues as long as the rule is "is empty"
        $js = <<<JS
<script type="text/javascript">
function validate_emptyruleuiform() {
    var sucess = true;

    if ($('#id_listofvalues').val() === '' && $('#id_equal').val() !== '$isemptyopt') {
        if ($('#id_error_listofvalues').length == 0 ) {
            $('div#fgroup_id_row1 > fieldset').prepend('<span id="id_error_listofvalues" class="error">{$error}</span><br>');
        }
        sucess = false;
    }
    return sucess;
}
</script>
JS;
        $mform->addElement('html', $js);
    }

    /**
     * Get the description of this rule for the list of rules
     * @param int $ruleid
     * @param boolean $static only display static description, without action controls
     * @return string
     */
    public function getRuleDescription($ruleid, $static=true) {
        global $COHORT_RULES_OP_IN_LIST;
        if (!isset($this->equal) || !isset($this->listofvalues)) {
            return get_string('error:rulemissingparams', 'totara_cohort');
        }

        $strvar = new \stdClass();
        $strvar->desc = $this->description;
        $strvar->join = $COHORT_RULES_OP_IN_LIST[$this->equal];

        // Show list of values only if the rule is different from "is_empty"
        $strvar->vars = '';
        if ($this->equal != COHORT_RULES_OP_IN_ISEMPTY) {
            $strvar->vars = '"' . htmlspecialchars(implode('", "', $this->listofvalues)) . '"';
        }

        return get_string('ruleformat-descjoinvars', 'totara_cohort', $strvar);
    }

    /**
     * Process the data returned by this UI element's form elements
     * @param \cohort_rule_sqlhandler $sqlhandler
     */
    public function handleDialogUpdate($sqlhandler) {
        $equal = required_param('equal', PARAM_INT);
        $listofvalues = required_param('listofvalues', PARAM_RAW);
        $listofvalues = explode(',', $listofvalues);
        array_walk(
            $listofvalues,
            function(&$value, $key){
                $value = trim($value);
            }
        );
        $this->equal = $sqlhandler->equal = $equal;
        $this->listofvalues = $sqlhandler->listofvalues = $listofvalues;
        $sqlhandler->write();
    }
}
