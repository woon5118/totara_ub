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

  @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
  @package mod_perform
-->

<template>
  <div class="tui-performActivityWorkflowSettings">
    <h3 class="tui-performActivityWorkflowSettings__heading">
      {{ $str('workflow_settings', 'mod_perform') }}
    </h3>
    <Form>
      <FormRow
        v-slot="{ label }"
        :label="$str('workflow_automatic_closure_label', 'mod_perform')"
        :helpmsg="$str('workflow_automatic_closure_label_help', 'mod_perform')"
      >
        <div>
          <ToggleButton
            v-model="value"
            :disabled="isSaving"
            :toggle-first="true"
            :text="
              $str('workflow_automatic_closure_on_completion', 'mod_perform')
            "
            :aria-label="label"
            @input="valueChanged"
          />

          <FormRowDetails :id="$id('aria-describedby')">
            {{
              $str(
                'workflow_automatic_closure_on_completion_help',
                'mod_perform'
              )
            }}
          </FormRowDetails>
        </div>
      </FormRow>
    </Form>

    <ConfirmationModal
      :open="modalOpen"
      :title="
        $str('workflow_automatic_closure_confirmation_title', 'mod_perform')
      "
      :confirm-button-text="$str('modal_confirm', 'mod_perform')"
      @confirm="modalConfirmed"
      @cancel="modalCancelled"
    >
      {{
        $str(
          value
            ? 'workflow_automatic_closure_enabled_confirmation_text'
            : 'workflow_automatic_closure_disabled_confirmation_text',
          'mod_perform'
        )
      }}
    </ConfirmationModal>
  </div>
</template>

<script>
import ConfirmationModal from 'totara_core/components/modal/ConfirmationModal';
import Form from 'totara_core/components/form/Form';
import FormRow from 'totara_core/components/form/FormRow';
import FormRowDetails from 'totara_core/components/form/FormRowDetails';
import ToggleButton from 'totara_core/components/buttons/ToggleButton';

// Util
import { notify } from 'totara_core/notifications';
import {
  ACTIVITY_STATUS_DRAFT,
  NOTIFICATION_DURATION,
} from 'mod_perform/constants';
// Queries
import toggleActivityCloseOnCompletion from 'mod_perform/graphql/toggle_activity_close_on_completion_setting';

export default {
  components: {
    ConfirmationModal,
    Form,
    FormRow,
    FormRowDetails,
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
      value: this.activity.settings.close_on_completion,
      modalOpen: false,
    };
  },

  computed: {
    isDraft() {
      return this.activity.state_details.name === ACTIVITY_STATUS_DRAFT;
    },
  },

  methods: {
    /**
     * Opens modal when value changes.
     */
    valueChanged() {
      if (this.isDraft) {
        this.save();
      } else {
        this.modalOpen = true;
      }
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

    save() {
      this.isSaving = true;
      this.$apollo
        .mutate({
          mutation: toggleActivityCloseOnCompletion,
          variables: {
            input: {
              activity_id: this.activity.id,
              setting: this.value,
            },
          },
          refetchAll: false, // Prevents 4 additional queries from executing unnecessarily
        })
        .then(() => {
          notify({
            duration: NOTIFICATION_DURATION,
            message: this.$str('toast_success_activity_update', 'mod_perform'),
            type: 'success',
          });
          this.isSaving = false;
        })
        .catch(() => {
          this.value = this.activity.settings.close_on_completion;
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
      "modal_confirm",
      "workflow_automatic_closure_confirmation_title",
      "workflow_automatic_closure_disabled_confirmation_text",
      "workflow_automatic_closure_enabled_confirmation_text",
      "workflow_automatic_closure_label",
      "workflow_automatic_closure_label_help",
      "workflow_automatic_closure_on_completion",
      "workflow_automatic_closure_on_completion_help",
      "workflow_settings",
      "toast_error_generic_update",
      "toast_success_activity_update"
    ],
    "moodle": [
      "help"
    ]
  }
</lang-strings>
