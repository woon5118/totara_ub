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
  <div class="tui-performActivityWorkflowOptions">
    <h3 class="tui-performActivityWorkflowOptions__sectionTitle">
      {{ $str('workflow', 'mod_perform') }}
    </h3>
    <Form>
      <FormRow
        :label="$str('workflow_automatic_closure_label', 'mod_perform')"
        :helpmsg="$str('workflow_automatic_closure_label_help', 'mod_perform')"
      >
        <Checkbox
          v-model="close_on_completion"
          :disabled="formDisabled"
          :label="
            $str('workflow_automatic_closure_on_completion', 'mod_perform')
          "
          @change="save"
        >
          <p class="tui-performActivityWorkflowOptions__automaticClosure-text">
            {{
              $str('workflow_automatic_closure_on_completion', 'mod_perform')
            }}
          </p>
          <p
            class="tui-performActivityWorkflowOptions__automaticClosure-subtext"
          >
            {{
              $str(
                'workflow_automatic_closure_on_completion_help',
                'mod_perform'
              )
            }}
          </p>
        </Checkbox>
      </FormRow>
    </Form>
  </div>
</template>

<script>
import UpdateActivityWorkflowMutation from 'mod_perform/graphql/update_activity_workflow.graphql';
import Checkbox from 'totara_core/components/form/Checkbox';
import Form from 'totara_core/components/form/Form';
import FormRow from 'totara_core/components/form/FormRow';
import { notify } from 'totara_core/notifications';
import { NOTIFICATION_DURATION } from 'mod_perform/constants';

export default {
  components: {
    Checkbox,
    Form,
    FormRow,
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
      close_on_completion: this.activity.close_on_completion,
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
          mutation: UpdateActivityWorkflowMutation,
          variables: {
            input: {
              activity_id: this.activity.id,
              close_on_completion: this.close_on_completion,
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
        })
        .catch(() => {
          this.close_on_completion = this.activity.close_on_completion;
          notify({
            duration: NOTIFICATION_DURATION,
            message: this.$str('toast_error_generic_update', 'mod_perform'),
            type: 'error',
          });
        })
        .finally(() => {
          this.formDisabled = false;
        });
    },
  },
};
</script>

<lang-strings>
  {
    "mod_perform": [
      "workflow",
      "workflow_automatic_closure_label",
      "workflow_automatic_closure_label_help",
      "workflow_automatic_closure_on_completion",
      "workflow_automatic_closure_on_completion_help",
      "toast_error_generic_update",
      "toast_success_activity_update"
    ]
  }
</lang-strings>
