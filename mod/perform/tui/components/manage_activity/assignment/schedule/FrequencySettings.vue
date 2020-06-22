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

  @author Riana Rossouw<riana.rossouw@totaralearning.com>
  @package mod_perform
-->
<template>
  <ScheduleSettingContainer :title="title">
    <div class="tui-performAssignmentScheduleFrequencySettings">
      <template v-if="!isRepeating">
        <span>
          {{ $str('schedule_repeating_disabled_description', 'mod_perform') }}
        </span>
      </template>
      <template v-else>
        <span :id="$id('schedule_repeating_type')">
          {{ $str('schedule_repeating_enabled_description', 'mod_perform') }}
        </span>
        <div class="tui-performAssignmentScheduleFrequencySettings__option">
          <FormRadioGroup
            name="repeatingType"
            :aria-labelledby="$id('schedule_repeating_type')"
          >
            <!-- Not using v-for as Radio must be a direct child of RadioGroup -->
            <Label
              :for="$id('schedule_repeating_type_after_creation')"
              :label="
                $str(
                  'schedule_repeating_type_after_creation_label',
                  'mod_perform'
                )
              "
              hidden
            />
            <Radio
              :id="$id('schedule_repeating_type_after_creation')"
              :value="typeOptions.afterCreation"
              :class="{
                'tui-performAssignmentSchedule__radio-disabled': !typeIsAfterCreation,
              }"
            >
              <div class="tui_performAssignmentSchedule__singleLine">
                <span
                  v-html="$str('schedule_repeating_every', 'mod_perform')"
                />
                <RelativeDateSelector
                  :path="['repeatingOffset', typeOptions.afterCreation]"
                  :disabled="!typeIsAfterCreation"
                />
                <span
                  v-html="
                    $str('schedule_repeating_after_creation', 'mod_perform')
                  "
                />
              </div>
            </Radio>

            <Label
              :for="$id('schedule_repeating_type_after_creation_when_complete')"
              :label="
                $str(
                  'schedule_repeating_type_after_creation_when_complete_label',
                  'mod_perform'
                )
              "
              hidden
            />
            <Radio
              :id="$id('schedule_repeating_type_after_creation_when_complete')"
              :value="typeOptions.afterCreationWhenComplete"
              :class="{
                'tui-performAssignmentSchedule__radio-disabled': !typeIsAfterCreationWhenComplete,
              }"
            >
              <div class="tui_performAssignmentSchedule__singleLine">
                <span
                  v-html="
                    $str(
                      'schedule_repeating_after_creation_when_complete',
                      'mod_perform'
                    )
                  "
                />
                <RelativeDateSelector
                  :path="[
                    'repeatingOffset',
                    typeOptions.afterCreationWhenComplete,
                  ]"
                  :disabled="!typeIsAfterCreationWhenComplete"
                />
                <span v-html="$str('schedule_repeating_ago', 'mod_perform')" />
              </div>
            </Radio>

            <Label
              :for="$id('schedule_repeating_type_after_completion')"
              :label="
                $str(
                  'schedule_repeating_type_after_creation_when_complete_label',
                  'mod_perform'
                )
              "
              hidden
            />
            <Radio
              :id="$id('schedule_repeating_type_after_completion')"
              :value="typeOptions.afterCompletion"
              :class="{
                'tui-performAssignmentSchedule__radio-disabled': !typeIsAfterCompletion,
              }"
            >
              <div class="tui_performAssignmentSchedule__singleLine">
                <span
                  v-html="$str('schedule_repeating_every', 'mod_perform')"
                />
                <RelativeDateSelector
                  :path="['repeatingOffset', typeOptions.afterCompletion]"
                  :disabled="!typeIsAfterCompletion"
                />
                <span
                  v-html="
                    $str('schedule_repeating_after_completion', 'mod_perform')
                  "
                />
              </div>
            </Radio>
          </FormRadioGroup>
        </div>

        <div
          class="tui-performAssignmentScheduleFrequencySettings__repeating_limit"
        >
          <span>{{ limitTitle }}</span>

          <div class="tui-performAssignmentScheduleFrequencySettings__option">
            <Label
              :for="$id('schedule_repeating_limit_group')"
              :label="$str('schedule_repeating_limit_label', 'mod_perform')"
              hidden
            />
            <FormRadioGroup
              :id="$id('schedule_repeating_limit_group')"
              name="repeatingIsLimited"
            >
              <Label
                :for="$id('schedule_repeating_limit_none')"
                :label="
                  $str('schedule_repeating_limit_none_label', 'mod_perform')
                "
                hidden
              />
              <Radio :id="$id('schedule_repeating_limit_none')" :value="false">
                <span>{{ noLimitLabel }}</span>
              </Radio>

              <Label
                :for="$id('schedule_repeating_limit_maximized')"
                :label="
                  $str(
                    'schedule_repeating_limit_maximized_label',
                    'mod_perform'
                  )
                "
                hidden
              />
              <Radio
                :id="$id('schedule_repeating_limit_maximized')"
                :value="true"
              >
                <div class="tui_performAssignmentSchedule__singleLine">
                  <span>{{
                    $str('schedule_repeating_limit_maximum_of', 'mod_perform')
                  }}</span>
                  <div
                    class=" tui-performAssignmentScheduleFrequencySettings__limit"
                  >
                    <FormNumber
                      name="repeatingLimit"
                      :aria-label="
                        $str(
                          'schedule_repeating_limit_count_label',
                          'mod_perform'
                        )
                      "
                      :min="1"
                    />
                  </div>
                  <span>{{
                    $str(
                      'schedule_repeating_limit_instances_per_user',
                      'mod_perform'
                    )
                  }}</span>
                </div>
              </Radio>
            </FormRadioGroup>
          </div>
        </div>
      </template>
    </div>
  </ScheduleSettingContainer>
