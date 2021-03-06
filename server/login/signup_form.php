<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * User sign-up form.
 *
 * @package    core
 * @subpackage auth
 * @copyright  1999 onwards Martin Dougiamas  http://dougiamas.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');
require_once($CFG->dirroot.'/user/profile/lib.php');
require_once($CFG->dirroot . '/user/editlib.php');

class login_signup_form extends moodleform implements renderable, templatable {
    function definition() {
        global $USER, $CFG;

        $mform = $this->_form;
        $positionid = $this->_customdata['positionid'];
        $organisationid = $this->_customdata['organisationid'];
        $managerjaid = $this->_customdata['managerjaid'];

        $mform->addElement('header', 'createuserandpass', get_string('createuserandpass'), '');


        $mform->addElement('text', 'username', get_string('username'), 'maxlength="100" size="12"');
        $mform->setType('username', PARAM_RAW);
        $mform->addRule('username', get_string('missingusername'), 'required', null, 'client');

        if (!empty($CFG->passwordpolicy)){
            $mform->addElement('static', 'passwordpolicyinfo', '', print_password_policy());
        }
        $mform->addElement('password', 'password', get_string('password'), 'size="12"');
        $mform->setType('password', core_user::get_property_type('password'));
        $mform->addRule('password', get_string('missingpassword'), 'required', null, 'client');

        $mform->addElement('header', 'supplyinfo', get_string('supplyinfo'),'');

        $mform->addElement('text', 'email', get_string('email'), 'maxlength="100" size="25"');
        $mform->setType('email', core_user::get_property_type('email'));
        $mform->addRule('email', get_string('missingemail'), 'required', null, 'client');
        $mform->setForceLtr('email');

        $mform->addElement('text', 'email2', get_string('emailagain'), 'maxlength="100" size="25"');
        $mform->setType('email2', core_user::get_property_type('email'));
        $mform->addRule('email2', get_string('missingemail'), 'required', null, 'client');
        $mform->setForceLtr('email2');

        $namefields = useredit_get_required_name_fields();
        foreach ($namefields as $field) {
            $mform->addElement('text', $field, get_string($field), 'maxlength="100" size="30"');
            $mform->setType($field, core_user::get_property_type('firstname'));
            $stringid = 'missing' . $field;
            if (!get_string_manager()->string_exists($stringid, 'moodle')) {
                $stringid = 'required';
            }
            $mform->addRule($field, get_string($stringid), 'required', null, 'client');
        }

        $mform->addElement('text', 'city', get_string('city'), 'maxlength="120" size="20"');
        $mform->setType('city', core_user::get_property_type('city'));
        if (!empty($CFG->defaultcity)) {
            $mform->setDefault('city', $CFG->defaultcity);
        }

        $country = get_string_manager()->get_list_of_countries();
        $default_country[''] = get_string('selectacountry');
        $country = array_merge($default_country, $country);
        $mform->addElement('select', 'country', get_string('country'), $country);

        if( !empty($CFG->country) ){
            $mform->setDefault('country', $CFG->country);
        }else{
            $mform->setDefault('country', '');
        }

        profile_signup_fields($mform);

        if ($this->signup_captcha_enabled()) {
            $mform->addElement('recaptcha', 'recaptcha_element', get_string('security_challenge', 'auth'));
            $mform->addHelpButton('recaptcha_element', 'recaptcha', 'auth');
            $mform->closeHeaderBefore('recaptcha_element');
        }

        $requirenojs = false;
        $nojs = optional_param('nojs', 0, PARAM_BOOL);

        // Manage positions in signup self-registration.
        if (get_config('totara_job', 'allowsignupposition')) {
            profile_signup_position($mform, $nojs, $positionid);
            $requirenojs = true;
        }

        // Manage organisations in signup self-registration.
        if (get_config('totara_job', 'allowsignuporganisation')) {
            profile_signup_organisation($mform, $nojs, $organisationid);
            $requirenojs = true;
        }

        // Manage managers in signup self-registration.
        if (get_config('totara_job', 'allowsignupmanager')) {
            profile_signup_manager($mform, $nojs, $managerjaid);
            $requirenojs = true;
        }

        if ($requirenojs) {
            if (!$nojs) {
                $mform->addElement('html', html_writer::tag('noscript', html_writer::tag('p', get_string('formrequiresjs', 'totara_hierarchy') .
                    html_writer::link(new moodle_url(qualified_me(), array('nojs' => '1')), get_string('clickfornonjsform', 'totara_hierarchy')))));
            }
        }

        if (!empty($CFG->sitepolicy)) {
            $mform->addElement('header', 'policyagreement', get_string('policyagreement'), '');
            $mform->setExpanded('policyagreement');
            $mform->addElement('static', 'policylink', '', '<a href="'.$CFG->sitepolicy.'" target="_blank">'.get_String('policyagreementclick').'</a>');
            $mform->addElement('checkbox', 'policyagreed', get_string('policyaccept'));
            $mform->addRule('policyagreed', get_string('policyagree'), 'required', null, 'client');
        }

        // buttons
        $this->add_action_buttons(true, get_string('createaccount'));

    }

