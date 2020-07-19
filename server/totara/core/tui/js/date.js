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
 * @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
 * @package totara_core
 */

import { langString } from './i18n';

/**
 * View date-fns docs for additional date helpers
 * https://date-fns.org/v2.13.0/docs/
 * The format methods would require additional lang support
 */
import {
  formatISO,
  getDate,
  getDaysInMonth,
  getMonth,
  getYear,
  isAfter,
  isBefore,
  isExists,
  parseISO,
} from 'date-fns';

/**
 * Get days of month array for select
 *
 * @param {number} month
 * @param {number} year
 * @returns {array}
 */
export function getDaysInMonthSelectArray(month, year) {
  let daysInMonth = month ? getDaysInMonth(new Date(year || 0, month)) : 31;
  let days = [];

  for (let i = 1; i <= daysInMonth; i++) {
    days.push({
      id: i,
      label: i,
    });
  }

  return days;
}

/**
 * Return array of month strings.
 *
 * @returns {array}
 */
export function getMonthStringsSelectArray() {
  const months = [
    {
      id: 0,
      label: langString('month_january', 'totara_core'),
      labelShort: langString('month_january_short', 'totara_core'),
    },
    {
      id: 1,
      label: langString('month_february', 'totara_core'),
      labelShort: langString('month_february_short', 'totara_core'),
    },
    {
      id: 2,
      label: langString('month_march', 'totara_core'),
      labelShort: langString('month_march_short', 'totara_core'),
    },
    {
      id: 3,
      label: langString('month_april', 'totara_core'),
      labelShort: langString('month_april_short', 'totara_core'),
    },
    {
      id: 4,
      label: langString('month_may', 'totara_core'),
      labelShort: langString('month_may_short', 'totara_core'),
    },
    {
      id: 5,
      label: langString('month_june', 'totara_core'),
      labelShort: langString('month_june_short', 'totara_core'),
    },
    {
      id: 6,
      label: langString('month_july', 'totara_core'),
      labelShort: langString('month_july_short', 'totara_core'),
    },
    {
      id: 7,
      label: langString('month_august', 'totara_core'),
      labelShort: langString('month_august_short', 'totara_core'),
    },
    {
      id: 8,
      label: langString('month_september', 'totara_core'),
      labelShort: langString('month_september_short', 'totara_core'),
    },
    {
      id: 9,
      label: langString('month_october', 'totara_core'),
      labelShort: langString('month_october_short', 'totara_core'),
    },
    {
      id: 10,
      label: langString('month_november', 'totara_core'),
      labelShort: langString('month_november_short', 'totara_core'),
    },
    {
      id: 11,
      label: langString('month_december', 'totara_core'),
      labelShort: langString('month_december_short', 'totara_core'),
    },
  ];
  return months;
}

/**
 * Get array for select input range of year
 *
 * @param {number} year mid range year (defaults to current)
 * @param {number} yearsBefore years before mid range (defaults to 50)
 * @param {number} yearsAfter years after mid range (defaults to 50)
 * @returns {array}
 */
export function getYearsSelectArray(year, yearsBefore, yearsAfter) {
  let years = [];

  // Provides Defaults for year, years before & years after
  year = year || getYear(new Date());
  yearsAfter = Number.isInteger(yearsAfter) ? yearsAfter : 50;
  yearsBefore = Number.isInteger(yearsBefore) ? yearsBefore : 50;

  for (let i = year - yearsBefore; i <= year + yearsAfter; i++) {
    years.push({
      id: i,
      label: i,
    });
  }

  return years;
}

/**
 * Get current date values
 *
 * @returns {object}
 */
export function getCurrentDateValues() {
  let date = new Date();

  return {
    day: getDate(date),
    month: getMonth(date),
    year: getYear(date),
  };
}

/**
 * Get date values from ISO
 *
 * @param {iso} iso
 * @returns {object}
 */
export function getValuesFromIso(iso) {
  let date = parseISO(iso);

  // Check if date is valid
  if (!(date instanceof Date) || isNaN(date)) {
    return;
  }

  return {
    day: getDate(date),
    month: getMonth(date),
    year: getYear(date),
  };
}

/**
 * Get ISO 8601 standard string
 *
 * @param {object} data {day: Int, month: Int, year: Int}
 * @param {boolean} dateOnly only return date ISO
 * @returns {string}
 */
export function getIsoFromValues(data, dateOnly) {
  if (!isExists(data.year, +data.month, data.day)) {
    return false;
  }

  let date = new Date(data.year, +data.month, data.day);
  return formatISO(date, { representation: dateOnly ? 'date' : 'complete' });
}

/**
 * Get user local time zone key
 * Not supported in IE
 *
 * @returns {string}
 */
export function getLocalTimeZone() {
  return Intl.DateTimeFormat().resolvedOptions().timeZone;
}

/**
 * Check if a ISO is after another
 *
 * @param {iso} date provided date
 * @param {iso} baseDate the date to compare against
 * @returns {boolean}
 */
export function isIsoAfter(date, baseDate) {
  // If date values not passed or are equal
  if (!date || !baseDate || date === baseDate) {
    return true;
  }

  return isAfter(parseISO(date), parseISO(baseDate));
}

/**
 * Check a ISO is before another
 *
 * @param {iso} date provided date
 * @param {iso} baseDate the date to compare against
 * @returns {boolean}
 */
export function isIsoBefore(date, baseDate) {
  // If date values not passed or are equal
  if (!date || !baseDate || date === baseDate) {
    return true;
  }

  return isBefore(parseISO(date), parseISO(baseDate));
}