</template>

<script>
import { FormNumber, FormRadioGroup } from 'totara_core/components/uniform';
import Radio from 'totara_core/components/form/Radio';
import RelativeDateSelector from 'mod_perform/components/manage_activity/assignment/schedule/RelativeDateSelector';
import ScheduleSettingContainer from 'mod_perform/components/manage_activity/assignment/schedule/ScheduleSettingContainer';

import {
  SCHEDULE_REPEATING_TYPE_AFTER_CREATION,
  SCHEDULE_REPEATING_TYPE_AFTER_CREATION_WHEN_COMPLETE,
  SCHEDULE_REPEATING_TYPE_AFTER_COMPLETION,
} from 'mod_perform/constants';

export default {
  components: {
    FormNumber,
    FormRadioGroup,
    Radio,
    RelativeDateSelector,
    ScheduleSettingContainer,
  },
  props: {
    isOpen: {
      type: Boolean,
      required: true,
    },
    isRepeating: {
      type: Boolean,
      required: true,
    },
    repeatingType: {
      type: String,
      required: true,
    },
  },
  data() {
    let typeOptions = {
      afterCreation: SCHEDULE_REPEATING_TYPE_AFTER_CREATION,
      afterCreationWhenComplete: SCHEDULE_REPEATING_TYPE_AFTER_CREATION_WHEN_COMPLETE,
      afterCompletion: SCHEDULE_REPEATING_TYPE_AFTER_COMPLETION,
    };

    return {
      typeOptions,
    };
  },

  computed: {
    title() {
      if (this.isRepeating) {
        return this.$str('schedule_repeating_enabled', 'mod_perform');
      } else {
        return this.$str('schedule_repeating_disabled', 'mod_perform');
      }
    },
    typeIsAfterCreation() {
      return this.repeatingType === SCHEDULE_REPEATING_TYPE_AFTER_CREATION;
    },
    typeIsAfterCreationWhenComplete() {
      return (
        this.repeatingType ===
        SCHEDULE_REPEATING_TYPE_AFTER_CREATION_WHEN_COMPLETE
      );
    },
    typeIsAfterCompletion() {
      return this.repeatingType === SCHEDULE_REPEATING_TYPE_AFTER_COMPLETION;
    },
    limitTitle() {
      if (this.isOpen) {
        return this.$str('schedule_repeating_with', 'mod_perform');
      } else {
        return this.$str('schedule_repeating_limited_until', 'mod_perform');
      }
    },
    noLimitLabel() {
      if (this.isOpen) {
        return this.$str(
          'schedule_repeating_limit_none_open_ended',
          'mod_perform'
        );
      } else {
        return this.$str('schedule_repeating_limit_none', 'mod_perform');
      }
    },
  },
};
</script>

<lang-strings>
  {
    "mod_perform": [
      "schedule_repeating_after_completion",
      "schedule_repeating_after_creation",
      "schedule_repeating_after_creation_when_complete",
      "schedule_repeating_ago",
      "schedule_repeating_disabled",
      "schedule_repeating_disabled_description",
      "schedule_repeating_enabled",
      "schedule_repeating_enabled_description",
      "schedule_repeating_every",
      "schedule_repeating_limit_count_label",
      "schedule_repeating_limit_instances_per_user",
      "schedule_repeating_limit_label",
      "schedule_repeating_limit_maximized_label",
      "schedule_repeating_limit_maximum_of",
      "schedule_repeating_limit_none",
      "schedule_repeating_limit_none_label",
      "schedule_repeating_limit_none_open_ended",
      "schedule_repeating_limited_until",
      "schedule_repeating_type_after_completion_label",
      "schedule_repeating_type_after_creation_label",
      "schedule_repeating_type_after_creation_when_complete_label",
      "schedule_repeating_with"
    ]
  }
</lang-strings>
