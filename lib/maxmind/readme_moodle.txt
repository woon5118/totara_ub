GeoIP2 PHP API
==============

No changes from the upstream version have been made, it is recommended by upstream
to install these depdencies via composer - but the composer installation is bundled
with a load of test files, shell scripts etc (and we don't use composer to manage
'production depdendencies') so we have to do it manually.

Information
-----------

URL: http://maxmind.github.io/GeoIP2-php/
License: Apache License, Version 2.0.

Installation
------------

1) Download the latest versions of GeoIP2-php and MaxMind-DB-Reader-php
download release from https://github.com/maxmind/GeoIP2-php/releases
download release from https://github.com/maxmind/MaxMind-DB-Reader-php/releases

2) Unzip the archives

3) Move the source code directories into place
replace /lib/maxmind/GeoIp2/ with /src/
replace /lib/maxmind/MaxMind/ with /src/MaxMind/

4) Run unit tests on iplookup/tests/geoip_test.php with PHPUNIT_LONGTEST defined.

5) run: php totara/core/dev/fix_file_permissions.php --fix

6) update /lib/thirdpartylibs.xml
