<?php
/**
 * This file is part of Totara Enterprise Extensions.
 *
 * Copyright (C) 2020 onwards Totara Learning Solutions LTD
 *
 * Totara Enterprise Extensions is provided only to Totara
 * Learning Solutions LTD’s customers and partners, pursuant to
 * the terms and conditions of a separate agreement with Totara
 * Learning Solutions LTD or its affiliate.
 *
 * If you do not have an agreement with Totara Learning Solutions
 * LTD, you may not access, use, modify, or distribute this software.
 * Please contact [licensing@totaralearning.com] for more information.
 *
 * @author Sam Hemelryk <sam.hemelryk@totaralearning.com>
 * @package core
 */

/** @var core_config $CFG */

/***********************************************************************************************************************
Getting started
***********************************************************************************************************************/
//
// Copy this file to "config.php" and edit it, providing the settings for your site.
// All settings already uncommented are required. All others are optional
//

/***********************************************************************************************************************
Web Address
 ***********************************************************************************************************************/
//
// Set the full web address that your Totara installation will be access on.
// Important information
//  * Totara can only be accessed via a single URL.
//  * It must be either https:// or http:// Totara cannot be accessed on both. https:// is strongly recommended.
//  * Do not add a trailing slash.
//
$CFG->wwwroot   = 'https://example.com/totara';

/***********************************************************************************************************************
Data directory
 ***********************************************************************************************************************/
//
// The following is the path to the directory that Totara will use to store files.
// This includes user uploaded files, temporary and cache files, and other miscellaneous files created by Totara.
// The directory MUST be both readable and writable by the web server user.
// It should NOT be accessible via the web.
//
$CFG->dataroot  = '/var/totara/sitedata';
//
// Sets the permissions applied to new directories careted by Totara within the dataroot set above.
// The default is usually acceptable. The value must be an octal (the leading 0 is important, don't put it in quotes)
//
$CFG->directorypermissions = 02777;
//
// Set the directory Totara will use to store temporary files. If not set a temp directory will be created within the
// dataroot directory. The directory used must be accessible to all cluster nodes.
// $CFG->tempdir = '/var/totara/temp';
//
// Set the directory Totara will use to store cache files. If not set a cache directory will be created within the
// dataroot directory. The directory used must be accessible to all cluster nodes, and locking must be supported.
// $CFG->cachedir = '/var/totara/cache';
//
// Set a locally available cache directory. This is intended for server clusters and does NOT need to be shared between
// nodes. Only data that is safe to cache like this will be stored in this directory.
// $CFG->localcachedir = '/var/totara/localcache/';

/***********************************************************************************************************************
 Database configuration
**********************************************************************************************************************/
//
// The following settings are common to all databases.
// Database specific options are detailed in following sections.
//
$CFG->dbtype = 'pgsql';      // One of pgsql, mariadb, mysqli, sqlsrv
$CFG->dblibrary = 'native';  // Always 'native'
$CFG->dbhost = 'localhost';  // Host, URL or IP address
$CFG->dbname = 'totara';     // Database name
$CFG->dbuser = 'username';   // Username for your database username
$CFG->dbpass = 'password';   // Password for your database user
$CFG->prefix = 'ttr_';       // Prefix to use for all table names
$CFG->dboptions = array(
    // Used to enable persistent database connections.
    'dbpersist' => false,
    // Set to true or to the socket path.
    'dbsocket'  => false,
    // Port to use to connect to the database. When empty the default port for your chosen DB will be used.
    'dbport'    => '',
);

