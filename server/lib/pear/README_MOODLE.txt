MOODLE-SPECIFIC PEAR MODIFICATIONS
==================================

Auth/RADIUS
===========

1/ Changed static call to correct alternative (MDL-38373):
    - From: PEAR::loadExtension('radius'); (in global scope)
    - To: $this->loadExtension('radius'); (in constructor)
2/ Upgraded to version 1.1.0 (see MDL-51523).
   Changes made to the lib/pear/Auth/RADIUS.php file that was downloaded.
    - Added "require_once('PEAR.php')".
    - Changed the 'Auth_RADIUS' class so that it extends the 'PEAR' class.
    - Changed the function 'loadExtension' to public.

XML/Parser
=================
1/ changed ereg_ to preg_
* http://cvs.moodle.org/moodle/lib/pear/XML/Parser.php.diff?r1=1.1&r2=1.2


Quickforms
==========
Full of our custom hacks, no way to upgrade to latest upstream.
Most probably we will stop using this library in the future.

MDL-20876 - replaced split() with explode() or preg_split() where appropriate
MDL-40267 - Moodle core_text strlen functions used for range rule rule to be utf8 safe.
MDL-46467 - $mform->hardfreeze causes labels to loose their for HTML attribute
MDL-52081 - made all constructors PHP7 compatible
MDL-52826 - Remove onsubmit events pointing to the global validation functions and script
            tag moved after the HTML
MDL-50484 - _getPersistantData() returns id with _persistant prefixed to element id.
MDL-55123 - corrected call to non-static functions in HTML_QuickForm to be PHP7.1-compliant
TL-14971 - replaced deprecated create_function() that was abused to do eval


Pear
====
Changed constructors in classes PEAR and PEAR_ERROR to be __construct(). This has
been already changed upstream in 1.10.0, remove this line after upgrade.


Crypt/CHAP
==========
MDL-52285 - made all constructors PHP7 compatible


Other changes:
 * TL-23374 lib: fix pear compatibility with PHP 7.4
