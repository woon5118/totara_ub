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
 * Lang strings
 *
 * @package    report
 * @subpackage security
 * @copyright  2008 petr Skoda
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['configuration'] = 'Configuration';
$string['description'] = 'Description';
$string['details'] = 'Details';
$string['check_configrw_details'] = '<p>It is recommended that the file permissions of config.php are changed after installation so that the file cannot be modified by the web server.
Please note that this measure does not improve security of the server significantly, though it may slow down or limit general exploits.</p>';
$string['check_configrw_name'] = 'Writable config.php';
$string['check_configrw_ok'] = 'config.php can not be modified by PHP scripts.';
$string['check_configrw_warning'] = 'PHP scripts may modify config.php.';
$string['check_cookiehttponly_error'] = 'Please enable HTTP only cookies.';
$string['check_cookiehttponly_details'] = '<p>It is recommended that HTTP only cookies is enabled to prevent client side scripts from accessing cookies set by the server, helping to mitigate the risk of XSS attacks.</p><p>The HTTP only flag is supported by all modern browsers but please be aware that older browsers may not support this flag and therefore should be considered less secure.</p>';
$string['check_cookiehttponly_name'] = 'HTTP only cookies';
$string['check_cookiehttponly_ok'] = 'HTTP only cookies enabled.';
$string['check_cookiesecure_details'] = '<p>If you enable https communication it is strongly recommended that you also enable secure cookies. You should also add permanent redirection from http to https. Ideally also serve HSTS headers well.</p>';
$string['check_cookiesecure_error'] = 'Please enable secure cookies';
$string['check_cookiesecure_name'] = 'Secure cookies';
$string['check_cookiesecure_ok'] = 'Secure cookies enabled.';
$string['check_defaultuserrole_details'] = '<p>All logged in users are given capabilities of the default user role. Please make sure no risky capabilities are allowed in this role.</p>
<p>The only supported legacy type for the default user role is <em>Authenticated user</em>. The course view capability must not be enabled.</p>';
$string['check_defaultuserrole_error'] = 'The default user role "{$a}" is incorrectly defined!';
$string['check_defaultuserrole_name'] = 'Default role for all users';
$string['check_defaultuserrole_notset'] = 'Default role is not set.';
$string['check_defaultuserrole_ok'] = 'Default role for all users definition is OK.';
$string['check_devgraphql_details'] = '<p>When implementing new code programmers may enable development mode for GraphQL based APIs by defining GRAPHQL_DEVELOPMENT_MODE constant in config.php. This mode must not be enabled on production servers for security and performance reasons.</p>';
$string['check_devgraphql_error'] = 'GraphQL development mode is active';
$string['check_devgraphql_name'] = 'GraphQL development mode';
$string['check_devgraphql_ok'] = 'GraphQL development mode is disabled';
$string['check_displayerrors_details'] = '<p>Enabling the PHP setting <code>display_errors</code> is not recommended on production sites because error messages can reveal sensitive information about your server.</p>';
$string['check_displayerrors_error'] = 'The PHP setting to display errors is enabled. It is recommended that this is disabled.';
$string['check_displayerrors_name'] = 'Displaying of PHP errors';
$string['check_displayerrors_ok'] = 'Displaying of PHP errors disabled.';
$string['check_emailchangeconfirmation_details'] = '<p>It is recommended that an email confirmation step is required when users change their email address in their profile. If disabled, spammers may try to exploit the server to send spam.</p>
<p>Email field may be also locked from authentication plugins, this possibility is not considered here.</p>';
$string['check_emailchangeconfirmation_error'] = 'Users may enter any email address.';
$string['check_emailchangeconfirmation_info'] = 'Users may enter email addresses from allowed domains only.';
$string['check_emailchangeconfirmation_name'] = 'Email change confirmation';
$string['check_emailchangeconfirmation_ok'] = 'Confirmation of change of email address in user profile.';
$string['check_embed_details'] = '<p>Unlimited object embedding is very dangerous - any registered user may launch an XSS attack against other server users. This setting should be disabled on production servers.</p>';
$string['check_embed_error'] = 'Unlimited object embedding enabled - this is very dangerous for the majority of servers.';
$string['check_embed_name'] = 'Allow EMBED and OBJECT';
$string['check_embed_ok'] = 'Unlimited object embedding is not allowed.';
$string['check_frontpagerole_details'] = '<p>The default frontpage role is given to all registered users for frontpage activities. Please make sure no risky capabilities are allowed for this role.</p>
<p>It is recommended that a special role is created for this purpose and a legacy type role is not used.</p>';
$string['check_frontpagerole_error'] = 'Incorrectly defined frontpage role "{$a}" detected!';
$string['check_frontpagerole_name'] = 'Frontpage role';
$string['check_frontpagerole_notset'] = 'Frontpage role is not set.';
$string['check_frontpagerole_ok'] = 'Frontpage role definition is OK.';
$string['check_google_details'] = '<p>The Open to Google setting enables search engines to enter courses with guest access. There is no point in enabling this setting if guest login is not allowed.</p>';
$string['check_google_error'] = 'Search engine access is allowed but guest access is disabled.';
$string['check_google_info'] = 'Search engines may enter as guests.';
$string['check_google_name'] = 'Open to Google';
$string['check_google_ok'] = 'Search engine access is not enabled.';
$string['check_guest_details'] = '<p>It is recommended to prevent log in without user account. Please consider disabling the "Guest login button" setting.</p>';
$string['check_guest_name'] = 'Guest access';
$string['check_guest_ok'] = 'Users must log in with their account.';
$string['check_guest_warning'] = 'Users may access this server as guests without password.';
$string['check_guestrole_details'] = '<p>The guest role is used for guests, not logged in users and temporary guest course access. Please make sure no risky capabilities are allowed in this role.</p>
<p>The only supported legacy type for guest role is <em>Guest</em>.</p>';
$string['check_guestrole_error'] = 'The guest role "{$a}" is incorrectly defined!';
$string['check_guestrole_name'] = 'Guest role';
$string['check_guestrole_notset'] = 'Guest role is not set.';
$string['check_guestrole_ok'] = 'Guest role definition is OK.';
$string['check_https_details'] = '<p>HTTP protocol is easily exploitable, it is strongly recommended to use HTTPS protocol on all production servers.</p>';
$string['check_https_warning'] = 'HTTPS protocol is not used.';
$string['check_https_name'] = 'HTTPS protocol';
$string['check_https_ok'] = 'HTTPS protocol is used.';
$string['check_disableconsistentcleaning_details'] = '<p>We do not recommend turning on the "Disable consistent cleaning" setting</p><p>When turned on content created by trainers and other privileged users will not be consistently sanitized. HTML tags and attributes that can be used to execute security attacks will not be removed from areas such course descriptions, activity introductions and questions.</p>';
$string['check_disableconsistentcleaning_name'] = 'Disable consistent cleaning';
$string['check_disableconsistentcleaning_ok'] = 'The "Disable consistent cleaning" setting is turned off.';
$string['check_disableconsistentcleaning_serious'] = 'The "Disable consistent cleaning" setting is turned on.';
$string['check_logincsrf_name'] = 'CSRF protection on login page';
$string['check_logincsrf_ok'] = 'CSRF protection is active on login page';
$string['check_logincsrf_error'] = 'CSRF protection is disabled on login page';
$string['check_logincsrf_details'] = '<p>It is strongly recommended to keep the CSRF protection enabled on all production sites. However it is not compatible with deprecated alternate login pages and it may interfere with some custom authentication plugins. See config.example.php for more details.</p>';
$string['check_mediafilterswf_name'] = 'Enabled .swf media filter';
$string['check_mediafilterswf_ok'] = 'Flash media filter is not enabled.';
$string['check_mediafilterswf_trusteddetails'] = '<p>Automatic swf embedding can be dangerous if users are not trusted. Review the security report for XSS trusted users. If not being used, disable Flash animation on production sites.</p>';
$string['check_mediafilterswf_warning'] = 'Flash media filter is enabled for trusted users. This increases XSS risk.';
$string['check_noauth_details'] = '<p>The <em>No authentication</em> plugin is not intended for production sites. Please disable it unless this is a development test site.</p>';
$string['check_noauth_error'] = 'The No authentication plugin cannot be used on production sites.';
$string['check_noauth_name'] = 'No authentication';
$string['check_noauth_ok'] = 'No authentication plugin is disabled.';
$string['check_nodemodules_details'] = '<p>The directory <em>{$a->path}</em> contains Node.js modules and their dependencies, typically installed by the NPM utility. These modules may be required for Totara development. They are not needed to run a Totara site and they can contain potentially dangerous code exposing your site to remote attacks.</p><p>It is strongly recommended to remove the directory if the site is available via a public URL, or at least prohibit web access to it.</p>';
$string['check_nodemodules_info'] = 'The node_modules directory should not be present on public sites.';
$string['check_nodemodules_name'] = 'Node.js modules directory';
$string['check_oauth2verify_detailed'] = '<p>It is recommended that all OAuth 2 providers are configured to enforce user email account verification by enabling <code>Require email verification</code> on the OAuth 2 issuers configuration page.</p><p>A possibility exists for a user to compromise the Totara account of another user with the same email address when logging in via an OAuth 2 provider. The risk is present when either both, or one of, the OAuth 2 issuer, or the Totara site, permit users to share an email address.</p>';
$string['check_oauth2verify_info_oauth2'] = 'At least one OAuth 2 issuer is not configured to verify user email addresses';
$string['check_oauth2verify_info_ok'] = 'OAuth 2 issuers and Totara site are configured to not permit sharing of email addresses between users.';
$string['check_oauth2verify_info_totara'] = 'Totara configuration currently permits users to share an email address.';
$string['check_oauth2verify_info_totara_oauth2'] = 'Totara configuration currently permits users to share an email address.  Additionally, at least one OAuth 2 issuer is not configured to verify user email addresses.';
$string['check_oauth2verify_name'] = 'OAuth 2 email verification';
$string['check_openprofiles_details'] = 'Open user profiles can be abused by spammers. It is recommended that either <code>Force users to log in for profiles</code> or <code>Force users to log in</code> are enabled.';
$string['check_openprofiles_error'] = 'Anyone can may view user profiles without logging in.';
$string['check_openprofiles_name'] = 'Open user profiles';
$string['check_openprofiles_ok'] = 'Login is required before viewing user profiles.';
$string['check_passwordpolicy_details'] = '<p>It is recommended that a password policy is set, since password guessing is very often the easiest way to gain unauthorised access.
Do not make the requirements too strict though, as this can result in users not being able to remember their passwords and either forgetting them or writing them down.</p>';
$string['check_passwordpolicy_error'] = 'Password policy not set.';
$string['check_passwordpolicy_name'] = 'Password policy';
$string['check_passwordpolicy_ok'] = 'Password policy enabled.';
$string['check_persistentlogin_details'] = '<p>If enabled persistent logins ignore standard session timeouts and set a permanent browser cookie. This cookie is later used to automatically re-login user after browser restart or session timeout.</p>';
$string['check_persistentlogin_name'] = 'Persistent login';
$string['check_persistentlogin_ok'] = 'Persistent login is disabled.';
$string['check_persistentlogin_warning'] = 'Persistent logins may be considered to be a security risk.';
$string['check_preventexecpath_name'] = 'Executable paths';
$string['check_preventexecpath_ok'] = 'Executable paths only settable in config.php.';
$string['check_preventexecpath_warning'] = 'Executable paths can be set in the Admin GUI.';
$string['check_preventexecpath_details'] = '<p>Allowing executable paths to be set via the Admin GUI is a vector for privilege escalation.</p>';
$string['check_repositoryurl_details'] = '<p>Enabling the URL downloader can allow external users to access URLs within your internal network. You should not enable this feature if you have users who are allowed to access your LMS but not allowed to access other resources within your internal network that are accessible from the LMS server.</p>';
$string['check_repositoryurl_warning'] = 'URL downloader repository is enabled.';
$string['check_repositoryurl_name'] = 'URL downloader repository';
$string['check_repositoryurl_ok'] = 'URL downloader repository is disabled.';
$string['check_resourcesallowpdfembedding_details'] = '<p>Users may upload untrusted PDF files as File resources.</p><p>It is recommended to disable "Allow PDF embedding" settings.</p>';
$string['check_resourcesallowpdfembedding_name'] = 'Embedding of PDF files in course resources';
$string['check_resourcesallowpdfembedding_ok'] = 'Uploaded PDF file are not opened directly in browsers.';
$string['check_resourcesallowpdfembedding_warning'] = 'Uploaded PDF files may be opened directly in browsers.';
$string['check_resourcesallowxss_details'] = '<p>Users that may add or update course resources must be fully trusted because they may
use unsafe markup and files in following resource types: {$a}</p><p>It is recommended to disable all "Allow unsafe markup and files" setting.</p>';
$string['check_resourcesallowxss_name'] = 'Unsafe markup and files in course resources';
$string['check_resourcesallowxss_ok'] = 'All "Allow unsafe markup and files" resource settings are disabled.';
$string['check_resourcesallowxss_warning'] = 'Users editing course resources may use unsafe markup and files.';
$string['check_riskadmin_detailsok'] = '<p>Please verify the following list of system administrators:</p>{$a}';
$string['check_riskadmin_detailswarning'] = '<p>Please verify the following list of system administrators:</p>{$a->admins}
<p>It is recommended to assign administrator role in the system context only. The following users have (unsupported) admin role assignments in other contexts:</p>{$a->unsupported}';
$string['check_riskadmin_name'] = 'Administrators';
$string['check_riskadmin_ok'] = 'Found {$a} server administrator(s).';
$string['check_riskadmin_unassign'] = '<a href="{$a->url}">{$a->fullname} ({$a->email}) review role assignment</a>';
$string['check_riskadmin_warning'] = 'Found {$a->admincount} server administrators and {$a->unsupcount} unsupported admin role assignments.';
$string['check_riskallowxss_details'] = '<p>RISK_ALLOWXSS denotes all dangerous capabilities that only trusted users may use.</p>
<p>Please verify the following list of users and make sure that you trust them completely on this server:</p><p>{$a}</p>';
$string['check_riskallowxss_name'] = 'XSS trusted users';
$string['check_riskallowxss_warning'] = 'RISK_ALLOWXSS - found {$a} users that have to be trusted.';
$string['check_riskbackup_detailsok'] = 'No roles explicitly allow backup of user data.  However, note that admins with the "doanything" capability are still likely to be able to do this.';
$string['check_riskbackup_details_overriddenroles'] = '<p>These active overrides give users the ability to include user data in backups. Please make sure this permission is necessary.</p> {$a}';
$string['check_riskbackup_details_systemroles'] = '<p>The following system roles currently allow users to include user data in backups.  Please make sure this permission is necessary.</p> {$a}';
$string['check_riskbackup_details_users'] = '<p>Because of the above roles or local overrides, the following user accounts currently have permission to make backups containing private data from any users enrolled in their course.  Make sure they are (a) trusted and (b) protected by strong passwords:</p> {$a}';
$string['check_riskbackup_editoverride'] = '<a href="{$a->url}">{$a->name} in {$a->contextname}</a>';
$string['check_riskbackup_editrole'] = '<a href="{$a->url}">{$a->name}</a>';
$string['check_riskbackup_name'] = 'Backup of user data';
$string['check_riskbackup_ok'] = 'No roles explicitly allow backup of user data';
$string['check_riskbackup_unassign'] = '<a href="{$a->url}">{$a->fullname} ({$a->email}) in {$a->contextname}</a>';
$string['check_riskbackup_warning'] = 'Found {$a->rolecount} roles, {$a->overridecount} overrides and {$a->usercount} users with the ability to backup user data.';
$string['check_riskxss_details'] = '<p>RISK_XSS denotes all dangerous capabilities that only trusted users may use.</p>
<p>Please verify the following list of users and make sure that you trust them completely on this server:</p><p>{$a}</p>';
$string['check_riskxss_name'] = 'XSS trusted users';
$string['check_riskxss_warning'] = 'RISK_XSS - found {$a} users that have to be trusted.';
$string['check_scormsessionkeepalive_details'] = '<p>If enabled user session is kept alive in SCORM player.</p>';
$string['check_scormsessionkeepalive_name'] = 'SCORM session keep alive';
$string['check_scormsessionkeepalive_ok'] = 'Standard session timeout is enforced in SCORM player.';
$string['check_scormsessionkeepalive_warning'] = 'Keeping user session alive in SCORM player may be considered to be a small security issue.';
$string['check_unsecuredataroot_details'] = '<p>The dataroot directory must not be accessible via web. The best way to make sure the directory is not accessible is to use a directory outside the public web directory.</p>
<p>If you move the directory, you need to update the <code>$CFG->dataroot</code> setting in <code>config.php</code> accordingly.</p>';
$string['check_unsecuredataroot_error'] = 'Your dataroot directory <code>{$a}</code> is in the wrong location and is exposed to the web!';
$string['check_unsecuredataroot_name'] = 'Insecure dataroot';
$string['check_unsecuredataroot_ok'] = 'Dataroot directory must not be accessible via the web.';
$string['check_unsecuredataroot_warning'] = 'Your dataroot directory <code>{$a}</code> is in the wrong location and might be exposed to the web.';
$string['check_usernameenumeration_details'] = '<p>When a user\'s credentials are incorrect Totara is careful to be vague about the reason why so as to not let the user know whether the username or the password is incorrect. This means would be attackers must guess both the username and the password.</p>
<p>With Self registration turned on would be attackers can use the signup form to enumerate usernames and work out which are valid. Once a valid username has been identified they only need to guess the password.<br />To prevent this turn off Self registration.</p>
<p>With Protect usernames turned off would be attackers can use the forgotten password form to enumerate usernames and work out which are valid. Once a valid username has been identified they only need to guess the password.<br />To prevent this turn on Protect usernames.</p>';
$string['check_usernameenumeration_name'] = 'Username enumeration';
$string['check_usernameenumeration_ok'] = 'Protect usernames is enabled and Self registration is not enabled';
$string['check_usernameenumeration_warning'] = 'With Self registration turned on or Protect usernames turned off unauthenticated users may be able to guess existing usernames';
$string['check_webcron_details'] = '<p>Running the cron from a web browser can expose privileged information to anonymous users. It is recommended to only run the cron from the command line or set a cron password for remote access.</p>';
$string['check_webcron_warning'] = 'Anonymous users can access cron.';
$string['check_webcron_name'] = 'Web cron';
$string['check_webcron_ok'] = 'Anonymous users can not access cron.';
$string['check_xxe_risk_name'] = 'XXE risk';
$string['check_xxe_risk_critical'] = 'External entities are loaded into XML by default.';
$string['check_xxe_risk_details'] = 'Some versions of LibXML and PHP potentially loaded external entities into XML by default, meaning contents of local files could be obtained by a malicious user. Ensure that you are using up-to-date versions of LibXML and PHP to help prevent against this vulnerability.';
$string['check_xxe_risk_ok'] = 'External entities are not loaded into XML by default.';
$string['issue'] = 'Issue';
$string['pluginname'] = 'Security overview';
$string['security:view'] = 'View security report';
$string['status'] = 'Status';
$string['statuscritical'] = 'Critical';
$string['statusinfo'] = 'Information';
$string['statusok'] = 'OK';
$string['statusserious'] = 'Serious';
$string['statuswarning'] = 'Warning';
$string['timewarning'] = 'Data processing may take a long time, please be patient...';
