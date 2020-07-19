<!--
  This file is part of Totara Enterprise Extensions.

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

  Totara Enterprise Extensions is provided only to Totara
  Learning Solutions LTD’s customers and partners, pursuant to
  the terms and conditions of a separate agreement with Totara
  Learning Solutions LTD or its affiliate.

  If you do not have an agreement with Totara Learning Solutions
  LTD, you may not access, use, modify, or distribute this software.
  Please contact [licensing@totaralearning.com] for more information.

  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @module totara_core
-->

<template>
  <div class="tui-dateSelector">
    <div class="tui-dateSelector__date">
      <div class="tui-dateSelector__date-day">
        <Label
          :for-id="$id('event-date-day')"
          :label="$str('day', 'totara_core')"
          :hidden="true"
        />
        <Select
          :id="$id('event-date-day')"
          v-model="day"
          :disabled="disabled"
          :options="dayRange"
          @input="update"
        />
      </div>

      <div class="tui-dateSelector__date-month">
        <Label
          :for-id="$id('event-date-month')"
          :label="$str('month', 'totara_core')"
          :hidden="true"
        />
        <Select
          :id="$id('event-date-month')"
          v-model="month"
          :disabled="disabled"
          :options="optionsMonths"
          @input="update"
        />
      </div>

      <div class="tui-dateSelector__date-year">
        <Label
          :for-id="$id('event-date-year')"
          :label="$str('year', 'totara_core')"
          :hidden="true"
        />
        <Select
          :id="$id('event-date-year')"
          v-model="year"
          :disabled="disabled"
          :options="yearRange"
          @input="update"
        />
      </div>
    </div>

    <div v-if="hasTimezone" class="tui-dateSelector__time">
      <Label
        :for-id="$id('event-date-time-zone')"
        :label="$str('time_zone', 'totara_core')"
        :hidden="true"
      />
      <Select
        :id="$id('event-date-time-zone')"
        v-model="timezone"
        :disabled="disabled"
        :options="optionsTimezones"
        @input="update"
      />
    </div>
  </div>
</template>

<script>
// Components
import Label from 'totara_core/components/form/Label';
import Select from 'totara_core/components/form/Select';

// Utils
import {
  getCurrentDateValues,
  getDaysInMonthSelectArray,
  getIsoFromValues,
  getMonthStringsSelectArray,
  getValuesFromIso,
  getYearsSelectArray,
} from 'totara_core/date';
import { getTimeZoneKeyStrings } from 'totara_core/time';
import { globalConfig } from 'totara_core/config';
import { loadLangStrings } from 'totara_core/i18n';

