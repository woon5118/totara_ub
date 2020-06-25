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
        :label="$str('workflow_automatic_closure_label', 'mod_perform')"
        :helpmsg="$str('workflow_automatic_closure_label_help', 'mod_perform')"
      >
        <div>
          <Checkbox
            v-model="close_on_completion"
            :disabled="formDisabled"
            :aria-describedby="$id('aria-describedby')"
            @change="save"
          >
            {{
              $str('workflow_automatic_closure_on_completion', 'mod_perform')
            }}
          </Checkbox>

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
  </div>
</template>

<script>
import Checkbox from 'totara_core/components/form/Checkbox';
import Form from 'totara_core/components/form/Form';
import FormRow from 'totara_core/components/form/FormRow';
import FormRowDetails from 'totara_core/components/form/FormRowDetails';
// Util
import { notify } from 'totara_core/notifications';
import { NOTIFICATION_DURATION } from 'mod_perform/constants';
// Queries
import toggleActivityCloseOnCompletion from 'mod_perform/graphql/toggle_activity_close_on_completion_setting';

export default {
  components: {
    Checkbox,
    Form,
    FormRow,
    FormRowDetails,
  },

  props: {
    activity: {
      type: Object,
      required: true,
    },
  },

  data() {
    return {
      formDisabled: false,
      close_on_completion: this.activity.settings.close_on_completion,
    };
  },

  computed: {
    isSaving() {
      return this.$apollo.loading;
    },
  },

  methods: {
    save() {
      this.formDisabled = true;
      this.$apollo
        .mutate({
          mutation: toggleActivityCloseOnCompletion,
          variables: {
            input: {
              activity_id: this.activity.id,
              setting: this.close_on_completion,
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
          this.formDisabled = false;
        })
        .catch(() => {
          this.close_on_completion = this.activity.settings.close_on_completion;
          notify({
            duration: NOTIFICATION_DURATION,
            message: this.$str('toast_error_generic_update', 'mod_perform'),
            type: 'error',
          });
          this.formDisabled = false;
        });
    },
  },
};
</script>

<lang-strings>
  {
    "mod_perform": [
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
