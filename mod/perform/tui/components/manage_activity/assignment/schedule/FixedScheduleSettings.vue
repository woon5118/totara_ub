<!--
  This file is part of Totara Learn

  Copyright (C) 2019 onwards Totara Learning Solutions LTD

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 3 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.

  @author Jaron Steenson jaron.steenson@totaralearning.com>
  @package mod_perform
-->

<template>
  <FormScope :path="uniformScopePath" :validate="fixedDateRangeValidator">
    <div class="tui_performAssignmentSchedule__narrative-inputs">
      <Label
        :for="$id('schedule_date_from')"
        class="tui_performAssignmentSchedule__narrative-label"
        :label="$str('schedule_date_from', 'mod_perform')"
      />
      <div class="tui_performAssignmentSchedule__narrative-date">
        <FormText
          :id="$id('schedule_date_from')"
          :name="fixedFromName"
          :validations="dateValidator"
        />
      </div>

      <template v-if="scheduleIsLimitedFixed">
        <Label
          :for="$id('schedule_date_to')"
          class="tui_performAssignmentSchedule__narrative-label tui_performAssignmentSchedule__narrative-label"
          :label="$str('schedule_date_to', 'mod_perform')"
        />
        <div class="tui_performAssignmentSchedule__narrative-date">
          <FormText
            :id="$id('schedule_date_to')"
            :name="fixedToName"
            :validations="dateValidator"
          />
        </div>
      </template>
      <template v-else-if="scheduleIsOpenFixed">
        <span class="tui_performAssignmentSchedule__narrative-label">{{
          $str('schedule_date_range_onwards', 'mod_perform')
        }}</span>
      </template>
    </div>
  </FormScope>
</template>
<script>
import FormScope from 'totara_core/components/reform/FormScope';
import FormText from 'totara_core/components/uniform/FormText';
import Label from 'totara_core/components/form/Label';

export default {
  components: {
    FormScope,
    FormText,
    Label,
  },
  props: {
    uniformScopePath: {
      required: true,
      type: String,
    },
    fixedFromName: {
      required: true,
      type: String,
    },
    fixedToName: {
      required: true,
      type: String,
    },
    scheduleIsLimitedFixed: {
      required: true,
      type: Boolean,
    },
    scheduleIsOpenFixed: {
      required: true,
      type: Boolean,
    },
  },
  methods: {
    /**
     * Validator for date strings.
     * Please note: Note this is most likely temporary until the date picker component is built.
     *
     * @return {{}}
     */
    dateValidator() {
      return {
        validate: val => !isNaN(Date.parse(val)),
        message: () => this.$str('schedule_error_date_required', 'mod_perform'),
      };
    },

    /**
     * Validator for the fixed instance creation to/from range.
     * From must not be after to.
     * Please note: Note this is most likely temporary until the date picker component is built.
     *
     * @param values
     * @return {{}}
     */
    fixedDateRangeValidator(values) {
      const errors = {};
      const fromDate = Date.parse(values.fixed_from);
      const toDate = Date.parse(values.fixed_to);

      if (fromDate > toDate) {
        errors.fixed_to = this.$str('schedule_error_date_range', 'mod_perform');
      }

      return errors;
    },
  },
};
</script>

<lang-strings>
  {
    "mod_perform": [
      "schedule_date_from",
      "schedule_date_range_onwards",
      "schedule_date_to",
      "schedule_error_date_range",
      "schedule_error_date_required"
    ]
  }
</lang-strings>
