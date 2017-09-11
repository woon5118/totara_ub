Description of HTML Purifier v4.8.0 library import into Moodle

* Make new (or delete contents of) /lib/htmlpurifier/
* Copy everything from /library/ folder to /lib/htmlpurifier/
* Copy CREDITS, LICENSE from root folder to /lib/htmlpurifier/
* Delete unused files:
    HTMLPurifier.auto.php
    HTMLPurifier.func.php
    HTMLPurifier.kses.php
    HTMLPurifier.autoload.php
    HTMLPurifier.composer.php
    HTMLPurifier.includes.php
    HTMLPurifier.path.php
* add locallib.php with Moodle specific extensions to /lib/htmlpurifier/
* add this readme_moodle.txt to /lib/htmlpurifier/
* TL-15981 Fix use of PHP 7.2 deprecated default parameter in idn_to_utf8() and idn_to_ascii(), use INTL_IDNA_VARIANT_UTS46

