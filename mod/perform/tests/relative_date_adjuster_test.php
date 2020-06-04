<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2020 onwards Totara Learning Solutions LTD
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
 * @author Jaron Steenson <jaron.steenson@totaralearning.com>
 * @package mod_perform
 * @category test
 */

use mod_perform\dates\relative_date_adjuster;
use mod_perform\dates\schedule_constants;

defined('MOODLE_INTERNAL') || die();

/**
 * @group perform
 */
class mod_perform_relative_date_adjuster_testcase extends advanced_testcase {

    /**
     * @dataProvider before_adjustments_provider
     * @param int $count
     * @param string $unit
     * @param int $expected
     */
    public function test_before_adjustments(int $count, string $unit, int $expected): void {
        $actual = (new relative_date_adjuster())->adjust(
            $count,
            $unit,
            schedule_constants::BEFORE,
            $this->get_reference_timestamp()
        );

        self::assertEquals($expected, $actual);
    }

    public function before_adjustments_provider(): array {
        return [
            '0 days' => [0, schedule_constants::DAY, $this->get_reference_timestamp()],
            '0 weeks' => [0, schedule_constants::WEEK, $this->get_reference_timestamp()],

            '7 days' => [7, schedule_constants::DAY, $this->get_one_week_earlier()],
            '1 week' => [1, schedule_constants::WEEK, $this->get_one_week_earlier()],
        ];
    }

    /**
     * @dataProvider after_adjustments_provider
     * @param int $count
     * @param string $unit
     * @param int $expected
     */
    public function test_after_adjustments(int $count, string $unit, int $expected): void {
        $actual = (new relative_date_adjuster())->adjust(
            $count,
            $unit,
            schedule_constants::AFTER,
            $this->get_reference_timestamp()
        );

        self::assertEquals($expected, $actual);
    }

    public function after_adjustments_provider(): array {
        return [
            '0 days' => [0, schedule_constants::DAY, $this->get_reference_timestamp()],
            '0 weeks' => [0, schedule_constants::WEEK, $this->get_reference_timestamp()],

            '7 days' => [7, schedule_constants::DAY, $this->get_one_week_later()],
            '1 week' => [1, schedule_constants::WEEK, $this->get_one_week_later()],
        ];
    }

    public function test_time_is_not_adjusted(): void {
        $reference_timestamp = (new DateTimeImmutable('2020-05-20 15:00:00', new DateTimeZone('Pacific/Auckland')))
            ->getTimestamp();

        $actual_timestamp = (new relative_date_adjuster())->adjust(
            1,
            schedule_constants::DAY,
            schedule_constants::AFTER,
            $reference_timestamp
        );

        $actual = (new DateTimeImmutable('@' . $actual_timestamp))->setTimezone(new DateTimeZone('Pacific/Auckland'));

        $actual_formatted = $actual->format('Y-m-d H:i:s');
        $actual_timestamp = $actual->getTimestamp();

        self::assertEquals('2020-05-21 15:00:00', $actual_formatted);
        self::assertEquals('1590030000', $actual_timestamp);
    }

    protected function get_reference_timestamp(): int {
        return 1589932800; // (utc) Wednesday, 20 May 2020 00:00:00
    }

    protected function get_one_week_earlier(): int {
        return 1589328000; // (utc) Wednesday, 13 May 2020 00:00:00
    }

    protected function get_one_week_later(): int {
        return 1590537600; // (utc) Wednesday, 27 May 2020 00:00:00
    }

}