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
    <h4 class="tui-performAssignmentSchedule__heading">
      {{ $str('schedule_additional_settings', 'mod_perform') }}
    </h4>
    <FormScope path="additionalSettings">
      <FormRow
        :label="$str('schedule_multiple_job_assignments', 'mod_perform')"
        :helpmsg="$str('schedule_multiple_job_assignments_help', 'mod_perform')"
      >
        <FormRadioGroup name="multiple_job_assignment">
          <Radio :value="ONE_PER_SUBJECT">{{
            $str(
              'schedule_multiple_job_assignments_single_subject_multiple_job',
              'mod_perform'
            )
          }}</Radio>
          <Radio :value="ONE_PER_JOB">{{
            $str(
              'schedule_multiple_job_assignments_single_subject_per_job',
              'mod_perform'
            )
          }}</Radio>
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
import Radio from 'totara_core/components/form/Radio';

const ONE_PER_SUBJECT = 'ONE_PER_SUBJECT';
const ONE_PER_JOB = 'ONE_PER_JOB';

export default {
  components: {
    FormRadioGroup,
    FormRow,
    FormScope,
    Radio,
  },
  data() {
    return {
      ONE_PER_SUBJECT,
      ONE_PER_JOB,
    };
  },
};
</script>
<lang-strings>
  {
    "mod_perform": [
      "schedule_additional_settings",
      "schedule_multiple_job_assignments",
      "schedule_multiple_job_assignments_help",
      "schedule_multiple_job_assignments_single_subject_multiple_job",
      "schedule_multiple_job_assignments_single_subject_per_job"
    ]
  }

</lang-strings>