/***********************************************************************************************************************
Database configuration: PostgreSQL
***********************************************************************************************************************/
//
// To use a custom schema uncomment the following and set it to the name of your schema.
// You will need to create the schema within the database manually.
//
// $CFG->dboptions['dbschema'] = 'totara';
//
// PgSQL connection poolers like pgbouncer don't support advanced options on connection. When set to true advanced
// options will not be sent in the connection string, instead you will need to set them in the database.
//    ALTER DATABASE moodle SET client_encoding = UTF8;
//    ALTER DATABASE moodle SET standard_conforming_strings = on;
//    ALTER DATABASE moodle SET search_path = 'totara,public';  -- Optional, if you wish to use a custom schema.
//
// $CFG->dboptions['dbhandlesoptions'] = false';
//
// Sets the maximum number of records that can be inserted in a single operation.
// Sets larger than this will be chunked and inserted in batches.
//
// $CFG->dboptions['bulkinsertsize'] = 500;
//
// Set Totara to connect using SSL.
//  * https://www.php.net/manual/en/function.pg-connect.php
//  * https://www.postgresql.org/docs/current/libpq-ssl.html#LIBPQ-SSL-PROTECTION
//  * https://www.postgresql.org/docs/current/runtime-config-connection.html#RUNTIME-CONFIG-CONNECTION-SSL
//  * https://www.postgresql.org/docs/current/ssl-tcp.html
//
// $CFG->dboptions['sslmode'] = true;
//
// Totara Full Text Search supports one language only, you need to configure it here before installation or upgrade to
// Totara 12 otherwise the default value will be used. If you change it later you need to run following CLI script to
// rebuild all full text search indexes: admin/cli/fts_rebuild_indexes.php
// It is recommended that the language selected here is compatible with $CFG->lang.
// PostgreSQL is using 'english' configuration for full text search by default,
// for list of available options see result of "SELECT cfgname FROM pg_ts_config;". For example:
//
// $CFG->dboptions['ftslanguage'] = 'english';
// $CFG->dboptions['ftslanguage'] = 'simple';
// $CFG->dboptions['ftslanguage'] = 'german';
//
// PostgreSQL does not support Japanese and other languages with very short words without spaces in between, enable the
// following setting to get a basic experimental support of these languages.
// If the value changes then you need to run: admin/cli/fts_repopulate_tables.php
//
// $CFG->dboptions['fts3bworkaround'] = true;
//
// PostgreSQL has built in support for accent sensitive full text searches.
// PostgreSQL provides this by means of an extension called unaccent which is not created by default.
// To change accent sensitive fulltext searches for you can set the following setting according to your requirement:
//
// $CFG->dboptions['ftsaccentsensitivity'] = true;
// $CFG->dboptions['ftsaccentsensitivity'] = false;
// $CFG->dboptions['ftsaccentsensitivity'] = 'dbdefault';
//
// After changing the accent sensitivity setting you need to run the following scripts in the listed order:
//    1. admin/cli/fts_rebuild_indexes.php
//    2. admin/cli/fts_repopulate_tables.php
//

/***********************************************************************************************************************
Database configuration: MySQL + MariaDB
***********************************************************************************************************************/
//
// Set the database collation.
// It is preferred to configure the database default collation prior to installation.
//
// $CFG->dboptions['dbcollation'] = 'utf8mb4_unicode_ci';
//
// Set the Database engine.
// It is preferable to configure the database default engine prior to installation.
// InnoDB or XtraDB are recommended.
//
// $CFG->dboptions['dbengine'] = 'InnoDB';
//
// Sets the maximum number of records that can be inserted in a single operation.
// Sets larger than this will be chunked and inserted in batches.
//
// $CFG->dboptions['bulkinsertsize'] = 500;
//
// Set Totara to connect using SSL.
//  * https://www.php.net/manual/en/mysqli.ssl-set.php
//  * https://www.php.net/manual/en/mysqli.real-connect.php
//  * https://dev.mysql.com/doc/refman/8.0/en/using-encrypted-connections.html
//
// $CFG->dboptions['client_ssl'] = true;
// $CFG->dboptions['client_dont_verify_server_cert'] = false;
// $CFG->dboptions['ssl_key'] = NULL;
// $CFG->dboptions['ssl_cert'] = NULL;
// $CFG->dboptions['ssl_ca'] = NULL;
// $CFG->dboptions['ssl_capath'] = NULL;
// $CFG->dboptions['ssl_cipher'] = NULL;
// $CFG->dboptions['ssl_verify_server_cert'] = false;
//
// Totara Full Text Search supports one language only, you need to configure it here before installation or upgrade to
// Totara 12 otherwise the default value will be used. If you change it later you need to run following CLI script to
// rebuild all full text search indexes: admin/cli/fts_rebuild_indexes.php
// It is recommended that the language selected here is compatible with $CFG->lang.
//
// MySQL is using case and accent insensitive collation for full text search by default, you can specify a different
// collation here, for example:
//
// $CFG->dboptions['ftslanguage'] = 'utf8_unicode_ci';
// $CFG->dboptions['ftslanguage'] = 'utf8mb4_0900_as_ci';
// $CFG->dboptions['ftslanguage'] = 'utf8mb4_de_pb_0900_ai_ci';
//
// MySQL does not support Japanese and other languages with very short words without spaces in between, enable the
// following setting to get a basic experimental support of these languages.
// If the value changes then you need to run: admin/cli/fts_repopulate_tables.php
//
// $CFG->dboptions['fts3bworkaround'] = true;
//
// MySQL also has the full-text parser plugin ngram, which make the search easier for Chinese, Japanese, and Korean
// languages. It is also useful for tokenising concatenated words. More information about ngram parser can be found in
// MySQL documentation. To use ngram support, enable the following setting:
//
// $CFG->dboptions['ftsngram'] = true;
//
// After changing the configuration of ngram support after Totara had been installed, you will need to run the following
// scripts in the listed order:
//    1. admin/cli/fts_rebuild_indexes.php
//    2. admin/cli/fts_repopulate_tables.php
//

