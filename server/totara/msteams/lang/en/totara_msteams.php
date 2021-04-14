<?php
/**
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
 * @author  Johannes Cilliers <johannes.cilliers@totaralearning.com>
 * @package totara_msteams
 */

defined('MOODLE_INTERNAL') || die();

$string['alert:opennew'] = 'This page is not fully compatible with Microsoft Teams. Click \'open in new window\'.';

// Bot framework
$string['botfw:cmd_help'] = 'help';
$string['botfw:cmd_signin'] = 'signin';
$string['botfw:cmd_signout'] = 'signout';
$string['botfw:error_auth_invalid'] = 'Authentication request was not valid.';
$string['botfw:error_auth_timeout'] = 'Authentication timed out.';
$string['botfw:friendly_username1'] = '{$a->alternatename}';
$string['botfw:friendly_username2'] = '{$a->firstname}';
$string['botfw:generic_failure'] = 'Something went wrong. Please try again.';
$string['botfw:help_help'] = 'View help on how to use the app';
$string['botfw:help_signin'] = 'Sign in to receive notifications';
$string['botfw:help_signout'] = 'Sign out to disable notifications';
$string['botfw:help_command'] = 'Run "{$a}" command';
$string['botfw:msg_canthearyou'] = 'Sorry, I\'m unsure what you\'re asking. Please contact your site administrator to find out how you can use the app.';
$string['botfw:msg_help'] = 'To find out what you can do with this app please contact your site administrator.';
$string['botfw:msg_private'] = 'Hi, I can\'t respond to that here. Please start a one-on-one chat instead.';
$string['botfw:msg_private_name'] = 'Hi {$a}, I can\'t respond to that here. Please start a one-on-one chat instead.';
$string['botfw:msg_signin'] = 'Sign in to get started.';
$string['botfw:msg_signin_already'] = 'Hi {$a}, you\'re already signed in.';
$string['botfw:msg_signin_button'] = 'Sign in';
$string['botfw:msg_signin_done'] = 'Great, {$a}. You\'re all set up to receive notifications.';
$string['botfw:msg_signout_already'] = 'Hi, you\'re already signed out.';
$string['botfw:msg_signout_done'] = 'Thanks {$a}. You\'ll no longer receive notifications here.';
$string['botfw:msg_subscribe_already'] = 'Hi {$a}, you\'re already set up to receive notifications.';
$string['botfw:msg_welcome'] = 'Welcome! This bot will let you receive all your notifications right here. You\'ll need to sign in to your account to get this started.';
$string['botfw:mx_command_search'] = 'Search catalogue';
$string['botfw:mx_command_search_desc'] = 'Search catalogue';
$string['botfw:mx_command_search_term'] = 'Search';
$string['botfw:mx_command_search_term_desc'] = 'Search';
$string['botfw:mx_initialrun'] = 'Browse learning content';
$string['botfw:mx_nomatches'] = 'We couldn\'t find any matches.';
$string['botfw:mx_signin'] = 'Sign in';
$string['botfw:mx_signinretry'] = 'Something went wrong. Please try again.';
$string['botfw:none'] = 'None';
$string['botfw:output_title'] = 'Signing in';