export default {
  components: {
    Label,
    Select,
  },

  props: {
    initialCurrentDate: Boolean,
    initialCustomDate: [Boolean, String],
    initialTimezone: [Boolean, String],
    disabled: Boolean,
    hasTimezone: Boolean,
    type: {
      default: 'date',
      type: String,
      validator: x => ['date', 'dateTime'].includes(x),
    },
    value: Object,
    yearsMidrange: Number,
    yearsBeforeMidrange: Number,
    yearsAfterMidrange: Number,
  },

  data() {
    return {
      day: '',
      month: '',
      year: '',
      timezone: '',
      optionsMonths: [],
      optionsTimezones: [],
    };
  },

  computed: {
    date() {
      let date = {
        day: this.day,
        month: this.month,
        year: this.year,
      };

      // Get ISO for provided date values
      let iso = {
        iso: getIsoFromValues(date, this.type === 'date'),
      };

      if (this.hasTimezone) {
        iso = Object.assign(iso, { timezone: this.timezone });
      }

      return iso;
    },

    /**
     * Get available days of month
     *
     */
    dayRange() {
      let days = getDaysInMonthSelectArray(this.month, this.year);

      // Check any existing day value is within range
      this.isDayWithinRange(days);
      return days;
    },

    /**
     * Get available years range
     *
     */
    yearRange() {
      let years = getYearsSelectArray(
        this.yearsMidrange,
        this.yearsBeforeMidrange,
        this.yearsAfterMidrange
      );

      // Check any existing year value is within range
      this.isYearWithinRange(years);
      return years;
    },
  },

  watch: {
    /**
     * Updates date to value
     */
    value: function() {
      if (this.value && this.value.iso) {
        const date = getValuesFromIso(this.value.iso);
        this.setDate(date);
      }
      if (this.value && this.value.timezone) {
        this.timezone = this.value.timezone;
        this.update();
      }
    },
  },

  /**
   * Fetch required data for populating selects
   *
   */
  mounted() {
    // Get select list options
    this.getMonths();
    if (this.hasTimezone) {
      this.getTimezones();
    }

    if (this.hasTimezone) {
      if (this.value) {
        this.timezone = this.value.timezone;
      } else if (this.initialTimezone) {
        this.timezone = this.initialTimezone;
      }
    }

    let initialDate = '';
    if (this.value) {
      initialDate = getValuesFromIso(this.value.iso);
    } else if (this.initialCustomDate) {
      initialDate = getValuesFromIso(this.initialCustomDate);
    } else if (this.initialCurrentDate) {
      initialDate = getCurrentDateValues();
    }

    this.setDate(initialDate);
  },

  methods: {
    /**
     * Get month keys & strings array for select options
     *
     */
    async getMonths() {
      let months = getMonthStringsSelectArray();
      await loadLangStrings(months.map(x => x.label));
      months.map(x => x.label.toString());
      this.optionsMonths = months;
    },

    /**
     * Get server time zone string
     *
     * @param {string} zone
     */
    getServerTimezone(zone) {
      return this.$str('server_timezone', 'totara_core', zone);
    },

    /**
     * Get time zone keys & strings array for select options
     *
     */
    async getTimezones() {
      let zones = getTimeZoneKeyStrings();
      let that = this;
      const serverTimeZone = globalConfig.servertimezone;

      await loadLangStrings(zones.map(x => x.label));
      zones.map(function(zone) {
        let label = zone.label.toString();
        zone.label =
          label === serverTimeZone ? that.getServerTimezone(label) : label;
        return zone;
      });
      this.optionsTimezones = zones;
    },

    /**
     * Check if all required fields have been populated
     *
     * @returns {boolean}
     */
    hasRequiredFields() {
      return (
        Number.isInteger(this.day) &&
        Number.isInteger(this.month) &&
        Number.isInteger(this.year)
      );
    },

    /**
     * Make sure existing day value in within current range
     *
     * @param {array} range array of days
     */
    isDayWithinRange(range) {
      let currentDay = this.day;

      // Remove existing value if not within range
      if (currentDay > range.length) {
        this.day = '';
        this.update();
      }
    },

    /**
     * Make sure existing year value in within current range
     *
     * @param {array} range array of years
     */
    isYearWithinRange(range) {
      let currentYear = this.year;

      if (currentYear) {
        const withinRange = range.filter(function(year) {
          return year.id === currentYear;
        });

        // Remove existing value if not within range
        if (!withinRange.length) {
          this.year = '';
          this.update();
        }
      }
    },

    /**
     * Set values to date
     *
     */
    setDate(newDate) {
      if (newDate) {
        this.day = newDate.day;
        this.month = newDate.month;
        this.year = newDate.year;
        this.update();
      }
    },

    /**
     * Update the selected date
     *
     */
    update() {
      // Check if all required fields have been populated if not emit null which will trigger the validation required error
      if (!this.hasRequiredFields()) {
        this.$emit('input');
        return;
      }

      // Emit date values
      this.$emit('input', this.date);
    },
  },
};
</script>

<lang-strings>
{
  "totara_core": [
    "day",
    "month",
    "server_timezone",
    "time_zone",
    "year"
  ]
}
</lang-strings>
