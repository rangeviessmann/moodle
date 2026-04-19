<?php
// This file is part of Moodle - http://moodle.org/
//
// Custom override of /login/forgot_password_form.php
// Changes: "username" label replaced with "PESEL".
//
// @package    core
// @subpackage auth
// @copyright  2006 Petr Skoda {@link http://skodak.org}
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');
require_once($CFG->dirroot.'/user/lib.php');
require_once($CFG->dirroot.'/login/lib.php');

/**
 * Reset forgotten password form definition.
 */
class login_forgot_password_form extends moodleform {

    /**
     * Define the forgot password form.
     */
    function definition() {
        global $USER;

        $mform    = $this->_form;
        $mform->setDisableShortforms(true);

        // Hook for plugins to extend form definition.
        core_login_extend_forgot_password_form($mform);

        // CUSTOM: Changed header from get_string('searchbyusername') to "PESEL".
        $mform->addElement('header', 'searchbyusername', get_string('searchbyusername'), '');

        // CUSTOM: PESEL is not a standard browser autocomplete field — disable autocomplete to prevent
        // the browser from filling this field when the user autofills the email field below.
        $mform->addElement('text', 'username', 'PESEL', 'size="20" autocomplete="off"');
        $mform->setType('username', PARAM_RAW);

        $mform->addElement('header', 'searchbyemail', get_string('searchbyemail'), '');

        $mform->addElement('text', 'email', get_string('email'), 'maxlength="100" size="30" autocomplete="email"');
        $mform->setType('email', PARAM_RAW_TRIMMED);

        $mform->disabledIf('email', 'username', 'neq', '');
        $mform->disabledIf('username', 'email', 'neq', '');

        $mform->addElement('html', '<hr />');

        if (forgotpassword_captcha_enabled()) {
            $mform->addElement('recaptcha', 'recaptcha_element', '');
        }

        $submitlabel = get_string('search');
        $mform->addElement('submit', 'submit', $submitlabel);
    }

    /**
     * Validate user input from the forgot password form.
     */
    function validation($data, $files) {

        $errors = parent::validation($data, $files);

        if (forgotpassword_captcha_enabled()) {
            $recaptchaelement = $this->_form->getElement('recaptcha_element');
            if (!empty($this->_form->_submitValues['g-recaptcha-response'])) {
                $response = $this->_form->_submitValues['g-recaptcha-response'];
                if (!$recaptchaelement->verify($response)) {
                    $errors['recaptcha_element'] = get_string('incorrectpleasetryagain', 'auth');
                }
            } else {
                $errors['recaptcha_element'] = get_string('missingrecaptchachallengefield');
            }
        }

        $errors = array_merge($errors, core_login_validate_extend_forgot_password_form($data));

        $errors += core_login_validate_forgot_password_data($data);

        return $errors;
    }

}