$string['check:appid_invalid'] = 'The application ID \'{$a}\' is not in a valid format for Microsoft services.';
$string['check:appid_notset'] = 'The application ID is not set.';
$string['check:bot_appid'] = 'Bot app ID';
$string['check:bot_disabled'] = 'Both bot feature and messaging extension feature are disabled.';
$string['check:bot_id_invalid'] = 'The bot app ID \'{$a}\' is not in a valid format for Microsoft services.';
$string['check:bot_id_notset'] = 'The bot app ID is not set.';
$string['check:bot_secret'] = 'Bot client secret';
$string['check:bot_secret_notset'] = 'The client secret is not set.';
$string['check:mf_appid'] = 'Manifest app ID';
$string['check:mf_appversion'] = 'Manifest app version';
$string['check:mf_appversion_notset'] = 'The app version is not set.';
$string['check:mf_desc'] = 'Manifest short description';
$string['check:mf_desc_notset'] = 'The short description is not set.';
$string['check:mf_desc_toolong'] = 'The short description is too long. It must not exceed {$a} characters.';
$string['check:mf_descfull'] = 'Manifest full description';
$string['check:mf_descfull_notset'] = 'The full description is not set.';
$string['check:mf_descfull_toolong'] = 'The full description is too long. It must not exceed {$a} characters.';
$string['check:mf_name'] = 'Manifest short name';
$string['check:mf_name_notset'] = 'The short name is not set.';
$string['check:mf_name_toolong'] = 'The short name is too long. It must not exceed {$a} characters.';
$string['check:mf_namefull'] = 'Manifest full name';
$string['check:mf_namefull_toolong'] = 'The full name is too long. It must not exceed {$a} characters.';
$string['check:mf_package'] = 'Manifest package name';
$string['check:mf_package_nodefault'] = 'The package name must not be default.';
$string['check:mf_package_notset'] = 'The package name is not set.';
$string['check:pub_mpnid_toolong'] = 'The Microsoft Partner Network ID is too long. It must not exceed {$a} characters.';
$string['check:pub_name_notset'] = 'The publisher\'s name is not set.';
$string['check:pub_name_toolong'] = 'The publisher\'s name is too long. It must not exceed {$a} characters.';
$string['check:pub_url_empty'] = 'The site URL is used by default.';
$string['check:pub_website_notset'] = 'The publisher\'s website is not set.';
$string['check:pub_website_insecure'] = 'The publisher\'s website is not an https URL.';
$string['check:pub_website_toolong'] = 'The publisher\'s website is too long. It must not exceed {$a} characters.';
$string['check:site_privacy_notset'] = 'The privacy policy is not set.';
$string['check:site_privacy_insecure'] = 'The privacy policy is not an https URL.';
$string['check:site_privacy_toolong'] = 'The privacy policy is a too long URL. It must not exceed {$a} characters.';
$string['check:site_terms_notset'] = 'The terms of use is not set.';
$string['check:site_terms_insecure'] = 'The terms of use is not an https URL.';
$string['check:site_terms_toolong'] = 'The terms of use is a too long URL. It must not exceed {$a} characters.';
$string['check:wwwroot'] = 'Site is https';
$string['check:wwwroot_insecure'] = 'Your site needs to use HTTPS.';
$string['check:grid_skip'] = 'If disabled, users will not be able to see catalogue images in a card and the messaging extension.';
$string['check:notticked'] = '\'{$a}\' is not enabled.';
$string['check:notequals'] = '\'{$a->name}\' is not set to \'{$a->value}\'.';
$string['check:pass'] = '<span aria-hidden="true">-</span>';
$string['check:sso_auth'] = 'SSO OAuth2 service';
$string['check:sso_disabled'] = 'Single sign-on is disabled.';
$string['check:sso_id'] = 'SSO app ID';
$string['check:sso_scope'] = 'SSO resource scope';
$string['check:sso_scope_invalid'] = 'The resource scope \'{$a}\' is not in a valid format.';
$string['check:sso_scope_notset'] = 'The resource scope is not set.';
$string['customtab_list_label'] = 'Select item';
$string['customtab_name_error'] = 'Name is required';
$string['customtab_name_label'] = 'Tab name';
$string['customtab_name_placeholder'] = 'Name your tab';
$string['customtab_search_label'] = 'Search the catalog and select an item to be added in a new tab';
$string['customtab_search_placeholder'] = 'Search';
$string['customtab_title'] = 'Create a tab';
$string['enable_msteams'] = 'Microsoft Teams integration';
$string['enable_msteams_description'] = 'When enabled this will allow users to view and interact with their Library of resources/playlists, current learning, notifications and Find learning catalogue through Microsoft Teams. If disabled, the Microsoft Teams API will not be accessible.';
$string['error:catalognotavailable'] = 'Find learning is not available. For more information, talk to your administrator.';
$string['error:manifest_createzip'] = 'Can not create a zip archive.';
$string['error:mylearningnotavailable'] = 'Current learning is not available. For more information, talk to your administrator.';
$string['error:nodirectaccess'] = 'Direct access to the page is prohibited.';
$string['error:oauth2_disabled'] = 'OAuth2 authentication plugin is not enabled.';
$string['error:oauth2_issuerdisabled'] = 'OAuth2 service \'{$a}\' is not enabled.';
$string['error:oauth2_issuerinvalid'] = 'OAuth2 service \'{$a}\' is not properly configured. Make sure the client ID and the client secret are correctly set.';
$string['error:oauth2_missingendpoint'] = 'The {$a->type}_endpoint is not found for the OAuth2 service \'{$a->issuer}\'.';
$string['error:oauth2_noissuer'] = 'OAuth2 service is not set.';
$string['error:sso_failure_desc'] = 'There is an error in the configuration and set up of your organisation\'s Totara app. Please contact your site administrator.';
$string['error:sso_failure_title'] = 'Single sign-on authentication failure';
$string['howtouploadapp'] = '';
$string['info:badsettings'] = 'One or more settings are not correctly set.';
$string['info:goodsettings'] = 'All settings have been verified, you can download the manifest file.';
$string['manifest_downloadbutton'] = 'Download manifest file';
$string['pluginname'] = 'Microsoft Teams';
$string['report:config'] = 'Information';
$string['report:report'] = 'Report';
$string['report:status'] = 'Status';
$string['report:status_failed'] = 'Failed';
$string['report:status_pass'] = 'OK';
$string['report:status_skipped'] = 'Skipped';
$string['report:summary'] = 'Verification of settings';
$string['settings:bot_app_id'] = 'Bot app ID';
$string['settings:bot_app_id_help'] = '';
$string['settings:bot_app_secret'] = 'Client secret';
$string['settings:bot_app_secret_help'] = '';
$string['settings:bot_feature_enabled'] = 'Bot feature enabled';
$string['settings:bot_feature_enabled_help'] = '';
$string['settings:header_app'] = 'Set up the Totara app';
$string['settings:header_app_help'] = '';
$string['settings:header_bot'] = 'Set up the conversational bot';
$string['settings:header_bot_help'] = '';
$string['settings:header_branding'] = 'Customise the Totara app';
$string['settings:header_branding_help'] = '';
$string['settings:header_publisher'] = 'Publisher information';
$string['settings:header_publisher_help'] = '';
$string['settings:header_sso'] = 'Set up single sign-on';
$string['settings:header_sso_help'] = '';
$string['settings:install_msteams_app'] = 'Install your Totara app on Microsoft Teams';
$string['settings:manifest_accent_colour'] = 'Accent colour';
$string['settings:manifest_accent_colour_default'] = '#FFFFFF';
$string['settings:manifest_accent_colour_help'] = 'Select the background colour of an app icon.';
$string['settings:manifest_app_desc'] = 'Short description';
$string['settings:manifest_app_desc_default'] = 'Access your Totara learning content within Microsoft Teams.';
$string['settings:manifest_app_desc_help'] = 'A short description for the app is required (80 characters or less). This is used when space is limited.';
$string['settings:manifest_app_full_name'] = 'Full name';
$string['settings:manifest_app_full_name_help'] = 'A full name for the app is optional and only needed if your preferred name exceeds 30 characters. The full name must be 100 characters or less.';
$string['settings:manifest_app_fulldesc'] = 'Full description';
$string['settings:manifest_app_fulldesc_default'] = 'The Totara app for Microsoft Teams allows you to access your learning and collaborate with others within Teams. You can access current learning, explore learning content, create and interact with playlists and resources.';
$string['settings:manifest_app_fulldesc_help'] = 'A full description for the app is required. Your short description must not be repeated within the long description.';
$string['settings:manifest_app_icon_colour'] = 'Full colour icon';
$string['settings:manifest_app_icon_colour_help'] = 'Upload a full-colour icon in png format.';
$string['settings:manifest_app_icon_outline'] = 'Outline icon';
$string['settings:manifest_app_icon_outline_help'] = 'Upload a transparent outline icon in png format.';
$string['settings:manifest_app_id'] = 'Manifest app ID';
$string['settings:manifest_app_id_help'] = 'A unique identifier for this app. It must be a registered GUID.';
$string['settings:manifest_app_name'] = 'Short name';
$string['settings:manifest_app_name_default'] = 'Totara';
$string['settings:manifest_app_name_help'] = 'A short name for the app is required (30 characters or less).';
$string['settings:manifest_app_version'] = 'App version';
$string['settings:manifest_app_version_default'] = '1.0.0';
$string['settings:manifest_app_version_help'] = 'If you update any settings for Teams integration, the version must be incremented and it must follow the <a href="{$a->semverurl}" target="_blank">semver</a> standard.';
$string['settings:manifest_package_name'] = 'Package name';
$string['settings:manifest_package_name_help'] = 'A unique identifier for this app in reverse domain notation.';
$string['settings:messaging_extension_enabled'] = 'Messaging extension feature enabled';
$string['settings:messaging_extension_enabled_help'] = '';
$string['settings:oauth2_issuer'] = 'OAuth2 service';
$string['settings:oauth2_issuer_help'] = 'The <a href="{$a->authurl}">OAuth2 authentication plugin</a> must be enabled for single sign-on. The <a href="{$a->issuerurl}">OAuth2 service</a> must use Microsoft\'s identity provider.';
$string['settings:oauth2_issuer_none'] = '(None)';
$string['settings:page_setup'] = 'Microsoft Teams integration';
$string['settings:page_totara_app'] = 'Totara app installation';
$string['settings:publisher_mpnid'] = 'Microsoft Partner Network ID';
$string['settings:publisher_mpnid_help'] = 'Leave this field as blank unless your organisation has joined the <a href="https://partner.microsoft.com/" target="_blank">Microsoft Partner Network</a>.';
$string['settings:publisher_name_help'] = 'The name of the organisation publishing the app (32 characters or less).';
$string['settings:publisher_privacypolicy_help'] = 'The https URL to the organisation or publisher\'s privacy policy.';
$string['settings:publisher_termsofuse_help'] = 'The https URL to the organisation or publisher\'s terms of use.';
$string['settings:publisher_website_help'] = 'The https URL to the publisher\'s website.';
$string['settings:sso_app_id'] = 'SSO app ID';
$string['settings:sso_app_id_help'] = '';
$string['settings:sso_scope'] = 'Resource scope';
$string['settings:sso_scope_help'] = 'Format: api://<fully-qualified-your-domain-name.com\>/<app_id_for_sso\>';
$string['spinner_loading'] = "Loading\u{2026}";
$string['spinner_signingin'] = "Signing in\u{2026}";
$string['tab:catalog'] = 'Find learning';
$string['tab:library'] = 'Library';
$string['tab:mylearning'] = 'Current learning';
$string['userdataitemuser'] = 'MS Teams user records';
$string['userdataitemusersetting'] = 'MS Teams user settings';
$string['userdataitemuserstate'] = 'MS Teams user authentication state';

// Deprecated since Totara 13.3
$string['settings:manifest_package_name_default'] = '';