/***********************************************************************************************************************
Database configuration: MSSQL
***********************************************************************************************************************/
//
// Set Totara to connect using SSL.
// * https://docs.microsoft.com/en-gb/sql/connect/php/connection-options?view=sql-server-2017
//
// $CFG->dboptions['encrypt'] = true;
// $CFG->dboptions['trustservercertificate'] = true;
//
// Totara Full Text Search supports one language only, you need to configure it here before installation or upgrade to
// Totara 12 otherwise the default value will be used. If you change it later you need to run following CLI script to
// rebuild all full text search indexes: admin/cli/fts_rebuild_indexes.php
// It is recommended that the language selected here is compatible with $CFG->lang.
//
// MS SQL Server is using 'English' language by default, list of options is at
// https://docs.microsoft.com/en-us/sql/relational-databases/system-catalog-views/sys-fulltext-languages-transact-sql?view=sql-server-2017
//
// $CFG->dboptions['ftslanguage'] = 'English';
// $CFG->dboptions['ftslanguage'] = 'German';
// $CFG->dboptions['ftslanguage'] = 'Japanese';
// $CFG->dboptions['ftslanguage'] = 1028; // Traditional Chinese
// $CFG->dboptions['ftslanguage'] = 2052; // Simplified Chinese
//
// MS SQL has built in support for accent sensitive full text searches.
// Accent sensitivity is on by default and to turn it off the fulltext catalog will need to be rebuilt.
// To change accent sensitive fulltext searches for MS SQL you can set the following setting according to your requirement:
//
// $CFG->dboptions['ftsaccentsensitivity'] = true;
// $CFG->dboptions['ftsaccentsensitivity'] = false;
// $CFG->dboptions['ftsaccentsensitivity'] = 'dbdefault';
//
// After changing the accent sensitivity setting you need to run the following scripts in the listed order:
//    1. admin/cli/fts_rebuild_indexes.php
//    2. admin/cli/fts_repopulate_tables.php
//

/***********************************************************************************************************************
PHP configuration
***********************************************************************************************************************/
//
// Set the default timezone for PHP.
// It is preferable to configure this in your php.ini file, however if you don't have access to modify that then this
// can be set in config.php as follows:
//
// date_default_timezone_set('Pacific/Auckland');

