<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2016 onwards Totara Learning Solutions LTD
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Brendan Cox <brendan.cox@totaralearning.com>
 * @package totara_core
 */

/**
 * This assists with autoloading when a class or its namespace has been renamed.
 * See lib/db/renamedclasses.php for further information on this type of file.
 */

defined('MOODLE_INTERNAL') || die();

// The old class name is the key, the new class name is the value.
// The array must be called $renamedclasses.
$renamedclasses = array(
    'totara_core\task\update_temporary_managers_task' => 'totara_job\task\update_temporary_managers_task',
    'totara_core\formatter\formatter' => core\webapi\formatter\formatter::class,
    'totara_core\formatter\field\base' => core\webapi\formatter\field\base::class,
    'totara_core\formatter\field\date_field_formatter' => core\webapi\formatter\field\date_field_formatter::class,
    'totara_core\formatter\field\field_formatter_interface' => core\webapi\formatter\field\field_formatter_interface::class,
    'totara_core\formatter\field\string_field_formatter' => core\webapi\formatter\field\string_field_formatter::class,
    'totara_core\formatter\field\text_field_formatter' => core\webapi\formatter\field\text_field_formatter::class,
    'totara_core\entities\relationship_resolver' => totara_core\entity\relationship_resolver::class,
    'totara_core\entities\relationship' => totara_core\entity\relationship::class,
);

// Add the old Tui component class, just while we reorganise.
$renamedclasses['totara_core\output\tui_component'] = \totara_tui\output\component::class;

// Only add these to the map if we're within PHPUnit.
// They should never be used outside of PHPUnit.
if (defined('PHPUNIT_TEST') && PHPUNIT_TEST) {
    $renamedclasses += [
        'PHPUnit_TextUI_Command' => \PHPUnit\TextUI\Command::class,
        'PHPUnit_TextUI_ResultPrinter' => \PHPUnit\TextUI\ResultPrinter::class,
        'PHPUnit_Framework_Exception' => \PHPUnit\Framework\Exception::class,
        'PHPUnit_Framework_TestCase' => \PHPUnit\Framework\TestCase::class,
        'PHPUnit_Framework_Constraint_IsEqual' => \PHPUnit\Framework\Constraint\IsEqual::class,
        'PHPUnit_Framework_Assert' => \PHPUnit\Framework\Assert::class,
        'PHPUnit_Framework_TestFailure' => \PHPUnit\Framework\TestFailure::class,
        'PHPUnit_Framework_MockObject_MockObject' => \PHPUnit\Framework\MockObject\MockObject::class,
        'PHPUnit_Framework_Error_Warning' => \PHPUnit\Framework\Error\Warning::class,
        'PHPUnit_Framework_Error_Notice' => \PHPUnit\Framework\Error\Notice::class,
        'PHPUnit_Framework_ExpectationFailedException' => \PHPUnit\Framework\ExpectationFailedException::class,
        'PHPUnit_Util_Fileloader' => \PHPUnit\Util\Fileloader::class,
        'PHPUnit_Util_Configuration' => \PHPUnit\Util\Configuration::class,
    ];
}
