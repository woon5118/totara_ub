# Readme

This file will be rewritten in the near future. Before release.
Until then here go a few helpful things to get you started.

## Putting Tui into developer mode

* Log in as an admin
* Navigate to "Admin settings > Appearance"
* Navigate to "Totara TUI frontend framework"
* Turn on developer mode
* Optionally turn off caching
(the two are separate settings so that support can ask folk to turn on devmode without killing performance)

## Generating Tui builds

```bash
npm install
npm run tui-build
```

## Generating AMD/Less builds

```bash
cd server
php composer.phar install
npm install
./node_modules/grunt/bin/grunt
```

## Running PHPUnit

There are two approaches to this, both covered as it'll probably help you understand what is going on.

```bash
php test/phpunit/phpunit.php init
php test/phpunit/phpunit.php run 
```

or

```bash
cd test/phpunit && php /path/to/composer.phar install
php ../../server/admin/tool/phpunit/cli/init.php
php ../../server/admin/tool/phpunit/cli/run.php
```

## Running Behat

There are two approaches to this, both covered as it'll probably help you understand what is going on.

```bash
php test/behat/behat.php init
php test/behat/behat.php run 
```

or

```bash
cd test/behat && php /path/to/composer.phar install
php ../../server/admin/tool/behat/cli/init.php
php ../../server/admin/tool/behat/cli/run.php
```


### 4. Optionals

#### Generate a jsconfig.json file for use with the VSCode Vue extension
```bash
npm run generate-configs
```

### 5. Install Totara
Just like before, except the server directory will appear in your path OR you will have updated your web server configuration to point the base directory at the server directory!