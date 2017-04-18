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
 * This script prints basic CSS for the installer
 *
 * @package    core
 * @subpackage install
 * @copyright  2011 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (file_exists(__DIR__.'/../config.php')) {
    // already installed
    die;
}

$content = file_get_contents(__DIR__.'/../theme/basis/style/totara.css');

// TODO TL-14169 fix basis style and add more RTL support

$content .= <<<EOF

body {
    padding: 4px;
}

.text-ltr {
    direction: ltr !important;
}

.headermain {
    margin: 15px;
}

#installdiv {
    width: 800px;
    margin-left:auto;
    margin-right:auto;
    padding: 5px;
    margin-bottom: 15px;
}

#installdiv dt {
    font-weight: bold;
}

#installdiv dd {
    padding-bottom: 0.5em;
}

.stage {
    margin-top: 2em;
    margin-bottom: 2em;
    padding: 25px;
}

fieldset {
    text-align:center;
    border:none;
}

fieldset .configphp,
fieldset .alert {
    text-align: left;
    direction: ltr;
}

.sitelink {
    text-align: center;
}

.fitem {
    clear:both;
    text-align:left;
    padding: 8px;
}

.fitemtitle {
    float: left;
    width: 245px;
    text-align: right;
}
html[dir=rtl] .fitemtitle {
    float: right;
}
label {
    font-weight: bold;
    display: inline-block;
    margin: 5px;
}

.fitemelement {
    margin-left: 265px;
}
html[dir=rtl] .fitemelement {
    margin-right: 265px;
    margin-left: 0;
    direction: ltr;
}

.breadcrumb {
    background-color: #f5f5f5;
}
.breadcrumb {
    padding: 8px 15px;
    margin: 0 0 20px;
    list-style: none;
    background-color: #f5f5f5;
    -webkit-border-radius: 4px;
    -moz-border-radius: 4px;
    border-radius: 4px;
}

.breadcrumb > li {
    display: inline-block;
    text-shadow: 0 1px 0 #fff;
    line-height: 20px;
}

.breadcrumb {
    background-color: rgb(245, 245, 245);
    padding: 8px 15px;
}

EOF;

@header('Content-Disposition: inline; filename="css.php"');
@header('Cache-Control: no-store, no-cache, must-revalidate');
@header('Cache-Control: post-check=0, pre-check=0', false);
@header('Pragma: no-cache');
@header('Expires: Mon, 20 Aug 1969 09:23:00 GMT');
@header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
@header('Accept-Ranges: none');
@header('Content-Type: text/css; charset=utf-8');

echo $content;