/***********************************************************************************************************************
Web server configuration
***********************************************************************************************************************/
//
// Offloading file serving
// Some web servers allow file serving to be offloaded from PHP directly to the web server using special headers.
// This can be beneficial for your web server performance.
//
// Apache https://tn123.org/mod_xsendfile/
// $CFG->xsendfile = 'X-Sendfile';
//
// Lighttpd http://redmine.lighttpd.net/projects/lighttpd/wiki/X-LIGHTTPD-send-file
// $CFG->xsendfile = 'X-LIGHTTPD-send-file';
//
// Nginx http://wiki.nginx.org/XSendfile
// $CFG->xsendfile = 'X-Accel-Redirect';
//
// Directory aliases for web servers like Nginx that require them you can specify them as following:
//
// $CFG->xsendfilealiases = [];
// $CFG->xsendfilealiases['/dataroot/'] = $CFG->dataroot;
// $CFG->xsendfilealiases['/cachedir/'] = '/var/totara/cache',     // for custom $CFG->cachedir locations
// $CFG->xsendfilealiases['/localcachedir/'] = '/var/local/cache', // for custom $CFG->localcachedir locations
// $CFG->xsendfilealiases['/tempdir/']  = '/var/totara/temp',      // for custom $CFG->tempdir locations
// $CFG->xsendfilealiases['/filedir']   = '/var/totara/filedir',   // for custom $CFG->filedir locations
//
// Inform Totara that it is behind a reverse proxy load balancing configuration.
// This may also be required when using port forwarding.
// $CFG->reverseproxy = true;
//
// Inform Totara that the site is being a server that is offloading SSL.
// Please note that a site may be available on https:// or http:// but not both.
// $CFG->sslproxy = true;
//

/***********************************************************************************************************************
General settings
***********************************************************************************************************************/
//
// Set the default language for the site. This can be set through the admin user interface.
// $CFG->lang = 'en';
//
// When undertaking an operation that is expected to be resource intensive Totara will raise the memory limit available
// to PHP. This setting allows you to control how much memory is made available.
// The value must be a valid PHP memory value.
// $CFG->extramemorylimit = '1024M';

