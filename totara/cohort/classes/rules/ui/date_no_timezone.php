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
 * UI for rule that uses date without timezone
 */
class date_no_timezone extends base_form {
    /**
     * @var array
     */
    public $params = array(
        'operator' => 0,
        'date' => 0,
    );

    /**
     * @var string
     */
    public $description;

    /**
     * @var string
     */
    public $formclass = 'totara_cohort\rules\ui\form_date';

    /**
     * cohort_rule_ui_date_no_timezone constructor.
     * @param string $description
     */
    public function __construct($description){
        $this->description = $description;
    }

    /**
     * Fill in the default form values. For this dialog, we need to specify which of the two
     * rows is active based on the selected operator. And if it's the date row, we need to
     * format the date from a timestamp to a user date
     *
     * @return array of data to be added to the form
     */
    protected function addFormData() {
        // Set up default values
        $formdata = array();
        $formdata['fixedordynamic'] = 1;
        $formdata['beforeafterdate'] = get_string('datepickerlongyearplaceholder', 'totara_core');
        if (isset($this->operator)) {
            if ($this->operator == COHORT_RULE_DATE_OP_AFTER_FIXED_DATE || $this->operator == COHORT_RULE_DATE_OP_BEFORE_FIXED_DATE) {
                $formdata['fixedordynamic'] = 1;
                $formdata['beforeaftermenu'] = $this->operator;
                if (!empty($this->date)) {
                    // For the custom date field, the date is always saved as UTC.
                    $formdata['beforeafterdate'] = userdate($this->date, get_string('datepickerlongyearphpuserdate', 'totara_core'), 'UTC', false);
                }
            } else if (
            in_array(
                $this->operator,
                array(
                    COHORT_RULE_DATE_OP_BEFORE_PAST_DURATION,
                    COHORT_RULE_DATE_OP_WITHIN_PAST_DURATION,
                    COHORT_RULE_DATE_OP_WITHIN_FUTURE_DURATION,
                    COHORT_RULE_DATE_OP_AFTER_FUTURE_DURATION
                )
            )
            ) {
                $formdata['fixedordynamic'] = 2;
                $formdata['durationmenu'] = $this->operator;
                if (isset($this->date)) {
                    $formdata['durationdate'] = $this->date;
                }
            } else {
                $formdata['fixedordynamic'] = 1;
            }
        }
        return $formdata;
    }

    /**
     * Form fields for this dialog. We have the elements on two rows, with the top row being for before/after a fixed date,
     * and the bottom row being for before/after/within a fixed present/past duration. A radio button called "fixedordynamic"
     * indicates which one is selected
     *
     * @param MoodleQuickForm $mform
     */
    public function addFormFields(&$mform) {

        // Put everything on two rows to make it look cooler.
        $row = array();
        $row[0] = $mform->createElement('radio', 'fixedordynamic', '', '', 1);
        $row[1] = $mform->createElement(
            'select',
            'beforeaftermenu',
            '',
            array(
                COHORT_RULE_DATE_OP_BEFORE_FIXED_DATE=>get_string('datemenufixeddatebefore', 'totara_cohort'),
                COHORT_RULE_DATE_OP_AFTER_FIXED_DATE=>get_string('datemenufixeddateafter', 'totara_cohort')
            )
        );
        $row[2] = $mform->createElement('text', 'beforeafterdate', '');
        $mform->addGroup($row, 'beforeafterrow', ' ', ' ', false);

        $datepickerjs = <<<JS
<script type="text/javascript">

    $(function() {
        $('#id_beforeafterdate').datepicker(
            {
                dateFormat: '
JS;
        $datepickerjs .= get_string('datepickerlongyeardisplayformat', 'totara_core');
        $datepickerjs .= <<<JS
',
                showOn: 'both',
                buttonImage: M.util.image_url('t/calendar'),
                buttonImageOnly: true,
                beforeShow: function() { $('#ui-datepicker-div').css('z-index', 1600); },
                constrainInput: true
            }
        );
    });
    </script>
JS;
        $mform->addElement('html', $datepickerjs);
        $durationmenu = array(
            COHORT_RULE_DATE_OP_BEFORE_PAST_DURATION =>   get_string('datemenudurationbeforepast', 'totara_cohort'),
            COHORT_RULE_DATE_OP_WITHIN_PAST_DURATION =>   get_string('datemenudurationwithinpast', 'totara_cohort'),
            COHORT_RULE_DATE_OP_WITHIN_FUTURE_DURATION => get_string('datemenudurationwithinfuture', 'totara_cohort'),
            COHORT_RULE_DATE_OP_AFTER_FUTURE_DURATION =>  get_string('datemenudurationafterfuture', 'totara_cohort'),
        );
        if ($this->rule == 'systemaccess-firstlogin' || $this->rule == 'systemaccess-lastlogin') {
            // Remove COHORT_RULE_DATE_OP_WITHIN_FUTURE_DURATION and COHORT_RULE_DATE_OP_AFTER_FUTURE_DURATION.
            unset($durationmenu[COHORT_RULE_DATE_OP_WITHIN_FUTURE_DURATION], $durationmenu[COHORT_RULE_DATE_OP_AFTER_FUTURE_DURATION]);
        }
        $row = array();
        $row[0] = $mform->createElement('radio', 'fixedordynamic', '', '', 2);
        $row[1] = $mform->createElement('select', 'durationmenu', '', $durationmenu);
        $row[2] = $mform->createElement('text', 'durationdate', '');
        $row[3] = $mform->createElement('static', '', '', get_string('durationdays', 'totara_cohort'));
        $mform->addGroup($row, 'durationrow', ' ', ' ', false);

        $mform->disabledIf('beforeaftermenu','fixedordynamic','neq',1);
        $mform->disabledIf('beforeafterdate','fixedordynamic','neq',1);
        $mform->disabledIf('durationmenu','fixedordynamic','neq',2);
        $mform->disabledIf('durationdate','fixedordynamic','neq',2);
    }

