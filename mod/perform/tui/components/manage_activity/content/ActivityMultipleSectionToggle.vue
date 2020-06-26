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

  @author Kunle Odusan <kunle.odusan@totaralearning.com>
  @package mod_perform
-->

<template>
  <div class="tui-activityMultipleSectionToggle">
    <ToggleButton
      v-model="value"
      :text="$str('multiple_sections_enabled', 'mod_perform')"
      @input="valueChanged"
    >
      <template v-slot:icon>
        <InfoIconButton :aria-label="$str('help', 'moodle')">
          {{ $str('multiple_sections_label_help', 'mod_perform') }}
        </InfoIconButton>
      </template>
    </ToggleButton>

    <ConfirmationModal
      :open="modalOpen"
      :title="$str('multiple_sections_confirmation_title', 'mod_perform')"
      @confirm="modalConfirmed"
      @cancel="modalCancelled"
    >
      {{
        value
          ? $str('multiple_sections_enabled_confirmation_text', 'mod_perform')
          : $str('multiple_sections_disabled_confirmation_text', 'mod_perform')
      }}
    </ConfirmationModal>
  </div>
</template>

<script>
import { notify } from 'totara_core/notifications';
import { NOTIFICATION_DURATION } from 'mod_perform/constants';
import ConfirmationModal from 'totara_core/components/modal/ConfirmationModal';
import InfoIconButton from 'totara_core/components/buttons/InfoIconButton';
import ToggleActivityMultiSectionSettingMutation from 'mod_perform/graphql/toggle_activity_multisection_setting.graphql';
import ToggleButton from 'totara_core/components/buttons/ToggleButton';

export default {
  components: {
    ConfirmationModal,
    InfoIconButton,
    ToggleButton,
  },

  props: {
    activity: {
      type: Object,
      required: true,
    },
  },

  data() {
    return {
      isSaving: false,
      modalOpen: false,
      value: this.activity.settings.multisection,
    };
  },
  methods: {
    /**
     * Opens modal when value changes.
     */
    valueChanged() {
      this.modalOpen = true;
    },
    /**
     * Action on modal cancel.
     */
    modalCancelled() {
      this.modalOpen = false;
      this.value = !this.value;
    },
    /**
     * Action on modal confirmation.
     */
    modalConfirmed() {
      this.modalOpen = false;
      this.isSaving = true;
      this.save();
    },
    /**
     * Save changes to multisection settings.
     */
    save() {
      this.$apollo
        .mutate({
          mutation: ToggleActivityMultiSectionSettingMutation,
          variables: {
            input: {
              activity_id: this.activity.id,
              setting: this.value,
            },
          },
          refetchAll: false,
        })
        .then(result => {
          this.$emit(
            'change',
            result.data.mod_perform_toggle_activity_multisection_setting
          );
          notify({
            duration: NOTIFICATION_DURATION,
            message: this.$str('toast_success_activity_update', 'mod_perform'),
            type: 'success',
          });
          this.isSaving = false;
        })
        .catch(() => {
          notify({
            duration: NOTIFICATION_DURATION,
            message: this.$str('toast_error_generic_update', 'mod_perform'),
            type: 'error',
          });
          this.isSaving = false;
        });
    },
  },
};
</script>

<lang-strings>
  {
    "mod_perform": [
      "multiple_sections_enabled",
      "multiple_sections_disabled_confirmation_text",
      "multiple_sections_enabled_confirmation_text",
      "multiple_sections_confirmation_title",
      "multiple_sections_label_help",
      "toast_success_activity_update",
      "toast_error_generic_update"
    ],
    "moodle": [
      "help"
    ]
  }
</lang-strings>