/***********************************************************************************************************************
Session handling
***********************************************************************************************************************/
//
// Totara supports several session storage methods.
// Use the following setting to set the storage method you want to use.
//
// $CFG->session_handler_class = '\core\session\database';
//
// Each requires its own configuration. The following blocks explain how to configure each to the storage methods
// Totara supports.
//
// ------------------------------------------------------------------------
// Database
//
// $CFG->session_handler_class = '\core\session\database';
// $CFG->session_database_acquire_lock_timeout = 120;
//
// ------------------------------------------------------------------------
// File
// Requires file system locking
//
// $CFG->session_handler_class = '\core\session\file';
// $CFG->session_file_save_path = $CFG->dataroot.'/sessions';
//
// ------------------------------------------------------------------------
// Memcached
// Requires a Memcached server and for the Memcached PHP extension to be available.
// It is important that you use a dedicated Memcached server for session storage. Do not configure anything else, either
// inside Totara or outside to use the same server. Memcached purges affect all stored data, and other systems using
// the same memcached server may unintentionally purge all session data.
//
// $CFG->session_handler_class = '\core\session\memcached';
// $CFG->session_memcached_save_path = '127.0.0.1:11211';
// $CFG->session_memcached_prefix = 'memc.sess.key.';
// $CFG->session_memcached_acquire_lock_timeout = 120;
// $CFG->session_memcached_lock_expire = 7200;
// $CFG->session_memcached_lock_retry_sleep = 150;   // ms
//
// ------------------------------------------------------------------------
// Native Redis
// Requires a Redis server, and for the Redis PHP extension to be available (version >= 5.0.0)
// The Redis server SET command must support EX and NX options.
// See https://github.com/phpredis/phpredis#php-session-handler for more information.
//
// $CFG->session_handler_class = '\core\session\redis5';
// $CFG->session_redis5_host = '127.0.0.1';              // Optional.
// $CFG->session_redis5_port = 6379;                     // Optional.
// $CFG->session_redis5_timeout = 5;                     // Optional (seconds).
// $CFG->session_redis5_database = 0;                    // Optional, database number.
// $CFG->session_redis5_auth = '';                       // Optional, password.
// $CFG->session_redis5_prefix = 'PHPREDIS_SESSION';     // Optional.
// $CFG->session_redis5_lock_expire = 7200;              // Optional, default is $CFG->sessiontimeout (seconds).
// $CFG->session_redis5_lock_wait_time = 200000;         // Optional, default is 0.2s (microseconds).
// $CFG->session_redis5_lock_retries = 100;              // Optional, default is 100 times.
// $CFG->session_redis5_serializer_use_igbinary = false; // Optional, default is PHP built-in serializer.
//
// Single master setup is required; RedisArray and RedisCluster environments are not currently supported.
//
// ------------------------------------------------------------------------
// Redis (legacy)
// Requires a Redis server, and for the Redis PHP Extension to be available (version >= 2.0.0)
// The Redis server SET command must support EX.
//
// $CFG->session_handler_class = '\core\session\redis';
// $CFG->session_redis_host = '127.0.0.1';
// $CFG->session_redis_port = 6379;  // Optional.
// $CFG->session_redis_database = 0;  // Optional, default is db 0.
// $CFG->session_redis_auth = ''; // Optional, default is don't set one.
// $CFG->session_redis_prefix = ''; // Optional, default is don't set one.
// $CFG->session_redis_acquire_lock_timeout = 120;
// $CFG->session_redis_lock_expire = 7200;
// $CFG->session_redis_lock_retry = 100; // ms
//
// The igbinary serialised can be used instead of the default serialiser provided by PHP if you want.
// If you change the serialiser setting you must flush the database.
//
// $CFG->session_redis_serializer_use_igbinary = true;
//
// ------------------------------------------------------------------------
// Memcache
// Requires a memcached server and the memcache PHP extension.
// It is important that you use a dedicated Memcached server for session storage. Do not configure anything else, either
// inside Totara or outside to use the same server. Memcached purges affect all stored data, and other systems using
// the same memcached server may unintentionally purge all session data.
// This session handler has been deprecated and will be removed in Totara 14. We recommend you use the memcached session
// handler instead.
//
// $CFG->session_handler_class = '\core\session\memcache';
// $CFG->session_memcache_save_path = '127.0.0.1:11211';
// $CFG->session_memcache_acquire_lock_timeout = 120;
//
// ------------------------------------------------------------------------
// Other session settings
//
// Totara stores the time a session was modified in the database. How frequently this happens can be configured.
// $CFG->session_update_timemodified_frequency = 20; // In seconds.
//
// For performance reasons some scripts (such as file serving) are by default allowed to acquire
// read-only sessions without locking. You can disable this feature by uncommenting following line:
// $CFG->allow_lockless_readonly_sessions = false;
//
// Track the current users IP and ensure it does not change during a session.
// This helps prevent the possibility of sessions being hijacked by XSS, but may cause problems for users coming through
// proxies that change the IP address regularly.
// $CFG->tracksessionip = true;
//

/***********************************************************************************************************************
Mail
***********************************************************************************************************************/
//
// The following settings alter the rules applied when handling email bounces
// $CFG->handlebounces = true;
// $CFG->minbounces = 10;
// $CFG->bounceratio = 0.20;
//
// Some mail systems require prefixes be used in outgoing mail in order to handle bounces. The prefix required will
// depend upon the system that you use.
// $CFG->mailprefix = 'ttr+'; // Exim and Postfix
// $CFG->mailprefix = 'ttr-'; // Qmail
// $CFG->maildomain = 'example.com';
//
// Notify of database connection errors by email to the following address.
// $CFG->emailconnectionerrorsto = 'errors@example.com';
//
// Force Totara to use a real user account as the noreply user when sending mail. By default Totara will create a dummy
// user for this purpose and use the configured noreply address.
// $CFG->noreplyuserid = -10;
//
// Force Totara to use a real user account as the support user when sending mail. By default Totara will create a dummy
//// user for this purpose and use the configured support email address.
// $CFG->supportuserid = -20;
//



/***********************************************************************************************************************
Themes
***********************************************************************************************************************/
//
// Set an additional directory in which themes can exist. This can be outside of the source directory root, but must be
// readable by the web server user. It does not need to be accesible via the web.
// $CFG->themedir = '/location/of/extra/themes';
//
// Set the order in which theme selection occurs.
// Priority is set from highest to lowest. Any theme selection point not included in this list is essentially disabled.
// Individual selection points such as user, and course still need to be enabled in the product.
// $CFG->themeorder = ['course', 'category', 'session', 'user', 'site'];
//
//
//