    /**
     * Print a description of the rule in text, for the rules list page
     * @param int $ruleid
     * @param boolean $static only display static description, without action controls
     * @return string
     */
    public function getRuleDescription($ruleid, $static=true) {
        global $CFG, $COHORT_RULE_DATE_OP;

        if (!isset($this->operator) || !isset($this->date)) {
            return get_string('error:rulemissingparams', 'totara_cohort');
        }

        $strvar = new \stdClass();
        $strvar->desc = $this->description;

        switch ($this->operator) {
            case COHORT_RULE_DATE_OP_BEFORE_FIXED_DATE:
            case COHORT_RULE_DATE_OP_AFTER_FIXED_DATE:
                $a = userdate($this->date, get_string('datepickerlongyearphpuserdate', 'totara_core'), 'UTC', false);
                break;
            case COHORT_RULE_DATE_OP_BEFORE_PAST_DURATION:
            case COHORT_RULE_DATE_OP_WITHIN_PAST_DURATION:
            case COHORT_RULE_DATE_OP_WITHIN_FUTURE_DURATION:
            case COHORT_RULE_DATE_OP_AFTER_FUTURE_DURATION:
                $a = $this->date;
                break;
        }

        $strvar->vars = get_string("dateis{$COHORT_RULE_DATE_OP[$this->operator]}", 'totara_cohort', $a);

        return get_string('ruleformat-descvars', 'totara_cohort', $strvar);
    }

    /**
     * Writes the new rule to the database
     *
     * @param cohort_rule_sqlhandler $sqlhandler
     */
    public function handleDialogUpdate($sqlhandler) {
        $fixedordynamic = required_param('fixedordynamic', PARAM_INT);
        switch($fixedordynamic) {
            case 1:
                $operator = required_param('beforeaftermenu', PARAM_INT);
                $dateparam = required_param('beforeafterdate', PARAM_TEXT);
                $dateformat = get_string('datepickerlongyearparseformat', 'totara_core');
                // We save the date as a timestamp with time of midday UTC.
                $dateparam .= " 12:00:00";
                $dateformat .= " H:i:s";
                $date = totara_date_parse_from_format($dateformat, $dateparam, false, 'UTC');
                break;
            case 2:
                $operator = required_param('durationmenu', PARAM_INT);
                $date = required_param('durationdate', PARAM_INT);
                break;
            default:
                return false;
        }
        $this->operator = $sqlhandler->operator = $operator;
        $this->date = $sqlhandler->date = $date;
        $sqlhandler->write();
    }
}
