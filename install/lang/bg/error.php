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

$string['cannotcreatedboninstall'] = '<p>Не може да се създаде базата данни.</p>
<p>Посочената база данни не съществува и посоченият потребител няма разрешение да създаде базата данни.</p>
<p>Администраторът на сайта трябва да провери конфигурацията на базата данни.</p>';
$string['cannotcreatelangdir'] = 'Не може да се създаде езикова директория';
$string['cannotcreatetempdir'] = 'Не може да създаде временна директория';
$string['cannotdownloadcomponents'] = 'Компонентите не може да се изтеглят';
$string['cannotdownloadzipfile'] = 'ZIP файлът не може да се изтегли';
$string['cannotfindcomponent'] = 'Не можа да намери компонент';
$string['cannotsavemd5file'] = 'Md5 файлът не може да се запише';
$string['cannotsavezipfile'] = 'ZIP файлът не може да се запише';
$string['cannotunzipfile'] = 'ZIP файлът не може да се разархивира';
$string['componentisuptodate'] = 'Компонентът е актуален';
$string['dmlexceptiononinstall'] = '<p>Възникна грешка в базата данни[{$a->errorcode}].<br />{$a->debuginfo}</p>';
$string['downloadedfilecheckfailed'] = 'Проверката на изтегления файл е неуспешна';
$string['invalidmd5'] = 'Контролната променлива е грешна – опитайте отново';
$string['missingrequiredfield'] = 'Липсва задължително поле';
$string['remotedownloaderror'] = 'Изтеглянето на компонента към вашия сървър пропадна, проверете настройките на proxy, препоръчително е PHP разширението cURL.<br /><br />Вие трябва ръчно да изтеглите файла <a href="{$a->url}">{$a->url}</a>, да го копирате в директория {$a->dest} на вашия сървър и да го разархивирате там.';
$string['wrongdestpath'] = 'Неправилен път до дестинация';
$string['wrongsourcebase'] = 'Неправилна основа на URL адресите';
$string['wrongzipfilename'] = 'Неправилно име на ZIP файла';