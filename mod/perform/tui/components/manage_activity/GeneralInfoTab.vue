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

  @author Jaron Steenson <jaron.steenson@totaralearning.com>
  @package totara_perform
-->

<template>
  <div class="tui-performManageActivityGeneralInfo">
    <h3
      v-if="!disableAfterSave"
      class="tui-performManageActivityGeneralInfo__heading"
    >
      {{ $str('activity_general_tab_heading', 'mod_perform') }}
    </h3>

    <Form>
      <FormRow
        v-slot="{ id }"
        :label="$str('general_info_label_activity_title', 'mod_perform')"
        required
      >
        <InputText
          :id="id"
          :value="activity.edit_name"
          :maxlength="ACTIVITY_NAME_MAX_LENGTH"
          @input="updateActivity({ edit_name: $event })"
        />
      </FormRow>

      <FormRow
        v-slot="{ id }"
        :label="$str('general_info_label_activity_description', 'mod_perform')"
      >
        <Textarea
          :id="id"
          :value="activity.edit_description"
          @input="updateActivity({ edit_description: $event })"
        />
      </FormRow>

      <FormRow
        v-slot="{ id }"
        :label="$str('general_info_label_activity_type', 'mod_perform')"
        :required="useModalStyling || !isActive"
        :no-input="!isActive"
      >
        <div>
          <Select
            v-if="disableAfterSave || (!useModalStyling && !isActive)"
            :id="id"
            v-model="activityTypeSelection"
            :aria-labelledby="id"
            :aria-describedby="$id('aria-describedby')"
            :options="activityTypes"
          />
          <span v-else class="tui-performManageActivityGeneralInfo">{{
            activityTypeName
          }}</span>

          <FormRowDetails
            v-show="useModalStyling"
            :id="$id('aria-describedby')"
          >
            {{ $str('activity_type_help_text', 'mod_perform') }}
          </FormRowDetails>
        </div>
      </FormRow>

      <template v-if="!useModalStyling">
        <h3 class="tui-performManageActivityGeneralInfo__heading">
          {{
            $str('activity_general_response_attribution_heading', 'mod_perform')
          }}
        </h3>

        <FormRow
          :label="
            $str('activity_general_anonymous_responses_label', 'mod_perform')
          "
          :no-input="!isActive"
        >
          <ToggleButton
            v-if="!isActive"
            :value="activity.anonymous_responses"
            toggle-first
            text=""
            @input="updateActivity({ anonymous_responses: $event })"
          />
          <span v-else>{{
            activeToggleText(activity.anonymous_responses)
          }}</span>
        </FormRow>
      </template>

      <FormRow
        :class="
          useModalStyling
            ? 'tui-performManageActivityGeneralInfo__modalBtnRow'
            : ''
        "
      >
        <ButtonGroup>
          <Button
            :styleclass="{ primary: true }"
            :text="submitButtonText"
            :disabled="isSaving || hasNoTitle || hasNoType"
            type="submit"
            @click.prevent="trySave"
          />
          <Button
            v-show="!useModalStyling"
            :disabled="isSaving"
            :text="$str('cancel', 'moodle')"
            @click="resetActivityChanges"
          />
        </ButtonGroup>
      </FormRow>
    </Form>
  </div>
</template>

<script>
import Button from 'totara_core/components/buttons/Button';
import ButtonGroup from 'totara_core/components/buttons/ButtonGroup';
import Form from 'totara_core/components/form/Form';
import FormRow from 'totara_core/components/form/FormRow';
import FormRowDetails from 'totara_core/components/form/FormRowDetails';
import InputText from 'totara_core/components/form/InputText';
import Select from 'totara_core/components/form/Select';
import Textarea from 'totara_core/components/form/Textarea';
import ToggleButton from 'totara_core/components/buttons/ToggleButton';
import { ACTIVITY_STATUS_ACTIVE } from 'mod_perform/constants';

//GraphQL
import activityTypesQuery from 'mod_perform/graphql/activity_types';
import createActivityMutation from 'mod_perform/graphql/create_activity';
import updateGeneralInfoMutation from 'mod_perform/graphql/update_activity';

// This should correspond to mod_perform\models\activity\activity::NAME_MAX_LENGTH in the back end.
const ACTIVITY_NAME_MAX_LENGTH = 1024;

