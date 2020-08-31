# Totara Talent Experience Platform

Table of contents:

* [Introduction](#introduction)
* [Licensing](#licensing)
* [System requirements](#system-requirements)
* [Browser requirements](#browser-requirements)
* [Installation](#installation)
* [Upgrading](#upgrading)
* [Development](#development)
* [Support](#support)

<a name="introduction" />

## Introduction

The Totara Talent Experience Platform (TXP) comprises three powerful solutions to help you build a better workplace, 
increase resilience and prosper in today’s fast-changing world. 

1. **Totara Learn**, the flexible LMS trusted by millions of learners and favoured by companies worldwide to deliver 
   transformational learning, and now even more powerful and adaptable to the unique needs of your organization.
   
2. **Totara Engage**, the new Learning Experience Platform (LXP) is built to engage, unite and upskill your workforce. 
   Totara Engage empowers employees to simplify complex knowledge sharing with collaborative workspaces and Microsoft 
   Teams integration, to deliver higher employee engagement.
   
3. **Totara Perform**, with flexibility, organizational control and continuous performance management at its core, our 
   new performance management system enables the modern enterprise to deliver peak productivity. Evidence-based 
   performance reviews allow for objective performance measurement to boost workplace productivity, anytime, anywhere.

Together, they empower you with a Talent Experience Platform to unlock your organization’s full potential and ensures 
that your people Learn, Engage and Perform at their absolute best.

<a name="licensing" />

## Licensing

For information on product licensing please see the [licensing readme](readme_licensing.md)

<a name="system-requirements" />

## System requirements

- Operating system
    - Recommended: CentOS, Red Hat, or Ubuntu
    - Debian, OSX, or any unix based operating system should be compatible.
    - Windows Server    
      _It should be noted that Microsoft have announced that they are discontinuing their support for future PHP versions in Windows from PHP 8.0_
      [ref](https://news-web.php.net/php.internals/110907 "PHP Internal mailing list post").
- Web server
    - Apache 2.4.x
    - Apache 2.2.x
    - IIS 8.x
- Domain name that resolves to your web server  
  _It is strongly recommended to use SSL/TLS_
- Database
    - PostgreSQL
        - Recommended: 12.x
        - Supported: 12.x, 11.x, 10.x, 9.6.x
        - Not supported: major releases greater than 12, 9.5 and lower.
    - MariaDB
        - Recommended: 10.5.x
        - Supported: 10.5.x, 10.4.x, 10.3.x, 10.2.6+
        - Not supported: major releases greater than 10.5, 10.2.5 or lower, 10.1 and lower.
    - MySQL
        - Recommended: 8.0.x
        - Supported: 8.0.x, 5.7.21+
        - Not supported: major releases greater than 8.0, 5.7.20 or lower, 5.6.x and lower. 
    - MSSQL
        - Recommended: 15.0 (2019) 
        - Supported: 14.0 (2017) (minimum)
        - Not supported: 13 (2016) and lower.
- PHP
    - Recommended: 7.4.x
    - Supported: 7.4.x, 7.3.x, 7.2.10+
    - Not supported: 7.2.9 or lower
- Required PHP extensions
    - curl
    - ctype
    - dom 
    - gd
    - iconv
    - intl
    - json
    - mbstring
    - pcre
    - simplexml
    - spl
    - xml
    - zip
    - zlib
- Recommended PHP extensions  
  These extensions are not strictly required, however when available to Totara they will be used.
    - opcache
    - openssl
    - soap
    - tokenizer
    - xmlrpc
    
### Required database configuration

*PostgreSQL*
- To create your database: createdb -E utf8 {dbname}
- For security we recommend you use a dedicated database user who has access just to the Totara database.

*MariaDB*
- Barracuda file format 
- To create your database: CREATE DATABASE {dbname} DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
- For security we recommend you use a dedicated database user who has access just to the Totara database.

*MySQL*
- Barracuda file format
- To create your database: CREATE DATABASE {dbname} DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
- For security we recommend you use a dedicated database user who has access just to the Totara database.

*MSSQL*
- Your database user must have permission to alter server settings  
  `ALTER SETTINGS(SERVER)`
- Full text search must be enabled; see [Full text search in MSSQL Server](https://help.totaralearning.com/display/latest/Full+Text+Search+in+MS+SQL+Server)
- For security, we recommend you use a dedicated database user who has access just to the Totara database.

### Required server configuration

If you are planning to use Totara in multiple user languages, you should ensure that the server is properly configured with the correct locales.
This is important to ensure that date, number and currency data is displayed correctly for each language.

### Required PHP configuration

The following settings should be adjusted in your php.ini file:

- *memory_limit* This will need to be increased for restoring large course backups or unzipping large files within Totara
- *post_max_size* Ensure that this is larger than the largest file that you expect your users to upload.
- *upload_max_filesize* - This setting in conjuction with "post_max_size" will determine how high you can set the max upload size within Totara
- *max_input_vars=10000* - Increasing this setting is recommended as operations such as modifying languages, or large gradebooks may hit the default limit of 1000. Setting to 10000 does not introduce any performance overheads.
- *upload_tmp_dir* - Some customers may wish to enable this setting and specifically set a directory where files are temporarily stored during the upload process. Note the web server user must have permissions to write files in this directory, or all file uploads will fail.
- *opcache.enable=1* If the opcache extension is installed then enabling opcache is recommended for performance reasons. This is enabled by default in modern versions of PHP.

For more information on changing these settings see [PHP documentation](http://php.net/manual/en/ini.core.php).
After changing any settings in php.ini you will need to restart your web server, and/or PHP-FPM if you are using that.

<a name="browser-requirements" />

## Browser requirements

The following browsers are fully tested and supported.
All browsers must have JavaScript enabled, and allow cookies.

### Desktop browsers

- Chrome latest versions
- Firefox latest versions
- Safari 11.3+
- Microsoft Chromium Edge latest versions
- Microsoft Edge 16 through 18
- Internet Explorer 11

### Mobile browsers

- Google Chrome latest versions 
- Safari on iOS 11+.

<a name="installation" />

## Installation

The following is a quick installation overview. 

1. Create a database for Totara to use.  
   Totara supports PostgreSQL, MariaDB, MySQL, and MSSQL. If you are unsure which to choose we recommend PostgreSQL as it
   delivers good performance out of the box without requiring tuning.  
   Totara needs to be provided with a username and password to use when connecting to the database. We recommend settings 
   up a dedicated user for Totara to use.
   
2. Create a directory in which Totara will store files.  
   User files, cache files, and temporary files will be stored in this directory. If you are horizontally scaling your
   web architecture then it is important to note that this directory MUST be shared between all nodes.
   The data directory must be writable by the web server user.
   The data directory must not be web accessible.
   
3. Configure your web server.  
   In order to access Totara you require a web server, and a domain on which Totara will be accessed. The web server needs
   to be configured so that the document root for your domain points to the "server" directory within the code base.
   The server directory contains web accessible code. The top level directory for Totara must not be web accessible.
   
4. Install Totara.  
   This can be done in a number of ways. If this is your first time doing this then we recommend installing through
   the web interface. Once your web server has been configured simply open your web browser, and enter your sites URL.
   You will be taken to the installation process. There are two things that will happen. The first is that you will be 
   prompted to enter required information including the database details, and directory for files. Once that information 
   has been gathered a config.php will be generated and put into the top level Totara directory. If the web server does
   not have write access to that directory (and it shouldn't) then you will be prompted to create this yourself by
   copying the details from the browser to the file.  
   Once the config.php file exists the installation will continue to create the database structure and essential data,
   and prepare the site data directory.
   
5. Configure cron.  
   Totara offloads heavy processing to cron. The likes of email, bulk enrolments, data imports all occur on cron. It is
   essential to have cron configured and running on your site. How you do this depends upon your environment, however
   you must configure cron to call **server/admin/cli/cron.php** on a regular basis. We have designed it to be called
   once a minute, however you are free to choose a schedule that works for you. Greater time between runs will lead to
   users having to wait for actions to be processed.
  

For a set of complete instructions please see the help documentation on [installing Totara](https://help.totaralearning.com/display/latest/Installing+Totara) 

<a name="upgrading" />

## Upgrading

The following steps should be followed when upgrading any Totara site, to any newer release.
For detailed instructions see [upgrading to Totara](https://help.totaralearning.com/display/latest/Upgrading+to+Totara)

1.  Check the live logs to check if any users are currently using the site.
    The site will be offline while the upgrades are performed.
    Reports -> Live Logs

2.  Enable maintenance mode in Totara.
    Server -> Maintenance Mode

3.  Backup the Totara database.

4.  Backup the site data directory.

5.  Backup the Totara source code directory.

6.  Remove the old source code, and extract the new source code into the source code directory.
    Note: Do not copy the new code on top of the existing code folder.
    If optional libraries were installed before then use composer to reinstall updated dependencies
    in /libraries/ directory.

7.  Navigate to the admin/index.php page. The upgrade process will
    automatically start.

8.  Check for Warnings or Errors on the upgrade review page.

9.  Edit new settings page if required.

10. Disable server maintenance mode.

11. Congratulations, your site is now upgraded. Read changelog.md for details on what is new.

### Totara 13

Totara 13 brings with it a number of significant changes. For information on these changes please see our 
[developer documentation](https://help.totaralearning.com/display/DEV/Totara+13+notable+technical+changes)

Most notably, as a partner you will now need to set the flavour that the site is using to ensure that the correct
functionality is available to the site.
This can be done by defining the following in your config.php:
```php
$CFG->forceflavour = 'learn';
```

The following are valid flavours:
* learn_perform_engage
* learn_perform
* learn_engage
* perform_engage
* learn
* perform
* engage

This should be set between steps 6 and 7 above.

<a name="development" />

## Development

For information on developing for Totara see [readme_development](readme_development.md)

<a name="support" />

## Support

For subscribers, please get in touch with your partner.
If you are a direct subscriber, or a partner, and require support, please contact us through [totara.support](http://totara.support)

Further information and help can be found at the following:
* [Help documentation](https://help.totaralearning.com/display/latest/)
* [Developer documentation](https://help.totaralearning.com/display/DEV/)
* [Community](https://totara.community/)
* [Academy](https://totara.academy/)