    function definition_after_data(){
        $mform = $this->_form;
        $mform->applyFilter('username', 'trim');

        // Trim required name fields.
        foreach (useredit_get_required_name_fields() as $field) {
            $mform->applyFilter($field, 'trim');
        }
    }

    /**
     * Validate user supplied data on the signup form.
     *
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $files array of uploaded files "element_name"=>tmp_file_path
     * @return array of "element_name"=>"error_description" if there are errors,
     *         or an empty array if everything is OK (true allowed for backwards compatibility too).
     */
    public function validation($data, $files) {
    global $CFG, $DB;
        $errors = parent::validation($data, $files);

        if ($this->signup_captcha_enabled()) {
            $recaptchaelement = $this->_form->getElement('recaptcha_element');
            if (!empty($this->_form->_submitValues['g-recaptcha-response'])) {
                $response = $this->_form->_submitValues['g-recaptcha-response'];
                if (!$recaptchaelement->verify($response)) {
                    $errors['recaptcha_element'] = get_string('incorrectpleasetryagain', 'auth');
                }
            } else {
                $errors['recaptcha_element'] = get_string('missingrecaptchachallengefield');
            }

            if (!empty($errors['recaptcha_element'])) {
                return $errors;
            }
        }

        $authplugin = get_auth_plugin($CFG->registerauth);

        if ($DB->record_exists('user', array('username'=>$data['username'], 'mnethostid'=>$CFG->mnet_localhost_id))) {
            $errors['username'] = get_string('usernameexists');
        } else {
            //check allowed characters
            if ($data['username'] !== core_text::strtolower($data['username'])) {
                $errors['username'] = get_string('usernamelowercase');
            } else {
                if ($data['username'] !== core_user::clean_field($data['username'], 'username')) {
                    $errors['username'] = get_string('invalidusername');
                }

            }
        }

        //check if user exists in external db
        //TODO: maybe we should check all enabled plugins instead
        if ($authplugin->user_exists($data['username'])) {
            $errors['username'] = get_string('usernameexists');
        }


        if (! validate_email($data['email'])) {
            $errors['email'] = get_string('invalidemail');

        } else if ($DB->record_exists_select('user', "LOWER(email) = LOWER(:email)", array('email' => $data['email']))) {
            $errors['email'] = get_string('emailexists') . ' ' .
                get_string('emailexistssignuphint', 'moodle',
                    html_writer::link(new moodle_url('/login/forgot_password.php'), get_string('emailexistshintlink')));
        }
        if (empty($data['email2'])) {
            $errors['email2'] = get_string('missingemail');

        } else if ($data['email2'] != $data['email']) {
            $errors['email2'] = get_string('invalidemail');
        }
        if (!isset($errors['email'])) {
            if ($err = email_is_not_allowed($data['email'])) {
                $errors['email'] = $err;
            }

        }

        $errmsg = '';
        if (!check_password_policy($data['password'], $errmsg)) {
            $errors['password'] = $errmsg;
        }

        // TOTARA: We need to validate that managerid is correct for the managerjaid specified.
        if (get_config('totara_job', 'allowsignupmanager')) {
            if (!empty($data['managerjaid'])) {
                if (empty($data['managerid'])) {
                    // Something's wrong. Advise the user to try again or select a manager later.
                    $errors['managerselector'] = get_string('managernomatchja', 'totara_job');
                } else {
                    $managerja = \totara_job\job_assignment::get_with_id($data['managerjaid']);
                    if ($managerja->userid != $data['managerid']) {
                        // Something's wrong. Advise the user to try again or select a manager later.
                        $errors['managerselector'] = get_string('managernomatchja', 'totara_job');
                    }
                }
            } else {
                if (!empty($data['managerid'])) {
                    // You can't currently add assign a manager without a manager's job assignment id.
                    $errors['managerselector'] = get_string('managernomatchja', 'totara_job');
                }
            }
        }

        // Validate customisable profile fields. (profile_validation expects an object as the parameter with userid set)
        $dataobject = (object)$data;
        $dataobject->id = 0;
        $errors += profile_validation($dataobject, $files);

        return $errors;
    }

    /**
     * Returns whether or not the captcha element is enabled, and the admin settings fulfil its requirements.
     * @return bool
     */
    public function signup_captcha_enabled() {
        global $CFG;
        $authplugin = get_auth_plugin($CFG->registerauth);
        return !empty($CFG->recaptchapublickey) && !empty($CFG->recaptchaprivatekey) && $authplugin->is_captcha_enabled();
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output Used to do a final render of any components that need to be rendered for export.
     * @return array
     */
    public function export_for_template(renderer_base $output) {
        ob_start();
        $this->display();
        $formhtml = ob_get_contents();
        ob_end_clean();
        $context = [
            'formhtml' => $formhtml
        ];
        return $context;
    }
}