/***********************************************************************************************************************
Backup and restore
 ***********************************************************************************************************************/
//
// Forcibly prevent the creation of user accounts when restoring backups.
// This setting overrides any capabilities and permissions otherwise granted to the user. Attempting to restore a course
// containing users that would require creation will lead to an error.
// $CFG->disableusercreationonrestore = true;
//
// Keep temporary directories used to facilitate backup and restore after the process has been completed.
// These directories will be kept on disk and cleaned up periodically by cron.
// This setting is useful when debugging backup and restore problems.
// $CFG->keeptempdirectoriesonbackup = true;
//
// Produce zip files rather than gzipped tar files (.tgz)
// Changes the default archiving format for backups. Does not affect restore.
// $CFG->usezipbackups = true;
//

/***********************************************************************************************************************
Block and page settings
***********************************************************************************************************************/
//
// Set the default blocks that get added to all newly created courses.
// This forces the default blocks for all course formats. We recommend you set the default blocks per course
// format using the settings below this one.
// $CFG->defaultblocks_override = 'participants,activity_modules,search_forums,course_list:news_items,calendar_upcoming,recent_activity';
//
// Set the default blocks that get added to all newly created courses of a certain format.
// $CFG->defaultblocks_site = 'site_main_menu,course_list:course_summary,calendar_month';
// $CFG->defaultblocks_social = 'participants,search_forums,calendar_month,calendar_upcoming,social_activities,recent_activity,course_list';
// $CFG->defaultblocks_topics = 'participants,activity_modules,search_forums,course_list:news_items,calendar_upcoming,recent_activity';
// $CFG->defaultblocks_weeks = 'participants,activity_modules,search_forums,course_list:news_items,calendar_upcoming,recent_activity';
//
// Set the default blocks that get added to a newly created course of any other format.
// $CFG->defaultblocks = 'participants,activity_modules,search_forums,course_list:news_items,calendar_upcoming,recent_activity';
//

/***********************************************************************************************************************
JavaScript and CSS
***********************************************************************************************************************/
//
// Enable slash arguments when serving YUI JavaScript.
// This may improve caching and performance in some environments, although may require special rewrite rules in order to
// work around path length limitations in some systems. RewriteRule (^.*/theme/yui_combo\.php)(/.*) $1?file=$2
// $CFG->yuislasharguments = 1;
//

/***********************************************************************************************************************
Miscelaneous settings
 ***********************************************************************************************************************/
//
// Prevent modifications to scheduled tasks through the user interfaces.
// $CFG->preventscheduledtaskchanges = true;
//
// Define new, or redefine existing supported file types.
// Extension, icon and type must be specified. All other fields are optional.
// $CFG->customfiletypes = [
//     (object)[
//         'extension' => 'frog',
//         'icon' => 'archive',
//         'type' => 'application/frog',
//         'customdescription' => 'Amphibian-related file archive'
//     ]
// ];
//
//
// Force firstname and lastname of users when displaying them
// This will anonymise all user names for all users who do not hold the capability to view user details.
// $CFG->forcefirstname = 'John';
// $CFG->forcelastname  = 'Smith';

/***********************************************************************************************************************
Experimental settings
***********************************************************************************************************************/
//
// Introduce a URL rewriter class that can be used to in conjunction with an Apache or Nginx handler to rewrite outgoing
// urls as "clean" urls. The introduced class must implement \core\output\url_rewriter.
// The setting should be considered experimental as it may require other hacks in order to function as expected and may
// not be compatible with some features.
// $CFG->urlrewriteclass = '\local_cleanurls\url_rewriter';
//
//