export default {
  components: {
    Button,
    ButtonGroup,
    Form,
    FormRow,
    FormRowDetails,
    InputText,
    Select,
    Textarea,
    ToggleButton,
  },

  props: {
    disableAfterSave: {
      type: Boolean,
      default: false,
    },
    submitButtonText: {
      type: String,
      default() {
        return this.$str('save_changes', 'mod_perform');
      },
    },
    useModalStyling: {
      type: Boolean,
      default: false,
    },
    value: {
      type: Object,
    },
  },

  data() {
    const activity = Object.assign({}, this.value);
    const typeId = activity && activity.id ? activity.type.id : 0;
    const typeName =
      activity && activity.id ? activity.type.display_name : 'unknown';

    return {
      activity: activity,
      activityTypeName: typeName,
      activityTypes: [
        {
          id: 0,
          label: this.$str('general_info_select_activity_type', 'mod_perform'),
        },
      ],
      activityTypeSelection: typeId,
      isSaving: false,
      mutationError: null,
    };
  },

  computed: {
    /**
     * Has the activity not yet been saved to the back-end.
     *
     * @returns {boolean}
     */
    exists() {
      return Boolean(this.activity.id);
    },

    /**
     * Is the title/name text empty.
     *
     * @returns {boolean}
     */
    hasNoTitle() {
      return (
        !this.activity.edit_name || this.activity.edit_name.trim().length === 0
      );
    },

    /**
     * Is the activity type unselected.
     *
     * @returns {boolean}
     */
    hasNoType() {
      return this.activityTypeSelection === 0;
    },

    /**
     * check activity status is active
     * @returns {boolean}
     */
    isActive() {
      if (!this.value) {
        return false;
      }
      return this.value.state_details.name === ACTIVITY_STATUS_ACTIVE;
    },
  },

  created() {
    this.ACTIVITY_NAME_MAX_LENGTH = ACTIVITY_NAME_MAX_LENGTH;
  },

  apollo: {
    activityTypes: {
      query: activityTypesQuery,
      variables() {
        return [];
      },
      update({ mod_perform_activity_types: types }) {
        const options = types
          .map(type => {
            return { id: type.id, label: type.display_name };
          })
          .sort((a, b) => a.label.localeCompare(b.label));
        //show 'select type' only when create activity
        if (this.useModalStyling) {
          options.unshift({
            id: 0,
            label: this.$str(
              'general_info_select_activity_type',
              'mod_perform'
            ),
          });
        }
        return options;
      },
    },
  },

  methods: {
    /**
     * @returns {Promise<{id, name, description}>}
     */
    async save() {
      let mutation, mutationName, variables;

      if (this.exists) {
        mutation = updateGeneralInfoMutation;
        mutationName = 'mod_perform_update_activity';

        variables = {
          activity_id: this.activity.id,
          name: this.activity.edit_name,
          description: this.activity.edit_description,
        };

        // Add draft only updates.
        if (!this.isActive) {
          variables.anonymous_responses = this.activity.anonymous_responses;
        }

        // Only mutate the type id if the user has explicitly changed the type.
        if (this.activity.type.id !== this.activityTypeSelection) {
          variables.type_id = this.activityTypeSelection;
        }
      } else {
        mutation = createActivityMutation;
        mutationName = 'mod_perform_create_activity';

        variables = {
          name: this.activity.edit_name,
          description: this.activity.edit_description,
          type: this.activityTypeSelection,
        };
      }

      const { data: resultData } = await this.$apollo.mutate({
        mutation,
        variables,
        refetchAll: false, // Don't refetch all the data again
      });

      return resultData[mutationName].activity;
    },

    /**
     * Get a textual representation of a toggle switch for an active activity (setting is no longer available).
     * @return {string}
     */
    activeToggleText(value) {
      if (value) {
        return this.$str('boolean_setting_text_enabled', 'mod_perform');
      }

      return this.$str('boolean_setting_text_disabled', 'mod_perform');
    },

    /**
     * Try to persist the activity to the back end.
     * Emitting events on success/failure.
     *
     * @returns {Promise<void>}
     */
    async trySave() {
      this.isSaving = true;
      this.activityTypeName = this.activityTypes[
        this.activityTypeSelection - 1
      ].label;

      try {
        const savedActivity = await this.save();
        this.updateActivity(savedActivity);
        this.$emit('mutation-success', savedActivity);

        // On create (from the modal) we keep the submit button disabled while the redirect is processing.
        if (!this.disableAfterSave) {
          this.isSaving = false;
        }
      } catch (e) {
        this.$emit('mutation-error', e);

        // If something goes wrong during create, allow the user to try again.
        this.isSaving = false;
      }
    },

    /**
     * Emit an input event with an updated activity object, changes are patched into the existing value (activity).
     *
     * @param {object} update - The new values to patch into the activity object emitted.
     */
    updateActivity(update) {
      this.activity = Object.assign({}, this.activity, update);

      this.$emit('input', this.activity);
    },

    /**
     * revert to last saved changes
     */
    resetActivityChanges() {
      this.activity.edit_name = this.activity.name;
      this.activity.edit_description = this.activity.description;
      this.activityTypeSelection = this.activity.type.id;
    },
  },
};
</script>

<lang-strings>
  {
    "mod_perform": [
      "activity_general_tab_heading",
      "activity_general_response_attribution_heading",
      "activity_general_anonymous_responses_label",
      "activity_type_help_text",
      "boolean_setting_text_enabled",
      "boolean_setting_text_disabled",
      "general_info_label_activity_description",
      "general_info_label_activity_title",
      "general_info_label_activity_type",
      "general_info_select_activity_type",
      "save_changes"
    ],
    "moodle": [
      "cancel"
    ]
  }
</lang-strings>
