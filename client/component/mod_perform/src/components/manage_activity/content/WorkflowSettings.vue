<!--
  This file is part of Totara Enterprise Extensions.

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

  Totara Enterprise Extensions is provided only to Totara
  Learning Solutions LTD's customers and partners, pursuant to
  the terms and conditions of a separate agreement with Totara
  Learning Solutions LTD or its affiliate.

  If you do not have an agreement with Totara Learning Solutions
  LTD, you may not access, use, modify, or distribute this software.
  Please contact [licensing@totaralearning.com] for more information.

  @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
  @module mod_perform
-->

<template>
  <div class="tui-performActivityWorkflowSettings">
    <h3 class="tui-performActivityWorkflowSettings__heading">
      {{ $str('workflow_settings', 'mod_perform') }}
    </h3>
    <Form>
      <FormRow
        :label="$str('workflow_automatic_closure_label', 'mod_perform')"
        :helpmsg="$str('workflow_automatic_closure_label_help', 'mod_perform')"
      >
        <ToggleSwitch
          v-model="value"
          :disabled="isSaving"
          :toggle-first="true"
          :text="
            $str('workflow_automatic_closure_on_completion', 'mod_perform')
          "
          @input="valueChanged"
        />

        <FormRowDetails :id="$id('aria-describedby')">
          {{
            $str('workflow_automatic_closure_on_completion_help', 'mod_perform')
          }}
        </FormRowDetails>
      </FormRow>

      <FormRow>
        <div class="tui-performActivityWorkflowSettings__warning">
          <NotificationBanner
            v-if="showWarning"
            type="warning"
            :message="
              $str('automatic_closure_status_mismatch_warning', 'mod_perform')
            "
          />
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
import ConfirmationModal from 'tui/components/modal/ConfirmationModal';
import Form from 'tui/components/form/Form';
import FormRow from 'tui/components/form/FormRow';
import FormRowDetails from 'tui/components/form/FormRowDetails';
import NotificationBanner from 'tui/components/notifications/NotificationBanner';
import ToggleSwitch from 'tui/components/toggle/ToggleSwitch';
// Util
import { notify } from 'tui/notifications';
import {
  ACTIVITY_STATUS_DRAFT,
  VISIBILITY_CONDITION_NONE,
} from 'mod_perform/constants';
// Queries
import toggleActivityCloseOnCompletion from 'mod_perform/graphql/toggle_activity_close_on_completion_setting';

export default {
  components: {
    ConfirmationModal,
    Form,
    FormRow,
    FormRowDetails,
    NotificationBanner,
    ToggleSwitch,
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
    showWarning() {
      return (
        this.activity.settings.visibility_condition != null &&
        this.activity.settings.visibility_condition.value !==
          VISIBILITY_CONDITION_NONE &&
        !this.value
      );
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
            message: this.$str('toast_success_activity_update', 'mod_perform'),
            type: 'success',
          });
          this.isSaving = false;

          const newSettings = Object.assign({}, this.activity.settings, {
            close_on_completion: this.value,
          });
          const newActivity = Object.assign({}, this.activity, {
            settings: newSettings,
          });
          this.$emit('change', newActivity);
        })
        .catch(() => {
          this.value = this.activity.settings.close_on_completion;
          notify({
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
      "automatic_closure_status_mismatch_warning",
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
    ]
  }
</lang-strings>

<style lang="scss">
.tui-performActivityWorkflowSettings {
  &__heading {
    margin: 0;
    @include tui-font-heading-small();
  }
  &__warning {
    max-width: 712px;
  }
}

@media (min-width: $tui-screen-sm) {
  .tui-performActivityWorkflowSettings {
    & > * + * {
      padding-top: var(--gap-4);
    }
  }
}
</style>
