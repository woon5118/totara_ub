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
 * Automatically generated strings for Moodle installer
 *
 * Do not edit this file manually! It contains just a subset of strings
 * needed during the very first steps of installation. This file was
 * generated automatically using the
 * list of strings defined in /install/stringnames.txt.
 *
 * @package   installer
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['admindirname'] = 'Admin directory';
$string['availablelangs'] = 'Available language packs';
$string['clialreadyconfigured'] = 'The configuration file config.php already exists. Please use admin/cli/install_database.php to install Totara for this site.';
$string['clialreadyinstalled'] = 'The configuration file config.php already exists. Please use admin/cli/install_database.php to upgrade Totara for this site.';
$string['cliinstallheader'] = 'Totara {$a} command line installation program';
$string['cliinstalllangdownloadfailedoptions'] = '1 - retry
2 - select different language';
$string['cliinstalllangdownloadstart'] = 'Downloading language packs';
$string['cliinstalllangdownloadsuccess'] = 'Language pack "{$a}" successfully downloaded.';
$string['configfilenotwritten'] = 'The installer script was not able to automatically create a config.php file containing your chosen settings, probably because the Totara directory is not writeable. You can manually copy the following code into a file named config.php within the root directory of Totara.';
$string['configfilewritten'] = 'config.php has been successfully created';
$string['configurationcompletesub'] = 'Totara made an attempt to save your configuration in a file in the root of your Totara installation.';
$string['databasehost'] = 'Database host';
$string['databasename'] = 'Database name';
$string['databasetypehead'] = 'Choose database driver';
$string['dataroot'] = 'Data directory';
$string['datarootpermission'] = 'Data directories permission';
$string['dbprefix'] = 'Tables prefix';
$string['dirroot'] = 'Totara directory';
$string['environmenthead'] = 'Checking your environment ...';
$string['environmentsub2'] = 'Each Totara release has some minimum PHP version requirement and a number of mandatory PHP extensions.
Full environment check is done before each install and upgrade. Please contact server administrator if you do not know how to install new version or enable PHP extensions.';
$string['errorsinenvironment'] = 'Environment check failed!';
$string['chooselanguagehead'] = 'Choose a language';
$string['chooselanguagesub'] = 'Please choose a language for the installation. This language will also be used as the default language for the site, though it may be changed later.';
$string['installation'] = 'Installation';
$string['langdownloaderror'] = 'Unfortunately the language "{$a}" could not be downloaded. The installation process will continue in English.';
$string['memorylimithelp'] = '<p>The PHP memory limit for your server is currently set to {$a}.</p>

<p>This may cause Totara to have memory problems later on, especially 
   if you have a lot of modules enabled and/or a lot of users.</p>

<p>We recommend that you configure PHP with a higher limit if possible, like 40M.  
   There are several ways of doing this that you can try:</p>
<ol>
<li>If you are able to, recompile PHP with <i>--enable-memory-limit</i>.  
    This will allow Totara to set the memory limit itself.</li>
<li>If you have access to your php.ini file, you can change the <b>memory_limit</b> 
    setting in there to something like 40M.  If you don\'t have access you might 
    be able to ask your administrator to do this for you.</li>
<li>On some PHP servers you can create a .htaccess file in the Totara directory 
    containing this line:
    <blockquote><div>php_value memory_limit 40M</div></blockquote>
    <p>However, on some servers this will prevent <b>all</b> PHP pages from working 
    (you will see errors when you look at pages) so you\'ll have to remove the .htaccess file.</p></li>
</ol>';
$string['paths'] = 'Paths';
$string['pathserrcreatedataroot'] = 'Data directory ({$a->dataroot}) cannot be created by the installer.';
$string['pathshead'] = 'Confirm paths';
$string['pathsrodataroot'] = 'Dataroot directory is not writable.';
$string['pathsroparentdataroot'] = 'Parent directory ({$a->parent}) is not writeable. Data directory ({$a->dataroot}) cannot be created by the installer.';
$string['pathssubadmindir'] = 'A very few webhosts use /admin as a special URL for you to access a
control panel or something.  Unfortunately this conflicts with the standard location for the Totara admin pages.  You can fix this by
renaming the admin directory in your installation, and putting that  new name here.  For example: <em>totaraadmin</em>. This will fix admin links in Totara.';
$string['pathssubdataroot'] = '<p>A directory where Totara will store all file content uploaded by users.</p>
<p>This directory should be both readable and writeable by the web server user (usually \'www-data\', \'nobody\', or \'apache\').</p>
<p>It must not be directly accessible over the web.</p>
<p>If the directory does not currently exist, the installation process will attempt to create it.</p>';
$string['pathssubdirroot'] = '<p>The full path to the directory containing the Totara code.</p>';
$string['pathssubwwwroot'] = '<p>The full address where Totara will be accessed i.e. the address that users will enter into the address bar of their browser to access Totara.</p>
<p>It is not possible to access Totara using multiple addresses. If your site is accessible via multiple addresses then choose the easiest one and set up a permanent redirect for each of the other addresses.</p>
<p>If your site is accessible both from the Internet, and from an internal network (sometimes called an Intranet), then use the public address here.</p>
<p>If the current address is not correct, please change the URL in your browser\'s address bar and restart the installation.</p>';
$string['pathsunsecuredataroot'] = 'Dataroot location is not secure';
$string['pathswrongadmindir'] = 'Admin directory does not exist';
$string['phpextension'] = '{$a} PHP extension';
$string['phpversion'] = 'PHP version';
$string['phpversionhelp'] = '<p>Totara requires a PHP version of at least 7.2.10.</p>
<p>You are currently running version {$a}.</p>
<p>You must upgrade PHP or move to a host with a newer version of PHP.</p>';
$string['welcomep10'] = '{$a->installername} ({$a->installerversion})';
$string['welcomep20'] = 'You are seeing this page because you have successfully installed and 
    launched the <strong>{$a->packname} {$a->packversion}</strong> package in your computer. Congratulations!';
$string['welcomep30'] = 'This release of the <strong>{$a->installername}</strong> includes the applications 
    to create an environment in which <strong>Totara</strong> will operate, namely:';
$string['welcomep40'] = 'The package also includes <strong>Totara {$a->moodlerelease} ({$a->moodleversion})</strong>.';
$string['welcomep50'] = 'The use of all the applications in this package is governed by their respective 
    licences. The complete <strong>{$a->installername}</strong> package is 
    <a href="http://www.opensource.org/docs/definition_plain.html">open source</a> and is distributed 
    under the <a href="http://www.gnu.org/copyleft/gpl.html">GPL</a> license.';
$string['welcomep60'] = 'The following pages will lead you through some easy to follow steps to 
    configure and set up <strong>Totara</strong> on your computer. You may accept the default 
    settings or, optionally, amend them to suit your own needs.';
$string['welcomep70'] = 'Click the "Next" button below to continue with the set up of <strong>Totara</strong>.';
$string['wwwroot'] = 'Web address';
