<!--
  This file is part of Totara Learn

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

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

  @author Samantha Jayasinghe <samantha.jayasinghe@totaralearning.com>
  @package mod_perform
-->
<template>
  <div>
    <FormScope path="additionalSettings">
      <FormRow
        :label="$str('schedule_job_assignment_based_instances', 'mod_perform')"
      >
        <FormRadioGroup
          :validate="jobBasedCanDisableValidator"
          name="subject_instance_generation"
        >
          <Radio
            :value="SUBJECT_INSTANCE_GENERATION_ONE_PER_SUBJECT"
            :aria-describedby="$id('aria-describedby')"
            >{{
              $str(
                'schedule_job_assignment_based_instances_disabled',
                'mod_perform'
              )
            }}</Radio
          >
          <FormRowDetails>
            <span
              class="tui-performAssignmentScheduleAdditionalSettings__radio_description"
            >
              {{
                $str(
                  'schedule_job_assignment_based_instances_disabled_description',
                  'mod_perform'
                )
              }}
            </span>
          </FormRowDetails>
          <Radio
            :value="SUBJECT_INSTANCE_GENERATION_ONE_PER_JOB"
            :aria-describedby="$id('aria-describedby')"
            class="tui-performAssignmentSchedule__radio"
            >{{
              $str(
                'schedule_job_assignment_based_instances_enabled',
                'mod_perform'
              )
            }}</Radio
          >
          <FormRowDetails>
            <span
              class="tui-performAssignmentScheduleAdditionalSettings__radio_description"
            >
              {{
                $str(
                  'schedule_job_assignment_based_instances_enabled_description',
                  'mod_perform'
                )
              }}
            </span>
          </FormRowDetails>
        </FormRadioGroup>
      </FormRow>
    </FormScope>
  </div>
</template>
<script>
import {
  FormScope,
  FormRadioGroup,
  FormRow,
} from 'totara_core/components/uniform';
import FormRowDetails from 'totara_core/components/form/FormRowDetails';
import Radio from 'totara_core/components/form/Radio';
import {
  SUBJECT_INSTANCE_GENERATION_ONE_PER_SUBJECT,
  SUBJECT_INSTANCE_GENERATION_ONE_PER_JOB,
} from 'mod_perform/constants';

export default {
  components: {
    FormRadioGroup,
    FormRow,
    FormRowDetails,
    FormScope,
    Radio,
  },
  props: {
    usesJobBasedDynamicSource: {
      type: Boolean,
    },
    dynamicSourceName: {
      type: String,
    },
  },
  data() {
    return {
      SUBJECT_INSTANCE_GENERATION_ONE_PER_SUBJECT,
      SUBJECT_INSTANCE_GENERATION_ONE_PER_JOB,
    };
  },
  methods: {
    /**
     * Validator for disabled job assigned based instance
     * May not disable if job based dynamic source is used
     *
     * @param v
     * @return {{}}
     */
    jobBasedCanDisableValidator(v) {
      if (
        this.usesJobBasedDynamicSource &&
        v == SUBJECT_INSTANCE_GENERATION_ONE_PER_SUBJECT
      ) {
        return this.$str(
          'schedule_job_assignment_based_disable_error',
          'mod_perform',
          this.dynamicSourceName
        );
      }

      return null;
    },
  },
};
</script>
<lang-strings>
  {
    "mod_perform": [
      "schedule_job_assignment_based_disable_error",
      "schedule_job_assignment_based_instances",
      "schedule_job_assignment_based_instances_disabled",
      "schedule_job_assignment_based_instances_disabled_description",
      "schedule_job_assignment_based_instances_enabled",
      "schedule_job_assignment_based_instances_enabled_description"
    ]
  }

</lang-strings>