/***********************************************************************************************************************
Deprecated settings
***********************************************************************************************************************/
//
// Disable the use of SVG images. Totara by default prefers SVG images. If you want to however disable the use of svg
// images then the following setting will allow you to achieve that. This setting is deprecated as some images are now
// only available in SVG. Deprecated in Totara 13.
// $CFG->svgicons = false;
//
// Totara 12 introduced a new site administration menu and removed the site administration branch in the administration
// block. If you want it back the following setting can be used. This setting has been deprecated as we do not intent to
// keep the old behaviour indefinitely. Deprecated in Totara 13.
// $CFG->legacyadminsettingsmenu = true;
//
// Restore course completion behaviour to that in Totara 2.7 for completed but failed activities. In Totara 2.7 and
// below a completed but failed activity completion does not count towards course completion. This behaviour chaned in
// Totara 2.9, and any activity completion (passed, failed, or not-specified) now counts towards course completion.
// This setting must be set BEFORE upgrading to Totara 2.9.0 or above. Deprecated in Totara 13.
// $CFG->completionexcludefailures = 1;
//
//

/***********************************************************************************************************************
Dangerous advanced settings
***********************************************************************************************************************/
//
// Theses settings are not recommended. Do not set these unless there is no other solution.
//
// Allow usernames to contain an extended set of characters.
// $CFG->extendedusernamechars = true;
//
// Export user passwords when backing up courses including users and user data.
// This setting is extremely dangerous.
// $CFG->includeuserpasswordsinbackup = true;
//
// Force restore to perform lighter user matching when restoring a backup produced in Totara 1.1 or below.
// Internally this causes Totara to treat the backup as though it is coming from a different site, meaning that user
// matching will be lighter, and more likely to match despite site differences.
// This is only useful for backups produced in very old versions of Totara 1.1 and below.
// $CFG->forcedifferentsitecheckingusersonrestore = true;
//
// Override the moodle_page class that is used to produce pages, and is made available as $PAGE.
// $CFG->moodlepageclass = 'moodle_page';
// $CFG->moodlepageclassfile = "$CFG->dirroot/local/myplugin/mypageclass.php";
//
// Override the block manager class that is used by the page to view and manage blocks.
// $CFG->blockmanagerclass = 'block_manager';
// $CFG->blockmanagerclassfile = "$CFG->dirroot/local/myplugin/myblockamanagerclass.php";
//
// Expose user information to Apache logs
// When enabled the configured user information will be exposed to Apache as "TOTARAUSER" and can be included in logs.
// This may expose otherwise secure information about your users. Use with caution.
// $CFG->apacheloguser = 0; // Off (default)
// $CFG->apacheloguser = 1; // Log user id.
// $CFG->apacheloguser = 2; // Log full name in cleaned format. ie, John Smith will be displayed as john_smith.
// $CFG->apacheloguser = 3; // Log username.
// In addition to setting this in your config.php you will also need to configure Apache to include the information in
// your log output, for example:
//   LogFormat "%h %l %{TOTARAUSER}n %t \"%r\" %s %b \"%{Referer}i\" \"%{User-Agent}i\"" totara_log_format
//   CustomLog "/your/path/to/log" totara_log_format
//
// Expose user information in HTTP header
// When enabled a header [X-TOTARAUSER] will be added to each page that includes the configured user information.
// This header is ideally stripped from the request by the web server and used in log output so that it never makes it
// to the end user. This may expose otherwise secure information about your users. Use with caution.
// $CFG->headerloguser = 0; // Off (default)
// $CFG->headerloguser = 1; // Log user id.
// $CFG->headerloguser = 2; // Log full name in cleaned format. ie, John Smith will be displayed as john_smith.
// $CFG->headerloguser = 3; // Log username.
//
// Disable file locking.
// Not all file systems support file locking. Some variations of NFS for example do not support it. Totara relies upon
// file locking to avoid race conditions and file state quality, particularly when under load. This setting should not
// be used on production sites.
// $CFG->preventfilelocking = false;
//
// Prevent executable paths from being changed in the user interface. When enabled this introduces a security risk.
// By default Totara requires paths to executable files required by the application to be specified in the config.php.
// When enabled this setting allows them to be changed by an administrator through the user interface.
// $CFG->preventexecpath = true;
//
// Disable CSRF protection on the login page.
// This is strongly discouraged due to its security implications. It may be useful when trying to get third party SSO
// authentication plugins working or if the deprecated alternative login URL setting is being used.
// $CFG->allowlogincsrf = 1;

// All done!
