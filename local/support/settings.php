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
 * Admin settings for local_support.
 *
 * @package    local_support
 * @copyright  2026 Custom Development
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage('local_support', get_string('pluginname', 'local_support'));

    $settings->add(new admin_setting_configtext(
        'local_support/fallbackemail',
        get_string('fallbackemail', 'local_support'),
        get_string('fallbackemail_desc', 'local_support'),
        '',
        PARAM_EMAIL
    ));

    // SMS settings (SerwerSMS.pl).
    $settings->add(new admin_setting_heading(
        'local_support/sms_heading',
        get_string('sms_heading', 'local_support'),
        get_string('sms_heading_desc', 'local_support')
    ));

    $settings->add(new admin_setting_configtext(
        'local_support/sms_api_token',
        get_string('sms_api_token', 'local_support'),
        get_string('sms_api_token_desc', 'local_support'),
        '',
        PARAM_RAW_TRIMMED
    ));

    $settings->add(new admin_setting_configtext(
        'local_support/sms_sender',
        get_string('sms_sender', 'local_support'),
        get_string('sms_sender_desc', 'local_support'),
        'INFO',
        PARAM_ALPHANUMEXT
    ));

    // WordPress sync settings.
    $settings->add(new admin_setting_heading(
        'local_support/wp_sync_heading',
        get_string('wp_sync_heading', 'local_support'),
        get_string('wp_sync_heading_desc', 'local_support')
    ));

    $settings->add(new admin_setting_configtext(
        'local_support/wp_sync_endpoint',
        get_string('wp_sync_endpoint', 'local_support'),
        get_string('wp_sync_endpoint_desc', 'local_support'),
        '',
        PARAM_URL
    ));

    $settings->add(new admin_setting_configtext(
        'local_support/wp_sync_token',
        get_string('wp_sync_token', 'local_support'),
        get_string('wp_sync_token_desc', 'local_support'),
        '',
        PARAM_RAW_TRIMMED
    ));

    // ── Red theme branding ────────────────────────────────────────────────────
    $settings->add(new admin_setting_heading(
        'local_support/red_brand_heading',
        get_string('red_brand_heading', 'local_support'),
        get_string('red_brand_heading_desc', 'local_support')
    ));

    $settings->add(new admin_setting_configstoredfile(
        'local_support/red_logo',
        get_string('red_logo', 'local_support'),
        get_string('red_logo_desc', 'local_support'),
        'red_logo',
        0,
        ['maxfiles' => 1, 'accepted_types' => ['.png', '.jpg', '.jpeg', '.svg', '.gif', '.webp']]
    ));

    $settings->add(new admin_setting_configstoredfile(
        'local_support/red_favicon',
        get_string('red_favicon', 'local_support'),
        get_string('red_favicon_desc', 'local_support'),
        'red_favicon',
        0,
        ['maxfiles' => 1, 'accepted_types' => ['.ico', '.png', '.svg', '.jpg', '.jpeg']]
    ));

    // ── Gardener (green theme) branding ──────────────────────────────────────
    $settings->add(new admin_setting_heading(
        'local_support/gardener_brand_heading',
        get_string('gardener_brand_heading', 'local_support'),
        get_string('gardener_brand_heading_desc', 'local_support')
    ));

    $settings->add(new admin_setting_configstoredfile(
        'local_support/green_logo',
        get_string('green_logo', 'local_support'),
        get_string('green_logo_desc', 'local_support'),
        'green_logo',
        0,
        ['maxfiles' => 1, 'accepted_types' => ['.png', '.jpg', '.jpeg', '.svg', '.gif', '.webp']]
    ));

    $settings->add(new admin_setting_configstoredfile(
        'local_support/green_favicon',
        get_string('green_favicon', 'local_support'),
        get_string('green_favicon_desc', 'local_support'),
        'green_favicon',
        0,
        ['maxfiles' => 1, 'accepted_types' => ['.ico', '.png', '.svg', '.jpg', '.jpeg']]
    ));

    // ── Gardener (green theme) SMTP ──────────────────────────────────────────
    $settings->add(new admin_setting_heading(
        'local_support/gardener_smtp_heading',
        get_string('gardener_smtp_heading', 'local_support'),
        get_string('gardener_smtp_heading_desc', 'local_support')
    ));

    $settings->add(new admin_setting_configtext(
        'local_support/gardener_smtphosts',
        get_string('gardener_smtphosts', 'local_support'),
        get_string('gardener_smtphosts_desc', 'local_support'),
        '',
        PARAM_RAW_TRIMMED
    ));

    $settings->add(new admin_setting_configtext(
        'local_support/gardener_smtpport',
        get_string('gardener_smtpport', 'local_support'),
        get_string('gardener_smtpport_desc', 'local_support'),
        '25',
        PARAM_INT
    ));

    $smtpsecureoptions = [
        ''    => get_string('gardener_smtpsecure_none', 'local_support'),
        'ssl' => 'SSL',
        'tls' => 'TLS',
    ];
    $settings->add(new admin_setting_configselect(
        'local_support/gardener_smtpsecure',
        get_string('gardener_smtpsecure', 'local_support'),
        get_string('gardener_smtpsecure_desc', 'local_support'),
        '',
        $smtpsecureoptions
    ));

    $settings->add(new admin_setting_configtext(
        'local_support/gardener_smtpuser',
        get_string('gardener_smtpuser', 'local_support'),
        get_string('gardener_smtpuser_desc', 'local_support'),
        '',
        PARAM_RAW_TRIMMED
    ));

    $settings->add(new admin_setting_configpasswordunmask(
        'local_support/gardener_smtppass',
        get_string('gardener_smtppass', 'local_support'),
        get_string('gardener_smtppass_desc', 'local_support'),
        ''
    ));

    $settings->add(new admin_setting_configtext(
        'local_support/gardener_smtp_fromemail',
        get_string('gardener_smtp_fromemail', 'local_support'),
        get_string('gardener_smtp_fromemail_desc', 'local_support'),
        '',
        PARAM_EMAIL
    ));

    $settings->add(new admin_setting_configtext(
        'local_support/gardener_smtp_fromname',
        get_string('gardener_smtp_fromname', 'local_support'),
        get_string('gardener_smtp_fromname_desc', 'local_support'),
        '',
        PARAM_TEXT
    ));

    $ADMIN->add('localplugins', $settings);
}
