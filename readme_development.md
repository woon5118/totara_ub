# Developing for Totara

This document provides a quick developer overview. 
For detailed information and more extensive options please see our (developer documentation)[https://help.totaralearning.com/display/DEV/]

## General setup

### Site registration

Tell the registration system we're a development install.
```bash
$CFG->sitetype = 'development';
```

### Enabling debugging

```bash
$CFG->debug = E_ALL;
$CFG->debugpageinfo = 1;
$CFG->perfdebug = 15;
$CFG->debugdisplay = 1;
```

### Useful settings

```php
$CFG->passwordpolicy = false;
$CFG->allowuserthemes = 1;
$CFG->allowcoursethemes = 1;
$CFG->allowthemechangeonurl = 1;
$CFG->noemailever = 1;
```

## Build processes

### Building Tui

Tui requires a build process to bundle src files into build files.
The following command will build both production and development builds.

```bash
npm install
npm run tui-build
```

For more information on the Tui build process see our (Build process developer documentation)[https://help.totaralearning.com/display/DEV/The+build+process]

### Building AMD and less themes

AMD and less based themes also still require a build process. This remains in the server directory, and operates as it 
did previously.

```bash
cd server
php composer.phar install
npm install
./node_modules/grunt/bin/grunt
```

For more information on the AMD and less themes build process see our (developer documentation)[https://help.totaralearning.com/display/DEV/Working+With+LESS+in+Themes]

## Automated testing

Totara uses PHPUnit for unit and integration testing (predominantly integration testing), and Behat for acceptance testing.

### Running PHPUnit

The following is a quick overview. For further information see our (Unit testing developer documentation)[https://help.totaralearning.com/display/DEV/Unit+testing].

- Quick overview of required config.php settings
```php
$CFG->phpunit_dataroot = $CFG->dataroot . '_phpunit';
$CFG->phpunit_prefix = 'p_';
$CFG->phpunit_dboptions = array_merge($CFG->dboptions, array(
    'dbschema' => 'phpunit'
));
```
- Quick copy + paste commands

### Running Behat

The following is a quick overview. For further information see our (Acceptance testing developer documentation)[https://help.totaralearning.com/display/DEV/Acceptance+testing].
 
- Quick overview of required config.php settings
```php
$CFG->behat_dataroot = $CFG->dataroot . '_behat';
$CFG->behat_prefix = 'b_';
$CFG->behat_faildump_path = '/tmp';
$CFG->behat_dboptions = array_merge($CFG->dboptions, array(
    'dbschema' => 'behat'
));
```
- Quick copy paste commands
